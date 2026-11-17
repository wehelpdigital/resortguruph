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

    /* ---------- Shared component polish ---------- */
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
