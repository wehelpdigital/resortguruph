<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copy every rg_* table from the default DB connection (the shared
 * onmartph_axis database the btc-check admin edits) into the Laravel Cloud
 * database (the "cloud" connection, configured from CLOUD_DB_* in .env).
 *
 * Run it from a local machine:
 *
 *   php artisan rg:cloud-sync-db                  # everything
 *   php artisan rg:cloud-sync-db --tables=rg_seo_pages,rg_content_blocks
 *
 * Target tables are emptied and refilled, so the Cloud copy ends up as an
 * exact mirror of the source. Re-run it any time to publish admin edits.
 */
class CloudSyncDb extends Command
{
    protected $signature = 'rg:cloud-sync-db
        {--target=cloud : Connection to copy INTO}
        {--tables= : Comma-separated subset of tables (default: every rg_* table)}
        {--skip-migrate : Do not run migrations on the target first}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Mirror the rg_* tables from the shared DB into the Laravel Cloud DB';

    /** Max bytes of row data per INSERT, well under MySQL max_allowed_packet. */
    private const BATCH_BYTES = 4_000_000;

    public function handle(): int
    {
        $source = config('database.default');
        $target = (string) $this->option('target');
        $tcfg = config("database.connections.{$target}");

        if ($source === $target) {
            $this->error('Source and target are the same connection.');
            return self::FAILURE;
        }
        if (! is_array($tcfg) || (empty($tcfg['host']) && empty($tcfg['url']))) {
            $this->error("Connection [{$target}] is not configured. Set CLOUD_DB_HOST, CLOUD_DB_PORT, CLOUD_DB_DATABASE, CLOUD_DB_USERNAME and CLOUD_DB_PASSWORD in .env.");
            return self::FAILURE;
        }
        if (! in_array($tcfg['driver'], ['mysql', 'mariadb'], true)) {
            $this->error("Target driver is [{$tcfg['driver']}]. The rg_* migrations and the btc-check admin are MySQL-only; attach a Laravel MySQL database instead.");
            return self::FAILURE;
        }

        $scfg = config("database.connections.{$source}");
        $this->line("Source: <info>{$scfg['host']}/{$scfg['database']}</info>  ({$source})");
        $this->line("Target: <comment>" . ($tcfg['host'] ?? 'url') . '/' . ($tcfg['database'] ?? '') . "</comment>  ({$target})");

        try {
            DB::connection($target)->getPdo();
        } catch (\Throwable $e) {
            $this->error('Cannot connect to target: ' . $e->getMessage());
            if (preg_match('/certificate|ssl|tls/i', $e->getMessage())) {
                $this->line('TLS hint: set CLOUD_DB_SSL_CA to the CA file from the Cloud dashboard, or CLOUD_DB_SSL_VERIFY=false to skip certificate verification (traffic stays encrypted).');
            }
            if (preg_match('/timed out|refused|getaddrinfo|No such host/i', $e->getMessage())) {
                $this->line('Network hint: enable the public endpoint on the Cloud database and copy its host (…public.db.laravel.cloud).');
            }
            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Every rg_* table on the TARGET will be emptied and refilled from the source. Continue?')) {
            return self::FAILURE;
        }

        if (! $this->option('skip-migrate')) {
            $this->info('Running migrations on target...');
            $this->call('migrate', ['--database' => $target, '--force' => true]);
        }

        $src = DB::connection($source);
        $dst = DB::connection($target);

        $tables = $this->option('tables')
            ? array_filter(array_map('trim', explode(',', $this->option('tables'))))
            : array_map(fn ($r) => array_values((array) $r)[0], $src->select("SHOW TABLES LIKE 'rg\\_%'"));

        $dst->statement('SET FOREIGN_KEY_CHECKS=0');
        $summary = [];

        try {
            foreach ($tables as $table) {
                if (! Schema::connection($target)->hasTable($table)) {
                    $this->warn("  skip {$table}: not on target (no migration creates it)");
                    $summary[] = [$table, '-', '-', 'missing on target'];
                    continue;
                }

                $srcCols = Schema::connection($source)->getColumnListing($table);
                $dstCols = Schema::connection($target)->getColumnListing($table);
                $cols = array_values(array_intersect($srcCols, $dstCols));
                $dropped = array_diff($srcCols, $dstCols);

                $dst->table($table)->truncate();

                $query = $src->table($table)->select($cols);
                if (in_array('id', $cols, true)) {
                    $query->orderBy('id');
                }

                $copied = 0;
                $batch = [];
                $bytes = 0;
                foreach ($query->cursor() as $row) {
                    $row = (array) $row;
                    $batch[] = $row;
                    $bytes += array_sum(array_map(fn ($v) => is_string($v) ? strlen($v) : 8, $row));
                    if ($bytes >= self::BATCH_BYTES || count($batch) >= 1000) {
                        $dst->table($table)->insert($batch);
                        $copied += count($batch);
                        $batch = [];
                        $bytes = 0;
                    }
                }
                if ($batch) {
                    $dst->table($table)->insert($batch);
                    $copied += count($batch);
                }

                $srcCount = $src->table($table)->count();
                $dstCount = $dst->table($table)->count();
                $note = $srcCount === $dstCount ? 'ok' : 'COUNT MISMATCH';
                if ($dropped) {
                    $note .= ' (source-only columns skipped: ' . implode(', ', $dropped) . ')';
                }
                $summary[] = [$table, $srcCount, $dstCount, $note];
                $this->line(sprintf('  %-28s %6d rows  %s', $table, $copied, $note));
            }
        } finally {
            $dst->statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // The Cloud app caches rg_settings and rendered fragments in the
        // database cache store; drop it so fresh data shows immediately.
        if (Schema::connection($target)->hasTable('cache')) {
            $dst->table('cache')->delete();
            $this->line('  cleared target cache table');
        }

        $this->newLine();
        $this->table(['table', 'source', 'target', 'status'], $summary);

        return collect($summary)->contains(fn ($r) => str_starts_with((string) $r[3], 'COUNT MISMATCH'))
            ? self::FAILURE
            : self::SUCCESS;
    }
}
