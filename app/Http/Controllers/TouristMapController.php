<?php

namespace App\Http\Controllers;

use App\Models\RgFiesta;
use App\Models\RgKeyword;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Tourist Map hub at /philippine-tourist-map.
 *
 * Loads every geocodable entity on the site (fiestas, destination
 * keywords, food keywords, hand-pinned activities, hand-pinned
 * regional dishes) into a single JSON dump. The client renders a
 * Leaflet map of the Philippines and, on drag-to-draw-circle,
 * filters the dump by haversine distance to populate a modal.
 *
 * Server-side prep is cached for 1 hour because the inputs change
 * rarely and the join logic is otherwise repeated on every page
 * hit.
 */
class TouristMapController extends Controller
{
    public function index()
    {
        $points = Cache::remember('tourist-map.points.v1', 3600, function () {
            return $this->buildPoints();
        });

        // Quick summary by type for the page footer.
        $countsByType = [];
        foreach ($points as $p) {
            $countsByType[$p['type']] = ($countsByType[$p['type']] ?? 0) + 1;
        }

        return view('tourist-map.index', [
            'points' => $points,
            'totalCount' => count($points),
            'countsByType' => $countsByType,
        ]);
    }

    /**
     * Walks every data source and emits a flat list of geocoded
     * points. Every point has at minimum {id, type, name, lat, lng,
     * city, url, icon}. The `type` controls the modal section the
     * item lands in client-side.
     */
    private function buildPoints(): array
    {
        $places = require database_path('data/ph_places.php');
        $locations = require database_path('data/tourist_map_locations.php');
        $points = [];

        // Fiestas: rg_fiestas.city_or_town -> place key
        $fiestas = RgFiesta::query()
            ->where('is_published', true)
            ->get();
        foreach ($fiestas as $fiesta) {
            $key = $this->resolveFiestaPlaceKey($fiesta, $places);
            if (!$key || !isset($places[$key])) continue;
            $coords = $places[$key];
            $points[] = [
                'id' => 'fiesta:' . $fiesta->slug,
                'type' => 'fiesta',
                'name' => $fiesta->name,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'city' => $coords['label'],
                'url' => '/fiestas/' . $fiesta->slug,
                'icon' => '🎉',
            ];
        }

        // Destination + restaurant keyword pages
        $keywords = RgKeyword::query()
            ->whereIn('category', ['resort', 'food'])
            ->whereHas('seoPages', fn ($q) => $q->where('is_published', true))
            ->get(['id', 'slug', 'phrase', 'category', 'cluster_tag']);
        foreach ($keywords as $keyword) {
            $key = $this->resolveKeywordPlaceKey($keyword, $places);
            if (!$key || !isset($places[$key])) continue;
            $coords = $places[$key];
            $isFood = $keyword->category === 'food';
            $points[] = [
                'id' => ($isFood ? 'restaurant:' : 'destination:') . $keyword->slug,
                'type' => $isFood ? 'restaurant' : 'destination',
                'name' => Str::title($keyword->phrase),
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'city' => $coords['label'],
                'url' => '/' . $keyword->slug,
                'icon' => $isFood ? '🍽️' : '🏖️',
            ];
        }

        // Hand-pinned activities (only the ones with a clear single
        // primary location — generic activities like "trail running"
        // happen everywhere and would clutter the map).
        foreach ($locations['activities'] as $slug => $entry) {
            $key = $entry['place'];
            if (!isset($places[$key])) continue;
            $coords = $places[$key];
            $points[] = [
                'id' => 'activity:' . $slug,
                'type' => 'activity',
                'name' => $entry['name'],
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'city' => $coords['label'],
                'url' => '/philippine-tourist-activities-adventures-what-to-do#cat-' . $entry['category'],
                'icon' => '🎪',
            ];
        }

        // Hand-pinned regional dishes
        foreach ($locations['foods'] as $slug => $entry) {
            $key = $entry['place'];
            if (!isset($places[$key])) continue;
            $coords = $places[$key];
            $points[] = [
                'id' => 'food:' . $slug,
                'type' => 'food',
                'name' => $entry['name'],
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'city' => $coords['label'],
                'url' => '/filipino-food-dishes-what-to-eat#cat-' . $entry['category'],
                'icon' => '🥘',
            ];
        }

        return $points;
    }

    /**
     * Resolves a fiesta's city_or_town to a place key in ph_places.
     * Tries the slugged city first, then falls back to the slugged
     * province, then to the region cluster (e.g. visayas, mindanao).
     */
    private function resolveFiestaPlaceKey($fiesta, array $places): ?string
    {
        $candidates = [
            Str::slug((string) $fiesta->city_or_town),
            Str::slug((string) $fiesta->province),
            (string) $fiesta->region_cluster,
        ];
        foreach ($candidates as $candidate) {
            if ($candidate && isset($places[$candidate])) {
                return $candidate;
            }
        }
        return null;
    }

    /**
     * Resolves a keyword's cluster_tag (or last word of its phrase)
     * to a place key in ph_places.
     */
    private function resolveKeywordPlaceKey($keyword, array $places): ?string
    {
        if ($keyword->cluster_tag && isset($places[$keyword->cluster_tag])) {
            return $keyword->cluster_tag;
        }
        // Fallback: take the last word of the phrase (e.g. "resort
        // in Cebu" -> "cebu") and try as a place key.
        $words = preg_split('/\s+/', strtolower($keyword->phrase));
        $last = $words[count($words) - 1] ?? null;
        if ($last && isset($places[$last])) {
            return $last;
        }
        return null;
    }
}
