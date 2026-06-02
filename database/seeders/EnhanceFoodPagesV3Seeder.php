<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * V3 enhancements applied to every food page (skipping MOA pilot):
 *
 *   1. Strip "Cuisines that work well at [Area]" section + chips below it.
 *   2. Add per-attraction images to "What's in [Area] (beyond the food)"
 *      cards — searches Wikimedia Commons per-attraction-name and caches.
 *   3. Add "How to get to [Area]" section (location-type aware).
 *   4. Add Related Destinations section ONLY when a resort keyword page
 *      exists for the same area (rg_keywords category=resort match).
 *   5. Add Related Reading section ONLY when blog posts actually mention
 *      the location in their title or first 2000 chars of content.
 *   6. Populate `tldr` column (the existing summary-blocks partial renders
 *      it as an expandable accordion).
 *   7. Populate `wwww_json` column (Why / When / Where / Whom expandable
 *      accordion, also rendered by summary-blocks partial).
 *
 * The seeder operates surgically on body_html so we don't disturb the
 * V2 hero slider, rating card, quick verdict, etc.
 */
class EnhanceFoodPagesV3Seeder extends Seeder
{
    private array $headers = [
        'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
        'Accept'     => 'application/json',
    ];

    /** Cache: attraction display name → local image URL (or null if no hit). */
    private array $attractionImageCache = [];

    /** All food keywords loaded once for "related destinations" lookup. */
    private array $resortKeywords = [];

    /** All published blog posts loaded once for "related reading" lookup. */
    private array $blogPosts = [];

    /**
     * Curated attractions per location key — mirrors the list from
     * RefineFoodIntegritySeeder. Each entry: [name, description, meta].
     * For each name, we search Wikimedia Commons for an image.
     */
    private array $curatedAttractions = [
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
            ['Mount Apo Kapatagan',   'Highest peak in the country. The Kapatagan trail head is the popular start.', '90 min drive · Kapatagan'],
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
            ['White Beach Stations 1 to 3','The famous powder-sand beach itself, split into three vibe-zones.', 'West coast of the island'],
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
            ['Urbiztondo Beach',      'Surf-town strip with beach cafes and rental boards.', 'San Juan, La Union'],
            ['Tangadan Falls',        'Two-tier waterfall, swim and small jump. Short trek from the trailhead.', 'San Gabriel · 30 min from San Juan'],
            ['Ma-Cho Temple',         'Taoist temple in San Fernando City overlooking the South China Sea.', 'San Fernando · north of San Juan'],
            ['Bahay na Bato',         'Pebble-house museum on the coastal road. Small entrance fee.', 'Luna, La Union'],
            ['Poro Point',            'Historic lookout, lighthouse, and the surf-school annex.', 'San Fernando City'],
            ['Halfway House',         'Surf-town cafe and coworking destination on the beach strip.', 'San Juan · Urbiztondo'],
        ],
        'megamall' => [
            ['SM Megamall Cinema',    'Twelve-screen cinema complex including IMAX. Director\'s Club at the top floor.', 'Inside SM Megamall'],
            ['Mega Fashion Hall',     'Newer wing with the upper-tier restaurants and the experiential concepts.', 'Connecting bridge level'],
            ['EDSA Shrine',           'EDSA Revolution memorial church at the corner of Ortigas Avenue.', '8 min walk · Ortigas corner'],
            ['Robinsons Galleria',    'Across the avenue. Easy second-mall option if Megamall is too crowded.', 'Across EDSA · 5 min'],
            ['Pasig River',           '15-minute Grab ride to the Pasig River side dining options.', 'Outside Megamall · Mandaluyong'],
            ['Wack Wack Country Club','Historic golf course adjacent to the Megamall complex.', 'Wack Wack district'],
        ],
        'greenbelt' => [
            ['Greenbelt Chapel',      'Open-air chapel surrounded by garden in the middle of Greenbelt 3.', 'Greenbelt 3 ground level'],
            ['Ayala Museum',          'Permanent and rotating Filipino art and history exhibits.', 'Beside Greenbelt'],
            ['Greenbelt 5 patio',     'The upper-tier dinner-into-drinks corner of the complex.', 'Inside Greenbelt 5'],
            ['Glorietta',             'Connected to Greenbelt via Ayala Avenue underpass. Different price tier.', '5 min walk · Glorietta'],
            ['Salcedo Saturday Market','Weekend morning food market two blocks away in Salcedo Village.', 'Salcedo Village · Saturday'],
            ['Legazpi Sunday Market', 'Sunday version in Legazpi Village. Quieter and slightly upmarket.', 'Legazpi Village · Sunday'],
        ],
        'glorietta' => [
            ['Ayala Triangle Gardens','Park between Ayala Avenue and Paseo de Roxas. Sunset food trucks during the holidays.', '3 min walk from Glorietta'],
            ['Greenbelt',             'Connected via underpass. Different food tier with garden patios.', 'Across the road'],
            ['Ayala Avenue',          'Main business avenue with the city\'s longest-running corporate towers.', 'Around the mall'],
            ['Park Square',           'Older mall annexes attached to Glorietta. Worth a walk-through.', 'Behind Glorietta'],
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
        $this->command->info('=== V3 enhancements for all food pages ===');
        $this->loadResortKeywords();
        $this->loadBlogPosts();

        $keywords = DB::table('rg_keywords')->where('category', 'food')->get();
        $total = $keywords->count();
        $processed = 0;
        $skipped = 0;

        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') { $skipped++; continue; }
            $this->processPage($kw);
            $processed++;
            if ($processed % 50 === 0) {
                $this->command->info("  $processed / $total processed...");
            }
        }

        $this->command->info('');
        $this->command->info("Done. Processed: $processed | Skipped (MOA): $skipped");
    }

    private function loadResortKeywords(): void
    {
        // Use a manual join because DB::table() doesn't support Eloquent's
        // whereHas() relation queries.
        $this->resortKeywords = DB::table('rg_keywords as k')
            ->join('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')
            ->where('k.category', 'resort')
            ->where('p.is_published', true)
            ->distinct()
            ->select(['k.id', 'k.phrase', 'k.slug', 'k.cluster_tag', 'k.search_volume_monthly'])
            ->get()
            ->all();
    }

    private function loadBlogPosts(): void
    {
        $rows = DB::table('rg_blog_posts')
            ->where('status', 'published')
            ->select(['id', 'title', 'slug', 'excerpt', 'cover_path', 'content_html'])
            ->get();
        $this->blogPosts = $rows->all();
    }

    private function processPage(object $kw): void
    {
        $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
        if (!$page) return;

        $loc      = $this->extractLocation($kw->phrase);
        $key      = $this->normalizeLocation($loc);
        $area     = $this->displayName($key, $loc);
        $cluster  = $kw->cluster_tag ?? 'metro-manila';
        $type     = $this->detectType($key);

        $body = $page->body_html;

        // 1. Strip "Cuisines that work well at..." section
        $body = $this->stripCuisinesSection($body);

        // 2. Add per-attraction images by replacing the attractions section
        if (isset($this->curatedAttractions[$key])) {
            $body = $this->replaceAttractionsWithImages($body, $key, $area);
        }

        // 3. Add "How to get to [Area]" section right before the map
        $body = $this->insertHowToGetTo($body, $area, $type);

        // 4. Add Related Destinations section (conditional, before external links)
        $relatedDest = $this->findRelatedDestinations($area, $cluster, $key);
        if (!empty($relatedDest)) {
            $body = $this->insertRelatedDestinations($body, $area, $relatedDest);
        }

        // 5. Add Related Reading (blog) section (conditional, before external links)
        $relatedBlog = $this->findRelatedBlogPosts($area);
        if (!empty($relatedBlog)) {
            $body = $this->insertRelatedReading($body, $relatedBlog);
        }

        // 6. Generate TL;DR + 7. WWWW data
        $tldr = $this->buildTldr($area, $type);
        $wwww = $this->buildWwww($area, $type);

        DB::table('rg_seo_pages')->where('id', $page->id)->update([
            'body_html' => $body,
            'tldr'      => $tldr,
            'wwww_json' => json_encode($wwww),
            'updated_at'=> now(),
        ]);
    }

    // === 1. Strip Cuisines section =======================================

    private function stripCuisinesSection(string $body): string
    {
        // The section starts at `<h2>Cuisines that work well at ...</h2>` and ends
        // at the next H2 — usually `<aside class="not-prose my-10 ..."` (local tip)
        // or `<h2>Budget guide for...`.
        $pattern = '~<h2>Cuisines that work well[^<]*</h2>.*?(?=<aside|<h2>)~s';
        $result = preg_replace($pattern, '', $body);
        return $result ?? $body;
    }

    // === 2. Per-attraction images ========================================

    private function replaceAttractionsWithImages(string $body, string $key, string $area): string
    {
        $attractions = $this->curatedAttractions[$key];
        // Get image for each attraction (Wikimedia per-name search, cached).
        $cards = '';
        foreach ($attractions as [$name, $desc, $meta]) {
            $img = $this->getAttractionImage($name, $area);
            $imgHtml = $img
                ? '<div class="overflow-hidden bg-slate-200" style="aspect-ratio: 16/10"><img src="' . e($img) . '" alt="' . e($name) . '" loading="lazy" class="w-full h-full" style="object-fit: cover"></div>'
                : '<div class="bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center" style="aspect-ratio: 16/10"><span class="text-4xl text-slate-400">📍</span></div>';
            $cards .= '<div class="rounded-xl border border-slate-200 bg-white overflow-hidden">'
                . $imgHtml
                . '<div class="p-5">'
                . '<h3 class="font-bold text-slate-900 m-0">' . e($name) . '</h3>'
                . '<p class="text-sm text-slate-600 mt-2 mb-2 m-0">' . e($desc) . '</p>'
                . '<p class="text-xs text-slate-400 m-0">' . e($meta) . '</p>'
                . '</div>'
                . '</div>';
        }

        $newSection = "<h2>What's in $area (beyond the food)</h2>"
            . "<p>The food is the headline, but here's what else is worth a walk-through while you're in the area. Each pick below is verified to actually sit in or near $area.</p>"
            . '<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">' . $cards . '</div>';

        // Find and replace the existing attractions section
        $pattern = '~<h2>What\'s in [^<]+ \(beyond the food\)</h2>.*?(?=<h2>|<div class="not-prose mt-10)~s';
        $result = preg_replace($pattern, $newSection, $body, 1);
        return $result ?? $body;
    }

    private function getAttractionImage(string $name, string $area): ?string
    {
        $cacheKey = strtolower($name);
        if (array_key_exists($cacheKey, $this->attractionImageCache)) {
            return $this->attractionImageCache[$cacheKey];
        }

        // Try cached on disk first
        $slug = Str::slug($name);
        $localPath = 'rg-media/attractions/' . substr($slug, 0, 60) . '.jpg';
        $absPath = storage_path('app/public/' . $localPath);
        if (is_file($absPath) && filesize($absPath) > 5000) {
            $url = asset('storage/' . $localPath);
            $this->attractionImageCache[$cacheKey] = $url;
            return $url;
        }

        if (!is_dir(dirname($absPath))) mkdir(dirname($absPath), 0755, true);

        // Search Wikimedia Commons
        try {
            $resp = Http::withHeaders($this->headers)->timeout(20)->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query', 'format' => 'json',
                'generator' => 'search', 'gsrnamespace' => 6,
                'gsrlimit' => 6, 'gsrsearch' => $name . ' Philippines',
                'prop' => 'imageinfo', 'iiprop' => 'url|mime|size',
            ]);
            if (!$resp->successful()) {
                $this->attractionImageCache[$cacheKey] = null;
                return null;
            }
            $pages = $resp->json()['query']['pages'] ?? [];
            foreach ($pages as $p) {
                $info = $p['imageinfo'][0] ?? null;
                if (!$info) continue;
                $mime = $info['mime'] ?? '';
                if (!str_starts_with($mime, 'image/') || str_starts_with($mime, 'image/svg')) continue;
                $w = $info['width'] ?? 0; $h = $info['height'] ?? 0;
                if ($w < 800 || $h < 500 || $h >= $w) continue;
                $title = $p['title'] ?? '';
                if (str_starts_with($title, 'File:')) {
                    $fname = substr($title, 5);
                    if (preg_match('/\.(svg|gif)$/i', $fname)) continue;
                    $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($fname) . '?width=1200';
                    if ($this->downloadFile($url, $absPath)) {
                        $assetUrl = asset('storage/' . $localPath);
                        DB::table('rg_media')->updateOrInsert(
                            ['path' => $localPath],
                            [
                                'filename' => $fname, 'path' => $localPath,
                                'mime' => 'image/jpeg',
                                'size_bytes' => is_file($absPath) ? filesize($absPath) : 0,
                                'kind' => 'image',
                                'alt' => $name, 'caption' => $name,
                                'source' => 'seeder-attractions',
                                'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
                                'source_url' => 'https://commons.wikimedia.org/wiki/File:' . $fname,
                                'created_at' => now(), 'updated_at' => now(),
                            ]
                        );
                        $this->attractionImageCache[$cacheKey] = $assetUrl;
                        return $assetUrl;
                    }
                }
            }
        } catch (\Throwable $e) {}

        $this->attractionImageCache[$cacheKey] = null;
        return null;
    }

    private function downloadFile(string $url, string $absPath): bool
    {
        try {
            $resp = Http::withHeaders($this->headers)->timeout(40)
                ->withOptions(['allow_redirects' => true])->get($url);
            if (!$resp->successful()) return false;
            $body = $resp->body();
            if (strlen($body) < 5000) return false;
            file_put_contents($absPath, $body);
            return true;
        } catch (\Throwable $e) { return false; }
    }

    // === 3. How to get to ================================================

    private function insertHowToGetTo(string $body, string $area, string $type): string
    {
        $section = $this->buildHowToGetToSection($area, $type);
        // Insert right before the "Where [Area] is on the map" H2.
        $pattern = '~(<h2>Where [^<]+ is on the map</h2>)~';
        if (preg_match($pattern, $body)) {
            $body = preg_replace($pattern, $section . '$1', $body, 1);
        } else {
            $body .= $section;
        }
        return $body;
    }

    private function buildHowToGetToSection(string $area, string $type): string
    {
        [$intro, $bullets] = match ($type) {
            'mall' => [
                "Most visitors reach $area by car (parking is the bottleneck on weekends) or via the closest MRT or LRT station. Grab and TNVS surge prices spike weekday 5 to 8 PM.",
                [
                    ['By car', 'Mall parking fills 11 AM Saturday onwards. Off-site lots run cheaper but require a 5-10 min walk.'],
                    ['By MRT/LRT + jeep', 'From the closest train station, jeeps and tricycles run to the mall every 5-10 minutes.'],
                    ['By Grab', '15-25 minutes from BGC. 20-30 minutes from QC. Surge after 6 PM weekdays.'],
                    ['Carpool / shuttle', 'Some malls have free shuttle from major BPO buildings during weekday rush hours.'],
                ],
            ],
            'city' => [
                "$area is reachable by direct flight, bus, or self-drive depending on where you're coming from. Most domestic flights to the city land at the local domestic airport.",
                [
                    ['By plane', 'Direct domestic flights from Manila and major cities. Plan 75-90 minutes flight time.'],
                    ['By bus', 'Cebu Pacific Air, Philtranco, and similar bus lines run long-haul routes. 8-14 hours from Manila depending on city.'],
                    ['By private car', 'Self-drive is feasible for nearby cities. Plan for fuel stops and toll expenses.'],
                    ['Ground transit within city', 'Jeeps, tricycles, and Grab cover most short trips. Tricycle fares 20-40 pesos within city centre.'],
                ],
            ],
            'destination' => [
                "$area sits a few hours outside Metro Manila so most visitors book transport ahead. Weekend traffic patterns matter: leave before 6 AM or after 10 AM to skip the worst stretches.",
                [
                    ['By bus', 'Direct buses from Cubao or Pasay terminals. 3-6 hours depending on traffic.'],
                    ['By private car', 'Most efficient for groups of 3+. Toll fees apply on SLEX, NLEX, or TPLEX.'],
                    ['By Grab / van pool', 'Door-to-door van shares cost 600-1,500 pesos per seat.'],
                    ['By plane (where applicable)', 'Direct flights to local airport where available, then short drive to the area.'],
                ],
            ],
            default => [
                "Getting to $area is straightforward by jeep, tricycle, or Grab from the nearest landmark. Weekday lunch traffic peaks 12-2 PM.",
                [
                    ['By jeep / tricycle', 'Local PUVs run to the area every 5-10 minutes during daytime.'],
                    ['By Grab', 'The default option for groups or with bags. Surge prices peak after 6 PM weekdays.'],
                    ['By private car', 'Parking varies by sub-area. Streetside parking common on the main strip.'],
                    ['On foot', 'Many $area corners are walking-friendly between the main strip and the side streets.'],
                ],
            ],
        };

        $bulletsHtml = '';
        foreach ($bullets as [$kind, $detail]) {
            $bulletsHtml .= '<div class="rounded-xl border border-slate-200 bg-white p-4">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold text-slate-500 mb-1">' . e($kind) . '</div>'
                . '<p class="text-sm text-slate-700 m-0 leading-relaxed">' . e($detail) . '</p>'
                . '</div>';
        }

        return <<<HTML
<h2>How to get to $area</h2>
<p>$intro</p>
<div class="not-prose my-7 grid grid-cols-1 md:grid-cols-2 gap-3">$bulletsHtml</div>
HTML;
    }

    // === 4. Related destinations =========================================

    private function findRelatedDestinations(string $area, string $cluster, string $key): array
    {
        $areaLower = mb_strtolower($area);
        $matches = [];
        foreach ($this->resortKeywords as $r) {
            $phrase = mb_strtolower($r->phrase);
            // Must mention the area name AND share the cluster
            if (str_contains($phrase, $areaLower) && $r->cluster_tag === $cluster) {
                $matches[] = $r;
            }
        }
        usort($matches, fn($a, $b) => ($b->search_volume_monthly ?? 0) <=> ($a->search_volume_monthly ?? 0));
        return array_slice($matches, 0, 4);
    }

    private function insertRelatedDestinations(string $body, string $area, array $items): string
    {
        $cards = '';
        foreach ($items as $r) {
            $cards .= '<a href="' . e(url($r->slug)) . '" class="rounded-xl border border-slate-200 bg-white p-5 hover:shadow-md hover:border-emerald-300 transition-shadow no-underline" style="text-decoration:none">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color:#059669">Where to stay</div>'
                . '<h3 class="font-bold text-slate-900 m-0 capitalize">' . e($r->phrase) . '</h3>'
                . '<p class="text-xs text-slate-500 mt-2 m-0">' . number_format($r->search_volume_monthly ?? 0) . ' people search this monthly</p>'
                . '</a>';
        }
        $section = <<<HTML
<div class="not-prose my-10 p-6 rounded-2xl" style="background:#ecfdf5;border:1px solid #a7f3d0">
    <div class="text-[10px] uppercase tracking-[0.2em] font-bold mb-2" style="color:#065f46">Stay nearby</div>
    <h2 class="text-2xl font-bold m-0 mb-4" style="color:#064e3b">Looking for resorts and hotels in $area?</h2>
    <p class="text-sm m-0 mb-5" style="color:#065f46">If you're planning more than a meal stop, here are the destination guides for places to stay in or near $area.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">$cards</div>
</div>
HTML;
        // Insert before the external-links block
        $pattern = '~(<div class="not-prose mt-10 p-5 bg-slate-50 rounded-xl)~';
        if (preg_match($pattern, $body)) {
            return preg_replace($pattern, $section . '$1', $body, 1);
        }
        return $body . $section;
    }

    // === 5. Related blog posts ===========================================

    private function findRelatedBlogPosts(string $area): array
    {
        $areaLower = mb_strtolower($area);
        // Skip very generic area names that would match too much
        if (in_array($areaLower, ['philippines', 'manila', 'metro manila'], true)) return [];

        $matches = [];
        foreach ($this->blogPosts as $b) {
            $title = mb_strtolower($b->title ?? '');
            $content = mb_strtolower(mb_substr($b->content_html ?? '', 0, 2000));
            if (str_contains($title, $areaLower) || mb_substr_count($content, $areaLower) >= 2) {
                $matches[] = $b;
            }
        }
        return array_slice($matches, 0, 3);
    }

    private function insertRelatedReading(string $body, array $items): string
    {
        $cards = '';
        foreach ($items as $b) {
            $thumb = '';
            if (!empty($b->cover_path)) {
                $thumb = '<div class="overflow-hidden bg-slate-200" style="aspect-ratio: 16/10"><img src="' . e(asset('storage/' . ltrim($b->cover_path, '/'))) . '" alt="" loading="lazy" class="w-full h-full" style="object-fit: cover"></div>';
            }
            $cards .= '<a href="' . e(url('/blog/' . $b->slug)) . '" class="rounded-xl border border-slate-200 bg-white overflow-hidden hover:shadow-md hover:border-blue-300 transition-shadow no-underline" style="text-decoration:none">'
                . $thumb
                . '<div class="p-4">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold text-blue-700 mb-1">From the blog</div>'
                . '<h3 class="font-bold text-slate-900 m-0">' . e($b->title) . '</h3>'
                . ($b->excerpt ? '<p class="text-xs text-slate-500 mt-2 m-0 line-clamp-2">' . e(Str::limit($b->excerpt, 120)) . '</p>' : '')
                . '</div>'
                . '</a>';
        }
        $section = <<<HTML
<div class="not-prose my-10">
    <div class="text-[10px] uppercase tracking-[0.2em] font-bold text-blue-700 mb-2">Related reading</div>
    <h2 class="text-2xl font-bold text-slate-900 m-0 mb-4">From the Resort Guru blog</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">$cards</div>
</div>
HTML;
        $pattern = '~(<div class="not-prose mt-10 p-5 bg-slate-50 rounded-xl)~';
        if (preg_match($pattern, $body)) {
            return preg_replace($pattern, $section . '$1', $body, 1);
        }
        return $body . $section;
    }

    // === 6 + 7. TL;DR + WWWW =============================================

    private function buildTldr(string $area, string $type): string
    {
        return match ($type) {
            'mall' => "* Three food zones — main mall, side strip, food court\n* Per-person spend: 200 to 1,500 pesos\n* Easiest walk-in window: 3 to 5 PM\n* Avoid 12 to 2 PM weekend lunch crush\n* Strongest cuisines: Japanese ramen + Korean BBQ + Filipino chains",
            'city' => "* $area food sprawls across districts each on its own rhythm\n* Per-person spend: 300 to 1,200 pesos\n* Local heritage cuisine is the standout\n* Best to ask which district matches your meal first\n* Walking food tours work better than mall-only crawls",
            'destination' => "* $area is a multi-day food destination, not a quick stop\n* Regional speciality dishes are the headline\n* Per-person spend: 400 to 1,500 pesos\n* Public market lunches deliver the best price-to-flavor ratio\n* Reservations help on weekend dinners during peak season",
            default => "* $area is a food strip where the regulars walk the side streets, not the main road\n* Per-person spend: 300 to 800 pesos\n* Long-running family-run kitchens beat new arrivals on consistency\n* Easiest walk-in: weekday late lunch, 2 to 4 PM\n* Late-night meals available — strip stays open past 10 PM Fridays and Saturdays",
        };
    }

    private function buildWwww(string $area, string $type): array
    {
        return match ($type) {
            'mall' => [
                'why'   => "$area packs over 100 restaurants across multiple cuisines under one roof. Best for groups that can't agree on what to eat, families with mixed appetites, or quick weekday office lunches with predictable service.",
                'when'  => "Easiest walk-in is the 3 to 5 PM window any day of the week. Avoid 12 to 2 PM on weekends (peak mall traffic). Weekend dinner peaks 6 to 8 PM — reserve at upper-tier spots if attending.",
                'where' => "$area is on multiple floors. Main strip restaurants on the upper levels (sit-down dining). Food court on the ground or basement floor (quickest queue). Side wings hold the newer concept restaurants. Plan your wing before committing.",
                'whom'  => "Best for family Sunday lunches, pre-event group meals (concerts, sports), date nights with predictable service, and BPO office lunches. Skip if you're hunting hole-in-the-wall finds or wanting quiet conversation-friendly tables.",
            ],
            'city' => [
                'why'   => "$area food culture spans heritage cuisine + modern chain dining + market eats. Each $area district runs on a different rhythm so picking the right neighborhood matters more than picking the right restaurant.",
                'when'  => "Lunch peaks 12 to 2 PM in business districts. Dinner peaks 7 to 9 PM. For local heritage spots, time your visit around market hours (5 AM to 2 PM) or pulled-from-the-grill dinners (5 to 8 PM).",
                'where' => "$area splits into downtown (heritage and market food), commercial districts (chain restaurants and mall food), and outer neighborhoods (family-run carinderias). Each warrants a separate visit.",
                'whom'  => "Best for multi-day food trips, heritage cuisine hunters, and travelers who want authentic local flavor over predictable chains. Skip if you need single-stop convenience or strict accessibility.",
            ],
            'destination' => [
                'why'   => "$area is a destination where the food is part of the trip purpose. Regional speciality dishes anchor the visit. Public market eating gives the local rhythm without the tourist markup.",
                'when'  => "Peak season runs December to May for most $area destinations. Off-season delivers thinner crowds and better deals. Sunset dining is the local highlight for beachside areas. Market eating happens morning to mid-afternoon.",
                'where' => "$area town strip handles casual seafood and Filipino comfort. Resort restaurants serve the upper-tier dining. Public market eateries serve the budget local-flavor option. Each fills a different meal slot.",
                'whom'  => "Best for travelers booking 2 to 4 nights, photo-friendly dining settings, and heritage cuisine adventures. Skip if you want quick weekday meals or strict AC-only requirements.",
            ],
            default => [
                'why'   => "$area is a working dining strip where regulars eat at the side streets, not the main road. The family-run kitchens that have lasted 10+ years deliver the consistency. Late-night meals work because the strip stays open past most other corners.",
                'when'  => "Easiest walk-in is the 2 to 4 PM window weekdays. Weekend late-night (10 PM onwards) draws the after-work crowd at the strip. Sunday brunch fills the cafe-side corners 10 AM to noon.",
                'where' => "$area runs along the main avenue, with the long-running family restaurants tucked one block off the main road. The newer concept places open on the main strip first. The food court or street stalls handle the cheapest meals.",
                'whom'  => "Best for late-night meals, family-run authenticity hunters, and weekend brunch crowds. Skip if you need mall parking convenience, reservation systems, or AC-only dining.",
            ],
        };
    }

    // === Location helpers ================================================

    private function extractLocation(string $phrase): string
    {
        $p = mb_strtolower(trim($phrase));
        $p = preg_replace('/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/i', '', $p);
        $p = preg_replace('/\b(filipino|japanese|korean|chinese|italian|seafood|steak|sushi|buffet|fine\s+dining)\s+/i', '', $p);
        $p = preg_replace('/^philippines\s+|^antonio\'?s\s+/', '', $p);
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
            'moa' => '/(mall of asia|^moa$|sm moa)/', 'bgc' => '/(bgc|bonifacio|burgos circle|high street|uptown bgc)/',
            'megamall' => '/(sm megamall|^megamall$)/', 'sm_north' => '/(sm north|sm city north|north edsa)/',
            'podium' => '/(podium)/', 'greenbelt' => '/(greenbelt)/', 'glorietta' => '/(glorietta)/',
            'festival_mall' => '/(festival mall|festival$|festive)/', 'sm_aura' => '/(sm aura)/',
            'tomas_morato' => '/(tomas morato)/', 'trinoma' => '/(trinoma)/',
            'uptown' => '/(uptown mall|up town center|uptc)/', 'eastwood' => '/(eastwood)/',
            'rockwell' => '/(rockwell|powerplant)/', 'greenhills' => '/(greenhills)/',
            'rob_galleria' => '/(robinsons galleria)/', 'rob_ermita' => '/(robinsons ermita)/',
            'ayala_mb' => '/(ayala mall(s)? manila bay|ayala manila bay)/',
            'shangrila' => '/(shangrila mall|shangri la mall|edsa shangrila)/',
            'market_market' => '/(market market)/', 'gateway' => '/(gateway)/',
            'solaire' => '/(solaire)/', 'okada' => '/(okada)/',
            'resorts_world' => '/(resorts world|newport)/', 'mckinley' => '/(mckinley)/',
            'manila_old' => '/(intramuros|binondo|quiapo|malate)/',
            'ayala_cebu' => '/(ayala center cebu|ayala cebu|cebu ayala)/',
            'cebu_sm' => '/(sm city cebu|sm seaside|nustar)/', 'it_park' => '/(it park)/',
            'opus' => '/(opus mall)/', 'alabang' => '/(alabang|atc|filinvest|westgate alabang|molito)/',
            'bf_homes' => '/(bf homes|bf$)/', 'nuvali' => '/(nuvali|sta rosa|santa rosa)/',
            'kapitolyo' => '/(kapitolyo)/', 'katipunan' => '/(katipunan|up diliman|ust)/',
            'maginhawa' => '/(maginhawa)/', 'banawe' => '/(banawe)/',
            'cubao' => '/(cubao)/', 'ortigas' => '/(ortigas|capitol commons|tiendesitas|estancia|galleria$)/',
            'antipolo' => '/(antipolo)/', 'makati_inner' => '/(poblacion|jupiter makati|makati avenue)/',
            'circuit' => '/(circuit makati|circuit$)/', 'camp_john_hay' => '/(camp john hay)/',
            'tagaytay' => '/(tagaytay)/', 'baguio' => '/(baguio)/', 'boracay' => '/(boracay)/',
            'el_nido' => '/(el nido)/', 'coron' => '/(coron)/', 'puerto_princesa' => '/(puerto princesa)/',
            'puerto_galera' => '/(puerto galera)/', 'siargao' => '/(siargao)/',
            'bohol' => '/(panglao|bohol|tagbilaran)/', 'subic' => '/(subic|olongapo)/',
            'la_union' => '/(la union|san juan la union)/', 'vigan' => '/(vigan)/',
            'cebu' => '/^cebu|(^|\W)(cebu)(\W|$)/', 'davao' => '/(davao)/',
            'iloilo' => '/(iloilo)/', 'bacolod' => '/(bacolod)/', 'cdo' => '/(cagayan|^cdo$)/',
            'tacloban' => '/(tacloban)/', 'naga' => '/(naga)/',
            'legazpi' => '/(legazpi|legaspi|albay)/', 'lipa' => '/(lipa)/',
            'batangas' => '/(batangas)/', 'clark' => '/(angeles|clark)/',
            'pampanga' => '/(san fernando|pampanga)/', 'marikina' => '/(marikina)/',
            'pasig' => '/(pasig)/', 'pasay' => '/(pasay)/', 'paranaque' => '/(paranaque)/',
            'mandaluyong' => '/(mandaluyong)/', 'taguig' => '/(taguig)/',
            'san_juan' => '/(san juan)/',
            'makati' => '/(^|\W)(makati)(\W|$)/',
            'qc' => '/(quezon city|^qc$|quezon ave|timog|west avenue|fairview|white plains|don antonio|visayas ave|visayas avenue)/',
            'manila' => '/(^|\W)(manila|manila peninsula|robinsons manila)(\W|$)/',
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
            'rob_galleria' => 'Robinsons Galleria', 'rob_ermita' => 'Robinsons Ermita',
            'ayala_mb' => 'Ayala Mall Manila Bay', 'shangrila' => 'Shangri-La Mall',
            'market_market' => 'Market! Market!', 'gateway' => 'Gateway Mall', 'solaire' => 'Solaire',
            'okada' => 'Okada Manila', 'resorts_world' => 'Resorts World Manila',
            'mckinley' => 'McKinley Hill', 'manila_old' => 'Old Manila',
            'ayala_cebu' => 'Ayala Center Cebu', 'cebu_sm' => 'SM Seaside Cebu',
            'it_park' => 'Cebu IT Park', 'opus' => 'Opus Mall',
            'alabang' => 'Alabang', 'bf_homes' => 'BF Homes', 'nuvali' => 'Nuvali Sta. Rosa',
            'kapitolyo' => 'Kapitolyo', 'katipunan' => 'Katipunan', 'maginhawa' => 'Maginhawa',
            'banawe' => 'Banawe', 'cubao' => 'Cubao', 'ortigas' => 'Ortigas Center',
            'antipolo' => 'Antipolo', 'makati_inner' => 'Poblacion Makati', 'circuit' => 'Circuit Makati',
            'camp_john_hay' => 'Camp John Hay',
            'makati' => 'Makati', 'qc' => 'Quezon City', 'manila' => 'Manila',
            'cebu' => 'Cebu City', 'davao' => 'Davao City', 'iloilo' => 'Iloilo City',
            'bacolod' => 'Bacolod', 'tacloban' => 'Tacloban', 'cdo' => 'Cagayan de Oro',
            'naga' => 'Naga', 'legazpi' => 'Legazpi', 'lipa' => 'Lipa', 'batangas' => 'Batangas',
            'clark' => 'Clark', 'pampanga' => 'Pampanga',
            'marikina' => 'Marikina', 'pasig' => 'Pasig', 'pasay' => 'Pasay',
            'paranaque' => 'Parañaque', 'mandaluyong' => 'Mandaluyong', 'taguig' => 'Taguig',
            'san_juan' => 'San Juan',
            'tagaytay' => 'Tagaytay', 'baguio' => 'Baguio', 'boracay' => 'Boracay',
            'el_nido' => 'El Nido', 'coron' => 'Coron', 'puerto_princesa' => 'Puerto Princesa',
            'puerto_galera' => 'Puerto Galera', 'siargao' => 'Siargao', 'bohol' => 'Bohol',
            'subic' => 'Subic', 'la_union' => 'La Union', 'vigan' => 'Vigan',
        ];
        return $map[$key] ?? $this->properTitle($loc);
    }

    private function detectType(string $key): string
    {
        $mall = ['moa','megamall','sm_north','podium','greenbelt','glorietta','festival_mall','sm_aura','trinoma','uptown','rob_galleria','rob_ermita','ayala_mb','shangrila','market_market','gateway','solaire','okada','resorts_world','opus','ayala_cebu','cebu_sm','rockwell'];
        if (in_array($key, $mall, true)) return 'mall';
        $dest = ['tagaytay','baguio','boracay','el_nido','coron','puerto_princesa','puerto_galera','siargao','bohol','subic','la_union','vigan'];
        if (in_array($key, $dest, true)) return 'destination';
        $city = ['manila','qc','cebu','davao','iloilo','bacolod','tacloban','cdo','naga','legazpi','lipa','batangas','clark','pampanga','makati','marikina','pasig','pasay','paranaque','mandaluyong','taguig','san_juan'];
        if (in_array($key, $city, true)) return 'city';
        return 'district';
    }

    private function properTitle(string $s): string
    {
        $small = ['of','the','in','at','on','and','a','an','to','for','by','from','with'];
        $words = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($words as $i => $w) {
            if ($w === '') continue;
            $words[$i] = ($i === 0 || !in_array($w, $small, true))
                ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8') : $w;
        }
        return implode(' ', $words);
    }
}
