<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Enforces Resort Guru content Rule 3 (the hard pricing line):
 *   "Never mention prices, peso amounts, PHP figures, day-use rates,
 *    'for under X PHP,' or 'around X pesos.'"
 *
 * Applied to every non-MOA food page (565 of them):
 *   1. Strip the entire "Budget guide for [Area]" H2 section + 5-tier
 *      pricing table — the whole section is about prices, can't be saved.
 *   2. Replace the "Per person" quick-fact card with a non-price tier
 *      indicator ("Multiple tiers / Quick eats to sit-down dining").
 *   3. Strip any remaining sentence in body_html that contains "peso(s)"
 *      or a ₱ symbol — aggressive but compliant.
 *   4. Rebuild tldr to drop the per-person-spend bullet.
 *   5. Rebuild wwww_json to drop any price mentions.
 *
 * MOA pilot skipped — its content was hand-curated separately.
 */
class StripPricesFromFoodPagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Stripping all pricing from food pages (Rule 3 enforcement) ===');

        $keywords = DB::table('rg_keywords')->where('category', 'food')->get();
        $processed = 0; $skipped = 0;
        $totalPesoBefore = 0; $totalPesoAfter = 0;

        foreach ($keywords as $kw) {
            if ($kw->slug === 'restaurant-in-mall-of-asia') { $skipped++; continue; }

            $page = DB::table('rg_seo_pages')->where('keyword_id', $kw->id)->first();
            if (!$page) continue;

            $body = $page->body_html;
            $totalPesoBefore += substr_count($body, '₱') + preg_match_all('/\bpesos?\b/i', $body);

            $body = $this->stripBudgetSection($body);
            $body = $this->fixQuickFactsPerPerson($body);
            $body = $this->stripPesoSentences($body);

            $totalPesoAfter += substr_count($body, '₱') + preg_match_all('/\bpesos?\b/i', $body);

            // Rebuild tldr + wwww without prices (regenerate clean templates)
            $area = $this->extractArea($kw->phrase);
            $type = $this->detectType($kw->cluster_tag ?? '');

            // Also strip prices from faq_json — each FAQ answer goes through
            // the same sentence-level peso filter.
            $faqJson = $this->stripPricesFromFaq($page->faq_json ?? '');

            DB::table('rg_seo_pages')->where('id', $page->id)->update([
                'body_html' => $body,
                'tldr'      => $this->cleanTldr($area, $type),
                'wwww_json' => json_encode($this->cleanWwww($area, $type)),
                'faq_json'  => $faqJson,
                'meta_description' => $this->cleanMeta($page->meta_description ?? ''),
                'updated_at' => now(),
            ]);
            $processed++;
            if ($processed % 50 === 0) {
                $this->command->info("  $processed processed...");
            }
        }

        $this->command->info('');
        $this->command->info("Done. Processed: $processed | Skipped (MOA): $skipped");
        $this->command->info("Peso mentions across all pages — before: $totalPesoBefore | after: $totalPesoAfter");
    }

    /**
     * Strip the entire "Budget guide for [Area]" H2 section through to the
     * next H2 (usually "What to actually order at [Area]"). The whole
     * section is about prices, no rewrite saves it.
     */
    private function stripBudgetSection(string $body): string
    {
        $pattern = '~<h2>Budget guide[^<]*</h2>.*?(?=<h2>)~s';
        return preg_replace($pattern, '', $body) ?? $body;
    }

    /**
     * Replace the "Per person" quick-fact card's value (which contains the
     * banned ₱X-X price range) with a qualitative tier descriptor.
     */
    private function fixQuickFactsPerPerson(string $body): string
    {
        // The card looks like:
        //   <div class="rounded-lg p-4 text-center" style="background:#eff6ff;..">
        //     ...icon...
        //     <div class="text-2xl font-bold" style="color:#1d4ed8">₱200&ndash;1,500</div>
        //     <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#1e3a8a">Per person</div>
        //     <div class="text-xs text-slate-600 mt-1">Quick eats to sit-down dining</div>
        //   </div>
        //
        // Replace the price line with a non-price tier indicator.
        $body = preg_replace(
            '~(<div class="text-2xl font-bold" style="color:#1d4ed8">)[^<]*(</div>\s*<div class="text-\[10px\] uppercase tracking-wide font-bold" style="color:#1e3a8a">Per person</div>)~',
            '$1Multiple$2',
            $body
        );
        // Also adjust the sub-label
        $body = preg_replace(
            '~(<div class="text-xs text-slate-600 mt-1">)Quick eats to sit-down dining(</div>)~',
            '$1tiers from quick eats to sit-down$2',
            $body
        );
        return $body;
    }

    /**
     * Strip any sentence in body_html that contains "peso(s)" or a ₱ symbol.
     *
     * Strategy: split on </p>, </li>, <br>, </h2>, </div> boundaries; for
     * each text chunk, find sentences (split on period+space) and drop the
     * ones containing peso/₱. Preserve markup structure.
     */
    private function stripPesoSentences(string $body): string
    {
        // First pass: drop entire <li> elements that contain peso mentions
        $body = preg_replace('~<li[^>]*>[^<]*(?:₱|\bpesos?\b)[^<]*</li>~i', '', $body);
        $body = preg_replace('~<li[^>]*>[^<]*(?:₱|\bpesos?\b)[\s\S]*?</li>~i', '', $body);

        // Second pass: strip sentences inside <p> tags (with or without
        // class/style attributes) that contain peso/₱. Preserve the opening
        // tag's attributes so styling stays intact.
        $body = preg_replace_callback(
            '~(<p\b[^>]*>)([^<]+)(</p>)~',
            function ($m) {
                $openTag = $m[1];
                $text = $m[2];
                $closeTag = $m[3];
                if (!preg_match('/₱|\bpesos?\b/i', $text)) return $m[0];

                $sentences = preg_split('/(?<=[.!?])\s+/', $text);
                $kept = array_filter($sentences, fn($s) => !preg_match('/₱|\bpesos?\b/i', $s));
                $joined = trim(implode(' ', $kept));
                return $joined === '' ? '' : $openTag . $joined . $closeTag;
            },
            $body
        );

        // Third pass: drop entire bullet/card wrappers whose content had a
        // peso mention that emptied the inner <p>. The wrapper sits in a
        // "How to get to" grid card — an empty card looks broken.
        $body = preg_replace(
            '~<div class="rounded-xl border border-slate-200 bg-white p-4">\s*<div class="text-\[10px\][^>]*>[^<]+</div>\s*</div>~',
            '',
            $body
        );

        // Third pass: catch peso/₱ in <span> chip labels
        $body = preg_replace('~<span[^>]*>[^<]*(?:₱|\bpesos?\b)[^<]*</span>~i', '', $body);

        return $body;
    }

    private function cleanTldr(string $area, string $type): string
    {
        return match ($type) {
            'mall'        => "* Two food zones to navigate — main mall and the side strip\n* Walk-in friendly between 3 and 5 PM any day\n* Avoid the noon to 2 PM weekend lunch crush\n* Strongest cuisines: Japanese ramen, Korean BBQ, Filipino chains\n* Reserve for upper-tier sit-downs on Friday and Saturday nights",
            'city'        => "* $area food sprawls across districts, each on its own rhythm\n* Local heritage cuisine is the standout\n* Better to ask which district matches your meal before picking a restaurant\n* Walking food tours work better than mall-only crawls\n* Market eating gives the local flavor without the tourist markup",
            'destination' => "* $area is a multi-day food destination, not a quick stop\n* Regional speciality dishes are the headline\n* Public market lunches deliver the best local flavor\n* Reservations help on weekend dinners during peak season\n* Sunset dining is the local highlight on coastal areas",
            default       => "* $area is a food strip where regulars walk the side streets, not the main road\n* Long-running family-run kitchens beat new arrivals on consistency\n* Easiest walk-in: weekday late lunch, 2 to 4 PM\n* Late-night meals available — strip stays open past 10 PM Fridays and Saturdays\n* Repeat visits reveal the better corners that first-timers miss",
        };
    }

    private function cleanWwww(string $area, string $type): array
    {
        return match ($type) {
            'mall' => [
                'why'   => "$area packs many restaurants across multiple cuisines under one roof. Best for groups that can't agree on what to eat, families with mixed appetites, or quick weekday office lunches with predictable service.",
                'when'  => "Easiest walk-in is the 3 to 5 PM window any day. Avoid 12 to 2 PM on weekends (peak mall traffic). Weekend dinner peaks 6 to 8 PM — reserve at upper-tier spots if attending.",
                'where' => "$area splits across multiple floors. Main strip restaurants on the upper levels. Food court on the ground or basement floor (quickest queue). Side wings hold the newer concept restaurants. Plan your wing before committing.",
                'whom'  => "Best for family Sunday lunches, pre-event group meals (concerts, sports), date nights with predictable service, and BPO office lunches. Skip if you're hunting hole-in-the-wall finds or wanting quiet conversation-friendly tables.",
            ],
            'city' => [
                'why'   => "$area food culture spans heritage cuisine, modern chain dining, and market eats. Each $area district runs on a different rhythm so picking the right neighborhood matters more than picking the right restaurant.",
                'when'  => "Lunch peaks 12 to 2 PM in business districts. Dinner peaks 7 to 9 PM. For local heritage spots, time your visit around market hours (5 AM to 2 PM) or pulled-from-the-grill dinners (5 to 8 PM).",
                'where' => "$area splits into downtown (heritage and market food), commercial districts (chain restaurants and mall food), and outer neighborhoods (family-run carinderias). Each warrants a separate visit.",
                'whom'  => "Best for multi-day food trips, heritage cuisine hunters, and travelers who want authentic local flavor over predictable chains. Skip if you need single-stop convenience or strict accessibility.",
            ],
            'destination' => [
                'why'   => "$area is a destination where the food is part of the trip purpose. Regional speciality dishes anchor the visit. Public market eating gives the local rhythm without the tourist markup.",
                'when'  => "Peak season runs December to May for most $area destinations. Off-season delivers thinner crowds. Sunset dining is the local highlight for beachside areas. Market eating happens morning to mid-afternoon.",
                'where' => "$area town strip handles casual seafood and Filipino comfort. Resort restaurants serve the upper-tier dining. Public market eateries serve the budget local-flavor option. Each fills a different meal slot.",
                'whom'  => "Best for travelers booking 2 to 4 nights, photo-friendly dining settings, and heritage cuisine adventures. Skip if you want quick weekday meals or strict AC-only requirements.",
            ],
            default => [
                'why'   => "$area is a working dining strip where regulars eat at the side streets, not the main road. The family-run kitchens that have lasted 10+ years deliver the consistency. Late-night meals work because the strip stays open past most other corners.",
                'when'  => "Easiest walk-in is the 2 to 4 PM window weekdays. Weekend late-night (10 PM onwards) draws the after-work crowd. Sunday brunch fills the cafe-side corners 10 AM to noon.",
                'where' => "$area runs along the main avenue, with the long-running family restaurants tucked one block off the main road. The newer concept places open on the main strip first. The food court or street stalls handle the quickest meals.",
                'whom'  => "Best for late-night meals, family-run authenticity hunters, and weekend brunch crowds. Skip if you need mall parking convenience, reservation systems, or AC-only dining.",
            ],
        };
    }

    private function stripPricesFromFaq(string $json): string
    {
        if ($json === '' || $json === null) return $json;
        $data = json_decode($json, true);
        if (!is_array($data)) return $json;

        foreach ($data as $i => $entry) {
            if (!is_array($entry)) continue;
            // Strip sentences containing peso/₱ from both question and answer
            foreach (['question', 'answer'] as $field) {
                if (!isset($entry[$field])) continue;
                $text = $entry[$field];
                if (!preg_match('/₱|\bpesos?\b/i', $text)) continue;

                $sentences = preg_split('/(?<=[.!?])\s+/', $text);
                $kept = array_filter($sentences, fn($s) => !preg_match('/₱|\bpesos?\b/i', $s));
                $data[$i][$field] = trim(implode(' ', $kept));
            }
            // If the answer became empty, give it a non-price replacement
            if (empty(trim($data[$i]['answer'] ?? ''))) {
                $data[$i]['answer'] = 'Walk the side streets before committing — the kitchens one block off the main strip usually deliver better food at a lower spend than the chains on the main road.';
            }
        }
        return json_encode($data);
    }

    private function cleanMeta(string $meta): string
    {
        // Strip any peso/₱ from the meta description sentence
        $meta = preg_replace('/₱[\d,–\-]+\s*(?:per\s+person)?/', '', $meta);
        $meta = preg_replace('/\b\d+(?:,\d+)?\s+(?:to|[-–])\s+\d+(?:,\d+)?\s+pesos?(?:\s+per\s+person)?/i', '', $meta);
        $meta = preg_replace('/\b\d+(?:,\d+)?\s+pesos?\b/i', '', $meta);
        $meta = preg_replace('/\s+/', ' ', $meta);
        return trim($meta);
    }

    private function extractArea(string $phrase): string
    {
        $p = mb_strtolower(trim($phrase));
        $p = preg_replace('/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/i', '', $p);
        $p = preg_replace('/^philippines\s+|^antonio\'?s\s+/', '', $p);
        if (preg_match('/(?:restaurant|to\s+eat)\s+(?:in|at|near)\s+(.+)$/', $p, $m)) $p = trim($m[1]);
        elseif (preg_match('/^where\s+to\s+eat\s+(.+)$/', $p, $m)) $p = trim($m[1]);
        $p = preg_replace('/\s+(philippines|with\s+view)$/', '', $p);
        $words = preg_split('/\s+/', trim($p));
        $small = ['of','the','in','at','on','and','a','an','to','for','by'];
        foreach ($words as $i => $w) {
            $words[$i] = ($i === 0 || !in_array(mb_strtolower($w), $small, true))
                ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8')
                : mb_strtolower($w);
        }
        return implode(' ', $words) ?: 'this area';
    }

    private function detectType(string $cluster): string
    {
        return match (true) {
            in_array($cluster, ['metro-manila'], true) => 'mall',  // default for NCR
            in_array($cluster, ['cavite','batangas','laguna','rizal','bulacan','pampanga','quezon','bicol','north-luzon','visayas','mindanao','palawan'], true) => 'destination',
            default => 'district',
        };
    }
}
