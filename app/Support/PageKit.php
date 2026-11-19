<?php

namespace App\Support;

/**
 * Helpers for the shared sub-page design (resources/views/layouts/partials/page-kit.blade.php).
 */
class PageKit
{
    /**
     * True when a rendered block stream opens with a dark photo hero
     * (hub_hero / dest_hero_search with a background photo, or the
     * page-kit hero). Those pages slide the hero under the transparent
     * header. Live-edit output is wrapped per block, so it never matches.
     */
    public static function leadsWithDarkHero(?string $html): bool
    {
        if (!$html) return false;
        $head = substr(ltrim($html), 0, 1500);
        return (bool) preg_match('/^(?:<style\b[^>]*>.*?<\/style>\s*|<!--.*?-->\s*)*<(?:section|header)\b[^>]*\bclass="[^"]*\b(?:rg-hero-dark|pk-hero)\b/s', $head);
    }

    /**
     * Hero copy for a static page whose content is a single intro block
     * (home_editorial_intro): eyebrow, heading (accent stars removed),
     * first paragraph and background photo. Keeps the copy editable in
     * the admin builder while the page renders with the page-kit hero.
     */
    public static function intro(string $slug): array
    {
        $id = \Illuminate\Support\Facades\DB::table('rg_static_pages')->where('slug', $slug)->value('id');
        $raw = $id ? \Illuminate\Support\Facades\DB::table('rg_content_blocks')->where('owner_type', 'static_page')->where('owner_id', $id)
            ->where('block_type', 'home_editorial_intro')->orderBy('sort_order')->value('payload_json') : null;
        $p = $raw ? (json_decode($raw, true) ?: []) : [];
        $paras = $p['paragraphs'] ?? [];
        if (is_string($paras)) $paras = json_decode($paras, true) ?: [$paras];
        return [
            'eyebrow' => trim((string) ($p['eyebrow'] ?? '')),
            'heading' => trim(str_replace(['*', '|'], ['', ' '], (string) ($p['heading'] ?? ''))),
            'lede' => trim((string) (is_array($paras) ? ($paras[0] ?? '') : '')),
            'image' => trim((string) ($p['background_image'] ?? '')),
        ];
    }

    /**
     * Drop stray closing tags (and close tags left open) in admin-authored
     * HTML so it cannot break out of the page layout around it. Only
     * structural containers are tracked; script, style, textarea and
     * comment contents are skipped.
     */
    public static function balance(?string $html): string
    {
        $html = (string) $html;
        if ($html === '') return '';
        $tags = 'div|section|article|aside|details|nav|figure|header|footer|main|ul|ol|table';
        $re = '~<!--.*?-->|<(script|style|textarea)\b[^>]*>.*?</\1\s*>|<(/?)(' . $tags . ')\b[^>]*>~is';
        $stack = [];
        $out = preg_replace_callback($re, function ($m) use (&$stack) {
            if (empty($m[3])) return $m[0];
            $tag = strtolower($m[3]);
            if ($m[2] === '') { $stack[] = $tag; return $m[0]; }
            $pos = array_search($tag, array_reverse($stack, true), true);
            if ($pos === false) return '';
            // close anything opened inside it, then the tag itself
            $extra = '';
            while (($top = array_pop($stack)) !== null && $top !== $tag) $extra .= '</' . $top . '>';
            return $extra . $m[0];
        }, $html);
        if ($out === null) return $html;
        while (($top = array_pop($stack)) !== null) $out .= '</' . $top . '>';
        return $out;
    }

    /**
     * Two or more image blocks in a row become one photo grid instead of a
     * stack of full-width pictures (BlockRenderer::image marks them .rg-fig).
     */
    public static function figures(string $html): string
    {
        return preg_replace_callback(
            '~(?:<figure class="rg-fig[^"]*">.*?</figure>\s*){2,}~s',
            fn ($m) => '<div class="rg-figs not-prose">' . $m[0] . '</div>',
            $html
        ) ?? $html;
    }
}
