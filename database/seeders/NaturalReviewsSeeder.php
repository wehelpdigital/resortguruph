<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Regenerates the bulk-positive review set with natural-sounding text that
 * references the destination by NAME (Tagaytay, Iloilo) rather than the SEO
 * keyword phrase ("resort in iloilo"), and pulls a real spot + real food from
 * destinations.php so each review reads like an actual guest comment.
 */
class NaturalReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $dests = require database_path('data/destinations.php');
        $slugMap = $this->slugMap();
        $clusterDef = [
            'rizal' => 'antipolo', 'cavite' => 'tagaytay', 'bulacan' => 'bulacan-province',
            'pampanga' => 'pampanga-province', 'batangas' => 'batangas-city', 'laguna' => 'pansol',
            'quezon' => 'lucena', 'bicol' => 'albay-legazpi', 'north-luzon' => 'la-union',
            'metro-manila' => 'manila', 'mindanao' => 'davao-city', 'visayas' => 'cebu-city',
            'palawan' => 'el-nido',
        ];

        $reviewerNames = ['Jonathan Cruz', 'Hannah Reyes', 'Patricia delos Santos', 'Mark Anthony Lim', 'Joan Villaruel', 'Carlo Mendoza', 'Aileen Bautista', 'Renzo Aquino', 'Sheryl Magno', 'Jessa Ramirez', 'Daniel Pascual', 'Rina Sandoval', 'Bryan Tan', 'Carmela Yulo', 'Aldous Cabrera', 'Ynna Domingo', 'Edwin Castillo', 'Mara Hernandez', 'Kim Esguerra', 'Liza Rivera'];
        $cities = ['Quezon City', 'Makati', 'Pasig', 'Mandaluyong', 'Marikina', 'Taguig', 'Cebu City', 'Davao City', 'Iloilo City', 'Baguio', 'Antipolo', 'Caloocan'];

        // Pools sized so 8 reviews per keyword can each get a unique opener,
        // body, and closer with no repeats inside a single page.
        $openers = [
            'We came up to %loc% for a long weekend and the stay was a highlight.',
            'Booked this for a quick getaway to %loc% and the photos held up to the place.',
            'Family of six in %loc% last month, no complaints from the kids.',
            'Spent two nights in %loc% and would honestly stay longer next time.',
            'Our anniversary weekend in %loc% ended up better than planned.',
            'Drove down to %loc% on a Saturday morning, smooth check-in by lunch.',
            'Friends had been hyping %loc% for months, finally got to see why.',
            'Quick escape from Manila to %loc% and totally worth the trip.',
            'Mid-week stay in %loc% when the rates dipped a bit, great call.',
            'First time taking the parents to %loc%, they were the ones who did not want to leave.',
            'Did a barkada trip to %loc% and the group chat is still talking about it.',
            'Solo work-from-anywhere week in %loc% turned out way better than I planned.',
            'Wanted somewhere chill near Manila and %loc% fit the bill perfectly.',
            'Reunion weekend in %loc% with the cousins, exactly what we needed.',
            'Booked last-minute on a Thursday for a Friday check-in in %loc%, no regrets.',
            'My wife picked %loc% for our babymoon and the timing was spot-on.',
            'Honestly, %loc% has been on my list for years and the visit lived up to it.',
            'After a stressful month at work, two nights in %loc% reset everything.',
        ];
        $bodies = [
            'Pool area was clean and quiet even on a Saturday afternoon.',
            'Staff went out of their way to recommend a couple of spots we would have missed.',
            'Breakfast was generous, and the menu switched up on the second morning.',
            'Room was way bigger than the photos suggested, and the bed was honestly great.',
            'Walking distance to %spot%, which made our morning plans easy.',
            'Tried %food% at the place they pointed us to, hard to top after that.',
            'Did the %spot% side trip and still made it back for sunset by the pool.',
            'View from the balcony alone was sulit, woke up early just to sit out there.',
            'Aircon was strong, water pressure was good, Wi-Fi held up for our work calls.',
            'Loved that we could bring outside food, no corkage drama.',
            'Hot shower, soft towels, clean linens, the basics done right.',
            'The lola who runs the place gave us a hand-drawn map of where to eat, gold.',
            'Late check-out on a Sunday saved our drive home, the host did not even ask why.',
            'They have a small library of board games for guests, which kept the kids off screens.',
            'Front desk arranged a tricycle for our %spot% run, way easier than figuring it out solo.',
            'Coffee in the morning came with a quiet view, that combo was the entire weekend for me.',
            'Pet-friendly without any extra fuss, our dog had a better time than we did.',
            'Even with a rainy Saturday, the indoor common area meant we never felt cooped up.',
        ];
        $closers = [
            'Coming back for sure, probably with more friends next time.',
            'Sulit promise, would book again.',
            'Already telling friends to check this place out.',
            'Five stars, no hesitation.',
            'Solid choice if you are doing %loc% as a weekend trip.',
            'Easy to plan around, would do it again on the next long weekend.',
            'Booked our return already.',
            'Petmalu, no other word for it.',
            'Honestly the kind of place I will gatekeep from group chats a little bit.',
            'My benchmark for weekend stays in %loc% now.',
            'Doing the same trip next quarter with the in-laws.',
            'Worth every minute of the drive.',
            'Will recommend to anyone who asks me about %loc% from now on.',
            'Bookmarking the host\'s Messenger so I do not have to search again.',
            'Already saving the date for a return visit before Christmas.',
            'Solid 9 out of 10, only docking a point because we wanted more time.',
            'If you are on the fence, do it.',
            'No notes from us, would book this exact stay again.',
        ];

        DB::table('rg_destination_reviews')->where('status', 'published')->delete();

        $keywords = DB::table('rg_keywords')->where('status', 'active')->get(['id', 'phrase', 'slug', 'cluster_tag']);
        $now = now();
        $rows = [];

        foreach ($keywords as $kw) {
            $seed = abs(crc32($kw->phrase));
            $destKey = $slugMap[$kw->slug] ?? ($clusterDef[$kw->cluster_tag] ?? 'tagaytay');
            $dest = $dests[$destKey] ?? null;
            $location = $dest['name'] ?? ucwords(preg_replace('/^(resort in|hotel in|airbnb in|beach resort in)\s+/i', '', $kw->phrase));
            $spots = $dest['spots'] ?? [];
            $foods = $dest['food'] ?? [];

            // Step by 1 so no two reviews on the same keyword share an opener,
            // body, or closer (with 18-entry pools and 8 reviews per keyword).
            for ($i = 0; $i < 8; $i++) {
                $opener = $openers[($seed + $i) % count($openers)];
                $body = $bodies[($seed + $i + 4) % count($bodies)];
                $closer = $closers[($seed + $i + 9) % count($closers)];

                $spot = !empty($spots) ? $spots[($seed + $i * 2) % count($spots)]['name'] : 'the town center';
                $food = !empty($foods) ? $this->dishName($foods[($seed + $i * 4) % count($foods)]) : 'the local food';

                $text = $opener . ' ' . $body . ' ' . $closer;
                $text = str_replace(['%loc%', '%spot%', '%food%'], [$location, $spot, $food], $text);

                $rows[] = [
                    'keyword_id' => $kw->id,
                    'reviewer_name' => $reviewerNames[($seed + $i * 7) % count($reviewerNames)],
                    'reviewer_location' => $cities[($seed + $i * 11) % count($cities)],
                    'reviewer_avatar' => 'https://i.pravatar.cc/200?img=' . (1 + (($seed + $i) % 70)),
                    'rating' => 4 + (($seed + $i) % 2),
                    'review_text' => $text,
                    'review_date' => $now->copy()->subDays(($seed + $i * 9) % 240)->format('Y-m-d'),
                    'is_featured' => $i === 0 ? 1 : 0,
                    'status' => 'published',
                    'sort_order' => $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('rg_destination_reviews')->insert($chunk);
        }

        $this->command->info('Natural reviews generated: ' . count($rows));
    }

    private function dishName(string $food): string
    {
        if (preg_match('/^([^(]+)\(/', $food, $m)) return trim($m[1]);
        foreach ([' at ', ' from ', ' with ', ' along ', ' by '] as $sep) {
            $pos = mb_stripos($food, $sep);
            if ($pos !== false && $pos > 0) return trim(mb_substr($food, 0, $pos));
        }
        return trim($food);
    }

    private function slugMap(): array
    {
        return [
            'resort-in-antipolo' => 'antipolo', 'resort-in-antipolo-private' => 'antipolo',
            'resort-in-tagaytay' => 'tagaytay', 'resort-in-cavite' => 'tagaytay',
            'resort-in-alfonso-cavite' => 'alfonso', 'resort-in-amadeo-cavite' => 'amadeo',
            'resort-in-bacoor-cavite' => 'bacoor', 'resort-in-dasma' => 'dasmarinas',
            'resort-in-imus' => 'imus', 'resort-in-imus-cavite' => 'imus',
            'resort-in-indang-cavite' => 'indang', 'resort-in-naic-cavite' => 'naic',
            'resort-in-silang-cavite' => 'silang',
            'resort-in-bulacan' => 'bulacan-province', 'resort-in-pandi-bulacan' => 'pandi',
            'resort-in-pampanga' => 'pampanga-province', 'resort-in-angeles-pampanga' => 'angeles',
            'resort-in-arayat-pampanga' => 'arayat',
            'resort-in-batangas' => 'batangas-city', 'resort-in-batangas-city' => 'batangas-city',
            'resort-in-batangas-with-pool-and-beach' => 'laiya',
            'resort-in-calatagan' => 'calatagan', 'resort-in-calatagan-batangas' => 'calatagan',
            'resort-in-laiya' => 'laiya', 'resort-in-san-juan-batangas' => 'laiya',
            'resort-in-lipa' => 'lipa', 'resort-in-lipa-batangas' => 'lipa',
            'resort-in-lobo-batangas' => 'lobo', 'resort-in-mabini-batangas' => 'anilao-mabini',
            'resort-in-nasugbu' => 'nasugbu', 'resort-in-nasugbu-batangas' => 'nasugbu',
            'resort-in-laguna' => 'pansol', 'resort-in-pansol' => 'pansol',
            'resort-in-calamba-laguna' => 'calamba', 'resort-in-san-pablo-laguna' => 'san-pablo',
            'resort-in-nagcarlan-laguna' => 'nagcarlan',
            'resort-in-tanay' => 'tanay', 'resort-in-rodriguez-rizal' => 'rodriguez-montalban',
            'resort-in-binangonan-rizal' => 'binangonan', 'resort-in-san-mateo-rizal' => 'san-mateo-rizal',
            'resort-in-taytay-rizal' => 'taytay-rizal', 'resort-in-marikina' => 'marikina',
            'resort-in-rizal' => 'antipolo', 'resort-in-rizal-province' => 'antipolo',
            'resort-in-lucena-city' => 'lucena', 'resort-in-sariaya-quezon' => 'sariaya',
            'resort-in-quezon' => 'lucena', 'resort-in-quezon-province' => 'lucena',
            'resort-in-albay' => 'albay-legazpi', 'resort-in-naga' => 'naga-camarines-sur',
            'resort-in-naga-city' => 'naga-camarines-sur', 'resort-in-naga-city-camarines-sur' => 'naga-camarines-sur',
            'resort-in-sorsogon' => 'sorsogon',
            'resort-in-subic' => 'subic', 'resort-in-subic-zambales' => 'subic',
            'resort-in-morong-bataan' => 'morong-bataan', 'resort-in-bataan' => 'bataan-province',
            'resort-in-pangasinan' => 'pangasinan-general', 'resort-in-bolinao' => 'bolinao',
            'beach-resort-in-la-union' => 'la-union', 'resort-in-la-union' => 'la-union',
            'resort-in-hundred-islands' => 'alaminos-hundred-islands',
            'resort-in-davao' => 'davao-city', 'resort-in-davao-city' => 'davao-city',
            'resort-in-samal-island' => 'samal-island', 'resort-in-gensan' => 'general-santos',
            'resort-in-glan' => 'glan-sarangani', 'resort-in-zamboanga' => 'zamboanga-city',
            'resort-in-kidapawan-city' => 'kidapawan',
            'resort-in-cebu-city' => 'cebu-city', 'hotel-in-cebu' => 'cebu-city',
            'resort-in-lapu-lapu' => 'mactan', 'resort-in-lapu-lapu-city' => 'mactan',
            'resort-in-panglao-bohol' => 'panglao',
            'resort-in-dumaguete' => 'dumaguete', 'resort-in-dauin' => 'dauin',
            'resort-in-iloilo' => 'iloilo-city', 'resort-in-iloilo-city' => 'iloilo-city',
            'resort-in-guimaras' => 'guimaras', 'resort-in-guimaras-island' => 'guimaras',
            'resort-in-bacolod' => 'bacolod', 'resort-in-don-salvador-benedicto' => 'bacolod',
            'resort-in-siquijor' => 'siquijor',
            'hotel-in-boracay' => 'boracay',
            'resort-in-el-nido' => 'el-nido', 'resort-in-el-nido-palawan' => 'el-nido',
            'beach-resort-in-palawan' => 'el-nido', 'resort-in-puerto-galera' => 'puerto-galera',
            'airbnb-in-manila' => 'manila', 'resort-in-manila' => 'manila',
            'resort-in-taguig' => 'taguig', 'resort-in-quezon-city' => 'quezon-city',
            'resort-in-nueva-ecija' => 'nueva-ecija', 'resort-in-tarlac' => 'tarlac',
            'resort-in-urdaneta-city-pangasinan' => 'urdaneta',
            'resort-in-dingalan-aurora' => 'dingalan',
        ];
    }
}
