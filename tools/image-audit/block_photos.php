<?php
// Usage (project root): php tools/image-audit/block_photos.php <reason> <rel path> [<rel path> ...]
// Marks library photos that show something else (another place, a scanned page) as blocked in
// database/data/subject_photos.json: GuideHtml::isPlaceholder() then treats them like a missing
// photo everywhere on the guides, and tourist-spot library rows using them lose the photo.
// Paths like rg-media/spots/x.jpg.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
require __DIR__ . '/registry.php';

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

$reason = $argv[1] ?? '';
$reg = subject_registry_load();
$unlinked = [];
foreach (array_slice($argv, 2) as $rel) {
    $rel = ltrim(preg_replace('~^/?storage/~', '', $rel), '/');
    $reg['_blocked']['/storage/' . $rel] = $reason;
    // drop it from any subject that was registered with it
    foreach ($reg as $kind => &$map) {
        if ($kind === '_blocked') continue;
        foreach ($map as $key => $src) if ($src === '/storage/' . $rel) unset($map[$key]);
    }
    unset($map);
    foreach (DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->where('m.path', $rel)->get(['s.id', 's.media_id']) as $sp) {
        $unlinked[$sp->id] = $sp->media_id;
        DB::table('rg_tourist_spots')->where('id', $sp->id)->update(['media_id' => null, 'updated_at' => now()]);
    }
    echo "blocked $rel\n";
}
subject_registry_save($reg);
foreach (['gh_spot_library_v1', 'dest_showcase_v1'] as $k) Cache::forget($k);
if ($unlinked) echo 'unlinked tourist spots (id => old media_id): ' . json_encode($unlinked) . "\n";
