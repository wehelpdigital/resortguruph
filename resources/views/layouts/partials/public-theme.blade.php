{{--
    Site-wide design system for the public layout. Loaded AFTER the legacy
    component styles in layouts/public.blade.php so these rules win.

    Tokens: ocean blue (brand), coral (primary CTA), sun (highlight),
    ink (dark surfaces), sand (warm tint). Headings use Plus Jakarta Sans,
    body copy stays on Inter.
--}}
<style>
    :root {
        --rg-ocean: #13669c;
        --rg-ocean-600: #13669c;
        --rg-ocean-700: #11527f;
        --rg-coral: #ef5b3a;
        --rg-coral-600: #d9432a;
        --rg-sun: #f5a524;
        --rg-ink: #0b1b2b;
        --rg-sand: #faf5ec;
        --rg-header-h: 112px;
        --rg-radius: 1rem;
        --rg-shadow-sm: 0 1px 2px rgba(11, 27, 43, .06), 0 1px 1px rgba(11, 27, 43, .04);
        --rg-shadow-md: 0 10px 30px -12px rgba(11, 27, 43, .22);
    }

    html { scroll-padding-top: calc(var(--rg-header-h) + 12px); }
    body { color: #1e293b; text-rendering: optimizeLegibility; }
    ::selection { background: #ffdd88; color: #0b1b2b; }

    h1, h2, h3, .font-display {
        font-family: "Plus Jakarta Sans", Inter, ui-sans-serif, system-ui, sans-serif;
        letter-spacing: -0.018em;
    }
    h4, h5, h6 { font-family: "Plus Jakarta Sans", Inter, ui-sans-serif, system-ui, sans-serif; }
    /* The Tahu script accent inside headings keeps its own face. */
    h1 .font-brand, h2 .font-brand, h3 .font-brand { letter-spacing: 0; }

    .prose { --tw-prose-links: var(--rg-ocean); --tw-prose-headings: #0f172a; }
    .prose a { text-underline-offset: 3px; text-decoration-thickness: 1px; }

    :focus-visible { outline: 2px solid #217db6; outline-offset: 2px; border-radius: 6px; }
    input:focus-visible, textarea:focus-visible, select:focus-visible { outline: none; }

    /* In-page sticky sub-navs were hardcoded to top-16/28/32 against an
       older header height; pin them to the real (JS-measured) height. */
    main .sticky.top-16, main .sticky.top-28, main .sticky.top-32 { top: var(--rg-header-h) !important; }

    .no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
    .no-scrollbar::-webkit-scrollbar { display: none; }

    /* ---------- Logo ---------- */
    .rg-logo { display: inline-flex; align-items: center; gap: .6rem; white-space: nowrap; }
    .rg-logo__mark { width: 2.5rem; height: 2.5rem; flex-shrink: 0; filter: drop-shadow(0 4px 10px rgba(19, 102, 156, .28)); }
    .rg-logo__wm { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; letter-spacing: -.035em; font-size: 1.4rem; line-height: 1; }
    .rg-logo__t { color: #0b1b2b; }
    .rg-logo__g { color: var(--rg-ocean); }
    .rg-logo__p { color: var(--rg-coral); }
    .rg-logo--dark .rg-logo__t { color: #fff; }
    .rg-logo--dark .rg-logo__g { color: #7bb9de; }
    .rg-logo--dark .rg-logo__mark { filter: none; }
    @media (min-width: 640px) { .rg-logo--md .rg-logo__mark { width: 2.75rem; height: 2.75rem; } .rg-logo--md .rg-logo__wm { font-size: 1.6rem; } }
    .rg-logo--sm .rg-logo__mark { width: 2.2rem; height: 2.2rem; }
    .rg-logo--sm .rg-logo__wm { font-size: 1.25rem; }

    /* ---------- Header ---------- */
    .rg-header { background: rgba(255, 255, 255, .92); -webkit-backdrop-filter: saturate(180%) blur(14px); backdrop-filter: saturate(180%) blur(14px); transition: box-shadow .25s ease, background-color .25s ease; }
    .rg-header.is-scrolled { box-shadow: 0 8px 24px -16px rgba(11, 27, 43, .35); background: rgba(255, 255, 255, .97); }
    .rg-header__bar { border-bottom: 1px solid rgba(15, 23, 42, .06); }

    .rg-toplink { display: inline-flex; align-items: center; gap: .4rem; padding: .5rem .8rem; border-radius: 999px; font-size: .9rem; font-weight: 600; color: #334155; white-space: nowrap; }
    .rg-toplink:hover { color: var(--rg-ocean); background: #eef6fb; }
    .rg-toplink.is-active { color: var(--rg-ocean); }

    .rg-btn { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; padding: .62rem 1.15rem; border-radius: 999px; font-size: .9rem; font-weight: 700; line-height: 1.1; white-space: nowrap; transition: background-color .18s ease, color .18s ease, box-shadow .18s ease, transform .12s ease, border-color .18s ease; }
    .rg-btn svg { width: 1rem; height: 1rem; flex-shrink: 0; }
    .rg-btn--coral { background: var(--rg-coral); color: #fff; box-shadow: 0 6px 16px -8px rgba(239, 91, 58, .8); }
    .rg-btn--coral:hover { background: var(--rg-coral-600); }
    .rg-btn--ocean { background: var(--rg-ocean); color: #fff; box-shadow: 0 6px 16px -8px rgba(19, 102, 156, .8); }
    .rg-btn--ocean:hover { background: var(--rg-ocean-700); }
    .rg-btn--outline { background: #fff; color: var(--rg-ocean); border: 1.5px solid #afd5ec; }
    .rg-btn--outline:hover { border-color: var(--rg-ocean); background: #eef6fb; }
    .rg-btn--glass { background: rgba(255, 255, 255, .12); color: #fff; border: 1.5px solid rgba(255, 255, 255, .35); }
    .rg-btn--glass:hover { background: rgba(255, 255, 255, .22); }
    .rg-btn--lg { padding: .85rem 1.5rem; font-size: 1rem; }

    .rg-iconbtn { display: inline-flex; align-items: center; justify-content: center; width: 2.6rem; height: 2.6rem; border-radius: 999px; color: #0f172a; }
    .rg-iconbtn:hover { background: #f1f5f9; }
    .rg-iconbtn svg { width: 1.35rem; height: 1.35rem; }

    /* Header search (desktop). */
    .rg-hsearch { display: flex; align-items: center; height: 2.75rem; padding: 0 1rem; border-radius: 999px; background: #f1f5f9; border: 1.5px solid transparent; transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease; }
    .rg-hsearch:hover { background: #eaeff5; }
    .rg-hsearch:focus-within { background: #fff; border-color: #4499cc; box-shadow: 0 0 0 4px rgba(68, 153, 204, .15); }
    .rg-hsearch input { box-shadow: none !important; }
    .rg-hsearch input::-webkit-search-cancel-button, .rg-hsearch input::-webkit-search-decoration { -webkit-appearance: none; display: none; }

    /* Section nav: neutral labels, each with its own colored icon tile.
       --c / --bg come from the item (see public-header). */
    .rg-sections { scroll-snap-type: x proximity; -webkit-mask-image: linear-gradient(90deg, transparent 0, #000 14px, #000 calc(100% - 28px), transparent 100%); mask-image: linear-gradient(90deg, transparent 0, #000 14px, #000 calc(100% - 28px), transparent 100%); }
    @media (min-width: 1024px) { .rg-sections { -webkit-mask-image: none; mask-image: none; } }
    .rg-sec { position: relative; display: inline-flex; align-items: center; gap: .5rem; height: 2.4rem; padding: 0 .85rem 0 .4rem; border-radius: 999px; font-size: .875rem; font-weight: 600; color: #334155; white-space: nowrap; flex-shrink: 0; scroll-snap-align: start; transition: background-color .18s ease, color .18s ease; }
    .rg-sec__ico { display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 999px; background: var(--bg); color: var(--c); transition: background-color .18s ease, color .18s ease; }
    .rg-sec__ico svg { width: .95rem; height: .95rem; }
    @media (min-width: 1024px) and (max-width: 1279px) {
        .rg-sec { gap: .4rem; padding: 0 .6rem 0 .3rem; font-size: .8rem; }
        .rg-sec__ico { width: 1.55rem; height: 1.55rem; }
    }
    .rg-sec:hover { background: var(--bg); color: #0f172a; }
    .rg-sec.is-active { background: var(--bg); color: var(--c); }
    .rg-sec.is-active .rg-sec__ico { background: var(--c); color: #fff; }

    /* Typeahead panel shared by header + drawer search. */
    .rg-ta-panel { padding: .4rem; }
    .rg-ta--dropdown .rg-ta-panel { position: absolute; left: 0; margin-top: .5rem; min-width: 100%; width: 34rem; max-width: calc(100vw - 2rem); max-height: 70vh; overflow-y: auto; background: #fff; border-radius: 1rem; box-shadow: 0 30px 60px -20px rgba(11, 27, 43, .35), 0 0 0 1px rgba(15, 23, 42, .06); z-index: 60; }
    .rg-ta--inline .rg-ta-panel { margin-top: .5rem; background: #fff; border-radius: 1rem; box-shadow: 0 0 0 1px rgba(15, 23, 42, .08); }
    .rg-ns-group { font-size: .66rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #94a3b8; padding: .6rem .75rem .3rem; }
    .rg-ns-item { border-radius: .7rem; }
    .rg-ns-item:hover { background: #f8fafc; }
    .rg-ns-item.is-active { background: #eef6fb; }
    .rg-ns-ico { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0; overflow: hidden; }

    /* ---------- Header (single row, mega menus) ---------- */
    .rg-hdr { position: sticky; top: 0; z-index: 50; background: rgba(255, 255, 255, .96); -webkit-backdrop-filter: saturate(180%) blur(16px); backdrop-filter: saturate(180%) blur(16px); border-bottom: 1px solid rgba(15, 23, 42, .07); transition: background-color .3s ease, box-shadow .3s ease, border-color .3s ease; }
    .rg-hdr.is-scrolled { box-shadow: 0 10px 30px -18px rgba(11, 27, 43, .35); }
    .rg-hdr__logo--dark { display: none; }
    .rg-nav { position: relative; display: inline-flex; align-items: center; gap: .3rem; height: 2.6rem; padding: 0 .85rem; border-radius: 999px; font-size: .95rem; font-weight: 600; color: #1e293b; white-space: nowrap; transition: background-color .18s ease, color .18s ease; }
    .rg-nav:hover, .rg-mm.is-open > .rg-nav { background: #f1f5f9; color: #0b1b2b; }
    .rg-nav.is-active { color: var(--rg-ocean); }
    .rg-nav.is-active::after { content: ""; position: absolute; left: 50%; bottom: .2rem; width: 1.1rem; height: 3px; margin-left: -.55rem; border-radius: 3px; background: var(--rg-coral); }
    .rg-nav__chev { width: .95rem; height: .95rem; opacity: .6; transition: transform .2s ease; }
    .rg-mm.is-open .rg-nav__chev { transform: rotate(180deg); }

    .rg-mm__panel { position: absolute; top: calc(100% + .35rem); left: 1rem; right: 1rem; background: #fff; border-radius: 1.5rem; padding: 1.75rem; box-shadow: 0 40px 80px -30px rgba(11, 27, 43, .45), 0 0 0 1px rgba(15, 23, 42, .06); opacity: 0; visibility: hidden; transform: translateY(-6px); transition: opacity .18s ease, transform .18s ease, visibility 0s linear .18s; z-index: 60; }
    @media (min-width: 1024px) { .rg-mm__panel { left: 1.5rem; right: 1.5rem; } }
    @media (min-width: 1280px) { .rg-mm__panel { left: 2rem; right: 2rem; } }
    .rg-mm.is-open .rg-mm__panel { opacity: 1; visibility: visible; transform: none; transition-delay: 0s; }
    .rg-mm::after { content: ""; position: absolute; left: 0; right: 0; top: 100%; height: .6rem; }
    .rg-mm--small { position: relative; }
    .rg-mm--small .rg-mm__panel { left: 0; right: auto; width: 22rem; padding: .6rem; border-radius: 1.1rem; }
    .rg-mm__label { font-size: .68rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #94a3b8; }
    .rg-mm__link { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .45rem .6rem; margin: 0 -.6rem; border-radius: .6rem; font-size: .92rem; font-weight: 600; color: #1e293b; }
    .rg-mm__link:hover { background: #f1f5f9; color: var(--rg-ocean); }
    .rg-mm__count { font-size: .72rem; font-weight: 700; color: #94a3b8; }
    .rg-mm__more { display: inline-flex; align-items: center; gap: .4rem; margin-top: 1.1rem; font-size: .9rem; font-weight: 700; color: var(--rg-ocean); }
    .rg-mm__more svg { width: 1rem; height: 1rem; }
    .rg-mm__card { position: relative; display: block; aspect-ratio: 16 / 9; border-radius: 1rem; overflow: hidden; background: #0c2438; isolation: isolate; }
    .rg-mm__card img, .rg-mm__feature img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .7s cubic-bezier(.22, 1, .36, 1); }
    .rg-mm__card:hover img, .rg-mm__feature:hover img { transform: scale(1.06); }
    .rg-mm__card::after, .rg-mm__feature::after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, 0) 35%, rgba(7, 18, 29, .8) 100%); z-index: 0; }
    .rg-mm__card > span, .rg-mm__feature > span { position: absolute; left: .9rem; right: .9rem; bottom: .75rem; z-index: 1; color: #fff; }
    .rg-mm__card strong, .rg-mm__feature strong { display: block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.05rem; line-height: 1.15; }
    .rg-mm__card small, .rg-mm__feature small { display: block; margin-top: .15rem; font-size: .78rem; color: rgba(255, 255, 255, .85); line-height: 1.35; }
    .rg-mm__feature { position: relative; display: block; min-height: 13rem; border-radius: 1.1rem; overflow: hidden; background: #0c2438; isolation: isolate; }
    .rg-mm__feature--tall { min-height: 15rem; }
    .rg-mm__row { display: flex; gap: .8rem; align-items: flex-start; padding: .7rem .75rem; border-radius: .8rem; }
    .rg-mm__row:hover { background: #f8fafc; }
    .rg-mm__row strong { display: block; font-size: .92rem; color: #0f172a; }
    .rg-mm__row small { display: block; font-size: .8rem; color: #64748b; margin-top: .1rem; }
    .rg-mm__rowico { flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; width: 2.3rem; height: 2.3rem; border-radius: .7rem; background: #eef6fb; color: var(--rg-ocean); }
    .rg-mm__rowico svg { width: 1.1rem; height: 1.1rem; }

    .rg-hdr__search { display: inline-flex; align-items: center; gap: .5rem; height: 2.6rem; padding: 0 .75rem; border-radius: 999px; color: #1e293b; font-size: .9rem; font-weight: 600; transition: background-color .18s ease; }
    @media (min-width: 1280px) { .rg-hdr__search { background: #f1f5f9; padding: 0 .6rem 0 .9rem; min-width: 10.5rem; } }
    .rg-hdr__search:hover { background: #e8eef4; }
    .rg-hdr__search svg { width: 1.2rem; height: 1.2rem; }
    .rg-hdr__search kbd { margin-left: auto; align-items: center; justify-content: center; min-width: 1.4rem; height: 1.4rem; border-radius: .35rem; background: #fff; border: 1px solid #e2e8f0; font: 600 .72rem Inter, sans-serif; color: #64748b; }

    /* Transparent over a photo/video hero until scrolled or a menu opens. */
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) { background: transparent; border-color: transparent; -webkit-backdrop-filter: none; backdrop-filter: none; }
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__logo--light { display: none; }
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__logo--dark { display: inline-flex; }
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-nav,
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__search,
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__menu { color: #fff; }
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-nav:hover,
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__menu:hover { background: rgba(255, 255, 255, .14); }
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-nav.is-active { color: #fff; }
    @media (min-width: 1280px) { .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__search { background: rgba(255, 255, 255, .14); } }
    .rg-hdr--overlay:not(.is-scrolled):not(.is-open) .rg-hdr__search kbd { background: transparent; border-color: rgba(255, 255, 255, .4); color: rgba(255, 255, 255, .85); }

    /* ---------- Search overlay ---------- */
    .rg-sov { position: fixed; inset: 0; z-index: 90; visibility: hidden; }
    .rg-sov.is-open { visibility: visible; }
    .rg-sov__backdrop { position: absolute; inset: 0; background: rgba(7, 18, 29, .55); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px); opacity: 0; transition: opacity .2s ease; }
    .rg-sov.is-open .rg-sov__backdrop { opacity: 1; }
    .rg-sov__panel { position: relative; margin: 0 auto; width: min(44rem, 100%); max-height: 100vh; overflow-y: auto; background: #fff; padding: 1rem; border-radius: 0 0 1.5rem 1.5rem; box-shadow: 0 40px 80px -30px rgba(7, 18, 29, .6); transform: translateY(-12px); opacity: 0; transition: transform .22s cubic-bezier(.22, 1, .36, 1), opacity .2s ease; }
    @media (min-width: 640px) { .rg-sov__panel { margin-top: 10vh; border-radius: 1.5rem; padding: 1.25rem; max-height: 80vh; } }
    .rg-sov.is-open .rg-sov__panel { transform: none; opacity: 1; }
    .rg-sov__field { display: flex; align-items: center; gap: .75rem; padding: .55rem .6rem .55rem 1rem; border-radius: 1rem; background: #f1f5f9; border: 2px solid transparent; }
    .rg-sov__field:focus-within { background: #fff; border-color: #4499cc; }
    .rg-sov__field input { flex: 1; min-width: 0; border: 0; background: transparent; padding: .5rem 0; font-size: 1.1rem; color: #0f172a; box-shadow: none !important; outline: none; }
    .rg-sov__field input::-webkit-search-cancel-button { -webkit-appearance: none; display: none; }
    .rg-sov__esc { flex-shrink: 0; height: 2rem; padding: 0 .6rem; border-radius: .5rem; background: #fff; border: 1px solid #e2e8f0; font-size: .75rem; font-weight: 700; color: #64748b; }
    .rg-sov__suggest { padding: 1.25rem .35rem .4rem; }
    .rg-sov__chip { display: inline-flex; align-items: center; gap: .35rem; padding: .45rem .8rem; border-radius: 999px; background: #eef6fb; color: var(--rg-ocean); font-size: .88rem; font-weight: 600; }
    .rg-sov__chip:hover { background: var(--rg-ocean); color: #fff; }
    .rg-sov__cat { display: flex; align-items: center; gap: .6rem; padding: .55rem .6rem; border-radius: .8rem; font-size: .88rem; font-weight: 600; color: #1e293b; }
    .rg-sov__cat:hover { background: var(--bg); }
    .rg-sov__catico { display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; border-radius: .6rem; background: var(--bg); color: var(--c); flex-shrink: 0; }
    .rg-sov__catico svg { width: 1rem; height: 1rem; }
    .rg-drawer__search { display: flex; width: 100%; align-items: center; gap: .6rem; height: 2.9rem; padding: 0 1rem; border-radius: 999px; background: #f1f5f9; color: #64748b; font-size: .95rem; }

    /* ---------- Mobile drawer ---------- */
    .rg-drawer { position: fixed; inset: 0; z-index: 80; visibility: hidden; }
    .rg-drawer.is-open { visibility: visible; }
    .rg-drawer__backdrop { position: absolute; inset: 0; background: rgba(7, 18, 29, .5); opacity: 0; transition: opacity .25s ease; }
    .rg-drawer.is-open .rg-drawer__backdrop { opacity: 1; }
    .rg-drawer__panel { position: absolute; top: 0; right: 0; bottom: 0; width: min(420px, 92vw); background: #fff; display: flex; flex-direction: column; transform: translateX(100%); transition: transform .3s cubic-bezier(.22, 1, .36, 1); box-shadow: -20px 0 50px -20px rgba(7, 18, 29, .45); }
    .rg-drawer.is-open .rg-drawer__panel { transform: translateX(0); }
    .rg-drawer__body { flex: 1; overflow-y: auto; overscroll-behavior: contain; padding: 1rem 1.25rem 1.5rem; }
    .rg-dlink { display: flex; align-items: center; gap: .85rem; padding: .7rem .6rem; border-radius: .9rem; }
    .rg-dlink:hover, .rg-dlink.is-active { background: var(--bg, #f1f5f9); }
    .rg-dlink__ico { display: inline-flex; align-items: center; justify-content: center; width: 2.6rem; height: 2.6rem; border-radius: .8rem; background: var(--bg); color: var(--c); flex-shrink: 0; }
    .rg-dlink__ico svg { width: 1.2rem; height: 1.2rem; }
    html.rg-lock, html.rg-lock body { overflow: hidden; }
    @media (prefers-reduced-motion: reduce) { .rg-drawer__panel, .rg-drawer__backdrop { transition: none; } }

    /* ---------- Footer ---------- */
    .rg-footer { background: radial-gradient(1200px 400px at 85% -10%, rgba(33, 125, 182, .28), transparent 60%), #0b1b2b; color: #cbd5e1; }
    .rg-footer a { transition: color .15s ease; }
    .rg-footer a:hover { color: #ffc74a; }
    .rg-footer h4, .rg-footer h5, .rg-footer h6 { font-family: "Plus Jakarta Sans", Inter, sans-serif; }
    .rg-footer-cta { background: linear-gradient(120deg, #13669c 0%, #11527f 45%, #0c2438 100%); position: relative; overflow: hidden; }
    .rg-footer-cta::after { content: ""; position: absolute; right: -80px; top: -80px; width: 320px; height: 320px; border-radius: 999px; background: radial-gradient(circle, rgba(245, 165, 36, .45), transparent 65%); pointer-events: none; }
    .rg-footer .border-slate-800, .rg-footer .border-slate-800\/70 { border-color: rgba(148, 163, 184, .14) !important; }

    /* ---------- Dark immersive photo heroes (hub + destination pages) ---------- */
    .rg-hero-dark { background-color: #0c2438; color: #fff; }
    .rg-hero-dark h1 { color: #fff !important; text-shadow: 0 2px 24px rgba(7, 18, 29, .45); }
    .rg-hero-dark h1 .font-brand, .rg-hero-dark h1 [style*="#c0392b"], .rg-hero-dark .font-brand[style*="#c0392b"] { color: #ffd166 !important; }
    .rg-hero-dark .text-slate-500, .rg-hero-dark .text-slate-600, .rg-hero-dark .text-slate-700, .rg-hero-dark .text-slate-800 { color: rgba(255, 255, 255, .88) !important; }
    .rg-hero-dark blockquote { color: rgba(255, 255, 255, .92) !important; }
    .rg-hero-dark nav a:hover { color: #ffd166 !important; }
    /* eyebrow labels (accent colored text) */
    .rg-hero-dark > div > .uppercase, .rg-hero-dark > div > p > .uppercase, .rg-hero-dark .text-blue-700:not([class*="rg-dest-ts"] *), .rg-hero-dark .text-amber-700:not([class*="rg-dest-ts"] *), .rg-hero-dark .text-emerald-700:not([class*="rg-dest-ts"] *), .rg-hero-dark .text-rose-700:not([class*="rg-dest-ts"] *), .rg-hero-dark .text-violet-700:not([class*="rg-dest-ts"] *), .rg-hero-dark .text-teal-700:not([class*="rg-dest-ts"] *) { color: #ffd166 !important; }
    /* stat pills ("185 destinations"): glass on the dark photo */
    .rg-hero-dark .rounded-full.bg-white.border { background: rgba(255, 255, 255, .12) !important; border-color: rgba(255, 255, 255, .28) !important; -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
    .rg-hero-dark .rounded-full.bg-white.border .font-bold { color: #ffd166 !important; }
    .rg-hero-dark .rounded-full.bg-white.border .text-slate-600 { color: rgba(255, 255, 255, .9) !important; }
    .rg-hero-dark .bg-blue-50.ring-1 { background: rgba(255, 255, 255, .12) !important; --tw-ring-color: rgba(255, 255, 255, .28) !important; color: #fff !important; }
    .rg-hero-dark .rg-dest-ts__tab { background: rgba(255, 255, 255, .12); border-color: rgba(255, 255, 255, .28); color: #fff; }
    .rg-hero-dark .rg-dest-ts__tab:hover { background: rgba(255, 255, 255, .2); border-color: rgba(255, 255, 255, .55); color: #fff; }
    .rg-hero-dark .rg-dest-ts__tab.is-active { background: #fff; border-color: #fff; color: #0b1b2b; }
    .rg-hero-dark .rg-dest-ts__shell { border-color: transparent; box-shadow: 0 24px 60px -18px rgba(0, 0, 0, .55), 0 0 0 6px rgba(255, 255, 255, .12); }
    .rg-hero-dark .rg-dest-ts__shell:focus-within { box-shadow: 0 24px 60px -18px rgba(0, 0, 0, .55), 0 0 0 6px rgba(255, 209, 102, .45); }
    .rg-hero-dark .rg-dest-ts__chips { color: rgba(255, 255, 255, .75); }
    .rg-hero-dark .rg-dest-ts__chip { background: rgba(255, 255, 255, .1); border-color: rgba(255, 255, 255, .25); color: #fff; }
    .rg-hero-dark .rg-dest-ts__chip:hover { background: #fff; border-color: #fff; color: #0b1b2b; }
    @media (max-width: 767px) {
        .rg-hero-dark .rg-dest-ts__tabs, .rg-hero-dark .rg-dest-ts__chips { flex-wrap: nowrap; overflow-x: auto; justify-content: flex-start; scrollbar-width: none; margin-left: -1rem; margin-right: -1rem; padding: 0 1rem .15rem; }
        .rg-hero-dark .rg-dest-ts__tabs::-webkit-scrollbar, .rg-hero-dark .rg-dest-ts__chips::-webkit-scrollbar { display: none; }
        .rg-hero-dark .rg-dest-ts__tab, .rg-hero-dark .rg-dest-ts__chip, .rg-hero-dark .rg-dest-ts__chips > span { flex-shrink: 0; }
    }

    /* ---------- Shared component polish ---------- */
    /* Card rows that stack into very tall columns on phones become swipe carousels. */
    @media (max-width: 639px) {
        .rg-swipe { display: flex !important; flex-direction: row; overflow-x: auto; scroll-snap-type: x mandatory; gap: .85rem !important; margin-left: -1rem; margin-right: -1rem; padding: 0 1rem 1rem; scrollbar-width: none; }
        .rg-swipe::-webkit-scrollbar { display: none; }
        .rg-swipe > * { flex: 0 0 84%; scroll-snap-align: start; min-width: 0; }
    }
    /* Share row: icon-only on phones so it fits one line. */
    @media (max-width: 639px) {
        .rg-share-btn { padding: .5rem; }
        .rg-share-btn span { display: none; }
        .rg-share-btn svg { width: 1.15rem; height: 1.15rem; }
    }
    .rg-share-btn { border-radius: 999px; }

    /* Breadcrumbs read a little calmer. */
    nav[aria-label="Breadcrumb"] { font-size: .85rem; }

    /* Cards and tinted containers: softer, more consistent corners. */
    .rg-card-lift:hover { box-shadow: var(--rg-shadow-md); }
</style>
