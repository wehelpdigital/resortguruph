<?php
// Usage: php tools/image-audit/fetch_subjects.php <subjects.json> <outdir> [fromId] [toId]
// For each subject from subjects.php: searches Wikimedia Commons (query, then alt_query)
// for freely licensed photos and caches a 330px thumbnail of each candidate (and of the
// local library matches) under <outdir>/thumbs. Resumable: results are appended to
// <outdir>/cand-<from>-<to>.jsonl, subjects already in that file are skipped.
[$_, $sfile, $out] = $argv + [null, null, null];
$from = (int) ($argv[3] ?? 1);
$to = (int) ($argv[4] ?? PHP_INT_MAX);
@mkdir("$out/thumbs", 0777, true);
$subjects = json_decode(file_get_contents($sfile), true);
$UA = 'image-audit-script/1.0 (generic client)';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: $UA\r\n", 'timeout' => 30, 'ignore_errors' => true]]);
$bad = '~\b(map|maps|logo|seal|flag|coat of arms|locator|diagram|chart|signature|stamp|banknote|coin|poster|document|scan|svg|plan|layout|route|sign|signage|screenshot|newspaper|cover|ticket|menu|book|page|djvu|pdf|label|advertisement)\b~i';

function rget(string $url, $ctx, int $tries = 8)
{
    for ($a = 0; $a < $tries; $a++) {
        $d = @file_get_contents($url, false, $ctx);
        $st = 0; $ra = 0;
        foreach (($http_response_header ?? []) as $h) {
            if (preg_match('~^HTTP/\S+\s+(\d+)~', $h, $m)) $st = (int) $m[1];
            if (preg_match('~^retry-after:\s*(\d+)~i', $h, $m)) $ra = (int) $m[1];
        }
        if ($d !== false && $st === 200) return $d;
        if ($st && $st !== 429 && $st < 500) return false;
        $wait = min(300, max($ra, 30 * ($a + 1))) + rand(0, 5);
        fwrite(STDERR, "  http $st, retry in {$wait}s\n");
        sleep($wait);
    }
    return false;
}

function search(string $q, $ctx): array
{
    $u = 'https://commons.wikimedia.org/w/api.php?' . http_build_query([
        'action' => 'query', 'format' => 'json', 'generator' => 'search', 'gsrnamespace' => 6,
        'gsrsearch' => "$q filetype:bitmap", 'gsrlimit' => 15, 'prop' => 'imageinfo',
        'iiprop' => 'url|size|mime|extmetadata',
        'iiextmetadatafilter' => 'LicenseShortName|Artist|LicenseUrl|ImageDescription',
    ]);
    $raw = rget($u, $ctx, 3);
    if ($raw === false) { fwrite(STDERR, "search throttled, stopping (resume later)
"); exit(3); }
    $j = json_decode((string) $raw, true);
    $pages = $j['query']['pages'] ?? [];
    uasort($pages, fn ($a, $b) => ($a['index'] ?? 0) <=> ($b['index'] ?? 0));
    return array_values($pages);
}

function thumb(string $url, string $dir, $ctx): string
{
    $f = $dir . '/' . md5($url) . '.jpg';
    if (is_file($f) && filesize($f) > 500) return $f;
    $d = rget($url, $ctx, 5);
    if ($d === false) return '';
    file_put_contents($f, $d);
    usleep(100000);
    return $f;
}

$done = [];
$file = sprintf('%s/cand-%d-%d%s.jsonl', $out, $from, min($to, 99999), getenv('ORDER') === 'asc' ? '-asc' : '');
if (is_file($file)) foreach (file($file) as $line) { $r = json_decode($line, true); if ($r) $done[$r['id']] = true; }
$fh = fopen($file, 'a');
$root = __DIR__ . '/../../storage/app/public/';
// wrong blog photos, hub items, festivals and dishes first, then places by how often they are shown
$prio = fn ($k) => ['blogfig' => 0, 'hub-foods' => 1, 'hub-cultures' => 1, 'hub-buys' => 1, 'hub-activities' => 1, 'fiesta' => 2, 'festival' => 2, 'food' => 3][$k] ?? 4;
usort($subjects, fn ($a, $b) => [$prio($a['kind']), -$a['uses']] <=> [$prio($b['kind']), -$b['uses']]);
if (getenv('ORDER') === 'asc') $subjects = array_reverse($subjects); // a second worker starts from the other end
$seenOther = function () use ($out) {
    $ids = [];
    // subjects already settled from the local library (written by the review step)
    if (is_file("$out/skip.json")) foreach (json_decode(file_get_contents("$out/skip.json"), true) ?: [] as $sid) $ids[(int) $sid] = true;
    foreach (glob("$out/cand-*.jsonl") as $f) foreach (file($f) as $line) if (preg_match('~^\{"id":(\d+)~', $line, $m)) $ids[(int) $m[1]] = true;
    return $ids;
};
$other = $seenOther();
$tick = 0;
foreach ($subjects as $s) {
    if (++$tick % 10 === 0) $other = $seenOther();
    if ($s['id'] < $from || $s['id'] > $to || isset($done[$s['id']]) || isset($other[$s['id']])) continue;
    $food = in_array($s['kind'], ['food', 'hub-foods'], true);
    $people = $s['kind'] === 'hub-cultures';
    $list = [];
    foreach (array_unique(array_filter([$s['query'], $s['alt_query']])) as $qi => $q) {
        if ($qi > 0 && count($list) >= 2) break; // the second query only when the first found little
        $res = search($q, $ctx);
        sleep((int) (getenv('SEARCH_GAP') ?: 2));
        foreach ($res as $p) {
            $i = $p['imageinfo'][0] ?? null;
            if (!$i || empty($i['url'])) continue;
            // thumbnail URL built from the file URL (asking the API to render one is rate limited)
            $i['thumburl'] = preg_replace('~^https://upload\.wikimedia\.org/wikipedia/commons/([0-9a-f])/([0-9a-f]{2})/([^/?]+).*$~', 'https://thumb.wikimedia.org/wikipedia/commons/thumb/$1/$2/$3/330px-$3', (string) $i['url']);
            if (!str_contains($i['thumburl'], '/thumb/')) continue;
            $lic = strip_tags($i['extmetadata']['LicenseShortName']['value'] ?? '');
            $w = (int) $i['width']; $h = (int) $i['height']; $r = $w / max($h, 1);
            if (!in_array($i['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) continue;
            if ($w < 700 || $h < 450) continue;
            if ($r < (($food || $people) ? 0.75 : 1.1) || $r > 2.4) continue;
            if (!preg_match('~^(CC BY|CC-BY|CC0|Public domain|PD)~i', $lic)) continue;
            if (preg_match($bad, $p['title'])) continue;
            if (isset($list[$p['title']])) continue;
            $list[$p['title']] = [
                'title' => substr($p['title'], 5), 'thumb' => preg_replace('~\?.*$~', '', $i['thumburl']), 'orig' => $i['url'],
                'w' => $w, 'h' => $h, 'license' => $lic, 'license_url' => $i['extmetadata']['LicenseUrl']['value'] ?? '',
                'artist' => trim(html_entity_decode(strip_tags($i['extmetadata']['Artist']['value'] ?? ''))),
                'desc' => mb_substr(trim(html_entity_decode(strip_tags($i['extmetadata']['ImageDescription']['value'] ?? ''))), 0, 200),
                'page' => $i['descriptionurl'],
            ];
            if (count($list) >= 4) break 2;
        }
    }
    $cands = array_values($list);
    foreach ($cands as &$c) $c['thumb_file'] = thumb($c['thumb'], "$out/thumbs", $ctx);
    unset($c);
    fwrite($fh, json_encode(['id' => $s['id'], 'candidates' => $cands], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
    fflush($fh);
    fwrite(STDERR, "#{$s['id']} {$s['name']}: " . count($cands) . "\n");
}
fclose($fh);
fwrite(STDERR, "done $from-$to\n");
