<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use DOMXPath;

/**
 * Re-lays out seeded blog post bodies (content_html) in the page design:
 * "Day N: ..." sections become an itinerary timeline, the tinted callouts
 * become cards, icon headings get an icon chip and the appended "Where to
 * stay near ..." grid becomes guide cards. Every heading, paragraph, list,
 * figure and link of the original is kept in the same order; only the
 * wrappers change. Posts without the seeded pattern are returned as is.
 * Styles: blog/show.blade.php (.bx-*).
 */
class BlogHtml
{
    private static DOMDocument $doc;

    public static function transform(?string $html): string
    {
        $html = (string) $html;
        if (!str_contains($html, 'rg-h2-iconized')) return $html;

        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $ok = $doc->loadHTML('<?xml encoding="utf-8"?><div id="bx-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $root = $ok ? $doc->getElementById('bx-root') : null;
        if (!$root) return $html;
        self::$doc = $doc;

        // photos found to show something else (or missing on disk) are left out wherever they sit
        foreach (iterator_to_array($doc->getElementsByTagName('img')) as $img) {
            if (GuideHtml::usable((string) $img->getAttribute('src'))) continue;
            $fig = $img->parentNode;
            while ($fig instanceof DOMElement && strtolower($fig->tagName) !== 'figure' && $fig !== $root) $fig = $fig->parentNode;
            $node = $fig instanceof DOMElement && strtolower($fig->tagName) === 'figure' ? $fig : $img;
            if ($node->parentNode) $node->parentNode->removeChild($node);
        }

        try {
            $items = [];
            self::walk($root, 0, $items);
            return self::render($items);
        } catch (\Throwable $e) {
            return $html;
        }
    }

    // ------------------------------------------------------------------ flatten

    /** Flattens wrappers into a list of headings and content nodes (document order). */
    private static function walk(DOMNode $parent, int $tint, array &$items): void
    {
        foreach (iterator_to_array($parent->childNodes) as $n) {
            if ($n instanceof DOMText) {
                $t = trim($n->textContent);
                if ($t !== '') $items[] = ['kind' => 'node', 'html' => '<p>' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '</p>', 'tint' => $tint];
                continue;
            }
            if (!$n instanceof DOMElement) continue;
            $tag = strtolower($n->tagName);
            $cls = ' ' . $n->getAttribute('class') . ' ';
            if ($tag === 'hr' || $tag === 'style' || $tag === 'script') continue;
            if ($tag === 'aside' && str_contains($cls, ' rg-tinted-section ')) {
                $t = preg_match('~ rg-tinted-(\d+) ~', $cls, $m) ? (int) $m[1] : 1;
                self::walk($n, max(1, $t), $items);
                continue;
            }
            if ($tag === 'div' && (str_contains($cls, ' prose ') || str_contains($cls, ' rg-stay-block '))) {
                self::walk($n, $tint, $items);
                continue;
            }
            if ($tag === 'h2') {
                $icon = '';
                foreach ($n->childNodes as $c) {
                    if ($c instanceof DOMElement && $c->getElementsByTagName('svg')->length) {
                        $svg = $c->getElementsByTagName('svg')->item(0);
                        $svg->removeAttribute('class');
                        $icon = self::$doc->saveHTML($svg);
                        break;
                    }
                }
                $items[] = ['kind' => 'h2', 'text' => trim(preg_replace('/\s+/u', ' ', $n->textContent)), 'icon' => $icon, 'tint' => $tint];
                continue;
            }
            if ($tag === 'div' && str_contains($cls, ' grid ') && $n->getElementsByTagName('a')->length) {
                $items[] = ['kind' => 'stays', 'node' => $n, 'tint' => $tint];
                continue;
            }
            if ($tag === 'div' && trim($n->textContent) === '' && !$n->getElementsByTagName('img')->length) continue;
            // a figure whose photo was found to show something else is left out
            if ($tag === 'figure' && ($fi = $n->getElementsByTagName('img')->item(0)) && !GuideHtml::usable((string) $fi->getAttribute('src'))) continue;
            $items[] = ['kind' => 'node', 'html' => self::$doc->saveHTML($n), 'tint' => $tint];
        }
    }

    // ------------------------------------------------------------------ render

    private static function render(array $items): string
    {
        $intro = [];
        $sections = [];
        $cur = null;
        foreach ($items as $it) {
            if ($it['kind'] === 'h2') {
                if ($cur) $sections[] = $cur;
                $cur = ['h2' => $it, 'body' => []];
            } elseif ($cur) {
                $cur['body'][] = $it;
            } else {
                $intro[] = $it;
            }
        }
        if ($cur) $sections[] = $cur;

        $out = '';
        if ($intro) $out .= '<div class="bx-intro">' . self::body($intro) . '</div>';

        $dayRe = '~^(Days?\s+\d+(?:\s*(?:-|–|to|and|&)\s*\d+)?)\s*[:.–-]\s*(.+)$~iu';
        $count = count($sections);
        for ($i = 0; $i < $count; $i++) {
            $s = $sections[$i];
            // a run of two or more "Day N: ..." sections becomes one timeline
            $run = [];
            for ($j = $i; $j < $count && preg_match($dayRe, $sections[$j]['h2']['text']); $j++) $run[] = $sections[$j];
            if (count($run) >= 2) {
                $out .= '<div class="bx-days">';
                foreach ($run as $d) {
                    preg_match($dayRe, $d['h2']['text'], $m);
                    $num = trim(preg_replace('~^Days?\s+~i', '', $m[1]));
                    $out .= '<section class="bx-day' . ($d['h2']['tint'] ? ' bx-day--hi' : '') . '">'
                        . '<div class="bx-day__node" aria-hidden="true"><span>Day</span><strong>' . self::e($num) . '</strong></div>'
                        . '<div class="bx-day__body"><h2 class="bx-day__title"><span class="bx-day__tag">' . self::e($m[1]) . '<span class="bx-colon">:</span></span> ' . self::e(trim($m[2])) . '</h2>'
                        . self::body($d['body']) . '</div></section>';
                }
                $out .= '</div>';
                $i = $j - 1;
                continue;
            }
            $hasStays = (bool) array_filter($s['body'], fn ($b) => $b['kind'] === 'stays');
            $tint = $s['h2']['tint'];
            $cls = 'bx-sec' . ($hasStays ? ' bx-sec--stays' : ($tint ? ' bx-sec--card bx-sec--t' . ($tint % 4) : ''));
            $out .= '<section class="' . $cls . '"><h2 class="bx-sec__title">'
                . ($s['h2']['icon'] !== '' ? '<span class="bx-ico" aria-hidden="true">' . $s['h2']['icon'] . '</span>' : '')
                . '<span>' . self::e($s['h2']['text']) . '</span></h2>' . self::body($s['body']) . '</section>';
        }
        return $out;
    }

    private static function body(array $nodes): string
    {
        $h = '';
        foreach ($nodes as $b) {
            $h .= $b['kind'] === 'stays' ? self::stays($b['node']) : self::localUrls($b['html']);
        }
        return $h;
    }

    /** The appended "Where to stay near ..." links as guide cards. */
    private static function stays(DOMElement $grid): string
    {
        $out = '<div class="bx-stays not-prose">';
        $seen = [];
        $n = 0;
        foreach ($grid->getElementsByTagName('a') as $a) {
            $href = self::localUrls($a->getAttribute('href'));
            $img = $a->getElementsByTagName('img')->item(0);
            $src = $img ? self::localUrls($img->getAttribute('src')) : '';
            $title = '';
            $kicker = '';
            foreach ($a->getElementsByTagName('div') as $d) {
                $c = ' ' . $d->getAttribute('class') . ' ';
                if (str_contains($c, ' font-bold ')) $title = trim($d->textContent);
                if (str_contains($c, ' uppercase ')) $kicker = trim($d->textContent);
            }
            if ($title === '') $title = $img ? $img->getAttribute('alt') : trim($a->textContent);
            $key = $src !== '' ? basename(parse_url($src, PHP_URL_PATH) ?: $src) : '';
            $photo = $src !== '' && !isset($seen[$key]) && !GuideHtml::isPlaceholder($src)
                ? '<span class="bx-stay__img"><img src="' . self::e($src) . '" alt="' . self::e($title) . '" loading="lazy"></span>'
                : '<span class="bx-stay__img gh-ph gh-ph--' . ($n % 4) . '"><span class="gh-ph__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.6-7-11.5A7 7 0 0 1 19 9.5C19 15.4 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.6"/></svg></span></span>';
            if ($key !== '') $seen[$key] = true;
            $out .= '<a class="bx-stay" href="' . self::e($href) . '">' . $photo
                . '<span class="bx-stay__body"><span class="bx-stay__kicker">' . self::e($kicker ?: 'Curated picks') . '</span>'
                . '<span class="bx-stay__title">' . self::e($title) . '</span>'
                . '<span class="bx-stay__go">See the guide <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span></span></a>';
            $n++;
        }
        return $out . '</div>';
    }

    /** Seeded links kept the dev host (http://resortguruph.test/...): make them root-relative. */
    private static function localUrls(string $s): string
    {
        return preg_replace('~https?://(?:resortguruph\.test|localhost(?::\d+)?|127\.0\.0\.1(?::\d+)?)(?=/)~i', '', $s) ?? $s;
    }

    private static function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
