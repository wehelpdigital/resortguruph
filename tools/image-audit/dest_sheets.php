<?php
// Contact sheets of every rg-media/destinations/*.jpg with its town and the
// original Commons file name, for a visual check. Usage: php dest_sheets.php <outdir>
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$out = rtrim($argv[1], '/\\');
@mkdir($out, 0775, true);
$root = storage_path('app/public/');
$meta = DB::table('rg_media')->where('path', 'like', 'rg-media/destinations/%')->get(['path', 'filename'])->keyBy('path');
$items = [];
foreach (glob($root . 'rg-media/destinations/*.jpg') as $f) {
    $rel = 'rg-media/destinations/' . basename($f);
    $items[] = ['file' => basename($f), 'town' => preg_replace('/-\d+$/', '', pathinfo($f, PATHINFO_FILENAME)), 'orig' => $meta[$rel]->filename ?? ''];
}
file_put_contents("$out/items.json", json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$W = 260; $H = 170; $T = 34;
foreach (array_chunk($items, 30) as $si => $chunk) {
    $im = imagecreatetruecolor($W * 5, ($H + $T) * 6);
    $white = imagecolorallocate($im, 255, 255, 255); imagefill($im, 0, 0, $white); $black = imagecolorallocate($im, 0, 0, 0);
    foreach ($chunk as $i => $r) {
        $x = ($i % 5) * $W; $y = intdiv($i, 5) * ($H + $T);
        $src = @imagecreatefromstring(@file_get_contents($root . 'rg-media/destinations/' . $r['file']));
        if ($src) imagecopyresampled($im, $src, $x, $y, 0, 0, $W - 4, $H, imagesx($src), imagesy($src));
        imagestring($im, 3, $x + 2, $y + $H + 2, pathinfo($r['file'], PATHINFO_FILENAME), $black);
        imagestring($im, 1, $x + 2, $y + $H + 18, substr($r['orig'], 0, 50), $black);
    }
    imagejpeg($im, sprintf('%s/dest-sheet-%02d.jpg', $out, $si + 1), 72);
}
echo count($items), " images\n";
