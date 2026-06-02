<?php

namespace App\Services;

use App\Models\RgContentBlock;
use App\Models\RgKeyword;
use App\Models\RgListing;
use Illuminate\Support\Collection;

class BlockRenderer
{
    public function renderFor(string $ownerType, int $ownerId, array $context = []): string
    {
        $blocks = RgContentBlock::forOwner($ownerType, $ownerId);
        return $this->renderBlocks($blocks, $context);
    }

    public function renderBlocks(Collection $blocks, array $context = []): string
    {
        $out = '';
        foreach ($blocks as $block) {
            $out .= $this->renderBlock($block, $context);
        }
        return $out;
    }

    public function renderBlock(RgContentBlock $block, array $context = []): string
    {
        $p = $block->payload ?? [];
        return match ($block->block_type) {
            'heading' => $this->heading($p),
            'rich_text' => $this->richText($p),
            'image' => $this->image($p),
            'gallery' => $this->gallery($p),
            'video' => $this->video($p),
            'faq' => $this->faq($p),
            'cta' => $this->cta($p),
            'two_column' => $this->twoColumn($p),
            'listing_slot' => $this->listingSlot($p, $context),
            'quote' => $this->quote($p),
            'divider' => $this->divider($p),
            'custom_html' => $this->customHtml($p),
            // Custom Resort Guru elements.
            'hero_slider' => $this->heroSlider($p),
            'quick_facts' => $this->quickFacts($p),
            'editor_rating' => $this->editorRating($p),
            'listing_block' => $this->listingBlock($p, $context),
            'attractions' => $this->attractions($p),
            'how_to_get_to' => $this->howToGetTo($p),
            default => '',
        };
    }

    public function extractFaqs(string $ownerType, int $ownerId): array
    {
        $out = [];
        foreach (RgContentBlock::forOwner($ownerType, $ownerId) as $block) {
            if ($block->block_type !== 'faq') continue;
            foreach (($block->payload['items'] ?? []) as $item) {
                if (!empty($item['question'])) $out[] = $item;
            }
        }
        return $out;
    }

    public function plainText(string $ownerType, int $ownerId): string
    {
        $parts = [];
        foreach (RgContentBlock::forOwner($ownerType, $ownerId) as $b) {
            $p = $b->payload ?? [];
            $parts[] = match ($b->block_type) {
                'heading' => $p['text'] ?? '',
                'rich_text' => strip_tags($p['html'] ?? ''),
                'two_column' => strip_tags(($p['left_html'] ?? '') . ' ' . ($p['right_html'] ?? '')),
                'quote' => $p['text'] ?? '',
                'cta' => ($p['headline'] ?? '') . ' ' . ($p['text'] ?? ''),
                'faq' => implode(' ', array_map(fn($i) => ($i['question'] ?? '') . ' ' . ($i['answer'] ?? ''), $p['items'] ?? [])),
                default => '',
            };
        }
        return trim(implode(' ', $parts));
    }

    // --- Block renderers ---

    private function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function heading(array $p): string
    {
        $level = in_array($p['level'] ?? 'h2', ['h2', 'h3', 'h4'], true) ? $p['level'] : 'h2';
        $sizes = ['h2' => 'text-3xl md:text-4xl', 'h3' => 'text-2xl md:text-3xl', 'h4' => 'text-xl md:text-2xl'];
        $size = $sizes[$level] ?? 'text-2xl';
        return "<$level class=\"$size font-bold text-slate-900 mt-2 mb-3\">" . $this->e($p['text'] ?? '') . "</$level>";
    }

    private function richText(array $p): string
    {
        $html = $p['html'] ?? '';
        return '<div class="prose prose-slate max-w-none mb-5 text-slate-700 leading-relaxed">' . $html . '</div>';
    }

    private function image(array $p): string
    {
        if (empty($p['src'])) return '';
        $align = $p['align'] ?? 'center';
        $alignClass = ['left' => 'mr-auto', 'right' => 'ml-auto', 'center' => 'mx-auto'][$align] ?? 'mx-auto';
        $alt = $this->e($p['alt'] ?? '');
        $caption = !empty($p['caption']) ? '<figcaption class="text-sm text-slate-500 mt-2 text-center">' . $this->e($p['caption']) . '</figcaption>' : '';
        return '<figure class="my-6"><img src="' . $this->e($p['src']) . '" alt="' . $alt . '" class="rounded-lg max-w-full ' . $alignClass . '" loading="lazy">' . $caption . '</figure>';
    }

    private function gallery(array $p): string
    {
        $images = $p['images'] ?? [];
        if (!$images) return '';
        $cols = (int) ($p['columns'] ?? 3);
        $cols = min(max($cols, 2), 4);
        $gridClass = ['2' => 'grid-cols-2', '3' => 'grid-cols-2 md:grid-cols-3', '4' => 'grid-cols-2 md:grid-cols-4'][$cols] ?? 'grid-cols-3';
        $out = '<div class="grid ' . $gridClass . ' gap-3 my-6">';
        foreach ($images as $img) {
            if (empty($img['src'])) continue;
            $out .= '<img src="' . $this->e($img['src']) . '" alt="' . $this->e($img['alt'] ?? '') . '" class="rounded-lg w-full aspect-square object-cover" loading="lazy">';
        }
        $out .= '</div>';
        return $out;
    }

    private function video(array $p): string
    {
        if (!empty($p['youtube_id'])) {
            $yid = preg_replace('/[^a-zA-Z0-9_-]/', '', $p['youtube_id']);
            $caption = !empty($p['caption']) ? '<figcaption class="text-sm text-slate-500 mt-2 text-center">' . $this->e($p['caption']) . '</figcaption>' : '';
            return '<figure class="my-6"><div class="aspect-video"><iframe class="w-full h-full rounded-lg" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe></div>' . $caption . '</figure>';
        }
        if (!empty($p['src'])) {
            return '<figure class="my-6"><video controls class="w-full rounded-lg"><source src="' . $this->e($p['src']) . '"></video></figure>';
        }
        return '';
    }

    private function faq(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $headingText = $p['heading'] ?? 'Frequently Asked Questions';
        $out = '<div class="my-8 space-y-3">';
        $out .= '<h2 class="text-2xl font-bold text-slate-900 mb-4">' . $this->e($headingText) . '</h2>';
        foreach ($items as $item) {
            if (empty($item['question'])) continue;
            $out .= '<details class="bg-white border border-slate-200 rounded-lg p-4 group">';
            $out .= '<summary class="font-semibold text-slate-900 cursor-pointer flex items-center justify-between">' . $this->e($item['question']) . '<span class="text-slate-400 group-open:rotate-180 transition">▾</span></summary>';
            $out .= '<div class="mt-2 text-slate-600 leading-relaxed">' . nl2br($this->e($item['answer'] ?? '')) . '</div>';
            $out .= '</details>';
        }
        $out .= '</div>';
        return $out;
    }

    private function cta(array $p): string
    {
        $style = $p['style'] ?? 'primary';
        $btnClass = match ($style) {
            'secondary' => 'bg-slate-700 hover:bg-slate-800 text-white',
            'outline' => 'border-2 border-brand-600 text-brand-600 hover:bg-brand-50',
            default => 'bg-brand-600 hover:bg-brand-700 text-white',
        };
        return '<div class="my-8 p-8 rounded-2xl bg-gradient-to-br from-brand-50 to-amber-50 text-center">'
            . ($p['headline'] ? '<h3 class="text-2xl font-bold text-slate-900 mb-2">' . $this->e($p['headline']) . '</h3>' : '')
            . ($p['text'] ? '<p class="text-slate-700 mb-5">' . $this->e($p['text']) . '</p>' : '')
            . '<a href="' . $this->e($p['button_url'] ?? '#') . '" class="inline-block px-6 py-3 rounded-lg font-semibold ' . $btnClass . '">' . $this->e($p['button_text'] ?? 'Click') . '</a>'
            . '</div>';
    }

    private function twoColumn(array $p): string
    {
        return '<div class="grid md:grid-cols-2 gap-6 my-6">'
            . '<div class="prose prose-slate max-w-none">' . ($p['left_html'] ?? '') . '</div>'
            . '<div class="prose prose-slate max-w-none">' . ($p['right_html'] ?? '') . '</div>'
            . '</div>';
    }

    private function listingSlot(array $p, array $context): string
    {
        // Wrap the entire output in sentinel comments so the keyword-page
        // view can strip this block when listings are rendered above the
        // hero slider via the new row-based layout. Keeps backwards-compat
        // for any other view that still wants inline listing_slot output.
        $sentinelStart = '<!--LISTING_SLOT_START-->';
        $sentinelEnd = '<!--LISTING_SLOT_END-->';

        $keywordId = $context['keyword_id'] ?? null;
        if (!$keywordId) return $sentinelStart . ($p['fallback_html'] ?? '') . $sentinelEnd;
        $listings = RgListing::where('keyword_id', $keywordId)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->with('resort')
            ->limit(10)
            ->get();

        if ($listings->isEmpty()) {
            return $sentinelStart . '<section class="my-8">' . ($p['fallback_html'] ?? '') . '</section>' . $sentinelEnd;
        }

        $label = $this->e($p['slot_label'] ?? 'Featured Properties');
        $out = $sentinelStart . '<section class="my-8">';
        $out .= '<h2 class="text-2xl font-bold text-slate-900 mb-5">' . $label . '</h2>';
        $out .= '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">';
        foreach ($listings as $listing) {
            $resort = $listing->resort;
            if (!$resort) continue;
            $img = $resort->hero_path ? '/storage/' . ltrim($resort->hero_path, '/') : 'https://placehold.co/400x250?text=' . urlencode($resort->name);
            $out .= '<a href="/listing/' . $this->e($resort->slug) . '" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-slate-200">';
            $out .= '<img src="' . $this->e($img) . '" alt="' . $this->e($resort->name) . '" class="w-full h-44 object-cover" loading="lazy">';
            $out .= '<div class="p-4"><h3 class="font-bold text-slate-900 mb-1">' . $this->e($resort->name) . '</h3>';
            if ($resort->tagline) $out .= '<p class="text-sm text-slate-600 mb-2 line-clamp-2">' . $this->e($resort->tagline) . '</p>';
            $loc = trim(($resort->city ?? '') . ($resort->province ? ', ' . $resort->province : ''));
            if ($loc) $out .= '<p class="text-xs text-slate-500">📍 ' . $this->e($loc) . '</p>';
            $out .= '</div></a>';
        }
        $out .= '</div></section>' . $sentinelEnd;
        return $out;
    }

    private function quote(array $p): string
    {
        return '<blockquote class="my-6 border-l-4 border-brand-500 bg-brand-50 pl-5 py-4 italic text-slate-700">'
            . '<p class="mb-1">"' . $this->e($p['text'] ?? '') . '"</p>'
            . ($p['author'] ? '<footer class="text-sm text-slate-500 not-italic">— ' . $this->e($p['author']) . '</footer>' : '')
            . '</blockquote>';
    }

    private function divider(array $p): string
    {
        $style = $p['style'] ?? 'line';
        return match ($style) {
            'dots' => '<div class="text-center my-8 text-slate-300 text-2xl tracking-[1em]">• • •</div>',
            'thick' => '<hr class="my-8 border-t-4 border-slate-200">',
            default => '<hr class="my-8 border-t border-slate-200">',
        };
    }

    private function customHtml(array $p): string
    {
        return '<div class="my-6">' . ($p['html'] ?? '') . '</div>';
    }

    // ============================================================
    // Custom Resort Guru elements. Each one mirrors the markup a
    // food / keyword-page seeder ships today so admin-authored blocks
    // render identically to the seeded ones.
    // ============================================================

    /**
     * Splide hero carousel. Multi-image slider with optional caption per
     * slide. Splide JS/CSS is already loaded site-wide by public.blade.php.
     */
    private function heroSlider(array $p): string
    {
        $images = array_values(array_filter(
            $p['images'] ?? [],
            fn($i) => !empty($i['src'])
        ));
        if (!$images) return '';

        $height = $p['height'] ?? 'md';
        $heightRatio = ['sm' => '0.40', 'md' => '0.55', 'lg' => '0.70'][$height] ?? '0.55';
        $autoplay = !empty($p['autoplay']) ? 'true' : 'false';
        $interval = (int) ($p['interval'] ?? 6500);
        $sliderId = 'rg-hero-' . substr(md5(json_encode($images)), 0, 8);

        $slides = '';
        foreach ($images as $img) {
            $caption = !empty($img['caption'])
                ? '<div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-white text-sm font-medium">'
                    . $this->e($img['caption']) . '</div>'
                : '';
            $slides .= '<li class="splide__slide">'
                . '<div class="relative w-full" style="aspect-ratio: 16/9;">'
                . '<img src="' . $this->e($img['src']) . '" alt="' . $this->e($img['alt'] ?? '')
                . '" class="w-full h-full object-cover rounded-xl" loading="lazy">'
                . $caption
                . '</div></li>';
        }

        return '<section class="not-prose my-8 rg-hero-slider">'
            . '<div id="' . $sliderId . '" class="splide" aria-label="Hero slider"'
            . ' data-splide-config="{&quot;type&quot;:&quot;loop&quot;,&quot;autoplay&quot;:' . $autoplay
            . ',&quot;interval&quot;:' . $interval . ',&quot;arrows&quot;:true,&quot;pagination&quot;:true,'
            . '&quot;heightRatio&quot;:' . $heightRatio . ',&quot;cover&quot;:true}">'
            . '<div class="splide__track"><ul class="splide__list">' . $slides . '</ul></div>'
            . '</div>'
            . '<script>(function(){if(!window.Splide)return;'
            . 'var el=document.getElementById(' . json_encode($sliderId) . ');'
            . 'if(!el||el.dataset.splideMounted)return;el.dataset.splideMounted=1;'
            . 'try{new Splide(el,JSON.parse(el.dataset.splideConfig.replace(/&quot;/g,String.fromCharCode(34)))).mount();}catch(e){}'
            . '})();</script>'
            . '</section>';
    }

    /**
     * 4-card quick-facts strip. Each card has an icon, big headline, small
     * label, and a one-line detail. Matches the food-page strip exactly
     * (Per person / Easiest window / Avoid / Traffic).
     */
    private function quickFacts(array $p): string
    {
        $cards = $p['cards'] ?? [];
        if (!$cards) return '';

        $palettes = [
            'blue'    => ['bg' => '#eff6ff', 'br' => '#bfdbfe', 'fg' => '#1d4ed8', 'sm' => '#1e3a8a'],
            'emerald' => ['bg' => '#ecfdf5', 'br' => '#a7f3d0', 'fg' => '#047857', 'sm' => '#064e3b'],
            'rose'    => ['bg' => '#fff1f2', 'br' => '#fecdd3', 'fg' => '#be123c', 'sm' => '#881337'],
            'amber'   => ['bg' => '#fffbeb', 'br' => '#fcd34d', 'fg' => '#b45309', 'sm' => '#78350f'],
            'violet'  => ['bg' => '#f5f3ff', 'br' => '#ddd6fe', 'fg' => '#6d28d9', 'sm' => '#4c1d95'],
            'slate'   => ['bg' => '#f8fafc', 'br' => '#cbd5e1', 'fg' => '#334155', 'sm' => '#0f172a'],
        ];

        $count = count($cards);
        $gridCols = match (true) {
            $count >= 4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
            $count === 3 => 'grid-cols-1 md:grid-cols-3',
            $count === 2 => 'grid-cols-1 md:grid-cols-2',
            default => 'grid-cols-1',
        };

        $out = '';
        if (!empty($p['heading'])) {
            $out .= '<h2 class="text-2xl font-bold text-slate-900 mt-8 mb-3">'
                . $this->e($p['heading']) . '</h2>';
        }
        $out .= '<div class="not-prose my-8 grid ' . $gridCols . ' gap-3">';
        foreach ($cards as $card) {
            $pal = $palettes[$card['color'] ?? 'blue'] ?? $palettes['blue'];
            $svg = $this->quickFactIconSvg($card['icon'] ?? 'info');
            $out .= '<div class="rounded-lg p-4 text-center"'
                . ' style="background:' . $pal['bg'] . ';border:1px solid ' . $pal['br'] . '">'
                . '<div class="flex justify-center mb-2" style="color:' . $pal['fg'] . '">' . $svg . '</div>'
                . '<div class="text-2xl font-bold" style="color:' . $pal['fg'] . '">'
                . $this->e($card['big'] ?? '') . '</div>'
                . '<div class="text-[10px] uppercase tracking-wide font-bold" style="color:' . $pal['sm'] . '">'
                . $this->e($card['label'] ?? '') . '</div>'
                . '<div class="text-xs text-slate-600 mt-1">' . $this->e($card['detail'] ?? '') . '</div>'
                . '</div>';
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Icon set for the quick_facts strip. Adding a new icon: add an `if`
     * branch here and add the slug to the builder's icon select.
     */
    private function quickFactIconSvg(string $icon): string
    {
        $base = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">';
        return $base . match ($icon) {
            'money'    => '<circle cx="12" cy="12" r="9"/><path d="M9 8.5h4.5a2.25 2.25 0 0 1 0 4.5H9m0-4.5v8m0-3.5h5"/>',
            'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
            'warning'  => '<path d="M12 9v4m0 3.5h.01M3.86 17.74 10.4 4.95a1.8 1.8 0 0 1 3.2 0l6.54 12.79c.66 1.3-.27 2.86-1.6 2.86H5.46c-1.33 0-2.26-1.56-1.6-2.86Z"/>',
            'traffic'  => '<rect x="9" y="3" width="6" height="18" rx="2"/><circle cx="12" cy="7.5" r="1.2"/><circle cx="12" cy="12" r="1.2"/><circle cx="12" cy="16.5" r="1.2"/>',
            'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/>',
            'location' => '<circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5-8 12-8 12S4 15 4 10a8 8 0 0 1 8-8z"/>',
            'food'     => '<path d="M8 3v18M16 3v18M3 10h6M15 6h6M5 14h4M16 14h4"/>',
            'star'     => '<path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/>',
            default    => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5m0 3h.01"/>',
        } . '</svg>';
    }

    /**
     * Resort Guru Editor Rating card. Big overall star score on the left,
     * per-criterion breakdown on the right, optional summary blurb below.
     */
    private function editorRating(array $p): string
    {
        $overall = (float) ($p['overall'] ?? 4.0);
        $overall = max(0, min(5, $overall));
        $criteria = $p['criteria'] ?? [];
        $title = $p['title'] ?? 'Resort Guru Editor Rating';
        $summary = $p['summary'] ?? '';

        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $fillPct = max(0, min(1, $overall - ($i - 1))) * 100;
            $stars .= '<span class="relative inline-block w-7 h-7" aria-hidden="true">'
                . '<svg viewBox="0 0 24 24" class="absolute inset-0 w-full h-full" fill="#e2e8f0"><path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/></svg>'
                . '<span class="absolute inset-0 overflow-hidden" style="width:' . $fillPct . '%">'
                . '<svg viewBox="0 0 24 24" class="absolute inset-0 w-7 h-7" fill="#f59e0b"><path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/></svg>'
                . '</span></span>';
        }

        $criteriaHtml = '';
        foreach ($criteria as $c) {
            $score = (float) ($c['score'] ?? 0);
            $pct = max(0, min(100, ($score / 5) * 100));
            $criteriaHtml .= '<div class="flex items-center gap-3 text-sm">'
                . '<div class="w-24 font-semibold text-slate-700">' . $this->e($c['name'] ?? '') . '</div>'
                . '<div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">'
                . '<div class="h-full" style="width:' . $pct . '%;background:#f59e0b"></div></div>'
                . '<div class="w-10 text-right font-bold text-slate-700 tabular-nums">'
                . number_format($score, 1) . '</div></div>';
        }

        $summaryHtml = $summary !== ''
            ? '<p class="text-sm text-slate-600 mt-5 leading-relaxed border-t border-slate-100 pt-4">'
                . $this->e($summary) . '</p>'
            : '';

        return '<div class="not-prose my-8 p-6 rounded-2xl bg-white border border-slate-200">'
            . '<div class="flex items-start justify-between gap-4 mb-5 flex-wrap">'
            . '<div class="min-w-0">'
            . '<div class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500 mb-1">'
            . $this->e($title) . '</div>'
            . '<div class="flex items-center gap-2"><div class="flex">' . $stars . '</div>'
            . '<div class="text-3xl font-extrabold text-slate-900 tabular-nums">'
            . number_format($overall, 1) . '</div>'
            . '<div class="text-sm text-slate-500 self-end pb-1">/ 5.0</div></div></div></div>'
            . ($criteriaHtml ? '<div class="space-y-2 mt-2">' . $criteriaHtml . '</div>' : '')
            . $summaryHtml
            . '</div>';
    }

    /**
     * Full "Restaurants/Resorts We Recommend" listing band. Defers to the
     * existing partials.listings-rows so it stays in sync with the rest of
     * the site if that partial changes. Context must carry $keyword for
     * category-aware wording (food vs resort).
     */
    private function listingBlock(array $p, array $context): string
    {
        $keyword = $context['keyword'] ?? null;
        if (!$keyword) {
            return '<!-- listing_block: missing keyword in context -->';
        }

        $isFood = ($keyword->category ?? 'resort') === 'food';
        $listings = $isFood
            ? ($context['restaurantListings'] ?? collect())
            : ($context['listings'] ?? collect());

        $view = view('partials.listings-rows', [
            'listings' => $listings,
            'listingGalleries' => $context['listingGalleries'] ?? [],
            'keyword' => $keyword,
            'area' => $p['area'] ?? ($context['areaForCta'] ?? null),
        ]);

        // Sentinel-wrap so the public keyword-page view can strip this block
        // when the explicit listings band is already shown above the content
        // body. Without the wrapper the same listings render twice.
        return '<!--LISTING_BLOCK_START-->' . (string) $view->render() . '<!--LISTING_BLOCK_END-->';
    }

    /**
     * "What's in X (beyond the food)" attraction-card grid. Each item has
     * name, image, short tagline, longer blurb, and an outbound URL with
     * rel="nofollow noopener" per Resort Guru Rule 11.
     */
    private function attractions(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $p['heading'] ?? "What's beyond the food";
        $intro = $p['intro'] ?? '';

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $this->e($heading) . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-slate-600 mb-6 leading-relaxed">' . $this->e($intro) . '</p>';
        }
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            if (empty($item['name'])) continue;
            $img = !empty($item['image'])
                ? '<div class="aspect-[16/10] overflow-hidden bg-slate-100">'
                    . '<img src="' . $this->e($item['image']) . '" alt="' . $this->e($item['name'])
                    . '" class="w-full h-full object-cover" loading="lazy"></div>'
                : '';
            $url = $item['url'] ?? '';
            $link = $url
                ? '<a href="' . $this->e($url) . '" rel="noopener nofollow" target="_blank" '
                    . 'class="text-sm font-semibold text-brand-600 hover:text-brand-700 mt-3 inline-flex items-center gap-1">'
                    . 'Learn more <span aria-hidden="true">→</span></a>'
                : '';
            $out .= '<div class="rounded-xl border border-slate-200 bg-white overflow-hidden">'
                . $img
                . '<div class="p-4">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold text-emerald-700 mb-1">'
                . $this->e($item['short'] ?? '') . '</div>'
                . '<h3 class="font-bold text-slate-900 mb-1">' . $this->e($item['name']) . '</h3>'
                . '<p class="text-sm text-slate-600 leading-relaxed">' . $this->e($item['blurb'] ?? '') . '</p>'
                . $link
                . '</div></div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * "How to get to X" transport-method grid. Each card has a title,
     * detail, and one of a fixed set of icons (jeepney / bus / car etc.).
     */
    private function howToGetTo(array $p): string
    {
        $methods = $p['methods'] ?? [];
        if (!$methods) return '';
        $heading = $p['heading'] ?? 'How to get there';
        $intro = $p['intro'] ?? '';

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $this->e($heading) . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-slate-600 mb-6 leading-relaxed">' . $this->e($intro) . '</p>';
        }
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';
        foreach ($methods as $m) {
            if (empty($m['title'])) continue;
            $svg = $this->transportIconSvg($m['icon'] ?? 'car');
            $out .= '<div class="rounded-xl border border-slate-200 bg-white p-4">'
                . '<div class="flex items-center gap-2 mb-2 text-slate-700">' . $svg
                . '<div class="text-[10px] uppercase tracking-wide font-bold text-slate-500">'
                . $this->e($m['title']) . '</div></div>'
                . '<p class="text-sm text-slate-700 m-0 leading-relaxed">' . $this->e($m['detail'] ?? '') . '</p>'
                . '</div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    private function transportIconSvg(string $icon): string
    {
        $base = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">';
        return $base . match ($icon) {
            'bus'      => '<rect x="4" y="4" width="16" height="13" rx="2"/><circle cx="8" cy="19" r="1.5"/><circle cx="16" cy="19" r="1.5"/><path d="M4 11h16M8 8h8"/>',
            'plane'    => '<path d="M21 12l-9 5-9-5 9-9z"/><path d="M12 7v10"/>',
            'car'      => '<path d="M3 17h2m14 0h2M5 17V9l1.5-3.5h11L19 9v8M5 17h14M7 13h2m6 0h2"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>',
            'jeepney'  => '<path d="M3 16h18M4 16V9l3-3h10l3 3v7"/><circle cx="7" cy="18" r="1.5"/><circle cx="17" cy="18" r="1.5"/><path d="M7 12h10"/>',
            'tricycle' => '<circle cx="6" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M6 17l3-9h6l2 9M9 8l-1-3"/>',
            'train'    => '<rect x="5" y="3" width="14" height="14" rx="2"/><path d="M5 11h14M8 7h8"/><circle cx="9" cy="14" r="1"/><circle cx="15" cy="14" r="1"/><path d="M7 21l3-4M14 17l3 4"/>',
            'boat'     => '<path d="M3 14l9-9 9 9-3 6H6z"/><path d="M3 18c2 1 4 1 6 0s4-1 6 0 4 1 6 0"/>',
            default    => '<path d="M3 17h2m14 0h2M5 17V9l1.5-3.5h11L19 9v8"/>',
        } . '</svg>';
    }
}
