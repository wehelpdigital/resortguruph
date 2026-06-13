<?php

namespace App\Services;

use App\Models\RgBlogPost;
use App\Models\RgKeyword;
use App\Models\RgResort;
use App\Models\RgTouristSpot;
use App\Http\Controllers\DestinationsController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Builds the flat, type-tagged JSON index that powers the unified
 * homepage typeahead search. Sources scanned (each contributes a
 * group of items with type / label / sub / url / haystack / volume
 * / image fields):
 *
 *   - destination  → published keyword pages (food + resort cats)
 *   - resort       → published rg_resorts (proper listings)
 *   - restaurant   → published rg_restaurants
 *   - spot         → published rg_tourist_spots
 *   - blog         → published rg_blog_posts
 *   - region       → destination clusters (Luzon, Visayas, etc.)
 *
 * Cached 1 hour (the underlying data changes slowly + the JSON is
 * shipped inline on every homepage hit). Cache key includes a row-
 * count fingerprint so adding/removing content invalidates within
 * the hour.
 */
class UnifiedSearchIndex
{
    private const CACHE_TTL = 3600;

    public function build(): array
    {
        $fingerprint = $this->fingerprint();
        return Cache::remember("rg.unified_search.{$fingerprint}", self::CACHE_TTL, function () {
            return array_merge(
                $this->regions(),
                $this->destinations(),
                $this->resorts(),
                $this->restaurants(),
                $this->spots(),
                $this->blog(),
            );
        });
    }

    /**
     * Row-count fingerprint so the cache invalidates when content
     * is added/removed without needing a manual flush.
     */
    private function fingerprint(): string
    {
        $parts = [];
        foreach (['rg_keywords', 'rg_resorts', 'rg_restaurants', 'rg_tourist_spots', 'rg_blog_posts'] as $table) {
            $parts[] = Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
        }
        return md5(implode('|', $parts));
    }

    private function regions(): array
    {
        $clusters = DestinationsController::clusterMetadata();
        $byCluster = RgKeyword::query()
            ->whereIn('category', ['resort', 'food'])
            ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
            ->get()
            ->groupBy('cluster_tag');

        $items = [];
        foreach ($clusters as $slug => $meta) {
            $count = isset($byCluster[$slug]) ? $byCluster[$slug]->count() : 0;
            if ($count === 0) continue;
            $items[] = [
                'type' => 'region',
                'label' => (string) $meta['name'],
                'sub' => $count . ' destinations and food pages',
                'url' => url('/destinations#cluster-' . $slug),
                'haystack' => mb_strtolower($meta['name'] . ' ' . ($meta['tagline'] ?? '')),
                'volume' => 0,
                'image' => null,
            ];
        }
        return $items;
    }

    private function destinations(): array
    {
        $clusters = DestinationsController::clusterMetadata();
        $keywords = RgKeyword::query()
            ->whereIn('category', ['resort', 'food'])
            ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->limit(800)
            ->get(['id', 'phrase', 'slug', 'category', 'cluster_tag', 'search_volume_monthly']);

        $items = [];
        foreach ($keywords as $kw) {
            $clusterName = $clusters[$kw->cluster_tag]['name'] ?? ucfirst($kw->cluster_tag);
            $items[] = [
                'type' => 'destination',
                'label' => $kw->phrase,
                'sub' => $clusterName . ' · ' . number_format((int) $kw->search_volume_monthly) . ' searches monthly',
                'url' => url('/' . $kw->slug),
                'haystack' => mb_strtolower($kw->phrase . ' ' . $clusterName),
                'volume' => (int) $kw->search_volume_monthly,
                'image' => null,
            ];
        }
        return $items;
    }

    private function resorts(): array
    {
        if (!Schema::hasTable('rg_resorts')) return [];
        $rows = RgResort::query()
            ->where('status', 'published')
            ->orderByDesc('updated_at')
            ->limit(400)
            ->get(['id', 'name', 'slug', 'city', 'province', 'tagline', 'hero_path']);
        $items = [];
        foreach ($rows as $r) {
            $loc = trim(($r->city ?? '') . (($r->city && $r->province) ? ', ' : '') . ($r->province ?? ''));
            $items[] = [
                'type' => 'resort',
                'label' => $r->name,
                'sub' => $loc !== '' ? $loc : ($r->tagline ?? 'Resort'),
                'url' => url('/listing/' . $r->slug),
                'haystack' => mb_strtolower($r->name . ' ' . $loc . ' ' . ($r->tagline ?? '')),
                'volume' => 0,
                'image' => $r->hero_path ? asset('storage/' . $r->hero_path) : null,
            ];
        }
        return $items;
    }

    private function restaurants(): array
    {
        if (!Schema::hasTable('rg_restaurants')) return [];
        $rows = DB::table('rg_restaurants')
            ->leftJoin('rg_restaurant_listings', 'rg_restaurants.id', '=', 'rg_restaurant_listings.restaurant_id')
            ->leftJoin('rg_keywords', 'rg_restaurant_listings.keyword_id', '=', 'rg_keywords.id')
            ->where('rg_restaurants.status', 'published')
            ->select(
                'rg_restaurants.id',
                'rg_restaurants.name',
                'rg_restaurants.city',
                'rg_restaurants.cuisine',
                'rg_restaurants.hero_path',
                'rg_keywords.slug as keyword_slug'
            )
            ->orderBy('rg_restaurants.name')
            ->limit(800)
            ->get();
        $items = [];
        $seen = [];
        foreach ($rows as $r) {
            if (isset($seen[$r->id])) continue;
            $seen[$r->id] = true;
            $url = $r->keyword_slug ? url('/' . $r->keyword_slug) : url('/food-trip');
            $items[] = [
                'type' => 'restaurant',
                'label' => $r->name,
                'sub' => trim(($r->cuisine ?? '') . (($r->cuisine && $r->city) ? ' · ' : '') . ($r->city ?? '')),
                'url' => $url,
                'haystack' => mb_strtolower(($r->name ?? '') . ' ' . ($r->city ?? '') . ' ' . ($r->cuisine ?? '')),
                'volume' => 0,
                'image' => $r->hero_path ? asset('storage/' . $r->hero_path) : null,
            ];
        }
        return $items;
    }

    private function spots(): array
    {
        if (!Schema::hasTable('rg_tourist_spots')) return [];
        $rows = RgTouristSpot::query()
            ->where('status', 'published')
            ->whereNotNull('keyword_id')
            ->with(['media', 'keyword'])
            ->limit(800)
            ->get();
        $items = [];
        foreach ($rows as $s) {
            $url = $s->keyword ? url('/' . $s->keyword->slug) : url('/destinations');
            $loc = $s->location ?: ($s->region_label ?? '');
            $items[] = [
                'type' => 'spot',
                'label' => $s->name,
                'sub' => $loc !== '' ? $loc : 'Tourist spot',
                'url' => $url,
                'haystack' => mb_strtolower(($s->name ?? '') . ' ' . $loc . ' ' . ($s->region_label ?? '')),
                'volume' => 0,
                'image' => $s->media ? asset('storage/' . $s->media->path) : null,
            ];
        }
        return $items;
    }

    private function blog(): array
    {
        if (!Schema::hasTable('rg_blog_posts')) return [];
        $rows = RgBlogPost::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(60)
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_path']);
        $items = [];
        foreach ($rows as $p) {
            $items[] = [
                'type' => 'blog',
                'label' => $p->title,
                'sub' => $p->excerpt ? mb_substr(strip_tags($p->excerpt), 0, 80) : 'Blog post',
                'url' => url('/blog/' . $p->slug),
                'haystack' => mb_strtolower($p->title . ' ' . ($p->excerpt ?? '')),
                'volume' => 0,
                'image' => $p->cover_path ? asset('storage/' . $p->cover_path) : null,
            ];
        }
        return $items;
    }
}
