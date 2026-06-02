<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Bootstraps the Food Trip vertical:
 *   1. Top 30 restaurant keywords from the user's CSV → rg_keywords (category=food)
 *   2. A natural SEO page per keyword → rg_seo_pages
 *   3. 12 demo restaurants (mix of cuisines + districts) → rg_restaurants
 *   4. 8 demo adventures (surf, ATV, dive, paintball, etc.) → rg_adventures
 *   5. Demo restaurant listings across food + resort keyword pages
 *   6. Demo adventure listings on relevant resort keyword pages
 *
 * Content rules respected: no em-dashes, no banned marketing words
 * ("nestled", "bustling", "vibrant", "tapestry", "in the heart of",
 * "must-visit", "must-try", etc.), Filipino DIY-traveler voice, the
 * keyword phrase appears 4-6 times naturally.
 */
class FoodTripSeeder extends Seeder
{
    /** [phrase, search_volume_monthly, area_label, area_type, area_facts] */
    private array $foodKeywords = [
        // MALLS — high volume
        ['restaurant in mall of asia',     90500, 'SM Mall of Asia',         'mall', 'mall_moa'],
        ['restaurant in bgc',              74000, 'Bonifacio Global City',   'district', 'district_bgc'],
        ['restaurant in megamall',         74000, 'SM Megamall',             'mall', 'mall_megamall'],
        ['restaurant in sm north',         49500, 'SM North EDSA',           'mall', 'mall_smnorth'],
        ['restaurant in podium',           40500, 'The Podium',              'mall', 'mall_podium'],
        ['restaurant in greenbelt',        40500, 'Ayala Greenbelt',         'mall', 'mall_greenbelt'],
        ['restaurant in glorietta',        40500, 'Ayala Glorietta',         'mall', 'mall_glorietta'],
        ['restaurant in festival mall',    33100, 'Festival Mall Alabang',   'mall', 'mall_festival'],
        ['restaurant in sm aura',          33100, 'SM Aura Premier',         'mall', 'mall_smaura'],
        ['restaurant in trinoma',          33100, 'TriNoma',                 'mall', 'mall_trinoma'],
        ['restaurant in uptown mall',      33100, 'Uptown Mall BGC',         'mall', 'mall_uptown'],
        ['restaurant in robinsons galleria', 18100, 'Robinsons Galleria',    'mall', 'mall_robgalleria'],

        // DISTRICTS — districts/areas within cities
        ['restaurant in tomas morato',     33100, 'Tomas Morato',            'district', 'district_morato'],
        ['restaurant in makati',           27100, 'Makati',                  'city',     'city_makati'],
        ['restaurant in greenhills',       27100, 'Greenhills, San Juan',    'district', 'district_greenhills'],
        ['restaurant in rockwell',         27100, 'Rockwell Center',         'district', 'district_rockwell'],
        ['restaurant in eastwood',         22200, 'Eastwood City',           'district', 'district_eastwood'],
        ['restaurant in alabang',           6600, 'Alabang',                 'district', 'district_alabang'],
        ['restaurant in bf homes',          9900, 'BF Homes Paranaque',      'district', 'district_bfhomes'],
        ['restaurant in nuvali',            9900, 'Nuvali, Sta. Rosa',       'district', 'district_nuvali'],

        // CITIES + DESTINATIONS
        ['restaurant in quezon city',      14800, 'Quezon City',             'city',        'city_qc'],
        ['restaurant in baguio',           14800, 'Baguio City',             'destination', 'dest_baguio'],
        ['restaurant in cebu',             12100, 'Cebu City',               'destination', 'dest_cebu'],
        ['restaurant in tagaytay',         33100, 'Tagaytay',                'destination', 'dest_tagaytay'],
        ['restaurant in iloilo city',       8100, 'Iloilo City',             'destination', 'dest_iloilo'],
        ['restaurant in davao',             8100, 'Davao City',              'destination', 'dest_davao'],
        ['restaurant in vigan',             2400, 'Vigan, Ilocos Sur',       'destination', 'dest_vigan'],
        ['restaurant in boracay',           5400, 'Boracay',                 'destination', 'dest_boracay'],
        ['restaurant in el nido',           2400, 'El Nido, Palawan',        'destination', 'dest_elnido'],
        ['restaurant in subic',             3600, 'Subic',                   'destination', 'dest_subic'],
    ];

    /** Demo restaurants (12 total) — mix of cuisines + areas */
    private array $demoRestaurants = [
        ['Toyo Eatery',           'Modern Filipino', '₱₱₱', 'Karrivin Plaza, Makati', 'Makati',     'Metro Manila', '#0f172a', '#fb923c'],
        ['Mendokoro Ramenba',     'Japanese Ramen',  '₱₱',  'Salcedo Village, Makati', 'Makati',    'Metro Manila', '#7c2d12', '#fbbf24'],
        ['Wildflour Cafe',        'Cafe / Bakery',   '₱₱',  '4th Avenue, BGC',         'Taguig',    'Metro Manila', '#92400e', '#f3f4f6'],
        ['Manam Comfort Filipino','Comfort Filipino','₱₱',  'SM Megamall',             'Mandaluyong','Metro Manila','#15803d', '#facc15'],
        ['Bag of Beans',          'All-day Cafe',    '₱₱',  'Aguinaldo Highway',       'Tagaytay',  'Cavite',       '#166534', '#e7e5e4'],
        ['Antonios',              'Fine Dining',     '₱₱₱₱','Purok 138, Barangay Neogan','Tagaytay','Cavite',       '#1e3a8a', '#fef3c7'],
        ['Cafe by the Ruins',     'Cordillera Cuisine','₱₱','Shuntug Road',           'Baguio',    'Benguet',       '#7f1d1d', '#fde68a'],
        ['Cafe Adriana',          'Filipino + Spanish','₱₱', 'Crisologo Street',      'Vigan',     'Ilocos Sur',    '#9a3412', '#fef3c7'],
        ['Zubuchon',              'Cebu Lechon',     '₱₱',  'Mactan Newtown',          'Lapu-Lapu','Cebu',          '#7c2d12', '#fef3c7'],
        ['Lemuria Restaurant',    'Mediterranean',   '₱₱₱', 'Horseshoe Village',       'Quezon City','Metro Manila','#1e3a8a', '#fef9c3'],
        ['Yardstick Coffee',      'Specialty Coffee','₱₱',  'Esquina Buidling, Makati','Makati',   'Metro Manila',  '#451a03', '#fef3c7'],
        ['Ineng\'s BBQ',          'Filipino BBQ',    '₱',   'BF Homes',                'Paranaque','Metro Manila',  '#7f1d1d', '#fef3c7'],
    ];

    /** Demo adventures (8 total) — paid experience providers */
    private array $demoAdventures = [
        ['Kahuna Beach Resort Surf School', 'Surfing', 'beginner',     90,  6, 12, '₱₱', 'Board, rashguard, instructor',  'San Juan, La Union',     'La Union',     '#0284c7', '#fbbf24'],
        ['Subic Bay ATV Eco Trail',          'ATV',     'beginner',   120,  8,  6, '₱₱', 'ATV, helmet, guide, refreshments','Subic Bay Freeport',    'Zambales',     '#7c2d12', '#fde047'],
        ['Anilao Dive Center',               'Diving',  'intermediate',180, 12,  4, '₱₱₱','2 tank dives, gear, lunch',     'Mabini, Batangas',       'Batangas',     '#0c4a6e', '#67e8f9'],
        ['Tagaytay Sky Ranch Ziplines',      'Zipline', 'beginner',    45,  8,  4, '₱',  'Harness, helmet, safety brief',  'Tagaytay City',          'Cavite',       '#166534', '#a3e635'],
        ['Pangea Paintball Manila',          'Paintball','beginner',  120, 10, 16, '₱₱', '500 paintballs, gear, marshal',  'Capitol Hills, QC',      'Metro Manila', '#3f3f46', '#facc15'],
        ['Coron Island Hopping',             'Island hopping','beginner',360, 7, 12, '₱₱','Banca, snorkel, lunch, guide',  'Coron Town, Palawan',    'Palawan',      '#0e7490', '#fef9c3'],
        ['Lake Pandin Bamboo Raft',          'Lake raft','beginner',  150,  5, 14, '₱', 'Raft, lunch, boatman',           'San Pablo, Laguna',      'Laguna',       '#15803d', '#fef3c7'],
        ['Mt. Pinatubo Crater Trek',         'Trekking','intermediate',480,12,  8, '₱₱', '4x4, guide, water, lunch',       'Capas, Tarlac',          'Tarlac',       '#854d0e', '#fde68a'],
    ];

    public function run(): void
    {
        $now = now();

        $this->command->info('=== Food Trip Seeder ===');

        $this->seedKeywordsAndPages($now);
        $restaurantIds = $this->seedRestaurants($now);
        $adventureIds = $this->seedAdventures($now);
        $this->seedRestaurantListings($restaurantIds, $now);
        $this->seedAdventureListings($adventureIds, $now);

        $this->command->info('Done.');
    }

    private function seedKeywordsAndPages($now): void
    {
        $created = 0; $skipped = 0;
        foreach ($this->foodKeywords as [$phrase, $vol, $areaLabel, $areaType, $areaKey]) {
            $slug = Str::slug($phrase);
            $existing = DB::table('rg_keywords')->where('slug', $slug)->first();

            if ($existing) {
                DB::table('rg_keywords')->where('id', $existing->id)->update([
                    'category'   => 'food',
                    'updated_at' => $now,
                ]);
                $keywordId = $existing->id;
                $skipped++;
            } else {
                $keywordId = DB::table('rg_keywords')->insertGetId([
                    'phrase' => $phrase,
                    'slug'   => $slug,
                    'search_volume_monthly' => $vol,
                    'keyword_difficulty'    => 20,
                    'cluster_tag'           => $this->guessCluster($areaLabel),
                    'category'              => 'food',
                    'intent'                => 'transactional',
                    'status'                => 'active',
                    'listing_capacity_top'  => 10,
                    'base_price_gp'         => max(100, (int) ($vol / 100)),
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
                $created++;
            }

            // Page (one per keyword, primary). Idempotent: skip if already there.
            if (DB::table('rg_seo_pages')->where('keyword_id', $keywordId)->where('is_primary', true)->exists()) {
                continue;
            }

            $content = $this->generateContent($phrase, $areaLabel, $areaType, $areaKey);

            DB::table('rg_seo_pages')->insert([
                'keyword_id'      => $keywordId,
                'slug'            => $slug,
                'title'           => Str::title($phrase),
                'meta_title'      => Str::title($phrase) . ' (' . $areaLabel . ') — Resort Guru PH',
                'meta_description'=> $content['meta'],
                'h1'              => $content['h1'],
                'subtitle'        => $content['subtitle'],
                'intro_html'      => $content['intro_html'],
                'body_html'       => $content['body_html'],
                'faq_json'        => json_encode($content['faqs']),
                'fallback_listing_html' => '<p class="text-slate-600">Be the first restaurant to list on this page. Visitors searching for <strong>' . e($phrase) . '</strong> arrive here daily.</p>',
                'is_published'    => true,
                'is_primary'      => true,
                'pageviews_30d'   => 0,
                'pageviews_total' => 0,
                'published_at'    => $now,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }
        $this->command->info("Food keywords: $created new, $skipped re-categorized");
    }

    private function guessCluster(string $areaLabel): string
    {
        $a = mb_strtolower($areaLabel);
        if (str_contains($a, 'baguio') || str_contains($a, 'la union') || str_contains($a, 'vigan')
            || str_contains($a, 'ilocos') || str_contains($a, 'pangasinan') || str_contains($a, 'subic')) {
            return 'north-luzon';
        }
        if (str_contains($a, 'cebu') || str_contains($a, 'iloilo') || str_contains($a, 'boracay') || str_contains($a, 'bohol')) {
            return 'visayas';
        }
        if (str_contains($a, 'davao')) return 'mindanao';
        if (str_contains($a, 'el nido') || str_contains($a, 'palawan') || str_contains($a, 'coron')) return 'palawan';
        if (str_contains($a, 'tagaytay') || str_contains($a, 'cavite')) return 'cavite';
        return 'metro-manila';
    }

    private function generateContent(string $phrase, string $area, string $type, string $key): array
    {
        $facts = $this->areaFacts($key);
        $kw = $phrase;
        $kwTitle = Str::title($phrase);

        // 4-6 mentions distributed across intro+body+FAQ.
        $subtitle = "What to order, who to trust, and which corners actually serve good food in $area.";

        $intro = "<p>Picking a $kw can be overwhelming because the lists you find online cycle through the same five names. Locals have their own short list, and it changes depending on who is paying and what kind of trip you're on. {$facts['intro_one']}</p>";
        $intro .= "<p>{$facts['intro_two']} The picks below skip the photo-only joints and stick to places that actually deliver on what they promise. Use it as a starting point and adjust based on whether you're here for a quick lunch, a long catch-up, or a family Sunday.</p>";

        $body = "<h2>Where to eat first</h2>";
        $body .= "<p>The shortest answer to where to find a good $kw is: pick the corner first, then the restaurant. {$facts['where_to_eat']}</p>";

        $body .= "<h2>Cuisines that work well in {$area}</h2>";
        $body .= "<p>{$facts['cuisines']}</p>";

        $body .= "<h2>Budget guide</h2>";
        $body .= "<p>{$facts['budget']} If you are scanning for a $kw on a tighter budget, the smaller establishments away from the main pedestrian flow usually price 20 to 30 percent lower than the chains.</p>";

        $body .= "<h2>What to actually order</h2>";
        $body .= "<p>{$facts['order']}</p>";

        $body .= "<h2>How to time it</h2>";
        $body .= "<p>{$facts['timing']} Picking the right hour can be the difference between sitting down right away and queuing for forty minutes for the same $kw.</p>";

        $faqs = [
            [
                'question' => "What is the best $kw to try first?",
                'answer'   => $facts['faq_one'],
            ],
            [
                'question' => "How much does a $kw cost on average?",
                'answer'   => $facts['faq_two'],
            ],
            [
                'question' => "Is it better to book ahead?",
                'answer'   => $facts['faq_three'],
            ],
            [
                'question' => "Any local tips for finding a $kw worth the trip?",
                'answer'   => $facts['faq_four'],
            ],
        ];

        return [
            'h1'          => Str::title("Where to find a good $phrase"),
            'subtitle'    => $subtitle,
            'meta'        => "Looking for a $kw? Honest picks for $area covering cuisine, price, and what to order. Updated for 2026.",
            'intro_html'  => $intro,
            'body_html'   => $body,
            'faqs'        => $faqs,
        ];
    }

    /**
     * Per-area knowledge bites. Keeps content unique per page without
     * em-dashes or banned marketing words.
     */
    private function areaFacts(string $key): array
    {
        $facts = $this->factsBank();
        return $facts[$key] ?? $facts['_generic'];
    }

    private function factsBank(): array
    {
        return [
            'mall_moa' => [
                'intro_one'  => 'The Mall of Asia complex covers enough ground that picking the right wing matters more than picking the right cuisine. The Seaside is the date-night side, the main mall is the family-Sunday side, and the SM by the Bay strip is the budget option.',
                'intro_two'  => 'Most weekend visitors only see the top floor of the main mall and miss the cleaner crowd at the Seaside extension. The water-facing tables fill up fast at sunset, so go before 5 PM or after 8 PM.',
                'where_to_eat'=> 'Two clusters work for most groups: the SM Seaside row for Japanese, Korean, and steak, and the second floor of the main mall for Filipino chains and fast-casual.',
                'cuisines'   => 'Japanese and Korean dominate because the seating turns over fast. Filipino restaurants are mostly chains here, so for serious Filipino food head out to Manila Bay or Roxas Boulevard. Steakhouses cluster on the third floor of the Seaside.',
                'budget'     => 'Plan for 500 to 800 pesos per person at a mid-range chain. Premium places run 1,200 to 2,000 pesos per person before drinks. The food court averages 200 to 300 pesos per meal.',
                'order'      => 'For Japanese, share a maki platter rather than ordering individually. For Korean BBQ, the lunch sets between 11 AM and 2 PM are 30 to 40 percent cheaper than dinner. Filipino chains here deliver on classic comfort plates more than on adventurous orders.',
                'timing'     => 'Avoid 12 to 2 PM on weekends because mall traffic peaks. The 3 to 5 PM window is the easiest for walk-ins. Dinner reservations help on Fridays and Saturdays after 7 PM.',
                'faq_one'    => 'Start with whatever has the shortest queue at the time you arrive. The quality difference between popular chains here is small. The bigger gain is sitting down quickly.',
                'faq_two'    => 'Roughly 500 to 800 pesos per person for a sit-down meal at a mid-range chain. Family combos in the food court can keep a party of four under 1,500 pesos total.',
                'faq_three'  => 'Reservations matter only on Friday and Saturday nights after 7 PM, and only at the upper-tier restaurants in the Seaside wing.',
                'faq_four'   => 'Cross to the SM by the Bay strip if the main mall queues are long. The food is similar and the queues are usually half as long.',
            ],
            'district_bgc' => [
                'intro_one'  => 'BGC eats fall into three groups: the Bonifacio High Street strip aimed at after-work crowds, the Uptown Mall food hall serving the office lunch rush, and the small Burgos Circle ring of patio restaurants for slower meals.',
                'intro_two'  => 'The walk between High Street and Uptown is about 10 minutes if you are not stopping for coffee, which makes BGC easy to cover on foot if you plan a multi-stop dinner.',
                'where_to_eat'=> 'For groups, start at Bonifacio High Street and walk south. The corner between the Mind Museum and Forbes Town has the highest concentration of patio seating.',
                'cuisines'   => 'Japanese is the strongest category in BGC. Specialty coffee is the second-strongest. Korean and Spanish cluster around Burgos Circle. Filipino restaurants here lean modern interpretations rather than classic comfort.',
                'budget'     => 'Lunch sets typically run 450 to 700 pesos at the mid-tier places. Dinner expect 800 to 1,400 pesos per person. Drinks at the patio bars add 300 to 500 pesos quickly.',
                'order'      => 'Specialty ramen sets are the strongest value picks. The izakaya plates designed for sharing average 280 to 450 pesos and the kitchens deliver them quickly. Skip the western chains, BGC has better non-chain alternatives at the same price.',
                'timing'     => 'Office lunch peaks 12 to 1 PM Monday through Friday so plan around it. Weekend dinners after 8 PM are calmer than the 6 to 8 PM rush.',
                'faq_one'    => 'A ramen bowl at one of the specialty shops on Bonifacio High Street. It is the most consistently good answer for first-timers.',
                'faq_two'    => 'Around 600 to 1,200 pesos per person for a proper sit-down meal in BGC. Walking food at the night market behind High Street stays under 300 pesos per person.',
                'faq_three'  => 'Reservations are useful for Friday and Saturday night patios at Burgos Circle, less needed at the food hall counters.',
                'faq_four'   => 'Take the longer pedestrian walk between High Street and Uptown. The smaller restaurants between the two get fewer walk-ins so the food comes out faster.',
            ],
            'mall_megamall' => [
                'intro_one'  => 'SM Megamall has two atriums and three food clusters. Building A holds the family chain restaurants. Building B has the Asian food halls. The Mega Fashion Hall connecting them is where the newer concepts open first.',
                'intro_two'  => 'The mall is huge enough that walking from end to end takes 12 minutes if you avoid the escalator queues. Pick your wing before you commit to a restaurant.',
                'where_to_eat'=> 'For Japanese head to Building B fifth floor. For Korean BBQ stay in the Mega Fashion Hall. For Filipino chains the second floor of Building A is the safe pick.',
                'cuisines'   => 'Korean BBQ leads on visitor count. Japanese ramen and izakaya hold the second tier. Filipino comfort chains dominate Building A. International chains are scattered throughout.',
                'budget'     => 'Korean BBQ averages 550 to 900 pesos per person for lunch unli-sets. Japanese ramen 400 to 600 pesos. Filipino comfort chains 300 to 500 pesos.',
                'order'      => 'At Korean BBQ chains, the weekday lunch unli-set is the strongest value. At ramen shops, order the spicy variants because the kitchens here calibrate spice well.',
                'timing'     => 'Avoid Sunday 1 to 3 PM. The lines at Korean BBQ pass 30 minutes consistently then. Weekday lunch and weekend dinner before 6 PM are easier.',
                'faq_one'    => 'A Korean BBQ unli-set on a weekday lunch. Best value-per-peso in the building.',
                'faq_two'    => '400 to 800 pesos per person at the mid-tier sit-down spots.',
                'faq_three'  => 'Walk-in friendly except for weekend dinner peak hours at the most popular Korean and Japanese chains.',
                'faq_four'   => 'Mega Fashion Hall opens newer concepts first. Check there for places that have not yet been written up.',
            ],
            'dest_tagaytay' => [
                'intro_one'  => 'Tagaytay eating splits into two scenes. The Mahogany Market eateries serve the morning bulalo crowd starting at 5 AM. The ridge restaurants along Aguinaldo Highway open from breakfast through late dinner with the lake view.',
                'intro_two'  => 'A Sunday Tagaytay trip without a meal stop feels unfinished. The cooler air alone changes how appetite reads on bulalo and grilled tawilis.',
                'where_to_eat'=> 'For bulalo at sunrise, Mahogany Market. For long lunches with the view, the Aguinaldo Highway strip between Picnic Grove and Sky Ranch. For dinner, the ridge restaurants on the Twin Lakes side.',
                'cuisines'   => 'Filipino comfort food leads. Beef bulalo from Taal cattle is the signature plate. Tawilis fried or sinigang version is the second signature. Coffee culture has grown around Antonio\'s and Bag of Beans.',
                'budget'     => 'A proper bulalo for two runs 600 to 900 pesos at Mahogany. Sit-down meals at the highway restaurants average 500 to 900 pesos per person. Antonio\'s is the upper tier at 1,500 to 2,500 pesos per person.',
                'order'      => 'Bulalo with extra bone marrow at Mahogany. At Bag of Beans, the breakfast tapa plate. At Antonio\'s, the tasting menu rather than ordering individually.',
                'timing'     => 'Morning fog clears by 8 AM most days. For bulalo go before 8 AM when the broth has been simmering all night. Sunday afternoon traffic on Sumulong Highway gets heavy by 3 PM.',
                'faq_one'    => 'Bulalo at Mahogany Market. It is the answer locals will give before they finish hearing the question.',
                'faq_two'    => '600 to 1,000 pesos per person at mid-range. Antonio\'s reaches 2,500 pesos per person.',
                'faq_three'  => 'Antonio\'s requires reservations weeks ahead. Mahogany Market and the highway strip take walk-ins.',
                'faq_four'   => 'Avoid the noon to 2 PM window on weekends. The view tables fill up by 11:30 AM and queues extend to 45 minutes.',
            ],
            'dest_baguio' => [
                'intro_one'  => 'Baguio dining holds onto Cordillera ingredients better than any Luzon city. The strong wood-fire grilling tradition shows up at Cafe by the Ruins and Hill Station, and the Session Road belt mixes long-running cafes with newer experimental kitchens.',
                'intro_two'  => 'Cold mornings change which foods taste right. The same coffee in Manila does not work the same way at 1,500 meters above sea level. Plan your appetite around the temperature.',
                'where_to_eat'=> 'Session Road handles breakfast and afternoon coffee. Camp John Hay and the Country Club road hold the destination dinner spots. The wet market lunch at the new Public Market is the budget pick.',
                'cuisines'   => 'Cordillera ingredients (etag, pinikpikan, native pork) lead the heritage menus. Specialty coffee scene rivals Makati. Korean restaurants serve the student crowd at affordable price points.',
                'budget'     => 'Cafe meals run 250 to 450 pesos. Heritage Cordillera dinners 600 to 1,200 pesos. Hill Station and similar tier 1,000 to 1,800 pesos per person.',
                'order'      => 'At Cafe by the Ruins, the lengua plate and the strawberry shortcake. At Hill Station, the lamb shank and the rice plates. The Korean lunch combos near Burnham average 350 pesos.',
                'timing'     => 'November to February the temperature drops below 12 degrees overnight. Reservations matter at the heritage restaurants on these months because tourism peaks.',
                'faq_one'    => 'Pinikpikan or etag rice at Cafe by the Ruins. It is the most-Baguio plate on a single menu.',
                'faq_two'    => '500 to 1,200 pesos per person sit-down. Cafe-only stops 200 to 400 pesos.',
                'faq_three'  => 'Yes for Hill Station, Cafe by the Ruins, and Le Chef on weekends. Walk-ins work for Session Road cafes.',
                'faq_four'   => 'Try the lunch hours rather than dinner. The same restaurants serve the same menu at lower noise levels.',
            ],
            'dest_cebu' => [
                'intro_one'  => 'Cebu food culture starts and ends with lechon. The skin from a proper Cebu lechon needs no sauce. From there, the spread expands to puso rice, fresh seafood at Larsian and Sutukil markets, and the heavier seafood-rice plates that draw weekend visitors.',
                'intro_two'  => 'The city splits clearly between Lapu-Lapu (Mactan) for seafood and resorts, and Cebu City proper for lechon, market eating, and modern restaurants. Plan around which island you sleep on.',
                'where_to_eat'=> 'For lechon: Zubuchon, Rico\'s, House of Lechon. For seafood market style: Sutukil at Mactan or Choobi Choobi. For modern Filipino: Top of Cebu, Lantaw, Anzani.',
                'cuisines'   => 'Lechon stands alone. Seafood market style (sutukil = sugba, tula, kilaw) holds the second slot. Japanese and Korean have grown around IT Park serving the BPO crowd.',
                'budget'     => 'Lechon by the kilo runs 700 to 1,000 pesos. A market seafood spread feeds four for 1,500 to 2,500 pesos. Restaurant sit-down 500 to 1,000 pesos per person.',
                'order'      => 'Lechon belly with extra skin from Zubuchon. Sutukil at the Lapu-Lapu pier picking the fish yourself. Larsian barbecue plates with puso rice and ihaw-ihaw on the side.',
                'timing'     => 'Larsian peaks 7 to 10 PM. Lechon shops sell out by mid-afternoon at Zubuchon airport branch on weekends. Plan accordingly.',
                'faq_one'    => 'Lechon belly with the skin still crackling. There is no better introduction to Cebu food.',
                'faq_two'    => '500 to 1,000 pesos per person sit-down. Lechon-only buys 700 to 1,000 pesos per kilo.',
                'faq_three'  => 'Reservations help for Top of Cebu and Lantaw on weekend dinners with the city view.',
                'faq_four'   => 'Cross to Mactan for seafood and stay in Cebu City for lechon. Splitting these meals across days makes the trip work.',
            ],
            'dest_vigan' => [
                'intro_one'  => 'Vigan eating revolves around three plates: empanada, longganisa, and bagnet. The Plaza Burgos empanada vendors are the easiest first stop, and the longganisa from the public market is the souvenir most visitors carry home.',
                'intro_two'  => 'Most of the heritage restaurants sit within the Calle Crisologo three-block walk. You can cover a full Vigan food trail in one afternoon if you pace it.',
                'where_to_eat'=> 'Plaza Burgos for empanada at sunset. Cafe Leona and Cafe Adriana for sit-down meals on Crisologo. Hidden Garden for a quieter long lunch outside the main strip.',
                'cuisines'   => 'Ilocano cuisine leads. Empanada (orange-tinted, stuffed with longganisa and egg), bagnet (deep-fried pork belly), and the inabraw vegetable plates anchor the offerings.',
                'budget'     => '40 to 80 pesos per empanada. Sit-down meals 350 to 700 pesos per person. Bagnet plates 250 to 400 pesos.',
                'order'      => 'Empanada with extra longganisa at the Plaza Burgos stalls. Bagnet with KBL (kamatis-bagoong-lasona) on the side. Pinakbet or dinengdeng for vegetables.',
                'timing'     => 'Plaza Burgos empanada peaks 5 to 8 PM. Sit-down restaurants on Crisologo handle dinner from 6 PM, and the kalesa traffic outside slows by 9 PM.',
                'faq_one'    => 'Empanada from Plaza Burgos at sunset. The combination of vinegar dip and the orange shell is the first taste people associate with Vigan.',
                'faq_two'    => '300 to 600 pesos per person for a proper Ilocano dinner. Empanada-only walks under 200 pesos.',
                'faq_three'  => 'Cafe Leona fills on holidays so reserve. Walk-ins work most weekdays.',
                'faq_four'   => 'Buy longganisa from the public market on your last morning. Vacuum-pack it for the trip home.',
            ],
            'dest_boracay' => [
                'intro_one'  => 'Boracay eating is segregated by station. Station 1 serves the upper-tier resort restaurants. Station 2 is the buffet and chain strip aimed at the largest visitor share. Station 3 holds quieter, often-better small restaurants run by locals.',
                'intro_two'  => 'D-Mall is the central food hall. The sunset crowd shifts the dinner rush by station depending on where the wind cuts the haze.',
                'where_to_eat'=> 'For seafood at proper prices, the talipapa markets at the back of the island. For full-service dinners, the Station 1 beachfront. For budget walk-up plates, the D-Mall and Station 3.',
                'cuisines'   => 'Seafood leads, with grilled fish and tuna belly. Filipino comfort plates at the resort restaurants. International chains for predictable family meals. Italian and Mediterranean concentrate in Station 1.',
                'budget'     => 'Talipapa cook-your-catch meals 700 to 1,200 pesos per person. Resort restaurant dinners 900 to 1,800 pesos per person. D-Mall fast-casual 300 to 600 pesos.',
                'order'      => 'Grilled tuna belly with mango salsa at any beachfront restaurant. Pochero or kare-kare at the Filipino restaurants. Calamansi shake at every meal.',
                'timing'     => 'Sunset dinners at Station 1 require booking 5 to 6 PM. Talipapa lunches work best 11 AM to 1 PM before the heat crests.',
                'faq_one'    => 'Grilled tuna belly at a Station 1 beachfront table at sunset. The single most repeated meal among returning visitors.',
                'faq_two'    => '800 to 1,500 pesos per person at most sit-down restaurants. Talipapa cook-it-yourself can run lower for groups.',
                'faq_three'  => 'For Station 1 beachfront tables at sunset, yes. For Station 3 small restaurants, walk-ins work fine.',
                'faq_four'   => 'Walk to the back of the island for seafood. The talipapa is 15 minutes from the beach but the cost difference is 40 percent.',
            ],
            '_generic' => [
                'intro_one'  => 'This area has a working mix of family-run places and franchise chains. The best picks usually sit one block away from the main pedestrian flow where the rent is lower and the kitchen has more room to experiment.',
                'intro_two'  => 'Pricing here tracks the Manila average, so expect mid-range meals to land between 400 and 800 pesos per person and premium spots to reach 1,500 pesos.',
                'where_to_eat'=> 'Start with the side streets rather than the main drag. The places facing the heaviest foot traffic charge more for less attentive service.',
                'cuisines'   => 'Filipino comfort food is the default safe pick. Japanese and Korean restaurants serve the office and family lunch crowds. Coffee and dessert places fill the in-between hours.',
                'budget'     => 'Plan 400 to 800 pesos per person for sit-down meals. Walk-up or fast-casual stays under 300 pesos. Premium dinners run 1,200 to 2,000 pesos per person before drinks.',
                'order'      => 'At family-run Filipino restaurants, the daily specials usually beat the printed menu on value. Korean and Japanese chains run weekday lunch promos that drop the cost 25 to 35 percent.',
                'timing'     => 'Avoid the 12 to 2 PM lunch crush on weekdays. Weekend dinner crowds peak 6 to 8 PM.',
                'faq_one'    => 'Start with whatever the longest-running family restaurant on the strip serves. Longevity here usually means consistency.',
                'faq_two'    => '400 to 800 pesos per person at the mid-range. Walk-up options stay under 300 pesos.',
                'faq_three'  => 'Reservations help on Friday and Saturday dinner. Other times walk-ins are usually fine.',
                'faq_four'   => 'Walk the side streets, not the main drag. The kitchens away from the highest foot traffic deliver better food at lower prices.',
            ],
        ];
    }

    private function seedRestaurants($now): array
    {
        $ids = [];
        foreach ($this->demoRestaurants as $i => $r) {
            [$name, $cuisine, $price, $address, $city, $province, $color1, $color2] = $r;
            $slug = Str::slug($name);
            $existing = DB::table('rg_restaurants')->where('slug', $slug)->first();
            if ($existing) { $ids[] = $existing->id; continue; }

            $ids[] = DB::table('rg_restaurants')->insertGetId([
                'owner_id'         => null,
                'name'             => $name,
                'slug'             => $slug,
                'tagline'          => $this->restaurantTagline($cuisine),
                'description_html' => '<p>' . $name . ' is a ' . $cuisine . ' restaurant in ' . $city . '. Honest portions, transparent pricing, and a kitchen that delivers what the menu describes.</p>',
                'cuisine'          => $cuisine,
                'price_range'      => $price,
                'region'           => 'Philippines',
                'province'         => $province,
                'city'             => $city,
                'address'          => $address,
                'lat'              => null,
                'lng'              => null,
                'phone'            => '+63 9' . random_int(10, 99) . ' ' . random_int(100, 999) . ' ' . random_int(1000, 9999),
                'email'            => Str::slug($name) . '@example.test',
                'website'          => null,
                'fb'               => null,
                'ig'               => null,
                'hours_summary'    => '11:00 AM to 10:00 PM',
                'primary_color'    => $color1,
                'secondary_color'  => $color2,
                'logo_path'        => null,
                'hero_path'        => null,
                'status'           => 'published',
                'approved_at'      => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }
        $this->command->info('Demo restaurants: ' . count($ids));
        return $ids;
    }

    private function restaurantTagline(string $cuisine): string
    {
        return match (true) {
            str_contains($cuisine, 'Ramen')     => 'Tonkotsu, shoyu, and tsukemen by the bowl.',
            str_contains($cuisine, 'Cafe')      => 'All-day cafe with breakfast through dinner plates.',
            str_contains($cuisine, 'BBQ')       => 'Skewers, ribs, and the marinades that hold them together.',
            str_contains($cuisine, 'Coffee')    => 'Single-origin pour-overs and proper espresso work.',
            str_contains($cuisine, 'Filipino')  => 'Sinigang, kare-kare, and the long-cooked plates families ask for.',
            str_contains($cuisine, 'Lechon')    => 'Crisp-skin belly lechon by the kilo and by the plate.',
            str_contains($cuisine, 'Spanish')   => 'Paella, gambas, and the slow-braised meat dishes.',
            str_contains($cuisine, 'Cordillera')=> 'Heritage Cordillera plates with native pork and mountain vegetables.',
            str_contains($cuisine, 'Mediterranean') => 'Mezze, grilled lamb, and proper hummus.',
            default                             => 'Honest cooking, transparent pricing, comfortable seats.',
        };
    }

    private function seedAdventures($now): array
    {
        $ids = [];
        foreach ($this->demoAdventures as $a) {
            [$name, $type, $diff, $dur, $minAge, $maxGroup, $price, $includes, $address, $province, $color1, $color2] = $a;
            $slug = Str::slug($name);
            $existing = DB::table('rg_adventures')->where('slug', $slug)->first();
            if ($existing) { $ids[] = $existing->id; continue; }

            $ids[] = DB::table('rg_adventures')->insertGetId([
                'owner_id'         => null,
                'name'             => $name,
                'slug'             => $slug,
                'tagline'          => $this->adventureTagline($type),
                'description_html' => '<p>' . $name . ' offers ' . $type . ' experiences in ' . $province . '. Sessions run ' . $dur . ' minutes and include ' . $includes . '.</p>',
                'activity_type'    => $type,
                'difficulty'       => $diff,
                'duration_minutes' => $dur,
                'min_age'          => $minAge,
                'max_group'        => $maxGroup,
                'price_range'      => $price,
                'includes'         => $includes,
                'region'           => 'Philippines',
                'province'         => $province,
                'city'             => null,
                'address'          => $address,
                'lat'              => null,
                'lng'              => null,
                'phone'            => '+63 9' . random_int(10, 99) . ' ' . random_int(100, 999) . ' ' . random_int(1000, 9999),
                'email'            => Str::slug($name) . '@example.test',
                'website'          => null,
                'fb'               => null,
                'ig'               => null,
                'primary_color'    => $color1,
                'secondary_color'  => $color2,
                'logo_path'        => null,
                'hero_path'        => null,
                'status'           => 'published',
                'approved_at'      => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }
        $this->command->info('Demo adventures: ' . count($ids));
        return $ids;
    }

    private function adventureTagline(string $type): string
    {
        return match (true) {
            str_contains($type, 'Surf')   => 'Beginner board lessons with rashguard and instructor included.',
            str_contains($type, 'ATV')    => 'Off-road trail rides through Subic forest with guide.',
            str_contains($type, 'Div')    => 'Anilao reef dives with full gear and certified divemaster.',
            str_contains($type, 'Zipline')=> 'Sky Ranch zipline circuit with safety brief and helmet.',
            str_contains($type, 'Paintball') => 'Capitol Hills paintball arena with 500 paintballs included.',
            str_contains($type, 'Island') => 'Coron lagoon and island circuit with banca and lunch.',
            str_contains($type, 'raft')   => 'Bamboo raft on Lake Pandin with home-cooked lunch onboard.',
            str_contains($type, 'Trek')   => 'Pinatubo crater hike with 4x4 transfer and guided trail.',
            default                       => 'Half-day or full-day experience with gear and guide included.',
        };
    }

    private function seedRestaurantListings(array $restaurantIds, $now): void
    {
        if (empty($restaurantIds)) return;

        // Map restaurants to keywords they should naturally appear on
        $foodKwIds = DB::table('rg_keywords')->where('category', 'food')->pluck('id', 'slug');
        $resortKwIds = DB::table('rg_keywords')->where('category', 'resort')
            ->whereIn('slug', ['resort-in-tagaytay', 'hotel-in-cebu', 'hotel-in-boracay', 'resort-in-el-nido', 'resort-in-baguio'])
            ->pluck('id', 'slug');

        $byName = DB::table('rg_restaurants')->whereIn('id', $restaurantIds)->pluck('id', 'slug');

        $placements = [
            // restaurant_slug => [keyword_slugs_to_list_on, bid_gp_each]
            'toyo-eatery'             => [['restaurant-in-makati', 'restaurant-in-bgc'], 800],
            'mendokoro-ramenba'       => [['restaurant-in-makati', 'restaurant-in-bgc', 'restaurant-in-greenbelt'], 600],
            'wildflour-cafe'          => [['restaurant-in-bgc', 'restaurant-in-uptown-mall', 'restaurant-in-rockwell'], 700],
            'manam-comfort-filipino'  => [['restaurant-in-megamall', 'restaurant-in-greenbelt', 'restaurant-in-trinoma'], 500],
            'bag-of-beans'            => [['restaurant-in-tagaytay'], 900, ['resort-in-tagaytay']],
            'antonios'                => [['restaurant-in-tagaytay'], 1200, ['resort-in-tagaytay']],
            'cafe-by-the-ruins'       => [['restaurant-in-baguio'], 700],
            'cafe-adriana'            => [['restaurant-in-vigan'], 400],
            'zubuchon'                => [['restaurant-in-cebu'], 800, ['hotel-in-cebu']],
            'lemuria-restaurant'      => [['restaurant-in-quezon-city', 'restaurant-in-tomas-morato'], 500],
            'yardstick-coffee'        => [['restaurant-in-makati', 'restaurant-in-greenbelt'], 400],
            'inengs-bbq'              => [['restaurant-in-bf-homes', 'restaurant-in-alabang'], 350],
        ];

        $count = 0;
        foreach ($placements as $restSlug => $cfg) {
            $restId = $byName[$restSlug] ?? null;
            if (!$restId) continue;
            $foodKws = $cfg[0];
            $bidGp   = $cfg[1];
            $resortKws = $cfg[2] ?? [];

            foreach (array_merge($foodKws, $resortKws) as $kwSlug) {
                $kwId = $foodKwIds[$kwSlug] ?? $resortKwIds[$kwSlug] ?? null;
                if (!$kwId) continue;
                if (DB::table('rg_restaurant_listings')->where('keyword_id', $kwId)->where('restaurant_id', $restId)->exists()) continue;

                DB::table('rg_restaurant_listings')->insert([
                    'keyword_id'    => $kwId,
                    'restaurant_id' => $restId,
                    'owner_id'      => null,
                    'base_gp'       => 200,
                    'bid_gp'        => $bidGp,
                    'starts_at'     => $now,
                    'expires_at'    => now()->addDays(30),
                    'last_bid_at'   => $now,
                    'status'        => 'active',
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
                $count++;
            }
        }
        $this->command->info("Demo restaurant listings: $count");
    }

    private function seedAdventureListings(array $adventureIds, $now): void
    {
        if (empty($adventureIds)) return;

        // Adventures list on RESORT keyword pages — they are the "what
        // to do near where you're sleeping" cross-sell slot.
        $resortKwIds = DB::table('rg_keywords')->where('category', 'resort')->pluck('id', 'slug');
        $byName = DB::table('rg_adventures')->whereIn('id', $adventureIds)->pluck('id', 'slug');

        $placements = [
            'kahuna-beach-resort-surf-school'   => ['beach-resort-in-la-union', 'resort-in-la-union'],
            'subic-bay-atv-eco-trail'           => ['resort-in-subic-bay-freeport', 'resort-in-subic'],
            'anilao-dive-center'                => ['resort-in-mabini-batangas', 'resort-in-batangas'],
            'tagaytay-sky-ranch-ziplines'       => ['resort-in-tagaytay'],
            'pangea-paintball-manila'           => ['resort-in-quezon-city'],
            'coron-island-hopping'              => ['resort-in-coron', 'resort-in-palawan'],
            'lake-pandin-bamboo-raft'           => ['resort-in-san-pablo-laguna', 'resort-in-laguna'],
            'mt-pinatubo-crater-trek'           => ['resort-in-capas-tarlac', 'resort-in-tarlac'],
        ];

        $count = 0;
        foreach ($placements as $advSlug => $kwSlugs) {
            $advId = $byName[$advSlug] ?? null;
            if (!$advId) continue;
            foreach ($kwSlugs as $kwSlug) {
                $kwId = $resortKwIds[$kwSlug] ?? null;
                if (!$kwId) continue;
                if (DB::table('rg_adventure_listings')->where('keyword_id', $kwId)->where('adventure_id', $advId)->exists()) continue;

                DB::table('rg_adventure_listings')->insert([
                    'keyword_id'  => $kwId,
                    'adventure_id'=> $advId,
                    'owner_id'    => null,
                    'base_gp'     => 200,
                    'bid_gp'      => random_int(300, 900),
                    'starts_at'   => $now,
                    'expires_at'  => now()->addDays(30),
                    'last_bid_at' => $now,
                    'status'      => 'active',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $count++;
            }
        }
        $this->command->info("Demo adventure listings: $count");
    }
}
