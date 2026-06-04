<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Rewrites the heading and intro of every how_to_get_to block (and the
 * map_embed "Where is X" heading) so it shows the *place name*, not the
 * full keyword phrase. Pre-existing destination seeders generated
 * headings like "How to get to Beach and Resort in La Union" because
 * they used the page H1 as the place name. The correct read is just
 * "How to get to La Union".
 *
 * Place-name resolution walks three sources in order:
 *   1. destinations.php data['name'] when the slug maps to a known
 *      destination key (e.g. "la-union" → "La Union").
 *   2. Common landmark / mall lookup table for slugs like "sm-aura",
 *      "mall-of-asia", "ortigas".
 *   3. Substring after the last " in " in the keyword phrase, with the
 *      result title-cased and any small connector words lowercased.
 *
 * Idempotent: safe to re-run.
 */
class FixHowToGetToHeadingsSeeder extends Seeder
{
    private array $destData = [];
    private array $destKeysByLength = [];

    /**
     * Slug-needle → proper venue name. Covers malls + districts that
     * destinations.php doesn't track because they live in the food
     * vertical. Order matters — longest substring first.
     */
    private array $venueMap = [
        'mall-of-asia-seaside' => 'Mall of Asia',
        'mall-of-asia' => 'Mall of Asia',
        'sm-aura' => 'SM Aura',
        'sm-megamall' => 'SM Megamall',
        'sm-north' => 'SM North Edsa',
        'sm-fairview' => 'SM Fairview',
        'sm-bacolod' => 'SM Bacolod',
        'sm-pampanga' => 'SM Pampanga',
        'sm-clark' => 'SM Clark',
        'sm-marikina' => 'SM Marikina',
        'sm-calamba' => 'SM Calamba',
        'sm-dasma' => 'SM Dasmariñas',
        'sm-baguio' => 'SM Baguio',
        'sm-bacoor' => 'SM Bacoor',
        'sm-bicutan' => 'SM Bicutan',
        'sm-city-cebu' => 'SM City Cebu',
        'sm-city-iloilo' => 'SM City Iloilo',
        'sm-seaside' => 'SM Seaside Cebu',
        'sm-southmall' => 'SM Southmall',
        'sm-san-lazaro' => 'SM San Lazaro',
        'sm-sta-rosa' => 'SM Sta. Rosa',
        'sm-sta-mesa' => 'SM Sta. Mesa',
        'sm-manila' => 'SM Manila',
        'sm-grand-central' => 'SM Grand Central',
        'sm-makati' => 'SM Makati',
        'megamall' => 'SM Megamall',
        'glorietta' => 'Glorietta',
        'greenbelt' => 'Greenbelt',
        'rockwell' => 'Power Plant Mall',
        'powerplant-mall' => 'Power Plant Mall',
        'high-street-bgc' => 'Bonifacio High Street',
        'bgc-high-street' => 'Bonifacio High Street',
        'high-street' => 'Bonifacio High Street',
        'shangrila-mall' => 'Shangri-La Plaza',
        'shangri-la' => 'Shangri-La Plaza',
        'solaire' => 'Solaire Resort',
        'okada-manila' => 'Okada Manila',
        'okada' => 'Okada Manila',
        'conrad-manila' => 'Conrad Manila',
        'conrad-hotel' => 'Conrad Manila',
        'nustar-cebu' => 'Nustar Cebu',
        'nustar' => 'Nustar Cebu',
        'trinoma' => 'TriNoma',
        'festival-mall' => 'Festival Mall',
        'festive-mall-iloilo' => 'Festive Walk Iloilo',
        'festive-walk-iloilo' => 'Festive Walk Iloilo',
        'gateway-mall-2' => 'Gateway Mall 2',
        'gateway-mall' => 'Gateway Mall',
        'gateway-cubao' => 'Gateway Mall',
        'gateway-2' => 'Gateway Mall 2',
        'gateway' => 'Gateway Mall',
        'robinsons-galleria-cebu' => 'Robinsons Galleria Cebu',
        'robinsons-galleria-south' => 'Robinsons Galleria South',
        'robinsons-galleria' => 'Robinsons Galleria',
        'robinsons-antipolo' => 'Robinsons Antipolo',
        'robinsons-ermita' => 'Robinsons Ermita',
        'robinsons-general-trias' => 'Robinsons General Trias',
        'robinsons-metro-east' => 'Robinsons Metro East',
        'uptown-mall' => 'Uptown Mall',
        'uptown-bgc' => 'Uptown BGC',
        'uptc' => 'UP Town Center',
        'up-town-center' => 'UP Town Center',
        'eastwood-city' => 'Eastwood City',
        'eastwood-mall' => 'Eastwood Mall',
        'eastwood' => 'Eastwood',
        'binondo' => 'Binondo',
        'cubao-expo' => 'Cubao Expo',
        'cubao' => 'Cubao',
        'tomas-morato' => 'Tomas Morato',
        'maginhawa-street' => 'Maginhawa Street',
        'maginhawa' => 'Maginhawa Street',
        'burgos-circle' => 'Burgos Circle',
        'kapitolyo-pasig' => 'Kapitolyo',
        'kapitolyo' => 'Kapitolyo',
        'katipunan' => 'Katipunan',
        'banawe-quezon-city' => 'Banawe (Quezon City)',
        'banawe-qc' => 'Banawe (Quezon City)',
        'jupiter-makati' => 'Jupiter Street, Makati',
        'makati-avenue' => 'Makati Avenue',
        'makati-peninsula' => 'Makati',
        'manila-peninsula' => 'The Peninsula Manila',
        'venice-grand-canal' => 'Venice Grand Canal',
        'mckinley-hill-taguig' => 'McKinley Hill',
        'mckinley-hill' => 'McKinley Hill',
        'ayala-malls-manila-bay' => 'Ayala Malls Manila Bay',
        'ayala-mall-manila-bay' => 'Ayala Malls Manila Bay',
        'ayala-feliz' => 'Ayala Feliz',
        'ayala-triangle' => 'Ayala Triangle Gardens',
        'ayala-cebu' => 'Ayala Center Cebu',
        'ayala-center-cebu' => 'Ayala Center Cebu',
        'ayala-malls' => 'Ayala Malls',
        'one-ayala-makati' => 'One Ayala',
        'one-ayala-mall' => 'One Ayala',
        'one-ayala' => 'One Ayala',
        'opus-mall' => 'Opus Mall',
        'capitol-commons' => 'Capitol Commons',
        'circuit-makati' => 'Circuit Makati',
        'circuit-mall' => 'Circuit Makati',
        'estancia-mall' => 'Estancia Mall',
        'estancia' => 'Estancia Mall',
        'arcovia' => 'Arcovia City',
        'tiendesitas' => 'Tiendesitas',
        'market-market' => 'Market! Market!',
        'newport-mall' => 'Newport Mall',
        'newport' => 'Newport City',
        'resorts-world-manila' => 'Resorts World Manila',
        'resorts-world' => 'Resorts World Manila',
        'city-of-dreams' => 'City of Dreams Manila',
        'ortigas-center' => 'Ortigas Center',
        'ortigas' => 'Ortigas Center',
        'cebu-it-park' => 'Cebu IT Park',
        'cebu-ayala' => 'Ayala Center Cebu',
        'it-park-cebu' => 'Cebu IT Park',
        'it-park' => 'Cebu IT Park',
        'camp-john-hay' => 'Camp John Hay',
        'baguio-session-road' => 'Session Road, Baguio',
        'baguio-with-view' => 'Baguio',
        'lucky-chinatown-mall' => 'Lucky Chinatown',
        'lucky-chinatown' => 'Lucky Chinatown',
        'podium' => 'The Podium',
        'fairview-terraces' => 'Fairview Terraces',
        'fairview' => 'Fairview',
        'fishermall' => 'Fisher Mall',
        'fisher-mall' => 'Fisher Mall',
        'naia-terminal-3' => 'NAIA Terminal 3',
        'naia-terminal-1' => 'NAIA Terminal 1',
        'evia-mall' => 'Evia Lifestyle Center',
        'evia' => 'Evia Lifestyle Center',
        'westgate-alabang' => 'Westgate Alabang',
        'westgate' => 'Westgate Alabang',
        'west-gate' => 'Westgate Alabang',
        'atc-alabang' => 'Alabang Town Center',
        'atc' => 'Alabang Town Center',
        'alabang-town-center' => 'Alabang Town Center',
        'filinvest-alabang' => 'Filinvest Alabang',
        'molito-alabang' => 'Molito Alabang',
        'molito' => 'Molito Alabang',
        'harbor-point-subic' => 'Harbor Point Subic',
        'green-belt' => 'Greenbelt',
        'gh-mall' => 'Greenhills Mall',
        'greenhills-mall' => 'Greenhills Mall',
        'greenhills-shopping-center' => 'Greenhills Shopping Center',
        'greenhills' => 'Greenhills',
        'banawe' => 'Banawe (Quezon City)',
        'gateway-cubao' => 'Gateway Mall',
        'pampanga-angeles' => 'Angeles, Pampanga',
        'pampanga-san-fernando' => 'San Fernando, Pampanga',
        'angeles-pampanga' => 'Angeles, Pampanga',
        'angeles' => 'Angeles, Pampanga',
        'clark-pampanga' => 'Clark, Pampanga',
        'clark' => 'Clark Freeport',
        'subic' => 'Subic Bay Freeport',
        'cdo' => 'Cagayan de Oro',
        'gensan' => 'General Santos',
        'qc' => 'Quezon City',
        'moa-seaside' => 'Mall of Asia',
        'moa' => 'Mall of Asia',
        'bgc' => 'Bonifacio Global City',
    ];

    public function run(): void
    {
        $this->destData = require database_path('data/destinations.php');
        $keys = array_keys($this->destData);
        usort($keys, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $this->destKeysByLength = $keys;

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->select(
                'p.id as page_id',
                'k.slug as slug',
                'k.phrase as phrase',
                'k.cluster_tag as cluster_tag'
            )
            ->get();

        $this->command->info('Pages to process: ' . $pages->count());

        $stats = ['how' => 0, 'map' => 0];

        foreach ($pages as $page) {
            $place = $this->resolvePlaceName((string) $page->slug, (string) $page->phrase, (string) $page->cluster_tag);
            if ($place === '') continue;

            $this->fixHowToGetTo((int) $page->page_id, $place, $stats);
            $this->fixMapEmbed((int) $page->page_id, $place, $stats);
        }

        $this->command->info("how_to_get_to headings fixed: {$stats['how']}");
        $this->command->info("map_embed headings fixed: {$stats['map']}");
    }

    /**
     * Three-tier place-name resolution. Returns "" if no usable name
     * can be derived (rare — most slugs have an "X in Y" structure).
     */
    private function resolvePlaceName(string $slug, string $phrase, string $cluster): string
    {
        // 1. Mall / district lookup table (longest-needle first via key order
        //    inside venueMap — see the multi-word entries above the singles).
        foreach ($this->venueMap as $needle => $name) {
            if (str_contains($slug, $needle)) return $name;
        }

        // 2. destinations.php name when the slug maps to a known dest key.
        foreach ($this->destKeysByLength as $key) {
            if ($key === '' || $key === '_default') continue;
            if (str_contains($slug, $key) && isset($this->destData[$key]['name'])) {
                return $this->destData[$key]['name'];
            }
        }

        // 3. Substring after the last " in " in the phrase, title-cased.
        $phrase = trim($phrase);
        if (preg_match('~\bin\s+(.+)$~i', $phrase, $m)) {
            return $this->titleCase(trim($m[1]));
        }

        return $this->titleCase($phrase);
    }

    private function titleCase(string $phrase): string
    {
        $phrase = trim(preg_replace('~\s+~', ' ', $phrase));
        if ($phrase === '') return '';
        $small = ['a', 'an', 'the', 'in', 'of', 'on', 'at', 'by', 'to', 'for', 'with', 'and', 'or', 'as', 'de', 'la', 'las', 'el', 'los'];
        $words = explode(' ', $phrase);
        $last = count($words) - 1;
        $out = [];
        foreach ($words as $i => $word) {
            $lower = mb_strtolower($word, 'UTF-8');
            // First / last word always cap. Small connector words stay
            // lowercase in the middle. "La Union" / "El Nido" already
            // come from the venueMap / destData with proper case so they
            // bypass this codepath.
            if ($i === 0 || $i === $last || !in_array($lower, $small, true)) {
                $out[] = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($word, 1, null, 'UTF-8');
            } else {
                $out[] = $lower;
            }
        }
        return implode(' ', $out);
    }

    private function fixHowToGetTo(int $pageId, string $place, array &$stats): void
    {
        $blocks = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', 'how_to_get_to')
            ->get();
        foreach ($blocks as $block) {
            $payload = json_decode((string) $block->payload_json, true) ?: [];
            $oldHeading = (string) ($payload['heading'] ?? '');
            $newHeading = 'How to get to ' . $place;
            if ($oldHeading === $newHeading) continue;
            $payload['heading'] = $newHeading;

            // Patch any intro line that quotes the place name so it
            // matches the new heading (only when the substring is
            // present — avoids garbling custom intros).
            if (!empty($payload['intro']) && $oldHeading !== '') {
                $stripped = preg_replace('~^How to get to\s+~i', '', $oldHeading);
                if ($stripped && $stripped !== $place) {
                    $payload['intro'] = str_ireplace($stripped, $place, (string) $payload['intro']);
                }
            }
            DB::table('rg_content_blocks')->where('id', $block->id)->update([
                'payload_json' => json_encode($payload),
                'updated_at' => now(),
            ]);
            $stats['how']++;
        }
    }

    private function fixMapEmbed(int $pageId, string $place, array &$stats): void
    {
        $blocks = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', 'map_embed')
            ->get();
        foreach ($blocks as $block) {
            $payload = json_decode((string) $block->payload_json, true) ?: [];
            $oldHeading = (string) ($payload['heading'] ?? '');
            $newHeading = 'Where is ' . $place . '?';
            if ($oldHeading === $newHeading) continue;
            $payload['heading'] = $newHeading;
            DB::table('rg_content_blocks')->where('id', $block->id)->update([
                'payload_json' => json_encode($payload),
                'updated_at' => now(),
            ]);
            $stats['map']++;
        }
    }
}
