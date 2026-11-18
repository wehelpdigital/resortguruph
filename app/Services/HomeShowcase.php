<?php

namespace App\Services;

use App\Http\Controllers\DestinationsController;
use App\Support\RegionResolver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data for the hand-designed homepage (resources/views/home/showcase.blade.php).
 * Everything is cached for 10 minutes; content still comes from the DB so
 * admin edits (pages, fiestas, reviews, blog) show up automatically.
 */
class HomeShowcase
{
    /** Destinations featured in the "Trending" row: label => [phrase match, photo]. */
    private const TRENDING = [
        'El Nido' => ['el nido', 'destinations/el-nido-3.jpg', 'Palawan'],
        'Boracay' => ['boracay', 'destinations/boracay-2.jpg', 'Aklan'],
        'Bohol' => ['bohol', 'food-locations/bohol.jpg', 'Central Visayas'],
        'Cebu' => ['cebu', 'spots/cebu-city-magellans-cross-and-basilica-del-santo-nino.jpg', 'Central Visayas'],
        'Coron' => ['coron', 'food-locations/coron-2.jpg', 'Palawan'],
        'Vigan' => ['vigan', 'destinations/vigan-2.jpg', 'Ilocos Sur'],
        'Baguio' => ['baguio', 'food-locations/baguio.jpg', 'Benguet'],
        'Tagaytay' => ['tagaytay', 'food-locations/tagaytay.jpg', 'Cavite'],
        'La Union' => ['la union', 'spots/la-union-san-juan-surf-beach-urbiztondo.jpg', 'Ilocos Region'],
    ];

    public function data(): array
    {
        return Cache::remember('home_showcase_v2', 600, fn () => [
            'stats' => $this->stats(),
            'trending' => $this->trending(),
            'styles' => $this->styles(),
            'fiestas' => $this->fiestas(),
            'regions' => $this->regions(),
            'reviews' => $this->reviews(),
            'posts' => $this->posts(),
            'faqs' => $this->faqs(),
        ]);
    }

    private function stats(): array
    {
        $has = fn ($t) => Schema::hasTable($t);
        return [
            'guides' => $has('rg_seo_pages') ? DB::table('rg_seo_pages')->where('is_published', 1)->count() : 0,
            'reviews' => $has('rg_destination_reviews') ? DB::table('rg_destination_reviews')->count() : 0,
            'rating' => $has('rg_destination_reviews') ? round((float) DB::table('rg_destination_reviews')->avg('rating'), 1) : 0,
            'spots' => $has('rg_tourist_spots') ? DB::table('rg_tourist_spots')->count() : 0,
            'fiestas' => $has('rg_fiestas') ? DB::table('rg_fiestas')->where('is_published', 1)->count() : 0,
            'posts' => $has('rg_blog_posts') ? DB::table('rg_blog_posts')->where('status', 'published')->count() : 0,
        ];
    }

    private function trending(): array
    {
        $out = [];
        foreach (self::TRENDING as $label => [$match, $img, $area]) {
            $base = DB::table('rg_keywords as k')->join('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')
                ->where('p.is_published', 1)->where('k.phrase', 'like', "%{$match}%");
            $page = (clone $base)->orderByRaw("k.category = 'resort' desc")->orderByDesc('k.search_volume_monthly')->first(['k.slug']);
            if (!$page) continue;
            $out[] = [
                'name' => $label,
                'area' => $area,
                'url' => url($page->slug),
                'image' => '/storage/rg-media/' . $img,
                'guides' => (clone $base)->count(),
            ];
        }
        return $out;
    }

    private function styles(): array
    {
        $img = fn ($p) => '/storage/rg-media/' . $p;
        return [
            ['title' => 'Beaches and islands', 'text' => 'Lagoons, sandbars and island hopping', 'url' => route('destinations.index'), 'image' => $img('spots/el-nido-small-lagoon-tour-a.jpg'), 'c' => '#13669c'],
            ['title' => 'Food trips', 'text' => 'Where locals line up to eat', 'url' => url('/food-trip'), 'image' => $img('restaurants/zubuchon.jpg'), 'c' => '#b45309'],
            ['title' => 'Filipino dishes', 'text' => 'Adobo, sisig, kakanin and more', 'url' => route('foods.index'), 'image' => $img('foods/adobo-1.jpg'), 'c' => '#c0392b'],
            ['title' => 'Adventures', 'text' => 'Treks, dives, falls and surf', 'url' => route('activities.index'), 'image' => $img('adventures/mt-pinatubo-crater-trek.jpg'), 'c' => '#4338ca'],
            ['title' => 'Festivals', 'text' => 'A fiesta somewhere every month', 'url' => route('fiestas.index'), 'image' => $img('fiestas/masskara.jpg'), 'c' => '#be185d'],
            ['title' => 'Pasalubong', 'text' => 'What to bring home and where', 'url' => route('buys.index'), 'image' => $img('buys/guimaras-mangoes-2.jpg'), 'c' => '#7c3aed'],
        ];
    }

    private function fiestas(): array
    {
        if (!Schema::hasTable('rg_fiestas')) return ['month' => '', 'items' => []];
        $m = (int) now()->format('n');
        $pick = function (int $month) {
            return DB::table('rg_fiestas')->where('is_published', 1)->where('month', $month)->whereNotNull('cover_image_path')
                ->orderByDesc('pageviews_30d')->limit(4)->get(['slug', 'name', 'city_or_town', 'province', 'date_label', 'cover_image_path'])->all();
        };
        $items = $pick($m);
        $month = $m;
        if (count($items) < 3) {
            $next = $m === 12 ? 1 : $m + 1;
            $items = array_slice(array_merge($items, $pick($next)), 0, 4);
            $month = count($pick($m)) ? $m : $next;
        }
        return [
            'month' => date('F', mktime(0, 0, 0, $month, 1)),
            'items' => array_map(fn ($f) => [
                'name' => preg_replace('/\s*\(.*\)$/', '', $f->name),
                'place' => trim(($f->city_or_town ?: '') . ($f->province ? ', ' . $f->province : ''), ', '),
                'when' => $f->date_label,
                'url' => route('fiestas.show', $f->slug),
                'image' => '/storage/' . ltrim($f->cover_image_path, '/'),
            ], $items),
        ];
    }

    private function regions(): array
    {
        $meta = DestinationsController::clusterMetadata();
        $counts = DB::table('rg_keywords as k')->join('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')->where('p.is_published', 1)
            ->get(['k.cluster_tag', 'k.phrase'])->groupBy(fn ($k) => RegionResolver::resolve($k->cluster_tag, $k->phrase))->map->count();
        $order = ['palawan', 'visayas', 'north-luzon', 'bicol', 'mindanao', 'batangas', 'cavite', 'rizal', 'laguna', 'metro-manila', 'pampanga', 'quezon', 'bulacan'];
        $out = [];
        foreach ($order as $slug) {
            if (!isset($meta[$slug])) continue;
            $out[] = ['name' => $meta[$slug]['name'], 'url' => route('destinations.cluster', $slug), 'count' => (int) ($counts[$slug] ?? 0), 'image' => '/storage/rg-media/landmarks/' . $slug . '.jpg'];
        }
        return $out;
    }

    private function reviews(): array
    {
        if (!Schema::hasTable('rg_destination_reviews')) return [];
        $rows = DB::table('rg_destination_reviews as r')->leftJoin('rg_keywords as k', 'k.id', '=', 'r.keyword_id')
            ->where('r.rating', '>=', 5)->where('r.status', 'published')
            ->whereRaw('char_length(r.review_text) between 140 and 280')
            ->orderByDesc('r.is_featured')->orderBy('r.id')->limit(60)
            ->get(['r.reviewer_name', 'r.reviewer_location', 'r.reviewer_avatar', 'r.rating', 'r.review_text', 'k.phrase', 'k.slug']);
        $seen = []; $out = [];
        foreach ($rows as $r) {
            // seeded reviews share templates: skip repeats of the same opening words or reviewer
            $sig = implode(' ', array_slice(preg_split('/\s+/', strtolower(preg_replace('/[^a-z ]/i', '', $r->review_text))), 0, 4));
            if (isset($seen[$r->reviewer_name]) || isset($seen['sig:' . $sig])) continue;
            $seen[$r->reviewer_name] = true; $seen['sig:' . $sig] = true;
            $av = (string) $r->reviewer_avatar;
            $out[] = [
                'name' => $r->reviewer_name, 'from' => $r->reviewer_location, 'rating' => (int) $r->rating, 'text' => $r->review_text,
                'about' => $r->phrase ? ucwords($r->phrase) : null, 'url' => $r->slug ? url($r->slug) : null,
                'avatar' => $av === '' ? \App\Support\Avatar::initials($r->reviewer_name) : (preg_match('#^https?://#', $av) ? $av : '/storage/' . ltrim($av, '/')),
            ];
            if (count($out) >= 6) break;
        }
        return $out;
    }

    private function posts(): array
    {
        if (!Schema::hasTable('rg_blog_posts')) return [];
        return DB::table('rg_blog_posts')->where('status', 'published')->whereNotNull('cover_path')
            ->orderByDesc('published_at')->limit(3)->get(['slug', 'title', 'excerpt', 'cover_path', 'published_at'])
            ->map(fn ($p) => ['title' => $p->title, 'excerpt' => $p->excerpt, 'url' => route('blog.show', $p->slug), 'image' => '/storage/' . ltrim($p->cover_path, '/'), 'date' => $p->published_at ? date('M j, Y', strtotime($p->published_at)) : ''])
            ->all();
    }

    private function faqs(): array
    {
        $home = DB::table('rg_static_pages')->where('slug', 'home')->value('id');
        $raw = $home ? DB::table('rg_content_blocks')->where('owner_type', 'static_page')->where('owner_id', $home)->where('block_type', 'home_faq')->value('payload_json') : null;
        $p = $raw ? json_decode($raw, true) : [];
        $items = $p['items'] ?? $p['faqs'] ?? [];
        if (is_string($items)) $items = json_decode($items, true) ?: [];
        $out = [];
        foreach ((array) $items as $it) {
            $q = trim((string) ($it['q'] ?? $it['question'] ?? '')); $a = trim((string) ($it['a'] ?? $it['answer'] ?? ''));
            if ($q !== '' && $a !== '') $out[] = ['q' => $q, 'a' => $a];
        }
        return array_slice($out, 0, 8);
    }
}
