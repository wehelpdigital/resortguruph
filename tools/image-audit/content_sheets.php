<?php
// Contact sheets for content/unchecked.json: 30 per sheet, captioned with the
// file path and the original Commons file name. php content_sheets.php <contentdir>
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$dir = rtrim($argv[1], '/\\');
$root = storage_path('app/public/');
$list = json_decode(file_get_contents("$dir/unchecked.json"), true);
usort($list, fn ($a, $b) => strcmp($a['path'], $b['path']));
$meta = DB::table('rg_media')->whereIn('path', array_column($list, 'path'))->pluck('filename', 'path');
$items = [];
foreach ($list as $i => $r) $items[] = ['n' => $i + 1, 'file' => $r['path'], 'orig' => (string) ($meta[$r['path']] ?? ''), 'blocks' => $r['blocks']];
file_put_contents("$dir/items.json", json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$W = 260; $H = 170; $T = 34;
foreach (array_chunk($items, 30) as $si => $chunk) {
    $im = imagecreatetruecolor($W * 5, ($H + $T) * 6);
    $white = imagecolorallocate($im, 255, 255, 255); imagefill($im, 0, 0, $white); $black = imagecolorallocate($im, 0, 0, 0);
    foreach ($chunk as $i => $r) {
        $x = ($i % 5) * $W; $y = intdiv($i, 5) * ($H + $T);
        $src = @imagecreatefromstring(@file_get_contents($root . $r['file']));
        if ($src) imagecopyresampled($im, $src, $x, $y, 0, 0, $W - 4, $H, imagesx($src), imagesy($src));
        $label = '#' . $r['n'] . ' ' . preg_replace('~^rg-media/~', '', $r['file']);
        imagestring($im, 2, $x + 2, $y + $H + 2, substr($label, 0, 42), $black);
        imagestring($im, 1, $x + 2, $y + $H + 18, substr($r['orig'], 0, 50), $black);
    }
    imagejpeg($im, sprintf('%s/sheet-%02d.jpg', $dir, $si + 1), 72);
}
echo count($items), ' items, ', ceil(count($items) / 30), " sheets\n";
