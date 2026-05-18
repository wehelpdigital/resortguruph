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
        return "<$level class=\"$size font-bold text-slate-900 mt-10 mb-5\">" . $this->e($p['text'] ?? '') . "</$level>";
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
        $out = '<div class="my-8 space-y-3">';
        $out .= '<h2 class="text-2xl font-bold text-slate-900 mb-4">Frequently Asked Questions</h2>';
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
        $keywordId = $context['keyword_id'] ?? null;
        if (!$keywordId) return $p['fallback_html'] ?? '';
        $listings = RgListing::where('keyword_id', $keywordId)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->with('resort')
            ->limit(10)
            ->get();

        if ($listings->isEmpty()) {
            return '<section class="my-8">' . ($p['fallback_html'] ?? '') . '</section>';
        }

        $label = $this->e($p['slot_label'] ?? 'Featured Properties');
        $out = '<section class="my-10">';
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
        $out .= '</div></section>';
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
}
