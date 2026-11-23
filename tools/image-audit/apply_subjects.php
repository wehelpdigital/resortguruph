<?php
// Usage (project root): php tools/image-audit/apply_subjects.php <subjects.json> <candDir> <sheetsDir> [--dry]
// Applies the reviewed picks (<sheetsDir>/picks/*.json: [{"id":12,"pick":"C2"|"L1"|"cur"|"none"}]).
// Each picked photo is stored once (Commons photos downloaded at 1280px with author/licence
// credit) and registered in rg_media with source "subject-photo" + meta {kind,name,place}.
// The pages pick them up through GuideHtml::subjectPhoto(); fiesta covers, hub item photos
// and tourist-spot library photos are updated directly. Writes a rollback backup.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
require __DIR__ . '/registry.php';

use App\Support\GuideHtml;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

[$_, $sfile, $cdir, $sdir] = $argv + [null, null, null, null];
$dry = in_array('--dry', $argv, true);
$root = storage_path('app/public/');
$UA = 'image-audit-script/1.0 (generic client)';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: $UA\r\n", 'timeout' => 60, 'ignore_errors' => true]]);

$subjects = [];
foreach (json_decode(file_get_contents($sfile), true) as $s) $subjects[$s['id']] = $s;
$cands = [];
foreach (glob("$cdir/cand-*.jsonl") as $f) foreach (file($f) as $line) { $r = json_decode($line, true); if ($r) $cands[$r['id']] = $r['candidates']; }
$cells = [];
foreach (json_decode(file_get_contents("$sdir/sheets.json"), true) as $rows) foreach ($rows as $row) $cells[$row['id']] = $row['cells'];
$picks = [];
foreach (glob("$sdir/picks/*.json") as $pf) foreach (json_decode(file_get_contents($pf), true) ?: [] as $p) {
    if (!isset($p['id'], $p['pick'])) continue;
    $picks[(int) $p['id']] = strtoupper(trim((string) $p['pick'])) === 'CUR' ? 'cur' : strtoupper(trim((string) $p['pick']));
}
$folder = ['place' => 'places', 'food' => 'foods', 'hub-foods' => 'foods', 'festival' => 'fiestas', 'fiesta' => 'fiestas',
    'hub-cultures' => 'cultures', 'hub-buys' => 'buys', 'hub-activities' => 'activities'];
$hubDir = ['hub-foods' => 'foods', 'hub-cultures' => 'cultures', 'hub-buys' => 'buys', 'hub-activities' => 'activities'];

function fetchUrl(string $url, $ctx)
{
    for ($a = 0; $a < 6; $a++) {
        $d = @file_get_contents($url, false, $ctx);
        $st = 0;
        foreach (($http_response_header ?? []) as $h) if (preg_match('~^HTTP/\S+\s+(\d+)~', $h, $m)) $st = (int) $m[1];
        if ($d !== false && $st === 200 && strlen($d) > 5000) return $d;
        if ($st && $st !== 429 && $st < 500) return false;
        sleep(10 * ($a + 1));
    }
    return false;
}

$backup = ['media' => [], 'files' => [], 'fiestas' => [], 'spots' => [], 'registry_before' => subject_registry_load()];
$done = 0; $skipped = 0; $failed = 0;
$reg = subject_registry_load();
$unlicensed = [];

foreach ($picks as $id => $pick) {
    $s = $subjects[$id] ?? null;
    if (!$s || $pick === 'NONE' || $pick === '') { $skipped++; continue; }
    $regKind = in_array($s['kind'], ['food', 'hub-foods'], true) ? 'food' : ($s['kind'] === 'fiesta' ? 'festival' : $s['kind']);
    if (isset($reg[$regKind][GuideHtml::nameKey($s['name']) . '|' . ($regKind === 'food' ? '' : GuideHtml::nameKey($s['place']))])) { $skipped++; continue; }
    $cell = $cells[$id][$pick] ?? null;
    if (!$cell) { fwrite(STDERR, "#$id: no cell $pick\n"); $failed++; continue; }
    $dirName = $folder[$s['kind']] ?? 'places';
    $credit = ''; $sourceUrl = ''; $filename = '';
    if (isset($cell['local'])) {
        $rel = ltrim(substr(preg_replace('~^https?://[^/]+~', '', $cell['local']), 9), '/');
        if (!is_file($root . $rel)) { fwrite(STDERR, "#$id: missing local $rel\n"); $failed++; continue; }
        $meta = DB::table('rg_media')->where('path', $rel)->orderByDesc('id')->first(['credit', 'source_url', 'filename']);
        $credit = (string) ($meta->credit ?? ''); $sourceUrl = (string) ($meta->source_url ?? ''); $filename = (string) ($meta->filename ?? basename($rel));
        // library files without a known free licence (some hub photos came from recipe blogs)
        // are not spread to more pages; those subjects wait for a Commons photo instead
        if (!preg_match('~wikimedia|commons|cc[ -]?by|cc0|public domain|pexels|unsplash|pixabay~i', $credit . ' ' . $sourceUrl)) {
            $unlicensed[] = $id;
            $skipped++;
            continue;
        }
        [$w, $h] = @getimagesize($root . $rel) ?: [0, 0];
    } else {
        $c = $cands[$id][$cell['commons']] ?? null;
        if (!$c) { fwrite(STDERR, "#$id: no candidate\n"); $failed++; continue; }
        $base = preg_replace('~\?.*$~', '', (string) $c['thumb']);
        $bucket = $c['w'] >= 1280 ? 1280 : ($c['w'] >= 960 ? 960 : 0);
        $url = $bucket ? preg_replace('~/\d+px-~', '/' . $bucket . 'px-', $base) : preg_replace('~\?.*$~', '', (string) $c['orig']);
        $slug = Str::limit(Str::slug($s['name'] . ($s['kind'] === 'place' && $s['place'] !== '' ? ' ' . $s['place'] : '')), 90, '');
        $rel = "rg-media/$dirName/$slug.jpg";
        for ($n = 2; is_file($root . $rel) || DB::table('rg_media')->where('path', $rel)->exists(); $n++) $rel = "rg-media/$dirName/$slug-$n.jpg";
        if ($dry) { $done++; continue; }
        $data = fetchUrl($url, $ctx);
        $im = $data ? @imagecreatefromstring($data) : false;
        if (!$im) { fwrite(STDERR, "#$id: download failed $url\n"); $failed++; continue; }
        if (imagesx($im) > 1280) $im = imagescale($im, 1280, -1, IMG_BICUBIC);
        @mkdir(dirname($root . $rel), 0775, true);
        imagejpeg($im, $root . $rel, 82);
        $backup['files'][] = $rel;
        $w = imagesx($im); $h = imagesy($im);
        $artist = Str::limit(preg_replace('/\s+/', ' ', $c['artist'] ?: 'Unknown author'), 80);
        $credit = "Photo: {$artist}, Wikimedia Commons ({$c['license']})";
        $sourceUrl = (string) $c['page']; $filename = (string) $c['title'];
        usleep(400000);
    }
    if ($dry) { $done++; continue; }
    // one rg_media row per file (path is unique): new Commons downloads get theirs with the credit
    $mid = DB::table('rg_media')->where('path', $rel)->value('id');
    if (!$mid) {
        $mid = DB::table('rg_media')->insertGetId([
            'filename' => Str::limit($filename, 250, ''), 'path' => $rel, 'mime' => 'image/jpeg',
            'size_bytes' => @filesize($root . $rel) ?: 0, 'kind' => 'image', 'width' => $w ?? null, 'height' => $h ?? null,
            'alt' => Str::limit($s['name'], 250, ''), 'caption' => Str::limit($s['name'], 250, ''),
            'source' => 'subject-photo', 'credit' => Str::limit($credit, 250, ''), 'source_url' => Str::limit($sourceUrl, 500, ''),
            'meta_json' => json_encode(['kind' => $s['kind'], 'name' => $s['name'], 'place' => $s['place'], 'subject' => $id], JSON_UNESCAPED_UNICODE),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $backup['media'][] = $mid;
    }
    subject_registry_put($reg, $regKind, $s['name'], $s['place'], '/storage/' . $rel);
    // hub cards read <hub folder>/<slug>-1.jpg
    if (isset($hubDir[$s['kind']]) && !empty($s['hub_slug'])) {
        $dest = "rg-media/{$hubDir[$s['kind']]}/{$s['hub_slug']}-1.jpg";
        if (!is_file($root . $dest)) { copy($root . $rel, $root . $dest); $backup['files'][] = $dest; }
    }
    // festival pages without a cover
    if ($s['kind'] === 'fiesta' && !empty($s['fiesta_id'])) {
        $old = DB::table('rg_fiestas')->where('id', $s['fiesta_id'])->value('cover_image_path');
        if (!$old || preg_match('~destinations/|placeholder~', (string) $old) || GuideHtml::isPlaceholder('/storage/' . ltrim((string) $old, '/'))) {
            $backup['fiestas'][$s['fiesta_id']] = $old;
            DB::table('rg_fiestas')->where('id', $s['fiesta_id'])->update(['cover_image_path' => $rel, 'updated_at' => now()]);
        }
    }
    $done++;
}

// tourist-spot library rows without an own photo get the checked photo of the same place
if (!$dry) {
    subject_registry_save($reg);
    foreach (DB::table('rg_tourist_spots as s')->leftJoin('rg_media as m', 'm.id', '=', 's.media_id')->get(['s.id', 's.name', 's.location', 's.media_id', 'm.path']) as $sp) {
        if ($sp->media_id && $sp->path && !preg_match('~destinations/|placeholder~', $sp->path) && !GuideHtml::isPlaceholder('/storage/' . $sp->path)) continue;
        $src = $reg['place'][GuideHtml::nameKey($sp->name) . '|' . GuideHtml::nameKey((string) $sp->location)] ?? null;
        $mid = $src ? DB::table('rg_media')->where('path', ltrim(substr($src, 9), '/'))->value('id') : null;
        if (!$mid) continue;
        $backup['spots'][$sp->id] = $sp->media_id;
        DB::table('rg_tourist_spots')->where('id', $sp->id)->update(['media_id' => $mid, 'updated_at' => now()]);
    }
    file_put_contents("$sdir/apply-backup-" . date('Ymd-His') . '.json', json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    foreach (['gh_spot_library_v1', 'dest_showcase_v1'] as $k) Cache::forget($k);
}
file_put_contents("$sdir/unlicensed.json", json_encode($unlicensed));
echo ($dry ? 'dry run: ' : '') . "registered $done, skipped $skipped (no known licence: " . count($unlicensed) . "), failed $failed; fiesta covers " . count($backup['fiestas']) . ', library spots ' . count($backup['spots']) . "\n";
