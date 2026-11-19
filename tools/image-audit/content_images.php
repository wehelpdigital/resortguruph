<?php
// Which local image files do guide pages still show, and which of them have never
// been through a visual check? Writes <out>/unchecked.json.
//   php content_images.php <out>
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$out = rtrim($argv[1], '/\\');
@mkdir($out, 0775, true);
$root = storage_path('app/public/');
$refs = [];
foreach (DB::table('rg_content_blocks')->select('id', 'owner_type', 'payload_json')->cursor() as $b) {
    $s = str_replace('\\/', '/', $b->payload_json);
    if (!preg_match_all('~/storage/(rg-media/[a-z0-9_\-]+/[^"\'\s?)<>]+\.(?:jpe?g|png|webp))~i', $s, $m)) continue;
    foreach (array_unique($m[1]) as $p) { $refs[$p]['blocks'] = ($refs[$p]['blocks'] ?? 0) + 1; $refs[$p]['owners'][$b->owner_type] = true; }
}
// checked: audited picks, the reviewed destination photos, spot photos the spot audit kept or replaced
$audited = DB::table('rg_media')->where('source', 'image-audit-2026-11')->pluck('path')->flip();
$spotKept = DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->pluck('m.path')->flip();
$byFolder = []; $unchecked = [];
foreach ($refs as $p => $r) {
    $folder = explode('/', $p)[1] ?? '';
    $exists = is_file($root . $p);
    $checked = isset($audited[$p]) || $folder === 'destinations' || $folder === 'landmarks' || isset($spotKept[$p]);
    $byFolder[$folder][$checked ? 'checked' : 'unchecked'] = ($byFolder[$folder][$checked ? 'checked' : 'unchecked'] ?? 0) + 1;
    if (!$exists) $byFolder[$folder]['missing'] = ($byFolder[$folder]['missing'] ?? 0) + 1;
    if (!$checked && $exists) $unchecked[] = ['path' => $p, 'blocks' => $r['blocks'], 'owners' => array_keys($r['owners'])];
}
ksort($byFolder);
foreach ($byFolder as $f => $c) echo str_pad($f, 16), json_encode($c), "\n";
usort($unchecked, fn ($a, $b) => $b['blocks'] <=> $a['blocks']);
file_put_contents("$out/unchecked.json", json_encode($unchecked, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo 'referenced files: ', count($refs), ' | unchecked: ', count($unchecked), "\n";
