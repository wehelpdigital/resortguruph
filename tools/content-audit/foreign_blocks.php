<?php
/**
 * Guides that were seeded from another destination's template ("Resort in Iloilo" carrying
 * "What's in Cebu City?", Cebu hero photos and Cebu key facts). Finds the template blocks that
 * name another place and fixes them:
 *   editor_rating            retitled "Our take on {own place}" (the scores are the generic template)
 *   attractions, facts_list  removed (the page's own "Tourist spots in X" list stays)
 *   hero_slider              the other place's photos replaced by the page's own town photos
 *   wwww / tldr / tip / getting there / map / history / intro
 *                            removed when they name the other place and never the page's own
 * The page's own place is the one its seeded guide text is about ("Tourist spots in X",
 * "Why X Is Worth The Trip"). Text sections are reported, not touched.
 *
 *   php tools/content-audit/foreign_blocks.php            dry run
 *   php tools/content-audit/foreign_blocks.php --apply    write (old rows saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\GuideHtml;
use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$stop = array_flip(['city', 'philippines', 'the', 'of', 'province', 'island', 'islands', 'beach', 'resort', 'resorts', 'hotel', 'hotels', 'best', 'top', 'in', 'and', 'with', 'pool', 'de', 'del', 'san', 'santa', 'sta', 'north', 'south', 'general']);
$toks = function (string $s) use ($stop): array {
    $s = preg_replace('~\(.*?\)~', ' ', $s);
    return array_values(array_filter(explode(' ', GuideHtml::nameKey($s)), fn ($t) => $t !== '' && !isset($stop[$t]) && strlen($t) > 2));
};
$core = fn (string $s) => trim(GuideHtml::nameKey(preg_replace('~\(.*?\)~', ' ', $s)));
$mentions = function (string $json, string $place) {
    if ($place === '') return false;
    $text = '';
    $data = json_decode($json, true);
    if (is_array($data)) array_walk_recursive($data, function ($v) use (&$text) { if (is_string($v)) $text .= ' ' . strip_tags(str_replace(['-', '_', '/'], ' ', $v)); });
    $hay = ' ' . GuideHtml::nameKey(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')) . ' ';
    return str_contains($hay, ' ' . $place . ' ');
};
// abbreviations and places that belong together (not a foreign template)
$same = [['bgc', 'bonifacio global'], ['cdo', 'cagayan oro'], ['gensan', 'santos'], ['rockwell', 'power plant'], ['fishermall', 'fisher mall'], ['uptc', 'town center'],
    ['west gate', 'westgate'], ['dasma', 'dasmarinas'], ['shangrila', 'shangri'], ['moa', 'mall asia'], ['laguna', 'pansol'], ['albay', 'legazpi'], ['lapu', 'mactan'],
    ['lapu', 'cebu'], ['batangas', 'laiya']];
$destFiles = array_map(fn ($f) => basename($f), glob(storage_path('app/public/rg-media/destinations/*.jpg')));

$pages = DB::table('rg_seo_pages as p')->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')->where('p.is_published', 1)->orderBy('p.slug')->get(['p.id', 'p.slug', 'p.h1', 'k.phrase']);
$backup = [];
$report = [];
foreach ($pages as $pg) {
    $blocks = DB::table('rg_content_blocks')->where('owner_type', 'seo_page')->where('owner_id', $pg->id)->orderBy('sort_order')->get();
    $rating = $blocks->firstWhere('block_type', 'editor_rating');
    $rp = $rating ? (json_decode($rating->payload_json, true) ?: []) : [];
    if (!preg_match('~^Our take on (.+)$~i', trim($rp['title'] ?? ''), $m)) continue;
    $foreignName = trim($m[1]);
    // the page's own place: what its seeded guide text is about, else the keyword
    $own = '';
    foreach ($blocks as $b) {
        if ($b->block_type !== 'custom_html') continue;
        $html = (string) (json_decode($b->payload_json, true)['html'] ?? '');
        if (preg_match('~<h2[^>]*>\s*(?:Tourist spots in|Why) ([^<]+?)(?: Is Worth The Trip)?\s*</h2>~i', $html, $hm)) { $own = trim(html_entity_decode($hm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')); break; }
    }
    $kwPlace = preg_replace('~^.*?\b(in|at|near|of)\s+~i', '', $pg->phrase);
    if ($own === '') $own = ucwords($kwPlace);
    $ownT = array_flip(array_merge($toks($own), $toks($kwPlace), $toks((string) $pg->h1)));
    $forT = $toks($foreignName);
    if (!$forT || array_intersect_key(array_flip($forT), $ownT)) continue; // same place
    $ownS = ' ' . implode(' ', array_keys($ownT)) . ' ';
    $forS = ' ' . GuideHtml::nameKey($foreignName) . ' ';
    $ok = false;
    foreach ($same as [$a, $b2]) if ((str_contains($ownS, $a) && str_contains($forS, $b2)) || (str_contains($ownS, $b2) && str_contains($forS, $a))) $ok = true;
    if ($ok) continue;

    $fCore = $core($foreignName);
    $fCoreShort = $forT[0] === 'cebu' || $forT[0] === 'davao' || $forT[0] === 'legazpi' || $forT[0] === 'tagaytay' || $forT[0] === 'antipolo' ? $forT[0] : $fCore;
    $oCore = $core($own);
    $ownDisplay = preg_replace('~\s+~', ' ', $own);
    $acts = [];
    // the page's own town photos for the hero
    $ownPhotos = [];
    $keys = array_keys($ownT);
    foreach ($destFiles as $f) {
        $stem = preg_replace('~-\d+\.jpg$~', '', $f);
        $st = explode('-', $stem);
        if (array_intersect($st, $keys) && count(array_intersect($st, $keys)) >= min(count($keys), 1) && !array_intersect($st, $forT)) {
            $src = '/storage/rg-media/destinations/' . $f;
            if (GuideHtml::usable($src)) $ownPhotos[$stem][] = $src;
        }
    }
    // the best-matching town (most shared words), e.g. dagupan-pangasinan before pangasinan-general on a Dagupan page
    uksort($ownPhotos, fn ($a, $b) => (count(array_intersect(explode('-', $b), $keys)) <=> count(array_intersect(explode('-', $a), $keys))) ?: (str_contains($b, 'general') <=> str_contains($a, 'general')) ?: strcmp($a, $b));
    $heroSrcs = $ownPhotos ? reset($ownPhotos) : [];

    foreach ($blocks as $b) {
        $p = json_decode($b->payload_json, true) ?: [];
        $hasF = $mentions($b->payload_json, $fCoreShort);
        $hasO = $oCore !== '' && $mentions($b->payload_json, $oCore);
        $new = null; $del = false;
        switch ($b->block_type) {
            case 'editor_rating':
                $p['title'] = 'Our take on ' . $ownDisplay; $new = $p; break;
            case 'attractions':
            case 'facts_list':
                if ($hasF) $del = true; break;
            case 'hero_slider':
                // keep only slides that are of the page's own place; the template's other photos go
                $isOwn = function ($i) use ($mentions, $ownT) { foreach (array_keys($ownT) as $t) if ($mentions(json_encode($i), $t)) return true; return false; };
                $keep = array_values(array_filter($p['images'] ?? [], fn ($i) => !$mentions(json_encode($i), $fCoreShort) && $isOwn($i)));
                if (count($keep) === count($p['images'] ?? [])) break;
                $have = array_column($keep, 'src');
                foreach (count($keep) >= 3 ? [] : $heroSrcs as $src) {
                    if (in_array($src, $have, true)) continue;
                    $mrow = DB::table('rg_media')->where('path', ltrim(substr($src, strlen('/storage/')), '/'))->first();
                    $keep[] = ['src' => $src, 'alt' => $ownDisplay, 'caption_title' => $ownDisplay, 'caption' => (string) ($mrow->credit ?? ''), 'credit_url' => (string) ($mrow->source_url ?? '')];
                }
                if ($keep) { $p['images'] = $keep; $new = $p; } else $del = true;
                break;
            case 'wwww_card': case 'tldr_card': case 'local_tip': case 'how_to_get_to': case 'map_embed': case 'place_history': case 'subtitle_intro': case 'quick_facts':
                if ($hasF && !$hasO) $del = true; break;
            case 'custom_html': case 'text_section':
                if ($hasF && !$hasO) $acts[] = "  note {$b->block_type}#{$b->id} names {$foreignName} only";
                break;
        }
        if ($del) {
            $acts[] = "  delete {$b->block_type}#{$b->id}";
            $backup[] = (array) $b;
            if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->delete();
        } elseif ($new !== null && json_encode($new) !== json_encode(json_decode($b->payload_json, true))) {
            $acts[] = "  update {$b->block_type}#{$b->id}" . ($b->block_type === 'hero_slider' ? ' (' . count($new['images']) . ' slides: ' . implode(', ', array_map(fn ($i) => basename($i['src']), $new['images'])) . ')' : ' -> ' . ($new['title'] ?? ''));
            $backup[] = (array) $b;
            if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($new, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
        }
    }
    $report[] = "{$pg->slug}  [own: {$ownDisplay} | template: {$foreignName}]\n" . implode("\n", $acts);
}
echo implode("\n", $report), "\n", count($report), " pages\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/foreign-blocks-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
    Illuminate\Support\Facades\Cache::flush();
}
