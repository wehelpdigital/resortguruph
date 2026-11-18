<?php

namespace App\Console\Commands;

use App\Support\ContentSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Refresh database/snapshots/content from the current (shared) DB.
 * The snapshot seeds a brand-new production database on deploy; for an
 * existing production DB use `php artisan rg:cloud-sync-db` instead.
 */
class ContentSnapshotExport extends Command
{
    protected $signature = 'rg:content-snapshot {--dir= : Output directory (default database/snapshots/content)}';

    protected $description = 'Export public site content (rg_* tables, personal data stripped) to a gzipped snapshot';

    public function handle(): int
    {
        $dir = $this->option('dir') ?: base_path(ContentSnapshot::DIR);
        $this->info('Exporting from ' . config('database.connections.' . config('database.default') . '.host') . ' to ' . $dir);
        $counts = ContentSnapshot::export(DB::connection(), $dir, fn ($m) => $this->line('  ' . $m));
        $size = array_sum(array_map('filesize', glob($dir . '/*.gz') ?: []));
        $this->info(sprintf('Done: %d tables, %s rows, %.1f MB compressed.', count($counts), number_format(array_sum($counts)), $size / 1048576));
        return self::SUCCESS;
    }
}
