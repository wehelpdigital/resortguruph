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

    /** Photo de-duplication for one page render (see startPage / claim). */
    private static bool $track = false;
    private static array $seen = [];
    private static array $keys = [];
    private static array $media = [];
    private static int $tone = 0;
    /** normalised spot name => photo (the page's own attraction photos) */
    private static array $prefer = [];

    /**
     * Start tracking photos for one page: from now on claim() only lets the
     * first use of each photo through, so a guide never shows the same
     * picture twice (several spots shared one fallback photo).
     */
    public static function startPage(): void
    {
        self::$track = true;
        self::$seen = [];
        self::$tone = 0;
        self::$prefer = [];
    }

    /**
     * Photos the page already has for named places (its attractions block):
     * spot cards with the same name use them instead of a generic town photo.
     */
    public static function preferPhotos(array $nameToSrc): void
    {
        foreach ($nameToSrc as $name => $src) {
            $src = trim((string) $src);
            $key = self::nameKey((string) $name);
            if ($key === '' || $src === '' || self::isPlaceholder($src)) continue;
            if (!preg_match('~^(https?:)?//|^/~', $src)) $src = '/storage/' . ltrim($src, '/');
            $src = preg_replace('~^https?://[^/]+(?=/storage/)~i', '', $src);
            if (preg_match('~/rg-media/destinations/~', $src)) continue;
            self::$prefer[$key] = $src;
        }
    }

    /** "Ma-Cho Temple (San Fernando city)" -> "ma cho temple" */
    public static function nameKey(string $s): string
    {
        $s = mb_strtolower(html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $s = preg_replace('~\(.*?\)~u', ' ', $s);
        $s = preg_replace('~[^\p{L}\p{N}]+~u', ' ', (string) $s);
        return trim(preg_replace('~\s+~u', ' ', (string) $s));
    }

    /**
     * The best photo for a spot card. Seeded lists filled photo-less spots with
     * the town's generic photos (rg-media/destinations/*), cycling through them,
     * so a halo-halo shop showed a forest path. Order: the page's own attraction
     * photo for that name, the spot's own photo, the tourist-spot library by
     * name; a generic town photo is not used (the card gets a panel).
     */
    private static function spotPhoto(string $name, string $src, string $place = ''): string
    {
        if (!self::$track) return $src;
        $checked = self::subjectPhoto('place', $name, $place);
        if ($checked !== '') return $checked;
        $key = self::nameKey($name);
        if ($key !== '' && isset(self::$prefer[$key])) return self::$prefer[$key];
        $generic = $src === '' || self::isPlaceholder($src) || preg_match('~/rg-media/destinations/~', $src);
        if (!$generic) return $src;
        $lib = self::spotLibrary();
        return $key !== '' && isset($lib[$key]) ? $lib[$key] : '';
    }

    /**
     * The photo checked for a named place, dish or festival (rg_media rows with
     * source "subject-photo", written by tools/image-audit/apply_subjects.php).
     * Looked up by name + place; by name alone only when the name is specific
     * ("Kawasan Falls", not "Public Market").
     */
    public static function subjectPhoto(string $kind, string $name, string $place = ''): string
    {
        $k = self::nameKey($name);
        if ($k === '') return '';
        $reg = self::registry()[$kind] ?? [];
        $hit = $reg[$k . '|' . self::nameKey($place)] ?? '';
        if ($hit !== '' || $kind === 'food') return $hit !== '' ? $hit : ($reg[$k . '|'] ?? '');
        $generic = ['public', 'market', 'plaza', 'church', 'cathedral', 'beach', 'hall', 'park', 'museum', 'town', 'city', 'municipal',
            'capitol', 'shrine', 'old', 'heritage', 'house', 'mall', 'river', 'falls', 'lake', 'boulevard', 'baywalk', 'pier', 'port',
            'parish', 'san', 'santa', 'santo', 'st', 'the', 'and', 'of', 'de', 'la', 'ng', 'sa', 'main', 'central', 'downtown', 'strip'];
        $specific = array_diff(explode(' ', $k), $generic);
        return count($specific) >= 1 && count(explode(' ', $k)) >= 2 ? ($reg[$k . '|'] ?? '') : '';
    }

    /**
     * database/data/subject_photos.json (kept in the repo so it ships with each deploy):
     * {"place": {"<name key>|<place key>": "/storage/...", ...}, "food": {...}, ...,
     *  "_blocked": {"/storage/...": "reason"}}. Written by tools/image-audit/apply_subjects.php
     * and block_photos.php. A name-only entry ("<name key>|") is added for the first photo
     * of each name.
     */
    private static function registry(): array
    {
        static $reg = null;
        if ($reg !== null) return $reg;
        $file = database_path('data/subject_photos.json');
        $reg = is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
        return $reg;
    }

    /**
     * A photo of an area (town or province names, most specific first): a checked place photo
     * of that area, a tourist-spot library photo there, then the town photo. Used where a card
     * must have a picture and has none of its own (a festival without a cover).
     */
    public static function areaPhoto(array $places, string $cluster = ''): string
    {
        $keys = array_values(array_filter(array_map(fn ($p) => self::nameKey((string) $p), $places)));
        foreach ($keys as $pk) {
            foreach (self::registry()['place'] ?? [] as $key => $src) {
                if (str_ends_with($key, '|' . $pk) && self::usable($src)) return $src;
            }
        }
        try {
            foreach (\Illuminate\Support\Facades\DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->where('s.status', 'published')
                ->get(['s.location', 's.region_label', 'm.path']) as $r) {
                if (!in_array(self::nameKey((string) $r->location), $keys, true) && !in_array(self::nameKey((string) $r->region_label), $keys, true)) continue;
                $src = '/storage/' . ltrim((string) $r->path, '/');
                if (!preg_match('~/rg-media/(destinations|food-locations)/~', $src) && self::usable($src)) return $src;
            }
        } catch (\Throwable $e) {
        }
        foreach ($places as $p) {
            $slug = \Illuminate\Support\Str::slug((string) $p);
            foreach (["/storage/rg-media/destinations/{$slug}-1.jpg", "/storage/rg-media/destinations/{$slug}-province-1.jpg"] as $src) if ($slug !== '' && self::usable($src)) return $src;
        }
        $land = '/storage/rg-media/landmarks/' . $cluster . '.jpg';
        return $cluster !== '' && self::usable($land) ? $land : '';
    }

    /** The tourist-spot library photo of a place with this name ('' when none). */
    public static function libraryPhoto(string $name): string
    {
        $lib = self::spotLibrary();
        $k = self::nameKey($name);
        return $k !== '' && isset($lib[$k]) && self::usable($lib[$k]) ? $lib[$k] : '';
    }

    /** Published tourist spots with their own photo, by normalised name (cached). */
    private static function spotLibrary(): array
    {
        static $lib = null;
        if ($lib !== null) return $lib;
        try {
            $lib = \Illuminate\Support\Facades\Cache::remember('gh_spot_library_v1', 600, function () {
                $out = [];
                if (!\Illuminate\Support\Facades\Schema::hasTable('rg_tourist_spots')) return $out;
                foreach (\Illuminate\Support\Facades\DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')
                    ->where('s.status', 'published')->get(['s.name', 'm.path']) as $r) {
                    $path = ltrim((string) $r->path, '/');
                    if ($path === '' || str_contains($path, 'rg-media/destinations/')) continue;
                    $key = self::nameKey((string) $r->name);
                    if ($key !== '' && !isset($out[$key])) $out[$key] = '/storage/' . $path;
                }
                return $out;
            });
        } catch (\Throwable $e) {
            $lib = [];
        }
        return $lib;
    }

    public static function tracking(): bool
    {
        return self::$track;
    }

    /** Has this photo (same file content) already been shown on the page? Does not record it. */
    public static function seen(string $src): bool
    {
        return self::$track && isset(self::$seen[self::photoKey(trim($src))]);
    }

    /** Content key of a photo: two files with the same picture share it. */
    public static function photoId(string $src): string
    {
        return self::photoKey(trim($src));
    }

    /** True when $src is a real photo not yet shown on this page (and records it). */
    public static function claim(string $src): bool
    {
        $src = trim($src);
        if ($src === '' || self::isPlaceholder($src)) return false;
        if (!self::$track) return true;
        $key = self::photoKey($src);
        if (str_starts_with($key, '/storage/')) return false; // a library file that is not on disk
        if (isset(self::$seen[$key])) return false;
        return self::$seen[$key] = true;
    }

    /** A real photo that can be shown: not a placeholder, not blocked, and on disk when it is a library file. */
    public static function usable(string $src): bool
    {
        $src = trim($src);
        if ($src === '' || self::isPlaceholder($src)) return false;
        $path = (string) strtok(preg_replace('~^https?://[^/]+~i', '', $src), '?');
        return !str_starts_with($path, '/storage/') || is_file(storage_path('app/public/' . rawurldecode(substr($path, 9))));
    }

    /**
     * The photo for an attraction card: its own "image", else the first entry of its
     * "images" list whose file name names the place ("moa-arena.jpg" for "Mall of Asia
     * Arena"). The lists were filled with the area's photos in turn, so an entry that
     * does not name the place is not used.
     */
    public static function attractionPhoto(array $item, string $place = ''): string
    {
        $own = trim((string) ($item['image'] ?? ''));
        if ($own !== '' && self::usable($own)) return $own;
        $generic = array_flip(array_merge(explode(' ', self::nameKey($place)), ['the', 'of', 'and', 'in', 'at', 'sm', 'mall', 'moa', 'asia', 'city', 'town', 'main', 'inside', 'area', 'side', 'beach', 'park']));
        $want = array_diff(explode(' ', self::nameKey((string) ($item['name'] ?? ''))), array_keys($generic));
        if (!$want) return '';
        foreach ((array) ($item['images'] ?? []) as $cand) {
            $cand = (string) $cand;
            if (!self::usable($cand)) continue;
            $file = self::nameKey(str_replace(['-', '_'], ' ', pathinfo((string) parse_url($cand, PHP_URL_PATH), PATHINFO_FILENAME)));
            foreach ($want as $w) if (strlen($w) > 3 && str_contains(' ' . $file . ' ', ' ' . $w . ' ')) return $cand;
            foreach ($want as $w) if (strlen($w) >= 3 && str_contains(str_replace(" ", "", $file), $w)) return $cand;
        }
        return '';
    }

    public static function isPlaceholder(string $src): bool
    {
        if (preg_match('~placeholder|placehold\.co|/images/brand/~i', $src)) return true;
        // photos found to show something else (a different place, a book scan): never shown
        $blocked = self::registry()['_blocked'] ?? [];
        return $blocked && isset($blocked[(string) strtok(preg_replace('~^https?://[^/]+~i', '', $src), '?')]);
    }

    /** Same file content = same photo, even under two names. */
    private static function photoKey(string $src): string
    {
        $path = (string) strtok(preg_replace('~^https?://[^/]+~i', '', $src), '?');
        if (isset(self::$keys[$path])) return self::$keys[$path];
        $key = $path;
        if (str_starts_with($path, '/storage/')) {
            $file = storage_path('app/public/' . rawurldecode(substr($path, 9)));
            if (is_file($file)) $key = filesize($file) . ':' . md5((string) file_get_contents($file, false, null, 0, 65536));
        }
        return self::$keys[$path] = $key;
    }

    /** Library caption + credit for a /storage photo, when the media library knows it. */
    public static function mediaMeta(string $src): ?array
    {
        $path = (string) strtok(preg_replace('~^(https?://[^/]+)?/storage/~i', '', trim($src)), '?');
        if ($path === '' || str_starts_with($path, 'http') || str_starts_with($path, '/')) return null;
        if (!array_key_exists($path, self::$media)) {
            try {
                $row = \Illuminate\Support\Facades\DB::table('rg_media')->where('path', $path)->first(['caption', 'credit', 'source_url']);
            } catch (\Throwable $e) {
                $row = null;
            }
            self::$media[$path] = $row ? (array) $row : null;
        }
        return self::$media[$path];
    }

    /** Caption that reads like a Commons file name rather than a sentence. */
    public static function looksLikeFileName(string $caption): bool
    {
        $c = trim($caption);
        if ($c === '') return true;
        if (preg_match('~^(photo|image|credit)\b|wikimedia|\bcc[ -]?(by|0)|public domain~i', $c)) return false;
        return (bool) preg_match('~\.(jpe?g|png|webp)$|[a-z]{2,}\d{3,}|\d{2,}[a-z]{2,}|\d{4} \d{2} \d{2}|\b(DSC|IMG|DD)[ _]?\d|,\S|jf\d~i', $c);
    }

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
        $place = preg_match('~^Tourist spots (?:in|near|around) (.+)$~i', trim($h2), $pm) ? trim($pm[1]) : '';

        // pass 1: read every card and settle its photo (claim() runs once per card)
        $cards = [];
        foreach (self::all('.//article', $el) as $art) {
            $img = self::one('.//img', $art);
            $imgLink = $img && $img->parentNode instanceof DOMElement && strtolower($img->parentNode->tagName) === 'a' ? $img->parentNode->getAttribute('href') : '';
            $h3 = self::one('.//h3', $art);
            $body = $h3 ? $h3->parentNode : $art;
            $name = self::text($h3);
            $own = $img ? trim($img->getAttribute('src')) : '';
            $src = self::spotPhoto($name, $own, $place);
            if ($src !== '' && !self::claim($src)) $src = '';
            if ($src === '' && !self::$track && $own !== '') $src = $own;
            $score = ''; $count = '';
            foreach (self::all('.//span', $body) as $sp) {
                $t = trim($sp->textContent);
                if ($score === '' && preg_match('/^\d(\.\d)?$/', $t)) $score = $t;
                if ($count === '' && preg_match('/review/i', $t) && strlen($t) < 40) $count = trim($t, '() ');
            }
            $paras = [];
            foreach ($body->childNodes as $c) if ($c instanceof DOMElement && strtolower($c->tagName) === 'p') $paras[] = self::inner($c);
            $links = [];
            foreach ($body->childNodes as $c) if ($c instanceof DOMElement && strtolower($c->tagName) === 'a') $links[] = [$c->getAttribute('href'), trim($c->textContent)];
            $review = self::one('.//*[contains(@class, "rg-spot-review-slide")]', $art);
            $cards[] = compact('name', 'src', 'imgLink', 'score', 'count', 'paras', 'links') + ['alt' => $img ? $img->getAttribute('alt') : '', 'quote' => $review ? self::quote($review) : ''];
        }
        // only cards with a photo are shown; places that have none make way for other
        // checked places of the same area (tourist-spot library), so every card has a picture
        $shown = array_values(array_filter($cards, fn ($c) => $c['src'] !== ''));
        $missing = count($cards) - count($shown);
        if ($missing > 0) {
            $have = array_map(fn ($c) => self::nameKey($c['name']), $shown);
            foreach (self::areaSpots($place, $have, min($missing, 8)) as $alt) $shown[] = $alt;
        }
        $cards = $shown;
        if (!$cards) return '';

        $out = '<section class="gh-spots not-prose">' . self::head('Things to see', $h2, $intro) . '<div class="gh-spots__grid">';
        foreach ($cards as $i => $c) {
            $n = $i + 1;
            $num = '<span class="gh-num">' . str_pad((string) $n, 2, '0', STR_PAD_LEFT) . '</span>';
            $out .= '<article class="gh-spot' . ($n === 1 ? ' gh-spot--lead' : '') . '">';
            $alt = trim($c['alt']) !== '' && !self::looksLikeFileName($c['alt']) ? $c['alt'] : $c['name'];
            $imgHtml = '<img src="' . self::e($c['src']) . '" alt="' . self::e($alt) . '" loading="lazy">';
            $out .= $c['imgLink'] !== ''
                ? '<a class="gh-spot__img" href="' . self::e($c['imgLink']) . '" target="_blank" rel="noopener nofollow">' . $imgHtml . $num . '</a>'
                : '<span class="gh-spot__img">' . $imgHtml . $num . '</span>';
            $out .= '<div class="gh-spot__body"><h3 class="gh-spot__name">' . self::e($c['name']) . '</h3>';
            if ($c['score'] !== '') {
                $out .= '<p class="gh-rating">' . self::stars((float) $c['score']) . '<strong>' . self::e($c['score']) . '</strong>' . ($c['count'] !== '' ? '<span>' . self::e($c['count']) . '</span>' : '') . '</p>';
            }
            if ($c['paras']) {
                $out .= '<p class="gh-p gh-p--clamp">' . $c['paras'][0] . '</p>';
                if (count($c['paras']) > 1) {
                    $out .= '<details class="gh-more"><summary><span>Read more</span></summary>';
                    foreach (array_slice($c['paras'], 1) as $p) $out .= '<p class="gh-p">' . $p . '</p>';
                    $out .= '</details>';
                }
            }
            $out .= $c['quote'];
            if ($c['links']) {
                $out .= '<p class="gh-links">';
                foreach ($c['links'] as [$href, $label]) $out .= '<a href="' . self::e($href) . '" target="_blank" rel="noopener nofollow">' . self::e($label) . self::arrow() . '</a>';
                $out .= '</p>';
            }
            $out .= '</div></article>';
        }
        return $out . '</div></section>';
    }

    /**
     * Other places of the same area with a checked photo (tourist-spot library), as spot
     * cards: used where a list's own places have no photo. Each photo once per page.
     */
    private static function areaSpots(string $place, array $haveKeys, int $limit): array
    {
        $pk = self::nameKey($place);
        if ($pk === '' || $limit < 1) return [];
        static $all = null;
        if ($all === null) {
            try {
                $all = \Illuminate\Support\Facades\DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')
                    ->where('s.status', 'published')->orderByRaw('s.featured_order is null')->orderBy('s.featured_order')->orderBy('s.id')
                    ->get(['s.name', 's.location', 's.region_label', 's.description', 'm.path'])->all();
            } catch (\Throwable $e) {
                $all = [];
            }
        }
        $have = array_flip($haveKeys);
        $out = [];
        foreach ($all as $s) {
            $lk = self::nameKey((string) $s->location);
            $rk = self::nameKey((string) $s->region_label);
            $near = ($lk !== '' && ($lk === $pk || str_contains(' ' . $pk . ' ', ' ' . $lk . ' ') || str_contains(' ' . $lk . ' ', ' ' . $pk . ' ')))
                || ($rk !== '' && $rk === $pk);
            if (!$near) continue;
            $k = self::nameKey((string) $s->name);
            if ($k === '' || isset($have[$k])) continue;
            $src = '/storage/' . ltrim((string) $s->path, '/');
            if (preg_match('~/rg-media/(destinations|food-locations)/~', $src) || !self::usable($src) || !self::claim($src)) continue;
            $have[$k] = true;
            $desc = trim(strip_tags((string) $s->description));
            $desc = $desc === '' ? '' : self::e(rtrim(ucfirst($desc), '.') . '.');
            $out[] = [
                'name' => (string) $s->name, 'src' => $src, 'imgLink' => '', 'score' => '', 'count' => '', 'alt' => (string) $s->name,
                'paras' => $desc !== '' ? [$desc] : [], 'quote' => '',
                'links' => [['https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($s->name . ' ' . $s->location) . ' Philippines'), 'Open in Google Maps']],
            ];
            if (count($out) >= $limit) break;
        }
        return $out;
    }

    private static function festivals(DOMElement $el, string $h2): string
    {
        $intro = self::first('.//div[1]/p', $el);
        $fplace = preg_match('~^Notable festivals (?:in|near|around) (.+)$~i', trim($h2), $fm) ? trim($fm[1]) : '';
        $items = [];
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
            // a checked festival photo first; a generic town photo is not a picture of the festival
            $src = self::subjectPhoto('festival', self::text($h3), $fplace);
            if ($src === '' && $img && !preg_match('~/rg-media/destinations/~', (string) $img->getAttribute('src'))) $src = trim((string) $img->getAttribute('src'));
            if ($src === '' || !self::claim($src)) continue; // no photo of the festival: left out
            $items[] = ['name' => self::text($h3), 'src' => $src, 'when' => $when, 'paras' => $paras, 'href' => ''];
        }
        // festival pages of the same area fill in for the ones without a photo
        foreach (self::areaFiestas($fplace, array_map(fn ($i) => self::nameKey($i['name']), $items), max(0, 3 - count($items))) as $f) $items[] = $f;
        if (!$items) return '';
        $out = '<section class="gh-fests not-prose">' . self::head('Festivals', $h2, $intro) . '<div class="gh-fests__list">';
        foreach ($items as $it) {
            $date = $it['when'] !== '' ? '<span class="gh-date">' . self::cal() . self::e($it['when']) . '</span>' : '';
            $out .= '<article class="gh-fest"><span class="gh-fest__img"><img src="' . self::e($it['src']) . '" alt="' . self::e($it['name']) . '" loading="lazy">' . $date . '</span>';
            $out .= '<div class="gh-fest__body"><h3 class="gh-fest__name">' . self::e($it['name']) . '</h3>';
            foreach ($it['paras'] as $i => $p) {
                if ($i === 1 && count($it['paras']) > 2) { $out .= '<details class="gh-more"><summary><span>Read more</span></summary>'; }
                $out .= '<p class="gh-p">' . $p . '</p>';
            }
            if (count($it['paras']) > 2) $out .= '</details>';
            if ($it['href'] !== '') $out .= '<p class="gh-links"><a href="' . self::e($it['href']) . '">Festival guide' . self::arrow() . '</a></p>';
            $out .= '</div></article>';
        }
        return $out . '</div></section>';
    }

    /** Festival pages (with a cover photo) of the same province or town, as festival cards. */
    private static function areaFiestas(string $place, array $haveKeys, int $limit): array
    {
        $pk = self::nameKey($place);
        if ($pk === '' || $limit < 1) return [];
        try {
            $rows = \Illuminate\Support\Facades\DB::table('rg_fiestas')->where('is_published', 1)->whereNotNull('cover_image_path')
                ->get(['slug', 'name', 'province', 'city_or_town', 'date_label', 'summary', 'cover_image_path']);
        } catch (\Throwable $e) {
            return [];
        }
        $have = array_flip($haveKeys);
        $out = [];
        foreach ($rows as $f) {
            $keys = array_filter([self::nameKey((string) $f->province), self::nameKey((string) $f->city_or_town)]);
            $near = false;
            foreach ($keys as $k) if ($k === $pk || str_contains(' ' . $pk . ' ', ' ' . $k . ' ')) $near = true;
            if (!$near || isset($have[self::nameKey((string) $f->name)])) continue;
            $cover = (string) $f->cover_image_path;
            $src = preg_match('~^(https?:)?//|^/~', $cover) ? $cover : '/storage/' . ltrim($cover, '/');
            if (!self::usable($src) || !self::claim($src)) continue;
            $out[] = ['name' => (string) $f->name, 'src' => $src, 'when' => (string) $f->date_label, 'paras' => [self::e((string) $f->summary)], 'href' => '/fiestas/' . $f->slug];
            if (count($out) >= $limit) break;
        }
        return $out;
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
        $src = $img ? trim($img->getAttribute('src')) : '';
        $out = $src !== '' && self::claim($src)
            ? '<aside class="gh-note not-prose"><a class="gh-note__img" href="' . self::e($href ?: '#') . '" target="_blank" rel="noopener nofollow"><img src="' . self::e($src) . '" alt="Field notes photo" loading="lazy"></a>'
            : '<aside class="gh-note gh-note--solo not-prose">';
        $out .= '<div class="gh-note__body"><p class="gh-eyebrow">Field notes</p>';
        foreach ($paras as $p) $out .= '<p class="gh-p">' . $p . '</p>';
        $out .= '<p class="gh-note__foot">' . ($via !== '' ? '<span>' . $via . '</span>' : '') . ($href !== '' ? '<a href="' . self::e($href) . '" target="_blank" rel="noopener nofollow">Read the full post' . self::arrow() . '</a>' : '') . '</p>';
        return $out . '</div></aside>';
    }

    private static function reads(DOMElement $el, string $h2): string
    {
        $intro = self::first('.//p', $el);
        $cards = self::all('.//a[.//h3]', $el);
        $covers = self::blogCovers(array_map(fn ($a) => $a->getAttribute('href'), $cards));
        $html = '';
        foreach ($cards as $a) {
            $img = self::one('.//img', $a);
            $href = $a->getAttribute('href');
            $title = self::text(self::one('.//h3', $a));
            $src = $covers[$href] ?? ($img ? trim($img->getAttribute('src')) : '');
            if ($src === '' || !self::claim($src)) continue; // a story without its own cover is left out
            $excerpt = self::one('.//p', $a);
            $html .= '<a class="gh-read" href="' . self::e($href) . '"><span class="gh-read__img"><img src="' . self::e($src) . '" alt="' . self::e($title) . '" loading="lazy"></span>';
            $html .= '<span class="gh-read__body"><span class="gh-read__kicker">Travel story</span><span class="gh-read__title">' . self::e($title) . '</span>';
            if ($excerpt) $html .= '<span class="gh-read__text">' . self::e(self::text($excerpt)) . '</span>';
            $html .= '<span class="gh-read__go">Read the story' . self::arrow() . '</span></span></a>';
        }
        if ($html === '') return '';
        return '<section class="gh-reads not-prose">' . self::head('From the blog', $h2, $intro) . '<div class="gh-reads__grid">' . $html . '</div></section>';
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
        $html = '';
        foreach (self::all('.//*[contains(concat(" ", normalize-space(@class), " "), " food-card ")]', $el) as $card) {
            $a = self::one('.//a', $card);
            $img = self::one('.//img', $card);
            $cap = self::text(self::one('.//figcaption', $card));
            $h3 = self::one('.//h3', $card);
            $p = self::one('.//p', $card);
            $name = self::text($h3);
            // the checked dish photo, else the card's own photo when it is not a town/mall stand-in, else the dish's photo
            $src = self::subjectPhoto('food', $name);
            if ($src !== '') $cap = '';
            if ($src === '' && $img && !preg_match('~/rg-media/(destinations|food-locations)/~', (string) $img->getAttribute('src'))) $src = trim((string) $img->getAttribute('src'));
            if ($src === '' || !self::usable($src) || self::seen($src)) { $src = self::dishPhoto($name); $cap = ''; }
            if ($src === '' || !self::claim($src)) continue; // a dish without a photo is left out
            $html .= '<article class="gh-food"><span class="gh-food__img"><img src="' . self::e($src) . '" alt="' . self::e($name) . '" loading="lazy"></span>';
            $html .= '<div class="gh-food__body"><h3 class="gh-food__name">' . self::e($name) . '</h3>';
            if ($p) $html .= '<p class="gh-food__text">' . self::inner($p) . '</p>';
            if ($a) $html .= '<a class="gh-food__link" href="' . self::e($a->getAttribute('href')) . '" target="_blank" rel="noopener nofollow">' . self::e($a->getAttribute('title') ?: 'Learn more') . self::arrow() . '</a>';
            if ($cap !== '') $html .= '<p class="gh-food__cap">Photo: ' . self::e($cap) . '</p>';
            $html .= '</div></article>';
        }
        return $html === '' ? '' : '<div class="gh-foods not-prose">' . $html . '</div>';
    }

    /**
     * A photo of the dish: the checked photo, else the photo of the same dish on the
     * Filipino food hub ("Pinakbet Ilocano" uses the Pinakbet photo). Not recorded as shown.
     */
    public static function dishPhoto(string $name): string
    {
        $checked = self::subjectPhoto('food', $name);
        if ($checked !== '' && !self::seen($checked)) return $checked;
        $hub = self::hubDishPhotos();
        $k = self::nameKey($name);
        if ($k === '') return '';
        $cands = [];
        if (isset($hub[$k])) $cands[] = $hub[$k];
        $toks = array_flip(explode(' ', $k));
        $best = []; // hub dishes whose whole name is part of this name, longest first
        foreach ($hub as $hk => $srcs) {
            $ht = array_unique(explode(' ', $hk));
            if (count(array_intersect_key(array_flip($ht), $toks)) === count($ht)) $best[strlen($hk)] = $srcs;
        }
        krsort($best);
        foreach ($best as $srcs) $cands[] = $srcs;
        foreach ($cands as $srcs) foreach ($srcs as $src) if (self::usable($src) && !self::seen($src)) return $src;
        return '';
    }

    /** name key => photos of each dish on the foods hub (database/data + rg-media/foods/{slug}-N.jpg). */
    private static function hubDishPhotos(): array
    {
        static $map = null;
        if ($map !== null) return $map;
        $map = [];
        try {
            $m = new \ReflectionMethod(\App\Http\Controllers\FoodsController::class, 'categories');
            $m->setAccessible(true);
            foreach ($m->invoke(app(\App\Http\Controllers\FoodsController::class)) as $cat) {
                foreach ($cat['items'] ?? [] as $it) {
                    $imgs = array_values(array_map(fn ($u) => (string) preg_replace('~^https?://[^/]+(?=/storage/)~i', '', (string) $u), (array) ($it['images'] ?? [])));
                    $k = self::nameKey((string) ($it['name'] ?? ''));
                    if ($k !== '' && $imgs) $map[$k] = $imgs;
                }
            }
        } catch (\Throwable $e) {
            $map = [];
        }
        return $map;
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

    /** href => current cover URL for /blog/{slug} links (the seeded markup kept old covers). */
    private static function blogCovers(array $hrefs): array
    {
        $slugs = [];
        foreach ($hrefs as $h) if (preg_match('~/blog/([a-z0-9-]+)/?$~i', (string) $h, $m)) $slugs[$m[1]] = $h;
        if (!$slugs) return [];
        try {
            $rows = \Illuminate\Support\Facades\DB::table('rg_blog_posts')->whereIn('slug', array_keys($slugs))->whereNotNull('cover_path')->pluck('cover_path', 'slug');
        } catch (\Throwable $e) {
            return [];
        }
        $out = [];
        foreach ($rows as $slug => $path) {
            $path = trim((string) $path);
            if ($path === '') continue;
            $out[$slugs[$slug]] = preg_match('~^(https?:)?//|^/~', $path) ? $path : '/storage/' . ltrim($path, '/');
        }
        return $out;
    }

    private static function head(string $eyebrow, string $title, string $intro): string
    {
        return '<header class="gh-head"><p class="gh-eyebrow">' . self::e($eyebrow) . '</p><h2 class="gh-title">' . self::e($title) . '</h2>'
            . ($intro !== '' ? '<p class="gh-intro">' . $intro . '</p>' : '') . '</header>';
    }

    private static function photo(?DOMElement $img, string $href, string $cls, string $extra, string $label = '', string $icon = 'pin'): string
    {
        $src = $img ? trim($img->getAttribute('src')) : '';
        if ($src !== '' && self::claim($src)) {
            $inner = '<img src="' . self::e($src) . '" alt="' . self::e($img->getAttribute('alt')) . '" loading="lazy"' . ($img->getAttribute('referrerpolicy') ? ' referrerpolicy="' . self::e($img->getAttribute('referrerpolicy')) . '"' : '') . '>';
        } else {
            // no photo of its own (or the same picture as an earlier card): a designed panel instead
            $cls .= ' gh-ph gh-ph--' . (self::$tone++ % 4);
            $inner = '<span class="gh-ph__icon" aria-hidden="true">' . self::icon($icon) . '</span>'
                . ($label !== '' ? '<span class="gh-ph__label" aria-hidden="true">' . self::e($label) . '</span>' : '');
            $href = '';
        }
        return $href !== ''
            ? '<a class="' . $cls . '" href="' . self::e($href) . '" target="_blank" rel="noopener nofollow">' . $inner . $extra . '</a>'
            : '<span class="' . $cls . '">' . $inner . $extra . '</span>';
    }

    private static function icon(string $name): string
    {
        $paths = [
            'pin' => '<path d="M12 21s-7-5.6-7-11.5A7 7 0 0 1 19 9.5C19 15.4 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.6"/>',
            'food' => '<path d="M4 3v8a3 3 0 0 0 3 3v7M10 3v8a3 3 0 0 1-3 3M7 3v5"/><path d="M17 21V3c-2.2 1.4-3 4-3 7 0 2 1 3 3 3"/>',
            'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/>',
            'star' => '<path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/>',
        ];
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($paths[$name] ?? $paths['pin']) . '</svg>';
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
