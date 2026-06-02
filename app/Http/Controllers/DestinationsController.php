<?php

namespace App\Http\Controllers;

use App\Models\RgKeyword;
use App\Models\RgTouristSpot;
use Illuminate\Support\Collection;

class DestinationsController extends Controller
{
    public function index()
    {
        $clusters = self::clusterMetadata();
        // Hard-filter to category=resort: food keywords have their own
        // /food-trip index and must never appear on /destinations.
        $keywords = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->get();

        $byCluster = $keywords->groupBy('cluster_tag');

        $orderedClusters = collect($clusters)
            ->map(function ($meta, $slug) use ($byCluster) {
                $meta['slug'] = $slug;
                $meta['keywords'] = $byCluster->get($slug, collect());
                $meta['count'] = $meta['keywords']->count();
                $meta['total_volume'] = $meta['keywords']->sum('search_volume_monthly');
                return $meta;
            })
            ->filter(fn($c) => $c['count'] > 0)
            ->sortByDesc('total_volume')
            ->values();

        $stats = [
            'total_destinations' => $keywords->count(),
            'total_regions' => $orderedClusters->count(),
            'top_volume' => (int) $keywords->max('search_volume_monthly'),
        ];

        $featuredSpots = $this->buildFeaturedSpots();
        $searchIndex   = $this->buildSearchIndex($orderedClusters);

        return view('destinations.index', compact('orderedClusters', 'stats', 'featuredSpots', 'searchIndex'));
    }

    /**
     * Flat, client-side-searchable list of regions, keyword landing pages,
     * and every published tourist spot from rg_tourist_spots. Shipped inline
     * to the page as JSON (~30-60KB) so typeahead matches are instant.
     */
    private function buildSearchIndex($orderedClusters): array
    {
        $idx = [];

        foreach ($orderedClusters as $c) {
            $idx[] = [
                'type'     => 'region',
                'label'    => $c['name'],
                'sub'      => $c['count'] . ' destinations',
                'url'      => route('destinations.cluster', $c['slug']),
                'haystack' => mb_strtolower($c['name'] . ' ' . ($c['tagline'] ?? '')),
                'volume'   => (int) $c['total_volume'],
            ];
        }

        foreach ($orderedClusters as $c) {
            foreach ($c['keywords'] as $kw) {
                $cleanLoc = trim(preg_replace(
                    '/^(beach\s+resort|resort|hotel|hotels|airbnb|villa|tourist\s+spot)\s+in\s+/i',
                    '',
                    $kw->phrase
                ));
                $idx[] = [
                    'type'     => 'destination',
                    'label'    => $kw->phrase,
                    'sub'      => $c['name'] . ' · ' . number_format($kw->search_volume_monthly) . ' people search this monthly',
                    'url'      => url('/' . $kw->slug),
                    'haystack' => mb_strtolower($kw->phrase . ' ' . $c['name'] . ' ' . $cleanLoc),
                    'volume'   => (int) $kw->search_volume_monthly,
                ];
            }
        }

        // Every published tourist spot that has a linked keyword. Eager-load
        // media + keyword so the index builds in two queries, not N+M.
        $spots = RgTouristSpot::query()
            ->where('status', 'published')
            ->whereNotNull('keyword_id')
            ->with(['media', 'keyword'])
            ->get();

        foreach ($spots as $s) {
            $idx[] = [
                'type'     => 'spot',
                'label'    => $s->name,
                'sub'      => $s->location ?: ($s->region_label ?? ''),
                'url'      => url('/' . $s->keyword->slug),
                'haystack' => mb_strtolower(
                    $s->name . ' ' . ($s->location ?? '') . ' ' . ($s->region_label ?? '')
                ),
                'volume'   => 0,
                'image'    => $s->media ? asset('storage/' . $s->media->path) : null,
            ];
        }

        return $idx;
    }

    /**
     * Tourist spots flagged with featured_order > 0 in rg_tourist_spots,
     * ordered. Each row needs both a linked keyword (so the card has a
     * "see nearby stays" target) and a media row (so the photo renders).
     * Managed via the mother-app admin under Resort Guru → Tourist Spots.
     */
    private function buildFeaturedSpots(): array
    {
        return RgTouristSpot::query()
            ->where('status', 'published')
            ->whereNotNull('featured_order')
            ->whereNotNull('keyword_id')
            ->whereNotNull('media_id')
            ->with(['media', 'keyword'])
            ->orderBy('featured_order')
            ->get()
            ->map(function ($s) {
                return [
                    'name'     => $s->name,
                    'location' => $s->location ?? '',
                    'region'   => $s->region_label ?? '',
                    'image'    => $s->media->path,
                    'slug'     => $s->keyword->slug,
                ];
            })
            ->filter(fn($row) => is_file(storage_path('app/public/' . $row['image']))
                && filesize(storage_path('app/public/' . $row['image'])) > 5000)
            ->values()
            ->all();
    }

    /**
     * (Legacy hand-curated array retained only as fallback if ever needed —
     * no longer reachable in the index() flow.)
     */
    private function legacyFeaturedSpotsArray(): array
    {
        return [
            [
                'name' => 'Hundred Islands',
                'location' => 'Alaminos, Pangasinan',
                'region' => 'North Luzon',
                'image' => 'rg-media/destinations/alaminos-hundred-islands-1.jpg',
                'slug' => 'resort-in-alaminos-pangasinan',
            ],
            [
                'name' => 'Big Lagoon',
                'location' => 'El Nido, Palawan',
                'region' => 'Palawan',
                'image' => 'rg-media/spots/el-nido-big-lagoon-tour-a.jpg',
                'slug' => 'resort-in-el-nido',
                'size' => 'normal',
            ],
            [
                'name' => 'Cagsawa Ruins',
                'location' => 'Daraga, Albay',
                'region' => 'Bicol',
                'image' => 'rg-media/spots/albay-legazpi-cagsawa-ruins.jpg',
                'slug' => 'resort-in-albay',
                'size' => 'normal',
            ],
            [
                'name' => "Magellan's Cross",
                'location' => 'Cebu City',
                'region' => 'Visayas',
                'image' => 'rg-media/spots/cebu-city-magellans-cross-and-basilica-del-santo-nino.jpg',
                'slug' => 'resort-in-cebu-city',
                'size' => 'wide',
            ],
            [
                'name' => 'Mt. Apo',
                'location' => 'Davao',
                'region' => 'Mindanao',
                'image' => 'rg-media/spots/kidapawan-mt-apo-natural-park.jpg',
                'slug' => 'resort-in-davao-city',
                'size' => 'normal',
            ],
            [
                'name' => 'Mayon Volcano',
                'location' => 'Legazpi, Albay',
                'region' => 'Bicol',
                'image' => 'rg-media/destinations/albay-legazpi-1.jpg',
                'slug' => 'resort-in-albay',
                'size' => 'normal',
            ],
            [
                'name' => 'Surf Beach',
                'location' => 'San Juan, La Union',
                'region' => 'La Union',
                'image' => 'rg-media/spots/la-union-san-juan-surf-beach-urbiztondo.jpg',
                'slug' => 'beach-resort-in-la-union',
            ],
            [
                'name' => 'Tangadan Falls',
                'location' => 'La Union',
                'region' => 'La Union',
                'image' => 'rg-media/spots/la-union-tangadan-falls.jpg',
                'slug' => 'beach-resort-in-la-union',
            ],
            [
                'name' => 'Bangui Windmills',
                'location' => 'Bangui, Ilocos Norte',
                'region' => 'Ilocos',
                'image' => 'rg-media/destinations/ilocos-norte-2.jpg',
                'slug' => 'tourist-spot-in-ilocos-norte',
            ],
            [
                'name' => 'Patar Beach',
                'location' => 'Bolinao, Pangasinan',
                'region' => 'North Luzon',
                'image' => 'rg-media/spots/bolinao-patar-beach.jpg',
                'slug' => 'resort-in-bolinao',
            ],
            [
                'name' => 'Manaoag Shrine',
                'location' => 'Pangasinan',
                'region' => 'North Luzon',
                'image' => 'rg-media/destinations/manaoag-1.jpg',
                'slug' => 'hotel-in-manaoag-pangasinan',
            ],
            [
                'name' => 'Dasol Beach',
                'location' => 'Dasol, Pangasinan',
                'region' => 'North Luzon',
                'image' => 'rg-media/destinations/dasol-1.jpg',
                'slug' => 'resort-in-dasol-pangasinan',
            ],
        ];

        // Filter: only include entries whose image actually exists on disk so
        // the carousel never renders broken slides.
        return array_values(array_filter($candidates, function ($c) {
            return is_file(storage_path('app/public/' . $c['image']))
                && filesize(storage_path('app/public/' . $c['image'])) > 5000;
        }));
    }

    public function cluster(string $cluster)
    {
        $meta = self::clusterMetadata()[$cluster] ?? null;
        if (!$meta) abort(404);

        $keywords = RgKeyword::where('cluster_tag', $cluster)
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->get();

        if ($keywords->isEmpty()) abort(404);

        $others = collect(self::clusterMetadata())
            ->except($cluster)
            ->map(function ($m, $slug) {
                $m['slug'] = $slug;
                $m['count'] = RgKeyword::where('cluster_tag', $slug)
                    ->where('category', 'resort')
                    ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
                    ->count();
                return $m;
            })
            ->filter(fn($c) => $c['count'] > 0)
            ->sortByDesc('count')
            ->take(8)
            ->values();

        return view('destinations.cluster', compact('meta', 'cluster', 'keywords', 'others'));
    }

    public static function clusterMetadata(): array
    {
        return [
            'batangas' => [
                'name' => 'Batangas',
                'tagline' => 'White-sand beaches in Laiya, diving in Anilao, and ridge-cool inland stays in Lipa.',
                'intro_html' => '<p>Batangas spans the southwestern tip of Luzon and packs unusual variety into one province. The eastern coast at Laiya in San Juan delivers white-sand family beaches. The western coast at Calatagan, Nasugbu, and the Hamilo Coast development holds higher-end resorts and beach clubs. Mabini\'s Anilao corner is the dive capital of Luzon. Inland Lipa and Lobo offer quieter weekend retreats with cooler weather.</p><p>Travel time from BGC ranges from 2 hours (closest beach clubs at Nasugbu) to 3.5 hours (Laiya beachfront). The STAR Tollway and CALAX have noticeably shortened most drives. Pick a town by what you want: white-sand beach (Laiya, Calatagan), divers\' base (Anilao), beach club day trip (Nasugbu, Hamilo), or quiet inland retreat (Lipa).</p>',
                'meta_description' => 'Browse resorts in Batangas across Laiya, Calatagan, Anilao, Nasugbu, and Lipa. Beach, dive, and inland weekend options compared.',
            ],
            'cavite' => [
                'name' => 'Cavite',
                'tagline' => 'From the cool Tagaytay ridge to the western coast beaches of Naic.',
                'intro_html' => '<p>Cavite stretches from the coastline at Ternate up to the highlands of Tagaytay, which means a resort in Cavite can be almost anything. Beach properties in Naic, pool resorts in Bacoor and Dasmariñas, coffee-country villas in Amadeo, and ridge-cooled hotels in Alfonso all fall under the same province.</p><p>Upper Cavite (Tagaytay, Alfonso, Amadeo, Indang) offers cool ridge weather and private villas. Mid Cavite (Silang, Dasmariñas, Imus) handles pool resort and function hall traffic. Lower Cavite (Bacoor) is urban and closest to NCR. Coastal Cavite (Naic, Ternate) opens to the West Philippine Sea with quieter beach stays.</p>',
                'meta_description' => 'Find resorts in Cavite from Tagaytay ridge villas to Dasmariñas pool venues and Naic coastal stays. Compare picks by town here.',
            ],
            'rizal' => [
                'name' => 'Rizal Province',
                'tagline' => 'Antipolo hilltops, Tanay nature retreats, and Pansol-side lakeshores.',
                'intro_html' => '<p>Rizal province wraps around the eastern edge of Metro Manila and is the closest weekend escape for QC and Marikina residents. A resort in Rizal can mean a hilltop villa in Antipolo, a lakeside stay in Binangonan, a riverside resort in Tanay, or a hot-spring property in Taytay.</p><p>Travel times from QC range from 45 minutes (Antipolo, Taytay) to 2 hours (Tanay\'s remote nature retreats). Antipolo gives hilltop views and cool air. Tanay holds nature retreats and the Masungi Georeserve. Binangonan offers lakeside fish meals along Laguna de Bay. Rodriguez and San Mateo have river resorts.</p>',
                'meta_description' => 'Browse resorts in Rizal province across Antipolo, Tanay, Binangonan, and Rodriguez. The closest weekend escape from Metro Manila.',
            ],
            'laguna' => [
                'name' => 'Laguna',
                'tagline' => 'Pansol hot springs, Seven Lakes country, and Pagsanjan\'s falls.',
                'intro_html' => '<p>Laguna sits roughly an hour south of Manila and is best known for hot spring resorts in Pansol and Calamba, and quieter lakeside stays around Pagsanjan and Lumban. The famous Pansol private pool rentals draw weekend bookings from across Metro Manila year-round, with geothermal water warm enough to enjoy at any time.</p><p>San Pablo\'s Seven Lakes country offers a quieter alternative with boutique inns and lakeside dining. Nagcarlan and Liliw sit at higher elevation with cooler weather. Travel time from QC is around 75 to 120 minutes via SLEX.</p>',
                'meta_description' => 'Find resorts in Laguna including Pansol hot springs, San Pablo lakes, and Nagcarlan upland stays. Compare picks by town.',
            ],
            'pampanga' => [
                'name' => 'Pampanga',
                'tagline' => 'Food capital with Clark hotels, cold-spring resorts, and big event venues.',
                'intro_html' => '<p>Pampanga is the food capital of the Philippines and a growing destination for pool resorts, hot-spring stays, and Clark-zone hotels. Angeles City inside the Clark Freeport offers business-class hotels with full amenities and proximity to Clark International Airport. Arayat has cold-spring pool resorts at the foot of Mount Arayat.</p><p>Mexico, Magalang, and Lubao host family pool venues and event halls for big reunions. Travel time from QC ranges from 60 to 90 minutes via NLEX. The province is particularly known for its food including sisig, kare-kare, and the lantern-making heritage in San Fernando.</p>',
                'meta_description' => 'Resorts in Pampanga: Clark hotels, Angeles business stays, Arayat cold-spring pool resorts, and reunion-ready event venues.',
            ],
            'bulacan' => [
                'name' => 'Bulacan',
                'tagline' => 'The closest weekend reunion province just north of Quezon City.',
                'intro_html' => '<p>Bulacan sits just north of Quezon City and is the closest weekend resort destination from Manila with proper pool and function-hall properties. The province serves the weekend reunion crowd well, with most resorts clustered around Norzagaray, San Jose del Monte, Pandi, and Angat.</p><p>Travel time from QC ranges from 60 to 90 minutes via NLEX. Resorts here range from classic pool-and-grill barangay-style venues to modern resort-hotels with infinity pools and event halls for 200-pax weddings. The cooler upland properties in Norzagaray benefit from the elevation.</p>',
                'meta_description' => 'Find resorts in Bulacan with Norzagaray hillside villas and Pandi family pool venues. The closest reunion-friendly weekend destination from Manila.',
            ],
            'quezon' => [
                'name' => 'Quezon Province',
                'tagline' => 'Pacific coast beaches, heritage Sariaya, and Lucban\'s Pahiyas country.',
                'intro_html' => '<p>Quezon province stretches along the eastern coast of Luzon and packs a surprising variety of resorts into a single province. Beach properties in Pagbilao and Atimonan face the Pacific. Heritage-themed inns in Sariaya and Lucban offer cooler weather. Lucena City serves as the central business and ferry hub.</p><p>Travel time from QC ranges from 3 hours (Lucena) to 5 hours (Aurora-bordering towns). The Pahiyas Festival in Lucban every May 15 is the most-famous regional event. Quezon offers something for almost every traveller type, from surf trips on the Pacific coast to heritage walks in colonial towns.</p>',
                'meta_description' => 'Browse resorts in Quezon province. Pacific coast beaches in Pagbilao, heritage inns in Sariaya and Lucban, and Lucena city hotels.',
            ],
            'metro-manila' => [
                'name' => 'Metro Manila',
                'tagline' => 'City hotels and pool venues for short same-day stays inside the capital.',
                'intro_html' => '<p>Metro Manila is the urban core of the country and not traditionally a resort destination, but several hotel-resorts and pool venues inside the city work for short same-day stays. Manila Bay hotels along Roxas Boulevard offer sunset views and pool decks. BGC in Taguig has the densest cluster of hotel-resorts with full amenities.</p><p>For Airbnb stays, BGC, Makati, Ortigas, and Poblacion are the main neighbourhoods. For pool day venues, Quezon City and the southern metro towns have growing options. Travel within the metro varies hugely with traffic.</p>',
                'meta_description' => 'Resorts and pool venues in Metro Manila. BGC hotel resorts, Manila Bay hotels, and Airbnb neighbourhoods compared.',
            ],
            'north-luzon' => [
                'name' => 'North Luzon',
                'tagline' => 'Subic family beaches, La Union surf, Bolinao white sand, and Hundred Islands.',
                'intro_html' => '<p>North Luzon covers Pangasinan, La Union, Zambales, Bataan, Tarlac, Nueva Ecija, and the surrounding provinces. The region offers everything from Subic\'s family beach resorts to Bolinao\'s white-sand stretches to La Union\'s surf scene. Travel time from QC ranges from 2.5 hours (Subic) to 6 hours (La Union).</p><p>TPLEX has cut significant time off most northern destinations. Pick a town by trip type: family beach (Subic, Anvaya, Bauang), surf (San Juan, La Union), island-hopping (Hundred Islands, Alaminos), or quieter inland stays (Tarlac, Nueva Ecija). The Mount Pinatubo crater trek launches from Capas, Tarlac.</p>',
                'meta_description' => 'Resorts in North Luzon: Subic beaches, La Union surf, Bolinao white sand, Hundred Islands, and Pinatubo trek base. Compare here.',
            ],
            'bicol' => [
                'name' => 'Bicol Region',
                'tagline' => 'Mayon Volcano views, Donsol whale sharks, and Naga\'s pilgrimage center.',
                'intro_html' => '<p>The Bicol region covers Albay, Camarines Sur, Sorsogon, and surrounding provinces. The headline attraction is Mount Mayon, the perfectly-cone-shaped active volcano in Albay. Whale shark interactions in Donsol (November to May), wakeboarding at CWC in Pili, and the historic Pasalubong stops in Naga round out the typical itinerary.</p><p>Travel time from QC takes 8 to 10 hours by road or 60 to 70 minutes by direct flight to Legazpi or Naga. Bicol is also famous for its spicy cuisine including Bicol Express and laing.</p>',
                'meta_description' => 'Resorts in Bicol region: Albay Mayon views, Donsol whale sharks, Naga pilgrimage stays, and Sorsogon coastal options.',
            ],
            'visayas' => [
                'name' => 'Visayas',
                'tagline' => 'Cebu, Bohol, Negros, Iloilo, Guimaras, and the central islands\' best beaches.',
                'intro_html' => '<p>The Visayas covers Cebu, Bohol, Negros, Iloilo, Guimaras, Siquijor, and the surrounding islands. The region holds some of the country\'s most-visited beach destinations including Mactan, Panglao, and the famous Boracay (Aklan). Cebu City is the largest urban center and gateway for most flights.</p><p>Heritage stays in Iloilo and Silay offer something different from the beach formula. Diving in Dauin, Apo Island, and the Camotes draws underwater enthusiasts year-round. Direct flights from Manila reach most Visayas destinations in 75 to 90 minutes.</p>',
                'meta_description' => 'Resorts in Visayas: Cebu, Mactan, Bohol\'s Panglao, Negros, Iloilo, Guimaras, Siquijor, and Boracay. Compare beach destinations.',
            ],
            'mindanao' => [
                'name' => 'Mindanao',
                'tagline' => 'Samal Island beaches, Davao\'s pulse, GenSan tuna country, and Glan\'s quiet sand.',
                'intro_html' => '<p>Mindanao covers the southern third of the country including Davao, General Santos, Zamboanga, and surrounding regions. The region is famous for its evenly warm weather (less affected by typhoons than Luzon), the tuna capital at General Santos, the beaches at Samal Island and Glan, and Mount Apo in Davao province.</p><p>Davao City is the largest urban center and is often praised for its safety and food scene. Travel from Manila is by direct flight, with most flights taking 90 minutes. Many destinations remain underrated relative to their actual beach and nature quality.</p>',
                'meta_description' => 'Resorts in Mindanao: Davao city hotels, Samal Island beaches, GenSan, Glan white sand, and Zamboanga\'s Pink Beach.',
            ],
            'palawan' => [
                'name' => 'Palawan & Mindoro',
                'tagline' => 'El Nido lagoons, Puerto Galera diving, and the western edge\'s island paradises.',
                'intro_html' => '<p>Palawan is the long island province on the western edge of the country, famous for the limestone islands of El Nido, the shipwrecks and lakes of Coron, and the underground river in Puerto Princesa. The province dominates "best islands in the world" lists year after year. Resort tiers run from backpacker hostels to private-island five-stars.</p><p>Direct AirSWIFT flights reach El Nido from Manila in 90 minutes. Otherwise fly to Puerto Princesa and take a 5-hour van transfer. We\'ve grouped Puerto Galera here as a similar-character beach destination just across the Verde Passage.</p>',
                'meta_description' => 'Resorts in Palawan including El Nido, Puerto Galera, and the western islands. Backpacker stays to private-island five-stars.',
            ],
        ];
    }
}
