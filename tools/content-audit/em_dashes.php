<?php
/**
 * House style has no em dashes. Replaces the ones left in stored content
 * (tourist spots, reviews, content blocks) with a comma.
 *
 *   php tools/content-audit/em_dashes.php            dry run
 *   php tools/content-audit/em_dashes.php --apply    write (old rows saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$fix = fn (string $s) => (string) preg_replace(['~\s*—\s*~u'], [', '], $s);
$backup = [];
$n = 0;

foreach (['rg_tourist_spots' => ['name', 'description'], 'rg_destination_reviews' => ['review_text'], 'rg_blog_posts' => ['title', 'excerpt', 'content_html'], 'rg_fiestas' => ['name', 'summary']] as $table => $cols) {
    foreach ($cols as $col) {
        foreach (DB::table($table)->where($col, 'like', '%—%')->get(['id', $col]) as $r) {
            $backup[] = ['table' => $table, 'id' => $r->id, $col => $r->$col];
            $n++;
            if ($apply) DB::table($table)->where('id', $r->id)->update([$col => $fix((string) $r->$col)]);
        }
    }
}
// JSON columns (blog "at a glance" tiles, FAQ answers): em dashes are stored as — there
foreach (['rg_blog_posts' => ['wwww_json'], 'rg_seo_pages' => ['wwww_json', 'faq_json', 'schema_json']] as $table => $cols) {
    foreach ($cols as $col) {
        foreach (DB::table($table)->where(fn ($q) => $q->where($col, 'like', '%—%')->orWhere($col, 'like', '%\\\\u2014%'))->get(['id', $col]) as $r) {
            $d = json_decode((string) $r->$col, true);
            if (!is_array($d)) continue;
            array_walk_recursive($d, function (&$v) use ($fix) { if (is_string($v) && str_contains($v, '—')) $v = $fix($v); });
            $backup[] = ['table' => $table, 'id' => $r->id, $col => $r->$col];
            $n++;
            if ($apply) DB::table($table)->where('id', $r->id)->update([$col => json_encode($d, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        }
    }
}
foreach (['rg_blog_posts' => ['tldr', 'meta_description'], 'rg_seo_pages' => ['tldr', 'subtitle', 'meta_description']] as $table => $cols) {
    foreach ($cols as $col) {
        foreach (DB::table($table)->where($col, 'like', '%—%')->get(['id', $col]) as $r) {
            $backup[] = ['table' => $table, 'id' => $r->id, $col => $r->$col];
            $n++;
            if ($apply) DB::table($table)->where('id', $r->id)->update([$col => $fix((string) $r->$col)]);
        }
    }
}
foreach (DB::table('rg_content_blocks')->where('payload_json', 'like', '%—%')->orWhere('payload_json', 'like', '%\\\\u2014%')->get() as $b) {
    $p = json_decode($b->payload_json, true);
    if (!is_array($p)) continue;
    array_walk_recursive($p, function (&$v) use ($fix) { if (is_string($v) && str_contains($v, '—')) $v = $fix($v); });
    $backup[] = (array) $b;
    $n++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}
echo "$n rows with em dashes\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/em-dashes-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
}
