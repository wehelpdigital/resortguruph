<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * For every food / restaurant SEO page, drops two new blocks right above
 * the author byline at the bottom of the article:
 *
 *   1. `related_blogs`     — auto-resolved via tag keywords for the page's
 *                            cluster_tag (e.g. metro-manila → Manila,
 *                            Makati, BGC, ...).
 *   2. `nearby_destinations` — auto-resolved via cluster_tag against
 *                              resort-category keyword pages.
 *
 * Both blocks store only the auto-resolve hints; the renderer pulls the
 * actual items at request time, so re-tagging blogs or adding new
 * resort-category keywords reflects on next page load without re-running
 * the seeder.
 *
 * Idempotent: skips pages that already have a block of either type.
 */
class AddNearbyAndRelatedBlogsToFoodPagesSeeder extends Seeder
{
    /**
     * Tag/keyword candidates per cluster — used by the related_blogs auto
     * resolver to match against rg_blog_posts.tags. Each list contains
     * the major city / locality / regional terms so a blog tagged with
     * any of them gets matched into the food page's cluster.
     */
    private array $clusterKeywords = [
        'metro-manila' => ['Manila', 'Metro Manila', 'Makati', 'BGC', 'Pasay', 'Quezon City', 'Mandaluyong', 'Pasig', 'Taguig', 'Ortigas', 'Pasay', 'Marikina'],
        'cavite' => ['Cavite', 'Tagaytay', 'Bacoor', 'Imus', 'Dasmarinas', 'Silang', 'Alfonso'],
        'batangas' => ['Batangas', 'Lipa', 'Calatagan', 'Nasugbu', 'Anilao', 'Mabini', 'Lemery', 'San Juan'],
        'laguna' => ['Laguna', 'Calamba', 'Los Banos', 'Nuvali', 'Pansol', 'Pagsanjan'],
        'rizal' => ['Rizal', 'Antipolo', 'Tanay', 'Taytay'],
        'bulacan' => ['Bulacan', 'Malolos', 'Baliuag', 'San Jose del Monte', 'Sta. Maria'],
        'pampanga' => ['Pampanga', 'Angeles', 'Clark', 'San Fernando', 'Subic'],
        'north-luzon' => ['Ilocos', 'Vigan', 'Baguio', 'La Union', 'Pangasinan', 'Tarlac', 'Cordillera', 'Sagada', 'Banaue', 'Laoag'],
        'bicol' => ['Bicol', 'Naga', 'Albay', 'Legazpi', 'Sorsogon', 'Camarines'],
        'quezon' => ['Quezon Province', 'Lucban', 'Lucena'],
        'visayas' => ['Cebu', 'Bohol', 'Iloilo', 'Visayas', 'Bacolod', 'Tacloban', 'Panay', 'Negros', 'Panglao', 'Boracay', 'Siquijor'],
        'palawan' => ['Palawan', 'El Nido', 'Coron', 'Puerto Princesa', 'Siargao'],
        'mindanao' => ['Mindanao', 'Davao', 'Cagayan de Oro', 'Zamboanga', 'Samal'],
        'other' => ['Philippines', 'DIY', 'itinerary'],
    ];

    public function run(): void
    {
        $foodPages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->where('k.category', 'food')
            ->select('p.id as page_id', 'k.slug as keyword_slug', 'k.cluster_tag as cluster_tag')
            ->get();

        $this->command->info("Food pages to process: " . $foodPages->count());

        $added = 0;
        $skipped = 0;

        foreach ($foodPages as $page) {
            $hasNearby = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->page_id)
                ->where('block_type', 'nearby_destinations')
                ->exists();
            $hasRelated = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->page_id)
                ->where('block_type', 'related_blogs')
                ->exists();

            if ($hasNearby && $hasRelated) {
                $skipped++;
                continue;
            }

            // Locate the author block — both new blocks insert right
            // above it so the bottom narrative stays: content → related
            // reads → nearby destinations → byline.
            $authorBlock = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->page_id)
                ->where('block_type', 'author')
                ->orderBy('sort_order')
                ->first();

            // If there's no author block we'll just append at the end —
            // some pages might not have had one seeded yet.
            $insertStart = $authorBlock
                ? (int) $authorBlock->sort_order
                : ((int) DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $page->page_id)
                    ->max('sort_order') + 1);

            $cluster = (string) ($page->cluster_tag ?? 'other');
            $keywords = $this->clusterKeywords[$cluster] ?? $this->clusterKeywords['other'];

            DB::transaction(function () use ($page, $insertStart, $cluster, $keywords, $hasRelated, $hasNearby, $authorBlock) {
                $newBlocksCount = 0;
                if (!$hasRelated) $newBlocksCount++;
                if (!$hasNearby) $newBlocksCount++;

                // Push the author block (and anything past it) down by
                // however many new blocks we're inserting. Walk in
                // descending sort_order so we don't temporarily collide
                // with the row we just bumped.
                if ($authorBlock) {
                    DB::table('rg_content_blocks')
                        ->where('owner_type', 'seo_page')
                        ->where('owner_id', $page->page_id)
                        ->where('sort_order', '>=', $insertStart)
                        ->orderByDesc('sort_order')
                        ->get()
                        ->each(function ($row) use ($newBlocksCount) {
                            DB::table('rg_content_blocks')
                                ->where('id', $row->id)
                                ->update(['sort_order' => $row->sort_order + $newBlocksCount]);
                        });
                }

                $cursor = $insertStart;

                if (!$hasRelated) {
                    DB::table('rg_content_blocks')->insert([
                        'owner_type' => 'seo_page',
                        'owner_id' => $page->page_id,
                        'sort_order' => $cursor,
                        'block_type' => 'related_blogs',
                        'payload_json' => json_encode([
                            'heading' => 'More reads on the area',
                            'intro' => '',
                            'auto_from_keywords' => $keywords,
                            'max' => 3,
                            'items' => [],
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $cursor++;
                }

                if (!$hasNearby) {
                    DB::table('rg_content_blocks')->insert([
                        'owner_type' => 'seo_page',
                        'owner_id' => $page->page_id,
                        'sort_order' => $cursor,
                        'block_type' => 'nearby_destinations',
                        'payload_json' => json_encode([
                            'heading' => 'Stay nearby after the meal',
                            'intro' => '',
                            'auto_from_cluster' => $cluster,
                            'exclude_slug' => $page->keyword_slug,
                            'max' => 6,
                            'items' => [],
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            $added++;
        }

        $this->command->info("Pages updated: {$added}, skipped (already had blocks): {$skipped}");
    }
}
