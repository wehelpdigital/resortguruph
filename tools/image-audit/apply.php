<?php
// Usage (from project root): php apply.php <img dir> [--dry]
// Reads work-*/picks.json + their r1/r2 candidates.json, downloads chosen Commons photos,
// stores them in storage/app/public/rg-media/places|blog-covers, records credit in rg_media,
// and repoints only the wrong usages. Writes a rollback backup to <img dir>/apply-backup.json.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$dir = rtrim($argv[1], '/\\');
$dry = in_array('--dry', $argv, true);
$subjects = json_decode(file_get_contents("$dir/subjects_indexed.json"), true);
$UA = 'image-audit-script/1.0 (generic client)';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: $UA\r\n", 'timeout' => 60]]);

$norm = function ($src) {
    $src = trim((string) $src);
    if ($src === '') return '';
    $src = preg_replace('~^https?://(resortguruph\.test|127\.0\.0\.1:800\d|localhost:800\d)~', '', $src);
    if (preg_match('~^https?://~', $src)) return $src;
    $src = '/' . ltrim($src, '/');
    if (!str_starts_with($src, '/storage/')) $src = '/storage/' . ltrim($src, '/');
    return $src;
};
$clean = fn ($s) => trim(html_entity_decode(strip_tags((string) $s)));

// ---- collect picks
$picks = [];
foreach (array_merge(glob("$dir/work-*/picks.json") ?: [], glob("$dir/work-spots/s*/picks.json") ?: []) as $pf) {
    $w = dirname($pf);
    $cands = [];
    foreach (['r1', 'r2'] as $r) if (is_file("$w/$r/candidates.json")) $cands[$r] = json_decode(file_get_contents("$w/$r/candidates.json"), true);
    foreach (json_decode(file_get_contents($pf), true) as $p) {
        if (($p['decision'] ?? '') !== 'pick') continue;
        if (in_array((int) $p['id'], array_map('intval', array_filter(explode(',', (string) getenv('EXCLUDE')))), true)) continue;
        $round = $p['round'] ?? 'r1';
        $c = $cands[$round][$p['id']]['candidates'][((int) $p['k']) - 1] ?? null;
        if (!$c) { fwrite(STDERR, "missing candidate for #{$p['id']} {$round}.{$p['k']}\n"); continue; }
        $picks[(int) $p['id']] = $c;
    }
}
echo 'picks: ', count($picks), $dry ? ' (dry run)' : '', "\n";

$backup = ['blocks' => [], 'spots' => [], 'fiestas' => [], 'blogs' => [], 'media_ids' => []];
$newPath = [];   // id => ['path'=>..., 'credit'=>..., 'page'=>..., 'media_id'=>...]

// ---- download + store
foreach ($picks as $id => $c) {
    $s = $subjects[$id];
    $isBlog = str_starts_with($s['subject'], 'Blog: ');
    $slug = Str::limit(Str::slug($isBlog ? substr($s['subject'], 6) : $s['subject']), 80, '');
    $rel = ($isBlog ? 'rg-media/blog-covers/' : 'rg-media/places/') . $slug . '.jpg';
    // Wikimedia serves thumbnails in standard buckets (/330px-, /1280px-...): ask for 1280.
    $thumbBase = preg_replace('~\?.*$~', '', (string) $c['thumb']);
    // originals on upload.wikimedia.org are rate limited, so prefer a standard thumb bucket
    $bucket = $c['w'] > 1280 ? 1280 : ($c['w'] > 960 ? 960 : 0);
    $url = ($bucket && preg_match('~/\d+px-~', $thumbBase)) ? preg_replace('~/\d+px-~', '/' . $bucket . 'px-', $thumbBase) : preg_replace('~\?.*$~', '', (string) $c['orig']);
    $artist = Str::limit(preg_replace('/\s+/', ' ', $c['artist'] ?: 'Unknown author'), 80);
    $credit = "Photo: {$artist}, Wikimedia Commons ({$c['license']})";
    $existing = DB::table('rg_media')->where('path', $rel)->where('source', 'image-audit-2026-11')->value('id');
    if (!$dry && $existing && is_file(storage_path("app/public/$rel"))) {
        // already stored by an earlier run: reuse it
        $newPath[$id] = ['path' => $rel, 'src' => '/storage/' . $rel, 'credit' => $credit, 'page' => $c['page'], 'media_id' => $existing];
        continue;
    }
    if (!$dry) {
        $data = false;
        foreach ([0, 8, 25] as $wait) {
            if ($wait) { fwrite(STDERR, "  retry #$id in {$wait}s
"); sleep($wait); }
            $data = @file_get_contents($url, false, $ctx);
            if ($data !== false && strlen($data) > 5000) break;
            $data = false;
        }
        if ($data === false) { fwrite(STDERR, "download failed #$id $url\n"); continue; }
        $im = @imagecreatefromstring($data);
        if (!$im) { fwrite(STDERR, "decode failed #$id\n"); continue; }
        if (imagesx($im) > 1280) $im = imagescale($im, 1280, -1, IMG_BICUBIC);
        @mkdir(dirname(storage_path("app/public/$rel")), 0775, true);
        imagejpeg($im, storage_path("app/public/$rel"), 82);
        $mediaId = DB::table('rg_media')->insertGetId([
            'filename' => Str::limit($c['title'], 250, ''), 'path' => $rel, 'mime' => 'image/jpeg',
            'size_bytes' => filesize(storage_path("app/public/$rel")), 'kind' => 'image',
            'width' => imagesx($im), 'height' => imagesy($im), 'alt' => Str::limit($isBlog ? substr($s['subject'], 6) : $s['subject'], 250, ''),
            'caption' => Str::limit($s['subject'], 250, ''), 'source' => 'image-audit-2026-11', 'credit' => Str::limit($credit, 250, ''),
            'source_url' => $c['page'], 'created_at' => now(), 'updated_at' => now(),
        ]);
        $backup['media_ids'][] = $mediaId;
        usleep(600000);
    }
    $newPath[$id] = ['path' => $rel, 'src' => '/storage/' . $rel, 'credit' => $credit, 'page' => $c['page'], 'media_id' => $mediaId ?? null];
}
echo 'stored: ', count($newPath), "\n";

// subject label -> id, and the wrong srcs per subject
$bySubject = [];
foreach ($newPath as $id => $np) $bySubject[$subjects[$id]['subject']] = $id;
$wrongSrcs = fn ($id) => array_map($norm, $subjects[$id]['srcs']);
// spot subjects are labelled "Name (Location)"; attraction cards and slides use the bare name
foreach ($newPath as $id => $np) {
    if (!empty($subjects[$id]['spot_id'])) {
        $bare = trim(preg_replace('/\s*\([^)]*\)\s*$/', '', $subjects[$id]['subject']));
        if ($bare !== '' && !isset($bySubject[$bare])) $bySubject[$bare] = $id;
    }
}

// ---- repoint block payloads
$changedBlocks = 0; $changedSlots = 0;
DB::transaction(function () use (&$changedBlocks, &$changedSlots, &$backup, $bySubject, $newPath, $wrongSrcs, $norm, $clean, $dry) {
    $titles = DB::table('rg_seo_pages')->pluck('title', 'id')->all();
    foreach (DB::table('rg_content_blocks')->select('id', 'owner_type', 'owner_id', 'block_type', 'payload_json')->orderBy('id')->cursor() as $b) {
        $p = json_decode($b->payload_json, true); if (!is_array($p)) continue;
        $page = $b->owner_type === 'seo_page' ? ($titles[$b->owner_id] ?? '') : '';
        $hits = 0;
        $fix = function (array &$node, string $imgKey, string $label) use (&$hits, $bySubject, $newPath, $wrongSrcs, $norm) {
            $id = $bySubject[$label] ?? null;
            if (!$id || !isset($node[$imgKey]) || !in_array($norm($node[$imgKey]), $wrongSrcs($id), true)) return;
            $node[$imgKey] = $newPath[$id]['src'];
            if (array_key_exists('credit_url', $node)) $node['credit_url'] = $newPath[$id]['page'];
            if (isset($node['caption']) && preg_match('~photo|commons|wikimedia~i', (string) $node['caption'])) {
                $node['caption'] = $newPath[$id]['credit'];
            }
            $hits++;
        };
        if ($b->block_type === 'hero_slider' && isset($p['images']) && is_array($p['images'])) {
            foreach ($p['images'] as &$im) if (is_array($im)) $fix($im, 'src', $clean(($im['caption_title'] ?? '') ?: ($im['alt'] ?? $page)));
            unset($im);
        } elseif ($b->block_type === 'image') {
            $fix($p, 'src', $clean(($p['alt'] ?? '') ?: $page));
        } else {
            $walk = function (array &$node) use (&$walk, $fix, $clean, $page) {
                foreach (['image', 'image_src', 'img', 'src', 'cover', 'photo', 'background_image'] as $k) {
                    if (isset($node[$k]) && is_string($node[$k]) && preg_match('~\.(jpe?g|png|webp|gif|avif)(\?|$)~i', $node[$k])) {
                        $label = '';
                        foreach (['name', 'title', 'caption_title', 'heading', 'alt', 'image_alt', 'label'] as $lk) if (!empty($node[$lk]) && is_string($node[$lk])) { $label = $node[$lk]; break; }
                        $fix($node, $k, $clean($label ?: $page));
                        break;
                    }
                }
                foreach ($node as &$v) if (is_array($v)) $walk($v);
                unset($v);
            };
            $walk($p);
        }
        if ($hits) {
            $changedBlocks++; $changedSlots += $hits;
            $backup['blocks'][$b->id] = $b->payload_json;
            if (!$dry) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
        }
    }
});
echo "blocks changed: $changedBlocks (slots: $changedSlots)\n";

// ---- tourist spots, fiestas, blog covers
foreach (DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->select('s.id', 's.name', 's.location', 's.media_id', 'm.path')->get() as $r) {
    $id = $bySubject["$r->name ($r->location)"] ?? null;
    if ($id && in_array($norm($r->path), $wrongSrcs($id), true) && $newPath[$id]['media_id']) {
        $backup['spots'][$r->id] = $r->media_id;
        if (!$dry) DB::table('rg_tourist_spots')->where('id', $r->id)->update(['media_id' => $newPath[$id]['media_id'], 'updated_at' => now()]);
    }
}
foreach (DB::table('rg_fiestas')->select('id', 'name', 'province', 'cover_image_path')->get() as $r) {
    $id = $bySubject["$r->name festival ($r->province)"] ?? null;
    if ($id && in_array($norm($r->cover_image_path), $wrongSrcs($id), true)) {
        $backup['fiestas'][$r->id] = $r->cover_image_path;
        if (!$dry) DB::table('rg_fiestas')->where('id', $r->id)->update(['cover_image_path' => $newPath[$id]['path'], 'updated_at' => now()]);
    }
}
foreach (DB::table('rg_blog_posts')->select('id', 'title', 'cover_path')->get() as $r) {
    $id = $bySubject["Blog: $r->title"] ?? null;
    if ($id && in_array($norm($r->cover_path), $wrongSrcs($id), true)) {
        $backup['blogs'][$r->id] = $r->cover_path;
        if (!$dry) DB::table('rg_blog_posts')->where('id', $r->id)->update(['cover_path' => $newPath[$id]['path'], 'updated_at' => now()]);
    }
}
echo 'spots: ', count($backup['spots']), ', fiestas: ', count($backup['fiestas']), ', blog covers: ', count($backup['blogs']), "\n";
if (!$dry) file_put_contents("$dir/apply-backup-" . date('His') . ".json", json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
