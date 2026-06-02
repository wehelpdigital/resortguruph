<?php

namespace App\Services;

/**
 * Post-processor that decorates blog content_html with:
 *  - Inline SVG icons before H2 headings (chosen by heading semantics).
 *  - Thin grey horizontal dividers between H2 sections (skipped before the
 *    very first H2 so the lede doesn't get a stray rule above it).
 *  - Google Maps deep-links on the first mention of known Philippine
 *    locations (lookup table at database/data/ph_locations.php).
 *
 * Idempotent: if the content already carries the marker class our rewrite
 * adds (rg-h2-iconized / rg-divider / rg-loc-linked), we leave it alone. The
 * seeder runs this on every reseed, so re-running must not double-decorate.
 */
class BlogContentEnhancer
{
    /** @var array<string,string>|null */
    private static ?array $locations = null;

    public function enhance(string $html): string
    {
        if ($html === '') return $html;
        $html = $this->stripAiTells($html);
        $html = $this->normalizeWikimediaUrls($html);
        $html = $this->addH2IconsAndDividers($html);
        $html = $this->linkLocations($html);
        $html = $this->wrapSectionsInTintedCallouts($html);
        return $html;
    }

    /**
     * Convert fabricated Wikimedia thumb URLs (with guessed hash paths) to
     * the Special:FilePath format which Wikimedia auto-redirects to the real
     * thumbnail. This fixes the "agents made up hash paths" bug that was
     * leaving ~30% of blog images broken in production.
     *
     * Before: https://upload.wikimedia.org/wikipedia/commons/thumb/0/0e/FILE.jpg/800px-FILE.jpg
     * After:  https://commons.wikimedia.org/wiki/Special:FilePath/FILE.jpg?width=800
     *
     * If the filename actually exists on Commons, the new URL serves the
     * thumbnail. If it doesn't, both URLs 404 — but at least we're not
     * relying on fabricated hash paths.
     */
    private function normalizeWikimediaUrls(string $html): string
    {
        $pattern = '#https?://upload\.wikimedia\.org/wikipedia/commons/thumb/[0-9a-f]/[0-9a-f]{2}/([^/]+)/\d+px-\1#i';
        return preg_replace_callback($pattern, function ($m) {
            return 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $m[1] . '?width=800';
        }, $html);
    }

    /**
     * Replace em-dashes and other AI-feeling tells with cleaner phrasing.
     * The em-dash is the most common LLM giveaway, per user feedback. We also
     * swap a handful of stock-AI words ("nestled", "bustling", "delve", etc.)
     * for natural alternatives. Runs on every blog post's content_html.
     */
    private function stripAiTells(string $html): string
    {
        // Defensive: strip literal backslash-apostrophe sequences from data
        // files that used double-quoted PHP strings (where `\'` does not
        // escape — it stays as two literal characters).
        $html = str_replace("\\'", "'", $html);
        // U+2014 EM DASH and U+2013 EN DASH → comma+space, with cleanup
        $html = str_replace(["\xE2\x80\x94", "\xE2\x80\x93"], [', ', ', '], $html);
        $html = preg_replace('/\s*,\s+,\s*/', ', ', $html);
        $html = preg_replace('/\s+,/', ',', $html);
        // Common AI phrasing replacements
        $swaps = [
            '/\bnestled\b/i'        => 'sitting',
            '/\bbustling\b/i'       => 'busy',
            '/\bvibrant\b/i'        => 'lively',
            '/\bdelve\b/i'          => 'look',
            '/\bdelving\b/i'        => 'looking',
            '/\bunveil\b/i'         => 'show',
            '/\bembark\b/i'         => 'start',
            '/\bhidden gem\b/i'     => 'overlooked stop',
            '/\brich tapestry\b/i'  => 'mix',
            '/\bin the heart of\b/i' => 'in',
            '/\bbreathtaking\b/i'   => 'striking',
            '/\bawe-inspiring\b/i'  => 'striking',
        ];
        return preg_replace(array_keys($swaps), array_values($swaps), $html);
    }

    /**
     * Wrap every Nth H2 section in a soft-tinted callout container so the post
     * doesn't read as a single flat white column end-to-end. We pick sections
     * by H2 index (skipping the first one, which already has the lede) and
     * cycle through 4 tint variants. The callout uses the same `.rg-tinted-*`
     * classes defined in public.blade.php so future restyles are centralized.
     *
     * Idempotent — uses an `rg-tinted-section` marker class to skip already
     * processed sections on reseed.
     */
    private function wrapSectionsInTintedCallouts(string $html): string
    {
        if (strpos($html, 'rg-tinted-section') !== false) return $html;

        // Match a full H2 section: from the H2 tag through the content up to
        // the next H2 or end of content. We only target every 3rd H2 (the 3rd,
        // 6th, 9th, etc.) so the page gets accent moments without becoming
        // patchwork.
        $sectionIndex = 0;
        return preg_replace_callback(
            '#(<h2\b[^>]*>.*?</h2>)(.*?)(?=<h2\b|$)#is',
            function ($m) use (&$sectionIndex) {
                $sectionIndex++;
                if ($sectionIndex < 3 || ($sectionIndex - 3) % 3 !== 0) return $m[0];

                $tintVariants = ['rg-tinted-1', 'rg-tinted-3'];
                $tint = $tintVariants[($sectionIndex / 3) % count($tintVariants)];

                return '<aside class="rg-tinted-section ' . $tint . ' rounded-xl border my-8 px-6 py-1 not-prose-margin">'
                    . '<div class="prose prose-slate max-w-none">' . $m[1] . $m[2] . '</div>'
                    . '</aside>';
            },
            $html
        );
    }

    /**
     * In one pass, rewrites <h2>...</h2> blocks to:
     *   - prepend a thin grey <hr> divider (except before the first H2),
     *   - inject an inline SVG icon at the start of the heading.
     * Skips headings already carrying the rg-h2-iconized class.
     */
    private function addH2IconsAndDividers(string $html): string
    {
        $first = true;
        return preg_replace_callback(
            '#<h2(?P<attrs>[^>]*)>(?P<inner>.*?)</h2>#is',
            function ($m) use (&$first) {
                $attrs = $m['attrs'];
                $inner = $m['inner'];

                // Idempotency guard
                if (strpos($attrs, 'rg-h2-iconized') !== false) {
                    $first = false;
                    return $m[0];
                }

                $plain = trim(strip_tags($inner));
                $iconKey = IconLibrary::pickFromHeading($plain);
                $iconSpan = IconLibrary::get($iconKey);

                // Merge class attribute so the iconized marker rides along.
                if (preg_match('#class=("|\')(?P<c>[^"\']*)("|\')#', $attrs, $cm)) {
                    $newClass = trim($cm['c'] . ' rg-h2-iconized');
                    $newAttrs = preg_replace('#class=("|\')[^"\']*("|\')#', 'class="' . $newClass . '"', $attrs, 1);
                } else {
                    $newAttrs = $attrs . ' class="rg-h2-iconized"';
                }

                $h2 = '<h2' . $newAttrs . '>' . $iconSpan . $inner . '</h2>';
                $divider = $first ? '' : '<hr class="rg-divider" />';
                $first = false;
                return $divider . $h2;
            },
            $html
        );
    }

    /**
     * Wraps the first occurrence (per post) of each known Philippine location
     * in a Google Maps deep link. Longest keys matched first to prevent
     * "Hundred Islands" hijacking "Hundred Islands National Park".
     * Skips matches already inside an <a> tag and skips any heading tags
     * (visual noise inside H1/H2/H3).
     */
    private function linkLocations(string $html): string
    {
        $locations = self::locations();

        // Idempotency: skip if already processed once.
        if (strpos($html, 'rg-loc-linked') !== false) return $html;

        // Sort keys by length desc so longer phrases match first.
        $keys = array_keys($locations);
        usort($keys, fn ($a, $b) => mb_strlen($b) - mb_strlen($a));

        // Mask out content we shouldn't touch: existing <a>...</a>, all heading tags,
        // image alt/figcaption text, and HTML attribute values. We do a placeholder
        // swap, run replacements on the surviving text, then restore.
        $masks = [];
        $maskIndex = 0;
        $maskedHtml = preg_replace_callback(
            '#<a\b[^>]*>.*?</a>|<h[1-6]\b[^>]*>.*?</h[1-6]>|<figure\b[^>]*>.*?</figure>|<figcaption\b[^>]*>.*?</figcaption>#is',
            function ($m) use (&$masks, &$maskIndex) {
                $token = "\x01RGMASK{$maskIndex}\x02";
                $masks[$token] = $m[0];
                $maskIndex++;
                return $token;
            },
            $html
        );

        foreach ($keys as $name) {
            $query = $locations[$name];
            $url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
            $escapedName = preg_quote($name, '#');
            // Word-boundary on either side; only first match in document.
            $pattern = '#(?<![\w>])(' . $escapedName . ')(?![\w<])#i';
            $maskedHtml = preg_replace_callback(
                $pattern,
                function ($m) use ($url) {
                    return '<a class="rg-loc-linked underline decoration-dotted decoration-slate-400 hover:text-brand-600" href="' . $url . '" target="_blank" rel="nofollow noopener" title="Open in Google Maps">' . $m[1] . '</a>';
                },
                $maskedHtml,
                1
            );
        }

        // Restore masked regions.
        if (!empty($masks)) {
            $maskedHtml = strtr($maskedHtml, $masks);
        }

        return $maskedHtml;
    }

    /** @return array<string,string> */
    private static function locations(): array
    {
        if (self::$locations === null) {
            $path = database_path('data/ph_locations.php');
            self::$locations = is_file($path) ? require $path : [];
        }
        return self::$locations;
    }
}
