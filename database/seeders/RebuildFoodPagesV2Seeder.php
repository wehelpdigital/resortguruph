<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * V2 rebuild for all 565 non-MOA food pages.
 *
 * Fixes applied:
 *   1. Hero is a slider again (was simplified to single figure in V1) but
 *      this time using CORRECT images — disambiguated Wikimedia queries
 *      per location so "Greenhills" pulls Greenhills San Juan, not random
 *      "Greenhills" results. Multiple images per location (up to 5) for
 *      proper slider variety.
 *   2. Quick-facts strip drops the "Food zones" card. Now 3 cards: Per
 *      person, Easiest window, Avoid (weekends).
 *   3. Editor Rating moves to first position in body_html, so it renders
 *      directly under the "What's In [Area]?" article header.
 *   4. All images use landscape aspect-ratio + object-fit: cover so source
 *      portrait files are forced to landscape.
 *   5. MOA pilot preserved (skipped — already hand-curated).
 *
 * Images go to storage/app/public/rg-media/food-locations/ with naming:
 *   - {key}.jpg            (primary, was already downloaded)
 *   - {key}-2.jpg, etc.    (additional contextual images)
 */
class RebuildFoodPagesV2Seeder extends Seeder
{
    private array $headers = [
        'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
        'Accept'     => 'application/json, image/jpeg, image/*',
    ];

    /** Cache: location key → list of image URLs (multiple per location). */
    private array $imagesByKey = [];

    /**
     * Contextual Wikimedia search queries per location key. The added city
     * / district / province disambiguates ambiguous names like "Greenhills"
     * → "Greenhills San Juan", "Festival Mall" → "Festival Mall Alabang",
     * etc.
     */
    private array $contextualQueries = [
        // Malls — anchored to the host city/district
        'megamall'       => ['SM Megamall Mandaluyong', 'SM Megamall EDSA Ortigas', 'SM Megamall Building B'],
        'sm_north'       => ['SM City North EDSA Quezon City', 'SM North EDSA Quezon City Philippines', 'SM City North The Block'],
        'sm_aura'        => ['SM Aura Premier BGC Taguig', 'SM Aura Bonifacio Global City', 'SM Aura Sky Park'],
        'podium'         => ['The Podium Ortigas Mandaluyong', 'Podium mall Ortigas Center', 'Podium ADB Avenue'],
        'greenbelt'      => ['Ayala Greenbelt Makati', 'Greenbelt 3 Makati Ayala Center', 'Greenbelt 5 Makati'],
        'glorietta'      => ['Ayala Glorietta Makati', 'Glorietta Ayala Center Makati Philippines', 'Glorietta 4 mall'],
        'trinoma'        => ['TriNoma Quezon City North Avenue', 'TriNoma Ayala mall Quezon City', 'TriNoma mall'],
        'uptown'         => ['Uptown Mall Bonifacio Global City', 'Uptown BGC Taguig', 'Uptown Mall Bonifacio'],
        'festival_mall'  => ['Festival Mall Alabang Muntinlupa', 'Festival Mall Filinvest', 'Festival Mall Alabang Philippines'],
        'rob_galleria'   => ['Robinsons Galleria Ortigas Quezon City', 'Robinsons Galleria Manila EDSA', 'Robinsons Galleria Mandaluyong'],
        'rob_ermita'     => ['Robinsons Place Manila Ermita', 'Robinsons Manila Pedro Gil', 'Robinsons Ermita Manila'],
        'ayala_mb'       => ['Ayala Malls Manila Bay Parañaque', 'Ayala Mall Manila Bay Las Piñas', 'Ayala Malls Manila Bay'],
        'shangrila'      => ['Shangri-La Plaza Mandaluyong', 'Shangri-La Plaza EDSA Ortigas', 'Shangri-La Mall Manila'],
        'market_market'  => ['Market Market Bonifacio Global City', 'Market Market mall Taguig', 'Market Market BGC'],
        'gateway'        => ['Gateway Mall Cubao Quezon City', 'Araneta Gateway Cubao', 'Gateway Mall Araneta'],
        'solaire'        => ['Solaire Resort Casino Entertainment City Parañaque', 'Solaire Manila Bay'],
        'okada'          => ['Okada Manila Parañaque', 'Okada Manila Entertainment City'],
        'resorts_world'  => ['Resorts World Manila Newport Pasay', 'Newport Mall Pasay City Philippines'],
        'opus'           => ['Opus Mall Bridgetowne Pasig', 'Opus Mall Eastwood', 'Opus mall Quezon City'],
        'eastwood'       => ['Eastwood City Libis Quezon City', 'Eastwood Mall Quezon City', 'Eastwood Cyber Park'],
        'rockwell'       => ['Rockwell Powerplant Mall Makati', 'Rockwell Center Makati', 'Powerplant Mall Rockwell Makati'],

        // Districts — disambiguated by city
        'bgc'            => ['Bonifacio Global City Taguig', 'BGC High Street Taguig', 'Bonifacio High Street BGC'],
        'greenhills'     => ['Greenhills Shopping Center San Juan Philippines', 'Greenhills San Juan City', 'Greenhills San Juan Promenade'],
        'tomas_morato'   => ['Tomas Morato Avenue Quezon City', 'Tomas Morato food strip Quezon City', 'Tomas Morato Boulevard'],
        'kapitolyo'      => ['Kapitolyo Pasig food strip', 'Kapitolyo Pasig City', 'Kapitolyo East Capitol'],
        'maginhawa'      => ['Maginhawa Street Quezon City', 'Maginhawa Sikatuna Quezon City', 'Maginhawa food street'],
        'banawe'         => ['Banawe Avenue Quezon City', 'Banawe street Quezon City', 'Banawe Chinese food'],
        'cubao'          => ['Cubao Quezon City Araneta Center', 'Cubao Expo Quezon City', 'Araneta Cubao'],
        'katipunan'      => ['Katipunan Avenue Quezon City', 'Katipunan Ateneo Loyola Heights', 'Katipunan University Belt'],
        'ortigas'        => ['Ortigas Center Pasig Mandaluyong', 'Ortigas Avenue Pasig', 'Ortigas business district'],
        'antipolo'       => ['Antipolo Rizal Philippines', 'Antipolo Cathedral', 'Antipolo Sumulong Highway'],
        'manila_old'     => ['Intramuros Manila walled city', 'Binondo Chinatown Manila', 'Old Manila streets'],
        'alabang'        => ['Alabang Muntinlupa Filinvest', 'Alabang Town Center Muntinlupa', 'Filinvest City Alabang'],
        'bf_homes'       => ['BF Homes Paranaque Aguirre Avenue', 'BF Homes Aguirre Paranaque', 'BF Homes Tropical Avenue'],
        'nuvali'         => ['Nuvali Solenad Santa Rosa Laguna', 'Nuvali Sta Rosa lake', 'Nuvali Ayala Land Sta Rosa'],
        'mckinley'       => ['McKinley Hill Taguig', 'McKinley West Taguig', 'McKinley Venice Grand Canal Taguig'],
        'makati_inner'   => ['Poblacion Makati food strip', 'Makati Avenue night scene', 'Poblacion Burgos Makati'],
        'circuit'        => ['Circuit Makati Carmona', 'Circuit Makati open lot', 'Ayala Circuit Makati'],
        'camp_john_hay'  => ['Camp John Hay Baguio Loakan', 'Camp John Hay Baguio City', 'Camp John Hay Manor'],

        // Cebu-area
        'ayala_cebu'     => ['Ayala Center Cebu Cebu City', 'Cebu Ayala Mall Cebu City', 'Ayala Cebu Business Park'],
        'cebu_sm'        => ['SM Seaside Cebu South Road Properties', 'SM City Cebu North Reclamation', 'SM Seaside Cebu City'],
        'it_park'        => ['Cebu IT Park Lahug Cebu City', 'IT Park Cebu BPO', 'Cebu IT Park Apas'],

        // Cities
        'makati'         => ['Makati Central Business District', 'Salcedo Village Makati', 'Ayala Avenue Makati'],
        'qc'             => ['Quezon City landmark', 'Quezon Memorial Circle', 'Quezon City Hall'],
        'manila'         => ['Manila city skyline', 'Manila Bay Roxas Boulevard', 'City of Manila Philippines'],
        'cebu'           => ['Cebu City Magellan\'s Cross', 'Cebu City downtown plaza', 'Cebu City skyline'],
        'davao'          => ['Davao City downtown', 'Davao Mount Apo', 'Davao People\'s Park'],
        'iloilo'         => ['Iloilo City Calle Real', 'Iloilo City plaza', 'Iloilo River Esplanade'],
        'bacolod'        => ['Bacolod City plaza', 'Bacolod public plaza', 'Bacolod City Negros'],
        'cdo'            => ['Cagayan de Oro City plaza', 'Cagayan de Oro Misamis Oriental', 'CDO landmark'],
        'tacloban'       => ['Tacloban City Leyte', 'Tacloban landmark', 'Tacloban City plaza'],
        'naga'           => ['Naga City Cathedral Camarines Sur', 'Naga City plaza', 'Naga City Bicol'],
        'legazpi'        => ['Legazpi City Mayon Volcano Albay', 'Legazpi Albay Mayon', 'Legazpi City Bicol'],
        'lipa'           => ['Lipa City Batangas', 'Lipa Cathedral Batangas', 'Lipa Batangas landmark'],
        'batangas'       => ['Batangas City plaza', 'Batangas City Bay', 'Batangas province landmark'],
        'clark'          => ['Clark Freeport Pampanga', 'Angeles City Pampanga', 'Clark Pampanga landmark'],
        'pampanga'       => ['San Fernando Pampanga', 'Pampanga landmark', 'Lantern Festival Pampanga'],

        // Destinations
        'tagaytay'       => ['Tagaytay ridge Taal volcano', 'Tagaytay Cavite Taal Lake', 'Tagaytay City Picnic Grove'],
        'baguio'         => ['Baguio Session Road', 'Baguio Burnham Park', 'Baguio City Benguet'],
        'boracay'        => ['Boracay White Beach Aklan', 'Boracay Station 1 Aklan', 'Boracay Island Aklan'],
        'el_nido'        => ['El Nido Bacuit Bay Palawan', 'El Nido Big Lagoon Palawan', 'El Nido Palawan island'],
        'coron'          => ['Coron Busuanga Palawan', 'Coron Kayangan Lake Palawan', 'Coron Island Palawan'],
        'puerto_princesa'=> ['Puerto Princesa Underground River Palawan', 'Puerto Princesa Baywalk', 'Puerto Princesa City'],
        'puerto_galera'  => ['Puerto Galera White Beach Oriental Mindoro', 'Puerto Galera Sabang', 'Puerto Galera Oriental Mindoro'],
        'siargao'        => ['Siargao Cloud 9 Surigao del Norte', 'Siargao General Luna', 'Siargao Island Philippines'],
        'bohol'          => ['Bohol Chocolate Hills Carmen', 'Panglao Island Bohol', 'Bohol Loboc River'],
        'subic'          => ['Subic Bay Freeport Zambales', 'Subic Bay Olongapo', 'Subic Freeport Zone'],
        'la_union'       => ['San Juan La Union surf beach', 'La Union Urbiztondo San Juan', 'San Juan La Union surf'],
        'vigan'          => ['Vigan Calle Crisologo Ilocos Sur', 'Vigan Heritage City Ilocos Sur', 'Vigan Plaza Burgos'],
        'aklan'          => ['Kalibo Aklan plaza', 'Aklan Ati-Atihan', 'Kalibo Aklan landmark'],
    ];

    public function run(): void
    {
        $this->command->info('=== V2 rebuild of all food pages ===');

        $this->command->info('Step 1: downloading contextual images for top locations...');
        $this->downloadAllContextualImages();

        $this->command->info('Step 2: rebuilding hero + body on all food pages...');
        $this->rebuildAllPages();

        $this->command->info('Done.');
    }

    // === IMAGE DOWNLOADS =================================================

    private function downloadAllContextualImages(): void
    {
        $dir = storage_path('app/public/rg-media/food-locations');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $totalDownloaded = 0;
        foreach ($this->contextualQueries as $key => $queries) {
            $downloaded = $this->downloadForKey($key, $queries);
            $totalDownloaded += $downloaded;
            if ($downloaded > 0) {
                $this->command->info("  $key: $downloaded images");
            }
        }
        $this->command->info("Total downloaded across all locations: $totalDownloaded");
    }

    private function downloadForKey(string $key, array $queries): int
    {
        $dir = storage_path('app/public/rg-media/food-locations');
        $downloaded = 0;
        $usedFiles = [];  // dedupe — don't save the same Wikimedia file twice

        // Try each query in order; aim for up to 5 unique images per location.
        for ($slot = 1; $slot <= 5 && count($queries) > 0; $slot++) {
            $localPath = $key . ($slot === 1 ? '' : "-$slot") . '.jpg';
            $absPath = $dir . DIRECTORY_SEPARATOR . $localPath;

            // For slot 1, only re-download if file is missing
            // For slots 2-5, always try to download to add variety
            if ($slot === 1 && is_file($absPath) && filesize($absPath) > 5000) {
                $downloaded++;
                continue;
            }
            if ($slot > 1 && is_file($absPath) && filesize($absPath) > 5000) {
                $downloaded++;
                continue;
            }

            $found = false;
            foreach ($queries as $q) {
                $files = $this->searchCommons($q, 8);
                foreach ($files as $f) {
                    if (in_array($f, $usedFiles, true)) continue;
                    if ($this->downloadFile($f, $absPath)) {
                        $usedFiles[] = $f;
                        $this->upsertMedia($localPath, $f, $key);
                        $downloaded++;
                        $found = true;
                        break 2;
                    }
                }
            }
            if (!$found) break;  // no more useful images for this location
        }
        return $downloaded;
    }

    private function upsertMedia(string $localPath, string $wikiFile, string $key): void
    {
        $absPath = storage_path('app/public/' . $localPath);
        DB::table('rg_media')->updateOrInsert(
            ['path' => $localPath],
            [
                'filename'   => $wikiFile,
                'path'       => $localPath,
                'mime'       => 'image/jpeg',
                'size_bytes' => is_file($absPath) ? filesize($absPath) : 0,
                'kind'       => 'image',
                'alt'        => $key . ' food location',
                'caption'    => $key,
                'source'     => 'seeder-food-location-v2',
                'credit'     => 'Photo: Wikimedia Commons (CC-BY-SA)',
                'source_url' => 'https://commons.wikimedia.org/wiki/File:' . $wikiFile,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function searchCommons(string $query, int $limit): array
    {
        try {
            $resp = Http::withHeaders($this->headers)->timeout(30)
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action' => 'query', 'format' => 'json',
                    'generator' => 'search', 'gsrnamespace' => 6,
                    'gsrlimit' => $limit, 'gsrsearch' => $query,
                    'prop' => 'imageinfo', 'iiprop' => 'url|mime|size',
                ]);
            if (!$resp->successful()) return [];
            $files = [];
            foreach (($resp->json()['query']['pages'] ?? []) as $page) {
                $title = $page['title'] ?? '';
                $info  = $page['imageinfo'][0] ?? null;
                if (!$info) continue;
                $mime = $info['mime'] ?? '';
                if (!str_starts_with($mime, 'image/') || str_starts_with($mime, 'image/svg')) continue;
                $w = $info['width'] ?? 0;
                $h = $info['height'] ?? 0;
                if ($w < 600 || $h < 400) continue;
                // Strongly prefer landscape images
                if ($h > $w) continue;
                if (str_starts_with($title, 'File:')) $files[] = substr($title, 5);
            }
            return $files;
        } catch (\Throwable $e) { return []; }
    }

    private function downloadFile(string $wikiFile, string $absPath): bool
    {
        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($wikiFile) . '?width=1400';
        try {
            $resp = Http::withHeaders($this->headers)->timeout(45)
                ->withOptions(['allow_redirects' => true])->get($url);
            if (!$resp->successful()) return false;
            $body = $resp->body();
            if (strlen($body) < 5000) return false;
            file_put_contents($absPath, $body);
            return true;
        } catch (\Throwable $e) { return false; }
    }

    // === PAGE REBUILD =====================================================

    private function rebuildAllPages(): void
    {
        $this->loadImagesByKey();

        $keywords = DB::table('rg_keywords')->where('category', 'food')->orderBy('id')->get();
        $processed = 0;

        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') continue;

            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page) continue;

            $loc  = $this->extractLocation($kw->phrase);
            $key  = $this->normalizeLocation($loc);
            $area = $this->displayName($key, $loc);

            // Rebuild hero with slider (multiple correct images per location).
            $newHero = $this->buildSliderHero($key, $area);

            // Surgical patches on existing body_html:
            //   (a) move editorRating to first position
            //   (b) remove "Food zones" quick-fact card
            $newBody = $this->patchBody($page->body_html);

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'hero_html'  => $newHero,
                'body_html'  => $newBody,
                'updated_at' => now(),
            ]);
            $processed++;
            if ($processed % 50 === 0) {
                $this->command->info("  $processed pages rebuilt...");
            }
        }
        $this->command->info("Page rebuild done. Processed: $processed");
    }

    private function loadImagesByKey(): void
    {
        $files = glob(storage_path('app/public/rg-media/food-locations/*.jpg'));
        foreach ($files as $f) {
            $name = basename($f, '.jpg');
            // Group: bgc.jpg, bgc-2.jpg, bgc-3.jpg → key = 'bgc'
            if (preg_match('/^(.+?)(-\d+)?$/', $name, $m)) {
                $key = $m[1];
                $this->imagesByKey[$key][] = asset('storage/rg-media/food-locations/' . basename($f));
            }
        }
        // Sort within each key so primary (.jpg) comes first
        foreach ($this->imagesByKey as $k => $list) {
            sort($list);
        }
    }

    private function buildSliderHero(string $key, string $area): string
    {
        $imgs = $this->imagesByKey[$key] ?? [];
        if (empty($imgs)) return '';

        $captions = [
            "$area — main view",
            "$area — surroundings",
            "$area — local scene",
            "$area — landmarks",
            "$area — area highlights",
        ];

        $slideHtml = '';
        foreach (array_slice($imgs, 0, 5) as $i => $url) {
            $caption = $captions[$i] ?? "$area";
            $slideHtml .= '<li class="splide__slide">'
                . '<figure class="rg-area-hero__slide">'
                . '<img src="' . e($url) . '" alt="' . e($area) . '" loading="lazy">'
                . '<figcaption><strong>' . e($area) . '</strong><span>' . e($caption) . '</span></figcaption>'
                . '</figure>'
                . '</li>';
        }

        return <<<HTML
<section class="rg-area-hero my-8 not-prose" aria-label="$area photos">
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
    /* Force landscape: aspect-ratio container + object-fit:cover crops portrait sources */
    .rg-area-hero__slide img { width: 100%; aspect-ratio: 21/9; object-fit: cover; display: block; height: auto; }
    @media (max-width: 640px) { .rg-area-hero__slide img { aspect-ratio: 16/10; } }
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

    /**
     * Surgical patches on body_html:
     *   (a) Pull the editorRating <div>...</div> block out and move it to
     *       the very top of body_html (so it renders right under the
     *       "What's In [Area]?" article header).
     *   (b) Strip the Food zones card from the quick-facts grid, leaving
     *       3 cards instead of 4.
     */
    private function patchBody(string $body): string
    {
        // Using ~ as delimiter throughout because the patterns contain
        // literal # hex codes (#fffbeb, #b45309, etc.) that would clash
        // with # as a regex delimiter.

        // (a) Extract editor rating block (wrapper that contains "Editor's Score")
        $ratingBlock = null;
        $body = preg_replace_callback(
            '~<div class="not-prose my-8 p-6 rounded-2xl bg-white border border-slate-200">\s*<div class="flex items-start justify-between gap-4 mb-5 flex-wrap">\s*<div class="min-w-0">\s*<div class="text-\[10px\] uppercase tracking-\[0\.2em\] font-bold text-slate-500 mb-1">Resort Guru Editor.+?</div>\s*</div>\s*</div>~s',
            function ($m) use (&$ratingBlock) {
                $ratingBlock = $m[0];
                return '';
            },
            $body,
            1
        );
        if (!empty($ratingBlock)) {
            $body = $ratingBlock . $body;
        }

        // (b) Strip the Food zones card from the quick-facts strip.
        $body = preg_replace(
            '~<div class="rounded-lg p-4 text-center"[^>]*background:#fffbeb[^>]*>.*?Food zones.*?</div>\s*</div>~s',
            '',
            $body
        );

        // (c) Widen the remaining 3 cards: grid-cols-4 → grid-cols-3.
        $body = preg_replace(
            '~<div class="not-prose my-8 grid grid-cols-2 md:grid-cols-4 gap-3"~',
            '<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-3 gap-3"',
            $body,
            1
        );

        return $body;
    }

    // === LOCATION HELPERS (copied/trimmed from prior seeders) =============

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
            'rob_galleria' => '/(robinsons galleria)/',
            'rob_ermita' => '/(robinsons ermita)/',
            'ayala_mb' => '/(ayala mall(s)? manila bay|ayala manila bay)/',
            'shangrila' => '/(shangrila mall|shangri la mall|edsa shangrila)/',
            'market_market' => '/(market market)/',
            'gateway' => '/(gateway)/',
            'solaire' => '/(solaire)/',
            'okada' => '/(okada)/',
            'resorts_world' => '/(resorts world|newport)/',
            'mckinley' => '/(mckinley)/',
            'manila_old' => '/(intramuros|binondo|quiapo|malate)/',
            'ayala_cebu' => '/(ayala center cebu|ayala cebu|cebu ayala)/',
            'cebu_sm' => '/(sm city cebu|sm seaside|nustar)/',
            'it_park' => '/(it park)/',
            'opus' => '/(opus mall)/',
            'alabang' => '/(alabang|atc|filinvest|westgate alabang|molito)/',
            'bf_homes' => '/(bf homes|bf$)/',
            'nuvali' => '/(nuvali|sta rosa|santa rosa)/',
            'kapitolyo' => '/(kapitolyo)/',
            'katipunan' => '/(katipunan|up diliman|ust)/',
            'maginhawa' => '/(maginhawa)/',
            'banawe' => '/(banawe)/',
            'cubao' => '/(cubao)/',
            'ortigas' => '/(ortigas|capitol commons|tiendesitas|estancia|galleria$)/',
            'antipolo' => '/(antipolo)/',
            'makati_inner' => '/(poblacion|jupiter makati|makati avenue)/',
            'circuit' => '/(circuit makati|circuit$)/',
            'camp_john_hay' => '/(camp john hay)/',
            'tagaytay' => '/(tagaytay)/',
            'baguio' => '/(baguio)/',
            'boracay' => '/(boracay)/',
            'el_nido' => '/(el nido)/',
            'coron' => '/(coron)/',
            'puerto_princesa' => '/(puerto princesa)/',
            'puerto_galera' => '/(puerto galera)/',
            'siargao' => '/(siargao)/',
            'bohol' => '/(panglao|bohol|tagbilaran)/',
            'subic' => '/(subic|olongapo)/',
            'la_union' => '/(la union|san juan la union)/',
            'vigan' => '/(vigan)/',
            'cebu' => '/^cebu|(^|\W)(cebu)(\W|$)/',
            'davao' => '/(davao)/',
            'iloilo' => '/(iloilo)/',
            'bacolod' => '/(bacolod)/',
            'cdo' => '/(cagayan|^cdo$)/',
            'tacloban' => '/(tacloban)/',
            'naga' => '/(naga)/',
            'legazpi' => '/(legazpi|legaspi|albay)/',
            'lipa' => '/(lipa)/',
            'batangas' => '/(batangas)/',
            'clark' => '/(angeles|clark)/',
            'pampanga' => '/(san fernando|pampanga)/',
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
            'naga' => 'Naga', 'legazpi' => 'Legazpi', 'lipa' => 'Lipa', 'batangas' => 'Batangas',
            'clark' => 'Clark', 'pampanga' => 'Pampanga',
            'tagaytay' => 'Tagaytay', 'baguio' => 'Baguio', 'boracay' => 'Boracay',
            'el_nido' => 'El Nido', 'coron' => 'Coron', 'puerto_princesa' => 'Puerto Princesa',
            'puerto_galera' => 'Puerto Galera', 'siargao' => 'Siargao', 'bohol' => 'Bohol',
            'subic' => 'Subic', 'la_union' => 'La Union', 'vigan' => 'Vigan',
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
