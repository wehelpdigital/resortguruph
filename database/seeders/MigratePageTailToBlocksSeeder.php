<?php

namespace Database\Seeders;

use App\Models\RgContentBlock;
use App\Models\RgSeoPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Final migration in the page-to-blocks port: every published
 * rg_seo_pages row gets restaurant_recs_band + adventures_band +
 * reviews_band blocks at the END of its block stream, replacing
 * the three hardcoded <section> blocks that used to sit between
 * the main content and the related-keywords footer.
 *
 * Visual order after this migration:
 *    1..N  (existing top + body blocks)
 *    N+1   restaurant_recs_band   ← only renders on non-food keyword
 *                                   pages with active restaurant
 *                                   listings; empty string otherwise.
 *    N+2   adventures_band        ← only renders on non-food keyword
 *                                   pages with active adventure
 *                                   listings.
 *    N+3   reviews_band           ← only renders when at least one
 *                                   published review is scoped to
 *                                   this keyword.
 *
 * Empty rendering is fine — the block is harmless on pages where
 * its data is empty, and the admin can drag/delete them in the
 * builder if they want to remove them entirely.
 *
 * Idempotent: skips any page that already has a block of the
 * target type.
 */
class MigratePageTailToBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $pageIds = RgSeoPage::query()
            ->where('is_published', true)
            ->pluck('id');

        $created = ['restaurant_recs_band' => 0, 'adventures_band' => 0, 'reviews_band' => 0];
        $skipped = ['restaurant_recs_band' => 0, 'adventures_band' => 0, 'reviews_band' => 0];

        foreach ($pageIds as $pageId) {
            $existingTypes = RgContentBlock::query()
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->whereIn('block_type', array_keys($created))
                ->pluck('block_type')
                ->all();

            $toInsert = [];
            foreach (array_keys($created) as $type) {
                if (in_array($type, $existingTypes, true)) {
                    $skipped[$type]++;
                } else {
                    $toInsert[] = $type;
                }
            }
            if (!$toInsert) continue;

            // Find the current maximum sort_order on this page so we
            // can append the new tail blocks AFTER everything else.
            $maxSort = (int) (DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->max('sort_order') ?? 0);

            $seat = $maxSort + 1;
            foreach ($toInsert as $type) {
                RgContentBlock::create([
                    'owner_type' => 'seo_page',
                    'owner_id' => $pageId,
                    'block_type' => $type,
                    'payload_json' => json_encode(
                        $this->defaultPayloadFor($type),
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    ),
                    'sort_order' => $seat++,
                ]);
                $created[$type]++;
            }
        }

        $this->command->info('restaurant_recs_band: ' . $created['restaurant_recs_band'] . ' created, ' . $skipped['restaurant_recs_band'] . ' skipped.');
        $this->command->info('adventures_band     : ' . $created['adventures_band'] . ' created, ' . $skipped['adventures_band'] . ' skipped.');
        $this->command->info('reviews_band        : ' . $created['reviews_band'] . ' created, ' . $skipped['reviews_band'] . ' skipped.');
    }

    private function defaultPayloadFor(string $type): array
    {
        return match ($type) {
            'restaurant_recs_band' => [
                'eyebrow' => 'Eat nearby',
                'heading' => 'Restaurant Recommendations',
                'caption' => 'Paid placements where your guests will likely want to eat.',
            ],
            'adventures_band' => [
                'eyebrow' => 'Things to do',
                'heading' => 'Memorable Adventures & Activities',
                'caption' => 'Surf schools, ATV trails, island hops, and paintball arenas open in the area.',
            ],
            'reviews_band' => [
                'heading' => 'What travelers are saying',
            ],
            default => [],
        };
    }
}
