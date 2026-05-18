<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RgKeywordsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['phrase' => 'resort in Bulacan', 'vol' => 12100, 'kd' => 13, 'cluster' => 'luzon-resorts'],
            ['phrase' => 'hotel in Cebu', 'vol' => 14800, 'kd' => 25, 'cluster' => 'visayas-city'],
            ['phrase' => 'resort in Tagaytay', 'vol' => 9900, 'kd' => 18, 'cluster' => 'luzon-resorts'],
            ['phrase' => 'beach resort in Palawan', 'vol' => 8100, 'kd' => 22, 'cluster' => 'visayas-beach'],
            ['phrase' => 'resort in Batangas', 'vol' => 7300, 'kd' => 16, 'cluster' => 'luzon-resorts'],
            ['phrase' => 'airbnb in Manila', 'vol' => 6600, 'kd' => 21, 'cluster' => 'metro-stays'],
            ['phrase' => 'resort in Laguna', 'vol' => 5400, 'kd' => 12, 'cluster' => 'luzon-resorts'],
            ['phrase' => 'hotel in Boracay', 'vol' => 6100, 'kd' => 28, 'cluster' => 'visayas-beach'],
            ['phrase' => 'resort in Pampanga', 'vol' => 4500, 'kd' => 11, 'cluster' => 'luzon-resorts'],
            ['phrase' => 'beach resort in La Union', 'vol' => 4100, 'kd' => 14, 'cluster' => 'luzon-beach'],
        ];

        $now = now();
        foreach ($rows as $r) {
            $slug = Str::slug($r['phrase']);
            DB::table('rg_keywords')->updateOrInsert(
                ['slug' => $slug],
                [
                    'phrase' => $r['phrase'],
                    'slug' => $slug,
                    'search_volume_monthly' => $r['vol'],
                    'keyword_difficulty' => $r['kd'],
                    'cluster_tag' => $r['cluster'],
                    'intent' => 'commercial',
                    'status' => 'active',
                    'listing_capacity_top' => 10,
                    'base_price_gp' => max(100, intval($r['vol'] / 100)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
