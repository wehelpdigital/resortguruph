<?php

namespace App\Services;

use App\Http\Controllers\DestinationsController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Data for the hand-designed destination pages
 * (destinations/showcase.blade.php and destinations/region.blade.php).
 * Content still comes from the DB (guides, spots, reviews, fiestas); the
 * travel facts per region are curated here. rg_settings.destinations_layout
 * = blocks switches both pages back to the block builder.
 */
class DestinationShowcase
{
    /** Island-group sections on the index, in order. */
    public const GROUPS = [
        'weekend' => ['Weekend from Manila', 'Beaches, hot springs and cool ridges a few hours by car.'],
        'roadtrip' => ['Luzon road trips', 'Surf towns, heritage streets and a perfect-cone volcano.'],
        'islands' => ['Fly to the islands', 'The lagoons, white beaches and reefs on every list.'],
        'mindanao' => ['Mindanao', 'Big-wave surf, quiet islands and the country\'s highest peak.'],
    ];

    /** Curated travel facts per region (cluster slug). */
    public const FACTS = [
        'palawan' => ['group' => 'islands', 'from' => '1 to 1.5 h flight', 'best' => 'November to May', 'stay' => '5 to 7 days', 'getting' => 'Fly to El Nido, Puerto Princesa or Coron. For Puerto Galera, take the ferry from Batangas port.', 'known' => ['Island hopping', 'Hidden lagoons', 'Shipwreck dives', 'Underground River']],
        'visayas' => ['group' => 'islands', 'from' => '1 to 1.5 h flight', 'best' => 'December to May', 'stay' => '5 to 10 days', 'getting' => 'Fly to Cebu, Iloilo, Bohol-Panglao or Caticlan for Boracay, then hop between islands by fast ferry.', 'known' => ['White sand beaches', 'Chocolate Hills', 'Lechon', 'Old stone churches']],
        'north-luzon' => ['group' => 'roadtrip', 'from' => '4 to 8 h drive', 'best' => 'November to April', 'stay' => '3 to 5 days', 'getting' => 'Drive or ride a bus up NLEX, SCTEX and TPLEX. Flights to Laoag save time for Ilocos Norte.', 'known' => ['Surf in La Union', 'Heritage Vigan', 'Hundred Islands', 'Sand dunes']],
        'bicol' => ['group' => 'roadtrip', 'from' => '1 h flight', 'best' => 'February to May', 'stay' => '3 to 5 days', 'getting' => 'Fly to Daraga for Legazpi and Mayon, or to Naga. Overnight buses take about 10 hours.', 'known' => ['Mayon Volcano', 'Caramoan islands', 'Bicol Express', 'Whale sharks in Donsol']],
        'mindanao' => ['group' => 'mindanao', 'from' => 'About 2 h flight', 'best' => 'All year, mostly outside the typhoon belt', 'stay' => '5 to 7 days', 'getting' => 'Fly to Davao, Cagayan de Oro, General Santos or Zamboanga, then go by boat or road to the islands.', 'known' => ['Samal beaches', 'Mount Apo', 'Durian and fresh tuna', 'Quiet island coves']],
        'batangas' => ['group' => 'weekend', 'from' => '2 to 3.5 h drive', 'best' => 'November to May', 'stay' => '2 to 3 days', 'getting' => 'Drive down SLEX and the STAR Tollway. Buses leave Manila for Batangas City, Nasugbu and Lipa.', 'known' => ['Anilao diving', 'Laiya white sand', 'Beach clubs', 'Taal heritage town']],
        'cavite' => ['group' => 'weekend', 'from' => '1.5 to 2.5 h drive', 'best' => 'December to February for cool air', 'stay' => '1 to 2 days', 'getting' => 'Drive via CAVITEX, CALAX or SLEX to Tagaytay. Buses and vans run from Manila all day.', 'known' => ['Taal Volcano views', 'Bulalo', 'Cool ridge air', 'Coffee farms']],
        'rizal' => ['group' => 'weekend', 'from' => '1 to 2 h drive', 'best' => 'November to May', 'stay' => 'Day trip to 2 days', 'getting' => 'Drive east via Marcos Highway or Ortigas Avenue Extension. Jeepneys run to Antipolo from Cubao.', 'known' => ['Sea of clouds in Tanay', 'Antipolo Cathedral', 'Waterfalls', 'Masungi Georeserve']],
        'laguna' => ['group' => 'weekend', 'from' => '1.5 to 3 h drive', 'best' => 'November to May', 'stay' => '1 to 2 days', 'getting' => 'Take SLEX to Calamba for Pansol, or continue to San Pablo and Pagsanjan. Buses run from Buendia and Cubao.', 'known' => ['Hot spring pools', 'Pagsanjan Falls', 'Seven lakes of San Pablo', 'Buko pie']],
        'metro-manila' => ['group' => 'weekend', 'from' => 'You are here', 'best' => 'December to April', 'stay' => '1 to 3 days', 'getting' => 'Fly into NAIA. Use ride hailing apps, the MRT and LRT lines, or the Pasig River ferry.', 'known' => ['Intramuros', 'Food halls and bars', 'Museums', 'Manila Bay sunsets']],
        'pampanga' => ['group' => 'weekend', 'from' => '1.5 to 2 h drive', 'best' => 'November to May, plus December for lanterns', 'stay' => '1 to 2 days', 'getting' => 'Drive up NLEX to San Fernando or Angeles, or fly straight into Clark.', 'known' => ['Sisig and kapampangan food', 'Giant lanterns', 'Mount Pinatubo trek', 'Clark']],
        'quezon' => ['group' => 'weekend', 'from' => '2.5 to 4 h drive', 'best' => 'March to May, Pahiyas in mid May', 'stay' => '2 to 3 days', 'getting' => 'Drive SLEX to Lucena, then on to Lucban, Tayabas or Padre Burgos. Buses leave from Buendia and Cubao.', 'known' => ['Pahiyas Festival', 'Pancit habhab', 'Heritage houses', 'Borawan islands']],
        'bulacan' => ['group' => 'weekend', 'from' => '1 to 1.5 h drive', 'best' => 'November to May', 'stay' => 'Day trip', 'getting' => 'Drive up NLEX to Malolos, Pandi or San Jose del Monte. Buses and the PNR line run north from Manila.', 'known' => ['Barasoain Church', 'Private pool resorts', 'Biak-na-Bato', 'Pastillas and kakanin']],
    ];

    /** "Find your kind of trip" panels: [title, blurb, photo, [label => keyword match]]. */
    private const STYLES = [
        'beach' => ['Beaches and islands', 'Powdery sand, clear lagoons and island-hopping boats.', 'spots/el-nido-small-lagoon-tour-a.jpg',
            ['El Nido' => 'el nido', 'Boracay' => 'boracay', 'Panglao, Bohol' => 'panglao', 'Puerto Galera' => 'puerto galera', 'Siquijor' => 'siquijor', 'Samal Island' => 'samal']],
        'cool' => ['Cool mountain air', 'Pine trees, misty mornings and hot soup weather.', 'food-locations/baguio.jpg',
            ['Tagaytay' => 'tagaytay', 'Baguio' => 'baguio', 'Antipolo' => 'antipolo', 'Tanay' => 'tanay', 'Alfonso' => 'alfonso', 'Lipa' => 'lipa']],
        'heritage' => ['Heritage towns', 'Cobblestone streets, ancestral houses and old churches.', 'spots/vigan-calle-crisologo.jpg',
            ['Vigan' => 'vigan', 'Manila' => 'manila', 'Iloilo' => 'iloilo', 'Cebu City' => 'cebu city', 'Taal, Batangas' => 'taal', 'Malolos, Bulacan' => 'bulacan']],
        'dive' => ['Diving and snorkeling', 'Turtles, coral walls and some of the best reefs in Asia.', 'spots/dauin-apo-island-marine-sanctuary.jpg',
            ['Anilao, Mabini' => 'mabini', 'Dauin' => 'dauin', 'Dumaguete' => 'dumaguete', 'Puerto Galera' => 'puerto galera', 'Mactan, Lapu-Lapu' => 'lapu', 'Moalboal' => 'moalboal']],
        'nature' => ['Waterfalls, volcanoes and parks', 'Jungle falls, Mayon, Mount Apo and sea-of-clouds ridges.', 'spots/siquijor-cambugahay-falls.jpg',
            ['Legazpi and Mayon' => 'albay', 'Tanay' => 'tanay', 'Hundred Islands' => 'hundred islands', 'Kidapawan and Mount Apo' => 'kidapawan', 'Pagsanjan' => 'pagsanjan', 'Bataan' => 'bataan']],
        'weekend' => ['Easy weekends from Manila', 'Pools, beaches and food trips without a flight.', 'destinations/laiya-1.jpg',
            ['Laiya, Batangas' => 'laiya', 'Nasugbu' => 'nasugbu', 'Pansol hot springs' => 'pansol', 'Tagaytay' => 'tagaytay', 'Pampanga' => 'pampanga', 'Subic' => 'subic']],
    ];

    /** Fiesta region keys per destination cluster (rg_fiestas.region_cluster) or a province filter. */
    private const FIESTA_SCOPE = [
        'palawan' => ['region', 'palawan'], 'visayas' => ['region', 'visayas'], 'mindanao' => ['region', 'mindanao'],
        'bicol' => ['region', 'bicol'], 'north-luzon' => ['region', 'north-luzon'], 'metro-manila' => ['region', 'metro-manila'],
        'batangas' => ['province', 'Batangas'], 'cavite' => ['province', 'Cavite'], 'laguna' => ['province', 'Laguna'],
        'quezon' => ['province', 'Quezon'], 'rizal' => ['province', 'Rizal'], 'pampanga' => ['province', 'Pampanga'], 'bulacan' => ['province', 'Bulacan'],
    ];

    public function index(): array
    {
        return Cache::remember('dest_showcase_v2', 600, function () {
            $meta = DestinationsController::clusterMetadata();
            $kw = DB::table('rg_keywords as k')->join('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')
                ->where('p.is_published', 1)->where('k.category', 'resort')
                ->orderByDesc('k.search_volume_monthly')
                ->get(['k.id', 'k.phrase', 'k.slug', 'k.cluster_tag', 'k.search_volume_monthly']);
            $byCluster = $kw->groupBy('cluster_tag');

            $regions = [];
            foreach (self::FACTS as $slug => $f) {
                if (!isset($meta[$slug])) continue;
                $list = $byCluster->get($slug, collect());
                if ($list->isEmpty()) continue;
                $regions[] = [
                    'slug' => $slug,
                    'name' => $meta[$slug]['name'],
                    'tagline' => $meta[$slug]['tagline'] ?? '',
                    'url' => route('destinations.cluster', $slug),
                    'image' => $this->landmark($slug),
                    'count' => $list->count(),
                    'guides' => $list->take(4)->map(fn ($k) => ['label' => self::title($k->phrase), 'url' => url($k->slug)])->all(),
                    'facts' => $f,
                ];
            }

            $home = app(HomeShowcase::class)->data();

            return [
                'stats' => [
                    'guides' => $kw->count(),
                    'regions' => count($regions),
                    'spots' => Schema::hasTable('rg_tourist_spots') ? DB::table('rg_tourist_spots')->where('status', 'published')->count() : 0,
                    'fiestas' => $home['stats']['fiestas'] ?? 0,
                ],
                'regions' => $regions,
                'classics' => $home['trending'] ?? [],
                'styles' => $this->styles($kw),
                // "Worth the detour" leaves out the places whose photo is already a classic above
                'spots' => $this->featuredSpots(8, array_column($home['trending'] ?? [], 'image')),
                'months' => $this->months(),
                'reviews' => array_slice($home['reviews'] ?? [], 0, 3),
                'az' => collect($regions)->map(fn ($r) => [
                    'name' => $r['name'], 'url' => $r['url'],
                    'guides' => $byCluster->get($r['slug'], collect())->sortBy('phrase')->map(fn ($k) => ['label' => self::title($k->phrase), 'url' => url($k->slug)])->values()->all(),
                ])->all(),
            ];
        });
    }

    /** Everything the region page needs beyond the controller's keyword list. */
    public function region(string $slug, $keywords): array
    {
        return Cache::remember('dest_region_v6_' . $slug, 600, function () use ($slug, $keywords) {
            $meta = DestinationsController::clusterMetadata()[$slug];
            $ids = $keywords->pluck('id')->all();

            $spots = [];
            if (Schema::hasTable('rg_tourist_spots')) {
                $shared = DB::table('rg_tourist_spots')->whereNotNull('media_id')->groupBy('media_id')->select('media_id', DB::raw('count(*) c'))->pluck('c', 'media_id');
                $spots = DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')
                    ->where('s.status', 'published')
                    ->where(fn ($q) => $q->where('s.cluster_tag', $slug)->orWhereIn('s.keyword_id', $ids ?: [0]))
                    ->orderByRaw('s.featured_order is null')->orderBy('s.featured_order')->orderBy('s.id')
                    ->limit(60)->get(['s.id', 's.name', 's.location', 's.description', 's.keyword_id', 's.media_id', 'm.path'])
                    // photos shared by several spots are placeholders (a town map, a generic view): leave those spots out
                    ->filter(fn ($s) => ($shared[$s->media_id] ?? 1) < 2 && !str_contains($s->path, 'generic') && is_file(storage_path('app/public/' . ltrim($s->path, '/'))))
                    ->unique('name')->values()
                    ->map(fn ($s) => [
                        'name' => $s->name,
                        'location' => (string) $s->location,
                        'desc' => Str::limit(trim(strip_tags((string) $s->description)), 140),
                        'image' => '/storage/' . ltrim($s->path, '/'),
                        'keyword_id' => $s->keyword_id,
                        'map' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim($s->name . ' ' . $s->location) . ' Philippines'),
                    ])->all();
            }

            // one photo per guide, each shown once on the page: the guide's own hero slides first
            // (several guides share a town photo, so take the first slide nobody used yet), then a
            // spot photo the spot cards do not show, then the next unused spot photo
            $byKw = $this->guideHeroImages($ids);
            $spotImgs = array_flip(array_column($spots, 'image'));
            $pool = array_values(array_unique(array_column($spots, 'image')));
            $used = [];
            $guides = [];
            $townPhotos = $this->townPhotos();
            foreach ($keywords->values() as $i => $k) {
                $img = null;
                foreach ($byKw[$k->id] ?? [] as $cand) if (!isset($used[$cand]) && !isset($spotImgs[$cand])) { $img = $cand; break; }
                // then a town photo of the guide's own place, which the spot cards do not show
                if (!$img) foreach ($this->placeTokens($k->phrase) as $tok) { foreach ($townPhotos[$tok] ?? [] as $cand) if (!isset($used[$cand]) && !isset($spotImgs[$cand])) { $img = $cand; break 2; } }
                // then another guide's photo of the region that no card shows
                if (!$img) foreach ($byKw as $imgs) { foreach ($imgs as $cand) if (!isset($used[$cand]) && !isset($spotImgs[$cand]) && \App\Support\GuideHtml::usable($cand)) { $img = $cand; break 2; } }
                if (!$img) foreach ($byKw[$k->id] ?? [] as $cand) if (!isset($used[$cand])) { $img = $cand; break; }
                if (!$img) foreach ($pool as $cand) if (!isset($used[$cand])) { $img = $cand; break; }
                $img = $img ?? ($pool ? $pool[$i % count($pool)] : $this->landmark($slug));
                $used[$img] = true;
                $guides[] = ['label' => self::title($k->phrase), 'url' => url($k->slug), 'image' => $img, 'popular' => $i < 3];
            }

            $reviews = [];
            if ($ids && Schema::hasTable('rg_destination_reviews')) {
                $seen = [];
                foreach (DB::table('rg_destination_reviews as r')->join('rg_keywords as k', 'k.id', '=', 'r.keyword_id')
                    ->whereIn('r.keyword_id', $ids)->where('r.status', 'published')->where('r.rating', '>=', 4)
                    ->whereRaw('char_length(r.review_text) between 90 and 320')
                    ->orderByDesc('r.is_featured')->orderByDesc('r.review_date')->limit(40)
                    ->get(['r.reviewer_name', 'r.reviewer_location', 'r.reviewer_avatar', 'r.rating', 'r.review_text', 'k.phrase', 'k.slug']) as $r) {
                    $sig = implode(' ', array_slice(preg_split('/\s+/', strtolower(preg_replace('/[^a-z ]/i', '', $r->review_text))), 0, 4));
                    if (isset($seen[$r->reviewer_name]) || isset($seen[$sig])) continue;
                    $seen[$r->reviewer_name] = $seen[$sig] = true;
                    $av = (string) $r->reviewer_avatar;
                    $reviews[] = [
                        'name' => $r->reviewer_name, 'from' => $r->reviewer_location, 'rating' => (int) $r->rating, 'text' => $r->review_text,
                        'about' => self::title($r->phrase), 'url' => url($r->slug),
                        'avatar' => $av === '' ? \App\Support\Avatar::initials($r->reviewer_name) : (preg_match('#^https?://#', $av) ? $av : '/storage/' . ltrim($av, '/')),
                    ];
                    if (count($reviews) >= 3) break;
                }
            }

            $others = [];
            $counts = DB::table('rg_keywords as k')->join('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')
                ->where('p.is_published', 1)->where('k.category', 'resort')->groupBy('k.cluster_tag')
                ->select('k.cluster_tag', DB::raw('count(*) c'))->pluck('c', 'cluster_tag');
            foreach (self::FACTS as $s => $f) {
                if ($s === $slug || empty($counts[$s]) || !isset(DestinationsController::clusterMetadata()[$s])) continue;
                $others[] = ['name' => DestinationsController::clusterMetadata()[$s]['name'], 'url' => route('destinations.cluster', $s), 'image' => $this->landmark($s), 'count' => (int) $counts[$s], 'from' => $f['from']];
            }

            // the "What X is like" collage: guide photos no card on the page uses
            $collage = [];
            foreach ($byKw as $imgs) foreach ($imgs as $cand) {
                if (count($collage) >= 3) break 2;
                if (isset($used[$cand]) || isset($spotImgs[$cand]) || in_array($cand, $collage, true) || !\App\Support\GuideHtml::usable($cand)) continue;
                $id = \App\Support\GuideHtml::photoId($cand);
                foreach ($collage as $c) if (\App\Support\GuideHtml::photoId($c) === $id) continue 2;
                $collage[] = $cand;
            }

            return [
                'collage' => $collage,
                'facts' => self::FACTS[$slug] ?? null,
                'hero' => array_values(array_filter(array_merge([$this->landmark($slug)], array_column(array_slice($spots, 0, 4), 'image')))),
                'guides' => $guides,
                'spots' => $spots,
                'fiestas' => $this->regionFiestas($slug),
                'reviews' => $reviews,
                'others' => $others,
                'intro' => $meta['intro_html'] ?? '',
            ];
        });
    }

    /** keyword_id => the hero_slider photos of that keyword's guide page, in order. */
    private function guideHeroImages(array $keywordIds): array
    {
        if (!$keywordIds) return [];
        $pages = DB::table('rg_seo_pages')->whereIn('keyword_id', $keywordIds)->pluck('keyword_id', 'id');
        $out = [];
        foreach (DB::table('rg_content_blocks')->where('owner_type', 'seo_page')->whereIn('owner_id', $pages->keys()->all() ?: [0])
            ->where('block_type', 'hero_slider')->orderBy('sort_order')->get(['owner_id', 'payload_json']) as $b) {
            $kid = $pages[$b->owner_id] ?? null;
            if (!$kid || isset($out[$kid])) continue;
            foreach ((json_decode($b->payload_json, true)['images'] ?? []) as $img) {
                $src = preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', trim((string) ($img['src'] ?? '')));
                if ($src === '') continue;
                $src = str_starts_with($src, '/') || preg_match('#^https?://#', $src) ? $src : '/' . ltrim($src, '/');
                $local = str_starts_with($src, '/storage/') ? storage_path('app/public/' . substr($src, 9)) : null;
                if ($local && !is_file($local)) continue;
                $out[$kid][] = $src;
            }
        }
        return $out;
    }

    /** word => town photos (rg-media/destinations/{town}-N.jpg) whose file name has that word. */
    private function townPhotos(): array
    {
        static $map = null;
        if ($map !== null) return $map;
        $map = [];
        foreach (glob(storage_path('app/public/rg-media/destinations/*.jpg')) ?: [] as $f) {
            $src = '/storage/rg-media/destinations/' . basename($f);
            if (!\App\Support\GuideHtml::usable($src)) continue;
            foreach (explode('-', preg_replace('~-\d+\.jpg$~', '', basename($f))) as $t) if (strlen($t) > 3 && !in_array($t, ['city', 'province', 'general', 'beach', 'island', 'islands'], true)) $map[$t][] = $src;
        }
        return $map;
    }

    /** Distinct place words of a keyword ("resort in lobo batangas" => lobo, batangas). */
    private function placeTokens(string $phrase): array
    {
        $place = preg_match('~\b(?:in|at|near|of)\s+(.+)$~i', $phrase, $m) ? $m[1] : $phrase;
        return array_values(array_filter(preg_split('~[^a-z]+~', strtolower($place)) ?: [], fn ($t) => strlen($t) > 3 && !in_array($t, ['city', 'philippines', 'beach', 'resort', 'with', 'pool', 'private'], true)));
    }

    private function styles($kw): array
    {
        $out = [];
        foreach (self::STYLES as $key => [$title, $blurb, $img, $items]) {
            $links = [];
            foreach ($items as $label => $match) {
                $k = $kw->first(fn ($k) => str_contains(strtolower($k->phrase), $match));
                if ($k) $links[] = ['label' => $label, 'url' => url($k->slug)];
            }
            if (count($links) >= 3) $out[] = ['key' => $key, 'title' => $title, 'blurb' => $blurb, 'image' => '/storage/rg-media/' . $img, 'links' => $links];
        }
        return $out;
    }

    private function featuredSpots(int $limit, array $skipPhotos = []): array
    {
        $skip = [];
        foreach ($skipPhotos as $src) $skip[\App\Support\GuideHtml::photoId((string) $src)] = true;
        if (!Schema::hasTable('rg_tourist_spots')) return [];
        $rows = DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->join('rg_keywords as k', 'k.id', '=', 's.keyword_id')
            ->where('s.status', 'published')->whereNotNull('s.featured_order')
            ->orderBy('s.featured_order')->limit($limit * 2)
            ->get(['s.name', 's.location', 'm.path', 'k.slug', 'k.id as kid']);
        $ratings = DB::table('rg_destination_reviews')->whereIn('keyword_id', $rows->pluck('kid')->unique()->all() ?: [0])->where('status', 'published')
            ->groupBy('keyword_id')->selectRaw('keyword_id, round(avg(rating), 1) r, count(*) c')->get()->keyBy('keyword_id');
        return $rows->filter(fn ($s) => is_file(storage_path('app/public/' . ltrim($s->path, '/'))) && !isset($skip[\App\Support\GuideHtml::photoId('/storage/' . ltrim($s->path, '/'))]))->take($limit)
            ->map(fn ($s) => ['name' => $s->name, 'location' => (string) $s->location, 'image' => '/storage/' . ltrim($s->path, '/'), 'url' => url($s->slug),
                'rating' => isset($ratings[$s->kid]) ? (float) $ratings[$s->kid]->r : null])->values()->all();
    }

    private function months(): array
    {
        $season = [
            1 => ['cool', 'Cool and dry'], 2 => ['cool', 'Cool and dry'], 3 => ['hot', 'Hot and dry'], 4 => ['hot', 'Hot and dry'], 5 => ['hot', 'Hot, first rains'],
            6 => ['wet', 'Rainy season'], 7 => ['storm', 'Wettest months'], 8 => ['storm', 'Wettest months'], 9 => ['storm', 'Typhoon peak'],
            10 => ['wet', 'Rains easing'], 11 => ['cool', 'Dry season starts'], 12 => ['cool', 'Cool and dry'],
        ];
        $counts = Schema::hasTable('rg_fiestas') ? DB::table('rg_fiestas')->where('is_published', 1)->groupBy('month')->select('month', DB::raw('count(*) c'))->pluck('c', 'month') : collect();
        $out = [];
        foreach ($season as $m => [$key, $label]) {
            $out[] = ['n' => $m, 'short' => date('M', mktime(0, 0, 0, $m, 1)), 'name' => date('F', mktime(0, 0, 0, $m, 1)), 'season' => $key, 'label' => $label, 'fiestas' => (int) ($counts[$m] ?? 0)];
        }
        return $out;
    }

    private function regionFiestas(string $slug): array
    {
        if (!Schema::hasTable('rg_fiestas') || !isset(self::FIESTA_SCOPE[$slug])) return [];
        [$by, $val] = self::FIESTA_SCOPE[$slug];
        $q = DB::table('rg_fiestas')->where('is_published', 1)->whereNotNull('cover_image_path');
        $by === 'region' ? $q->where('region_cluster', $val) : $q->where('province', 'like', '%' . $val . '%');
        return $q->orderBy('month')->limit(12)->get(['slug', 'name', 'city_or_town', 'province', 'month', 'date_label', 'cover_image_path'])
            ->filter(fn ($f) => \App\Support\GuideHtml::usable('/storage/' . ltrim($f->cover_image_path, '/')))->take(8)->values()
            ->map(fn ($f) => [
                'name' => preg_replace('/\s*\(.*\)$/', '', $f->name), 'url' => route('fiestas.show', $f->slug),
                'image' => '/storage/' . ltrim($f->cover_image_path, '/'),
                'when' => $f->date_label ?: date('F', mktime(0, 0, 0, max(1, (int) $f->month), 1)),
                'place' => trim(($f->city_or_town ?: '') . ($f->province ? ', ' . $f->province : ''), ', '),
            ])->all();
    }

    private function landmark(string $slug): string
    {
        return is_file(storage_path('app/public/rg-media/landmarks/' . $slug . '.jpg')) ? '/storage/rg-media/landmarks/' . $slug . '.jpg' : '/storage/rg-media/landmarks/other.jpg';
    }

    /** "resort in el nido palawan" -> "Resort in El Nido Palawan" */
    public static function title(string $s): string
    {
        $small = ['in', 'of', 'the', 'and', 'with', 'near', 'for', 'a', 'to', 'at', 'on'];
        $w = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($w as $i => $x) $w[$i] = ($i === 0 || !in_array($x, $small, true)) ? mb_convert_case($x, MB_CASE_TITLE, 'UTF-8') : $x;
        return preg_replace_callback('/\b(Sm|Bgc|Moa|Qc|Ncr)\b/', fn ($m) => strtoupper($m[1]), implode(' ', $w));
    }
}
