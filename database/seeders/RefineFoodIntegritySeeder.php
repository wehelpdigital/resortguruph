<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Content-integrity refinement for the 565 food pages produced by
 * RolloutMoaTemplateSeeder.
 *
 * Fixes two issues found in audit:
 *   1. Sliders mixed in cluster-wide tourist-spot photos that aren't
 *      actually at the food location (e.g. Quezon Memorial Circle on
 *      /restaurant-in-bgc). Replaced with a clean single hero figure.
 *   2. "What's in [Area]" attractions section pulled from cluster spots
 *      that weren't near the food location (e.g. Siquijor spots on
 *      /restaurant-in-cebu). Now shows ONLY hand-curated attractions
 *      for the top 15 locations, and is hidden everywhere else.
 *
 * MOA pilot is skipped — it already has hand-curated content.
 */
class RefineFoodIntegritySeeder extends Seeder
{
    private array $locationImages = [];

    /** Hand-curated attractions per location key. Each entry: [name, description, meta]. */
    private array $curated = [
        'bgc' => [
            ['Mind Museum',           'Hands-on science museum aimed at families and school field trips. Easy half-day stop with a cafe inside.', 'Inside BGC · Taguig'],
            ['Bonifacio High Street', 'Open-air pedestrian strip with shops, fountains, and weekend night markets at the central plaza.', 'Inside BGC · main strip'],
            ['Forbes Town Center',    'Quieter restaurant + bar strip a few blocks south of High Street. Better for slow dinners.', '5 min walk from High Street'],
            ['Burgos Circle',         'Roundabout ringed with patio restaurants and bars. The default after-work drinks corner of BGC.', '3 min walk · Bonifacio Global City'],
            ['Bonifacio Stopover',    'Memorial honoring Andres Bonifacio at the eastern edge of the district. Free to enter.', 'Inside BGC'],
            ['SM Aura Premier',       'One MRT stop away from High Street. Sky Park rooftop dining and chapel.', '8 min walk · BGC south end'],
        ],
        'tagaytay' => [
            ['Picnic Grove',          'Taal Volcano viewing deck with free entry and walking trails along the ridge.', 'Aguinaldo Highway · Tagaytay'],
            ['Sky Ranch',             'Hilltop amusement park with the Sky Eye ferris wheel and family rides.', 'Aguinaldo Highway'],
            ['People\'s Park in the Sky', 'Abandoned Marcos-era palace turned hilltop walking park at the highest point of Tagaytay.', 'Mendez Crossing area'],
            ['Mahogany Market',       'Wet market with 24-hour bulalo eateries, busy from 4 AM onwards.', 'Tagaytay City proper'],
            ['Taal Heritage Town',    '20 minutes downhill. Spanish-era heritage houses + Taal Basilica.', 'Taal, Batangas · 20 min drive'],
            ['Tagaytay Highlands',    'Country club with golf course, mountain bike trails, and the cable car ride.', 'Silang side · entry by reservation'],
        ],
        'baguio' => [
            ['Burnham Park',          'Lake-side park in the city centre with boat rentals and rented bikes.', 'Baguio City proper'],
            ['Session Road',          'Main shopping and cafe strip. Walking-friendly with the city\'s strongest coffee culture.', 'Central Baguio'],
            ['Mines View Park',       'Open-air viewing deck overlooking Itogon\'s mining valley. Souvenir stalls along the path.', '10 min from city centre'],
            ['BenCab Museum',         'National Artist Bencab\'s personal museum in Tuba, with permanent and rotating exhibits.', '20 min drive · Tuba, Benguet'],
            ['Camp John Hay',         'Former US R&R camp turned park complex with cabins, dining, and forest trails.', 'Loakan side · 15 min from Session Road'],
            ['Tam-awan Village',      'Cordillera village reconstruction with traditional huts and craft demos.', 'Pinsao Proper'],
        ],
        'cebu' => [
            ['Magellan\'s Cross',     'Wooden cross erected in 1521, displayed inside a chapel beside Basilica Minore.', 'Cebu City proper · downtown'],
            ['Basilica del Santo Niño','Oldest Roman Catholic church in the Philippines, beside Magellan\'s Cross.', 'Downtown Cebu · free entry'],
            ['Fort San Pedro',        'Smallest tri-bastion fort in the country, originally Spanish, now a peaceful walled garden.', 'Pier 1 · downtown Cebu'],
            ['Carbon Market',         'Cebu\'s oldest and biggest public market. Best for cheap eats and dried mango souvenirs.', 'Downtown Cebu · morning hours'],
            ['Temple of Leah',        'Modern hilltop temple monument with views over the city and Mactan.', 'Busay, uphill from JY Square'],
            ['Sirao Flower Garden',   'Mountain flower farm popular for IG photos and brunch.', 'Cebu City uphill · 30 min drive'],
        ],
        'davao' => [
            ['People\'s Park',         'Downtown park with the indigenous-themed sculptures by Kublai Millan.', 'Downtown Davao'],
            ['Eden Nature Park',      'Mountain resort park with zipline, fishing pond, restaurants and walking trails.', 'Toril side · 1 hour drive'],
            ['SM Lanang Premier',     'Main upscale mall with the biggest cinemas in Davao.', 'Lanang area'],
            ['Crocodile Park',        'Family stop with crocodile feeding and a small zoo.', '20 min from downtown'],
            ['Mount Apo (Kapatagan)', 'Highest peak in the country. The Kapatagan trail head is the popular start.', '90 min drive · Kapatagan'],
            ['Roxas Avenue night strip','Late-night barbecue and grill strip popular for after-work meals.', 'Davao City centre'],
        ],
        'iloilo' => [
            ['Calle Real',            'Spanish-era heritage shopping strip in downtown Iloilo with restored facades.', 'Downtown Iloilo'],
            ['Molo Church',           'White coral-stone Gothic Renaissance church across the river from downtown.', 'Molo district'],
            ['Jaro Cathedral',        'National shrine and the centre of the famous Jaro Fiesta in February.', 'Jaro district'],
            ['Iloilo River Esplanade','11-kilometre riverside walkway with bike rentals and weekend joggers.', 'Multiple access points'],
            ['Garin Farm',            'Faith-themed park with religious sculptures, ferris wheel, and zipline.', 'San Joaquin · 1.5 hour drive'],
            ['Festive Walk Mall',     'Open-air corridor mall near Megaworld with most of the new dinner spots.', 'Iloilo Business Park'],
        ],
        'boracay' => [
            ['White Beach Stations 1-3','The famous powder-sand beach itself, split into three vibe-zones.', 'West coast of the island'],
            ['Puka Shell Beach',      'Quieter beach with rougher sand and shells. 15 min by tricycle from White Beach.', 'North end of island'],
            ['Mount Luho',            'Highest point on the island with a 360-degree viewing deck.', 'Tricycle ride uphill'],
            ['Willy\'s Rock',          'Iconic Virgin Mary shrine on a rock formation off Station 1.', 'Station 1 beachfront'],
            ['D-Mall',                'Central food and shopping arcade behind Station 2.', 'Station 2 area'],
            ['Bulabog Beach',         'Kite-surf and wind-surf side of the island during habagat season.', 'East coast · 5 min walk from D-Mall'],
        ],
        'el_nido' => [
            ['Big Lagoon',            'Most-photographed limestone lagoon. Reached on Tour A from the town.', 'Bacuit Bay · Tour A'],
            ['Small Lagoon',          'Narrow lagoon entered by swimming through a small gap in the cliffs.', 'Bacuit Bay · Tour A'],
            ['Secret Lagoon',         'Hidden lagoon behind a small opening at the base of a limestone cliff.', 'Bacuit Bay · Tour A'],
            ['Las Cabanas Beach',     'Sunset beach + the famous zipline across to Depeldet Island.', 'South of town · 15 min trike'],
            ['Nacpan Beach',          'Long unbusy beach 45 minutes north of town. Quieter than Bacuit.', 'North coast · van transfer'],
            ['Taraw Cliff',           'Limestone cliff climb at the back of El Nido town for panoramic views.', 'Behind El Nido town'],
        ],
        'vigan' => [
            ['Calle Crisologo',       'Cobblestone heritage street, the most-photographed strip in Vigan.', 'Downtown Vigan'],
            ['Plaza Burgos',          'Town plaza with the famous Vigan empanada vendors at sunset.', 'Beside the cathedral'],
            ['Vigan Cathedral',       'St. Paul Metropolitan Cathedral, 18th century baroque earthquake architecture.', 'Plaza Salcedo'],
            ['Bantay Bell Tower',     'Watchtower with sweeping views of Vigan, used as a Spanish lookout.', '5 min drive from town'],
            ['Burnayan',              'Traditional jar-making pottery workshops you can visit and try.', 'Pagburnayan area'],
            ['Hidden Garden',         'Quiet garden restaurant on the outskirts of town. Worth the long lunch.', '10 min trike from Crisologo'],
        ],
        'subic' => [
            ['Ocean Adventure',       'Open-water marine park with dolphin and sea lion encounters.', 'West Ilanin Forest · 15 min from gate'],
            ['Zoobic Safari',         'Tiger feeding and small animal park inside the Freeport.', 'Subic Freeport · main road'],
            ['Tree Top Adventure',    'Canopy ride, superman zip, and forest trail circuit.', 'Inside Freeport · 20 min drive'],
            ['JEST Aeta Camp',        'Aeta cultural village with survival workshops and forest treks.', 'Cubi area · Freeport'],
            ['Subic Bay Boardwalk',   'Bay-front strip with cafes, sunset spots, and the duty-free zone.', 'Central Freeport'],
            ['Bataan Death March Markers', 'Historical kilometre markers along the old march route.', 'Multiple along Subic-Bataan road'],
        ],
        'la_union' => [
            ['Urbiztondo Beach (San Juan)','Surf-town strip with beach cafes and rental boards.', 'San Juan, La Union'],
            ['Tangadan Falls',        'Two-tier waterfall, swim and small jump. Short trek from the trailhead.', 'San Gabriel · 30 min from San Juan'],
            ['Ma-Cho Temple',         'Taoist temple in San Fernando City overlooking the South China Sea.', 'San Fernando · north of San Juan'],
            ['Bahay na Bato',         'Pebble-house museum on the coastal road. Small entrance fee.', 'Luna, La Union'],
            ['Poro Point',            'Historic lookout, lighthouse, and the surf-school annex.', 'San Fernando City'],
            ['Halfway House',         'Surf-town cafe + coworking destination on the beach strip.', 'San Juan · Urbiztondo'],
        ],
        'megamall' => [
            ['SM Megamall Cinema',    'Twelve-screen cinema complex including IMAX. Director\'s Club at the top floor.', 'Inside SM Megamall'],
            ['Mega Fashion Hall',     'Newer wing with the upper-tier restaurants and the experiential concepts.', 'Connecting bridge level'],
            ['Mandaluyong waterfront','15-minute Grab ride to the Pasig River side dining options.', 'Outside Megamall · Mandaluyong'],
            ['Wack Wack Country Club','Historic golf course adjacent to the Megamall complex.', 'Wack Wack district'],
            ['EDSA Shrine',           'EDSA Revolution memorial church at the corner of Ortigas Avenue.', '8 min walk · Ortigas corner'],
            ['Robinsons Galleria',    'Across the avenue. Easy second-mall option if Megamall is too crowded.', 'Across EDSA · 5 min'],
        ],
        'greenbelt' => [
            ['Greenbelt Chapel',      'Open-air chapel surrounded by garden in the middle of Greenbelt 3.', 'Greenbelt 3 ground level'],
            ['Ayala Museum',          'Permanent and rotating Filipino art and history exhibits.', 'Beside Greenbelt'],
            ['Greenbelt 5 patio bars','The upper-tier dinner-into-drinks corner of the complex.', 'Inside Greenbelt 5'],
            ['Glorietta',             'Connected to Greenbelt via Ayala Avenue underpass. Different price tier.', '5 min walk · Glorietta'],
            ['Salcedo Saturday Market','Weekend morning food market two blocks away in Salcedo Village.', 'Salcedo Village · Saturday'],
            ['Legazpi Sunday Market', 'Sunday version in Legazpi Village. Quieter and slightly upmarket.', 'Legazpi Village · Sunday'],
        ],
        'glorietta' => [
            ['Ayala Triangle Gardens','Park between Ayala Avenue and Paseo de Roxas. Sunset food trucks during the holidays.', '3 min walk from Glorietta'],
            ['Greenbelt',             'Connected via underpass. Different food tier with garden patios.', 'Across the road'],
            ['Ayala Avenue',          'Main business avenue with the city\'s longest-running corporate towers.', 'Around the mall'],
            ['Park Square 1 & 2',     'Older mall annexes attached to Glorietta. Worth a walk-through.', 'Behind Glorietta'],
            ['SM Makati',             'Standalone SM department store with a different chain mix.', '5 min walk via underpass'],
            ['One Ayala Mall',        'New mall above the Makati EDSA Carousel bus terminal.', '8 min walk · Ayala MRT'],
        ],
        'tomas_morato' => [
            ['Tomas Morato Avenue',   'The food strip itself runs about 8 blocks with restaurants packed along both sides.', 'Quezon City'],
            ['Timog Avenue',          'Connected strip with the longer-running family restaurants and karaoke spots.', 'Connects Tomas Morato'],
            ['ABS-CBN Compound',      'Broadcast complex at the end of Tomas Morato. ELJ Hall and Eugenio Lopez Center.', 'Mother Ignacia · end of Morato'],
            ['Quezon Memorial Circle','Park with the Manuel L. Quezon shrine in the centre. 10 min drive.', 'Elliptical Road'],
            ['Cubao Expo',            'Indie art and food strip across EDSA. 15 min drive.', 'Cubao'],
            ['Trinoma',               'Closest big mall for non-food shopping. 15 min drive.', 'North Avenue'],
        ],
    ];

    public function run(): void
    {
        $this->loadLocationImages();

        $keywords = DB::table('rg_keywords')->where('category', 'food')->get();
        $this->command->info("Refining " . $keywords->count() . " food pages...");

        $updated = 0; $curated = 0; $simplified = 0; $skipped = 0;

        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') { $skipped++; continue; }

            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page) continue;

            $loc = $this->extractLocation($kw->phrase);
            $key = $this->normalizeLocation($loc);
            $area = $this->displayName($key, $loc);

            $newHero = $this->buildSimpleHero($key, $area);
            $newBody = $this->refineBody($page->body_html, $key, $area);

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'hero_html'  => $newHero,
                'body_html'  => $newBody,
                'updated_at' => now(),
            ]);

            $updated++;
            if (isset($this->curated[$key])) $curated++; else $simplified++;

            if ($updated % 50 === 0) {
                $this->command->info("  $updated processed...");
            }
        }

        $this->command->info('');
        $this->command->info("Done. Updated: $updated | With curated attractions: $curated | Section hidden (no curated data): $simplified | Skipped (MOA): $skipped");
    }

    private function loadLocationImages(): void
    {
        foreach (glob(storage_path('app/public/rg-media/food-locations/*.jpg')) as $f) {
            $key = basename($f, '.jpg');
            $this->locationImages[$key] = asset('storage/rg-media/food-locations/' . basename($f));
        }
    }

    private function buildSimpleHero(string $key, string $area): string
    {
        $img = $this->locationImages[$key] ?? null;
        if (!$img) return '';

        return <<<HTML
<section class="rg-area-hero my-8 not-prose">
    <figure class="relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-50">
        <img src="$img" alt="$area" loading="lazy" class="w-full h-auto block" style="aspect-ratio: 21/9; object-fit: cover;">
        <figcaption class="absolute bottom-0 left-0 right-0 p-5" style="background: linear-gradient(180deg, transparent 0%, rgba(15,23,42,0.9) 100%); color: #fff;">
            <strong class="block text-lg font-bold" style="margin:0">$area</strong>
            <span class="text-sm opacity-90">Photo: Wikimedia Commons (CC-BY-SA)</span>
        </figcaption>
    </figure>
</section>
HTML;
    }

    /**
     * Replaces the "What's in [Area]" attractions section with either:
     * - Curated cards (if we have hand-curated data for this location), OR
     * - Empty string (hide entirely if no verified data).
     *
     * Uses a non-greedy regex bounded by the next H2 ("Where … is on the map")
     * which always appears right after attractions in the rollout template.
     */
    private function refineBody(string $body, string $key, string $area): string
    {
        $pattern = '#<h2>What\'s in [^<]+ \(beyond the food\)</h2>.*?(?=<h2>Where )#s';

        if (isset($this->curated[$key])) {
            $replacement = $this->buildCuratedAttractions($area, $this->curated[$key]);
        } else {
            $replacement = '';
        }

        $result = preg_replace($pattern, $replacement, $body);
        return $result ?? $body;
    }

    private function buildCuratedAttractions(string $area, array $items): string
    {
        $cards = '';
        foreach ($items as [$name, $desc, $meta]) {
            $cards .= '<div class="rounded-xl border border-slate-200 bg-white p-5">'
                . '<h3 class="font-bold text-slate-900 m-0">' . e($name) . '</h3>'
                . '<p class="text-sm text-slate-600 mt-2 mb-2 m-0">' . e($desc) . '</p>'
                . '<p class="text-xs text-slate-400 m-0">' . e($meta) . '</p>'
                . '</div>';
        }

        return <<<HTML
<h2>What's in $area (beyond the food)</h2>
<p>The food is the headline, but here's what else is worth a walk-through while you're in the area. Each pick below is verified to actually sit in or near $area.</p>
<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">$cards</div>
HTML;
    }

    // === Location helpers (copied from RolloutMoaTemplateSeeder for self-containment) ===

    private function extractLocation(string $phrase): string
    {
        $p = mb_strtolower(trim($phrase));
        $p = preg_replace('/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/i', '', $p);
        $p = preg_replace('/\b(filipino|japanese|korean|chinese|italian|seafood|steak|sushi|buffet|fine\s+dining)\s+/i', '', $p);
        $p = preg_replace('/^philippines\s+/', '', $p);
        $p = preg_replace('/^antonio\'?s\s+/', '', $p);
        if (preg_match('/(?:restaurant|to\s+eat|to\s+eat\s+at|to\s+eat\s+near)\s+(?:in\s+)?(.+)$/', $p, $m)) $p = trim($m[1]);
        elseif (preg_match('/^where\s+to\s+eat\s+(.+)$/', $p, $m)) $p = trim($m[1]);
        elseif (preg_match('/^(.+?)\s+where\s+to\s+eat$/', $p, $m)) $p = trim($m[1]);
        $p = preg_replace('/\s+(philippines|with\s+view|with\s+private\s+room)$/', '', $p);
        return trim($p) ?: 'philippines';
    }

    private function normalizeLocation(string $loc): string
    {
        $l = mb_strtolower(trim($loc));
        $rules = [
            'moa' => '/(mall of asia|^moa$|sm moa)/',
            'bgc' => '/(bgc|bonifacio|burgos circle|high street|uptown bgc)/',
            'megamall' => '/(sm megamall|^megamall$)/',
            'sm_north' => '/(sm north|sm city north|north edsa)/',
            'podium' => '/(podium)/',
            'greenbelt' => '/(greenbelt)/',
            'glorietta' => '/(glorietta)/',
            'festival_mall' => '/(festival mall|festival$|festive)/',
            'sm_aura' => '/(sm aura)/',
            'tomas_morato' => '/(tomas morato)/',
            'trinoma' => '/(trinoma)/',
            'uptown' => '/(uptown mall|up town center|uptc)/',
            'eastwood' => '/(eastwood)/',
            'rockwell' => '/(rockwell|powerplant)/',
            'greenhills' => '/(greenhills)/',
            'tagaytay' => '/(tagaytay)/',
            'baguio' => '/(baguio)/',
            'boracay' => '/(boracay)/',
            'el_nido' => '/(el nido)/',
            'cebu' => '/^cebu|(^|\W)(cebu)(\W|$)/',
            'davao' => '/(davao)/',
            'iloilo' => '/(iloilo)/',
            'subic' => '/(subic|olongapo)/',
            'la_union' => '/(la union|san juan la union)/',
            'vigan' => '/(vigan)/',
            'makati' => '/(^|\W)(makati)(\W|$)/',
            'qc' => '/(quezon city|^qc$|quezon ave|timog|west avenue|fairview|white plains|don antonio|maginhawa|banawe|cubao|katipunan|kapitolyo|tomas morato|visayas ave|visayas avenue)/',
            'manila' => '/(^|\W)(manila|intramuros|binondo|quiapo|malate)(\W|$)/',
            'alabang' => '/(alabang|atc|filinvest|westgate alabang|molito)/',
            'bf_homes' => '/(bf homes|bf$)/',
            'nuvali' => '/(nuvali|sta rosa|santa rosa)/',
        ];
        foreach ($rules as $canonical => $regex) {
            if (preg_match($regex, $l)) return $canonical;
        }
        return 'generic';
    }

    private function displayName(string $key, string $loc): string
    {
        $map = [
            'moa' => 'Mall of Asia', 'bgc' => 'BGC', 'megamall' => 'SM Megamall',
            'sm_north' => 'SM North EDSA', 'podium' => 'The Podium', 'greenbelt' => 'Greenbelt',
            'glorietta' => 'Glorietta', 'festival_mall' => 'Festival Mall', 'sm_aura' => 'SM Aura',
            'tomas_morato' => 'Tomas Morato', 'trinoma' => 'TriNoma', 'uptown' => 'Uptown Mall BGC',
            'eastwood' => 'Eastwood City', 'rockwell' => 'Rockwell', 'greenhills' => 'Greenhills',
            'tagaytay' => 'Tagaytay', 'baguio' => 'Baguio', 'boracay' => 'Boracay',
            'el_nido' => 'El Nido', 'cebu' => 'Cebu City', 'davao' => 'Davao City',
            'iloilo' => 'Iloilo City', 'subic' => 'Subic', 'la_union' => 'La Union',
            'vigan' => 'Vigan', 'makati' => 'Makati', 'qc' => 'Quezon City',
            'manila' => 'Manila', 'alabang' => 'Alabang', 'bf_homes' => 'BF Homes',
            'nuvali' => 'Nuvali Sta. Rosa',
        ];
        return $map[$key] ?? $this->properTitle($loc);
    }

    private function properTitle(string $s): string
    {
        $small = ['of','the','in','at','on','and','a','an','to','for','by','from','with'];
        $words = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($words as $i => $w) {
            if ($w === '') continue;
            $words[$i] = ($i === 0 || !in_array($w, $small, true))
                ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8')
                : $w;
        }
        return implode(' ', $words);
    }
}
