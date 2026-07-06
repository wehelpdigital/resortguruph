<?php

namespace App\Http\Controllers;

use App\Models\RgContentBlock;
use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgRestaurantListing;
use App\Models\RgSeoPage;
use App\Services\BlockRenderer;
use Illuminate\Http\Response;

/**
 * Renders a single rg_content_blocks row as a standalone HTML page so the
 * mother-app admin builder can iframe it as a true miniature of the
 * public render. Read-only and public — blocks already surface on the
 * public site, so there is nothing here that wasn't already exposed.
 */
class BuilderPreviewController extends Controller
{
    public function show(int $blockId, BlockRenderer $renderer): Response
    {
        $block = RgContentBlock::find($blockId);
        if (!$block) {
            // Render an "empty" page rather than 404 so the iframe shows a
            // tidy message instead of the browser error page.
            return response()
                ->view('builder-preview', [
                    'rendered'  => '',
                    'blockId'   => $blockId,
                    'blockType' => 'missing',
                ])
                ->header('X-Frame-Options', 'SAMEORIGIN');
        }

        $context = $this->resolveContext($block);
        $rendered = $renderer->renderBlock($block, $context);

        return response()
            ->view('builder-preview', [
                'rendered'  => $rendered,
                'blockId'   => $blockId,
                'blockType' => $block->block_type,
            ])
            // Allow the mother admin to iframe this. ALLOWALL is a non-
            // standard value some browsers honor; the canonical fix is to
            // skip the header entirely (default = allow same-origin via
            // referrer policy + CSP), so we set neither X-Frame-Options
            // nor CSP frame-ancestors and rely on the browser default.
            ->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Build the render context the same way KeywordPageController does for
     * the live public page — keyword, listings, restaurantListings — so
     * context-dependent blocks (listing_block, future relations) render
     * accurately in the preview.
     */
    private function resolveContext(RgContentBlock $block): array
    {
        // Homepage static_page blocks get the same context HomeController
        // exposes, so context-driven home blocks (keyword grid, region
        // grid, resort grid, blog strip, unified search) preview with real
        // data instead of rendering empty.
        if ($block->owner_type === 'static_page') {
            $page = \Illuminate\Support\Facades\DB::table('rg_static_pages')
                ->where('id', $block->owner_id)->first();
            if ($page && ($page->slug ?? '') === 'home') {
                try {
                    return $this->homeContext();
                } catch (\Throwable $e) {
                    return [];
                }
            }
            return [];
        }
        if ($block->owner_type !== 'seo_page') {
            return [];
        }
        $page = RgSeoPage::find($block->owner_id);
        if (!$page) return [];
        $keyword = RgKeyword::find($page->keyword_id);
        if (!$keyword) return ['keyword_id' => $page->keyword_id];

        $isFood = ($keyword->category ?? '') === 'food';
        if ($isFood) {
            $restaurantListings = RgRestaurantListing::query()
                ->where('keyword_id', $keyword->id)
                ->where('status', 'active')
                ->with('restaurant')
                ->orderByDesc('bid_gp')
                ->limit(8)
                ->get()
                ->filter(fn($l) => $l->restaurant)
                ->values();
            $listings = collect();
        } else {
            $listings = RgListing::query()
                ->where('keyword_id', $keyword->id)
                ->where('status', 'active')
                ->with('resort')
                ->orderByDesc('bid_gp')
                ->limit(8)
                ->get()
                ->filter(fn($l) => $l->resort)
                ->values();
            $restaurantListings = collect();
        }

        return [
            'keyword_id'         => $keyword->id,
            'keyword'            => $keyword,
            'listings'           => $listings,
            'restaurantListings' => $restaurantListings,
            'listingGalleries'   => [],
        ];
    }

    /**
     * The homepage render context, mirroring HomeController@index so the
     * builder's per-block previews show the same data the live home page
     * does.
     */
    private function homeContext(): array
    {
        $publishedResortKeyword = fn($q) => $q->where('is_published', true);

        $featuredKeywords = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', $publishedResortKeyword)
            ->orderByDesc('search_volume_monthly')
            ->limit(12)
            ->get();

        $featuredResorts = \App\Models\RgResort::where('status', 'published')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $latestPosts = \App\Models\RgBlogPost::where('status', 'published')
            ->inRandomOrder()
            ->limit(9)
            ->get();

        $stats = [
            'pages' => RgSeoPage::where('is_published', true)->count(),
            'resorts' => \App\Models\RgResort::where('status', 'published')->count(),
        ];

        $clusterMeta = \App\Http\Controllers\DestinationsController::clusterMetadata();
        $regions = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', $publishedResortKeyword)
            ->get()
            ->groupBy('cluster_tag')
            ->map(function ($kws, $slug) use ($clusterMeta) {
                if (!isset($clusterMeta[$slug])) return null;
                return [
                    'slug' => $slug,
                    'name' => $clusterMeta[$slug]['name'],
                    'tagline' => $clusterMeta[$slug]['tagline'],
                    'count' => $kws->count(),
                    'total_volume' => $kws->sum('search_volume_monthly'),
                ];
            })
            ->filter()
            ->sortByDesc('total_volume')
            ->values();

        return [
            'featuredKeywords'   => $featuredKeywords,
            'featuredResorts'    => $featuredResorts,
            'latestPosts'        => $latestPosts,
            'regions'            => $regions,
            'stats'              => $stats,
            'unifiedSearchIndex' => app(\App\Services\UnifiedSearchIndex::class)->build(),
        ];
    }
}
