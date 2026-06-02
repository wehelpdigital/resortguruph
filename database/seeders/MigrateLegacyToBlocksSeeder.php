<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Converts the legacy rg_seo_pages.intro_html/body_html/faq_json/fallback_listing_html
 * columns into proper rg_content_blocks so the drag-drop admin builder shows real
 * blocks instead of an empty canvas. Idempotent — skips pages that already have blocks.
 *
 * Each migrated page receives:
 *   1. Intro rich_text
 *   2. Hero image (Picsum, seeded by slug for deterministic URL)
 *   3. body_html split by <h2> into heading + rich_text blocks
 *   4. Mid-body image (after 2nd h2)
 *   5. Listing slot block (with fallback_listing_html)
 *   6. FAQ block (from faq_json)
 *   7. Closing CTA
 *
 * Also fills og_image_path with a Picsum URL and ensures meta_title/h1/meta_description.
 */
class MigrateLegacyToBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $pages = DB::table('rg_seo_pages')->get();
        $migrated = 0;
        $skipped = 0;
        $metaUpdated = 0;
        $now = now();

        foreach ($pages as $page) {
            $hasBlocks = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->count();

            if ($hasBlocks > 0) {
                $skipped++;
                continue;
            }

            $keyword = DB::table('rg_keywords')->where('id', $page->keyword_id)->first();
            if (!$keyword) continue;

            $this->createBlocks($page, $keyword, $now);
            if ($this->ensureSeoMeta($page, $keyword, $now)) $metaUpdated++;
            $migrated++;
        }

        $this->command->info(sprintf(
            'SEO page blocks migrated: %d  |  skipped (already had blocks): %d  |  meta filled: %d',
            $migrated, $skipped, $metaUpdated
        ));
    }

    private function createBlocks($page, $keyword, $now): void
    {
        $rows = [];
        $sort = 1;
        $slug = $page->slug ?: $keyword->slug;
        $phrase = ucwords($keyword->phrase);

        // 1. Intro
        if (trim($page->intro_html ?? '') !== '') {
            $rows[] = $this->row($page->id, $sort++, 'rich_text', ['html' => $page->intro_html], $now);
        }

        // 2. Hero image
        $rows[] = $this->row($page->id, $sort++, 'image', [
            'src' => "https://picsum.photos/seed/{$slug}-hero/1600/900",
            'alt' => $phrase . ' in the Philippines',
            'caption' => '',
            'align' => 'center',
        ], $now);

        // 3. Body split by <h2>
        if (trim($page->body_html ?? '') !== '') {
            $parts = preg_split('/<h2[^>]*>(.*?)<\/h2>/i', $page->body_html, -1, PREG_SPLIT_DELIM_CAPTURE);
            $h2Count = 0;

            // Pre-h2 content
            if (isset($parts[0]) && trim(strip_tags($parts[0])) !== '') {
                $rows[] = $this->row($page->id, $sort++, 'rich_text', ['html' => trim($parts[0])], $now);
            }

            // Iterate (h2 text, content) pairs
            for ($i = 1; $i < count($parts); $i += 2) {
                $h2Text = strip_tags($parts[$i] ?? '');
                if ($h2Text === '') continue;

                $rows[] = $this->row($page->id, $sort++, 'heading', [
                    'level' => 'h2',
                    'text' => $h2Text,
                ], $now);

                // Inject mid-body image after the 2nd h2
                $h2Count++;
                if ($h2Count === 2) {
                    $rows[] = $this->row($page->id, $sort++, 'image', [
                        'src' => "https://picsum.photos/seed/{$slug}-body/1200/700",
                        'alt' => $phrase . ' destination',
                        'caption' => '',
                        'align' => 'center',
                    ], $now);
                }

                $bodyChunk = trim($parts[$i + 1] ?? '');
                if ($bodyChunk !== '') {
                    $rows[] = $this->row($page->id, $sort++, 'rich_text', ['html' => $bodyChunk], $now);
                }
            }
        }

        // 4. Listing slot
        $fallback = trim($page->fallback_listing_html ?? '') !== ''
            ? $page->fallback_listing_html
            : '<p>We are still finalizing partner resorts for ' . $phrase . '. If you operate one, <a href="/register">sign up</a> to list your property here.</p>';
        $rows[] = $this->row($page->id, $sort++, 'listing_slot', [
            'slot_label' => 'Featured ' . $phrase,
            'fallback_html' => $fallback,
        ], $now);

        // 5. FAQ
        if (!empty($page->faq_json)) {
            $faqs = json_decode($page->faq_json, true);
            if (is_array($faqs) && count($faqs) > 0) {
                // Filter to entries with content
                $filtered = array_values(array_filter($faqs, fn($f) => !empty($f['question'])));
                if (count($filtered) > 0) {
                    $rows[] = $this->row($page->id, $sort++, 'faq', ['items' => $filtered], $now);
                }
            }
        }

        // 6. Closing CTA
        $rows[] = $this->row($page->id, $sort++, 'cta', [
            'headline' => 'Run a property in ' . $phrase . '?',
            'text' => 'Get featured on this page. Resort owners pay only what they want to bid — no monthly fees.',
            'button_text' => 'List your property',
            'button_url' => '/register',
            'style' => 'primary',
        ], $now);

        if (count($rows) > 0) {
            DB::table('rg_content_blocks')->insert($rows);
        }
    }

    private function ensureSeoMeta($page, $keyword, $now): bool
    {
        $updates = [];
        $phrase = ucwords($keyword->phrase);
        $slug = $page->slug ?: $keyword->slug;

        if (empty($page->meta_title)) {
            $updates['meta_title'] = $phrase . ' — Top Picks for ' . date('Y') . ' | Resort Guru PH';
        }
        if (empty($page->meta_description)) {
            $updates['meta_description'] = 'Compare top picks for ' . strtolower($keyword->phrase) . ' in the Philippines. Real prices, real photos, and curated picks on Resort Guru PH.';
        }
        if (empty($page->h1)) {
            $updates['h1'] = $phrase;
        }
        if (empty($page->og_image_path)) {
            $updates['og_image_path'] = "https://picsum.photos/seed/{$slug}-og/1200/630";
        }
        if (empty($page->meta_keywords)) {
            $updates['meta_keywords'] = strtolower($keyword->phrase) . ', philippines, resort, hotel, ' . ($keyword->cluster_tag ?? '');
        }

        if (count($updates) > 0) {
            $updates['updated_at'] = $now;
            DB::table('rg_seo_pages')->where('id', $page->id)->update($updates);
            return true;
        }
        return false;
    }

    private function row(int $ownerId, int $sort, string $type, array $payload, $now): array
    {
        return [
            'owner_type' => 'seo_page',
            'owner_id' => $ownerId,
            'sort_order' => $sort,
            'block_type' => $type,
            'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
