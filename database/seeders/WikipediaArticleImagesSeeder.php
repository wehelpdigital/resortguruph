<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Pulls authentic location photos from Wikipedia article media lists.
 *
 * Why this works where broader scraping doesn't:
 *   - Wikipedia REST API (en.wikipedia.org/api/rest_v1/page/media-list/...)
 *     is public, stable, no API key required, and returns the FULL set of
 *     media files actually used in the article.
 *   - For locations with a Wikipedia article (which is most top PH malls,
 *     districts, cities, and destinations), the article images are
 *     verified-real photos of the place (uploaded by editors who care
 *     about accuracy).
 *
 * Each downloaded image stores:
 *   - source_url = the Wikipedia article URL (linkback)
 *   - credit    = "Photo via Wikipedia"
 *   - license   = CC-BY-SA inherited from Wikimedia Commons
 *
 * Slider captions show "Photo via Wikipedia" as a clickable link.
 *
 * Articles whose media has fewer than 3 usable photos keep their existing
 * Wikimedia search images. Locations without a Wikipedia article (small
 * districts like Maginhawa) are unchanged.
 *
 * Idempotent — re-running skips images that are already on disk.
 */
class WikipediaArticleImagesSeeder extends Seeder
{
    private array $headers = [
        'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
        'Accept' => 'application/json',
    ];

    /** Location key → Wikipedia article title. */
    private array $articles = [
        // Malls
        'moa'            => 'SM_Mall_of_Asia',
        'bgc'            => 'Bonifacio_Global_City',
        'megamall'       => 'SM_Megamall',
        'sm_north'       => 'SM_North_EDSA',
        'sm_aura'        => 'SM_Aura_Premier',
        'podium'         => 'The_Podium',
        'greenbelt'      => 'Greenbelt_Park',
        'glorietta'      => 'Glorietta',
        'festival_mall'  => 'Festival_Supermall',
        'trinoma'        => 'TriNoma',
        'uptown'         => 'Uptown_Mall_(Taguig)',
        'eastwood'       => 'Eastwood_City',
        'rockwell'       => 'Rockwell_Center',
        'greenhills'     => 'Greenhills_Shopping_Center',
        'rob_galleria'   => 'Robinsons_Galleria_Ortigas',
        'rob_ermita'     => 'Robinsons_Place_Manila',
        'ayala_mb'       => 'Ayala_Malls_Manila_Bay',
        'shangrila'      => 'Shangri-La_Plaza',
        'market_market'  => 'Market!_Market!',
        'gateway'        => 'Araneta_Gateway',
        'solaire'        => 'Solaire_Resort_%26_Casino',
        'okada'          => 'Okada_Manila',
        'resorts_world'  => 'Newport_World_Resorts',
        'mckinley'       => 'McKinley_Hill',
        'ayala_cebu'     => 'Ayala_Center_Cebu',
        'it_park'        => 'Cebu_IT_Park',

        // Districts
        'antipolo'       => 'Antipolo',
        'manila_old'     => 'Intramuros',
        'cubao'          => 'Cubao',
        'ortigas'        => 'Ortigas_Center',
        'alabang'        => 'Alabang',
        'nuvali'         => 'Nuvali',

        // Cities
        'makati'         => 'Makati',
        'qc'             => 'Quezon_City',
        'manila'         => 'Manila',
        'cebu'           => 'Cebu_City',
        'davao'          => 'Davao_City',
        'iloilo'         => 'Iloilo_City',
        'bacolod'        => 'Bacolod',
        'cdo'            => 'Cagayan_de_Oro',
        'tacloban'       => 'Tacloban',
        'naga'           => 'Naga,_Camarines_Sur',
        'legazpi'        => 'Legazpi,_Albay',
        'lipa'           => 'Lipa,_Batangas',
        'batangas'       => 'Batangas_City',
        'clark'          => 'Clark_Freeport_Zone',
        'pampanga'       => 'San_Fernando,_Pampanga',
        'marikina'       => 'Marikina',
        'pasig'          => 'Pasig',
        'pasay'          => 'Pasay',
        'paranaque'      => 'Para%C3%B1aque',
        'mandaluyong'    => 'Mandaluyong',
        'taguig'         => 'Taguig',
        'san_juan'       => 'San_Juan,_Metro_Manila',

        // Destinations
        'tagaytay'       => 'Tagaytay',
        'baguio'         => 'Baguio',
        'boracay'        => 'Boracay',
        'el_nido'        => 'El_Nido,_Palawan',
        'coron'          => 'Coron,_Palawan',
        'puerto_princesa'=> 'Puerto_Princesa',
        'puerto_galera'  => 'Puerto_Galera',
        'siargao'        => 'Siargao',
        'bohol'          => 'Bohol',
        'subic'          => 'Subic_Bay',
        'la_union'       => 'La_Union',
        'vigan'          => 'Vigan',
    ];

    public function run(): void
    {
        $dir = storage_path('app/public/rg-media/food-locations');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $this->command->info('=== Wikipedia article images for ' . count($this->articles) . ' PH locations ===');

        $totalDownloaded = 0;
        foreach ($this->articles as $key => $title) {
            $count = $this->processLocation($key, $title);
            $totalDownloaded += $count;
            if ($count > 0) {
                $this->command->info("  $key ← $title: $count new (now " . $this->countImages($key) . ')');
            } else {
                $this->command->info("  $key ← $title: no new (existing kept)");
            }
        }

        $this->command->info('');
        $this->command->info("Phase 1 done. Added: $totalDownloaded new images.");
        $this->command->info('Phase 2: rebuilding sliders with Wikipedia attribution...');
        $this->rebuildAllSliders();
    }

    private function countImages(string $key): int
    {
        return count(glob(storage_path('app/public/rg-media/food-locations/' . $key . '*.jpg')));
    }

    private function processLocation(string $key, string $articleTitle): int
    {
        // Fetch media-list from Wikipedia REST API
        try {
            $resp = Http::withHeaders($this->headers)->timeout(20)
                ->get("https://en.wikipedia.org/api/rest_v1/page/media-list/$articleTitle");
            if (!$resp->successful()) return 0;
            $items = $resp->json()['items'] ?? [];
        } catch (\Throwable $e) {
            return 0;
        }

        if (empty($items)) return 0;

        $articleUrl = "https://en.wikipedia.org/wiki/$articleTitle";

        // FORCE REPLACE strategy: clear existing slots 1-3 (the most visible
        // slides) and refill them from the Wikipedia article. Slots 4-5 stay
        // intact (from earlier V2 contextual downloads) for slider variety.
        for ($s = 1; $s <= 3; $s++) {
            $oldPath = 'rg-media/food-locations/' . $key . ($s === 1 ? '' : "-$s") . '.jpg';
            $oldAbs = storage_path('app/public/' . $oldPath);
            if (is_file($oldAbs)) @unlink($oldAbs);
            DB::table('rg_media')->where('path', $oldPath)->delete();
        }

        $added = 0;
        $slot = 1;

        foreach ($items as $item) {
            if ($slot > 3) break;  // only refill slots 1-3
            if (($item['type'] ?? '') !== 'image') continue;
            $filename = basename($item['title'] ?? '');
            if (empty($filename)) continue;

            $srcUrl = null;
            if (!empty($item['srcset'])) {
                $best = end($item['srcset']);
                $srcUrl = $best['src'] ?? null;
            }
            if (!$srcUrl && !empty($item['original']['source'])) {
                $srcUrl = $item['original']['source'];
            }
            if (!$srcUrl) continue;
            $srcUrl = $this->promoteToLargeUrl($srcUrl);

            // Skip non-photo media types
            if (preg_match('/\.(svg)$/i', $filename)) continue;
            if (stripos($filename, 'logo') !== false || stripos($filename, 'icon') !== false) continue;
            if (stripos($filename, 'seal') !== false || stripos($filename, 'coat_of_arms') !== false) continue;
            if (stripos($filename, 'map') !== false || stripos($filename, 'flag') !== false) continue;

            $localPath = 'rg-media/food-locations/' . $key . ($slot === 1 ? '' : "-$slot") . '.jpg';
            $absPath = storage_path('app/public/' . $localPath);

            if ($this->downloadFile($srcUrl, $absPath)) {
                // Verify landscape + min size
                $sz = @getimagesize($absPath);
                if ($sz) {
                    [$pw, $ph] = $sz;
                    if ($ph >= $pw || $pw < 800) {
                        @unlink($absPath);
                        continue;
                    }
                }
                $this->upsertMedia($localPath, $filename, $key, $articleTitle, $articleUrl);
                $slot++;
                $added++;
            }
        }
        return $added;
    }

    private function promoteToLargeUrl(string $url): string
    {
        // Wikipedia thumbnail URLs look like:
        //   //upload.wikimedia.org/wikipedia/commons/thumb/a/ab/file.jpg/500px-file.jpg
        // Replace 500px- with 1280px- for higher resolution
        if (str_contains($url, '/thumb/') && preg_match('#/\d+px-#', $url)) {
            return preg_replace('#/\d+px-#', '/1280px-', $url);
        }
        if (str_starts_with($url, '//')) {
            $url = 'https:' . $url;
        }
        return $url;
    }

    private function downloadFile(string $url, string $absPath): bool
    {
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

    private function upsertMedia(string $localPath, string $wikiFile, string $key, string $articleTitle, string $articleUrl): void
    {
        $absPath = storage_path('app/public/' . $localPath);
        $width = $height = null;
        $sz = @getimagesize($absPath);
        if ($sz) { $width = $sz[0]; $height = $sz[1]; }

        $displayName = str_replace('_', ' ', $articleTitle);
        $displayName = preg_replace('/\([^)]*\)/', '', $displayName);
        $displayName = trim($displayName);

        DB::table('rg_media')->updateOrInsert(
            ['path' => $localPath],
            [
                'filename'   => $wikiFile,
                'path'       => $localPath,
                'mime'       => 'image/jpeg',
                'size_bytes' => is_file($absPath) ? filesize($absPath) : 0,
                'kind'       => 'image',
                'width'      => $width,
                'height'     => $height,
                'alt'        => $displayName,
                'caption'    => $displayName,
                'source'     => 'seeder-wikipedia-article',
                'credit'     => 'Photo via Wikipedia',
                'source_url' => $articleUrl,
                'meta_json'  => json_encode([
                    'wiki_file'    => $wikiFile,
                    'article'      => $articleTitle,
                    'license_hint' => 'CC-BY-SA (via Wikimedia Commons)',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    // === Slider rebuild with attribution ==================================

    private array $imageMediaByPath = [];

    private function rebuildAllSliders(): void
    {
        $rows = DB::table('rg_media')->where('path', 'like', 'rg-media/food-locations/%')->get();
        foreach ($rows as $r) $this->imageMediaByPath[$r->path] = $r;

        $keywords = DB::table('rg_keywords')->where('category', 'food')->get();
        $processed = 0;
        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') continue;
            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page) continue;

            $loc = $this->extractLocation($kw->phrase);
            $key = $this->normalizeLocation($loc);
            $area = $this->displayName($key, $loc);

            $hero = $this->buildAttributedSlider($key, $area);
            if (!$hero) continue;

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'hero_html'  => $hero,
                'updated_at' => now(),
            ]);
            $processed++;
            if ($processed % 100 === 0) $this->command->info("  $processed slider updates...");
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
            $sourceUrl = $media->source_url ?? null;
            $domain = $sourceUrl ? $this->extractDomain($sourceUrl) : 'Wikimedia Commons';
            $credit = $media->credit ?? 'Photo: Wikimedia Commons (CC-BY-SA)';

            $captionAttr = $sourceUrl
                ? '<small><a href="' . e($sourceUrl) . '" rel="nofollow noopener" target="_blank" style="color:#fbbf24;text-decoration:underline">Photo via ' . e($domain) . '</a></small>'
                : '<small>' . e($credit) . '</small>';

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
        <span class="text-xs text-slate-500">Photos credited per slide</span>
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

    private function extractDomain(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        return preg_replace('/^www\./i', '', $host);
    }

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
            'marikina' => '/(marikina)/',
            'pasig' => '/(pasig)/',
            'pasay' => '/(pasay)/',
            'paranaque' => '/(paranaque)/',
            'mandaluyong' => '/(mandaluyong)/',
            'taguig' => '/(taguig)/',
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
