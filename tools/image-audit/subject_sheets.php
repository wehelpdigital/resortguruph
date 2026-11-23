<?php
// Usage: php tools/image-audit/subject_sheets.php <subjects.json> <candDir> <outDir> [onlyIdsFile]
// Review sheets for subjects.php + fetch_subjects.php: one row per subject with the photo it
// shows now ("cur"), matching files already in the library (L1..L3) and Commons candidates
// (C1..C4). 6 subjects per sheet. Writes <outDir>/sheet-NNN.png and <outDir>/sheets.json
// (sheet => [{id, cells: {label: source}}]) for apply_subjects.php.
[$_, $sfile, $cdir, $out] = $argv + [null, null, null, null];
$only = isset($argv[4]) && is_file($argv[4]) ? array_flip(json_decode(file_get_contents($argv[4]), true)) : null;
@mkdir($out, 0777, true);
$root = realpath(__DIR__ . '/../../storage/app/public') . '/';
$subjects = [];
foreach (json_decode(file_get_contents($sfile), true) as $s) $subjects[$s['id']] = $s;
$cands = [];
foreach (glob("$cdir/cand-*.jsonl") as $f) foreach (file($f) as $line) { $r = json_decode($line, true); if ($r) $cands[$r['id']] = $r['candidates']; }

$rows = [];
foreach ($subjects as $id => $s) {
    if ($only !== null && !isset($only[$id])) continue;
    if (!isset($cands[$id]) && !$s['local']) continue;          // not fetched yet
    $cells = [];
    if ($s['current'] !== '' && ($s['kind'] === 'blogfig' || !preg_match('~/rg-media/destinations/~', $s['current']))) $cells['cur'] = ['local' => $s['current']];
    foreach (array_slice($s['local'], 0, 3) as $k => $l) if ($l !== $s['current']) $cells['L' . ($k + 1)] = ['local' => $l];
    foreach (array_slice($cands[$id] ?? [], 0, 4) as $k => $c) if (!empty($c['thumb_file'])) $cells['C' . ($k + 1)] = ['commons' => $k, 'file' => $c['thumb_file']];
    if (!getenv('CUR_ONLY') && !array_filter(array_keys($cells), fn ($l) => $l !== 'cur')) continue; // nothing new to choose from
    $rows[] = ['id' => $id, 'cells' => $cells];
}

$CW = 236; $CH = 158; $HDR = 36; $GAP = 6; $per = 6; $cols = 7;
$W = $cols * ($CW + $GAP); $font = 5;
$manifest = [];
foreach (array_chunk($rows, $per) as $n => $chunk) {
    $img = imagecreatetruecolor($W, count($chunk) * ($HDR + $CH + 14));
    $white = imagecolorallocate($img, 255, 255, 255); $black = imagecolorallocate($img, 0, 0, 0);
    $yel = imagecolorallocate($img, 255, 214, 0); $blue = imagecolorallocate($img, 40, 110, 220); $red = imagecolorallocate($img, 210, 40, 40); $grey = imagecolorallocate($img, 120, 120, 120); $line = imagecolorallocate($img, 200, 200, 200);
    imagefill($img, 0, 0, $white);
    foreach ($chunk as $ri => $row) {
        $s = $subjects[$row['id']];
        $y = $ri * ($HDR + $CH + 14);
        if ($ri) imageline($img, 0, $y - 6, $W, $y - 6, $line);
        $title = '#' . $row['id'] . '  [' . $s['kind'] . ']  ' . $s['name'] . ($s['place'] !== '' ? '  --  ' . $s['place'] : '');
        imagestring($img, $font, 4, $y + 2, mb_strimwidth(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title) ?: $title, 0, 190, '..'), $black);
        imagestring($img, 3, 4, $y + 19, mb_strimwidth(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s['desc']) ?: '', 0, 260, '..'), $grey);
        $ci = 0;
        foreach ($row['cells'] as $label => $cell) {
            $x = $ci * ($CW + $GAP);
            $path = isset($cell['local']) ? $root . rawurldecode(substr(preg_replace('~^https?://[^/]+~', '', $cell['local']), 9)) : $cell['file'];
            $t = is_file($path) ? @imagecreatefromstring(file_get_contents($path)) : false;
            if ($t) {
                $sw = imagesx($t); $sh = imagesy($t); $sc = max($CW / $sw, $CH / $sh); $cw = (int) ($CW / $sc); $chh = (int) ($CH / $sc);
                imagecopyresampled($img, $t, $x, $y + $HDR, (int) (($sw - $cw) / 2), (int) (($sh - $chh) / 2), $CW, $CH, $cw, $chh);
            } else {
                imagestring($img, $font, $x + 70, $y + $HDR + 70, 'no image', $grey);
            }
            $bg = $label === 'cur' ? $red : ($label[0] === 'L' ? $blue : $yel);
            imagefilledrectangle($img, $x, $y + $HDR, $x + 10 * strlen($label) + 10, $y + $HDR + 20, $bg);
            imagestring($img, $font, $x + 5, $y + $HDR + 3, $label, $label[0] === 'C' ? $black : $white);
            $ci++;
        }
        $manifest[sprintf('sheet-%03d', $n + 1)][] = $row;
    }
    imagepng($img, sprintf('%s/sheet-%03d.png', $out, $n + 1));
}
file_put_contents("$out/sheets.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo count($rows), ' subjects on ', count($manifest), " sheets\n";
