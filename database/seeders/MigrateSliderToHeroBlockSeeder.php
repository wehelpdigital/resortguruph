<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Converts custom_html blocks containing the seeded Splide carousel
 * markup (food-page rg-area-hero OR resort-page rg-hero-splide) into
 * structured hero_slider blocks. After migration the public page
 * renders via the canonical heroSlider() renderer, and the builder
 * shows it as a hero_slider element (slides list with image picker
 * per slide + eyebrow / caption / credit fields), not a raw Quill blob.
 *
 * Idempotent: blocks already typed hero_slider are skipped.
 */
class MigrateSliderToHeroBlockSeeder extends Seeder
{
    public function run(): void
    {
        $rows = DB::table('rg_content_blocks')
            ->where('block_type', 'custom_html')
            ->where(function ($q) {
                $q->where('payload_json', 'like', '%rg-area-hero%')
                  ->orWhere('payload_json', 'like', '%rg-hero-splide%');
            })
            ->select('id', 'payload_json')
            ->orderBy('id')
            ->get();

        $now = Carbon::now()->toDateTimeString();
        $scanned = 0;
        $migrated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $scanned++;
            $payload = json_decode($row->payload_json, true);
            $html = is_array($payload) ? ($payload['html'] ?? '') : '';
            $parsed = $this->tryParse($html);
            if ($parsed === null) {
                $skipped++;
                continue;
            }

            DB::table('rg_content_blocks')
                ->where('id', $row->id)
                ->update([
                    'block_type'   => 'hero_slider',
                    'payload_json' => json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at'   => $now,
                ]);
            $migrated++;
            if ($migrated % 100 === 0) {
                $this->command->info("  migrated {$migrated}...");
            }
        }

        $this->command->info("Done. Scanned: {$scanned} | Migrated: {$migrated} | Skipped: {$skipped}");
    }

    private function tryParse(string $html): ?array
    {
        if (str_contains($html, 'rg-area-hero')) return $this->parseCard($html);
        if (str_contains($html, 'rg-hero-splide')) return $this->parseFullbleed($html);
        return null;
    }

    /**
     * Food-page hero pattern:
     *   <section class="rg-area-hero my-8 not-prose" aria-label="...">
     *     <div class="flex items-baseline justify-between mb-3">
     *       <h2 class="text-xs uppercase tracking-[0.18em] font-bold text-brand-700 m-0">EYEBROW</h2>
     *       <span class="text-xs text-slate-500">SUBTITLE</span>
     *     </div>
     *     <div class="rg-area-hero__splide splide">
     *       ...<li class="splide__slide"><figure><img/><figcaption>...</figcaption></figure></li>...
     */
    private function parseCard(string $html): ?array
    {
        $payload = [
            'style'            => 'card',
            'eyebrow_title'    => '',
            'eyebrow_subtitle' => '',
            'height'           => 'md',
            'autoplay'         => true,
            'interval'         => 6500,
            'images'           => [],
        ];

        if (preg_match('~<h2[^>]*text-brand-700[^>]*>\s*([^<]+?)\s*</h2>~', $html, $m)) {
            $payload['eyebrow_title'] = $this->clean($m[1]);
        }
        if (preg_match('~<span\s+class="text-xs text-slate-500">\s*([^<]+?)\s*</span>~', $html, $m)) {
            $payload['eyebrow_subtitle'] = $this->clean($m[1]);
        }

        // Each <li class="splide__slide"><figure ...>INNER</figure></li>.
        if (!preg_match_all(
            '~<li\s+class="splide__slide">\s*<figure[^>]*>(.+?)</figure>\s*</li>~s',
            $html,
            $sm
        )) {
            return null;
        }

        foreach ($sm[1] as $slideInner) {
            $img = ['src' => '', 'alt' => '', 'caption_title' => '', 'caption' => '', 'credit_url' => ''];
            if (preg_match('~<img\s+src="([^"]+)"\s+alt="([^"]*)"~', $slideInner, $m)) {
                $img['src'] = $m[1];
                $img['alt'] = $m[2];
            }
            if (preg_match('~<figcaption>(.+?)</figcaption>~s', $slideInner, $fm)) {
                $cap = $fm[1];
                if (preg_match('~<strong>\s*(.+?)\s*</strong>~s', $cap, $m)) {
                    $img['caption_title'] = $this->clean($m[1]);
                }
                // Credit link variant: <small><a href="URL">LABEL</a></small>
                if (preg_match('~<small>\s*<a\s+href="([^"]+)"[^>]*>\s*(.+?)\s*</a>\s*</small>~s', $cap, $m)) {
                    $img['credit_url'] = $m[1];
                    $img['caption'] = $this->clean($m[2]);
                } elseif (preg_match('~<span>\s*(.+?)\s*</span>~s', $cap, $m)) {
                    $img['caption'] = $this->clean($m[1]);
                } elseif (preg_match('~<small>\s*(.+?)\s*</small>~s', $cap, $m)) {
                    $img['caption'] = $this->clean(strip_tags($m[1]));
                }
            }
            if ($img['src'] !== '') $payload['images'][] = $img;
        }

        return $payload['images'] ? $payload : null;
    }

    /**
     * Resort-page hero pattern:
     *   <section class="rg-hero-splide splide not-prose my-6 ..." aria-label="...">
     *     <div class="splide__track">
     *       <ul class="splide__list">
     *         <li class="splide__slide">
     *           <div class="relative w-full h-full bg-slate-900">
     *             <img src="..." alt="..." class="absolute inset-0 ..."/>
     *             <div class="absolute inset-0 bg-gradient-to-t ..."></div>
     *             <div class="absolute bottom-0 ...">...caption text...</div>
     *           </div></li>...
     */
    private function parseFullbleed(string $html): ?array
    {
        $payload = [
            'style'            => 'fullbleed',
            'eyebrow_title'    => '',
            'eyebrow_subtitle' => '',
            'height'           => 'md',
            'autoplay'         => true,
            'interval'         => 6500,
            'images'           => [],
        ];

        if (!preg_match_all(
            '~<li\s+class="splide__slide">(.+?)</li>~s',
            $html,
            $sm
        )) {
            return null;
        }

        foreach ($sm[1] as $slideInner) {
            $img = ['src' => '', 'alt' => '', 'caption_title' => '', 'caption' => '', 'credit_url' => ''];
            if (preg_match('~<img\s+src="([^"]+)"\s+alt="([^"]*)"~', $slideInner, $m)) {
                $img['src'] = $m[1];
                $img['alt'] = $m[2];
            }
            // Caption block: <div class="absolute bottom-0 ...">...</div>
            if (preg_match('~<div\s+class="absolute bottom-0[^"]*"[^>]*>(.+?)</div>\s*</div>\s*$~s', $slideInner, $cm)) {
                $capHtml = $cm[1];
                // Try title + body pattern (often the seeder used h3/p inside).
                if (preg_match('~<(?:h[1-6]|div)[^>]*>\s*(.+?)\s*</(?:h[1-6]|div)>~s', $capHtml, $m)) {
                    $img['caption_title'] = $this->clean(strip_tags($m[1]));
                }
                if (preg_match('~<p[^>]*>\s*(.+?)\s*</p>~s', $capHtml, $m)) {
                    $img['caption'] = $this->clean(strip_tags($m[1]));
                }
                // Fallback: take all text as caption.
                if ($img['caption_title'] === '' && $img['caption'] === '') {
                    $img['caption'] = $this->clean(strip_tags($capHtml));
                }
            }
            if ($img['src'] !== '') $payload['images'][] = $img;
        }

        return $payload['images'] ? $payload : null;
    }

    private function clean(string $s): string
    {
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = str_replace(['&amp;', '&nbsp;', '&ndash;'], ['&', ' ', '-'], $s);
        return trim($s);
    }
}
