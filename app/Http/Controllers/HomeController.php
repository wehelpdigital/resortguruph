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
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $stats = [
            'pages' => RgSeoPage::where('is_published', true)->count(),
            'resorts' => RgResort::where('status', 'published')->count(),
        ];

        $clusterMeta = DestinationsController::clusterMetadata();
        $regions = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
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
                    'featuredKeywords' => $featuredKeywords,
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
