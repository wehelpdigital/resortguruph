<?php
/**
 * Restaurant guides carry a "From the Tourist Guide PH blog" list that was imported as
 * pieces: one text section per post, with a blog card's <a> tag split across them
 * ("<a href=...>\n\nFrom the blog" in one block, "...</a><a href=...>" in the next).
 * It renders as loose headings with stray "From the blog" lines and broken links.
 * This rebuilds each list as one "Related guides on our blog" section (shown as story
 * cards by GuideHtml::reads) and removes the pieces. It also drops the dangling
 * "Related reading" line left at the end of the map text on the same guides.
 *
 *   php tools/content-audit/blog_teasers.php            dry run
 *   php tools/content-audit/blog_teasers.php --apply    write (old rows saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$e = fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$backup = [];
$pages = 0;
$dropped = 0;

$owners = DB::table('rg_content_blocks')->where('owner_type', 'seo_page')->where('block_type', 'text_section')
    ->where('payload_json', 'like', '%<a href%')->distinct()->pluck('owner_id');
$guides = 0;

foreach ($owners as $oid) {
    $blocks = DB::table('rg_content_blocks')->where('owner_type', 'seo_page')->where('owner_id', $oid)->orderBy('sort_order')->orderBy('id')->get()->values();
    $p = fn ($b) => json_decode($b->payload_json, true) ?: [];
    // a piece: an h3 text section that closes a card opened in the block before it
    $isPiece = fn ($b) => $b->block_type === 'text_section' && ($p($b)['heading_level'] ?? '') === 'h3' && str_contains((string) ($p($b)['body'] ?? ''), '</a>');
    // a header: a text section whose last card link is left open
    $isHead = function ($b) use ($p) {
        $body = (string) ($p($b)['body'] ?? '');
        return $b->block_type === 'text_section' && ($p($b)['heading_level'] ?? 'h2') !== 'h3'
            && ($open = strrpos($body, '<a href')) !== false && (($close = strrpos($body, '</a>')) === false || $open > $close);
    };
    $used = [];
    foreach ($blocks as $hi => $head) {
        if (!$isHead($head) || isset($used[$head->id])) continue;
        // a piece that closes its card without opening another ends the chain
        $opensNext = fn ($b) => (bool) preg_match('~</a>.*<a href~s', (string) ($p($b)['body'] ?? ''));
        $pieces = [];
        for ($i = $hi + 1; $i < count($blocks) && $isPiece($blocks[$i]) && !isset($used[$blocks[$i]->id]); $i++) {
            $pieces[] = $blocks[$i];
            if (!$opensNext($blocks[$i])) break;
        }
        if (!$pieces) {
            for ($i = $hi - 1; $i >= 0 && $isPiece($blocks[$i]) && !isset($used[$blocks[$i]->id]); $i--) {
                if ($pieces && !$opensNext($blocks[$i])) break; // the end of an earlier chain
                array_unshift($pieces, $blocks[$i]);
            }
        }
        if (!$pieces) continue;
        foreach ($pieces as $b) $used[$b->id] = true;
        $hp = $p($head);
        // the links in order: the header opens the first card, each piece opens the next
        preg_match_all('~<a href="([^"]*)"~i', (string) ($hp['body'] ?? ''), $m);
        $links = $m[1];
        $items = [];
        foreach ($pieces as $k => $b) {
            $pl = $p($b);
            preg_match_all('~<a href="([^"]*)"~i', (string) ($pl['body'] ?? ''), $m2);
            $items[] = ['title' => trim((string) ($pl['heading'] ?? '')), 'text' => trim(strip_tags((string) preg_replace('~</a>.*$~s', '', (string) ($pl['body'] ?? '')))), 'href' => $links[$k] ?? ''];
            foreach ($m2[1] as $h) $links[] = $h;
        }
        $isBlog = str_contains((string) ($hp['body'] ?? ''), 'From the blog') || preg_match('~/blog/~', (string) ($items[0]['href'] ?? ''));
        $intro = trim(strip_tags((string) preg_replace('~<a href.*$~s', '', (string) ($hp['body'] ?? ''))));
        if ($isBlog) {
            $cards = '';
            foreach ($items as $it) {
                if (!preg_match('~/blog/([a-z0-9-]+)~i', $it['href'], $sm)) continue;
                $row = DB::table('rg_blog_posts')->where('slug', $sm[1])->where('status', 'published')->first(['slug', 'title', 'excerpt', 'cover_path']);
                if (!$row) continue;
                $cover = $row->cover_path ? '/storage/' . ltrim($row->cover_path, '/') : '';
                $excerpt = (string) preg_replace('~\.{2,}$|…$~u', '.', rtrim($it['text'] !== '' ? $it['text'] : (string) $row->excerpt));
                $cards .= '<a href="/blog/' . $e($row->slug) . '">' . ($cover !== '' ? '<img src="' . $e($cover) . '" alt="' . $e($row->title) . '">' : '')
                    . '<h3>' . $e($row->title) . '</h3><p>' . $e($excerpt) . '</p></a>';
            }
            $new = $cards === '' ? null : ['block_type' => 'custom_html', 'payload' => ['html' => '<section><h2>Related guides on our blog</h2><p>Longer reads from our blog on the same place, with day plans and the small practical notes that do not fit in a guide.</p>' . $cards . '</section>']];
            $pages++;
        } else {
            // links to other guides ("resort in cebu city"): a plain list, without the search counts the pieces carried
            $list = '';
            foreach ($items as $it) {
                $href = (string) preg_replace('~^https?://[^/]+~i', '', $it['href']);
                if ($href === '' || $it['title'] === '') continue;
                $list .= '<li><a href="' . $e($href) . '">' . $e(ucwords($it['title'])) . '</a></li>';
            }
            $hp['body'] = $intro . ($list !== '' ? "\n\n<ul>" . $list . '</ul>' : '');
            $new = ['block_type' => 'text_section', 'payload' => $hp];
            $guides++;
        }
        $backup[] = (array) $head;
        foreach ($pieces as $b) $backup[] = (array) $b;
        if ($apply) {
            DB::transaction(function () use ($head, $pieces, $new) {
                if ($new === null) DB::table('rg_content_blocks')->where('id', $head->id)->delete();
                else DB::table('rg_content_blocks')->where('id', $head->id)->update(['block_type' => $new['block_type'], 'payload_json' => json_encode($new['payload'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
                DB::table('rg_content_blocks')->whereIn('id', array_map(fn ($b) => $b->id, $pieces))->delete();
            });
        }
    }
}

// "Related reading" left dangling at the end of a text section
foreach (DB::table('rg_content_blocks')->where('block_type', 'text_section')->where('payload_json', 'like', '%Related reading%')->get() as $b) {
    $pl = json_decode($b->payload_json, true) ?: [];
    $body = (string) ($pl['body'] ?? '');
    $new = (string) preg_replace('~\s*Related reading\s*$~', '', $body);
    if ($new === $body) continue;
    $pl['body'] = $new;
    $backup[] = (array) $b;
    $dropped++;
    if ($apply) DB::table('rg_content_blocks')->where('id', $b->id)->update(['payload_json' => json_encode($pl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'updated_at' => now()]);
}

echo "$pages blog lists rebuilt, $guides guide link lists rebuilt, $dropped 'Related reading' lines dropped
";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/blog-teasers-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
}
