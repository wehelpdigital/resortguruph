<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Light-only site: stops Chrome/Edge "Auto Dark Mode" from inverting images (green photos -> purple). --}}
    <meta name="color-scheme" content="light">
    <meta name="darkreader-lock">

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

    <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">

    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eaf2f8', 100: '#d4e6f1', 200: '#a9cce3', 300: '#7fb3d5', 400: '#5499c7', 500: '#3498db', 600: '#2980b9', 700: '#2471a3', 800: '#1f618b', 900: '#1a5276' },
                        blue:  { 50: '#eaf2f8', 100: '#d4e6f1', 200: '#a9cce3', 300: '#7fb3d5', 400: '#5499c7', 500: '#3498db', 600: '#2980b9', 700: '#2471a3', 800: '#1f618b', 900: '#1a5276' },
                        rose:  { 50: '#f9ebea', 100: '#f2d7d5', 200: '#e6b0aa', 300: '#d98880', 400: '#cd6155', 500: '#e74c3c', 600: '#c0392b', 700: '#a93226', 800: '#922b21', 900: '#7b241c' },
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
        @font-face {
            font-family: 'Tahu';
            src: url('{{ asset('fonts/Tahu.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        .font-brand { font-family: 'Tahu', cursive; }
        @keyframes rgCtaArrow { 0%, 100% { transform: translateX(0); } 50% { transform: translateX(5px); } }
        .rg-cta-arrow { animation: rgCtaArrow 1.2s ease-in-out infinite; }
        a:hover > .rg-cta-arrow { animation-duration: .7s; }
        @media (prefers-reduced-motion: reduce) { .rg-cta-arrow { animation: none; } }
        /* Water-ripple border pulse for the corner check badge */
        @keyframes rgRipple {
            0%   { transform: scale(1);   opacity: .6; }
            100% { transform: scale(2.1); opacity: 0; }
        }
        .rg-ripple::before, .rg-ripple::after {
            content: ''; position: absolute; inset: 0;
            border-radius: 9999px; border: 2px solid #10b981;
            animation: rgRipple 2.6s ease-out infinite; pointer-events: none;
        }
        .rg-ripple::after { animation-delay: 1.3s; }
        @media (prefers-reduced-motion: reduce) {
            .rg-ripple::before, .rg-ripple::after { animation: none; display: none; }
        }
        :root { color-scheme: light; }
        html { scroll-behavior: smooth; }
        /* Kill the horizontal scrollbar and stop centered content from
           jumping sideways:
           - full-bleed `width:100vw` sections are ~one scrollbar-width wider
             than the content area; `overflow-x: hidden` on the root scroller
             trims that overflow (the sticky header still works because html
             stays the vertical scroll container).
           - `overflow-y: scroll` always shows the vertical scrollbar, so the
             content width never changes when the page gets taller/shorter
             (e.g. opening an accordion card) — no horizontal shift. */
        html { overflow-x: hidden; overflow-y: scroll; }
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
           default tight leading. The !important is necessary because
           Tailwind's `leading-[*]` utility (specificity 0,1,0) on most
           page H1s beats a plain element selector (0,0,1). The user
           explicitly asked for this to apply to ALL H1 titles, so the
           heavy-handed override is the right call. */
        h1 { line-height: 60px !important; }

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

@php
    // Active-state matching for the secondary pill bar. Each "pillar"
    // section spans more than just its hub URL — Where to Go also covers
    // /destinations/{cluster} and individual keyword resort pages; What
    // to Do also covers fiestas. We match by request path prefix.
    $activeWhereToGo = request()->is('tourist-spots-destinations-philippines*')
        || (request()->path() !== '/' && \App\Models\RgKeyword::where('slug', request()->path())->where('category', 'resort')->exists());
    $activeWhereToEat = request()->is('food-trip*')
        || (request()->path() !== '/' && \App\Models\RgKeyword::where('slug', request()->path())->where('category', 'food')->exists());
    $activeWhatToDo = request()->is('philippine-tourist-activities-adventures-what-to-do')
        || request()->is('philippine-fiestas-festivals-guide')
        || request()->is('fiestas/*');
    $activeWhatToEat = request()->is('filipino-food-dishes-what-to-eat');
    $activeWhatToBuy = request()->is('philippine-souvenirs-pasalubong-what-to-buy');
    $activeCultures = request()->is('philippine-tribes-ethnic-groups-cultures-to-meet');
@endphp

<header class="border-b border-slate-200 bg-white sticky top-0 z-50 backdrop-blur bg-white/85">
    {{-- Level 1: brand + utility nav --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 h-20">
        <a href="{{ route('home') }}" class="flex items-center gap-0 font-bold text-lg shrink-0">
            <img src="{{ asset('images/logo.webp') }}" alt="{{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} logo" class="h-14 sm:h-16 w-auto" width="160" height="100">
            <span class="font-brand text-4xl sm:text-5xl leading-none font-light pt-2"><span style="color:#2980b9">Tourist</span><span style="color:#c0392b">Guide</span><span style="color:#f39c12">.Ph</span></span>
        </a>

        {{-- Dynamic typeahead search (server-suggested, like the homepage
             hero search). Sits between the logo and the nav on md+ screens. --}}
        <div class="hidden md:block relative flex-1 max-w-xl mx-2 lg:mx-4" id="rgNavSearch">
            <div class="flex items-center rounded-full border border-slate-300 bg-white px-4 h-10 focus-within:border-brand-600 focus-within:ring-2 focus-within:ring-brand-600/20 transition-colors">
                <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input id="rgNavSearchInput" type="text" autocomplete="off" spellcheck="false" placeholder="Search" aria-label="Search the site" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="rgNavSearchPanel" class="ml-2 w-full bg-transparent border-0 appearance-none outline-none focus:ring-0 text-sm text-slate-800 placeholder-slate-400">
                <button id="rgNavSearchClear" type="button" aria-label="Clear search" class="hidden text-slate-400 hover:text-slate-600 shrink-0"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
            </div>
            <div id="rgNavSearchPanel" class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-xl ring-1 ring-slate-200 overflow-hidden hidden" style="z-index:60;max-height:70vh;overflow-y:auto" role="listbox" aria-label="Search suggestions"></div>
        </div>
        <style>#rgNavSearchPanel{padding:.4rem;min-width:30rem;max-width:calc(100vw - 1rem)}@media(min-width:1024px){#rgNavSearchPanel{min-width:42rem}}.rg-ns-group{font-size:.66rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#94a3b8;padding:.6rem .75rem .3rem}.rg-ns-item{border-radius:.6rem}.rg-ns-item:hover{background:#f8fafc}.rg-ns-item.is-active{background:#eff6ff}.rg-ns-ico{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9px;flex-shrink:0;overflow:hidden}</style>
        <script>
        (function(){
            var wrap=document.getElementById('rgNavSearch'); if(!wrap) return;
            var input=document.getElementById('rgNavSearchInput');
            var panel=document.getElementById('rgNavSearchPanel');
            var clear=document.getElementById('rgNavSearchClear');
            var endpoint='{{ route('search.suggest') }}';
            var timer=null, active=-1, items=[];
            var TYPE={destination:{k:'pin',c:'#2563eb',bg:'#dbeafe',l:'Destination'},resort:{k:'bed',c:'#059669',bg:'#d1fae5',l:'Resort'},restaurant:{k:'cup',c:'#dc2626',bg:'#fee2e2',l:'Food'},spot:{k:'cam',c:'#7c3aed',bg:'#ede9fe',l:'Tourist spot'},region:{k:'map',c:'#d97706',bg:'#fef3c7',l:'Region'},blog:{k:'doc',c:'#475569',bg:'#e2e8f0',l:'Blog'}};
            var ICON={pin:'<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',bed:'<path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/>',cup:'<path d="M10 2v2"/><path d="M14 2v2"/><path d="M6 2v2"/><path d="M16 8a1 1 0 0 1 1 1v8a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V9a1 1 0 0 1 1-1h14a4 4 0 1 1 0 8h-1"/>',cam:'<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>',map:'<path d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z"/><path d="M9 3v15"/><path d="M15 6v15"/>',doc:'<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v6h6"/>'};
            function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];});}
            function svg(k){return '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'+(ICON[k]||ICON.pin)+'</svg>';}
            function open(){panel.classList.remove('hidden');input.setAttribute('aria-expanded','true');}
            function close(){panel.classList.add('hidden');input.setAttribute('aria-expanded','false');active=-1;}
            var GROUP_ORDER=['region','destination','resort','restaurant','spot','blog'];
            var GROUP_LABEL={region:'Regions',destination:'Destinations',resort:'Stays',restaurant:'Food finds',spot:'Tourist spots',blog:'Blog'};
            function rowHtml(it,idx){
                var tp=TYPE[it.type]||{k:'pin',c:'#475569',bg:'#e2e8f0'};
                var thumb=it.image?'<span class="rg-ns-ico" style="background:#f1f5f9"><img src="'+esc(it.image)+'" alt="" style="width:100%;height:100%;object-fit:cover;display:block"></span>':'<span class="rg-ns-ico" style="background:'+tp.bg+';color:'+tp.c+'">'+svg(tp.k)+'</span>';
                return '<a href="'+esc(it.url)+'" class="rg-ns-item flex items-center gap-3 px-3 py-2 no-underline" role="option" data-idx="'+idx+'">'+thumb+
                    '<span class="min-w-0 flex-1"><span class="block text-sm font-semibold text-slate-900 truncate">'+esc(it.label)+'</span>'+
                    (it.sub?'<span class="block text-xs text-slate-500 truncate">'+esc(it.sub)+'</span>':'')+'</span></a>';
            }
            function render(res,q){
                items=res||[];
                if(!items.length){panel.innerHTML='<div class="px-4 py-4 text-sm text-slate-500">No matches for “'+esc(q)+'”</div>';open();return;}
                var groups={};
                items.forEach(function(it){var t=it.type||'other';(groups[t]=groups[t]||[]).push(it);});
                var order=GROUP_ORDER.slice();
                Object.keys(groups).forEach(function(t){if(order.indexOf(t)<0)order.push(t);});
                var h='', flat=0; items=[];
                order.forEach(function(t){
                    var arr=groups[t]; if(!arr||!arr.length)return;
                    h+='<div class="rg-ns-group">'+esc(GROUP_LABEL[t]||t)+'</div>';
                    arr.forEach(function(it){h+=rowHtml(it,flat);items.push(it);flat++;});
                });
                panel.innerHTML=h; active=-1; open();
            }
            function fetchSuggest(q){
                fetch(endpoint+'?q='+encodeURIComponent(q),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
                    .then(function(r){return r.json();})
                    .then(function(d){ if(input.value.trim()===q) render(d.results,q); })
                    .catch(function(){});
            }
            function hl(rows){rows.forEach(function(r,i){r.classList.toggle('is-active',i===active);if(i===active)r.scrollIntoView({block:'nearest'});});}
            input.addEventListener('input',function(){
                var q=input.value.trim();
                clear.classList.toggle('hidden', q==='');
                if(timer)clearTimeout(timer);
                if(q.length<2){close();return;}
                timer=setTimeout(function(){fetchSuggest(q);},180);
            });
            input.addEventListener('keydown',function(e){
                var rows=panel.querySelectorAll('.rg-ns-item');
                if(e.key==='ArrowDown'){e.preventDefault();if(!rows.length)return;active=(active+1)%rows.length;hl(rows);}
                else if(e.key==='ArrowUp'){e.preventDefault();if(!rows.length)return;active=(active-1+rows.length)%rows.length;hl(rows);}
                else if(e.key==='Enter'){var sel=active>=0?rows[active]:rows[0];if(sel){window.location.href=sel.getAttribute('href');}}
                else if(e.key==='Escape'){close();}
            });
            input.addEventListener('focus',function(){if(items.length&&input.value.trim().length>=2)open();});
            clear.addEventListener('click',function(){input.value='';clear.classList.add('hidden');close();input.focus();});
            document.addEventListener('click',function(e){if(!wrap.contains(e.target))close();});
        })();
        </script>

        <nav class="hidden lg:flex items-center gap-5 text-sm font-medium shrink-0">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-brand-600 font-semibold' : 'hover:text-brand-600' }}">Home</a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="px-3 py-1.5 rounded-md bg-brand-600 text-white hover:bg-brand-700 whitespace-nowrap">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-brand-600">Sign in</a>
                <a href="{{ route('create-your-adventure') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-red-500 text-white font-bold hover:bg-red-600 transition whitespace-nowrap">Create Your Adventure for Free<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="shrink-0"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a>
                <a href="{{ route('become-a-partner') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-brand-600 text-white font-bold hover:bg-brand-700 transition whitespace-nowrap"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="shrink-0"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>Become a Partner</a>
            @endauth
        </nav>
        <button class="lg:hidden shrink-0" onclick="document.getElementById('mobileNav').classList.toggle('hidden')" aria-label="Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    {{-- Level 2: tagged pill bar for the three pillar sections. Each
         pill has its own color identity (emerald for places, amber for
         food, indigo for activities) so the user reads them as distinct
         tracks rather than three same-looking links. Active state
         darkens the pill so it reads as the current section. Stays
         visible on mobile with a horizontal scroll if needed. --}}
    <div class="border-t border-slate-100 bg-slate-50/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center gap-2 overflow-x-auto">
            <span class="hidden sm:inline text-[10px] uppercase tracking-[0.18em] font-bold text-slate-900 shrink-0 mr-1">Discover Philippines</span>

            <a href="{{ route('destinations.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ $activeWhereToGo ? 'bg-emerald-700 text-white ring-2 ring-emerald-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>
                Where to Go
            </a>

            <a href="{{ url('/food-trip') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ $activeWhereToEat ? 'bg-amber-700 text-white ring-2 ring-amber-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-amber-600 text-white hover:bg-amber-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.5 2v7"/><path d="M8 2v7"/><path d="M11.5 2v7"/><path d="M4.5 9h7"/><path d="M8 9v13"/><path d="M17 2c-1.7 0-3 2-3 4.5S15.3 11 17 11s3-2 3-4.5S18.7 2 17 2z"/><path d="M17 11v11"/></svg>
                Where to Eat
            </a>

            <a href="{{ route('foods.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ $activeWhatToEat ? 'bg-rose-700 text-white ring-2 ring-rose-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-rose-600 text-white hover:bg-rose-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 11h16a8 8 0 0 1-16 0z"/><path d="M9 7V4M12 7V4M15 7V4"/></svg>
                What to Eat
            </a>

            <a href="{{ route('activities.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ $activeWhatToDo ? 'bg-indigo-700 text-white ring-2 ring-indigo-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/></svg>
                What to Do
            </a>

            <a href="{{ route('buys.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ $activeWhatToBuy ? 'bg-violet-700 text-white ring-2 ring-violet-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-violet-600 text-white hover:bg-violet-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1.5 12.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 8z"/><path d="M9 8V5a3 3 0 0 1 6 0v3"/></svg>
                What to Buy
            </a>

            <a href="{{ route('cultures.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ $activeCultures ? 'bg-teal-700 text-white ring-2 ring-teal-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-teal-600 text-white hover:bg-teal-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21v-1a7 7 0 0 1 14 0v1"/></svg>
                Cultures to Meet
            </a>

            {{-- Partner Directory — same size and a flat fill like the other
                 section pills on this line; links to the public directory. --}}
            <a href="{{ route('partner-directory') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap shrink-0 transition
                      {{ request()->is('partner-directory') ? 'bg-brand-700 text-white ring-2 ring-brand-400 ring-offset-2 ring-offset-slate-50 shadow-md' : 'bg-brand-600 text-white hover:bg-brand-700' }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="M4 9h16 M9 9v11"/></svg>
                Partner Directory
            </a>
        </div>
    </div>

    {{-- Mobile menu (level-1 utility links only — the pillar pills
         above stay visible at all viewport widths). --}}
    <div id="mobileNav" class="hidden lg:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-3 space-y-3 text-sm font-medium">
            <a href="{{ route('home') }}" class="block {{ request()->routeIs('home') ? 'text-brand-600 font-semibold' : '' }}">Home</a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="block text-brand-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block">Sign in</a>
                <a href="{{ route('create-your-adventure') }}" class="flex items-center justify-center gap-1.5 text-center px-4 py-2.5 rounded-full bg-red-500 text-white font-bold hover:bg-red-600 transition">Create Your Adventure for Free<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="shrink-0"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a>
                <a href="{{ route('become-a-partner') }}" class="flex items-center justify-center gap-1.5 text-center px-4 py-2.5 rounded-full bg-brand-600 text-white font-bold hover:bg-brand-700 transition"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="shrink-0"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>Become a Partner</a>
            @endauth
        </div>
    </div>
</header>

<main class="flex-1">
    @yield('content')
</main>

@php
    $footerRegions = \Illuminate\Support\Facades\Cache::remember('footer_regions_v3_resort', 600, function () {
        $meta = \App\Http\Controllers\DestinationsController::clusterMetadata();
        return \App\Models\RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->get()
            ->groupBy(fn($k) => \App\Support\RegionResolver::resolve($k->cluster_tag, $k->phrase))
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
    $footerAllKeywords = \Illuminate\Support\Facades\Cache::remember('footer_all_keywords_v4_resort', 600, function () {
        return \App\Models\RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->get(['slug', 'phrase', 'cluster_tag', 'search_volume_monthly'])
            ->groupBy(fn($k) => \App\Support\RegionResolver::resolve($k->cluster_tag, $k->phrase));
    });
    // Footer hashtag cloud — all active tags from rg_tags (populated by
    // RgTagsSeeder from the published keyword pages, editable later in admin).
    $footerHashtags = \Illuminate\Support\Facades\Cache::remember('footer_hashtags_v2_resort', 600, function () {
        if (!\Illuminate\Support\Facades\Schema::hasTable('rg_tags')) return collect();
        return \App\Models\RgTag::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderByDesc('search_volume_monthly')
            ->get(['tag', 'slug']);
    });
@endphp

<footer class="bg-slate-900 text-slate-300 mt-16 mt-auto">
    {{-- Mega-footer: regional links (sitewide internal linking for SEO) --}}
    @if($footerRegions->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8 border-b border-slate-800">
            <div class="flex items-end justify-between mb-5 flex-wrap gap-2">
                <h4 class="text-white font-bold text-lg">Browse by region</h4>
                <a href="{{ route('destinations.index') }}" class="text-sm text-brand-300 hover:text-white">View all destinations &rarr;</a>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid md:grid-cols-[2fr_auto_auto_auto] gap-x-12 gap-y-8 text-sm">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-0 mb-4">
                <img src="{{ asset('images/logo.webp') }}" alt="{{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }}" class="h-11 w-auto shrink-0" style="filter:brightness(0) invert(1)">
                <span class="font-brand text-2xl sm:text-3xl leading-none font-light pt-1 text-white">TouristGuide.Ph</span>
            </a>
            <p class="text-slate-400 leading-relaxed">Your one-stop guide to exploring the Philippines. Find where to stay, what to eat, and where to go next across more than 7,000 islands, with picks from travelers who have actually been there.</p>
            <div class="mt-5 grid grid-cols-2 gap-x-6 gap-y-1.5 max-w-xs">
                <a href="{{ route('about') }}" class="hover:text-white">About</a>
                <a href="{{ route('about.logo') }}" class="hover:text-white">About the Logo</a>
            </div>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">Discover</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                <li><a href="{{ route('destinations.index') }}" class="hover:text-white">All destinations</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">For Business Owners</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('register') }}" class="hover:text-white">Become a Partner</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">Sign in</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">Legal</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('terms') }}" class="hover:text-white">Terms of Service</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                <li><a href="{{ route('sitemap.page') }}" class="hover:text-white">Sitemap</a></li>
            </ul>
        </div>
    </div>

    {{-- Hashtag cloud: every active tag from rg_tags rendered as small plain
         "#tag" text links (no pills). Collapsed by default behind a <details>
         toggle, mirroring the "All destinations" block below. --}}
    @if($footerHashtags->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <details class="group rg-details">
                    <summary class="flex items-center justify-between cursor-pointer select-none">
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-brand-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                            Trending tags
                        </p>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="group-open:hidden">Show all</span>
                            <span class="hidden group-open:inline">Hide</span>
                            <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </summary>
                    <div class="flex flex-wrap gap-x-2.5 gap-y-1 text-xs leading-5 text-slate-500 pt-2">
                        @foreach($footerHashtags as $h)<a href="{{ url($h['slug']) }}" class="text-slate-400 hover:text-white">#{{ $h['tag'] }}</a>@endforeach
                    </div>
                </details>
            </div>
        </div>
    @endif

    {{-- All keyword pages grouped by cluster,full internal-link surface
         for site-wide crawlability. Rendered inside a <details> so the
         visual footer stays compact while the HTML is fully indexable. --}}
    @if($footerAllKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <details class="group rg-details">
                    <summary class="flex items-center justify-between cursor-pointer select-none">
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">All destinations on Tourist Guide Ph</p>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="group-open:hidden">Show all</span>
                            <span class="hidden group-open:inline">Hide</span>
                            <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </summary>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-1 pt-2 items-start">
                        @foreach($footerAllKeywords->sortKeys() as $cluster => $kws)
                            @php $label = \App\Support\RegionResolver::label($cluster); @endphp
                            {{-- Groups sit in a responsive column grid; an open group
                                 breaks out to the full container width (see rg-region
                                 CSS) and lays its destinations out in multiple columns. --}}
                            <details class="group/cl rg-details rg-region border-b border-slate-800/70">
                                <summary class="flex items-center justify-between cursor-pointer select-none py-2">
                                    <h6 class="text-white font-semibold text-sm flex items-center gap-2">
                                        {{ $label }}
                                        <span class="text-[11px] font-normal text-slate-500">{{ $kws->count() }}</span>
                                    </h6>
                                    <svg class="w-3 h-3 text-slate-500 shrink-0 transition-transform group-open/cl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </summary>
                                <ul class="list-none columns-2 sm:columns-3 lg:columns-4 gap-x-6 pt-1 pb-3 text-[11px] leading-relaxed">
                                    @foreach($kws as $k)
                                        <li class="break-inside-avoid"><a href="{{ url($k->slug) }}" class="block text-slate-400 hover:text-white capitalize">{{ $k->phrase }}</a></li>
                                    @endforeach
                                </ul>
                            </details>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    @endif

    {{-- Most popular destinations strip (compact top-20 above the fold) --}}
    @if($footerTopKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Check what's popular</p>
                <ul class="list-none columns-2 sm:columns-3 lg:columns-4 gap-x-6 text-xs leading-relaxed">
                    @foreach($footerTopKeywords as $k)
                        <li class="break-inside-avoid"><a href="{{ url($k->slug) }}" class="block py-0.5 text-slate-400 hover:text-white capitalize">{{ $k->phrase }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }}. All rights reserved.</p>
            <p>Made with love in the Philippines.</p>
        </div>
    </div>

    {{-- Smooth height animation for the collapsible footer sections. Native
         <details> snaps open/closed; this animates both directions by tweening
         the panel height, and falls back to an instant toggle when motion is
         reduced. Scoped to footer .rg-details so nothing else is affected. --}}
    @verbatim
    <style>
      footer details.rg-details > summary { list-style: none; }
      footer details.rg-details > summary::-webkit-details-marker { display: none; }
      footer details.rg-details > summary + * { overflow: hidden; will-change: height; transition: height .3s cubic-bezier(.4, 0, .2, 1); }
      @media (prefers-reduced-motion: reduce) { footer details.rg-details > summary + * { transition: none; } }
      /* An open region breaks out of its grid column to span the whole row,
         then returns to a single cell when collapsed. */
      footer details.rg-region[open] { grid-column: 1 / -1; }
    </style>
    <script>
    (function () {
        var motionOK = !(window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches);
        document.querySelectorAll('footer details.rg-details').forEach(function (d) {
            var summary = d.querySelector(':scope > summary');
            if (!summary) return;
            var panel = summary.nextElementSibling;
            if (!panel) return;
            summary.addEventListener('click', function (e) {
                e.preventDefault();
                if (d.dataset.animating) return;
                if (!motionOK) { d.open = !d.open; return; }
                d.dataset.animating = '1';
                if (d.open) {
                    panel.style.height = panel.scrollHeight + 'px';
                    panel.getBoundingClientRect();
                    panel.style.height = '0px';
                    panel.addEventListener('transitionend', function te(ev) {
                        if (ev.propertyName !== 'height' || ev.target !== panel) return;
                        panel.removeEventListener('transitionend', te);
                        d.open = false; panel.style.height = ''; delete d.dataset.animating;
                    });
                } else {
                    d.open = true;
                    var target = panel.scrollHeight;
                    panel.style.height = '0px';
                    panel.getBoundingClientRect();
                    panel.style.height = target + 'px';
                    panel.addEventListener('transitionend', function te(ev) {
                        if (ev.propertyName !== 'height' || ev.target !== panel) return;
                        panel.removeEventListener('transitionend', te);
                        panel.style.height = ''; delete d.dataset.animating;
                    });
                }
            });
        });
    })();
    </script>
    @endverbatim
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
