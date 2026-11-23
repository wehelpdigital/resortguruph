<?php
/**
 * House rule: no prices (rates go stale and are not verified). Removes peso amounts from
 * blog posts and guide text: the price clause where the sentence stands without it
 * ("..., fare around PHP 200 to 250." -> "..."), else the whole sentence.
 *
 *   php tools/content-audit/prices.php            dry run (prints before/after)
 *   php tools/content-audit/prices.php --apply    write (old values saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$verbose = in_array('-v', $argv, true);

const PRICE = '(?:PHP|Php|₱)\s?\d[\d,]*(?:\.\d+)?(?:\s*(?:to|-|–|and)\s*(?:PHP|Php|₱)?\s?\d[\d,]*(?:\.\d+)?)?(?:\s*(?:pesos?))?(?:\s+(?:per|a|each)\s+\w+)?|\d[\d,]*(?:\s*(?:to|-|–)\s*\d[\d,]*)?\s*pesos?\b';

$log = [];
/** Remove prices from the sentences of an HTML (or plain) string. */
function strip_prices(string $html, array &$log): string
{
    if (!preg_match('~' . PRICE . '~u', $html)) return $html;
    $fixRun = function (string $run) use (&$log): string {
        if (!preg_match('~' . PRICE . '~u', $run)) return $run;
        $parts = preg_split('~(?<=[.!?])(\s+)(?=[A-Z"“(])~u', $run, -1, PREG_SPLIT_DELIM_CAPTURE);
        $out = '';
        for ($i = 0; $i < count($parts); $i += 2) {
            $sentence = $parts[$i];
            $gap = $parts[$i + 1] ?? '';
            if (!preg_match('~' . PRICE . '~u', $sentence)) { $out .= $sentence . $gap; continue; }
            $orig = $sentence;
            $price = '(?:' . PRICE . ')';
            // "Fare runs around PHP 150 to 200, travel time around two hours." -> "Travel time is around two hours."
            if (preg_match('~^(?:The\s+)?(?:fare|fares|entrance|entry|fee|ride)\b[^,]*?' . $price . '[^,]*?(?:,\s*|\s+and\s+)((?:the\s+)?(?:travel time|trip|ride|drive)\b.+)$~iu', $sentence, $tm)) {
                $rest = (string) preg_replace('~^(travel time)\s+(?!is\b)~iu', '$1 is ', $tm[1]);
                $sentence = mb_strtoupper(mb_substr($rest, 0, 1)) . mb_substr($rest, 1);
            }
            // "(PHP 50)" asides
            $sentence = (string) preg_replace('~\s*\([^()]*?' . $price . '[^()]*\)~u', '', $sentence);
            // "... and PHP 50 fare."
            $sentence = (string) preg_replace('~\s+and\s+' . $price . '(?:\s+(?:fare|fee|each way|one way|round trip))?(?=[.,;!?])~iu', '', $sentence);
            // a closing clause after a comma: ", fare around PHP 200 to 250."
            $sentence = (string) preg_replace('~,\s*[^,;]*?' . $price . '[^,;.!?]*(?=[.!?]\s*$)~u', '', $sentence);
            if (preg_match('~' . $price . '~u', $sentence) || mb_strlen(trim($sentence)) < 30) $sentence = ''; // the price is the point: drop the sentence
            $log[] = [trim($orig), trim($sentence)];
            if ($sentence !== '') $out .= $sentence . $gap;
            elseif ($out !== '' && $gap === '') $out = rtrim($out); // last sentence dropped: no trailing space
        }
        return $out;
    };
    if (!str_contains($html, '<')) return $fixRun($html);
    return (string) preg_replace_callback('~(?<=>)([^<>]+)(?=<)~u', fn ($m) => $fixRun($m[1]), $html);
}

$backup = [];
$rows = 0;
foreach (['rg_blog_posts' => ['content_html', 'excerpt', 'meta_description'], 'rg_seo_pages' => ['tldr', 'meta_description', 'intro_html', 'body_html'], 'rg_fiestas' => ['summary']] as $table => $cols) {
    foreach ($cols as $col) {
        foreach (DB::table($table)->where(fn ($q) => $q->where($col, 'like', '%PHP %')->orWhere($col, 'like', '%₱%')->orWhere($col, 'like', '%peso%'))->get(['id', $col]) as $r) {
            $new = strip_prices((string) $r->$col, $log);
            $new = (string) preg_replace(['~\s{2,}~', '~<p>\s*</p>~', '~<li>\s*</li>~'], [' ', '', ''], $new);
            if ($new === (string) $r->$col) continue;
            $backup[] = ['table' => $table, 'id' => $r->id, $col => $r->$col];
            $rows++;
            if ($apply) DB::table($table)->where('id', $r->id)->update([$col => $new]);
        }
    }
}
foreach (DB::table('rg_content_blocks')->where(fn ($q) => $q->where('payload_json', 'like', '%PHP %')->orWhere('payload_json', 'like', '%\\\\u20b1%')->orWhere('payload_json', 'like', '%₱%')->orWhere('payload_json', 'like', '%peso%'))->get() as $b) {
    $p = json_decode($b->payload_json, true);
    if (!is_array($p)) continue;
    $before = json_encode($p);
    // fact cards whose figure is a price go as a whole ("Per person: PHP 200-2,000")
    foreach (['cards', 'items', 'facts'] as $listKey) {
        if (!isset($p[$listKey]) || !is_array($p[$listKey])) continue;
        $p[$listKey] = array_values(array_filter($p[$listKey], function ($card) use (&$log) {
            if (!is_array($card)) return true;
            foreach (['big', 'value', 'label', 'title'] as $f) if (isset($card[$f]) && is_string($card[$f]) && preg_match('~' . PRICE . '~u', $card[$f])) { $log[] = [json_encode($card, JSON_UNESCAPED_UNICODE), '']; return false; }
            return true;
        }));
    }
    array_walk_recursive($p, function (&$v, $k) use (&$log) {
        if (is_string($v) && !in_array($k, ['src', 'url', 'embed_url', 'credit_url'], true)) $v = strip_prices($v, $log);
    });
    if (json_encode($p) === $before) continue;
    $backup[] = (array) $b;
    $rows++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}

$cut = count(array_filter($log, fn ($l) => $l[1] === ''));
echo count($log) . " sentences with prices in $rows rows: " . (count($log) - $cut) . " trimmed, $cut dropped\n";
if ($verbose) foreach ($log as [$a, $b]) echo "- $a\n+ " . ($b === '' ? '(dropped)' : $b) . "\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/prices-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
}
