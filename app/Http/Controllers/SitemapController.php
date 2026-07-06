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
        $xml = Cache::remember('rg.sitemap.v6', 3600, function () {
            $urls = [
                ['loc' => route('home'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'],
                ['loc' => route('destinations.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('activities.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('foods.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('buys.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
                ['loc' => route('cultures.index'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
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

    /**
     * Human-readable HTML sitemap at /sitemap. Every public URL, grouped and
     * ordered, for both visitors and crawlers. Cached 1 hour; content is fully
     * dynamic (regions, keyword pages, fiestas, resorts, blog).
     */
    public function page()
    {
        $sections = Cache::remember('rg.sitemap.page.v1', 3600, function () {
            $sections = [];

            $sections[] = ['group' => 'Explore', 'title' => 'Main pages', 'links' => [
                ['label' => 'Home', 'url' => route('home'), 'strong' => true],
                ['label' => 'All Destinations', 'url' => route('destinations.index'), 'strong' => true],
                ['label' => 'Food Trip', 'url' => route('foods.index')],
                ['label' => 'Things to Do', 'url' => route('activities.index')],
                ['label' => 'What to Buy', 'url' => route('buys.index')],
                ['label' => 'Culture', 'url' => route('cultures.index')],
                ['label' => 'Fiestas & Festivals', 'url' => route('fiestas.index')],
                ['label' => 'Blog', 'url' => route('blog.index')],
            ]];

            // Destination keyword pages, grouped by resolved region.
            $meta = \App\Http\Controllers\DestinationsController::clusterMetadata();
            RgKeyword::query()
                ->where('category', 'resort')
                ->whereHas('seoPage', fn ($q) => $q->where('is_published', true))
                ->orderByDesc('search_volume_monthly')
                ->get(['phrase', 'slug', 'cluster_tag'])
                ->groupBy(fn ($k) => \App\Support\RegionResolver::resolve($k->cluster_tag, $k->phrase))
                ->sortKeys()
                ->each(function ($kws, $regionKey) use (&$sections, $meta) {
                    if (!isset($meta[$regionKey])) {
                        return;
                    }
                    $links = [['label' => 'All resorts in ' . $meta[$regionKey]['name'], 'url' => route('destinations.cluster', $regionKey), 'strong' => true]];
                    foreach ($kws as $k) {
                        $links[] = ['label' => ucwords($k->phrase), 'url' => url($k->slug)];
                    }
                    $sections[] = ['group' => 'Destinations by region', 'title' => $meta[$regionKey]['name'], 'links' => $links];
                });

            // Food keyword pages.
            $foodKws = RgKeyword::query()
                ->where('category', 'food')
                ->whereHas('seoPage', fn ($q) => $q->where('is_published', true))
                ->orderByDesc('search_volume_monthly')
                ->get(['phrase', 'slug']);
            if ($foodKws->isNotEmpty()) {
                $sections[] = ['group' => 'Food & experiences', 'title' => 'Food finds', 'links' => $foodKws->map(fn ($k) => ['label' => ucwords($k->phrase), 'url' => url($k->slug)])->all()];
            }

            // Fiestas.
            $fiestas = RgFiesta::where('is_published', true)->orderBy('name')->get(['name', 'slug']);
            if ($fiestas->isNotEmpty()) {
                $sections[] = ['group' => 'Food & experiences', 'title' => 'Fiestas & festivals', 'links' => $fiestas->map(fn ($f) => ['label' => $f->name, 'url' => route('fiestas.show', $f->slug)])->all()];
            }

            // Published resorts.
            $resorts = RgResort::where('status', 'published')->orderBy('name')->get(['name', 'slug']);
            if ($resorts->isNotEmpty()) {
                $sections[] = ['group' => 'Stays', 'title' => 'Resorts', 'links' => $resorts->map(fn ($r) => ['label' => $r->name, 'url' => route('resort.show', $r->slug)])->all()];
            }

            // Blog posts.
            $posts = RgBlogPost::where('status', 'published')->orderByDesc('id')->get(['title', 'slug']);
            if ($posts->isNotEmpty()) {
                $sections[] = ['group' => 'Reading', 'title' => 'Blog articles', 'links' => $posts->map(fn ($p) => ['label' => $p->title, 'url' => route('blog.show', $p->slug)])->all()];
            }

            // Company + legal.
            $sections[] = ['group' => 'Company', 'title' => 'About & legal', 'links' => [
                ['label' => 'About', 'url' => route('about')],
                ['label' => 'Contact', 'url' => route('contact')],
                ['label' => 'Terms of Service', 'url' => route('terms')],
                ['label' => 'Privacy Policy', 'url' => route('privacy')],
                ['label' => 'XML Sitemap', 'url' => route('sitemap')],
            ]];

            return $sections;
        });

        return view('sitemap', ['sections' => $sections]);
    }

    public function robots()
    {
        $txt = "User-agent: *\nAllow: /\n\nSitemap: " . route('sitemap') . "\n";
        return response($txt, 200)->header('Content-Type', 'text/plain');
    }
}
