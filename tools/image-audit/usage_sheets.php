<?php
// Usage (project root): php tools/image-audit/usage_sheets.php <outDir> <path prefix, e.g. /storage/rg-media/commons/>
// Contact sheets of every photo under the prefix that blog posts or hero sliders show, captioned
// with the alt/caption texts it is published with, for a "does the photo match its caption" review.
// Writes <outDir>/figs.json ({n: {src, alts[], posts[]}}) and sheet-NN.png (12 per sheet).
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

[$_, $out, $prefix] = $argv + [null, null, '/storage/'];
$out = rtrim($out, '/\\');
@mkdir($out, 0777, true);
$root = storage_path('app/public/');
$figs = [];
$add = function ($src, $alt, $where) use (&$figs, $prefix) {
    $src = preg_replace('~^https?://[^/]+~', '', html_entity_decode((string) $src));
    if (!str_starts_with($src, $prefix)) return;
    $alt = trim(html_entity_decode((string) $alt, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($alt !== '') $figs[$src]['alts'][mb_strtolower($alt)] = $alt;
    $figs[$src]['posts'][$where] = true;
};
foreach (DB::table('rg_blog_posts')->get(['slug', 'content_html']) as $p) {
    preg_match_all('~<img[^>]*src="([^"]+)"[^>]*alt="([^"]*)"~', (string) $p->content_html, $m, PREG_SET_ORDER);
    foreach ($m as $x) $add($x[1], $x[2], 'blog:' . $p->slug);
}
$slugs = DB::table('rg_seo_pages')->pluck('slug', 'id');
foreach (DB::table('rg_content_blocks')->where('block_type', 'hero_slider')->get(['owner_type', 'owner_id', 'payload_json']) as $b) {
    foreach ((json_decode($b->payload_json, true)['images'] ?? []) as $im) {
        $add($im['src'] ?? '', trim(($im['caption_title'] ?? '') ?: ($im['alt'] ?? '')), 'hero:' . ($b->owner_type === 'seo_page' ? ($slugs[$b->owner_id] ?? $b->owner_id) : $b->owner_type . $b->owner_id));
    }
}
ksort($figs);
$list = [];
$n = 0;
foreach ($figs as $src => $f) $list[++$n] = ['src' => $src, 'alts' => array_values($f['alts'] ?? []), 'posts' => array_keys($f['posts'])];
file_put_contents("$out/figs.json", json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$CW = 400; $CH = 260; $TXT = 58; $cols = 4; $per = 12;
foreach (array_chunk($list, $per, true) as $si => $chunk) {
    $img = imagecreatetruecolor($cols * ($CW + 8), (int) ceil(count($chunk) / $cols) * ($CH + $TXT + 10));
    $white = imagecolorallocate($img, 255, 255, 255); $black = imagecolorallocate($img, 0, 0, 0); $yel = imagecolorallocate($img, 255, 214, 0);
    imagefill($img, 0, 0, $white);
    $i = 0;
    foreach ($chunk as $num => $f) {
        $x = ($i % $cols) * ($CW + 8); $y = intdiv($i, $cols) * ($CH + $TXT + 10);
        $file = $root . rawurldecode(substr($f['src'], 9));
        $t = is_file($file) ? @imagecreatefromstring(file_get_contents($file)) : false;
        if ($t) {
            $sw = imagesx($t); $sh = imagesy($t); $sc = max($CW / $sw, $CH / $sh); $cw = (int) ($CW / $sc); $chh = (int) ($CH / $sc);
            imagecopyresampled($img, $t, $x, $y, (int) (($sw - $cw) / 2), (int) (($sh - $chh) / 2), $CW, $CH, $cw, $chh);
        } else {
            imagestring($img, 5, $x + 120, $y + 120, 'missing file', $black);
        }
        imagefilledrectangle($img, $x, $y, $x + 12 * strlen((string) $num) + 14, $y + 20, $yel);
        imagestring($img, 5, $x + 5, $y + 3, '#' . $num, $black);
        $txt = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', implode(' / ', array_slice($f['alts'], 0, 3)) ?: basename($f['src'])) ?: '';
        foreach (str_split(mb_strimwidth($txt, 0, 195, '..'), 65) as $li => $line) imagestring($img, 3, $x + 2, $y + $CH + 3 + $li * 14, $line, $black);
        $i++;
    }
    imagepng($img, sprintf('%s/sheet-%02d.png', $out, $si + 1));
}
echo count($list), ' photos on ', (int) ceil(count($list) / $per), " sheets\n";
