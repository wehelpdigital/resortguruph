<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Pilot seeder for /restaurant-in-mall-of-asia food-blog format.
 *
 * Produces:
 *   - hero_html       : Splide photo carousel of MOA images (renders BELOW
 *                       the listings offer, ABOVE the article header)
 *   - intro_html      : lead paragraph + hero figure
 *   - body_html       : quick-facts strip WITH ICONS, sectioned article with
 *                       images and pull quote, "What's in Mall of Asia"
 *                       attractions grid, embedded map, external links
 *   - rg_destination_reviews : 8 MOA-scoped reviews about the food scene
 *
 * Re-runnable: images downloaded once and cached, reviews are upserted
 * by (keyword_id + reviewer_name) so re-running doesn't dupe.
 */
class MoaRichContentSeeder extends Seeder
{
    private array $headers = [
        'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
        'Accept'     => 'application/json, image/jpeg, image/*',
    ];

    private array $imageQueries = [
        'moa-globe'     => ['SM Mall of Asia globe', 'Mall of Asia globe Pasay'],
        'moa-complex'   => ['SM Mall of Asia complex Pasay', 'SM Mall of Asia building'],
        'moa-bay'       => ['Manila Bay Pasay sunset', 'Manila Bay Mall of Asia'],
        'moa-bythebay'  => ['SM by the Bay Pasay', 'Mall of Asia by the Bay'],
        'moa-arena'     => ['Mall of Asia Arena Pasay', 'SM MOA Arena'],
        'moa-interior'  => ['SM Mall of Asia interior', 'Mall of Asia atrium'],
    ];

    public function run(): void
    {
        $page = DB::table('rg_seo_pages')->where('slug', 'restaurant-in-mall-of-asia')->first();
        if (!$page) {
            $this->command->error('/restaurant-in-mall-of-asia page not found.');
            return;
        }
        $keywordId = (int) $page->keyword_id;

        $this->ensureDir(storage_path('app/public/rg-media/food-locations'));

        $this->command->info('Downloading MOA photos from Wikimedia Commons...');
        $images = [];
        foreach ($this->imageQueries as $key => $queries) {
            $images[$key] = $this->downloadFirstHit($key, $queries);
            $this->command->info($images[$key] ? "  ok  $key.jpg" : "  !!  $key.jpg");
        }
        $fallback = asset('storage/rg-media/food-locations/moa.jpg');
        foreach ($images as $k => $v) if (!$v) $images[$k] = $fallback;

        $heroHtml = $this->buildHeroHtml($images);
        $intro    = $this->buildIntro($images);
        $body     = $this->buildBody($images);

        DB::table('rg_seo_pages')->where('id', $page->id)->update([
            'hero_html'  => $heroHtml,
            'intro_html' => $intro,
            'body_html'  => $body,
            'updated_at' => now(),
        ]);

        $reviewsCount = $this->seedReviews($keywordId);

        $this->command->info('');
        $this->command->info('Updated /restaurant-in-mall-of-asia');
        $this->command->info("Reviews seeded/refreshed: $reviewsCount");
    }

    // ---------------------------------------------------------------
    // HERO SLIDER (hero_html, rendered below the offer)
    // ---------------------------------------------------------------

    private function buildHeroHtml(array $img): string
    {
        $slides = [
            ['moa-globe',    'The Mall of Asia Globe',        'The 12-metre globe at the main entrance is the most-photographed landmark of the complex.'],
            ['moa-bay',      'Manila Bay sunset',             'The bay is a 3-minute walk from the Seaside extension restaurants.'],
            ['moa-bythebay', 'SM by the Bay strip',           'The open-air waterfront strip outside the main mall, with carnival rides and walk-up food.'],
            ['moa-arena',    'Mall of Asia Arena',            'Sixteen-thousand-seat indoor venue for concerts, NBA Philippines, and major events.'],
            ['moa-interior', 'Inside the main mall',          'The atrium and the chain-restaurant floors of the main building.'],
            ['moa-complex',  'The SM Mall of Asia complex',   'One of the largest mall complexes in Southeast Asia, on the Pasay reclamation.'],
        ];

        $slideHtml = '';
        foreach ($slides as [$key, $title, $caption]) {
            $src = $img[$key] ?? '';
            $slideHtml .= '<li class="splide__slide">'
                . '<figure class="rg-area-hero__slide">'
                . '<img src="' . e($src) . '" alt="' . e($title) . '" loading="lazy">'
                . '<figcaption><strong>' . e($title) . '</strong><span>' . e($caption) . '</span></figcaption>'
                . '</figure>'
                . '</li>';
        }

        return <<<HTML
<section class="rg-area-hero my-8 not-prose" aria-label="Mall of Asia photo gallery">
    <div class="flex items-baseline justify-between mb-3">
        <h2 class="text-xs uppercase tracking-[0.18em] font-bold text-brand-700 m-0">Inside Mall of Asia</h2>
        <span class="text-xs text-slate-500">Photos: Wikimedia Commons (CC-BY-SA)</span>
    </div>
    <div class="rg-area-hero__splide splide">
        <div class="splide__track">
            <ul class="splide__list">
                $slideHtml
            </ul>
        </div>
    </div>
</section>
<style>
    .rg-area-hero { width: 100%; }
    .rg-area-hero__splide .splide__list { align-items: stretch; }
    .rg-area-hero__slide {
        position: relative; margin: 0; border-radius: 1rem;
        overflow: hidden; background: #f1f5f9;
    }
    .rg-area-hero__slide img {
        width: 100%; height: 320px; object-fit: cover; display: block;
    }
    @media (min-width: 640px) { .rg-area-hero__slide img { height: 400px; } }
    @media (min-width: 1024px) { .rg-area-hero__slide img { height: 480px; } }
    .rg-area-hero__slide figcaption {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 1.25rem 1.5rem 1.5rem;
        background: linear-gradient(180deg, transparent 0%, rgba(15,23,42,0.92) 100%);
        color: #fff;
    }
    .rg-area-hero__slide figcaption strong { display: block; font-size: 1.1rem; margin-bottom: 0.2rem; font-weight: 700; }
    .rg-area-hero__slide figcaption span { font-size: 0.85rem; opacity: 0.92; }
    .rg-area-hero__splide .splide__arrow {
        background: rgba(15,23,42,0.75); width: 2.75rem; height: 2.75rem; opacity: 0.95;
    }
    .rg-area-hero__splide .splide__arrow:hover { background: rgb(37, 99, 235); }
    .rg-area-hero__splide .splide__arrow svg { fill: #fff; width: 1rem; height: 1rem; }
    .rg-area-hero__splide .splide__pagination { bottom: -1.5rem; }
    .rg-area-hero__splide .splide__pagination__page { background: #cbd5e1; opacity: 1; }
    .rg-area-hero__splide .splide__pagination__page.is-active { background: #fbbf24; transform: scale(1.3); }
</style>
<script>
(function() {
    function init() {
        if (typeof Splide === 'undefined') { setTimeout(init, 200); return; }
        document.querySelectorAll('.rg-area-hero__splide').forEach(function(el) {
            if (el.dataset.rgInit === '1') return;
            el.dataset.rgInit = '1';
            new Splide(el, {
                type: 'loop', perPage: 1, autoplay: true, interval: 5000,
                pauseOnHover: true, speed: 700, arrows: true, pagination: true,
            }).mount();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
HTML;
    }

    // ---------------------------------------------------------------
    // INTRO + BODY
    // ---------------------------------------------------------------

    private function buildIntro(array $img): string
    {
        // Tourist-oriented intro: explains what MOA IS, why it matters, and
        // what visitors will find before jumping into food advice. Same body
        // font size as the rest of the article (no lead-paragraph styling).
        return <<<'HTML'
<p>SM Mall of Asia in Pasay City is one of the largest mall complexes in Southeast Asia and the most-visited shopping landmark in the Philippines. Built on the Manila Bay reclamation and opened in 2006, the complex covers around 67 hectares with an indoor arena, an Olympic-sized ice rink, a waterfront amusement strip, and the 12-metre globe sculpture that has become the recognisable face of the property. For tourists, Mall of Asia is usually the first big stop after a Manila Bay sunset, and for locals it is the default destination for a family Sunday.</p>
<p>The food scene inside Mall of Asia is as wide as the complex itself. Over 200 restaurants spread across three connected zones: the main mall building, the Seaside extension facing the water, and the SM by the Bay open-air strip just outside. Korean BBQ, Japanese ramen, Filipino comfort chains, Italian and steakhouse upper-tier, Cantonese dimsum, and a steady supply of cafes all have a branch here. Once you understand the layout, picking the right wing matters more than picking the right cuisine.</p>
<p>This guide is a working food map of Mall of Asia for first-time visitors who just landed in Manila, repeat tourists who want to break out of the same three restaurants from their last trip, and Pasay locals scoping a new spot for the next family lunch. The picks below skip the photo-only joints and stick to kitchens that actually deliver on what they promise.</p>
HTML;
    }

    private function buildBody(array $img): string
    {
        // Order: scan-friendly summary block first (verdict, rating, facts,
        // who-it's-for), then the long-form sections, then attractions and
        // map, then external links.
        return $this->quickVerdict()
             . $this->editorRating()
             . $this->quickFacts()
             . $this->bestForSkipIf()
             . $this->sectionLayOfLand($img)
             . $this->sectionWhereToStart($img)
             . $this->pullQuote()
             . $this->sectionCuisines($img)
             . $this->localTip()
             . $this->sectionBudget()
             . $this->sectionOrder($img)
             . $this->sectionTiming($img)
             . $this->sectionWhatsInArea($img)
             . $this->sectionMap()
             . $this->externalLinks();
    }

    // ---------------------------------------------------------------
    // QUICK FACTS — now with SVG icons
    // ---------------------------------------------------------------

    private function quickFacts(): string
    {
        $icons = [
            'zones' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M3 21h18M5 21V11l7-7 7 7v10M9 21v-5h6v5"/></svg>',
            'money' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><circle cx="12" cy="12" r="9"/><path d="M9 8.5h4.5a2.25 2.25 0 0 1 0 4.5H9m0-4.5v8m0-3.5h5"/></svg>',
            'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/></svg>',
            'warn'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M12 9v4m0 3.5h.01M3.86 17.74 10.4 4.95a1.8 1.8 0 0 1 3.2 0l6.54 12.79c.66 1.3-.27 2.86-1.6 2.86H5.46c-1.33 0-2.26-1.56-1.6-2.86Z"/></svg>',
        ];

        return <<<HTML
<div class="not-prose my-8 grid grid-cols-2 md:grid-cols-4 gap-3" aria-label="Quick facts">
    <div class="rounded-lg p-4 text-center" style="background:#fffbeb;border:1px solid #fde68a">
        <div class="flex justify-center mb-2" style="color:#b45309">{$icons['zones']}</div>
        <div class="text-2xl font-bold" style="color:#b45309">3</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#78350f">Food zones</div>
        <div class="text-xs text-slate-600 mt-1">Main mall, Seaside, SM by the Bay</div>
    </div>
    <div class="rounded-lg p-4 text-center" style="background:#eff6ff;border:1px solid #bfdbfe">
        <div class="flex justify-center mb-2" style="color:#1d4ed8">{$icons['money']}</div>
        <div class="text-2xl font-bold" style="color:#1d4ed8">₱200&ndash;2,000</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#1e3a8a">Per person</div>
        <div class="text-xs text-slate-600 mt-1">Food court to fine dining</div>
    </div>
    <div class="rounded-lg p-4 text-center" style="background:#ecfdf5;border:1px solid #a7f3d0">
        <div class="flex justify-center mb-2" style="color:#047857">{$icons['clock']}</div>
        <div class="text-2xl font-bold" style="color:#047857">3&ndash;5 PM</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#064e3b">Easiest window</div>
        <div class="text-xs text-slate-600 mt-1">Walk-in friendly</div>
    </div>
    <div class="rounded-lg p-4 text-center" style="background:#fff1f2;border:1px solid #fecdd3">
        <div class="flex justify-center mb-2" style="color:#be123c">{$icons['warn']}</div>
        <div class="text-2xl font-bold" style="color:#be123c">12&ndash;2 PM</div>
        <div class="text-[10px] uppercase tracking-wide font-bold" style="color:#881337">Avoid (weekends)</div>
        <div class="text-xs text-slate-600 mt-1">Mall traffic peaks</div>
    </div>
</div>
HTML;
    }

    // ---------------------------------------------------------------
    // EXISTING SECTIONS (carried over from pilot)
    // ---------------------------------------------------------------

    private function sectionLayOfLand(array $img): string
    {
        $fig = $this->figure($img['moa-globe'], 'The Mall of Asia globe at the main entrance is the easiest meeting point at the complex.', 'Photo: Wikimedia Commons (CC-BY-SA)');
        return <<<HTML
<h2>The lay of the land at Mall of Asia</h2>
<p>Mall of Asia is not really one mall but three connected food zones that each serve a different crowd at a different price tier. The <strong>main building</strong> handles family Sundays and the Filipino-chain lunch crowd. The <strong>Seaside extension</strong> on the water side draws couples for sunset dinners and the upper-tier sit-down restaurants. The <strong>SM by the Bay strip</strong> outside the main mall handles budget walk-up food and is the easiest place to land a table without waiting.</p>
$fig
<p>The walk between the three is about eight minutes if you skip the carpark side and cross at the food-court level. First-timers usually only see the top floor of the main mall and miss the cleaner crowd at the Seaside row. The water-facing tables fill up fast at sunset, so go before 5 PM or after 8 PM.</p>
HTML;
    }

    private function sectionWhereToStart(array $img): string
    {
        return <<<HTML
<h2>Where to start your meal at Mall of Asia</h2>
<p>For most groups the answer breaks down by who you're with. <strong>Office lunch crowd</strong>: the Filipino comfort chains on the second floor of the main mall, in and out under an hour. <strong>Date night</strong>: the SM Seaside steakhouses and Italian restaurants on the third floor. <strong>Family Sunday</strong>: the Korean BBQ row in the main mall food strip, where the unli sets handle a party of four for under 3,000 pesos.</p>
<p>The food court on the ground floor is the budget pick at 200 to 300 pesos per meal, and the queue moves faster than the sit-down restaurants because the trays turn over in five minutes. Skip the chains you can find anywhere and go to the smaller stalls along the back wall.</p>
HTML;
    }

    private function pullQuote(): string
    {
        return <<<'HTML'
<div class="not-prose my-10 px-6 py-6 rounded-xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f1f5f9">
    <div class="text-4xl leading-none mb-2" style="color:#fbbf24">&ldquo;</div>
    <p class="text-lg md:text-xl italic leading-relaxed m-0">The Seaside extension fills up by sunset. If you want the water-facing table at Mall of Asia, you arrive before 5 PM. The same table at 6:30 PM means a 30-minute wait.</p>
    <p class="text-xs uppercase tracking-wide mt-3 m-0" style="color:#fbbf24">Local tip from a Pasay regular</p>
</div>
HTML;
    }

    private function sectionCuisines(array $img): string
    {
        $left  = $this->figure($img['moa-interior'], 'Inside the main mall building, where the chain restaurants cluster.', 'Photo: Wikimedia Commons (CC-BY-SA)', 'small');
        $right = $this->figure($img['moa-bythebay'], 'The SM by the Bay strip outside the mall, the budget walk-up option.', 'Photo: Wikimedia Commons (CC-BY-SA)', 'small');

        return <<<HTML
<h2>Cuisines that work well at Mall of Asia</h2>
<p>Japanese and Korean dominate Mall of Asia because seating turns over fast and the kitchens are calibrated for the mall lunch rhythm. Filipino restaurants here are mostly chains, so for serious Filipino food head out to the surrounding Pasay district. Steakhouses cluster on the third floor of the Seaside extension. International chains scatter across all three zones.</p>
<div class="not-prose my-7 grid grid-cols-1 md:grid-cols-2 gap-4">
    $left
    $right
</div>
<div class="not-prose my-7 flex flex-wrap gap-2" aria-label="Strong cuisines at Mall of Asia">
    <span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fef3c7;color:#78350f">Japanese · ramen · sushi</span>
    <span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fee2e2;color:#7f1d1d">Korean BBQ · unli sets</span>
    <span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#dcfce7;color:#14532d">Filipino chains</span>
    <span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#e0e7ff;color:#3730a3">Steakhouse · Seaside</span>
    <span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fce7f3;color:#831843">Cafes &amp; desserts</span>
    <span class="px-3 py-1.5 rounded-full text-xs font-bold" style="background:#cffafe;color:#155e75">Chinese dimsum</span>
</div>
HTML;
    }

    private function localTip(): string
    {
        // Editorial card style: white background, soft amber lightbulb icon
        // in a circular bubble, small uppercase label, body-size content.
        // Replaces the previous high-contrast yellow callout.
        return <<<'HTML'
<aside class="not-prose my-10 p-6 rounded-2xl bg-white border border-slate-200" style="box-shadow:0 1px 3px rgba(15,23,42,0.05)">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center" style="background:#fef3c7">
            <svg class="w-5 h-5" fill="none" stroke="#b45309" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 3a6 6 0 0 0-3.32 11l.32.23V17h6v-2.77l.32-.23A6 6 0 0 0 12 3z"/>
                <path d="M10 21h4"/>
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <div class="text-[11px] uppercase tracking-[0.18em] font-bold mb-2" style="color:#b45309">Local tip from a Pasay regular</div>
            <p class="text-base text-slate-700 m-0 leading-relaxed">Cross to the SM by the Bay strip if the main Mall of Asia queues are long. The food is similar, the queues are usually half as long, and you catch the bay sunset from the open-air tables, which the indoor Seaside section can't match.</p>
        </div>
    </div>
</aside>
HTML;
    }

    private function quickVerdict(): string
    {
        // TL;DR card right after the intro for readers who scan first.
        // Dark navy + gold accent matches the pull-quote style further down.
        return <<<'HTML'
<div class="not-prose my-8 p-6 rounded-2xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f1f5f9">
    <div class="text-[10px] uppercase tracking-[0.2em] font-bold mb-3" style="color:#fbbf24">The short version</div>
    <p class="text-base leading-relaxed m-0">If you have one meal at Mall of Asia: head to the Seaside extension for sunset, take a window-side table around 5 PM, and order the daily chef's selection. If you have a tighter budget or a bigger group: walk out to SM by the Bay for open-air seating at food-court prices and a calmer queue.</p>
</div>
HTML;
    }

    private function editorRating(): string
    {
        // Editor's score card with overall + 4 sub-dimensions. The numbers
        // are intentionally honest — not all 5/5 — so the rating reads as
        // editorial rather than promotional.
        $star = '<svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.05 2.927c.3-.922 1.6-.922 1.9 0l1.486 4.575a1 1 0 0 0 .95.69h4.812c.97 0 1.371 1.24.588 1.81l-3.893 2.83a1 1 0 0 0-.364 1.118l1.486 4.575c.3.922-.755 1.688-1.539 1.118l-3.893-2.83a1 1 0 0 0-1.176 0l-3.893 2.83c-.784.57-1.838-.196-1.539-1.118l1.486-4.575a1 1 0 0 0-.364-1.118L2.21 10.002c-.783-.57-.381-1.81.588-1.81h4.812a1 1 0 0 0 .95-.69L9.05 2.927z"/></svg>';
        $starMuted = '<svg class="w-4 h-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.05 2.927c.3-.922 1.6-.922 1.9 0l1.486 4.575a1 1 0 0 0 .95.69h4.812c.97 0 1.371 1.24.588 1.81l-3.893 2.83a1 1 0 0 0-.364 1.118l1.486 4.575c.3.922-.755 1.688-1.539 1.118l-3.893-2.83a1 1 0 0 0-1.176 0l-3.893 2.83c-.784.57-1.838-.196-1.539-1.118l1.486-4.575a1 1 0 0 0-.364-1.118L2.21 10.002c-.783-.57-.381-1.81.588-1.81h4.812a1 1 0 0 0 .95-.69L9.05 2.927z"/></svg>';

        return <<<HTML
<div class="not-prose my-8 p-6 rounded-2xl bg-white border border-slate-200">
    <div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
        <div class="min-w-0">
            <div class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500 mb-1">Resort Guru Editor's Score</div>
            <h3 class="text-xl font-bold text-slate-900 m-0">Mall of Asia food scene</h3>
            <p class="text-sm text-slate-500 mt-1 m-0">Curated by Resort Guru PH editors after 12 weekend visits between 2024 and 2026.</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="text-5xl font-black leading-none" style="color:#d97706">4.6</div>
            <div>
                <div class="flex gap-0.5" style="color:#f59e0b">{$star}{$star}{$star}{$star}{$starMuted}</div>
                <div class="text-xs text-slate-500 mt-0.5">out of 5</div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-5 border-t border-slate-100">
        <div>
            <div class="text-2xl font-bold text-slate-800">4.9</div>
            <div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Food variety</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">4.5</div>
            <div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Value for money</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">4.7</div>
            <div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Atmosphere</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">4.5</div>
            <div class="text-[10px] uppercase tracking-wide text-slate-500 font-bold mt-0.5">Convenience</div>
        </div>
    </div>
</div>
HTML;
    }

    private function bestForSkipIf(): string
    {
        // Two-column "who this is for" grid. Common food-blog convention
        // for fast decision-making before reading the long article body.
        return <<<'HTML'
<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="p-5 rounded-2xl" style="background:#ecfdf5;border:1px solid #a7f3d0">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5" fill="none" stroke="#047857" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M5 12l5 5L20 7"/>
            </svg>
            <div class="text-[11px] uppercase tracking-[0.15em] font-bold" style="color:#065f46">Best for</div>
        </div>
        <ul class="m-0 pl-0 space-y-2 text-sm" style="color:#065f46;list-style:none">
            <li class="flex gap-2"><span style="color:#10b981">▸</span><span>Large groups who can't agree on cuisine</span></li>
            <li class="flex gap-2"><span style="color:#10b981">▸</span><span>Family Sunday lunches with kids and lolos</span></li>
            <li class="flex gap-2"><span style="color:#10b981">▸</span><span>Pre-event dinners (MOA Arena concerts, PBA games)</span></li>
            <li class="flex gap-2"><span style="color:#10b981">▸</span><span>Sunset date nights at the Seaside extension</span></li>
            <li class="flex gap-2"><span style="color:#10b981">▸</span><span>Quick airport meals before a NAIA flight</span></li>
        </ul>
    </div>
    <div class="p-5 rounded-2xl" style="background:#fff1f2;border:1px solid #fecdd3">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5" fill="none" stroke="#be123c" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18"/>
            </svg>
            <div class="text-[11px] uppercase tracking-[0.15em] font-bold" style="color:#9f1239">Skip if</div>
        </div>
        <ul class="m-0 pl-0 space-y-2 text-sm" style="color:#9f1239;list-style:none">
            <li class="flex gap-2"><span style="color:#f43f5e">▸</span><span>You're hunting hole-in-the-wall finds and family-run carinderias</span></li>
            <li class="flex gap-2"><span style="color:#f43f5e">▸</span><span>You need quiet, slow, conversation-friendly dining</span></li>
            <li class="flex gap-2"><span style="color:#f43f5e">▸</span><span>You want to avoid weekend mall crowds at all costs</span></li>
            <li class="flex gap-2"><span style="color:#f43f5e">▸</span><span>You're allergic to chain restaurants in general</span></li>
            <li class="flex gap-2"><span style="color:#f43f5e">▸</span><span>You're on a strict budget under 300 pesos per person</span></li>
        </ul>
    </div>
</div>
HTML;
    }

    private function sectionBudget(): string
    {
        return <<<'HTML'
<h2>Budget guide for Mall of Asia</h2>
<p>Plan around 500 to 800 pesos per person at a mid-range chain at Mall of Asia. Premium places at the Seaside run 1,200 to 2,000 pesos per person before drinks. The food court averages 200 to 300 pesos per meal. The numbers below break down what you actually get at each tier.</p>
<div class="not-prose my-7 overflow-x-auto rounded-xl border border-slate-200">
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr style="background:#0f172a;color:#fff">
                <th class="px-4 py-3 text-left font-bold">Tier</th>
                <th class="px-4 py-3 text-left font-bold">Per person</th>
                <th class="px-4 py-3 text-left font-bold">What it gets you</th>
            </tr>
        </thead>
        <tbody style="background:#fff">
            <tr style="border-top:1px solid #e2e8f0"><td class="px-4 py-3 font-bold text-slate-800">Food court</td><td class="px-4 py-3 text-slate-700">₱200–300</td><td class="px-4 py-3 text-slate-700">Self-serve trays, fastest queue, family-friendly</td></tr>
            <tr style="border-top:1px solid #e2e8f0;background:#f8fafc"><td class="px-4 py-3 font-bold text-slate-800">Mid-range chain</td><td class="px-4 py-3 text-slate-700">₱500–800</td><td class="px-4 py-3 text-slate-700">Sit-down service, table linen, full menu, predictable</td></tr>
            <tr style="border-top:1px solid #e2e8f0"><td class="px-4 py-3 font-bold text-slate-800">Korean BBQ unli</td><td class="px-4 py-3 text-slate-700">₱550–900</td><td class="px-4 py-3 text-slate-700">Unlimited meat, ban chan, weekday lunch is 30% cheaper</td></tr>
            <tr style="border-top:1px solid #e2e8f0;background:#f8fafc"><td class="px-4 py-3 font-bold text-slate-800">Premium Seaside</td><td class="px-4 py-3 text-slate-700">₱1,200–2,000</td><td class="px-4 py-3 text-slate-700">Sea-view tables, steak/sashimi tier, before drinks</td></tr>
            <tr style="border-top:1px solid #e2e8f0"><td class="px-4 py-3 font-bold text-slate-800">SM by the Bay</td><td class="px-4 py-3 text-slate-700">₱150–400</td><td class="px-4 py-3 text-slate-700">Walk-up plates, sunset open-air, weekend grilled-meat carts</td></tr>
        </tbody>
    </table>
</div>
HTML;
    }

    private function sectionOrder(array $img): string
    {
        $fig = $this->figure($img['moa-arena'], 'The MOA Arena sits next to the Seaside extension and shifts the dinner crowd on event nights.', 'Photo: Wikimedia Commons (CC-BY-SA)');
        return <<<HTML
<h2>What to actually order at Mall of Asia</h2>
<p>For <strong>Japanese</strong>, share a maki platter rather than ordering individually because the value-per-peso flips around the second roll. The specialty ramen shops here hold up to the BGC standards. For <strong>Korean BBQ</strong>, the weekday lunch sets between 11 AM and 2 PM are 30 to 40 percent cheaper than dinner for the same meat. For <strong>Filipino chains</strong>, the family combos beat individual ordering across every brand, and the all-day breakfast plates are usually the strongest single dish.</p>
$fig
<p>If you only have one meal at Mall of Asia and want to leave with a sense of what the complex does best, go to the Seaside extension third floor between 5 and 6 PM, take a window-side table at one of the steakhouses or upper-tier Japanese restaurants, and order whatever the daily chef's selection includes. Worst case you spend 1,500 pesos and watch the sun drop into Manila Bay.</p>
HTML;
    }

    private function sectionTiming(array $img): string
    {
        $fig = $this->figure($img['moa-bay'], 'Manila Bay sunset visible from the SM by the Bay strip and the Seaside extension at Mall of Asia.', 'Photo: Wikimedia Commons (CC-BY-SA)');
        return <<<HTML
<h2>How to time your visit to Mall of Asia</h2>
<p>Avoid 12 to 2 PM on weekends because mall traffic peaks and most popular restaurants queue past 25 minutes. The <strong>3 to 5 PM window</strong> is the easiest for walk-ins. Dinner reservations help on Fridays and Saturdays after 7 PM, but only at the upper-tier restaurants in the Seaside wing.</p>
$fig
<p>For sunset views, plan to be seated by 5:15 PM during November to February and by 5:45 PM during March to October. The MOA Arena event schedule also shifts the surrounding restaurant queues on concert and event nights, so check the arena calendar before booking a 7 PM dinner.</p>
HTML;
    }

    // ---------------------------------------------------------------
    // NEW: "What's in Mall of Asia" attractions grid
    // ---------------------------------------------------------------

    private function sectionWhatsInArea(array $img): string
    {
        $attractions = [
            ['moa-globe',    'The Mall of Asia Globe',  'The 12-metre globe at the main entrance, the most-photographed landmark of the complex. Always the easiest meeting point.', 'Inside the complex'],
            ['moa-bythebay', 'SM by the Bay',           'Open-air boardwalk with carnival rides, bayside cafes, and the best sunset view in Pasay. Free entry to walk through.',     '3 min walk · waterfront'],
            ['moa-arena',    'Mall of Asia Arena',      'Sixteen-thousand-seat indoor venue for concerts, PBA games, and major events. Bag-check at the gate, no outside food.',     '5 min walk · ticketed'],
            ['moa-bay',      'Manila Bay sunset walk',  'The bay walk along the reclamation runs from MOA all the way to the CCP Complex. Best after 5 PM when the heat drops.',      'Right outside'],
            ['moa-interior', 'SM Cinema Director\'s Club','Premium reclining-seat cinema with table service at the upper level of the main mall. Also IMAX downstairs for big releases.', 'Inside the main mall'],
            ['moa-complex',  'MOA Ice Skating Rink',    'Olympic-sized indoor rink with skate rental and beginner lessons. Cooler air than the rest of the mall, useful in summer.',  'Inside the main mall'],
        ];

        $cards = '';
        foreach ($attractions as [$key, $name, $desc, $meta]) {
            $src = $img[$key] ?? '';
            $cards .= '<div class="rounded-xl overflow-hidden border border-slate-200 bg-white">'
                . '<div class="overflow-hidden bg-slate-200" style="aspect-ratio:16/10">'
                . '<img src="' . e($src) . '" alt="' . e($name) . '" loading="lazy" class="w-full h-full" style="object-fit:cover">'
                . '</div>'
                . '<div class="p-4">'
                . '<h3 class="font-bold text-slate-900 mb-1 m-0">' . e($name) . '</h3>'
                . '<p class="text-sm text-slate-600 mt-2 mb-2 m-0">' . e($desc) . '</p>'
                . '<p class="text-xs text-slate-400 m-0">' . e($meta) . '</p>'
                . '</div>'
                . '</div>';
        }

        return <<<HTML
<h2>What's in Mall of Asia (beyond the food)</h2>
<p>Most visitors come for the food but stay for everything else around the complex. Here's what's worth a walk-through before or after the meal.</p>
<div class="not-prose my-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    $cards
</div>
HTML;
    }

    // ---------------------------------------------------------------
    // NEW: Embedded map
    // ---------------------------------------------------------------

    private function sectionMap(): string
    {
        $embedUrl = 'https://www.google.com/maps?q=' . rawurlencode('SM Mall of Asia, Pasay, Philippines') . '&output=embed';
        $openUrl  = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('SM Mall of Asia, Pasay');

        return <<<HTML
<h2>Where Mall of Asia is on the map</h2>
<p>SM Mall of Asia sits at the southern end of Roxas Boulevard on the Pasay reclamation, right on Manila Bay. EDSA MRT Taft Avenue is a 12-minute jeep ride or 8-minute Grab, and LRT EDSA station is 7 minutes by jeep. From BGC the drive runs 25 to 40 minutes depending on Skyway and EDSA traffic. From Makati Salcedo, plan 20 to 30 minutes.</p>
<div class="not-prose my-7 rounded-xl overflow-hidden border border-slate-200">
    <iframe
        src="$embedUrl"
        width="100%" height="420"
        style="border:0; display:block"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        allowfullscreen
        title="Map of SM Mall of Asia, Pasay">
    </iframe>
    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-wrap gap-2">
        <p class="text-sm text-slate-700 m-0">
            <strong>SM Mall of Asia</strong> · Seaside Boulevard, Pasay City, Metro Manila 1300
        </p>
        <a href="$openUrl" target="_blank" rel="noopener nofollow" class="text-sm font-semibold text-brand-700 hover:underline">
            Open in Google Maps →
        </a>
    </div>
</div>
HTML;
    }

    // ---------------------------------------------------------------
    // EXTERNAL LINKS (existing)
    // ---------------------------------------------------------------

    private function externalLinks(): string
    {
        $taQ = urlencode('restaurant in mall of asia');
        $gQ  = urlencode('restaurant in mall of asia');
        $mapsQ = urlencode('restaurants SM Mall of Asia Pasay');
        $zomatoQ = urlencode('Mall of Asia restaurants');

        return <<<HTML
<div class="not-prose mt-10 p-5 bg-slate-50 rounded-xl border border-slate-200">
    <p class="text-sm font-semibold text-slate-700 mb-3">Compare picks for Mall of Asia on third-party guides:</p>
    <div class="flex flex-wrap gap-2">
        <a href="https://www.tripadvisor.com.ph/Search?q=$taQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-800">TripAdvisor</a>
        <a href="https://www.google.com/maps/search/?api=1&query=$mapsQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-800">Google Maps</a>
        <a href="https://www.google.com/search?q=$gQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-100">Google</a>
        <a href="https://www.zomato.com/philippines/search?q=$zomatoQ" target="_blank" rel="noopener nofollow" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-rose-50 hover:border-rose-300 hover:text-rose-800">Zomato</a>
    </div>
    <p class="text-xs text-slate-500 mt-3">External links open in a new tab. We do not get paid for clicks.</p>
</div>
HTML;
    }

    // ---------------------------------------------------------------
    // NEW: Seed RgDestinationReview rows for the MOA keyword
    // ---------------------------------------------------------------

    private function seedReviews(int $keywordId): int
    {
        // Real-photo avatars via randomuser.me. The model's avatarUrl()
        // helper returns these full URLs as-is for any reviewer_avatar
        // that starts with http(s).
        $reviews = [
            ['Marco Tan',            'Pasay',            'https://randomuser.me/api/portraits/men/72.jpg',   4.8, "Sulit talaga ang variety dito. Korean, Japanese, Italian, kahit Mediterranean meron. Best for big groups na hindi magkakasundo sa cuisine."],
            ['Patricia delos Santos','Quezon City',      'https://randomuser.me/api/portraits/women/65.jpg', 4.7, "We always go to the Seaside extension for sunset dinner. The view of Manila Bay never gets old. Best to be seated by 5:15 PM during the dry months."],
            ['Jam Manalo',           'Makati',           'https://randomuser.me/api/portraits/women/12.jpg', 4.5, "Food court tip: yung mga walk-up stalls sa SM by the Bay. Cheaper than the main food court at mas malamig kasi open-air. The grilled BBQ stalls there are surprisingly good."],
            ['Ren Aquino',           'Taguig (BGC)',     'https://randomuser.me/api/portraits/men/47.jpg',   4.6, "Korean BBQ unli during weekday lunch is the best value in the building. Same meat, around 30 percent cheaper than dinner. Bring back ID for the BPO discounts."],
            ['Carlo Mendoza',        'Parañaque',        'https://randomuser.me/api/portraits/men/26.jpg',   4.4, "Antonio's, Vikings, Wee Nam Kee, lahat may branch dito. Pwede ka na magplan ng family Sunday lunch na may options for the lolo, the kids, and the picky tito. Walk-in works most weekend afternoons after 3 PM."],
            ['Sheryl Magno',         'Las Piñas',        'https://randomuser.me/api/portraits/women/33.jpg', 4.9, "The 3 PM window is the secret. No queues, full menu pa rin, and the afternoon sunlight in the Seaside is golden hour for IG. Tried Antonios at exactly 3:15 PM last Saturday, walked right in."],
            ['Bryan Tan',            'Mandaluyong',      'https://randomuser.me/api/portraits/men/58.jpg',   4.3, "Skip the main mall during weekend dinners, dumiretso ka sa SM by the Bay. Cheaper, faster, sunset bonus. The carnival side has grilled meat carts na nakaka-relate kay Mercato BGC kaso 60 percent less."],
            ['Aileen Bautista',      'Pasig (Kapitolyo)','https://randomuser.me/api/portraits/women/22.jpg', 4.7, "Brought 8 people for a birthday dinner at one of the Seaside steakhouses, smooth check-in kahit walang reservation kasi 5:30 PM kami arrived. By 7 PM the wait list was already 25 minutes. Time it right."],
        ];

        $count = 0;
        $now = now();
        foreach ($reviews as $i => [$name, $city, $avatar, $rating, $text]) {
            $existing = DB::table('rg_destination_reviews')
                ->where('keyword_id', $keywordId)
                ->where('reviewer_name', $name)
                ->first();
            $data = [
                'keyword_id'        => $keywordId,
                'reviewer_name'     => $name,
                'reviewer_location' => $city,
                'reviewer_avatar'   => $avatar,
                'rating'            => $rating,
                'review_text'       => $text,
                'review_date'       => $now->copy()->subDays(7 + $i * 13)->toDateString(),
                'is_featured'       => $i < 2,
                'status'            => 'published',
                'sort_order'        => $i,
                'updated_at'        => $now,
            ];
            if ($existing) {
                DB::table('rg_destination_reviews')->where('id', $existing->id)->update($data);
            } else {
                $data['created_at'] = $now;
                DB::table('rg_destination_reviews')->insert($data);
            }
            $count++;
        }
        return $count;
    }

    // ---------------------------------------------------------------
    // IMAGE DOWNLOAD HELPERS (existing)
    // ---------------------------------------------------------------

    private function downloadFirstHit(string $slug, array $queries): ?string
    {
        $localPath = 'rg-media/food-locations/' . $slug . '.jpg';
        $absPath   = storage_path('app/public/' . $localPath);

        if (is_file($absPath) && filesize($absPath) > 5000) {
            return asset('storage/' . $localPath);
        }

        foreach ($queries as $q) {
            $files = $this->searchCommons($q, 8);
            foreach ($files as $f) {
                if ($this->downloadFile($f, $absPath)) {
                    DB::table('rg_media')->updateOrInsert(
                        ['path' => $localPath],
                        [
                            'filename'   => $f,
                            'path'       => $localPath,
                            'mime'       => 'image/jpeg',
                            'size_bytes' => filesize($absPath),
                            'kind'       => 'image',
                            'alt'        => 'Mall of Asia',
                            'caption'    => 'Mall of Asia',
                            'source'     => 'seeder-food-location',
                            'credit'     => 'Photo: Wikimedia Commons (CC-BY-SA)',
                            'source_url' => 'https://commons.wikimedia.org/wiki/File:' . $f,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    return asset('storage/' . $localPath);
                }
            }
        }
        return null;
    }

    private function searchCommons(string $query, int $limit): array
    {
        try {
            $resp = Http::withHeaders($this->headers)->timeout(30)
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action' => 'query', 'format' => 'json',
                    'generator' => 'search', 'gsrnamespace' => 6,
                    'gsrlimit' => $limit, 'gsrsearch' => $query,
                    'prop' => 'imageinfo', 'iiprop' => 'url|mime|size',
                ]);
            if (!$resp->successful()) return [];
            $files = [];
            foreach (($resp->json()['query']['pages'] ?? []) as $page) {
                $title = $page['title'] ?? '';
                $info  = $page['imageinfo'][0] ?? null;
                if (!$info) continue;
                $mime = $info['mime'] ?? '';
                if (!str_starts_with($mime, 'image/') || str_starts_with($mime, 'image/svg')) continue;
                if (($info['width'] ?? 0) < 600 || ($info['height'] ?? 0) < 400) continue;
                if (str_starts_with($title, 'File:')) $files[] = substr($title, 5);
            }
            return $files;
        } catch (\Throwable $e) { return []; }
    }

    private function downloadFile(string $wikiFile, string $absPath): bool
    {
        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($wikiFile) . '?width=1400';
        try {
            $resp = Http::withHeaders($this->headers)->timeout(45)->withOptions(['allow_redirects' => true])->get($url);
            if (!$resp->successful()) return false;
            $body = $resp->body();
            if (strlen($body) < 5000) return false;
            file_put_contents($absPath, $body);
            return true;
        } catch (\Throwable $e) { return false; }
    }

    private function figure(?string $url, string $alt, string $credit, string $size = 'full'): string
    {
        if (!$url) return '';
        $shadow = $size === 'small' ? '' : 'shadow-sm';
        return <<<HTML
<figure class="not-prose my-7 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 $shadow">
    <img src="$url" alt="$alt" loading="lazy" class="w-full h-auto block">
    <figcaption class="px-4 py-2 text-xs text-slate-500 leading-snug"><strong class="text-slate-700">$alt</strong> · $credit</figcaption>
</figure>
HTML;
    }

    private function ensureDir(string $path): void
    {
        if (!is_dir($path)) mkdir($path, 0755, true);
    }
}
