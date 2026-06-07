<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\RgSetting::get('site_tagline', 'Find the best resorts and hotels in the Philippines'))">
    @hasSection('meta_keywords')<meta name="keywords" content="@yield('meta_keywords')">@endif
    @hasSection('canonical')<link rel="canonical" href="@yield('canonical')">@endif

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', \App\Models\RgSetting::get('site_tagline', ''))">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')<meta property="og:image" content="@yield('og_image')">@endif

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8F%96%EF%B8%8F%3C/text%3E%3C/svg%3E">

    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eef4ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' },
                    },
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Splide carousel (used by keyword pages' hero gallery) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.rg-hero-splide').forEach(function (el) {
                if (typeof Splide !== 'undefined') {
                    // fixedHeight (vs heightRatio) gives predictable carousel
                    // dimensions and removes the bottom-cut-off bug where the
                    // ratio-driven track padding fought with min-height CSS.
                    new Splide(el, {
                        type: 'loop',
                        autoplay: true,
                        interval: 4500,
                        speed: 800,
                        pauseOnHover: true,
                        arrows: true,
                        pagination: false,
                        fixedHeight: '480px',
                        breakpoints: { 640: { fixedHeight: '320px' } },
                    }).mount();
                }
            });
        });
    </script>

    {{-- TLDR / WWWW accordion animator. Native <details> snaps open/closed;
         this measures the body content and animates max-height + opacity so
         expand/contract feels smooth in both directions. --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('details.rg-accordion').forEach(function (det) {
                const body = det.querySelector('.rg-accordion-body');
                const inner = det.querySelector('.rg-accordion-body-inner');
                if (!body || !inner) return;
                const summary = det.querySelector('summary');
                summary.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (det.open) {
                        // Closing: set max-height to current pixel value (so transition has a starting point), then drop to 0
                        body.style.maxHeight = inner.scrollHeight + 'px';
                        det.setAttribute('data-anim', 'closing');
                        requestAnimationFrame(function () {
                            body.style.maxHeight = '0px';
                        });
                        body.addEventListener('transitionend', function handler(ev) {
                            if (ev.propertyName !== 'max-height') return;
                            body.removeEventListener('transitionend', handler);
                            det.open = false;
                            det.removeAttribute('data-anim');
                            body.style.maxHeight = '';
                        });
                    } else {
                        // Opening: set open, measure, then animate to target
                        det.open = true;
                        const target = inner.scrollHeight + 16; // +pad for safety
                        body.style.maxHeight = '0px';
                        requestAnimationFrame(function () {
                            body.style.maxHeight = target + 'px';
                        });
                        body.addEventListener('transitionend', function handler(ev) {
                            if (ev.propertyName !== 'max-height') return;
                            body.removeEventListener('transitionend', handler);
                            body.style.maxHeight = ''; // let it size naturally after
                        });
                    }
                });
            });
        });
    </script>

    {{-- Global UX animations + smooth scrolling --}}
    <style>
        html { scroll-behavior: smooth; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
        }
        /* Page entrance fade */
        body { animation: rgFadeIn 0.5s ease-out both; }
        @keyframes rgFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* Reveal on scroll (toggled by IntersectionObserver) */
        .rg-reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease-out, transform 0.7s ease-out; }
        .rg-reveal.is-visible { opacity: 1; transform: translateY(0); }

        /* Stagger children,each child gets an incremental delay */
        .rg-stagger > * { opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
        .rg-stagger.is-visible > *:nth-child(1) { opacity: 1; transform: translateY(0); transition-delay: 0.05s; }
        .rg-stagger.is-visible > *:nth-child(2) { opacity: 1; transform: translateY(0); transition-delay: 0.12s; }
        .rg-stagger.is-visible > *:nth-child(3) { opacity: 1; transform: translateY(0); transition-delay: 0.19s; }
        .rg-stagger.is-visible > *:nth-child(4) { opacity: 1; transform: translateY(0); transition-delay: 0.26s; }
        .rg-stagger.is-visible > *:nth-child(5) { opacity: 1; transform: translateY(0); transition-delay: 0.33s; }
        .rg-stagger.is-visible > *:nth-child(6) { opacity: 1; transform: translateY(0); transition-delay: 0.40s; }
        .rg-stagger.is-visible > *:nth-child(7) { opacity: 1; transform: translateY(0); transition-delay: 0.47s; }
        .rg-stagger.is-visible > *:nth-child(8) { opacity: 1; transform: translateY(0); transition-delay: 0.54s; }
        .rg-stagger.is-visible > *:nth-child(9) { opacity: 1; transform: translateY(0); transition-delay: 0.61s; }
        .rg-stagger.is-visible > *:nth-child(10) { opacity: 1; transform: translateY(0); transition-delay: 0.68s; }
        .rg-stagger.is-visible > *:nth-child(n+11) { opacity: 1; transform: translateY(0); transition-delay: 0.75s; }

        /* Card hover lift */
        .rg-card-lift { transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, border-color 0.25s ease-out; will-change: transform; }
        .rg-card-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -8px rgba(15, 23, 42, 0.18); }

        /* Button micro-interaction */
        a, button { transition: background-color 0.18s ease-out, color 0.18s ease-out, transform 0.12s ease-out, box-shadow 0.18s ease-out; }
        a:active, button:active { transform: translateY(1px); }
        /* Splide arrows must not press-down,they're absolutely-positioned
           overlay controls and shifting them 1px on click made each retry
           feel like the button was dodging the cursor. */
        .splide__arrow,
        .splide__arrow:active,
        .splide__arrow:hover,
        .rg-hero-splide .splide__arrow,
        .rg-hero-splide .splide__arrow:active { transform: translateY(-50%) !important; }

        /* Listings band: tinted background container above the hero slider.
           Soft cream-amber gradient,no border (user feedback: orange edge
           read as a warning state). Keep just the rounded corners and a
           gentle inset shadow so the band still pops out of the white body. */
        .rg-listings-band {
            background: linear-gradient(180deg, #fefce8 0%, #fff7ed 100%);
            border-radius: 1rem;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
        }

        /* 4-strip fade animation on each listing row. Each strip cycles
           between its two images. Cycle bumped to 14s (was 7.2s) so each
           image lingers ~7 seconds before fading,calmer, less distracting.
           Strips are offset by 3.5s so the row always has motion but never
           all four changing at once. */
        .rg-fade-strip { position: relative; }
        .rg-fade-strip img {
            opacity: 0;
            animation: rg-fade-cycle 14s infinite ease-in-out;
        }
        @keyframes rg-fade-cycle {
            0%, 45%  { opacity: 1; }
            55%, 95% { opacity: 0; }
            100%     { opacity: 1; }
        }
        .rg-fade-strip[data-strip-index="0"] img:nth-child(1) { opacity: 1; animation-delay: 0s; }
        .rg-fade-strip[data-strip-index="0"] img:nth-child(2) { animation-delay: 7s; }
        .rg-fade-strip[data-strip-index="1"] img:nth-child(1) { opacity: 1; animation-delay: 3.5s; }
        .rg-fade-strip[data-strip-index="1"] img:nth-child(2) { animation-delay: 10.5s; }
        .rg-fade-strip[data-strip-index="2"] img:nth-child(1) { opacity: 1; animation-delay: 7s; }
        .rg-fade-strip[data-strip-index="2"] img:nth-child(2) { animation-delay: 0s; }
        .rg-fade-strip[data-strip-index="3"] img:nth-child(1) { opacity: 1; animation-delay: 10.5s; }
        .rg-fade-strip[data-strip-index="3"] img:nth-child(2) { animation-delay: 3.5s; }

        /* Per-spot review fader on keyword-page tourist spots cards: smaller
           than the listing-row review fader, with a brand-blue left rule so
           it visually anchors as a quoted reader review. */
        .rg-spot-review-fader {
            position: relative;
            min-height: 100px;
            margin-top: 0.5rem;
            padding: 0.7rem 1rem;
            background: #f8fafc;
            border-left: 3px solid #2563eb;
            border-radius: 0.5rem;
        }
        .rg-spot-review-fader .rg-spot-review-slide {
            position: absolute;
            inset: 0.7rem 1rem;
            opacity: 0;
            transition: opacity 0.9s ease-in-out;
            animation: rg-spot-review-cycle 18s infinite;
        }
        .rg-spot-review-fader .rg-spot-review-slide[data-review-index="0"] { animation-delay: 0s; opacity: 1; }
        .rg-spot-review-fader .rg-spot-review-slide[data-review-index="1"] { animation-delay: 6s; }
        .rg-spot-review-fader .rg-spot-review-slide[data-review-index="2"] { animation-delay: 12s; }
        @keyframes rg-spot-review-cycle {
            0%, 28% { opacity: 1; }
            33%, 95% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Review fader: cycles through 3 review slides on each listing card,
           one visible at a time with a slow cross-fade. */
        .rg-review-fader { position: relative; }
        .rg-review-fader .rg-review-slide {
            position: absolute;
            inset: 1rem 1.75rem;
            opacity: 0;
            transition: opacity 0.9s ease-in-out;
            animation: rg-review-cycle 18s infinite;
        }
        .rg-review-fader .rg-review-slide[data-review-index="0"] { animation-delay: 0s; opacity: 1; }
        .rg-review-fader .rg-review-slide[data-review-index="1"] { animation-delay: 6s; }
        .rg-review-fader .rg-review-slide[data-review-index="2"] { animation-delay: 12s; }
        @keyframes rg-review-cycle {
            0%, 28% { opacity: 1; }
            33%, 95% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* CTA buttons inside listing rows,three distinct colored buttons.
           Each is a full-width block so the description column reads as a
           clean stack. */
        .rg-cta {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.55rem 0.85rem;
            border-radius: 0.55rem;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.1;
            text-align: center;
            transition: background-color 0.18s ease-out, color 0.18s ease-out, transform 0.12s ease-out, box-shadow 0.18s ease-out;
        }
        .rg-cta-reserve {
            background: #059669; color: #ffffff;
            box-shadow: 0 1px 2px rgba(5, 150, 105, 0.25);
        }
        .rg-cta-reserve:hover { background: #047857; }
        .rg-cta-amenities {
            background: #2563eb; color: #ffffff;
            box-shadow: 0 1px 2px rgba(37, 99, 235, 0.25);
        }
        .rg-cta-amenities:hover { background: #1d4ed8; }
        .rg-cta-experiences {
            background: #ea580c; color: #ffffff;
            box-shadow: 0 1px 2px rgba(234, 88, 12, 0.25);
        }
        .rg-cta-experiences:hover { background: #c2410c; }

        /* Image lazy-load fade */
        img[loading="lazy"] { opacity: 0; transition: opacity 0.5s ease-out; }
        img[loading="lazy"].loaded, img[loading="lazy"][complete] { opacity: 1; }

        /* FAQ smooth expand animation (native <details> + summary) */
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        details[open] > div, details[open] > p { animation: faqSlideDown 0.28s ease-out; }
        @keyframes faqSlideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Image hover zoom: smooth, silky scale. Previous setup used 0.4s
           ease-out which felt snappy and Tailwind's default `transition`
           utility on spot/festival card imgs runs at 150ms,way too fast,
           hence the jerky feel. We override globally with a longer duration
           and an ease-out-quart curve, plus GPU hints (will-change +
           backface-visibility) to prevent sub-pixel jitter during the scale. */
        .splide__slide img,
        figure img,
        .spot-card img,
        article.group img,
        a.group img {
            transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            will-change: transform;
            backface-visibility: hidden;
            transform: translateZ(0); /* force compositor layer */
        }
        .splide__slide:hover img { transform: scale(1.05); }
        .spot-card:hover img,
        article.group:hover img,
        a.group:hover img { transform: scale(1.05); }
        aside.field-notes a:hover img,
        aside:has(> a) a:hover img { transform: scale(1.05); }

        /* Reduced-motion respect: kill the zoom entirely for users who opt out */
        @media (prefers-reduced-motion: reduce) {
            .splide__slide:hover img,
            .spot-card:hover img,
            article.group:hover img,
            a.group:hover img,
            aside.field-notes a:hover img,
            aside:has(> a) a:hover img { transform: none; }
        }

        /* Blog visual decorations injected by BlogContentEnhancer */
        .prose hr.rg-divider {
            border: 0;
            border-top: 1px solid #e2e8f0;   /* slate-200 */
            margin: 2.5rem auto;
            max-width: 5rem;                  /* visual mid-section pause */
            opacity: 0.9;
        }
        .prose h2.rg-h2-iconized {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            scroll-margin-top: 5rem;          /* avoid sticky header overlap on TOC clicks */
        }
        .prose h2.rg-h2-iconized > span[aria-hidden] {
            color: #2563eb;                   /* brand-600 */
            flex: 0 0 auto;
            transform: translateY(2px);
        }
        .prose a.rg-loc-linked {
            text-decoration-style: dotted;
            text-decoration-color: #94a3b8;   /* slate-400 */
            text-underline-offset: 3px;
        }
        .prose a.rg-loc-linked:hover { color: #2563eb; }

        /* Blog content containers,give real top/bottom padding so the wrapped
           content (rg-tinted-section callouts, the appended "Where to stay
           near" block, accordions) doesn't visually slam against the
           container edges. User feedback: prior padding was too thin. */
        .prose aside.rg-tinted-section {
            padding: 1.75rem 1.5rem !important;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .prose aside.rg-tinted-section > .prose {
            margin: 0;
        }
        .prose aside.rg-tinted-section > .prose > :first-child {
            margin-top: 0;
        }
        .prose aside.rg-tinted-section > .prose > :last-child {
            margin-bottom: 0;
        }
        /* Where-to-stay block appended by BlogContentSeeder */
        .prose .rg-stay-block {
            margin-top: 2.5rem;
            padding: 1.75rem 1.5rem;
            border-radius: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .prose .rg-stay-block > :first-child { margin-top: 0; }
        .prose .rg-stay-block > :last-child { margin-bottom: 0; }
        /* Per-blog figures injected by the bespoke image pass */
        .prose figure.rg-figure {
            margin: 1.75rem 0;
        }
        .prose figure.rg-figure img {
            width: 100%;
            height: auto;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.08);
        }
        .prose figure.rg-figure figcaption {
            font-size: 0.78rem;
            color: #64748b;                   /* slate-500 */
            margin-top: 0.5rem;
            text-align: center;
            line-height: 1.4;
        }
        .prose figure.rg-figure figcaption a {
            color: #475569;                   /* slate-600 */
            text-decoration: underline;
            text-decoration-color: #cbd5e1;   /* slate-300 */
        }

        /* Hero slider: Splide's fixedHeight controls the track size, so the
           CSS just needs to round the outer corners and make the inner slide
           markup fill the carousel. No min-heights, no aspect-ratio fights. */
        .rg-hero-splide { border-radius: 1rem; overflow: hidden; position: relative; }
        .rg-hero-splide .splide__track { border-radius: 1rem; overflow: hidden; }
        .rg-hero-splide .splide__slide,
        .rg-hero-splide .splide__slide > div { height: 100%; width: 100%; }

        /* Card-style hero slider used by food / keyword pages. Lives in
           hero_slider blocks (style: card). Originally these styles
           shipped inline inside the seeded hero_html column; after
           migrating to a structured block we lift them to the layout so
           every page gets the same look. */
        .rg-area-hero { width: 100%; }
        .rg-area-hero__splide .splide__list { align-items: stretch; }
        .rg-area-hero__slide {
            position: relative;
            margin: 0;
            border-radius: 1rem;
            overflow: hidden;
            background: #f1f5f9;
        }
        .rg-area-hero__slide img {
            width: 100%;
            aspect-ratio: 21/9;
            object-fit: cover;
            display: block;
            height: auto;
        }
        @media (max-width: 640px) {
            .rg-area-hero__slide img { aspect-ratio: 16/10; }
        }
        .rg-area-hero__slide figcaption {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 1.1rem 1.5rem 1.3rem;
            background: linear-gradient(180deg, transparent 0%, rgba(15,23,42,0.93) 100%);
            color: #fff;
        }
        .rg-area-hero__slide figcaption strong {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
            font-weight: 700;
        }
        .rg-area-hero__slide figcaption span,
        .rg-area-hero__slide figcaption small {
            font-size: 0.85rem;
            opacity: 0.92;
            display: block;
            line-height: 1.35;
        }
        .rg-area-hero__splide .splide__arrow {
            background: rgba(15,23,42,0.75);
            width: 2.75rem;
            height: 2.75rem;
            opacity: 0.95;
        }
        .rg-area-hero__splide .splide__arrow:hover { background: rgb(37, 99, 235); }
        .rg-area-hero__splide .splide__arrow svg { fill: #fff; width: 1rem; height: 1rem; }
        .rg-area-hero__splide .splide__pagination { bottom: -1.5rem; }
        .rg-area-hero__splide .splide__pagination__page { background: #cbd5e1; opacity: 1; }
        .rg-area-hero__splide .splide__pagination__page.is-active {
            background: #fbbf24;
            transform: scale(1.3);
        }

        /* H1 line-height set globally to 60px (user request). On desktop
           where H1 hits text-5xl (~48px) this gives a 1.25 ratio which
           reads as airy-not-cramped; on mobile where H1 is ~30px it
           reads as generous spacing, which the user prefers over the
           default tight leading. */
        h1 { line-height: 60px; }

        /* H2 spacing,tightened globally. Previous values pushed H2s way
           down because Tailwind prose defaults compound with section gaps and
           the page-scoped CSS. We now override prose + page-scope with a near-
           zero top margin; preceding block elements provide the visual gap. */
        .prose h2,
        article h2,
        section h2 { margin-top: 0.5rem; margin-bottom: 0.75rem; }
        .prose h3 { margin-top: 1rem; margin-bottom: 0.5rem; }
        .prose > section.not-prose + h2,
        .prose > h2 + section.not-prose,
        .prose > section.not-prose:first-of-type { margin-top: 0.75rem; }
        .prose > h2:first-child,
        article > h2:first-child { margin-top: 0; }

        /* Alternating soft tints on keyword + blog `rg-tinted` containers so
           the page doesn't read as a single flat white column end-to-end. */
        .rg-tinted-1 { background: #f8fafc; border-color: #e2e8f0; } /* slate-50 */
        .rg-tinted-2 { background: #fff7ed; border-color: #fed7aa; } /* orange-50 */
        .rg-tinted-3 { background: #ecfeff; border-color: #cffafe; } /* cyan-50  */
        .rg-tinted-4 { background: #fdf4ff; border-color: #f5d0fe; } /* fuchsia-50 */

        /* Collapsible TL;DR + WWWW sections. Each is an independent <details>
           with a JS-animated max-height transition (Splide-style smooth open
           and close,native <details> snaps instantly, which feels janky).
           The chevron icon rotates 180° when open. */
        .rg-accordion { box-shadow: 0 1px 2px rgba(15,23,42,0.04); transition: box-shadow .2s ease, border-color .2s ease; }
        .rg-accordion:hover { border-color: #cbd5e1; }
        .rg-accordion > summary { list-style: none; }
        .rg-accordion > summary::-webkit-details-marker { display: none; }
        .rg-accordion[open] > summary .rg-accordion-chevron { transform: rotate(180deg); }
        .rg-accordion-body {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.42s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .rg-accordion[open] .rg-accordion-body {
            max-height: var(--rg-accordion-target, 1500px);
        }
        .rg-accordion[data-anim="closing"] .rg-accordion-body { max-height: 0; }
        /* Subtle inner fade so content doesn't pop */
        .rg-accordion-body-inner {
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity .35s ease-out .08s, transform .35s ease-out .08s;
        }
        .rg-accordion[open] .rg-accordion-body-inner {
            opacity: 1;
            transform: translateY(0);
        }

        /* Social share row */
        .rg-share-btn { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem .75rem; border-radius:.5rem; font-size:.8rem; font-weight:600; border:1px solid #e2e8f0; color:#475569; background:#fff; transition:background .15s, color .15s, border-color .15s; }
        .rg-share-btn:hover { background:#f8fafc; border-color:#cbd5e1; color:#0f172a; }
        .rg-share-btn svg { width: 1rem; height: 1rem; }

        /* Star rating display + input */
        .rg-stars { display:inline-flex; gap:.1rem; color:#f59e0b; font-size:.9rem; line-height:1; }
        .rg-star-input { display:inline-flex; gap:.25rem; flex-direction:row-reverse; }
        .rg-star-input input { display:none; }
        .rg-star-input label { cursor:pointer; color:#cbd5e1; font-size:1.4rem; line-height:1; transition:color .15s; }
        .rg-star-input label:hover,
        .rg-star-input label:hover ~ label,
        .rg-star-input input:checked ~ label { color:#f59e0b; }

        /* Top progress bar shown briefly during navigation */
        #rgTopBar { position: fixed; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, #2563eb, transparent); transform: scaleX(0); transform-origin: left; z-index: 100; pointer-events: none; transition: transform 0.6s ease-out; }
        #rgTopBar.active { transform: scaleX(1); }

        /* View Transitions API (Chromium 111+) */
        @view-transition { navigation: auto; }
        ::view-transition-old(root) { animation: rgFadeOut 0.2s ease-in both; }
        ::view-transition-new(root) { animation: rgFadeIn 0.35s ease-out both; }
        @keyframes rgFadeOut { to { opacity: 0; transform: translateY(-6px); } }
    </style>

    @stack('head')
    @yield('jsonld')
</head>
<body class="bg-white text-slate-800 antialiased min-h-screen flex flex-col">

<div id="rgTopBar"></div>

<header class="border-b border-slate-200 bg-white sticky top-0 z-30 backdrop-blur bg-white/85">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg">
            <span class="text-2xl">🏖️</span>
            <span>{{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }}</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
            <a href="{{ url('/destinations') }}" class="hover:text-brand-600">Destinations</a>
            <a href="{{ url('/food-trip') }}" class="hover:text-brand-600">Food Trip</a>
            <a href="{{ route('activities.index') }}" class="hover:text-brand-600">Activities</a>
            <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a>
            <a href="{{ route('about') }}" class="hover:text-brand-600">About</a>
            <a href="{{ route('contact') }}" class="hover:text-brand-600">Contact</a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="px-3 py-1.5 rounded-md bg-brand-600 text-white hover:bg-brand-700">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-brand-600">Sign in</a>
                <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-md bg-brand-600 text-white hover:bg-brand-700">List your resort</a>
            @endauth
        </nav>
        <button class="md:hidden" onclick="document.getElementById('mobileNav').classList.toggle('hidden')" aria-label="Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div id="mobileNav" class="hidden md:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-3 space-y-2 text-sm font-medium">
            <a href="{{ route('home') }}" class="block">Home</a>
            <a href="{{ url('/destinations') }}" class="block">Destinations</a>
            <a href="{{ url('/food-trip') }}" class="block">Food Trip</a>
            <a href="{{ route('activities.index') }}" class="block">Activities</a>
            <a href="{{ route('blog.index') }}" class="block">Blog</a>
            <a href="{{ route('about') }}" class="block">About</a>
            <a href="{{ route('contact') }}" class="block">Contact</a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="block text-brand-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block">Sign in</a>
                <a href="{{ route('register') }}" class="block text-brand-600">List your resort</a>
            @endauth
        </div>
    </div>
</header>

<main class="flex-1">
    @yield('content')
</main>

@php
    $footerRegions = \Illuminate\Support\Facades\Cache::remember('footer_regions_v2_resort', 600, function () {
        $meta = \App\Http\Controllers\DestinationsController::clusterMetadata();
        return \App\Models\RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->get()
            ->groupBy('cluster_tag')
            ->map(function ($kws, $slug) use ($meta) {
                if (!isset($meta[$slug])) return null;
                return [
                    'slug' => $slug,
                    'name' => $meta[$slug]['name'],
                    'top_keywords' => $kws->sortByDesc('search_volume_monthly')->take(4)->values(),
                    'total_volume' => $kws->sum('search_volume_monthly'),
                ];
            })
            ->filter()
            ->sortByDesc('total_volume')
            ->take(6)
            ->values();
    });
    $footerTopKeywords = \Illuminate\Support\Facades\Cache::remember('footer_top_keywords_v2_resort', 600, function () {
        return \App\Models\RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->limit(20)
            ->get();
    });
    // Full RESORT keyword cloud grouped by cluster,every page's footer
    // renders the complete internal-link surface for resort-side crawlability.
    // Food keywords have their own /food-trip index and are intentionally
    // kept out of the destination/resort footer cloud.
    $footerAllKeywords = \Illuminate\Support\Facades\Cache::remember('footer_all_keywords_v3_resort', 600, function () {
        return \App\Models\RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->get(['slug', 'phrase', 'cluster_tag', 'search_volume_monthly'])
            ->groupBy('cluster_tag');
    });
    $clusterLabels = [
        'antipolo' => 'Rizal', 'rizal' => 'Rizal',
        'cavite' => 'Cavite', 'tagaytay' => 'Cavite',
        'batangas' => 'Batangas',
        'laguna' => 'Laguna',
        'quezon' => 'Quezon',
        'bulacan' => 'Bulacan',
        'pampanga' => 'Pampanga',
        'metro-manila' => 'Metro Manila',
        'bicol' => 'Bicol',
        'north-luzon' => 'North Luzon (Pangasinan + La Union + Ilocos)',
        'mindanao' => 'Mindanao',
        'visayas' => 'Visayas',
        'palawan' => 'Palawan',
        'other' => 'Other',
    ];
@endphp

<footer class="bg-slate-900 text-slate-300 mt-16 mt-auto">
    {{-- Mega-footer: regional links (sitewide internal linking for SEO) --}}
    @if($footerRegions->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8 border-b border-slate-800">
            <div class="flex items-end justify-between mb-5 flex-wrap gap-2">
                <h4 class="text-white font-bold text-lg">Browse by region</h4>
                <a href="{{ url('/destinations') }}" class="text-sm text-brand-300 hover:text-white">View all destinations &rarr;</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6 text-sm">
                @foreach($footerRegions as $region)
                    <div>
                        <h5 class="text-white font-semibold mb-2">
                            <a href="{{ route('destinations.cluster', $region['slug']) }}" class="hover:text-brand-300">{{ $region['name'] }}</a>
                        </h5>
                        <ul class="space-y-1">
                            @foreach($region['top_keywords'] as $k)
                                <li><a href="{{ url($k->slug) }}" class="hover:text-white capitalize">{{ $k->phrase }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Standard footer --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid md:grid-cols-4 gap-8 text-sm">
        <div>
            <h4 class="text-white font-bold mb-3 flex items-center gap-2">🏖️ {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }}</h4>
            <p>{{ \App\Models\RgSetting::get('site_tagline', '') }}</p>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">Discover</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                <li><a href="{{ url('/destinations') }}" class="hover:text-white">All destinations</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-white">About</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">For Resort Owners</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('register') }}" class="hover:text-white">List your resort</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">Sign in</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Get help</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">Legal</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('terms') }}" class="hover:text-white">Terms of Service</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
            </ul>
        </div>
    </div>

    {{-- All keyword pages grouped by cluster,full internal-link surface
         for site-wide crawlability. Rendered inside a <details> so the
         visual footer stays compact while the HTML is fully indexable. --}}
    @if($footerAllKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer select-none mb-3">
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">All destinations on Resort Guru PH</p>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="group-open:hidden">Show all</span>
                            <span class="hidden group-open:inline">Hide</span>
                            <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </summary>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5 mt-2">
                        @foreach($footerAllKeywords->sortKeys() as $cluster => $kws)
                            @php $label = $clusterLabels[$cluster] ?? ucwords(str_replace('-', ' ', $cluster)); @endphp
                            <div>
                                <h6 class="text-white font-semibold mb-1.5 text-sm">{{ $label }}</h6>
                                <div class="flex flex-wrap gap-x-2 gap-y-0.5 text-[11px] leading-snug">
                                    @foreach($kws as $k)
                                        <a href="{{ url($k->slug) }}" class="text-slate-400 hover:text-white capitalize">{{ $k->phrase }}</a>
                                        @if(!$loop->last)<span class="text-slate-600">·</span>@endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    @endif

    {{-- Most popular destinations strip (compact top-20 above the fold) --}}
    @if($footerTopKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Most popular keyword pages</p>
                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs">
                    @foreach($footerTopKeywords as $k)
                        <a href="{{ url($k->slug) }}" class="text-slate-400 hover:text-white capitalize">{{ $k->phrase }}</a>
                        @if(!$loop->last)<span class="text-slate-600">·</span>@endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }}. All rights reserved.</p>
            <p>Made with love in the Philippines.</p>
        </div>
    </div>
</footer>

@stack('scripts')

{{-- UX animations: reveal-on-scroll, smooth nav, image fade-in --}}
<script>
(function() {
    // 1. Reveal-on-scroll (IntersectionObserver)
    const revealEls = document.querySelectorAll('.rg-reveal, .rg-stagger');
    if ('IntersectionObserver' in window && revealEls.length > 0) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        revealEls.forEach(el => io.observe(el));
    } else {
        // No IO support,just show everything
        revealEls.forEach(el => el.classList.add('is-visible'));
    }

    // 2. Image lazy-load fade-in
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        if (img.complete && img.naturalHeight !== 0) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
            img.addEventListener('error', () => img.classList.add('loaded'), { once: true });
        }
    });

    // 3. Top progress bar on internal navigation
    const topBar = document.getElementById('rgTopBar');
    if (topBar) {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank') return;
            try {
                const url = new URL(href, location.href);
                if (url.origin !== location.origin) return;
                if (url.pathname === location.pathname && url.search === location.search) return;
                // Trigger progress bar
                topBar.classList.add('active');
            } catch (_) {}
        });
        // Hide bar when navigated away (handled by pageshow on bfcache return)
        window.addEventListener('pageshow', () => topBar.classList.remove('active'));
    }

    // 4. Smooth-scroll for ALL same-page anchor links (fallback to CSS scroll-behavior)
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href').slice(1);
            if (!id) return;
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            history.replaceState(null, '', '#' + id);
        });
    });
})();
</script>
</body>
</html>
