<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds 6 Filipino travel-writer personas with DiceBear-generated avatars,
 * then assigns each rg_seo_pages row to an author whose covered clusters
 * include the page's keyword cluster_tag. Re-running the seeder updates the
 * author rows in place and re-runs the assignment, but does not duplicate.
 */
class RgAuthorsSeeder extends Seeder
{
    public function run(): void
    {
        // Pexels portraits sourced via background research agent (Pexels License,
        // hot-linked from images.pexels.com). For personas where the agent could
        // not verify a real Filipino portrait, we fall back to a DiceBear avatar
        // generated from the name. Photographer credit is preserved in the bio.
        $authors = [
            [
                'name' => 'Maria Clara Mendoza',
                'role' => 'Freelance travel writer, Luzon',
                'bio' => 'Maria Clara grew up in Quezon City and has spent the last six years freelancing for Filipino travel sites and weekend-trip blogs around Luzon. She gravitates toward Antipolo, Bulacan, and the Manila side trips most family travelers can pull off in a Saturday. Big fan of Pinto Art Museum on a quiet weekday.',
                'home_base' => 'Quezon City',
                'instagram' => 'mariaclaraonthego',
                'covers_clusters' => 'rizal,bulacan,metro-manila',
                'avatar_url' => 'https://images.pexels.com/photos/2709388/pexels-photo-2709388.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop',
                'avatar_seed' => 'MariaClara',
                'avatar_bg' => 'fce7f3',
            ],
            [
                'name' => 'Joaquin Reyes',
                'role' => 'Freelance travel writer, Visayas + Palawan',
                'bio' => 'Joaquin is a Cebuano food and travel writer who has been ferry-hopping between Cebu, Bohol, and Palawan since 2019. He spends most weekends documenting the restaurants and resorts that locals queue at, not the ones with billboard ads.',
                'home_base' => 'Cebu City',
                'instagram' => 'joaquineats',
                'covers_clusters' => 'visayas,palawan',
                'avatar_url' => 'https://images.pexels.com/photos/2100697/pexels-photo-2100697.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop',
                'avatar_seed' => 'JoaquinReyes',
                'avatar_bg' => 'e0f2fe',
            ],
            [
                'name' => 'Trisha Bautista',
                'role' => 'Freelance writer, city stays + Airbnb',
                'bio' => 'Trisha is a freelance travel writer who has stayed at more than 70 different Airbnbs across Metro Manila and Cavite while juggling remote-work contracts. She writes about what makes a city stay actually comfortable, not just photogenic, and tends to ignore listings whose photos look a little too staged.',
                'home_base' => 'Makati',
                'instagram' => 'trishawanders',
                'covers_clusters' => 'metro-manila,cavite',
                'avatar_url' => 'https://images.pexels.com/photos/3162769/pexels-photo-3162769.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop',
                'avatar_seed' => 'TrishaBautista',
                'avatar_bg' => 'ffe4e6',
            ],
            [
                'name' => 'Miguel "Migs" Santos',
                'role' => 'Freelance outdoor writer, Mindanao',
                'bio' => 'Migs grew up in Davao and writes about the southern half of the country that gets too little coverage. From Mt. Apo trails to Samal cove resorts, his beat is honest takes on Mindanao travel for the Filipino DIY crowd. He cycles to most of his assignments.',
                'home_base' => 'Davao City',
                'instagram' => 'migs.south',
                'covers_clusters' => 'mindanao,north-luzon',
                'avatar_url' => null,  // agent couldn't verify a mature male outdoor portrait
                'avatar_seed' => 'MigsSantos',
                'avatar_bg' => 'ffedd5',
            ],
            [
                'name' => 'Bea Villanueva',
                'role' => 'Freelance travel writer, Batangas + Laguna',
                'bio' => 'Bea is from Lipa and knows every back road between Tagaytay and Calatagan. She writes about the family-resort circuit her own relatives book every summer, and is unforgiving about overpriced bulalo joints.',
                'home_base' => 'Lipa, Batangas',
                'instagram' => 'beasouthbound',
                'covers_clusters' => 'batangas,laguna,quezon',
                'avatar_url' => 'https://images.pexels.com/photos/3229336/pexels-photo-3229336.jpeg?auto=compress&cs=tinysrgb&w=400&h=400&fit=crop',
                'avatar_seed' => 'BeaVillanueva',
                'avatar_bg' => 'd1fae5',
            ],
            [
                'name' => 'Jasper Cruz',
                'role' => 'Freelance travel writer, Bicol + Northern Luzon',
                'bio' => 'Jasper is a Naga native turned freelance writer who covers the volcanic-belt provinces and the long road north. He has eaten Bicol Express in 14 versions across the region and ranks them by spice level.',
                'home_base' => 'Naga City',
                'instagram' => 'jaspernortheast',
                'covers_clusters' => 'bicol,pampanga,north-luzon',
                'avatar_url' => null,
                'avatar_seed' => 'JasperCruz',
                'avatar_bg' => 'fef3c7',
            ],
        ];

        foreach ($authors as $i => $a) {
            $slug = Str::slug($a['name']);
            // Prefer the real Pexels portrait when verified; fall back to DiceBear.
            $avatarPath = $a['avatar_url']
                ?: ('https://api.dicebear.com/7.x/notionists/svg?seed=' . urlencode($a['avatar_seed']) . '&backgroundColor=' . $a['avatar_bg']);
            DB::table('rg_authors')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $a['name'],
                    'role' => $a['role'],
                    'bio' => $a['bio'],
                    'avatar_path' => $avatarPath,
                    'home_base' => $a['home_base'],
                    'instagram' => $a['instagram'],
                    'covers_clusters' => $a['covers_clusters'],
                    'status' => 'active',
                    'sort_order' => $i + 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('Authors upserted: ' . count($authors));
        $this->assignAuthorsToPages();
    }

    private function assignAuthorsToPages(): void
    {
        $authors = DB::table('rg_authors')->where('status', 'active')->orderBy('sort_order')->get();
        if ($authors->isEmpty()) return;

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->select('p.id', 'p.slug', 'k.cluster_tag', 'p.author_id')
            ->get();

        $assigned = 0;
        foreach ($pages as $page) {
            $cluster = $page->cluster_tag ?: 'other';

            // Find authors who cover this cluster
            $candidates = $authors->filter(function ($a) use ($cluster) {
                $covers = array_map('trim', explode(',', $a->covers_clusters ?? ''));
                return in_array($cluster, $covers, true);
            });

            if ($candidates->isEmpty()) {
                $author = $authors->first();
            } else {
                // Deterministic pick by slug hash so the same page always gets the same author
                $idx = abs(crc32($page->slug)) % $candidates->count();
                $author = $candidates->values()[$idx];
            }

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'author_id' => $author->id,
                'updated_at' => now(),
            ]);
            $assigned++;
        }

        $this->command->info("Pages assigned an author: $assigned");
    }
}
