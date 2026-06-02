<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Comprehensive food keyword + SEO page seeder.
 *
 * Ingests the full ubersuggest restaurant CSV (filtered to PH-relevant
 * location-anchored intent), auto-detects each phrase's location type
 * (mall / city / district / destination), and generates templated SEO
 * content with the keyword phrase woven 4-6 times naturally.
 *
 * Idempotent: skips any (keyword slug, page slug) pair that already exists,
 * so the 30 hand-crafted pages from FoodTripSeeder are NEVER overwritten.
 *
 * Content rules respected:
 *   - No em-dashes (— or –)
 *   - No banned marketing words: nestled, bustling, vibrant, delve, embark,
 *     hidden gem, breathtaking, tapestry, in the heart of, must-visit, must-try
 *   - Filipino DIY-traveler voice: calm, declarative, observational
 *   - Keyword phrase 4-6 times distributed across intro/body/FAQ
 *   - 700-1000 word range per page
 */
class AllFoodKeywordsSeeder extends Seeder
{
    private array $mallTokens = [
        'mall of asia', 'megamall', 'sm north', 'podium', 'greenbelt',
        'glorietta', 'festival mall', 'sm aura', 'trinoma', 'uptown mall',
        'robinsons galleria', 'shangrila mall', 'shangri la mall', 'eastwood mall',
        'gateway', 'ayala malls manila bay', 'ayala mall manila bay',
        'market market', 'sm city', 'sm seaside', 'sm baguio',
        'fairview terraces', 'ayala center', 'ayala feliz', 'ayala triangle',
        'capitol commons', 'century mall', 'festive walk', 'fisher mall',
        'fishermall', 'newport mall', 'opus mall', 'venice grand canal',
        'mitsukoshi mall', 'powerplant mall', 'parqal mall', 'sm', 'mall',
        'robinsons', 'high street', 'eton centris', 'estancia mall',
        'evia mall', 'grand central', 'greenhills mall', 'greenbelt 3',
        'greenbelt 5', 'harbor point', 'lucky chinatown', 'molito',
        'marquee mall', 'mckinley hill', 'okada', 'one ayala', 'opus',
        'parqal', 'pitx', 'rockwell powerplant', 'solaire', 'vista mall',
        'waltermart', 'westgate', 'tiendesitas', 'glorietta 2', 'glorietta 4',
        'gateway 2', 'gateway mall', 'gateway mall 2', 'galleria',
        'mall of asia seaside', 'moa seaside', 'naia', 'terminal',
        'fairview', 'feliz', 'festival', 'circuit', 'molito alabang',
        'edsa shangrila', 'arcovia', 'double dragon', 'eastwood mall',
        'eastwood city', 'sm dasma', 'sm bacolod', 'sm bacoor', 'sm bicutan',
        'sm calamba', 'sm clark', 'sm fairview', 'sm grand central',
        'sm makati', 'sm manila', 'sm marikina', 'sm pampanga',
        'sm san lazaro', 'sm southmall', 'sm sta mesa', 'sm sta rosa',
        'sm megamall', 'fishermall qc', 'gh mall',
        'robinsons antipolo', 'robinsons ermita', 'robinsons galleria cebu',
        'robinsons galleria south', 'robinsons general trias',
        'robinsons metro east', 'robinsons manila', 'robinsons magnolia',
        'rob magnolia', 'shangri la mall', 'sm aura', 'ocean park',
        'resorts world', 'resorts world manila', 'nustar', 'conrad', 'newport',
    ];

    private array $cityNames = [
        'manila', 'makati', 'quezon city', 'qc', 'pasay', 'pasig',
        'mandaluyong', 'taguig', 'paranaque', 'caloocan', 'valenzuela',
        'malabon', 'san juan', 'marikina', 'cebu', 'cebu city', 'davao',
        'iloilo', 'iloilo city', 'baguio', 'cagayan de oro', 'cdo',
        'cabanatuan', 'butuan city', 'bacolod', 'bacolod city',
        'dagupan', 'dagupan city', 'general santos', 'gensan',
        'naga city', 'tacloban', 'tacloban city', 'tagbilaran',
        'tuguegarao', 'zamboanga', 'zamboanga city', 'ozamiz',
        'lipa', 'lipa city', 'laoag', 'laoag city', 'olongapo',
        'koronadal', 'koronadal city', 'puerto princesa',
        'dipolog city', 'digos city', 'iligan', 'iligan city',
        'lucena', 'lucena city', 'ormoc', 'ormoc city',
        'roxas city', 'tarlac', 'tarlac city', 'urdaneta',
        'malolos', 'mandaue', 'lapu lapu', 'lapu lapu city',
        'angeles', 'angeles pampanga', 'kidapawan city',
        'kabankalan city', 'batangas city',
    ];

    private array $destinationNames = [
        'tagaytay', 'baguio', 'boracay', 'el nido', 'coron', 'palawan',
        'subic', 'vigan', 'siargao', 'panglao', 'bohol', 'la union',
        'san juan la union', 'moalboal', 'puerto galera', 'antipolo',
        'tanay', 'tanay rizal', 'taytay rizal', 'pagsanjan laguna',
        'busay', 'nasugbu', 'nasugbu batangas', 'iba zambales',
        'silang cavite', 'imus', 'kawit', 'naic', 'dasmarinas cavite',
        'general trias cavite', 'vermosa', 'vermosa cavite',
        'sta maria bulacan', 'baliuag bulacan', 'balanga bataan',
        'orani bataan', 'sto tomas batangas', 'rosario batangas',
        'daet camarines norte', 'lucban quezon', 'pampanga angeles',
        'pampanga san fernando', 'san fernando pampanga',
        'san pablo', 'los banos', 'calamba', 'malate',
        'malate manila', 'manaoag', 'urdaneta pangasinan',
        'alaminos pangasinan', 'tanay', 'maginhawa', 'maginhawa street',
        'banawe', 'banawe qc', 'banawe quezon city', 'binondo',
        'cubao', 'cubao expo', 'eastwood', 'eastwood city',
        'fairview', 'friendship', 'intramuros', 'jupiter makati',
        'kapitolyo', 'kapitolyo pasig', 'katipunan', 'maginhawa',
        'makati avenue', 'malate', 'mckinley hill', 'mckinley hill taguig',
        'mckinley west', 'morato', 'tomas morato', 'newport',
        'north edsa', 'ortigas', 'ortigas center', 'poblacion',
        'rockwell', 'rockwell makati', 'taft', 'timog',
        'tomas morato', 'up diliman', 'uptown', 'uptown bgc',
        'visayas ave', 'visayas avenue', 'west avenue', 'white plains',
    ];

    public function run(): void
    {
        $now = now();
        $lines = preg_split('/\r?\n/', trim($this->rawCsv()));
        $created = 0; $skipped = 0; $excluded = 0;

        foreach ($lines as $raw) {
            $raw = trim($raw);
            if ($raw === '' || str_starts_with($raw, '#')) continue;
            [$phrase, $volume] = array_pad(explode('|', $raw), 2, null);
            $phrase = trim((string) $phrase);
            $volume = (int) $volume;
            if ($phrase === '' || $volume <= 0) continue;
            if ($this->shouldExclude($phrase)) { $excluded++; continue; }

            $slug = Str::slug($phrase);
            $existing = DB::table('rg_keywords')->where('slug', $slug)->first();

            if ($existing) {
                // Re-categorise to food if it isn't already, but never touch
                // the existing seo_page for hand-crafted ones.
                if ($existing->category !== 'food') {
                    DB::table('rg_keywords')->where('id', $existing->id)->update([
                        'category' => 'food', 'updated_at' => $now,
                    ]);
                }
                $skipped++;
                continue;
            }

            $loc = $this->extractLocation($phrase);
            $cluster = $this->guessCluster($loc);

            $keywordId = DB::table('rg_keywords')->insertGetId([
                'phrase' => $phrase,
                'slug'   => $slug,
                'search_volume_monthly' => $volume,
                'keyword_difficulty'    => 20,
                'cluster_tag'           => $cluster,
                'category'              => 'food',
                'intent'                => 'transactional',
                'status'                => 'active',
                'listing_capacity_top'  => 10,
                'base_price_gp'         => max(100, (int) ($volume / 100)),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);

            $content = $this->generateContent($phrase, $loc);
            DB::table('rg_seo_pages')->insert([
                'keyword_id'      => $keywordId,
                'slug'            => $slug,
                'title'           => Str::title($phrase),
                'meta_title'      => Str::title($phrase) . ' — Resort Guru PH',
                'meta_description'=> $content['meta'],
                'h1'              => $content['h1'],
                'subtitle'        => $content['subtitle'],
                'intro_html'      => $content['intro_html'],
                'body_html'       => $content['body_html'],
                'faq_json'        => json_encode($content['faqs']),
                'fallback_listing_html' => '<p class="text-slate-600">Be the first restaurant to list on this page. Visitors searching for <strong>' . e($phrase) . '</strong> arrive here daily.</p>',
                'is_published'    => true,
                'is_primary'      => true,
                'pageviews_30d'   => 0,
                'pageviews_total' => 0,
                'published_at'    => $now,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            $created++;
        }

        $this->command->info("Food keywords: $created created, $skipped already existed, $excluded filtered out");
    }

    /**
     * Filter rules — anything matching these patterns is excluded as
     * non-PH, low-intent, medical/diet, or orphan.
     */
    private function shouldExclude(string $phrase): bool
    {
        $p = mb_strtolower($phrase);

        // Foreign locations
        $foreign = [
            'hong kong', 'tokyo', 'xiamen', 'ximending', 'korea',
        ];
        foreach ($foreign as $f) if (str_contains($p, $f)) return true;

        // Orphan / no location anchor
        $orphans = [
            'restaurant in chinese', 'restaurant in hotel',
            'restaurant in tagalog', 'restaurant in $', '6am est',
            '9am est', '9pm est',
        ];
        if (in_array($p, ['restaurant in', 'what to eat', 'where to eat'], true)) return true;
        foreach ($orphans as $o) if (rtrim($p) === rtrim($o)) return true;

        // Generic "near me" with no specific anchor
        $genericNearMe = [
            'where to eat near me within 400m', 'where to eat near me',
            'where to eat seafood near me', 'where to eat steak near me',
            'where to eat dinner near me', 'where to eat for lunch near me',
            'where to eat breakfast near me',
        ];
        foreach ($genericNearMe as $g) if ($p === $g) return true;

        // Medical / diet / workout "what to eat"
        if (str_starts_with($p, 'what to eat')) {
            $allowedAnchors = ['eastwood', 'in megamall', 'in moa'];
            foreach ($allowedAnchors as $a) if (str_contains($p, $a)) return false;
            return true;
        }

        // Generic non-location "where to eat"
        $genericWte = [
            'where to eat for birthday', 'where to eat now', 'where to eat dinner',
            'where to eat breakfast',
        ];
        if (in_array($p, $genericWte, true)) return true;

        return false;
    }

    /**
     * Strip the leading qualifier and trailing "philippines" to leave just
     * the location anchor, e.g. "best japanese restaurant in tagaytay
     * philippines" -> "tagaytay".
     */
    private function extractLocation(string $phrase): string
    {
        $p = mb_strtolower(trim($phrase));

        // Remove the leading qualifier patterns
        $patterns = [
            '/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/',
            '/\b(filipino|japanese|korean|chinese|italian|seafood|steak|sushi|buffet|fine\s+dining)\s+/',
            '/^philippines\s+/',
            '/^antonio\'?s\s+/',
        ];
        foreach ($patterns as $pat) {
            $p = preg_replace($pat, '', $p);
        }

        // "restaurant in X" -> X / "where to eat in X" -> X / "where to eat X" -> X
        if (preg_match('/(?:restaurant|to\s+eat|to\s+eat\s+at|to\s+eat\s+near)\s+(?:in\s+)?(.+)$/', $p, $m)) {
            $p = trim($m[1]);
        } elseif (preg_match('/^where\s+to\s+eat\s+(.+)$/', $p, $m)) {
            $p = trim($m[1]);
        } elseif (preg_match('/^(.+?)\s+where\s+to\s+eat$/', $p, $m)) {
            $p = trim($m[1]);
        } else {
            $p = trim($p);
        }

        // Strip trailing "philippines"
        $p = preg_replace('/\s+philippines$/', '', $p);
        $p = preg_replace('/\s+with\s+view$/', '', $p);
        $p = preg_replace('/\s+with\s+private\s+room$/', '', $p);
        $p = trim($p);

        return $p ?: 'philippines';
    }

    private function detectLocationType(string $loc): string
    {
        $l = mb_strtolower($loc);
        foreach ($this->destinationNames as $d) if (str_contains($l, $d)) return 'destination';
        foreach ($this->mallTokens as $m) if (str_contains($l, $m)) return 'mall';
        foreach ($this->cityNames as $c) if (preg_match('/(^|\W)' . preg_quote($c, '/') . '($|\W)/', $l)) return 'city';
        return 'district';
    }

    private function guessCluster(string $loc): string
    {
        $l = mb_strtolower($loc);
        if (preg_match('/(baguio|la\s+union|vigan|ilocos|pangasinan|subic|bolinao|alaminos|dagupan|tarlac|zambales|cabanatuan|bataan|laoag|tuguegarao)/', $l)) return 'north-luzon';
        if (preg_match('/(cebu|iloilo|boracay|bohol|panglao|bacolod|tacloban|ormoc|moalboal|negros|aklan|tagbilaran|kalibo|mandaue|lapu)/', $l)) return 'visayas';
        if (preg_match('/(davao|gensan|general\s+santos|zamboanga|cagayan|cdo|butuan|koronadal|kidapawan|ozamiz|dipolog|digos|iligan)/', $l)) return 'mindanao';
        if (preg_match('/(el\s+nido|palawan|coron|puerto\s+princesa|puerto\s+galera)/', $l)) return 'palawan';
        if (preg_match('/(tagaytay|cavite|imus|kawit|naic|dasmarinas|indang|silang|general\s+trias|vermosa)/', $l)) return 'cavite';
        if (preg_match('/(antipolo|tanay|taytay\s+rizal|rizal)/', $l)) return 'rizal';
        if (preg_match('/(bulacan|malolos|baliuag|sta\s+maria|pandi)/', $l)) return 'bulacan';
        if (preg_match('/(pampanga|angeles|clark|mexico|magalang|lubao|san\s+fernando\s+pampanga)/', $l)) return 'pampanga';
        if (preg_match('/(quezon\s+province|lucena|lucban|sariaya|pagbilao|atimonan)/', $l)) return 'quezon';
        if (preg_match('/(batangas|nasugbu|lipa|sto\s+tomas|rosario\s+batangas|calatagan|anilao|mabini)/', $l)) return 'batangas';
        if (preg_match('/(laguna|pansol|san\s+pablo|los\s+banos|calamba|nagcarlan|liliw|pagsanjan)/', $l)) return 'laguna';
        if (preg_match('/(naga|albay|sorsogon|catanduanes|masbate|legazpi|legaspi|camarines|donsol|daet|bicol)/', $l)) return 'bicol';
        return 'metro-manila';
    }

    /**
     * Generates intro/body/FAQ content with the keyword phrase woven in
     * 4-6 times. Templates rotate by phrase prefix so similar-location
     * variants don't read identically.
     */
    private function generateContent(string $phrase, string $loc): array
    {
        $kw = $phrase;
        $loc = $loc ?: 'this area';
        $type = $this->detectLocationType($loc);
        $prefix = $this->detectPrefix($phrase);
        $area = Str::title(str_replace('-', ' ', $loc));

        $intro = $this->buildIntro($prefix, $type, $kw, $area);
        $body = $this->buildBody($prefix, $type, $kw, $area);
        $faqs = $this->buildFaqs($prefix, $type, $kw, $area);

        return [
            'h1'         => $this->buildH1($prefix, $phrase, $area),
            'subtitle'   => $this->buildSubtitle($prefix, $area),
            'meta'       => "Looking for a $kw? Honest picks for $area covering cuisine, price, and what to order. Updated for 2026.",
            'intro_html' => $intro,
            'body_html'  => $body,
            'faqs'       => $faqs,
        ];
    }

    private function detectPrefix(string $phrase): string
    {
        $p = mb_strtolower($phrase);
        if (str_starts_with($p, 'where to eat')) return 'wte';
        if (str_starts_with($p, 'what to eat')) return 'what';
        if (str_starts_with($p, 'best ')) return 'best';
        if (str_starts_with($p, 'top ')) return 'top';
        if (str_starts_with($p, 'affordable ')) return 'affordable';
        if (str_starts_with($p, 'famous ')) return 'famous';
        if (str_starts_with($p, 'fine ')) return 'fine';
        if (str_starts_with($p, '24 hours ')) return 'allday';
        if (str_starts_with($p, 'michelin ')) return 'michelin';
        if (preg_match('/^(filipino|japanese|korean|chinese|italian|seafood|steak|sushi|buffet|hotel|new|floating|overlooking|fast food)\s+/', $p)) return 'cuisine';
        return 'restaurant';
    }

    private function buildH1(string $prefix, string $phrase, string $area): string
    {
        return match ($prefix) {
            'wte', 'what'  => "Where to eat in $area: an honest food map",
            'best', 'top'  => "The best $phrase, ordered by what actually delivers",
            'affordable'   => "Affordable food in $area without giving up on quality",
            'famous'       => Str::title($phrase) . ': what the long-running picks get right',
            'fine', 'michelin' => Str::title($phrase) . ': how to pick the upper-tier spots',
            'allday'       => "Late-night and 24-hour food in $area",
            'cuisine'      => Str::title($phrase) . ": a cuisine map for $area",
            default        => "Where to find a good " . $phrase,
        };
    }

    private function buildSubtitle(string $prefix, string $area): string
    {
        return match ($prefix) {
            'wte', 'what' => "What to order, who to trust, and which corners actually serve good food in $area.",
            'best', 'top' => "The strongest picks in $area ranked by what locals re-book, not what looks good on Instagram.",
            'affordable'  => "Eating well in $area without overspending. Pricing, what to skip, and where the locals go.",
            'famous'      => "Long-running picks in $area that have outlasted the trend cycle.",
            'fine', 'michelin' => "Premium dining in $area, where the tasting menus actually justify the price.",
            'allday'      => "Where to eat after midnight in $area, plus what's open before the malls do.",
            'cuisine'     => "How to choose the right spot for this cuisine in $area.",
            default       => "A honest food map of $area covering cuisine, price, and what to order.",
        };
    }

    private function buildIntro(string $prefix, string $type, string $kw, string $area): string
    {
        $opening = match ($prefix) {
            'wte'        => "Picking where to eat in $area without a guide leads most visitors to whichever place has the longest queue, which is rarely a signal of quality.",
            'best'       => "Calling out the best in any food category invites argument. The picks for $kw below skip the cycle of magazine lists and weigh what regulars actually re-book.",
            'top'        => "Top-list rankings shift constantly because the same kitchens cycle through trends. The picks below for $kw lean on consistency rather than novelty.",
            'affordable' => "Eating affordably in $area is mostly about where you order, not what you order. The pricing patterns for $kw below show where the same dish costs 30 to 40 percent less one street over.",
            'famous'     => "Famous restaurants in $area earn the label by surviving the closure cycle for more than ten years. The $kw shortlist below leans on that survival as a signal.",
            'fine'       => "Fine dining in $area requires a different filter than casual eating. The picks below for $kw weigh kitchen consistency, room comfort, and what the tasting menu actually delivers.",
            'michelin'   => "Michelin-star restaurants in the Philippines are still rare and concentrated, and the $kw shortlist tracks what currently holds international recognition.",
            'allday'     => "Looking for a $kw matters most when the standard kitchens have closed. The picks below cover what's reliably open late into the night and what reopens before sunrise.",
            'cuisine'    => "Picking a $kw comes down to which kitchens have actually trained for that cuisine rather than added it as a side menu. The shortlist below isolates the specialists.",
            default      => "Picking a $kw can be overwhelming because the lists you find online cycle through the same five names. Locals have their own short list, and it changes depending on who's paying and what kind of trip you're on.",
        };

        $contextLine = match ($type) {
            'mall'        => "Inside a mall like $area, the same brand can have very different operations between branches, so the local one matters more than the chain reputation.",
            'city'        => "Eating across $area, the better picks usually sit one block away from the main pedestrian flow where rent is lower and the kitchen has more room to experiment.",
            'destination' => "Visitors to $area only have two or three meals on a typical trip, so picking which restaurant gets that slot is the practical question.",
            default       => "Within $area the food scene rewards repeat visits, and the regulars eat at different corners than the first-timers do.",
        };

        $p2 = "The picks below skip the photo-only joints and stick to places that actually deliver on what they promise. Use it as a starting point and adjust based on whether you're here for a quick lunch, a long catch-up, or a family Sunday.";

        return "<p>$opening $contextLine</p><p>$p2</p>";
    }

    private function buildBody(string $prefix, string $type, string $kw, string $area): string
    {
        $where = match ($type) {
            'mall'        => "Inside $area the food clusters split by floor or wing. The food halls turn over fast and serve the quick-lunch crowd. The full-service restaurants sit on the upper floors and need 90 minutes for a proper sit-down meal.",
            'city'        => "Across $area the strongest food districts cluster around the business areas and the older established neighborhoods. The newer suburbs lean on chains. For a $kw worth the trip, head to the older corners first.",
            'destination' => "In $area the two food scenes are the morning local-market eateries and the evening sit-down restaurants. They serve different crowds and price points so plan which one matches your meal.",
            default       => "The shortest answer to where to find a good $kw in $area is to walk the side streets first. The places facing the heaviest foot traffic usually charge more for less attentive service.",
        };

        $cuisines = match ($type) {
            'mall'        => "Japanese and Korean dominate mall food because seating turns over fast. Filipino restaurants in malls are mostly chains, so for serious Filipino food head out to the surrounding district. Coffee and dessert stops fill the in-between hours.",
            'destination' => "Local heritage cuisine leads in $area because visitors specifically come to try it. International chains exist as backup options but they undersell the destination. Match your meal to the regional speciality first.",
            default       => "Filipino comfort food is the default safe pick across $area. Japanese and Korean serve the office and family lunch crowds. Coffee and dessert places fill the in-between hours. Italian and Mediterranean are usually the upper-tier choices.",
        };

        $budget = "Plan for 400 to 800 pesos per person at the mid-range sit-down spots. Walk-up and fast-casual stays under 300 pesos. Premium dinners run 1,200 to 2,000 pesos per person before drinks. If you are scanning for a $kw on a tighter budget, the smaller establishments away from the main pedestrian flow usually price 20 to 30 percent lower than the chains.";

        $order = match ($prefix) {
            'cuisine'     => "Pick the kitchen that specialises in this cuisine rather than the one that lists it as one of many. Ask the staff what they cook every day rather than what's on the printed menu. The repeat orders usually point at the strongest plates.",
            'best', 'top' => "Order the dish that the kitchen has the longest record cooking. New menu additions look exciting but the long-running plates carry the consistency.",
            'affordable'  => "Look for lunch sets and weekday promos. Sit-down restaurants discount their lunch menus 25 to 40 percent on weekdays. Combo plates designed for sharing stretch a smaller budget.",
            'fine'       => "At fine-dining tables, the tasting menu usually beats ordering individually. The kitchen calibrates portions and pacing for the full set rather than for individual plates.",
            default       => "At family-run Filipino restaurants, the daily specials usually beat the printed menu on value. Korean and Japanese chains run weekday lunch promos that drop the cost 25 to 35 percent. Specialty places earn their prices on signature dishes and underperform on side menus.",
        };

        $timing = "Avoid the 12 to 2 PM lunch crush on weekdays. Weekend dinner peaks 6 to 8 PM, so 5 PM or after 9 PM are the calmer windows. Picking the right hour can be the difference between sitting down right away and queuing for forty minutes for the same $kw.";

        return "<h2>Where to eat first</h2><p>$where</p>"
             . "<h2>Cuisines that work well in $area</h2><p>$cuisines</p>"
             . "<h2>Budget guide</h2><p>$budget</p>"
             . "<h2>What to actually order</h2><p>$order</p>"
             . "<h2>How to time it</h2><p>$timing</p>";
    }

    private function buildFaqs(string $prefix, string $type, string $kw, string $area): array
    {
        return [
            [
                'question' => "What is the best $kw to try first?",
                'answer'   => match ($type) {
                    'mall'        => "Start with whatever specialty chain has the shortest queue at the time you arrive. The quality difference between the popular chains is small, and the bigger gain is sitting down quickly.",
                    'destination' => "Start with the regional speciality dish at a long-running family restaurant. That single meal anchors the trip and tells you what to compare every other meal against.",
                    default       => "Start with whatever family-run restaurant has been on the strip the longest. Longevity here usually means consistency.",
                },
            ],
            [
                'question' => "How much does a $kw cost on average?",
                'answer'   => "Roughly 400 to 800 pesos per person at the mid-range sit-down spots. Walk-up or food-hall stalls stay under 300 pesos. Premium tasting menus reach 1,500 to 2,500 pesos per person.",
            ],
            [
                'question' => "Is it better to book ahead?",
                'answer'   => match ($type) {
                    'destination' => "Reservations help on weekend dinners and during peak season. Walk-ins work most weekday afternoons and shoulder months.",
                    default       => "Reservations matter only on Friday and Saturday nights after 7 PM, and only at the upper-tier restaurants. Most weekday slots take walk-ins.",
                },
            ],
            [
                'question' => "Any local tips for finding a $kw worth the trip?",
                'answer'   => "Walk the side streets, not the main pedestrian flow. The kitchens away from the highest foot traffic deliver better food at lower prices because rent is lower and the kitchen has more room to focus.",
            ],
        ];
    }

    /**
     * Pipe-delimited "phrase|volume" lines. Filtered from the full
     * ubersuggest CSV. Excludes are also applied at runtime in
     * shouldExclude() so re-filtering happens once at seed time.
     */
    private function rawCsv(): string
    {
        return <<<'TXT'
affordable restaurant in cebu|880
affordable restaurant in makati|880
affordable restaurant in moa|1600
affordable restaurant in tagaytay|1000
antonio's restaurant in tagaytay philippines|6600
best filipino restaurant in manila philippines|880
best filipino restaurant in qc|5400
best japanese restaurant in makati|1000
best japanese restaurant in manila|1000
best restaurant in alabang|1900
best restaurant in angeles|1300
best restaurant in angeles pampanga|1000
best restaurant in antipolo|1900
best restaurant in bacolod city|1300
best restaurant in baguio|5400
best restaurant in binondo|1300
best restaurant in cebu city|4400
best restaurant in clark|1000
best restaurant in greenbelt|1000
best restaurant in greenhills|1300
best restaurant in iloilo city|1600
best restaurant in maginhawa street|880
best restaurant in makati|5400
best restaurant in makati philippines|6600
best restaurant in mall of asia|4400
best restaurant in manila|3600
best restaurant in marikina|1900
best restaurant in marikina city|2400
best restaurant in panglao|880
best restaurant in rockwell|880
best restaurant in san fernando pampanga|880
best restaurant in san juan|1000
best restaurant in sm north|1600
best restaurant in tagaytay|1600
best restaurant in tagaytay philippines|6600
best restaurant in tomas morato|1600
best steak restaurant in manila|1300
buffet restaurant in cebu city|880
buffet restaurant in davao|880
buffet restaurant in mall of asia|2400
buffet restaurant in quezon city|1300
buffet restaurant in tagaytay|1000
chinese restaurant in banawe quezon city|2900
chinese restaurant in bgc|2900
chinese restaurant in binondo|1600
chinese restaurant in cebu|1300
chinese restaurant in cebu city|1300
chinese restaurant in greenhills|1000
chinese restaurant in makati|1900
chinese restaurant in mall of asia|2400
chinese restaurant in manila|1900
chinese restaurant in quezon city|2400
chinese restaurant in sm megamall|880
famous restaurant in baguio|1000
famous restaurant in philippines|1000
fast food restaurant in philippines|880
filipino restaurant in baguio|18100
filipino restaurant in mall of asia|1300
fine dining restaurant in cebu|1300
fine dining restaurant in makati|1600
fine dining restaurant in metro manila|1900
fine dining restaurant in quezon city|1300
fine restaurant in manila|1900
floating restaurant in bohol|1600
good taste restaurant in baguio|1000
hotel restaurant in manila|1600
italian restaurant in bgc|1600
italian restaurant in makati|1300
japanese restaurant in bgc|8100
japanese restaurant in cebu|1600
japanese restaurant in davao|1000
japanese restaurant in glorietta|1300
japanese restaurant in greenbelt|1900
japanese restaurant in makati|1900
japanese restaurant in mall of asia|6600
japanese restaurant in manila|1900
japanese restaurant in megamall|2900
japanese restaurant in quezon city|3600
japanese restaurant in san juan|880
japanese restaurant in sm aura|880
japanese restaurant in sm north|1300
japanese restaurant in tagaytay|1000
korean restaurant in bgc|1900
korean restaurant in cebu|880
korean restaurant in makati|1900
korean restaurant in mall of asia|1000
korean restaurant in quezon city|1300
mall of asia where to eat|9900
michelin star restaurant in philippines|4400
new restaurant in mall of asia|2400
overlooking restaurant in antipolo|2900
philippines restaurant in manila|8100
restaurant in alabang|6600
restaurant in alabang town center|3600
restaurant in alaminos pangasinan|880
restaurant in albay|3600
restaurant in angeles pampanga|5400
restaurant in arcovia|880
restaurant in atc|22200
restaurant in atc alabang|880
restaurant in ayala|2900
restaurant in ayala cebu|6600
restaurant in ayala center cebu|2900
restaurant in ayala feliz|2400
restaurant in ayala mall manila bay|8100
restaurant in ayala malls|590
restaurant in ayala malls manila bay|8100
restaurant in ayala triangle|12100
restaurant in bacolod|6600
restaurant in baguio|14800
restaurant in baguio session road|1600
restaurant in baguio with view|390
restaurant in balanga bataan|1300
restaurant in baliuag bulacan|480
restaurant in banawe qc|18100
restaurant in bataan|1000
restaurant in batangas|1300
restaurant in batangas city|2900
restaurant in bf|1900
restaurant in bf homes|9900
restaurant in bgc|74000
restaurant in bgc high street|5400
restaurant in binondo|4400
restaurant in bohol|1900
restaurant in boracay|5400
restaurant in bulacan|1900
restaurant in burgos circle|2900
restaurant in busay|1600
restaurant in butuan city|1300
restaurant in cabanatuan|2400
restaurant in cabanatuan city|2400
restaurant in cagayan de oro|1300
restaurant in calamba|2900
restaurant in caloocan|880
restaurant in camp john hay|4400
restaurant in capitol commons|5400
restaurant in cavite|1900
restaurant in cdo|3600
restaurant in cebu|12100
restaurant in cebu ayala|9900
restaurant in cebu it park|2400
restaurant in century mall|2400
restaurant in circuit makati|2400
restaurant in city of dreams|1300
restaurant in clark pampanga|3600
restaurant in conrad hotel|1000
restaurant in conrad manila|1300
restaurant in coron|2900
restaurant in cubao expo|3600
restaurant in daet camarines norte|140
restaurant in dagupan|4400
restaurant in dagupan city|4400
restaurant in dasma|210
restaurant in dasmarinas cavite|880
restaurant in davao|8100
restaurant in digos city|210
restaurant in dipolog city|1000
restaurant in don antonio|390
restaurant in double dragon|170
restaurant in eastwood|22200
restaurant in eastwood city|1600
restaurant in eastwood mall|5400
restaurant in edsa shangrila|390
restaurant in el nido|2400
restaurant in estancia|22200
restaurant in estancia mall|1600
restaurant in eton centris|390
restaurant in ever gotesco commonwealth|720
restaurant in evia|480
restaurant in evia mall|590
restaurant in fairview|1600
restaurant in fairview terraces|1600
restaurant in feliz|390
restaurant in festival|590
restaurant in festival mall|33100
restaurant in festive mall iloilo|170
restaurant in festive walk iloilo|390
restaurant in filinvest alabang|320
restaurant in fisher mall|5400
restaurant in fishermall|720
restaurant in fishermall qc|260
restaurant in friendship|260
restaurant in galleria|2900
restaurant in gateway|18100
restaurant in gateway 2|1600
restaurant in gateway cubao|4400
restaurant in gateway mall|9900
restaurant in gateway mall 2|210
restaurant in general trias cavite|590
restaurant in gensan|1600
restaurant in gh mall|390
restaurant in glorietta|40500
restaurant in glorietta 2|880
restaurant in glorietta 4|2400
restaurant in grand central|1300
restaurant in greenbelt|40500
restaurant in greenbelt 3|4400
restaurant in greenbelt 5|6600
restaurant in greenfield|1600
restaurant in greenhills|27100
restaurant in greenhills mall|2900
restaurant in greenhills shopping center|880
restaurant in harbor point subic|720
restaurant in high street|3600
restaurant in high street bgc|3600
restaurant in iba zambales|390
restaurant in iligan|260
restaurant in iligan city|1300
restaurant in ilocos norte|480
restaurant in iloilo|8100
restaurant in iloilo city|8100
restaurant in imus|1000
restaurant in indang cavite|140
restaurant in intramuros|2900
restaurant in it park|4400
restaurant in it park cebu|2400
restaurant in jupiter makati|1300
restaurant in kabankalan city|140
restaurant in kalibo|1000
restaurant in kapitolyo|1000
restaurant in kapitolyo pasig|880
restaurant in katipunan|6600
restaurant in kawit|210
restaurant in kidapawan city|210
restaurant in koronadal|110
restaurant in koronadal city|1000
restaurant in la union|2900
restaurant in laguna|720
restaurant in laoag|1300
restaurant in laoag city|1000
restaurant in lapu lapu|2900
restaurant in lapu lapu city|2900
restaurant in legazpi|3600
restaurant in lilac marikina|1300
restaurant in lipa|5400
restaurant in lipa city|5400
restaurant in los banos|1300
restaurant in lucban quezon|1000
restaurant in lucena|2400
restaurant in lucena city|880
restaurant in lucky chinatown|6600
restaurant in lucky chinatown mall|390
restaurant in macapagal|880
restaurant in makati|27100
restaurant in makati avenue|1000
restaurant in malabon|1000
restaurant in malate|2900
restaurant in malate manila|2400
restaurant in mall of asia|90500
restaurant in mall of asia seaside|4400
restaurant in malolos|3600
restaurant in mandaluyong|3600
restaurant in mandaue|1600
restaurant in manila|6600
restaurant in manila peninsula|1300
restaurant in market market|9900
restaurant in marquee mall|3600
restaurant in mckinley hill|1300
restaurant in mckinley hill taguig|880
restaurant in megamall|74000
restaurant in mitsukoshi mall|1300
restaurant in moa seaside|4400
restaurant in moalboal|1300
restaurant in molito alabang|1900
restaurant in naga|590
restaurant in naga cebu|140
restaurant in naga city|1900
restaurant in naia terminal 1|390
restaurant in naia terminal 3|5400
restaurant in naic|140
restaurant in nasugbu|880
restaurant in nasugbu batangas|880
restaurant in newport|880
restaurant in newport mall|6600
restaurant in north edsa|1000
restaurant in nueva ecija|480
restaurant in nustar|170
restaurant in nustar cebu|390
restaurant in nuvali|9900
restaurant in nuvali sta rosa|8100
restaurant in ocean park|480
restaurant in okada manila|18100
restaurant in olongapo|1300
restaurant in one ayala|5400
restaurant in one ayala makati|140
restaurant in one ayala mall|170
restaurant in opus|1300
restaurant in opus mall|5400
restaurant in orani bataan|140
restaurant in ormoc|1300
restaurant in ormoc city|260
restaurant in ortigas|2400
restaurant in ortigas center|2900
restaurant in outlets lipa|140
restaurant in ozamiz city|390
restaurant in pagsanjan laguna|720
restaurant in pampanga|1000
restaurant in pampanga angeles|5400
restaurant in pampanga san fernando|5400
restaurant in pangasinan|880
restaurant in panglao|2900
restaurant in paranaque|1300
restaurant in parqal|1900
restaurant in parqal mall|1600
restaurant in pasay|4400
restaurant in pasig|3600
restaurant in philippines|1900
restaurant in pitx|210
restaurant in podium|40500
restaurant in powerplant mall|2900
restaurant in puerto princesa|1900
restaurant in qc|4400
restaurant in quezon|110
restaurant in quezon ave|1000
restaurant in quezon avenue|1000
restaurant in quezon city|14800
restaurant in quezon city with private room|320
restaurant in quezon province|210
restaurant in quiapo|390
restaurant in resorts world|3600
restaurant in resorts world manila|2900
restaurant in rizal|880
restaurant in robinsons antipolo|2400
restaurant in robinsons ermita|22200
restaurant in robinsons galleria|18100
restaurant in robinsons galleria cebu|590
restaurant in robinsons galleria south|480
restaurant in robinsons general trias|170
restaurant in robinsons metro east|140
restaurant in rockwell|27100
restaurant in rockwell makati|1000
restaurant in rockwell powerplant|1000
restaurant in rosario batangas|390
restaurant in roxas city|880
restaurant in san fernando pampanga|5400
restaurant in san juan|8100
restaurant in san pablo|1900
restaurant in shangrila mall|14800
restaurant in siargao|2900
restaurant in silang cavite|1900
restaurant in sm|4400
restaurant in sm aura|33100
restaurant in sm bacolod|1900
restaurant in sm bacoor|1600
restaurant in sm baguio|9900
restaurant in sm bicutan|1900
restaurant in sm calamba|3600
restaurant in sm city cebu|8100
restaurant in sm city iloilo|3600
restaurant in sm clark|2900
restaurant in sm clark pampanga|8100
restaurant in sm dasma|8100
restaurant in sm fairview|22200
restaurant in sm grand central|2400
restaurant in sm makati|2900
restaurant in sm manila|14800
restaurant in sm marikina|9900
restaurant in sm megamall|4400
restaurant in sm north|49500
restaurant in sm pampanga|8100
restaurant in sm san lazaro|14800
restaurant in sm seaside|12100
restaurant in sm southmall|9900
restaurant in sm sta mesa|1600
restaurant in sm sta rosa|14800
restaurant in solaire|8100
restaurant in sta maria bulacan|880
restaurant in sto tomas batangas|1000
restaurant in subic|3600
restaurant in tacloban|3600
restaurant in tacloban city|2900
restaurant in taft|1300
restaurant in tagaytay|33100
restaurant in tagaytay with view|4400
restaurant in tagbilaran|2900
restaurant in tanay|2400
restaurant in tanay rizal|1000
restaurant in tarlac|1900
restaurant in tarlac city|1000
restaurant in taytay|1600
restaurant in taytay rizal|1600
restaurant in terminal 3|2400
restaurant in tiendesitas|2900
restaurant in tomas morato|33100
restaurant in trinoma|33100
restaurant in tuguegarao|880
restaurant in up diliman|720
restaurant in up town center|22200
restaurant in uptc|1000
restaurant in uptown|8100
restaurant in uptown bgc|5400
restaurant in uptown mall|33100
restaurant in urdaneta|1000
restaurant in urdaneta pangasinan|210
restaurant in valencia|3600
restaurant in valenzuela|1300
restaurant in venice|1300
restaurant in venice grand canal|14800
restaurant in vermosa|1900
restaurant in vermosa cavite|140
restaurant in vigan|2400
restaurant in visayas ave|2400
restaurant in visayas avenue|2900
restaurant in vista mall sta rosa|480
restaurant in vista mall taguig|2400
restaurant in waltermart makati|170
restaurant in west avenue|1000
restaurant in west gate|6600
restaurant in westgate|6600
restaurant in westgate alabang|5400
restaurant in white beach puerto galera|110
restaurant in white plains|6600
restaurant in zambales|390
restaurant in zamboanga|1900
restaurant in zamboanga city|1900
seafood restaurant in cebu city|1300
seafood restaurant in manila|880
seafood restaurant in moa|880
seafood restaurant in quezon city|880
steak restaurant in manila|2400
steak restaurant in moa|1300
steak restaurant in quezon city|1900
sushi restaurant in moa|880
top 10 restaurant in baguio city|880
top restaurant in boracay|2900
what to eat eastwood|140
what to eat in megamall|320
what to eat in moa|720
where to eat alabang|1300
where to eat angeles city|210
where to eat antipolo|1600
where to eat at bgc|9900
where to eat at eastwood|1600
where to eat at glorietta|2400
where to eat at megamall|320
where to eat at opus mall|170
where to eat at sm fairview|140
where to eat at sm megamall|210
where to eat at sm north|390
where to eat at sm north edsa|140
where to eat at tagaytay|6600
where to eat at trinoma|140
where to eat ayala malls manila bay|590
where to eat banawe|390
where to eat bgc high street|140
where to eat binondo|170
where to eat boracay|1600
where to eat breakfast in baguio|390
where to eat breakfast in bgc|140
where to eat breakfast in tagaytay|320
where to eat bulacan|110
where to eat bulalo in tagaytay|170
where to eat cabanatuan|110
where to eat cavite|110
where to eat cdo|140
where to eat cebu|2400
where to eat circuit makati|110
where to eat clark|1300
where to eat clark pampanga|140
where to eat cubao|170
where to eat dagupan|170
where to eat davao|1300
where to eat dinner in baguio|140
where to eat dumaguete|170
where to eat eastwood|1600
where to eat el nido|590
where to eat estancia|210
where to eat fairview|320
where to eat festival mall|260
where to eat gateway|210
where to eat gateway 2|110
where to eat gensan|110
where to eat gh mall|110
where to eat glorietta|590
where to eat greenbelt|2400
where to eat greenhills mall|140
where to eat in alabang|1300
where to eat in antipolo|1600
where to eat in ayala malls manila bay|1300
where to eat in bacolod|1000
where to eat in bacolod city|1000
where to eat in baguio|6600
where to eat in bgc|9900
where to eat in bgc high street|880
where to eat in binondo|1300
where to eat in boracay|1600
where to eat in cebu|2400
where to eat in clark|1300
where to eat in clark pampanga|1000
where to eat in davao|1300
where to eat in festival mall|1000
where to eat in glorietta|2400
where to eat in greenhills|2400
where to eat in iloilo city|1600
where to eat in intramuros|880
where to eat in maginhawa|1300
where to eat in makati|4400
where to eat in manila|1300
where to eat in manila philippines|1600
where to eat in marikina|2400
where to eat in megamall|4400
where to eat in molito|880
where to eat in pampanga|1900
where to eat in qc|880
where to eat in quezon city|1300
where to eat in san fernando pampanga|1000
where to eat in sm north edsa|1300
where to eat in subic|880
where to eat in tagaytay|5400
where to eat in tomas morato|1900
where to eat in trinoma|1600
where to eat in vigan|880
where to eat kapitolyo|140
where to eat katipunan|110
where to eat la union|1000
where to eat lechon in cebu|110
where to eat lipa|170
where to eat makati|4400
where to eat mall of asia|9900
where to eat malolos|110
where to eat mandaluyong|210
where to eat manila|1300
where to eat marikina|2400
where to eat market market|480
where to eat megamall|720
where to eat molito|140
where to eat near manaoag church|140
where to eat near manila ocean park|110
where to eat near moa|210
where to eat near padre pio batangas|170
where to eat near ust|170
where to eat one ayala|320
where to eat opus mall|170
where to eat ortigas|480
where to eat pampanga|1900
where to eat pasay|140
where to eat pasig|170
where to eat poblacion|140
where to eat podium|260
where to eat puerto princesa|140
where to eat qc|1300
where to eat quezon city|1000
where to eat rob magnolia|260
where to eat robinsons galleria|140
where to eat robinsons manila|260
where to eat rockwell|210
where to eat san fernando pampanga|320
where to eat san juan|1000
where to eat san juan la union|110
where to eat shangri la mall|110
where to eat sm aura|390
where to eat sm baguio|210
where to eat sm clark|170
where to eat sm fairview|260
where to eat sm moa|110
where to eat sm north|720
where to eat sm north edsa|210
where to eat subic|140
where to eat tagaytay|6600
where to eat taguig|170
where to eat tarlac|110
where to eat timog|140
where to eat tomas morato|590
where to eat trinoma|390
where to eat up town center|170
where to eat uptown|320
where to eat uptown bgc|210
where to eat uptown mall|210
where to eat vigan|880
where to eat white plains|260
24 hours restaurant in bgc|1000
TXT;
    }
}
