<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Rebuilds the content blocks for every rg_seo_pages row using per-destination
 * data from database/data/destinations.php. Each page's blocks are deleted and
 * regenerated with one of 5 rotating template layouts (decided by slug hash)
 * so no two pages on the same destination feel identical, and no two destinations
 * share H2 ordering. Existing FAQs are preserved if present; otherwise
 * destination-localized FAQs are generated.
 *
 * Goals:
 *   - Localized tourist spots, food, and transit per page
 *   - Multiple per-destination Wikimedia images (from rg_media)
 *   - Natural Filipina content-writer voice; no em-dashes
 *   - Phrase varies, so duplicate detectors see distinct content
 */
class LocalizeContentSeeder extends Seeder
{
    private array $destinations;
    private array $slugToKey;
    private array $clusterToKey;

    public function run(): void
    {
        $this->destinations = require database_path('data/destinations.php');
        $this->buildLookups();

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->select('p.id as page_id', 'p.slug as page_slug', 'p.title', 'p.h1',
                     'k.id as keyword_id', 'k.phrase', 'k.slug as keyword_slug',
                     'k.cluster_tag', 'k.search_volume_monthly')
            ->get();

        $rebuilt = 0;
        $missingDest = 0;

        foreach ($pages as $page) {
            $destKey = $this->resolveDestination($page->keyword_slug, $page->cluster_tag);
            if (!$destKey) {
                $missingDest++;
                $this->command->warn(sprintf('  ? no destination match for "%s"', $page->keyword_slug));
                continue;
            }
            $dest = $this->destinations[$destKey];
            $existingFaqs = $this->loadExistingFaqs($page->page_id);
            $images = $this->loadDestinationImages($destKey);

            DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->page_id)
                ->delete();

            $templateIdx = abs(crc32($page->keyword_slug)) % 5;
            $blocks = $this->buildBlocks($page, $dest, $destKey, $images, $existingFaqs, $templateIdx);

            foreach ($blocks as $i => $block) {
                DB::table('rg_content_blocks')->insert([
                    'owner_type' => 'seo_page',
                    'owner_id' => $page->page_id,
                    'sort_order' => $i + 1,
                    'block_type' => $block['type'],
                    'payload_json' => json_encode($block['payload'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (!empty($images)) {
                // og_image_path stored without /storage/ prefix; the view re-prefixes via asset().
                $rawOg = preg_replace('#^/?storage/#', '', $images[0]);
                DB::table('rg_seo_pages')->where('id', $page->page_id)->update([
                    'og_image_path' => $rawOg,
                    'updated_at' => now(),
                ]);
            }

            $rebuilt++;
            $this->command->info(sprintf('  rebuilt %-44s -> %-22s tpl=%d  (%d blocks)',
                $page->keyword_slug, $destKey, $templateIdx, count($blocks)));
        }

        $this->command->info('');
        $this->command->info("Pages rebuilt: $rebuilt");
        $this->command->info("Pages without a destination match: $missingDest");
    }

    private function buildLookups(): void
    {
        $this->slugToKey = [
            'resort-in-antipolo' => 'antipolo',
            'resort-in-antipolo-private' => 'antipolo',
            'resort-in-tagaytay' => 'tagaytay',
            'resort-in-alfonso-cavite' => 'alfonso',
            'resort-in-amadeo-cavite' => 'amadeo',
            'resort-in-bacoor-cavite' => 'bacoor',
            'resort-in-dasma' => 'dasmarinas',
            'resort-in-imus' => 'imus',
            'resort-in-imus-cavite' => 'imus',
            'resort-in-indang-cavite' => 'indang',
            'resort-in-naic-cavite' => 'naic',
            'resort-in-silang-cavite' => 'silang',
            'resort-in-cavite' => 'tagaytay',
            'resort-in-bulacan' => 'bulacan-province',
            'resort-in-pandi-bulacan' => 'pandi',
            'resort-in-pampanga' => 'pampanga-province',
            'resort-in-angeles-pampanga' => 'angeles',
            'resort-in-arayat-pampanga' => 'arayat',
            'resort-in-batangas' => 'batangas-city',
            'resort-in-batangas-city' => 'batangas-city',
            'resort-in-batangas-with-pool-and-beach' => 'laiya',
            'resort-in-calatagan' => 'calatagan',
            'resort-in-calatagan-batangas' => 'calatagan',
            'resort-in-laiya' => 'laiya',
            'resort-in-san-juan-batangas' => 'laiya',
            'resort-in-lipa' => 'lipa',
            'resort-in-lipa-batangas' => 'lipa',
            'resort-in-lobo-batangas' => 'lobo',
            'resort-in-mabini-batangas' => 'anilao-mabini',
            'resort-in-nasugbu' => 'nasugbu',
            'resort-in-nasugbu-batangas' => 'nasugbu',
            'resort-in-laguna' => 'pansol',
            'resort-in-pansol' => 'pansol',
            'resort-in-calamba-laguna' => 'calamba',
            'resort-in-san-pablo-laguna' => 'san-pablo',
            'resort-in-nagcarlan-laguna' => 'nagcarlan',
            'resort-in-tanay' => 'tanay',
            'resort-in-rodriguez-rizal' => 'rodriguez-montalban',
            'resort-in-binangonan-rizal' => 'binangonan',
            'resort-in-san-mateo-rizal' => 'san-mateo-rizal',
            'resort-in-taytay-rizal' => 'taytay-rizal',
            'resort-in-marikina' => 'marikina',
            'resort-in-rizal' => 'antipolo',
            'resort-in-rizal-province' => 'antipolo',
            'resort-in-lucena-city' => 'lucena',
            'resort-in-sariaya-quezon' => 'sariaya',
            'resort-in-quezon' => 'lucena',
            'resort-in-quezon-province' => 'lucena',
            'resort-in-albay' => 'albay-legazpi',
            'resort-in-naga' => 'naga-camarines-sur',
            'resort-in-naga-city' => 'naga-camarines-sur',
            'resort-in-naga-city-camarines-sur' => 'naga-camarines-sur',
            'resort-in-sorsogon' => 'sorsogon',
            'resort-in-subic' => 'subic',
            'resort-in-subic-zambales' => 'subic',
            'resort-in-morong-bataan' => 'morong-bataan',
            'resort-in-bataan' => 'bataan-province',
            'resort-in-pangasinan' => 'pangasinan-general',
            'resort-in-bolinao' => 'bolinao',
            'beach-resort-in-la-union' => 'la-union',
            'resort-in-la-union' => 'la-union',
            'resort-in-hundred-islands' => 'alaminos-hundred-islands',
            'resort-in-davao' => 'davao-city',
            'resort-in-davao-city' => 'davao-city',
            'resort-in-samal-island' => 'samal-island',
            'resort-in-gensan' => 'general-santos',
            'resort-in-glan' => 'glan-sarangani',
            'resort-in-zamboanga' => 'zamboanga-city',
            'resort-in-kidapawan-city' => 'kidapawan',
            'resort-in-cebu-city' => 'cebu-city',
            'hotel-in-cebu' => 'cebu-city',
            'resort-in-lapu-lapu' => 'mactan',
            'resort-in-lapu-lapu-city' => 'mactan',
            'resort-in-panglao-bohol' => 'panglao',
            'resort-in-dumaguete' => 'dumaguete',
            'resort-in-dauin' => 'dauin',
            'resort-in-iloilo' => 'iloilo-city',
            'resort-in-iloilo-city' => 'iloilo-city',
            'resort-in-guimaras' => 'guimaras',
            'resort-in-guimaras-island' => 'guimaras',
            'resort-in-bacolod' => 'bacolod',
            'resort-in-don-salvador-benedicto' => 'bacolod',
            'resort-in-siquijor' => 'siquijor',
            'hotel-in-boracay' => 'boracay',
            'resort-in-el-nido' => 'el-nido',
            'resort-in-el-nido-palawan' => 'el-nido',
            'beach-resort-in-palawan' => 'el-nido',
            'resort-in-puerto-galera' => 'puerto-galera',
            'airbnb-in-manila' => 'manila',
            'resort-in-manila' => 'manila',
            'resort-in-taguig' => 'taguig',
            'resort-in-quezon-city' => 'quezon-city',
            'resort-in-nueva-ecija' => 'nueva-ecija',
            'resort-in-tarlac' => 'tarlac',
            'resort-in-urdaneta-city-pangasinan' => 'urdaneta',
            'resort-in-dingalan-aurora' => 'dingalan',
        ];

        $this->clusterToKey = [
            'rizal' => 'antipolo',
            'cavite' => 'tagaytay',
            'bulacan' => 'bulacan-province',
            'pampanga' => 'pampanga-province',
            'batangas' => 'batangas-city',
            'laguna' => 'pansol',
            'quezon' => 'lucena',
            'bicol' => 'albay-legazpi',
            'north-luzon' => 'la-union',
            'metro-manila' => 'manila',
            'mindanao' => 'davao-city',
            'visayas' => 'cebu-city',
            'palawan' => 'el-nido',
            'other' => '_default',
        ];
    }

    private function resolveDestination(?string $slug, ?string $cluster): ?string
    {
        if ($slug && isset($this->slugToKey[$slug])) return $this->slugToKey[$slug];
        if ($cluster && isset($this->clusterToKey[$cluster])) return $this->clusterToKey[$cluster];
        return '_default';
    }

    private function loadDestinationImages(string $key): array
    {
        $rows = DB::table('rg_media')
            ->where('path', 'like', "rg-media/destinations/{$key}-%")
            ->orderBy('path')
            ->get(['path']);

        $paths = [];
        foreach ($rows as $r) {
            $paths[] = '/storage/' . ltrim($r->path, '/');
        }
        return $paths;
    }

    private function loadExistingFaqs(int $pageId): array
    {
        $existing = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', 'faq')
            ->first();
        if (!$existing) return [];
        $payload = json_decode($existing->payload_json, true);
        return $payload['items'] ?? [];
    }

    // === Block builders ===

    private function buildBlocks($page, array $dest, string $destKey, array $images, array $existingFaqs, int $templateIdx): array
    {
        $phrase = $page->phrase;
        $destName = $dest['name'];
        $img1 = $images[0] ?? null;
        $img2 = $images[1] ?? $img1;
        $img3 = $images[2] ?? $img1;

        $intro = $this->buildIntro($phrase, $dest, $templateIdx);
        $spotsBlock = $this->buildSpotsBlock($dest);
        $foodBlock = $this->buildFoodBlock($dest);
        $transitBlock = $this->buildTransitBlock($dest, $destName);
        $seasonBlock = $this->buildSeasonBlock($dest, $destName);
        $tipBlock = $this->buildTipBlock($dest);
        $faqs = !empty($existingFaqs) ? $existingFaqs : $this->buildLocalizedFaqs($phrase, $dest);

        $heroAlt = ucfirst($phrase) . ' near ' . $destName;
        $heroCaption = $destName . ' — ' . $this->captionFor($templateIdx);

        $spotsImage = $img2 ? $this->imageBlock($img2, $heroAlt, $dest['spots'][0]['name'] ?? $destName) : null;
        $foodImage = $img3 ? $this->imageBlock($img3, 'Local food near ' . $destName, 'Local food in ' . $destName) : null;
        $hero = $img1 ? $this->imageBlock($img1, $heroAlt, $heroCaption) : null;

        $listingSlot = [
            'type' => 'listing_slot',
            'payload' => [
                'slot_label' => $this->listingLabel($templateIdx, $phrase),
                'fallback_html' => '<div class="border border-slate-200 rounded-lg p-6 bg-slate-50"><p class="text-slate-700 mb-3">We are still curating the best <strong>' . htmlspecialchars($phrase) . '</strong> stays. If you operate a property here, your listing can be the first one travelers see.</p><a href="/register" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">List your property</a></div>',
            ],
        ];

        $faqBlock = ['type' => 'faq', 'payload' => ['items' => array_values($faqs)]];

        $closingCta = [
            'type' => 'cta',
            'payload' => [
                'headline' => $this->ctaHeadline($templateIdx, $phrase),
                'text' => 'Get your property in front of travelers searching for ' . $phrase . ' right now.',
                'button_text' => 'List your property',
                'button_url' => '/register',
                'style' => 'primary',
            ],
        ];

        $intro = ['type' => 'rich_text', 'payload' => ['html' => $intro]];

        $blocks = match ($templateIdx) {
            0 => array_filter([
                $intro,
                $hero,
                $this->heading($this->h2Spots($destName, 0)),
                $spotsBlock,
                $this->heading($this->h2Food($destName, 0)),
                $foodBlock,
                $foodImage,
                $this->heading($this->h2Transit($destName, 0)),
                $transitBlock,
                $listingSlot,
                $this->heading($this->h2Season($destName, 0)),
                $seasonBlock,
                $tipBlock,
                $faqBlock,
                $closingCta,
            ]),
            1 => array_filter([
                $intro,
                $this->heading($this->h2Transit($destName, 1)),
                $transitBlock,
                $hero,
                $this->heading($this->h2Spots($destName, 1)),
                $spotsBlock,
                $spotsImage,
                $this->heading($this->h2Food($destName, 1)),
                $foodBlock,
                $listingSlot,
                $tipBlock,
                $this->heading($this->h2Season($destName, 1)),
                $seasonBlock,
                $faqBlock,
                $closingCta,
            ]),
            2 => array_filter([
                $intro,
                $tipBlock,
                $hero,
                $this->heading($this->h2Spots($destName, 2)),
                $spotsBlock,
                $this->heading($this->h2Food($destName, 2)),
                $foodBlock,
                $foodImage,
                $this->heading($this->h2Transit($destName, 2)),
                $transitBlock,
                $this->heading($this->h2Season($destName, 2)),
                $seasonBlock,
                $listingSlot,
                $faqBlock,
                $closingCta,
            ]),
            3 => array_filter([
                $hero,
                $intro,
                $this->heading($this->h2Spots($destName, 3)),
                $spotsBlock,
                $spotsImage,
                $this->heading($this->h2Food($destName, 3)),
                $foodBlock,
                $this->heading($this->h2Transit($destName, 3)),
                $transitBlock,
                $listingSlot,
                $tipBlock,
                $this->heading($this->h2Season($destName, 3)),
                $seasonBlock,
                $faqBlock,
                $closingCta,
            ]),
            4 => array_filter([
                $intro,
                $this->heading($this->h2Spots($destName, 4)),
                $hero,
                $spotsBlock,
                $this->heading($this->h2Food($destName, 4)),
                $foodBlock,
                $this->heading($this->h2Transit($destName, 4)),
                $transitBlock,
                $foodImage,
                $tipBlock,
                $this->heading($this->h2Season($destName, 4)),
                $seasonBlock,
                $listingSlot,
                $faqBlock,
                $closingCta,
            ]),
            default => [$intro, $hero, $spotsBlock, $foodBlock, $transitBlock, $listingSlot, $faqBlock, $closingCta],
        };

        return array_values($blocks);
    }

    private function imageBlock(string $src, string $alt, string $caption): array
    {
        return [
            'type' => 'image',
            'payload' => [
                'src' => $src,
                'alt' => $alt,
                'caption' => $caption,
                'align' => 'center',
            ],
        ];
    }

    private function heading(string $text, string $level = 'h2'): array
    {
        return ['type' => 'heading', 'payload' => ['text' => $text, 'level' => $level]];
    }

    // === Voice helpers ===

    private function buildIntro(string $phrase, array $dest, int $tpl): string
    {
        $voice = $dest['voice_intro'];
        $openers = [
            "If you are searching for a <strong>{$phrase}</strong>, here is the honest local read.",
            "Looking for a <strong>{$phrase}</strong>? Let me save you the generic write-up.",
            "Most lists about <strong>{$phrase}</strong> all say the same things. This one is from someone who knows the town.",
            "Here is what nobody bothers to tell you when you Google <strong>{$phrase}</strong>.",
            "<strong>" . ucfirst($phrase) . "</strong> — there is more to this than the brochure version. Here is the inside read.",
        ];
        $opener = $openers[$tpl];

        return '<p>' . $opener . '</p><p>' . $voice . '</p>';
    }

    private function buildSpotsBlock(array $dest): array
    {
        $spots = $dest['spots'];
        $html = '<p>The places worth blocking your itinerary around:</p><ul class="list-disc pl-6 space-y-2">';
        foreach ($spots as $spot) {
            $html .= '<li><strong>' . htmlspecialchars($spot['name']) . '</strong> — ' . htmlspecialchars($spot['desc']) . '.</li>';
        }
        $html .= '</ul>';
        return ['type' => 'rich_text', 'payload' => ['html' => $html]];
    }

    private function buildFoodBlock(array $dest): array
    {
        $food = $dest['food'];
        $html = '<p>What to eat while you are here, listed in the order most locals would tell you to try them:</p><ul class="list-disc pl-6 space-y-1">';
        foreach ($food as $f) {
            $html .= '<li>' . htmlspecialchars($f) . '</li>';
        }
        $html .= '</ul>';
        return ['type' => 'rich_text', 'payload' => ['html' => $html]];
    }

    private function buildTransitBlock(array $dest, string $destName): array
    {
        $html = '<p>' . htmlspecialchars($dest['transit']) . '</p>';
        return ['type' => 'rich_text', 'payload' => ['html' => $html]];
    }

    private function buildSeasonBlock(array $dest, string $destName): array
    {
        $html = '<p>' . htmlspecialchars($dest['season']) . '</p>';
        return ['type' => 'rich_text', 'payload' => ['html' => $html]];
    }

    private function buildTipBlock(array $dest): array
    {
        return [
            'type' => 'quote',
            'payload' => [
                'text' => $dest['tip'],
                'author' => "Local insider tip, " . $dest['name'],
            ],
        ];
    }

    private function captionFor(int $tpl): string
    {
        $options = [
            'one of the photo stops every visitor lines up for',
            'the view that ends up on most weekend phones',
            'the scene that gives this place its character',
            'a familiar sight to anyone who has been here',
            'the postcard angle, in person',
        ];
        return $options[$tpl % count($options)];
    }

    private function h2Spots(string $destName, int $tpl): string
    {
        $options = [
            "Things worth seeing around {$destName}",
            "Where to actually go in {$destName}",
            "The {$destName} spots locals recommend first",
            "Tourist spots in {$destName} that are worth your time",
            "Side trips and places to add in {$destName}",
        ];
        return $options[$tpl % count($options)];
    }

    private function h2Food(string $destName, int $tpl): string
    {
        $options = [
            "What to eat in {$destName}",
            "{$destName} food you should try",
            "Local dishes around {$destName}",
            "The {$destName} food list, ranked by locals",
            "Eat this while you are in {$destName}",
        ];
        return $options[$tpl % count($options)];
    }

    private function h2Transit(string $destName, int $tpl): string
    {
        $options = [
            "How to get to {$destName}",
            "Getting to {$destName} from Manila",
            "The honest travel time to {$destName}",
            "{$destName} travel and access notes",
            "Routes and timing for {$destName}",
        ];
        return $options[$tpl % count($options)];
    }

    private function h2Season(string $destName, int $tpl): string
    {
        $options = [
            "When to visit {$destName}",
            "The best months for {$destName}",
            "{$destName} weather and peak seasons",
            "Avoiding crowds in {$destName}",
            "Seasonal notes for {$destName}",
        ];
        return $options[$tpl % count($options)];
    }

    private function listingLabel(int $tpl, string $phrase): string
    {
        $options = [
            'Featured ' . $phrase . ' picks',
            'Top ' . $phrase . ' on Resort Guru PH',
            $phrase . ' worth booking',
            'Curated properties for ' . $phrase,
            'Currently featured: ' . $phrase,
        ];
        return $options[$tpl % count($options)];
    }

    private function ctaHeadline(int $tpl, string $phrase): string
    {
        $options = [
            'Own a property here?',
            'Run a resort or hotel in this area?',
            'Have a stay to list?',
            'Want bookings for your ' . $phrase . ' property?',
            'Bring your property to the front of this page',
        ];
        return $options[$tpl % count($options)];
    }

    private function buildLocalizedFaqs(string $phrase, array $dest): array
    {
        $destName = $dest['name'];
        $firstSpot = $dest['spots'][0]['name'] ?? 'the town center';
        $firstFood = $dest['food'][0] ?? 'local dishes';

        return [
            [
                'question' => "What makes a {$phrase} different from staying elsewhere?",
                'answer' => "Most {$phrase} properties put you within reach of {$firstSpot} and the local food worth trying like {$firstFood}. The point of staying here over a generic hotel is the access to specific places only this area offers.",
            ],
            [
                'question' => "What is the best time to book a {$phrase}?",
                'answer' => $dest['season'],
            ],
            [
                'question' => "How do you get to {$destName} from Manila?",
                'answer' => $dest['transit'],
            ],
            [
                'question' => "What should a first-timer know about a {$phrase}?",
                'answer' => $dest['tip'],
            ],
            [
                'question' => "What local food should I try at a {$phrase}?",
                'answer' => "The standouts to try around {$destName} are " . implode(', ', array_slice($dest['food'], 0, 3)) . ". Ask staff at your property — most can point you to a specific carinderia or stall they trust.",
            ],
        ];
    }
}
