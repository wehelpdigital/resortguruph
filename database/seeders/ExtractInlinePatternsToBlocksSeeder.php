<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Walks every rich_text and text_section block and extracts the inline
 * patterns the seeders embedded into prose:
 *   - <figure> with image + figcaption  → image block
 *   - dark-gradient pull-quote          → local_tip (color=dark)
 *   - light-aside Local tip card        → local_tip (color=amber)
 *   - flex-wrap colored span row        → tag_pills
 *   - overflow-x-auto table wrapper     → data_table
 *   - Google Maps iframe wrapper        → map_embed
 *   - "Compare picks" external-link box → external_guides
 *
 * Each extracted chunk becomes a NEW block inserted right after the
 * source (sort_order shifted). The source block keeps everything that
 * wasn't matched. Idempotent: re-runs don't re-extract the same chunk
 * because the source HTML no longer contains it.
 */
class ExtractInlinePatternsToBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $rows = DB::table('rg_content_blocks')
            ->whereIn('block_type', ['rich_text', 'text_section'])
            ->orderByDesc('id')   // reverse so sort-shifts don't collide
            ->get(['id', 'owner_type', 'owner_id', 'sort_order', 'block_type', 'payload_json']);

        $now = Carbon::now()->toDateTimeString();
        $scanned = 0;
        $blocksCreated = 0;
        $stats = ['image' => 0, 'local_tip_dark' => 0, 'local_tip_amber' => 0,
            'tag_pills' => 0, 'data_table' => 0, 'map_embed' => 0, 'external_guides' => 0];

        foreach ($rows as $row) {
            $scanned++;
            $payload = json_decode($row->payload_json, true) ?: [];
            $sourceHtml = $this->payloadToHtml($row->block_type, $payload);
            if ($sourceHtml === '') continue;

            [$cleaned, $extracted] = $this->extractAll($sourceHtml);
            if (!$extracted) continue;

            // Wrap in a deadlock-retrying transaction. Concurrent reads
            // from the iframe preview controller / page render cache
            // occasionally collide with the bulk sort_order increment.
            $attempt = 0;
            $maxAttempts = 5;
            retry:
            try {
            DB::transaction(function () use ($row, $payload, $cleaned, $extracted, $now, &$blocksCreated, &$stats) {
                // 1. Write the cleaned content back to the source block.
                if ($row->block_type === 'text_section') {
                    $payload['paragraphs'] = $this->htmlToParagraphs($cleaned);
                } else {
                    $payload['html'] = $cleaned;
                }
                DB::table('rg_content_blocks')
                    ->where('id', $row->id)
                    ->update([
                        'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at'   => $now,
                    ]);

                // 2. Shift everything after the source down by N (one per extracted block).
                $n = count($extracted);
                DB::table('rg_content_blocks')
                    ->where('owner_type', $row->owner_type)
                    ->where('owner_id', $row->owner_id)
                    ->where('sort_order', '>', $row->sort_order)
                    ->increment('sort_order', $n);

                // 3. Insert each new block right after the source, in order.
                foreach ($extracted as $offset => $entry) {
                    DB::table('rg_content_blocks')->insert([
                        'owner_type'   => $row->owner_type,
                        'owner_id'     => $row->owner_id,
                        'sort_order'   => $row->sort_order + 1 + $offset,
                        'block_type'   => $entry['block_type'],
                        'payload_json' => json_encode($entry['payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                    $blocksCreated++;
                    $statKey = $entry['stat_key'];
                    $stats[$statKey] = ($stats[$statKey] ?? 0) + 1;
                }
            });
            } catch (\Illuminate\Database\QueryException $e) {
                $attempt++;
                if (str_contains($e->getMessage(), 'Deadlock') && $attempt < $maxAttempts) {
                    usleep(100000 * $attempt); // 100ms, 200ms, 300ms backoff
                    goto retry;
                }
                $this->command->warn("  block {$row->id} failed after {$attempt} attempts: " . substr($e->getMessage(), 0, 80));
            }
        }

        $this->command->info("Done. Scanned: {$scanned} | New blocks: {$blocksCreated}");
        foreach ($stats as $k => $v) $this->command->info("  {$k}: {$v}");
    }

    private function payloadToHtml(string $type, array $payload): string
    {
        if ($type === 'rich_text') {
            return (string) ($payload['html'] ?? '');
        }
        // text_section: stitch paragraphs back into one html string so
        // extraction operates on a single buffer. We re-split after.
        $parts = (array) ($payload['paragraphs'] ?? []);
        return implode("\n", array_map(fn($p) => (string) $p, $parts));
    }

    private function htmlToParagraphs(string $html): array
    {
        $html = trim($html);
        if ($html === '') return [];
        // Coarse split by paragraph + block-level boundaries the
        // text_section editor produces. Anything that looks like a
        // top-level <p>, <figure>, <ul>, <ol>, <blockquote>, <table>,
        // or <div> becomes its own paragraph entry.
        $parts = preg_split(
            '~(?=<(?:p|figure|ul|ol|blockquote|table|div|aside|section)\b)~i',
            $html,
            -1,
            PREG_SPLIT_NO_EMPTY
        );
        $out = [];
        foreach ($parts as $part) {
            $p = trim($part);
            if ($p !== '') $out[] = $p;
        }
        return $out;
    }

    /**
     * Run every pattern extractor in order. Each one mutates $html
     * (removing matched substrings) and pushes new block entries.
     */
    private function extractAll(string $html): array
    {
        $extracted = [];

        $html = $this->extractEach(
            $html,
            $extracted,
            // External guides FIRST so it's not eaten by other matchers.
            ['external_guides', [$this, 'extractExternalGuides']],
            ['map_embed',       [$this, 'extractMapEmbed']],
            ['data_table',      [$this, 'extractTable']],
            ['local_tip_dark',  [$this, 'extractDarkPullQuote']],
            ['local_tip_amber', [$this, 'extractLightAside']],
            ['tag_pills',       [$this, 'extractTagPills']],
            ['image',           [$this, 'extractFigure']],
        );

        return [$html, $extracted];
    }

    /**
     * Runs each extractor in a loop on the current $html, collecting
     * blocks until no more matches. Mutates both $html and $extracted.
     */
    private function extractEach(string $html, array &$extracted, array ...$extractors): string
    {
        foreach ($extractors as [$statKey, $extractor]) {
            while (true) {
                $result = $extractor($html);
                if ($result === null) break;
                [$newHtml, $blockType, $payload] = $result;
                $extracted[] = [
                    'block_type' => $blockType,
                    'payload'    => $payload,
                    'stat_key'   => $statKey,
                ];
                $html = $newHtml;
            }
        }
        return $html;
    }

    // ── Pattern extractors ─────────────────────────────────────────

    private function extractFigure(string $html): ?array
    {
        if (!preg_match(
            '~<figure\b[^>]*>.*?</figure>~s',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $fig = $m[0][0];
        $payload = ['src' => '', 'alt' => '', 'caption' => '', 'align' => 'center'];
        if (preg_match('~<img\s+src="([^"]+)"\s+alt="([^"]*)"~', $fig, $im)) {
            $payload['src'] = $this->normalizeMediaUrl($im[1]);
            $payload['alt'] = $im[2];
        }
        if (preg_match('~<figcaption[^>]*>(.+?)</figcaption>~s', $fig, $cm)) {
            $payload['caption'] = trim(strip_tags($cm[1]));
        }
        if ($payload['src'] === '') return null;

        $newHtml = substr_replace($html, '', $m[0][1], strlen($fig));
        return [$newHtml, 'image', $payload];
    }

    private function extractDarkPullQuote(string $html): ?array
    {
        if (!preg_match(
            '~<div\s+class="not-prose my-10 px-6 py-6 rounded-xl"\s+style="background:linear-gradient\(135deg,#0f172a[^"]*"[^>]*>.*?</div>~s',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $div = $m[0][0];
        $body = '';
        if (preg_match('~<p\s+class="text-lg md:text-xl italic[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $div, $bm)) {
            $body = trim(strip_tags($bm[1]));
        }
        $eyebrow = 'Local tip';
        if (preg_match('~<p\s+class="text-xs uppercase tracking-wide[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $div, $em)) {
            $eyebrow = trim(strip_tags($em[1]));
        }
        if ($body === '') return null;
        $payload = ['eyebrow' => $eyebrow, 'body' => $body, 'color' => 'dark'];
        $newHtml = substr_replace($html, '', $m[0][1], strlen($div));
        return [$newHtml, 'local_tip', $payload];
    }

    private function extractLightAside(string $html): ?array
    {
        if (!preg_match(
            '~<aside\s+class="not-prose my-10 p-6 rounded-2xl bg-white border border-slate-200"[^>]*>.*?</aside>~s',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $aside = $m[0][0];
        $eyebrow = 'Local tip';
        if (preg_match('~<div\s+class="text-\[11px\] uppercase tracking-\[0.18em\][^"]*"[^>]*>\s*(.+?)\s*</div>~s', $aside, $em)) {
            $eyebrow = trim(strip_tags($em[1]));
        }
        $body = '';
        if (preg_match('~<p\s+class="text-base text-slate-700[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $aside, $bm)) {
            $body = trim(strip_tags($bm[1]));
        }
        if ($body === '') return null;
        $payload = ['eyebrow' => $eyebrow, 'body' => $body, 'color' => 'amber'];
        $newHtml = substr_replace($html, '', $m[0][1], strlen($aside));
        return [$newHtml, 'local_tip', $payload];
    }

    private function extractTagPills(string $html): ?array
    {
        if (!preg_match(
            '~<div\s+class="not-prose my-7 flex flex-wrap gap-2"(?:\s+aria-label="([^"]*)")?[^>]*>(.*?)</div>~s',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $div = $m[0][0];
        $label = $m[1][0] ?? '';
        $inner = $m[2][0] ?? '';
        $items = [];
        $colorMap = [
            '#fef3c7' => 'amber', '#fee2e2' => 'rose', '#dcfce7' => 'emerald',
            '#e0e7ff' => 'indigo', '#fce7f3' => 'pink', '#cffafe' => 'cyan',
            '#ede9fe' => 'violet', '#e2e8f0' => 'slate',
        ];
        if (preg_match_all(
            '~<span\s+class="px-3 py-1.5 rounded-full text-xs font-bold"\s+style="background:([^;]+);[^"]*"[^>]*>\s*(.+?)\s*</span>~s',
            $inner,
            $sm,
            PREG_SET_ORDER
        )) {
            foreach ($sm as $hit) {
                $bg = strtolower(trim($hit[1]));
                $items[] = [
                    'text'  => trim(strip_tags($hit[2])),
                    'color' => $colorMap[$bg] ?? 'amber',
                ];
            }
        }
        if (!$items) return null;
        $payload = ['label' => $label, 'items' => $items];
        $newHtml = substr_replace($html, '', $m[0][1], strlen($div));
        return [$newHtml, 'tag_pills', $payload];
    }

    private function extractTable(string $html): ?array
    {
        if (!preg_match(
            '~<div\s+class="not-prose my-7 overflow-x-auto rounded-xl[^"]*"[^>]*>\s*<table[^>]*>(.*?)</table>\s*</div>~s',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $tableInner = $m[1][0];
        $headers = [];
        if (preg_match('~<thead[^>]*>(.+?)</thead>~s', $tableInner, $tm)) {
            if (preg_match_all('~<th[^>]*>\s*(.+?)\s*</th>~s', $tm[1], $hm)) {
                foreach ($hm[1] as $h) $headers[] = trim(strip_tags($h));
            }
        }
        $rows = [];
        if (preg_match('~<tbody[^>]*>(.+?)</tbody>~s', $tableInner, $bm)) {
            if (preg_match_all('~<tr[^>]*>(.+?)</tr>~s', $bm[1], $rm)) {
                foreach ($rm[1] as $tr) {
                    $cells = [];
                    if (preg_match_all('~<td[^>]*>\s*(.+?)\s*</td>~s', $tr, $cm)) {
                        foreach ($cm[1] as $cell) $cells[] = trim(strip_tags($cell));
                    }
                    if ($cells) $rows[] = $cells;
                }
            }
        }
        if (!$headers || !$rows) return null;
        $payload = ['caption' => '', 'headers' => $headers, 'rows' => $rows];
        $newHtml = substr_replace($html, '', $m[0][1], strlen($m[0][0]));
        return [$newHtml, 'data_table', $payload];
    }

    private function extractMapEmbed(string $html): ?array
    {
        if (!preg_match(
            '~<div\s+class="not-prose my-7 rounded-xl overflow-hidden border border-slate-200"[^>]*>\s*<iframe\s+src="(https://www\.google\.com/maps[^"]+)"\s+width="100%"\s+height="(\d+)"[^>]*>.*?</iframe>.*?</div>~s',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $payload = [
            'heading'   => '',
            'embed_url' => $m[1][0],
            'height'    => (int) $m[2][0],
        ];
        $newHtml = substr_replace($html, '', $m[0][1], strlen($m[0][0]));
        return [$newHtml, 'map_embed', $payload];
    }

    private function extractExternalGuides(string $html): ?array
    {
        $opener = '~<div\s+class="not-prose mt-10 p-5 bg-slate-50 rounded-xl border border-slate-200"[^>]*>~';
        if (!preg_match($opener, $html, $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $start = $m[0][1];
        $afterOpen = $start + strlen($m[0][0]);
        // Walk balanced <div>/</div> to find the close.
        $depth = 1;
        $pos = $afterOpen;
        $len = strlen($html);
        while ($depth > 0 && $pos < $len) {
            $no = stripos($html, '<div', $pos);
            $nc = stripos($html, '</div>', $pos);
            if ($nc === false) return null;
            if ($no !== false && $no < $nc) { $depth++; $pos = $no + 4; }
            else { $depth--; $pos = $nc + 6; }
        }
        $end = $pos;
        $div = substr($html, $start, $end - $start);
        // Build a synthetic offset array shaped like preg_match output.
        $m = [[$div, $start]];
        $heading = 'Compare picks on third-party guides';
        if (preg_match('~<p\s+class="text-sm font-semibold text-slate-700[^"]*"[^>]*>\s*(.+?):?\s*</p>~s', $div, $hm)) {
            $heading = trim(strip_tags($hm[1]));
        }
        $items = [];
        $colorByHover = [
            'hover:bg-emerald-50' => 'emerald',
            'hover:bg-blue-50'    => 'blue',
            'hover:bg-rose-50'    => 'rose',
            'hover:bg-amber-50'   => 'amber',
            'hover:bg-violet-50'  => 'violet',
            'hover:bg-slate-100'  => 'slate',
        ];
        if (preg_match_all(
            '~<a\s+href="([^"]+)"[^>]*class="([^"]*)"[^>]*>\s*(.+?)\s*</a>~s',
            $div,
            $am,
            PREG_SET_ORDER
        )) {
            foreach ($am as $hit) {
                $url = $hit[1];
                $classes = $hit[2];
                $name = trim(strip_tags($hit[3]));
                $color = 'slate';
                foreach ($colorByHover as $cls => $c) {
                    if (str_contains($classes, $cls)) { $color = $c; break; }
                }
                $items[] = [
                    'name' => $name, 'url' => $url, 'color' => $color,
                    'blurb' => '', 'logo' => '', 'screenshot' => '',
                ];
            }
        }
        $footnote = 'External links open in a new tab. We do not get paid for clicks.';
        if (preg_match('~<p\s+class="text-xs text-slate-500[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $div, $fm)) {
            $footnote = trim(strip_tags($fm[1]));
        }
        if (!$items) return null;
        $payload = ['heading' => $heading, 'intro' => '', 'footnote' => $footnote, 'items' => $items];
        $newHtml = substr_replace($html, '', $m[0][1], strlen($div));
        return [$newHtml, 'external_guides', $payload];
    }

    private function normalizeMediaUrl(string $url): string
    {
        return preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $url) ?: $url;
    }
}
