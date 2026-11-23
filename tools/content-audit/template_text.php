<?php
/**
 * Template text that went out unfinished in seeded restaurant guides:
 *   "$area" left in the getting-there methods ("Many $area corners are walking-friendly")
 *     -> the place named in the block's own heading ("How to get to BGC")
 *   "X is reachable by the usual Metro Manila routes" on guides outside Metro Manila
 *     -> a sentence that fits any town
 *
 *   php tools/content-audit/template_text.php            dry run
 *   php tools/content-audit/template_text.php --apply    write (old rows saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$backup = [];
$count = ['area' => 0, 'routes' => 0];

$q = DB::table('rg_content_blocks as b')->join('rg_seo_pages as p', 'p.id', '=', 'b.owner_id')->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
    ->where('b.owner_type', 'seo_page');

foreach ((clone $q)->where('b.payload_json', 'like', '%$area%')->get(['b.*', 'p.slug']) as $b) {
    $p = json_decode($b->payload_json, true) ?: [];
    $place = preg_match('~^How to get to (.+)$~i', trim($p['heading'] ?? ''), $m) ? trim($m[1]) : '';
    if ($place === '') { echo "skip {$b->slug} #{$b->id}: no place in heading\n"; continue; }
    array_walk_recursive($p, function (&$v) use ($place) { if (is_string($v)) $v = str_replace('$area', $place, $v); });
    $backup[] = (array) $b;
    $count['area']++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}

// guides tagged metro-manila that are outside it (Nuvali, Dasmarinas, Bacoor, Taytay, Angeles, Nueva Ecija ...)
$outsideMM = '~nuvali|dasma|marquee|nueva-ecija|sm-bacoor|sta-rosa|taytay|valencia~';
foreach ((clone $q)->where('b.payload_json', 'like', '%usual Metro Manila routes%')->get(['b.*', 'p.slug', 'k.cluster_tag']) as $b) {
    if ($b->cluster_tag === 'metro-manila' && !preg_match($outsideMM, $b->slug)) continue;
    $p = json_decode($b->payload_json, true) ?: [];
    $p['body'] = preg_replace('~ is reachable by the usual Metro Manila routes plus the surrounding ride options\.~', ' is easy to reach once you are in town, usually a short tricycle, jeepney, or car ride from the center.', (string) ($p['body'] ?? ''));
    $backup[] = (array) $b;
    $count['routes']++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}

// "Best stays picks in Pangasinan lean toward north-luzon character over generic chains."
$chains = '~Best ([\w ]+?) picks in ([^.\n]+?) lean toward [^ ]+ character over generic chains\.~u';
$chainsTo = 'The best $1 in $2 feel local rather than like a generic chain.';
$count['chains'] = 0;
foreach ((clone $q)->where('b.payload_json', 'like', '%character over generic chains%')->get(['b.*', 'p.slug']) as $b) {
    $p = json_decode($b->payload_json, true) ?: [];
    array_walk_recursive($p, function (&$v) use ($chains, $chainsTo) { if (is_string($v)) $v = preg_replace($chains, $chainsTo, $v); });
    $backup[] = (array) $b;
    $count['chains']++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}
foreach (DB::table('rg_seo_pages')->where('tldr', 'like', '%character over generic chains%')->get(['id', 'tldr']) as $pg) {
    $backup[] = ['rg_seo_pages.id' => $pg->id, 'tldr' => $pg->tldr];
    if ($apply) DB::table('rg_seo_pages')->where('id', $pg->id)->update(['tldr' => preg_replace($chains, $chainsTo, $pg->tldr)]);
}

// a stray closing quote left at the end of a paragraph (festival guides: '... and unmoved."')
$count['quotes'] = 0;
foreach (DB::table('rg_content_blocks')->whereIn('block_type', ['text_section', 'short_version', 'rich_text'])->where('payload_json', 'like', '%.\\\\"%')->get() as $b) {
    $p = json_decode($b->payload_json, true) ?: [];
    $changed = false;
    array_walk_recursive($p, function (&$v, $k) use (&$changed) {
        if (!is_string($v) || in_array($k, ['html', 'src', 'url'], true)) return;
        $paras = preg_split("~(\n\s*\n)~", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($paras as &$para) {
            $t = rtrim($para);
            if (preg_match('~[.!?]"$~', $t) && substr_count($t, '"') % 2 === 1) { $para = substr($t, 0, -1) . substr($para, strlen($t)); $changed = true; }
        }
        $v = implode('', $paras);
    });
    if (!$changed) continue;
    $backup[] = (array) $b;
    $count['quotes']++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}

echo "\$area filled: {$count['area']}, Metro Manila routes line fixed: {$count['routes']}, chain-character line rewritten: {$count['chains']}, stray quotes: {$count['quotes']}\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/template-text-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
}
