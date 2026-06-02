<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Imports every spot defined in destinations.php into rg_tourist_spots.
 *
 * Resolution rules:
 * - keyword_id : first keyword whose phrase contains the destination's name
 *                (case-insensitive). Null if no match — spot still seeded so
 *                an admin can wire it up later in the builder.
 * - media_id   : looks up an rg_media row at the path SpotImageSeeder writes
 *                to. Null if no image exists yet.
 * - status     : 'published' when both media + keyword resolved, else 'draft'.
 *
 * Idempotent — re-running skips spots whose slug already exists.
 *
 * After the bulk import, applies a curated list of featured_order values to
 * the 15 spots that DestinationsController::buildFeaturedSpots() used to
 * hard-code, so the existing carousel order is preserved.
 */
class RgTouristSpotsSeeder extends Seeder
{
    private array $clusterLabels = [
        'metro-manila' => 'Metro Manila',
        'rizal'        => 'Rizal',
        'cavite'       => 'Cavite',
        'batangas'     => 'Batangas',
        'laguna'       => 'Laguna',
        'bulacan'      => 'Bulacan',
        'pampanga'     => 'Pampanga',
        'quezon'       => 'Quezon',
        'north-luzon'  => 'North Luzon',
        'bicol'        => 'Bicol',
        'visayas'      => 'Visayas',
        'mindanao'     => 'Mindanao',
        'palawan'      => 'Palawan',
        'other'        => 'Other',
    ];

    /**
     * Curated featured-spot list — mirrors the old hand-coded array in
     * DestinationsController so the carousel keeps its existing order.
     * Match key is (destination_key, spot_name_substring).
     */
    private array $featured = [
        ['alaminos-hundred-islands', 'Hundred Islands',  'Alaminos, Pangasinan',  1],
        ['vigan',                   'Calle Crisologo',   'Vigan, Ilocos Sur',     2],
        ['tagaytay',                "People's Park",     'Tagaytay, Cavite',      3],
        ['boracay',                 'White Beach',       'Boracay, Aklan',        4],
        ['el-nido',                 'Big Lagoon',        'El Nido, Palawan',      5],
        ['albay-legazpi',           'Cagsawa Ruins',     'Daraga, Albay',         6],
        ['cebu-city',               "Magellan's Cross",  'Cebu City',             7],
        ['kidapawan',               'Mt. Apo',           'Davao',                 8],
        ['albay-legazpi',           'Mayon',             'Legazpi, Albay',        9],
        ['la-union',                'San Juan surf',     'San Juan, La Union',    10],
        ['la-union',                'Tangadan Falls',    'La Union',              11],
        ['ilocos-norte',            'Bangui windmills',  'Bangui, Ilocos Norte',  12],
        ['bolinao',                 'Patar Beach',       'Bolinao, Pangasinan',   13],
        ['manaoag',                 'Manaoag',           'Pangasinan',            14],
        ['dasol',                   'Tambobong',         'Dasol, Pangasinan',     15],
    ];

    public function run(): void
    {
        $destinations = require database_path('data/destinations.php');

        $keywords    = DB::table('rg_keywords')->get(['id', 'phrase', 'cluster_tag']);
        $mediaByPath = DB::table('rg_media')
            ->where(function ($q) {
                $q->where('path', 'like', 'rg-media/spots/%')
                  ->orWhere('path', 'like', 'rg-media/destinations/%');
            })
            ->get(['id', 'path'])
            ->keyBy('path');

        $now = now();
        $inserted = 0; $skipped = 0; $withImage = 0; $withKeyword = 0;

        foreach ($destinations as $destKey => $info) {
            if ($destKey === '_default') continue;
            $spots    = $info['spots'] ?? [];
            $destName = $info['name'] ?? null;
            $cluster  = $info['cluster'] ?? 'other';
            $region   = $this->clusterLabels[$cluster] ?? Str::title(str_replace('-', ' ', $cluster));
            $keywordId = $this->resolveKeywordId($destName, $cluster, $keywords);

            foreach ($spots as $spot) {
                $spotName = trim($spot['name'] ?? '');
                if ($spotName === '') continue;

                $slug = $this->buildSlug($destKey, $spotName);
                if (DB::table('rg_tourist_spots')->where('slug', $slug)->exists()) {
                    $skipped++;
                    continue;
                }

                $mediaId = $this->resolveMediaId($destKey, $spotName, $mediaByPath);

                DB::table('rg_tourist_spots')->insert([
                    'name'            => $spotName,
                    'slug'            => $slug,
                    'location'        => $destName,
                    'region_label'    => $region,
                    'cluster_tag'     => $cluster,
                    'destination_key' => $destKey,
                    'keyword_id'      => $keywordId,
                    'media_id'        => $mediaId,
                    'description'    => $spot['desc'] ?? null,
                    'featured_order' => null,
                    'status'         => ($mediaId && $keywordId) ? 'published' : 'draft',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
                $inserted++;
                if ($mediaId) $withImage++;
                if ($keywordId) $withKeyword++;
            }
        }

        $featuredApplied = $this->applyFeaturedOrder();

        $this->command->info("Inserted: $inserted | Skipped (existing): $skipped");
        $this->command->info("With image: $withImage | With keyword: $withKeyword");
        $this->command->info("Featured order applied: $featuredApplied / " . count($this->featured));
    }

    private function buildSlug(string $destKey, string $spotName): string
    {
        $base = Str::slug($destKey . '-' . $spotName);
        return substr($base, 0, 191);
    }

    private function resolveKeywordId(?string $destName, string $cluster, $keywords): ?int
    {
        if (!$destName) return null;
        $destLower = mb_strtolower($destName);
        // Prefer a keyword inside the same cluster — avoids "Cebu" matching a
        // North Luzon spot just because some unrelated phrase contains it.
        $sameCluster = $keywords->where('cluster_tag', $cluster);
        foreach ($sameCluster as $kw) {
            if (mb_stripos($kw->phrase, $destLower) !== false) return (int) $kw->id;
        }
        // Fall back to any cluster.
        foreach ($keywords as $kw) {
            if (mb_stripos($kw->phrase, $destLower) !== false) return (int) $kw->id;
        }
        return null;
    }

    private function resolveMediaId(string $destKey, string $spotName, $mediaByPath): ?int
    {
        // Mirror SpotImageSeeder's naming: rg-media/spots/{destKey}-{slug-of-spot-name}.jpg
        $spotSlug = substr(Str::slug($spotName), 0, 50);
        $candidates = [
            "rg-media/spots/{$destKey}-{$spotSlug}.jpg",
            "rg-media/destinations/{$destKey}-1.jpg",
            "rg-media/destinations/{$destKey}-2.jpg",
        ];
        foreach ($candidates as $path) {
            if ($mediaByPath->has($path)) return (int) $mediaByPath->get($path)->id;
        }
        return null;
    }

    private function applyFeaturedOrder(): int
    {
        $applied = 0;
        foreach ($this->featured as [$destKey, $nameContains, $location, $order]) {
            $row = DB::table('rg_tourist_spots')
                ->where('destination_key', $destKey)
                ->where('name', 'like', '%' . $nameContains . '%')
                ->first();
            if (!$row) continue;
            DB::table('rg_tourist_spots')->where('id', $row->id)->update([
                'featured_order' => $order,
                'location'       => $location,
                'status'         => 'published',
                'updated_at'     => now(),
            ]);
            $applied++;
        }
        return $applied;
    }
}
