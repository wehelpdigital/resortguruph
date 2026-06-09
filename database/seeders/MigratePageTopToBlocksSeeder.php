<?php

namespace Database\Seeders;

use App\Models\RgContentBlock;
use App\Models\RgSeoPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * One-time follow-up to MigratePageMetaToBlocksSeeder: every
 * published rg_seo_pages row gets a `social_share` block + a
 * `we_recommend_band` block inserted near the top, replacing the
 * hardcoded @include('partials.social-share') and the @if/@else
 * @include('partials.listings-rows') that used to render in
 * keyword-page.blade.php.
 *
 * Placement strategy mirrors the legacy visual order:
 *   1. subtitle_intro (already migrated, if exists)
 *   2. social_share        ← INSERTED HERE by this seeder
 *   3. we_recommend_band   ← INSERTED HERE by this seeder
 *   4. tldr_card           (already migrated, if exists)
 *   5. wwww_card           (already migrated, if exists)
 *   N. ...existing content blocks (bumped to follow)
 *
 * Strategy: find the lowest sort_order on the page; if there's a
 * subtitle_intro at slot 1, insert after it (bump everything from
 * slot 2 up by 2). Otherwise insert at the top (bump everything
 * up by 2). Skips any page that already has either of the new
 * block types so re-running is idempotent.
 */
class MigratePageTopToBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $pageIds = RgSeoPage::query()
            ->where('is_published', true)
            ->pluck('id');

        $created = ['social_share' => 0, 'we_recommend_band' => 0];
        $skipped = ['social_share' => 0, 'we_recommend_band' => 0];

        foreach ($pageIds as $pageId) {
            $existingTypes = RgContentBlock::query()
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->whereIn('block_type', ['social_share', 'we_recommend_band'])
                ->pluck('block_type')
                ->all();

            $toInsert = [];
            if (!in_array('social_share', $existingTypes, true)) {
                $toInsert[] = 'social_share';
            } else {
                $skipped['social_share']++;
            }
            if (!in_array('we_recommend_band', $existingTypes, true)) {
                $toInsert[] = 'we_recommend_band';
            } else {
                $skipped['we_recommend_band']++;
            }
            if (!$toInsert) continue;

            // Find the sort_order of the existing subtitle_intro (if
            // any) so we can insert AFTER it. Otherwise insert at the
            // top.
            $subtitleSort = RgContentBlock::query()
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'subtitle_intro')
                ->value('sort_order');

            $insertAfter = $subtitleSort ?: 0;
            $shift = count($toInsert);

            // Bump every block at or below `insertAfter + 1` up by
            // $shift so positions [insertAfter+1 .. insertAfter+shift]
            // open up for the new blocks.
            DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('sort_order', '>', $insertAfter)
                ->update(['sort_order' => DB::raw('sort_order + ' . $shift)]);

            $seat = $insertAfter + 1;
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

        $this->command->info('social_share     : ' . $created['social_share'] . ' created, ' . $skipped['social_share'] . ' skipped (already had one).');
        $this->command->info('we_recommend_band: ' . $created['we_recommend_band'] . ' created, ' . $skipped['we_recommend_band'] . ' skipped.');
    }

    private function defaultPayloadFor(string $type): array
    {
        return match ($type) {
            'social_share' => ['align' => 'between'],
            'we_recommend_band' => [],
            default => [],
        };
    }
}
