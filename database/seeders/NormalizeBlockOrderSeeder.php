<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Reflows the block sort_order on every SEO page so the narrative reads
 * top → bottom as a coherent article instead of a flat tail of enrichment
 * blocks dumped above the author byline (which is how the layered
 * seeders left things).
 *
 * Each block_type is assigned to one of 11 rank buckets. Blocks are sorted
 * by (bucket, current sort_order, id) — stable within bucket — and
 * renumbered 1..N. Blocks of the same bucket preserve their original
 * order, so text_section / image / heading sequences in the content body
 * stay interleaved exactly as they were authored.
 *
 * Idempotent: re-running produces the same final sort_orders.
 */
class NormalizeBlockOrderSeeder extends Seeder
{
    /**
     * Bucket per block_type. Lower number = higher on the page. Block
     * types that don't appear here default to the main-content bucket
     * (rank 4) so unknown / future block types still flow with the body.
     */
    private array $bucketByType = [
        // 1: Page header
        'hero_slider' => 1,
        'section_header' => 1,

        // 2: TLDR / verdict / facts strip — right after the header
        'short_version' => 2,
        'editor_rating' => 2,
        'quick_facts' => 2,

        // 3: Paid listings band
        'listing_slot' => 3,
        'listing_block' => 3,

        // 4: Main content body (default bucket) — preserves authoring
        // order via the stable secondary sort, so interleaved
        // text_section / image / heading blocks stay in place.
        'text_section' => 4,
        'rich_text' => 4,
        'heading' => 4,
        'image' => 4,
        'image_text_pair' => 4,
        'gallery' => 4,
        'video' => 4,
        'two_column' => 4,
        'quote' => 4,
        'divider' => 4,
        'custom_html' => 4,
        'data_table' => 4,
        'summary_accordion' => 4,
        'pros_cons' => 4,
        'traveler_reviews' => 4,
        'faq' => 4,
        'cta' => 4,

        // 5: Mid-content callouts — attractions card grid + cuisine pills
        // + the dark pull-quote land here so they break up the main body
        // without competing with the top verdict strip.
        'attractions' => 5,
        'tag_pills' => 5,
        'local_tip' => 5,

        // 6: Location + transit (geographical context, bottom-ish)
        'map_embed' => 6,
        'how_to_get_to' => 6,

        // 7: Outbound comparison links (third-party guides)
        'external_guides' => 7,
        'related_guides' => 7,

        // 8: Cross-vertical recommendations (other destinations + reads)
        'nearby_destinations' => 8,
        'related_blogs' => 8,

        // 9: Byline at the very bottom
        'author' => 9,
    ];

    public function run(): void
    {
        $pageIds = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->distinct()
            ->pluck('owner_id');

        $this->command->info('SEO pages to renumber: ' . $pageIds->count());

        $renumbered = 0;
        foreach ($pageIds as $pageId) {
            if ($this->renumberPage((int) $pageId)) $renumbered++;
        }

        $this->command->info("Pages renumbered: {$renumbered}");
    }

    /**
     * Returns true when any sort_order actually changed (so the run
     * counter only counts pages we modified, not no-op iterations).
     */
    private function renumberPage(int $pageId): bool
    {
        $blocks = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'block_type', 'sort_order']);

        if ($blocks->isEmpty()) return false;

        // Build the new ordering by (bucket, original sort_order, id).
        $withBucket = $blocks->map(function ($b) {
            $bucket = $this->bucketByType[$b->block_type] ?? 4;
            return [
                'id' => $b->id,
                'old' => (int) $b->sort_order,
                'bucket' => $bucket,
                'type' => $b->block_type,
            ];
        })->sort(function ($a, $b) {
            if ($a['bucket'] !== $b['bucket']) return $a['bucket'] <=> $b['bucket'];
            if ($a['old'] !== $b['old']) return $a['old'] <=> $b['old'];
            return $a['id'] <=> $b['id'];
        })->values();

        $changed = false;
        // Two-phase update to avoid sort_order collisions if the table
        // has a unique index on (owner, sort_order). The column is
        // smallint UNSIGNED so we shift into the high range (10000+)
        // for phase 1, then write the final small positive values.
        DB::transaction(function () use ($withBucket, &$changed) {
            $newOrder = [];
            foreach ($withBucket as $i => $row) {
                $newSort = $i + 1;
                if ($row['old'] !== $newSort) {
                    $newOrder[$row['id']] = $newSort;
                    DB::table('rg_content_blocks')
                        ->where('id', $row['id'])
                        ->update(['sort_order' => 10000 + $newSort]);
                }
            }
            foreach ($newOrder as $id => $sort) {
                DB::table('rg_content_blocks')
                    ->where('id', $id)
                    ->update(['sort_order' => $sort]);
                $changed = true;
            }
        });

        return $changed;
    }
}
