<?php

namespace App\Services;

use App\Models\RgKeyword;
use App\Support\RegionResolver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Builds a LOCATION-searchable index for a hub page (foods, activities,
 * buys, cultures). The typed location (e.g. "Cebu", "BGC", "Baguio") is
 * resolved to a region cluster, and every item is tagged with its region
 * plus that region's place aliases, so searching a place surfaces the items
 * from that part of the country.
 *
 * Each row matches the shape the inline typeahead expects:
 *   { type, label, sub, url, image, volume, haystack }
 *
 * Region signal per item, in order of confidence:
 *   1. A category-key hint for region-scoped categories (e.g. foods
 *      "visayas", cultures "cordillera").
 *   2. RegionResolver parsing a place name out of the item name + note.
 *   3. Otherwise nationwide (no place tokens; matches by text only).
 *
 * The foods hub additionally merges the real food inventory — published
 * "restaurant in {place}" keyword pages and published restaurants (with
 * cuisine) — since those carry the strongest location data.
 */
class HubLocationSearch
{
    private const HUB_URL = [
        'foods' => '/filipino-food-dishes-what-to-eat',
        'activities' => '/philippine-tourist-activities-adventures-what-to-do',
        'buys' => '/philippine-souvenirs-pasalubong-what-to-buy',
        'cultures' => '/philippine-tribes-ethnic-groups-cultures-to-meet',
    ];

    private const ITEM_TYPE = [
        'foods' => 'dish', 'activities' => 'activity', 'buys' => 'buy', 'cultures' => 'culture',
    ];

    /** Region-scoped categories → cluster hint. Broad categories parse instead. */
    private const CAT_REGION = [
        'foods' => ['visayas' => 'visayas', 'mindanao' => 'mindanao'],
        'cultures' => [
            'cordillera' => 'north-luzon', 'caraballo' => 'north-luzon',
            'mimaropa' => 'palawan', 'visayas' => 'visayas',
            'lumad' => 'mindanao', 'moro' => 'mindanao',
        ],
    ];

    /**
     * The top slider: one card per category ("Street Food & Offal Cuts, 18 dishes")
     * linking to that category. Its photo is an item's second photo, which the grid
     * below does not show, so the slider never repeats a card or a picture from the grid.
     * Shape matches dest_featured_slider (name, region = eyebrow, image, url).
     */
    public function featured(string $hub, array $categories, int $limit = 12): array
    {
        $hubUrl = self::HUB_URL[$hub] ?? '';
        $noun = ['buys' => 'finds', 'foods' => 'dishes', 'cultures' => 'groups', 'activities' => 'experiences'][$hub] ?? 'picks';
        $usable = fn ($src) => \App\Support\GuideHtml::usable((string) preg_replace('~^https?://[^/]+(?=/storage/)~i', '', (string) $src));
        $out = [];
        foreach ($categories as $cat) {
            if (count($out) >= $limit) break;
            $items = array_values(array_filter($cat['items'] ?? [], fn ($it) => count(array_filter((array) ($it['images'] ?? []), $usable)) > 0));
            if (!$items) continue;
            $gridPhotos = [];
            foreach ($items as $it) foreach ((array) ($it['images'] ?? []) as $img) if ($usable($img)) { $gridPhotos[\App\Support\GuideHtml::photoId((string) $img)] = true; break; }
            // an item's other photo, from the middle of the list first
            $order = array_merge(array_slice($items, (int) floor(count($items) / 3)), array_slice($items, 0, (int) floor(count($items) / 3)));
            $photo = '';
            foreach ($order as $it) {
                foreach (array_slice((array) ($it['images'] ?? []), 1) as $img) {
                    if ($usable($img) && !isset($gridPhotos[\App\Support\GuideHtml::photoId((string) $img)])) { $photo = (string) $img; break 2; }
                }
            }
            if ($photo === '') continue;
            $catKey = (string) ($cat['key'] ?? '');
            $out[] = [
                'name' => (string) ($cat['label'] ?? ''),
                'region' => count($items) . ' ' . $noun,
                'location' => '',
                'image' => $photo,
                'url' => $hubUrl . ($catKey !== '' ? '#cat-' . $catKey : ''),
                'slug' => $catKey,
            ];
        }
        return $out;
    }

    /**
     * A #Tags cloud built from the hub's item names (CamelCase hashtags),
     * each linking to its category section anchor. Shape: [{tag, url}].
     */
    public function tags(string $hub, array $categories, int $limit = 42): array
    {
        $hubUrl = self::HUB_URL[$hub] ?? '';
        $out = [];
        $seen = [];
        foreach ($categories as $cat) {
            $catKey = (string) ($cat['key'] ?? '');
            $anchor = $hubUrl . ($catKey !== '' ? '#cat-' . $catKey : '');
            foreach (($cat['items'] ?? []) as $it) {
                if (count($out) >= $limit) return $out;
                $name = trim((string) ($it['name'] ?? ''));
                if ($name === '') continue;
                $words = preg_split('/[^a-z0-9]+/i', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $tag = '';
                foreach ($words as $w) $tag .= ucfirst(mb_strtolower($w));
                if ($tag === '') continue;
                $key = mb_strtolower($tag);
                if (isset($seen[$key])) continue;
                $seen[$key] = true;
                $out[] = ['tag' => $tag, 'url' => $anchor];
            }
        }
        return $out;
    }

    public function build(string $hub, array $categories): array
    {
        // Cache the assembled index briefly; item data is hardcoded and
        // keyword/restaurant rows change rarely.
        return Cache::remember('rg.hubsearch.' . $hub . '.v1', 600, function () use ($hub, $categories) {
            return $this->assemble($hub, $categories);
        });
    }

    private function assemble(string $hub, array $categories): array
    {
        $hubUrl = self::HUB_URL[$hub] ?? '';
        $type = self::ITEM_TYPE[$hub] ?? 'item';
        $placeTokens = $this->placeTokensByCluster();
        $out = [];

        foreach ($categories as $cat) {
            $catKey = (string) ($cat['key'] ?? '');
            $catLabel = (string) ($cat['label'] ?? '');
            $hint = self::CAT_REGION[$hub][$catKey] ?? null;
            $anchor = $hubUrl . ($catKey !== '' ? '#cat-' . $catKey : '');
            foreach (($cat['items'] ?? []) as $it) {
                $name = trim((string) ($it['name'] ?? ''));
                $note = trim((string) ($it['note'] ?? $it['description'] ?? ''));
                if ($name === '') continue;
                $region = RegionResolver::resolve($hint, $name . ' ' . $note);
                $regionLabel = $region !== 'other' ? RegionResolver::label($region) : '';
                $tokens = $region !== 'other' ? ($placeTokens[$region] ?? '') : '';
                $imgs = $it['images'] ?? [];
                $img = is_array($imgs) && count($imgs) ? (string) $imgs[0] : '';
                $out[] = [
                    'type' => $type,
                    'label' => $name,
                    'sub' => $regionLabel !== '' ? ($catLabel . ' · ' . $regionLabel) : $catLabel,
                    'url' => $anchor,
                    'image' => $img,
                    'volume' => 0,
                    'haystack' => mb_strtolower(trim($name . ' ' . $note . ' ' . $catLabel . ' ' . $regionLabel . ' ' . $tokens)),
                ];
            }
        }

        if ($hub === 'foods') {
            $out = array_merge($out, $this->foodGuides(), $this->restaurants());
        }
        return $out;
    }

    /** Invert RegionResolver::placeMap() to cluster => "place1 place2 …". */
    private function placeTokensByCluster(): array
    {
        $map = [];
        foreach (RegionResolver::placeMap() as $token => $cluster) {
            $map[$cluster] = ($map[$cluster] ?? '') . ' ' . $token;
        }
        foreach (RegionResolver::clusters() as $key => $label) {
            $map[$key] = ($map[$key] ?? '') . ' ' . mb_strtolower($label);
        }
        return array_map('trim', $map);
    }

    /** Published "restaurant in {place}" keyword pages. */
    private function foodGuides(): array
    {
        if (!Schema::hasTable('rg_keywords')) return [];
        try {
            $kws = RgKeyword::query()->where('category', 'food')
                ->whereHas('seoPages', fn ($q) => $q->where('is_published', true))
                ->orderByDesc('search_volume_monthly')->limit(300)
                ->get(['phrase', 'slug', 'cluster_tag', 'search_volume_monthly']);
        } catch (\Throwable $e) {
            return [];
        }
        $out = [];
        foreach ($kws as $kw) {
            $region = RegionResolver::resolve((string) $kw->cluster_tag, (string) $kw->phrase);
            $regionLabel = $region !== 'other' ? RegionResolver::label($region) : '';
            $vol = (int) $kw->search_volume_monthly;
            $out[] = [
                'type' => 'guide',
                'label' => $kw->phrase,
                'sub' => trim($regionLabel . ($vol ? ' · ' . number_format($vol) . ' monthly searches' : '')),
                'url' => url('/' . $kw->slug),
                'image' => '',
                'volume' => $vol,
                'haystack' => mb_strtolower($kw->phrase . ' ' . $regionLabel),
            ];
        }
        return $out;
    }

    /** Published restaurants (with cuisine), linked to their location keyword. */
    private function restaurants(): array
    {
        if (!Schema::hasTable('rg_restaurants')) return [];
        try {
            $rows = DB::table('rg_restaurants as r')
                ->leftJoin('rg_restaurant_listings as rl', 'rl.restaurant_id', '=', 'r.id')
                ->leftJoin('rg_keywords as k', 'k.id', '=', 'rl.keyword_id')
                ->where('r.status', 'published')
                ->groupBy('r.id', 'r.name', 'r.city', 'r.province', 'r.cuisine', 'r.hero_path')
                ->get([
                    'r.id', 'r.name', 'r.city', 'r.province', 'r.cuisine', 'r.hero_path',
                    DB::raw('MIN(k.slug) as kslug'),
                ]);
        } catch (\Throwable $e) {
            return [];
        }
        $out = [];
        foreach ($rows as $r) {
            $loc = trim(implode(' ', array_filter([$r->city, $r->province])));
            $cuisine = trim((string) $r->cuisine);
            $out[] = [
                'type' => 'restaurant',
                'label' => $r->name,
                'sub' => trim(($cuisine !== '' ? $cuisine : 'Restaurant') . ($r->city ? ' · ' . $r->city : '')),
                'url' => $r->kslug ? url('/' . $r->kslug) : url('/food-trip'),
                'image' => $r->hero_path ? '/storage/' . ltrim($r->hero_path, '/') : '',
                'volume' => 0,
                'haystack' => mb_strtolower($r->name . ' ' . $loc . ' ' . $cuisine),
            ];
        }
        return $out;
    }
}
