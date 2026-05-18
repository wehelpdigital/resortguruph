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

        return view('home', compact('featuredKeywords', 'featuredResorts', 'latestPosts', 'stats', 'regions', 'jsonld'));
    }
}
