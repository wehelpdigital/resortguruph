<?php

namespace App\Http\Controllers;

use App\Models\RgAdventureListing;
use App\Models\RgAuthor;
use App\Models\RgContentBlock;
use App\Models\RgDestinationReview;
use Illuminate\Support\Facades\Auth;
use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgRestaurantListing;
use App\Models\RgSeoPage;
use App\Models\RgSetting;
use App\Services\BlockRenderer;
use App\Services\SchemaGenerator;
use App\Support\LiveEditToken;
use Illuminate\Http\Request;

/**
 * Renders a published SEO page. Each keyword can have multiple pages;
 * each page has its own slug and is independently rankable. Listings
 * are filtered by keyword_id so paid bids surface on every page of
 * the same keyword.
 */
class KeywordPageController extends Controller
{
    public function show(Request $request, RgSeoPage $page, BlockRenderer $renderer, SchemaGenerator $schema)
    {
        if (!$page->is_published) abort(404);
        $keyword = RgKeyword::find($page->keyword_id);
        if (!$keyword) abort(404);

        $listingsPerPage = (int) RgSetting::get('listings_per_page_default', 10);

        $listings = RgListing::query()
            ->where('keyword_id', $keyword->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['resort' => fn($q) => $q->where('status', 'published')])
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->paginate($listingsPerPage)
            ->withQueryString();

        // Live-edit mode: an HMAC-signed token from the mother
        // super-admin enables admin chrome on the rendered page.
        // The token is slug-scoped and short-lived. When valid we
        // pass `live_edit=true` into the BlockRenderer so each
        // block's output gets wrapped in a `data-rg-block` element
        // the iframe-side JS can find.
        $liveEdit = LiveEditToken::valid($page->slug, $request->query('_lt'));

        $blocks = RgContentBlock::forOwner('seo_page', $page->id);
        $hasListingSlot = $blocks->contains(fn($b) => $b->block_type === 'listing_slot');
        // First render with minimal context (listing_slot etc. only need
        // keyword_id). Re-rendered later if any block needs the food /
        // adventure listings collections — see comment near $restaurantListings.
        $renderedBlocks = $renderer->renderBlocks($blocks, [
            'keyword_id' => $keyword->id,
            'live_edit' => $liveEdit,
        ]);
        $needsListingBlockCtx = $blocks->contains(fn($b) => $b->block_type === 'listing_block');

        $faqs = $renderer->extractFaqs('seo_page', $page->id);
        if (empty($faqs) && $page->faq_json) {
            $decoded = json_decode($page->faq_json, true);
            if (is_array($decoded)) $faqs = $decoded;
        }

        // Sibling pages for the same keyword (for "Other pages" cross-links)
        $siblingPages = RgSeoPage::where('keyword_id', $keyword->id)
            ->where('id', '<>', $page->id)
            ->where('is_published', true)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get(['id', 'slug', 'title', 'is_primary']);

        if (!$this->isBot($request->userAgent())) {
            $page->increment('pageviews_30d');
            $page->increment('pageviews_total');
        }

        $cluster = DestinationsController::clusterMetadata()[$keyword->cluster_tag] ?? null;
        $related = collect();
        if ($keyword->cluster_tag) {
            // Related keywords must share the same category — resort pages
            // cross-link to other resort pages, food pages to other food
            // pages. Mixing them would confuse search engines and visitors.
            $related = RgKeyword::where('cluster_tag', $keyword->cluster_tag)
                ->where('category', $keyword->category)
                ->where('id', '<>', $keyword->id)
                ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
                ->orderByDesc('search_volume_monthly')
                ->limit(8)
                ->get();
        }

        // Build JSON-LD: breadcrumb, FAQPage (if any), ItemList of listings.
        // Food keywords route under /food-trip not /destinations.
        if ($keyword->category === 'food') {
            $crumbs = [
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Food Trip', 'url' => url('/food-trip')],
                ['name' => 'Food Destinations', 'url' => url('/food-trip')],
            ];
        } else {
            $crumbs = [
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Destinations', 'url' => url('/destinations')],
            ];
            if ($cluster) {
                $crumbs[] = ['name' => $cluster['name'], 'url' => route('destinations.cluster', $keyword->cluster_tag)];
            }
        }
        $crumbs[] = ['name' => ucwords($page->title), 'url' => url($page->slug)];

        $author = $page->author_id ? RgAuthor::find($page->author_id) : null;
        $heroImageUrl = $page->og_image_path
            ? (preg_match('#^https?://#i', $page->og_image_path) ? $page->og_image_path : asset('storage/' . ltrim($page->og_image_path, '/')))
            : null;

        // Destination reviews: scoped to this keyword, plus globally-scoped reviews
        $reviews = RgDestinationReview::query()
            ->where('status', 'published')
            ->where(function ($q) use ($keyword) {
                $q->where('keyword_id', $keyword->id)->orWhereNull('keyword_id');
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('review_date')
            ->limit(12)
            ->get();

        $jsonldParts = [
            $schema->emit($schema->breadcrumb($crumbs)),
            $schema->emit($schema->article($page, $author, $heroImageUrl, url($page->slug))),
        ];
        if (!empty($faqs)) $jsonldParts[] = $schema->emit($schema->faqPage($faqs));
        if ($listings->count() > 0) $jsonldParts[] = $schema->emit($schema->itemList(collect($listings->items()), $keyword));
        if ($reviews->isNotEmpty()) {
            $jsonldParts[] = $schema->emit($schema->aggregateRating($keyword, $reviews, url($page->slug)));
        }
        if ($page->schema_json) {
            $decoded = json_decode($page->schema_json, true);
            if (is_array($decoded)) $jsonldParts[] = $schema->emit($decoded);
        }
        $jsonld = implode('', $jsonldParts);

        // Build per-listing gallery (8 image URLs to feed 4 fade-strips with
        // 2 images each), a fake "high" rating, and 3 rotating fake reviews.
        // All deterministic per listing.id so the page is stable across loads.
        $listingGalleries = [];
        $listingRatings = [];
        $listingReviews = [];
        foreach ($listings as $listing) {
            $listingGalleries[$listing->id] = $this->buildListingGallery($listing, $keyword->cluster_tag);
            $listingRatings[$listing->id] = $this->buildListingRating($listing->id);
            $listingReviews[$listing->id] = $this->buildListingReviews($listing->id);
        }

        // Sort listings by fake rating descending (highest on top, per user
        // request). We re-sort the collection items but keep paginator meta.
        $sortedItems = collect($listings->items())->sortByDesc(fn($l) => $listingRatings[$l->id] ?? 0)->values();
        $listings->setCollection($sortedItems);

        // Restaurant listings: appear on BOTH food + resort keyword pages.
        // (food page = primary; resort page = "Restaurant Recommendations" section)
        $restaurantListings = RgRestaurantListing::query()
            ->where('keyword_id', $keyword->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->with(['restaurant' => fn($q) => $q->where('status', 'published')])
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->limit(8)
            ->get()
            ->filter(fn($l) => $l->restaurant)
            ->values();

        // If a listing_block exists in the page's content blocks, re-render
        // now that we have the listings + galleries to feed it. The earlier
        // pass output only handled non-context-aware blocks; this pass
        // replaces the full string.
        if ($needsListingBlockCtx) {
            $renderedBlocks = $renderer->renderBlocks($blocks, [
                'keyword_id' => $keyword->id,
                'keyword' => $keyword,
                'listings' => collect($listings->items()),
                'restaurantListings' => $restaurantListings,
                'listingGalleries' => $listingGalleries ?? [],
                'live_edit' => $liveEdit,
            ]);
        }

        // Adventure listings: only relevant on resort keyword pages
        // ("Memorable Adventures" section under the resorts).
        $adventureListings = $keyword->category === 'food'
            ? collect()
            : RgAdventureListing::query()
                ->where('keyword_id', $keyword->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->with(['adventure' => fn($q) => $q->where('status', 'published')])
                ->orderByDesc('bid_gp')
                ->orderBy('last_bid_at')
                ->limit(6)
                ->get()
                ->filter(fn($l) => $l->adventure)
                ->values();

        return view('keyword-page', compact(
            'keyword', 'page', 'listings', 'listingGalleries', 'listingRatings', 'listingReviews',
            'faqs', 'cluster', 'related',
            'renderedBlocks', 'hasListingSlot', 'siblingPages', 'jsonld', 'author', 'reviews',
            'restaurantListings', 'adventureListings', 'liveEdit'
        ));
    }

    /**
     * Deterministic fake rating in the 4.5-4.9 band (high but believable).
     * Seeded by listing id so the same listing always shows the same value.
     */
    private function buildListingRating(int $listingId): float
    {
        $seed = abs(crc32('listing_rating_' . $listingId));
        // 4.5 → 4.9 range in 0.1 increments
        $tenths = $seed % 5; // 0..4
        return 4.5 + ($tenths * 0.1);
    }

    /**
     * Returns 3 short fake reviews per listing (commenter + text). Cycled
     * through a fader in the listing card so the section feels alive.
     */
    private function buildListingReviews(int $listingId): array
    {
        $names = [
            'Mark Anthony Lim', 'Sheryl Magno', 'Patricia delos Santos', 'Renzo Aquino',
            'Aileen Bautista', 'Carlo Mendoza', 'Jessa Ramirez', 'Daniel Pascual',
            'Hannah Reyes', 'Joan Villaruel', 'Bryan Tan', 'Carmela Yulo',
        ];
        $bodies = [
            "Smooth check-in, helpful staff, sulit ang bayad. Will book again.",
            "We came as a barkada of 8 and the place handled the group well.",
            "The pool area was clean and the breakfast spread was solid.",
            "Quiet at night, good for couples who want to actually rest.",
            "Front desk gave us spot-on directions to the nearby food spots.",
            "Family-friendly, the kids loved it. Big enough for our group.",
            "Booking was straightforward, room was as advertised, no surprises.",
            "Honest value for the price, would recommend to friends planning the same trip.",
            "Loved the small touches, the staff remembered our names by day two.",
            "Clean, quiet, and the location made the rest of the trip easy.",
        ];
        $cities = ['Quezon City', 'Makati', 'Pasig', 'Cebu City', 'Davao City', 'Iloilo', 'Antipolo'];

        $seed = abs(crc32('listing_reviews_' . $listingId));
        $out = [];
        for ($i = 0; $i < 3; $i++) {
            $name = $names[($seed + $i * 7) % count($names)];
            $body = $bodies[($seed + $i * 11) % count($bodies)];
            $city = $cities[($seed + $i * 3) % count($cities)];
            $daysAgo = 3 + (($seed + $i * 5) % 60);
            $out[] = [
                'name' => $name,
                'city' => $city,
                'body' => $body,
                'days_ago' => $daysAgo,
            ];
        }
        return $out;
    }

    /**
     * Returns 6 image URLs for a listing's photo strip carousel. Sources:
     * (1) resort.hero_path if set, (2) any rg_resort_media records, then
     * (3) cluster-level destination images, (4) cluster landmark — in that
     * priority order — until 6 unique paths are collected.
     */
    private function buildListingGallery($listing, ?string $cluster): array
    {
        $out = [];
        $resort = $listing->resort;
        if ($resort && $resort->hero_path) {
            $out[] = asset('storage/' . ltrim($resort->hero_path, '/'));
        }

        // Cluster-based fallback pool from rg-media/destinations/{cluster}-*
        $clusterKey = $cluster ?: 'visayas';
        $clusterCity = $resort ? strtolower(\Illuminate\Support\Str::slug($resort->city ?: '')) : '';
        $disk = storage_path('app/public/rg-media/');

        // Try city/spots images first if we have a city
        if ($clusterCity) {
            $matches = glob($disk . 'spots/' . $clusterCity . '-*.jpg');
            foreach ($matches as $abs) {
                if (count($out) >= 6) break;
                $url = '/storage/rg-media/spots/' . basename($abs);
                if (!in_array($url, $out)) $out[] = $url;
            }
        }

        // Pad with cluster-level destination images
        $destMatches = glob($disk . 'destinations/' . $clusterCity . '-*.jpg');
        foreach ($destMatches as $abs) {
            if (count($out) >= 6) break;
            $url = '/storage/rg-media/destinations/' . basename($abs);
            if (!in_array($url, $out)) $out[] = $url;
        }

        // Cluster landmark catch-all
        if (count($out) < 6) {
            $lm = $disk . 'landmarks/' . $clusterKey . '.jpg';
            if (is_file($lm)) {
                $url = '/storage/rg-media/landmarks/' . $clusterKey . '.jpg';
                if (!in_array($url, $out)) $out[] = $url;
            }
        }

        // Final fallback: pull random spots from any cluster so the strip
        // always has 6 entries (avoids blank cells in the fading strips).
        if (count($out) < 6) {
            $any = glob($disk . 'spots/*.jpg');
            shuffle($any);
            foreach (array_slice($any, 0, 6 - count($out)) as $abs) {
                $url = '/storage/rg-media/spots/' . basename($abs);
                if (!in_array($url, $out)) $out[] = $url;
            }
        }

        return array_slice($out, 0, 6);
    }

    /**
     * Member-only review submission for a destination/keyword page. New
     * reviews default to status='pending' so admin moderates before they go
     * live. Public review form on the keyword page is gated by auth:owner.
     */
    public function storeReview(Request $request)
    {
        if (!Auth::guard('owner')->check()) abort(403);

        $data = $request->validate([
            'keyword_id' => 'required|integer|exists:rg_keywords,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:20|max:2000',
            'redirect_to' => 'nullable|string|max:200',
        ]);

        $user = \Illuminate\Support\Facades\Auth::guard('owner')->user();
        RgDestinationReview::create([
            'keyword_id' => $data['keyword_id'],
            'reviewer_name' => $user->name,
            'reviewer_location' => null,
            'reviewer_avatar' => $user->avatar_path ? asset('storage/' . ltrim($user->avatar_path, '/')) : null,
            'rating' => $data['rating'],
            'review_text' => trim($data['review_text']),
            'review_date' => now()->format('Y-m-d'),
            'status' => 'draft',  // 'draft' acts as pending until admin publishes via the Reviews admin
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        $redirect = $data['redirect_to'] ?? '/';
        return redirect($redirect)->with('review_status', 'Thanks. Your review was submitted and is waiting for moderation.');
    }

    private function isBot(?string $ua): bool
    {
        if (!$ua) return true;
        $bots = ['bot', 'crawler', 'spider', 'slurp', 'mediapartners', 'baidu', 'ahrefs', 'semrush', 'yandex'];
        $ua = strtolower($ua);
        foreach ($bots as $b) {
            if (str_contains($ua, $b)) return true;
        }
        return false;
    }
}
