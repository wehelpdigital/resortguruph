<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Import91KeywordsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $inserted = 0;
        $updated = 0;
        $pagesCreated = 0;

        foreach ($this->rows() as $row) {
            $slug = Str::slug($row[0]);
            $cluster = $this->cluster($row[0]);

            $existing = DB::table('rg_keywords')->where('slug', $slug)->first();
            if ($existing) {
                DB::table('rg_keywords')->where('id', $existing->id)->update([
                    'search_volume_monthly' => $row[1],
                    'keyword_difficulty' => $row[2],
                    'cluster_tag' => $existing->cluster_tag ?: $cluster,
                    'base_price_gp' => max(100, intval($row[1] / 100)),
                    'updated_at' => $now,
                ]);
                $updated++;
                $keywordId = $existing->id;
            } else {
                $keywordId = DB::table('rg_keywords')->insertGetId([
                    'phrase' => $row[0],
                    'slug' => $slug,
                    'search_volume_monthly' => $row[1],
                    'keyword_difficulty' => $row[2],
                    'cluster_tag' => $cluster,
                    'intent' => 'commercial',
                    'status' => 'active',
                    'listing_capacity_top' => 10,
                    'base_price_gp' => max(100, intval($row[1] / 100)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }

            $hasPage = DB::table('rg_seo_pages')->where('keyword_id', $keywordId)->exists();
            if (!$hasPage) {
                DB::table('rg_seo_pages')->insert([
                    'keyword_id' => $keywordId,
                    'title' => ucwords($row[0]),
                    'meta_title' => ucwords($row[0]),
                    'meta_description' => '',
                    'h1' => ucwords($row[0]),
                    'is_published' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $pagesCreated++;
            }
        }

        $this->command->info("Inserted: $inserted  |  Updated: $updated  |  Placeholder pages created: $pagesCreated");
    }

    private function cluster(string $phrase): string
    {
        $p = strtolower($phrase);
        $map = [
            'visayas' => ['cebu', 'bohol', 'panglao', 'guimaras', 'iloilo', 'dumaguete', 'siquijor', 'bacolod', 'don salvador benedicto', 'dauin'],
            'mindanao' => ['davao', 'samal', 'gensan', 'general santos', 'kidapawan', 'zamboanga', 'glan'],
            'palawan' => ['palawan', 'el nido', 'puerto galera'],
            'bicol' => ['albay', 'sorsogon', 'naga', 'camarines'],
            'north-luzon' => ['pangasinan', 'bolinao', 'la union', 'hundred islands', 'bataan', 'tarlac', 'subic', 'zambales', 'nueva ecija', 'urdaneta', 'aurora', 'dingalan', 'morong'],
            'cavite' => ['cavite', 'amadeo', 'alfonso', 'dasma', 'imus', 'indang', 'naic', 'silang', 'bacoor'],
            'batangas' => ['batangas', 'calatagan', 'laiya', 'lipa', 'lobo', 'mabini', 'nasugbu', 'san juan batangas'],
            'rizal' => ['rizal', 'antipolo', 'binangonan', 'rodriguez', 'san mateo', 'taytay', 'tanay', 'marikina'],
            'laguna' => ['laguna', 'calamba', 'pansol', 'pagsanjan', 'nagcarlan', 'san pablo'],
            'pampanga' => ['pampanga', 'angeles', 'arayat'],
            'bulacan' => ['bulacan', 'pandi'],
            'quezon' => ['quezon', 'lucena', 'sariaya'],
            'metro-manila' => ['manila', 'taguig', 'makati'],
        ];
        foreach ($map as $tag => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($p, $kw)) return $tag;
            }
        }
        return 'other';
    }

    private function rows(): array
    {
        return [
            ['resort in albay', 1000, 20],
            ['resort in alfonso cavite', 1000, 17],
            ['resort in amadeo cavite', 1300, 25],
            ['resort in angeles pampanga', 1000, 14],
            ['resort in antipolo', 9900, 28],
            ['resort in antipolo private', 6600, 20],
            ['resort in arayat pampanga', 1000, 22],
            ['resort in bacolod', 1900, 21],
            ['resort in bacoor cavite', 1300, 22],
            ['resort in bataan', 4400, 31],
            ['resort in batangas', 2900, 26],
            ['resort in batangas city', 1600, 16],
            ['resort in batangas with pool and beach', 2900, 20],
            ['resort in binangonan rizal', 1300, 23],
            ['resort in bolinao', 2400, 24],
            ['resort in bulacan', 12100, 13],
            ['resort in calamba laguna', 1600, 22],
            ['resort in calatagan', 4400, 26],
            ['resort in calatagan batangas', 2900, 21],
            ['resort in cavite', 5400, 22],
            ['resort in cebu city', 4400, 31],
            ['resort in dasma', 1300, 19],
            ['resort in dauin', 1600, 22],
            ['resort in davao', 2400, 33],
            ['resort in davao city', 4400, 24],
            ['resort in dingalan aurora', 1000, 11],
            ['resort in don salvador benedicto', 2400, 22],
            ['resort in dumaguete', 1600, 29],
            ['resort in el nido', 5400, 29],
            ['resort in el nido palawan', 4400, 28],
            ['resort in gensan', 1300, 19],
            ['resort in glan', 1000, 16],
            ['resort in guimaras', 3600, 20],
            ['resort in guimaras island', 2400, 16],
            ['resort in hundred islands', 1000, 20],
            ['resort in iloilo', 2900, 20],
            ['resort in iloilo city', 1900, 19],
            ['resort in imus', 1300, 22],
            ['resort in imus cavite', 1000, 23],
            ['resort in indang cavite', 5400, 25],
            ['resort in kidapawan city', 1300, 27],
            ['resort in la union', 2400, 33],
            ['resort in laguna', 1600, 33],
            ['resort in laiya', 2900, 24],
            ['resort in lapu lapu', 2400, 19],
            ['resort in lapu lapu city', 3600, 20],
            ['resort in lipa', 2900, 19],
            ['resort in lipa batangas', 3600, 17],
            ['resort in lobo batangas', 6600, 25],
            ['resort in lucena city', 1300, 25],
            ['resort in mabini batangas', 1000, 23],
            ['resort in manila', 1600, 32],
            ['resort in marikina', 1300, 23],
            ['resort in morong bataan', 5400, 18],
            ['resort in naga', 1300, 17],
            ['resort in naga city', 2400, 14],
            ['resort in naga city camarines sur', 1600, 23],
            ['resort in nagcarlan laguna', 1900, 22],
            ['resort in naic cavite', 1000, 25],
            ['resort in nasugbu', 1300, 24],
            ['resort in nasugbu batangas', 9900, 16],
            ['resort in nueva ecija', 1900, 23],
            ['resort in pampanga', 4400, 24],
            ['resort in pandi bulacan', 1000, 22],
            ['resort in pangasinan', 2900, 14],
            ['resort in panglao bohol', 1600, 34],
            ['resort in pansol', 8100, 26],
            ['resort in puerto galera', 1900, 28],
            ['resort in quezon', 2400, 18],
            ['resort in quezon city', 1900, 23],
            ['resort in quezon province', 1900, 24],
            ['resort in rizal', 2900, 18],
            ['resort in rizal province', 2400, 18],
            ['resort in rodriguez rizal', 5400, 14],
            ['resort in samal island', 8100, 21],
            ['resort in san juan batangas', 1600, 17],
            ['resort in san mateo rizal', 1600, 19],
            ['resort in san pablo laguna', 2400, 27],
            ['resort in sariaya quezon', 1300, 22],
            ['resort in silang cavite', 1900, 23],
            ['resort in siquijor', 1900, 30],
            ['resort in sorsogon', 1300, 18],
            ['resort in subic', 5400, 27],
            ['resort in subic zambales', 3600, 32],
            ['resort in tagaytay', 2900, 27],
            ['resort in taguig', 1300, 21],
            ['resort in tanay', 5400, 20],
            ['resort in tarlac', 1300, 23],
            ['resort in taytay rizal', 1900, 23],
            ['resort in urdaneta city pangasinan', 1000, 20],
            ['resort in zamboanga', 1300, 18],
        ];
    }
}
