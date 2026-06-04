<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Brings every resort/destination keyword page up to the same block
 * coverage food/restaurant pages have. Each block is inserted only when
 * the page doesn't already carry it, so the seeder is fully re-runnable
 * — adding new block types over time without disrupting prior runs.
 *
 * All blocks land just above the author byline so the bottom narrative
 * stays: content → nearby destinations → related reads → byline. Map
 * embed + external guides slot in earlier (before the author tail) so
 * they live next to the destination-detail material rather than the
 * cross-vertical recommendations.
 *
 * Blocks added per page:
 *   - map_embed             — Google Maps embed centred on the destination
 *   - external_guides       — TripAdvisor + Booking + Agoda + Google search URLs
 *   - how_to_get_to         — generic transport-method card
 *   - nearby_destinations   — auto-resolved from same cluster_tag
 *   - related_blogs         — auto-resolved by area keywords
 */
class AddBlocksToDestinationPagesSeeder extends Seeder
{
    /**
     * Tag/keyword candidates per cluster — used by the related_blogs
     * auto-resolver. Same table the food-page seeder uses so behaviour
     * stays in sync across both verticals.
     */
    private array $clusterKeywords = [
        'metro-manila' => ['Manila', 'Metro Manila', 'Makati', 'BGC', 'Pasay', 'Quezon City', 'Mandaluyong', 'Pasig', 'Taguig', 'Ortigas', 'Marikina'],
        'cavite' => ['Cavite', 'Tagaytay', 'Bacoor', 'Imus', 'Dasmarinas', 'Silang', 'Alfonso'],
        'batangas' => ['Batangas', 'Lipa', 'Calatagan', 'Nasugbu', 'Anilao', 'Mabini', 'Lemery'],
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
        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->where('k.category', 'resort')
            ->select(
                'p.id as page_id',
                'p.h1 as h1',
                'k.slug as keyword_slug',
                'k.phrase as keyword_phrase',
                'k.cluster_tag as cluster_tag'
            )
            ->get();

        $this->command->info('Destination pages to process: ' . $pages->count());

        $stats = [
            'map_embed' => 0,
            'external_guides' => 0,
            'how_to_get_to' => 0,
            'nearby_destinations' => 0,
            'related_blogs' => 0,
        ];

        foreach ($pages as $page) {
            $cluster = (string) ($page->cluster_tag ?? 'other');
            $keywords = $this->clusterKeywords[$cluster] ?? $this->clusterKeywords['other'];
            $placeName = trim((string) ($page->h1 ?: $page->keyword_phrase));

            $this->insertIfMissing($page->page_id, 'map_embed', fn () => $this->mapEmbedPayload($placeName), $stats);
            $this->insertIfMissing($page->page_id, 'external_guides', fn () => $this->externalGuidesPayload($placeName), $stats);
            $this->insertIfMissing($page->page_id, 'how_to_get_to', fn () => $this->howToGetToPayload($placeName, $cluster), $stats);
            $this->insertIfMissing($page->page_id, 'nearby_destinations', fn () => $this->nearbyDestinationsPayload($cluster, $page->keyword_slug), $stats);
            $this->insertIfMissing($page->page_id, 'related_blogs', fn () => $this->relatedBlogsPayload($keywords), $stats);
        }

        foreach ($stats as $type => $count) {
            $this->command->info("  {$type}: {$count} inserted");
        }
    }

    /**
     * Insert a new block of the given type only when the page doesn't
     * already have one. Positioning rule: drop the new block immediately
     * before the page's author byline (so the bottom-of-article rhythm
     * stays consistent), or append at the end when no author exists.
     */
    private function insertIfMissing(int $pageId, string $type, callable $payloadFactory, array &$stats): void
    {
        $existing = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', $type)
            ->exists();
        if ($existing) return;

        $author = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', 'author')
            ->orderBy('sort_order')
            ->first();

        $targetSortOrder = $author
            ? (int) $author->sort_order
            : ((int) (DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->max('sort_order') ?? 0) + 1);

        DB::transaction(function () use ($pageId, $type, $payloadFactory, $author, $targetSortOrder) {
            if ($author) {
                DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $pageId)
                    ->where('sort_order', '>=', $targetSortOrder)
                    ->orderByDesc('sort_order')
                    ->get()
                    ->each(function ($row) {
                        DB::table('rg_content_blocks')
                            ->where('id', $row->id)
                            ->update(['sort_order' => $row->sort_order + 1]);
                    });
            }
            DB::table('rg_content_blocks')->insert([
                'owner_type' => 'seo_page',
                'owner_id' => $pageId,
                'sort_order' => $targetSortOrder,
                'block_type' => $type,
                'payload_json' => json_encode($payloadFactory()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $stats[$type]++;
    }

    private function mapEmbedPayload(string $placeName): array
    {
        $query = $placeName . ', Philippines';
        return [
            'heading' => 'Where is ' . $placeName . '?',
            'embed_url' => 'https://www.google.com/maps?q=' . rawurlencode($query) . '&output=embed',
            'height' => 420,
        ];
    }

    private function externalGuidesPayload(string $placeName): array
    {
        $q = rawurlencode($placeName);
        return [
            'heading' => 'Compare picks for ' . $placeName . ' on third-party guides',
            'intro' => '',
            'footnote' => 'External links open in a new tab. We do not get paid for clicks.',
            'items' => [
                ['name' => 'TripAdvisor', 'url' => "https://www.tripadvisor.com.ph/Search?q={$q}", 'color' => 'emerald', 'blurb' => ''],
                ['name' => 'Booking.com', 'url' => "https://www.booking.com/searchresults.html?ss={$q}+Philippines", 'color' => 'blue', 'blurb' => ''],
                ['name' => 'Agoda', 'url' => "https://www.agoda.com/search?q={$q}+Philippines", 'color' => 'rose', 'blurb' => ''],
                ['name' => 'Google Maps', 'url' => "https://www.google.com/maps/search/?api=1&query={$q}+Philippines", 'color' => 'blue', 'blurb' => ''],
            ],
        ];
    }

    private function howToGetToPayload(string $placeName, string $cluster): array
    {
        // Per-cluster transport defaults — Luzon places lean on bus/car
        // from Manila, Visayas/Palawan/Mindanao lean on plane.
        $isAirHeavy = in_array($cluster, ['visayas', 'palawan', 'mindanao', 'north-luzon'], true);

        $methods = $isAirHeavy
            ? [
                [
                    'title' => 'By plane',
                    'icon' => 'plane',
                    'color' => 'blue',
                    'subtitle' => 'Direct domestic flights',
                    'detail' => 'Cebu Pacific, Philippine Airlines, and AirAsia operate the main routes from Manila and Cebu. Book midweek departures for the cheapest fares; weekends and long weekends spike fast.',
                ],
                [
                    'title' => 'By ferry',
                    'icon' => 'boat',
                    'color' => 'emerald',
                    'subtitle' => 'Cross-island option',
                    'detail' => '2GO and OceanJet sail the inter-island routes when the weather cooperates. Slower than flying but useful when you are bringing a vehicle or extra luggage.',
                ],
                [
                    'title' => 'Local transport on arrival',
                    'icon' => 'tricycle',
                    'color' => 'amber',
                    'subtitle' => 'Tricycle or Grab',
                    'detail' => 'From the airport or port, ride-hail apps work in the main cities; tricycles and habal-habal cover the last mile in smaller towns. Negotiate fares before getting in when there is no meter.',
                ],
            ]
            : [
                [
                    'title' => 'By private car',
                    'icon' => 'car',
                    'color' => 'emerald',
                    'subtitle' => 'Self-drive from Manila',
                    'detail' => 'Quickest door-to-door option. Travel time depends heavily on EDSA traffic — leave before 6 AM to skip the first wave, or push past 10 PM on Friday for a quieter run.',
                ],
                [
                    'title' => 'By bus',
                    'icon' => 'bus',
                    'color' => 'blue',
                    'subtitle' => 'Provincial bus lines',
                    'detail' => 'Victory Liner, Genesis, and DLTB run regular schedules from Cubao and Pasay terminals. Tickets are easy to buy at the terminal, deluxe seats add a small premium for reclining seats and Wi-Fi.',
                ],
                [
                    'title' => 'By ride-hailing',
                    'icon' => 'car',
                    'color' => 'amber',
                    'subtitle' => 'Grab door-to-door',
                    'detail' => 'For shorter inter-province hops, Grab and InDrive both run point-to-point trips. Cleaner than commuting if you are bringing luggage, more expensive than a bus.',
                ],
            ];

        return [
            'heading' => 'How to get to ' . $placeName,
            'intro' => 'Most weekend trips to ' . $placeName . ' come down to one of these three routes. The right pick depends on whether you value time, cost, or comfort the most.',
            'methods' => $methods,
            'footer' => 'Whichever route you take, build in a buffer of one to two hours on top of any published schedule. Schedules slip during peak season, and Metro Manila traffic on Friday afternoons can stretch a four-hour drive into seven.',
        ];
    }

    private function nearbyDestinationsPayload(string $cluster, string $excludeSlug): array
    {
        return [
            'heading' => 'More destinations in the same area',
            'intro' => '',
            'auto_from_cluster' => $cluster,
            'exclude_slug' => $excludeSlug,
            'max' => 6,
            'items' => [],
        ];
    }

    private function relatedBlogsPayload(array $keywords): array
    {
        return [
            'heading' => 'More reads on the area',
            'intro' => '',
            'auto_from_keywords' => $keywords,
            'max' => 3,
            'items' => [],
        ];
    }
}
