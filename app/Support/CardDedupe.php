<?php

namespace App\Support;

/**
 * One card per place, dish, festival or story on a page.
 *
 * A guide is assembled from several seeded blocks that often name the same thing
 * (Halo-Halo de Iloko in the tourist spots and again in Foods to try, the same blog
 * post under "Related guides" and "More reads"). run() walks the page's HTML parts in
 * the order they are shown and drops every later card with a name or link already
 * shown. Only cards built by GuideHtml / BlockRenderer are touched (their markup is
 * known and never nests the same tag), the rest of the HTML passes through unchanged.
 */
class CardDedupe
{
    /** card markup => how to read its name */
    private const CARD_RE = [
        '~<article class="(?:gh-spot|gh-food|gh-fest|rg-food)(?:[ "][^>]*)?>.*?</article>~s',
        '~<a class="gh-read" href="[^"]*">.*?</a>~s',
        '~<a href="[^"]*"[^>]*class="rg-rel-card[^"]*">.*?</a>~s',
    ];

    private const NAME_RE = '~class="(?:gh-spot__name|gh-food__name|gh-fest__name|rg-food__name|gh-read__title)"[^>]*>(.*?)</|<h3[^>]*>(.*?)</h3>~s';

    private array $names = [];
    private array $links = [];

    /** @param array<string,string> $parts html in display order @return array<string,string> */
    public static function run(array $parts): array
    {
        $d = new self();
        foreach ($parts as $k => $html) $parts[$k] = $d->filter((string) $html);
        return $parts;
    }

    /** Names already shown (normalised), for callers that build cards themselves. */
    public static function key(string $name): string
    {
        $name = html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return GuideHtml::nameKey(trim((string) preg_replace('~\(.*?\)~', ' ', $name)));
    }

    public function seen(string $name): bool
    {
        return isset($this->names[self::key($name)]);
    }

    private function filter(string $html): string
    {
        if ($html === '' || !preg_match('~gh-spot|gh-food|gh-fest|rg-food|gh-read|rg-rel-card~', $html)) return $html;
        $removed = false;
        foreach (self::CARD_RE as $re) {
            $html = (string) preg_replace_callback($re, function ($m) use (&$removed) {
                $card = $m[0];
                $name = preg_match(self::NAME_RE, $card, $nm) ? ($nm[1] !== '' ? $nm[1] : ($nm[2] ?? '')) : '';
                $key = self::key($name);
                $href = preg_match('~href="([^"]*)"~', $card, $hm) && !str_contains($hm[1], 'google.com/maps') && !str_contains($hm[1], 'wikipedia') && !str_contains($hm[1], 'tripadvisor') ? rtrim(strtolower(strtok($hm[1], '?#')), '/') : '';
                $isLink = str_starts_with($card, '<a ');
                if (($key !== '' && isset($this->names[$key])) || ($isLink && $href !== '' && isset($this->links[$href]))) {
                    $removed = true;
                    return '';
                }
                if ($key !== '') $this->names[$key] = true;
                if ($isLink && $href !== '') $this->links[$href] = true;
                return $card;
            }, $html);
        }
        if (!$removed) return $html;
        // containers left without cards go too; spot numbers run 01, 02, ... again
        $html = (string) preg_replace('~<div class="gh-foods not-prose">\s*</div>~', '', $html);
        $html = (string) preg_replace_callback('~<section class="rg-foods not-prose[^"]*">.*?</section>~s', fn ($m) => str_contains($m[0], '<article class="rg-food') ? $m[0] : '', $html);
        $html = (string) preg_replace_callback('~<section class="gh-(?:reads|fests|spots) not-prose">.*?</section>~s', fn ($m) => preg_match('~<(?:article|a) class="gh-(?:read|fest|spot)~', $m[0]) ? $m[0] : '', $html);
        $html = (string) preg_replace_callback('~<section class="not-prose my-12">(?:(?!</section>).)*?<div class="grid grid-cols-1[^"]*">\s*</div></section>~s', fn () => '', $html);
        $html = (string) preg_replace_callback('~<section class="gh-spots not-prose">.*?</section>~s', function ($m) {
            $n = 0;
            return (string) preg_replace_callback('~<span class="gh-num">\d+</span>~', function () use (&$n) { return '<span class="gh-num">' . str_pad((string) ++$n, 2, '0', STR_PAD_LEFT) . '</span>'; }, $m[0]);
        }, $html);
        return $html;
    }
}
