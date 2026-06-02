@extends('layouts.public')

@section('title') All Destinations Across the Philippines | {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }} @endsection
@section('meta_description') Browse every resort, hotel, and beach destination we cover across the Philippines, organized by region. From Batangas weekend pools to Palawan island hops. @endsection
@section('canonical') {{ url('/destinations') }} @endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => url('/destinations')],
    ]
]) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'All Destinations',
    'description' => 'Browse resort and hotel destinations across the Philippines',
    'url' => url('/destinations'),
]) !!}
</script>
@endsection

@section('content')
<section class="bg-gradient-to-br from-brand-50 via-white to-emerald-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700">Destinations</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">All destinations across the Philippines</h1>
        <p class="text-lg text-slate-600 max-w-3xl">Browse every region we cover, from Luzon's ridge towns and weekend pools to the quieter Mindanao beaches and the postcard islands of the Visayas and Palawan.</p>

        {{-- TripAdvisor-style typeahead search: filter tabs above, big
             pill input with thick dark border + offset shadow, single
             right-side dark circular submit. Client-side typeahead over
             regions, keyword pages, and featured tourist spots. --}}
        <div class="rg-search" data-rg-search>
            <div class="rg-search__tabs" role="tablist" aria-label="Filter search">
                <button type="button" class="rg-search__tab is-active" role="tab" aria-selected="true" data-rg-filter="all">All</button>
                <button type="button" class="rg-search__tab" role="tab" aria-selected="false" data-rg-filter="region">Regions</button>
                <button type="button" class="rg-search__tab" role="tab" aria-selected="false" data-rg-filter="destination">Destinations</button>
                <button type="button" class="rg-search__tab" role="tab" aria-selected="false" data-rg-filter="spot">Tourist spots</button>
            </div>

            <div class="rg-search__core">
                <label class="sr-only" for="rg-dest-search">Search destinations</label>
                <div class="rg-search__shell">
                    <input
                        id="rg-dest-search"
                        type="search"
                        class="rg-search__input"
                        placeholder="Where to? Try Cebu, Palawan, or Mayon"
                        autocomplete="off"
                        spellcheck="false"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="rg-dest-search-panel"
                    />
                    <button type="button" class="rg-search__clear" aria-label="Clear search" hidden>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                    <button type="button" class="rg-search__submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>
                    </button>
                </div>

                <div class="rg-search__panel" id="rg-dest-search-panel" role="listbox" hidden></div>
            </div>

            <div class="rg-search__chips" aria-hidden="true">
                <span>Popular:</span>
                <button type="button" class="rg-search__chip" data-rg-quick="Cebu">Cebu</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Palawan">Palawan</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Tagaytay">Tagaytay</button>
                <button type="button" class="rg-search__chip" data-rg-quick="La Union">La Union</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Boracay">Boracay</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Bicol">Bicol</button>
            </div>
        </div>
    </div>
</section>

{{-- Featured spots Splide carousel: large rotating showcase of iconic
     tourist attractions across the country. Auto-plays, loops, and
     reuses the site-wide Splide CDN already loaded by public.blade.php. --}}
@if(!empty($featuredSpots))
<section class="py-12 md:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <p class="text-xs sm:text-sm uppercase tracking-[0.18em] text-brand-700 font-bold mb-2">Across the Philippines</p>
            <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-3">Tourist spots worth the trip</h2>
            <p class="text-base md:text-lg text-slate-600 max-w-3xl mx-auto">A snapshot of the country, from the limestone lagoons of Palawan and the heritage cobblestones of Vigan to the volcano lakes of Cavite and the white sands of Visayas. Slide through and tap any spot to see the resorts and hotels closest to it.</p>
        </div>

        <section class="rg-spots-splide splide" aria-label="Featured tourist spots carousel">
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach($featuredSpots as $spot)
                        <li class="splide__slide">
                            <a href="{{ url($spot['slug']) }}" class="rg-spot-card group">
                                <img src="{{ asset('storage/' . $spot['image']) }}" alt="{{ $spot['name'] }} in {{ $spot['location'] }}" loading="lazy" class="rg-spot-img">
                                <div class="rg-spot-overlay"></div>
                                <div class="rg-spot-content">
                                    <div class="rg-spot-region">{{ $spot['region'] }}</div>
                                    <div class="rg-spot-name">{{ $spot['name'] }}</div>
                                    <div class="rg-spot-location">
                                        <svg class="w-4 h-4 inline-block opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                        {{ $spot['location'] }}
                                    </div>
                                    <div class="rg-spot-cta">
                                        See nearby stays
                                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>
</section>

@push('head')
<style>
    /* Featured-spots carousel: large slides (480px desktop, smaller on mobile)
       with full-bleed image + dark gradient + overlay text. */
    .rg-spots-splide { border-radius: 1rem; overflow: visible; }
    .rg-spots-splide .splide__list { align-items: stretch; }
    .rg-spots-splide .splide__slide { padding: 0; }
    .rg-spots-splide .splide__arrow { background: rgba(15,23,42,0.7); width: 2.75rem; height: 2.75rem; opacity: 0.95; }
    .rg-spots-splide .splide__arrow:hover { background: #2563eb; }
    .rg-spots-splide .splide__arrow svg { fill: #fff; width: 1rem; height: 1rem; }
    .rg-spots-splide .splide__arrow--prev { left: -0.5rem; }
    .rg-spots-splide .splide__arrow--next { right: -0.5rem; }
    @media (min-width: 768px) {
        .rg-spots-splide .splide__arrow--prev { left: -1.25rem; }
        .rg-spots-splide .splide__arrow--next { right: -1.25rem; }
    }
    .rg-spots-splide .splide__pagination { bottom: -1.75rem; }
    .rg-spots-splide .splide__pagination__page { background: #cbd5e1; opacity: 1; }
    .rg-spots-splide .splide__pagination__page.is-active { background: #2563eb; transform: scale(1.3); }

    .rg-spot-card {
        position: relative;
        display: block;
        height: 360px;
        overflow: hidden;
        border-radius: 1rem;
        background: #e2e8f0;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.15);
        transition: transform 0.35s ease, box-shadow 0.35s ease;
    }
    @media (min-width: 768px) { .rg-spot-card { height: 420px; } }
    @media (min-width: 1024px) { .rg-spot-card { height: 480px; } }

    .rg-spot-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.35);
    }
    .rg-spot-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .rg-spot-card:hover .rg-spot-img { transform: scale(1.06); }
    .rg-spot-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.05) 0%, rgba(15, 23, 42, 0.35) 50%, rgba(15, 23, 42, 0.9) 100%);
        pointer-events: none;
    }
    .rg-spot-content {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 1.5rem 1.75rem;
        color: #fff;
    }
    .rg-spot-region {
        display: inline-block;
        font-size: 0.7rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        font-weight: 700;
        color: #fef3c7;
        background: rgba(0,0,0,0.35);
        padding: 0.25rem 0.6rem;
        border-radius: 0.35rem;
        margin-bottom: 0.5rem;
    }
    .rg-spot-name {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.1;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.45);
        margin-bottom: 0.6rem;
    }
    @media (min-width: 768px) { .rg-spot-name { font-size: 2rem; } }
    .rg-spot-location {
        font-size: 0.9rem;
        opacity: 0.95;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.85rem;
    }
    .rg-spot-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        background: rgba(37, 99, 235, 0.95);
        padding: 0.55rem 0.95rem;
        border-radius: 0.45rem;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .rg-spot-card:hover .rg-spot-cta {
        background: #1d4ed8;
        transform: translateX(3px);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Splide === 'undefined') return;
        document.querySelectorAll('.rg-spots-splide').forEach(function (el) {
            new Splide(el, {
                type: 'loop',
                perPage: 3,
                perMove: 1,
                gap: '1.25rem',
                autoplay: true,
                interval: 4500,
                speed: 700,
                pauseOnHover: true,
                arrows: true,
                pagination: true,
                breakpoints: {
                    1024: { perPage: 2, gap: '1rem' },
                    640: { perPage: 1, gap: '0.75rem' },
                },
            }).mount();
        });
    });
</script>
@endpush
@endif

<section class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Jump to region</h2>
        <div class="flex flex-wrap gap-2 mb-12">
            @foreach($orderedClusters as $c)
                <a href="#{{ $c['slug'] }}" class="px-3 py-1.5 text-sm rounded-full bg-slate-100 hover:bg-brand-100 hover:text-brand-700 text-slate-700 transition">
                    {{ $c['name'] }} <span class="text-slate-500">({{ $c['count'] }})</span>
                </a>
            @endforeach
        </div>

        @foreach($orderedClusters as $cluster)
            <section id="{{ $cluster['slug'] }}" class="mb-14 scroll-mt-20">
                <div class="flex items-end justify-between mb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">
                            <a href="{{ route('destinations.cluster', $cluster['slug']) }}" class="hover:text-brand-600">{{ $cluster['name'] }}</a>
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $cluster['tagline'] }}</p>
                    </div>
                    <a href="{{ route('destinations.cluster', $cluster['slug']) }}" class="text-sm text-brand-600 font-semibold hover:underline">{{ $cluster['count'] }} destinations &rarr;</a>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 rg-stagger">
                    @foreach($cluster['keywords'] as $k)
                        <a href="{{ url($k->slug) }}" class="group block p-4 rounded-lg border border-slate-200 hover:border-brand-300 hover:shadow-md hover:bg-brand-50/30 rg-card-lift">
                            <h3 class="font-semibold text-slate-900 group-hover:text-brand-700 capitalize">{{ $k->phrase }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ number_format($k->search_volume_monthly) }} people search this each month</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Run a property in any of these regions?</h2>
        <p class="text-slate-600 mb-5">List your resort, hotel, or beach house on the destination pages your future guests are already searching.</p>
        <a href="{{ route('register') }}" class="inline-block px-6 py-3 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Start listing free</a>
    </div>
</section>

@push('head')
<style>
    /* ── Destinations typeahead search (TripAdvisor-inspired) ────────── */
    .rg-search {
        max-width: 920px;
        width: 100%;
        margin: 2.5rem auto 0;
        position: relative;
        z-index: 30;
    }

    /* ─ Filter tabs above the bar ─ */
    .rg-search__tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .rg-search__tab {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.1rem;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 999px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
    }
    .rg-search__tab svg { width: 16px; height: 16px; flex-shrink: 0; }
    .rg-search__tab:hover {
        border-color: #94a3b8;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .rg-search__tab.is-active {
        background: #0f172a;
        border-color: #0f172a;
        color: #fff;
        box-shadow: 0 6px 14px -4px rgba(15, 23, 42, 0.35);
    }
    @media (min-width: 768px) {
        .rg-search__tab { padding: 0.7rem 1.35rem; font-size: 0.95rem; }
        .rg-search__tab svg { width: 18px; height: 18px; }
    }

    /* ─ Wrapper for shell + dropdown panel ─ */
    .rg-search__core { position: relative; }

    /* ─ The big pill bar (flat — no offset shadow) ─ */
    .rg-search__shell {
        position: relative;
        display: flex;
        align-items: center;
        background: #fff;
        border: 2px solid #0f172a;
        border-radius: 999px;
        padding: 0.45rem 0.45rem 0.45rem 1.6rem;
        transition: border-color 0.18s ease;
    }
    @media (min-width: 768px) {
        .rg-search__shell {
            padding: 0.55rem 0.55rem 0.55rem 2.25rem;
        }
    }
    .rg-search__shell:focus-within {
        border-color: rgb(37, 99, 235);
    }

    /* The inner input — every conceivable native focus chrome killed
       with !important, including Chrome autofill and the inner border
       some browsers draw on type="search" elements. */
    .rg-search__input {
        flex: 1 1 auto;
        min-width: 0;
        background: transparent !important;
        border: 0 !important;
        outline: 0 !important;
        outline-offset: 0 !important;
        box-shadow: none !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        border-radius: 0;
        font-size: 1.05rem;
        line-height: 1.25;
        color: #0f172a;
        padding: 0.95rem 0.5rem 0.95rem 0;
        font-weight: 500;
        -webkit-tap-highlight-color: transparent;
    }
    .rg-search__input:hover,
    .rg-search__input:focus,
    .rg-search__input:focus-visible,
    .rg-search__input:active {
        outline: 0 !important;
        outline-offset: 0 !important;
        box-shadow: none !important;
        border: 0 !important;
        background: transparent !important;
    }
    /* Chrome autofill yellow box also draws an inner ring — kill it. */
    .rg-search__input:-webkit-autofill,
    .rg-search__input:-webkit-autofill:hover,
    .rg-search__input:-webkit-autofill:focus,
    .rg-search__input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
        box-shadow: 0 0 0 1000px #fff inset !important;
        -webkit-text-fill-color: #0f172a !important;
        caret-color: #0f172a;
    }
    @media (min-width: 768px) {
        .rg-search__input {
            font-size: 1.4rem;
            padding: 1.15rem 0.5rem 1.15rem 0;
        }
    }
    .rg-search__input::placeholder { color: #94a3b8; font-weight: 400; }
    .rg-search__input::-webkit-search-cancel-button,
    .rg-search__input::-webkit-search-decoration,
    .rg-search__input::-webkit-search-results-button,
    .rg-search__input::-webkit-search-results-decoration { display: none !important; -webkit-appearance: none !important; }

    .rg-search__clear {
        flex: 0 0 auto;
        width: 2.4rem;
        height: 2.4rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        border: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.55rem;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .rg-search__clear svg { width: 0.95rem; height: 0.95rem; }
    .rg-search__clear:hover { background: #e2e8f0; color: #0f172a; }

    .rg-search__submit {
        flex: 0 0 auto;
        width: 3.4rem;
        height: 3.4rem;
        border-radius: 999px;
        background: #0f172a;
        color: #fff;
        border: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.18s ease, transform 0.18s ease;
    }
    @media (min-width: 768px) {
        .rg-search__submit { width: 4rem; height: 4rem; }
    }
    .rg-search__submit svg { width: 1.45rem; height: 1.45rem; }
    @media (min-width: 768px) {
        .rg-search__submit svg { width: 1.6rem; height: 1.6rem; }
    }
    .rg-search__submit:hover {
        background: rgb(37, 99, 235);
        transform: scale(1.06);
    }

    /* ─ Quick-pick chips ─ */
    .rg-search__chips {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        margin-top: 1.1rem;
        font-size: 0.8rem;
        color: #64748b;
    }
    .rg-search__chips > span {
        font-weight: 700;
        letter-spacing: 0.02em;
        margin-right: 0.25rem;
    }
    .rg-search__chip {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(4px);
        border: 1.5px solid #e2e8f0;
        color: #334155;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
    }
    .rg-search__chip:hover {
        background: rgb(37, 99, 235);
        border-color: rgb(37, 99, 235);
        color: #fff;
        transform: translateY(-1px);
    }

    /* ─ Dropdown results panel ─ */
    .rg-search__panel {
        position: absolute;
        top: calc(100% + 0.75rem);
        left: 0;
        right: 0;
        max-height: 32rem;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1.25rem;
        box-shadow: 0 30px 70px -20px rgba(15, 23, 42, 0.3), 0 10px 24px -8px rgba(15, 23, 42, 0.15);
        padding: 0.6rem;
        z-index: 40;
        animation: rgSearchFade 0.18s ease-out;
    }
    @keyframes rgSearchFade {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rg-search__group-label {
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: #94a3b8;
        padding: 0.75rem 1rem 0.4rem;
    }
    .rg-search__opt {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.8rem 1rem;
        border-radius: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        transition: background 0.12s ease;
    }
    .rg-search__opt:hover,
    .rg-search__opt.is-active { background: rgba(37, 99, 235, 0.08); }
    .rg-search__opt-thumb {
        flex: 0 0 auto;
        width: 3rem;
        height: 3rem;
        border-radius: 0.7rem;
        background: rgba(37, 99, 235, 0.1);
        color: rgb(37, 99, 235);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .rg-search__opt-thumb svg { width: 1.35rem; height: 1.35rem; }
    .rg-search__opt-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .rg-search__opt[data-type="region"] .rg-search__opt-thumb { background: rgba(16, 185, 129, 0.12); color: rgb(5, 150, 105); }
    .rg-search__opt[data-type="spot"]   .rg-search__opt-thumb { background: rgba(245, 158, 11, 0.14); color: rgb(180, 83, 9); }
    /* Force label and sub onto separate lines — spans default to inline,
       which made them run together with no separator. */
    .rg-search__opt-body {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .rg-search__opt-label {
        display: block;
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        text-transform: capitalize;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rg-search__opt-label mark {
        background: rgba(250, 204, 21, 0.45);
        color: inherit;
        padding: 0 0.05em;
        border-radius: 0.2em;
        font-weight: 700;
    }
    .rg-search__opt-sub {
        display: block;
        font-size: 0.85rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .rg-search__opt-chip {
        flex: 0 0 auto;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
    }
    .rg-search__opt[data-type="region"] .rg-search__opt-chip { background: rgba(16, 185, 129, 0.15); color: rgb(5, 122, 85); }
    .rg-search__opt[data-type="spot"]   .rg-search__opt-chip { background: rgba(245, 158, 11, 0.18); color: rgb(146, 64, 14); }
    .rg-search__opt-arrow { flex: 0 0 auto; color: #cbd5e1; transition: transform 0.15s ease, color 0.15s ease; }
    .rg-search__opt.is-active .rg-search__opt-arrow,
    .rg-search__opt:hover .rg-search__opt-arrow {
        color: rgb(37, 99, 235);
        transform: translateX(2px);
    }

    .rg-search__empty {
        padding: 1.5rem 1rem;
        text-align: center;
        color: #64748b;
        font-size: 0.95rem;
    }
    .rg-search__empty strong { color: #0f172a; font-weight: 700; }
</style>
@endpush

@push('scripts')
<script id="rg-dest-search-data" type="application/json">{!! json_encode($searchIndex, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script>
(function () {
    const root = document.querySelector('[data-rg-search]');
    if (!root) return;
    const input = root.querySelector('.rg-search__input');
    const panel = root.querySelector('.rg-search__panel');
    const clearBtn = root.querySelector('.rg-search__clear');
    const chips = root.querySelectorAll('.rg-search__chip');
    const tabs = root.querySelectorAll('.rg-search__tab');
    const submitBtn = root.querySelector('.rg-search__submit');
    const dataEl = document.getElementById('rg-dest-search-data');

    let index = [];
    try { index = JSON.parse(dataEl.textContent); } catch (e) { index = []; }

    const ORDER = { region: 0, destination: 1, spot: 2 };
    const LABELS = { region: 'Regions', destination: 'Destinations', spot: 'Tourist spots' };
    const CHIP = { region: 'Region', destination: 'Page', spot: 'Spot' };
    const MAX_PER_GROUP = { region: 3, destination: 6, spot: 4 };

    let results = [];
    let activeIdx = -1;
    let debounceId = 0;
    let currentFilter = 'all';

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }

    function escapeRe(s) {
        return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function highlight(text, tokens) {
        let out = escapeHtml(text);
        for (const t of tokens) {
            if (!t) continue;
            const re = new RegExp('(' + escapeRe(t) + ')', 'gi');
            out = out.replace(re, '<mark>$1</mark>');
        }
        return out;
    }

    function score(item, q, tokens) {
        const h = item.haystack;
        if (h.startsWith(q)) return 1000;
        // word-boundary start match on full query
        if (new RegExp('\\b' + escapeRe(q)).test(h)) return 800;
        // every token must hit somewhere
        if (tokens.every(t => h.includes(t))) {
            // bonus if any token sits at a word boundary
            const bonus = tokens.some(t => new RegExp('\\b' + escapeRe(t)).test(h)) ? 50 : 0;
            return 500 + bonus;
        }
        // loose substring of whole query
        if (h.includes(q)) return 300;
        return 0;
    }

    function search(q) {
        const ql = q.toLowerCase().trim();
        if (!ql) return [];
        const tokens = ql.split(/\s+/).filter(Boolean);
        const scored = [];
        for (const item of index) {
            if (currentFilter !== 'all' && item.type !== currentFilter) continue;
            const s = score(item, ql, tokens);
            if (s > 0) scored.push({ item, s });
        }
        scored.sort((a, b) => (b.s - a.s) || (b.item.volume - a.item.volume));

        if (currentFilter !== 'all') {
            return scored.slice(0, 12).map(r => r.item);
        }

        // "All" tab: per-type cap so a popular query like "cebu" doesn't
        // drown out spots, then re-order so groups render cleanly.
        const caps = Object.assign({}, MAX_PER_GROUP);
        const picked = [];
        for (const r of scored) {
            const t = r.item.type;
            if (caps[t] > 0) { caps[t]--; picked.push(r.item); }
            if (picked.length >= 12) break;
        }
        picked.sort((a, b) =>
            (ORDER[a.type] - ORDER[b.type]) ||
            ((b.volume || 0) - (a.volume || 0))
        );
        return picked;
    }

    function iconFor(type, image) {
        // When a spot has an image, the thumb gets a background-image style
        // directly (see thumbAttrs below). Returning '' here keeps the SVG
        // out of the way so the photo isn't covered by an icon.
        if (type === 'spot' && image) return '';
        if (type === 'region') {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3z"/><path d="M9 4v13M15 7v13"/></svg>';
        }
        if (type === 'spot') {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/></svg>';
        }
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5-8 12-8 12S4 15 4 10a8 8 0 0 1 8-8z"/></svg>';
    }

    function render(q) {
        if (!results.length) {
            panel.innerHTML =
                '<div class="rg-search__empty">No matches for <strong>"' + escapeHtml(q) + '"</strong>.<br>' +
                'Try a region (Palawan, Cebu, Bicol) or a town (Tagaytay, Vigan, Boracay).</div>';
            panel.hidden = false;
            input.setAttribute('aria-expanded', 'true');
            return;
        }
        const tokens = q.toLowerCase().trim().split(/\s+/).filter(Boolean);
        const parts = [];
        let lastType = null;
        let optIdx = 0;
        for (const item of results) {
            if (item.type !== lastType) {
                parts.push('<div class="rg-search__group-label">' + LABELS[item.type] + '</div>');
                lastType = item.type;
            }
            const thumbStyle = (item.type === 'spot' && item.image)
                ? ' style="background-image:url(\'' + item.image.replace(/'/g, '%27').replace(/"/g, '%22') + '\');background-size:cover;background-position:center;background-repeat:no-repeat"'
                : '';
            parts.push(
                '<a class="rg-search__opt" role="option" data-type="' + item.type +
                '" data-idx="' + optIdx + '" id="rg-opt-' + optIdx + '" href="' + escapeHtml(item.url) + '">' +
                    '<span class="rg-search__opt-thumb"' + thumbStyle + '>' + iconFor(item.type, item.image) + '</span>' +
                    '<span class="rg-search__opt-body">' +
                        '<span class="rg-search__opt-label">' + highlight(item.label, tokens) + '</span>' +
                        '<span class="rg-search__opt-sub">' + escapeHtml(item.sub) + '</span>' +
                    '</span>' +
                    '<span class="rg-search__opt-chip">' + CHIP[item.type] + '</span>' +
                    '<svg class="rg-search__opt-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>' +
                '</a>'
            );
            optIdx++;
        }
        panel.innerHTML = parts.join('');
        panel.hidden = false;
        input.setAttribute('aria-expanded', 'true');
        setActive(-1);
    }

    function close() {
        panel.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
        activeIdx = -1;
    }

    function setActive(i) {
        const opts = panel.querySelectorAll('.rg-search__opt');
        if (!opts.length) return;
        opts.forEach(o => o.classList.remove('is-active'));
        if (i < 0 || i >= opts.length) {
            activeIdx = -1;
            input.removeAttribute('aria-activedescendant');
            return;
        }
        activeIdx = i;
        const el = opts[i];
        el.classList.add('is-active');
        el.scrollIntoView({ block: 'nearest' });
        input.setAttribute('aria-activedescendant', el.id);
    }

    function runSearch(q) {
        results = search(q);
        render(q);
    }

    input.addEventListener('input', e => {
        const q = e.target.value;
        clearBtn.hidden = !q;
        clearTimeout(debounceId);
        if (!q.trim()) { close(); return; }
        debounceId = setTimeout(() => runSearch(q), 60);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim()) runSearch(input.value);
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'ArrowDown') {
            if (panel.hidden && input.value.trim()) runSearch(input.value);
            e.preventDefault();
            const opts = panel.querySelectorAll('.rg-search__opt');
            setActive(Math.min(activeIdx + 1, opts.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const opts = panel.querySelectorAll('.rg-search__opt');
            setActive(activeIdx <= 0 ? opts.length - 1 : activeIdx - 1);
        } else if (e.key === 'Enter') {
            const opts = panel.querySelectorAll('.rg-search__opt');
            if (!opts.length) return;
            e.preventDefault();
            const target = activeIdx >= 0 ? opts[activeIdx] : opts[0];
            if (target) window.location.href = target.getAttribute('href');
        } else if (e.key === 'Escape') {
            if (input.value) { input.value = ''; clearBtn.hidden = true; }
            close();
        }
    });

    // Use mousedown so we navigate before the input loses focus + closes the panel.
    panel.addEventListener('mousedown', e => {
        const opt = e.target.closest('.rg-search__opt');
        if (!opt) return;
        e.preventDefault();
        window.location.href = opt.getAttribute('href');
    });

    panel.addEventListener('mouseover', e => {
        const opt = e.target.closest('.rg-search__opt');
        if (!opt) return;
        setActive(parseInt(opt.dataset.idx, 10));
    });

    clearBtn.addEventListener('click', () => {
        input.value = '';
        clearBtn.hidden = true;
        close();
        input.focus();
    });

    chips.forEach(c => c.addEventListener('click', () => {
        const q = c.dataset.rgQuick;
        input.value = q;
        clearBtn.hidden = false;
        input.focus();
        runSearch(q);
    }));

    tabs.forEach(t => t.addEventListener('click', () => {
        tabs.forEach(x => {
            x.classList.remove('is-active');
            x.setAttribute('aria-selected', 'false');
        });
        t.classList.add('is-active');
        t.setAttribute('aria-selected', 'true');
        currentFilter = t.dataset.rgFilter || 'all';
        if (input.value.trim()) runSearch(input.value);
        input.focus();
    }));

    submitBtn.addEventListener('click', () => {
        const opts = panel.querySelectorAll('.rg-search__opt');
        if (opts.length) {
            const target = activeIdx >= 0 ? opts[activeIdx] : opts[0];
            window.location.href = target.getAttribute('href');
            return;
        }
        if (input.value.trim()) {
            runSearch(input.value);
        }
        input.focus();
    });

    document.addEventListener('click', e => {
        if (!root.contains(e.target)) close();
    });
})();
</script>
@endpush
@endsection
