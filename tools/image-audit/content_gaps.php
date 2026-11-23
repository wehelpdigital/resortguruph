<?php
// Usage: php tools/image-audit/content_gaps.php <outdir>
// Writes the content that is missing or too thin to read as complete:
//   foods_hub.json        dishes on the foods hub without a researched description
//   foods_to_try.json     "Foods to try" items whose blurb is empty or one short line
//   places_short.json     attraction / spot items with an empty or one-line description
//   fiestas_short.json    festival pages with a thin summary
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$out = rtrim($argv[1], '/\\');
@mkdir($out, 0777, true);
$j = fn ($v) => json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// foods hub
$m = new ReflectionMethod(\App\Http\Controllers\FoodsController::class, 'categories');
$m->setAccessible(true);
$foods = [];
foreach ($m->invoke(app(\App\Http\Controllers\FoodsController::class)) as $cat) {
    foreach ($cat['items'] as $it) {
        if (mb_strlen(trim((string) ($it['description'] ?? ''))) >= 120) continue;
        $foods[] = ['slug' => $it['slug'], 'name' => $it['name'], 'where' => $it['where'] ?? '', 'category' => $cat['label'], 'note' => $it['note'] ?? ''];
    }
}
file_put_contents("$out/foods_hub.json", $j($foods));

// foods to try (block payloads), grouped by dish + place
$ftt = [];
foreach (DB::table('rg_content_blocks')->where('block_type', 'foods_to_try')->get(['id', 'payload_json']) as $b) {
    $p = json_decode($b->payload_json, true) ?: [];
    $place = preg_replace('~^Foods? to try in ~i', '', (string) ($p['heading'] ?? ''));
    foreach ($p['items'] ?? [] as $k => $it) {
        $blurb = trim((string) ($it['blurb'] ?? ''));
        if (mb_strlen($blurb) >= 90) continue;
        $key = mb_strtolower(trim($it['name'] ?? '')) . '|' . mb_strtolower($place);
        $ftt[$key] ??= ['name' => $it['name'] ?? '', 'place' => $place, 'where' => $it['where'] ?? '', 'blurb' => $blurb, 'refs' => []];
        $ftt[$key]['refs'][] = [$b->id, $k];
    }
}
file_put_contents("$out/foods_to_try.json", $j(array_values($ftt)));

// places with thin descriptions (attractions blurbs)
$pl = [];
foreach (DB::table('rg_content_blocks')->where('block_type', 'attractions')->get(['id', 'payload_json']) as $b) {
    $p = json_decode($b->payload_json, true) ?: [];
    $place = preg_replace("~^What['’]?s in |\\?$~i", '', (string) ($p['heading'] ?? ''));
    foreach ($p['items'] ?? [] as $k => $it) {
        $blurb = trim((string) ($it['blurb'] ?? ''));
        if (mb_strlen($blurb) >= 40) continue;
        $key = mb_strtolower(trim($it['name'] ?? '')) . '|' . mb_strtolower($place);
        $pl[$key] ??= ['name' => $it['name'] ?? '', 'place' => $place, 'short' => $it['short'] ?? '', 'blurb' => $blurb, 'refs' => []];
        $pl[$key]['refs'][] = [$b->id, $k];
    }
}
file_put_contents("$out/places_short.json", $j(array_values($pl)));

$fi = DB::table('rg_fiestas')->get(['id', 'slug', 'name', 'province', 'city_or_town', 'month', 'date_label', 'summary'])
    ->filter(fn ($f) => mb_strlen(trim((string) $f->summary)) < 120)->values();
file_put_contents("$out/fiestas_short.json", $j($fi));

echo 'foods hub: ', count($foods), ', foods to try: ', count($ftt), ' (', array_sum(array_map(fn ($x) => count($x['refs']), $ftt)), ' uses), places: ', count($pl), ' (', array_sum(array_map(fn ($x) => count($x['refs']), $pl)), ' uses), fiestas: ', count($fi), "\n";
