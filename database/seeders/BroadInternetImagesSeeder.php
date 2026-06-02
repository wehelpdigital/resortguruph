<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Broad-internet image downloader for food locations.
 *
 * Strategy: Wikimedia Commons coverage of specific PH malls/districts is
 * sparse. This seeder searches the broader internet via DuckDuckGo image
 * search (no API key required), grabs up to 5 location-specific photos
 * per key, downloads them locally, and stores the source URL + page in
 * rg_media so the slider caption can credit "Photo via {source}" with a
 * nofollow linkback (editorial fair use pattern).
 *
 * Then rebuilds the hero_html for every food page so the captions include
 * the attribution + clickable source link.
 *
 * If DDG returns nothing, the existing Wikimedia image stays in place.
 */
class BroadInternetImagesSeeder extends Seeder
{
    private array $ua = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept' => 'application/json, text/html, */*',
        'Accept-Language' => 'en-US,en;q=0.9',
    ];

    private array $locationQueries = [
        // Each entry: [display_name, search_queries[]]
        'bgc'            => ['BGC',                  ['BGC High Street Bonifacio Global City', 'BGC Taguig Manila', 'Bonifacio Global City Philippines']],
        'megamall'       => ['SM Megamall',          ['SM Megamall Mandaluyong', 'SM Megamall EDSA Ortigas', 'SM Megamall building Philippines']],
        'sm_north'       => ['SM North EDSA',        ['SM City North EDSA Quezon City', 'SM North EDSA mall facade', 'SM North The Block QC']],
        'sm_aura'        => ['SM Aura',              ['SM Aura Premier BGC Taguig', 'SM Aura Bonifacio Global City', 'SM Aura Sky Park rooftop']],
        'podium'         => ['The Podium',           ['The Podium Ortigas mall', 'Podium ADB Avenue Mandaluyong', 'Podium expansion mall']],
        'greenbelt'      => ['Greenbelt',            ['Greenbelt Ayala Makati', 'Greenbelt 3 Makati patio', 'Greenbelt Chapel Makati']],
        'glorietta'      => ['Glorietta',            ['Glorietta Ayala Makati', 'Glorietta mall facade', 'Glorietta 4 Makati Philippines']],
        'trinoma'        => ['TriNoma',              ['TriNoma Ayala North Avenue', 'TriNoma mall Quezon City', 'TriNoma Garden Quezon City']],
        'uptown'         => ['Uptown Mall BGC',      ['Uptown Mall BGC Taguig', 'Uptown Bonifacio Mall', 'Uptown BGC patio Taguig']],
        'festival_mall'  => ['Festival Mall',        ['Festival Mall Alabang Muntinlupa', 'Festival Mall Filinvest City', 'Festival Mall expansion Philippines']],
        'rob_galleria'   => ['Robinsons Galleria',   ['Robinsons Galleria Ortigas', 'Robinsons Galleria EDSA Quezon City', 'Robinsons Galleria mall']],
        'rob_ermita'     => ['Robinsons Ermita',     ['Robinsons Place Manila Ermita', 'Robinsons Manila Pedro Gil', 'Robinsons Ermita Manila Philippines']],
        'ayala_mb'       => ['Ayala Mall Manila Bay',['Ayala Malls Manila Bay Paranaque', 'Ayala Mall Manila Bay facade', 'Manila Bay Ayala mall Philippines']],
        'shangrila'      => ['Shangri-La Mall',      ['Shangri-La Plaza Mandaluyong', 'Shangri-La EDSA Ortigas mall', 'Shangri-La Plaza Manila']],
        'market_market'  => ['Market Market',        ['Market Market BGC Taguig', 'Market Market mall Bonifacio', 'Market Market Manila']],
        'gateway'        => ['Gateway Mall',         ['Gateway Mall Cubao Araneta', 'Gateway Cubao Quezon City', 'Araneta Gateway 2 Quezon City']],
        'solaire'        => ['Solaire',              ['Solaire Resort Entertainment City', 'Solaire Manila Bay Paranaque', 'Solaire Manila casino']],
        'okada'          => ['Okada Manila',         ['Okada Manila Paranaque resort', 'Okada Manila Entertainment City', 'Okada Manila fountain Philippines']],
        'resorts_world'  => ['Resorts World',        ['Resorts World Manila Newport Pasay', 'Newport City Pasay Mall', 'Newport Mall Resorts World']],
        'opus'           => ['Opus Mall',            ['Opus Mall Bridgetowne Pasig', 'Opus Mall Robinsons Pasig', 'Opus mall Philippines']],
        'eastwood'       => ['Eastwood City',        ['Eastwood City Libis Quezon City', 'Eastwood Cyber Park Manila', 'Eastwood Mall Quezon City']],
        'rockwell'       => ['Rockwell',             ['Rockwell Powerplant Mall Makati', 'Rockwell Center Makati Philippines', 'Powerplant Mall Rockwell tower']],
        'greenhills'     => ['Greenhills',           ['Greenhills Shopping Center San Juan', 'Greenhills Promenade San Juan', 'Greenhills mall tiangge San Juan']],
        'tomas_morato'   => ['Tomas Morato',         ['Tomas Morato Quezon City avenue', 'Tomas Morato restaurant strip QC', 'Tomas Morato food strip Manila']],
        'kapitolyo'      => ['Kapitolyo',            ['Kapitolyo Pasig food strip', 'Kapitolyo East Capitol Pasig', 'Kapitolyo restaurants Pasig']],
        'maginhawa'      => ['Maginhawa',            ['Maginhawa Street Quezon City food', 'Maginhawa Sikatuna Quezon City', 'Maginhawa Diliman QC street']],
        'banawe'         => ['Banawe',               ['Banawe Avenue Quezon City', 'Banawe street Filipino-Chinese', 'Banawe Quezon City restaurants']],
        'cubao'          => ['Cubao',                ['Cubao Araneta Center QC', 'Cubao Expo Quezon City', 'Araneta Coliseum Cubao']],
        'katipunan'      => ['Katipunan',            ['Katipunan Avenue Ateneo QC', 'Katipunan Loyola Heights Manila', 'Katipunan UP Diliman']],
        'ortigas'        => ['Ortigas',              ['Ortigas Center Pasig Mandaluyong', 'Ortigas Avenue business district', 'Ortigas skyline Manila']],
        'antipolo'       => ['Antipolo',             ['Antipolo Cathedral Rizal', 'Antipolo Sumulong Highway view', 'Antipolo Rizal Pinto Art']],
        'manila_old'     => ['Old Manila',           ['Intramuros Manila walled city', 'Binondo Chinatown Manila', 'Manila old city streets']],
        'alabang'        => ['Alabang',              ['Alabang Town Center Muntinlupa', 'Alabang Filinvest City Manila', 'Alabang Westgate Muntinlupa']],
        'bf_homes'       => ['BF Homes',             ['BF Homes Aguirre Paranaque', 'BF Homes Paranaque restaurants', 'BF Homes Tropical Avenue']],
        'nuvali'         => ['Nuvali Sta. Rosa',     ['Nuvali Solenad Sta Rosa Laguna', 'Nuvali Lake Sta Rosa', 'Nuvali Ayala Sta Rosa']],
        'mckinley'       => ['McKinley Hill',        ['McKinley Hill Taguig Venice', 'McKinley Venice Grand Canal', 'McKinley West Taguig']],
        'makati_inner'   => ['Poblacion Makati',     ['Poblacion Makati Burgos strip', 'Poblacion Makati nightlife', 'Makati Avenue Poblacion']],
        'circuit'        => ['Circuit Makati',       ['Circuit Makati Ayala open lot', 'Circuit Makati events ground', 'Circuit Makati lawn']],
        'camp_john_hay'  => ['Camp John Hay',        ['Camp John Hay Baguio pine', 'Camp John Hay Manor Baguio', 'Camp John Hay forest cottages']],
        'ayala_cebu'     => ['Ayala Center Cebu',    ['Ayala Center Cebu mall', 'Ayala Cebu Business Park', 'Ayala mall Cebu City Philippines']],
        'cebu_sm'        => ['SM Seaside Cebu',      ['SM Seaside Cebu South Road', 'SM City Cebu reclamation', 'SM Seaside Cebu shape building']],
        'it_park'        => ['Cebu IT Park',         ['Cebu IT Park Lahug', 'IT Park Cebu BPO district', 'Apas IT Park Cebu']],
        'makati'         => ['Makati',               ['Makati CBD skyline', 'Salcedo Village Makati food', 'Ayala Avenue Makati office']],
        'qc'             => ['Quezon City',          ['Quezon Memorial Circle QC', 'Quezon City Hall', 'Quezon City skyline view']],
        'manila'         => ['Manila',               ['Manila Roxas Boulevard sunset', 'Manila skyline Roxas Blvd', 'Manila Bay Pasay landmark']],
        'cebu'           => ['Cebu City',            ['Cebu City skyline downtown', 'Magellan Cross Cebu City', 'Cebu City Philippines']],
        'davao'          => ['Davao City',           ['Davao downtown Roxas Avenue', 'Davao People Park sculpture', 'Davao city Mindanao']],
        'iloilo'         => ['Iloilo City',          ['Iloilo Calle Real heritage', 'Iloilo River Esplanade', 'Iloilo City Molo plaza']],
        'bacolod'        => ['Bacolod',              ['Bacolod City plaza Negros', 'Bacolod Capitol Lagoon', 'Bacolod public plaza']],
        'cdo'            => ['Cagayan de Oro',       ['Cagayan de Oro Plaza Divisoria', 'CDO Misamis Oriental', 'Cagayan de Oro skyline']],
        'tacloban'       => ['Tacloban',             ['Tacloban City Leyte plaza', 'Tacloban San Juanico bridge', 'Tacloban capitol Leyte']],
        'naga'           => ['Naga',                 ['Naga City Cathedral Camarines Sur', 'Naga plaza Bicol', 'Naga River park']],
        'legazpi'        => ['Legazpi',              ['Mayon Volcano Legazpi Albay', 'Legazpi Cagsawa Ruins', 'Legazpi City Bicol']],
        'lipa'           => ['Lipa',                 ['Lipa Cathedral Batangas', 'Lipa City Batangas plaza', 'Lipa Mount Malarayat']],
        'batangas'       => ['Batangas',             ['Batangas City plaza', 'Batangas province landmark', 'Batangas pier port']],
        'clark'          => ['Clark',                ['Clark Freeport Pampanga', 'Angeles City Pampanga', 'Clark Mimosa Pampanga']],
        'pampanga'       => ['Pampanga',             ['San Fernando Pampanga lantern', 'Pampanga landmark capitol', 'Sisig Pampanga Angeles']],
        'tagaytay'       => ['Tagaytay',             ['Tagaytay Taal Volcano view', 'Tagaytay ridge Aguinaldo highway', 'Tagaytay Picnic Grove view']],
        'baguio'         => ['Baguio',               ['Baguio Session Road view', 'Baguio Burnham Park lake', 'Baguio Mines View Park']],
        'boracay'        => ['Boracay',              ['Boracay White Beach Aklan', 'Boracay Station 2 D Mall', 'Boracay Willy Rock']],
        'el_nido'        => ['El Nido',              ['El Nido Big Lagoon Palawan', 'El Nido Bacuit Bay limestone', 'El Nido Las Cabanas sunset']],
        'coron'          => ['Coron',                ['Coron Kayangan Lake Palawan', 'Coron Twin Lagoon', 'Coron Busuanga Palawan']],
        'puerto_princesa'=> ['Puerto Princesa',      ['Puerto Princesa Underground River', 'Puerto Princesa baywalk', 'Puerto Princesa City']],
        'puerto_galera'  => ['Puerto Galera',        ['Puerto Galera White Beach Mindoro', 'Puerto Galera Sabang', 'Puerto Galera Mindoro Philippines']],
        'siargao'        => ['Siargao',              ['Siargao Cloud 9 boardwalk', 'Siargao General Luna beach', 'Siargao surfing wave']],
        'bohol'          => ['Bohol',                ['Bohol Chocolate Hills Carmen', 'Panglao Island Bohol', 'Bohol Loboc River cruise']],
        'subic'          => ['Subic',                ['Subic Bay Boardwalk Freeport', 'Subic Bay Zambales', 'Subic Freeport Zambales']],
        'la_union'       => ['La Union',             ['La Union San Juan surf', 'Urbiztondo Beach La Union', 'La Union beach Manila']],
        'vigan'          => ['Vigan',                ['Vigan Calle Crisologo Ilocos', 'Vigan Heritage City', 'Vigan Plaza Burgos empanada']],
    ];

    public function run(): void
    {
        $dir = storage_path('app/public/rg-media/food-locations');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $this->command->info('=== Broad-internet image download via DuckDuckGo ===');
        $this->command->info('Searching ' . count($this->locationQueries) . ' locations...');

        $totalAdded = 0;
        $idx = 0;
        foreach ($this->locationQueries as $key => [$display, $queries]) {
            $idx++;
            $added = $this->fillLocation($key, $display, $queries);
            $totalAdded += $added;
            if ($added > 0) {
                $this->command->info("  [$idx] $key: +$added (now " . $this->countImages($key) . ')');
            }
        }

        $this->command->info('');
        $this->command->info("Phase 1 done. Added: $totalAdded new images.");
        $this->command->info('Phase 2: rebuilding sliders with source attribution...');
        $this->rebuildAllSliders();
    }

    private function countImages(string $key): int
    {
        return count(glob(storage_path('app/public/rg-media/food-locations/' . $key . '*.jpg')));
    }

    /**
     * Aim for 5 images per location. If we already have 5 from prior runs,
     * skip. Otherwise, DDG search until we have 5 or queries exhausted.
     */
    private function fillLocation(string $key, string $display, array $queries): int
    {
        $current = $this->countImages($key);
        if ($current >= 5) return 0;

        $needed = 5 - $current;
        $added = 0;
        $usedUrls = [];

        foreach ($queries as $q) {
            if ($added >= $needed) break;
            $results = $this->searchDuckDuckGo($q, 15);
            foreach ($results as $r) {
                if ($added >= $needed) break;
                if (in_array($r['url'], $usedUrls, true)) continue;
                if ($this->downloadAndSave($key, $current + $added + 1, $r, $display)) {
                    $added++;
                    $usedUrls[] = $r['url'];
                }
            }
        }
        return $added;
    }

    /**
     * DuckDuckGo image search (no API key). Two-step:
     *   1. Fetch the search page to obtain the vqd token.
     *   2. Hit i.js with the token to get JSON results.
     */
    private function searchDuckDuckGo(string $query, int $limit): array
    {
        try {
            // Step 1: get vqd token
            $resp = Http::withHeaders($this->ua)->timeout(20)->get('https://duckduckgo.com/', [
                'q' => $query,
                'iar' => 'images',
                'iax' => 'images',
                'ia' => 'images',
            ]);
            if (!$resp->successful()) return [];
            $body = $resp->body();
            if (!preg_match('/vqd=["\']?([\d-]+)["\']?/', $body, $m)) return [];
            $vqd = $m[1];

            // Step 2: get image results
            $resp = Http::withHeaders(array_merge($this->ua, [
                'Referer' => 'https://duckduckgo.com/',
                'X-Requested-With' => 'XMLHttpRequest',
            ]))->timeout(20)->get('https://duckduckgo.com/i.js', [
                'l' => 'us-en',
                'o' => 'json',
                'q' => $query,
                'vqd' => $vqd,
                'f' => ',,,,,',
                'p' => '1',
            ]);
            if (!$resp->successful()) return [];

            $data = $resp->json();
            $results = [];
            foreach (($data['results'] ?? []) as $r) {
                if (empty($r['image']) || empty($r['url'])) continue;
                $w = (int) ($r['width'] ?? 0);
                $h = (int) ($r['height'] ?? 0);
                // Filter: landscape, min 800x500
                if ($w < 800 || $h < 500) continue;
                if ($h >= $w) continue;
                // Filter: skip uncommon hosts / weird URLs
                $img = $r['image'];
                if (!preg_match('~^https?://~i', $img)) continue;
                if (!preg_match('~\.(jpe?g|png|webp)([?#]|$)~i', $img)) continue;

                $results[] = [
                    'url' => $img,
                    'source_url' => $r['url'],
                    'source_title' => $r['title'] ?? '',
                    'source_domain' => $this->extractDomain($r['url']),
                    'width' => $w,
                    'height' => $h,
                ];
                if (count($results) >= $limit) break;
            }
            return $results;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function downloadAndSave(string $key, int $slot, array $result, string $display): bool
    {
        $localPath = 'rg-media/food-locations/' . $key . ($slot === 1 ? '' : "-$slot") . '.jpg';
        $absPath = storage_path('app/public/' . $localPath);

        try {
            $resp = Http::withHeaders([
                    'User-Agent' => $this->ua['User-Agent'],
                    'Referer' => $result['source_url'],
                ])
                ->timeout(40)
                ->withOptions(['allow_redirects' => true])
                ->get($result['url']);

            if (!$resp->successful()) return false;

            $contentType = $resp->header('content-type', '');
            if (!str_starts_with($contentType, 'image/')) return false;

            $body = $resp->body();
            if (strlen($body) < 5000 || strlen($body) > 8 * 1024 * 1024) return false;

            file_put_contents($absPath, $body);
            $this->upsertMedia($localPath, $result, $display);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function upsertMedia(string $localPath, array $result, string $display): void
    {
        $absPath = storage_path('app/public/' . $localPath);
        $width = $height = null;
        try {
            $sz = @getimagesize($absPath);
            if ($sz) { $width = $sz[0]; $height = $sz[1]; }
        } catch (\Throwable $e) {}

        DB::table('rg_media')->updateOrInsert(
            ['path' => $localPath],
            [
                'filename'   => basename($localPath),
                'path'       => $localPath,
                'mime'       => 'image/jpeg',
                'size_bytes' => is_file($absPath) ? filesize($absPath) : 0,
                'kind'       => 'image',
                'width'      => $width,
                'height'     => $height,
                'alt'        => $display,
                'caption'    => $display,
                'source'     => 'seeder-broad-internet',
                'credit'     => 'Photo via ' . ($result['source_domain'] ?? 'web'),
                'source_url' => $result['source_url'],
                'meta_json'  => json_encode([
                    'image_url'    => $result['url'],
                    'source_title' => $result['source_title'] ?? null,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function extractDomain(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        return preg_replace('/^www\./i', '', $host);
    }

    // === Phase 2: rebuild sliders with attribution =======================

    private array $imageMediaByPath = [];

    private function loadMediaCredits(): void
    {
        $rows = DB::table('rg_media')->where('path', 'like', 'rg-media/food-locations/%')->get();
        foreach ($rows as $r) {
            $this->imageMediaByPath[$r->path] = $r;
        }
    }

    private function rebuildAllSliders(): void
    {
        $this->loadMediaCredits();

        $keywords = DB::table('rg_keywords')->where('category', 'food')->get();
        $processed = 0;
        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') continue;
            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page) continue;

            $loc = $this->extractLocation($kw->phrase);
            $key = $this->normalizeLocation($loc);
            $area = $this->locationQueries[$key][0] ?? $this->properTitle($loc);

            $hero = $this->buildAttributedSlider($key, $area);
            if (!$hero) continue;

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'hero_html'  => $hero,
                'updated_at' => now(),
            ]);
            $processed++;
            if ($processed % 100 === 0) {
                $this->command->info("  $processed pages updated...");
            }
        }
        $this->command->info("Slider rebuild done. Updated: $processed pages.");
    }

    private function buildAttributedSlider(string $key, string $area): string
    {
        $files = glob(storage_path('app/public/rg-media/food-locations/' . $key . '*.jpg'));
        if (empty($files)) return '';
        sort($files);

        $slideHtml = '';
        foreach (array_slice($files, 0, 5) as $f) {
            $relPath = 'rg-media/food-locations/' . basename($f);
            $url = asset('storage/' . $relPath);
            $media = $this->imageMediaByPath[$relPath] ?? null;
            $credit = $media->credit ?? 'Photo: Wikimedia Commons (CC-BY-SA)';
            $sourceUrl = $media->source_url ?? null;

            $captionAttr = '';
            if ($sourceUrl) {
                $domain = $this->extractDomain($sourceUrl);
                $captionAttr = '<small><a href="' . e($sourceUrl) . '" rel="nofollow noopener" target="_blank" style="color:#fbbf24;text-decoration:underline">Photo via ' . e($domain) . '</a></small>';
            } else {
                $captionAttr = '<small>' . e($credit) . '</small>';
            }

            $slideHtml .= '<li class="splide__slide">'
                . '<figure class="rg-area-hero__slide">'
                . '<img src="' . e($url) . '" alt="' . e($area) . '" loading="lazy">'
                . '<figcaption><strong>' . e($area) . '</strong>' . $captionAttr . '</figcaption>'
                . '</figure>'
                . '</li>';
        }

        return <<<HTML
<section class="rg-area-hero my-8 not-prose" aria-label="$area photos">
    <div class="flex items-baseline justify-between mb-3">
        <h2 class="text-xs uppercase tracking-[0.18em] font-bold text-brand-700 m-0">Inside $area</h2>
        <span class="text-xs text-slate-500">Photos credited per slide. Editorial fair use with linkback.</span>
    </div>
    <div class="rg-area-hero__splide splide">
        <div class="splide__track"><ul class="splide__list">$slideHtml</ul></div>
    </div>
</section>
<style>
    .rg-area-hero { width: 100%; }
    .rg-area-hero__splide .splide__list { align-items: stretch; }
    .rg-area-hero__slide { position: relative; margin: 0; border-radius: 1rem; overflow: hidden; background: #f1f5f9; }
    .rg-area-hero__slide img { width: 100%; aspect-ratio: 21/9; object-fit: cover; display: block; height: auto; }
    @media (max-width: 640px) { .rg-area-hero__slide img { aspect-ratio: 16/10; } }
    .rg-area-hero__slide figcaption {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 1.1rem 1.5rem 1.3rem;
        background: linear-gradient(180deg, transparent 0%, rgba(15,23,42,0.93) 100%);
        color: #fff;
    }
    .rg-area-hero__slide figcaption strong { display: block; font-size: 1.1rem; margin-bottom: 0.2rem; font-weight: 700; }
    .rg-area-hero__slide figcaption small { font-size: 0.75rem; opacity: 0.92; }
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
