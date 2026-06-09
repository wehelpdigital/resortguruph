<?php

namespace Database\Seeders;

use App\Models\RgContentBlock;
use App\Models\RgSeoPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Cleanup after the page-to-blocks migration series ran multiple
 * times during iteration. Three classes of duplicates to fix:
 *
 *   1. Same-type duplicates of the new singleton block types
 *      (subtitle_intro, tldr_card, wwww_card, social_share,
 *       we_recommend_band, restaurant_recs_band, adventures_band,
 *       reviews_band). These appear twice on some pages because
 *      migration seeders ran multiple times. Keep the row with the
 *      longest payload (fall back to lowest id), delete the rest.
 *
 *   2. tldr_card + short_version overlap. The seeder originally
 *      created short_version blocks; the MigratePageMetaToBlocksSeeder
 *      added tldr_card. Both render summary cards near the top, so
 *      visually they read as two stacked "short version" panels.
 *      Per user direction: delete short_version on pages that have
 *      tldr_card. The migrated tldr_card content is the canonical
 *      one going forward.
 *
 *   3. Renormalize sort_order after every page's deletions so the
 *      ladder is 1..N with no gaps. Keeps the builder's "move up /
 *      move down" arithmetic consistent.
 *
 * Idempotent. Safe to re-run. Reports counts at the end.
 */
class DedupPageBlocksSeeder extends Seeder
{
    /** Singleton block types — each one should appear at most once per page. */
    private const SINGLETON_TYPES = [
        'subtitle_intro',
        'tldr_card',
        'wwww_card',
        'social_share',
        'we_recommend_band',
        'restaurant_recs_band',
        'adventures_band',
        'reviews_band',
    ];

    public function run(): void
    {
        $deleted = ['same_type' => 0, 'short_version_overlap' => 0];
        $pagesNormalized = 0;

        // ----- 1. Same-type duplicates -----
        foreach (self::SINGLETON_TYPES as $type) {
            $pageDupes = DB::table('rg_content_blocks')
                ->select('owner_id')
                ->where('owner_type', 'seo_page')
                ->where('block_type', $type)
                ->groupBy('owner_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('owner_id');

            foreach ($pageDupes as $pageId) {
                $rows = DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $pageId)
                    ->where('block_type', $type)
                    ->get(['id', 'payload_json']);

                // Score each row by total payload length; keep the
                // longest. Ties broken by lowest id (the original
                // insert).
                $scored = $rows->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'len' => strlen((string) $r->payload_json),
                    ];
                })->sortByDesc('len')->values();

                $keepId = $scored->first()['id'];
                $deleteIds = $scored->skip(1)->pluck('id')->all();

                if ($deleteIds) {
                    DB::table('rg_content_blocks')->whereIn('id', $deleteIds)->delete();
                    $deleted['same_type'] += count($deleteIds);
                }
            }
        }

        // ----- 2. tldr_card + short_version overlap -----
        // Find pages that have BOTH and delete the short_version row.
        $overlapPages = DB::table('rg_content_blocks as a')
            ->join('rg_content_blocks as b', function ($j) {
                $j->on('a.owner_id', '=', 'b.owner_id')
                    ->where('a.owner_type', '=', 'seo_page')
                    ->where('b.owner_type', '=', 'seo_page');
            })
            ->where('a.block_type', 'tldr_card')
            ->where('b.block_type', 'short_version')
            ->distinct()
            ->pluck('a.owner_id');

        foreach ($overlapPages as $pageId) {
            $count = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'short_version')
                ->delete();
            $deleted['short_version_overlap'] += $count;
        }

        // ----- 3. Renormalize sort_order on every touched page -----
        $touchedPages = collect();
        foreach (self::SINGLETON_TYPES as $type) {
            $touchedPages = $touchedPages->merge(
                DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('block_type', $type)
                    ->pluck('owner_id')
            );
        }
        $touchedPages = $touchedPages->merge($overlapPages)->unique();

        foreach ($touchedPages as $pageId) {
            $blocks = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id');
            $i = 1;
            foreach ($blocks as $id) {
                DB::table('rg_content_blocks')->where('id', $id)->update(['sort_order' => $i++]);
            }
            $pagesNormalized++;
        }

        $this->command->info('Same-type duplicates deleted: ' . $deleted['same_type']);
        $this->command->info('short_version rows deleted (overlap with tldr_card): ' . $deleted['short_version_overlap']);
        $this->command->info('Pages renormalized: ' . $pagesNormalized);
    }
}
