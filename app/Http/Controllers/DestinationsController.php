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

        // Per-region photo pools for the region circle thumbnails, mirroring
        // the homepage "Browse by region" imagery: published tourist-spot
        // media resolved to each cluster via RegionResolver (cached 10 min).
        $regionImgs = \Illuminate\Support\Facades\Cache::remember('dest_region_imgs_v1', 600, function () {
            $map = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots')) {
                $spots = RgTouristSpot::query()
                    ->where('status', 'published')
                    ->whereNotNull('media_id')
                    ->with('media')
                    ->get(['id', 'name', 'location', 'cluster_tag', 'media_id']);
                foreach ($spots as $s) {
                    if (!$s->media || !$s->media->path) continue;
                    $rk = \App\Support\RegionResolver::resolve((string) ($s->cluster_tag ?? ''), trim(($s->name ?? '') . ' ' . ($s->location ?? '')));
                    $map[$rk][] = '/storage/' . ltrim($s->media->path, '/');
                }
            }
            return $map;
        });

        $orderedClusters = collect($clusters)
            ->map(function ($meta, $slug) use ($byCluster, $regionImgs) {
                $meta['slug'] = $slug;
                $meta['keywords'] = $byCluster->get($slug, collect());
                $meta['count'] = $meta['keywords']->count();
                $meta['total_volume'] = $meta['keywords']->sum('search_volume_monthly');
                $imgs = array_values(array_unique($regionImgs[$slug] ?? []));
                shuffle($imgs);
                $meta['images'] = array_slice($imgs, 0, 4);
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

        // Block-driven render: if the `destinations` static_page row
        // has blocks attached, render them via BlockRenderer with the
        // controller data exposed via context. Otherwise fall back to
        // the legacy hardcoded view. Live Editor support: when the
        // request carries an HMAC _lt token signed against the
        // static_page slug, render with the live_edit context flag
        // and inject the rg-live-edit chrome assets in the view.
        $page = \DB::table('rg_static_pages')
            ->where('slug', 'destinations')
            ->where('is_published', 1)
            ->first();
        if ($page) {
            $blocks = \App\Models\RgContentBlock::forOwner('static_page', $page->id);
            if ($blocks->isNotEmpty()) {
                $liveEdit = false;
                $request = request();
                if ($request && $request->query('_lt')) {
                    $liveEdit = \App\Support\LiveEditToken::valid('destinations', $request->query('_lt'));
                }
                $renderer = app(\App\Services\BlockRenderer::class);
                $renderedBlocks = $renderer->renderBlocks($blocks, [
                    'static_page_id' => $page->id,
                    'orderedClusters' => $orderedClusters,
                    'stats' => $stats,
                    'featuredSpots' => $featuredSpots,
                    'searchIndex' => $searchIndex,
                    'live_edit' => $liveEdit,
                ]);
                return view('destinations.blocks', [
                    'page' => $page,
                    'renderedBlocks' => $renderedBlocks,
                    'liveEdit' => $liveEdit,
                ]);
            }
        }

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
        $spots = RgTouristSpot::query()
            ->where('status', 'published')
            ->whereNotNull('featured_order')
            ->whereNotNull('keyword_id')
            ->whereNotNull('media_id')
            ->with(['media', 'keyword'])
            ->orderBy('featured_order')
            ->get();

        // Bulk aggregate published reviews per keyword in one trip
        // so the featured-spots block can render star ratings + a
        // review count on each card. Keyed by keyword_id for O(1)
        // lookup during the map() below; spots whose keyword has no
        // reviews simply omit the rating fields and the card hides
        // the badge gracefully.
        $keywordIds = $spots->pluck('keyword_id')->filter()->unique()->values()->all();
        $ratings = [];
        if (!empty($keywordIds)) {
            $ratings = \DB::table('rg_destination_reviews')
                ->whereIn('keyword_id', $keywordIds)
                ->where('status', 'published')
                ->selectRaw('keyword_id, AVG(rating) as avg_rating, COUNT(*) as cnt')
                ->groupBy('keyword_id')
                ->get()
                ->keyBy('keyword_id')
                ->map(fn($r) => [
                    'rating' => round((float) $r->avg_rating, 1),
                    'review_count' => (int) $r->cnt,
                ])
                ->all();
        }

        return $spots
            ->map(function ($s) use ($ratings) {
                $row = [
                    'name'     => $s->name,
                    'location' => $s->location ?? '',
                    'region'   => $s->region_label ?? '',
                    'image'    => $s->media->path,
                    'slug'     => $s->keyword->slug,
                ];
                if (isset($ratings[$s->keyword_id])) {
                    $row['rating'] = $ratings[$s->keyword_id]['rating'];
                    $row['review_count'] = $ratings[$s->keyword_id]['review_count'];
                }
                return $row;
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

        // Nearby tourist-spot photos for the crossfading 3-column galleries
        // above and below the hero. Randomised per request so the mix varies.
        $spotImages = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots')) {
            $spotImages = RgTouristSpot::where('cluster_tag', $cluster)
                ->where('status', 'published')
                ->whereNotNull('media_id')
                ->with('media')
                ->inRandomOrder()
                ->limit(24)
                ->get()
                ->map(fn ($s) => ($s->media && $s->media->path)
                    ? [
                        'url' => '/storage/' . ltrim($s->media->path, '/'),
                        'name' => (string) $s->name,
                        'location' => (string) ($s->location ?? ''),
                        'desc' => \Illuminate\Support\Str::limit(trim(strip_tags((string) ($s->description ?? ''))), 110),
                        'link' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim((string) $s->name . ' ' . (string) ($s->location ?? '')) . ' Philippines'),
                    ]
                    : null)
                ->filter()
                ->values();
        }

        // A few representative photos per OTHER region for the homepage-style
        // "Explore other regions" cards (crossfading circle thumbnails).
        $otherImages = [];
        $otherSlugs = $others->pluck('slug')->all();
        if (\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots') && !empty($otherSlugs)) {
            $rows = RgTouristSpot::whereIn('cluster_tag', $otherSlugs)
                ->where('status', 'published')
                ->whereNotNull('media_id')
                ->with('media')
                ->inRandomOrder()
                ->limit(160)
                ->get(['id', 'cluster_tag', 'media_id']);
            foreach ($rows as $s) {
                if (!$s->media || !$s->media->path) {
                    continue;
                }
                if (count($otherImages[$s->cluster_tag] ?? []) >= 4) {
                    continue;
                }
                $otherImages[$s->cluster_tag][] = '/storage/' . ltrim($s->media->path, '/');
            }
        }

        // Representative photo per keyword page for the "What's In {region}" cards.
        $kwImages = [];
        $kwIds = $keywords->pluck('id')->all();
        if (\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots') && !empty($kwIds)) {
            $ksp = RgTouristSpot::whereIn('keyword_id', $kwIds)
                ->where('status', 'published')
                ->whereNotNull('media_id')
                ->with('media')
                ->get(['id', 'keyword_id', 'media_id']);
            foreach ($ksp as $s) {
                if ($s->media && $s->media->path && !isset($kwImages[$s->keyword_id])) {
                    $kwImages[$s->keyword_id] = '/storage/' . ltrim($s->media->path, '/');
                }
            }
        }

        // Block-driven render: a single shared `destination-cluster` template
        // page (in rg_static_pages) holds the block layout; it is rendered for
        // EVERY cluster with this cluster's data injected as context. Editing
        // the template updates all destination pages (they are intentionally
        // identical). Falls back to the hardcoded destinations.cluster view
        // when the template has no blocks. Mirrors the /destinations index.
        $tpl = \DB::table('rg_static_pages')->where('slug', 'destination-cluster')->where('is_published', 1)->first();
        if ($tpl) {
            $blocks = \App\Models\RgContentBlock::forOwner('static_page', $tpl->id);
            if ($blocks->isNotEmpty()) {
                $liveEdit = false;
                $request = request();
                if ($request && $request->query('_lt')) {
                    $liveEdit = \App\Support\LiveEditToken::valid('destination-cluster', $request->query('_lt'));
                }
                $ctx = $this->clusterContext($cluster, $meta, $keywords, $others, $spotImages, $otherImages, $kwImages);
                $ctx['live_edit'] = $liveEdit;
                $renderer = app(\App\Services\BlockRenderer::class);
                $renderedBlocks = $renderer->renderBlocks($blocks, $ctx);
                return view('destinations.cluster-blocks', [
                    'page' => $tpl,
                    'meta' => $meta,
                    'cluster' => $cluster,
                    'keywords' => $keywords,
                    'spotImages' => $spotImages,
                    'renderedBlocks' => $renderedBlocks,
                    'liveEdit' => $liveEdit,
                ]);
            }
        }

        return view('destinations.cluster', compact('meta', 'cluster', 'keywords', 'others', 'spotImages', 'otherImages', 'kwImages'));
    }

    /**
     * Full render context for one destination cluster, shared by the block
     * renderers (dest cluster blocks) so their output matches the current
     * destinations.cluster design. Ports the review/hashtag/gallery-column
     * logic that used to live inline in the Blade view.
     */
    private function clusterContext(string $cluster, array $meta, $keywords, $others, $spotImages, $otherImages, array $kwImages): array
    {
        $spots = collect($spotImages);
        $region = $meta['name'];

        // 3-column gallery split (round-robin, adjacent spots never share a tile)
        $cols = [[], [], []];
        foreach ($spots->take(12)->values() as $i => $sp) {
            $cols[$i % 3][] = $sp;
        }
        $heroImage = $spots->isNotEmpty() ? ($spots->first()['url'] ?? null) : null;

        // Placeholder reviews that reference REAL spots/locations in this region.
        $revSpots = $spots->unique('location')->values();
        if ($revSpots->count() < 3) {
            $revSpots = $spots->values();
        }
        $revNames = ['Marco Reyes', 'Bea Santos', 'Josh Lim', 'Andrea Cruz', 'Paolo Mendoza', 'Camille Tan'];
        $revCities = ['Makati City', 'Pasig City', 'Quezon City', 'Taguig City', 'Antipolo', 'Mandaluyong'];
        $revRatings = [5, 5, 4, 5, 5, 4];
        $revTitles = ['Worth the drive', 'A great home base', 'So much to explore', 'Better than expected', 'We will be back', 'Exactly as planned'];
        $revTpl = [
            'We based ourselves near {loc} and {spot} was the highlight of the trip. {region} is bigger than it looks, so pick an area and take your time.',
            'Brought the whole family to {loc}. {spot} alone made the trip, and there was still so much of {region} we did not get to.',
            '{spot} in {loc} completely surprised us. {region} has so many different corners that one weekend is never enough.',
            'Spent a few days around {loc} and loved every minute. {spot} was the kind of place you do not want to leave, and planning it here was easy.',
            '{loc} was the perfect slow escape. We visited {spot}, ate well, and drove home relaxed. {region} keeps pulling us back.',
            'Compared a few stays here and booked near {loc}. {spot} was a short trip away and worth it. This is how we plan {region} now.',
        ];
        $reviews = [];
        foreach ($revSpots->take(6)->values() as $i => $sp) {
            $reviews[] = [
                'name' => $revNames[$i] ?? 'Guest Traveler',
                'city' => $revCities[$i] ?? 'Philippines',
                'rating' => $revRatings[$i] ?? 5,
                'title' => $revTitles[$i] ?? 'A memorable trip',
                'text' => str_replace(['{spot}', '{loc}', '{region}'], [$sp['name'] ?? 'the area', ($sp['location'] ?: $region), $region], $revTpl[$i] ?? ''),
            ];
        }

        // Dynamic hashtags from this region's keyword pages.
        $hstop = ['in', 'on', 'at', 'of', 'the', 'a', 'an', 'to', 'for', 'and', 'or', 'near', 'with', 'your', 'best', 'top', 'resorts', 'resort', 'hotels', 'hotel', 'airbnb', 'airbnbs', 'stays', 'stay', 'places', 'place', 'beach', 'beaches', 'tourist', 'spot', 'spots', 'private', 'pool'];
        $hashtags = [];
        $hseen = [];
        foreach ($keywords as $hkw) {
            $words = preg_split('/[^a-z0-9]+/i', mb_strtolower($hkw->phrase), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $sig = array_values(array_filter($words, fn ($w) => !in_array($w, $hstop, true)));
            if (empty($sig)) {
                $sig = $words;
            }
            $sig = array_slice($sig, 0, 3);
            $tag = '';
            foreach ($sig as $w) {
                $tag .= ucfirst($w);
            }
            if ($tag === '') {
                continue;
            }
            $lk = mb_strtolower($tag);
            if (isset($hseen[$lk])) {
                continue;
            }
            $hseen[$lk] = true;
            $hashtags[] = ['tag' => $tag, 'url' => url($hkw->slug)];
        }

        return [
            'meta' => $meta,
            'cluster' => $cluster,
            'region' => $region,
            'keywords' => $keywords,
            'others' => $others,
            'spots' => $spots->all(),
            'galleryCols' => $cols,
            'heroImage' => $heroImage,
            'kwImages' => $kwImages,
            'otherImages' => $otherImages,
            'reviews' => $reviews,
            'hashtags' => $hashtags,
        ];
    }

    public static function clusterMetadata(): array
    {
        return [
            'batangas' => [
                'name' => 'Batangas',
                'tagline' => 'White sand one weekend, cool ridge air the next, and a dive site for every mood.',
                'intro_html' => '<p>Batangas spans the southwestern tip of Luzon and packs unusual variety into one province. The eastern coast at Laiya in San Juan delivers white-sand family beaches. The western coast at Calatagan, Nasugbu, and the Hamilo Coast development holds higher-end resorts and beach clubs. Mabini\'s Anilao corner is the dive capital of Luzon. Inland Lipa and Lobo offer quieter weekend retreats with cooler weather.</p><p>Travel time from BGC ranges from 2 hours (closest beach clubs at Nasugbu) to 3.5 hours (Laiya beachfront). The STAR Tollway and CALAX have noticeably shortened most drives. Pick a town by what you want: white-sand beach (Laiya, Calatagan), divers\' base (Anilao), beach club day trip (Nasugbu, Hamilo), or quiet inland retreat (Lipa).</p>',
                'meta_description' => 'Browse resorts in Batangas across Laiya, Calatagan, Anilao, Nasugbu, and Lipa. Beach, dive, and inland weekend options compared.',
            ],
            'cavite' => [
                'name' => 'Cavite',
                'tagline' => 'Sweater-cool ridge mornings up top, quiet coastal afternoons just down the road.',
                'intro_html' => '<p>Cavite stretches from the coastline at Ternate up to the highlands of Tagaytay, which means a resort in Cavite can be almost anything. Beach properties in Naic, pool resorts in Bacoor and Dasmariñas, coffee-country villas in Amadeo, and ridge-cooled hotels in Alfonso all fall under the same province.</p><p>Upper Cavite (Tagaytay, Alfonso, Amadeo, Indang) offers cool ridge weather and private villas. Mid Cavite (Silang, Dasmariñas, Imus) handles pool resort and function hall traffic. Lower Cavite (Bacoor) is urban and closest to NCR. Coastal Cavite (Naic, Ternate) opens to the West Philippine Sea with quieter beach stays.</p>',
                'meta_description' => 'Find resorts in Cavite from Tagaytay ridge villas to Dasmariñas pool venues and Naic coastal stays. Compare picks by town here.',
            ],
            'rizal' => [
                'name' => 'Rizal Province',
                'tagline' => 'Hilltop views, riverside quiet, and the city lights feeling a whole world away.',
                'intro_html' => '<p>Rizal province wraps around the eastern edge of Metro Manila and is the closest weekend escape for QC and Marikina residents. A resort in Rizal can mean a hilltop villa in Antipolo, a lakeside stay in Binangonan, a riverside resort in Tanay, or a hot-spring property in Taytay.</p><p>Travel times from QC range from 45 minutes (Antipolo, Taytay) to 2 hours (Tanay\'s remote nature retreats). Antipolo gives hilltop views and cool air. Tanay holds nature retreats and the Masungi Georeserve. Binangonan offers lakeside fish meals along Laguna de Bay. Rodriguez and San Mateo have river resorts.</p>',
                'meta_description' => 'Browse resorts in Rizal province across Antipolo, Tanay, Binangonan, and Rodriguez. The closest weekend escape from Metro Manila.',
            ],
            'laguna' => [
                'name' => 'Laguna',
                'tagline' => 'Warm spring water any time of year, seven quiet lakes, and waterfalls past the trees.',
                'intro_html' => '<p>Laguna sits roughly an hour south of Manila and is best known for hot spring resorts in Pansol and Calamba, and quieter lakeside stays around Pagsanjan and Lumban. The famous Pansol private pool rentals draw weekend bookings from across Metro Manila year-round, with geothermal water warm enough to enjoy at any time.</p><p>San Pablo\'s Seven Lakes country offers a quieter alternative with boutique inns and lakeside dining. Nagcarlan and Liliw sit at higher elevation with cooler weather. Travel time from QC is around 75 to 120 minutes via SLEX.</p>',
                'meta_description' => 'Find resorts in Laguna including Pansol hot springs, San Pablo lakes, and Nagcarlan upland stays. Compare picks by town.',
            ],
            'pampanga' => [
                'name' => 'Pampanga',
                'tagline' => 'Come hungry. Between the sisig and the cold-spring pools, no one leaves the food capital sad.',
                'intro_html' => '<p>Pampanga is the food capital of the Philippines and a growing destination for pool resorts, hot-spring stays, and Clark-zone hotels. Angeles City inside the Clark Freeport offers business-class hotels with full amenities and proximity to Clark International Airport. Arayat has cold-spring pool resorts at the foot of Mount Arayat.</p><p>Mexico, Magalang, and Lubao host family pool venues and event halls for big reunions. Travel time from QC ranges from 60 to 90 minutes via NLEX. The province is particularly known for its food including sisig, kare-kare, and the lantern-making heritage in San Fernando.</p>',
                'meta_description' => 'Resorts in Pampanga: Clark hotels, Angeles business stays, Arayat cold-spring pool resorts, and reunion-ready event venues.',
            ],
            'bulacan' => [
                'name' => 'Bulacan',
                'tagline' => 'The reunion province: close enough to be home by dinner, cool enough to stay the night.',
                'intro_html' => '<p>Bulacan sits just north of Quezon City and is the closest weekend resort destination from Manila with proper pool and function-hall properties. The province serves the weekend reunion crowd well, with most resorts clustered around Norzagaray, San Jose del Monte, Pandi, and Angat.</p><p>Travel time from QC ranges from 60 to 90 minutes via NLEX. Resorts here range from classic pool-and-grill barangay-style venues to modern resort-hotels with infinity pools and event halls for 200-pax weddings. The cooler upland properties in Norzagaray benefit from the elevation.</p>',
                'meta_description' => 'Find resorts in Bulacan with Norzagaray hillside villas and Pandi family pool venues. The closest reunion-friendly weekend destination from Manila.',
            ],
            'quezon' => [
                'name' => 'Quezon Province',
                'tagline' => 'Pacific surf on one side, heritage towns and Pahiyas color on the other.',
                'intro_html' => '<p>Quezon province stretches along the eastern coast of Luzon and packs a surprising variety of resorts into a single province. Beach properties in Pagbilao and Atimonan face the Pacific. Heritage-themed inns in Sariaya and Lucban offer cooler weather. Lucena City serves as the central business and ferry hub.</p><p>Travel time from QC ranges from 3 hours (Lucena) to 5 hours (Aurora-bordering towns). The Pahiyas Festival in Lucban every May 15 is the most-famous regional event. Quezon offers something for almost every traveller type, from surf trips on the Pacific coast to heritage walks in colonial towns.</p>',
                'meta_description' => 'Browse resorts in Quezon province. Pacific coast beaches in Pagbilao, heritage inns in Sariaya and Lucban, and Lucena city hotels.',
            ],
            'metro-manila' => [
                'name' => 'Metro Manila',
                'tagline' => 'A rooftop pool, a bay-side sunset, and a proper staycation without ever leaving the city.',
                'intro_html' => '<p>Metro Manila is the urban core of the country and not traditionally a resort destination, but several hotel-resorts and pool venues inside the city work for short same-day stays. Manila Bay hotels along Roxas Boulevard offer sunset views and pool decks. BGC in Taguig has the densest cluster of hotel-resorts with full amenities.</p><p>For Airbnb stays, BGC, Makati, Ortigas, and Poblacion are the main neighbourhoods. For pool day venues, Quezon City and the southern metro towns have growing options. Travel within the metro varies hugely with traffic.</p>',
                'meta_description' => 'Resorts and pool venues in Metro Manila. BGC hotel resorts, Manila Bay hotels, and Airbnb neighbourhoods compared.',
            ],
            'north-luzon' => [
                'name' => 'North Luzon',
                'tagline' => 'Pine-cool mornings, warm afternoon sand, and a northern coastline that never seems to end.',
                'intro_html' => '<p>North Luzon is where the drive becomes part of the adventure. One morning the air is cool and sharp with pine as you wind through the highlands, and by afternoon you are barefoot on warm sand, watching the sun melt into the West Philippine Sea. This is the north of lazy surf-town mornings in La Union, of Subic\'s easy family weekends, of Bolinao\'s bright endless shoreline, and of quiet inland provinces where the pace slows to the rhythm of the fields.</p><p>Wake to the smell of strong local coffee and freshly grilled bangus. Paddle out at San Juan where first-timers and old salts share the same friendly break, then go island-hopping across the scattered green Hundred Islands off Alaminos. Trade the coast for the strange grey moonscape of the Pinatubo crater, or lose an afternoon in a sleepy heritage town. From Pangasinan and Zambales to Bataan, Tarlac, and the Ilocos heartland, North Luzon keeps handing you one more reason to stay another day.</p>',
                'meta_description' => 'Resorts in North Luzon: Subic beaches, La Union surf, Bolinao white sand, Hundred Islands, and Pinatubo trek base. Compare here.',
            ],
            'bicol' => [
                'name' => 'Bicol Region',
                'tagline' => 'Mayon on the horizon, gentle giants in the water, and chili in nearly everything.',
                'intro_html' => '<p>The Bicol region covers Albay, Camarines Sur, Sorsogon, and surrounding provinces. The headline attraction is Mount Mayon, the perfectly-cone-shaped active volcano in Albay. Whale shark interactions in Donsol (November to May), wakeboarding at CWC in Pili, and the historic Pasalubong stops in Naga round out the typical itinerary.</p><p>Travel time from QC takes 8 to 10 hours by road or 60 to 70 minutes by direct flight to Legazpi or Naga. Bicol is also famous for its spicy cuisine including Bicol Express and laing.</p>',
                'meta_description' => 'Resorts in Bicol region: Albay Mayon views, Donsol whale sharks, Naga pilgrimage stays, and Sorsogon coastal options.',
            ],
            'visayas' => [
                'name' => 'Visayas',
                'tagline' => 'The country\'s postcard beaches, easy island hops, and heritage streets in between.',
                'intro_html' => '<p>The Visayas covers Cebu, Bohol, Negros, Iloilo, Guimaras, Siquijor, and the surrounding islands. The region holds some of the country\'s most-visited beach destinations including Mactan, Panglao, and the famous Boracay (Aklan). Cebu City is the largest urban center and gateway for most flights.</p><p>Heritage stays in Iloilo and Silay offer something different from the beach formula. Diving in Dauin, Apo Island, and the Camotes draws underwater enthusiasts year-round. Direct flights from Manila reach most Visayas destinations in 75 to 90 minutes.</p>',
                'meta_description' => 'Resorts in Visayas: Cebu, Mactan, Bohol\'s Panglao, Negros, Iloilo, Guimaras, Siquijor, and Boracay. Compare beach destinations.',
            ],
            'mindanao' => [
                'name' => 'Mindanao',
                'tagline' => 'Warm all year and wildly underrated, from Samal\'s sand to Davao\'s easy city pulse.',
                'intro_html' => '<p>Mindanao covers the southern third of the country including Davao, General Santos, Zamboanga, and surrounding regions. The region is famous for its evenly warm weather (less affected by typhoons than Luzon), the tuna capital at General Santos, the beaches at Samal Island and Glan, and Mount Apo in Davao province.</p><p>Davao City is the largest urban center and is often praised for its safety and food scene. Travel from Manila is by direct flight, with most flights taking 90 minutes. Many destinations remain underrated relative to their actual beach and nature quality.</p>',
                'meta_description' => 'Resorts in Mindanao: Davao city hotels, Samal Island beaches, GenSan, Glan white sand, and Zamboanga\'s Pink Beach.',
            ],
            'palawan' => [
                'name' => 'Palawan & Mindoro',
                'tagline' => 'Hidden lagoons, glass-clear water, and the islands that top every dream list.',
                'intro_html' => '<p>Palawan is the long island province on the western edge of the country, famous for the limestone islands of El Nido, the shipwrecks and lakes of Coron, and the underground river in Puerto Princesa. The province dominates "best islands in the world" lists year after year. Resort tiers run from backpacker hostels to private-island five-stars.</p><p>Direct AirSWIFT flights reach El Nido from Manila in 90 minutes. Otherwise fly to Puerto Princesa and take a 5-hour van transfer. We\'ve grouped Puerto Galera here as a similar-character beach destination just across the Verde Passage.</p>',
                'meta_description' => 'Resorts in Palawan including El Nido, Puerto Galera, and the western islands. Backpacker stays to private-island five-stars.',
            ],
        ];
    }
}
