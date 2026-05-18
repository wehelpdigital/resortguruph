<?php

namespace App\Http\Controllers;

use App\Models\RgContentBlock;
use App\Models\RgKeyword;
use App\Models\RgListing;
use App\Models\RgSeoPage;
use App\Models\RgSetting;
use App\Services\BlockRenderer;
use App\Services\SchemaGenerator;
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

        $blocks = RgContentBlock::forOwner('seo_page', $page->id);
        $hasListingSlot = $blocks->contains(fn($b) => $b->block_type === 'listing_slot');
        $renderedBlocks = $renderer->renderBlocks($blocks, ['keyword_id' => $keyword->id]);

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
            $related = RgKeyword::where('cluster_tag', $keyword->cluster_tag)
                ->where('id', '<>', $keyword->id)
                ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
                ->orderByDesc('search_volume_monthly')
                ->limit(8)
                ->get();
        }

        // Build JSON-LD: breadcrumb, FAQPage (if any), ItemList of listings
        $crumbs = [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Destinations', 'url' => url('/destinations')],
        ];
        if ($cluster) {
            $crumbs[] = ['name' => $cluster['name'], 'url' => route('destinations.cluster', $keyword->cluster_tag)];
        }
        $crumbs[] = ['name' => ucwords($page->title), 'url' => url($page->slug)];

        $jsonldParts = [$schema->emit($schema->breadcrumb($crumbs))];
        if (!empty($faqs)) $jsonldParts[] = $schema->emit($schema->faqPage($faqs));
        if ($listings->count() > 0) $jsonldParts[] = $schema->emit($schema->itemList(collect($listings->items()), $keyword));
        if ($page->schema_json) {
            $decoded = json_decode($page->schema_json, true);
            if (is_array($decoded)) $jsonldParts[] = $schema->emit($decoded);
        }
        $jsonld = implode('', $jsonldParts);

        return view('keyword-page', compact(
            'keyword', 'page', 'listings', 'faqs', 'cluster', 'related',
            'renderedBlocks', 'hasListingSlot', 'siblingPages', 'jsonld'
        ));
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
