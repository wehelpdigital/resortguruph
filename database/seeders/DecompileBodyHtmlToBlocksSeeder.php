<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Converts seeded body_html + hero_html + intro_html + faq_json on every
 * rg_seo_pages row into discrete rg_content_blocks records so the mother
 * admin builder shows the same structured content the public site
 * already renders. Idempotent: skips pages that already have any blocks.
 *
 * For each page we emit blocks in this order (matches the public page):
 *   1. hero_slider (from hero_html, if present)
 *   2. listing_block   — admin-visible representation of the listings band
 *   3. rich_text(intro_html), if present
 *   4. Parsed body_html: editor_rating | quick_facts | attractions
 *      | how_to_get_to | rich_text fallback per h2 boundary
 *   5. faq (from faq_json), if present
 *
 * Anything the parser fails on is preserved as a custom_html block so we
 * never silently lose content. Run with:
 *     php artisan db:seed --class=DecompileBodyHtmlToBlocksSeeder
 */
class DecompileBodyHtmlToBlocksSeeder extends Seeder
{
    /** Map quick-facts background hex → palette key in BlockRenderer. */
    private const QF_COLORS = [
        '#eff6ff' => 'blue',
        '#ecfdf5' => 'emerald',
        '#fff1f2' => 'rose',
        '#fffbeb' => 'amber',
        '#f5f3ff' => 'violet',
        '#f8fafc' => 'slate',
    ];

    /** Map quick-fact label text → icon slug (since SVG paths aren't easy to reverse). */
    private const QF_LABEL_ICONS = [
        'per person'       => 'money',
        'easiest window'   => 'clock',
        'avoid'            => 'warning',
        'avoid (weekends)' => 'warning',
        'traffic'          => 'traffic',
        'food zones'       => 'food',
        'season'           => 'calendar',
        'best for'         => 'star',
    ];

    /** Map transport-method title text → icon slug. */
    private const HTGT_ICONS = [
        'by bus'         => 'bus',
        'by car'         => 'car',
        'by private car' => 'car',
        'by jeepney'     => 'jeepney',
        'by tricycle'    => 'tricycle',
        'by plane'       => 'plane',
        'by train'       => 'train',
        'by boat'        => 'boat',
        'by ferry'       => 'boat',
        'by grab'        => 'car',
        'by van'         => 'bus',
    ];

    public function run(): void
    {
        $pages = DB::table('rg_seo_pages')
            ->select('id', 'hero_html', 'intro_html', 'body_html', 'faq_json')
            ->orderBy('id')
            ->get();

        $now = Carbon::now()->toDateTimeString();
        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalBlocks = 0;
        $totalErrors = 0;

        foreach ($pages as $page) {
            $existing = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->exists();
            if ($existing) {
                $totalSkipped++;
                continue;
            }

            try {
                $blocks = $this->decompilePage($page);
            } catch (\Throwable $e) {
                $this->command->error("Page {$page->id} parse error: " . $e->getMessage());
                $totalErrors++;
                continue;
            }
            if (!$blocks) {
                $totalSkipped++;
                continue;
            }

            DB::transaction(function () use ($page, $blocks, $now) {
                $rows = [];
                foreach ($blocks as $i => $b) {
                    $rows[] = [
                        'owner_type'   => 'seo_page',
                        'owner_id'     => $page->id,
                        'sort_order'   => $i + 1,
                        'block_type'   => $b['block_type'],
                        'payload_json' => json_encode(
                            $b['payload'],
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        ),
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }
                DB::table('rg_content_blocks')->insert($rows);
            });

            $totalBlocks += count($blocks);
            $totalProcessed++;
            if ($totalProcessed % 50 === 0) {
                $this->command->info("  {$totalProcessed} pages processed ({$totalBlocks} blocks)...");
            }
        }

        $this->command->info(
            "Done. Pages: {$totalProcessed} decompiled | {$totalSkipped} skipped | {$totalErrors} errors | {$totalBlocks} blocks created."
        );
    }

    /**
     * Build the full list of blocks for one page. Returns an array of
     * ['block_type' => ..., 'payload' => [...]] in the order they should
     * appear in the builder canvas.
     */
    private function decompilePage(object $page): array
    {
        $blocks = [];

        // 1. hero_slider from hero_html. The seeded markup uses figcaption
        //    + outbound photo-credit links, which the structured payload
        //    doesn't carry — preserve as custom_html so admins see the
        //    actual content and can swap to a structured hero_slider later.
        if (!empty($page->hero_html) && trim($page->hero_html) !== '') {
            $blocks[] = [
                'block_type' => 'custom_html',
                'payload'    => ['html' => $page->hero_html],
            ];
        }

        // 2. listing_block — represents the live listings band. The public
        //    page already renders it via the partial; this block lets admin
        //    see it in the builder timeline and reorder around it.
        $blocks[] = [
            'block_type' => 'listing_block',
            'payload'    => ['area' => '', 'note' => 'Renders live from rg_restaurant_listings / rg_listings'],
        ];

        // 3. intro_html — typically a wrapper with the H2 lay-of-the-land
        //    intro. Save as rich_text since it's pure prose.
        if (!empty($page->intro_html) && trim($page->intro_html) !== '') {
            $blocks[] = [
                'block_type' => 'rich_text',
                'payload'    => ['html' => trim($page->intro_html)],
            ];
        }

        // 4. body_html — split + parse the structured sections, fall back
        //    to rich_text per remaining h2-bounded section.
        if (!empty($page->body_html)) {
            $bodyBlocks = $this->parseBodyHtml($page->body_html);
            $blocks = array_merge($blocks, $bodyBlocks);
        }

        // 5. faq from faq_json (if not already inside body_html as a block).
        if (!empty($page->faq_json)) {
            $decoded = json_decode($page->faq_json, true);
            if (is_array($decoded) && $decoded) {
                $blocks[] = [
                    'block_type' => 'faq',
                    'payload'    => [
                        'heading' => 'Frequently Asked Questions',
                        'items'   => array_values(array_filter(
                            $decoded,
                            fn($i) => !empty($i['question'] ?? null)
                        )),
                    ],
                ];
            }
        }

        return $blocks;
    }

    /**
     * Extract structured sections from body_html and emit a flat block list.
     * Strategy: find each known marker, pull it out, record the block, then
     * walk what's left and split by h2 boundaries into rich_text blocks.
     */
    private function parseBodyHtml(string $body): array
    {
        $blocks = [];

        // ── Pass 1: editor_rating ─────────────────────────────────────
        // Find an opening <div> whose class begins "not-prose my-8 p-6
        // rounded-2xl bg-white border" AND whose inner text contains
        // "Resort Guru Editor". Match the balanced closing </div>.
        $body = $this->extractBalancedDiv(
            $body,
            '~<div class="not-prose my-8 p-6 rounded-2xl bg-white border border-slate-200">~',
            function ($match) {
                return str_contains($match, 'Resort Guru Editor');
            },
            function ($match) use (&$blocks) {
                $parsed = $this->parseEditorRating($match);
                if ($parsed && !empty($parsed['criteria'])) {
                    $blocks[] = ['block_type' => 'editor_rating', 'payload' => $parsed];
                } else {
                    $blocks[] = ['block_type' => 'custom_html', 'payload' => ['html' => $match]];
                }
            }
        );

        // ── Pass 2: "short version" gradient summary card ────────────
        // Preserved as custom_html — no structured type for it (yet).
        $body = $this->extractBalancedDiv(
            $body,
            '~<div class="not-prose my-8 p-6 rounded-2xl" style="background:linear-gradient~',
            null,
            function ($match) use (&$blocks) {
                $blocks[] = ['block_type' => 'custom_html', 'payload' => ['html' => $match]];
            }
        );

        // ── Pass 3: quick_facts strip ─────────────────────────────────
        // 4-card variant.
        $body = $this->extractBalancedDiv(
            $body,
            '~<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">~',
            null,
            function ($match) use (&$blocks) {
                $parsed = $this->parseQuickFacts($match);
                if ($parsed && !empty($parsed['cards'])) {
                    $blocks[] = ['block_type' => 'quick_facts', 'payload' => $parsed];
                } else {
                    $blocks[] = ['block_type' => 'custom_html', 'payload' => ['html' => $match]];
                }
            }
        );
        // 3-card legacy variant (pre-Traffic).
        $body = $this->extractBalancedDiv(
            $body,
            '~<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-3 gap-3">~',
            null,
            function ($match) use (&$blocks) {
                $parsed = $this->parseQuickFacts($match);
                if ($parsed && !empty($parsed['cards'])) {
                    $blocks[] = ['block_type' => 'quick_facts', 'payload' => $parsed];
                } else {
                    $blocks[] = ['block_type' => 'custom_html', 'payload' => ['html' => $match]];
                }
            }
        );

        // ── Pass 4: attractions (h2 "beyond the food" → grid) ─────────
        $body = $this->extractH2ToBalancedGrid(
            $body,
            'beyond the food',
            function ($match) use (&$blocks) {
                $parsed = $this->parseAttractions($match);
                if ($parsed && !empty($parsed['items'])) {
                    $blocks[] = ['block_type' => 'attractions', 'payload' => $parsed];
                } else {
                    $blocks[] = ['block_type' => 'custom_html', 'payload' => ['html' => $match]];
                }
            }
        );

        // ── Pass 5: how_to_get_to (h2 "How to get to" → grid) ────────
        $body = $this->extractH2ToBalancedGrid(
            $body,
            'How to get to',
            function ($match) use (&$blocks) {
                $parsed = $this->parseHowToGetTo($match);
                if ($parsed && !empty($parsed['methods'])) {
                    $blocks[] = ['block_type' => 'how_to_get_to', 'payload' => $parsed];
                } else {
                    $blocks[] = ['block_type' => 'custom_html', 'payload' => ['html' => $match]];
                }
            }
        );

        // ── Pass 6: any remaining content split by <h2> boundaries ───
        $remaining = trim($body);
        if ($remaining !== '') {
            $sections = $this->splitByH2($remaining);
            foreach ($sections as $section) {
                $section = trim($section);
                if ($section === '' || trim(strip_tags($section)) === '') continue;
                $blocks[] = ['block_type' => 'rich_text', 'payload' => ['html' => $section]];
            }
        }

        return $blocks;
    }

    /**
     * Find the first <div> matching $openPattern, walk forward counting
     * <div> open and </div> close tags until balanced, optionally call
     * $accept($match) to validate (return true to keep, false to skip),
     * and pass the balanced match HTML to $handler. Returns $body with
     * the match removed.
     *
     * This is more robust than nested-tag regex which doesn't handle
     * arbitrary depth.
     */
    private function extractBalancedDiv(
        string $body,
        string $openPattern,
        ?callable $accept,
        callable $handler
    ): string {
        if (!preg_match($openPattern, $body, $m, PREG_OFFSET_CAPTURE)) {
            return $body;
        }
        $start = $m[0][1];
        $openLen = strlen($m[0][0]);
        $end = $this->findBalancedDivClose($body, $start + $openLen);
        if ($end < 0) return $body;
        $match = substr($body, $start, $end - $start);
        if ($accept && !$accept($match)) {
            // Skip this hit but search the rest after it so we don't infinite
            // loop on the same opener.
            $rest = substr($body, $end);
            $restProcessed = $this->extractBalancedDiv($rest, $openPattern, $accept, $handler);
            return substr($body, 0, $end) . $restProcessed;
        }
        $handler($match);
        return substr($body, 0, $start) . substr($body, $end);
    }

    /**
     * Walk $body from $pos counting <div>/<\div>. Returns the byte index
     * just past the </div> that balances the implicit "depth = 1" at $pos.
     * Returns -1 if the document doesn't balance (truncated / malformed).
     */
    private function findBalancedDivClose(string $body, int $pos): int
    {
        $depth = 1;
        $len = strlen($body);
        while ($pos < $len) {
            $nextOpen = stripos($body, '<div', $pos);
            $nextClose = stripos($body, '</div>', $pos);
            if ($nextClose === false) return -1;
            if ($nextOpen !== false && $nextOpen < $nextClose) {
                // Skip past the opening tag (no need to find the > since
                // div has attributes — just advance past "<div" to allow
                // self-counting to keep going).
                $depth++;
                $pos = $nextOpen + 4;
            } else {
                $depth--;
                $pos = $nextClose + 6;
                if ($depth === 0) return $pos;
            }
        }
        return -1;
    }

    /**
     * Find an <h2> containing $needle (case-insensitive), then capture
     * everything from that h2 up through the balanced close of the next
     * grid div that follows it. Used for "beyond the food" and "How to
     * get to" sections which the seeder emits as a bare h2 + intro <p> +
     * grid div, NOT wrapped in a <section>.
     */
    private function extractH2ToBalancedGrid(string $body, string $needle, callable $handler): string
    {
        $needleEsc = preg_quote($needle, '~');
        if (!preg_match('~<h2[^>]*>[^<]*' . $needleEsc . '[^<]*</h2>~i', $body, $m, PREG_OFFSET_CAPTURE)) {
            return $body;
        }
        $h2Start = $m[0][1];
        $afterH2 = $h2Start + strlen($m[0][0]);

        // From the h2, find the next opening div with class containing
        // "grid grid-cols" (the card grid). Stop at the next <h2> so we
        // don't accidentally cross into another section.
        $nextH2 = stripos($body, '<h2', $afterH2);
        $haystack = $nextH2 !== false ? substr($body, $afterH2, $nextH2 - $afterH2) : substr($body, $afterH2);
        if (!preg_match('~<div class="[^"]*grid grid-cols-1 md:grid-cols-[23][^"]*"~', $haystack, $gm, PREG_OFFSET_CAPTURE)) {
            return $body;
        }
        $gridStartInHaystack = $gm[0][1];
        $gridOpenLen = strlen($gm[0][0]);
        // Walk past the opening tag's `>`.
        $absGridOpenStart = $afterH2 + $gridStartInHaystack;
        $gtPos = strpos($body, '>', $absGridOpenStart + $gridOpenLen - 1);
        if ($gtPos === false) return $body;
        $gridEnd = $this->findBalancedDivClose($body, $gtPos + 1);
        if ($gridEnd < 0) return $body;

        $match = substr($body, $h2Start, $gridEnd - $h2Start);
        $handler($match);
        return substr($body, 0, $h2Start) . substr($body, $gridEnd);
    }

    /**
     * Split a stream of HTML by <h2> tags. Each chunk starts with its h2
     * and contains the prose under it until the next h2 or end.
     */
    private function splitByH2(string $html): array
    {
        $parts = preg_split('~(?=<h2\b)~i', $html, -1, PREG_SPLIT_NO_EMPTY);
        return $parts ?: [$html];
    }

    /**
     * Pull overall score + criteria + summary from the Editor Rating card.
     */
    private function parseEditorRating(string $html): array
    {
        $payload = [
            'title'    => 'Resort Guru Editor Rating',
            'overall'  => 4.0,
            'criteria' => [],
            'summary'  => '',
        ];

        if (preg_match('~Resort Guru Editor[^<]*~i', $html, $m)) {
            $payload['title'] = trim(preg_replace('/\s+/', ' ', $m[0]));
        }
        if (preg_match('~<div class="text-5xl font-black[^"]*"[^>]*>\s*([\d.]+)\s*</div>~', $html, $m)) {
            $payload['overall'] = (float) $m[1];
        }

        // Criteria grid: each cell is <div>...<div class="text-2xl ...">SCORE</div>
        // <div class="text-[10px] ...">NAME</div></div>.
        if (preg_match_all(
            '~<div class="text-2xl font-bold text-slate-800[^"]*"[^>]*>\s*([\d.]+)\s*</div>\s*<div class="text-\[10px\][^"]*"[^>]*>\s*([^<]+?)\s*</div>~',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $cell) {
                $payload['criteria'][] = [
                    'name'  => trim($cell[2]),
                    'score' => (float) $cell[1],
                ];
            }
        }

        // Optional summary <p> near the end. The seeder didn't always emit
        // one, so this is best-effort.
        if (preg_match('~<p class="text-sm text-slate-600[^"]*"[^>]*>(.+?)</p>~s', $html, $m)) {
            $payload['summary'] = trim(strip_tags($m[1]));
        }

        return $payload;
    }

    /**
     * Walk each card in the quick-facts strip and produce the
     * structured cards array (label + big + detail + color + icon).
     */
    private function parseQuickFacts(string $html): ?array
    {
        $cards = [];

        // Each card: <div class="rounded-lg p-4 text-center" style="background:#HEX;border:..."> ... <div class="text-2xl font-bold" style="color:#HEX">BIG</div> <div class="text-[10px] uppercase ..." style="color:#HEX">LABEL</div> <div class="text-xs text-slate-600 mt-1">DETAIL</div> </div>
        if (preg_match_all(
            '~<div[^>]*class="rounded-lg p-4 text-center"[^>]*style="background:(#[0-9a-fA-F]+)[^"]*"[^>]*>(.*?)(?=<div[^>]*class="rounded-lg p-4 text-center"|</div>\s*</div>\s*(?:</div>)?\s*$)~s',
            $html,
            $rawCards,
            PREG_SET_ORDER
        )) {
            foreach ($rawCards as $rc) {
                $bg = strtolower($rc[1]);
                $color = self::QF_COLORS[$bg] ?? 'blue';
                $inner = $rc[2];

                $big = '';
                if (preg_match('~<div class="text-2xl font-bold"[^>]*>\s*(.+?)\s*</div>~s', $inner, $m)) {
                    $big = $this->cleanText($m[1]);
                }
                $label = '';
                if (preg_match('~<div class="text-\[10px\] uppercase[^"]*"[^>]*>\s*(.+?)\s*</div>~s', $inner, $m)) {
                    $label = $this->cleanText($m[1]);
                }
                $detail = '';
                if (preg_match('~<div class="text-xs text-slate-600 mt-1">\s*(.+?)\s*</div>~s', $inner, $m)) {
                    $detail = $this->cleanText($m[1]);
                }

                $iconKey = strtolower($label);
                $icon = self::QF_LABEL_ICONS[$iconKey] ?? 'info';

                $cards[] = compact('icon', 'big', 'label', 'detail', 'color');
            }
        }

        if (!$cards) return null;
        return ['heading' => '', 'cards' => $cards];
    }

    /**
     * Pull the heading, intro, and items[] from an attractions section.
     */
    private function parseAttractions(string $html): ?array
    {
        $payload = ['heading' => '', 'intro' => '', 'items' => []];

        if (preg_match('~<h2[^>]*>\s*(.+?)\s*</h2>~s', $html, $m)) {
            $payload['heading'] = $this->cleanText($m[1]);
        }
        // The intro paragraph sits between the h2 and the grid wrapper.
        if (preg_match('~</h2>\s*<p[^>]*>\s*(.+?)\s*</p>~s', $html, $m)) {
            $payload['intro'] = $this->cleanText(strip_tags($m[1]));
        }

        // Each card: optional aspect img + p-4 block with eyebrow + h3 + p
        // + optional outbound link.
        if (preg_match_all(
            '~<div class="rounded-xl border border-slate-200 bg-white overflow-hidden">(.*?)</div>\s*</div>~s',
            $html,
            $cards,
            PREG_SET_ORDER
        )) {
            foreach ($cards as $card) {
                $inner = $card[1];
                $item = ['name' => '', 'image' => '', 'short' => '', 'blurb' => '', 'url' => ''];
                if (preg_match('~<img src="([^"]+)"~', $inner, $m)) {
                    $item['image'] = $m[1];
                }
                if (preg_match('~<div class="text-\[10px\] uppercase[^"]*text-emerald-700[^"]*"[^>]*>\s*(.+?)\s*</div>~s', $inner, $m)) {
                    $item['short'] = $this->cleanText($m[1]);
                }
                if (preg_match('~<h3[^>]*>\s*(.+?)\s*</h3>~s', $inner, $m)) {
                    $item['name'] = $this->cleanText(strip_tags($m[1]));
                }
                if (preg_match('~<p class="text-sm text-slate-600[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $inner, $m)) {
                    $item['blurb'] = $this->cleanText(strip_tags($m[1]));
                }
                if (preg_match('~<a href="([^"]+)"[^>]*rel="[^"]*nofollow~', $inner, $m)) {
                    $item['url'] = $m[1];
                }
                if ($item['name'] !== '') {
                    $payload['items'][] = $item;
                }
            }
        }

        return $payload;
    }

    /**
     * Pull the heading, intro, and methods[] from a how-to-get-to section.
     */
    private function parseHowToGetTo(string $html): ?array
    {
        $payload = ['heading' => '', 'intro' => '', 'methods' => []];

        if (preg_match('~<h2[^>]*>\s*(.+?)\s*</h2>~s', $html, $m)) {
            $payload['heading'] = $this->cleanText($m[1]);
        }
        if (preg_match('~</h2>\s*<p[^>]*>\s*(.+?)\s*</p>~s', $html, $m)) {
            $payload['intro'] = $this->cleanText(strip_tags($m[1]));
        }

        // Each method card: title in text-[10px] uppercase div (or a bare
        // text-slate-500 div), detail in following <p>.
        if (preg_match_all(
            '~<div class="rounded-xl border border-slate-200 bg-white p-4">(.*?)</div>\s*(?=<div class="rounded-xl border border-slate-200 bg-white p-4">|</div>)~s',
            $html,
            $methods,
            PREG_SET_ORDER
        )) {
            foreach ($methods as $method) {
                $inner = $method[1];
                $title = '';
                if (preg_match('~<div class="text-\[10px\] uppercase[^"]*"[^>]*>\s*(.+?)\s*</div>~s', $inner, $m)) {
                    $title = $this->cleanText($m[1]);
                }
                $detail = '';
                if (preg_match('~<p[^>]*>\s*(.+?)\s*</p>~s', $inner, $m)) {
                    $detail = $this->cleanText(strip_tags($m[1]));
                }
                if ($title === '') continue;
                $icon = self::HTGT_ICONS[strtolower($title)] ?? 'car';
                $payload['methods'][] = compact('icon', 'title', 'detail');
            }
        }

        return $payload;
    }

    private function cleanText(string $s): string
    {
        // Collapse whitespace + strip stray entities → clean inline text.
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = str_replace(['&amp;', '&nbsp;', '&ndash;'], ['&', ' ', '-'], $s);
        return trim($s);
    }
}
