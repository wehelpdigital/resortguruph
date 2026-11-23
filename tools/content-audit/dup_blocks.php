<?php
/**
 * The same block twice on one page (a guide with its "Local history" block repeated,
 * an image block added twice by the importer). Keeps the first and removes the copies.
 *
 *   php tools/content-audit/dup_blocks.php            dry run
 *   php tools/content-audit/dup_blocks.php --apply    write (old rows saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$groups = DB::table('rg_content_blocks')
    ->whereIn('block_type', ['place_history', 'image', 'data_table', 'text_section', 'custom_html', 'faq', 'pros_cons', 'foods_to_try', 'attractions', 'local_tip', 'quick_facts', 'facts_list'])
    ->select('owner_type', 'owner_id', 'block_type', 'payload_json', DB::raw('count(*) c'), DB::raw('min(id) keep_id'))
    ->groupBy('owner_type', 'owner_id', 'block_type', 'payload_json')->having('c', '>', 1)->get();
$backup = [];
$byType = [];
foreach ($groups as $g) {
    $copies = DB::table('rg_content_blocks')->where('owner_type', $g->owner_type)->where('owner_id', $g->owner_id)->where('block_type', $g->block_type)
        ->where('payload_json', $g->payload_json)->where('id', '!=', $g->keep_id)->get();
    foreach ($copies as $b) $backup[] = (array) $b;
    $byType[$g->block_type] = ($byType[$g->block_type] ?? 0) + $copies->count();
    if ($apply) DB::table('rg_content_blocks')->whereIn('id', $copies->pluck('id')->all())->delete();
}
echo count($backup) . " repeated blocks: " . json_encode($byType) . "\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/dup-blocks-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
}
