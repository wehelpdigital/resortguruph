<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Re-renders the seeded custom_html patterns used across guide pages
 * (tourist spot lists, festival cards, field notes, related reads, map
 * cards, food cards, history panels) in the current page design. Every
 * text, link and image from the original markup is kept; only the
 * markup around it changes. Anything not recognised is returned as is.
 * Styles: layouts/partials/page-kit.blade.php (.gh-*).
 */
class GuideHtml
{
    private static DOMDocument $doc;
    private static DOMXPath $xp;

    public static function transform(string $html): string
    {
        $trim = ltrim($html);
        if ($trim === '' || str_starts_with($trim, '<style') || !preg_match('~Tourist spots|Notable festivals|Field notes|Related guides|<iframe|food-card|border-amber-200~', $html)) {
            return $html;
        }
        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $ok = $doc->loadHTML('<?xml encoding="utf-8"?><div id="gh-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $root = $ok ? $doc->getElementById('gh-root') : null;
        if (!$root) return $html;
        self::$doc = $doc;
        self::$xp = new DOMXPath($doc);

        $out = '';
        $changed = false;
        foreach (iterator_to_array($root->childNodes) as $node) {
            $rep = null;
            if ($node instanceof DOMElement) {
                try { $rep = self::rebuild($node); } catch (\Throwable $e) { $rep = null; }
            }
            if ($rep !== null) { $out .= $rep; $changed = true; }
            else { $out .= $doc->saveHTML($node); }
        }
        return $changed ? $out : $html;
    }

    // ------------------------------------------------------------------ dispatch

    private static function rebuild(DOMElement $el): ?string
    {
        $tag = strtolower($el->tagName);
        $h2 = self::text(self::one('.//h2', $el));
        $hasArticles = self::one('.//article', $el) !== null;
        if ($tag === 'section' && $hasArticles && stripos($h2, 'Tourist spots') === 0) return self::spots($el, $h2);
        if ($tag === 'section' && $hasArticles && stripos($h2, 'Notable festivals') === 0) return self::festivals($el, $h2);
        if ($tag === 'aside' && stripos($el->textContent, 'Field notes') !== false && self::one('.//img', $el)) return self::note($el);
        if ($tag === 'section' && stripos($h2, 'Related guides') === 0) return self::reads($el, $h2);
        if ($tag === 'section' && self::one('.//iframe', $el)) return self::map($el, $h2);
        if ($tag === 'div' && self::one('.//*[contains(concat(" ", normalize-space(@class), " "), " food-card ")]', $el)) return self::foods($el);
        if ($tag === 'section' && str_contains((string) $el->getAttribute('class'), 'border-amber-200') && $h2 !== '') return self::history($el, $h2);
        return null;
    }

    // ------------------------------------------------------------------ patterns

    private static function spots(DOMElement $el, string $h2): string
    {
        $intro = self::first('.//div[1]/p', $el);
        $out = '<section class="gh-spots not-prose">' . self::head('Things to see', $h2, $intro);
        $out .= '<div class="gh-spots__grid">';
        $n = 0;
        foreach (self::all('.//article', $el) as $art) {
            $n++;
            $img = self::one('.//img', $art);
            $imgLink = $img && $img->parentNode instanceof DOMElement && strtolower($img->parentNode->tagName) === 'a' ? $img->parentNode->getAttribute('href') : '';
            $h3 = self::one('.//h3', $art);
            $body = $h3 ? $h3->parentNode : $art;
            $name = self::text($h3);
            $score = ''; $count = '';
            foreach (self::all('.//span', $body) as $sp) {
                $t = trim($sp->textContent);
                if ($score === '' && preg_match('/^\d(\.\d)?$/', $t)) $score = $t;
                if ($count === '' && preg_match('/review/i', $t) && strlen($t) < 40) $count = trim($t, '() ');
            }
            $paras = [];
            foreach ($body->childNodes as $c) if ($c instanceof DOMElement && strtolower($c->tagName) === 'p') $paras[] = self::inner($c);
            $review = self::one('.//*[contains(@class, "rg-spot-review-slide")]', $art);
            $links = [];
            foreach ($body->childNodes as $c) if ($c instanceof DOMElement && strtolower($c->tagName) === 'a') $links[] = [$c->getAttribute('href'), trim($c->textContent)];

            $out .= '<article class="gh-spot' . ($n === 1 ? ' gh-spot--lead' : '') . '">';
            $out .= self::photo($img, $imgLink, 'gh-spot__img', '<span class="gh-num">' . str_pad((string) $n, 2, '0', STR_PAD_LEFT) . '</span>');
            $out .= '<div class="gh-spot__body"><h3 class="gh-spot__name">' . self::e($name) . '</h3>';
            if ($score !== '') {
                $out .= '<p class="gh-rating">' . self::stars((float) $score) . '<strong>' . self::e($score) . '</strong>' . ($count !== '' ? '<span>' . self::e($count) . '</span>' : '') . '</p>';
            }
            if ($paras) {
                $out .= '<p class="gh-p gh-p--clamp">' . $paras[0] . '</p>';
                if (count($paras) > 1) {
                    $out .= '<details class="gh-more"><summary><span>Read more</span></summary>';
                    foreach (array_slice($paras, 1) as $p) $out .= '<p class="gh-p">' . $p . '</p>';
                    $out .= '</details>';
                }
            }
            if ($review) $out .= self::quote($review);
            if ($links) {
                $out .= '<p class="gh-links">';
                foreach ($links as [$href, $label]) $out .= '<a href="' . self::e($href) . '" target="_blank" rel="noopener nofollow">' . self::e($label) . self::arrow() . '</a>';
                $out .= '</p>';
            }
            $out .= '</div></article>';
        }
        return $out . '</div></section>';
    }

    private static function festivals(DOMElement $el, string $h2): string
    {
        $intro = self::first('.//div[1]/p', $el);
        $out = '<section class="gh-fests not-prose">' . self::head('Festivals', $h2, $intro) . '<div class="gh-fests__list">';
        foreach (self::all('.//article', $el) as $art) {
            $img = self::one('.//img', $art);
            $h3 = self::one('.//h3', $art);
            $body = $h3 ? $h3->parentNode : $art;
            $when = '';
            foreach ($body->childNodes as $c) {
                if ($c instanceof DOMElement && strtolower($c->tagName) === 'div' && self::one('.//svg', $c)) { $when = trim($c->textContent); break; }
            }
            $paras = [];
            foreach ($body->childNodes as $c) if ($c instanceof DOMElement && strtolower($c->tagName) === 'p') $paras[] = self::inner($c);
            $out .= '<article class="gh-fest">' . self::photo($img, '', 'gh-fest__img', $when !== '' ? '<span class="gh-date">' . self::cal() . self::e($when) . '</span>' : '');
            $out .= '<div class="gh-fest__body"><h3 class="gh-fest__name">' . self::e(self::text($h3)) . '</h3>';
            foreach ($paras as $i => $p) {
                if ($i === 1 && count($paras) > 2) { $out .= '<details class="gh-more"><summary><span>Read more</span></summary>'; }
                $out .= '<p class="gh-p">' . $p . '</p>';
            }
            if (count($paras) > 2) $out .= '</details>';
            $out .= '</div></article>';
        }
        return $out . '</div></section>';
    }

    private static function note(DOMElement $el): string
    {
        $img = self::one('.//img', $el);
        $a = self::one('.//a', $el);
        $href = $a ? $a->getAttribute('href') : '';
        $title = '';
        foreach (self::all('.//div', $el) as $d) {
            $t = trim($d->textContent);
            if ($t !== '' && stripos($t, 'Field notes') === false && !self::one('.//div', $d) && self::one('ancestor::a', $d)) { $title = $t; break; }
        }
        $paras = [];
        foreach (self::all('.//p', $el) as $p) $paras[] = self::inner($p);
        $foot = self::one('.//*[contains(@class, "border-t")]', $el);
        $via = '';
        if ($foot) { $src = self::one('.//span', $foot); $via = $src ? self::inner($src) : ''; }
        // the overlay title and alt were the photo's original file name, which no
        // longer matches after the photo fixes: keep the note, drop the file name
        if ($img) $img->setAttribute('alt', 'Field notes photo');
        $out = '<aside class="gh-note not-prose">' . self::photo($img, $href, 'gh-note__img', '');
        $out .= '<div class="gh-note__body"><p class="gh-eyebrow">Field notes</p>';
        foreach ($paras as $p) $out .= '<p class="gh-p">' . $p . '</p>';
        $out .= '<p class="gh-note__foot">' . ($via !== '' ? '<span>' . $via . '</span>' : '') . ($href !== '' ? '<a href="' . self::e($href) . '" target="_blank" rel="noopener nofollow">Read the full post' . self::arrow() . '</a>' : '') . '</p>';
        return $out . '</div></aside>';
    }

    private static function reads(DOMElement $el, string $h2): string
    {
        $intro = self::first('.//p', $el);
        $out = '<section class="gh-reads not-prose">' . self::head('From the blog', $h2, $intro) . '<div class="gh-reads__grid">';
        foreach (self::all('.//a[.//h3]', $el) as $a) {
            $img = self::one('.//img', $a);
            $excerpt = self::one('.//p', $a);
            $out .= '<a class="gh-read" href="' . self::e($a->getAttribute('href')) . '">';
            $out .= '<span class="gh-read__img">' . ($img ? '<img src="' . self::e($img->getAttribute('src')) . '" alt="' . self::e($img->getAttribute('alt')) . '" loading="lazy">' : '') . '</span>';
            $out .= '<span class="gh-read__body"><span class="gh-read__kicker">Travel story</span><span class="gh-read__title">' . self::e(self::text(self::one('.//h3', $a))) . '</span>';
            if ($excerpt) $out .= '<span class="gh-read__text">' . self::e(self::text($excerpt)) . '</span>';
            $out .= '<span class="gh-read__go">Read the story' . self::arrow() . '</span></span></a>';
        }
        return $out . '</div></section>';
    }

    private static function map(DOMElement $el, string $h2): string
    {
        $iframe = self::one('.//iframe', $el);
        $sub = self::first('.//p', $el);
        $open = self::one('.//a[contains(@href, "google.com/maps")]', $el);
        $out = '<section class="gh-map not-prose"><div class="gh-map__head"><div><p class="gh-eyebrow">On the map</p>';
        if ($h2 !== '') $out .= '<h2 class="gh-map__title">' . self::e($h2) . '</h2>';
        if ($sub !== '') $out .= '<p class="gh-map__sub">' . $sub . '</p>';
        $out .= '</div>';
        if ($open) $out .= '<a class="rg-btn rg-btn--outline" href="' . self::e($open->getAttribute('href')) . '" target="_blank" rel="noopener">Open in Google Maps</a>';
        $out .= '</div><div class="gh-map__frame"><iframe src="' . self::e($iframe->getAttribute('src')) . '" title="' . self::e($h2 ?: 'Map') . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div></section>';
        return $out;
    }

    private static function foods(DOMElement $el): string
    {
        $out = '<div class="gh-foods not-prose">';
        foreach (self::all('.//*[contains(concat(" ", normalize-space(@class), " "), " food-card ")]', $el) as $card) {
            $a = self::one('.//a', $card);
            $img = self::one('.//img', $card);
            $cap = self::text(self::one('.//figcaption', $card));
            $h3 = self::one('.//h3', $card);
            $p = self::one('.//p', $card);
            $out .= '<article class="gh-food">' . self::photo($img, '', 'gh-food__img', '');
            $out .= '<div class="gh-food__body"><h3 class="gh-food__name">' . self::e(self::text($h3)) . '</h3>';
            if ($p) $out .= '<p class="gh-food__text">' . self::inner($p) . '</p>';
            if ($a) $out .= '<a class="gh-food__link" href="' . self::e($a->getAttribute('href')) . '" target="_blank" rel="noopener nofollow">' . self::e($a->getAttribute('title') ?: 'Learn more') . self::arrow() . '</a>';
            if ($cap !== '') $out .= '<p class="gh-food__cap">Photo: ' . self::e($cap) . '</p>';
            $out .= '</div></article>';
        }
        return $out . '</div>';
    }

    private static function history(DOMElement $el, string $h2): string
    {
        $eyebrow = '';
        foreach (self::all('.//div', $el) as $d) {
            $t = trim($d->textContent);
            if ($t !== '' && !self::one('.//div', $d) && !self::one('.//p', $d) && !self::one('.//h2', $d) && strlen($t) < 40) { $eyebrow = $t; break; }
        }
        $paras = [];
        foreach (self::all('.//p', $el) as $p) $paras[] = self::inner($p);
        $out = '<section class="gh-history not-prose"><div class="gh-history__rail" aria-hidden="true"></div><div class="gh-history__body">';
        $out .= '<p class="gh-eyebrow">' . self::e($eyebrow ?: 'History') . '</p><h2 class="gh-history__title">' . self::e($h2) . '</h2>';
        foreach ($paras as $i => $p) $out .= '<p class="gh-p' . ($i === 0 ? ' gh-p--lead' : '') . '">' . $p . '</p>';
        return $out . '</div></section>';
    }

    // ------------------------------------------------------------------ pieces

    private static function head(string $eyebrow, string $title, string $intro): string
    {
        return '<header class="gh-head"><p class="gh-eyebrow">' . self::e($eyebrow) . '</p><h2 class="gh-title">' . self::e($title) . '</h2>'
            . ($intro !== '' ? '<p class="gh-intro">' . $intro . '</p>' : '') . '</header>';
    }

    private static function photo(?DOMElement $img, string $href, string $cls, string $extra): string
    {
        $inner = $img ? '<img src="' . self::e($img->getAttribute('src')) . '" alt="' . self::e($img->getAttribute('alt')) . '" loading="lazy"' . ($img->getAttribute('referrerpolicy') ? ' referrerpolicy="' . self::e($img->getAttribute('referrerpolicy')) . '"' : '') . '>' : '';
        return $href !== ''
            ? '<a class="' . $cls . '" href="' . self::e($href) . '" target="_blank" rel="noopener nofollow">' . $inner . $extra . '</a>'
            : '<span class="' . $cls . '">' . $inner . $extra . '</span>';
    }

    private static function quote(DOMElement $slide): string
    {
        $name = self::text(self::one('.//span[contains(@class, "font-semibold")]', $slide));
        $meta = self::text(self::one('.//span[contains(@class, "text-slate-400")]', $slide));
        $text = self::text(self::one('.//p', $slide));
        if ($text === '') return '';
        return '<figure class="gh-quote"><blockquote>' . self::e(trim($text, " \u{201C}\u{201D}\"")) . '</blockquote><figcaption>' . self::e($name) . ($meta !== '' ? ' · ' . self::e($meta) : '') . '</figcaption></figure>';
    }

    private static function stars(float $r): string
    {
        $s = '<span class="gh-stars" aria-hidden="true">';
        for ($i = 1; $i <= 5; $i++) $s .= '<svg viewBox="0 0 24 24" class="' . ($r >= $i - 0.25 ? 'on' : '') . '"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>';
        return $s . '</span>';
    }

    private static function arrow(): string
    {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    }

    private static function cal(): string
    {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>';
    }

    // ------------------------------------------------------------------ DOM helpers

    private static function one(string $q, DOMNode $ctx): ?DOMElement
    {
        $r = self::$xp->query($q, $ctx);
        return $r && $r->length && $r->item(0) instanceof DOMElement ? $r->item(0) : null;
    }

    /** @return DOMElement[] */
    private static function all(string $q, DOMNode $ctx): array
    {
        $r = self::$xp->query($q, $ctx);
        $out = [];
        if ($r) foreach ($r as $n) if ($n instanceof DOMElement) $out[] = $n;
        return $out;
    }

    private static function first(string $q, DOMNode $ctx): string
    {
        $n = self::one($q, $ctx);
        return $n ? self::inner($n) : '';
    }

    private static function inner(?DOMNode $n): string
    {
        if (!$n) return '';
        $h = '';
        foreach ($n->childNodes as $c) {
            if ($c instanceof DOMElement && strtolower($c->tagName) === 'svg') continue;
            $h .= self::$doc->saveHTML($c);
        }
        return trim($h);
    }

    private static function text(?DOMNode $n): string
    {
        return $n ? trim(preg_replace('/\s+/u', ' ', $n->textContent)) : '';
    }

    private static function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
