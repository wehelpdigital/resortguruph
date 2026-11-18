<?php
// Usage: php fetch.php <queries.json> <outdir>
// queries.json: [{"id":12,"subject":"SM Mall of Asia","query":"SM Mall of Asia Pasay","alt_query":"Mall of Asia"}]
// Writes <outdir>/candidates.json and review sheets <outdir>/sheet-NN.png (4 subjects per sheet,
// column 0 = image currently used on the site, columns 1-5 = Commons candidates labelled "<id>.<k>").
[$_, $qfile, $out] = $argv + [null, null, null];
@mkdir($out, 0777, true);
$rows = json_decode(file_get_contents($qfile), true);
$inv = json_decode(file_get_contents(__DIR__ . '/subjects_indexed.json'), true);
$UA = 'image-audit-script/1.0 (generic client)';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: $UA\r\n", 'timeout' => 25]]);
$bad = '~\b(map|maps|logo|seal|flag|coat of arms|locator|diagram|chart|signature|stamp|banknote|coin|poster|document|scan|svg|plan|layout|route|sign|screenshot|newspaper|cover|ticket|menu)\b~i';
function rget($url, $ctx) {
    for ($a = 0; $a < 10; $a++) {
        $opts = stream_context_get_options($ctx); $opts["http"]["ignore_errors"] = true;
        $c = stream_context_create($opts);
        $d = @file_get_contents($url, false, $c);
        $st = 0; $ra = 0;
        foreach (($http_response_header ?? []) as $h) {
            if (preg_match("~^HTTP/\S+\s+(\d+)~", $h, $m)) $st = (int) $m[1];
            if (preg_match("~^retry-after:\s*(\d+)~i", $h, $m)) $ra = (int) $m[1];
        }
        if ($d !== false && $st === 200) return $d;
        if ($st && $st != 429 && $st < 500) return false;
        $wait = max($ra, 3 * ($a + 1)) + rand(0, 3);
        fwrite(STDERR, "  retry $st in {$wait}s\n");
        sleep($wait);
    }
    return false;
}
function commons($q, $ctx) {
    $u = 'https://commons.wikimedia.org/w/api.php?' . http_build_query(['action' => 'query', 'format' => 'json', 'generator' => 'search', 'gsrnamespace' => 6, 'gsrsearch' => "$q filetype:bitmap", 'gsrlimit' => 20, 'prop' => 'imageinfo', 'iiprop' => 'url|size|mime|extmetadata', 'iiurlwidth' => 320, 'iiextmetadatafilter' => 'LicenseShortName|Artist|LicenseUrl']);
    $j = json_decode(rget($u, $ctx), true);
    $pages = $j['query']['pages'] ?? [];
    uasort($pages, fn ($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));
    return array_values($pages);
}
$cands = [];
foreach ($rows as $r) {
    $list = [];
    foreach (array_filter([$r['query'] ?? '', $r['alt_query'] ?? '']) as $q) {
        foreach (commons($q, $ctx) as $p) {
            $i = $p['imageinfo'][0] ?? null; if (!$i) continue;
            $lic = strip_tags($i['extmetadata']['LicenseShortName']['value'] ?? '');
            $w = $i['width']; $h = $i['height'];
            if (!in_array($i['mime'], ['image/jpeg', 'image/png', 'image/webp'])) continue;
            if ($w < 900 || $h < 500 || $w / max($h, 1) < 1.15 || $w / max($h, 1) > 2.4) continue;
            if (!preg_match('~^(CC BY|CC-BY|CC0|Public domain|PD)~i', $lic)) continue;
            if (preg_match($bad, $p['title'])) continue;
            $key = $p['title']; if (isset($list[$key])) continue;
            $list[$key] = ['title' => substr($p['title'], 5), 'thumb' => $i['thumburl'], 'orig' => $i['url'], 'w' => $w, 'h' => $h, 'license' => $lic,
                'license_url' => $i['extmetadata']['LicenseUrl']['value'] ?? '', 'artist' => trim(html_entity_decode(strip_tags($i['extmetadata']['Artist']['value'] ?? ''))), 'page' => $i['descriptionurl']];
            if (count($list) >= 5) break 2;
        }
        usleep(1000000);
    }
    $cands[$r['id']] = ['subject' => $r['subject'], 'query' => $r['query'], 'candidates' => array_values($list)];
    fwrite(STDERR, "#{$r['id']} " . count($list) . " candidates\n");
}
file_put_contents("$out/candidates.json", json_encode($cands, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
// sheets
$CW = 300; $CH = 200; $HDR = 24; $per = 4; $cols = 6;
$ids = array_keys($cands); $n = 0;
foreach (array_chunk($ids, $per) as $chunk) {
    $img = imagecreatetruecolor($CW * $cols + 5 * ($cols - 1), ($HDR + $CH + 8) * count($chunk));
    $white = imagecolorallocate($img, 255, 255, 255); $black = imagecolorallocate($img, 0, 0, 0); $yel = imagecolorallocate($img, 255, 230, 0); $red = imagecolorallocate($img, 220, 40, 40); $grey = imagecolorallocate($img, 90, 90, 90);
    imagefill($img, 0, 0, $white);
    foreach ($chunk as $ri => $id) {
        $y = $ri * ($HDR + $CH + 8);
        imagestring($img, 5, 4, $y + 4, substr("#$id  " . $cands[$id]['subject'] . "   [query: " . $cands[$id]['query'] . "]", 0, 190), $black);
        $cells = [['label' => 'current', 'src' => $inv[$id]['current_file'] ?? null, 'local' => true]];
        foreach ($cands[$id]['candidates'] as $k => $c) $cells[] = ['label' => "$id." . ($k + 1), 'src' => $c['thumb'], 'local' => false];
        foreach ($cells as $ci => $cell) {
            $x = $ci * ($CW + 5);
            $data = $cell['src'] ? ($cell['local'] ? @file_get_contents($cell['src']) : rget($cell['src'], $ctx)) : false;
            $t = $data ? @imagecreatefromstring($data) : false;
            if ($t) {
                $sw = imagesx($t); $sh = imagesy($t); $scale = max($CW / $sw, $CH / $sh); $cw = (int) ($CW / $scale); $ch = (int) ($CH / $scale);
                imagecopyresampled($img, $t, $x, $y + $HDR, (int) (($sw - $cw) / 2), (int) (($sh - $ch) / 2), $CW, $CH, $cw, $ch);
            } else { imagestring($img, 5, $x + 90, $y + $HDR + 90, 'no image', $grey); }
            imagefilledrectangle($img, $x, $y + $HDR, $x + 9 * strlen($cell['label']) + 8, $y + $HDR + 18, $ci === 0 ? $red : $yel);
            imagestring($img, 5, $x + 4, $y + $HDR + 2, $cell['label'], $ci === 0 ? $white : $black);
        }
        usleep(300000);
    }
    $n++; imagepng($img, sprintf('%s/sheet-%02d.png', $out, $n));
}
fwrite(STDERR, "sheets: $n\n");
