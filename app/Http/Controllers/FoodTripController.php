<?php

namespace App\Http\Controllers;

use App\Models\RgKeyword;
use App\Models\RgRestaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoodTripController extends Controller
{
    /**
     * The Food Trip top-level index — mirrors /destinations but for
     * restaurant/food-category keyword pages.
     */
    public function index()
    {
        $foodKeywords = RgKeyword::query()
            ->where('category', 'food')
            ->whereHas('seoPages', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->get();

        $groups = $foodKeywords->groupBy('cluster_tag')->map(function ($items, $cluster) {
            return [
                'cluster_tag' => $cluster,
                'name'        => DestinationsController::clusterMetadata()[$cluster]['name'] ?? ucfirst($cluster),
                'count'       => $items->count(),
                'total_volume'=> $items->sum('search_volume_monthly'),
                'keywords'    => $items,
            ];
        })
        ->sortByDesc('total_volume')
        ->values();

        $featuredRestaurants = RgRestaurant::query()
            ->where('status', 'published')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $stats = [
            'total_keywords'     => $foodKeywords->count(),
            'total_areas'        => $groups->count(),
            'featured_count'     => $featuredRestaurants->count(),
        ];

        $searchIndex = $this->buildSearchIndex($groups);

        return view('food-trip.index', compact('foodKeywords', 'groups', 'featuredRestaurants', 'stats', 'searchIndex'));
    }

    /**
     * Typeahead index for the food-trip page. Three result types:
     *   region       — cluster groupings (Metro Manila, Visayas, etc.).
     *                  Url anchors to that cluster's section on the same page.
     *   destination  — every published food keyword page.
     *   restaurant   — every published restaurant. Url links to the keyword
     *                  page the restaurant is listed on (rg_restaurant_listings
     *                  join); falls back to /food-trip when not yet listed.
     */
    private function buildSearchIndex($groups): array
    {
        $idx = [];

        foreach ($groups as $g) {
            $idx[] = [
                'type'     => 'region',
                'label'    => $g['name'],
                'sub'      => $g['count'] . ' restaurant guide' . ($g['count'] === 1 ? '' : 's'),
                'url'      => url('/food-trip') . '#cluster-' . Str::slug($g['cluster_tag']),
                'haystack' => mb_strtolower($g['name'] . ' ' . $g['cluster_tag']),
                'volume'   => (int) $g['total_volume'],
            ];
        }

        foreach ($groups as $g) {
            foreach ($g['keywords'] as $kw) {
                $cleanLoc = trim(preg_replace(
                    '/^(restaurant|restaurants|where\s+to\s+eat|food)\s+(in|at|near)\s+/i',
                    '',
                    $kw->phrase
                ));
                $idx[] = [
                    'type'     => 'destination',
                    'label'    => $kw->phrase,
                    'sub'      => $g['name'] . ' · ' . number_format($kw->search_volume_monthly) . ' people search this monthly',
                    'url'      => url('/' . $kw->slug),
                    'haystack' => mb_strtolower($kw->phrase . ' ' . $g['name'] . ' ' . $cleanLoc),
                    'volume'   => (int) $kw->search_volume_monthly,
                ];
            }
        }

        // Pull every published restaurant once, plus one linked keyword slug
        // each (the first by listing order). One restaurant → one search row.
        $restaurants = DB::table('rg_restaurants as r')
            ->leftJoin(DB::raw(
                '(SELECT rl.restaurant_id, MIN(rl.id) AS pick_id FROM rg_restaurant_listings rl GROUP BY rl.restaurant_id) AS picks'
            ), 'picks.restaurant_id', '=', 'r.id')
            ->leftJoin('rg_restaurant_listings as rl', 'rl.id', '=', 'picks.pick_id')
            ->leftJoin('rg_keywords as k', 'k.id', '=', 'rl.keyword_id')
            ->where('r.status', 'published')
            ->select(
                'r.id', 'r.name', 'r.slug', 'r.city', 'r.cuisine', 'r.hero_path',
                'k.slug as keyword_slug', 'k.phrase as keyword_phrase'
            )
            ->get();

        foreach ($restaurants as $r) {
            $sub = trim(implode(' · ', array_filter([
                $r->cuisine ?: null,
                $r->city ?: null,
            ])));
            $href = $r->keyword_slug
                ? url('/' . $r->keyword_slug) . '#restaurant-' . $r->id
                : url('/food-trip');

            $idx[] = [
                'type'     => 'restaurant',
                'label'    => $r->name,
                'sub'      => $sub !== '' ? $sub : 'Restaurant',
                'url'      => $href,
                'haystack' => mb_strtolower(
                    $r->name . ' ' . ($r->city ?? '') . ' ' . ($r->cuisine ?? '') . ' ' . ($r->keyword_phrase ?? '')
                ),
                'volume'   => 0,
                'image'    => $r->hero_path ? asset('storage/' . $r->hero_path) : null,
            ];
        }

        return $idx;
    }
}
