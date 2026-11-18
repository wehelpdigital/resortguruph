<?php

namespace App\Support;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Public site content packed as gzipped NDJSON (one row per line), so a
 * brand-new database (Laravel Cloud) can be filled during `migrate` with
 * no credentials or manual import step.
 *
 *  - export: `php artisan rg:content-snapshot` (reads the default DB).
 *  - import: migration 2026_11_19_000000_import_content_snapshot, which
 *    only runs the import when rg_keywords is empty.
 *
 * Deliberately NOT shipped (the repo is public): audit logs (IPs), Gold
 * Points ledger / holds / top-ups, bid history, password-reset tokens.
 * Owner accounts are included only so listings keep their foreign keys;
 * their passwords and remember tokens are replaced with random values so
 * nobody can sign in as them.
 */
class ContentSnapshot
{
    public const DIR = 'database/snapshots/content';

    /** Tables in a foreign-key friendly order. */
    public const TABLES = [
        'rg_owners',
        'rg_keywords',
        'rg_media',
        'rg_authors',
        'rg_seo_pages',
        'rg_static_pages',
        'rg_content_blocks',
        'rg_blog_posts',
        'rg_blog_comments',
        'rg_destination_reviews',
        'rg_tourist_spots',
        'rg_fiestas',
        'rg_tags',
        'rg_resorts',
        'rg_resort_media',
        'rg_listings',
        'rg_restaurants',
        'rg_restaurant_media',
        'rg_restaurant_listings',
        'rg_adventures',
        'rg_adventure_media',
        'rg_adventure_listings',
        'rg_partners',
        'rg_settings',
    ];

    /** Hosts that only exist on a developer machine. */
    private const LOCAL_HOSTS = ['resortguruph.test', '127.0.0.1:8001', 'localhost:8001', '127.0.0.1:8000', 'localhost:8000'];

    /**
     * Turn absolute links to the local dev host into root-relative ones,
     * in both plain and JSON-escaped (http:\/\/host\/) form.
     */
    public static function rewriteLocalUrls(string $s): string
    {
        if ($s === '' || !preg_match('~resortguruph\.test|(?:127\.0\.0\.1|localhost):800[01]~', $s)) {
            return $s;
        }
        foreach (self::LOCAL_HOSTS as $host) {
            $h = preg_quote($host, '~');
            // http://host/path  ->  /path      (and the JSON-escaped twin)
            $s = preg_replace('~https?://' . $h . '(?=/)~i', '', $s);
            $s = preg_replace('~https?:\\\\/\\\\/' . $h . '(?=\\\\/)~i', '', $s);
            // bare http://host  ->  /
            $s = preg_replace('~https?://' . $h . '(?![\w./-])~i', '/', $s);
            $s = preg_replace('~https?:\\\\/\\\\/' . $h . '(?![\w./\\\\-])~i', '\\/', $s);
        }
        return $s;
    }

    public static function rewriteRow(array $row): array
    {
        foreach ($row as $k => $v) {
            if (is_string($v)) {
                $row[$k] = self::rewriteLocalUrls($v);
            }
        }
        return $row;
    }

    private static function sanitizeRow(string $table, array $row): array
    {
        if ($table === 'rg_owners') {
            $row['password'] = password_hash(Str::random(40), PASSWORD_BCRYPT);
            $row['remember_token'] = null;
        }
        return $row;
    }

    /**
     * Write one <table>.ndjson.gz per table plus manifest.json.
     *
     * @return array<string,int> rows written per table
     */
    public static function export(Connection $src, string $dir, ?callable $log = null): array
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $counts = [];
        foreach (self::TABLES as $table) {
            if (!Schema::connection($src->getName())->hasTable($table)) {
                continue;
            }
            $gz = gzopen("{$dir}/{$table}.ndjson.gz", 'wb9');
            $n = 0;
            $q = $src->table($table);
            if (Schema::connection($src->getName())->hasColumn($table, 'id')) {
                $q->orderBy('id');
            }
            foreach ($q->cursor() as $row) {
                $row = self::sanitizeRow($table, self::rewriteRow((array) $row));
                gzwrite($gz, json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) . "\n");
                $n++;
            }
            gzclose($gz);
            $counts[$table] = $n;
            if ($log) $log("{$table}: {$n}");
        }
        file_put_contents("{$dir}/manifest.json", json_encode([
            'generated_at' => now()->toIso8601String(),
            'tables' => $counts,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        return $counts;
    }

    /**
     * Load every table file into $dst. Target tables are emptied first.
     *
     * @return array<string,int> rows inserted per table
     */
    public static function import(Connection $dst, string $dir, ?callable $log = null): array
    {
        $manifest = json_decode((string) @file_get_contents("{$dir}/manifest.json"), true);
        if (!is_array($manifest) || empty($manifest['tables'])) {
            if ($log) $log('No content snapshot found, skipping.');
            return [];
        }

        $schema = Schema::connection($dst->getName());
        $schema->disableForeignKeyConstraints();
        $counts = [];
        try {
            foreach (array_keys($manifest['tables']) as $table) {
                $file = "{$dir}/{$table}.ndjson.gz";
                if (!is_file($file) || !$schema->hasTable($table)) {
                    continue;
                }
                $cols = array_flip($schema->getColumnListing($table));
                $dst->table($table)->truncate();

                $gz = gzopen($file, 'rb');
                $batch = [];
                $bytes = 0;
                $n = 0;
                while (($line = gzgets($gz)) !== false) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $row = json_decode($line, true);
                    if (!is_array($row)) continue;
                    $row = array_intersect_key($row, $cols);
                    foreach ($row as $k => $v) {
                        if (is_array($v)) $row[$k] = json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                    $batch[] = $row;
                    $bytes += strlen($line);
                    if ($bytes > 2_000_000 || count($batch) >= 500) {
                        $dst->table($table)->insert($batch);
                        $n += count($batch);
                        $batch = [];
                        $bytes = 0;
                    }
                }
                gzclose($gz);
                if ($batch) {
                    $dst->table($table)->insert($batch);
                    $n += count($batch);
                }
                $counts[$table] = $n;
                if ($log) $log("{$table}: {$n}");
            }
        } finally {
            $schema->enableForeignKeyConstraints();
        }

        // Anything cached while the tables were empty is now stale.
        if ($schema->hasTable('cache')) {
            $dst->table('cache')->delete();
        }

        return $counts;
    }
}
