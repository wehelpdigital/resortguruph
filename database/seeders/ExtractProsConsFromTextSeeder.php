<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Walks every text_section block that carries the legacy
 * "Best for / Skip if" prose pattern (with the ▸ bullet character used
 * by the original seeders), splits it into pros[] / cons[] arrays, and
 * inserts a proper pros_cons block in place — keeping the rest of the
 * original text_section content intact.
 *
 * Idempotent: skips blocks where the parser can't find both Best for AND
 * Skip if. Each successful extraction strips the parsed lines from the
 * text_section's body and inserts a fresh pros_cons block immediately
 * after, so the visual flow stays:
 *   ... opening prose ... → pros_cons card pair → ... continuation prose ...
 */
class ExtractProsConsFromTextSeeder extends Seeder
{
    public function run(): void
    {
        $candidates = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('block_type', 'text_section')
            ->where('payload_json', 'like', '%Best for%')
            ->where('payload_json', 'like', '%Skip if%')
            ->get();

        $this->command->info('Candidate text_section blocks: ' . $candidates->count());

        $extracted = 0;
        foreach ($candidates as $block) {
            $payload = json_decode((string) $block->payload_json, true) ?: [];
            $body = (string) ($payload['body'] ?? '');
            if ($body === '') continue;

            $parsed = $this->parseProsCons($body);
            if (!$parsed) continue;

            DB::transaction(function () use ($block, $payload, $parsed) {
                // Shift any block past this one down by +1 so the new
                // pros_cons can slot in immediately after.
                DB::table('rg_content_blocks')
                    ->where('owner_type', $block->owner_type)
                    ->where('owner_id', $block->owner_id)
                    ->where('sort_order', '>', $block->sort_order)
                    ->orderByDesc('sort_order')
                    ->get()
                    ->each(function ($row) {
                        DB::table('rg_content_blocks')
                            ->where('id', $row->id)
                            ->update(['sort_order' => $row->sort_order + 1]);
                    });

                // Update the original text_section with the trimmed body.
                $payload['body'] = $parsed['remaining'];
                DB::table('rg_content_blocks')->where('id', $block->id)->update([
                    'payload_json' => json_encode($payload),
                    'updated_at' => now(),
                ]);

                // Insert the new pros_cons block right after.
                DB::table('rg_content_blocks')->insert([
                    'owner_type' => $block->owner_type,
                    'owner_id' => $block->owner_id,
                    'sort_order' => $block->sort_order + 1,
                    'block_type' => 'pros_cons',
                    'payload_json' => json_encode([
                        'pros_label' => 'Best for',
                        'cons_label' => 'Skip if',
                        'pros' => $parsed['pros'],
                        'cons' => $parsed['cons'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $extracted++;
        }

        $this->command->info("pros_cons extracted: {$extracted}");
    }

    /**
     * Walk the body looking for two blocks of bulleted text — the first
     * tagged "Best for" and the second tagged "Skip if". Returns the
     * pros[] / cons[] arrays plus the body with those sections snipped
     * out. Each item is stripped of the leading bullet glyph (▸, -, *).
     */
    private function parseProsCons(string $body): ?array
    {
        // Match the whole "Best for ... Skip if ..." region. The inner
        // capture groups are the bulleted lines following each label.
        // Up to the next h2/h3-like heading (capitalised line) or end.
        $pattern = '~Best\s+for\s*\n([\s\S]+?)\n\s*Skip\s+if\s*\n([\s\S]+?)(?=\n\s*\n[A-Z][^a-z\n]{0,3}[A-Z]|\Z)~i';
        if (!preg_match($pattern, $body, $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $prosRaw = $m[1][0];
        $consRaw = $m[2][0];
        $matchStart = $m[0][1];
        $matchEnd = $matchStart + strlen($m[0][0]);

        $pros = $this->parseBulletLines($prosRaw);
        $cons = $this->parseBulletLines($consRaw);
        if (!$pros || !$cons) return null;

        // Remove the parsed region from the body, leaving a clean break
        // where it used to be.
        $remaining = substr($body, 0, $matchStart) . substr($body, $matchEnd);
        $remaining = preg_replace('~\n{3,}~', "\n\n", $remaining);
        $remaining = trim((string) $remaining);

        return [
            'pros' => $pros,
            'cons' => $cons,
            'remaining' => $remaining,
        ];
    }

    /**
     * Pull each bulleted line out of the chunk. Accepts the legacy ▸
     * glyph (with or without a leading dash), plain dashes, and stars.
     * Stops at the first non-bullet line.
     */
    private function parseBulletLines(string $chunk): array
    {
        $items = [];
        $lines = preg_split('~\n~', $chunk) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            // Strip leading bullet patterns: "- ▸item", "▸item",
            // "- item", "* item", "• item". PCRE2 doesn't support
            // \u{...} escapes in character classes so we list the
            // literal glyphs (• ‣ ▸) in the class directly.
            $stripped = preg_replace('~^[\-\*•\s]*[▸‣]?\s*~u', '', $line);
            $stripped = trim((string) $stripped);
            if ($stripped === '') continue;
            // Stop the moment we hit a line that looks like a heading
            // (no bullet glyph, starts uppercase + ends without colon).
            if ($stripped === $line && preg_match('~^[A-Z][a-z]+\s+[a-z]+~', $stripped)) {
                // The bullet glyph was missing — treat as out-of-section.
                break;
            }
            $items[] = $stripped;
        }
        return $items;
    }
}
