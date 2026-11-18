<?php
// Replace the destination photos flagged as wrong (dest/review-*.json) with a
// verified local photo of the same town. The file at the old path gets the new
// picture (so every page, cover and gallery that uses it is fixed at once), its
// rg_media row gets the new credit, and hero slides showing it get the credit too.
//   php dest_fix.php <destdir> [--apply]
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$dir = rtrim($argv[1], '/\\');
$apply = in_array('--apply', $argv, true);
$root = storage_path('app/public/');
$bad = [];
foreach (glob("$dir/review-*.json") as $f) foreach ((json_decode(file_get_contents($f), true)['bad'] ?? []) as $b) $bad[$b['file']] = $b['reason'] ?? '';
$badSet = $bad;

$stop = ['city', 'province', 'general', 'philippines', 'the', 'and', 'of', 'in', 'island', 'islands', 'san', 'santa', 'sta', 'mount'];
$tok = fn ($s) => array_values(array_diff(array_filter(preg_split('/[^a-z]+/', strtolower($s)), fn ($t) => strlen($t) >= 3), $stop));

// verified photos: the image-audit picks (places/) with credits, plus good destination photos
$verified = DB::table('rg_media')->where('source', 'image-audit-2026-11')->where('path', 'like', 'rg-media/places/%')->get(['path', 'filename', 'credit', 'source_url', 'width', 'height', 'size_bytes']);
$destMeta = DB::table('rg_media')->where('path', 'like', 'rg-media/destinations/%')->get()->keyBy('path');
$shared = DB::table('rg_tourist_spots')->whereNotNull('media_id')->groupBy('media_id')->select('media_id', DB::raw('count(*) c'))->pluck('c', 'media_id');
$spotPhotos = DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->where('s.status', 'published')
    ->get(['s.name', 's.location', 's.media_id', 'm.path', 'm.credit', 'm.source_url', 'm.filename'])
    ->filter(fn ($r) => ($shared[$r->media_id] ?? 1) < 2 && is_file(storage_path('app/public/' . $r->path)) && filesize(storage_path('app/public/' . $r->path)) > 25000)->values();
$used = [];
$plan = [];
foreach ($bad as $file => $reason) {
    $town = preg_replace('/-\d+$/', '', pathinfo($file, PATHINFO_FILENAME));
    $tt = $tok($town);
    $best = null; $bestScore = 0;
    $need = count($tt) <= 2 ? max(1, count($tt)) : count($tt) - 1;
    // 1) an audited Commons pick whose name mentions the town
    foreach ($verified as $v) {
        if (isset($used[$v->path])) continue;
        $vt = $tok(pathinfo($v->path, PATHINFO_FILENAME) . ' ' . $v->filename);
        $hit = count(array_intersect($tt, $vt));
        if ($hit >= $need && $hit > $bestScore) {
            $best = ['path' => $v->path, 'credit' => $v->credit, 'source_url' => $v->source_url, 'filename' => $v->filename, 'why' => 'audited pick'];
            $bestScore = $hit;
        }
    }
    // 2) a tourist-spot photo from that town (only photos used by one spot, so no placeholders)
    if (!$best) {
        foreach ($spotPhotos as $sp) {
            if (isset($used[$sp->path]) || str_contains($sp->path, 'rg-media/destinations/')) continue;
            $hit = count(array_intersect($tt, $tok($sp->name . ' ' . $sp->location . ' ' . pathinfo($sp->path, PATHINFO_FILENAME))));
            if ($hit >= $need && $hit > $bestScore) {
                $best = ['path' => $sp->path, 'credit' => $sp->credit ?: 'Photo: Wikimedia Commons', 'source_url' => $sp->source_url, 'filename' => $sp->filename ?: basename($sp->path), 'why' => 'spot photo: ' . $sp->name];
                $bestScore = $hit;
            }
        }
    }
    // 3) another good photo of the same town in destinations/
    if (!$best) {
        foreach (glob($root . 'rg-media/destinations/' . $town . '-*.jpg') as $g) {
            $b = basename($g);
            if (isset($badSet[$b]) || isset($used['rg-media/destinations/' . $b])) continue;
            $m = $destMeta['rg-media/destinations/' . $b] ?? null;
            $best = ['path' => 'rg-media/destinations/' . $b, 'credit' => $m->credit ?? 'Photo: Wikimedia Commons', 'source_url' => $m->source_url ?? null, 'filename' => $m->filename ?? $b, 'why' => 'same town'];
            break;
        }
    }
    // 4) the region's landmark photo (may repeat)
    if (!$best) {
        $words = implode(' ', $tt);
        $cluster = $words !== '' ? DB::table('rg_keywords')->where('phrase', 'like', '%' . $words . '%')->whereNotNull('cluster_tag')->value('cluster_tag') : null;
        $lm = $cluster ? 'rg-media/landmarks/' . $cluster . '.jpg' : null;
        if ($lm && is_file($root . $lm)) {
            $m = DB::table('rg_media')->where('path', $lm)->first();
            $best = ['path' => $lm, 'credit' => $m->credit ?? 'Photo: Wikimedia Commons', 'source_url' => $m->source_url ?? null, 'filename' => $m->filename ?? basename($lm), 'why' => 'region landmark'];
        }
    }
    $plan[] = ['file' => $file, 'reason' => $reason, 'town' => $town, 'replacement' => $best];
    if ($best) $used[$best['path']] = true;
}
// manual picks after the visual check of the before/after sheets
$overrides = is_file("$dir/overrides.json") ? json_decode(file_get_contents("$dir/overrides.json"), true) : [];
foreach ($plan as $i => $p) {
    if (!isset($overrides[$p['file']])) continue;
    $path = $overrides[$p['file']];
    $m = DB::table('rg_media')->where('path', $path)->first();
    $plan[$i]['replacement'] = ['path' => $path, 'credit' => $m->credit ?? 'Photo: Wikimedia Commons', 'source_url' => $m->source_url ?? null, 'filename' => $m->filename ?? basename($path), 'why' => 'manual'];
}
file_put_contents("$dir/fix-plan.json", json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$ok = array_values(array_filter($plan, fn ($p) => $p['replacement']));
echo 'bad: ', count($bad), ' | with replacement: ', count($ok), ' | none: ', count($plan) - count($ok), "\n";
foreach ($plan as $p) if (!$p['replacement']) echo '  none: ', $p['file'], ' (', $p['reason'], ")\n";

// before / after sheet
$W = 200; $H = 130; $T = 16;
foreach (array_chunk($ok, 16) as $si => $chunk) {
    $im = imagecreatetruecolor($W * 2 * 4, ($H + $T) * 4);
    $white = imagecolorallocate($im, 255, 255, 255); imagefill($im, 0, 0, $white); $black = imagecolorallocate($im, 0, 0, 0);
    foreach ($chunk as $i => $p) {
        $x = ($i % 4) * $W * 2; $y = intdiv($i, 4) * ($H + $T);
        foreach ([$root . 'rg-media/destinations/' . $p['file'], $root . $p['replacement']['path']] as $k => $f) {
            $src = @imagecreatefromstring(@file_get_contents($f));
            if ($src) imagecopyresampled($im, $src, $x + $k * $W, $y, 0, 0, $W - 3, $H, imagesx($src), imagesy($src));
        }
        imagestring($im, 2, $x + 2, $y + $H + 1, substr($p['file'] . ' -> ' . basename($p['replacement']['path']), 0, 64), $black);
    }
    imagejpeg($im, sprintf('%s/fix-sheet-%02d.jpg', $dir, $si + 1), 74);
}

if ($apply) {
    $backupDir = "$dir/originals-" . date('His');
    @mkdir($backupDir, 0775, true);
    $slides = 0;
    foreach ($ok as $p) {
        $dst = $root . 'rg-media/destinations/' . $p['file'];
        copy($dst, "$backupDir/" . $p['file']);
        copy($root . $p['replacement']['path'], $dst);
        [$w, $h] = @getimagesize($dst) ?: [null, null];
        DB::table('rg_media')->where('path', 'rg-media/destinations/' . $p['file'])->update([
            'filename' => $p['replacement']['filename'], 'credit' => $p['replacement']['credit'], 'source_url' => $p['replacement']['source_url'],
            'width' => $w, 'height' => $h, 'size_bytes' => filesize($dst), 'source' => 'image-audit-2026-11', 'updated_at' => now(),
        ]);
        // hero slides that show this file: carry the new credit
        foreach (DB::table('rg_content_blocks')->where('block_type', 'hero_slider')->where(fn ($q) => $q->where('payload_json', 'like', '%destinations/' . $p['file'] . '%')->orWhere('payload_json', 'like', '%destinations\\\\/' . $p['file'] . '%'))->get(['id', 'payload_json']) as $b) {
            $pl = json_decode($b->payload_json, true);
            $changed = false;
            foreach ($pl['images'] ?? [] as $i => $img) {
                if (str_ends_with((string) ($img['src'] ?? ''), 'destinations/' . $p['file'])) {
                    $pl['images'][$i]['caption'] = $p['replacement']['credit'];
                    $pl['images'][$i]['credit_url'] = $p['replacement']['source_url'];
                    $changed = true;
                }
            }
            if ($changed) { DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($pl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]); $slides++; }
        }
    }
    echo 'applied ', count($ok), ' files, ', $slides, " slide blocks re-credited; originals in $backupDir\n";
}
