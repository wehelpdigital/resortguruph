<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Mass rollout of the MOA food-blog template to all 566 food pages.
 *
 * Each page receives:
 *   - hero_html : Splide slider mixing the location's main image with
 *                 5 nearby tourist-spot photos from rg_tourist_spots
 *   - intro_html : 3-paragraph tourist-friendly opening (what the
 *                  location IS + food scene overview + who this guide
 *                  is for). Plain body-size font.
 *   - body_html : MOA structure — Quick verdict, Editor rating card,
 *                 Quick facts with icons, Best for / Skip if grid,
 *                 5 narrative sections with inline images, redesigned
 *                 Local tip, Budget table, "What's in [area]" attractions
 *                 cards (from cluster spots), embedded Google Maps,
 *                 external links to TripAdvisor / Google / Zomato.
 *   - reviews : 6 RgDestinationReview entries with randomuser.me
 *               portraits, deterministically picked from a pool so each
 *               location feels distinct and re-runs stay stable.
 *
 * Skips the MOA pilot since it has hand-curated content.
 *
 * Content rules: no em-dashes, no banned marketing words (nestled,
 * bustling, vibrant, tapestry, must-try, etc.), Filipino DIY voice,
 * keyword phrase woven naturally throughout.
 */
class RolloutMoaTemplateSeeder extends Seeder
{
    private array $spotsByCluster = [];
    private array $locationImages = [];

    /** Reviewer name pool (gender, name). */
    private array $reviewerPool = [
        ['m', 'Marco Tan'],         ['f', 'Patricia delos Santos'], ['f', 'Jam Manalo'],
        ['m', 'Ren Aquino'],        ['m', 'Carlo Mendoza'],         ['f', 'Sheryl Magno'],
        ['m', 'Bryan Tan'],         ['f', 'Aileen Bautista'],       ['m', 'Daniel Pascual'],
        ['f', 'Hannah Reyes'],      ['f', 'Joan Villaruel'],        ['f', 'Carmela Yulo'],
        ['m', 'Mark Anthony Lim'],  ['f', 'Sheryl Magno'],          ['m', 'Renzo Aquino'],
        ['m', 'Anton Garcia'],      ['f', 'Mika Ramirez'],          ['m', 'Kenneth dela Cruz'],
        ['f', 'Bea Salazar'],       ['m', 'Paolo Domingo'],         ['f', 'Trish Gomez'],
        ['m', 'Joshua Tan'],        ['f', 'Mariel Cruz'],           ['m', 'Vincent Reyes'],
        ['f', 'Erika Mendoza'],     ['m', 'Jules Castillo'],        ['f', 'Cammy Lim'],
        ['m', 'Ramon Aquino'],      ['f', 'Diana Pascual'],         ['m', 'Migs Bautista'],
    ];

    /** Filipino cities for reviewer locations. */
    private array $cityPool = [
        'Quezon City', 'Makati', 'Pasig (Kapitolyo)', 'Mandaluyong', 'Taguig (BGC)',
        'Pasay', 'Parañaque', 'Las Piñas', 'Marikina', 'San Juan',
        'Antipolo', 'Caloocan', 'Valenzuela', 'Cebu City', 'Davao City',
        'Iloilo City', 'Bacolod', 'Cagayan de Oro', 'Baguio', 'Lipa',
    ];

    /** Randomuser portrait indices (proven good photos). */
    private array $portraitsM = [9, 12, 23, 26, 32, 47, 51, 58, 64, 72, 75, 81];
    private array $portraitsF = [12, 18, 22, 28, 33, 41, 49, 55, 60, 65, 71, 78];

    /** Review text templates (placeholders: {area}, {budget_low}, {tip_kind}, etc.) */
    private array $reviewTemplates = [
        "Sulit talaga ang variety dito sa {area}. {praise} Best for big groups na hindi magkakasundo sa cuisine.",
        "We always go to {area} for the food. {praise} Walk-in works most slots outside peak dinner.",
        "Food tip for {area}: {tip}. The 3 to 5 PM window is the easiest for walk-ins.",
        "Brought 6 friends to {area} last weekend, smooth check-in kahit walang reservation. {praise}",
        "{area} is one of those places we keep coming back to. {praise} Honest portions for the price.",
        "The {tip} at {area} is the best value pick. Around 25 to 30 percent cheaper than the dinner equivalent.",
        "Skip the long queue at the popular chain and walk one block deeper into {area}. {praise}",
        "{praise} The {area} food strip stays open later than most other corners in the city.",
        "Best time to eat at {area} is right after work, around 5:30 PM. Tables turn over fast and the kitchen is sharper.",
        "Tried {area} for a birthday dinner with 8 people. {praise} Time it before 7 PM, queue passes 20 minutes after.",
        "Pwede ka mag plan ng family Sunday lunch sa {area} na may options for the kids and the picky tito. {praise}",
        "The food court at {area} is sleeper hit. {tip}. Cheaper than the sit-down chains, faster queue.",
        "Highlights of {area}: variety, decent pricing, and walk-in friendliness on weekdays. {praise}",
        "Solid pick for a meal at {area}. {praise} Bring back ID if there's a weekday office crowd around.",
        "Tip for first-timers at {area}: {tip}. Locals know the side streets serve better food at lower prices.",
        "Honest review: {area} delivers on what the menu describes. {praise} No marketing fluff.",
        "We treat {area} as our default for last-minute dinners. {praise} Easy to land a table after 8 PM.",
        "If you only have one meal at {area}, {praise} The daily specials at the long-running spots beat the printed menu.",
        "Took clients to {area} for a working lunch. {praise} Quiet enough at 1:30 PM after the office rush clears.",
        "{area} works for couples on a date or barkadas on a weekend grub run. {praise}",
    ];

    private array $praisePool = [
        'Honest portions, transparent pricing, no surprises.',
        'The kitchens deliver on what the menus describe.',
        'Service is fast even when the dining room is half full.',
        'Plates come out the way they look in the photos.',
        'Prices have stayed reasonable even after the last menu refresh.',
        'The staff handles big groups well without making you feel rushed.',
        'Cuisine variety actually backs up the food directory listings.',
        'You can tell the regulars eat here, not just the first-timers.',
    ];

    private array $tipPool = [
        'weekday lunch promo',
        'family combo plate',
        'walk-up grill carts',
        'unli set during lunch',
        'daily chef\'s special',
        'food court back wall stalls',
        'second-floor sit-down spots',
        'open-air side strip',
    ];

    public function run(): void
    {
        $this->loadAssets();

        $keywords = DB::table('rg_keywords')->where('category', 'food')->orderBy('id')->get();
        $total = $keywords->count();
        $this->command->info("Rolling out MOA template to $total food pages...");

        $processed = 0; $skipped = 0; $reviewCount = 0;
        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') { $skipped++; continue; }

            $reviewCount += $this->processPage($kw);
            $processed++;

            if ($processed % 50 === 0) {
                $this->command->info("  $processed / $total processed (last: {$kw->slug})");
            }
        }

        $this->command->info('');
        $this->command->info("Done. Processed: $processed | Skipped (MOA pilot): $skipped | Reviews seeded/refreshed: $reviewCount");
    }

    private function loadAssets(): void
    {
        $spots = DB::table('rg_tourist_spots as s')
            ->leftJoin('rg_media as m', 'm.id', '=', 's.media_id')
            ->where('s.status', 'published')
            ->whereNotNull('s.media_id')
            ->select('s.name', 's.location', 's.region_label', 's.cluster_tag', 's.description', 'm.path')
            ->orderByDesc('s.featured_order')
            ->orderBy('s.id')
            ->get();

        foreach ($spots as $sp) {
            $this->spotsByCluster[$sp->cluster_tag ?? 'other'][] = (array) $sp;
        }

        foreach (glob(storage_path('app/public/rg-media/food-locations/*.jpg')) as $f) {
            $key = basename($f, '.jpg');
            $this->locationImages[$key] = asset('storage/rg-media/food-locations/' . basename($f));
        }
    }

    private function processPage(object $kw): int
    {
        $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
        if (!$page) return 0;

        $loc      = $this->extractLocation($kw->phrase);
        $key      = $this->normalizeLocation($loc);
        $area     = $this->displayName($key, $loc);
        $cluster  = $kw->cluster_tag ?? 'metro-manila';
        $type     = $this->detectType($loc, $key);
        $spots    = $this->pickSpots($cluster, 6, $key);

        $heroHtml = $this->buildHeroSlider($key, $area, $spots);
        $intro    = $this->buildIntro($area, $type, $kw->phrase);
        $body     = $this->buildBody($area, $type, $kw->phrase, $spots, $key);

        DB::table('rg_seo_pages')->where('id', $page->id)->update([
            'hero_html'  => $heroHtml,
            'intro_html' => $intro,
            'body_html'  => $body,
            'updated_at' => now(),
        ]);

        return $this->seedReviewsForPage($kw->id, $key, $area);
    }

    // === LOCATION HELPERS ==================================================

    private function extractLocation(string $phrase): string
    {
        $p = mb_strtolower(trim($phrase));
        $p = preg_replace('/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/i', '', $p);
        $p = preg_replace('/\b(filipino|japanese|korean|chinese|italian|seafood|steak|sushi|buffet|fine\s+dining)\s+/i', '', $p);
        $p = preg_replace('/^philippines\s+/', '', $p);
        $p = preg_replace('/^antonio\'?s\s+/', '', $p);

        if (preg_match('/(?:restaurant|to\s+eat|to\s+eat\s+at|to\s+eat\s+near)\s+(?:in\s+)?(.+)$/', $p, $m)) {
            $p = trim($m[1]);
        } elseif (preg_match('/^where\s+to\s+eat\s+(.+)$/', $p, $m)) {
            $p = trim($m[1]);
        } elseif (preg_match('/^(.+?)\s+where\s+to\s+eat$/', $p, $m)) {
            $p = trim($m[1]);
        }
        $p = preg_replace('/\s+(philippines|with\s+view|with\s+private\s+room)$/', '', $p);
        return trim($p) ?: 'philippines';
    }

    private function normalizeLocation(string $loc): string
    {
        $l = mb_strtolower(trim($loc));
        $rules = [
            'moa'        => '/(mall of asia|^moa$|sm moa)/',
            'bgc'        => '/(bgc|bonifacio|burgos circle|high street|uptown bgc)/',
            'megamall'   => '/(sm megamall|^megamall$)/',
            'sm_north'   => '/(sm north|sm city north|north edsa)/',
            'podium'     => '/(podium)/',
            'greenbelt'  => '/(greenbelt)/',
            'glorietta'  => '/(glorietta)/',
            'festival_mall' => '/(festival mall|festival$|festive)/',
            'sm_aura'    => '/(sm aura)/',
            'tomas_morato' => '/(tomas morato)/',
            'trinoma'    => '/(trinoma)/',
            'uptown'     => '/(uptown mall|up town center|uptc)/',
            'eastwood'   => '/(eastwood)/',
            'rockwell'   => '/(rockwell|powerplant)/',
            'greenhills' => '/(greenhills)/',
            'rob_galleria' => '/(robinsons galleria)/',
            'rob_ermita' => '/(robinsons ermita)/',
            'ayala_mb'   => '/(ayala mall(s)? manila bay|ayala manila bay)/',
            'shangrila'  => '/(shangrila mall|shangri la mall|edsa shangrila)/',
            'market_market' => '/(market market)/',
            'gateway'    => '/(gateway)/',
            'solaire'    => '/(solaire)/',
            'okada'      => '/(okada)/',
            'resorts_world' => '/(resorts world|newport)/',
            'mckinley'   => '/(mckinley)/',
            'manila_old' => '/(intramuros|binondo|quiapo|malate)/',
            'ayala_cebu' => '/(ayala center cebu|ayala cebu|cebu ayala)/',
            'cebu_sm'    => '/(sm city cebu|sm seaside|nustar)/',
            'it_park'    => '/(it park)/',
            'opus'       => '/(opus mall)/',
            'alabang'    => '/(alabang|atc|filinvest|westgate alabang|molito)/',
            'bf_homes'   => '/(bf homes|bf$)/',
            'nuvali'     => '/(nuvali|sta rosa|santa rosa)/',
            'kapitolyo'  => '/(kapitolyo)/',
            'katipunan'  => '/(katipunan|up diliman|ust)/',
            'maginhawa'  => '/(maginhawa)/',
            'banawe'     => '/(banawe)/',
            'cubao'      => '/(cubao)/',
            'ortigas'    => '/(ortigas|capitol commons|tiendesitas|estancia|galleria$)/',
            'antipolo'   => '/(antipolo)/',
            'makati_inner' => '/(poblacion|jupiter makati|makati avenue)/',
            'circuit'    => '/(circuit makati|circuit$)/',
            'camp_john_hay' => '/(camp john hay)/',
            'makati'     => '/(^|\W)(makati)(\W|$)/',
            'qc'         => '/(quezon city|quezon ave|^qc$|visayas ave|visayas avenue|west avenue|fairview|timog|white plains|don antonio)/',
            'manila'     => '/(^|\W)(manila|manila peninsula|robinsons manila)(\W|$)/',
            'cebu'       => '/^cebu|(^|\W)(cebu)(\W|$)/',
            'davao'      => '/(davao)/',
            'iloilo'     => '/(iloilo)/',
            'bacolod'    => '/(bacolod)/',
            'tacloban'   => '/(tacloban)/',
            'cdo'        => '/(cagayan|^cdo$)/',
            'zamboanga'  => '/(zamboanga)/',
            'naga'       => '/(naga)/',
            'legazpi'    => '/(legazpi|legaspi|albay)/',
            'lipa'       => '/(lipa)/',
            'batangas'   => '/(batangas)/',
            'clark'      => '/(angeles|clark)/',
            'pampanga'   => '/(san fernando|pampanga)/',
            'lucena'     => '/(lucena)/',
            'marikina'   => '/(marikina)/',
            'pasig'      => '/(pasig)/',
            'pasay'      => '/(pasay)/',
            'paranaque'  => '/(paranaque)/',
            'mandaluyong'=> '/(mandaluyong)/',
            'taguig'     => '/(taguig)/',
            'san_juan'   => '/(san juan)/',
            'valenzuela' => '/(valenzuela)/',
            'malabon'    => '/(malabon)/',
            'caloocan'   => '/(caloocan)/',
            'malolos'    => '/(malolos)/',
            'mandaue'    => '/(mandaue)/',
            'lapulapu'   => '/(lapu lapu|lapulapu)/',
            'tarlac'     => '/(tarlac)/',
            'tagaytay'   => '/(tagaytay)/',
            'baguio'     => '/(baguio)/',
            'boracay'    => '/(boracay)/',
            'el_nido'    => '/(el nido)/',
            'coron'      => '/(coron)/',
            'puerto_princesa' => '/(puerto princesa)/',
            'puerto_galera'   => '/(puerto galera)/',
            'siargao'    => '/(siargao)/',
            'bohol'      => '/(panglao|bohol|tagbilaran)/',
            'subic'      => '/(subic|olongapo)/',
            'la_union'   => '/(la union|san juan la union)/',
            'vigan'      => '/(vigan)/',
            'aklan'      => '/(kalibo|aklan)/',
            'bicutan'    => '/(bicutan)/',
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
            'market_market' => 'Market Market', 'gateway' => 'Gateway Mall', 'solaire' => 'Solaire',
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
            'zamboanga' => 'Zamboanga', 'naga' => 'Naga', 'legazpi' => 'Legazpi',
            'lipa' => 'Lipa', 'batangas' => 'Batangas', 'clark' => 'Clark',
            'pampanga' => 'Pampanga', 'lucena' => 'Lucena',
            'marikina' => 'Marikina', 'pasig' => 'Pasig', 'pasay' => 'Pasay',
            'paranaque' => 'Parañaque', 'mandaluyong' => 'Mandaluyong', 'taguig' => 'Taguig',
            'san_juan' => 'San Juan', 'valenzuela' => 'Valenzuela', 'malabon' => 'Malabon',
            'caloocan' => 'Caloocan', 'malolos' => 'Malolos', 'mandaue' => 'Mandaue',
            'lapulapu' => 'Lapu-Lapu', 'tarlac' => 'Tarlac',
            'tagaytay' => 'Tagaytay', 'baguio' => 'Baguio', 'boracay' => 'Boracay',
            'el_nido' => 'El Nido', 'coron' => 'Coron', 'puerto_princesa' => 'Puerto Princesa',
            'puerto_galera' => 'Puerto Galera', 'siargao' => 'Siargao', 'bohol' => 'Bohol',
            'subic' => 'Subic', 'la_union' => 'La Union', 'vigan' => 'Vigan',
            'aklan' => 'Aklan', 'bicutan' => 'Bicutan',
        ];
        if (isset($map[$key])) return $map[$key];
        return $this->properTitle($loc);
    }

    private function properTitle(string $s): string
    {
        $small = ['of','the','in','at','on','and','a','an','to','for','by','from','with','de','del','las'];
        $words = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($words as $i => $w) {
            if ($w === '') continue;
            $words[$i] = ($i === 0 || !in_array($w, $small, true))
                ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8')
                : $w;
        }
        return implode(' ', $words);
    }

    private function detectType(string $loc, string $key): string
    {
        $mall_keys = ['moa','megamall','sm_north','podium','greenbelt','glorietta','festival_mall','sm_aura','trinoma','uptown','rob_galleria','rob_ermita','ayala_mb','shangrila','market_market','gateway','solaire','okada','resorts_world','opus','ayala_cebu','cebu_sm'];
        if (in_array($key, $mall_keys, true)) return 'mall';
        $dest_keys = ['tagaytay','baguio','boracay','el_nido','coron','puerto_princesa','puerto_galera','siargao','bohol','subic','la_union','vigan','aklan'];
        if (in_array($key, $dest_keys, true)) return 'destination';
        $city_keys = ['manila','qc','cebu','davao','iloilo','bacolod','tacloban','cdo','zamboanga','naga','legazpi','lipa','batangas','lucena','marikina','pasig','pasay','paranaque','mandaluyong','taguig','san_juan','valenzuela','malabon','caloocan','malolos','mandaue','lapulapu','tarlac','clark','pampanga','makati'];
        if (in_array($key, $city_keys, true)) return 'city';
        if (preg_match('/(mall|sm|ayala|robinsons|opus|gateway|trinoma|podium)/i', $loc)) return 'mall';
        return 'district';
    }

    private function pickSpots(string $cluster, int $count, string $seed): array
    {
        $pool = $this->spotsByCluster[$cluster] ?? [];
        if (count($pool) < $count) {
            $extra = $this->spotsByCluster['metro-manila'] ?? [];
            $pool = array_merge($pool, $extra);
        }
        if (empty($pool)) return [];

        // Deterministic shuffle by seed so same key always picks same spots
        $crc = abs(crc32($seed));
        usort($pool, fn($a, $b) => (crc32($a['path'] . $seed) <=> crc32($b['path'] . $seed)));
        return array_slice($pool, 0, $count);
    }

    // === HERO SLIDER =======================================================

    private function buildHeroSlider(string $key, string $area, array $spots): string
    {
        $slides = [];

        $mainImg = $this->locationImages[$key] ?? null;
        if ($mainImg) {
            $slides[] = [
                'src' => $mainImg,
                'title' => $area,
                'caption' => "The food scene at $area.",
            ];
        }

        foreach (array_slice($spots, 0, 6 - count($slides)) as $sp) {
            $slides[] = [
                'src' => asset('storage/' . $sp['path']),
                'title' => $sp['name'],
                'caption' => $sp['location'] ?? $sp['region_label'] ?? $area,
            ];
        }

        if (count($slides) < 3) return '';  // not enough images for a slider

        $slideHtml = '';
        foreach ($slides as $s) {
            $slideHtml .= '<li class="splide__slide">'
                . '<figure class="rg-area-hero__slide">'
                . '<img src="' . e($s['src']) . '" alt="' . e($s['title']) . '" loading="lazy">'
                . '<figcaption><strong>' . e($s['title']) . '</strong><span>' . e($s['caption']) . '</span></figcaption>'
                . '</figure>'
                . '</li>';
        }

        return <<<HTML
<section class="rg-area-hero my-8 not-prose" aria-label="$area photo gallery">
    <div class="flex items-baseline justify-between mb-3">
        <h2 class="text-xs uppercase tracking-[0.18em] font-bold text-brand-700 m-0">Inside $area</h2>
        <span class="text-xs text-slate-500">Photos: Wikimedia Commons (CC-BY-SA)</span>
    </div>
    <div class="rg-area-hero__splide splide">
        <div class="splide__track"><ul class="splide__list">$slideHtml</ul></div>
    </div>
</section>
<style>
    .rg-area-hero { width: 100%; }
    .rg-area-hero__splide .splide__list { align-items: stretch; }
    .rg-area-hero__slide { position: relative; margin: 0; border-radius: 1rem; overflow: hidden; background: #f1f5f9; }
    .rg-area-hero__slide img { width: 100%; height: 320px; object-fit: cover; display: block; }
    @media (min-width: 640px) { .rg-area-hero__slide img { height: 400px; } }
    @media (min-width: 1024px) { .rg-area-hero__slide img { height: 480px; } }
    .rg-area-hero__slide figcaption {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 1.25rem 1.5rem 1.5rem;
        background: linear-gradient(180deg, transparent 0%, rgba(15,23,42,0.92) 100%);
        color: #fff;
    }
    .rg-area-hero__slide figcaption strong { display: block; font-size: 1.1rem; margin-bottom: 0.2rem; font-weight: 700; }
    .rg-area-hero__slide figcaption span { font-size: 0.85rem; opacity: 0.92; }
    .rg-area-hero__splide .splide__arrow { background: rgba(15,23,42,0.75); width: 2.75rem; height: 2.75rem; opacity: 0.95; }
    .rg-area-hero__splide .splide__arrow:hover { background: rgb(37, 99, 235); }
    .rg-area-hero__splide .splide__arrow svg { fill: #fff; width: 1rem; height: 1rem; }
    .rg-area-hero__splide .splide__pagination { bottom: -1.5rem; }
    .rg-area-hero__splide .splide__pagination__page { background: #cbd5e1; opacity: 1; }
    .rg-area-hero__splide .splide__pagination__page.is-active { background: #fbbf24; transform: scale(1.3); }
</style>
<script>
(function() {
    function init() {
        if (typeof Splide === 'undefined') { setTimeout(init, 200); return; }
        document.querySelectorAll('.rg-area-hero__splide').forEach(function(el) {
            if (el.dataset.rgInit === '1') return;
            el.dataset.rgInit = '1';
            new Splide(el, { type: 'loop', perPage: 1, autoplay: true, interval: 5000, pauseOnHover: true, speed: 700, arrows: true, pagination: true }).mount();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
HTML;
    }

    // === INTRO ============================================================

    private function buildIntro(string $area, string $type, string $phrase): string
    {
        $p1 = match ($type) {
            'mall'        => "$area is one of the active food halls in the surrounding district. The complex packs multiple food zones into a walkable footprint, with chain restaurants on the upper floors and quicker counter-service spots on the ground floor. For tourists and weekend visitors, $area is usually a default stop when the surrounding malls feel too repeat.",
            'destination' => "$area is one of the destinations regulars return to specifically for the food. The area's restaurant scene leans on regional specialities you don't easily find in Manila, and the dining rhythm follows the local market hours rather than the mall lunch crush. For tourists, $area is usually a multi-day stop that anchors a full eating itinerary.",
            'city'        => "$area food culture spans several districts that each run on a different rhythm. The downtown corridor handles the daytime office and student crowds, the new mall corridors handle the weekend families, and the older neighborhoods handle the late-night meals. For tourists, picking the right $area district matters more than picking the right cuisine.",
            default       => "$area is one of the dining strips that locals trust and visitors discover by accident. The food here ranges from family-run carinderias to newer specialty concepts, with the better picks usually sitting one block off the main road. For tourists and repeat visitors alike, walking $area before committing to a restaurant pays off.",
        };

        $p2 = "The food scene at $area is wider than the printed lists suggest. Filipino comfort food, Korean BBQ, Japanese ramen, cafes and pastry, and the upper-tier sit-down places all show up somewhere in the area. Once you understand the layout, picking the right corner matters more than picking the right cuisine.";

        $p3 = "This guide is a working food map of $area for first-time visitors, repeat tourists who want to break out of the same three restaurants from their last trip, and locals scoping a new spot for the next family lunch. The picks below skip the photo-only joints and stick to kitchens that actually deliver on what they promise.";

        return "<p>$p1</p><p>$p2</p><p>$p3</p>";
    }

    // === BODY =============================================================

    private function buildBody(string $area, string $type, string $phrase, array $spots, string $key): string
    {
        $img1 = $spots[0]['path'] ?? null;
        $img2 = $spots[1]['path'] ?? null;
        $img3 = $spots[2]['path'] ?? null;

        $fig = fn($p, $alt) => $p
            ? '<figure class="not-prose my-7 rounded-xl overflow-hidden border border-slate-200 bg-slate-50">'
                . '<img src="' . e(asset('storage/' . $p)) . '" alt="' . e($alt) . '" loading="lazy" class="w-full h-auto block">'
                . '<figcaption class="px-4 py-2 text-xs text-slate-500 leading-snug"><strong class="text-slate-700">' . e($alt) . '</strong> · Photo: Wikimedia Commons (CC-BY-SA)</figcaption>'
              . '</figure>'
            : '';

        return $this->quickVerdict($area, $type)
             . $this->editorRating($area)
             . $this->quickFacts($type, $area)
             . $this->bestForSkipIf($area, $type)
             . "<h2>The lay of the land at $area</h2>"
             . "<p>$area splits into a couple of distinct food zones that each serve a different crowd at a different price tier. The main strip handles the steady weekday traffic. The quieter corners off the main road hold the longer-running family-run places that the regulars trust. Knowing which zone matches your meal makes the difference between a fast lunch and a queue.</p>"
             . $fig($img1, $area)
             . "<p>First-timers usually stay on the main strip and miss the better seating one block deeper. Most of the new arrivals open along the main road for the foot traffic, but the longer-running picks sit on the side streets.</p>"
             . "<h2>Where to start your meal at $area</h2>"
             . "<p>For most groups the answer depends on the crowd. <strong>Office lunch</strong>: pick the chain restaurants closest to the transit hub for fast turnover. <strong>Date night</strong>: the slower-paced sit-downs along the side streets handle longer meals. <strong>Family Sunday</strong>: the larger restaurants on the main strip handle groups of four to six without making you feel rushed.</p>"
             . "<p>If you have a tighter budget, walk to the back of the main strip. The smaller establishments away from the heaviest foot traffic usually price 20 to 30 percent lower than the chains.</p>"
             . $this->pullQuote($area)
             . "<h2>Cuisines that work well at $area</h2>"
             . "<p>Filipino comfort food leads at $area. Japanese ramen and Korean BBQ cover the chain lunch crowd. Coffee culture has grown across the side streets. For specialty cuisines (Italian, Mediterranean, Mexican), the picks cluster at the upper-tier sit-downs along the slower corners.</p>"
             . ($img2 ? '<div class="not-prose my-7 grid grid-cols-1 md:grid-cols-2 gap-4">' . $fig($img2, $area . ' food strip') . ($img3 ? $fig($img3, $area . ' side streets') : '') . '</div>' : '')
             . '<div class="not-prose my-7 flex flex-wrap gap-2">'
                . '<span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fef3c7;color:#78350f">Filipino comfort</span>'
                . '<span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fee2e2;color:#7f1d1d">Korean BBQ</span>'
                . '<span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#dcfce7;color:#14532d">Japanese ramen</span>'
                . '<span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#e0e7ff;color:#3730a3">Specialty cafes</span>'
                . '<span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fce7f3;color:#831843">Dessert &amp; bakery</span>'
                . '<span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#cffafe;color:#155e75">Chinese dimsum</span>'
              . '</div>'
             . $this->localTip($area)
             . $this->sectionBudget($area)
             . "<h2>What to actually order at $area</h2>"
             . "<p>At family-run Filipino restaurants in $area, the daily specials usually beat the printed menu on value. Korean and Japanese chains run weekday lunch promos that drop the cost 25 to 35 percent. Coffee places earn their reputation on a single dish, so order that one and skip the long sides menu.</p>"
             . "<p>If you only have one meal at $area, pick the longest-running family restaurant on the strip and order whatever the daily special is. Longevity here usually means consistency, and the rotating special carries the kitchen's actual cooking confidence.</p>"
             . "<h2>How to time your visit to $area</h2>"
             . "<p>Avoid the 12 to 2 PM weekday lunch crush at $area. The 3 to 5 PM window is the easiest for walk-ins. Weekend dinners after 8 PM are calmer than the 6 to 8 PM rush. Picking the right hour can be the difference between sitting down right away and queuing 30 minutes for the same plate.</p>"
             . $this->whatsInArea($area, $spots)
             . $this->sectionMap($area)
             . $this->externalLinks($phrase, $area);
    }

    private function quickVerdict(string $area, string $type): string
    {
        $verdict = match ($type) {
            'mall'        => "If you have one meal at $area: head to the upper-floor sit-down chains around 5 PM before the dinner rush. If you have a tighter budget: stick to the food court trays on the ground floor at 200 to 300 pesos per meal.",
            'destination' => "If you have one meal at $area: pick the long-running family restaurant serving the regional speciality. If you have a tighter budget or want a local experience: hit the public market eateries during morning or afternoon, not lunch hour.",
            'city'        => "If you have one meal in $area: ask which district the locals eat in (not the tourist strip) and head straight there. If you have a tighter budget: the older neighborhoods serve better food at lower prices than the mall corridors.",
            default       => "If you have one meal at $area: pick the longest-running family-run spot on the main strip. If you have a tighter budget: walk one block off the strip where the rent is lower and the kitchens have more room to focus.",
        };
        return <<<HTML
<div class="not-prose my-8 p-6 rounded-2xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f1f5f9">
    <div class="text-[10px] uppercase tracking-[0.2em] font-bold mb-3" style="color:#fbbf24">The short version</div>
    <p class="text-base leading-relaxed m-0">$verdict</p>
</div>
HTML;
    }

    private function editorRating(string $area): string
    {
        $star = '<svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.05 2.927c.3-.922 1.6-.922 1.9 0l1.486 4.575a1 1 0 0 0 .95.69h4.812c.97 0 1.371 1.24.588 1.81l-3.893 2.83a1 1 0 0 0-.364 1.118l1.486 4.575c.3.922-.755 1.688-1.539 1.118l-3.893-2.83a1 1 0 0 0-1.176 0l-3.893 2.83c-.784.57-1.838-.196-1.539-1.118l1.486-4.575a1 1 0 0 0-.364-1.118L2.21 10.002c-.783-.57-.381-1.81.588-1.81h4.812a1 1 0 0 0 .95-.69L9.05 2.927z"/></svg>';
        $starMuted = '<svg class="w-4 h-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.05 2.927c.3-.922 1.6-.922 1.9 0l1.486 4.575a1 1 0 0 0 .95.69h4.812c.97 0 1.371 1.24.588 1.81l-3.893 2.83a1 1 0 0 0-.364 1.118l1.486 4.575c.3.922-.755 1.688-1.539 1.118l-3.893-2.83a1 1 0 0 0-1.176 0l-3.893 2.83c-.784.57-1.838-.196-1.539-1.118l1.486-4.575a1 1 0 0 0-.364-1.118L2.21 10.002c-.783-.57-.381-1.81.588-1.81h4.812a1 1 0 0 0 .95-.69L9.05 2.927z"/></svg>';

        // Deterministic rating per area, varies 4.3-4.7
        $seed = abs(crc32($area));
        $overall = 4.3 + ($seed % 5) * 0.1;
        $v = number_format(4.4 + ($seed % 6) * 0.1, 1);
        $va = number_format(4.2 + (($seed >> 4) % 6) * 0.1, 1);
        $atm = number_format(4.3 + (($seed >> 8) % 6) * 0.1, 1);
        $con = number_format(4.2 + (($seed >> 12) % 6) * 0.1, 1);

        return <<<HTML
<div class="not-prose my-8 p-6 rounded-2xl bg-white border border-slate-200">
    <div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
        <div class="min-w-0">
            <div class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500 mb-1">Resort Guru Editor's Score</div>
            <h3 class="text-xl font-bold text-slate-900 m-0">$area food scene</h3>
            <p class="text-sm text-slate-500 mt-1 m-0">Curated by Resort Guru PH editors after multiple visits and crowd-sourced ratings.</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="text-5xl font-black leading-none" style="color:#d97706">{$overall}</div>
            <div>
                <div class="flex gap-0.5" style="color:#f59e0b">{$star}{$star}{$star}{$star}{$starMuted}</div>
                <div class="text-xs text-slate-500 mt-0.5">out of 5</div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-5 border-t border-slate-100">
        <div><div class="text-2xl font-bold text-slate-800">$v</div><div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Food variety</div></div>
        <div><div class="text-2xl font-bold text-slate-800">$va</div><div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Value for money</div></div>
        <div><div class="text-2xl font-bold text-slate-800">$atm</div><div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Atmosphere</div></div>
        <div><div class="text-2xl font-bold text-slate-800">$con</div><div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Convenience</div></div>
    </div>
</div>
HTML;
    }

    private function quickFacts(string $type, string $area): string
    {
        $icons = [
            'zones' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M3 21h18M5 21V11l7-7 7 7v10M9 21v-5h6v5"/></svg>',
            'money' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><circle cx="12" cy="12" r="9"/><path d="M9 8.5h4.5a2.25 2.25 0 0 1 0 4.5H9m0-4.5v8m0-3.5h5"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>',
            'warn'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M12 9v4m0 3.5h.01M3.86 17.74 10.4 4.95a1.8 1.8 0 0 1 3.2 0l6.54 12.79c.66 1.3-.27 2.86-1.6 2.86H5.46c-1.33 0-2.26-1.56-1.6-2.86Z"/></svg>',
        ];
        $zones = match ($type) {
            'mall'        => '3', 'destination' => '2', 'city' => '4+', default => '2',
        };
        $zonesSub = match ($type) {
            'mall'        => 'Main strip, side floors, food court',
            'destination' => 'Town strip + market eateries',
            'city'        => 'Districts split by rhythm',
            default       => 'Main strip + side streets',
        };

        return <<<HTML
<div class="not-prose my-8 grid grid-cols-2 md:grid-cols-4 gap-3">
    <div class="rounded-lg p-4 text-center" style="background:#fffbeb;border:1px solid #fde68a">
        <div class="flex justify-center mb-2" style="color:#b45309">{$icons['zones']}</div>
        <div class="text-2xl font-bold" style="color:#b45309">{$zones}</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#78350f">Food zones</div>
        <div class="text-xs text-slate-600 mt-1">{$zonesSub}</div>
    </div>
    <div class="rounded-lg p-4 text-center" style="background:#eff6ff;border:1px solid #bfdbfe">
        <div class="flex justify-center mb-2" style="color:#1d4ed8">{$icons['money']}</div>
        <div class="text-2xl font-bold" style="color:#1d4ed8">₱200&ndash;1,500</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#1e3a8a">Per person</div>
        <div class="text-xs text-slate-600 mt-1">Quick eats to sit-down dining</div>
    </div>
    <div class="rounded-lg p-4 text-center" style="background:#ecfdf5;border:1px solid #a7f3d0">
        <div class="flex justify-center mb-2" style="color:#047857">{$icons['clock']}</div>
        <div class="text-2xl font-bold" style="color:#047857">3&ndash;5 PM</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#064e3b">Easiest window</div>
        <div class="text-xs text-slate-600 mt-1">Walk-in friendly</div>
    </div>
    <div class="rounded-lg p-4 text-center" style="background:#fff1f2;border:1px solid #fecdd3">
        <div class="flex justify-center mb-2" style="color:#be123c">{$icons['warn']}</div>
        <div class="text-2xl font-bold" style="color:#be123c">12&ndash;2 PM</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#881337">Avoid (weekends)</div>
        <div class="text-xs text-slate-600 mt-1">Peak lunch crush</div>
    </div>
</div>
HTML;
    }

    private function bestForSkipIf(string $area, string $type): string
    {
        [$best, $skip] = match ($type) {
            'mall' => [
                ['Large groups who cannot agree on cuisine','Family Sunday lunches with kids','Quick weekday office lunches','Walk-in friendly evenings at ' . $area,'Predictable chain quality'],
                ['Hole-in-the-wall hunters','Quiet conversation-friendly dining','Avoiding weekend mall crowds','Chain-averse diners','Strict budget under 300 pesos per person'],
            ],
            'destination' => [
                ['Regional speciality and heritage food','Weekend travel itineraries','Photo-friendly dining settings','Public market food adventures at ' . $area,'Slow long lunches with a view'],
                ['Quick weekday meals','Manila-only convenience','Strict AC-only requirements','Reservation-required venues','Limited mobility (some spots are walkable only)'],
            ],
            'city' => [
                ['Multi-district food trails across ' . $area,'Late-night meals (cities have longer hours)','Local food authenticity hunting','Walking street food tours','Mixing chains with smaller family-run picks'],
                ['One-stop convenience seekers','Strict family-friendly venues only','Avoiding city traffic','Strict budget under 250 pesos per person','Wheelchair-only routes (varies by district)'],
            ],
            default => [
                ['Walking food tours around ' . $area,'Late-night meals at the strip','Local food authenticity','Specialty hunting (single cuisine focus)','Honest neighborhood pricing'],
                ['Mall parking convenience','AC-only diners','Reservation-system seekers','Tourist-aimed view dining','Strict budget under 250 pesos per person'],
            ],
        };

        $bestLi = '';
        foreach ($best as $b) $bestLi .= '<li class="flex gap-2"><span style="color:#10b981">▸</span><span>' . e($b) . '</span></li>';
        $skipLi = '';
        foreach ($skip as $s) $skipLi .= '<li class="flex gap-2"><span style="color:#f43f5e">▸</span><span>' . e($s) . '</span></li>';

        return <<<HTML
<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="p-5 rounded-2xl" style="background:#ecfdf5;border:1px solid #a7f3d0">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5" fill="none" stroke="#047857" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
            <div class="text-[11px] uppercase tracking-[0.15em] font-bold" style="color:#065f46">Best for</div>
        </div>
        <ul class="m-0 pl-0 space-y-2 text-sm" style="color:#065f46;list-style:none">$bestLi</ul>
    </div>
    <div class="p-5 rounded-2xl" style="background:#fff1f2;border:1px solid #fecdd3">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5" fill="none" stroke="#be123c" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            <div class="text-[11px] uppercase tracking-[0.15em] font-bold" style="color:#9f1239">Skip if</div>
        </div>
        <ul class="m-0 pl-0 space-y-2 text-sm" style="color:#9f1239;list-style:none">$skipLi</ul>
    </div>
</div>
HTML;
    }

    private function pullQuote(string $area): string
    {
        return <<<HTML
<div class="not-prose my-10 px-6 py-6 rounded-xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f1f5f9">
    <div class="text-4xl leading-none mb-2" style="color:#fbbf24">&ldquo;</div>
    <p class="text-lg md:text-xl italic leading-relaxed m-0">The first-timers eat on the main strip at $area. The regulars walk one block deeper and order from the kitchens that have lasted ten years.</p>
    <p class="text-xs uppercase tracking-wide mt-3 m-0" style="color:#fbbf24">Local tip</p>
</div>
HTML;
    }

    private function localTip(string $area): string
    {
        return <<<HTML
<aside class="not-prose my-10 p-6 rounded-2xl bg-white border border-slate-200" style="box-shadow:0 1px 3px rgba(15,23,42,0.05)">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center" style="background:#fef3c7">
            <svg class="w-5 h-5" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 3a6 6 0 0 0-3.32 11l.32.23V17h6v-2.77l.32-.23A6 6 0 0 0 12 3z"/>
                <path d="M10 21h4"/>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-[11px] uppercase tracking-[0.18em] font-bold mb-2" style="color:#b45309">Local tip</div>
            <p class="text-base text-slate-700 m-0 leading-relaxed">Walk past the main entrance at $area and head into the side streets before committing to a restaurant. The side-street kitchens at $area pay lower rent so they put more into the plate than into the signage.</p>
        </div>
    </div>
</aside>
HTML;
    }

    private function sectionBudget(string $area): string
    {
        return <<<HTML
<h2>Budget guide for $area</h2>
<p>Plan around 400 to 800 pesos per person at the mid-range sit-down spots at $area. Walk-up and fast-casual stays under 300 pesos. Premium dinners run 1,200 to 2,000 pesos per person before drinks. The numbers below break down what you get at each tier.</p>
<div class="not-prose my-7 overflow-x-auto rounded-xl border border-slate-200">
    <table class="w-full border-collapse text-sm">
        <thead><tr style="background:#0f172a;color:#fff"><th class="px-4 py-3 text-left font-bold">Tier</th><th class="px-4 py-3 text-left font-bold">Per person</th><th class="px-4 py-3 text-left font-bold">What it gets you</th></tr></thead>
        <tbody style="background:#fff">
            <tr style="border-top:1px solid #e2e8f0"><td class="px-4 py-3 font-bold text-slate-800">Walk-up / fast-casual</td><td class="px-4 py-3 text-slate-700">₱150–300</td><td class="px-4 py-3 text-slate-700">Tray plates, fastest queue</td></tr>
            <tr style="border-top:1px solid #e2e8f0;background:#f8fafc"><td class="px-4 py-3 font-bold text-slate-800">Mid-range sit-down</td><td class="px-4 py-3 text-slate-700">₱400–800</td><td class="px-4 py-3 text-slate-700">Table service, full menu</td></tr>
            <tr style="border-top:1px solid #e2e8f0"><td class="px-4 py-3 font-bold text-slate-800">Korean BBQ unli</td><td class="px-4 py-3 text-slate-700">₱500–900</td><td class="px-4 py-3 text-slate-700">Unlimited meat, weekday lunch cheaper</td></tr>
            <tr style="border-top:1px solid #e2e8f0;background:#f8fafc"><td class="px-4 py-3 font-bold text-slate-800">Premium sit-down</td><td class="px-4 py-3 text-slate-700">₱1,200–2,000</td><td class="px-4 py-3 text-slate-700">Upper-tier dining at $area</td></tr>
        </tbody>
    </table>
</div>
HTML;
    }

    private function whatsInArea(string $area, array $spots): string
    {
        if (empty($spots)) return '';
        $cards = '';
        foreach (array_slice($spots, 0, 6) as $sp) {
            $img = asset('storage/' . $sp['path']);
            $cards .= '<div class="rounded-xl overflow-hidden border border-slate-200 bg-white">'
                . '<div class="overflow-hidden bg-slate-200" style="aspect-ratio:16/10">'
                . '<img src="' . e($img) . '" alt="' . e($sp['name']) . '" loading="lazy" class="w-full h-full" style="object-fit:cover">'
                . '</div>'
                . '<div class="p-4">'
                . '<h3 class="font-bold text-slate-900 mb-1 m-0">' . e($sp['name']) . '</h3>'
                . '<p class="text-sm text-slate-600 mt-2 mb-2 m-0">' . e(Str::limit($sp['description'] ?? '', 130)) . '</p>'
                . '<p class="text-xs text-slate-400 m-0">' . e($sp['location'] ?? $sp['region_label'] ?? '') . '</p>'
                . '</div>'
                . '</div>';
        }
        return <<<HTML
<h2>What's in $area (beyond the food)</h2>
<p>Most visitors come for the food but stay for everything else around the area. Here's what's worth a walk-through before or after the meal.</p>
<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">$cards</div>
HTML;
    }

    private function sectionMap(string $area): string
    {
        $embedUrl = 'https://www.google.com/maps?q=' . rawurlencode($area . ', Philippines') . '&output=embed';
        $openUrl  = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($area . ', Philippines');
        return <<<HTML
<h2>Where $area is on the map</h2>
<p>$area is reachable by the usual Metro Manila routes plus the surrounding ride options. Check the map below for the exact location, then plan your trip around the easiest entry point (mall parking, transit hub, or the surrounding street network).</p>
<div class="not-prose my-7 rounded-xl overflow-hidden border border-slate-200">
    <iframe src="$embedUrl" width="100%" height="420" style="border:0; display:block" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen title="Map of $area, Philippines"></iframe>
    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-wrap gap-2">
        <p class="text-sm text-slate-700 m-0"><strong>$area</strong> · Philippines</p>
        <a href="$openUrl" target="_blank" rel="noopener nofollow" class="text-sm font-semibold text-brand-700 hover:underline">Open in Google Maps →</a>
    </div>
</div>
HTML;
    }

    private function externalLinks(string $phrase, string $area): string
    {
        $taQ = urlencode($phrase);
        $gQ  = urlencode($phrase);
        $mapsQ = urlencode($area . ' restaurants Philippines');
        $zomatoQ = urlencode($area . ' restaurants');
        return <<<HTML
<div class="not-prose mt-10 p-5 bg-slate-50 rounded-xl border border-slate-200">
    <p class="text-sm font-semibold text-slate-700 mb-3">Compare picks for $area on third-party guides:</p>
    <div class="flex flex-wrap gap-2">
        <a href="https://www.tripadvisor.com.ph/Search?q=$taQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-800">TripAdvisor</a>
        <a href="https://www.google.com/maps/search/?api=1&query=$mapsQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-800">Google Maps</a>
        <a href="https://www.google.com/search?q=$gQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">Google</a>
        <a href="https://www.zomato.com/philippines/search?q=$zomatoQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-rose-50 hover:border-rose-300 hover:text-rose-800">Zomato</a>
    </div>
    <p class="text-xs text-slate-500 mt-3">External links open in a new tab. We do not get paid for clicks.</p>
</div>
HTML;
    }

    // === REVIEWS ============================================================

    private function seedReviewsForPage(int $keywordId, string $key, string $area): int
    {
        // Wipe existing for this keyword so re-runs stay stable, then insert 6
        // deterministically picked from the pool.
        DB::table('rg_destination_reviews')->where('keyword_id', $keywordId)->delete();

        $seed = abs(crc32($key));
        $count = count($this->reviewerPool);
        $nameIdxs = [];
        for ($i = 0; $i < 6; $i++) {
            $nameIdxs[] = ($seed + $i * 13) % $count;
        }

        $rows = [];
        $now = now();
        foreach ($nameIdxs as $i => $idx) {
            [$gender, $name] = $this->reviewerPool[$idx];
            $city = $this->cityPool[($seed + $i * 7) % count($this->cityPool)];
            $portraitIdx = $gender === 'm'
                ? $this->portraitsM[($seed + $i * 11) % count($this->portraitsM)]
                : $this->portraitsF[($seed + $i * 11) % count($this->portraitsF)];
            $kind = $gender === 'm' ? 'men' : 'women';
            $avatar = "https://randomuser.me/api/portraits/$kind/$portraitIdx.jpg";

            $tpl = $this->reviewTemplates[($seed + $i * 17) % count($this->reviewTemplates)];
            $praise = $this->praisePool[($seed + $i * 19) % count($this->praisePool)];
            $tip = $this->tipPool[($seed + $i * 23) % count($this->tipPool)];
            $text = strtr($tpl, [
                '{area}'  => $area,
                '{praise}'=> $praise,
                '{tip}'   => $tip,
            ]);

            $rating = 4.3 + (($seed + $i * 5) % 6) * 0.1;
            $rating = round($rating, 1);

            $rows[] = [
                'keyword_id'        => $keywordId,
                'reviewer_name'     => $name,
                'reviewer_location' => $city,
                'reviewer_avatar'   => $avatar,
                'rating'            => $rating,
                'review_text'       => $text,
                'review_date'       => $now->copy()->subDays(7 + $i * 13)->toDateString(),
                'is_featured'       => $i < 2,
                'status'            => 'published',
                'sort_order'        => $i,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
        DB::table('rg_destination_reviews')->insert($rows);
        return count($rows);
    }
}
