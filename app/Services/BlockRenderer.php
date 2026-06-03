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
            'text_section' => $this->textSection($p),
            // New standardized templates covering the section patterns
            // that previously lived as raw custom_html on seeded pages.
            'short_version' => $this->shortVersion($p),
            'pros_cons' => $this->prosCons($p),
            'summary_accordion' => $this->summaryAccordion($p),
            'image_text_pair' => $this->imageTextPair($p),
            'traveler_reviews' => $this->travelerReviews($p),
            'map_embed' => $this->mapEmbed($p),
            'local_tip' => $this->localTip($p),
            'related_guides' => $this->relatedGuides($p),
            'data_table' => $this->dataTable($p),
            'section_header' => $this->sectionHeader($p),
            'tag_pills' => $this->tagPills($p),
            'external_guides' => $this->externalGuides($p),
            'author' => $this->author($p),
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
     * Splide hero carousel. Supports two visual styles:
     *   - card (default): the food-page rg-area-hero pattern with an
     *     eyebrow title row + figure/figcaption captions per slide.
     *   - fullbleed: the resort-page rg-hero-splide pattern with a
     *     dark gradient overlay + caption stacked at the bottom.
     * Splide JS/CSS is already loaded site-wide by public.blade.php.
     */
    private function heroSlider(array $p): string
    {
        $images = array_values(array_filter(
            $p['images'] ?? [],
            fn($i) => !empty($i['src'])
        ));
        if (!$images) return '';

        $style = ($p['style'] ?? 'card') === 'fullbleed' ? 'fullbleed' : 'card';
        return $style === 'fullbleed'
            ? $this->heroSliderFullbleed($p, $images)
            : $this->heroSliderCard($p, $images);
    }

    /**
     * Card-style hero (rg-area-hero). Used by the food-page seeder.
     * Eyebrow title + subtitle above the carousel, figcaption per slide
     * with optional credit link.
     */
    private function heroSliderCard(array $p, array $images): string
    {
        $eyebrowTitle = $this->e($p['eyebrow_title'] ?? '');
        $eyebrowSubtitle = $this->e($p['eyebrow_subtitle'] ?? '');
        $autoplay = !empty($p['autoplay']) ? 'true' : 'false';
        $interval = (int) ($p['interval'] ?? 6500);
        $sliderId = 'rg-area-hero-' . substr(md5(json_encode($images)), 0, 8);

        $eyebrowHtml = '';
        if ($eyebrowTitle !== '' || $eyebrowSubtitle !== '') {
            $eyebrowHtml = '<div class="flex items-baseline justify-between mb-3">';
            $eyebrowHtml .= $eyebrowTitle !== ''
                ? '<h2 class="text-xs uppercase tracking-[0.18em] font-bold text-brand-700 m-0">' . $eyebrowTitle . '</h2>'
                : '<span></span>';
            if ($eyebrowSubtitle !== '') {
                $eyebrowHtml .= '<span class="text-xs text-slate-500">' . $eyebrowSubtitle . '</span>';
            }
            $eyebrowHtml .= '</div>';
        }

        $slides = '';
        foreach ($images as $img) {
            $captionTitle = trim($img['caption_title'] ?? '');
            $caption = trim($img['caption'] ?? '');
            $creditUrl = trim($img['credit_url'] ?? '');

            $captionHtml = '';
            if ($captionTitle !== '' || $caption !== '') {
                $captionHtml = '<figcaption>';
                if ($captionTitle !== '') {
                    $captionHtml .= '<strong>' . $this->e($captionTitle) . '</strong>';
                }
                if ($caption !== '') {
                    if ($creditUrl !== '') {
                        $captionHtml .= '<small><a href="' . $this->e($creditUrl)
                            . '" rel="nofollow noopener" target="_blank" style="color:#fbbf24;text-decoration:underline">'
                            . $this->e($caption) . '</a></small>';
                    } else {
                        $captionHtml .= '<span>' . $this->e($caption) . '</span>';
                    }
                }
                $captionHtml .= '</figcaption>';
            }

            $slides .= '<li class="splide__slide"><figure class="rg-area-hero__slide">'
                . '<img src="' . $this->e($this->normalizeMediaUrl($img['src'])) . '" alt="' . $this->e($img['alt'] ?? '')
                . '" loading="lazy">' . $captionHtml . '</figure></li>';
        }

        return '<section class="rg-area-hero my-8 not-prose" aria-label="Photo gallery">'
            . $eyebrowHtml
            . '<div id="' . $sliderId . '" class="rg-area-hero__splide splide"'
            . ' data-splide-config="{&quot;type&quot;:&quot;loop&quot;,&quot;autoplay&quot;:' . $autoplay
            . ',&quot;interval&quot;:' . $interval . ',&quot;arrows&quot;:true,&quot;pagination&quot;:true}">'
            . '<div class="splide__track"><ul class="splide__list">' . $slides . '</ul></div>'
            . '</div>'
            . $this->splideAutoMount($sliderId)
            . '</section>';
    }

    /**
     * Fullbleed hero (rg-hero-splide). Used by the resort-page seeder.
     * Each slide is a darkened image with caption stack at the bottom.
     */
    private function heroSliderFullbleed(array $p, array $images): string
    {
        $autoplay = !empty($p['autoplay']) ? 'true' : 'false';
        $interval = (int) ($p['interval'] ?? 6500);
        $sliderId = 'rg-hero-' . substr(md5(json_encode($images)), 0, 8);

        $slides = '';
        foreach ($images as $img) {
            $captionTitle = $this->e($img['caption_title'] ?? '');
            $caption = $this->e($img['caption'] ?? '');
            $captionBlock = '';
            if ($captionTitle !== '' || $caption !== '') {
                $captionBlock = '<div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8 text-white">'
                    . ($captionTitle !== '' ? '<div class="text-lg sm:text-2xl font-bold leading-tight mb-1">' . $captionTitle . '</div>' : '')
                    . ($caption !== '' ? '<div class="text-sm sm:text-base opacity-90">' . $caption . '</div>' : '')
                    . '</div>';
            }
            $slides .= '<li class="splide__slide">'
                . '<div class="relative w-full h-full bg-slate-900">'
                . '<img src="' . $this->e($this->normalizeMediaUrl($img['src'])) . '" alt="' . $this->e($img['alt'] ?? '')
                . '" class="absolute inset-0 w-full h-full object-cover" loading="lazy">'
                . '<div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/5 to-transparent"></div>'
                . $captionBlock
                . '</div></li>';
        }

        return '<section id="' . $sliderId . '" class="rg-hero-splide splide not-prose my-6 overflow-hidden rounded-xl" aria-label="Hero gallery"'
            . ' data-splide-config="{&quot;type&quot;:&quot;loop&quot;,&quot;autoplay&quot;:' . $autoplay
            . ',&quot;interval&quot;:' . $interval . ',&quot;arrows&quot;:true,&quot;pagination&quot;:true,&quot;heightRatio&quot;:0.55,&quot;cover&quot;:true}">'
            . '<div class="splide__track"><ul class="splide__list">' . $slides . '</ul></div>'
            . $this->splideAutoMount($sliderId)
            . '</section>';
    }

    /**
     * Strip any protocol+host that points at /storage so image URLs
     * resolve relative to the current host. Absolute URLs stamped at
     * seed time (e.g. http://resortguruph.test/storage/...) otherwise
     * 404 when the page is browsed from a different domain (Valet vs
     * php artisan serve on localhost, prod-staging differences, etc.).
     */
    private function normalizeMediaUrl(string $url): string
    {
        return preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $url) ?: $url;
    }

    /**
     * Auto-mount script shared by both hero styles. The Splide CDN script
     * is `defer`-loaded by layouts/public.blade.php, so this inline
     * script may execute BEFORE window.Splide is defined. We wait for
     * DOMContentLoaded (after which deferred scripts have executed) and
     * fall back to polling if Splide still isn't there for any reason.
     */
    private function splideAutoMount(string $sliderId): string
    {
        $id = json_encode($sliderId);
        return '<script>(function(){'
            . 'function mount(){'
              . 'var el=document.getElementById(' . $id . ');'
              . 'if(!el||el.dataset.splideMounted)return;'
              . 'if(!window.Splide){return setTimeout(mount,50);}'
              . 'el.dataset.splideMounted=1;'
              . 'try{new Splide(el,JSON.parse(el.dataset.splideConfig.replace(/&quot;/g,String.fromCharCode(34)))).mount();}catch(e){console.error("Splide mount failed",e);}'
            . '}'
            . 'if(document.readyState==="loading"){'
              . 'document.addEventListener("DOMContentLoaded",mount);'
            . '}else{mount();}'
            . '})();</script>';
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

        // Per-card hero is now a stack of fading images. Each item supports
        // either a single `image` string (legacy) or an `images[]` array.
        // When multiple images exist, the renderer emits a relative-
        // positioned <div> with absolutely-stacked <img>s that fade
        // through one another using CSS keyframes — animation-delay
        // staggers each layer by (cycleSeconds / count). Clicking any
        // image fires the rg-attr-lightbox handler emitted below.
        $perImageSeconds = 3.5;

        $out = '<section class="not-prose my-10" data-rg-attractions>';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $this->e($heading) . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-slate-600 mb-6 leading-relaxed">' . $this->e($intro) . '</p>';
        }
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';

        $cycleStyles = []; // collected per-card CSS for the fade animation
        foreach ($items as $cardIndex => $item) {
            if (empty($item['name'])) continue;
            $name = $this->e($item['name']);

            // Resolve the image set: prefer the new images[] array, fall
            // back to the legacy single image. Strings are accepted but
            // arrays-of-strings are the canonical shape.
            $images = [];
            if (!empty($item['images']) && is_array($item['images'])) {
                foreach ($item['images'] as $im) {
                    if (is_string($im) && trim($im) !== '') $images[] = trim($im);
                    elseif (is_array($im) && !empty($im['src'])) $images[] = trim($im['src']);
                }
            }
            if (!$images && !empty($item['image'])) $images[] = (string) $item['image'];
            $images = array_values(array_unique($images));

            // Build the hero — empty container if no images, single still
            // if only one image, or the fading stack when 2+ images.
            if (!$images) {
                $hero = '';
            } else if (count($images) === 1) {
                $src = $this->e($this->normalizeMediaUrl($images[0]));
                $hero = '<button type="button" class="block w-full aspect-[16/10] overflow-hidden bg-slate-100 cursor-zoom-in"'
                    . ' data-rg-lightbox="' . $src . '" data-rg-lightbox-caption="' . $name . '" aria-label="Open ' . $name . ' image">'
                    . '<img src="' . $src . '" alt="' . $name . '" class="w-full h-full object-cover" loading="lazy">'
                    . '</button>';
            } else {
                $count = count($images);
                $cycle = number_format($perImageSeconds * $count, 2, '.', '');
                // Per-card phase offset (seconds) so the 6 cards on a
                // section don't all hit their crossfade at the same
                // moment. Without this, the whole section visibly blinks
                // every {$perImageSeconds} seconds. 1.3s gives a clean
                // spread across the cycle for up to ~8 cards before the
                // offset starts wrapping back near zero.
                $cardPhaseSec = 1.3;
                $phaseOffsetSec = $cardIndex * $cardPhaseSec;
                $layers = '';
                foreach ($images as $i => $src) {
                    $safeSrc = $this->e($this->normalizeMediaUrl($src));
                    // Negative animation-delay = animation appears to
                    // have been running for that many seconds already.
                    // animation-fill-mode: both (on the CSS rule below)
                    // makes positive-delay images render the 0% keyframe
                    // state (opacity 0) during the wait, so we no longer
                    // need the legacy opacity-{100,0} initial classes.
                    $delay = number_format($perImageSeconds * $i - $phaseOffsetSec, 2, '.', '');
                    $layers .= '<img src="' . $safeSrc . '" alt="' . $name . ($i === 0 ? '' : ' (' . ($i + 1) . ')') . '"'
                        . ' class="rg-attr-fade absolute inset-0 w-full h-full object-cover"'
                        . ' data-rg-fade-index="' . $i . '"'
                        . ' style="animation-delay:' . $delay . 's"'
                        . ' loading="lazy">';
                }
                $cardKey = $cardIndex;
                $cycleStyles[] = ".rg-attr-card-{$cardKey} .rg-attr-fade{animation:rgAttrFade {$cycle}s ease-in-out infinite;animation-fill-mode:both}";
                $imageJson = htmlspecialchars(json_encode(array_map(fn($s) => $this->normalizeMediaUrl($s), $images)), ENT_QUOTES, 'UTF-8');
                $hero = '<button type="button" class="block w-full aspect-[16/10] overflow-hidden bg-slate-100 cursor-zoom-in relative rg-attr-card-' . $cardKey . '"'
                    . ' data-rg-lightbox-group="' . $imageJson . '" data-rg-lightbox-caption="' . $name . '" aria-label="Open gallery for ' . $name . '">'
                    . $layers
                    // Tiny pip indicator showing how many images are in the stack.
                    . '<div class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/55 text-white text-[10px] font-semibold pointer-events-none">'
                    . '<span class="inline-flex items-center gap-1">'
                    . '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 14l6-6 7 7M14 11l3-3 4 4"/></svg>'
                    . $count . '</span></div>'
                    . '</button>';
            }

            $url = $item['url'] ?? '';
            $link = $url
                ? '<a href="' . $this->e($url) . '" rel="noopener nofollow" target="_blank" '
                    . 'class="text-sm font-semibold text-brand-600 hover:text-brand-700 mt-3 inline-flex items-center gap-1">'
                    . 'Learn more <span aria-hidden="true">→</span></a>'
                : '';
            $out .= '<div class="rounded-xl border border-slate-200 bg-white overflow-hidden flex flex-col">'
                . $hero
                . '<div class="p-4 flex-1 flex flex-col">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold text-emerald-700 mb-1">'
                . $this->e($item['short'] ?? '') . '</div>'
                . '<h3 class="font-bold text-slate-900 mb-1">' . $name . '</h3>'
                . '<p class="text-sm text-slate-600 leading-relaxed">' . $this->e($item['blurb'] ?? '') . '</p>'
                . $link
                . '</div></div>';
        }
        $out .= '</div>';

        // CSS keyframes — single shared keyframe, per-card duration set
        // via class above. 12% / 28% pin opacity:1 (visible window),
        // 33% / 95% drop back to 0 (next layer takes over). With N=3
        // images at 3.5s each (10.5s cycle), each layer sits visible for
        // ~1.7s and fades for ~0.8s. Tweak $perImageSeconds to taste.
        $out .= '<style>'
            . '@keyframes rgAttrFade{0%{opacity:0}5%{opacity:1}30%{opacity:1}38%{opacity:0}100%{opacity:0}}'
            . '.rg-attr-fade{transition:opacity .4s ease-in-out}'
            . implode('', $cycleStyles)
            // Lightbox styles.
            . '.rg-attr-lightbox{position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.92);display:none;align-items:center;justify-content:center;padding:1.5rem}'
            . '.rg-attr-lightbox.is-open{display:flex}'
            . '.rg-attr-lightbox__inner{position:relative;max-width:96vw;max-height:90vh}'
            . '.rg-attr-lightbox__img{max-width:96vw;max-height:80vh;display:block;border-radius:8px;box-shadow:0 25px 50px rgba(0,0,0,.5)}'
            . '.rg-attr-lightbox__caption{margin-top:.75rem;color:#e2e8f0;font-size:14px;font-weight:600;text-align:center}'
            . '.rg-attr-lightbox__close{position:absolute;top:-2.4rem;right:0;background:transparent;color:white;border:0;font-size:28px;line-height:1;cursor:pointer;padding:.25rem .6rem}'
            . '.rg-attr-lightbox__nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.1);color:white;border:0;width:44px;height:44px;border-radius:50%;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center}'
            . '.rg-attr-lightbox__nav:hover{background:rgba(255,255,255,.2)}'
            . '.rg-attr-lightbox__nav--prev{left:-3.5rem}'
            . '.rg-attr-lightbox__nav--next{right:-3.5rem}'
            . '@media (max-width:640px){.rg-attr-lightbox__nav--prev{left:.5rem}.rg-attr-lightbox__nav--next{right:.5rem}}'
            . '</style>';

        // Single global lightbox + JS. Idempotent — only the first
        // attractions block on the page injects the lightbox markup;
        // subsequent blocks just attach handlers to the shared overlay.
        $out .= '<div class="rg-attr-lightbox" data-rg-lightbox-overlay role="dialog" aria-modal="true" aria-label="Image gallery">'
            . '<div class="rg-attr-lightbox__inner">'
            . '<button type="button" class="rg-attr-lightbox__close" data-rg-lightbox-close aria-label="Close">&times;</button>'
            . '<button type="button" class="rg-attr-lightbox__nav rg-attr-lightbox__nav--prev" data-rg-lightbox-prev aria-label="Previous image">&larr;</button>'
            . '<button type="button" class="rg-attr-lightbox__nav rg-attr-lightbox__nav--next" data-rg-lightbox-next aria-label="Next image">&rarr;</button>'
            . '<img class="rg-attr-lightbox__img" data-rg-lightbox-img src="" alt="">'
            . '<div class="rg-attr-lightbox__caption" data-rg-lightbox-cap></div>'
            . '</div></div>';

        $out .= '<script>(function(){'
            . 'if(window.__rgAttrLightboxWired)return;window.__rgAttrLightboxWired=true;'
            . 'function init(){'
              . 'var overlay=document.querySelector("[data-rg-lightbox-overlay]");'
              . 'if(!overlay)return;'
              . 'var imgEl=overlay.querySelector("[data-rg-lightbox-img]");'
              . 'var capEl=overlay.querySelector("[data-rg-lightbox-cap]");'
              . 'var closeBtn=overlay.querySelector("[data-rg-lightbox-close]");'
              . 'var prevBtn=overlay.querySelector("[data-rg-lightbox-prev]");'
              . 'var nextBtn=overlay.querySelector("[data-rg-lightbox-next]");'
              . 'var state={list:[],i:0,caption:""};'
              . 'function render(){imgEl.src=state.list[state.i]||"";capEl.textContent=state.caption+(state.list.length>1?" ("+(state.i+1)+"/"+state.list.length+")":"");var multi=state.list.length>1;prevBtn.style.display=multi?"flex":"none";nextBtn.style.display=multi?"flex":"none"}'
              . 'function open(list,caption,i){state.list=list||[];state.caption=caption||"";state.i=Math.max(0,Math.min(i||0,state.list.length-1));render();overlay.classList.add("is-open");document.body.style.overflow="hidden"}'
              . 'function close(){overlay.classList.remove("is-open");document.body.style.overflow=""}'
              . 'function step(d){if(!state.list.length)return;state.i=(state.i+d+state.list.length)%state.list.length;render()}'
              . 'document.addEventListener("click",function(e){'
                . 'var single=e.target.closest("[data-rg-lightbox]");'
                . 'var group=e.target.closest("[data-rg-lightbox-group]");'
                . 'var t=single||group;'
                . 'if(!t)return;'
                . 'e.preventDefault();'
                . 'if(group){try{var imgs=JSON.parse(group.getAttribute("data-rg-lightbox-group"));open(imgs,group.getAttribute("data-rg-lightbox-caption")||"",0)}catch(err){}}'
                . 'else{open([single.getAttribute("data-rg-lightbox")],single.getAttribute("data-rg-lightbox-caption")||"",0)}'
              . '});'
              . 'closeBtn.addEventListener("click",close);'
              . 'prevBtn.addEventListener("click",function(){step(-1)});'
              . 'nextBtn.addEventListener("click",function(){step(1)});'
              . 'overlay.addEventListener("click",function(e){if(e.target===overlay)close()});'
              . 'document.addEventListener("keydown",function(e){if(!overlay.classList.contains("is-open"))return;if(e.key==="Escape")close();else if(e.key==="ArrowLeft")step(-1);else if(e.key==="ArrowRight")step(1)});'
            . '}'
            . 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}'
            . '})();</script>';

        $out .= '</section>';
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
        $footer = trim((string) ($p['footer'] ?? ''));

        // Per-method icon color palette. Matches the gradient swatch shown
        // on the destination "How to get to La Union" card so the food /
        // restaurant pages render with the same visual rhythm.
        $iconPalettes = [
            'amber'   => ['from' => '#fef3c7', 'to' => '#fffbeb'],
            'blue'    => ['from' => '#dbeafe', 'to' => '#eff6ff'],
            'emerald' => ['from' => '#d1fae5', 'to' => '#ecfdf5'],
            'rose'    => ['from' => '#ffe4e6', 'to' => '#fff1f2'],
            'violet'  => ['from' => '#ede9fe', 'to' => '#f5f3ff'],
            'slate'   => ['from' => '#e2e8f0', 'to' => '#f8fafc'],
        ];
        // Default colour rotation when admin didn't pick one explicitly so
        // each row looks distinct without needing a palette decision.
        $cycle = ['amber', 'blue', 'emerald', 'rose', 'violet', 'slate'];

        $out = '<section class="not-prose mt-6 mb-8 rounded-xl border border-slate-200 bg-white overflow-hidden">';

        // Card header — H2 + intro paragraph, capped at max-w-3xl so the
        // copy reads like a magazine lede instead of stretching edge-to-edge.
        $out .= '<div class="p-5 sm:p-6 pb-3 border-b border-slate-100">';
        $out .= '<h2 class="text-xl font-bold text-slate-900 mb-3">' . $this->e($heading) . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-sm text-slate-700 leading-relaxed max-w-3xl">' . $this->e($intro) . '</p>';
        }
        $out .= '</div>';

        // Method rows — collapsed by default using <details>. Click the
        // summary row to expand the operator list + detail paragraph. The
        // summary always shows: icon square + uppercase title + bold
        // subtitle + chevron. Body shows: operator list (when present) +
        // detail paragraph. Default-open the first method so the panel
        // reads "active" on first paint.
        $out .= '<div class="divide-y divide-slate-100">';
        $i = 0;
        foreach ($methods as $m) {
            if (empty($m['title'])) continue;
            $color = $m['color'] ?? $cycle[$i % count($cycle)];
            $pal = $iconPalettes[$color] ?? $iconPalettes['amber'];
            $icon = $m['icon'] ?? 'car';
            $emoji = $this->transportEmoji($icon);
            $title = $this->e($m['title']);
            $subtitle = $this->e(trim((string) ($m['subtitle'] ?? '')));
            $detail = trim((string) ($m['detail'] ?? ''));
            $operators = is_array($m['operators'] ?? null) ? array_values(array_filter(
                $m['operators'],
                fn($op) => !empty($op['name'])
            )) : [];

            // Subtitle defaults to operator count when not given. Falls
            // back to a plain "Recommended route" line if no operators
            // either — keeps the bolded second line from looking empty.
            if ($subtitle === '') {
                if ($operators) {
                    $count = count($operators);
                    $subtitle = $count === 1 ? '1 option worth booking' : ($count . ' options worth booking');
                } else {
                    $subtitle = 'Recommended route';
                }
            }

            $bodyHtml = '';
            if ($operators) {
                $bodyHtml .= '<ul class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden">';
                foreach ($operators as $op) {
                    $opName = $this->e($op['name']);
                    $opNote = $this->e($op['note'] ?? '');
                    $opUrl = trim((string) ($op['url'] ?? ''));
                    $imgPath = trim((string) ($op['image'] ?? ''));
                    $thumb = $imgPath !== ''
                        ? '<div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 shrink-0">'
                            . '<img src="' . $this->e($this->normalizeMediaUrl($imgPath)) . '" alt="' . $opName . '" class="w-full h-full object-cover" loading="lazy">'
                            . '</div>'
                        : '<div class="w-16 h-16 rounded-lg flex items-center justify-center text-2xl shrink-0"'
                            . ' style="background:linear-gradient(135deg,' . $pal['from'] . ' 0%,' . $pal['to'] . ' 100%)">'
                            . $emoji . '</div>';
                    $arrow = '<svg class="inline-block w-3 h-3 opacity-50 ml-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14"/></svg>';

                    $inner = $thumb
                        . '<div class="flex-1 min-w-0">'
                        . '<div class="font-semibold text-slate-900' . ($opUrl !== '' ? ' hover:underline' : '') . '">'
                        . $opName . ($opUrl !== '' ? $arrow : '') . '</div>'
                        . ($opNote !== '' ? '<p class="text-sm text-slate-600 leading-snug mt-1 m-0">' . $opNote . '</p>' : '')
                        . '</div>';

                    if ($opUrl !== '') {
                        $bodyHtml .= '<li class="bg-white"><a href="' . $this->e($opUrl) . '" target="_blank" rel="noopener nofollow" class="flex items-start gap-4 p-4 hover:bg-slate-50 transition">' . $inner . '</a></li>';
                    } else {
                        $bodyHtml .= '<li class="bg-white"><div class="flex items-start gap-4 p-4">' . $inner . '</div></li>';
                    }
                }
                $bodyHtml .= '</ul>';
                if ($detail !== '') {
                    $bodyHtml .= '<p class="text-sm text-slate-600 leading-relaxed mt-3 m-0">' . $this->e($detail) . '</p>';
                }
            } else if ($detail !== '') {
                $bodyHtml = '<p class="text-sm text-slate-700 leading-relaxed m-0">' . $this->e($detail) . '</p>';
            }

            // <details> handles the expand/collapse natively with no JS
            // dependency. summary::-webkit-details-marker hidden in the
            // public layout. Chevron rotates via group-open: utility so it
            // gives a visual affordance for the expandable state. All
            // methods start collapsed so the section reads as a compact
            // menu on first paint.
            $out .= '<details class="group p-5 sm:p-6">';
            $out .= '<summary class="rg-htgt-summary cursor-pointer list-none flex items-center gap-3 select-none">'
                . '<div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0"'
                . ' style="background:linear-gradient(135deg,' . $pal['from'] . ' 0%,' . $pal['to'] . ' 100%)">'
                . $emoji . '</div>'
                . '<div class="flex-1 min-w-0">'
                . '<div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">' . $title . '</div>'
                . '<div class="font-bold text-slate-900 truncate">' . $subtitle . '</div>'
                . '</div>'
                . '<svg class="w-5 h-5 text-slate-400 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>'
                . '</summary>';
            if ($bodyHtml !== '') {
                $out .= '<div class="mt-4">' . $bodyHtml . '</div>';
            }
            $out .= '</details>';
            $i++;
        }
        $out .= '</div>';

        // Inline stylesheet for the <details> summary — Tailwind doesn't
        // have a built-in for hiding the WebKit/Firefox default disclosure
        // triangle, so we knock it out here. Scoped via the wrapper class
        // so it doesn't leak into other <details> on the page.
        $out .= '<style>'
            . '.rg-htgt-summary::-webkit-details-marker{display:none}'
            . '.rg-htgt-summary::marker{display:none;content:""}'
            . '</style>';

        // Closing footer block — slate-tinted band with a closing prose
        // paragraph. Renders only when admin authored a footer.
        if ($footer !== '') {
            $out .= '<div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-100">'
                . '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $this->e($footer) . '</p>'
                . '</div>';
        }

        $out .= '</section>';
        return $out;
    }

    /**
     * Emoji glyph for the gradient icon square in howToGetTo. Mirrors
     * transportIconSvg's keys so admins can keep using the same `icon`
     * field while the renderer picks the right pictograph.
     */
    private function transportEmoji(string $icon): string
    {
        return match ($icon) {
            'bus'      => '🚌',
            'plane'    => '✈️',
            'car'      => '🚗',
            'jeepney'  => '🚐',
            'tricycle' => '🛺',
            'ferry'    => '⛴️',
            'train'    => '🚆',
            'walk'     => '🚶',
            'bike'     => '🚲',
            default    => '🧭',
        };
    }

    /**
     * Heading + ordered list of paragraphs. Authored via the structured
     * builder (heading text + add/remove paragraph cards), rendered as
     * <h2>/<h3> + chained <p> elements. Paragraph bodies are HTML so the
     * admin can wrap selections in <strong> / <em> / <a> if needed, but
     * the editor itself stays list-based (no Quill).
     */
    private function textSection(array $p): string
    {
        $heading = trim($p['heading'] ?? '');
        $level = in_array($p['heading_level'] ?? 'h2', ['h2', 'h3', 'h4'], true)
            ? $p['heading_level']
            : 'h2';
        $anchor = trim($p['anchor'] ?? '');
        $paragraphs = array_values(array_filter(
            (array) ($p['paragraphs'] ?? []),
            fn($x) => trim((string) $x) !== ''
        ));

        if ($heading === '' && !$paragraphs) return '';

        $sizes = [
            'h2' => 'text-3xl md:text-4xl mt-8 mb-3',
            'h3' => 'text-2xl md:text-3xl mt-6 mb-2',
            'h4' => 'text-xl md:text-2xl mt-5 mb-2',
        ];
        $sizeClass = $sizes[$level] ?? $sizes['h2'];

        $headingHtml = '';
        if ($heading !== '') {
            $idAttr = $anchor !== ''
                ? ' id="' . $this->e(preg_replace('~[^a-z0-9-]+~i', '-', $anchor)) . '"'
                : '';
            $headingHtml = "<{$level}{$idAttr} class=\"{$sizeClass} font-bold text-slate-900\">"
                . $this->e($heading) . "</{$level}>";
        }

        $bodyHtml = '';
        foreach ($paragraphs as $para) {
            $inner = trim((string) $para);
            if ($inner === '') continue;
            // Items that are already block-level (figure/blockquote/list)
            // render raw. Plain text gets a fresh <p> wrapper. The outer <p>
            // is stripped if the admin pre-wrapped to avoid double-nest.
            if (preg_match('~^<(figure|blockquote|ul|ol|table|div)\b~i', $inner)) {
                $bodyHtml .= $inner;
            } else {
                if (preg_match('~^<p[^>]*>(.*?)</p>$~is', $inner, $m)) {
                    $inner = $m[1];
                }
                $bodyHtml .= '<p>' . $inner . '</p>';
            }
        }

        // Heading sits OUTSIDE the prose wrapper so its custom sizing
        // (text-3xl / text-2xl etc.) doesn't get overridden by Tailwind
        // Typography. The body sits INSIDE so paragraphs, lists, blockquotes,
        // and inline links inherit prose's auto-styling — same behaviour
        // the legacy rich_text block had.
        return '<section class="my-6">' . $headingHtml
            . '<div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">' . $bodyHtml . '</div>'
            . '</section>';
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

    // ════════════════════════════════════════════════════════════════
    // Standardized section templates — each one renders a layout that
    // previously lived as raw custom_html on seeded pages. Now any
    // admin can author the same pattern through a structured editor.
    // ════════════════════════════════════════════════════════════════

    /**
     * "The short version" — dark gradient summary card with eyebrow
     * + one-line statement. Matches the food-page seeder's TLDR card.
     */
    private function shortVersion(array $p): string
    {
        $eyebrow = $this->e($p['eyebrow'] ?? 'The short version');
        $body = trim($p['body'] ?? '');
        if ($body === '') return '';
        $accent = $p['accent_color'] ?? 'amber';
        $palettes = [
            'amber'  => ['bg' => 'linear-gradient(135deg,#0f172a 0%,#1e293b 100%)', 'fg' => '#f1f5f9', 'eyebrow' => '#fbbf24'],
            'emerald'=> ['bg' => 'linear-gradient(135deg,#064e3b 0%,#065f46 100%)', 'fg' => '#ecfdf5', 'eyebrow' => '#6ee7b7'],
            'rose'   => ['bg' => 'linear-gradient(135deg,#4c0519 0%,#881337 100%)', 'fg' => '#fff1f2', 'eyebrow' => '#fda4af'],
            'blue'   => ['bg' => 'linear-gradient(135deg,#1e3a8a 0%,#1e40af 100%)', 'fg' => '#eff6ff', 'eyebrow' => '#93c5fd'],
        ];
        $pal = $palettes[$accent] ?? $palettes['amber'];
        return '<div class="not-prose my-8 p-6 rounded-2xl" style="background:' . $pal['bg'] . ';color:' . $pal['fg'] . '">'
            . '<div class="text-[10px] uppercase tracking-[0.2em] font-bold mb-3" style="color:' . $pal['eyebrow'] . '">' . $eyebrow . '</div>'
            . '<p class="text-base leading-relaxed m-0">' . $this->e($body) . '</p>'
            . '</div>';
    }

    /**
     * "Best for / Skip if" two-column comparison list. Renders the
     * familiar emerald + rose chevron-bullet pattern from the seeders.
     */
    private function prosCons(array $p): string
    {
        $prosLabel = $this->e($p['pros_label'] ?? 'Best for');
        $consLabel = $this->e($p['cons_label'] ?? 'Skip if');
        $pros = array_values(array_filter((array) ($p['pros'] ?? []), fn($x) => trim((string) $x) !== ''));
        $cons = array_values(array_filter((array) ($p['cons'] ?? []), fn($x) => trim((string) $x) !== ''));
        if (!$pros && !$cons) return '';

        $renderColumn = function (string $label, array $items, string $bg, string $border, string $eyebrowColor, string $bulletColor, string $bulletChar): string {
            $lis = '';
            foreach ($items as $item) {
                $lis .= '<li class="flex items-start gap-2 text-sm text-slate-700">'
                    . '<span class="flex-none mt-1" style="color:' . $bulletColor . '">' . $bulletChar . '</span>'
                    . '<span>' . $this->e($item) . '</span></li>';
            }
            return '<div class="p-5 rounded-2xl" style="background:' . $bg . ';border:1px solid ' . $border . '">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold mb-3" style="color:' . $eyebrowColor . '">' . $label . '</div>'
                . '<ul class="space-y-2 m-0 p-0 list-none">' . $lis . '</ul></div>';
        };

        $left = $renderColumn($prosLabel, $pros, '#ecfdf5', '#a7f3d0', '#047857', '#10b981', '▸');
        $right = $renderColumn($consLabel, $cons, '#fef2f2', '#fecaca', '#b91c1c', '#f43f5e', '▸');

        return '<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 gap-4">' . $left . $right . '</div>';
    }

    /**
     * Expandable summary accordion ("At a glance" + "The short version"
     * pattern from the resort pages). Native <details>/<summary> so it
     * works without JS.
     */
    private function summaryAccordion(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $out = '<div class="not-prose my-8 space-y-2">';
        foreach ($items as $i => $item) {
            if (empty($item['title']) && empty($item['body'])) continue;
            $eyebrow = $this->e($item['eyebrow'] ?? '');
            $title = $this->e($item['title'] ?? '');
            $body = $item['body'] ?? '';
            $open = !empty($item['open']) || $i === 0 ? ' open' : '';
            $out .= '<details' . $open . ' class="bg-white border border-slate-200 rounded-xl p-5 group">'
                . '<summary class="flex items-center justify-between cursor-pointer list-none">'
                . '<div>'
                . ($eyebrow !== '' ? '<div class="text-[10px] uppercase tracking-wide font-bold text-amber-700 mb-1">' . $eyebrow . '</div>' : '')
                . '<div class="text-lg md:text-xl font-bold text-slate-900">' . $title . '</div>'
                . '</div>'
                . '<span class="text-slate-400 group-open:rotate-180 transition text-xl" aria-hidden="true">▾</span>'
                . '</summary>'
                . '<div class="mt-4 text-slate-700 leading-relaxed">' . $body . '</div>'
                . '</details>';
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Left/right image + text pair (tourist spot rows). Toggleable image
     * position (left|right) so a series of these alternates cleanly.
     */
    private function imageTextPair(array $p): string
    {
        if (empty($p['image']) && empty($p['title']) && empty($p['body'])) return '';
        $position = ($p['image_position'] ?? 'left') === 'right' ? 'right' : 'left';
        $eyebrow = $this->e($p['eyebrow'] ?? '');
        $title = $this->e($p['title'] ?? '');
        $body = $p['body'] ?? '';
        $url = $p['url'] ?? '';
        $urlLabel = $this->e($p['url_label'] ?? 'Learn more');

        $img = !empty($p['image'])
            ? '<div class="rounded-xl overflow-hidden aspect-[4/3] bg-slate-100">'
                . '<img src="' . $this->e($p['image']) . '" alt="' . $title . '" class="w-full h-full object-cover" loading="lazy">'
                . '</div>'
            : '';

        $cta = $url !== ''
            ? '<a href="' . $this->e($url) . '" rel="noopener nofollow" target="_blank" '
                . 'class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-3">'
                . $urlLabel . ' <span aria-hidden="true">→</span></a>'
            : '';

        $text = '<div>'
            . ($eyebrow !== '' ? '<div class="text-[10px] uppercase tracking-wide font-bold text-emerald-700 mb-1">' . $eyebrow . '</div>' : '')
            . ($title !== '' ? '<h3 class="text-xl md:text-2xl font-bold text-slate-900 mb-2">' . $title . '</h3>' : '')
            . ($body !== '' ? '<div class="text-slate-700 leading-relaxed">' . $body . '</div>' : '')
            . $cta
            . '</div>';

        $cols = $position === 'right' ? $text . $img : $img . $text;
        return '<div class="not-prose my-8 grid md:grid-cols-2 gap-6 items-start">' . $cols . '</div>';
    }

    /**
     * "What travelers are saying" — static testimonial grid. Each item:
     * name + avatar + rating + quote + optional date.
     */
    private function travelerReviews(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'What travelers are saying');
        $intro = $this->e($p['intro'] ?? '');

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        if ($intro !== '') $out .= '<p class="text-slate-600 mb-6">' . $intro . '</p>';
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            if (empty($item['quote'])) continue;
            $name = $this->e($item['name'] ?? 'Anonymous traveler');
            $date = $this->e($item['date'] ?? '');
            $rating = max(0, min(5, (int) ($item['rating'] ?? 5)));
            $avatar = !empty($item['avatar'])
                ? '<img src="' . $this->e($item['avatar']) . '" alt="' . $name . '" class="w-10 h-10 rounded-full object-cover" loading="lazy">'
                : '<div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">' . $this->e(mb_substr($name, 0, 1)) . '</div>';
            $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
            $out .= '<div class="p-5 rounded-xl border border-slate-200 bg-white">'
                . '<div class="flex items-center gap-3 mb-3">' . $avatar
                . '<div><div class="font-bold text-slate-900 text-sm">' . $name . '</div>'
                . ($date !== '' ? '<div class="text-xs text-slate-500">' . $date . '</div>' : '')
                . '</div></div>'
                . '<div class="text-amber-500 mb-2 text-sm" aria-label="' . $rating . ' of 5 stars">' . $stars . '</div>'
                . '<p class="text-sm text-slate-700 leading-relaxed m-0">"' . $this->e($item['quote']) . '"</p>'
                . '</div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Google Maps embed (or any iframe URL — the field accepts the raw
     * embed URL admins copy out of Google's Share -> Embed dialog).
     */
    private function mapEmbed(array $p): string
    {
        $url = trim($p['embed_url'] ?? '');
        if ($url === '') return '';
        $heading = $this->e($p['heading'] ?? '');
        $height = max(200, min(800, (int) ($p['height'] ?? 450)));

        // Defensive: only allow embed URLs from Google Maps + OpenStreetMap to
        // avoid arbitrary iframe injection from a paste. Google supports two
        // embed shapes — /maps/embed?... (iframe-only host) and the plain
        // /maps?q=...&output=embed query — both are valid for <iframe src>.
        if (!preg_match('~^https://(?:www\.)?(?:google\.[a-z.]+/maps|maps\.google\.[a-z.]+|www\.openstreetmap\.org)~i', $url)) {
            return '<div class="not-prose my-8 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900">'
                . 'Map embed URL must come from Google Maps or OpenStreetMap.</div>';
        }

        $out = '<section class="not-prose my-8">';
        if ($heading !== '') {
            $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-3">' . $heading . '</h2>';
        }
        $out .= '<div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100">'
            . '<iframe src="' . $this->e($url) . '" width="100%" height="' . $height . '" style="border:0;display:block" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>'
            . '</div>';
        $out .= '</section>';
        return $out;
    }

    /**
     * Local-tip / pull-quote callout. Color picker chooses between two
     * visual styles:
     *   - dark: dark gradient with opening &ldquo; quote, italic body,
     *     amber footer eyebrow. Matches the seeded "Local tip from a
     *     Pasay regular" pull-quote pattern.
     *   - amber/emerald/blue/rose: light-bg aside with emoji icon,
     *     coloured eyebrow above plain body text.
     */
    private function localTip(array $p): string
    {
        $body = trim($p['body'] ?? '');
        if ($body === '') return '';
        $eyebrow = $this->e($p['eyebrow'] ?? 'Local tip');
        $color = $p['color'] ?? 'amber';

        if ($color === 'dark') {
            return '<div class="not-prose my-10 px-6 py-6 rounded-xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f1f5f9">'
                . '<div class="text-4xl leading-none mb-2" style="color:#fbbf24">&ldquo;</div>'
                . '<p class="text-lg md:text-xl italic leading-relaxed m-0">' . $this->e($body) . '</p>'
                . '<p class="text-xs uppercase tracking-wide mt-3 m-0" style="color:#fbbf24">' . $eyebrow . '</p>'
                . '</div>';
        }

        $palettes = [
            'amber'   => ['bg' => '#fef3c7', 'br' => '#f59e0b', 'eb' => '#92400e', 'ic' => '#b45309', 'icbg' => '#fde68a', 'emoji' => '💡'],
            'emerald' => ['bg' => '#ecfdf5', 'br' => '#10b981', 'eb' => '#065f46', 'ic' => '#047857', 'icbg' => '#a7f3d0', 'emoji' => '🌿'],
            'blue'    => ['bg' => '#eff6ff', 'br' => '#3b82f6', 'eb' => '#1e3a8a', 'ic' => '#1d4ed8', 'icbg' => '#bfdbfe', 'emoji' => '🧭'],
            'rose'    => ['bg' => '#fff1f2', 'br' => '#f43f5e', 'eb' => '#881337', 'ic' => '#be123c', 'icbg' => '#fecdd3', 'emoji' => '⚠️'],
        ];
        $pal = $palettes[$color] ?? $palettes['amber'];
        return '<aside class="not-prose my-8 p-5 rounded-2xl border-l-4" style="background:' . $pal['bg'] . ';border-color:' . $pal['br'] . '">'
            . '<div class="flex items-start gap-3">'
            . '<div class="flex-none w-10 h-10 rounded-full flex items-center justify-center text-xl" style="background:' . $pal['icbg'] . ';color:' . $pal['ic'] . '">' . $pal['emoji'] . '</div>'
            . '<div class="min-w-0">'
            . '<div class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color:' . $pal['eb'] . '">' . $eyebrow . '</div>'
            . '<p class="text-slate-700 leading-relaxed m-0">' . $this->e($body) . '</p>'
            . '</div></div></aside>';
    }

    /**
     * Pill-row of cuisine / category tags. Each pill has its own
     * background + text color chosen from a small palette so the row
     * stays visually varied. Matches the seeded
     * "Japanese · ramen · sushi" rows.
     */
    private function tagPills(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $label = $p['label'] ?? '';

        $palettes = [
            'amber'   => ['bg' => '#fef3c7', 'fg' => '#78350f'],
            'rose'    => ['bg' => '#fee2e2', 'fg' => '#7f1d1d'],
            'emerald' => ['bg' => '#dcfce7', 'fg' => '#14532d'],
            'indigo'  => ['bg' => '#e0e7ff', 'fg' => '#3730a3'],
            'pink'    => ['bg' => '#fce7f3', 'fg' => '#831843'],
            'cyan'    => ['bg' => '#cffafe', 'fg' => '#155e75'],
            'violet'  => ['bg' => '#ede9fe', 'fg' => '#5b21b6'],
            'slate'   => ['bg' => '#e2e8f0', 'fg' => '#1e293b'],
        ];
        $cycle = array_keys($palettes);

        $pillHtml = '';
        foreach ($items as $i => $item) {
            $text = is_array($item) ? ($item['text'] ?? '') : (string) $item;
            $color = is_array($item) ? ($item['color'] ?? '') : '';
            $pal = $palettes[$color] ?? $palettes[$cycle[$i % count($cycle)]];
            $pillHtml .= '<span class="px-3 py-1.5 rounded-full text-xs font-bold"'
                . ' style="background:' . $pal['bg'] . ';color:' . $pal['fg'] . '">'
                . $this->e($text) . '</span>';
        }
        $ariaLabel = $label !== '' ? ' aria-label="' . $this->e($label) . '"' : '';
        return '<div class="not-prose my-7 flex flex-wrap gap-2"' . $ariaLabel . '>' . $pillHtml . '</div>';
    }

    /**
     * "Compare picks on third-party guides" card grid. Each card has
     * a platform name + accent + outbound URL + optional logo /
     * screenshot uploads. When no upload is provided, a styled gradient
     * placeholder with the platform name fills the preview slot so the
     * card never renders blank.
     */
    private function externalGuides(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'Compare picks on third-party guides');
        $intro = trim((string) ($p['intro'] ?? ''));
        $footnote = $this->e($p['footnote'] ?? 'External links open in a new tab. We do not get paid for clicks.');

        // Sensible fallback intro so the section always reads as more
        // than just a label. Admin can override per page via the intro
        // field; the fallback is generic enough to fit any keyword page.
        if ($intro === '') {
            $intro = 'A quick scan of how this place is covered across the major travel sites. Each one has its own slant — reviews, maps, menus, deals — so the picks rarely overlap. Open one or two before you book to triangulate.';
        }

        // Clean text-only card layout. No brand-color tile or logo
        // square — just the platform name (in its brand colour) + the
        // host caption + a short blurb describing what the link gets you.
        // Reads like a curated bookmark list instead of a logo wall.
        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        $out .= '<p class="text-slate-600 mb-6 leading-relaxed max-w-3xl">' . $this->e($intro) . '</p>';
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">';

        foreach ($items as $item) {
            if (empty($item['name']) || empty($item['url'])) continue;
            $rawUrl = (string) $item['url'];
            $rawName = (string) $item['name'];
            $platform = $this->detectGuidePlatform($rawUrl, $rawName);

            $name = $this->e($rawName);
            $url = $this->e($rawUrl);
            $brandColor = $platform['brandColor'] ?? '#475569';
            $brandColorDark = $this->darkenHex($brandColor, 18);
            $host = $platform['host'] ?? (parse_url($rawUrl, PHP_URL_HOST) ?: '');
            $hostLabel = $this->e(preg_replace('~^www\.~i', '', strtolower($host)));

            // Per-item blurb — admin-provided if present, otherwise the
            // canned platform default so every card has descriptive text.
            $blurbRaw = trim((string) ($item['blurb'] ?? ''));
            if ($blurbRaw === '' && !empty($platform['defaultBlurb'])) {
                $blurbRaw = $platform['defaultBlurb'];
            }
            $blurb = $this->e($blurbRaw);

            $out .= '<a href="' . $url . '" rel="noopener nofollow" target="_blank"'
                . ' class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:shadow-md hover:-translate-y-px"'
                . ' style="border-left:4px solid ' . $brandColor . '">'
                . '<div class="flex items-start justify-between gap-3 mb-2">'
                . '<div class="min-w-0 flex-1">'
                . '<h3 class="font-bold text-lg leading-tight m-0" style="color:' . $brandColorDark . '">' . $name . '</h3>'
                . ($hostLabel !== '' ? '<div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mt-1">' . $hostLabel . '</div>' : '')
                . '</div>'
                . '<svg class="flex-none w-5 h-5 text-slate-400 mt-1 group-hover:translate-x-0.5 group-hover:text-slate-700 transition" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</div>'
                . ($blurb !== '' ? '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $blurb . '</p>' : '')
                . '</a>';
        }
        $out .= '</div>';
        if ($footnote !== '') {
            $out .= '<p class="text-xs text-slate-500 mt-3 m-0">' . $footnote . '</p>';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * Author / byline card. Renders at the bottom of an SEO page so the
     * editorial voice is attributed without competing for attention with
     * the listing band at the top. Supports avatar, name, role, bio,
     * outbound social links, and an optional "More from this author"
     * footer link.
     */
    private function author(array $p): string
    {
        $name = trim($p['name'] ?? '');
        $bio = trim($p['bio'] ?? '');
        if ($name === '' && $bio === '') return '';

        $role = $this->e($p['role'] ?? '');
        $avatar = trim($p['avatar'] ?? '');
        $eyebrow = $this->e($p['eyebrow'] ?? 'Written by');
        $links = (array) ($p['links'] ?? []);
        $moreUrl = trim((string) ($p['more_url'] ?? ''));
        $moreLabel = trim((string) ($p['more_label'] ?? ''));

        // Avatar tile — uploaded image, or initials on a slate gradient.
        $avatarHtml = '';
        if ($avatar !== '') {
            $avatarHtml = '<img src="' . $this->e($this->normalizeMediaUrl($avatar)) . '" alt="' . $this->e($name) . '"'
                . ' class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover shadow-md ring-4 ring-white" loading="lazy">';
        } else {
            $initials = '';
            foreach (preg_split('~\s+~', $name, -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
                if (mb_strlen($initials) >= 2) break;
            }
            if ($initials === '') $initials = 'RG';
            $avatarHtml = '<div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full flex items-center justify-center shadow-md ring-4 ring-white"'
                . ' style="background:linear-gradient(135deg,#0f172a 0%,#475569 100%)">'
                . '<span class="text-2xl font-extrabold text-white">' . $this->e($initials) . '</span>'
                . '</div>';
        }

        // Social link icons — each entry is { type, url } where type maps
        // to a recognised platform (facebook/instagram/twitter/x/tiktok/
        // youtube/linkedin/website/email). Unknown types fall back to a
        // generic globe icon so admin-added rows still render.
        $linksHtml = '';
        foreach ($links as $l) {
            if (empty($l['url'])) continue;
            $type = strtolower((string) ($l['type'] ?? 'website'));
            $svg = $this->authorSocialSvg($type);
            $label = ucfirst($type);
            $linksHtml .= '<a href="' . $this->e($l['url']) . '" rel="noopener nofollow" target="_blank"'
                . ' class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-slate-900 hover:border-slate-400 transition"'
                . ' aria-label="' . $this->e($label) . '">' . $svg . '</a>';
        }

        $out = '<section class="not-prose my-12">'
            . '<div class="rounded-2xl overflow-hidden border border-slate-200 bg-gradient-to-br from-slate-50 to-white">'

            // Header band — slate gradient strip with the eyebrow label.
            . '<div class="px-6 py-3 text-[10px] uppercase tracking-[0.15em] font-bold text-slate-100"'
            . ' style="background:linear-gradient(90deg,#0f172a 0%,#1e293b 100%)">'
            . $eyebrow
            . '</div>'

            // Body — avatar + name + role + bio + socials + more link.
            . '<div class="p-6 sm:p-8">'
            . '<div class="flex flex-col sm:flex-row sm:items-start gap-5">'
            . '<div class="flex-none">' . $avatarHtml . '</div>'
            . '<div class="flex-1 min-w-0">'
            . ($name !== '' ? '<h3 class="text-xl sm:text-2xl font-bold text-slate-900 m-0 leading-tight">' . $this->e($name) . '</h3>' : '')
            . ($role !== '' ? '<div class="text-sm text-slate-500 mt-1">' . $role . '</div>' : '')
            . ($bio !== '' ? '<p class="text-sm sm:text-base text-slate-700 leading-relaxed mt-3 m-0">' . $this->e($bio) . '</p>' : '')
            . ($linksHtml !== '' ? '<div class="flex flex-wrap items-center gap-2 mt-4">' . $linksHtml . '</div>' : '')
            . '</div></div>';

        if ($moreUrl !== '') {
            $label = $moreLabel !== '' ? $moreLabel : 'More from ' . ($name !== '' ? $name : 'this author');
            $external = !str_starts_with($moreUrl, '/');
            $rel = $external ? ' rel="noopener nofollow" target="_blank"' : '';
            $out .= '<div class="mt-5 pt-4 border-t border-slate-200">'
                . '<a href="' . $this->e($moreUrl) . '"' . $rel . ' class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-900 hover:text-emerald-700 transition">'
                . $this->e($label)
                . '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</a></div>';
        }

        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * Brand icon for the author block's social-link row. Returns a tight
     * inline SVG sized for the 36px round button. Unknown types fall back
     * to a globe so admin-added rows still render with a sensible glyph.
     */
    private function authorSocialSvg(string $type): string
    {
        $base = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">';
        $stroke = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
        return match ($type) {
            'facebook'  => $base . '<path d="M22 12a10 10 0 1 0-11.6 9.9V14.9H8v-2.9h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7 1 0 2.1.2 2.1.2v2.3h-1.2c-1.2 0-1.5.7-1.5 1.5V12h2.6l-.4 2.9h-2.2v6.9A10 10 0 0 0 22 12z"/></svg>',
            'instagram' => $stroke . '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>',
            'twitter', 'x' => $base . '<path d="M18.244 2H21.5l-7.5 8.57L23 22h-6.844l-5.36-7.01L4.7 22H1.44l8.04-9.19L1 2h7.014l4.847 6.41L18.244 2zm-1.198 18.273h1.83L7.045 3.61H5.082l11.964 16.663z"/></svg>',
            'tiktok'    => $base . '<path d="M19.6 6.4a4.5 4.5 0 0 1-3-2.4 4.5 4.5 0 0 1-.8-2H12v13.2a2.6 2.6 0 1 1-2.6-2.6c.3 0 .6 0 .8.1V9.4a6 6 0 1 0 5.2 5.9V8.7a7.8 7.8 0 0 0 4.4 1.4V6.7c-.2 0-.6-.1-.2-.3z"/></svg>',
            'youtube'   => $base . '<path d="M23.5 6.5a3 3 0 0 0-2.1-2.1C19.5 4 12 4 12 4s-7.5 0-9.4.4A3 3 0 0 0 .5 6.5 31.4 31.4 0 0 0 0 12a31.4 31.4 0 0 0 .5 5.5 3 3 0 0 0 2.1 2.1C4.5 20 12 20 12 20s7.5 0 9.4-.4a3 3 0 0 0 2.1-2.1A31.4 31.4 0 0 0 24 12a31.4 31.4 0 0 0-.5-5.5zM9.6 15.6V8.4l6.4 3.6-6.4 3.6z"/></svg>',
            'linkedin'  => $base . '<path d="M20.4 3H3.6A.6.6 0 0 0 3 3.6v16.8c0 .3.3.6.6.6h16.8c.3 0 .6-.3.6-.6V3.6a.6.6 0 0 0-.6-.6zM8.3 18.3H5.5V9.4h2.8v8.9zM6.9 8.2A1.6 1.6 0 1 1 8.5 6.6a1.6 1.6 0 0 1-1.6 1.6zm11.4 10.1h-2.8v-4.3c0-1 0-2.4-1.5-2.4s-1.7 1.1-1.7 2.3v4.4H9.5V9.4h2.7v1.2h.1a3 3 0 0 1 2.7-1.5c2.9 0 3.4 1.9 3.4 4.4v4.8z"/></svg>',
            'email'     => $stroke . '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
            default     => $stroke . '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a13 13 0 0 1 0 18M12 3a13 13 0 0 0 0 18"/></svg>',
        };
    }

    /**
     * Resolve a known third-party guide platform from its outbound URL
     * (and falling back to the display name) so the card can render with
     * brand colours, a wordmark, and a Google-favicon sneak-peek without
     * the admin having to upload assets per platform.
     *
     * Returns an associative array with:
     *   - host:        canonical host string used in the card caption
     *   - brandColor:  hex accent used on the wordmark + preview gradient
     *   - color:       palette key (matches $palettes) for the card border
     *   - wordmark:    HTML wordmark element shown in the bottom-left
     *   - faviconUrl:  Google s2-favicon URL for the preview tile (64px)
     * Returns an empty array when no platform matched.
     */
    private function detectGuidePlatform(string $url, string $name): array
    {
        $host = '';
        $parsed = parse_url($url);
        if (!empty($parsed['host'])) {
            $host = preg_replace('~^www\.~i', '', strtolower($parsed['host']));
        }
        $needle = strtolower($host . ' ' . $name);

        // Each entry: keys are substrings searched against host+name; brand
        // colour matches the platform's recognised primary; favicon falls
        // back to Google's generic favicon service so unknown TLDs (e.g.
        // .com.ph) still resolve.
        $platforms = [
            'tripadvisor' => [
                'host' => 'tripadvisor.com',
                'brandColor' => '#00aa6c',
                'color' => 'emerald',
                'wordmark' => 'Tripadvisor',
                'defaultBlurb' => 'Traveler reviews sorted by rating, with user-shot photos of the dining rooms and rooms you are about to book. Useful for cross-checking the marketing photos against what guests actually saw.',
            ],
            'booking' => [
                'host' => 'booking.com',
                'brandColor' => '#003580',
                'color' => 'blue',
                'wordmark' => 'Booking.com',
                'defaultBlurb' => 'Live availability and rate calendars with the largest hotel inventory in the Philippines. Filter by guest score to weed out the listings that look better than they read.',
            ],
            'agoda' => [
                'host' => 'agoda.com',
                'brandColor' => '#d72f60',
                'color' => 'rose',
                'wordmark' => 'agoda',
                'defaultBlurb' => 'Asia-leaning OTA that often surfaces local promos Booking misses. Pay attention to the cancellation-policy filter before locking in non-refundable rates.',
            ],
            'klook' => [
                'host' => 'klook.com',
                'brandColor' => '#ff5722',
                'color' => 'rose',
                'wordmark' => 'Klook',
                'defaultBlurb' => 'Activity and day-pass marketplace. Strong for transfers, ferry tickets, and tour bundles around the area when you want one booking instead of three.',
            ],
            'airbnb' => [
                'host' => 'airbnb.com',
                'brandColor' => '#ff385c',
                'color' => 'rose',
                'wordmark' => 'airbnb',
                'defaultBlurb' => 'Short-term rentals from condo units to private homes. Useful when you want a kitchen, more space, or a long-stay discount instead of a hotel room.',
            ],
            'zomato' => [
                'host' => 'zomato.com',
                'brandColor' => '#e23744',
                'color' => 'rose',
                'wordmark' => 'zomato',
                'defaultBlurb' => 'Restaurant database with menus, photo galleries, and opening hours. The cuisine filter is what you want for narrowing down by ramen vs grill vs Filipino.',
            ],
            'foursquare' => [
                'host' => 'foursquare.com',
                'brandColor' => '#f94877',
                'color' => 'rose',
                'wordmark' => 'Foursquare',
                'defaultBlurb' => 'Crowd-sourced tips from locals and frequent visitors, often more specific than long-form reviews. Skim for the one-line "best seat in the house" notes.',
            ],
            'yelp' => [
                'host' => 'yelp.com',
                'brandColor' => '#d32323',
                'color' => 'rose',
                'wordmark' => 'yelp',
                'defaultBlurb' => 'Western-leaning review platform with thinner Philippines coverage, but the listings it does have tend to have detailed write-ups worth reading.',
            ],
            'google.com/maps' => [
                'host' => 'maps.google.com',
                'brandColor' => '#1a73e8',
                'color' => 'blue',
                'wordmark' => 'Google Maps',
                'defaultBlurb' => 'Crowd-sourced reviews plus live traffic and crowd-busy indicators. The Popular Times graph is the best signal for when the place is quiet enough to walk in.',
            ],
            'maps.google' => [
                'host' => 'maps.google.com',
                'brandColor' => '#1a73e8',
                'color' => 'blue',
                'wordmark' => 'Google Maps',
                'defaultBlurb' => 'Crowd-sourced reviews plus live traffic and crowd-busy indicators. The Popular Times graph is the best signal for when the place is quiet enough to walk in.',
            ],
            'google maps' => [
                'host' => 'maps.google.com',
                'brandColor' => '#1a73e8',
                'color' => 'blue',
                'wordmark' => 'Google Maps',
                'defaultBlurb' => 'Crowd-sourced reviews plus live traffic and crowd-busy indicators. The Popular Times graph is the best signal for when the place is quiet enough to walk in.',
            ],
            'google' => [
                'host' => 'google.com',
                'brandColor' => '#4285f4',
                'color' => 'blue',
                'wordmark' => 'Google',
                'defaultBlurb' => 'Web-wide search results pulling in mentions from food blogs, Reddit threads, and local forums. Good for the off-the-beaten-path takes the big OTA sites miss.',
            ],
            'expedia' => [
                'host' => 'expedia.com',
                'brandColor' => '#fdc72f',
                'color' => 'amber',
                'wordmark' => 'Expedia',
                'defaultBlurb' => 'Bundled flight + hotel deals plus the Vrbo inventory under the hood. Better as a comparison tab than a primary booking site for Philippines stays.',
            ],
            'hotels.com' => [
                'host' => 'hotels.com',
                'brandColor' => '#d32f2f',
                'color' => 'rose',
                'wordmark' => 'Hotels.com',
                'defaultBlurb' => 'Stay-and-earn rewards programme. Useful if you book hotels often enough to bank a free night, otherwise the rates are usually the same as Expedia.',
            ],
            'lonelyplanet' => [
                'host' => 'lonelyplanet.com',
                'brandColor' => '#0089cf',
                'color' => 'blue',
                'wordmark' => 'Lonely Planet',
                'defaultBlurb' => 'Editorially curated long-form guides. Lighter on bookable inventory, heavier on context and what to do once you are there.',
            ],
            'wikitravel' => [
                'host' => 'wikitravel.org',
                'brandColor' => '#0066ff',
                'color' => 'blue',
                'wordmark' => 'Wikitravel',
                'defaultBlurb' => 'Volunteer-edited destination guides with the basic logistics — transit options, neighbourhoods, scams to avoid — written by frequent travellers.',
            ],
            'wikipedia' => [
                'host' => 'wikipedia.org',
                'brandColor' => '#36c',
                'color' => 'slate',
                'wordmark' => 'Wikipedia',
                'defaultBlurb' => 'Historical and geographic context for the area. Worth a 60-second skim before you go so the cultural references and place names land properly.',
            ],
            'reddit' => [
                'host' => 'reddit.com',
                'brandColor' => '#ff4500',
                'color' => 'rose',
                'wordmark' => 'Reddit',
                'defaultBlurb' => 'r/Philippines and r/Manila threads from people who actually live there. Search by venue name for the unvarnished current take on what is and is not worth it.',
            ],
            'facebook' => [
                'host' => 'facebook.com',
                'brandColor' => '#1877f2',
                'color' => 'blue',
                'wordmark' => 'Facebook',
                'defaultBlurb' => 'Official page with the latest announcements, holiday hours, and event posters. Filipino businesses post here more reliably than they update their websites.',
            ],
            'instagram' => [
                'host' => 'instagram.com',
                'brandColor' => '#e1306c',
                'color' => 'rose',
                'wordmark' => 'Instagram',
                'defaultBlurb' => 'Recent guest photos and the daily-special story posts. Filter by the location tag to see what the food and rooms actually look like right now, not in the brochure.',
            ],
            'youtube' => [
                'host' => 'youtube.com',
                'brandColor' => '#ff0000',
                'color' => 'rose',
                'wordmark' => 'YouTube',
                'defaultBlurb' => 'Walk-through videos from Filipino vloggers. The 10-minute room tour or food haul format gives you a better feel for the space than any photo gallery.',
            ],
        ];

        foreach ($platforms as $key => $meta) {
            if (str_contains($needle, $key)) {
                $faviconHost = $meta['host'];
                $meta['faviconUrl'] = 'https://www.google.com/s2/favicons?domain=' . $faviconHost . '&sz=64';
                return $meta;
            }
        }

        // Unknown platform — still hand back a usable favicon URL so the
        // preview tile doesn't fall back to a plain wordmark block when
        // the host is anything resolvable.
        if ($host !== '') {
            return [
                'host' => $host,
                'brandColor' => '#475569',
                'color' => 'slate',
                'wordmark' => '',
                'faviconUrl' => 'https://www.google.com/s2/favicons?domain=' . $host . '&sz=64',
            ];
        }
        return [];
    }

    /**
     * Darken a hex colour by a percentage (0-100). Used to build the
     * 2-stop preview gradient in externalGuides cards so the brand-colour
     * tile gets a subtle depth instead of looking flat.
     */
    private function darkenHex(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) return '#' . $hex;
        $r = max(0, hexdec(substr($hex, 0, 2)) - (int) (255 * $percent / 100));
        $g = max(0, hexdec(substr($hex, 2, 2)) - (int) (255 * $percent / 100));
        $b = max(0, hexdec(substr($hex, 4, 2)) - (int) (255 * $percent / 100));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Related-guides cross-link card grid. Each item links to another
     * keyword page or external guide.
     */
    private function relatedGuides(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'Related guides');

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4">' . $heading . '</h2>';
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            if (empty($item['title']) || empty($item['url'])) continue;
            $eyebrow = $this->e($item['eyebrow'] ?? '');
            $title = $this->e($item['title']);
            $blurb = $this->e($item['blurb'] ?? '');
            $external = !str_starts_with($item['url'], '/');
            $rel = $external ? ' rel="noopener nofollow" target="_blank"' : '';
            $out .= '<a href="' . $this->e($item['url']) . '"' . $rel . ' class="block rounded-xl border border-slate-200 bg-white p-5 hover:shadow-md hover:border-emerald-300 transition">'
                . ($eyebrow !== '' ? '<div class="text-[10px] uppercase tracking-wide font-bold text-emerald-700 mb-1">' . $eyebrow . '</div>' : '')
                . '<h3 class="font-bold text-slate-900 mb-1">' . $title . '</h3>'
                . ($blurb !== '' ? '<p class="text-sm text-slate-600 m-0">' . $blurb . '</p>' : '')
                . '</a>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Section header: H2 + small subtitle lede. Replaces the hardcoded
     * "What's in [Area]?" template block on keyword pages so the section
     * opener becomes editable in the builder like every other element.
     */
    private function sectionHeader(array $p): string
    {
        $heading = $this->e(trim($p['heading'] ?? ''));
        $subtitle = $this->e(trim($p['subtitle'] ?? ''));
        $anchor = trim($p['anchor'] ?? '');
        if ($heading === '' && $subtitle === '') return '';

        $idAttr = $anchor !== ''
            ? ' id="' . $this->e(preg_replace('~[^a-z0-9-]+~i', '-', $anchor)) . '"'
            : '';

        $out = '<section class="not-prose my-8 mt-10 mb-2"' . $idAttr . '>';
        if ($heading !== '') {
            $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">'
                . $heading . '</h2>';
        }
        if ($subtitle !== '') {
            $out .= '<p class="text-sm text-slate-500 mt-1">' . $subtitle . '</p>';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * Generic data table. Headers + rows. Caller manages column ordering.
     * Mobile: horizontal scroll. NO price columns — Rule 3 means peso
     * mentions get caught by the audit even here.
     */
    private function dataTable(array $p): string
    {
        $headers = (array) ($p['headers'] ?? []);
        $rows = (array) ($p['rows'] ?? []);
        if (!$headers || !$rows) return '';
        $caption = $this->e($p['caption'] ?? '');

        $out = '<div class="not-prose my-8">';
        if ($caption !== '') {
            $out .= '<div class="text-[10px] uppercase tracking-wide font-bold text-slate-500 mb-2">' . $caption . '</div>';
        }
        $out .= '<div class="overflow-x-auto rounded-xl border border-slate-200">'
            . '<table class="w-full border-collapse text-sm"><thead><tr style="background:#0f172a;color:#fff">';
        foreach ($headers as $h) {
            $out .= '<th class="px-4 py-3 text-left font-bold">' . $this->e($h) . '</th>';
        }
        $out .= '</tr></thead><tbody style="background:#fff">';
        $alt = false;
        foreach ($rows as $row) {
            $bg = $alt ? 'background:#f8fafc;' : '';
            $out .= '<tr style="border-top:1px solid #e2e8f0;' . $bg . '">';
            foreach ($headers as $i => $_) {
                $cell = $row[$i] ?? '';
                $weight = $i === 0 ? 'font-bold text-slate-800' : 'text-slate-700';
                $out .= '<td class="px-4 py-3 ' . $weight . '">' . $this->e((string) $cell) . '</td>';
            }
            $out .= '</tr>';
            $alt = !$alt;
        }
        $out .= '</tbody></table></div></div>';
        return $out;
    }
}
