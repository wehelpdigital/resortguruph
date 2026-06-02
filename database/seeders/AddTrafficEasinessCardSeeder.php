<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Adds a "Traffic" quick-fact card to every food keyword page.
 *
 *   - Widens the quick-facts strip from 3 cards to 4 (grid-cols-4 on lg,
 *     grid-cols-2 on md, stacked on mobile).
 *   - Inserts the new card after "Avoid (weekends)" with an amber palette
 *     so it visually reads as the fourth column (traffic-light caution).
 *   - Pulls per-location copy from a curated map; everything else falls
 *     back to a sensible default for that location type (mall vs city
 *     vs province vs beach).
 *
 * Idempotent: detects the new card by its data-traffic-card attribute and
 * skips already-patched pages, so it's safe to re-run after edits.
 */
class AddTrafficEasinessCardSeeder extends Seeder
{
    /**
     * Per-location traffic copy. Keys match the normalized location strings
     * already used elsewhere (RebuildFoodPagesV2Seeder::normalizeLocation).
     *
     * Each entry:
     *   big   — 2-word headline shown in 2xl font
     *   tip   — 1-line detail under the label
     *
     * @var array<string,array{big:string,tip:string}>
     */
    private array $trafficMap = [
        // Heavy-traffic Metro Manila malls / districts
        'moa' => ['big' => 'Heavy weekends', 'tip' => 'Roxas Blvd jams 5-8 PM, park early'],
        'greenhills' => ['big' => 'Brutal Fri-Sun', 'tip' => 'Connecticut & Ortigas crawl, off-peak rec'],
        'megamall' => ['big' => 'Heavy PM', 'tip' => 'EDSA ramps clog 5-9 PM weekdays'],
        'sm_north' => ['big' => 'Heavy weekdays', 'tip' => 'North EDSA crush 6-8 PM, MRT helps'],
        'cubao' => ['big' => 'Constant heavy', 'tip' => 'EDSA & Aurora jam most of the day'],
        'trinoma' => ['big' => 'Heavy PM', 'tip' => 'North EDSA queues, MRT North station easier'],
        'shangrila' => ['big' => 'Heavy PM', 'tip' => 'EDSA-Ortigas crawls 5-9 PM'],
        'gateway' => ['big' => 'Heavy', 'tip' => 'EDSA-Cubao jams, take MRT Araneta'],
        'rob_galleria' => ['big' => 'Heavy PM', 'tip' => 'EDSA-Ortigas peak 5-9 PM'],
        'podium' => ['big' => 'Heavy PM', 'tip' => 'Ortigas Center clogs 5-8 PM weekdays'],
        'ortigas' => ['big' => 'Heavy PM', 'tip' => 'Ortigas CBD jams 5-9 PM, skip Fridays'],
        'divisoria' => ['big' => 'Brutal daytime', 'tip' => 'Recto-Divisoria foot traffic + tight roads'],
        'binondo' => ['big' => 'Heavy weekends', 'tip' => 'Tight one-ways, weekends are slow'],
        'manila' => ['big' => 'Heavy PM', 'tip' => 'Roxas & Quirino jam 5-9 PM weekdays'],
        'pasay' => ['big' => 'Heavy PM', 'tip' => 'Airport-bound traffic 5-9 PM'],
        'malate' => ['big' => 'Moderate', 'tip' => 'Calmer than Makati but Roxas slows PM'],

        // Moderate-traffic CBDs and lifestyle districts
        'bgc' => ['big' => 'Moderate', 'tip' => '5-8 PM is worst, parking lots fill weekends'],
        'rockwell' => ['big' => 'Moderate', 'tip' => 'Estrella ramp clogs 6-8 PM weekdays'],
        'glorietta' => ['big' => 'Moderate', 'tip' => 'Ayala-Makati crawls 5-9 PM weekdays'],
        'greenbelt' => ['big' => 'Moderate', 'tip' => 'Ayala-Makati crawls 5-9 PM weekdays'],
        'makati' => ['big' => 'Moderate', 'tip' => 'CBD jams 5-9 PM, parking pricey'],
        'eastwood' => ['big' => 'Moderate', 'tip' => 'C-5 jams 6-9 PM, walk inside the city'],
        'tomas_morato' => ['big' => 'Moderate', 'tip' => 'Quezon Ave busy at dinner, side streets ok'],
        'uptown' => ['big' => 'Moderate', 'tip' => '32nd St calm, BGC fringe jams PM'],
        'festival_mall' => ['big' => 'Moderate', 'tip' => 'Alabang-Zapote crawls Fri-Sun'],
        'alabang' => ['big' => 'Moderate', 'tip' => 'SLEX exits clog 5-9 PM weekdays'],
        'sm_aura' => ['big' => 'Moderate', 'tip' => 'BGC fringe jams 5-8 PM weekdays'],
        'market_market' => ['big' => 'Moderate', 'tip' => 'BGC south-side jams Fri-Sun PM'],
        'rob_ermita' => ['big' => 'Moderate', 'tip' => 'Pedro Gil & Roxas crawl 5-9 PM'],
        'ayala_mb' => ['big' => 'Moderate', 'tip' => 'Roxas Blvd PM jams, weekends busiest'],
        'solaire' => ['big' => 'Moderate', 'tip' => 'Roxas-Macapagal calmer than EDSA'],
        'okada' => ['big' => 'Moderate', 'tip' => 'Macapagal jams Fri-Sun, weekday calm'],
        'resorts_world' => ['big' => 'Heavy PM', 'tip' => 'NAIA-bound traffic 5-9 PM daily'],
        'quezon_city' => ['big' => 'Heavy PM', 'tip' => 'EDSA & Commonwealth jam 5-9 PM'],
        'pasig' => ['big' => 'Moderate', 'tip' => 'Ortigas-Pasig clogs 5-9 PM weekdays'],
        'mandaluyong' => ['big' => 'Heavy PM', 'tip' => 'EDSA-Shangri-La crawls 5-9 PM'],
        'paranaque' => ['big' => 'Moderate', 'tip' => 'Sucat & SLEX jam 5-9 PM weekdays'],
        'las_pinas' => ['big' => 'Moderate', 'tip' => 'Alabang-Zapote crawls Fri-Sun'],
        'taguig' => ['big' => 'Moderate', 'tip' => 'BGC ramps jam 5-8 PM weekdays'],
        'antipolo' => ['big' => 'Manageable', 'tip' => 'Sumulong Hwy slow Sat-Sun mornings'],

        // Out-of-town day trips & province
        'tagaytay' => ['big' => 'Heavy weekends', 'tip' => 'Aguinaldo Hwy jams Fri-Sun, go weekday'],
        'baguio' => ['big' => 'Peak-season heavy', 'tip' => 'Marcos Hwy crawls Holy Week & December'],
        'la_union' => ['big' => 'Manageable', 'tip' => 'NLEX-TPLEX smooth, weekends slower'],
        'pampanga' => ['big' => 'Easy weekdays', 'tip' => 'NLEX smooth, San Fernando exits jam'],
        'bulacan' => ['big' => 'Easy weekdays', 'tip' => 'NLEX smooth except Fri PM'],
        'subic' => ['big' => 'Easy', 'tip' => 'SCTEX runs free, weekends pick up'],
        'cavite' => ['big' => 'Moderate', 'tip' => 'CAVITEX jams 5-9 PM weekdays'],
        'laguna' => ['big' => 'Moderate', 'tip' => 'SLEX-Calamba jams 5-9 PM weekdays'],
        'batangas' => ['big' => 'Manageable', 'tip' => 'STAR Tollway smooth, port area busy'],
        'rizal' => ['big' => 'Heavy Saturdays', 'tip' => 'Marcos Hwy & Sumulong slow weekend AM'],
        'vigan' => ['big' => 'Easy', 'tip' => 'Heritage zone is pedestrian, calzada is calm'],
        'sagada' => ['big' => 'Easy', 'tip' => 'Mountain roads quiet outside Holy Week'],
        'banaue' => ['big' => 'Easy', 'tip' => 'Mountain roads quiet most of the year'],
        'puerto_galera' => ['big' => 'Easy on land', 'tip' => 'Ferry timing matters more than traffic'],
        'iloilo' => ['big' => 'Manageable', 'tip' => 'Diversion Road calm, downtown busier'],
        'bacolod' => ['big' => 'Manageable', 'tip' => 'Lacson St calm outside MassKara week'],

        // Cebu / Visayas
        'cebu' => ['big' => 'Heavy PM', 'tip' => 'IT Park & Ayala jam 5-9 PM weekdays'],
        'mactan' => ['big' => 'Moderate', 'tip' => 'Bridge bottlenecks 5-9 PM weekdays'],
        'bohol' => ['big' => 'Easy', 'tip' => 'Tagbilaran calm, Panglao roads quiet'],
        'dumaguete' => ['big' => 'Easy', 'tip' => 'Rizal Blvd calm, Sibulan road slow PM'],
        'tacloban' => ['big' => 'Manageable', 'tip' => 'Downtown busy at noon, easy otherwise'],

        // Mindanao
        'davao' => ['big' => 'Manageable', 'tip' => 'Strict speed limits, but flows steady'],
        'cagayan_de_oro' => ['big' => 'Moderate', 'tip' => 'Limketkai-Divisoria jams 5-9 PM'],
        'zamboanga' => ['big' => 'Manageable', 'tip' => 'Downtown calm, evenings pick up'],
        'gensan' => ['big' => 'Easy', 'tip' => 'City roads flow steady outside festival'],
        'butuan' => ['big' => 'Easy', 'tip' => 'Downtown busy at noon, calm otherwise'],

        // Beach / Island destinations
        'boracay' => ['big' => 'No cars', 'tip' => 'Walk or e-trike everywhere on White Beach'],
        'palawan' => ['big' => 'Easy', 'tip' => 'Roads quiet, ferry & flight timing matter more'],
        'coron' => ['big' => 'Easy on land', 'tip' => 'Town small, boat schedules matter more'],
        'el_nido' => ['big' => 'Easy on land', 'tip' => 'Town small, boat schedules matter more'],
        'siargao' => ['big' => 'Easy', 'tip' => 'Habal-habal & van flow, surf-zone busier'],
        'siquijor' => ['big' => 'Easy', 'tip' => 'Coastal loop quiet, ferry pier busy AM'],
        'camiguin' => ['big' => 'Easy', 'tip' => 'Loop road quiet most of the year'],
    ];

    public function run(): void
    {
        $foodKeywords = DB::table('rg_keywords')
            ->where('category', 'food')
            ->select('id', 'slug')
            ->get();

        $processed = 0;
        $patched = 0;
        $skipped = 0;

        foreach ($foodKeywords as $kw) {
            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page || empty($page->body_html)) {
                $skipped++;
                continue;
            }

            $body = $page->body_html;

            // Idempotency: skip if traffic card already injected.
            if (str_contains($body, 'data-traffic-card')) {
                $skipped++;
                continue;
            }

            $locKey = $this->locationKeyFromSlug($kw->slug);
            $copy = $this->trafficMap[$locKey] ?? $this->fallbackCopy($locKey);

            $newCard = $this->buildCard($copy['big'], $copy['tip']);

            // Two-step:
            //   1) Widen the grid from 3 cards → 4 cards.
            //   2) Append the new card immediately before the strip closes.
            $patched3 = preg_replace(
                '~(<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-3 gap-3")~',
                '<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3"',
                $body,
                1,
                $hits
            );
            if ($hits === 0) {
                $skipped++;
                continue;
            }

            // Find the strip block and append the new card to its end.
            $patched4 = preg_replace_callback(
                '~(<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">)((?:.|\s)*?)(</div>\s*</section>|</div>\s*</article>|</div>\s*<h2|</div>\s*<div class="not-prose)~',
                function ($m) use ($newCard) {
                    // Insert the new card just before the closing </div> that ends
                    // the strip. We trust that the strip is balanced (3 cards in,
                    // then closing div). Find the last </div> in the inner block.
                    $inner = $m[2];
                    // Append the new card after the 3 existing cards but BEFORE
                    // the grid's own closing </div>. Strategy: count opens vs
                    // closes — when we find the closing </div> that balances the
                    // opening grid <div>, insert before it.
                    return $m[1] . $inner . $newCard . $m[3];
                },
                $patched3,
                1,
                $cardHits
            );

            if ($cardHits === 0) {
                // Fallback: simpler insertion at the next sibling boundary.
                $patched4 = $this->insertCardSimple($patched3, $newCard);
            }

            DB::table('rg_seo_pages')
                ->where('id', $page->id)
                ->update(['body_html' => $patched4]);

            $patched++;
            $processed++;

            if ($processed % 50 === 0) {
                $this->command->info("  {$processed} processed (patched: {$patched})...");
            }
        }

        $this->command->info("Done. Processed: {$processed} | Patched: {$patched} | Skipped: {$skipped}");
    }

    /**
     * Brittle fallback: find the third card's closing </div></div> and
     * insert the new card right after it. The strip ends with a 4th </div>
     * that closes the grid itself.
     */
    private function insertCardSimple(string $body, string $newCard): string
    {
        // Find the third "Avoid (weekends)" card and inject after its outer div.
        // The strip has exactly 3 nested top-level card divs. We anchor on the
        // unique "Avoid (weekends)" text and walk forward to its outer </div>.
        $pos = strpos($body, 'Avoid (weekends)');
        if ($pos === false) return $body;

        $closeMarker = '</div>';
        $depth = 1;
        $i = $pos;
        $len = strlen($body);
        $endOfCard = null;
        while ($i < $len) {
            $open = strpos($body, '<div', $i);
            $close = strpos($body, '</div>', $i);
            if ($close === false) break;
            if ($open !== false && $open < $close) {
                $depth++;
                $i = $open + 4;
            } else {
                $depth--;
                $i = $close + 6;
                if ($depth === 0) {
                    $endOfCard = $i;
                    break;
                }
            }
        }
        if ($endOfCard === null) return $body;

        return substr($body, 0, $endOfCard) . ' ' . $newCard . substr($body, $endOfCard);
    }

    private function buildCard(string $big, string $tip): string
    {
        // Amber palette so the card visually reads as the 4th column.
        // Icon: traffic light + chevron suggesting flow/queue.
        $svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">'
             . '<rect x="9" y="3" width="6" height="18" rx="2"/>'
             . '<circle cx="12" cy="7.5" r="1.2"/>'
             . '<circle cx="12" cy="12" r="1.2"/>'
             . '<circle cx="12" cy="16.5" r="1.2"/>'
             . '</svg>';

        $bigSafe = htmlspecialchars($big, ENT_QUOTES, 'UTF-8');
        $tipSafe = htmlspecialchars($tip, ENT_QUOTES, 'UTF-8');

        return ' <div data-traffic-card class="rounded-lg p-4 text-center" style="background:#fffbeb;border:1px solid #fcd34d">'
             . ' <div class="flex justify-center mb-2" style="color:#b45309">' . $svg . '</div>'
             . ' <div class="text-2xl font-bold" style="color:#b45309">' . $bigSafe . '</div>'
             . ' <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#78350f">Traffic</div>'
             . ' <div class="text-xs text-slate-600 mt-1">' . $tipSafe . '</div>'
             . ' </div>';
    }

    /**
     * Derive the location key from a slug like "restaurant-in-tagaytay" or
     * "best-restaurant-in-bgc-philippines". Mirrors the V2 normalizer.
     */
    private function locationKeyFromSlug(string $slug): string
    {
        $loc = preg_replace('~^.*?in-~', '', $slug);
        $loc = preg_replace('~-philippines$~', '', $loc);
        $loc = str_replace('-', '_', $loc);
        return strtolower($loc);
    }

    /**
     * When no curated entry matches, infer level from name hints
     * (mall / city / town) so the card still ships sensible copy.
     */
    private function fallbackCopy(string $locKey): array
    {
        if (preg_match('/(mall|sm_|robinsons|gateway|trinoma|festival|podium|megamall|aura|moa|north_edsa)/', $locKey)) {
            return ['big' => 'Heavy weekends', 'tip' => 'Mall ramps clog Fri-Sun, weekday off-peak rec'];
        }
        if (preg_match('/(city|district|quezon|manila|makati|pasay|taguig|mandaluyong|paranaque|pasig|caloocan|marikina|las_pinas)/', $locKey)) {
            return ['big' => 'Heavy PM', 'tip' => 'CBD roads jam 5-9 PM, MRT/bus calmer'];
        }
        if (preg_match('/(island|beach|boracay|palawan|coron|el_nido|siargao|camiguin|guimaras|siquijor)/', $locKey)) {
            return ['big' => 'Easy', 'tip' => 'Roads quiet, plan around ferry & flight'];
        }
        return ['big' => 'Moderate', 'tip' => 'Weekday off-peak is the calmest window'];
    }
}
