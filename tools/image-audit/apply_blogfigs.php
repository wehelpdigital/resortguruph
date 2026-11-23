<?php
// Usage (project root): php tools/image-audit/apply_blogfigs.php <subjects.json> <candDir> <sheetsDir> [--dry]
// Applies the reviewed picks for "blogfig" subjects (one per blog photo + caption that a review
// found wrong): "cur" keeps the photo, L*/C* swaps it for the picked photo (Commons photos are
// downloaded once at 1280px, the credit is added to the figure caption), "none" removes that
// figure from the posts. Only <img> tags with that src AND that alt text are touched.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

[$_, $sfile, $cdir, $sdir] = $argv + [null, null, null, null];
$dry = in_array('--dry', $argv, true);
$root = storage_path('app/public/');
$UA = 'image-audit-script/1.0 (generic client)';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: $UA\r\n", 'timeout' => 60, 'ignore_errors' => true]]);

$subjects = [];
foreach (json_decode(file_get_contents($sfile), true) as $s) if ($s['kind'] === 'blogfig') $subjects[$s['id']] = $s;
$cands = [];
foreach (glob("$cdir/cand-*.jsonl") as $f) foreach (file($f) as $line) { $r = json_decode($line, true); if ($r) $cands[$r['id']] = $r['candidates']; }
$cells = [];
foreach (glob("$sdir/sheets.json") as $mf) foreach (json_decode(file_get_contents($mf), true) as $rows) foreach ($rows as $row) $cells[$row['id']] = $row['cells'];
$picks = [];
foreach (glob("$sdir/picks/*.json") as $pf) foreach (json_decode(file_get_contents($pf), true) ?: [] as $p) {
    if (isset($p['id'], $p['pick']) && isset($subjects[(int) $p['id']])) $picks[(int) $p['id']] = trim((string) $p['pick']);
}

function fetch_photo(string $url, $ctx)
{
    for ($a = 0; $a < 5; $a++) {
        $d = @file_get_contents($url, false, $ctx);
        $st = 0;
        foreach (($http_response_header ?? []) as $h) if (preg_match('~^HTTP/\S+\s+(\d+)~', $h, $m)) $st = (int) $m[1];
        if ($d !== false && $st === 200 && strlen($d) > 3000) return $d;
        if ($st && $st !== 429 && $st < 500) return false;
        sleep(10 * ($a + 1));
    }
    return false;
}

$swap = []; $remove = []; $kept = 0; $failed = 0;
$backup = ['blogs' => [], 'files' => [], 'media' => []];
foreach ($picks as $id => $pick) {
    $s = $subjects[$id];
    $p = strtolower($pick);
    if ($p === 'cur') { $kept++; continue; }
    if ($p === 'none' || $p === '') { $remove[] = $s; continue; }
    $cell = $cells[$id][strtoupper($pick)] ?? null;
    if (!$cell) { $failed++; fwrite(STDERR, "#$id no cell $pick\n"); continue; }
    if (isset($cell['local'])) { $swap[] = [$s, $cell['local'], '']; continue; }
    $c = $cands[$id][$cell['commons']] ?? null;
    if (!$c) { $failed++; continue; }
    $rel = 'rg-media/blog-photos/' . Str::limit(Str::slug($s['name']), 80, '') . '.jpg';
    if (!$dry && !is_file($root . $rel)) {
        $base = preg_replace('~\?.*$~', '', (string) $c['thumb']);
        $bucket = $c['w'] >= 1280 ? 1280 : ($c['w'] >= 960 ? 960 : 0);
        $data = fetch_photo($bucket ? preg_replace('~/\d+px-~', '/' . $bucket . 'px-', $base) : preg_replace('~\?.*$~', '', (string) $c['orig']), $ctx);
        $im = $data ? @imagecreatefromstring($data) : false;
        if (!$im) { $failed++; fwrite(STDERR, "#$id download failed\n"); continue; }
        if (imagesx($im) > 1280) $im = imagescale($im, 1280, -1, IMG_BICUBIC);
        @mkdir(dirname($root . $rel), 0775, true);
        imagejpeg($im, $root . $rel, 82);
        $backup['files'][] = $rel;
        $artist = Str::limit(preg_replace('/\s+/', ' ', $c['artist'] ?: 'Unknown author'), 80);
        $credit = "Photo: {$artist}, Wikimedia Commons ({$c['license']})";
        if (!DB::table('rg_media')->where('path', $rel)->exists()) {
            $backup['media'][] = DB::table('rg_media')->insertGetId([
                'filename' => Str::limit($c['title'], 250, ''), 'path' => $rel, 'mime' => 'image/jpeg', 'size_bytes' => filesize($root . $rel),
                'kind' => 'image', 'width' => imagesx($im), 'height' => imagesy($im), 'alt' => Str::limit($s['name'], 250, ''),
                'caption' => Str::limit($s['name'], 250, ''), 'source' => 'subject-photo', 'credit' => Str::limit($credit, 250, ''),
                'source_url' => $c['page'], 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $swap[] = [$s, '/storage/' . $rel, $credit];
        usleep(300000);
    } else {
        $swap[] = [$s, '/storage/' . $rel, ''];
    }
}
echo 'swap ', count($swap), ', remove ', count($remove), ", keep $kept, failed $failed\n";
if ($dry) exit;

$esc = fn ($s) => htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$posts = [];
foreach (array_merge($swap, array_map(fn ($s) => [$s, null, ''], $remove)) as [$s, $new, $credit]) {
    foreach ($s['pages'] as $slug) $posts[$slug][] = [$s, $new, $credit];
}
foreach ($posts as $slug => $ops) {
    $row = DB::table('rg_blog_posts')->where('slug', $slug)->first(['id', 'content_html']);
    if (!$row) continue;
    $html = (string) $row->content_html;
    foreach ($ops as [$s, $new, $credit]) {
        $cur = preg_quote($s['current'], '~');
        $alts = array_unique([preg_quote($s['name'], '~'), preg_quote($esc($s['name']), '~'), preg_quote(htmlspecialchars($s['name'], ENT_QUOTES), '~')]);
        $altRe = '(?:' . implode('|', $alts) . ')';
        $imgRe = '<img(?=[^>]*\bsrc="(?:https?://[^/"]+)?' . $cur . '")(?=[^>]*\balt="' . $altRe . '")[^>]*>';
        if ($new === null) {
            // drop the whole figure (or the lone img)
            $html = preg_replace('~<figure\b[^>]*>(?:(?!</figure>).)*?' . $imgRe . '.*?</figure>~is', '', $html, -1, $c1);
            if (!$c1) $html = preg_replace('~' . $imgRe . '~i', '', $html);
        } else {
            $html = preg_replace_callback('~(<figure\b[^>]*>(?:(?!</figure>).)*?)(' . $imgRe . ')(.*?</figure>)~is', function ($m) use ($s, $new, $credit, $esc) {
                $img = preg_replace('~\bsrc="[^"]*"~', 'src="' . $esc($new) . '"', $m[2]);
                $tail = $m[3];
                if ($credit !== '') $tail = preg_replace('~</figcaption>~i', ' <span class="rg-fig__credit">' . $esc($credit) . '</span></figcaption>', $tail, 1);
                return $m[1] . $img . $tail;
            }, $html, -1, $c2);
            if (!$c2) $html = preg_replace_callback('~' . $imgRe . '~i', fn ($m) => preg_replace('~\bsrc="[^"]*"~', 'src="' . $esc($new) . '"', $m[0]), $html);
        }
    }
    if ($html !== $row->content_html) {
        $backup['blogs'][$row->id] = $row->content_html;
        DB::table('rg_blog_posts')->where('id', $row->id)->update(['content_html' => $html, 'updated_at' => now()]);
    }
}
@mkdir(storage_path('app/image-audit'), 0775, true);
file_put_contents(storage_path('app/image-audit/blogfigs-backup-' . date('Ymd-His') . '.json'), json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo 'posts changed: ', count($backup['blogs']), "\n";
