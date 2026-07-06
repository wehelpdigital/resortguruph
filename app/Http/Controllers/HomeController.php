<?php

namespace App\Http\Controllers;

use App\Models\RgBlogPost;
use App\Models\RgKeyword;
use App\Models\RgResort;
use App\Models\RgSeoPage;
use App\Services\SchemaGenerator;

class HomeController extends Controller
{
    public function index(SchemaGenerator $schema)
    {
        $jsonld = $schema->emit($schema->website()) . $schema->emit($schema->organization());
        $featuredKeywords = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->limit(12)
            ->get();

        $featuredResorts = RgResort::where('status', 'published')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $latestPosts = RgBlogPost::where('status', 'published')
            ->inRandomOrder()
            ->limit(9)
            ->get();

        $stats = [
            'pages' => RgSeoPage::where('is_published', true)->count(),
            'resorts' => RgResort::where('status', 'published')->count(),
        ];

        $clusterMeta = DestinationsController::clusterMetadata();

        // Dynamic per-region photo pools for the region-grid circle thumbnails.
        // Pulled from the CURRENT published tourist-spot media in each cluster,
        // so the imagery reflects whatever photos the region's pages currently
        // use and refreshes as content changes (cached 10 min).
        $regionImgs = \Illuminate\Support\Facades\Cache::remember('home_region_imgs_v1', 600, function () {
            $map = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots')) {
                $spots = \App\Models\RgTouristSpot::query()
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

        $regions = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->get()
            ->groupBy('cluster_tag')
            ->map(function ($kws, $slug) use ($clusterMeta, $regionImgs) {
                if (!isset($clusterMeta[$slug])) return null;
                $imgs = array_values(array_unique($regionImgs[$slug] ?? []));
                shuffle($imgs);
                return [
                    'slug' => $slug,
                    'name' => $clusterMeta[$slug]['name'],
                    'tagline' => $clusterMeta[$slug]['tagline'],
                    'count' => $kws->count(),
                    'total_volume' => $kws->sum('search_volume_monthly'),
                    'images' => array_slice($imgs, 0, 4),
                ];
            })
            ->filter()
            ->sortByDesc('total_volume')
            ->values();

        // Per-keyword photo pools for the "Popular destinations" circles.
        // Prefer a page's OWN tourist-spot / restaurant photos, then fill from
        // the region pool so every card has several images to crossfade.
        $kwDirect = \Illuminate\Support\Facades\Cache::remember('home_kw_direct_imgs_v1', 600, function () {
            $map = [];
            if (\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots')) {
                $spots = \App\Models\RgTouristSpot::query()
                    ->where('status', 'published')
                    ->whereNotNull('media_id')
                    ->whereNotNull('keyword_id')
                    ->with('media')
                    ->get(['id', 'keyword_id', 'media_id']);
                foreach ($spots as $s) {
                    if ($s->media && $s->media->path) $map[$s->keyword_id][] = '/storage/' . ltrim($s->media->path, '/');
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('rg_restaurant_listings') && \Illuminate\Support\Facades\Schema::hasTable('rg_restaurants')) {
                $rows = \DB::table('rg_restaurant_listings')
                    ->join('rg_restaurants', 'rg_restaurant_listings.restaurant_id', '=', 'rg_restaurants.id')
                    ->where('rg_restaurants.status', 'published')
                    ->whereNotNull('rg_restaurants.hero_path')
                    ->select('rg_restaurant_listings.keyword_id', 'rg_restaurants.hero_path')
                    ->get();
                foreach ($rows as $r) {
                    if ($r->keyword_id) $map[$r->keyword_id][] = '/storage/' . ltrim($r->hero_path, '/');
                }
            }
            return $map;
        });
        $featuredKeywordsForBlocks = $featuredKeywords->map(function ($kw) use ($kwDirect, $regionImgs) {
            $region = \App\Support\RegionResolver::resolve((string) ($kw->cluster_tag ?? ''), (string) ($kw->phrase ?? ''));
            $direct = array_values(array_unique($kwDirect[$kw->id] ?? []));
            $pool = $regionImgs[$region] ?? [];
            shuffle($pool);
            $imgs = array_values(array_unique(array_merge($direct, $pool)));
            return [
                'phrase' => $kw->phrase,
                'slug' => $kw->slug,
                'images' => array_slice($imgs, 0, 4),
            ];
        });

        // Block-driven render: if the `home` static_page row has
        // blocks attached, render them via BlockRenderer with the
        // controller data exposed via context. Otherwise fall back
        // to the legacy hardcoded view.
        $page = \DB::table('rg_static_pages')
            ->where('slug', 'home')
            ->where('is_published', 1)
            ->first();
        if ($page) {
            $blocks = \App\Models\RgContentBlock::forOwner('static_page', $page->id);
            if ($blocks->isNotEmpty()) {
                $liveEdit = false;
                $request = request();
                if ($request && $request->query('_lt')) {
                    $liveEdit = \App\Support\LiveEditToken::valid('home', $request->query('_lt'));
                }
                $renderer = app(\App\Services\BlockRenderer::class);
                $renderedBlocks = $renderer->renderBlocks($blocks, [
                    'static_page_id' => $page->id,
                    'featuredKeywords' => $featuredKeywordsForBlocks,
                    'featuredResorts' => $featuredResorts,
                    'latestPosts' => $latestPosts,
                    'regions' => $regions,
                    'stats' => $stats,
                    'unifiedSearchIndex' => app(\App\Services\UnifiedSearchIndex::class)->build(),
                    'jsonld' => $jsonld,
                    'live_edit' => $liveEdit,
                ]);
                return view('home.blocks', [
                    'page' => $page,
                    'renderedBlocks' => $renderedBlocks,
                    'liveEdit' => $liveEdit,
                    'jsonld' => $jsonld,
                ]);
            }
        }

        return view('home', compact('featuredKeywords', 'featuredResorts', 'latestPosts', 'stats', 'regions', 'jsonld'));
    }
}
