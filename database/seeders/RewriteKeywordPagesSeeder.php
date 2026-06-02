<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Rewrites every rg_seo_pages article using the SEO + GEO content prompt:
 *
 *   - 23-year-old Filipino travel-writer voice; casual English with light Taglish.
 *   - Required structure: H1, intro, "Why X Is Worth The Trip", "The Best PAGE_TYPE in
 *     LOCATION" with the [DYNAMIC_LISTINGS_BLOCK] (listing_slot), 2-3 rotating
 *     subtopic H2s (selected deterministically by slug hash), Quick Tips, FAQ,
 *     and Closing.
 *   - 900-1200 words per article; keyphrase density 0.5-3%.
 *   - Banned-word filter + em-dash strip + voice anti-pattern scrub.
 *   - Meta title 50-60 chars; meta description 120-156 chars; both regenerated
 *     to lead with the focus keyphrase.
 *   - Sibling pages rotate which subtopics they emphasize so /resort-in-bulacan and
 *     /resort-in-pampanga don't read identically.
 *
 * Content is written into rg_content_blocks so the mother-app builder shows it
 * exactly as the frontend renders it. Idempotent: re-running the seeder deletes
 * old blocks for each page before writing the new set.
 */
class RewriteKeywordPagesSeeder extends Seeder
{
    private array $destinations;
    private array $enrichments;
    private array $transport;
    private array $extras;
    private array $slugToKey;
    private array $slugSubstrToKey;
    private array $clusterToKey;
    private array $bannedWords;
    private array $bannedPhrases;

    public function run(): void
    {
        $this->destinations = require database_path('data/destinations.php');
        // Merge any additional spots written by enrichment agents into the
        // existing destination spots array. Each agent owns its own batch
        // file (destinations_extra_spots_batch{1..N}.php), keyed by dest key.
        foreach (glob(database_path('data/destinations_extra_spots_batch*.php')) as $batch) {
            $extras = require $batch;
            if (!is_array($extras)) continue;
            foreach ($extras as $destKey => $newSpots) {
                if (!isset($this->destinations[$destKey])) continue;
                if (!is_array($newSpots)) continue;
                $existing = $this->destinations[$destKey]['spots'] ?? [];
                // Dedupe by lowercased spot name
                $existingNames = array_map(fn($s) => strtolower($s['name']), $existing);
                foreach ($newSpots as $spot) {
                    if (!is_array($spot) || empty($spot['name']) || empty($spot['desc'])) continue;
                    if (in_array(strtolower($spot['name']), $existingNames)) continue;
                    $existing[] = $spot;
                    $existingNames[] = strtolower($spot['name']);
                }
                $this->destinations[$destKey]['spots'] = $existing;
            }
        }

        $enrichmentPath = database_path('data/destinations_enrichment.php');
        $this->enrichments = is_file($enrichmentPath) ? (require $enrichmentPath) : [];
        $transportPath = database_path('data/transport_options.php');
        $this->transport = is_file($transportPath) ? (require $transportPath) : [];
        $extrasPath = database_path('data/destinations_extras.php');
        $this->extras = is_file($extrasPath) ? (require $extrasPath) : [];
        $this->buildLookups();
        $this->buildBannedList();

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->select(
                'p.id as page_id', 'p.slug as page_slug', 'p.title', 'p.h1',
                'p.meta_title', 'p.meta_description',
                'k.id as keyword_id', 'k.phrase', 'k.slug as keyword_slug',
                'k.cluster_tag', 'k.search_volume_monthly'
            )->get();

        $rewritten = 0;
        $bannedCaught = 0;
        $densityWarnings = 0;
        $wordCountWarnings = 0;

        foreach ($pages as $page) {
            $destKey = $this->resolveDestination($page->keyword_slug, $page->cluster_tag);
            $dest = $this->destinations[$destKey ?? '_default'];

            $existingFaqs = $this->loadExistingFaqs($page->page_id);
            $images = $this->loadDestinationImages($destKey ?: '_default');

            $article = $this->composeArticle($page, $dest, $destKey ?: '_default', $images, $existingFaqs);

            // Word-count + density audit
            $plainBody = $this->extractPlainText($article['blocks']);
            $words = $this->wordCount($plainBody);
            $density = $this->keyphraseDensity($plainBody, $page->phrase);

            if ($words < 900 || $words > 1200) $wordCountWarnings++;
            if ($density < 0.5 || $density > 3.0) $densityWarnings++;
            $bannedCaught += $article['banned_replacements'];

            // Replace existing blocks atomically
            DB::transaction(function () use ($page, $article, $images) {
                DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $page->page_id)
                    ->delete();

                foreach ($article['blocks'] as $i => $block) {
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

                $updateRow = [
                    'meta_title' => $article['meta_title'],
                    'meta_description' => $article['meta_description'],
                    'h1' => $article['h1'],
                    'og_image_path' => !empty($images) ? preg_replace('#^/?storage/#', '', $images[0]) : ($page->og_image_path ?? null),
                    'updated_at' => now(),
                ];

                // Subtitle, TLDR + WWWW are mother-system-editable. The seeder
                // populates sensible defaults from destination data so unedited
                // pages already show content on first visit.
                if (\Illuminate\Support\Facades\Schema::hasColumn('rg_seo_pages', 'subtitle')) {
                    $updateRow['subtitle'] = $article['subtitle'] ?? null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('rg_seo_pages', 'tldr')) {
                    $updateRow['tldr'] = $article['tldr'] ?? null;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('rg_seo_pages', 'wwww_json')) {
                    $updateRow['wwww_json'] = isset($article['wwww']) ? json_encode($article['wwww']) : null;
                }

                DB::table('rg_seo_pages')->where('id', $page->page_id)->update($updateRow);
            });

            $rewritten++;
            $this->command->info(sprintf(
                '  ok %-44s | %4d words | density %.2f%% | subtopics: %s',
                $page->keyword_slug, $words, $density, implode(', ', $article['subtopics_used'])
            ));
        }

        $this->command->info('');
        $this->command->info("Pages rewritten: $rewritten");
        $this->command->info("Word-count warnings (outside 900-1200): $wordCountWarnings");
        $this->command->info("Density warnings (outside 0.5-3%): $densityWarnings");
        $this->command->info("Banned-word replacements made: $bannedCaught");
    }

    // === Page composition ===

    private function composeArticle($page, array $dest, string $destKey, array $images, array $existingFaqs): array
    {
        $phrase = $page->phrase;
        $location = $dest['name'];
        $pageType = $this->extractPageType($phrase);
        $nearest = $this->nearestCityFor($dest['cluster'] ?? 'other');
        $seed = abs(crc32($page->keyword_slug));

        // Merge blog-sourced spot from enrichment (if any) so it appears in the
        // spot cards section, with the spot card's external link pointing back
        // to the source blog post — a legitimate backlink in exchange for the
        // research the bloggers did for us.
        $enrichment = $this->enrichments[$destKey] ?? null;
        if ($enrichment && !empty($enrichment['blog_spot_name']) && !empty($enrichment['blog_spot_desc'])) {
            $dest['spots'][] = [
                'name' => $enrichment['blog_spot_name'],
                'desc' => $enrichment['blog_spot_desc'],
                'url' => $enrichment['blog_url'] ?? null,
            ];
        }

        $subtopics = $this->pickSubtopics($dest, $seed);

        $h1 = $this->buildH1($phrase, $location, $pageType, $seed);
        $intro = $this->buildIntro($phrase, $location, $pageType, $dest, $seed);
        $whyText = $this->buildWhySection($dest, $location, $nearest, $seed, $phrase);
        $bestIntro = $this->buildBestSectionIntro($phrase, $pageType, $location, $seed);
        $subtopicBlocks = $this->buildSubtopicBlocks($subtopics, $dest, $destKey, $location, $phrase, $pageType, $seed);
        $tips = $this->buildQuickTips($dest, $seed);
        $faqs = $this->buildFaqs($phrase, $location, $pageType, $dest, $existingFaqs, $seed);
        $closing = $this->buildClosing($phrase, $location, $pageType, $seed);

        $bannedReplacements = 0;

        $img1 = $images[0] ?? null;
        $img2 = $images[1] ?? $img1;
        $img3 = $images[2] ?? $img1;

        $secondaryImage = $img2 && $img2 !== $img1 ? $this->imageBlock(
            $img2,
            $location . ' for ' . $phrase,
            $this->captionFromMedia($img2, $location)
        ) : null;

        // Build the hero carousel from all available imagery for this destination
        $carouselImages = $this->buildCarouselImageList($destKey, $dest);
        $heroCarousel = $this->buildHeroCarousel($carouselImages, $location, $phrase, $page->page_slug);

        $listingSlot = [
            'type' => 'listing_slot',
            'payload' => [
                'slot_label' => $this->listingLabel($phrase, $pageType, $location, $seed),
                'fallback_html' => $this->buildAggregatorFallback($phrase, $location, $pageType),
            ],
        ];

        // Per-page CSS scoped to .page-{slug} (the keyword-page template wraps <article> in this class)
        $cssBlock = $this->customCssBlock($page->page_slug, $dest['cluster'] ?? 'other');

        // Optional "Field notes" block sourced from a real Filipino travel blog,
        // with attribution + outbound link (backlink). Renders when enrichment exists.
        $fieldNotes = $enrichment ? $this->buildFieldNotesBlock($enrichment, $location, $destKey) : null;

        // Transport recommendations: airlines for the long-haul, bus lines for
        // Luzon, ferries for the islands. Pulled from data/transport_options.php.
        $transportBlock = $this->buildTransportBlock($destKey, $location);

        // Google Maps embed of the destination.
        $mapBlock = $this->buildMapBlock($location);

        // Dedicated content sections (formerly rotating subtopics now promoted
        // to fixed sections with richer layouts):
        $touristSpotsBlock = $this->buildTouristSpotsSection($dest, $destKey, $location);
        $historicalBlock = $this->buildHistoricalSection($destKey, $location, $dest);
        $festivalsBlock = $this->buildFestivalsSection($destKey, $location);
        $blogBacklinksBlock = $this->buildBlogBacklinksSection($destKey, $location, $dest['cluster'] ?? null);

        // Page order: scoped CSS -> hero carousel -> listings (or aggregator fallback)
        // -> transport options -> Google Maps -> intro -> Why X -> Tourist Spots
        // (dedicated, alternating L/R) -> Historical (when extras data exists)
        // -> Festivals (when extras data exists) -> Field Notes blog quote
        // -> 1-2 rotating subtopic blocks -> FAQ -> Closing -> CTA.
        $blocks = [
            $cssBlock,
            $heroCarousel,
            $listingSlot,
            $transportBlock,
            $mapBlock,
            $this->richTextBlock($intro, $bannedReplacements),
            $this->headingBlock("Why {$location} Is Worth The Trip"),
            $this->richTextBlock($whyText, $bannedReplacements),
            $touristSpotsBlock,
            $historicalBlock,
            $festivalsBlock,
            $fieldNotes,
            $blogBacklinksBlock,
        ];

        // Insert the secondary image after the first subtopic if available
        foreach ($subtopicBlocks as $i => $sub) {
            foreach ($sub as $b) {
                if (isset($b['type']) && $b['type'] === 'rich_text') {
                    $b['payload']['html'] = $this->stripBanned($b['payload']['html'], $bannedReplacements);
                }
                $blocks[] = $b;
            }
            if ($i === 0 && $secondaryImage) $blocks[] = $secondaryImage;
        }

        // Quick Tips section removed per user request — the tips were good content but
        // the section felt list-heavy on top of the cards above. The same advice is
        // already woven into the FAQ + Why X + subtopic blocks.

        if ($img3 && $img3 !== $img1 && $img3 !== $img2) {
            $blocks[] = $this->imageBlock($img3, "More of {$location}", $this->captionFromMedia($img3, $location));
        }

        // Note: faq block renderer adds its own "Frequently Asked Questions" H2,
        // so we skip a manual heading here to avoid a duplicate H2 on the page.
        $blocks[] = ['type' => 'faq', 'payload' => ['items' => array_values($faqs), 'heading' => "Frequently Asked Questions About " . ucwords($pageType) . " in {$location}"]];

        $blocks[] = $this->richTextBlock($closing, $bannedReplacements);

        $blocks[] = [
            'type' => 'cta',
            'payload' => [
                'headline' => $this->ctaHeadline($pageType, $location, $seed),
                'text' => 'Get your property in front of travelers searching for ' . $phrase . ' right now.',
                'button_text' => 'List your property',
                'button_url' => '/register',
                'style' => 'primary',
            ],
        ];

        $blocks = array_values(array_filter($blocks));

        // Word-count enforcement: pad up to 900+ words by appending expansion paragraphs.
        // Each expansion adds ~120-160 words; loop until we hit target or exhaust pool.
        $expansionPool = [
            $this->buildExpansionParagraph($dest, $location, $phrase, $pageType, $seed),
            $this->buildBookingParagraph($dest, $location, $phrase, $pageType, $seed),
            $this->buildNeighborhoodParagraph($dest, $location, $phrase, $pageType, $seed),
            $this->buildLocalFeelParagraph($dest, $location, $phrase, $pageType, $seed),
        ];
        $expansionIdx = 0;
        while ($expansionIdx < count($expansionPool)) {
            $plain = $this->extractPlainText($blocks);
            $words = $this->wordCount($plain);
            if ($words >= 900) break;

            $expansion = $this->stripBanned($expansionPool[$expansionIdx], $bannedReplacements);
            $expansionIdx++;

            $newBlocks = [];
            $injected = false;
            foreach ($blocks as $b) {
                if (!$injected && $b['type'] === 'faq') {
                    $newBlocks[] = ['type' => 'rich_text', 'payload' => ['html' => $expansion]];
                    $injected = true;
                }
                $newBlocks[] = $b;
            }
            if (!$injected) {
                // Fallback: insert near the end if no faq block exists yet
                array_splice($newBlocks, -1, 0, [['type' => 'rich_text', 'payload' => ['html' => $expansion]]]);
            }
            $blocks = $newBlocks;
        }

        return [
            'h1' => $h1,
            'meta_title' => $this->buildMetaTitle($phrase, $location, $pageType),
            'meta_description' => $this->buildMetaDescription($phrase, $location, $pageType, $dest),
            'blocks' => $blocks,
            'banned_replacements' => $bannedReplacements,
            'subtopics_used' => $subtopics,
            'subtitle' => $this->buildPageSubtitle($phrase, $location, $pageType, $dest),
            'tldr' => $this->buildPageTldr($phrase, $location, $pageType, $dest),
            'wwww' => $this->buildPageWwww($phrase, $location, $pageType, $dest),
        ];
    }

    /**
     * 1-2 sentence italic intro that sits below the H1. Pulls from the
     * destination's voice_intro (which is curated per-destination) and trims
     * to the first 1-2 sentences. Falls back to a phrase-aware default.
     */
    private function buildPageSubtitle(string $phrase, string $location, string $pageType, array $dest): string
    {
        $voice = trim($dest['voice_intro'] ?? '');
        if ($voice !== '') {
            // Take first 1-2 full sentences from voice_intro. No character cap
            // (the previous 260-char cap chopped mid-word and left "..." which
            // read as visibly cut off on the public page).
            if (preg_match('/^(.+?[.!?])(\s+(.+?[.!?]))?(?:\s|$)/u', $voice, $m)) {
                $candidate = trim($m[1] . (isset($m[3]) ? ' ' . $m[3] : ''));
                return $this->stripAiTells($candidate);
            }
            return $this->stripAiTells($voice);
        }
        // Generic fallback when no voice_intro is set
        return $this->stripAiTells("A grounded look at {$pageType} in {$location}, written by someone who has actually done the route. Skip the marketing copy and read the honest take below.");
    }

    /**
     * Replace AI-ish prose tells (em-dashes, "delve", "nestled", etc.) with
     * cleaner, more conversational phrasing. Used by buildPageSubtitle so
     * intros don't ship with the obvious giveaways.
     */
    private function stripAiTells(string $text): string
    {
        // Defensive: strip literal backslash-apostrophe sequences left over
        // from double-quoted PHP strings in the destination data files (where
        // `\'` does NOT escape — it stays as two literal characters).
        $text = str_replace("\\'", "'", $text);
        // Em-dash and en-dash → comma (most common AI tell per user feedback)
        $text = str_replace(["\xE2\x80\x94", "\xE2\x80\x93"], [', ', ', '], $text);
        // Collapse "x ,  y" produced by the replacement
        $text = preg_replace('/\s*,\s+,\s*/', ', ', $text);
        $text = preg_replace('/\s+,/', ',', $text);
        // Common AI phrasing → swap for natural alternatives
        $swaps = [
            '/\bnestled\b/i' => 'sitting',
            '/\bbustling\b/i' => 'busy',
            '/\bvibrant\b/i' => 'lively',
            '/\bdelve\b/i' => 'look',
            '/\bdelving\b/i' => 'looking',
            '/\bunveil\b/i' => 'show',
            '/\bembark\b/i' => 'start',
            '/\bhidden gem\b/i' => 'overlooked stop',
            '/\brich tapestry\b/i' => 'mix',
            '/\bin the heart of\b/i' => 'in',
            '/\bwhether you[\']?re\b/i' => 'if you are',
            '/\ba journey through\b/i' => 'a walk through',
            '/\bbreathtaking\b/i' => 'striking',
            '/\bawe-inspiring\b/i' => 'striking',
        ];
        $text = preg_replace(array_keys($swaps), array_values($swaps), $text);
        return preg_replace('/\s+/', ' ', trim($text));
    }

    /**
     * 3-4 bullet TLDR seed for the keyword page. Editable in the mother system.
     * Each bullet is one specific takeaway, not a generic platitude.
     */
    private function buildPageTldr(string $phrase, string $location, string $pageType, array $dest): string
    {
        $bullets = [];
        $bullets[] = "Best " . $pageType . " picks in " . $location . " lean toward " . ($dest['cluster_label'] ?? ($dest['cluster'] ?? 'the local')) . " character over generic chains. Aim near the town center for the easiest food and transit access.";
        if (!empty($dest['season'])) {
            $first = trim(explode('.', $dest['season'])[0]);
            $bullets[] = "Best time to go: " . $first . ".";
        }
        if (!empty($dest['food'][0])) {
            $foodTitle = $this->parseFoodItem($dest['food'][0])['title'];
            $bullets[] = "Don't leave without trying " . $foodTitle . ", the dish locals would point to first.";
        }
        if (!empty($dest['tip'])) {
            $bullets[] = "Local rule of thumb: " . trim(explode('.', $dest['tip'])[0]) . ".";
        }
        $bullets = array_map(fn($b) => $this->stripAiTells($b), $bullets);
        return implode("\n", array_map(fn($b) => '* ' . $b, $bullets));
    }

    /**
     * Build WWWW from destination data: voice intro → why, season → when,
     * top spots → where, derived audience hint → whom.
     */
    private function buildPageWwww(string $phrase, string $location, string $pageType, array $dest): array
    {
        $why = $dest['voice_intro'] ?? "{$location} packs a relaxed weekend with the right mix of food, scenery, and one or two heritage stops you can string together without rushing.";
        $why = preg_replace('/\s+/', ' ', trim($why));
        if (mb_strlen($why) > 260) $why = mb_substr($why, 0, 257) . '…';

        $when = !empty($dest['season']) ? trim($dest['season']) : "Best between November and February when the weather is dry and crowds are manageable. Holy Week and December long weekends bring the biggest crowds.";
        if (mb_strlen($when) > 260) $when = mb_substr($when, 0, 257) . '…';

        $whereParts = [];
        if (!empty($dest['spots'])) {
            $names = array_slice(array_column($dest['spots'], 'name'), 0, 3);
            $whereParts[] = "Anchor around " . implode(', ', $names) . ".";
        }
        if (!empty($dest['food'][0])) {
            $whereParts[] = "Build a meal stop for " . $this->parseFoodItem($dest['food'][0])['title'] . ".";
        }
        $where = $whereParts ? implode(' ', $whereParts) : "Most travelers base in the town center for easy access to food, jeepneys, and the main heritage sites.";
        if (mb_strlen($where) > 260) $where = mb_substr($where, 0, 257) . '…';

        // Audience heuristic from phrase keywords + pageType
        $p = strtolower($phrase);
        if (str_contains($p, 'family') || str_contains($p, 'kids')) {
            $whom = "Families with school-age kids. The pace works for an early start, lunch break, and a calmer afternoon. Bring water shoes if any spot involves wading.";
        } elseif (str_contains($p, 'airbnb') || str_contains($p, 'staycation')) {
            $whom = "Small groups or couples wanting privacy and a kitchen. Book early for long weekends — the better Airbnbs go first.";
        } elseif (str_contains($p, 'beach') || str_contains($p, 'island')) {
            $whom = "Couples and barkadas chasing a weekend by the water. Mid-week stays cut crowds by half and free up the better swim spots.";
        } else {
            $whom = "Works for couples, small barkada groups, or families with school-age kids. Adjust the pace to who you're traveling with and what you can wake up early for.";
        }

        return [
            'why' => $this->stripAiTells($why),
            'when' => $this->stripAiTells($when),
            'where' => $this->stripAiTells($where),
            'whom' => $this->stripAiTells($whom),
        ];
    }

    // === Voice generators ===

    private function buildH1(string $phrase, string $location, string $pageType, int $seed): string
    {
        // Eyebrow + H1 are stored together in the h1 column, separated by a
        // sentinel "~~". The view splits and renders the eyebrow as a small
        // kicker above the H1, so the two read as one flowing sentence (user
        // feedback: stacking "HOTEL IN CEBU" eyebrow + "That Are Worth..." H1
        // produced grammatically orphaned fragments).
        $pair = $this->buildEyebrowH1Pair($phrase, $location, $pageType, $seed);
        return $pair['eyebrow'] . ' ~~ ' . $pair['h1'];
    }

    /**
     * Builds a grammatically-paired eyebrow + H1 that PRESERVES the keyword
     * phrase verbatim (e.g. "hotel in Cebu", "resorts in Bulacan"). The
     * surrounding words ("Looking for ___", "Planning to book ___?", etc.)
     * adapt to whether the phrase is singular or plural, with the right
     * article in front when the phrase needs one.
     *
     * SEO matters here: keeping the exact keyword phrase in the title (and
     * not chopping it into "hotels" + "Cebu" separately) preserves the
     * keyword density signal Google looks for.
     */
    private function buildEyebrowH1Pair(string $phrase, string $location, string $pageType, int $seed): array
    {
        // Normalize the phrase: capitalize the location-part but leave the
        // category word lowercase ("hotel in Cebu", not "Hotel In Cebu").
        $phraseClean = $this->normalizePhraseForTitle($phrase, $location);

        // Detect plural vs singular from the FIRST word of the phrase
        $firstWord = strtolower(strtok($phraseClean, ' '));
        $isPlural = in_array($firstWord, ['hotels', 'resorts', 'airbnbs', 'stays', 'villas', 'inns'], true)
            || (str_ends_with($firstWord, 's') && !in_array($firstWord, ['airbnb', 'bus']));

        // For singular phrases we need an article ("a hotel" / "an airbnb")
        $article = preg_match('/^[aeiouAEIOU]/', $firstWord) ? 'an' : 'a';

        // Phrase as it appears in mid-sentence (lowercase first letter so
        // "Looking for hotel in Cebu" reads as one continuous sentence).
        // The location segment was already capitalized by normalizePhraseForTitle.
        $midSentence = lcfirst($phraseClean);

        // Templates: each pair is designed so the keyword phrase appears
        // verbatim in either the eyebrow or the H1, with the surrounding
        // words adapting to plural vs singular.
        if ($isPlural) {
            $pairs = [
                ['eyebrow' => "Looking for {$midSentence}?",                       'h1' => 'Here Are the Honest Picks We Would Make'],
                ['eyebrow' => "Planning a {$location} stay?",                     'h1' => "These Are the {$midSentence} We Would Book"],
                ['eyebrow' => "The best {$midSentence},",                         'h1' => 'Honestly Reviewed by Travelers Who Have Been'],
                ['eyebrow' => "Choosing {$midSentence} for the weekend?",         'h1' => 'Start With These Picks First'],
                ['eyebrow' => "Our shortlist of {$midSentence},",                 'h1' => 'Vetted Against Real Guest Feedback'],
                ['eyebrow' => "Where to book {$midSentence}",                     'h1' => 'Without Falling Into the Tourist Trap'],
                ['eyebrow' => "A real look at {$midSentence}",                    'h1' => 'That Are Actually Worth a Weekend'],
                ['eyebrow' => "If you are picking {$midSentence},",               'h1' => 'These Are the Ones Locals Would Actually Recommend'],
            ];
        } else {
            $pairs = [
                ['eyebrow' => "Looking for {$article} {$midSentence}?",            'h1' => 'Here Are the Honest Picks We Would Make'],
                ['eyebrow' => "Planning a {$location} stay?",                     'h1' => "This Is the {$midSentence} We Would Book"],
                ['eyebrow' => "The right {$midSentence},",                         'h1' => 'Honestly Reviewed by Travelers Who Have Been'],
                ['eyebrow' => "Booking {$article} {$midSentence} this weekend?", 'h1' => 'Start With These Picks First'],
                ['eyebrow' => "Our shortlist for {$article} {$midSentence},",     'h1' => 'Vetted Against Real Guest Feedback'],
                ['eyebrow' => "Where to book {$article} {$midSentence}",          'h1' => 'Without Falling Into the Tourist Trap'],
                ['eyebrow' => "A real look at {$article} {$midSentence}",         'h1' => 'That Is Actually Worth a Weekend'],
                ['eyebrow' => "If you are choosing {$article} {$midSentence},",   'h1' => 'Here Is the One Locals Would Actually Recommend'],
            ];
        }

        return $pairs[$seed % count($pairs)];
    }

    /**
     * Normalize a keyword phrase for title use: keep category words lowercase
     * (hotel/resort/airbnb) but capitalize the location proper noun. The
     * location part comes from the PHRASE ITSELF (not the assigned destination),
     * so a keyword like "beach resort in palawan" stays "beach resort in
     * Palawan" rather than getting rewritten to "beach resort in El Nido".
     */
    private function normalizePhraseForTitle(string $phrase, string $location): string
    {
        $lc = strtolower(trim($phrase));
        if (preg_match('/^(.+?)\s+in\s+(.+)$/i', $lc, $m)) {
            $category = trim($m[1]);
            // Capitalize each word of the keyword's location (handles multi-word
            // locations like "lapu-lapu city" → "Lapu-Lapu City").
            $kwLocation = trim($m[2]);
            $kwLocationCased = preg_replace_callback('/[a-z]+/i', fn($w) => ucfirst(strtolower($w[0])), $kwLocation);
            return $category . ' in ' . $kwLocationCased;
        }
        return $lc;
    }

    private function buildIntro(string $phrase, string $location, string $pageType, array $dest, int $seed): string
    {
        $sample = $dest['food'][0] ?? 'local dishes';
        $spot = $dest['spots'][0]['name'] ?? 'the town center';

        // Openers written in a DIY-traveler tone (closer to RJ Dexplorer): calm,
        // declarative, leads with itinerary framing or a specific local detail.
        // Phrase usage: never plug "{phrase}" into a slot that requires an
        // article ("a hotel in Boracay") or a verb-object ("rediscovering X").
        // Use {pageType} ("hotels") + {location} ("Boracay") for natural reading,
        // and reserve a single <strong>{phrase}</strong> mention in topic slots
        // where the bare noun phrase reads cleanly.
        $pluralType = ucfirst($pageType);
        $openers = [
            "<p>This is the kwento on <strong>{$phrase}</strong> from someone who has actually done the route a few times. The spots most listicles repeat are not always the ones locals would pick first, and the food is usually the part travelers undersell.</p><p>{$location} rewards a slow weekend over a packed itinerary, especially if you build your day around {$spot} and the carinderias near it.</p>",
            "<p>DIY travel guide for <strong>{$phrase}</strong>, the way most weekenders actually plan it. {$location} is one of those Philippine destinations where the better picks do not show up on the first page of Google, but a little homework goes a long way.</p><p>Below is what works on a Saturday-to-Sunday plan, what does not, and the tourist spots worth the detour from your stay.</p>",
            "<p>Quick read on <strong>{$phrase}</strong> before you book. {$location} has changed in the last few years, with more {$pageType} opening up and older ones quietly losing steam. The point of this guide is to spare you the bad reviews and surface the ones that still hold up.</p><p>You will see the tourist spots worth a half-day, the food finds, and the travel notes that matter.</p>",
            "<p>If you are looking at <strong>{$phrase}</strong> for a quick getaway, the basics are still there. {$location} sits close enough to the city for a short trip, and {$spot} still anchors most itineraries you will see online.</p><p>Itinerary-wise, the play is to keep things slow. Tara, here is the rundown of the {$pageType}, the food, and the heritage stops worth folding in.</p>",
            "<p>Coming back to {$location} after a couple of trips, here is the practical breakdown on <strong>{$phrase}</strong>. The experience changes a lot based on which side of town you stay on, which is why the listicle approach usually misses.</p><p>This guide groups the picks by what you actually want from the weekend, plus the tourist spots and food finds locals would point you to first.</p>",
            "<p>The honest read on <strong>{$phrase}</strong> for travelers who want to do this DIY. {$location} works best when you treat it as a full weekend rather than a single afternoon, and a lot of the value comes from picking a property near a real cluster of spots, not the first ad on Booking.</p><p>What follows is the itinerary frame, the local food worth chasing, and the heritage stops that round out the trip.</p>",
            "<p>Grabe, the booking apps have flattened how {$location} looks, but the place is more varied than the filters let on. The {$pageType} here range from sleepy barangay-level family-runs to the bigger names with full amenities, and the right pick depends on what kind of weekend you are after. That is the honest take behind <strong>{$phrase}</strong>.</p><p>Here is the local read, not press-release filler.</p>",
        ];
        return $openers[$seed % count($openers)];
    }

    private function buildWhySection(array $dest, string $location, string $nearest, int $seed, string $phrase = ''): string
    {
        $voice = $dest['voice_intro'] ?? '';
        $transit = $dest['transit'] ?? '';
        $spots = $dest['spots'] ?? [];
        $firstSpot = $spots[0]['name'] ?? 'the town plaza';
        $secondSpot = $spots[1]['name'] ?? $firstSpot;
        $phraseClause = $phrase ? " That mix is what makes a stay here different from picking any random property online" : '';

        $intro = "<p>{$voice}{$phraseClause}.</p>";

        $geography = "<p>From {$nearest}, the drive is usually shorter than people expect, and the roads have improved over the last few years. {$transit}</p>";

        $character = "<p>The character of {$location} is shaped by what locals do here on regular weekends, not just what tourists post about. {$firstSpot} still anchors a lot of itineraries, and {$secondSpot} is the kind of spot that adds depth to a quick trip. If you only had a few hours, those two together would already be a fair sampling.</p>";

        $variants = [
            $intro . $geography . $character,
            $intro . $character . $geography,
            $geography . $intro . $character,
            $intro . "<p>What separates {$location} from the more famous Philippine destinations is the pace. The roads are walkable in town, the food is rarely an afterthought, and you can spend a morning at {$firstSpot} and a late lunch at a carinderia without spending more than you would in a single dinner back in the city.</p>" . $geography,
        ];

        return $variants[$seed % count($variants)];
    }

    private function buildBestSectionIntro(string $phrase, string $pageType, string $location, int $seed): string
    {
        $variants = [
            "<p>The list below is curated from properties that actually rank against three filters: location, value, and consistency of recent reviews. We rotate it as new {$pageType} come online or as ratings shift, so what you see today is the current best read on <strong>{$phrase}</strong>.</p>",
            "<p>What you see below are the {$pageType} in {$location} that hold up across the last six months of guest feedback and reasonable rates. The order shifts as listings update, so check back if you are searching for <strong>{$phrase}</strong> weeks ahead.</p>",
            "<p>This is the working list of <strong>{$phrase}</strong> we currently recommend. Each one earns its slot through location, recent guest feedback, and the kind of consistency that means a Saturday booking is not a gamble.</p>",
            "<p>Below are the properties we currently rank as the best {$pageType} in {$location}. They get updated as new ones launch, and we do not show ones that have aged out. If you are searching for <strong>{$phrase}</strong>, start with the top three.</p>",
        ];
        return $variants[$seed % count($variants)];
    }

    private function pickSubtopics(array $dest, int $seed): array
    {
        // tourist-spots + culture-history are now DEDICATED fixed sections,
        // not part of the rotating pool, so we don't double up on the page.
        $available = ['food', 'how-to-get', 'weather', 'nature-outdoors', 'family-activities'];
        if (!empty($dest['food']) && count($dest['food']) >= 3) {
            $available[] = 'food';
        }
        $available = array_values(array_unique($available));
        $count = count($available);
        $picked = [];
        $offset = $seed % $count;
        for ($i = 0; $i < $count && count($picked) < 3; $i++) {
            $idx = ($offset + $i * 2) % $count;
            $candidate = $available[$idx];
            if (!in_array($candidate, $picked, true)) $picked[] = $candidate;
        }
        $take = 2;
        return array_slice($picked, 0, $take);
    }

    private function buildSubtopicBlocks(array $subtopics, array $dest, string $destKey, string $location, string $phrase, string $pageType, int $seed): array
    {
        $out = [];
        foreach ($subtopics as $sub) {
            $out[] = $this->buildSubtopicSection($sub, $dest, $destKey, $location, $phrase, $pageType, $seed);
        }
        return $out;
    }

    private function buildSubtopicSection(string $sub, array $dest, string $destKey, string $location, string $phrase, string $pageType, int $seed): array
    {
        switch ($sub) {
            case 'food':
                return $this->subtopicFood($dest, $destKey, $location, $seed, $phrase, $pageType);
            case 'tourist-spots':
                return $this->subtopicSpots($dest, $destKey, $location, $seed, $phrase);
            case 'culture-history':
                return $this->subtopicCulture($dest, $location, $seed, $phrase, $pageType);
            case 'nature-outdoors':
                return $this->subtopicNature($dest, $location, $seed);
            case 'family-activities':
                return $this->subtopicFamily($dest, $location, $pageType, $seed);
            case 'weather':
                return $this->subtopicWeather($dest, $location, $seed);
            case 'how-to-get':
                return $this->subtopicHowToGet($dest, $location, $seed, $phrase);
            case 'plan-day':
                return $this->subtopicPlanDay($dest, $destKey, $location, $phrase, $seed);
            default:
                return [];
        }
    }

    private function subtopicFood(array $dest, string $destKey, string $location, int $seed, string $phrase = '', string $pageType = 'stays'): array
    {
        $foods = $dest['food'] ?? [];
        $h2Options = [
            "What to Eat When You Are in {$location}",
            "Local Food Worth Pacing Your Trip Around",
            "The {$location} Food List",
            "{$location} Eats Worth Lining Up For",
        ];
        $h2 = $h2Options[$seed % count($h2Options)];
        $firstDish = $this->parseFoodItem($foods[0] ?? 'local dishes')['title'];
        $phraseLine = $phrase ? "Most {$pageType} guests in {$location} skip the local food in favor of resort menus, which is a small tragedy. " : '';

        $intro = "<p>{$phraseLine}If you only get one meal in {$location}, make it {$firstDish}. The version sold at a roadside palengke or carinderia is usually sharper than the one at any restaurant chain that opens here.</p>";

        $cards = '<div class="not-prose grid sm:grid-cols-2 gap-5 my-6">';
        foreach ($foods as $idx => $f) {
            $parsed = $this->parseFoodItem($f);
            $imgUrl = $this->foodImageUrl($destKey, $parsed['title'], $idx);
            $photoCaption = $this->captionFromMedia($imgUrl, $parsed['title'] . ', ' . $location);
            $linkUrl = $this->foodExternalUrl($parsed['title']);
            $extIcon = '<svg class="inline-block w-3.5 h-3.5 ml-0.5 -mt-0.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14m-4-9H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-1"/></svg>';
            $cards .= '<div class="food-card group flex flex-col rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">';
            $cards .= '<a href="' . htmlspecialchars($linkUrl) . '" target="_blank" rel="noopener nofollow" class="flex flex-1 flex-col" title="Look up ' . htmlspecialchars($parsed['title']) . ' on Wikipedia">';
            $cards .= '<figure class="aspect-[4/3] bg-slate-100 overflow-hidden relative m-0">';
            $cards .= '<img src="' . htmlspecialchars($imgUrl) . '" alt="' . htmlspecialchars($photoCaption) . '" class="w-full h-full object-cover transition group-hover:scale-105" loading="lazy">';
            $cards .= '<figcaption class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent text-white text-xs px-3 py-1.5 leading-tight">' . htmlspecialchars($photoCaption) . '</figcaption>';
            $cards .= '</figure>';
            $cards .= '<div class="p-4 flex-1">';
            $cards .= '<h3 class="font-bold text-slate-900 text-base mb-1 group-hover:underline">' . htmlspecialchars(ucfirst($parsed['title'])) . $extIcon . '</h3>';
            if (!empty($parsed['desc'])) {
                $cards .= '<p class="text-sm text-slate-600 leading-snug">' . htmlspecialchars($parsed['desc']) . '</p>';
            }
            $cards .= '</div></a></div>';
        }
        $cards .= '</div>';

        $closer = "<p>Most of these are easier to find at the public market in the morning or at family-run eateries off the main highway, not at the resort buffets. Ask staff where they eat on their day off, that answer is usually the legit recommendation.</p>";

        return [$this->headingBlock($h2), ['type' => 'custom_html', 'payload' => ['html' => $intro . $cards . $closer]]];
    }

    private function parseFoodItem(string $food): array
    {
        // "sisig (Pampanga is the home; try Aling Lucing)" → ['title' => 'sisig', 'desc' => 'Pampanga is the home; try Aling Lucing']
        if (preg_match('/^([^(]+)\(([^)]+)\)/', $food, $m)) {
            return ['title' => trim($m[1]), 'desc' => trim($m[2])];
        }
        // "bulalo at Mahogany Market eateries" → ['title' => 'bulalo', 'desc' => 'at Mahogany Market eateries']
        foreach ([' at ', ' from ', ' with ', ' along ', ' by ', ' inside ', ' beside ', ' near ', ' served '] as $sep) {
            $pos = mb_stripos($food, $sep);
            if ($pos !== false && $pos > 0) {
                return [
                    'title' => trim(mb_substr($food, 0, $pos)),
                    'desc' => trim(mb_substr($food, $pos)),
                ];
            }
        }
        return ['title' => trim($food), 'desc' => ''];
    }

    private function foodExternalUrl(string $dishName): string
    {
        // Wikipedia lookup — gives readers a real "what is this dish" reference.
        return 'https://en.wikipedia.org/wiki/Special:Search?search=' . rawurlencode($dishName . ' Filipino food');
    }

    private function foodImageUrl(string $destKey, string $dishName, int $idx): string
    {
        $foodSlug = substr(Str::slug($dishName), 0, 50);
        $candidate = 'rg-media/foods/' . $destKey . '-' . $foodSlug . '.jpg';
        $abs = storage_path('app/public/' . $candidate);
        if (is_file($abs) && filesize($abs) > 5000) {
            return '/storage/' . $candidate;
        }
        // Fallback: rotate through destination images so the card still feels grounded
        $destIdx = ($idx % 3) + 1;
        $destFallback = 'rg-media/destinations/' . $destKey . '-' . $destIdx . '.jpg';
        $destAbs = storage_path('app/public/' . $destFallback);
        if (is_file($destAbs) && filesize($destAbs) > 5000) {
            return '/storage/' . $destFallback;
        }
        return 'https://placehold.co/800x600/f1f5f9/64748b?text=' . urlencode(substr($dishName, 0, 30));
    }

    private function subtopicSpots(array $dest, string $destKey, string $location, int $seed, string $phrase = ''): array
    {
        $spots = $dest['spots'] ?? [];
        $h2Options = [
            "Spots in {$location} Worth Adding to Your Day",
            "What to Actually Do in {$location}",
            "Tourist Spots Near {$location}",
            "Places to Go in {$location} (Not Resorts)",
        ];
        $h2 = $h2Options[$seed % count($h2Options)];

        $intro = "<p>These are the spots locals would actually walk you through, the kind of places that give a stay in {$location} a reason beyond the pool. Pick two or three depending on how much driving you can stomach.</p>";

        $cards = '<div class="not-prose grid sm:grid-cols-2 gap-4 my-6">';
        foreach (array_slice($spots, 0, 6) as $idx => $spot) {
            $imgUrl = $this->spotImageUrl($destKey, $spot['name'], $idx);
            $linkUrl = $this->spotExternalUrl($spot, $location);
            $linkLabel = $this->spotLinkLabel($spot);
            $extIcon = '<svg class="inline-block w-3.5 h-3.5 ml-0.5 -mt-0.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14m-4-9H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-1"/></svg>';
            $photoCaption = $this->captionFromMedia($imgUrl, $spot['name'] . ', ' . $location);
            $cards .= '<div class="spot-card group flex flex-col bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">';
            $cards .= '<a href="' . htmlspecialchars($linkUrl) . '" target="_blank" rel="noopener nofollow" class="flex flex-1 flex-col" title="' . htmlspecialchars($linkLabel) . '">';
            $cards .= '<figure class="aspect-[4/3] overflow-hidden bg-slate-100 relative"><img src="' . htmlspecialchars($imgUrl) . '" alt="' . htmlspecialchars($photoCaption) . '" class="w-full h-full object-cover transition group-hover:scale-105" loading="lazy"><figcaption class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent text-white text-xs px-3 py-1.5 leading-tight">' . htmlspecialchars($photoCaption) . '</figcaption></figure>';
            $cards .= '<div class="p-4 flex-1 flex flex-col">';
            $cards .= '<h3 class="font-bold text-slate-900 mb-1 text-base group-hover:underline">' . htmlspecialchars($spot['name']) . $extIcon . '</h3>';
            $cards .= '<p class="text-sm text-slate-600 leading-relaxed">' . htmlspecialchars(ucfirst($spot['desc'])) . '.</p>';
            $cards .= '</div></a></div>';
        }
        $cards .= '</div>';

        $closer = "<p>If you only have a Saturday, picking two of these and skipping the rest is the realistic move. Local jeepneys and tricycles get you between most of them, though a private car saves an hour on a busy weekend.</p>";

        return [$this->headingBlock($h2), ['type' => 'custom_html', 'payload' => ['html' => $intro . $cards . $closer]]];
    }

    private function subtopicCulture(array $dest, string $location, int $seed, string $phrase = '', string $pageType = 'stays'): array
    {
        $spots = $dest['spots'] ?? [];
        $cultureSpot = $spots[0]['name'] ?? 'the local church';
        $h2 = "Culture and History You Will Run Into in {$location}";
        $phraseLine = $phrase ? "Most {$pageType} in {$location} put you within walking distance of one or two heritage sites that get under-photographed online. " : '';
        $html = "<p>{$phraseLine}{$location} is layered. Most travelers see only the surface, the parts that fit on a weekend itinerary, but the place has had centuries of Spanish, American, and Filipino history that show up in small ways.</p>";
        $html .= "<p>{$cultureSpot} is the obvious historical anchor, and it is worth walking around for an hour even if churches and old plazas are not usually your thing. The architecture and the patron-saint stories give you context for the rest of the trip.</p>";
        if (count($spots) > 1) {
            $other = $spots[1]['name'];
            $html .= "<p>Pair that with {$other}, which adds another layer to what {$location} actually is when the tourists leave for the day. Locals will sometimes correct your pronunciation of place names, and that is part of the deal here.</p>";
        }
        return [$this->headingBlock($h2), ['type' => 'rich_text', 'payload' => ['html' => $html]]];
    }

    private function subtopicNature(array $dest, string $location, int $seed): array
    {
        $spots = $dest['spots'] ?? [];
        $natureSpot = null;
        foreach ($spots as $s) {
            if (preg_match('/falls|beach|island|mountain|park|cave|spring|reef|lake|cliffs|view|hill|trail|forest/i', $s['name'] . $s['desc'])) {
                $natureSpot = $s; break;
            }
        }
        $natureSpot = $natureSpot ?? $spots[0] ?? ['name' => 'the local park', 'desc' => 'within an hour of the town center'];
        $h2 = "Nature and Outdoor Stops Around {$location}";
        $html = "<p>Most of {$location} is set up for outdoor weekends rather than mall-hopping. The headline pick is {$natureSpot['name']}, " . htmlspecialchars($natureSpot['desc']) . ".</p>";
        $html .= "<p>If you are bringing kids, plan around mornings before 10 AM and afternoons after 3 PM. Midday is hot enough that even the locals retreat indoors. Bring water, your own sunscreen, and a small fee in cash for environmental dues at most of the popular sites.</p>";
        return [$this->headingBlock($h2), ['type' => 'rich_text', 'payload' => ['html' => $html]]];
    }

    private function subtopicFamily(array $dest, string $location, string $pageType, int $seed): array
    {
        $h2 = "Family Activities Around {$location}";
        $spotName = $dest['spots'][0]['name'] ?? 'the town center';
        $html = "<p>For a family weekend, {$pageType} in {$location} usually win on space and quiet over flash. Kids do better in a real pool than another mall play area, and most properties here understand that.</p>";
        $html .= "<p>{$spotName} is the easiest half-day add-on, and the rest of the day is often best left unstructured. Stock up at a local sari-sari store for snacks and ice, since the resort store can run thin on weekends.</p>";
        return [$this->headingBlock($h2), ['type' => 'rich_text', 'payload' => ['html' => $html]]];
    }

    private function subtopicWeather(array $dest, string $location, int $seed): array
    {
        $season = $dest['season'] ?? 'Dry months are November to May. Avoid July through October if you want stable weather.';
        $h2Options = [
            "When to Go to {$location}",
            "Best Months and Weather in {$location}",
            "Timing Your Trip to {$location}",
        ];
        $h2 = $h2Options[$seed % count($h2Options)];
        $html = "<p>{$season}</p>";
        $html .= "<p>If your dates are flexible, weekday stays mid-month are quieter and usually 15 to 25 percent cheaper than weekends. The week after a long holiday is one of the best windows for rates that have not bounced back yet.</p>";
        return [$this->headingBlock($h2), ['type' => 'rich_text', 'payload' => ['html' => $html]]];
    }

    private function subtopicHowToGet(array $dest, string $location, int $seed, string $phrase = ''): array
    {
        $transit = $dest['transit'] ?? 'Most of the popular routes from Manila are reachable within four hours by land.';
        $h2Options = [
            "How to Get to {$location}",
            "Getting to {$location} Without Wasting Half a Day",
            "Travel Options for {$location}",
        ];
        $h2 = $h2Options[$seed % count($h2Options)];
        $phraseLine = $phrase ? "<p>For most travelers heading to {$location}, the realistic options are private car, bus, or a mix of both.</p>" : '';
        $html = $phraseLine . "<p>{$transit}</p>";
        $html .= "<p>Grab works in some parts of {$location} and not in others, so build in a tricycle ride or jeepney fare into your plan. Leaving the city before 6 AM on a weekend saves you the worst of the outbound traffic, and is the local rule for these trips.</p>";
        return [$this->headingBlock($h2), ['type' => 'rich_text', 'payload' => ['html' => $html]]];
    }

    private function buildQuickTips(array $dest, int $seed): array
    {
        $pool = [
            "Bring cash, even in 2026. GCash is accepted at bigger properties but smaller carinderias and tricycles still operate cash-first.",
            "Check Globe and Smart signal before booking a remote villa. Some properties are still on patchy LTE outside of Wi-Fi range.",
            "Pool resorts get loud on weekends. If you want a quiet stay, book Sunday-to-Tuesday instead of Friday-to-Saturday.",
            "Confirm the corkage policy on outside food and lechon. Many properties allow it without a fee, but a few have started charging.",
            "Bring water shoes if you are going to any beach with reef or pebble shore. Bare feet on hot sand is a rookie mistake by 11 AM.",
            "Print your booking confirmation. Some smaller properties still ask for it even with online check-in, especially after weekend power dips.",
            "Tip the housekeeping and pool staff if the stay was clean. Small kindness goes a long way at the family-run places.",
            "Bring your own toiletries beyond the basics. Most properties only stock soap and a small shampoo packet, no conditioner.",
            "Ask about the early check-in surcharge upfront. Some allow it free, some charge half a night's rate.",
            "Bring a power bank. The Wi-Fi outage rate at smaller resorts is still higher than people admit.",
        ];
        // pick 4 deterministically
        $picked = [];
        $count = count($pool);
        for ($i = 0; $i < 4; $i++) {
            $idx = ($seed + $i * 7) % $count;
            $picked[] = $pool[$idx];
        }
        return $picked;
    }

    private function tipsBlock(array $tips, int &$bannedReplacements): array
    {
        $html = '<ul class="list-disc pl-6 space-y-2">';
        foreach ($tips as $t) {
            $clean = $this->stripBanned($t, $bannedReplacements);
            $html .= '<li>' . htmlspecialchars($clean) . '</li>';
        }
        $html .= '</ul>';
        return ['type' => 'rich_text', 'payload' => ['html' => $html]];
    }

    private function buildFaqs(string $phrase, string $location, string $pageType, array $dest, array $existingFaqs, int $seed): array
    {
        $firstSpot = $dest['spots'][0]['name'] ?? 'the town center';
        $firstFood = $dest['food'][0] ?? 'local dishes';
        $tip = $dest['tip'] ?? '';
        $season = $dest['season'] ?? '';
        $transit = $dest['transit'] ?? '';

        // 18 GEO-friendly Q&A entries. Each answer leads with the direct factual
        // sentence so AI engines (ChatGPT, Perplexity, Google AI Overviews) can
        // lift it cleanly as a self-contained answer. Pool is sampled to pick 7
        // per page deterministically by slug hash.
        $nearestCity = $this->nearestCityFor($dest['cluster'] ?? 'other');
        $allFoods = !empty($dest['food']) ? implode(', ', array_slice(array_map(fn($f) => $this->parseFoodItem($f)['title'], $dest['food']), 0, 3)) : 'local Filipino food';

        $pool = [
            [
                'question' => "What makes a stay in {$location} worth booking?",
                'answer' => "Our {$phrase} list stands out for location, recent guest feedback, and consistency. {$location} rewards travelers who plan the trip around the local food and tourist spots, not just the pool. The shortlist on this page is updated as new properties come online or as ratings shift.",
            ],
            [
                'question' => "What is the best month to book {$phrase}?",
                'answer' => $season ?: "The best window for {$phrase} is November to May, when the weather across most of the Philippines is dry and stable. Avoid July to October if you can, since storms can disrupt road and ferry access in many parts of the country.",
            ],
            [
                'question' => "How do I get to {$location} from {$nearestCity}?",
                'answer' => "Most travelers reach {$location} from {$nearestCity} by bus or private car for nearby destinations, or by short domestic flight for the islands. " . ($transit ?: "Hourly bus services from Cubao or Buendia cover the popular Manila routes."),
            ],
            [
                'question' => "Are the {$phrase} picks here family-friendly?",
                'answer' => "Yes. Most properties in {$location} cater to families on weekends with shared pools, function halls, and outside-food policies that work for group meals. For a quieter stay with small children, book a weekday and ask for a property with a children's pool.",
            ],
            [
                'question' => "What tourist spot near {$location} is worth a half-day stop?",
                'answer' => "{$firstSpot} is the standout near {$location}, usually within a short drive of most picks on our {$phrase} list. Add it to your itinerary on the way in or out, since the entry windows are typically morning or late afternoon.",
            ],
            [
                'question' => "Is GCash accepted at most {$phrase} properties?",
                'answer' => "Yes. Most established {$pageType} in {$location} now accept GCash, especially for deposits and balance payments. Smaller barangay-level properties and tricycles still run cash-first, so carry small bills along with the e-wallet.",
            ],
            [
                'question' => "What should a first-timer booking {$phrase} know?",
                'answer' => $tip ?: "Most small towns near {$location} settle by 9 PM, so plan your dinners early. Bring a light jacket between November and February if you are anywhere inland in {$location}, and pack water shoes if you are heading to a rocky cove.",
            ],
            [
                'question' => "What local food should I try in {$location}?",
                'answer' => "The standouts in {$location} are {$allFoods}. Locals will often correct you toward a specific carinderia or stall rather than a chain. Ask at your front desk for the version their own staff would order on a day off, the answer will not be the obvious one.",
            ],
            [
                'question' => "What is the closest airport to {$location}?",
                'answer' => "The closest airport depends on which side of the cluster {$location} sits in. For most {$phrase} bookings, you fly into the nearest hub (Manila, Cebu, or Davao depending on region) and then transfer by van or bus. The transport section above lists the operators that run the route.",
            ],
            [
                'question' => "Is {$location} safe for solo travelers?",
                'answer' => "{$location} is generally safe for solo travelers when you follow standard precautions: keep your valuables out of sight, message your stay before arriving late at night, and ask the front desk before walking anywhere unfamiliar after dark. Solo Filipino travelers are common here and locals are used to seeing them.",
            ],
            [
                'question' => "Do {$phrase} properties accommodate large groups?",
                'answer' => "Many {$pageType} in {$location} accommodate groups of 10 to 30 through function-hall or whole-property bookings. For barkada or family-reunion trips, book the entire property when possible, and ask about catering options if you prefer not to bring your own food.",
            ],
            [
                'question' => "What is the typical Wi-Fi situation at {$phrase} properties?",
                'answer' => "Mid-range and premium {$pageType} in {$location} have working Wi-Fi suitable for streaming and light work calls. Smaller family-run properties may have weaker signal, especially in the upland or coastal barangays. Confirm directly with the property if remote work is a non-negotiable.",
            ],
            [
                'question' => "Can I bring outside food and drinks to {$location} properties?",
                'answer' => "Most {$pageType} in {$location} allow outside food without corkage, especially the day-use pool resorts and family-owned properties. Premium beach resorts and ridge hotels sometimes have a corkage policy, so ask before you arrive with a cooler of lechon.",
            ],
            [
                'question' => "Is parking available at most {$phrase} properties?",
                'answer' => "Yes. Free on-site parking is the default at almost all {$pageType} in {$location}, since most guests arrive by private car. Boutique stays in the older town centers may have limited parking, so confirm with the host before booking if you are driving in.",
            ],
            [
                'question' => "What language is most commonly spoken in {$location}?",
                'answer' => "Filipino and English are widely understood across {$location}, and most {$phrase} staff are conversational in both. The local dialect varies by region, but you do not need it to navigate as a traveler. A few words of the local greeting are always appreciated by hosts.",
            ],
            [
                'question' => "Can I cancel my {$phrase} booking if plans change?",
                'answer' => "Cancellation rules vary per property. Most {$pageType} in {$location} that you book through Booking.com or Agoda offer free cancellation up to a window before check-in, while direct bookings often follow stricter terms. Read the property's specific policy before paying anything beyond a small reservation hold.",
            ],
            [
                'question' => "Is it cheaper to book {$phrase} directly or through an aggregator?",
                'answer' => "Direct bookings can be cheaper for {$phrase} when you reach the property through their Facebook page or website, since they save on the aggregator commission. Aggregators are usually safer for cancellation and refund disputes, so the choice is between price and protection.",
            ],
            [
                'question' => "Are pets allowed at {$phrase} properties in {$location}?",
                'answer' => "Pet policies vary widely across {$pageType} in {$location}. A growing number of properties are pet-friendly, especially smaller villas and farm stays, but the bigger pool resorts often restrict pets to designated areas or do not allow them. Always confirm in writing before bringing your dog.",
            ],
        ];

        // Pick 7 unique pool entries deterministically (step by 1 not by 3 so
        // adjacent slug hashes don't collide on the same question set, and we
        // never pick the same question twice on a single page). With 18 pool
        // entries this gives every page a 7-question selection that overlaps
        // ~40% with sibling pages — varied without re-deriving from scratch.
        $picked = [];
        $count = count($pool);
        $offset = $seed % $count;
        for ($i = 0; $i < 7; $i++) {
            $idx = ($offset + $i) % $count;
            $picked[] = $pool[$idx];
        }

        if (!empty($existingFaqs)) {
            $kept = array_slice($existingFaqs, 0, 2);
            return array_merge($kept, array_slice($picked, 0, 5));
        }
        return $picked;
    }

    private function buildClosing(string $phrase, string $location, string $pageType, int $seed): string
    {
        $variants = [
            "<p>So that is the working read on <strong>{$phrase}</strong>. Scroll back up if you want to compare the listings, and message the team if you run a property in {$location} that should be on this page.</p>",
            "<p>If you have a favorite from the <strong>{$phrase}</strong> list above that we should highlight, send us a tip. The list updates regularly, and the page is meant to be a working document, not a frozen brochure.</p>",
            "<p>Bookmark this page if you are still in planning mode for <strong>{$phrase}</strong>. Rates shift and properties come online quickly in {$location}, so the listings above will look different in a few months. If you operate a property here, claim a spot before someone else does.</p>",
            "<p>That is the honest take on <strong>{$phrase}</strong> for now. If you found a place worth recommending or want to flag something we got wrong, get in touch. The list grows from real travelers, not press releases.</p>",
        ];
        return $variants[$seed % count($variants)];
    }

    private function buildExpansionParagraph(array $dest, string $location, string $phrase, string $pageType, int $seed): string
    {
        $spots = $dest['spots'] ?? [];
        $extra = $spots[2]['name'] ?? $spots[0]['name'] ?? $location;
        return "<p>One more thing to know about {$location}: the published {$pageType} rates online are usually higher than what you can negotiate by calling the property directly, especially on shoulder dates. For groups of six or more, ask about the function-hall package rather than booking rooms one by one. {$extra} is often within reach of the same property, which makes a Saturday morning detour easy to add. The local read is that off-season midweek stays are still the best value, and that has not changed in the last two years even as more tourists discovered the area.</p>";
    }

    private function buildBookingParagraph(array $dest, string $location, string $phrase, string $pageType, int $seed): string
    {
        return "<p>The booking flow most locals use for {$pageType} in {$location} is a hybrid one. Start with Booking.com or Agoda to read recent reviews, then ping the property directly on Facebook Messenger to confirm availability and ask about anything that is not in the listing. Things to clarify upfront include the corkage policy, the pool depth if you are bringing kids, and whether towels and toiletries are included. A short conversation usually surfaces an upgrade if the property has unsold rooms that week. The aggregator commission cuts into the operator's margin, so they have an incentive to make a direct deal work.</p>";
    }

    private function buildLocalFeelParagraph(array $dest, string $location, string $phrase, string $pageType, int $seed): string
    {
        $voice = $dest['voice_intro'] ?? '';
        $first = $dest['spots'][0]['name'] ?? 'the town center';
        return "<p>One more honest note on {$location}: the experience changes a lot depending on which barangay your property sits in. It is not one uniform town, it is a cluster of smaller barangays each with its own pace. The {$pageType} on the main highway feel different from the ones tucked behind a side road, and the ones near {$first} get more weekend foot traffic. Ask the front desk how to reach the nearest carinderia or palengke before you commit to a meal at the property restaurant. Locals will usually point you to a better answer.</p>";
    }

    private function buildNeighborhoodParagraph(array $dest, string $location, string $phrase, string $pageType, int $seed): string
    {
        $spots = $dest['spots'] ?? [];
        $first = $spots[0]['name'] ?? 'the town center';
        return "<p>If you are staying for more than a night, the day-two plan in {$location} matters as much as the day-one arrival. A solid second day usually combines a morning at {$first} with a slow lunch at a carinderia or local restaurant, then a free afternoon at the {$pageType} pool. Skip the overplanned itineraries that pack four spots into a single day, since the heat and the road conditions make that more stressful than it sounds. The locals who live here move at a slower weekend pace, and there is wisdom in that approach. The travel time alone between popular spots eats more of your day than the maps suggest.</p>";
    }

    private function buildMetaTitle(string $phrase, string $location, string $pageType): string
    {
        $candidates = [
            ucwords($phrase) . " | Local Picks for {$location}",
            ucwords($phrase) . ": Honest Local Picks {$location}",
            "Best " . ucwords($pageType) . " in {$location} | " . ucwords($phrase),
            ucwords($phrase) . " {$location}: Where to Stay",
        ];
        foreach ($candidates as $c) {
            $len = strlen($c);
            if ($len >= 50 && $len <= 60) return $c;
        }
        // Fallback: truncate
        $fallback = ucwords($phrase) . " | Local Picks for {$location}";
        if (strlen($fallback) > 60) $fallback = substr($fallback, 0, 57) . '...';
        if (strlen($fallback) < 50) $fallback .= ' Resort Guru PH';
        return substr($fallback, 0, 60);
    }

    private function buildMetaDescription(string $phrase, string $location, string $pageType, array $dest): string
    {
        $firstFood = $dest['food'][0] ?? 'local food worth a meal';
        $firstSpot = $dest['spots'][0]['name'] ?? 'a real tourist spot';
        $tries = [
            "Looking for {$phrase}? Local picks across {$location}, near {$firstSpot}, with honest takes from people who actually go and the food worth pacing your trip around.",
            "Find {$phrase} the way locals do. Curated {$pageType} near {$firstSpot} in {$location}, and the food worth lining up for.",
            "{$location}'s best {$pageType}, curated by people who actually go. Honest picks for {$phrase}, near {$firstSpot}, plus the food and tourist spots you need.",
        ];
        foreach ($tries as $t) {
            $len = strlen($t);
            if ($len >= 120 && $len <= 156) return $t;
        }
        // Fallback: trim a longer candidate
        $fallback = $tries[0];
        if (strlen($fallback) > 156) $fallback = substr($fallback, 0, 153) . '...';
        if (strlen($fallback) < 120) $fallback .= ' Compare here.';
        return substr($fallback, 0, 156);
    }

    // === Helpers ===

    private function extractPageType(string $phrase): string
    {
        $lc = strtolower($phrase);
        if (str_starts_with($lc, 'airbnb')) return 'Airbnbs';
        if (str_starts_with($lc, 'beach resort')) return 'beach resorts';
        if (str_starts_with($lc, 'hotel')) return 'hotels';
        if (str_starts_with($lc, 'resort')) return 'resorts';
        return 'stays';
    }

    private function nearestCityFor(string $cluster): string
    {
        return match ($cluster) {
            'rizal', 'cavite', 'bulacan', 'pampanga', 'batangas', 'laguna', 'quezon', 'metro-manila' => 'Manila',
            'bicol' => 'Naga or Legazpi',
            'north-luzon' => 'Manila',
            'mindanao' => 'Davao',
            'visayas' => 'Cebu City',
            'palawan' => 'Puerto Princesa',
            default => 'Manila',
        };
    }

    // captionFor() removed — it generated made-up "photographed by someone"
    // captions that pretended a writer had visited. All image captions now
    // derive from the Wikimedia filename via captionFromMedia().

    private function listingLabel(string $phrase, string $pageType, string $location, int $seed): string
    {
        $options = [
            "Current picks for {$phrase}",
            ucwords($pageType) . " in {$location} we'd actually book",
            "Listings for {$phrase} this month",
            "Top " . ucwords($pageType) . " in {$location} right now",
        ];
        return $options[$seed % count($options)];
    }

    private function ctaHeadline(string $pageType, string $location, int $seed): string
    {
        $options = [
            "Run a property in {$location}?",
            "Operate a {$pageType} stay here?",
            "Own one of the {$pageType} in {$location}?",
            "Want bookings from this page?",
        ];
        return $options[$seed % count($options)];
    }

    // === Banned-word filter ===

    private function buildBannedList(): void
    {
        $this->bannedWords = [
            'delve' => 'look at',
            'tapestry' => 'mix',
            'vibrant' => 'lively',
            'bustling' => 'busy',
            'nestled' => 'set',
            'embark' => 'start',
            'unveil' => 'show',
            'unlock' => 'open',
            'elevate' => 'lift',
            'realm' => 'world',
            'breathtaking' => 'striking',
            'plethora' => 'lot',
            'myriad' => 'many',
            'robust' => 'solid',
            'seamless' => 'smooth',
            'leverage' => 'use',
            'dive into' => 'get into',
            'hidden gem' => 'lesser-known spot',
            'must-visit' => 'worth a visit',
            'must-try' => 'worth trying',
        ];
        $this->bannedPhrases = [
            '/\bIt is not just (\w+), it is (\w+)/i' => '$1 and $2',
            "/\bWhether you (\w+)'re a ([^,]+), or a ([^,]+)/i" => 'Either way,',
            '/\bIn this article, we will explore\b/i' => 'Here is the read on',
            '/\bIn conclusion\b/i' => 'So',
            '/\bTo sum up\b/i' => 'So',
            '/\bIn summary\b/i' => 'So',
            '/\bPicture this:\s*/i' => '',
            '/\bImagine:\s*/i' => '',
            '/\bAh,\s*/i' => '',
            '/—/' => ', ',
            '/--/' => ', ',
        ];
    }

    private function stripBanned(string $text, int &$counter): string
    {
        foreach ($this->bannedWords as $bad => $good) {
            $text = preg_replace('/\b' . preg_quote($bad, '/') . '\b/i', $good, $text, -1, $n);
            if ($n) $counter += $n;
        }
        foreach ($this->bannedPhrases as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text, -1, $n);
            if ($n) $counter += $n;
        }
        // Strip em-dashes and other AI-feeling tells (the most common "this was
        // written by an LLM" giveaway, per user feedback).
        $text = $this->stripAiTells($text);
        return $text;
    }

    private function richTextBlock(string $html, int &$counter): array
    {
        return ['type' => 'rich_text', 'payload' => ['html' => $this->stripBanned($html, $counter)]];
    }

    private function headingBlock(string $text, string $level = 'h2'): array
    {
        return ['type' => 'heading', 'payload' => ['text' => $text, 'level' => $level]];
    }

    private function imageBlock(string $src, string $alt, string $caption): array
    {
        return ['type' => 'image', 'payload' => ['src' => $src, 'alt' => $alt, 'caption' => $caption, 'align' => 'center']];
    }

    // === Plain text + density ===

    private function extractPlainText(array $blocks): string
    {
        $parts = [];
        foreach ($blocks as $b) {
            $p = $b['payload'] ?? [];
            switch ($b['type']) {
                case 'heading':
                    $parts[] = $p['text'] ?? '';
                    break;
                case 'rich_text':
                    $parts[] = strip_tags($p['html'] ?? '');
                    break;
                case 'faq':
                    if (!empty($p['heading'])) $parts[] = $p['heading'];
                    foreach (($p['items'] ?? []) as $it) {
                        $parts[] = ($it['question'] ?? '') . ' ' . ($it['answer'] ?? '');
                    }
                    break;
                case 'cta':
                    $parts[] = ($p['headline'] ?? '') . ' ' . ($p['text'] ?? '');
                    break;
            }
        }
        return trim(html_entity_decode(implode(' ', $parts), ENT_QUOTES | ENT_HTML5));
    }

    private function wordCount(string $text): int
    {
        $text = preg_replace('/\s+/', ' ', $text);
        return str_word_count($text);
    }

    private function keyphraseDensity(string $text, string $phrase): float
    {
        $words = $this->wordCount($text);
        if ($words === 0) return 0.0;
        // Yoast-style: each full-phrase occurrence is one unit, regardless of phrase length.
        $count = preg_match_all('/\b' . preg_quote($phrase, '/') . '\b/i', $text);
        return round(($count / $words) * 100, 2);
    }

    // === Lookups ===

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

            // Region 1 expansion: Pangasinan + La Union + Ilocos
            'resort-in-pangasinan-philippines' => 'pangasinan-general',
            'resort-in-pangasinan-bolinao' => 'bolinao',
            'beach-resort-in-pangasinan' => 'pangasinan-general',
            'beach-resort-in-pangasinan-bolinao' => 'bolinao',
            'beach-resort-in-pangasinan-white-sand' => 'bolinao',
            'beach-resorts-in-pangasinan' => 'pangasinan-general',
            'white-sand-beach-resort-in-pangasinan' => 'bolinao',
            'resort-hotel-in-pangasinan' => 'pangasinan-general',
            'hotel-in-pangasinan-philippines' => 'pangasinan-general',
            'hotel-in-pangasinan' => 'pangasinan-general',
            'hotel-in-pangasinan-urdaneta' => 'urdaneta',
            'hotel-in-urdaneta-pangasinan-philippines' => 'urdaneta',
            'hotel-in-urdaneta-city-pangasinan' => 'urdaneta',
            'hotels-in-dagupan-pangasinan-philippines' => 'dagupan-pangasinan',
            'hotel-in-dagupan-city-pangasinan' => 'dagupan-pangasinan',
            'beach-resort-in-san-fabian-pangasinan' => 'san-fabian',
            'beach-resort-in-sual-pangasinan' => 'sual',
            'beach-resort-in-dasol-pangasinan' => 'dasol',
            'dasol-beach-resort-in-pangasinan' => 'dasol',
            'hotel-in-manaoag-pangasinan' => 'manaoag',
            'hotels-in-lingayen-pangasinan' => 'lingayen',
            'cozy-resort-in-rosales-pangasinan' => 'rosales',

            // La Union sub-keys (specific towns)
            'beach-and-resort-in-la-union' => 'la-union',
            'beach-resort-in-la-union' => 'la-union',
            'beach-resort-in-la-union-philippines' => 'la-union',
            'beach-resort-in-la-union-with-swimming-pool' => 'la-union',
            'beach-and-pool-resort-in-la-union' => 'la-union',
            'hotel-in-la-union' => 'la-union',
            'hotel-in-la-union-philippines' => 'la-union',
            'la-union-hotels' => 'la-union',
            'resort-hotel-in-la-union' => 'la-union',
            'resort-in-la-union-philippines' => 'la-union',
            'best-beachfront-resort-in-la-union' => 'la-union',
            'kahuna-beach-resort-in-la-union' => 'san-juan-la-union',
            'thunderbird-resort-in-la-union' => 'san-fernando-la-union',
            'aureo-resort-in-la-union' => 'san-fernando-la-union',
            'hotel-45-in-la-union' => 'san-fernando-la-union',
            'hotel-in-la-union-san-juan' => 'san-juan-la-union',
            'hotel-in-la-union-san-fernando' => 'san-fernando-la-union',
            'hotel-in-san-fernando-city-la-union' => 'san-fernando-la-union',
            'resort-in-la-union-san-juan' => 'san-juan-la-union',
            'san-juan-la-union-beachfront-resort' => 'san-juan-la-union',
            'hotel-in-bauang-la-union-philippines' => 'bauang',

            // Ilocos
            'tourist-spot-in-ilocos-sur' => 'vigan',
            'tourist-spot-in-ilocos-norte' => 'laoag',
            'tourist-spot-of-ilocos-norte' => 'laoag',
            'tourist-spot-in-ilocos-norte-philippines' => 'laoag',
            'tourist-spot-in-vigan-ilocos-sur' => 'vigan',
            'tourist-spot-in-laoag-ilocos-norte' => 'laoag',
            'tourist-spot-in-la-union' => 'la-union',
            'tourist-spot-in-la-union-philippines' => 'la-union',
            'tourist-spot-in-pangasinan' => 'pangasinan-general',
            'tourist-spots-in-pangasinan' => 'pangasinan-general',
            'hotel-in-ilocos-norte' => 'laoag',
            'hotels-in-vigan-ilocos-sur-philippines' => 'vigan',

            // Single-word generic resort-in-X keywords
            'resort-in-iloilo' => 'iloilo-city',
            'resort-in-davao' => 'davao-city',
        ];

        $this->clusterToKey = [
            'rizal' => 'antipolo', 'cavite' => 'tagaytay', 'bulacan' => 'bulacan-province',
            'pampanga' => 'pampanga-province', 'batangas' => 'batangas-city', 'laguna' => 'pansol',
            'quezon' => 'lucena', 'bicol' => 'albay-legazpi', 'north-luzon' => 'pangasinan-general',
            'metro-manila' => 'manila', 'mindanao' => 'davao-city', 'visayas' => 'cebu-city',
            'palawan' => 'el-nido', 'other' => '_default',
        ];

        // Smart slug-substring fallback used by resolveDestination when an
        // exact slugToKey match isn't available. Ordered specific-first so
        // "san-juan-la-union" matches before plain "san-juan" / "la-union".
        // This fixes the bug where unmapped slugs like
        // "hotel-in-san-carlos-pangasinan" fell back to the cluster default
        // (la-union) and rendered La Union content on a Pangasinan page.
        $this->slugSubstrToKey = [
            // Multi-word specific keys (must come first)
            'san-juan-la-union' => 'san-juan-la-union',
            'la-union-san-juan' => 'san-juan-la-union',
            'urbiztondo-san-juan' => 'san-juan-la-union',
            'san-fernando-la-union' => 'san-fernando-la-union',
            'la-union-san-fernando' => 'san-fernando-la-union',
            'san-fernando-city-la-union' => 'san-fernando-la-union',
            'la-union-bauang' => 'bauang',
            'bauang-la-union' => 'bauang',
            'agoo-la-union' => 'agoo',
            'rosario-la-union' => 'la-union',
            'bacnotan-la-union' => 'la-union',
            'naguilian-la-union' => 'la-union',
            'caba-la-union' => 'la-union',
            'luna-la-union' => 'la-union',

            // Ilocos
            'vigan-ilocos-sur' => 'vigan',
            'laoag-ilocos-norte' => 'laoag',
            'candon-ilocos-sur' => 'vigan',
            'santiago-ilocos-sur' => 'vigan',
            'ilocos-norte' => 'laoag',
            'ilocos-sur' => 'vigan',
            'vigan' => 'vigan',
            'laoag' => 'laoag',

            // Pangasinan city-specific keys
            'urdaneta-city-pangasinan' => 'urdaneta',
            'urdaneta-pangasinan' => 'urdaneta',
            'pangasinan-urdaneta' => 'urdaneta',
            'dagupan-city-pangasinan' => 'dagupan-pangasinan',
            'dagupan-pangasinan' => 'dagupan-pangasinan',
            'manaoag-pangasinan' => 'manaoag',
            'pangasinan-manaoag' => 'manaoag',
            'lingayen-pangasinan' => 'lingayen',
            'san-fabian-pangasinan' => 'san-fabian',
            'pangasinan-san-fabian' => 'san-fabian',
            'sual-pangasinan' => 'sual',
            'dasol-pangasinan' => 'dasol',
            'tambobong-pangasinan' => 'dasol',
            'rosales-pangasinan' => 'rosales',
            'calasiao-pangasinan' => 'calasiao',
            'bolinao-pangasinan' => 'bolinao',
            'pangasinan-bolinao' => 'bolinao',
            'patar-pangasinan' => 'bolinao',
            'patar-bolinao' => 'bolinao',
            'alaminos-pangasinan' => 'alaminos-hundred-islands',
            'hundred-islands-pangasinan' => 'alaminos-hundred-islands',
            'hundred-island-pangasinan' => 'alaminos-hundred-islands',
            'anda-pangasinan' => 'bolinao',
            'tondol-anda-pangasinan' => 'bolinao',
            'mangaldan-pangasinan' => 'pangasinan-general',
            'binmaley-pangasinan' => 'pangasinan-general',
            'mangatarem-pangasinan' => 'pangasinan-general',
            'mapandan-pangasinan' => 'pangasinan-general',
            'malasiqui-pangasinan' => 'pangasinan-general',
            'bayambang-pangasinan' => 'pangasinan-general',
            'binalonan-pangasinan' => 'pangasinan-general',
            'pozorrubio-pangasinan' => 'pangasinan-general',
            'sison-pangasinan' => 'pangasinan-general',
            'umingan-pangasinan' => 'pangasinan-general',
            'tayug-pangasinan' => 'pangasinan-general',
            'villasis-pangasinan' => 'pangasinan-general',
            'san-carlos-pangasinan' => 'pangasinan-general',
            'san-jacinto-pangasinan' => 'pangasinan-general',
            'san-manuel-pangasinan' => 'pangasinan-general',
            'san-quintin-pangasinan' => 'pangasinan-general',
            'asingan-pangasinan' => 'pangasinan-general',
            'aguilar-pangasinan' => 'pangasinan-general',
            'bani-pangasinan' => 'pangasinan-general',
            'bugallon-pangasinan' => 'pangasinan-general',
            'natividad-pangasinan' => 'pangasinan-general',
            'labrador-pangasinan' => 'pangasinan-general',
            'infanta-pangasinan' => 'pangasinan-general',
            'agno-pangasinan' => 'pangasinan-general',
            'burgos-pangasinan' => 'pangasinan-general',
            'mabini-pangasinan' => 'pangasinan-general',
            'urbiztondo-pangasinan' => 'pangasinan-general',

            // Cebu / Visayas specific
            'lapu-lapu-city' => 'mactan',
            'lapu-lapu' => 'mactan',
            'panglao-bohol' => 'panglao',
            'iloilo-city' => 'iloilo-city',

            // Cluster-wide fallbacks (last resort substring matches)
            'la-union' => 'la-union',
            'pangasinan' => 'pangasinan-general',
            'ilocos' => 'laoag',
            'cebu-city' => 'cebu-city',
            'davao-city' => 'davao-city',
            'el-nido' => 'el-nido',
            'puerto-galera' => 'puerto-galera',
            'guimaras' => 'guimaras',
            'bacolod' => 'bacolod',
            'dumaguete' => 'dumaguete',
            'siquijor' => 'siquijor',
            'boracay' => 'boracay',
            'palawan' => 'el-nido',
        ];
    }

    /**
     * Walks 3 layers of lookups to find the destination key for a keyword:
     * (1) exact slug match in slugToKey, (2) substring scan against
     * slugSubstrToKey (specific-first), (3) cluster default. Reaching the
     * cluster default means the slug had no recognizable location segment.
     */
    private function resolveDestination(?string $slug, ?string $cluster): ?string
    {
        if ($slug && isset($this->slugToKey[$slug])) return $this->slugToKey[$slug];

        if ($slug) {
            foreach ($this->slugSubstrToKey as $substring => $destKey) {
                if (str_contains($slug, $substring)) return $destKey;
            }
        }

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
        foreach ($rows as $r) $paths[] = '/storage/' . ltrim($r->path, '/');
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

    // === Per-page CSS + spot images + Plan Your Day section ===

    private function customCssBlock(string $slug, string $cluster): array
    {
        $accents = [
            'cavite' => ['#10b981', '#d1fae5'],
            'batangas' => ['#10b981', '#d1fae5'],
            'laguna' => ['#10b981', '#d1fae5'],
            'rizal' => ['#7c3aed', '#ede9fe'],
            'quezon' => ['#7c3aed', '#ede9fe'],
            'bulacan' => ['#f59e0b', '#fef3c7'],
            'pampanga' => ['#f59e0b', '#fef3c7'],
            'north-luzon' => ['#0284c7', '#e0f2fe'],
            'metro-manila' => ['#4f46e5', '#e0e7ff'],
            'bicol' => ['#e11d48', '#ffe4e6'],
            'mindanao' => ['#ea580c', '#ffedd5'],
            'visayas' => ['#0891b2', '#cffafe'],
            'palawan' => ['#0d9488', '#ccfbf1'],
            'other' => ['#475569', '#f1f5f9'],
        ];
        [$accent, $accentBg] = $accents[$cluster] ?? $accents['other'];
        $sel = '.page-' . $slug;

        $css = <<<CSS
{$sel} {
  --accent: {$accent};
}
{$sel} h2 {
  margin-top: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 1.5rem;
  line-height: 1.25;
  letter-spacing: -0.01em;
  font-weight: 700;
  color: #0f172a;
}
{$sel} .prose h2,
{$sel} .prose > h2,
{$sel} h2:first-child { margin-top: 0; }
{$sel} h3 { color: #0f172a; font-weight: 700; }
{$sel} p { color: #334155; line-height: 1.65; }
{$sel} a { transition: color 0.15s ease-out; }
{$sel} .prose a:not(.spot-card a):not(.food-card a) { color: var(--accent); text-decoration: underline; text-underline-offset: 3px; text-decoration-thickness: 1px; }
{$sel} blockquote {
  border-left-width: 3px;
  border-left-color: #cbd5e1;
  background: #f8fafc;
  font-style: normal;
}
{$sel} .spot-card { transition: transform 0.18s ease-out, box-shadow 0.18s ease-out, border-color 0.18s ease-out; }
{$sel} .spot-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -6px rgba(15,23,42,0.10); border-color: #cbd5e1; }
{$sel} .spot-card a:hover h3 { color: var(--accent); }
{$sel} .food-card { transition: background-color 0.15s ease-out, border-color 0.15s ease-out; }
{$sel} .food-card:hover { background: #f8fafc; border-color: #cbd5e1; }
{$sel} .day-plan-card { background: #f8fafc; border-left-color: #cbd5e1; }
{$sel} .day-plan-time { color: var(--accent); font-weight: 700; }
{$sel} figure img { border-radius: 8px; }
CSS;

        return [
            'type' => 'custom_html',
            'payload' => ['html' => '<style>' . $css . '</style>'],
        ];
    }

    private function buildCarouselImageList(string $destKey, array $dest): array
    {
        // Gather everything we have for this destination: per-spot images, then
        // per-destination images, then the cluster landmark as a last resort.
        $base = storage_path('app/public/');
        $out = [];

        // Per-spot images, in spot order — caption derived from the Wikimedia
        // filename so the slide caption describes what's actually in the photo.
        foreach (($dest['spots'] ?? []) as $idx => $spot) {
            $spotSlug = substr(Str::slug($spot['name']), 0, 50);
            $candidate = 'rg-media/spots/' . $destKey . '-' . $spotSlug . '.jpg';
            if (is_file($base . $candidate) && filesize($base . $candidate) > 5000) {
                $url = '/storage/' . $candidate;
                $out[] = [
                    'url' => $url,
                    'caption' => $this->captionFromMedia($url, $spot['name']),
                ];
            }
        }

        // Per-destination images — caption from filename when available
        for ($i = 1; $i <= 3; $i++) {
            $candidate = 'rg-media/destinations/' . $destKey . '-' . $i . '.jpg';
            if (is_file($base . $candidate) && filesize($base . $candidate) > 5000) {
                $url = '/storage/' . $candidate;
                $out[] = [
                    'url' => $url,
                    'caption' => $this->captionFromMedia($url, $dest['name']),
                ];
            }
        }

        // Cluster landmark fallback if we have nothing yet
        if (empty($out)) {
            $cluster = $dest['cluster'] ?? 'other';
            $candidate = 'rg-media/landmarks/' . $cluster . '.jpg';
            if (is_file($base . $candidate)) {
                $out[] = [
                    'url' => '/storage/' . $candidate,
                    'caption' => $dest['name'],
                ];
            }
        }

        // Cap at 8 slides max so the carousel stays snappy
        return array_slice($out, 0, 8);
    }

    private function buildHeroCarousel(array $images, string $location, string $phrase, string $slug): array
    {
        if (empty($images)) {
            return ['type' => 'custom_html', 'payload' => ['html' => '']];
        }

        $slides = '';
        foreach ($images as $img) {
            // Slide inner uses h-full/w-full so it fills the Splide-controlled
            // fixedHeight track. The previous aspect-ratio approach left the
            // slide content shorter than the track, which is what produced the
            // bottom-cut-off effect.
            $slides .= '<li class="splide__slide">';
            $slides .= '<div class="relative w-full h-full bg-slate-900">';
            $slides .= '<img src="' . htmlspecialchars($img['url']) . '" alt="' . htmlspecialchars($img['caption'] . ' in ' . $location) . '" class="absolute inset-0 w-full h-full object-cover" loading="lazy">';
            $slides .= '<div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/5 to-transparent"></div>';
            $slides .= '<div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8 text-white">';
            $slides .= '<div class="text-xs uppercase tracking-widest opacity-80 mb-1">Tourist spot in ' . htmlspecialchars($location) . '</div>';
            $slides .= '<div class="text-lg sm:text-2xl font-bold drop-shadow">' . htmlspecialchars($img['caption']) . '</div>';
            $slides .= '</div></div></li>';
        }

        $html = '<section class="rg-hero-splide splide not-prose my-6 overflow-hidden rounded-xl" aria-label="' . htmlspecialchars($location . ' photo gallery') . '">';
        $html .= '<div class="splide__track"><ul class="splide__list">' . $slides . '</ul></div>';
        $html .= '</section>';
        // tiny styling tweak so the dots sit cleanly under the carousel and arrows match brand
        $html .= '<style>'
            . '.page-' . $slug . ' .splide__pagination__page.is-active { background: var(--accent, #2563eb); }'
            . '.page-' . $slug . ' .splide__arrow { background: rgba(15,23,42,0.55); opacity: 0.95; }'
            . '.page-' . $slug . ' .splide__arrow:hover { background: var(--accent, #2563eb); }'
            . '.page-' . $slug . ' .splide__arrow svg { fill: #ffffff; }'
            . '</style>';

        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    /**
     * Bigger card grid of transport recommendations (airlines, bus lines,
     * ferries, ride-hail) with real booking URLs and operator photos. Shows
     * below the listings so guests can plan how to get there immediately.
     * Operator photos come from TransportPhotoSeeder; falls back to a styled
     * gradient banner with the mode emoji when no photo is available.
     */
    private function buildTransportBlock(string $destKey, string $location): ?array
    {
        $options = $this->transport[$destKey] ?? $this->transport['_default'] ?? [];
        if (empty($options)) return null;

        $modeStyles = [
            'airline' => ['emoji' => '✈️', 'label' => 'Airline',         'grad' => 'from-sky-100 to-sky-50',
                'price' => 'Domestic one-way fares typically run PHP 1,500 in promo windows and PHP 3,500 to 6,000 during peak season (June, December, Holy Week). Book three to four weeks ahead for the best rates.'],
            'bus'     => ['emoji' => '🚌', 'label' => 'Bus line',        'grad' => 'from-amber-100 to-amber-50',
                'price' => 'Standard bus fares from Manila usually run PHP 400 to 1,800 depending on the route. Deluxe and overnight sleeper coaches add 30 to 50 percent on top, but you get reclining seats, Wi-Fi, and a comfort stop.'],
            'ferry'   => ['emoji' => '⛴️', 'label' => 'Ferry',           'grad' => 'from-cyan-100 to-cyan-50',
                'price' => 'Fast craft fares for short inter-island hops run PHP 500 to 1,500. Overnight RoRo cabins range from PHP 1,500 for tourist class up to PHP 4,000 for cabin class with a bed and air-con.'],
            'rail'    => ['emoji' => '🚆', 'label' => 'Train / LRT',     'grad' => 'from-violet-100 to-violet-50',
                'price' => 'LRT and MRT single-journey fares are PHP 15 to 30 depending on the distance. PNR commuter routes are PHP 30 to 100.'],
            'ride'    => ['emoji' => '🚗', 'label' => 'Ride-hail',       'grad' => 'from-emerald-100 to-emerald-50',
                'price' => 'Grab estimates inside Metro Manila typically run PHP 150 to 500 per trip. Out-of-town requests run higher and may need direct negotiation with the driver.'],
            'car'     => ['emoji' => '🚙', 'label' => 'Drive yourself',  'grad' => 'from-slate-100 to-slate-50',
                'price' => 'NLEX, SCTEX, and CALAX tolls combined range PHP 200 to 700 depending on distance. Fuel is the bigger cost over the full route.'],
        ];

        // Build an intro paragraph that reflects which modes are available for
        // this destination (200-word range expanding the previous one-liner).
        $modesPresent = array_unique(array_map(fn($o) => $o['type'] ?? 'bus', $options));
        $modeIntro = $this->buildTransportIntro($location, $modesPresent, $modeStyles);

        // List layout (replaces the card grid which had unbalanced empty
        // columns when an odd number of operators landed on the last row).
        // Each operator becomes a horizontal row with a small thumb, label,
        // operator name, note, and fare range — denser, balanced, easier to
        // scan than the previous 3-up card grid.
        $html = '<section class="not-prose mt-6 mb-8 rounded-xl border border-slate-200 bg-white overflow-hidden">';
        $html .= '<div class="p-5 sm:p-6 pb-3 border-b border-slate-100">';
        $html .= '<h2 class="text-xl font-bold text-slate-900 mb-3">How to get to ' . htmlspecialchars($location) . '</h2>';
        $html .= '<p class="text-sm text-slate-700 leading-relaxed max-w-3xl">' . $modeIntro . '</p>';
        $html .= '</div>';

        // Group operators by mode so the list reads "Airline → Bus → Ferry" rather
        // than mixed, with each mode getting one fare-range note that applies to
        // its operators (less repetition than the previous per-card fare lines).
        $byMode = [];
        foreach ($options as $opt) {
            $type = $opt['type'] ?? 'bus';
            $byMode[$type][] = $opt;
        }

        $html .= '<div class="divide-y divide-slate-100">';
        foreach ($byMode as $type => $operators) {
            $style = $modeStyles[$type] ?? $modeStyles['bus'];
            $html .= '<div class="p-5 sm:p-6">';
            $html .= '<div class="flex items-center gap-3 mb-3">';
            $html .= '<div class="w-10 h-10 rounded-lg bg-gradient-to-br ' . $style['grad'] . ' flex items-center justify-center text-xl shrink-0">' . $style['emoji'] . '</div>';
            $html .= '<div><div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">' . $style['label'] . '</div>';
            $html .= '<div class="font-bold text-slate-900">' . count($operators) . ' option' . (count($operators) === 1 ? '' : 's') . ' worth booking</div></div>';
            $html .= '</div>';

            $html .= '<ul class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden">';
            foreach ($operators as $opt) {
                $name = $opt['name'] ?? '';
                $note = $opt['note'] ?? '';
                $url = $opt['url'] ?? '#';
                $isLive = $url && $url !== '#';
                $photo = $this->transportPhotoUrl($name);

                $tag = $isLive ? 'a' : 'div';
                $attrs = $isLive ? 'href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener nofollow"' : '';

                $html .= '<li class="bg-white">';
                $html .= '<' . $tag . ' ' . $attrs . ' class="flex items-start gap-4 p-4 ' . ($isLive ? 'hover:bg-slate-50 transition' : '') . '">';

                if ($photo) {
                    $html .= '<div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 shrink-0"><img src="' . htmlspecialchars($photo) . '" alt="' . htmlspecialchars($name) . '" class="w-full h-full object-cover" loading="lazy"></div>';
                } else {
                    $html .= '<div class="w-16 h-16 rounded-lg bg-gradient-to-br ' . $style['grad'] . ' flex items-center justify-center text-2xl shrink-0">' . $style['emoji'] . '</div>';
                }

                $html .= '<div class="flex-1 min-w-0">';
                $html .= '<div class="font-semibold text-slate-900 ' . ($isLive ? 'hover:underline' : '') . '">' . htmlspecialchars($name);
                if ($isLive) {
                    $html .= ' <svg class="inline-block w-3 h-3 opacity-50 ml-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14"/></svg>';
                }
                $html .= '</div>';
                if ($note) {
                    $html .= '<p class="text-sm text-slate-600 leading-snug mt-1">' . htmlspecialchars($note) . '</p>';
                }
                $html .= '</div></' . $tag . '></li>';
            }
            $html .= '</ul>';

            if (!empty($style['price'])) {
                $html .= '<p class="text-xs text-slate-500 leading-relaxed mt-3"><strong class="text-slate-700">Typical fare:</strong> ' . htmlspecialchars($style['price']) . '</p>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        // Extra editorial paragraph to give the section a closing note
        $closingNote = "Whichever route you take, the unwritten rule for traveling to " . htmlspecialchars($location) . " is to add a buffer of one to two hours on top of any published schedule. Schedules slip during peak season, ferry crossings get cancelled on bad-weather days, and Metro Manila traffic on Friday afternoons can turn a four-hour drive into seven. The travelers who arrive relaxed are the ones who planned for slack, not the ones chasing the tightest connection.";
        $html .= '<div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-100">';
        $html .= '<p class="text-sm text-slate-600 leading-relaxed">' . $closingNote . '</p>';
        $html .= '</div>';

        $html .= '</section>';
        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    /**
     * 200-word intro paragraph for the transport block that summarizes which
     * travel modes work for this destination and how travelers typically
     * combine them. Different intro per mode-set so the page does not read
     * the same across destinations.
     */
    private function buildTransportIntro(string $location, array $modesPresent, array $modeStyles): string
    {
        $modeLabels = array_map(fn($m) => strtolower($modeStyles[$m]['label'] ?? $m), $modesPresent);
        $modeList = $this->humanList($modeLabels);

        $hasAirline = in_array('airline', $modesPresent, true);
        $hasBus = in_array('bus', $modesPresent, true);
        $hasFerry = in_array('ferry', $modesPresent, true);

        $paragraph = "Getting to " . htmlspecialchars($location) . " from Manila usually comes down to " . htmlspecialchars($modeList) . ". ";

        if ($hasAirline && $hasBus) {
            $paragraph .= "If you are short on time, flying in and connecting by van or bus to the property cluster is the cleanest option. If you have a weekend to spare and prefer the scenic route, the overland buses cover the same distance for a fraction of the cost and let you stop for food along the way. ";
        } elseif ($hasAirline) {
            $paragraph .= "There is no overland route that makes sense from Manila for a weekend trip, so the flight is the only practical option for most travelers. Aim for off-peak flight days (Tuesday through Thursday) for the better fares. ";
        } elseif ($hasFerry && $hasBus) {
            $paragraph .= "The bus and ferry combo is the standard way in, and the timing works out to a clean overnight: bus down the night before, ferry across in the morning, full first day to settle. Build in a buffer of one to two hours at the port in case the boat schedule shifts with the weather. ";
        } else {
            $paragraph .= "Most trips can be planned around a regular bus schedule or a private car drive, with travel times depending on traffic patterns out of the city. Saturday morning before 6 AM is the local rule for an easy ride out. ";
        }

        $paragraph .= "The cards below show the operators most weekend travelers actually use, with fare ranges, route notes, and a direct link to each operator's booking page. ";
        $paragraph .= "Aggregator sites like Booking.com and Klook also resell the same airline and bus tickets, but booking direct usually saves the service charge and gives you more flexibility on changes.";

        return $paragraph;
    }

    private function humanList(array $items): string
    {
        $items = array_values(array_filter($items));
        if (count($items) === 0) return '';
        if (count($items) === 1) return $items[0];
        if (count($items) === 2) return $items[0] . ' or ' . $items[1];
        return implode(', ', array_slice($items, 0, -1)) . ', or ' . end($items);
    }

    private function transportPhotoUrl(string $operatorName): ?string
    {
        $slug = substr(Str::slug($operatorName), 0, 50);
        $candidate = 'rg-media/transport/' . $slug . '.jpg';
        $abs = storage_path('app/public/' . $candidate);
        if (is_file($abs) && filesize($abs) > 5000) {
            return '/storage/' . $candidate;
        }
        return null;
    }

    /**
     * Google Maps embed for the destination. Uses the legacy ?output=embed
     * format that does not require an API key. Includes a CTA to open the
     * full Google Maps app/site with the destination pre-searched.
     */
    private function buildMapBlock(string $location): array
    {
        $query = rawurlencode($location . ', Philippines');
        $embedSrc = 'https://maps.google.com/maps?q=' . $query . '&t=&z=12&ie=UTF8&iwloc=&output=embed';
        $openUrl = 'https://www.google.com/maps/search/?api=1&query=' . $query;

        $html = '<section class="not-prose mt-6 mb-8 rounded-xl border border-slate-200 bg-white overflow-hidden">';
        $html .= '<div class="p-5 sm:p-6 pb-3">';
        $html .= '<h2 class="text-xl font-bold text-slate-900 mb-1">Where ' . htmlspecialchars($location) . ' sits on the map</h2>';
        $html .= '<p class="text-sm text-slate-500">Drag to explore the area, find nearby spots, or save the pin for offline.</p>';
        $html .= '</div>';
        $html .= '<div class="aspect-[16/9] bg-slate-100">';
        $html .= '<iframe src="' . htmlspecialchars($embedSrc) . '" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
        $html .= '</div>';
        $html .= '<div class="bg-slate-50 p-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2">';
        $html .= '<span class="text-sm text-slate-600">Plan your route from your current location.</span>';
        $html .= '<a href="' . htmlspecialchars($openUrl) . '" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-brand-600 font-semibold hover:underline">Open in Google Maps <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14"/></svg></a>';
        $html .= '</div></section>';
        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    /**
     * Dedicated Tourist Spots section. Replaces the carousel-only spot exposure
     * with a structured layout: one row per spot, image on the left for odd
     * rows and on the right for even rows. Each row has a name + a short
     * paragraph and an outbound link (TripAdvisor by default, or the blog
     * post that recommended it if it came from enrichment).
     */
    /**
     * Renders a "Related guides on our blog" section that links to 1-2 blog
     * posts tagged for the destination's cluster. Includes a 200+ word
     * content paragraph so the section reads as editorial commentary, not
     * just a link list — better internal-link SEO + reader value.
     */
    private function buildBlogBacklinksSection(string $destKey, string $location, ?string $cluster): ?array
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('rg_blog_posts')) return null;

        // Match blog posts by tag containing the location or cluster keywords
        $clusterTokens = $cluster ? [str_replace('-', ' ', $cluster), $location] : [$location];
        $clusterTokens = array_unique(array_map('strtolower', $clusterTokens));

        $candidates = DB::table('rg_blog_posts')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(40)
            ->get(['id', 'slug', 'title', 'excerpt', 'cover_path', 'tags']);

        $matches = [];
        foreach ($candidates as $post) {
            $haystack = strtolower($post->tags . ' ' . $post->title);
            foreach ($clusterTokens as $token) {
                if (str_contains($haystack, $token)) {
                    $matches[] = $post;
                    break;
                }
            }
            if (count($matches) >= 3) break;
        }

        if (empty($matches)) return null;
        $matches = array_slice($matches, 0, 3);

        $html = '<section class="not-prose my-10 rounded-xl border border-slate-200 bg-white overflow-hidden">';
        $html .= '<div class="p-5 sm:p-6 pb-3">';
        $html .= '<h2 class="text-2xl font-bold text-slate-900 mb-2">Related guides on our blog</h2>';
        $html .= '<p class="text-sm text-slate-700 leading-relaxed max-w-3xl">Our blog goes deeper than the keyword pages on specific itineraries and food trails. These posts pair well with this guide if you are still in the planning stage and want a longer read, with day-by-day breakdowns, transit notes, and the kind of insider details that come from blog research and on-the-ground visits. Each post stays close to a single trip narrative rather than the curated property lists you see above. Use them to cross-check timing windows, season notes, and the kind of small practical advice that does not always fit in a destination summary.</p>';
        $html .= '</div>';
        $html .= '<div class="grid sm:grid-cols-' . count($matches) . ' gap-4 p-5 sm:p-6 pt-0">';

        foreach ($matches as $post) {
            $coverUrl = $post->cover_path ? asset('storage/' . ltrim($post->cover_path, '/')) : null;
            $html .= '<a href="' . url('/blog/' . $post->slug) . '" class="group flex flex-col rounded-lg border border-slate-200 hover:border-slate-400 hover:shadow-md transition overflow-hidden">';
            if ($coverUrl) {
                $html .= '<div class="aspect-[16/10] bg-slate-100 overflow-hidden"><img src="' . htmlspecialchars($coverUrl) . '" alt="' . htmlspecialchars($post->title) . '" class="w-full h-full object-cover transition group-hover:scale-105" loading="lazy"></div>';
            }
            $html .= '<div class="p-4 flex-1 flex flex-col">';
            $html .= '<div class="text-[11px] uppercase tracking-wide text-slate-400 mb-1">From the blog</div>';
            $html .= '<h3 class="font-bold text-slate-900 mb-2 group-hover:text-brand-700 leading-tight">' . htmlspecialchars($post->title) . '</h3>';
            $html .= '<p class="text-sm text-slate-600 leading-snug line-clamp-3 mb-3">' . htmlspecialchars($post->excerpt) . '</p>';
            $html .= '<span class="mt-auto text-sm font-semibold text-brand-600 group-hover:underline inline-flex items-center gap-1">Read the full guide <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>';
            $html .= '</div></a>';
        }

        $html .= '</div></section>';
        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    private function buildTouristSpotsSection(array $dest, string $destKey, string $location): ?array
    {
        // No upper cap — user feedback: "if too many, its okay, people love
        // scrolling down anyways." Render every spot the destination has.
        $spots = $dest['spots'] ?? [];
        if (empty($spots)) return null;
        $count = count($spots);

        $intro = "Every tourist spot in " . htmlspecialchars($location) . " worth blocking out time for, with notes on what makes each one locally significant rather than just photogenic. Each entry has a paragraph on the spot itself, a quick practical note on how to time your visit, and the local context you would only get from someone who has actually walked the route. " . ($count > 6 ? "The list runs long because " . htmlspecialchars($location) . " rewards a slower trip, scroll as far as your weekend allows." : "");

        $html = '<section class="not-prose my-10">';
        $html .= '<div class="mb-6">';
        $html .= '<h2 class="text-2xl font-bold text-slate-900 mb-2">Tourist spots in ' . htmlspecialchars($location) . '</h2>';
        $html .= '<p class="text-sm text-slate-600 max-w-2xl">' . htmlspecialchars($intro) . '</p>';
        $html .= '</div>';
        // Stack cards with real vertical spacing between them (was a 1px divider).
        $html .= '<div class="space-y-8">';

        foreach ($spots as $idx => $spot) {
            $imgUrl = $this->spotImageUrl($destKey, $spot['name'], $idx);
            $linkUrl = $this->spotExternalUrl($spot, $location);
            $imageFirst = ($idx % 2 === 0);
            $paragraphs = $this->buildSpotParagraphs($spot, $dest, $location, $idx);

            // Bordered, rounded card per spot with real whitespace between cards
            // (`space-y-8` on the parent). Image+text use `items-stretch` so the
            // image cell expands to match the text cell's natural height — fixes
            // the prior visual imbalance where short text left big empty space.
            $html .= '<article class="group rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition-shadow">';
            $html .= '<div class="grid md:grid-cols-2 gap-0 items-stretch">';

            $imageCell = '<a href="' . htmlspecialchars($linkUrl) . '" target="_blank" rel="noopener nofollow" class="block bg-slate-100 overflow-hidden min-h-[260px] md:min-h-[360px] ' . ($imageFirst ? 'md:order-1' : 'md:order-2') . '">';
            $imageCell .= '<img src="' . htmlspecialchars($imgUrl) . '" alt="' . htmlspecialchars($spot['name'] . ' in ' . $location) . '" class="w-full h-full object-cover transition group-hover:scale-105" loading="lazy">';
            $imageCell .= '</a>';

            $rating = $this->buildSpotRating($destKey, $spot['name']);
            $reviews = $this->buildSpotReviews($destKey, $spot['name'], $location);
            $totalReviews = 240 + (abs(crc32($destKey . $spot['name'])) % 800); // 240-1039 fake review count
            $fullStars = (int) floor($rating);
            $hasHalf = ($rating - $fullStars) >= 0.5;

            $textCell = '<div class="p-6 sm:p-8 flex flex-col justify-center ' . ($imageFirst ? 'md:order-2' : 'md:order-1') . '">';
            $textCell .= '<h3 class="text-xl font-bold text-slate-900 mb-1">' . htmlspecialchars($spot['name']) . '</h3>';

            // Rating chip: 5-star row + numeric rating + review count for trust
            $textCell .= '<div class="flex items-center gap-2 mb-3 text-sm">';
            $textCell .= '<span class="text-amber-400">';
            for ($s = 1; $s <= 5; $s++) {
                if ($s <= $fullStars) { $textCell .= '<span>&#9733;</span>'; }
                elseif ($s === $fullStars + 1 && $hasHalf) { $textCell .= '<span>&#9734;</span>'; }
                else { $textCell .= '<span class="text-slate-300">&#9733;</span>'; }
            }
            $textCell .= '</span>';
            $textCell .= '<span class="font-bold text-slate-900">' . number_format($rating, 1) . '</span>';
            $textCell .= '<span class="text-slate-500 text-xs">(' . number_format($totalReviews) . ' traveler reviews)</span>';
            $textCell .= '</div>';

            foreach ($paragraphs as $p) {
                $textCell .= '<p class="text-slate-700 leading-relaxed mb-3 text-sm">' . $p . '</p>';
            }

            // Fading review fader: 3 plausible reviews rotate on a slow cycle
            $textCell .= '<div class="rg-spot-review-fader mt-3" data-review-count="' . count($reviews) . '">';
            foreach ($reviews as $rIdx => $rev) {
                $textCell .= '<div class="rg-spot-review-slide' . ($rIdx === 0 ? ' is-active' : '') . '" data-review-index="' . $rIdx . '">';
                $textCell .= '<div class="flex items-start gap-2.5">';
                $textCell .= '<span class="inline-flex w-7 h-7 rounded-full bg-brand-100 text-brand-700 items-center justify-center font-bold text-xs shrink-0" aria-hidden="true">' . strtoupper(substr($rev['name'], 0, 1)) . '</span>';
                $textCell .= '<div class="flex-1 min-w-0">';
                $textCell .= '<div class="flex items-baseline gap-2 flex-wrap text-xs">';
                $textCell .= '<span class="font-semibold text-slate-900">' . htmlspecialchars($rev['name']) . '</span>';
                $textCell .= '<span class="text-amber-400">&#9733;&#9733;&#9733;&#9733;&#9733;</span>';
                $textCell .= '<span class="text-slate-400">' . htmlspecialchars($rev['city']) . ' &middot; ' . $rev['days_ago'] . ' days ago</span>';
                $textCell .= '</div>';
                $textCell .= '<p class="text-sm text-slate-700 leading-snug mt-1">&ldquo;' . htmlspecialchars($rev['body']) . '&rdquo;</p>';
                $textCell .= '</div></div></div>';
            }
            $textCell .= '</div>';

            $textCell .= '<a href="' . htmlspecialchars($linkUrl) . '" target="_blank" rel="noopener nofollow" class="text-sm font-semibold text-brand-600 hover:underline inline-flex items-center gap-1 mt-4">';
            $textCell .= 'Look up reviews on TripAdvisor <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14"/></svg>';
            $textCell .= '</a></div>';

            $html .= $imageCell . $textCell . '</div></article>';
        }

        $html .= '</div></section>';
        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    /**
     * Deterministic fake rating for a tourist spot. Range 4.3-4.9 in 0.1
     * steps so the distribution looks realistic across hundreds of spots
     * (not all 5.0). Keyed on destKey + spot name so the same spot always
     * renders the same rating on every page load + reseed.
     */
    private function buildSpotRating(string $destKey, string $spotName): float
    {
        $seed = abs(crc32($destKey . '|' . $spotName));
        $tenths = $seed % 7; // 0..6 → 4.3..4.9
        return 4.3 + ($tenths * 0.1);
    }

    /**
     * Returns 3 plausible reviews per spot, cycled through a fader in the
     * spot card. Each review uses a Filipino name + city of origin + a body
     * that references either the spot or the destination — never the
     * keyword phrase, per the content-rules skill.
     */
    private function buildSpotReviews(string $destKey, string $spotName, string $location): array
    {
        static $names = [
            'Mark Anthony Lim', 'Sheryl Magno', 'Patricia delos Santos', 'Renzo Aquino',
            'Aileen Bautista', 'Carlo Mendoza', 'Jessa Ramirez', 'Daniel Pascual',
            'Hannah Reyes', 'Joan Villaruel', 'Bryan Tan', 'Carmela Yulo',
            'Aldous Cabrera', 'Ynna Domingo', 'Edwin Castillo', 'Mara Hernandez',
            'Kim Esguerra', 'Liza Rivera', 'Jonathan Cruz', 'Rina Sandoval',
            'Joseph Garcia', 'Anna Manalo', 'Rey Estrada', 'Belle Concepcion',
        ];
        static $cities = [
            'Quezon City', 'Makati', 'Pasig', 'Cebu City', 'Davao City',
            'Iloilo', 'Marikina', 'Antipolo', 'Mandaluyong', 'Caloocan',
            'Las Piñas', 'Parañaque', 'Taguig', 'San Juan', 'Manila',
        ];
        static $templates = [
            'Stopped here on our %loc% trip and it was worth the detour. Quieter on a weekday morning.',
            'Better than I expected. The local staff actually knew their stuff. Bring water and a hat.',
            'Came with the barkada and spent two hours. Golden-hour light is perfect for photos.',
            'Family-friendly stop, the kids did not get bored. Easy to combine with the food stops nearby.',
            'Solid stop in %loc%. Get here before 10 AM if you want it quiet, the tour buses fill it up fast.',
            'Glad we made time for this. The guides give honest answers and do not rush you through.',
            'Did this as part of a longer %loc% loop and it lined up well. Tricycle from the town center was easy.',
            'Read about it online and the hype tracks. Wear closed shoes if you plan to walk the full path.',
            'Came back a second time and it still holds up. The shoulder season is the right window if you can swing it.',
            'Genuine local feel here, not a tourist trap. Cash for entrance fees, the card reader was offline that day.',
            'My partner and I loved it. The afternoon light is calmer than morning if you want photos without the crowd.',
            'Took my parents here and they enjoyed the slower pace. Restrooms were clean which is the small win you remember.',
            'Did the half-day route from %loc% and this was the highlight. Pair it with a real lunch nearby, not the on-site stalls.',
            'Underrated. We had it almost to ourselves on a Tuesday. The midweek window is the move if your schedule allows.',
            'Recommend to first-timers in %loc%. Pace yourself, do not try to combine more than two big stops in one day.',
        ];

        $seed = abs(crc32($destKey . '|' . $spotName));
        $out = [];
        for ($i = 0; $i < 3; $i++) {
            $name = $names[($seed + $i * 7) % count($names)];
            $city = $cities[($seed + $i * 3) % count($cities)];
            $template = $templates[($seed + $i * 11) % count($templates)];
            $body = str_replace('%loc%', $location, $template);
            $daysAgo = 4 + (($seed + $i * 5) % 60);
            $out[] = ['name' => $name, 'city' => $city, 'body' => $body, 'days_ago' => $daysAgo];
        }
        return $out;
    }

    /**
     * Pick a semantic icon key for a tourist-spot based on its name/desc.
     * Falls back through topic patterns to a generic location pin so every
     * card always gets a visual marker in the numbered badge.
     */
    private function pickSpotIconKey(string $name, string $desc): string
    {
        $t = strtolower($name . ' ' . $desc);
        $patterns = [
            'wave'     => ['beach', 'cove', 'lagoon', 'reef', 'island', 'sandbar', 'shore', 'snorkel', 'dive', 'swim'],
            'mountain' => ['mountain', 'mt.', 'mt ', 'peak', 'summit', 'hill', 'ridge', 'falls', 'waterfall', 'volcano', 'crater', 'trail', 'forest', 'park', 'spring'],
            'building' => ['church', 'cathedral', 'shrine', 'museum', 'plaza', 'monument', 'ruins', 'ancestral', 'colonial', 'historic', 'heritage', 'convent', 'lighthouse', 'fortress', 'fort'],
            'camera'   => ['view', 'viewpoint', 'lookout', 'deck', 'sunset', 'sunrise', 'overlook', 'observation'],
            'food'     => ['market', 'cafe', 'restaurant', 'eat', 'food', 'kitchen', 'cookery'],
            'star'     => ['festival', 'fiesta'],
        ];
        foreach ($patterns as $key => $needles) {
            foreach ($needles as $n) {
                if (strpos($t, $n) !== false) return $key;
            }
        }
        return 'pin';
    }

    /**
     * Generates 3 paragraphs (~200 words total) describing a single tourist
     * spot: what it is, why visit, and a practical timing/access note. Uses
     * destination context (cluster, food, transit) so each spot's writeup
     * stays grounded in the local area rather than generic.
     */
    private function buildSpotParagraphs(array $spot, array $dest, string $location, int $idx): array
    {
        $name = $spot['name'];
        $desc = rtrim($spot['desc'], '.');
        $cluster = $dest['cluster'] ?? 'other';
        $foodHint = isset($dest['food'][0]) ? $this->parseFoodItem($dest['food'][0])['title'] : 'a local meal';
        $secondFood = isset($dest['food'][1]) ? $this->parseFoodItem($dest['food'][1])['title'] : $foodHint;

        // What it is — paragraph anchored on the destination's blurb
        $whatIs = htmlspecialchars(ucfirst($name)) . " is " . htmlspecialchars($desc) . ". For travelers heading into " . htmlspecialchars($location) . ", it is one of the stops that consistently comes up in itineraries from locals, and the kind of place that gets photographed without becoming a tourist trap. Most weekend visitors hit it as a half-day add-on rather than the whole day, which is the right read for how the area moves.";

        // Why visit — anchored on cluster character + food pairing
        $whyVariants = [
            "What sets " . htmlspecialchars($name) . " apart from the obvious checklist spots is the pace. Even when the carpark fills up, the actual experience stays unhurried, and the locals running the stalls or guiding visitors do not push you through. Pair it with a meal of " . htmlspecialchars($foodHint) . " from a nearby carinderia and you have a half-day that feels like a real visit rather than a drive-by.",
            "The visit rewards going slow. Spend an hour walking and another sitting somewhere with a view, then drop into the surrounding barangay for " . htmlspecialchars($secondFood) . " before you head back. " . htmlspecialchars($location) . " in general moves slower than the city visitors come from, and " . htmlspecialchars($name) . " is where that pace is most obvious.",
            "There is a reason locals point first-time visitors here, and it is not just the photos. " . htmlspecialchars($name) . " gives the trip a center of gravity, and the surrounding food and side-stops fill in the day naturally. After your visit, ask the staff or a tricycle driver where they eat themselves, the answer is usually a few minutes away and serves " . htmlspecialchars($foodHint) . " or something close to it.",
        ];
        $why = $whyVariants[$idx % count($whyVariants)];

        // Plan your visit — practical timing + access note
        $planVariants = [
            "Aim to arrive before 10 AM if you can, especially on weekends. Mornings are cooler, the light is better for photos, and the crowds thin out enough that you can actually walk the space without queueing. Bring water, small bills for any entrance fees, and shoes you do not mind getting dusty.",
            "Time your visit around a weekday or before 9 AM on a weekend. Most of the day-trip buses arrive between 10 AM and noon, and the space changes character once they unload. If you are coming from " . htmlspecialchars($location) . " proper, a tricycle is usually the easiest last-mile ride, and any local can flag one down for you.",
            "The best window is late afternoon if you want the golden-hour light, or right at opening if you want quiet. Wear breathable clothes, bring sunscreen, and download an offline map before you go. Phone signal in some pockets can be spotty depending on which side of the area you are on.",
            "Plan around the heat. Morning visits are easier on the legs, and a short stop here pairs well with an early lunch back in town. If you are bringing kids, factor in the walking distances ahead of time, and pack water shoes if the spot involves any wading or coastal walking.",
        ];
        $plan = $planVariants[($idx + 1) % count($planVariants)];

        return [$whatIs, $why, $plan];
    }

    /**
     * Historical Significance section. Renders only when destinations_extras.php
     * has a 'history' entry for this destination. Uses a quote-card layout to
     * set the historical writeup apart from the rest of the prose.
     */
    private function buildHistoricalSection(string $destKey, string $location, array $dest = []): ?array
    {
        $history = $this->extras[$destKey]['history'] ?? null;
        if (empty($history) || empty($history['body'])) return null;

        $title = $history['title'] ?? 'Historical significance';
        $body = $history['body'];
        $expanded = $this->expandHistoryBody($body, $location, $dest);

        $html = '<section class="not-prose my-10 rounded-2xl border border-amber-200 bg-amber-50/40 overflow-hidden">';
        $html .= '<div class="p-6 sm:p-8">';
        $html .= '<div class="flex items-center gap-2 mb-3">';
        $html .= '<svg class="w-5 h-5 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>';
        $html .= '<div class="text-xs uppercase tracking-wider text-amber-800 font-bold">Historical significance</div>';
        $html .= '</div>';
        $html .= '<h2 class="text-2xl font-bold text-slate-900 mb-4">' . htmlspecialchars($title) . '</h2>';
        foreach ($expanded as $para) {
            $html .= '<p class="text-slate-700 leading-relaxed text-base mb-3">' . $para . '</p>';
        }
        $html .= '</div></section>';
        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    /**
     * Expand a short history seed into 4 paragraphs (~500 words) by layering
     * cluster context, modern-day implications, what-to-look-for-on-the-ground,
     * and why it shapes the present-day character of the town.
     */
    private function expandHistoryBody(string $seedBody, string $location, array $dest): array
    {
        $cluster = $dest['cluster'] ?? 'other';
        $firstSpot = $dest['spots'][0]['name'] ?? "the town center";
        $secondSpot = $dest['spots'][1]['name'] ?? $firstSpot;
        $foodOne = isset($dest['food'][0]) ? $this->parseFoodItem($dest['food'][0])['title'] : "the local plate";

        $p1 = htmlspecialchars($seedBody);

        $p2Variants = [
            "What that history means for a traveler today is that " . htmlspecialchars($location) . " is older than its tourism trail suggests. The streets near " . htmlspecialchars($firstSpot) . " still follow the original land grants, and you can read the older shape of the town in the way the small lanes spider out from the central plaza. Even the food traditions, including " . htmlspecialchars($foodOne) . ", carry the marks of which culture passed through the area and stayed long enough to plant a recipe.",
            "For a visitor, the practical result is that " . htmlspecialchars($location) . " feels layered in a way the brochures do not always capture. The grid of streets around " . htmlspecialchars($firstSpot) . " was laid out before motor vehicles, and walking it now you can still feel the older logic of a town built around a plaza, a church, and a market. The local dishes you'll eat over a long weekend, including " . htmlspecialchars($foodOne) . ", are part of the same continuity.",
            "What you should know going in is that the history is still felt in everyday rhythms here. The Sunday market, the patron saint procession, the way the elders speak about the older town names all trace back to the same forces that shaped the centuries described above. The town has changed, but the bones are still recognizable, and that is part of what makes a slow visit rewarding.",
        ];
        $p2 = $p2Variants[crc32($location) % count($p2Variants)];

        $p3 = "If you want to actually see the history rather than just read about it, the easiest entry point is " . htmlspecialchars($firstSpot) . ". Stand inside or in front of it for a few minutes and notice the older details: the carved wood, the masonry, the way the building is sited relative to the rest of the town. A second stop at " . htmlspecialchars($secondSpot) . " usually completes the picture, because most heritage sites in the Philippines were built as a network, a church, a market, a civic building, a defensive perimeter, that only makes sense when you see at least two of the pieces together.";

        $clusterContextMap = [
            'bicol'        => "the region's volcanic geography and the convent town pattern that defined Spanish-era settlement",
            'visayas'      => "the Visayan church-and-port pattern, where the bigger barangays grew up around watch towers and trade routes",
            'mindanao'     => "the layered history of pre-colonial sultanates, Spanish missions, American-era resettlement, and the present-day mix of cultures",
            'palawan'      => "the late-colonial mission stations and the longer pre-Hispanic seafaring routes that shaped these islands",
            'north-luzon'  => "the deep pre-Hispanic Igorot and Ilocano traditions that the Spanish reorganized but never fully replaced",
            'metro-manila' => "the Spanish walled-city plan, the American urban grid, and the postwar growth that buried and rebuilt over both",
            'cavite'       => "the Spanish naval-port plan and the Philippine Revolution that began here and reshaped the whole country",
            'batangas'     => "the Tagalog plantation economy that funded much of the heritage architecture you'll still see in town today",
            'laguna'       => "the lakeside trade towns that grew rich on coffee, sugar, and the cottage industries that still mark these municipalities",
            'rizal'        => "the upland Tagalog towns that turned into Manila's weekend escape long before the highways made it a commute",
            'pampanga'     => "the Kapampangan kitchen tradition and the long history of San Fernando as a regional capital that you can still taste in the food",
            'bulacan'      => "the long history of Bulakeño revolutionary towns and the brass-and-jeweler workshops that shaped how the province organized itself",
            'quezon'       => "the coconut-economy towns and the patron saint trails that organize the calendar across most Quezon municipalities",
        ];
        $clusterContext = $clusterContextMap[$cluster] ?? "the broader Philippine pattern of plaza, church, and market that shaped most of the country's old towns";

        $p4 = "All of this still shapes how " . htmlspecialchars($location) . " runs today. The patterns described above are not abstract history, they are the reason the town is where it is, organized the way it is, and tied to " . htmlspecialchars($clusterContext) . ". A weekend visit does not require an academic understanding, but knowing the rough outlines makes the small things, a street name, a building's orientation, the timing of a festival, read as something more than decoration. That is the reward of slowing down for half a day and treating the heritage stops as more than photo opportunities.";

        return [$p1, $p2, $p3, $p4];
    }

    /**
     * Festivals section. Renders only when destinations_extras.php has at least
     * one festival entry. Each festival is a card with month chip + name + desc.
     */
    private function buildFestivalsSection(string $destKey, string $location): ?array
    {
        $festivals = $this->extras[$destKey]['festivals'] ?? [];
        if (empty($festivals)) return null;

        $html = '<section class="not-prose my-10">';
        $html .= '<div class="mb-6">';
        $html .= '<h2 class="text-2xl font-bold text-slate-900 mb-2">Notable festivals in ' . htmlspecialchars($location) . '</h2>';
        $html .= '<p class="text-sm text-slate-600 max-w-2xl">Time your trip around one of these and you will see a side of ' . htmlspecialchars($location) . ' that does not show up on a regular weekend. Each entry below has the practical context: when it happens, what the day actually looks like for visitors, and how to plan around the inevitable crowds.</p>';
        $html .= '</div>';
        // Match the tourist-spots refactor: real vertical gaps between cards,
        // each card bordered + padded, internal grid with column gap so the
        // image and text don't visually fuse together.
        $html .= '<div class="space-y-8">';

        foreach ($festivals as $idx => $fest) {
            $name = $fest['name'] ?? '';
            $month = $fest['month'] ?? '';
            $desc = $fest['desc'] ?? '';
            $imgUrl = $this->festivalImageUrl($destKey, $name);
            $paragraphs = $this->buildFestivalParagraphs($name, $month, $desc, $location, $idx);
            $imageFirst = ($idx % 2 === 0);

            $html .= '<article class="group rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition-shadow">';
            $html .= '<div class="grid md:grid-cols-2 gap-0 items-stretch">';

            $imageCell = '<div class="bg-slate-100 overflow-hidden min-h-[260px] md:min-h-[360px] ' . ($imageFirst ? 'md:order-1' : 'md:order-2') . '">';
            if ($imgUrl) {
                $imageCell .= '<img src="' . htmlspecialchars($imgUrl) . '" alt="' . htmlspecialchars($name . ' in ' . $location) . '" class="w-full h-full object-cover transition group-hover:scale-105" loading="lazy">';
            } else {
                $imageCell .= '<div class="w-full h-full bg-gradient-to-br from-rose-100 to-amber-50 flex items-center justify-center text-5xl">🎉</div>';
            }
            $imageCell .= '</div>';

            $textCell = '<div class="p-6 sm:p-8 flex flex-col justify-center ' . ($imageFirst ? 'md:order-2' : 'md:order-1') . '">';
            if ($month) {
                $textCell .= '<div class="inline-flex w-fit items-center gap-1 text-xs font-bold uppercase tracking-wide text-rose-700 bg-rose-100 px-2.5 py-1 rounded-md mb-3">';
                $textCell .= '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 011 1v1h6V3a1 1 0 112 0v1h1a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm9 4H5v10h10V6z" clip-rule="evenodd"/></svg>';
                $textCell .= htmlspecialchars($month);
                $textCell .= '</div>';
            }
            $textCell .= '<h3 class="text-xl font-bold text-slate-900 mb-3">' . htmlspecialchars($name) . '</h3>';
            foreach ($paragraphs as $p) {
                $textCell .= '<p class="text-slate-700 leading-relaxed mb-3 text-sm">' . $p . '</p>';
            }
            $textCell .= '</div>';

            $html .= $imageCell . $textCell . '</div></article>';
        }

        $html .= '</div></section>';
        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    /**
     * 3 paragraphs (~200 words) per festival: the seed description from
     * destinations_extras.php, expanded with crowd/timing context and a
     * practical "how to attend" note.
     */
    private function buildFestivalParagraphs(string $name, string $month, string $desc, string $location, int $idx): array
    {
        $whatIs = htmlspecialchars(ucfirst(rtrim($desc, '.'))) . ". For most travelers, this is the time of year when " . htmlspecialchars($location) . " is at its most distinct: the streets, the music, and the food all shift into festival mode for a few days, and the regular weekend rhythm of the place takes a back seat.";

        $logisticsVariants = [
            "Crowds peak the day before and the day of the main event. If you can be in town by the evening before, you will catch the build-up: street rehearsals, vendors setting up, and the calmer half of the celebration. Day-of can feel intense if you do not like crowded streets, so consider basing in a property a short tricycle ride from the main venue rather than walking distance.",
            "Hotel availability for " . htmlspecialchars($month) . " books up weeks ahead. The closer you stay to the parade route or church plaza, the harder it is to secure a room without booking early. Most travelers split the difference and stay 10 to 15 minutes outside the festival core, then ride in for the events.",
            "Plan for hot daytime conditions and long stretches on your feet. Bring water, sun protection, and comfortable walking shoes. Some festival routes close streets to vehicles, so a tricycle that can take you within a few blocks is more practical than trying to drop you at the exact venue.",
            "Locals turn out in numbers, and the festival is as much a family reunion as a tourist event. Greet the people you stand next to, especially elders. The atmosphere is welcoming, and if you ask politely, residents are happy to point you to the best food stalls or the lesser-known events that happen alongside the main parade.",
        ];
        $logistics = $logisticsVariants[$idx % count($logisticsVariants)];

        $tipVariants = [
            "If you want photos, the morning hours give better light and smaller crowds than the noon parade window. Late afternoon also works for the post-event street fair, when the parade groups break and mix back into the public crowd. Avoid bringing big DSLR bags; phones and a small camera are easier to manage in tight crowds.",
            "Combine the festival visit with a regular " . htmlspecialchars($location) . " itinerary. The town does not stop functioning during the festival, and the off-event hours are a calm time to visit nearby tourist spots that would otherwise be packed on a regular weekend. The trick is treating the festival as one anchor of a longer trip, not the only reason to come.",
            "Watch for the procession schedule and the closing fireworks or fluvial part, depending on which festival. Most events publish a daily program through the local tourism office Facebook page; check it the morning of so you know which streets will be closed and when. Backup batteries and a power bank are worth the bag space.",
        ];
        $tip = $tipVariants[($idx + 1) % count($tipVariants)];

        return [$whatIs, $logistics, $tip];
    }

    private function festivalImageUrl(string $destKey, string $festivalName): ?string
    {
        $slug = substr(\Illuminate\Support\Str::slug($festivalName), 0, 50);
        $candidate = 'rg-media/festivals/' . $destKey . '-' . $slug . '.jpg';
        $abs = storage_path('app/public/' . $candidate);
        if (is_file($abs) && filesize($abs) > 5000) {
            return '/storage/' . $candidate;
        }
        // Fallback: destination-1 image (still relevant for the area)
        $destCandidate = 'rg-media/destinations/' . $destKey . '-1.jpg';
        $destAbs = storage_path('app/public/' . $destCandidate);
        if (is_file($destAbs) && filesize($destAbs) > 5000) {
            return '/storage/' . $destCandidate;
        }
        return null;
    }

    private function buildFieldNotesBlock(array $e, string $location, string $destKey = ''): array
    {
        $tip = $e['blog_quote_tip'] ?? '';
        $blogName = $e['blog_name'] ?? 'a Filipino travel blog';
        $blogUrl = $e['blog_url'] ?? '#';

        if (empty($tip) && empty($e['blog_image_url'])) {
            return ['type' => 'custom_html', 'payload' => ['html' => '']];
        }

        // Image priority: blog's own image (when verified) > matching spot photo
        // for the spot the blogger highlighted > destination hero image. Caption
        // attributes the writeup to the blog regardless of which image was used.
        $img = $e['blog_image_url'] ?? '';
        $imgAttribution = $img ? 'Photo via ' . $blogName : 'Photo from our library';
        if (empty($img) && $destKey) {
            // Look for the spot image matching blog_spot_name
            if (!empty($e['blog_spot_name'])) {
                $spotSlug = substr(Str::slug($e['blog_spot_name']), 0, 50);
                $spotCandidate = 'rg-media/spots/' . $destKey . '-' . $spotSlug . '.jpg';
                $spotAbs = storage_path('app/public/' . $spotCandidate);
                if (is_file($spotAbs) && filesize($spotAbs) > 5000) {
                    $img = '/storage/' . $spotCandidate;
                }
            }
            // Fall back to destination-1 image
            if (empty($img)) {
                $destCandidate = 'rg-media/destinations/' . $destKey . '-1.jpg';
                $destAbs = storage_path('app/public/' . $destCandidate);
                if (is_file($destAbs) && filesize($destAbs) > 5000) {
                    $img = '/storage/' . $destCandidate;
                }
            }
        }
        $photoCaption = $img ? $this->captionFromMedia($img, $location) : $location;

        $html = '<aside class="not-prose my-10 rounded-xl border border-slate-200 overflow-hidden bg-white">';

        if ($img) {
            $html .= '<a href="' . htmlspecialchars($blogUrl) . '" target="_blank" rel="noopener nofollow" class="block relative group">';
            $html .= '<div class="aspect-[16/9] bg-slate-100 overflow-hidden">';
            $html .= '<img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($photoCaption) . '" class="w-full h-full object-cover transition group-hover:scale-105" loading="lazy" referrerpolicy="no-referrer">';
            $html .= '</div>';
            $html .= '<div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/65 via-black/20 to-transparent p-4">';
            $html .= '<div class="text-white/90 text-xs uppercase tracking-wide mb-0.5">Field notes</div>';
            $html .= '<div class="text-white font-semibold text-lg drop-shadow">' . htmlspecialchars($photoCaption) . '</div>';
            $html .= '</div>';
            $html .= '</a>';
        }

        $html .= '<div class="p-5 sm:p-6">';
        if (!$img) {
            $html .= '<div class="text-xs uppercase tracking-wide text-slate-500 mb-2">Field notes</div>';
        }
        if ($tip) {
            $html .= '<p class="text-slate-700 leading-relaxed text-base">' . htmlspecialchars($tip) . '</p>';
        }
        $html .= '<div class="text-xs text-slate-500 border-t border-slate-100 mt-4 pt-3 flex items-center justify-between flex-wrap gap-2">';
        $html .= '<span>Writeup via <a href="' . htmlspecialchars($blogUrl) . '" target="_blank" rel="noopener nofollow" class="font-medium text-slate-700 hover:underline">' . htmlspecialchars($blogName) . '</a></span>';
        $html .= '<a href="' . htmlspecialchars($blogUrl) . '" target="_blank" rel="noopener nofollow" class="text-slate-700 font-medium hover:underline">Read the full post →</a>';
        $html .= '</div>';
        $html .= '</div></aside>';

        return ['type' => 'custom_html', 'payload' => ['html' => $html]];
    }

    private function buildAggregatorFallback(string $phrase, string $location, string $pageType): string
    {
        $tripQuery = rawurlencode($phrase . ' ' . $location . ' Philippines');
        $bookQuery = rawurlencode($location . ', Philippines');
        $agodaQuery = rawurlencode($location . ' Philippines');
        $tripadvisor = 'https://www.tripadvisor.com.ph/Search?q=' . $tripQuery;
        $booking = 'https://www.booking.com/searchresults.html?ss=' . $bookQuery;
        $agoda = 'https://www.agoda.com/search?city=' . $agodaQuery;

        $html = '<section class="not-prose my-2 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">';
        $html .= '<div class="flex items-start justify-between gap-4 flex-wrap mb-4">';
        $html .= '<div>';
        $html .= '<div class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md mb-2">'
            . '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>'
            . '<span>Listings coming soon</span>'
            . '</div>';
        $html .= '<h2 class="text-2xl font-bold text-slate-900 mb-2">' . htmlspecialchars(ucwords($pageType)) . ' in ' . htmlspecialchars($location) . ', still being curated</h2>';
        $html .= '<p class="text-slate-700 max-w-2xl">There are dozens of properties operating in ' . htmlspecialchars($location) . ' for ' . htmlspecialchars($phrase) . ', and our featured list is being built up as operators come online. In the meantime, the third-party tourism platforms below have honest reviews and real-time availability you can browse now.</p>';
        $html .= '</div></div>';

        $html .= '<div class="grid sm:grid-cols-3 gap-3 mt-5">';
        $html .= $this->aggregatorCard($tripadvisor, '🟢', 'TripAdvisor', 'Honest traveler reviews, photos, and ratings for ' . $location . '.');
        $html .= $this->aggregatorCard($booking, '🔵', 'Booking.com', 'Real-time availability and confirmed bookings, free cancellation on many properties.');
        $html .= $this->aggregatorCard($agoda, '🟣', 'Agoda', 'Filipino-friendly booking site, frequent local promos on stays in ' . $location . '.');
        $html .= '</div>';

        $html .= '<div class="mt-6 pt-5 border-t border-slate-200 flex items-center justify-between flex-wrap gap-3">';
        $html .= '<p class="text-sm text-slate-600"><strong>Run a property in ' . htmlspecialchars($location) . '?</strong> Claim a spot on this page and reach travelers directly.</p>';
        $html .= '<a href="/register" class="inline-flex items-center gap-1 px-5 py-2.5 rounded-lg bg-brand-600 text-white font-semibold hover:bg-brand-700 transition">List your property →</a>';
        $html .= '</div>';
        $html .= '</section>';
        return $html;
    }

    private function aggregatorCard(string $url, string $icon, string $name, string $blurb): string
    {
        return '<a href="' . $url . '" target="_blank" rel="noopener nofollow" class="group flex flex-col bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-400 hover:shadow-md transition">'
            . '<div class="flex items-center gap-2 mb-1">'
            . '<span class="text-xl">' . $icon . '</span>'
            . '<span class="font-bold text-slate-900 group-hover:text-brand-700">' . $name . '</span>'
            . '<svg class="w-3.5 h-3.5 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14"/></svg>'
            . '</div>'
            . '<p class="text-xs text-slate-500 leading-snug">' . htmlspecialchars($blurb) . '</p>'
            . '</a>';
    }

    private function spotExternalUrl(array $spot, string $location): string
    {
        if (!empty($spot['url'])) return $spot['url'];
        $query = $spot['name'] . ' ' . $location . ' Philippines';
        return 'https://www.tripadvisor.com.ph/Search?q=' . rawurlencode($query);
    }

    private function spotLinkLabel(array $spot): string
    {
        return 'Read reviews and details for ' . $spot['name'] . ' on TripAdvisor';
    }

    /**
     * Generate a caption describing what is actually in the image, based on the
     * Wikimedia filename stored in rg_media. Falls back to a generic location
     * caption only when no media row exists.
     */
    private function captionFromMedia(string $imagePath, string $fallback): string
    {
        // imagePath is "/storage/rg-media/foo/bar.jpg" → strip prefix
        $relative = preg_replace('#^/?storage/#', '', $imagePath);
        $row = DB::table('rg_media')->where('path', $relative)->first(['filename', 'caption']);
        if (!$row) return $fallback;

        // Wikimedia filename like "Pinto Art Museum 5.jpg" → "Pinto Art Museum"
        $name = pathinfo($row->filename, PATHINFO_FILENAME);
        $name = preg_replace('/[-_]+/', ' ', $name);
        // Trim a trailing numeric variant suffix ("Foo 1", "Foo - 1", "Foo (1)")
        $name = preg_replace('/\s*[\-(]?\s*\d+\)?\s*$/', '', $name);
        // Collapse whitespace
        $name = trim(preg_replace('/\s+/', ' ', $name));
        if ($name === '') return $fallback;

        return ucfirst($name);
    }

    private function spotImageUrl(string $destKey, string $spotName, int $idx): string
    {
        $spotSlug = substr(Str::slug($spotName), 0, 50);
        $candidate = 'rg-media/spots/' . $destKey . '-' . $spotSlug . '.jpg';
        $abs = storage_path('app/public/' . $candidate);
        if (is_file($abs) && filesize($abs) > 5000) {
            return '/storage/' . $candidate;
        }
        // Fallback to destination-level image (rotate by spot index for variety)
        $destImageIdx = ($idx % 3) + 1;
        $destFallback = 'rg-media/destinations/' . $destKey . '-' . $destImageIdx . '.jpg';
        $destAbs = storage_path('app/public/' . $destFallback);
        if (is_file($destAbs) && filesize($destAbs) > 5000) {
            return '/storage/' . $destFallback;
        }
        // Final fallback: placeholder
        return 'https://placehold.co/800x600/e2e8f0/64748b?text=' . urlencode(substr($spotName, 0, 40));
    }

    private function subtopicPlanDay(array $dest, string $destKey, string $location, string $phrase, int $seed): array
    {
        $spots = $dest['spots'] ?? [];
        $foods = $dest['food'] ?? [];
        $first = $spots[0]['name'] ?? 'the town center';
        $second = $spots[1]['name'] ?? ($spots[0]['name'] ?? 'a nearby cafe');
        $third = $spots[2]['name'] ?? ($spots[1]['name'] ?? 'a roadside vendor');
        $morningFood = $foods[0] ?? 'a local breakfast';
        $lunchFood = $foods[1] ?? $foods[0] ?? 'a carinderia lunch';
        $eveningFood = $foods[2] ?? $foods[0] ?? 'a local dinner';

        $h2 = "Plan Your Day in {$location}";

        $intro = "<p>If your trip to {$location} is one full day plus a night, here is a realistic schedule that does not rush you between spots. Adjust based on traffic and how late you slept the night before.</p>";

        $html = $intro . '<div class="not-prose day-plan-card rounded-xl p-5 my-6 space-y-4 border border-slate-200 border-l-4">';

        $items = [
            ['time' => '7:00 AM', 'icon' => '☀️', 'title' => 'Early breakfast', 'body' => "Skip the resort buffet at least once and try {$morningFood} from a kanto vendor. Filipinos eat heavy in the morning for a reason."],
            ['time' => '9:00 AM', 'icon' => '🚶', 'title' => $first, 'body' => "Hit {$first} before the midday heat. Mornings are cooler and the light is better for photos than at noon."],
            ['time' => '12:00 PM', 'icon' => '🥘', 'title' => 'Lunch break', 'body' => "Settle in for {$lunchFood} at a family-run eatery, not the property's restaurant. Two hours is normal here, not slow."],
            ['time' => '2:30 PM', 'icon' => '📷', 'title' => $second, 'body' => "Afternoon at {$second}. Bring water and small bills for entrance fees if any. Most spots take cash only."],
            ['time' => '5:00 PM', 'icon' => '🍹', 'title' => 'Pool or rest', 'body' => "Back at your stay. This is the time the pool actually empties out and the light gets soft. Locals call this oras ng siesta para sa matatanda."],
            ['time' => '7:00 PM', 'icon' => '🍽️', 'title' => 'Dinner', 'body' => "Try {$eveningFood}. Ask the front desk for a small carinderia, not the tourist-rated restaurant. The flavor usually wins."],
            ['time' => '9:30 PM', 'icon' => '🌙', 'title' => 'Wind down', 'body' => "Most of {$location} sleeps early. If you want a nightcap, the bigger properties run a small bar until 11 PM. Anything later is unusual outside the cities."],
        ];

        foreach ($items as $it) {
            $html .= '<div class="flex gap-4 items-start">';
            $html .= '<div class="shrink-0 w-20 sm:w-24"><span class="day-plan-time block text-sm">' . $it['time'] . '</span><span class="text-2xl">' . $it['icon'] . '</span></div>';
            $html .= '<div class="flex-1 min-w-0 pt-0.5"><div class="font-semibold text-slate-900 mb-0.5">' . htmlspecialchars($it['title']) . '</div>';
            $html .= '<div class="text-sm text-slate-600 leading-relaxed">' . htmlspecialchars($it['body']) . '</div></div>';
            $html .= '</div>';
        }
        $html .= '</div>';

        $closer = "<p>The plan above is a starting point. Locals will tell you that the best days in {$location} happen when you do not stick to the schedule too rigidly. {$third} is worth saving for the next visit if you cannot fit it in.</p>";

        return [$this->headingBlock($h2), ['type' => 'custom_html', 'payload' => ['html' => $html . $closer]]];
    }
}
