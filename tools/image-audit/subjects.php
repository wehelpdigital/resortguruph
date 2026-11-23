<?php
// Usage (project root): php tools/image-audit/subjects.php <outdir>
// Lists every named place / dish / festival / hub item shown on the site that has no
// photo of its own (no photo, placeholder, a generic town photo, or a photo shared with
// a different name), grouped per subject (kind + name + place), with search queries and
// matching files already in the media library. Writes <outdir>/subjects.json.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\GuideHtml;
use Illuminate\Support\Facades\DB;

$out = rtrim($argv[1] ?? 'storage/app/image-audit', '/\\');
@mkdir($out, 0777, true);
$root = storage_path('app/public/');
$nk = fn ($s) => GuideHtml::nameKey((string) $s);
$isPh = fn ($s) => $s === '' || (bool) preg_match('~placeholder|placehold\.co|/images/brand/~i', $s);
$isGeneric = fn ($s) => (bool) preg_match('~/rg-media/(destinations|food-locations)/~', $s);
$hash = [];
$fileKey = function ($src) use (&$hash, $root) {
    $path = (string) strtok(preg_replace('~^https?://[^/]+~i', '', trim($src)), '?');
    if (!str_starts_with($path, '/storage/')) return 'x:' . $path;
    if (isset($hash[$path])) return $hash[$path];
    $f = $root . rawurldecode(substr($path, 9));
    return $hash[$path] = is_file($f) ? md5_file($f) : 'missing:' . $path;
};
$norm = function ($src) {
    $src = trim((string) $src);
    if ($src === '') return '';
    if (!preg_match('~^(https?:)?//|^/~', $src)) $src = '/storage/' . ltrim($src, '/');
    return preg_replace('~^https?://[^/]+(?=/storage/)~i', '', $src);
};
$placeOf = function (string $heading, array $res) {
    foreach ($res as $re) if (preg_match($re, trim($heading), $m)) return trim($m[1], " ?\t");
    return '';
};

$uses = []; // subject key => data
$use = function (string $kind, string $name, string $place, string $cluster, string $src, string $where, string $desc = '', array $extra = []) use (&$uses, $nk, $norm) {
    $name = trim(html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($name === '' || mb_strlen($name) > 120) return;
    $place = trim(html_entity_decode($place, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $key = $kind . '|' . $nk($name) . '|' . ($kind === 'food' ? '' : $nk($place));
    $u = $uses[$key] ?? ['kind' => $kind, 'name' => $name, 'place' => $place, 'cluster' => $cluster, 'srcs' => [], 'pages' => [], 'uses' => 0, 'desc' => ''] + $extra;
    $u['uses']++;
    $s = $norm($src);
    $u['srcs'][$s] = ($u['srcs'][$s] ?? 0) + 1;
    if (count($u['pages']) < 6) $u['pages'][$where] = true;
    $d = trim(strip_tags($desc));
    if (mb_strlen($d) > mb_strlen($u['desc'])) $u['desc'] = mb_substr($d, 0, 400);
    $uses[$key] = $u;
};

$pages = DB::table('rg_seo_pages as p')->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')->get(['p.id', 'p.slug', 'k.cluster_tag'])->keyBy('id');
$fiestas = DB::table('rg_fiestas')->get()->keyBy('id');
foreach (DB::table('rg_content_blocks')->whereIn('owner_type', ['seo_page', 'fiesta'])->whereIn('block_type', ['custom_html', 'attractions', 'foods_to_try'])->cursor() as $b) {
    $p = json_decode($b->payload_json, true) ?: [];
    if ($b->owner_type === 'seo_page') {
        $pg = $pages[$b->owner_id] ?? null;
        $where = '/' . ($pg->slug ?? $b->owner_id);
        $cluster = (string) ($pg->cluster_tag ?? '');
    } else {
        $f = $fiestas[$b->owner_id] ?? null;
        $where = '/fiestas/' . ($f->slug ?? $b->owner_id);
        $cluster = (string) ($f->region_cluster ?? '');
    }
    if ($b->block_type === 'attractions') {
        $place = $placeOf((string) ($p['heading'] ?? ''), ["~^What['’]?s in (.+?)\??$~i", '~^(?:Tourist spots|Things to do|Spots) (?:in|near|around) (.+)$~i']);
        foreach ($p['items'] ?? [] as $i) $use('place', (string) ($i['name'] ?? ''), $place, $cluster, (string) ($i['image'] ?? ''), $where, (string) ($i['blurb'] ?? ''));
        continue;
    }
    if ($b->block_type === 'foods_to_try') {
        $place = $placeOf((string) ($p['heading'] ?? ''), ['~^Foods? to try in (.+)$~i', '~^What to eat in (.+)$~i']);
        foreach ($p['items'] ?? [] as $i) $use('food', (string) ($i['name'] ?? ''), $place, $cluster, (string) ($i['image'] ?? ''), $where, (string) ($i['blurb'] ?? ''));
        continue;
    }
    $html = (string) ($p['html'] ?? '');
    if ($html === '' || !preg_match('~Tourist spots|Notable festivals|food-card~', $html)) continue;
    $doc = new DOMDocument();
    @$doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>');
    $xp = new DOMXPath($doc);
    foreach ($xp->query('//section') as $sec) {
        $h2 = $xp->query('.//h2', $sec)->item(0);
        $t = $h2 ? trim($h2->textContent) : '';
        if (stripos($t, 'Tourist spots') === 0) $kind = 'place';
        elseif (stripos($t, 'Notable festivals') === 0) $kind = 'festival';
        else continue;
        $place = $placeOf($t, ['~^(?:Tourist spots|Notable festivals) (?:in|near|around) (.+)$~i']);
        foreach ($xp->query('.//article', $sec) as $art) {
            $h3 = $xp->query('.//h3', $art)->item(0);
            $img = $xp->query('.//img', $art)->item(0);
            $ps = $xp->query('.//p', $art);
            $use($kind, $h3 ? $h3->textContent : '', $place, $cluster, $img ? $img->getAttribute('src') : '', $where, $ps->length ? $ps->item(0)->textContent : '');
        }
    }
    foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " food-card ")]') as $card) {
        $h3 = $xp->query('.//h3', $card)->item(0);
        $img = $xp->query('.//img', $card)->item(0);
        $pp = $xp->query('.//p', $card)->item(0);
        $use('food', $h3 ? $h3->textContent : '', '', $cluster, $img ? $img->getAttribute('src') : '', $where, $pp ? $pp->textContent : '');
    }
}
foreach ($fiestas as $f) $use('fiesta', $f->name, trim(($f->city_or_town ? $f->city_or_town . ', ' : '') . $f->province, ', '), (string) $f->region_cluster, (string) ($f->cover_image_path ?? ''), '/fiestas/' . $f->slug, (string) $f->summary, ['fiesta_id' => $f->id]);
foreach (['buys' => \App\Http\Controllers\BuysController::class, 'foods' => \App\Http\Controllers\FoodsController::class, 'cultures' => \App\Http\Controllers\CulturesController::class, 'activities' => \App\Http\Controllers\ActivitiesController::class] as $hub => $cls) {
    $m = new ReflectionMethod($cls, 'categories');
    $m->setAccessible(true);
    foreach ($m->invoke(app($cls)) as $cat) foreach ($cat['items'] ?? [] as $it) {
        $imgs = is_array($it['images'] ?? null) ? $it['images'] : [];
        $use('hub-' . $hub, (string) ($it['name'] ?? ''), (string) ($it['where'] ?? ''), (string) $cat['key'], $imgs[0] ?? '', "/$hub#item-" . ($it['slug'] ?? ''), (string) (($it['description'] ?? '') ?: ($it['note'] ?? '')), ['hub_slug' => $it['slug'] ?? '', 'hub_images' => count($imgs), 'category' => $cat['label'] ?? '']);
    }
}

// which subject names each photo file is used for
$namesByFile = [];
foreach ($uses as $u) foreach ($u['srcs'] as $s => $n) if (!$isPh($s)) $namesByFile[$fileKey($s)][$nk($u['name'])] = true;

// media library files usable as local candidates, by folder
$lib = [];
foreach (['places' => ['spots', 'places', 'attractions', 'landmarks'], 'food' => ['foods'], 'festival' => ['fiestas'], 'hub-buys' => ['buys'], 'hub-cultures' => ['cultures'], 'hub-activities' => ['activities'], 'hub-foods' => ['foods']] as $k => $dirs) {
    foreach ($dirs as $d) foreach (glob($root . "rg-media/$d/*.{jpg,jpeg,png,webp}", GLOB_BRACE) ?: [] as $f) {
        $lib[$k][] = ['src' => '/storage/rg-media/' . $d . '/' . basename($f), 'toks' => array_values(array_filter(preg_split('~[^a-z0-9]+~', strtolower(pathinfo($f, PATHINFO_FILENAME))), fn ($t) => $t !== '' && !ctype_digit($t)))];
    }
}
$stop = array_flip(['the', 'of', 'and', 'in', 'at', 'on', 'a', 'an', 'to', 'de', 'del', 'la', 'ng', 'sa', 'side', 'trip', 'south', 'north', 'east', 'west', 'city', 'town', 'area', 'grounds', 'strip', 'public', 'old', 'new', 'nearby', 'local', 'spot', 'stop']);
$tok = fn ($s) => array_values(array_filter(array_unique(preg_split('~[^a-z0-9]+~', strtolower(\Illuminate\Support\Str::ascii((string) $s)))), fn ($t) => strlen($t) > 2 && !isset($stop[$t])));
$localFor = function (array $u) use ($lib, $tok) {
    $pool = $lib[in_array($u['kind'], ['place'], true) ? 'places' : ($u['kind'] === 'fiesta' ? 'festival' : $u['kind'])] ?? [];
    $want = $tok(preg_replace('~\(.*?\)~', ' ', $u['name']));
    if (!$want) return [];
    $placeT = $tok($u['place']);
    $isFood = in_array($u['kind'], ['food', 'hub-foods'], true);
    $hits = [];
    foreach ($pool as $f) {
        $has = array_intersect($want, $f['toks']);
        if ($isFood) {
            // a dish photo named after the dish itself ("pinakbet-1") matches "Pinakbet Ilocos-style"
            $ftoks = array_values(array_filter($f['toks'], fn ($t) => strlen($t) > 2));
            if (!$ftoks || array_diff($ftoks, $want, $placeT)) continue;
            $hits[$f['src']] = count($has) * 3 - count($ftoks) * 0.1;
            continue;
        }
        if (count($has) < count($want) && !(count($want) >= 3 && count($has) >= count($want) - 1)) continue;
        $score = count($has) * 3 + count(array_intersect($placeT, $f['toks'])) - count(array_diff($f['toks'], $want, $placeT)) * 0.3;
        $hits[$f['src']] = $score;
    }
    arsort($hits);
    return array_slice(array_keys($hits), 0, 3);
};

$subjects = [];
$id = 0;
foreach ($uses as $key => $u) {
    // does any use already show an own, real photo?
    $own = false;
    foreach ($u['srcs'] as $s => $n) {
        if ($isPh($s) || ($isGeneric($s) && $u['kind'] !== 'fiesta')) continue;
        $fk = $fileKey($s);
        if (str_starts_with($fk, 'missing:')) continue;
        if (count($namesByFile[$fk] ?? []) > 2) continue; // one photo for 3+ different names: a stand-in
        $own = true;
    }
    if (str_starts_with($u['kind'], 'hub-') && ($u['hub_images'] ?? 0) > 0) $own = true;
    if ($own) continue;
    arsort($u['srcs']);
    $cur = (string) array_key_first($u['srcs']);
    $bare = trim(preg_replace('~\s*\((.*?)\)~', ' $1', $u['name']));
    if (in_array($u['kind'], ['food', 'hub-foods'], true)) {
        // search for the dish, not "Halo-halo at Halo-Halo de Iloko" / "Pinakbet Ilocos-style"
        $bare = trim(preg_replace(['~\s*\(.*?\)~', '~\s+(at|from|in|by|of the|near)\s+.*$~i', '~\b[\w-]+-style\b~i', '~\b(ilocos|ilocano|batangas|pampanga|kapampangan|bicol|bicolano|cebu|cebuano|ilonggo|iloilo|bacolod|davao|style|version|special|famous|original|best|local|street|homemade|classic)\b~i'], ' ', $u['name']));
        $bare = trim(preg_replace('~\s+~', ' ', $bare)) ?: $u['name'];
    }
    $q = match (true) {
        $u['kind'] === 'food', $u['kind'] === 'hub-foods' => [trim(preg_replace('~\s*\(.*?\)~', ' ', $u['name'])) . ' food', $bare . ' Filipino food'],
        $u['kind'] === 'hub-cultures' => [$bare . ' people Philippines', $bare . ' tribe'],
        $u['kind'] === 'hub-buys' => [$bare . ' ' . $u['place'], $bare],
        $u['kind'] === 'hub-activities' => [$bare . ' Philippines', $bare],
        $u['kind'] === 'festival', $u['kind'] === 'fiesta' => [$bare . ' ' . $u['place'], $bare . ' festival'],
        default => [trim($bare . ' ' . $u['place']), $bare],
    };
    $subjects[] = [
        'id' => ++$id, 'kind' => $u['kind'], 'name' => $u['name'], 'place' => $u['place'], 'cluster' => $u['cluster'],
        'uses' => $u['uses'], 'pages' => array_keys($u['pages']), 'current' => $isPh($cur) ? '' : $cur,
        'desc' => $u['desc'], 'query' => $q[0], 'alt_query' => $q[1], 'local' => $localFor($u),
    ] + array_intersect_key($u, array_flip(['fiesta_id', 'hub_slug', 'category']));
}
usort($subjects, fn ($a, $b) => [$a['kind'], -$a['uses']] <=> [$b['kind'], -$b['uses']]);
foreach ($subjects as $i => &$s) $s['id'] = $i + 1;
unset($s);
file_put_contents("$out/subjects.json", json_encode($subjects, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$by = [];
foreach ($subjects as $s) { $by[$s['kind']]['n'] = ($by[$s['kind']]['n'] ?? 0) + 1; if ($s['local']) $by[$s['kind']]['local'] = ($by[$s['kind']]['local'] ?? 0) + 1; }
echo count($uses), ' subjects in use, ', count($subjects), " without an own photo\n";
foreach ($by as $k => $v) echo "  $k: {$v['n']} (with local file match: " . ($v['local'] ?? 0) . ")\n";
