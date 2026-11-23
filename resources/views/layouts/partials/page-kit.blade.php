{{-- Page kit: shared building blocks for every sub page (guides, blog,
     fiestas, listings, static and auth pages). Same visual language as
     the hand-built homepage: photo heroes under the transparent header,
     Plus Jakarta headings, rounded cards, coral calls to action.
     Markup helpers: partials/page-hero.blade.php, partials/page-aside.blade.php. --}}
<style>
    /* ---------- section titles (shared with the homepage) ---------- */
    .pk-eyebrow { font-size: .72rem; font-weight: 800; letter-spacing: .2em; text-transform: uppercase; margin-bottom: .6rem; color: var(--rg-coral-600, #d9472a); }
    .pk-h2 { font-size: clamp(1.7rem, 3.2vw, 2.5rem); line-height: 1.12; font-weight: 800; color: #0b1b2b; letter-spacing: -0.025em; }
    .pk-wrap { max-width: 80rem; margin: 0 auto; padding-left: 1rem; padding-right: 1rem; }
    @media (min-width: 640px) { .pk-wrap { padding-left: 1.5rem; padding-right: 1.5rem; } }
    @media (min-width: 1024px) { .pk-wrap { padding-left: 2rem; padding-right: 2rem; } }

    /* ---------- photo hero (sits under the transparent header) ---------- */
    .pk-hero { position: relative; isolation: isolate; overflow: hidden; color: #fff; background: #0c2438; margin-top: calc(-1 * var(--rg-header-h, 76px)); padding-top: var(--rg-header-h, 76px); }
    .pk-hero__media { position: absolute; inset: 0; z-index: -2; }
    .pk-hero__media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1.4s ease; }
    .pk-hero__media img.is-on { opacity: 1; }
    @media (prefers-reduced-motion: no-preference) { .pk-hero__media img.is-on { animation: pkKen 16s ease-out both; } }
    @keyframes pkKen { from { transform: scale(1.09); } to { transform: scale(1); } }
    .pk-hero__shade { position: absolute; inset: 0; z-index: -1; background: linear-gradient(90deg, rgba(7, 18, 29, .88) 0%, rgba(7, 18, 29, .6) 48%, rgba(7, 18, 29, .18) 100%), linear-gradient(180deg, rgba(7, 18, 29, .45) 0%, rgba(7, 18, 29, 0) 32%, rgba(7, 18, 29, .55) 100%); }
    @media (max-width: 767px) { .pk-hero__shade { background: linear-gradient(180deg, rgba(7, 18, 29, .55) 0%, rgba(7, 18, 29, .62) 45%, rgba(7, 18, 29, .9) 100%); } }
    .pk-hero__inner { position: relative; max-width: 80rem; margin: 0 auto; padding: 1.25rem 1rem 2.25rem; display: flex; flex-direction: column; min-height: 30rem; }
    @media (min-width: 640px) { .pk-hero__inner { padding: 1.5rem 1.5rem 3rem; } }
    @media (min-width: 1024px) { .pk-hero__inner { padding: 1.75rem 2rem 3.75rem; min-height: 36rem; } }
    .pk-hero--md .pk-hero__inner { min-height: 25rem; }
    @media (min-width: 1024px) { .pk-hero--md .pk-hero__inner { min-height: 30rem; } }
    .pk-hero--sm .pk-hero__inner { min-height: 0; padding-top: 2rem; padding-bottom: 3rem; }
    @media (min-width: 1024px) { .pk-hero--sm .pk-hero__inner { padding-top: 3rem; padding-bottom: 4.25rem; } }
    .pk-hero__body { margin-top: auto; padding-top: 3rem; max-width: 50rem; }
    .pk-hero--sm .pk-hero__body { padding-top: 2.25rem; }
    .pk-hero--center .pk-hero__body { margin-left: auto; margin-right: auto; text-align: center; }
    .pk-hero--center .pk-chips, .pk-hero--center .pk-actions { justify-content: center; }
    .pk-hero--center .pk-lede { margin-left: auto; margin-right: auto; }

    /* no photo: ocean gradient with a warm sun glow */
    .pk-hero--plain { background: radial-gradient(60rem 30rem at 85% -10%, rgba(255, 199, 74, .28), transparent 60%), radial-gradient(50rem 30rem at -10% 120%, rgba(239, 91, 58, .22), transparent 60%), linear-gradient(135deg, #0b1b2b 0%, #0f3a5c 55%, #13669c 100%); }
    .pk-hero--plain::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 5rem; z-index: -1; opacity: .12; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 80' preserveAspectRatio='none'%3E%3Cpath d='M0 40c100-30 200-30 300 0s200 30 300 0 200-30 300 0 200 30 300 0v40H0z' fill='%23fff'/%3E%3C/svg%3E") bottom / 100% 100% no-repeat; }

    .pk-crumbs { display: flex; flex-wrap: wrap; align-items: center; gap: .35rem; font-size: .8rem; color: rgba(255, 255, 255, .72); }
    .pk-crumbs a { color: rgba(255, 255, 255, .82); }
    .pk-crumbs a:hover { color: #fff; text-decoration: underline; text-underline-offset: 3px; }
    .pk-crumbs svg { width: .8rem; height: .8rem; opacity: .55; flex-shrink: 0; }
    .pk-crumbs [aria-current] { color: #fff; font-weight: 600; text-transform: capitalize; }

    .pk-kicker { display: inline-flex; align-items: center; gap: .5rem; border-radius: 999px; background: rgba(255, 255, 255, .12); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .25); -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); padding: .4rem .9rem; font-size: .8rem; font-weight: 700; color: #fff; }
    .pk-kicker::before { content: ""; width: .45rem; height: .45rem; border-radius: 999px; background: #ffc74a; box-shadow: 0 0 0 3px rgba(255, 199, 74, .25); }
    .pk-h1 { margin-top: 1.1rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: clamp(2.15rem, 5.2vw, 4.1rem); line-height: 1.06 !important; letter-spacing: -0.03em; color: #fff; text-shadow: 0 2px 30px rgba(7, 18, 29, .4); text-wrap: balance; }
    .pk-hero--sm .pk-h1 { font-size: clamp(2rem, 4.4vw, 3.4rem); }
    .pk-lede { margin-top: 1.1rem; max-width: 42rem; font-size: clamp(1.02rem, 1.6vw, 1.2rem); line-height: 1.65; color: rgba(255, 255, 255, .86); }
    .pk-chips { margin-top: 1.4rem; display: flex; flex-wrap: wrap; gap: .5rem; }
    .pk-chip { display: inline-flex; align-items: center; gap: .4rem; border-radius: 999px; background: rgba(7, 18, 29, .35); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .2); -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); padding: .38rem .8rem; font-size: .82rem; font-weight: 600; color: #fff; }
    .pk-chip svg { width: .95rem; height: .95rem; flex-shrink: 0; }
    .pk-chip--sun svg { color: #ffc74a; }
    .pk-actions { margin-top: 1.75rem; display: flex; flex-wrap: wrap; gap: .65rem; }
    .pk-hero__cap { position: absolute; right: 1rem; bottom: .85rem; max-width: min(24rem, 60%); text-align: right; font-size: .7rem; line-height: 1.35; color: rgba(255, 255, 255, .62); }
    .pk-hero__cap strong { display: block; color: rgba(255, 255, 255, .9); font-weight: 600; font-size: .78rem; }
    .pk-hero__cap a { color: inherit; text-decoration: underline; text-underline-offset: 2px; }
    @media (min-width: 1024px) { .pk-hero__cap { right: 2rem; bottom: 1.25rem; } }
    @media (max-width: 767px) { .pk-hero__cap { display: none; } }
    .pk-hero__dots { position: absolute; left: 50%; bottom: .8rem; transform: translateX(-50%); display: flex; gap: .4rem; }
    @media (min-width: 768px) { .pk-hero__dots { left: auto; right: 2rem; transform: none; bottom: auto; top: calc(var(--rg-header-h, 76px) + 1.6rem); } }
    .pk-hero__dots button { width: .5rem; height: .5rem; border-radius: 999px; background: rgba(255, 255, 255, .45); transition: width .3s ease, background-color .3s ease; }
    .pk-hero__dots button.is-on { width: 1.4rem; background: #fff; }

    /* block-rendered heroes (hub_hero, dest_hero_search) sliding under the header */
    .pk-pull > .rg-hero-dark:first-child, .pk-pull > style:first-child + .rg-hero-dark { margin-top: calc(-2rem - var(--rg-header-h, 76px)) !important; padding-top: var(--rg-header-h, 76px); }
    .pk-pull--flush > .rg-hero-dark:first-child, .pk-pull--flush > style:first-child + .rg-hero-dark { margin-top: calc(-1 * var(--rg-header-h, 76px)) !important; }
    .pk-pull > .rg-hero-dark:first-child > div:first-child { padding-top: 3.5rem; padding-bottom: 4rem; }
    @media (min-width: 768px) { .pk-pull > .rg-hero-dark:first-child > div:first-child { padding-top: 5rem; padding-bottom: 5.5rem; } }
    .pk-pull > .rg-hero-dark:first-child h1 { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; letter-spacing: -0.03em; }

    .pk-hero--xl .pk-hero__inner { min-height: 34rem; }
    @media (min-width: 1024px) { .pk-hero--xl .pk-hero__inner { min-height: min(46rem, calc(100vh - 2rem)); padding-bottom: 5.5rem; } }
    .pk-hero__thumbs { display: none; }
    @media (min-width: 900px) {
        .pk-hero__thumbs { display: flex; gap: .5rem; position: absolute; right: 2rem; bottom: 3.4rem; }
        .pk-hero__thumbs button { width: 4.6rem; height: 3.2rem; border-radius: .7rem; overflow: hidden; opacity: .62; box-shadow: 0 0 0 2px rgba(255, 255, 255, .25); transition: opacity .25s ease, box-shadow .25s ease, transform .25s ease; }
        .pk-hero__thumbs button img { width: 100%; height: 100%; object-fit: cover; }
        .pk-hero__thumbs button:hover { opacity: .9; }
        .pk-hero__thumbs button.is-on { opacity: 1; box-shadow: 0 0 0 2px #fff; transform: translateY(-2px); }
    }

    /* ---------- in-page navigation ---------- */
    .pk-jump { position: sticky; top: var(--rg-header-h, 64px); z-index: 30; background: rgba(255, 255, 255, .94); -webkit-backdrop-filter: saturate(180%) blur(14px); backdrop-filter: saturate(180%) blur(14px); border-bottom: 1px solid #e8edf3; }
    .pk-jump__row { display: flex; gap: .4rem; overflow-x: auto; padding: .6rem 1rem; scrollbar-width: none; }
    .pk-jump__row::-webkit-scrollbar { display: none; }
    .pk-jump a { flex-shrink: 0; border-radius: 999px; padding: .42rem .85rem; font-size: .8rem; font-weight: 600; color: #334155; background: #f1f5f9; white-space: nowrap; }
    .pk-jump a.is-on { background: #0b1b2b; color: #fff; }
    @media (min-width: 1280px) { .pk-jump--auto { display: none; } }

    .pk-grid { display: block; }
    @media (min-width: 1280px) { .pk-grid { display: grid; grid-template-columns: minmax(0, 1fr) 19rem; gap: 3.5rem; align-items: start; } }
    .pk-grid--narrow { max-width: 72rem; margin: 0 auto; }
    @media (min-width: 1280px) { .pk-grid--narrow { grid-template-columns: minmax(0, 46rem) 17.5rem; justify-content: space-between; gap: 3rem; } }
    .pk-aside { display: none; }
    @media (min-width: 1280px) { .pk-aside { display: flex; flex-direction: column; gap: 1rem; position: sticky; top: calc(var(--rg-header-h, 76px) + 1.5rem); max-height: calc(100vh - var(--rg-header-h, 76px) - 2.5rem); overflow-y: auto; scrollbar-width: thin; padding-bottom: .5rem; } }
    .pk-aside > * { flex-shrink: 0; }
    .pk-main { min-width: 0; }
    .pk-main :where(h2, h3)[id] { scroll-margin-top: calc(var(--rg-header-h, 76px) + 4.5rem); }
    @media (min-width: 1280px) { .pk-main :where(h2, h3)[id] { scroll-margin-top: calc(var(--rg-header-h, 76px) + 1.5rem); } }
    .rg-listings-band, #top-picks { scroll-margin-top: calc(var(--rg-header-h, 76px) + 4.5rem); }

    .pk-toc { border-radius: 1.25rem; border: 1px solid #e2e8f0; background: #fff; padding: 1.1rem 1.1rem .9rem; }
    .pk-toc__title { font-size: .7rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #64748b; margin-bottom: .6rem; }
    .pk-toc ol { display: flex; flex-direction: column; gap: .1rem; border-left: 2px solid #eef2f6; max-height: min(22rem, 42vh); overflow-y: auto; scrollbar-width: thin; }
    .pk-toc a { display: block; margin-left: -2px; border-left: 2px solid transparent; padding: .38rem 0 .38rem .85rem; font-size: .85rem; line-height: 1.35; color: #475569; transition: color .15s ease, border-color .15s ease; }
    .pk-toc a:hover { color: #0b1b2b; }
    .pk-toc a.is-on { color: #0b1b2b; font-weight: 700; border-left-color: var(--rg-coral, #ef5b3a); }

    .pk-card { border-radius: 1.25rem; border: 1px solid #e2e8f0; background: #fff; padding: 1.25rem; }
    .pk-card--ink { position: relative; overflow: hidden; isolation: isolate; border: 0; color: #fff; background: #0b1b2b; }
    .pk-card--ink::before { content: ""; position: absolute; right: -6rem; top: -6rem; width: 14rem; height: 14rem; border-radius: 999px; z-index: -1; background: radial-gradient(circle, rgba(239, 91, 58, .45), transparent 65%); }
    .pk-card--sand { background: #fbf6ee; border-color: #f0e4cf; }
    .pk-card__title { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.1rem; line-height: 1.25; letter-spacing: -0.01em; }
    .pk-share { display: flex; gap: .4rem; flex-wrap: wrap; }
    .pk-share a, .pk-share button { width: 2.35rem; height: 2.35rem; border-radius: 999px; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; color: #334155; background: #fff; transition: border-color .15s ease, color .15s ease; }
    .pk-share a:hover, .pk-share button:hover { border-color: #0b1b2b; color: #0b1b2b; }
    .pk-share svg { width: 1rem; height: 1rem; }

    /* ---------- long-form text (blog posts, fiestas, static pages) ---------- */
    .pk-prose { font-size: 1.07rem; line-height: 1.8; color: #334155; }
    .pk-prose > * + * { margin-top: 1.15em; }
    .pk-prose :where(h2) { margin-top: 2.2em; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: clamp(1.45rem, 2.4vw, 1.85rem); line-height: 1.2; font-weight: 800; color: #0b1b2b; letter-spacing: -0.02em; }
    .pk-prose :where(h3) { margin-top: 1.8em; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 1.25rem; line-height: 1.3; font-weight: 700; color: #0b1b2b; }
    .pk-prose :where(h2 + *, h3 + *) { margin-top: .7em; }
    .pk-prose :where(a) { color: #0f5788; font-weight: 600; text-decoration: underline; text-decoration-color: rgba(19, 102, 156, .35); text-underline-offset: 3px; }
    .pk-prose :where(a):hover { text-decoration-color: currentColor; }
    .pk-prose :where(strong) { color: #0b1b2b; }
    .pk-prose :where(ul, ol) { padding-left: 1.3em; }
    .pk-prose :where(ul) { list-style: none; }
    .pk-prose :where(ul > li) { position: relative; }
    .pk-prose :where(ul > li)::before { content: ""; position: absolute; left: -1.05em; top: .72em; width: .42em; height: .42em; border-radius: 999px; background: var(--rg-coral, #ef5b3a); }
    .pk-prose :where(ol) { list-style: decimal; }
    .pk-prose :where(ol > li)::marker { color: #94a3b8; font-weight: 700; }
    .pk-prose :where(li + li) { margin-top: .45em; }
    .pk-prose :where(blockquote) { border-left: 4px solid var(--rg-coral, #ef5b3a); background: #fbf6ee; border-radius: 0 1rem 1rem 0; padding: 1rem 1.25rem; font-size: 1.1rem; color: #0b1b2b; }
    .pk-prose :where(img) { border-radius: 1.25rem; }
    .pk-prose :where(table) { width: 100%; font-size: .95rem; border-collapse: collapse; }
    .pk-prose :where(th, td) { border-bottom: 1px solid #e2e8f0; padding: .6rem .5rem; text-align: left; }
    .pk-prose :where(hr) { border-color: #e2e8f0; margin: 2.5em 0; }

    /* ---------- block polish inside guide pages ---------- */
    .pk-main h2.text-2xl, .pk-main h2.text-3xl, .pk-main h2.text-xl { font-weight: 800; letter-spacing: -0.02em; color: #0b1b2b; }
    .pk-main h2.text-2xl { font-size: clamp(1.45rem, 2.4vw, 1.85rem); line-height: 1.2; }
    .pk-main section.my-12:first-child, .pk-main section.my-14:first-child { margin-top: 0; }
    .pk-main .rounded-xl.border, .pk-main .rounded-lg.border { border-radius: 1.1rem; }

    /* ---------- guide content skin (keyword guides, blog posts, fiestas) ----------
       #guide / .pk-guide outrank the per-page style blocks seeded into content. */
    .pk-guide { font-size: 1.04rem; }
    .pk-guide h2:not(.rg-keep) { font-family: "Plus Jakarta Sans", Inter, sans-serif !important; font-weight: 800 !important; font-size: clamp(1.55rem, 2.7vw, 2.15rem) !important; line-height: 1.15 !important; letter-spacing: -0.025em !important; color: #0b1b2b !important; }
    .pk-guide h3 { font-family: "Plus Jakarta Sans", Inter, sans-serif; letter-spacing: -0.01em; color: #0b1b2b; }
    .pk-guide p { line-height: 1.75; }
    .pk-guide .prose { --tw-prose-body: #3b4a5e; --tw-prose-headings: #0b1b2b; --tw-prose-links: #0f5788; }
    .pk-guide > section, .pk-guide > div > section { scroll-margin-top: calc(var(--rg-header-h, 76px) + 4.5rem); }
    .pk-guide .rounded-xl.border, .pk-guide .rounded-2xl.border, .pk-guide .rounded-lg.border { border-radius: 1.25rem; border-color: #e3e9f0; }
    .pk-guide .font-serif:is(h2, h3) { font-family: "Plus Jakarta Sans", Inter, sans-serif !important; }
    .pk-guide figure img, .pk-guide img.rounded-lg, .pk-guide img.rounded-xl { border-radius: 1.1rem; }
    .pk-guide figcaption { font-size: .8rem; color: #64748b; }

    .rg-tldr { position: relative; border-radius: 1.25rem; background: #fbf6ee; border: 1px solid #f0e4cf; padding: 1.35rem 1.4rem 1.35rem 1.6rem; overflow: hidden; }
    .rg-tldr::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: linear-gradient(180deg, #ef5b3a, #ffc74a); }
    .rg-tldr__eyebrow { display: flex; align-items: center; gap: .45rem; font-size: .72rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #c2410c !important; }
    .rg-tldr__eyebrow svg { width: 1rem; height: 1rem; }
    .rg-tldr__text { margin-top: .6rem; font-size: 1.06rem; line-height: 1.75 !important; color: #0b1b2b !important; }
    .rg-tldr__list { margin-top: .75rem; display: grid; gap: .55rem; }
    .rg-tldr__list li { display: flex; gap: .6rem; align-items: flex-start; color: #0b1b2b; line-height: 1.6; }
    .rg-tldr__list svg { width: 1.05rem; height: 1.05rem; margin-top: .25rem; flex-shrink: 0; color: #ef5b3a; }

    .rg-glance__eyebrow, .rg-fact__label, .rg-tip__eyebrow, .rg-rating__label { font-size: .7rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
    .rg-glance__eyebrow { color: #64748b !important; margin-bottom: .7rem; }
    .rg-glance__grid { display: grid; gap: .75rem; }
    @media (min-width: 640px) { .rg-glance__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .rg-glance__grid--one { grid-template-columns: 1fr; } }
    .rg-glance__tile { display: flex; gap: .85rem; align-items: flex-start; border-radius: 1.1rem; border: 1px solid #e3e9f0; background: #fff; padding: 1rem 1.1rem; }
    .rg-glance__ico, .rg-fact__ico, .rg-tip__ico { flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: .85rem; display: inline-flex; align-items: center; justify-content: center; color: var(--c); background: color-mix(in srgb, var(--c) 11%, #fff); }
    .rg-glance__ico svg, .rg-tip__ico svg { width: 1.2rem; height: 1.2rem; }
    .rg-glance__label { font-size: .72rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--c) !important; }
    .rg-glance__text { margin-top: .2rem; font-size: .95rem; line-height: 1.6 !important; color: #334155 !important; }

    .rg-fact { border-radius: 1.1rem; border: 1px solid #e3e9f0; background: #fff; padding: 1.1rem 1.15rem; }
    .rg-fact__ico svg { width: 1.3rem !important; height: 1.3rem !important; }
    .rg-fact__label { margin-top: .85rem; color: var(--c) !important; }
    .rg-fact__big { margin-top: .25rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.08rem; line-height: 1.3 !important; color: #0b1b2b !important; }
    .rg-fact__detail { margin-top: .3rem; font-size: .88rem; line-height: 1.55 !important; color: #5b6b80 !important; }
    .rg-fact--row { display: flex; gap: .9rem; align-items: flex-start; }
    .rg-fact--row .rg-fact__label { margin-top: 0; }

    .rg-rating { display: grid; border-radius: 1.4rem; overflow: hidden; border: 1px solid #e3e9f0; background: #fff; }
    @media (min-width: 640px) { .rg-rating { grid-template-columns: 13.5rem minmax(0, 1fr); } }
    .rg-rating__score { position: relative; isolation: isolate; overflow: hidden; background: #0b1b2b; color: #fff; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; }
    .rg-rating__score::before { content: ""; position: absolute; right: -4rem; top: -4rem; width: 11rem; height: 11rem; border-radius: 999px; z-index: -1; background: radial-gradient(circle, rgba(255, 199, 74, .35), transparent 65%); }
    .rg-rating__label { color: rgba(255, 255, 255, .78) !important; }
    .rg-rating__big { margin-top: .35rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 3.4rem; font-weight: 800; line-height: 1 !important; letter-spacing: -0.04em; color: #fff !important; }
    .rg-rating__big span { font-size: 1.1rem; font-weight: 700; color: rgba(255, 255, 255, .55); margin-left: .2rem; letter-spacing: 0; }
    .rg-rating__stars { display: flex; gap: .15rem; margin-top: .65rem; }
    .rg-rating__star { position: relative; width: 1.15rem; height: 1.15rem; }
    .rg-rating__star svg { position: absolute; inset: 0; width: 1.15rem; height: 1.15rem; fill: rgba(255, 255, 255, .22); }
    .rg-rating__star > span { position: absolute; inset: 0; overflow: hidden; }
    .rg-rating__star > span svg { fill: #ffc74a; }
    .rg-rating__verdict { margin-top: .6rem; font-weight: 700; color: #ffc74a !important; }
    .rg-rating__body { padding: 1.4rem 1.5rem; display: flex; flex-direction: column; justify-content: center; gap: 1rem; }
    .rg-rating__rows { display: grid; gap: .7rem; }
    .rg-rating__row { display: grid; grid-template-columns: 6.5rem minmax(0, 1fr) 2.2rem; align-items: center; gap: .75rem; font-size: .9rem; }
    .rg-rating__name { font-weight: 600; color: #334155; }
    .rg-rating__bar { height: .5rem; border-radius: 999px; background: #eef2f6; overflow: hidden; }
    .rg-rating__bar > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #ffc74a, #ef5b3a); }
    .rg-rating__num { text-align: right; font-weight: 800; color: #0b1b2b; font-variant-numeric: tabular-nums; }
    .rg-rating__summary { font-size: .95rem; line-height: 1.65 !important; color: #475569 !important; padding-top: .9rem; border-top: 1px solid #eef2f6; }

    .rg-faq__list { border-radius: 1.25rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; }
    .rg-faq__item + .rg-faq__item { border-top: 1px solid #eef2f6; }
    .rg-faq__item > summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.1rem 1.25rem; font-weight: 700; color: #0b1b2b; }
    .rg-faq__item > summary::-webkit-details-marker { display: none; }
    .rg-faq__item > summary:hover { background: #f8fafc; }
    .rg-faq__icon { position: relative; width: 1.75rem; height: 1.75rem; border-radius: 999px; border: 1.5px solid #cbd5e1; flex-shrink: 0; transition: transform .25s ease, background-color .2s ease; }
    .rg-faq__icon::before, .rg-faq__icon::after { content: ""; position: absolute; left: 50%; top: 50%; width: .7rem; height: 2px; background: #0f172a; transform: translate(-50%, -50%); }
    .rg-faq__icon::after { transform: translate(-50%, -50%) rotate(90deg); transition: opacity .2s ease; }
    .rg-faq__item[open] .rg-faq__icon { transform: rotate(180deg); background: #0b1b2b; border-color: #0b1b2b; }
    .rg-faq__item[open] .rg-faq__icon::before { background: #fff; }
    .rg-faq__item[open] .rg-faq__icon::after { opacity: 0; }
    .rg-faq__answer { padding: 0 1.25rem 1.2rem; color: #475569; line-height: 1.7; }
    .rg-food__name, .rg-faq__item > summary span { color: #0b1b2b !important; }

    .rg-sechead__sub { margin-top: .5rem; font-size: 1rem; color: #5b6b80 !important; }
    .rg-cta { position: relative; isolation: isolate; overflow: hidden; display: flex; flex-direction: column; gap: 1.1rem; border-radius: 1.4rem; background: #0b1b2b; color: #fff; padding: 1.6rem 1.6rem; }
    @media (min-width: 640px) { .rg-cta { flex-direction: row; align-items: center; justify-content: space-between; padding: 1.8rem 2rem; } }
    .rg-cta::before { content: ""; position: absolute; right: -6rem; top: -8rem; width: 18rem; height: 18rem; border-radius: 999px; z-index: -1; background: radial-gradient(circle, rgba(239, 91, 58, .42), transparent 65%); }
    .rg-cta__title { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.35rem; line-height: 1.25 !important; color: #fff !important; }
    .rg-cta__text { margin-top: .3rem; color: rgba(255, 255, 255, .75) !important; }

    .rg-foods__intro { color: #5b6b80 !important; max-width: 44rem; }
    .rg-food { display: flex; flex-direction: column; border-radius: 1.25rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; transition: box-shadow .25s ease, transform .25s ease; }
    .rg-food:hover { box-shadow: 0 18px 36px -22px rgba(11, 27, 43, .35); transform: translateY(-2px); }
    .rg-food__img { display: block; aspect-ratio: 16 / 10; overflow: hidden; background: #eef2f6; }
    .rg-food__img img { width: 100%; height: 100%; object-fit: cover; }
    .rg-food__body { padding: 1.1rem 1.2rem 1.25rem; }
    .rg-food--text { background: linear-gradient(160deg, #fff 0%, #fdf8f1 100%); }
    .rg-food__num { display: inline-block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.6rem; line-height: 1; color: #ef5b3a; letter-spacing: -0.03em; margin-bottom: .55rem; }
    .rg-food__name { font-weight: 800; font-size: 1.12rem; line-height: 1.3; }
    .rg-food__where { margin-top: .35rem; display: flex; gap: .35rem; align-items: flex-start; font-size: .8rem; font-weight: 600; color: #b45309 !important; line-height: 1.45 !important; }
    .rg-food__where svg { width: .9rem; height: .9rem; flex-shrink: 0; margin-top: .1rem; }
    .rg-food__blurb { margin-top: .55rem; font-size: .92rem; line-height: 1.6 !important; color: #475569 !important; }

    .rg-tip { display: flex; gap: .9rem; align-items: flex-start; border-radius: 1.25rem; padding: 1.15rem 1.25rem; background: color-mix(in srgb, var(--c) 7%, #fff); border: 1px solid color-mix(in srgb, var(--c) 22%, #fff); }
    .rg-tip__eyebrow { color: var(--c) !important; }
    .rg-tip__text { margin-top: .25rem; color: #1e293b !important; line-height: 1.65 !important; }
    .rg-quote { position: relative; border-radius: 1.4rem; background: #0b1b2b; color: #fff; padding: 1.8rem 1.8rem 1.6rem; overflow: hidden; }
    .rg-quote__mark { position: absolute; right: 1.2rem; top: -.6rem; font-family: Georgia, serif; font-size: 7rem; line-height: 1; color: rgba(255, 199, 74, .25); }
    .rg-quote__text { position: relative; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 1.2rem; font-weight: 600; line-height: 1.55 !important; color: #fff !important; }
    .rg-quote__by { margin-top: .8rem; font-size: .72rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #ffc74a !important; }

    .rg-region__more > summary { list-style: none; }
    .rg-region__more > summary::-webkit-details-marker { display: none; }
    .rg-region__more[open] > summary svg { transform: rotate(180deg); }
    .rg-dest-clusters__nav .pk-jump__row { padding-left: 1rem; padding-right: 1rem; }
    @media (min-width: 640px) { .rg-dest-clusters__nav .pk-jump__row { padding-left: 1.5rem; padding-right: 1.5rem; } }
    @media (min-width: 1024px) { .rg-dest-clusters__nav .pk-jump__row { padding-left: 2rem; padding-right: 2rem; } }

    /* ---------- full guide: re-rendered content (App\Support\GuideHtml) ---------- */
    .gh-eyebrow { font-size: .7rem !important; line-height: 1.4 !important; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #d9472a !important; margin-bottom: .45rem; display: flex; align-items: center; gap: .6rem; }
    .gh-head { margin-bottom: 1.5rem; }
    .gh-intro { margin-top: .6rem; max-width: 42rem; color: #5b6b80 !important; font-size: 1rem; line-height: 1.7 !important; }
    .gh-p { font-size: .98rem; line-height: 1.78 !important; color: #3b4a5e !important; }
    .gh-p + .gh-p { margin-top: .75rem; }
    .gh-p a, .gh-intro a { color: #0f5788; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; }
    .gh-spots, .gh-fests, .gh-reads, .gh-note, .gh-map, .gh-history, .gh-foods { margin: 3.25rem 0; }

    .gh-spots__grid { display: grid; gap: 1.25rem; }
    @media (min-width: 768px) { .gh-spots__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .gh-spot--lead { grid-column: 1 / -1; } }
    .gh-spot { display: flex; flex-direction: column; border-radius: 1.4rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; transition: box-shadow .3s ease; }
    .gh-spot:hover { box-shadow: 0 24px 48px -30px rgba(11, 27, 43, .45); }
    @media (min-width: 768px) { .gh-spot--lead { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); } .gh-spot--lead .gh-spot__img { aspect-ratio: auto; min-height: 100%; } }
    .gh-spot__img, .gh-fest__img, .gh-note__img, .gh-food__img, .gh-read__img { position: relative; display: block; overflow: hidden; background: linear-gradient(135deg, #e0f0fa, #fdf3e3); }
    .gh-spot__img { aspect-ratio: 16 / 10; flex: 1 0 auto; } /* a short card beside a long one grows its photo, not a blank strip */
    /* 2 columns: an odd card left alone on the last row takes the full row, photo beside the text */
    @media (min-width: 768px) {
        .gh-spots__grid > .gh-spot:last-child:nth-child(even):not(.gh-spot--lead) { grid-column: 1 / -1; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .gh-spots__grid > .gh-spot:last-child:nth-child(even):not(.gh-spot--lead) .gh-spot__img { aspect-ratio: auto; min-height: 15rem; }
        .gh-spots__grid > .gh-spot:last-child:nth-child(even):not(.gh-spot--lead) .gh-spot__body { justify-content: center; padding: 1.6rem 1.8rem; }
        .gh-spots__grid > .gh-spot:last-child:not(.gh-spot--lead) .gh-spot__body .gh-links { margin-top: .35rem; }
    }
    .gh-spot__img img, .gh-fest__img img, .gh-note__img img, .gh-food__img img, .gh-read__img img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); margin: 0; border-radius: 0 !important; }
    .gh-spot:hover .gh-spot__img img, .gh-fest:hover img, .gh-note:hover img, .gh-food:hover img, .gh-read:hover img { transform: scale(1.05); }
    .gh-num { position: absolute; top: .85rem; left: .85rem; min-width: 2.4rem; height: 2.4rem; padding: 0 .55rem; border-radius: 999px; background: #0b1b2b; color: #ffc74a; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: .9rem; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 8px 18px -8px rgba(0, 0, 0, .5); }
    .gh-spot__body { padding: 1.25rem 1.35rem 1.35rem; display: flex; flex-direction: column; gap: .55rem; }
    @media (min-width: 768px) { .gh-spot--lead .gh-spot__body { padding: 1.75rem 1.9rem; justify-content: center; } }
    .gh-spot__name, .gh-fest__name, .gh-food__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800 !important; font-size: 1.2rem; line-height: 1.25; letter-spacing: -0.015em; color: #0b1b2b !important; }
    .gh-spot--lead .gh-spot__name { font-size: 1.6rem; }
    .gh-rating { display: flex; align-items: center; flex-wrap: wrap; gap: .4rem; font-size: .85rem; color: #64748b !important; }
    .gh-rating strong { color: #0b1b2b; }
    .gh-stars { display: inline-flex; gap: .1rem; }
    .gh-stars svg { width: .95rem; height: .95rem; fill: #e2e8f0; }
    .gh-stars svg.on { fill: #f5a524; }
    .gh-spot__body > .gh-p--clamp { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
    .gh-spot--lead .gh-spot__body > .gh-p--clamp { -webkit-line-clamp: 6; }
    .gh-spot__body:has(.gh-more[open]) > .gh-p--clamp { display: block; -webkit-line-clamp: unset; overflow: visible; }
    .gh-more > summary { list-style: none; cursor: pointer; display: inline-flex; align-items: center; gap: .35rem; font-size: .85rem; font-weight: 700; color: #0f5788; }
    .gh-more > summary::-webkit-details-marker { display: none; }
    .gh-more > summary::after { content: ""; width: .45rem; height: .45rem; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(45deg) translateY(-2px); }
    .gh-more[open] > summary { display: none; }
    .gh-more[open] .gh-p:first-of-type { margin-top: 0; }
    .gh-quote { margin: .35rem 0 0; border-radius: 1rem; background: #fbf6ee; border: 1px solid #f0e4cf; padding: .85rem 1rem; }
    .gh-quote blockquote { margin: 0; padding: 0; border: 0; background: none; font-size: .92rem; line-height: 1.55; color: #1e293b; font-style: normal; }
    .gh-quote blockquote::before { content: "\201C"; color: #ef5b3a; font-weight: 800; margin-right: .1rem; }
    .gh-quote figcaption { margin-top: .35rem; font-size: .75rem; font-weight: 600; color: #64748b; }
    .gh-links { display: flex; flex-wrap: wrap; gap: .4rem 1rem; margin-top: auto; padding-top: .35rem; }
    .gh-links a, .gh-food__link, .gh-note__foot a { display: inline-flex; align-items: center; gap: .3rem; font-size: .85rem; font-weight: 700; color: #d9432a !important; text-decoration: none !important; }
    .gh-links a svg, .gh-food__link svg, .gh-note__foot a svg, .gh-read__go svg { width: .9rem; height: .9rem; }

    .gh-fests__list { display: grid; gap: 1.25rem; }
    .gh-fest { display: grid; border-radius: 1.4rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; }
    @media (min-width: 768px) { .gh-fest { grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr); } }
    .gh-fest__img { aspect-ratio: 4 / 3; }
    @media (min-width: 768px) { .gh-fest__img { aspect-ratio: auto; min-height: 100%; } }
    .gh-date { position: absolute; left: 1rem; bottom: 1rem; display: inline-flex; align-items: center; gap: .4rem; border-radius: 999px; background: #fff; color: #0b1b2b; font-size: .78rem; font-weight: 800; padding: .4rem .8rem; box-shadow: 0 10px 22px -10px rgba(0, 0, 0, .45); }
    .gh-date svg { width: .95rem; height: .95rem; color: #ef5b3a; }
    .gh-fest__body { padding: 1.4rem 1.5rem 1.5rem; display: flex; flex-direction: column; gap: .6rem; justify-content: center; }

    .gh-note { display: grid; border-radius: 1.4rem; overflow: hidden; background: #0b1b2b; color: #fff; }
    @media (min-width: 640px) { .gh-note { grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr); } }
    .gh-note__img { aspect-ratio: 16 / 10; }
    @media (min-width: 640px) { .gh-note__img { aspect-ratio: auto; min-height: 100%; } }
    .gh-note__body { padding: 1.5rem 1.6rem; display: flex; flex-direction: column; gap: .6rem; justify-content: center; }
    .gh-note .gh-eyebrow { color: #ffc74a !important; }
    .gh-note .gh-p { color: rgba(255, 255, 255, .85) !important; font-size: 1.02rem; }
    .gh-note__foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .5rem; margin-top: .4rem; padding-top: .8rem; border-top: 1px solid rgba(255, 255, 255, .12); font-size: .8rem; color: rgba(255, 255, 255, .6) !important; }
    .gh-note__foot span a { color: #fff; font-weight: 600; }
    .gh-note__foot > a { color: #ffc74a !important; }

    .gh-reads__grid { display: grid; gap: 1rem; }
    @media (min-width: 640px) { .gh-reads__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .gh-reads__grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .gh-read { display: flex; flex-direction: column; border-radius: 1.25rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; text-decoration: none !important; transition: box-shadow .3s ease, transform .3s ease; }
    .gh-read:hover { box-shadow: 0 22px 44px -28px rgba(11, 27, 43, .45); transform: translateY(-2px); }
    .gh-read__img { aspect-ratio: 16 / 10; }
    .gh-read__body { display: flex; flex-direction: column; gap: .35rem; padding: 1rem 1.1rem 1.15rem; flex: 1; }
    .gh-read__kicker { font-size: .66rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #94a3b8; }
    .gh-read__title { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1rem; line-height: 1.3; color: #0b1b2b; }
    .gh-read__text { font-size: .85rem; line-height: 1.5; color: #5b6b80; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .gh-read__go { margin-top: auto; padding-top: .4rem; display: inline-flex; align-items: center; gap: .3rem; font-size: .82rem; font-weight: 700; color: #d9432a; }

    .gh-map { border-radius: 1.5rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; }
    .gh-map__head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1.35rem 1.5rem 1.2rem; }
    .pk-guide h2.gh-map__title, .gh-map__title { font-size: 1.35rem !important; margin: 0; }
    .gh-map__sub { margin-top: .25rem; font-size: .9rem; color: #64748b !important; }
    .gh-map__frame iframe { display: block; width: 100%; height: 22rem; border: 0; }
    @media (min-width: 1024px) { .gh-map__frame iframe { height: 28rem; } }

    .gh-foods { display: grid; gap: 1rem; }
    @media (min-width: 640px) { .gh-foods { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1280px) { .gh-foods { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    .gh-food { display: flex; flex-direction: column; border-radius: 1.25rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; }
    .gh-food__img { aspect-ratio: 4 / 3; }
    .gh-food__body { padding: 1rem 1.1rem 1.1rem; display: flex; flex-direction: column; gap: .4rem; flex: 1; }
    .gh-food__name { font-size: 1.05rem; }
    .gh-food__text { font-size: .9rem; line-height: 1.55 !important; color: #5b6b80 !important; }
    .gh-food__link { margin-top: auto; }
    .gh-food__cap { font-size: .7rem; color: #94a3b8 !important; }

    .gh-history { display: grid; grid-template-columns: 6px minmax(0, 1fr); gap: 1.4rem; border-radius: 1.5rem; padding: 1.75rem 1.6rem 1.75rem 1.4rem; background: linear-gradient(160deg, #fbf6ee 0%, #fffdf9 100%); border: 1px solid #f0e4cf; }
    @media (min-width: 640px) { .gh-history { padding: 2.25rem 2.25rem 2.25rem 1.8rem; gap: 1.75rem; } }
    .gh-history__rail { border-radius: 999px; background: linear-gradient(180deg, #ef5b3a, #ffc74a 60%, rgba(255, 199, 74, 0)); }
    .gh-history__since { border-radius: 999px; background: #0b1b2b; color: #ffc74a; padding: .2rem .6rem; letter-spacing: .1em; font-size: .64rem; }
    .pk-guide h2.gh-history__title, .gh-history__title { margin: .15rem 0 1rem; }
    .gh-p--lead { font-size: 1.1rem !important; color: #0b1b2b !important; }
    .gh-history__source { margin-top: 1.1rem; padding-top: .9rem; border-top: 1px solid #efe3cc; font-size: .8rem; color: #8a6d3b !important; font-style: italic; }
    .gh-history__source a { color: inherit; text-decoration: underline; }

    /* spot cards without a photo of their own: text cards with a tinted top (GuideHtml::spots) */
    .gh-spot--text { background: linear-gradient(180deg, #f3f8fc 0, #fff 7rem); }
    .gh-spot--text .gh-spot__body { padding-top: 1.1rem; }
    .gh-spot__top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .2rem; }
    .gh-spot__top .gh-num { position: static; box-shadow: none; }
    .gh-spot__pin { width: 2.2rem; height: 2.2rem; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; color: #0f5788; background: #fff; box-shadow: inset 0 0 0 1px #d4e7f5; }
    .gh-spot__pin svg { width: 1.05rem; height: 1.05rem; }

    /* cards without a photo of their own: a designed panel (GuideHtml::photo) */
    .gh-ph { display: flex !important; flex-direction: column; align-items: flex-start; justify-content: flex-end; gap: .65rem; padding: 1.25rem 1.35rem; color: #fff; isolation: isolate; text-decoration: none !important; }
    .gh-ph::before { content: ""; position: absolute; inset: 0; z-index: -1; background-image: radial-gradient(rgba(255, 255, 255, .16) 1px, transparent 1.5px); background-size: 18px 18px; -webkit-mask-image: linear-gradient(160deg, #000 15%, transparent 80%); mask-image: linear-gradient(160deg, #000 15%, transparent 80%); }
    .gh-ph::after { content: ""; position: absolute; right: -18%; top: -30%; width: 70%; aspect-ratio: 1; border-radius: 999px; z-index: -1; background: radial-gradient(circle, rgba(255, 255, 255, .22), transparent 65%); }
    .gh-ph--0 { background: linear-gradient(140deg, #0b3f66 0%, #0f5788 45%, #0e9aa7 100%) !important; }
    .gh-ph--1 { background: linear-gradient(140deg, #8a2c12 0%, #e2542f 55%, #f5a524 100%) !important; }
    .gh-ph--2 { background: linear-gradient(140deg, #0f3f3b 0%, #0f766e 50%, #5fa82a 100%) !important; }
    .gh-ph--3 { background: linear-gradient(140deg, #16163f 0%, #24479a 55%, #3fb5e8 100%) !important; }
    .gh-ph__icon { width: 2.6rem; height: 2.6rem; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, .16); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .28); }
    .gh-ph__icon svg { width: 1.3rem; height: 1.3rem; }
    .gh-ph__label { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.3rem; line-height: 1.18; letter-spacing: -0.02em; max-width: 92%; text-wrap: balance; text-shadow: 0 2px 14px rgba(0, 0, 0, .25); }
    .gh-spot--lead .gh-ph { padding: 1.75rem; min-height: 15rem; }
    .gh-spot--lead .gh-ph__label { font-size: 1.85rem; }
    .gh-fest__img.gh-ph { padding-bottom: 4rem; min-height: 12rem; }
    .gh-read__img.gh-ph, .gh-food__img.gh-ph { align-items: center; justify-content: center; }
    .gh-food__img.gh-ph .gh-ph__label { display: none; }
    .gh-note--solo { grid-template-columns: minmax(0, 1fr) !important; background: radial-gradient(120% 140% at 100% 0%, #1d3b5c 0%, #0b1b2b 55%) !important; }
    .gh-note--solo .gh-note__body { padding: 1.75rem 2rem; }
    .gh-note--solo .gh-p { font-size: 1.12rem; }

    /* plain text sections, headings and images in the full guide: numbered chapters */
    .pk-guide { counter-reset: gh-ch; }
    .pk-guide section.my-6 { margin: 2.75rem 0; }
    .pk-guide section.my-6 > h2, .pk-guide h2.rg-h { margin-bottom: .9rem; counter-increment: gh-ch; }
    .pk-guide section.my-6 > h2::before, .pk-guide h2.rg-h::before { content: counter(gh-ch, decimal-leading-zero); display: block; width: max-content; padding-right: 3.25rem; margin-bottom: .8rem; font: 800 .8rem/1 "Plus Jakarta Sans", Inter, sans-serif; letter-spacing: .06em; color: #ef5b3a; background: linear-gradient(90deg, #ef5b3a, #ffc74a) no-repeat right center / 2.4rem 3px; }
    .pk-guide h2.rg-h { margin-top: 3.5rem !important; }
    .pk-guide h2.rg-h + section.my-6 { margin-top: .5rem; }
    .pk-guide .prose p { font-size: 1.04rem; line-height: 1.85; }
    .pk-guide .prose strong { color: #0b1b2b; }
    .pk-guide figure.my-6 { margin: 2.5rem 0; }
    .pk-guide figure.my-6 img { display: block; width: 100%; border-radius: 1.4rem !important; box-shadow: 0 30px 60px -40px rgba(11, 27, 43, .55); }
    .pk-guide figure.rg-fig img { aspect-ratio: 16 / 10; object-fit: cover; max-width: 100%; }
    .pk-guide figure.my-6 figcaption { margin-top: .7rem; display: flex; flex-wrap: wrap; align-items: center; gap: .3rem .8rem; text-align: left; font-size: .76rem; color: #94a3b8; }
    .rg-fig__place { display: inline-flex; align-items: center; gap: .4rem; font-weight: 700; font-size: .8rem; color: #334155; }
    .rg-fig__place::before { content: ""; width: .45rem; height: .45rem; border-radius: 999px; background: #ef5b3a; }
    .rg-figs { display: grid; gap: 1.1rem; margin: 2.5rem 0; }
    @media (min-width: 640px) { .rg-figs { grid-template-columns: repeat(2, minmax(0, 1fr)); } .pk-guide .rg-figs figure.rg-fig img { aspect-ratio: 4 / 5; } }
    @media (min-width: 640px) { .rg-figs > figure:only-child img { aspect-ratio: 16 / 10; } }
    .pk-guide .rg-figs figure.my-6 { margin: 0; }

    /* ---------- cross-check links + hashtag chips (externalGuides / tagPills) ---------- */
    .xg { margin: 3.25rem 0; }
    .xg__grid { display: grid; gap: .8rem; }
    @media (min-width: 768px) { .xg__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    .xg__card { position: relative; display: flex; align-items: flex-start; gap: .9rem; padding: 1rem 2.6rem 1rem 1rem; border-radius: 1.15rem; border: 1px solid #e3e9f0; background: #fff; text-decoration: none !important; transition: box-shadow .3s ease, border-color .3s ease, transform .3s ease; }
    .xg__card:hover { border-color: color-mix(in srgb, var(--brand) 35%, #e3e9f0); box-shadow: 0 20px 40px -28px rgba(11, 27, 43, .45); transform: translateY(-2px); }
    .xg__logo { flex: none; width: 2.6rem; height: 2.6rem; border-radius: .85rem; display: inline-flex; align-items: center; justify-content: center; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.1rem; color: #fff; background: linear-gradient(140deg, var(--brand), var(--brand-d)); box-shadow: 0 10px 20px -12px var(--brand); }
    .xg__body { display: flex; flex-direction: column; gap: .15rem; min-width: 0; }
    .xg__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1rem; line-height: 1.25; color: #0b1b2b; }
    .xg__host { font-size: .66rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #94a3b8; }
    .xg__blurb { margin-top: .25rem; font-size: .86rem; line-height: 1.55; color: #5b6b80; }
    .xg__go { position: absolute; top: 1rem; right: 1rem; width: 1.1rem; height: 1.1rem; color: #94a3b8; transition: color .2s ease, transform .2s ease; }
    .xg__card:hover .xg__go { color: var(--brand-d); transform: translate(2px, -2px); }
    .xg__foot { margin-top: .9rem; font-size: .76rem; color: #94a3b8; }
    .rg-tags { margin: 2.25rem 0; }
    .rg-tags__row { display: flex; flex-wrap: wrap; gap: .5rem; }
    .rg-tag { display: inline-flex; align-items: center; border-radius: 999px; background: #fff; border: 1px solid #e3e9f0; overflow: hidden; transition: border-color .2s ease, box-shadow .2s ease; }
    .rg-tag:hover { border-color: color-mix(in srgb, var(--tag) 40%, #e3e9f0); box-shadow: 0 8px 18px -12px rgba(11, 27, 43, .4); }
    .rg-tag__main { display: inline-flex; align-items: center; gap: .1rem; padding: .42rem .4rem .42rem .8rem; font-size: .8rem; font-weight: 700; color: #1e293b !important; text-decoration: none !important; }
    .rg-tag__hash { color: var(--tag); font-weight: 800; }
    .rg-tag__x { display: inline-flex; align-items: center; justify-content: center; width: 1.9rem; align-self: stretch; color: #94a3b8 !important; }
    .rg-tag__x:hover { color: #0b1b2b !important; }
    .rg-tag__x svg { width: .7rem; height: .7rem; }

    /* ---------- hub category grids + quick look (BlockRenderer::hubCategoryGrid) ---------- */
    .hx-cat { margin: 3.5rem 0 4.5rem; scroll-margin-top: calc(var(--rg-header-h, 76px) + 1.5rem); }
    .hx-cat__head { display: flex; align-items: flex-start; gap: 1.1rem; margin-bottom: 1.75rem; }
    .hx-cat__icon { flex: none; width: 3.6rem; height: 3.6rem; border-radius: 1.15rem; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; background: #fff; box-shadow: inset 0 0 0 1px #e3e9f0, 0 14px 28px -18px rgba(11, 27, 43, .45); }
    .hx-cat__count { font-size: .7rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #d9472a; }
    .hx-cat__title { margin-top: .3rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: clamp(1.5rem, 2.6vw, 2.15rem); line-height: 1.15; letter-spacing: -0.025em; color: #0b1b2b; }
    .hx-cat__intro { margin-top: .55rem; max-width: 46rem; color: #5b6b80; line-height: 1.7; }
    .hx-grid { display: grid; gap: .85rem; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-flow: dense; }
    @media (min-width: 768px) { .hx-grid { gap: 1.15rem; grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .hx-grid--4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } .hx-grid--5 { grid-template-columns: repeat(5, minmax(0, 1fr)); } .hx-grid--6 { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
    .hx-card { position: relative; display: flex; flex-direction: column; min-width: 0; border-radius: 1.25rem; overflow: hidden; background: #fff; border: 1px solid #e3e9f0; text-decoration: none !important; transition: box-shadow .3s ease, transform .3s ease; scroll-margin-top: calc(var(--rg-header-h, 76px) + 1rem); }
    .hx-card:hover { box-shadow: 0 24px 48px -30px rgba(11, 27, 43, .5); transform: translateY(-3px); }
    .hx-card:focus-visible { outline: 3px solid #ef5b3a; outline-offset: 2px; }
    .hx-card__img { position: relative; display: block; aspect-ratio: 4 / 3; overflow: hidden; background: linear-gradient(135deg, #e0f0fa, #fdf3e3); }
    .hx-card__img img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .hx-card:hover .hx-card__img img { transform: scale(1.06); }
    .hx-card__img.gh-ph { align-items: center; justify-content: center; padding: 0; }
    .hx-card__emoji { font-size: 2.7rem; line-height: 1; filter: drop-shadow(0 8px 14px rgba(0, 0, 0, .3)); }
    .hx-card__where { position: absolute; left: .6rem; bottom: .6rem; max-width: calc(100% - 1.2rem); display: inline-flex; align-items: center; gap: .3rem; padding: .3rem .6rem; border-radius: 999px; background: rgba(11, 27, 43, .74); backdrop-filter: blur(6px); color: #fff; font-size: .7rem; font-weight: 700; }
    .hx-card__where span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hx-card__where svg { width: .8rem; height: .8rem; flex: none; color: #ffc74a; }
    .hx-card__body { display: flex; flex-direction: column; gap: .3rem; flex: 1; padding: .85rem .95rem 1rem; }
    .hx-card__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: .98rem; line-height: 1.25; letter-spacing: -0.01em; color: #0b1b2b; }
    .hx-card__note { font-size: .82rem; line-height: 1.5; color: #5b6b80; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hx-card__go { margin-top: auto; padding-top: .3rem; display: inline-flex; align-items: center; gap: .3rem; font-size: .78rem; font-weight: 700; color: #d9432a; }
    .hx-card__go svg { width: .85rem; height: .85rem; transition: transform .25s ease; }
    .hx-card:hover .hx-card__go svg { transform: translateX(3px); }
    .hx-card--lead { grid-column: 1 / -1; }
    .hx-card--lead .hx-card__img { aspect-ratio: 16 / 10; }
    @media (min-width: 768px) {
        .hx-card--lead { grid-column: span 2; grid-row: span 2; }
        .hx-card--lead .hx-card__img { aspect-ratio: auto; flex: 1; min-height: 15rem; }
        .hx-card--lead .hx-card__body { flex: none; padding: 1.15rem 1.3rem 1.25rem; }
        .hx-card--lead .hx-card__name { font-size: 1.4rem; }
        .hx-card--lead .hx-card__note { -webkit-line-clamp: 3; font-size: .92rem; }
    }

    .hx-dlg { width: min(52rem, calc(100% - 1.5rem)); max-width: none; max-height: calc(100dvh - 1.5rem); padding: 0; border: 0; border-radius: 1.5rem; overflow: hidden; background: #fff; color: #0b1b2b; box-shadow: 0 40px 90px -30px rgba(0, 0, 0, .6); }
    .hx-dlg::backdrop { background: rgba(7, 18, 29, .62); backdrop-filter: blur(4px); }
    .hx-dlg[open] { animation: hxIn .28s cubic-bezier(.22, 1, .36, 1); }
    @keyframes hxIn { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
    .hx-dlg__inner { display: grid; max-height: calc(100dvh - 1.5rem); overflow-y: auto; }
    @media (min-width: 768px) { .hx-dlg__inner { grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); } }
    .hx-dlg__media { position: relative; aspect-ratio: 4 / 3; overflow: hidden; background: #0b1b2b; display: flex; align-items: center; justify-content: center; }
    @media (min-width: 768px) { .hx-dlg__media { aspect-ratio: auto; min-height: 24rem; } }
    .hx-dlg__media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity .5s ease; }
    .hx-dlg__media img.is-on { opacity: 1; }
    .hx-dlg__emoji { font-size: 4.5rem; filter: drop-shadow(0 10px 18px rgba(0, 0, 0, .35)); }
    .hx-dlg__dots { position: absolute; left: 0; right: 0; bottom: .8rem; display: flex; justify-content: center; gap: .4rem; }
    .hx-dlg__dots button { width: .55rem; height: .55rem; border-radius: 999px; background: rgba(255, 255, 255, .55); transition: width .25s ease, background .25s ease; }
    .hx-dlg__dots button[aria-current="true"] { width: 1.4rem; background: #fff; }
    .hx-dlg__body { display: flex; flex-direction: column; gap: .7rem; padding: 1.6rem 1.5rem 1.5rem; }
    @media (min-width: 768px) { .hx-dlg__body { padding: 2.2rem 2rem 1.8rem; } }
    .hx-dlg__cat { font-size: .7rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: #d9472a; padding-right: 2.5rem; }
    .hx-dlg__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: clamp(1.5rem, 3vw, 2rem); line-height: 1.12; letter-spacing: -0.025em; }
    .hx-dlg__where { display: inline-flex; align-items: center; gap: .4rem; align-self: flex-start; padding: .35rem .75rem; border-radius: 999px; background: #eef6fc; color: #0f5788; font-size: .82rem; font-weight: 700; }
    .hx-dlg__where svg { width: .95rem; height: .95rem; }
    .hx-dlg__desc { color: #3b4a5e; line-height: 1.75; }
    .hx-dlg__note { border-radius: 1rem; background: #fbf6ee; border: 1px solid #f0e4cf; padding: .8rem 1rem; font-size: .9rem; line-height: 1.6; color: #1e293b; }
    .hx-dlg__actions { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: auto; padding-top: .6rem; }
    .hx-dlg__x { position: absolute; top: .8rem; right: .8rem; width: 2.5rem; height: 2.5rem; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, .92); color: #0b1b2b; box-shadow: 0 10px 24px -12px rgba(0, 0, 0, .45); }
    .hx-dlg__x svg { width: 1.1rem; height: 1.1rem; }
    [hidden].hx-dlg__where, [hidden].hx-dlg__note, [hidden].hx-dlg__desc, [hidden].hx-dlg__cat { display: none; }

    /* ---------- auth / form pages ---------- */
    .pk-auth { min-height: calc(100vh - var(--rg-header-h, 76px)); display: grid; }
    @media (min-width: 1024px) { .pk-auth { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } }
    .pk-auth__art { position: relative; display: none; overflow: hidden; isolation: isolate; color: #fff; }
    @media (min-width: 1024px) { .pk-auth__art { display: flex; flex-direction: column; justify-content: flex-end; padding: 3rem; } }
    .pk-auth__art img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; }
    .pk-auth__art::after { content: ""; position: absolute; inset: 0; z-index: -1; background: linear-gradient(180deg, rgba(7, 18, 29, .15) 30%, rgba(7, 18, 29, .85) 100%); }
    .pk-auth__panel { display: flex; align-items: center; justify-content: center; padding: 2.5rem 1rem 3.5rem; background: #f8fafc; }
    @media (min-width: 640px) { .pk-auth__panel { padding: 3.5rem 1.5rem; } }
    .pk-auth__card { width: 100%; max-width: 28rem; }
    .pk-field { display: block; width: 100%; border-radius: .9rem; border: 1px solid #cbd5e1; background: #fff; padding: .75rem .95rem; font-size: 1rem; color: #0f172a; transition: border-color .15s ease, box-shadow .15s ease; }
    .pk-field:focus { outline: none; border-color: #13669c; box-shadow: 0 0 0 4px rgba(19, 102, 156, .15); }
    .pk-label { display: block; font-size: .85rem; font-weight: 600; color: #334155; margin-bottom: .4rem; }
</style>

<script>
(function () {
    // Hero slideshow: crossfade between the photos in [data-pk-slides].
    function slides(root) {
        var imgs = root.querySelectorAll('[data-pk-slide]');
        var caps = root.querySelectorAll('[data-pk-cap]');
        var dots = root.querySelectorAll('[data-pk-dot]');
        if (imgs.length < 2) return;
        var i = 0, timer = null;
        function show(n) {
            imgs[i].classList.remove('is-on'); if (caps[i]) caps[i].hidden = true; if (dots[i]) dots[i].classList.remove('is-on');
            i = (n + imgs.length) % imgs.length;
            var im = imgs[i];
            if (im.dataset.src) { im.src = im.dataset.src; im.removeAttribute('data-src'); }
            im.classList.add('is-on'); if (caps[i]) caps[i].hidden = false; if (dots[i]) dots[i].classList.add('is-on');
            var nx = imgs[(i + 1) % imgs.length];
            if (nx.dataset.src) { nx.src = nx.dataset.src; nx.removeAttribute('data-src'); }
        }
        function play() { stop(); if (!document.hidden) timer = setInterval(function () { show(i + 1); }, 7000); }
        function stop() { if (timer) clearInterval(timer); timer = null; }
        dots.forEach(function (d, n) { d.addEventListener('click', function () { show(n); play(); }); });
        document.addEventListener('visibilitychange', function () { document.hidden ? stop() : play(); });
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) play();
        var nx = imgs[1]; if (nx && nx.dataset.src) { setTimeout(function () { nx.src = nx.dataset.src; nx.removeAttribute('data-src'); }, 2500); }
    }

    // "On this page": collect h2s from the main column, fill every [data-pk-toc]
    // list and keep the current section highlighted while scrolling.
    function toc(main) {
        var lists = document.querySelectorAll('[data-pk-toc]');
        if (!lists.length) return;
        var seen = {}, items = [];
        main.querySelectorAll('h2').forEach(function (h) {
            if (h.closest('[data-pk-notoc], details:not([open]) > :not(summary), .rg-live-toolbar, nav, footer, form')) return;
            var r = h.getBoundingClientRect(); if (!r.width && !r.height) return;
            var label = (h.getAttribute('data-toc') || h.textContent || '').replace(/\s+/g, ' ').trim();
            if (!label || label.length > 90) label = label.slice(0, 60).replace(/\s+\S*$/, '') + '...';
            if (!label || seen[label.toLowerCase()]) return; seen[label.toLowerCase()] = 1;
            if (!h.id) h.id = 'sec-' + label.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 48);
            items.push({ id: h.id, label: label, el: h });
        });
        if (items.length < 3) { lists.forEach(function (l) { var box = l.closest('[data-pk-toc-box]'); if (box) box.hidden = true; }); return; }
        lists.forEach(function (l) {
            l.innerHTML = items.map(function (it) { return '<li><a href="#' + it.id + '" data-pk-toc-link="' + it.id + '">' + it.label.replace(/[<>&"]/g, function (c) { return { '<': '&lt;', '>': '&gt;', '&': '&amp;', '"': '&quot;' }[c]; }) + '</a></li>'; }).join('');
            if (l.tagName !== 'OL') l.innerHTML = l.innerHTML.replace(/<\/?li>/g, '');
        });
        var links = document.querySelectorAll('[data-pk-toc-link]');
        var current = null;
        function mark() {
            var off = (parseInt(getComputedStyle(document.documentElement).getPropertyValue('--rg-header-h')) || 70) + 120, cur = items[0];
            for (var k = 0; k < items.length; k++) { if (items[k].el.getBoundingClientRect().top - off <= 0) cur = items[k]; else break; }
            if (cur === current) return; current = cur;
            links.forEach(function (a) {
                var on = a.getAttribute('data-pk-toc-link') === cur.id; a.classList.toggle('is-on', on);
                if (on && a.closest('.pk-jump__row')) { var row = a.closest('.pk-jump__row'); row.scrollTo({ left: a.offsetLeft - 16, behavior: 'smooth' }); }
            });
        }
        var ticking = false;
        window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(function () { ticking = false; mark(); }); } }, { passive: true });
        mark();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-pk-slides]').forEach(slides);
        var main = document.querySelector('[data-pk-main]');
        if (main) toc(main);
        document.querySelectorAll('[data-pk-copy]').forEach(function (b) {
            b.addEventListener('click', function () {
                var u = b.getAttribute('data-pk-copy') || location.href;
                (navigator.clipboard ? navigator.clipboard.writeText(u) : Promise.reject()).then(function () { b.setAttribute('data-done', '1'); b.title = 'Link copied'; setTimeout(function () { b.removeAttribute('data-done'); b.title = 'Copy link'; }, 1800); }).catch(function () { window.prompt('Copy this link', u); });
            });
        });
    });
})();
</script>
