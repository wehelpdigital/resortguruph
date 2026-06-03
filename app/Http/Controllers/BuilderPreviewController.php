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
}
