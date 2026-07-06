<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Creates the shared `destination-cluster` template page and its blocks.
 * Once seeded, DestinationsController@cluster renders these blocks for EVERY
 * /destinations/{region} page (with that region's data injected as context),
 * instead of the hardcoded destinations.cluster view. Re-runnable.
 */
class SeedDestinationClusterBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $pageId = DB::table('rg_static_pages')->where('slug', 'destination-cluster')->value('id');
        if (!$pageId) {
            $pageId = DB::table('rg_static_pages')->insertGetId([
                'slug' => 'destination-cluster',
                'title' => 'Destination Cluster Template',
                'meta_title' => 'Destination Cluster Template',
                'meta_description' => 'Shared block layout rendered for every /destinations/{region} page.',
                'is_published' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('rg_static_pages')->where('id', $pageId)->update(['is_published' => 1, 'updated_at' => $now]);
        }

        $blocks = [
            ['destcluster_hero', ['eyebrow' => 'Destination Guide', 'show_breadcrumb' => true]],
            ['destcluster_whats_in', ['heading_prefix' => "What's In", 'show_intro' => true]],
            ['destcluster_featured_spots', ['heading_prefix' => 'Featured', 'tahu_word' => 'Tourist Spots', 'description' => 'A rotating look at the places worth building a trip around. Tap any card to open it on the map.']],
            ['destcluster_testimonials', ['heading_prefix' => 'What', 'tahu_word' => 'Travelers Say', 'heading_suffix' => 'About', 'description' => '']],
            ['destcluster_explore_regions', ['heading' => 'Explore Other Regions']],
            ['destcluster_hashtags', ['heading' => 'Tags', 'description' => '']],
        ];

        DB::table('rg_content_blocks')->where('owner_type', 'static_page')->where('owner_id', $pageId)->delete();
        $sort = 0;
        foreach ($blocks as [$type, $payload]) {
            DB::table('rg_content_blocks')->insert([
                'owner_type' => 'static_page',
                'owner_id' => $pageId,
                'sort_order' => $sort++,
                'block_type' => $type,
                'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command?->info("destination-cluster template seeded (page #{$pageId}, " . count($blocks) . ' blocks).');
    }
}
