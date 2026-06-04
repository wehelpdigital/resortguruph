<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Converts every legacy `rich_text` content block into the new
 * `text_section` shape (title + body textarea, blank-line-separated
 * paragraphs). One rich_text block becomes ONE text_section block
 * unless the original HTML contains an embedded H2/H3/H4, in which case
 * the block is split — each heading + following paragraphs becomes its
 * own text_section, expanded inline in place of the original row.
 *
 * Also converts any existing text_section block authored under the old
 * paragraphs[] shape into the new body shape, so the editor only ever
 * has to deal with one representation.
 *
 * Idempotent: rich_text rows already gone are simply absent from the
 * working set; text_section rows that already have a body field are
 * left alone.
 */
class MigrateRichTextToBodyTextSectionSeeder extends Seeder
{
    public function run(): void
    {
        $rich = DB::table('rg_content_blocks')->where('block_type', 'rich_text')->get();
        $this->command->info('rich_text blocks: ' . $rich->count());

        $convertedRich = 0;
        foreach ($rich as $block) {
            $payload = json_decode((string) $block->payload_json, true) ?: [];
            $html = (string) ($payload['html'] ?? '');

            $sections = $this->splitHtmlIntoSections($html);
            if (!$sections) continue;

            DB::transaction(function () use ($block, $sections) {
                // Shift everything after this block by (count-1) so we
                // can insert the extra sections in place.
                $extra = count($sections) - 1;
                if ($extra > 0) {
                    DB::table('rg_content_blocks')
                        ->where('owner_type', $block->owner_type)
                        ->where('owner_id', $block->owner_id)
                        ->where('sort_order', '>', $block->sort_order)
                        ->orderByDesc('sort_order')
                        ->get()
                        ->each(function ($row) use ($extra) {
                            DB::table('rg_content_blocks')
                                ->where('id', $row->id)
                                ->update(['sort_order' => $row->sort_order + $extra]);
                        });
                }

                // Replace the original block with the first section, then
                // insert the rest inline at sequential sort_orders.
                $first = array_shift($sections);
                DB::table('rg_content_blocks')->where('id', $block->id)->update([
                    'block_type' => 'text_section',
                    'payload_json' => json_encode([
                        'heading' => $first['heading'],
                        'heading_level' => $first['heading_level'],
                        'anchor' => '',
                        'body' => $first['body'],
                    ]),
                    'updated_at' => now(),
                ]);

                $cursor = $block->sort_order + 1;
                foreach ($sections as $section) {
                    DB::table('rg_content_blocks')->insert([
                        'owner_type' => $block->owner_type,
                        'owner_id' => $block->owner_id,
                        'sort_order' => $cursor,
                        'block_type' => 'text_section',
                        'payload_json' => json_encode([
                            'heading' => $section['heading'],
                            'heading_level' => $section['heading_level'],
                            'anchor' => '',
                            'body' => $section['body'],
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $cursor++;
                }
            });

            $convertedRich++;
        }

        $this->command->info("rich_text → text_section: {$convertedRich} blocks");

        // Convert legacy text_section paragraphs[] → body so the editor
        // only ever has one shape to manage.
        $legacy = DB::table('rg_content_blocks')->where('block_type', 'text_section')->get();
        $convertedLegacy = 0;
        foreach ($legacy as $block) {
            $payload = json_decode((string) $block->payload_json, true) ?: [];
            $bodyAlready = trim((string) ($payload['body'] ?? ''));
            $paragraphs = $payload['paragraphs'] ?? [];

            if ($bodyAlready !== '' || empty($paragraphs)) continue;

            $cleaned = array_values(array_filter(array_map(
                fn ($p) => trim((string) $p),
                (array) $paragraphs
            )));
            if (!$cleaned) continue;

            $payload['body'] = implode("\n\n", $cleaned);
            unset($payload['paragraphs']);
            DB::table('rg_content_blocks')->where('id', $block->id)->update([
                'payload_json' => json_encode($payload),
                'updated_at' => now(),
            ]);
            $convertedLegacy++;
        }

        $this->command->info("text_section paragraphs[] → body: {$convertedLegacy} blocks");
    }

    /**
     * Walk the HTML linearly. Each H2/H3/H4 begins a new section and
     * resets the body accumulator. Paragraph-level markup (<p>) is
     * stripped to plain text + newlines but inline tags (<strong>, <em>,
     * <a href>, <br>) are preserved so the rendered output keeps emphasis.
     */
    private function splitHtmlIntoSections(string $html): array
    {
        $html = trim($html);
        if ($html === '') return [];

        // Normalise — collapse repeated whitespace and decode entities so
        // the offset arithmetic below works on clean source.
        $normalized = preg_replace('~\s+~', ' ', $html);
        $normalized = trim((string) $normalized);

        // Snip on each opening heading tag so the body that follows is
        // attributed to the most recent heading.
        $parts = preg_split('~(?=<h[234]\b)~i', $normalized) ?: [];
        $sections = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;

            $headingText = '';
            $headingLevel = 'h2';
            if (preg_match('~^<(h[234])\b[^>]*>(.*?)</\1>(.*)$~is', $part, $m)) {
                $headingLevel = strtolower($m[1]);
                $headingText = $this->stripInlineToPlain($m[2]);
                $body = trim($m[3]);
            } else {
                $body = $part;
            }

            $bodyText = $this->htmlBodyToBlankLineParagraphs($body);
            if ($headingText === '' && $bodyText === '') continue;

            $sections[] = [
                'heading' => $headingText,
                'heading_level' => $headingLevel,
                'body' => $bodyText,
            ];
        }

        // No headings at all? Single section with the whole body.
        if (!$sections) {
            $sections[] = [
                'heading' => '',
                'heading_level' => 'h2',
                'body' => $this->htmlBodyToBlankLineParagraphs($html),
            ];
        }

        return $sections;
    }

    /**
     * Convert paragraph-bearing HTML to plain text with blank lines
     * between paragraphs. Inline emphasis tags are preserved verbatim so
     * the rendered text_section keeps bold/italic/links.
     */
    private function htmlBodyToBlankLineParagraphs(string $html): string
    {
        if (trim($html) === '') return '';

        // Replace <br> with newlines first so they survive the <p> strip.
        $html = preg_replace('~<br\s*/?>~i', "\n", $html);

        // Replace each block-level closer with a paragraph break marker.
        $html = preg_replace('~</(?:p|div|section|article)\s*>~i', "\n\n", $html);
        // Strip the opening tags (and any attributes) — content stays.
        $html = preg_replace('~<(?:p|div|section|article)\b[^>]*>~i', '', $html);

        // Lists: turn each <li> into a bulleted line so the body still
        // reads as a list when the renderer wraps each chunk in <p>.
        // Renderer accepts inline lists so wrap with - prefix.
        $html = preg_replace('~<li\b[^>]*>~i', '- ', $html);
        $html = preg_replace('~</li\s*>~i', "\n", $html);
        $html = preg_replace('~</?(?:ul|ol)\s*>~i', "\n\n", $html);

        // Drop any remaining block-level openers/closers we don't handle.
        $html = preg_replace('~<(?!/?(?:strong|em|b|i|a|u|code|mark|s|sub|sup)\b)[^>]+>~i', '', $html);

        // Decode entities to real characters.
        $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalise paragraph spacing: trim each line, collapse 3+ blank
        // lines down to exactly 2.
        $lines = preg_split('~\n~', $text) ?: [];
        $lines = array_map(fn ($l) => trim((string) $l), $lines);
        $text = implode("\n", $lines);
        $text = preg_replace('~\n{3,}~', "\n\n", $text);
        return trim((string) $text);
    }

    /**
     * Strip inline markup and decode entities — used for the heading
     * text where the renderer expects plain string content.
     */
    private function stripInlineToPlain(string $html): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim((string) preg_replace('~\s+~', ' ', $text));
    }
}
