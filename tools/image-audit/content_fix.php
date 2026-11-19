<?php
// Replace the content photos flagged in content/review-*.json with a checked
// photo of the same subject (same folder first, then audited picks, town photos
// and spot photos). The old file gets the new picture, its rg_media row and any
// hero slide showing it get the new credit. Writes a before/after sheet.
//   php content_fix.php <contentdir> [--apply]
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$dir = rtrim($argv[1], '/\\');
$apply = in_array('--apply', $argv, true);
$root = storage_path('app/public/');
$bad = [];
foreach (glob("$dir/review-*.json") as $f) foreach ((json_decode(file_get_contents($f), true)['bad'] ?? []) as $b) if (!empty($b['file'])) $bad[$b['file']] = $b['reason'] ?? '';
// files left as they are after the visual check, and files that get no photo
$skip = is_file("$dir/skip.json") ? array_flip(json_decode(file_get_contents("$dir/skip.json"), true)) : [];
$forceNone = is_file("$dir/force_none.json") ? array_flip(json_decode(file_get_contents("$dir/force_none.json"), true)) : [];
$bad = array_diff_key($bad, $skip);
// byte-identical copies of a bad photo are bad too (one wrong download saved under several names)
$md5 = [];
foreach (['foods', 'food-locations', 'attractions', 'activities', 'adventures', 'buys', 'cultures', 'fiestas', 'restaurants', 'transport'] as $fold) {
    foreach (glob($root . 'rg-media/' . $fold . '/*.{jpg,jpeg,webp}', GLOB_BRACE) ?: [] as $f) $md5['rg-media/' . $fold . '/' . basename($f)] = md5_file($f);
}
$badHashes = [];
foreach (array_keys($bad) as $b) if (isset($md5[$b])) $badHashes[$md5[$b]] = $bad[$b];
foreach ($md5 as $p => $h) if (isset($badHashes[$h]) && !isset($bad[$p]) && !isset($skip[$p])) $bad[$p] = 'same file as a flagged photo: ' . $badHashes[$h];
$stop = array_flip(['the', 'and', 'of', 'in', 'at', 'city', 'province', 'philippines', 'general', 'san', 'santa', 'sta', 'mall', 'jpg']);
$tok = function ($s) use ($stop) {
    $t = preg_split('/[^a-z]+/', strtolower((string) $s));
    return array_values(array_unique(array_filter($t, fn ($x) => strlen($x) >= 3 && !isset($stop[$x]))));
};
$subject = fn ($path) => $tok(preg_replace('/-\d+$/', '', pathinfo($path, PATHINFO_FILENAME)));

// pools of checked photos
$media = DB::table('rg_media')->get(['id', 'path', 'filename', 'credit', 'source_url'])->keyBy('path');
$audited = DB::table('rg_media')->where('source', 'image-audit-2026-11')->pluck('path')->all();
$spotPaths = DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->get(['m.path', 's.name', 's.location']);
$shared = DB::table('rg_tourist_spots')->whereNotNull('media_id')->groupBy('media_id')->select('media_id', DB::raw('count(*) c'))->pluck('c', 'media_id');
$folderFiles = function ($folder) use ($root) {
    return array_map(fn ($f) => 'rg-media/' . $folder . '/' . basename($f), glob($root . 'rg-media/' . $folder . '/*.{jpg,jpeg,webp}', GLOB_BRACE) ?: []);
};
$pools = [
    'same' => null, // filled per item
    'audited' => array_map(fn ($p) => ['path' => $p, 'toks' => $tok(pathinfo($p, PATHINFO_FILENAME) . ' ' . ($media[$p]->filename ?? ''))], $audited),
    'dest' => array_map(fn ($p) => ['path' => $p, 'toks' => $tok(preg_replace('/-\d+$/', '', pathinfo($p, PATHINFO_FILENAME)))], $folderFiles('destinations')),
    'spots' => array_values(array_filter(array_map(fn ($r) => ['path' => $r->path, 'toks' => $tok($r->name . ' ' . $r->location . ' ' . pathinfo($r->path, PATHINFO_FILENAME))], $spotPaths->all()), fn ($r) => is_file($root . $r['path']))),
];
// place prefixes used in foods/<place>-<dish>.jpg names, longest first
$townPrefixes = array_unique(array_map(fn ($f) => preg_replace('/-\d+$/', '', pathinfo($f, PATHINFO_FILENAME)), glob($root . 'rg-media/destinations/*.jpg') ?: []));
usort($townPrefixes, fn ($a, $b) => strlen($b) <=> strlen($a));
$used = [];
$plan = [];
foreach ($bad as $file => $reason) {
    $folder = explode('/', $file)[1] ?? '';
    $st = $subject($file);
    if (!$st) { $plan[] = ['file' => $file, 'reason' => $reason, 'replacement' => null]; continue; }
    $need = count($st) <= 2 ? count($st) : count($st) - 1;
    if ($folder === 'foods') {
        // foods/<place>-<dish>: the dish must match exactly, the place may differ
        $name = pathinfo($file, PATHINFO_FILENAME);
        foreach ($townPrefixes as $pre) if (str_starts_with($name, $pre . '-')) { $name = substr($name, strlen($pre) + 1); break; }
        $st = $tok(preg_replace('/-\d+$/', '', $name));
        $need = count($st);
        if (!$st) { $plan[] = ['file' => $file, 'reason' => $reason, 'replacement' => null]; continue; }
    }
    $best = null; $bestHit = 0;
    $try = function (array $cands, string $why) use (&$best, &$bestHit, &$st, &$need, $bad, &$used, $root, $file) {
        foreach ($cands as $c) {
            if ($c['path'] === $file || isset($bad[$c['path']]) || isset($used[$c['path']]) || !is_file($root . $c['path']) || filesize($root . $c['path']) < 25000) continue;
            if (isset($GLOBALS['badHashes'][md5_file($root . $c['path'])])) continue;
            $hit = count(array_intersect($st, $c['toks']));
            if ($hit >= $need && $hit > $bestHit) { $best = $c['path'] . '|' . $why; $bestHit = $hit; }
        }
    };
    // 1) another photo of the same subject in the same folder
    $same = array_map(fn ($p) => ['path' => $p, 'toks' => $subject($p)], $folderFiles($folder));
    $try($same, 'same folder');
    if (!$best) $try($pools['audited'], 'audited pick');
    if (!$best && in_array($folder, ['food-locations', 'attractions', 'activities', 'adventures', 'cultures'], true)) { $try($pools['spots'], 'spot photo'); if (!$best) $try($pools['dest'], 'town photo'); }
    if (!$best && $folder === 'foods' && in_array('kinilaw', $st, true)) { // any kinilaw works for "kinilaw na tuna"
        $st = ['kinilaw']; $need = 1;
        $try($same, 'same folder (kinilaw)');
    }
    if (!$best) { // last resort: repeat a good photo of the same subject
        $saved = $used; $used = [];
        $try($same, 'same folder (repeat)');
        $used = $saved;
    }
    $rep = null;
    if ($best) {
        [$path, $why] = explode('|', $best, 2);
        $used[$path] = true;
        $m = $media[$path] ?? null;
        $rep = ['path' => $path, 'why' => $why, 'credit' => $m->credit ?? 'Photo: Wikimedia Commons', 'source_url' => $m->source_url ?? null, 'filename' => $m->filename ?? basename($path)];
    }
    if (isset($forceNone[$file])) { if ($rep) unset($used[$rep['path']]); $rep = null; }
    $plan[] = ['file' => $file, 'reason' => $reason, 'replacement' => $rep];
}
// manual picks after looking at the before/after sheets
$overrides = is_file("$dir/overrides.json") ? json_decode(file_get_contents("$dir/overrides.json"), true) : [];
foreach ($plan as $i => $p) {
    if (!isset($overrides[$p['file']])) continue;
    $path = $overrides[$p['file']];
    $m = $media[$path] ?? null;
    $plan[$i]['replacement'] = ['path' => $path, 'why' => 'manual', 'credit' => $m->credit ?? 'Photo: Wikimedia Commons', 'source_url' => $m->source_url ?? null, 'filename' => $m->filename ?? basename($path)];
}
file_put_contents("$dir/fix-plan.json", json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$ok =array_values(array_filter($plan, fn ($p) => $p['replacement']));
echo 'bad: ', count($bad), ' | with replacement: ', count($ok), ' | none: ', count($plan) - count($ok), "\n";

foreach (glob("$dir/fix-sheet-*.jpg") as $f) unlink($f);
$W = 200; $H = 130; $T = 16;
foreach (array_chunk($ok, 16) as $si => $chunk) {
    $im = imagecreatetruecolor($W * 2 * 4, ($H + $T) * 4);
    $white = imagecolorallocate($im, 255, 255, 255); imagefill($im, 0, 0, $white); $black = imagecolorallocate($im, 0, 0, 0);
    foreach ($chunk as $i => $p) {
        $x = ($i % 4) * $W * 2; $y = intdiv($i, 4) * ($H + $T);
        foreach ([$root . $p['file'], $root . $p['replacement']['path']] as $k => $f) {
            $src = @imagecreatefromstring(@file_get_contents($f));
            if ($src) imagecopyresampled($im, $src, $x + $k * $W, $y, 0, 0, $W - 3, $H, imagesx($src), imagesy($src));
        }
        imagestring($im, 2, $x + 2, $y + $H + 1, substr(basename($p['file']) . ' -> ' . basename($p['replacement']['path']), 0, 64), $black);
    }
    imagejpeg($im, sprintf('%s/fix-sheet-%02d.jpg', $dir, $si + 1), 74);
}

if ($apply) {
    $overrides = [];
    $bk = "$dir/originals-" . date('His');
    @mkdir($bk, 0775, true);
    $n = 0; $slides = 0;
    foreach ($ok as $p) {
        if (isset($overrides[$p['file']])) continue;
        $dst = $root . $p['file'];
        @mkdir(dirname("$bk/" . $p['file']), 0775, true);
        copy($dst, "$bk/" . $p['file']);
        copy($root . $p['replacement']['path'], $dst);
        [$w, $h] = @getimagesize($dst) ?: [null, null];
        DB::table('rg_media')->where('path', $p['file'])->update([
            'filename' => $p['replacement']['filename'], 'credit' => $p['replacement']['credit'], 'source_url' => $p['replacement']['source_url'],
            'width' => $w, 'height' => $h, 'size_bytes' => filesize($dst), 'source' => 'image-audit-2026-11', 'updated_at' => now(),
        ]);
        $base = basename($p['file']);
        foreach (DB::table('rg_content_blocks')->where('block_type', 'hero_slider')->where('payload_json', 'like', '%' . $base . '%')->get(['id', 'payload_json']) as $b) {
            $pl = json_decode($b->payload_json, true); $ch = false;
            foreach ($pl['images'] ?? [] as $i => $img) if (str_ends_with(str_replace('\\/', '/', (string) ($img['src'] ?? '')), $p['file'])) { $pl['images'][$i]['caption'] = $p['replacement']['credit']; $pl['images'][$i]['credit_url'] = $p['replacement']['source_url']; $ch = true; }
            if ($ch) { DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($pl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]); $slides++; }
        }
        $n++;
    }
    // no good photo: drop it from slideshows, show the neutral placeholder elsewhere
    $blockBackup = []; $dropped = 0; $placeheld = 0;
    $slash = chr(92) . '/';
    foreach ($plan as $p) {
        if ($p['replacement']) continue;
        $base = basename($p['file']);
        foreach (DB::table('rg_content_blocks')->where('payload_json', 'like', '%' . $base . '%')->get(['id', 'block_type', 'payload_json']) as $b) {
            $blockBackup[$b->id] = $blockBackup[$b->id] ?? $b->payload_json;
            $pl = json_decode($b->payload_json, true);
            if ($b->block_type === 'hero_slider' && !empty($pl['images'])) {
                $keep = array_values(array_filter($pl['images'], fn ($img) => !str_ends_with(str_replace($slash, '/', (string) ($img['src'] ?? '')), $p['file'])));
                if ($keep && count($keep) < count($pl['images'])) {
                    $pl['images'] = $keep; $dropped++;
                    DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($pl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
                }
                continue;
            }
            $plain = '/storage/' . $p['file'];
            $escaped = str_replace('/', $slash, $plain);
            $json = str_replace([$plain, $escaped], ['/images/placeholder-stay.svg', str_replace('/', $slash, '/images/placeholder-stay.svg')], $b->payload_json);
            if ($json !== $b->payload_json) { DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => $json, 'updated_at' => now()]); $placeheld++; }
        }
    }
    file_put_contents("$dir/blocks-backup-" . date('His') . '.json', json_encode($blockBackup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "applied $n files, $slides slide blocks re-credited; dropped from $dropped slideshows, placeholder in $placeheld blocks; originals in $bk\n";
}
