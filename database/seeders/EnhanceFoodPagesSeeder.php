<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Rewrites EVERY food-category SEO page with:
 *   - Location-specific intro/body/FAQ keyed by a normalized location
 *     ("bgc high street" / "bgc" / "burgos circle" all map to the same
 *     BGC facts; "mall of asia" / "moa" all map to MOA facts).
 *   - One embedded Wikimedia photo of the actual location (cached by
 *     normalized key so 12 Tagaytay keywords share one Tagaytay download).
 *   - An external-links block at the bottom with TripAdvisor + Google
 *     deep-links pre-filled with the keyword phrase.
 *
 * Idempotent and overwrite-safe: re-running refreshes content from the
 * current facts bank without touching keyword rows or listings.
 *
 * Content rules respected:
 *   - No em-dashes
 *   - No banned words (nestled, bustling, vibrant, tapestry, must-try,
 *     in the heart of, hidden gem, breathtaking, delve, embark)
 *   - Filipino DIY-traveler voice
 *   - Keyword phrase woven 4-6 times per page
 */
class EnhanceFoodPagesSeeder extends Seeder
{
    private array $headers = [
        'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
        'Accept'     => 'application/json, image/jpeg, image/*',
    ];

    /** Runtime cache: normalized location key → local image path (or false on fail). */
    private array $imageCache = [];

    public function run(): void
    {
        $this->ensureDir(storage_path('app/public/rg-media/food-locations'));

        $foodKeywords = DB::table('rg_keywords')->where('category', 'food')->get();
        $this->command->info('Enhancing ' . $foodKeywords->count() . ' food pages...');

        $updated = 0; $skippedNoPage = 0;
        foreach ($foodKeywords as $kw) {
            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page) { $skippedNoPage++; continue; }

            $loc = $this->extractLocation($kw->phrase);
            $key = $this->normalizeLocation($loc);
            $facts = $this->locationFacts($key, $loc);
            $imageUrl = $this->getOrDownloadImage($key, $facts);
            $content = $this->buildContent($kw->phrase, $loc, $facts, $imageUrl);

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'h1'               => $content['h1'],
                'subtitle'         => $content['subtitle'],
                'meta_description' => $content['meta'],
                'intro_html'       => $content['intro_html'],
                'body_html'        => $content['body_html'],
                'faq_json'         => json_encode($content['faqs']),
                'updated_at'       => now(),
            ]);
            $updated++;
            if ($updated % 50 === 0) $this->command->info("  $updated pages updated...");
        }

        $imagesDownloaded = count(array_filter($this->imageCache));
        $this->command->info("Done. Updated: $updated | Skipped (no page): $skippedNoPage | Unique location images downloaded: $imagesDownloaded");
    }

    /**
     * Strip qualifiers + "philippines" + "with view" to leave just the
     * location anchor. Mirrors AllFoodKeywordsSeeder so behaviour is
     * consistent across the two seeders.
     */
    private function extractLocation(string $phrase): string
    {
        $p = mb_strtolower(trim($phrase));
        $patterns = [
            '/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/',
            '/\b(filipino|japanese|korean|chinese|italian|seafood|steak|sushi|buffet|fine\s+dining)\s+/',
            '/^philippines\s+/',
            '/^antonio\'?s\s+/',
        ];
        foreach ($patterns as $pat) $p = preg_replace($pat, '', $p);

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

    /**
     * Map every variant of a location to a single canonical key. This is
     * what lets "bgc high street", "bgc", "burgos circle" share BGC facts.
     */
    private function normalizeLocation(string $loc): string
    {
        $l = mb_strtolower(trim($loc));

        // Malls + landmark complexes
        if (preg_match('/^(mall of asia|moa)( seaside)?$/', $l) || $l === 'sm moa' || str_contains($l, 'mall of asia')) return 'moa';
        if (preg_match('/(bgc|bonifacio|burgos circle|high street|uptown bgc)/', $l)) return 'bgc';
        if (preg_match('/(sm megamall|^megamall$)/', $l)) return 'megamall';
        if (preg_match('/(sm north|sm city north|north edsa)/', $l)) return 'sm_north';
        if (preg_match('/(podium)/', $l)) return 'podium';
        if (preg_match('/(greenbelt)/', $l)) return 'greenbelt';
        if (preg_match('/(glorietta)/', $l)) return 'glorietta';
        if (preg_match('/(festival mall|festival$|festive)/', $l)) return 'festival_mall';
        if (preg_match('/(sm aura)/', $l)) return 'sm_aura';
        if (preg_match('/(tomas morato)/', $l)) return 'tomas_morato';
        if (preg_match('/(trinoma)/', $l)) return 'trinoma';
        if (preg_match('/(uptown mall|up town center|uptc)/', $l)) return 'uptown';
        if (preg_match('/(eastwood)/', $l)) return 'eastwood';
        if (preg_match('/(rockwell|powerplant)/', $l)) return 'rockwell';
        if (preg_match('/(greenhills)/', $l)) return 'greenhills';
        if (preg_match('/(robinsons galleria)/', $l)) return 'robinsons_galleria';
        if (preg_match('/(robinsons ermita)/', $l)) return 'robinsons_ermita';
        if (preg_match('/(ayala mall(s)? manila bay|ayala manila bay)/', $l)) return 'ayala_manila_bay';
        if (preg_match('/(shangrila mall|shangri la mall|edsa shangrila)/', $l)) return 'shangrila';
        if (preg_match('/(market market)/', $l)) return 'market_market';
        if (preg_match('/(gateway)/', $l)) return 'gateway';
        if (preg_match('/(solaire)/', $l)) return 'solaire';
        if (preg_match('/(okada)/', $l)) return 'okada';
        if (preg_match('/(resorts world|newport)/', $l)) return 'resorts_world';
        if (preg_match('/(intramuros|binondo|quiapo|malate)/', $l)) return 'manila_old';
        if (preg_match('/(ayala center cebu|ayala cebu|cebu ayala)/', $l)) return 'ayala_cebu';
        if (preg_match('/(sm city cebu|sm seaside|nustar)/', $l)) return 'cebu_malls';
        if (preg_match('/(it park)/', $l)) return 'it_park';
        if (preg_match('/(mckinley)/', $l)) return 'mckinley';
        if (preg_match('/(opus mall)/', $l)) return 'opus';

        // Districts / neighborhoods
        if (preg_match('/(alabang|atc|filinvest|westgate alabang|molito)/', $l)) return 'alabang';
        if (preg_match('/(bf homes|bf$)/', $l)) return 'bf_homes';
        if (preg_match('/(nuvali|sta rosa|santa rosa)/', $l)) return 'nuvali';
        if (preg_match('/(kapitolyo)/', $l)) return 'kapitolyo';
        if (preg_match('/(katipunan|up diliman|ust)/', $l)) return 'katipunan';
        if (preg_match('/(maginhawa)/', $l)) return 'maginhawa';
        if (preg_match('/(banawe)/', $l)) return 'banawe';
        if (preg_match('/(cubao)/', $l)) return 'cubao';
        if (preg_match('/(ortigas|capitol commons|tiendesitas|estancia|galleria$)/', $l)) return 'ortigas';
        if (preg_match('/(antipolo)/', $l)) return 'antipolo';
        if (preg_match('/(poblacion|jupiter makati|makati avenue)/', $l)) return 'makati_inner';
        if (preg_match('/(circuit makati|circuit$)/', $l)) return 'circuit';
        if (preg_match('/(camp john hay)/', $l)) return 'camp_john_hay';

        // Cities
        if (preg_match('/(^|\W)(makati)(\W|$)/', $l)) return 'makati';
        if (preg_match('/(quezon city|quezon ave|^qc$|visayas ave|visayas avenue|west avenue|fairview|timog|white plains|don antonio)/', $l)) return 'qc';
        if (preg_match('/(^|\W)(manila|manila peninsula|robinsons manila)(\W|$)/', $l)) return 'manila';
        if (preg_match('/(^|\W)(cebu)(\W|$)/', $l) || preg_match('/^cebu/', $l)) return 'cebu';
        if (preg_match('/(davao)/', $l)) return 'davao';
        if (preg_match('/(iloilo)/', $l)) return 'iloilo';
        if (preg_match('/(bacolod)/', $l)) return 'bacolod';
        if (preg_match('/(tacloban)/', $l)) return 'tacloban';
        if (preg_match('/(cagayan|^cdo$)/', $l)) return 'cdo';
        if (preg_match('/(zamboanga)/', $l)) return 'zamboanga';
        if (preg_match('/(naga)/', $l)) return 'naga';
        if (preg_match('/(legazpi|legaspi|albay)/', $l)) return 'legazpi';
        if (preg_match('/(lipa)/', $l)) return 'lipa';
        if (preg_match('/(batangas)/', $l)) return 'batangas';
        if (preg_match('/(angeles|clark)/', $l)) return 'clark';
        if (preg_match('/(san fernando|pampanga)/', $l)) return 'pampanga';
        if (preg_match('/(lucena)/', $l)) return 'lucena';
        if (preg_match('/(marikina)/', $l)) return 'marikina';
        if (preg_match('/(pasig)/', $l)) return 'pasig';
        if (preg_match('/(pasay)/', $l)) return 'pasay';
        if (preg_match('/(paranaque)/', $l)) return 'paranaque';
        if (preg_match('/(mandaluyong)/', $l)) return 'mandaluyong';
        if (preg_match('/(taguig)/', $l)) return 'taguig';
        if (preg_match('/(san juan)/', $l)) return 'san_juan';
        if (preg_match('/(valenzuela)/', $l)) return 'valenzuela';
        if (preg_match('/(malabon)/', $l)) return 'malabon';
        if (preg_match('/(caloocan)/', $l)) return 'caloocan';
        if (preg_match('/(malolos)/', $l)) return 'malolos';
        if (preg_match('/(mandaue)/', $l)) return 'mandaue';
        if (preg_match('/(lapu lapu|lapulapu)/', $l)) return 'lapulapu';
        if (preg_match('/(tarlac)/', $l)) return 'tarlac';
        if (preg_match('/(dagupan|laoag|cabanatuan|urdaneta)/', $l)) return 'north_luzon_city';

        // Destinations
        if (preg_match('/(tagaytay)/', $l)) return 'tagaytay';
        if (preg_match('/(baguio)/', $l)) return 'baguio';
        if (preg_match('/(boracay)/', $l)) return 'boracay';
        if (preg_match('/(el nido)/', $l)) return 'el_nido';
        if (preg_match('/(coron)/', $l)) return 'coron';
        if (preg_match('/(puerto princesa)/', $l)) return 'puerto_princesa';
        if (preg_match('/(puerto galera)/', $l)) return 'puerto_galera';
        if (preg_match('/(siargao)/', $l)) return 'siargao';
        if (preg_match('/(panglao|bohol|tagbilaran)/', $l)) return 'bohol';
        if (preg_match('/(subic|olongapo)/', $l)) return 'subic';
        if (preg_match('/(la union|san juan la union)/', $l)) return 'la_union';
        if (preg_match('/(vigan)/', $l)) return 'vigan';
        if (preg_match('/(moalboal|dumaguete)/', $l)) return 'negros_oriental';
        if (preg_match('/(kalibo|aklan)/', $l)) return 'aklan';
        if (preg_match('/(bohol)/', $l)) return 'bohol';

        return 'generic';
    }

    private function getOrDownloadImage(string $key, array $facts): ?string
    {
        if (array_key_exists($key, $this->imageCache)) return $this->imageCache[$key];

        $localPath = 'rg-media/food-locations/' . $key . '.jpg';
        $absPath   = storage_path('app/public/' . $localPath);

        if (is_file($absPath) && filesize($absPath) > 5000) {
            $url = asset('storage/' . $localPath);
            $this->imageCache[$key] = $url;
            return $url;
        }

        foreach ($facts['image_queries'] as $q) {
            $files = $this->searchCommons($q, 6);
            foreach ($files as $f) {
                if ($this->downloadFile($f, $absPath)) {
                    DB::table('rg_media')->updateOrInsert(
                        ['path' => $localPath],
                        [
                            'filename'   => $f,
                            'path'       => $localPath,
                            'mime'       => 'image/jpeg',
                            'size_bytes' => filesize($absPath),
                            'kind'       => 'image',
                            'alt'        => $facts['display'] . ' food scene',
                            'caption'    => $facts['display'],
                            'source'     => 'seeder-food-location',
                            'credit'     => 'Photo: Wikimedia Commons (CC-BY-SA)',
                            'source_url' => 'https://commons.wikimedia.org/wiki/File:' . $f,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $url = asset('storage/' . $localPath);
                    $this->imageCache[$key] = $url;
                    return $url;
                }
            }
        }
        $this->imageCache[$key] = null;
        return null;
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
                if (($info['width'] ?? 0) < 600 || ($info['height'] ?? 0) < 400) continue;
                if (str_starts_with($title, 'File:')) $files[] = substr($title, 5);
            }
            return $files;
        } catch (\Throwable $e) { return []; }
    }

    private function downloadFile(string $wikiFile, string $absPath): bool
    {
        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($wikiFile) . '?width=1200';
        try {
            $resp = Http::withHeaders($this->headers)->timeout(45)->withOptions(['allow_redirects' => true])->get($url);
            if (!$resp->successful()) return false;
            $body = $resp->body();
            if (strlen($body) < 5000) return false;
            file_put_contents($absPath, $body);
            return true;
        } catch (\Throwable $e) { return false; }
    }

    private function buildContent(string $phrase, string $loc, array $facts, ?string $imageUrl): array
    {
        $kw     = $phrase;
        $area   = $facts['display'];
        $kwTitle = Str::title($phrase);

        // Image embed — sits between intro paragraphs so it lands above the fold.
        $imgEmbed = $imageUrl
            ? '<figure class="my-7 rounded-xl overflow-hidden border border-slate-200 bg-slate-50">'
                . '<img src="' . e($imageUrl) . '" alt="' . e($area) . ' food scene" loading="lazy" class="w-full h-auto block">'
                . '<figcaption class="text-xs text-slate-500 px-4 py-2">Photo of ' . e($area) . ' via Wikimedia Commons (CC-BY-SA)</figcaption>'
            . '</figure>'
            : '';

        $intro = "<p>{$facts['intro_one']}</p>";
        $intro .= $imgEmbed;
        $intro .= "<p>{$facts['intro_two']} {$this->kwAnchor($kw)}</p>";

        $body  = "<h2>Where to start your $kw</h2>";
        $body .= "<p>{$facts['where_to_eat']}</p>";

        $body .= "<h2>Cuisines that work well in $area</h2>";
        $body .= "<p>{$facts['cuisines']}</p>";

        $body .= "<h2>Budget guide</h2>";
        $body .= "<p>{$facts['budget']} If you are scanning for a $kw on a tighter budget, the smaller establishments away from the main pedestrian flow usually price 20 to 30 percent lower than the chains.</p>";

        $body .= "<h2>What to actually order</h2>";
        $body .= "<p>{$facts['order']}</p>";

        $body .= "<h2>How to time your visit</h2>";
        $body .= "<p>{$facts['timing']}</p>";

        // External links block at the bottom
        $body .= $this->externalLinksBlock($phrase, $area, $facts);

        $faqs = [
            ['question' => "What is the best $kw to try first?", 'answer' => $facts['faq_one']],
            ['question' => "How much does a $kw cost on average in $area?", 'answer' => $facts['faq_two']],
            ['question' => "Is it better to book ahead or walk in?", 'answer' => $facts['faq_three']],
            ['question' => "Any local tips for finding a $kw worth the trip?", 'answer' => $facts['faq_four']],
        ];

        return [
            'h1'         => $this->buildSmartH1($kw, $area),
            'subtitle'   => $facts['subtitle'] ?? "What to order, who to trust, and which corners actually serve good food in $area.",
            'meta'       => "Looking for a " . mb_strtolower($kw) . "? Honest picks for $area covering cuisine, price, timing, and what to order. Updated for 2026.",
            'intro_html' => $intro,
            'body_html'  => $body,
            'faqs'       => $faqs,
        ];
    }

    /**
     * Smart H1 that avoids "restaurant in mall of asia in Mall of Asia"
     * redundancy when the phrase already contains the location name, and
     * normalizes capitalisation properly (no "Mall Of Asia").
     */
    private function buildSmartH1(string $kw, string $area): string
    {
        $kwLower = mb_strtolower($kw);
        $areaLower = mb_strtolower($area);
        $kwPretty = Str::title($kw);

        // If phrase already references the area, just use the phrase as H1.
        // Replace lowercased area inside the phrase with properly-cased area.
        if (str_contains($kwLower, $areaLower)) {
            $h1 = str_ireplace($area, $area, $kwPretty); // preserve area's natural casing
            // Force the area substring to its canonical display casing
            $pos = stripos($kwPretty, $area);
            if ($pos !== false) {
                $h1 = substr($kwPretty, 0, $pos) . $area . substr($kwPretty, $pos + strlen($area));
            }
            return "Where to find a good " . $h1;
        }

        // Otherwise it's safe to append the area.
        return "Where to find a good $kwPretty in $area";
    }

    /** Small connector that weaves the keyword into the second intro paragraph. */
    private function kwAnchor(string $kw): string
    {
        return "The picks below skip the photo-only joints and stick to places that actually deliver. Use this list as a starting point for choosing a $kw and adjust based on whether you're here for a quick lunch, a long catch-up, or a family Sunday.";
    }

    private function externalLinksBlock(string $phrase, string $area, array $facts): string
    {
        $taQ = urlencode($phrase);
        $gQ  = urlencode($phrase);
        $mapsQ = urlencode($facts['maps_q'] ?? ($phrase . ' Philippines'));
        $zomatoQ = urlencode(($facts['display'] ?? '') . ' restaurants');

        return '<div class="mt-10 p-5 bg-slate-50 rounded-xl border border-slate-200 not-prose">'
             . '<p class="text-sm font-semibold text-slate-700 mb-3">Compare picks for ' . e($area) . ' on third-party guides:</p>'
             . '<div class="flex flex-wrap gap-2">'
             . '<a href="https://www.tripadvisor.com.ph/Search?q=' . $taQ . '" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-800">TripAdvisor</a>'
             . '<a href="https://www.google.com/maps/search/?api=1&query=' . $mapsQ . '" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-800">Google Maps</a>'
             . '<a href="https://www.google.com/search?q=' . $gQ . '" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">Google</a>'
             . '<a href="https://www.zomato.com/philippines/search?q=' . $zomatoQ . '" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-rose-50 hover:border-rose-300 hover:text-rose-800">Zomato</a>'
             . '</div>'
             . '<p class="text-xs text-slate-500 mt-3">External links open in a new tab. We do not get paid for clicks.</p>'
             . '</div>';
    }

    /** Lookup facts by normalized key; falls back to a smart generic. */
    private function locationFacts(string $key, string $rawLoc): array
    {
        $bank = $this->factsBank();
        if (isset($bank[$key])) return $bank[$key];

        // Generic auto-template — uses the raw location label so each page
        // still references the actual area name (not "this area" placeholder).
        $area = Str::title(str_replace('-', ' ', $rawLoc));
        return [
            'display'     => $area,
            'image_queries' => [$area . ' Philippines', $area . ' street', $area],
            'maps_q'      => 'restaurants ' . $rawLoc . ' Philippines',
            'intro_one'   => "$area is one of those food corners where the printed lists shuffle every six months but the regulars keep returning to the same handful of kitchens. The picks worth queuing for are usually the ones you can't see from the main road.",
            'intro_two'   => "Most visitors to $area only see the strip facing the heaviest foot traffic and miss the smaller restaurants one street over.",
            'where_to_eat'=> "The shortest answer for $area is to walk the side streets before the main strip. The places facing the heaviest pedestrian flow usually charge more for less attentive service. Family-run restaurants run by the second generation tend to outperform the new arrivals on consistency.",
            'cuisines'    => "Filipino comfort food leads in $area. Korean and Japanese cover the office and student lunch crowd. Coffee and dessert places handle the in-between hours. Specialty places (Italian, Mediterranean, fine dining) cluster wherever rent allows.",
            'budget'      => "Plan 400 to 800 pesos per person for a sit-down meal at the mid-range spots in $area. Walk-up plates stay under 300 pesos. Premium dinners run 1,200 to 2,000 pesos per person before drinks.",
            'order'       => "At family-run Filipino restaurants in $area, the daily specials beat the printed menu on value. At Korean and Japanese spots, the weekday lunch promo drops the cost 25 to 35 percent. Coffee places earn their reputation on a single dish, so order that one and skip the long sides menu.",
            'timing'      => "Avoid the 12 to 2 PM weekday lunch crush in $area. The 3 to 5 PM window is the easiest for walk-ins. Weekend dinners after 8 PM are calmer than the 6 to 8 PM rush.",
            'faq_one'     => "Start with the family-run restaurant that has been on the strip the longest in $area. Longevity here usually means consistency, and you can recalibrate every other meal against it.",
            'faq_two'     => "Roughly 400 to 800 pesos per person at the mid-range sit-down spots in $area. Walk-up or fast-casual stays under 300 pesos. Premium tasting menus reach 1,200 to 2,000 pesos per person.",
            'faq_three'   => "Reservations matter only on Friday and Saturday nights after 7 PM at the upper-tier restaurants. Other times walk-ins work fine across $area.",
            'faq_four'    => "Walk the side streets, not the main pedestrian flow. The kitchens away from the highest foot traffic deliver better food at lower prices because rent is lower and the kitchen has more room to focus.",
        ];
    }

    private function factsBank(): array
    {
        return [
            'moa' => [
                'display' => 'Mall of Asia',
                'image_queries' => ['SM Mall of Asia globe', 'Mall of Asia Pasay', 'SM Mall of Asia complex'],
                'maps_q' => 'restaurants SM Mall of Asia Pasay',
                'intro_one' => "Mall of Asia covers enough ground that picking the right wing matters more than picking the right cuisine. The Seaside extension is the date-night side, the main mall is the family-Sunday side, and the SM by the Bay strip is the budget option. Visitors who only see one wing usually miss the better seating across the bridge.",
                'intro_two' => "Most weekend visitors at Mall of Asia only walk the top floor of the main building and miss the cleaner crowd at the Seaside row. The water-facing tables fill up fast at sunset.",
                'where_to_eat' => "Two clusters work for most groups at Mall of Asia: the SM Seaside row for Japanese, Korean, and steak, and the second floor of the main mall for Filipino chains and fast-casual. The food court on the ground floor is the budget pick.",
                'cuisines' => "Japanese and Korean dominate Mall of Asia because seating turns over fast. Filipino restaurants are mostly chains here, so for serious Filipino food head to the surrounding district. Steakhouses cluster on the third floor of the Seaside.",
                'budget' => "Plan for 500 to 800 pesos per person at a mid-range chain at Mall of Asia. Premium places run 1,200 to 2,000 pesos per person before drinks. The food court averages 200 to 300 pesos per meal.",
                'order' => "For Japanese, share a maki platter rather than ordering individually. For Korean BBQ, the lunch sets between 11 AM and 2 PM are 30 to 40 percent cheaper than dinner. Filipino chains at Mall of Asia deliver on classic comfort plates more than on adventurous orders.",
                'timing' => "Avoid 12 to 2 PM on weekends at Mall of Asia because mall traffic peaks. The 3 to 5 PM window is the easiest for walk-ins. Dinner reservations help on Fridays and Saturdays after 7 PM.",
                'faq_one' => "Start with whatever has the shortest queue at Mall of Asia at the time you arrive. The quality difference between popular chains here is small. The bigger gain is sitting down quickly.",
                'faq_two' => "Roughly 500 to 800 pesos per person for a sit-down meal at a mid-range chain at Mall of Asia. Family combos in the food court can keep a party of four under 1,500 pesos total.",
                'faq_three' => "Reservations matter only on Friday and Saturday nights after 7 PM, and only at the upper-tier restaurants in the Seaside wing of Mall of Asia.",
                'faq_four' => "Cross to the SM by the Bay strip if the main Mall of Asia queues are long. The food is similar and the queues are usually half as long.",
            ],
            'bgc' => [
                'display' => 'BGC',
                'image_queries' => ['Bonifacio Global City High Street', 'BGC Taguig', 'Bonifacio Global City skyline'],
                'maps_q' => 'restaurants Bonifacio Global City Taguig',
                'intro_one' => "BGC eats fall into three groups: the Bonifacio High Street strip aimed at after-work crowds, the Uptown Mall food hall serving the office lunch rush, and the small Burgos Circle ring of patio restaurants for slower meals. The walk between the three is about 10 minutes on foot.",
                'intro_two' => "The compact BGC layout makes multi-stop dinners feasible without needing transport between corners.",
                'where_to_eat' => "For groups in BGC, start at Bonifacio High Street and walk south. The corner between the Mind Museum and Forbes Town has the highest concentration of patio seating. Burgos Circle is the slower-paced ring for longer dinners.",
                'cuisines' => "Japanese is the strongest category in BGC. Specialty coffee is the second-strongest. Korean and Spanish cluster around Burgos Circle. Filipino restaurants in BGC lean modern interpretations rather than classic comfort.",
                'budget' => "Lunch sets in BGC typically run 450 to 700 pesos at the mid-tier places. Dinner expect 800 to 1,400 pesos per person. Drinks at the patio bars add 300 to 500 pesos quickly.",
                'order' => "Specialty ramen sets are the strongest value picks in BGC. The izakaya plates designed for sharing average 280 to 450 pesos and the kitchens deliver them quickly. Skip the western chains, BGC has better non-chain alternatives at the same price.",
                'timing' => "BGC office lunch peaks 12 to 1 PM Monday through Friday so plan around it. Weekend dinners after 8 PM are calmer than the 6 to 8 PM rush.",
                'faq_one' => "A ramen bowl at one of the specialty shops on Bonifacio High Street. It is the most consistently good answer for BGC first-timers.",
                'faq_two' => "Around 600 to 1,200 pesos per person for a proper sit-down meal in BGC. Walking food at the night market behind High Street stays under 300 pesos per person.",
                'faq_three' => "Reservations help for Friday and Saturday night patios at Burgos Circle, less needed at the BGC food hall counters.",
                'faq_four' => "Take the pedestrian walk between High Street and Uptown BGC. The smaller restaurants between the two get fewer walk-ins so the food comes out faster.",
            ],
            'megamall' => [
                'display' => 'SM Megamall',
                'image_queries' => ['SM Megamall Mandaluyong', 'SM Megamall building', 'Megamall EDSA'],
                'maps_q' => 'restaurants SM Megamall Mandaluyong',
                'intro_one' => "SM Megamall has two atriums and three food clusters. Building A holds the family chain restaurants, Building B has the Asian food halls, and the Mega Fashion Hall connecting them is where the newer concepts open first. The walk end to end takes 12 minutes if you avoid the escalator queues.",
                'intro_two' => "Megamall traffic at peak weekends can push food-court queues past 30 minutes, so pick your wing before you commit to a restaurant.",
                'where_to_eat' => "For Japanese head to Megamall Building B fifth floor. For Korean BBQ stay in the Mega Fashion Hall. For Filipino chains the second floor of Building A is the safe pick.",
                'cuisines' => "Korean BBQ leads at Megamall on visitor count. Japanese ramen and izakaya hold the second tier. Filipino comfort chains dominate Building A. International chains scatter throughout.",
                'budget' => "Korean BBQ at Megamall averages 550 to 900 pesos per person for lunch unli-sets. Japanese ramen 400 to 600 pesos. Filipino comfort chains 300 to 500 pesos.",
                'order' => "At Megamall Korean BBQ chains, the weekday lunch unli-set is the strongest value. At ramen shops, order the spicy variants because the kitchens here calibrate spice well.",
                'timing' => "Avoid Megamall on Sunday 1 to 3 PM. The lines at Korean BBQ pass 30 minutes consistently then. Weekday lunch and weekend dinner before 6 PM are easier.",
                'faq_one' => "A Korean BBQ unli-set on a Megamall weekday lunch. Best value-per-peso in the building.",
                'faq_two' => "400 to 800 pesos per person at Megamall's mid-tier sit-down spots.",
                'faq_three' => "Megamall is walk-in friendly except for weekend dinner peak hours at the most popular Korean and Japanese chains.",
                'faq_four' => "Mega Fashion Hall opens newer concepts first. Check there for places that haven't yet been written up.",
            ],
            'tagaytay' => [
                'display' => 'Tagaytay',
                'image_queries' => ['Tagaytay Taal volcano', 'Tagaytay ridge view', 'Tagaytay City'],
                'maps_q' => 'restaurants Tagaytay Cavite',
                'intro_one' => "Tagaytay eating splits into two scenes. The Mahogany Market eateries serve the morning bulalo crowd starting at 5 AM. The ridge restaurants along Aguinaldo Highway open from breakfast through late dinner with the lake view. A Sunday Tagaytay trip without a meal stop feels unfinished.",
                'intro_two' => "The cooler air alone changes how appetite reads on Tagaytay bulalo and grilled tawilis.",
                'where_to_eat' => "For Tagaytay bulalo at sunrise, Mahogany Market. For long lunches with the view, the Aguinaldo Highway strip between Picnic Grove and Sky Ranch. For dinner, the ridge restaurants on the Twin Lakes side.",
                'cuisines' => "Filipino comfort food leads in Tagaytay. Beef bulalo from Taal cattle is the signature plate. Tawilis fried or sinigang version is the second signature. Coffee culture has grown around Antonio's and Bag of Beans.",
                'budget' => "A proper Tagaytay bulalo for two runs 600 to 900 pesos at Mahogany. Sit-down meals at the highway restaurants average 500 to 900 pesos per person. Antonio's is the upper tier at 1,500 to 2,500 pesos per person.",
                'order' => "Bulalo with extra bone marrow at Mahogany. At Bag of Beans Tagaytay, the breakfast tapa plate. At Antonio's, the tasting menu rather than ordering individually.",
                'timing' => "Tagaytay morning fog clears by 8 AM most days. For bulalo go before 8 AM when the broth has been simmering all night. Sunday afternoon traffic on Sumulong Highway gets heavy by 3 PM.",
                'faq_one' => "Bulalo at Mahogany Market in Tagaytay. It is the answer locals will give before they finish hearing the question.",
                'faq_two' => "600 to 1,000 pesos per person at Tagaytay mid-range. Antonio's reaches 2,500 pesos per person.",
                'faq_three' => "Antonio's requires reservations weeks ahead. Mahogany Market and the Tagaytay highway strip take walk-ins.",
                'faq_four' => "Avoid the noon to 2 PM window on weekends in Tagaytay. The view tables fill up by 11:30 AM.",
            ],
            'baguio' => [
                'display' => 'Baguio',
                'image_queries' => ['Baguio Session Road', 'Baguio Burnham Park', 'Baguio City'],
                'maps_q' => 'restaurants Baguio City',
                'intro_one' => "Baguio dining holds onto Cordillera ingredients better than any Luzon city. The strong wood-fire grilling tradition shows up at Cafe by the Ruins and Hill Station, and the Session Road belt mixes long-running cafes with newer experimental kitchens. Cold mornings change which foods taste right.",
                'intro_two' => "The same coffee in Manila does not work the same way at 1,500 meters above sea level. Plan your Baguio appetite around the temperature.",
                'where_to_eat' => "Session Road in Baguio handles breakfast and afternoon coffee. Camp John Hay and the Country Club road hold the destination dinner spots. The wet market lunch at the new Public Market is the budget pick.",
                'cuisines' => "Cordillera ingredients (etag, pinikpikan, native pork) lead Baguio heritage menus. Specialty coffee scene rivals Makati. Korean restaurants serve the student crowd at affordable prices.",
                'budget' => "Baguio cafe meals run 250 to 450 pesos. Heritage Cordillera dinners 600 to 1,200 pesos. Hill Station and similar tier 1,000 to 1,800 pesos per person.",
                'order' => "At Cafe by the Ruins, the lengua plate and the strawberry shortcake. At Hill Station, the lamb shank and the rice plates. The Baguio Korean lunch combos near Burnham average 350 pesos.",
                'timing' => "November to February the Baguio temperature drops below 12 degrees overnight. Reservations matter at the heritage restaurants on these months because tourism peaks.",
                'faq_one' => "Pinikpikan or etag rice at Cafe by the Ruins. It is the most-Baguio plate on a single menu.",
                'faq_two' => "500 to 1,200 pesos per person sit-down in Baguio. Cafe-only stops 200 to 400 pesos.",
                'faq_three' => "Yes for Hill Station, Cafe by the Ruins, and Le Chef on Baguio weekends. Walk-ins work for Session Road cafes.",
                'faq_four' => "Try Baguio lunch hours rather than dinner. The same restaurants serve the same menu at lower noise levels.",
            ],
            'cebu' => [
                'display' => 'Cebu',
                'image_queries' => ["Magellan's Cross Cebu", 'Cebu City skyline', 'Cebu IT Park'],
                'maps_q' => 'restaurants Cebu City',
                'intro_one' => "Cebu food culture starts and ends with lechon. The skin from a proper Cebu lechon needs no sauce. From there, the spread expands to puso rice, fresh seafood at Larsian and Sutukil markets, and the heavier seafood-rice plates that draw weekend visitors.",
                'intro_two' => "Cebu splits clearly between Lapu-Lapu (Mactan) for seafood and resorts, and Cebu City proper for lechon, market eating, and modern restaurants.",
                'where_to_eat' => "For Cebu lechon: Zubuchon, Rico's, House of Lechon. For seafood market style: Sutukil at Mactan or Choobi Choobi. For modern Filipino: Top of Cebu, Lantaw, Anzani.",
                'cuisines' => "Cebu lechon stands alone. Seafood market style (sutukil = sugba, tula, kilaw) holds the second slot. Japanese and Korean have grown around IT Park serving the BPO crowd.",
                'budget' => "Cebu lechon by the kilo runs 700 to 1,000 pesos. A market seafood spread feeds four for 1,500 to 2,500 pesos. Restaurant sit-down 500 to 1,000 pesos per person.",
                'order' => "Cebu lechon belly with extra skin from Zubuchon. Sutukil at the Lapu-Lapu pier picking the fish yourself. Larsian barbecue plates with puso rice and ihaw-ihaw on the side.",
                'timing' => "Larsian peaks 7 to 10 PM. Cebu lechon shops sell out by mid-afternoon at Zubuchon airport branch on weekends. Plan accordingly.",
                'faq_one' => "Cebu lechon belly with the skin still crackling. There is no better introduction to Cebu food.",
                'faq_two' => "500 to 1,000 pesos per person sit-down in Cebu. Lechon-only buys 700 to 1,000 pesos per kilo.",
                'faq_three' => "Cebu reservations help for Top of Cebu and Lantaw on weekend dinners with the city view.",
                'faq_four' => "Cross to Mactan for seafood and stay in Cebu City for lechon. Splitting these meals across days makes the trip work.",
            ],
            'vigan' => [
                'display' => 'Vigan',
                'image_queries' => ['Calle Crisologo Vigan', 'Vigan heritage city', 'Vigan Ilocos Sur'],
                'maps_q' => 'restaurants Vigan Ilocos Sur',
                'intro_one' => "Vigan eating revolves around three plates: empanada, longganisa, and bagnet. The Plaza Burgos empanada vendors are the easiest first stop, and the Vigan longganisa from the public market is the souvenir most visitors carry home.",
                'intro_two' => "Most of the Vigan heritage restaurants sit within the Calle Crisologo three-block walk so a full food trail covers in one afternoon.",
                'where_to_eat' => "Plaza Burgos in Vigan for empanada at sunset. Cafe Leona and Cafe Adriana for sit-down meals on Crisologo. Hidden Garden for a quieter long lunch outside the main strip.",
                'cuisines' => "Ilocano cuisine leads in Vigan. Empanada (orange-tinted, stuffed with longganisa and egg), bagnet (deep-fried pork belly), and the inabraw vegetable plates anchor the offerings.",
                'budget' => "40 to 80 pesos per Vigan empanada. Sit-down meals 350 to 700 pesos per person. Bagnet plates 250 to 400 pesos.",
                'order' => "Empanada with extra longganisa at the Plaza Burgos stalls in Vigan. Bagnet with KBL (kamatis-bagoong-lasona) on the side. Pinakbet or dinengdeng for vegetables.",
                'timing' => "Plaza Burgos empanada peaks 5 to 8 PM. Vigan sit-down restaurants on Crisologo handle dinner from 6 PM, and the kalesa traffic outside slows by 9 PM.",
                'faq_one' => "Empanada from Plaza Burgos in Vigan at sunset. The combination of vinegar dip and the orange shell is the first taste people associate with the heritage city.",
                'faq_two' => "300 to 600 pesos per person for a proper Ilocano dinner in Vigan. Empanada-only walks under 200 pesos.",
                'faq_three' => "Cafe Leona fills on holidays so reserve. Walk-ins work most Vigan weekdays.",
                'faq_four' => "Buy Vigan longganisa from the public market on your last morning. Vacuum-pack it for the trip home.",
            ],
            'boracay' => [
                'display' => 'Boracay',
                'image_queries' => ['White Beach Boracay', 'Boracay sunset', 'Boracay Aklan'],
                'maps_q' => 'restaurants Boracay Aklan',
                'intro_one' => "Boracay eating is segregated by station. Station 1 serves the upper-tier resort restaurants. Station 2 is the buffet and chain strip aimed at the largest visitor share. Station 3 holds quieter, often-better small restaurants run by locals.",
                'intro_two' => "D-Mall is the central Boracay food hall and the sunset crowd shifts the dinner rush by station depending on where the wind cuts the haze.",
                'where_to_eat' => "For Boracay seafood at proper prices, the talipapa markets at the back of the island. For full-service dinners, the Station 1 beachfront. For budget walk-up plates, D-Mall and Station 3.",
                'cuisines' => "Boracay seafood leads with grilled fish and tuna belly. Filipino comfort plates at the resort restaurants. International chains for predictable family meals. Italian and Mediterranean concentrate in Station 1.",
                'budget' => "Boracay talipapa cook-your-catch meals 700 to 1,200 pesos per person. Resort restaurant dinners 900 to 1,800 pesos per person. D-Mall fast-casual 300 to 600 pesos.",
                'order' => "Grilled tuna belly with mango salsa at any Boracay beachfront restaurant. Pochero or kare-kare at the Filipino restaurants. Calamansi shake at every meal.",
                'timing' => "Boracay sunset dinners at Station 1 require booking 5 to 6 PM. Talipapa lunches work best 11 AM to 1 PM before the heat crests.",
                'faq_one' => "Grilled tuna belly at a Station 1 Boracay beachfront table at sunset. The single most repeated meal among returning visitors.",
                'faq_two' => "800 to 1,500 pesos per person at most Boracay sit-down restaurants. Talipapa cook-it-yourself can run lower for groups.",
                'faq_three' => "For Station 1 Boracay beachfront tables at sunset, yes. For Station 3 small restaurants, walk-ins work fine.",
                'faq_four' => "Walk to the back of Boracay for seafood. The talipapa is 15 minutes from the beach but the cost difference is 40 percent.",
            ],
            'glorietta' => [
                'display' => 'Glorietta',
                'image_queries' => ['Ayala Glorietta Makati', 'Glorietta mall', 'Glorietta Ayala Center'],
                'maps_q' => 'restaurants Glorietta Makati',
                'intro_one' => "Glorietta connects to Greenbelt and the Ayala Triangle underpass, which means a meal at Glorietta is rarely just a meal at Glorietta. The food clusters here serve the BPO and office crowd more than the destination diner.",
                'intro_two' => "The third floor of Glorietta holds the chain restaurants that handle the lunch crush, while the smaller alcoves on the second floor handle quieter sit-down meals.",
                'where_to_eat' => "For Filipino comfort chains in Glorietta, the third-floor strip. For Japanese, the second floor of Glorietta 3 holds the better picks. For coffee and pastry, the basement-level cafes near the carpark exit.",
                'cuisines' => "Filipino comfort chains lead at Glorietta. Japanese ramen and izakaya cover the second tier. Korean BBQ is light here compared to Megamall. Coffee culture is strong because of the office crowd.",
                'budget' => "Glorietta meals run 350 to 700 pesos per person at the mid-range chains. Premium dinners reach 1,200 to 1,800 pesos per person. Food court at Glorietta averages 200 to 280 pesos.",
                'order' => "At Glorietta Filipino chains, the family combos beat ordering individually. Japanese set lunches drop the cost 30 percent against dinner ordering.",
                'timing' => "Glorietta office lunch peaks 12 to 1:30 PM. After-work crush hits 6 to 7:30 PM. Weekend lunch is easier than weekday lunch.",
                'faq_one' => "A Filipino comfort chain on the Glorietta third floor for first-timers. Predictable quality at predictable speed.",
                'faq_two' => "400 to 800 pesos per person mid-range at Glorietta. Food court stays under 300 pesos.",
                'faq_three' => "Walk-ins work at Glorietta except for premium dinners on Friday and Saturday nights.",
                'faq_four' => "Cross to Greenbelt for slower meals. The Glorietta side is built for office turnover.",
            ],
            'greenbelt' => [
                'display' => 'Greenbelt',
                'image_queries' => ['Ayala Greenbelt Makati', 'Greenbelt 3 Makati', 'Greenbelt chapel'],
                'maps_q' => 'restaurants Greenbelt Makati',
                'intro_one' => "Greenbelt is the slower-paced sibling to Glorietta. The garden-facing patios at Greenbelt 3 and 5 hold the dinner crowd and the chapel-side restaurants serve the after-mass Sunday lunch.",
                'intro_two' => "The Greenbelt food scene rewards walk-throughs because the same building can hold three very different price tiers.",
                'where_to_eat' => "Greenbelt 3 holds the upper-tier sit-down restaurants. Greenbelt 5 has the patio bars that handle dinner-into-drinks. Greenbelt 2 covers the cafes and smaller plates.",
                'cuisines' => "Greenbelt holds strong Japanese, Spanish, Italian, and modern Filipino. The premium dining concentration is higher than Glorietta.",
                'budget' => "Greenbelt dinners run 800 to 1,500 pesos per person mid-range. Premium tasting menus 2,000 to 3,500 pesos per person. Cafes and small plates 350 to 600 pesos.",
                'order' => "At Greenbelt Spanish restaurants, the paella for sharing rather than tapas-only. At Japanese, the omakase counter beats the table menu.",
                'timing' => "Greenbelt 5 patios fill 7 to 9 PM. After-work drinks crowd hits 6 to 7 PM. Weekend lunch is the quieter window.",
                'faq_one' => "A garden-facing dinner at a Greenbelt 3 patio restaurant. The setting alone makes it the best first Greenbelt meal.",
                'faq_two' => "800 to 1,500 pesos per person at Greenbelt mid-range, 2,000 to 3,500 at the upper tier.",
                'faq_three' => "Greenbelt reservations matter on Friday and Saturday dinners. Walk-ins work weekday lunches.",
                'faq_four' => "Skip Greenbelt 1, head straight to Greenbelt 3 and 5. The food calibre and the seating both step up.",
            ],
            'podium' => [
                'display' => 'The Podium',
                'image_queries' => ['The Podium Ortigas mall', 'Podium ADB mall', 'Podium mall'],
                'maps_q' => 'restaurants The Podium Ortigas',
                'intro_one' => "The Podium in Ortigas runs leaner than the SM and Ayala mall food halls. The restaurants here cluster around fewer concepts but each one tends to be a single owner rather than a chain.",
                'intro_two' => "The Podium expansion added a Japanese-heavy second wing that drew the foodie crowd away from Megamall across the street.",
                'where_to_eat' => "For Japanese at the Podium, the new wing's upper floor holds the better picks. For Filipino, the original building's second floor has the longer-running concepts.",
                'cuisines' => "Japanese is the strongest category at the Podium. Filipino modern and Western chains hold the rest. Korean and Chinese are lighter than at neighboring malls.",
                'budget' => "Podium meals run 500 to 1,000 pesos per person mid-range. Premium Japanese reaches 1,500 to 2,500 pesos. Coffee and pastry 250 to 400 pesos.",
                'order' => "At Podium Japanese restaurants, the chef's selection beats the printed menu. Filipino concept restaurants serve their best plates as the daily specials.",
                'timing' => "Podium peaks weekday lunch and Saturday dinner. Sundays are noticeably quieter than at Megamall across the street.",
                'faq_one' => "An owner-operated Japanese restaurant on the Podium's new wing. The single-concept depth shows.",
                'faq_two' => "500 to 1,000 pesos per person mid-range at the Podium. Premium dinners 1,500 to 2,500.",
                'faq_three' => "Reservations help at the Podium upper-tier Japanese on weekends. Other slots accept walk-ins.",
                'faq_four' => "Cross from the Podium to Megamall for lower prices on the same cuisine. Stay at the Podium for kitchens that take more care with the plate.",
            ],
            'sm_north' => [
                'display' => 'SM North EDSA',
                'image_queries' => ['SM North EDSA Quezon City', 'SM North EDSA mall', 'SM City North'],
                'maps_q' => 'restaurants SM North EDSA Quezon City',
                'intro_one' => "SM North EDSA is the largest SM by visitor count and the food halls show it. The Annex food strip and the Sky Garden levels each serve different crowds, so picking the right level matters more than picking the right restaurant.",
                'intro_two' => "The Sky Garden at SM North runs cooler in the evening which makes the upper-level patios surprisingly comfortable for dinner.",
                'where_to_eat' => "Sky Garden at SM North for the patio dining. The Block side for Korean and Japanese. The main mall's fourth floor for the Filipino chains.",
                'cuisines' => "Korean BBQ leads at SM North on visitor numbers. Filipino comfort chains hold the family Sunday slot. Japanese ramen and Chinese dimsum cluster on the upper levels.",
                'budget' => "SM North meals run 400 to 800 pesos per person at mid-range. Korean BBQ unli-sets 500 to 800 pesos. Food court 220 to 320 pesos.",
                'order' => "At SM North Korean BBQ, the weekday lunch unli-set is the best value. Filipino chains serve their best dishes through the all-day breakfast plates.",
                'timing' => "Avoid SM North Saturday afternoons 2 to 5 PM. The queues at Korean BBQ pass 45 minutes consistently. Weekday late lunch (2 to 4 PM) is the easiest window.",
                'faq_one' => "A Korean BBQ unli-set at the Block side of SM North. Best value and easiest walk-in.",
                'faq_two' => "400 to 800 pesos per person at SM North sit-down restaurants. Food court stays under 320 pesos.",
                'faq_three' => "SM North reservations matter only on weekend dinners at the upper-tier restaurants in Sky Garden.",
                'faq_four' => "Use the Sky Garden levels for dinner. The patio dining is calmer than the main mall.",
            ],
            'festival_mall' => [
                'display' => 'Festival Mall Alabang',
                'image_queries' => ['Festival Mall Alabang', 'Filinvest City Alabang', 'Festival Mall Muntinlupa'],
                'maps_q' => 'restaurants Festival Mall Alabang Muntinlupa',
                'intro_one' => "Festival Mall Alabang is the south-Manila food hub for families. The expansion added a second wing that brought in the BGC-tier restaurants without the BGC parking situation, which makes Festival Mall a south-Manila weekend default.",
                'intro_two' => "Festival Mall traffic flows out to the Filinvest City restaurants in the evening, so booking before 6 PM helps on weekends.",
                'where_to_eat' => "For Festival Mall family dinners, the second-level chain restaurants in the new wing. For Korean BBQ, the food strip on the third floor. For coffee, the Filinvest City corners outside the mall proper.",
                'cuisines' => "Family chain restaurants lead Festival Mall. Korean BBQ second tier. Japanese ramen and Chinese chains fill the gaps. Specialty coffee scattered across Filinvest City outside the mall.",
                'budget' => "Festival Mall meals run 400 to 800 pesos per person at mid-range. Family combos 1,500 to 2,500 pesos for four. Coffee and pastry 250 to 400 pesos.",
                'order' => "At Festival Mall Filipino chains, the family combo beats individual ordering. Korean BBQ unli-sets at lunch are 30 percent cheaper than dinner.",
                'timing' => "Festival Mall peaks 6 to 8 PM weekend dinners. Weekday lunches stay manageable. Late-night dinners after 9 PM clear the queues.",
                'faq_one' => "A Korean BBQ unli-set or a family Filipino combo at Festival Mall. Either choice is the easiest first meal.",
                'faq_two' => "400 to 800 pesos per person at Festival Mall sit-down restaurants.",
                'faq_three' => "Festival Mall walk-ins work outside weekend dinner peak. Reservations help 6 to 8 PM on Saturdays.",
                'faq_four' => "Step outside Festival Mall into Filinvest City for the calmer dinner restaurants. Same walking distance, less mall noise.",
            ],
            'sm_aura' => [
                'display' => 'SM Aura',
                'image_queries' => ['SM Aura Premier Taguig', 'SM Aura BGC', 'SM Aura Tower'],
                'maps_q' => 'restaurants SM Aura Premier Taguig',
                'intro_one' => "SM Aura Premier in BGC sits one MRT stop away from High Street and runs at the same upper-mid price tier. The Sky Park at the top floor handles the dinner crowd and the lower floors hold the office lunch traffic.",
                'intro_two' => "SM Aura's smaller footprint makes it faster to walk end-to-end than Megamall, which helps when you're picking between three restaurants.",
                'where_to_eat' => "Sky Park at SM Aura for dinner with view. The fourth floor for Korean BBQ and Japanese. The second floor for Filipino chains and fast-casual.",
                'cuisines' => "Japanese and Korean lead at SM Aura. Filipino modern restaurants hold the second tier. Italian and Mediterranean cluster on the Sky Park level.",
                'budget' => "SM Aura meals run 500 to 1,000 pesos per person mid-range. Sky Park dinners 1,200 to 2,000 pesos. Food court 280 to 380 pesos.",
                'order' => "At SM Aura Japanese, share a maki platter and a sashimi plate rather than ordering individually. Sky Park restaurants do better with the chef's selection.",
                'timing' => "SM Aura weekend dinner peaks 7 to 9 PM. Weekday lunch is faster than at neighboring Megamall.",
                'faq_one' => "A Sky Park dinner at SM Aura for the view. The food matches the BGC tier without the BGC parking situation.",
                'faq_two' => "500 to 1,000 pesos per person mid-range at SM Aura. Sky Park 1,200 to 2,000 pesos.",
                'faq_three' => "SM Aura reservations help at Sky Park on weekend dinners. Other floors accept walk-ins.",
                'faq_four' => "Use SM Aura instead of High Street when High Street parking is full. The walk between the two is 8 minutes.",
            ],
            'tomas_morato' => [
                'display' => 'Tomas Morato',
                'image_queries' => ['Tomas Morato Quezon City', 'Tomas Morato avenue', 'Tomas Morato food strip'],
                'maps_q' => 'restaurants Tomas Morato Quezon City',
                'intro_one' => "Tomas Morato in Quezon City is the longest-running food strip in the city. Restaurants here have lasted three decades because the rent allows the kitchen to focus on the food rather than the interior design.",
                'intro_two' => "Tomas Morato dining is heavier on weekday late-night and weekend brunch than on weekend dinner.",
                'where_to_eat' => "For Tomas Morato seafood and Filipino sit-down, the corners around Sct. Borromeo. For coffee and brunch, the smaller cafes one block off the main avenue. For dessert, the strip near the Sct. Tobias crossing.",
                'cuisines' => "Filipino comfort food leads Tomas Morato. Seafood second tier. Japanese ramen has grown the last five years. Coffee culture spans the side streets.",
                'budget' => "Tomas Morato meals run 350 to 700 pesos per person at mid-range. Seafood family meals 1,500 to 2,500 pesos for four. Cafe stops 200 to 350 pesos.",
                'order' => "At Tomas Morato Filipino restaurants, the long-running specials beat the new menu items. Seafood places serve their best plates as the daily catch.",
                'timing' => "Tomas Morato peaks Friday and Saturday late nights (10 PM onwards). Weekday dinners are calmer. Weekend brunch (10 AM to noon) is the strongest non-dinner window.",
                'faq_one' => "A long-running Filipino restaurant on Tomas Morato. The thirty-year-old kitchens hold the consistency.",
                'faq_two' => "400 to 700 pesos per person at Tomas Morato mid-range. Cafe stops 200 to 350 pesos.",
                'faq_three' => "Tomas Morato walk-ins work most slots. Weekend late nights at the popular spots can hit 30-minute queues.",
                'faq_four' => "Walk Tomas Morato during the day before committing. The strip lights differently at night and the picks change.",
            ],
            'trinoma' => [
                'display' => 'TriNoma',
                'image_queries' => ['TriNoma North Avenue', 'TriNoma mall Quezon City', 'Ayala TriNoma'],
                'maps_q' => 'restaurants TriNoma Quezon City',
                'intro_one' => "TriNoma at North Avenue connects to the MRT North station and serves the dense weekday office crowd at lunch. The food clusters at TriNoma split between the main mall food halls and the open-air Garden area near the carpark.",
                'intro_two' => "The TriNoma weekend traffic peaks differently than the weekday office lunch, so the same restaurant runs at different waits depending on the day.",
                'where_to_eat' => "TriNoma's third floor for Korean BBQ. Second floor for Japanese and Chinese chains. Garden level for the patio restaurants with quieter dinner crowds.",
                'cuisines' => "Korean BBQ leads at TriNoma. Filipino comfort chains for family Sundays. Japanese ramen and izakaya in the food halls.",
                'budget' => "TriNoma meals run 400 to 800 pesos per person mid-range. Korean BBQ unli-sets 500 to 800 pesos. Food court 220 to 320 pesos.",
                'order' => "At TriNoma Korean BBQ, weekday lunch unli-sets are the strongest value. Garden-level restaurants do better with their cocktails than at the chains nearby.",
                'timing' => "TriNoma weekday lunch peaks 12 to 1 PM. Weekend dinner 6 to 8 PM. The Garden level runs calmer than the main mall food halls.",
                'faq_one' => "A Korean BBQ weekday lunch unli-set at TriNoma. Easiest walk-in, strongest value.",
                'faq_two' => "400 to 800 pesos per person at TriNoma sit-down. Food court under 320 pesos.",
                'faq_three' => "TriNoma reservations help only at the Garden-level restaurants on weekend dinners.",
                'faq_four' => "Use the Garden level for slower dinners. The main mall food halls turn the table faster than you want.",
            ],
            'uptown' => [
                'display' => 'Uptown Mall BGC',
                'image_queries' => ['Uptown Mall BGC Taguig', 'Uptown BGC', 'Uptown Bonifacio'],
                'maps_q' => 'restaurants Uptown Mall BGC Taguig',
                'intro_one' => "Uptown Mall in BGC sits at the quieter end of the BGC walking circuit. The food hall here runs lighter on the office lunch rush than High Street's offerings and the upper-floor restaurants hold steadier weekend dinners.",
                'intro_two' => "Uptown Mall's location pulls a different crowd than BGC High Street, which means the same chain restaurants at Uptown run shorter queues.",
                'where_to_eat' => "Uptown Mall's third floor food hall for office lunch picks. The patio side for slower dinners. The basement for cafes and small plates.",
                'cuisines' => "Specialty Japanese and Korean lead at Uptown Mall. Filipino modern restaurants and Italian fill the second tier. Coffee culture is strong with several specialty operators.",
                'budget' => "Uptown Mall meals run 500 to 1,000 pesos per person mid-range. Premium dinners 1,500 to 2,500 pesos. Cafe stops 280 to 450 pesos.",
                'order' => "At Uptown Mall Japanese specialty shops, the chef's omakase delivers better than the printed menu. Filipino modern restaurants serve strongest at the prix-fixe.",
                'timing' => "Uptown Mall lunch peaks 12 to 1:30 PM. Weekend dinner 7 to 9 PM. The basement cafes run calmer hours.",
                'faq_one' => "A specialty Japanese omakase at Uptown Mall. The BGC kitchens take more care with the plate than at the larger malls.",
                'faq_two' => "500 to 1,000 pesos per person at Uptown Mall mid-range. Premium 1,500 to 2,500.",
                'faq_three' => "Uptown Mall walk-ins work most slots except weekend dinners at the premium tier.",
                'faq_four' => "Walk between Uptown Mall and BGC High Street. The smaller restaurants between them get fewer walk-ins so the food comes out faster.",
            ],
            'eastwood' => [
                'display' => 'Eastwood City',
                'image_queries' => ['Eastwood City Libis', 'Eastwood Mall Quezon City', 'Eastwood Libis'],
                'maps_q' => 'restaurants Eastwood City Libis Quezon City',
                'intro_one' => "Eastwood City in Libis runs on BPO and entertainment rhythm. The food strip here stays open later than most QC corners, which makes Eastwood the default for late-night meals after the malls have closed.",
                'intro_two' => "Eastwood Mall's central plaza handles the after-work crowd while the smaller restaurants on the side streets handle weekend slow meals.",
                'where_to_eat' => "Eastwood Mall's second floor for Korean BBQ. The plaza-facing restaurants for after-work dinners. The Cyber Mall side for quick lunches.",
                'cuisines' => "Korean BBQ and Japanese lead at Eastwood. Filipino comfort chains second tier. Late-night Filipino sisig spots are the Eastwood signature.",
                'budget' => "Eastwood meals run 350 to 700 pesos per person at mid-range. Late-night sisig and grill plates 280 to 500 pesos. Cafe stops 200 to 350 pesos.",
                'order' => "At Eastwood late-night Filipino grills, the sisig with rice and beer. Korean BBQ chains run weekday lunch unli at the strongest value.",
                'timing' => "Eastwood peaks 9 PM onwards on Fridays and Saturdays. Weekday lunch (12 to 1 PM) hits the office crowd. The 3 to 5 PM window is the easiest walk-in slot.",
                'faq_one' => "A late-night sisig plate at Eastwood. The Libis BPO crowd has kept the late grills consistent.",
                'faq_two' => "350 to 700 pesos per person at Eastwood mid-range. Late-night grills 280 to 500.",
                'faq_three' => "Eastwood walk-ins work most slots. Weekend late nights at the most popular grills can hit 20-minute queues.",
                'faq_four' => "Use Eastwood for late-night meals when other QC corners have closed. The strip runs past midnight on weekends.",
            ],
            'rockwell' => [
                'display' => 'Rockwell',
                'image_queries' => ['Rockwell Powerplant Makati', 'Rockwell Center Makati', 'Powerplant Mall Rockwell'],
                'maps_q' => 'restaurants Rockwell Center Makati',
                'intro_one' => "Rockwell Center in Makati holds the Powerplant Mall on one side and the high-rise residential corner on the other. The food here runs upper-mid tier with single-concept restaurants outweighing the chain footprint.",
                'intro_two' => "Powerplant Mall's compact layout makes Rockwell faster to navigate than the bigger Ayala malls and the food picks are more curated.",
                'where_to_eat' => "Powerplant Mall's third floor at Rockwell for the upper-tier sit-down. Ground floor for casual lunch. The Rockwell Center plaza for the patio dining.",
                'cuisines' => "Italian and Modern Filipino lead at Rockwell. Japanese specialty operators second tier. Specialty coffee strong across the complex.",
                'budget' => "Rockwell meals run 700 to 1,400 pesos per person at mid-range. Upper-tier dinners 1,800 to 3,000 pesos. Cafe stops 350 to 550 pesos.",
                'order' => "At Rockwell Italian restaurants, the daily pasta beats the printed menu. Modern Filipino concepts deliver strongest at the tasting menu.",
                'timing' => "Rockwell weekend dinner peaks 7:30 to 9 PM. Sunday brunch (11 AM to 1 PM) at Powerplant cafes fills fast. Weekday lunch runs calmer.",
                'faq_one' => "A daily pasta at a Rockwell Italian restaurant or the tasting menu at a Modern Filipino concept. Both deliver Rockwell-tier consistency.",
                'faq_two' => "700 to 1,400 pesos per person at Rockwell mid-range. Upper-tier 1,800 to 3,000.",
                'faq_three' => "Rockwell reservations help at the upper-tier restaurants on Friday and Saturday dinners.",
                'faq_four' => "Walk through Powerplant before committing. The smaller restaurants between the chains run shorter waits.",
            ],
            'makati' => [
                'display' => 'Makati',
                'image_queries' => ['Makati CBD skyline', 'Ayala Avenue Makati', 'Salcedo Village Makati'],
                'maps_q' => 'restaurants Makati City',
                'intro_one' => "Makati food splits between the Ayala Center mall corridor (Glorietta and Greenbelt), the Salcedo and Legaspi Villages, and the Poblacion strip. Each runs on a different rhythm and serves a different crowd, so picking the right village matters more than picking the right cuisine.",
                'intro_two' => "Makati restaurant rents run high so the kitchens that survive have to deliver consistently. That filter shows up in the long-running picks.",
                'where_to_eat' => "For Makati office lunch: Salcedo or Legaspi Village. For Makati after-work drinks: Poblacion. For Makati family Sundays: Greenbelt. For Makati late nights: Poblacion or Burgos.",
                'cuisines' => "Japanese is the strongest single category in Makati. Spanish and Italian hold the second tier (Salcedo Village). Filipino modern restaurants lead at Salcedo and Poblacion. Korean BBQ scattered through the malls.",
                'budget' => "Makati mid-range dinners run 700 to 1,400 pesos per person. Upper-tier 1,800 to 3,500 pesos. Office lunches stay 350 to 600 pesos. Poblacion casual 400 to 800 pesos.",
                'order' => "At Makati Japanese restaurants, the omakase counter beats the table menu. At Salcedo Spanish places, the paella for sharing. At Poblacion, follow the locals to whatever single-dish corner has the longest queue.",
                'timing' => "Makati office lunch 12 to 1:30 PM weekdays. After-work drinks 6 to 8 PM. Poblacion peaks 10 PM to midnight on Fridays. Sunday brunch in Salcedo Village 10 AM to 1 PM.",
                'faq_one' => "An omakase counter at a Salcedo Village Japanese restaurant. Makati's strongest single category and the easiest first meal.",
                'faq_two' => "700 to 1,400 pesos per person at Makati mid-range. Upper-tier 1,800 to 3,500.",
                'faq_three' => "Makati reservations matter on Friday and Saturday nights at the upper-tier restaurants and at the popular Poblacion corners.",
                'faq_four' => "Walk Salcedo Village on a weekday lunch to scout. The same restaurants you'll visit at dinner serve their best at lunch.",
            ],
            'greenhills' => [
                'display' => 'Greenhills',
                'image_queries' => ['Greenhills Shopping Center San Juan', 'Greenhills mall', 'San Juan Greenhills'],
                'maps_q' => 'restaurants Greenhills San Juan',
                'intro_one' => "Greenhills in San Juan runs on the Chinese-Filipino crowd that has anchored the area for two generations. The food here leans Chinese and Filipino-Chinese fusion more than any other Metro Manila corner.",
                'intro_two' => "Greenhills food picks reward repeat visits because the daily specials at the long-running restaurants change with what the chef found at the morning market.",
                'where_to_eat' => "For Greenhills Chinese hotpot and dimsum, the Promenade side. For Filipino comfort, the Theater Mall corridor. For Korean BBQ, the food strip outside the main shopping center.",
                'cuisines' => "Chinese leads at Greenhills (Cantonese, Hong Kong style, hotpot). Filipino-Chinese fusion holds the second tier. Korean BBQ and Japanese ramen scattered.",
                'budget' => "Greenhills meals run 400 to 900 pesos per person at mid-range. Chinese family combos 1,800 to 3,000 pesos for four. Dimsum lunches 300 to 500 pesos per person.",
                'order' => "At Greenhills Chinese restaurants, dimsum carts at the long-running operators. Hotpot for groups at the upper-tier places. Filipino-Chinese fusion serves strongest at the rice toppings.",
                'timing' => "Greenhills Sunday dimsum lunch (11 AM to 1 PM) fills fast. Weekday dinners are calmer. Chinese New Year week the popular spots queue 45 minutes.",
                'faq_one' => "Dimsum at a long-running Greenhills Chinese restaurant. The Sunday cart service is the easiest Greenhills first meal.",
                'faq_two' => "400 to 900 pesos per person at Greenhills mid-range. Family Chinese dinners 1,800 to 3,000 for four.",
                'faq_three' => "Greenhills reservations help on Sunday dimsum lunches and Chinese holiday weeks. Other slots accept walk-ins.",
                'faq_four' => "Cross from the Greenhills shopping center to the strip mall food strip outside. The non-mall restaurants serve better Filipino-Chinese plates.",
            ],
            'qc' => [
                'display' => 'Quezon City',
                'image_queries' => ['Quezon City landmark', 'Quezon Memorial Circle', 'Quezon City skyline'],
                'maps_q' => 'restaurants Quezon City',
                'intro_one' => "Quezon City food sprawls across districts that each run on their own rhythm. Tomas Morato handles the late-night crowd, Maginhawa serves the student belly-busters, Banawe holds the Chinese food, and Katipunan absorbs the university lunch traffic.",
                'intro_two' => "QC dining tends to deliver more food at lower prices than Makati for the same plate. The catch is needing to know which district to go to.",
                'where_to_eat' => "For QC late nights: Tomas Morato. For QC budget meals: Maginhawa or Katipunan. For QC Chinese food: Banawe or Banawe Quezon City. For QC family Sundays: Eastwood or Quezon Memorial circle.",
                'cuisines' => "Filipino comfort food leads across QC. Chinese strong at Banawe. Korean BBQ scattered across malls. Japanese ramen has grown the last five years on Maginhawa and Tomas Morato.",
                'budget' => "QC meals run 300 to 700 pesos per person at mid-range. Maginhawa student-budget plates 150 to 280 pesos. Banawe Chinese family combos 1,500 to 2,500 pesos for four.",
                'order' => "At QC Filipino comfort spots, the daily specials beat the printed menu. At Banawe Chinese, the dimsum at the long-running operators. At Maginhawa, the unli-rice plates at the student-belt restaurants.",
                'timing' => "QC late nights peak Friday and Saturday 10 PM onwards (Tomas Morato, Eastwood). Maginhawa serves through dinner into 11 PM most nights. Banawe weekend lunch fills fast.",
                'faq_one' => "Depends on the QC district. For first-timers, Tomas Morato Filipino for late nights or Maginhawa for student-budget.",
                'faq_two' => "300 to 700 pesos per person at QC mid-range. Student-budget plates 150 to 280 pesos.",
                'faq_three' => "QC walk-ins work most slots. Banawe Sunday dimsum and Maginhawa weekend late nights can hit 20-minute queues.",
                'faq_four' => "Pick the QC district before picking the restaurant. Each runs on a different rhythm and price tier.",
            ],
            'davao' => [
                'display' => 'Davao',
                'image_queries' => ['Davao City landmark', 'Davao downtown', 'Davao Mount Apo'],
                'maps_q' => 'restaurants Davao City',
                'intro_one' => "Davao food revolves around durian, tuna, and the Mindanao seafood that doesn't reach the Manila markets the same way. The city's restaurants split between the downtown corridor and the SM Lanang Premier mall area.",
                'intro_two' => "Davao's weather stays evenly warm year-round which keeps the outdoor patios and grill setups in steady use.",
                'where_to_eat' => "For Davao seafood: the boardwalk-side restaurants near the People's Park. For Davao mall dining: SM Lanang Premier. For Davao late-night grill: the strip near Roxas Avenue.",
                'cuisines' => "Davao seafood leads (tuna belly, lapu-lapu, grilled fish). Filipino comfort second tier. Durian dessert culture is the Davao signature.",
                'budget' => "Davao meals run 350 to 800 pesos per person mid-range. Seafood family meals 1,500 to 2,500 pesos for four. Grilled tuna belly plates 280 to 450 pesos.",
                'order' => "At Davao seafood restaurants, the tuna belly grilled with calamansi. The kinilaw na malasugi for sharing. Durian pastries for dessert at the SM Lanang side.",
                'timing' => "Davao dinners run 6 to 9 PM at the seafood spots. Mall dining at SM Lanang follows the standard Filipino mall rhythm.",
                'faq_one' => "Grilled tuna belly at a Davao boardwalk seafood restaurant. The most-Davao first meal.",
                'faq_two' => "350 to 800 pesos per person at Davao mid-range. Seafood meals 600 to 1,200 pesos.",
                'faq_three' => "Davao walk-ins work most slots. Reservations help only at the upper-tier downtown restaurants on weekend dinners.",
                'faq_four' => "Pair seafood at the boardwalk with durian dessert at SM Lanang. Two stops cover the Davao food signature.",
            ],
            'iloilo' => [
                'display' => 'Iloilo',
                'image_queries' => ['Iloilo City Plaza', 'Iloilo riverside', 'Iloilo Calle Real'],
                'maps_q' => 'restaurants Iloilo City',
                'intro_one' => "Iloilo food culture leans heavily on La Paz batchoy, fresh seafood, and pancit Molo. The city's restaurant scene splits between Old Town heritage spots and the newer Festive Walk corridor.",
                'intro_two' => "Iloilo dinner runs earlier than Manila and the heritage restaurants tend to close by 9 PM.",
                'where_to_eat' => "For Iloilo batchoy: La Paz district at the long-running stalls. For Iloilo seafood: the Tatoy's-style talipapa near the airport. For Iloilo modern Filipino: the Festive Walk corridor.",
                'cuisines' => "Filipino heritage leads in Iloilo (batchoy, pancit Molo, KBL). Seafood holds the second tier. Korean BBQ has grown around Festive Walk.",
                'budget' => "Iloilo meals run 250 to 600 pesos per person at mid-range. Heritage stalls 150 to 280 pesos. Seafood family meals 1,200 to 2,000 pesos for four.",
                'order' => "Iloilo batchoy with all toppings at La Paz. Pancit Molo soup for sharing. KBL (kadyos-baboy-langka) at the heritage Filipino restaurants.",
                'timing' => "Iloilo batchoy stalls run breakfast through afternoon. Heritage dinners close by 9 PM. Festive Walk corridor runs later into the night.",
                'faq_one' => "La Paz batchoy at a long-running Iloilo stall. The single most-Iloilo first meal.",
                'faq_two' => "250 to 600 pesos per person at Iloilo mid-range. Heritage stalls 150 to 280 pesos.",
                'faq_three' => "Iloilo walk-ins work most slots. Reservations help at Festive Walk corridor on weekend dinners.",
                'faq_four' => "Pair La Paz batchoy lunch with seafood dinner at Tatoy's or the airport-side talipapa. Two stops cover Iloilo's signature.",
            ],
            'alabang' => [
                'display' => 'Alabang',
                'image_queries' => ['Alabang Town Center Muntinlupa', 'Filinvest City Alabang', 'Alabang Westgate'],
                'maps_q' => 'restaurants Alabang Muntinlupa',
                'intro_one' => "Alabang food runs across three corners: Alabang Town Center (ATC) for chain restaurants, Westgate for the older sit-down spots, and Molito for the newer concept restaurants. Each draws a different crowd at a different price tier.",
                'intro_two' => "Alabang dining serves south-Manila residents who don't want to drive to BGC. The selection has grown enough that the trip uptown is rarely necessary.",
                'where_to_eat' => "ATC for Alabang chain restaurants and the food court. Westgate for the older Filipino sit-down spots. Molito for the newer specialty concepts.",
                'cuisines' => "Filipino comfort chains lead at ATC. Korean BBQ and Japanese strong at Westgate. Molito holds the upper-tier modern Filipino and Italian concepts.",
                'budget' => "Alabang meals run 400 to 800 pesos per person mid-range. Molito upper-tier 1,000 to 1,800 pesos. ATC food court 200 to 320 pesos.",
                'order' => "At Alabang Filipino chains in ATC, the family combos. At Westgate Korean BBQ, the weekday lunch unli. At Molito concept restaurants, the chef's selection.",
                'timing' => "Alabang weekend dinner 6 to 8 PM peaks. ATC weekday lunch hits the office crowd 12 to 1 PM. Molito runs calmer hours.",
                'faq_one' => "Depends on which Alabang corner. ATC for family chain dinners, Molito for slower modern Filipino.",
                'faq_two' => "400 to 800 pesos per person at Alabang mid-range. Molito 1,000 to 1,800.",
                'faq_three' => "Alabang reservations help at Molito on Friday and Saturday dinners. ATC and Westgate accept walk-ins.",
                'faq_four' => "Skip ATC if you want quieter dinners and head straight to Molito. The driving distance is 5 minutes and the food calibre steps up.",
            ],
            'bf_homes' => [
                'display' => 'BF Homes',
                'image_queries' => ['BF Homes Paranaque', 'BF Homes Aguirre Avenue', 'BF Homes commercial strip'],
                'maps_q' => 'restaurants BF Homes Paranaque',
                'intro_one' => "BF Homes in Paranaque holds Aguirre Avenue, which packs more restaurants per kilometer than most Metro Manila corners. The strip survives on residents from the surrounding subdivisions and stays open later than other south-Manila eat-streets.",
                'intro_two' => "BF Homes dining doesn't draw crowds from outside the area, which keeps the prices reasonable and the kitchens focused on regulars.",
                'where_to_eat' => "Aguirre Avenue in BF Homes for the full strip walk-through. The corners around Tropical Avenue for the newer concept restaurants. The El Grande side for older Filipino sit-downs.",
                'cuisines' => "Filipino comfort food leads BF Homes. Japanese ramen, Korean BBQ, and Italian fill the second tier. Late-night sisig grills are the BF Homes signature.",
                'budget' => "BF Homes meals run 300 to 650 pesos per person at mid-range. Late-night grills 250 to 450 pesos. Cafe stops 200 to 350 pesos.",
                'order' => "At BF Homes Filipino restaurants, the daily specials at the long-running spots. Late-night sisig with rice and beer at the grill corners.",
                'timing' => "BF Homes peaks Friday and Saturday 9 PM onwards. Weekday dinners run calmer. Sunday lunch is the easiest walk-in window.",
                'faq_one' => "A late-night sisig at a BF Homes Aguirre Avenue grill. The BF residents have kept these consistent.",
                'faq_two' => "300 to 650 pesos per person at BF Homes mid-range. Late-night grills 250 to 450.",
                'faq_three' => "BF Homes walk-ins work most slots. Weekend late nights at the popular grills can hit 20-minute queues.",
                'faq_four' => "Walk Aguirre Avenue before committing. The strip changes character every two blocks.",
            ],
            'nuvali' => [
                'display' => 'Nuvali Sta. Rosa',
                'image_queries' => ['Nuvali Sta Rosa Laguna', 'Nuvali lake', 'Nuvali Sta Rosa'],
                'maps_q' => 'restaurants Nuvali Sta Rosa Laguna',
                'intro_one' => "Nuvali at Sta. Rosa sits one Laguna exit south of the Manila boundary and runs on the weekend family crowd. The food strip here mixes mall chain dining with the open-air patio restaurants around the lake.",
                'intro_two' => "Nuvali weekend traffic peaks differently than the mall corridor. Sunday brunch hours fill the lake-facing patios before noon.",
                'where_to_eat' => "Solenad at Nuvali for the mall chain restaurants. The lake-side patio strip for slower meals. The Treveia side for the smaller specialty restaurants.",
                'cuisines' => "Filipino comfort chains lead Nuvali. Korean BBQ and Italian on the second tier. Coffee and brunch concepts strong on the patio side.",
                'budget' => "Nuvali meals run 400 to 800 pesos per person mid-range. Patio brunches 350 to 600 pesos. Premium dinners 1,000 to 1,800 pesos.",
                'order' => "At Nuvali Solenad chains, the family combos beat individual ordering. Patio restaurants serve their best plates as the Sunday brunch specials.",
                'timing' => "Nuvali Sunday brunch (10 AM to 1 PM) fills fast. Weekend dinners 7 to 9 PM. Weekday slots run calmer.",
                'faq_one' => "A Sunday brunch at a Nuvali lake-side patio restaurant. The setting alone makes it the first meal worth the trip.",
                'faq_two' => "400 to 800 pesos per person at Nuvali mid-range. Brunch and patio meals 350 to 600.",
                'faq_three' => "Nuvali reservations matter on Sunday brunch and Saturday dinner. Other slots accept walk-ins.",
                'faq_four' => "Drive past Solenad to the lake-side patios. The walk is 8 minutes and the air runs cooler.",
            ],
            'el_nido' => [
                'display' => 'El Nido',
                'image_queries' => ['El Nido Palawan Big Lagoon', 'El Nido Bacuit Bay', 'El Nido Palawan'],
                'maps_q' => 'restaurants El Nido Palawan',
                'intro_one' => "El Nido restaurants spread along the small town strip facing the bay and the higher-end resorts on the Lio side. The town strip runs casual and turns to budget seafood, while Lio holds the upper-tier dining.",
                'intro_two' => "El Nido prices run 30 to 50 percent higher than Manila for the same plate because everything has to be brought in by van.",
                'where_to_eat' => "El Nido town strip for casual seafood and Filipino comfort. Lio Beach side for the upper-tier resort restaurants. Las Cabanas for sunset patio dining.",
                'cuisines' => "Filipino seafood leads El Nido. Italian and pizza concepts strong on the town strip. Resort fine-dining at Lio.",
                'budget' => "El Nido casual meals run 400 to 800 pesos per person. Resort dinners 1,500 to 3,000 pesos per person. Sunset cocktails 350 to 600 pesos.",
                'order' => "At El Nido town strip, the grilled fish with mango salsa. Pizza at the Italian-run spots. Resort dining serves strongest at the tasting menu.",
                'timing' => "El Nido sunset dinners 6 to 8 PM fill fast. Town strip serves through 10 PM. Resort dining requires advance booking in peak season.",
                'faq_one' => "Grilled fresh fish at an El Nido town strip seafood restaurant. The first meal that anchors the trip.",
                'faq_two' => "400 to 800 pesos per person at El Nido casual. Resort dining 1,500 to 3,000.",
                'faq_three' => "El Nido resort restaurants need reservations in peak season (December to April). Town strip walk-ins work fine.",
                'faq_four' => "Skip the resort restaurants for dinner one night and walk the El Nido town strip. The casual seafood spots deliver the local food at a third of the price.",
            ],
            'subic' => [
                'display' => 'Subic',
                'image_queries' => ['Subic Bay Freeport', 'Subic Bay Olongapo', 'Subic resort'],
                'maps_q' => 'restaurants Subic Bay Freeport Zone',
                'intro_one' => "Subic food runs across the Freeport restaurants for the family weekend crowd and the Barrio Barretto strip for the longer-running expat-favorite spots. Each corner serves a distinctly different crowd.",
                'intro_two' => "Subic dining stays affordable compared to Manila because the freeport zone exempts the rent and operations from the same tax burden.",
                'where_to_eat' => "Subic Bay Freeport restaurants at the Boardwalk side for waterfront dining. Barrio Barretto for the older expat-favorite seafood spots. The Harbor Point mall for chain restaurants.",
                'cuisines' => "Seafood leads at Subic (fresh catch from the bay). American and Filipino comfort second tier. Korean BBQ scattered across the Freeport.",
                'budget' => "Subic meals run 350 to 700 pesos per person at mid-range. Seafood family meals 1,500 to 2,500 pesos for four. Harbor Point chains 350 to 600 pesos.",
                'order' => "At Subic seafood restaurants, the grilled bangus or pugapo. Barrio Barretto strip for the older sit-down expat menus. Harbor Point chains deliver predictable family meals.",
                'timing' => "Subic weekend lunch 12 to 2 PM peaks. Sunset dinners on the Boardwalk 5 to 7 PM. Weekday slots run calmer.",
                'faq_one' => "Grilled fish at a Subic Bay Boardwalk seafood restaurant. The bay catch keeps the kitchen honest.",
                'faq_two' => "350 to 700 pesos per person at Subic mid-range. Seafood family meals 1,500 to 2,500.",
                'faq_three' => "Subic reservations help at the upper-tier Boardwalk restaurants on weekend sunsets. Other slots accept walk-ins.",
                'faq_four' => "Cross from the Freeport to Barrio Barretto for the older long-running sit-down restaurants. The Freeport handles family weekends, Barretto handles the longer dinners.",
            ],
            'la_union' => [
                'display' => 'La Union',
                'image_queries' => ['La Union San Juan beach', 'San Juan La Union surf', 'La Union Urbiztondo'],
                'maps_q' => 'restaurants San Juan La Union',
                'intro_one' => "La Union food culture has grown around the San Juan surf scene. The Urbiztondo beachfront restaurants serve the surf crowd while the older town corners hold the traditional Ilocano restaurants.",
                'intro_two' => "La Union dining mixes the surf-town casual style with the heritage Ilocano cuisine in a way you don't find elsewhere on the North Luzon coast.",
                'where_to_eat' => "Urbiztondo Beach strip in La Union for surf-side casual dining. The El Union and Flotsam corner for coffee culture. San Juan town proper for heritage Ilocano restaurants.",
                'cuisines' => "Casual cafe culture leads La Union (smoothie bowls, coffee, pastries). Ilocano heritage second tier (bagnet, longganisa, pinakbet). Pizza and Italian concepts grew with the surf crowd.",
                'budget' => "La Union meals run 350 to 700 pesos per person at mid-range. Cafe stops 250 to 450 pesos. Heritage Ilocano dinners 400 to 700 pesos.",
                'order' => "At La Union beachfront restaurants, the smoothie bowl breakfast or the pizza late-night. At heritage Ilocano spots, the bagnet with KBL on the side.",
                'timing' => "La Union weekend brunch fills Urbiztondo cafes 9 to 11 AM. Sunset dinners 5 to 7 PM. Holy Week and surf season peaks queue past 30 minutes.",
                'faq_one' => "A smoothie bowl breakfast at an Urbiztondo cafe in La Union. The first meal that defines a surf-town stay.",
                'faq_two' => "350 to 700 pesos per person at La Union mid-range. Cafe stops 250 to 450.",
                'faq_three' => "La Union walk-ins work most slots. Surf season weekends and Holy Week need reservations.",
                'faq_four' => "Pair beach-strip cafe brunches with heritage Ilocano dinner in town. Two stops cover the La Union range.",
            ],
        ];
    }

    private function ensureDir(string $path): void
    {
        if (!is_dir($path)) mkdir($path, 0755, true);
    }
}
