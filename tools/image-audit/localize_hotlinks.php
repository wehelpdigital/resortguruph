<?php
// Usage (project root): php tools/image-audit/localize_hotlinks.php [--dry]
// Blog posts and hero sliders still hotlink Wikimedia Commons (Special:FilePath/... or
// upload.wikimedia.org). Downloads each photo once (1280px thumbnail bucket, built from the
// file name hash so no API call is needed), stores it under storage/app/public/rg-media/commons,
// records it in rg_media, then rewrites the content to the local /storage path. Writes a backup.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$dry = in_array('--dry', $argv, true);
$root = storage_path('app/public/');
$UA = 'image-audit-script/1.0 (generic client)';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: $UA\r\n", 'timeout' => 60, 'ignore_errors' => true]]);
$re = '~https?://(?:commons\.wikimedia\.org/wiki/Special:FilePath/|upload\.wikimedia\.org/wikipedia/commons/(?:thumb/)?[0-9a-f]/[0-9a-f]{2}/)([^"\'\s<>?)]+)(?:\?[^"\'\s<>)]*)?~';

$urls = [];
foreach (DB::table('rg_blog_posts')->get(['id', 'content_html']) as $p) {
    preg_match_all($re, (string) $p->content_html, $m, PREG_SET_ORDER);
    foreach ($m as $x) $urls[$x[0]] = $x[1];
}
foreach (DB::table('rg_content_blocks')->where('payload_json', 'like', '%wikimedia.org%')->get(['payload_json']) as $b) {
    preg_match_all($re, str_replace('\\/', '/', (string) $b->payload_json), $m, PREG_SET_ORDER);
    foreach ($m as $x) $urls[$x[0]] = $x[1];
}
echo count($urls), " hotlinked URLs\n";

function get_ok(string $url, $ctx)
{
    for ($a = 0; $a < 4; $a++) {
        $d = @file_get_contents($url, false, $ctx);
        $st = 0;
        foreach (($http_response_header ?? []) as $h) if (preg_match('~^HTTP/\S+\s+(\d+)~', $h, $m)) $st = (int) $m[1];
        if ($d !== false && $st === 200 && strlen($d) > 3000) return $d;
        if ($st && $st !== 429 && $st < 500) return false;
        sleep(8 * ($a + 1));
    }
    return false;
}

$map = []; // url => local src
$backup = ['media' => [], 'files' => [], 'blogs' => [], 'blocks' => []];
foreach ($urls as $url => $namePart) {
    // "Casa_de_Segunda_%28Lipa%29.jpg" (thumb URLs end with "/1280px-Name": keep the file name only)
    $name = str_replace(' ', '_', rawurldecode(explode('/', $namePart)[0]));
    $name = mb_strtoupper(mb_substr($name, 0, 1)) . mb_substr($name, 1);
    $hash = md5($name);
    $enc = rawurlencode($name);
    $rel = 'rg-media/commons/' . Str::limit(Str::slug(pathinfo($name, PATHINFO_FILENAME)), 90, '') . '.jpg';
    $existing = DB::table('rg_media')->where('path', $rel)->value('id');
    if ($existing && is_file($root . $rel)) { $map[$url] = '/storage/' . $rel; continue; }
    if ($dry) { $map[$url] = '/storage/' . $rel; continue; }
    $data = false;
    foreach ([1280, 960, 500] as $w) {
        $data = get_ok("https://thumb.wikimedia.org/wikipedia/commons/thumb/{$hash[0]}/" . substr($hash, 0, 2) . "/$enc/{$w}px-$enc", $ctx);
        if ($data) break;
    }
    if (!$data) $data = get_ok("https://upload.wikimedia.org/wikipedia/commons/{$hash[0]}/" . substr($hash, 0, 2) . "/$enc", $ctx);
    $im = $data ? @imagecreatefromstring($data) : false;
    if (!$im) { fwrite(STDERR, "failed: $name\n"); continue; }
    if (imagesx($im) > 1280) $im = imagescale($im, 1280, -1, IMG_BICUBIC);
    @mkdir(dirname($root . $rel), 0775, true);
    imagejpeg($im, $root . $rel, 82);
    $backup['files'][] = $rel;
    if (!$existing) {
        $backup['media'][] = DB::table('rg_media')->insertGetId([
            'filename' => Str::limit(str_replace('_', ' ', $name), 250, ''), 'path' => $rel, 'mime' => 'image/jpeg',
            'size_bytes' => filesize($root . $rel), 'kind' => 'image', 'width' => imagesx($im), 'height' => imagesy($im),
            'alt' => Str::limit(str_replace('_', ' ', pathinfo($name, PATHINFO_FILENAME)), 250, ''), 'caption' => null,
            'source' => 'commons-localized', 'credit' => 'Photo: Wikimedia Commons',
            'source_url' => 'https://commons.wikimedia.org/wiki/File:' . $enc, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
    $map[$url] = '/storage/' . $rel;
    usleep(200000);
}
echo count($map), " stored locally\n";
if ($dry) exit;

// rewrite the content (longest URLs first so a URL that prefixes another is not cut)
uksort($map, fn ($a, $b) => strlen($b) <=> strlen($a));
$from = array_keys($map); $to = array_values($map);
foreach (DB::table('rg_blog_posts')->where('content_html', 'like', '%wikimedia.org%')->get(['id', 'content_html']) as $p) {
    $new = str_replace($from, $to, $p->content_html);
    $new = str_replace(array_map(fn ($u) => htmlspecialchars($u, ENT_QUOTES), $from), $to, $new);
    if ($new !== $p->content_html) { $backup['blogs'][$p->id] = $p->content_html; DB::table('rg_blog_posts')->where('id', $p->id)->update(['content_html' => $new, 'updated_at' => now()]); }
}
$escFrom = array_map(fn ($u) => str_replace('/', '\\/', $u), $from);
$escTo = array_map(fn ($u) => str_replace('/', '\\/', $u), $to);
foreach (DB::table('rg_content_blocks')->where('payload_json', 'like', '%wikimedia.org%')->get(['id', 'payload_json']) as $b) {
    $new = str_replace($escFrom, $escTo, str_replace($from, $to, $b->payload_json));
    if ($new !== $b->payload_json && json_decode($new) !== null) { $backup['blocks'][$b->id] = $b->payload_json; DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => $new, 'updated_at' => now()]); }
}
@mkdir(storage_path('app/image-audit'), 0775, true);
file_put_contents(storage_path('app/image-audit/localize-backup-' . date('Ymd-His') . '.json'), json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo 'rewritten: ', count($backup['blogs']), ' blog posts, ', count($backup['blocks']), " blocks\n";
