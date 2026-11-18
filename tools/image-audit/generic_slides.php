<?php
// Report (or with --apply, fix) hero-slider slides whose caption is generic ("Tourist spot in X", "Philippines"...):
// give them a photo of a real spot on the SAME page (attractions items / page tourist spots) and caption it with that spot's name.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$apply = in_array('--apply', $argv, true);
$generic = '~^(tourist spots?|resorts?|hotels?|beach(es)? (and )?resorts?|airbnb|staycation|restaurants?|places to visit)( in| near| at| of)? .{0,60}$|^philippines$~i';
$norm = fn ($s) => '/' . ltrim(preg_replace('~^https?://[^/]+~', '', (string) $s), '/');
$stats = ['slides' => 0, 'generic' => 0, 'fixed' => 0, 'no_pool' => 0, 'pages' => 0];
$backup = [];
foreach (DB::table('rg_content_blocks')->where('owner_type', 'seo_page')->where('block_type', 'hero_slider')->orderBy('id')->get(['id', 'owner_id', 'payload_json']) as $b) {
    $p = json_decode($b->payload_json, true); if (!isset($p['images']) || !is_array($p['images'])) continue;
    // pool: attractions / foods / image items on the same page with a name + image
    $pool = [];
    foreach (DB::table('rg_content_blocks')->where('owner_type', 'seo_page')->where('owner_id', $b->owner_id)->whereIn('block_type', ['attractions', 'foods_to_try', 'place_history', 'image'])->get(['block_type', 'payload_json']) as $o) {
        $op = json_decode($o->payload_json, true); if (!is_array($op)) continue;
        $items = $o->block_type === 'image' ? [['name' => $op['alt'] ?? '', 'image' => $op['src'] ?? '']] : ($op['items'] ?? []);
        foreach ((array) $items as $it) {
            if (!is_array($it)) continue;
            $img = $it['image'] ?? ($it['src'] ?? ''); $name = trim((string) ($it['name'] ?? ($it['title'] ?? '')));
            if ($img && $name && !preg_match($generic, $name) && !str_contains($img, 'generic.jpg') && is_file(storage_path('app/public/' . ltrim(preg_replace('~^/?storage/~', '', $norm($img)), '/')))) $pool[$norm($img)] = $name;
        }
    }
    // secondary pool: tourist spots tied to this page's keyword
    $kw = DB::table('rg_seo_pages')->where('id', $b->owner_id)->value('keyword_id');
    if ($kw) foreach (DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->where('s.keyword_id', $kw)->get(['s.name', 'm.path']) as $sp) {
        $src = '/storage/' . ltrim($sp->path, '/');
        if (!isset($pool[$src]) && !str_contains($src, 'generic.jpg') && is_file(storage_path('app/public/' . ltrim($sp->path, '/')))) $pool[$src] = $sp->name;
    }
    // tertiary pool: same-region tourist spots, best title-word matches first
    if (!$pool && $kw) {
        $kwRow = DB::table('rg_keywords')->where('id', $kw)->first(['cluster_tag', 'phrase']);
        $title = strtolower((string) DB::table('rg_seo_pages')->where('id', $b->owner_id)->value('title') . ' ' . ($kwRow->phrase ?? ''));
        $toks = array_filter(preg_split('/[^a-z0-9]+/', $title), fn ($t) => strlen($t) > 3 && !in_array($t, ['resort', 'resorts', 'hotel', 'hotels', 'beach', 'tourist', 'spot', 'spots', 'near', 'with', 'philippines', 'restaurant', 'private', 'cheap', 'best']));
        $cands = [];
        if ($kwRow && $kwRow->cluster_tag) foreach (DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->where('s.cluster_tag', $kwRow->cluster_tag)->get(['s.name', 's.location', 'm.path']) as $sp) {
            if (str_contains($sp->path, 'generic.jpg') || !is_file(storage_path('app/public/' . ltrim($sp->path, '/')))) continue;
            $hay = strtolower($sp->name . ' ' . $sp->location); $score = 0;
            foreach ($toks as $t) if (str_contains($hay, $t)) $score++;
            $cands[] = [$score, crc32($sp->name . $b->owner_id), '/storage/' . ltrim($sp->path, '/'), $sp->name . ($sp->location ? ' (' . $sp->location . ')' : '')];
        }
        if (!array_filter($cands, fn ($c) => $c[0] > 0)) {
            // region unknown or no match there: any spot nationwide whose name/town matches the page title
            $cands = [];
            foreach (DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->get(['s.name', 's.location', 'm.path']) as $sp) {
                if (str_contains($sp->path, 'generic.jpg') || !is_file(storage_path('app/public/' . ltrim($sp->path, '/')))) continue;
                $hay = strtolower($sp->name . ' ' . $sp->location); $score = 0;
                foreach ($toks as $t) if (str_contains($hay, $t)) $score++;
                if ($score > 0) $cands[] = [$score, crc32($sp->name . $b->owner_id), '/storage/' . ltrim($sp->path, '/'), $sp->name . ($sp->location ? ' (' . $sp->location . ')' : '')];
            }
        }
        usort($cands, fn ($a, $c) => [$c[0], $a[1]] <=> [$a[0], $c[1]]);
        foreach (array_slice($cands, 0, 6) as $c) $pool[$c[2]] = $c[3];
    }
    $used = array_map(fn ($im) => $norm($im['src'] ?? ''), $p['images']);
    $pool = array_diff_key($pool, array_flip($used));
    $changed = false; $hasGeneric = false;
    foreach ($p['images'] as &$im) {
        $stats['slides']++;
        $cap = trim((string) (($im['caption_title'] ?? '') ?: ($im['alt'] ?? '')));
        if ($cap === '' || !preg_match($generic, $cap)) continue;
        $stats['generic']++; $hasGeneric = true;
        if (!$pool) { $stats['no_pool']++; continue; }
        $src = array_key_first($pool); $name = $pool[$src]; unset($pool[$src]);
        $im['src'] = $src; $im['caption_title'] = $name; $im['alt'] = $name;
        $credit = DB::table('rg_media')->where('path', ltrim(preg_replace('~^/storage/~', '', $src), '/'))->first(['credit', 'source_url']);
        $im['caption'] = $credit->credit ?? ''; if (array_key_exists('credit_url', $im) || $credit) $im['credit_url'] = $credit->source_url ?? '';
        $stats['fixed']++; $changed = true;
    }
    unset($im);
    if ($hasGeneric) $stats['pages']++;
    if ($changed && $apply) { $backup[$b->id] = $b->payload_json; DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]); }
}
if ($apply) file_put_contents(__DIR__ . '/generic-slides-backup-' . date('His') . '.json', json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo json_encode($stats), $apply ? " (applied)" : " (dry run)", "\n";
