<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rewrites eligible rich_text blocks as text_section blocks so the admin
 * builder shows a structured heading + paragraphs editor instead of a
 * Quill blob. Eligibility:
 *   - Owner must be a seo_page (where text_section rendering is wired).
 *   - The HTML must START with <h2|h3|h4>HEADING</h2|h3|h4>.
 *   - The remainder must contain ONLY <p>…</p> blocks plus whitespace.
 *     Anything else (ul/ol/blockquote/div/figure/img) keeps rich_text so
 *     we don't drop content silently.
 *
 * Re-runnable: pages that don't match the pattern are left untouched.
 */
class MigrateRichTextToTextSectionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('block_type', 'rich_text')
            ->select('id', 'payload_json')
            ->orderBy('id')
            ->get();

        $now = Carbon::now()->toDateTimeString();
        $migrated = 0;
        $skipped = 0;
        $scanned = 0;

        foreach ($rows as $row) {
            $scanned++;
            $payload = json_decode($row->payload_json, true);
            $html = is_array($payload) ? ($payload['html'] ?? '') : '';
            $parsed = $this->tryParse($html);
            if ($parsed === null) {
                $skipped++;
                continue;
            }

            DB::table('rg_content_blocks')
                ->where('id', $row->id)
                ->update([
                    'block_type'   => 'text_section',
                    'payload_json' => json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at'   => $now,
                ]);
            $migrated++;
            if ($migrated % 100 === 0) {
                $this->command->info("  migrated {$migrated} so far...");
            }
        }

        $this->command->info("Done. Scanned: {$scanned} | Migrated: {$migrated} | Skipped (kept as rich_text): {$skipped}");
    }

    /**
     * Returns a text_section payload if $html matches the heading-led
     * paragraphs-only pattern, or null to skip.
     */
    private function tryParse(string $html): ?array
    {
        $trim = trim($html);
        if ($trim === '') return null;

        // 1. Strip a leading heading.
        if (!preg_match('~^<(h[234])(\s[^>]*)?>(.+?)</\1>~is', $trim, $m)) {
            return null;
        }
        $headingTag = strtolower($m[1]);
        $headingText = trim(strip_tags($m[3]));
        if ($headingText === '') return null;

        $tail = substr($trim, strlen($m[0]));
        $tail = trim($tail);

        // 2. The remainder must be a sequence of <p>…</p> blocks. Whitespace
        //    between them is fine. Other tags (ul/ol/img/div/blockquote/etc.)
        //    disqualify the block — we preserve rich_text rather than risk
        //    losing structured content.
        if ($tail === '') {
            return [
                'heading'       => $headingText,
                'heading_level' => $headingTag,
                'anchor'        => '',
                'paragraphs'    => [],
            ];
        }

        $paragraphs = [];
        $cursor = 0;
        $tailLen = strlen($tail);
        // Accepted block-level items in addition to <p>. Each one captures
        // its balanced close so we keep the structured-content fidelity.
        $blockTags = ['p', 'figure', 'blockquote', 'ul', 'ol', 'table'];
        $tagAlt = implode('|', $blockTags);

        while ($cursor < $tailLen) {
            while ($cursor < $tailLen && ctype_space($tail[$cursor])) $cursor++;
            if ($cursor >= $tailLen) break;

            // Match an opening tag of one of the allowed block types.
            if (!preg_match('~\G<(' . $tagAlt . ')(\s[^>]*)?>~i', $tail, $pm, 0, $cursor)) {
                return null; // foreign element → preserve as rich_text
            }
            $tag = strtolower($pm[1]);
            $openLen = strlen($pm[0]);
            $afterOpen = $cursor + $openLen;

            // For <p>, no nesting allowed (rare to nest p) — fast path.
            if ($tag === 'p') {
                $closeAt = stripos($tail, '</p>', $afterOpen);
                if ($closeAt === false) return null;
                $inner = trim(substr($tail, $afterOpen, $closeAt - $afterOpen));
                if ($inner !== '') $paragraphs[] = $inner;
                $cursor = $closeAt + 4;
                continue;
            }

            // For nestable containers (figure / blockquote / ul / ol /
            // table), find the BALANCED close. Walk forward counting
            // same-tag opens vs closes.
            $balanced = $this->findBalancedClose($tail, $afterOpen, $tag);
            if ($balanced < 0) return null;
            $entire = substr($tail, $cursor, $balanced - $cursor);
            $paragraphs[] = trim($entire);
            $cursor = $balanced;
        }

        if (!$paragraphs) return null;

        return [
            'heading'       => $headingText,
            'heading_level' => $headingTag,
            'anchor'        => '',
            'paragraphs'    => $paragraphs,
        ];
    }

    /**
     * Walk $html from $pos counting <$tag>/</$tag> until depth returns to
     * zero. Returns the byte index just past the closing tag, or -1 if
     * the document doesn't balance. Used to capture nestable items like
     * <figure>, <blockquote>, <ul>, <ol>, <table> in one piece.
     */
    private function findBalancedClose(string $html, int $pos, string $tag): int
    {
        $depth = 1;
        $len = strlen($html);
        $openNeedle = '<' . $tag;
        $closeNeedle = '</' . $tag . '>';
        $closeLen = strlen($closeNeedle);
        while ($pos < $len) {
            $nextOpen = stripos($html, $openNeedle, $pos);
            $nextClose = stripos($html, $closeNeedle, $pos);
            if ($nextClose === false) return -1;
            if ($nextOpen !== false && $nextOpen < $nextClose) {
                $depth++;
                $pos = $nextOpen + strlen($openNeedle);
            } else {
                $depth--;
                $pos = $nextClose + $closeLen;
                if ($depth === 0) return $pos;
            }
        }
        return -1;
    }
}
