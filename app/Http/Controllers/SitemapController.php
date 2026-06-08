<?php

namespace App\Http\Controllers;

use App\Models\RgBlogPost;
use App\Models\RgFiesta;
use App\Models\RgKeyword;
use App\Models\RgResort;
use App\Models\RgSeoPage;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('rg.sitemap.v5', 3600, function () {
            $urls = [
                ['loc' => route('home'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'],
                ['loc' => url('/destinations'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('activities.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('foods.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('buys.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('fiestas.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.85'],
                ['loc' => route('blog.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.6'],
                ['loc' => route('about'), 'lastmod' => now()->toAtomString(), 'priority' => '0.4'],
                ['loc' => route('contact'), 'lastmod' => now()->toAtomString(), 'priority' => '0.4'],
                ['loc' => route('terms'), 'lastmod' => now()->toAtomString(), 'priority' => '0.2'],
                ['loc' => route('privacy'), 'lastmod' => now()->toAtomString(), 'priority' => '0.2'],
            ];

            // Individual fiesta pages
            RgFiesta::where('is_published', true)->orderBy('id')->each(function ($f) use (&$urls) {
                $urls[] = [
                    'loc' => route('fiestas.show', $f->slug),
                    'lastmod' => optional($f->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'priority' => '0.65',
                ];
            });

            // Cluster hub pages
            $clusterMeta = \App\Http\Controllers\DestinationsController::clusterMetadata();
            $activeClusterSlugs = RgKeyword::query()
                ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
                ->distinct()
                ->pluck('cluster_tag')
                ->filter()
                ->all();
            foreach ($activeClusterSlugs as $slug) {
                if (!isset($clusterMeta[$slug])) continue;
                $urls[] = [
                    'loc' => route('destinations.cluster', $slug),
                    'lastmod' => now()->toAtomString(),
                    'priority' => '0.85',
                ];
            }

            // Iterate ALL published SEO pages (a keyword can have many)
            RgSeoPage::where('is_published', true)
                ->orderByDesc('is_primary')
                ->orderBy('id')
                ->each(function ($page) use (&$urls) {
                    $urls[] = [
                        'loc' => url($page->slug),
                        'lastmod' => optional($page->updated_at)->toAtomString() ?? now()->toAtomString(),
                        'priority' => $page->is_primary ? '0.9' : '0.7',
                    ];
                });

            // Resort detail pages
            RgResort::where('status', 'published')->orderBy('id')->each(function ($r) use (&$urls) {
                $urls[] = [
                    'loc' => route('resort.show', $r->slug),
                    'lastmod' => optional($r->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'priority' => '0.7',
                ];
            });

            // Blog posts
            RgBlogPost::where('status', 'published')->orderBy('id')->each(function ($p) use (&$urls) {
                $urls[] = [
                    'loc' => route('blog.show', $p->slug),
                    'lastmod' => optional($p->updated_at)->toAtomString() ?? now()->toAtomString(),
                    'priority' => '0.5',
                ];
            });

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($urls as $u) {
                $xml .= "  <url>\n";
                $xml .= "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
                $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
                $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
                $xml .= "  </url>\n";
            }
            $xml .= '</urlset>';
            return $xml;
        });
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $txt = "User-agent: *\nAllow: /\n\nSitemap: " . route('sitemap') . "\n";
        return response($txt, 200)->header('Content-Type', 'text/plain');
    }
}
