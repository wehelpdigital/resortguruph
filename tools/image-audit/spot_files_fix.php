<?php
// The spot audit pointed 147 tourist spots at a new, correct photo, but the old
// (wrong) spot photo files are still referenced from guide content (tourist-spot
// cards, attraction lists, image blocks). Put the correct photo into each old
// file too, and carry the credit. Only files named after that spot (spots/...)
// and used by no other spot are touched.
//   php spot_files_fix.php <imgdir> <outdir> [--apply]
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

[$imgDir, $out] = [rtrim($argv[1], '/\\'), rtrim($argv[2], '/\\')];
$apply = in_array('--apply', $argv, true);
@mkdir($out, 0775, true);
$root = storage_path('app/public/');
$old = [];
foreach (glob("$imgDir/apply-backup*.json") as $f) foreach ((json_decode(file_get_contents($f), true)['spots'] ?? []) as $sid => $mid) $old[(int) $sid] = (int) $mid;

$plan = [];
foreach ($old as $sid => $oldMid) {
    $spot = DB::table('rg_tourist_spots')->where('id', $sid)->first(['id', 'name', 'media_id']);
    if (!$spot || (int) $spot->media_id === $oldMid) continue;
    $from = DB::table('rg_media')->where('id', $spot->media_id)->first();
    $to = DB::table('rg_media')->where('id', $oldMid)->first();
    if (!$from || !$to || !str_starts_with($to->path, 'rg-media/spots/')) continue;
    if (DB::table('rg_tourist_spots')->where('media_id', $oldMid)->exists()) continue; // still used by another spot
    if (!is_file($root . $from->path) || !is_file($root . $to->path)) continue;
    $refs = DB::table('rg_content_blocks')->where('payload_json', 'like', '%' . basename($to->path) . '%')->count();
    $plan[] = ['spot' => $spot->name, 'old_file' => $to->path, 'new_file' => $from->path, 'old_media_id' => $oldMid, 'credit' => $from->credit, 'source_url' => $from->source_url, 'filename' => $from->filename, 'refs' => $refs];
}
file_put_contents("$out/spot-files-plan.json", json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo 'spots re-pointed by the audit: ', count($old), ' | old files to refresh: ', count($plan), ' | still referenced in content: ', count(array_filter($plan, fn ($p) => $p['refs'] > 0)), "\n";

if ($apply) {
    $bk = "$out/spot-originals-" . date('His');
    @mkdir($bk, 0775, true);
    $slides = 0;
    foreach ($plan as $p) {
        copy($root . $p['old_file'], "$bk/" . basename($p['old_file']));
        copy($root . $p['new_file'], $root . $p['old_file']);
        [$w, $h] = @getimagesize($root . $p['old_file']) ?: [null, null];
        DB::table('rg_media')->where('id', $p['old_media_id'])->update([
            'filename' => $p['filename'], 'credit' => $p['credit'], 'source_url' => $p['source_url'], 'width' => $w, 'height' => $h,
            'size_bytes' => filesize($root . $p['old_file']), 'source' => 'image-audit-2026-11', 'updated_at' => now(),
        ]);
        $base = basename($p['old_file']);
        foreach (DB::table('rg_content_blocks')->where('block_type', 'hero_slider')->where('payload_json', 'like', '%' . $base . '%')->get(['id', 'payload_json']) as $b) {
            $pl = json_decode($b->payload_json, true); $ch = false;
            foreach ($pl['images'] ?? [] as $i => $img) if (str_ends_with((string) ($img['src'] ?? ''), '/' . $base)) { $pl['images'][$i]['caption'] = $p['credit']; $pl['images'][$i]['credit_url'] = $p['source_url']; $ch = true; }
            if ($ch) { DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($pl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]); $slides++; }
        }
    }
    echo 'refreshed ', count($plan), " files, $slides slide blocks re-credited; originals in $bk\n";
}
