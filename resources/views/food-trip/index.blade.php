@extends('layouts.public')

@section('title') Food Trip — Where to Eat Across the Philippines | Tourist Guide Ph @endsection
@section('meta_description') Honest restaurant guides for every mall, district, and city worth eating in across the Philippines. Skip the photo-only joints. @endsection
@section('canonical') {{ url('/food-trip') }} @endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Food Trip', 'item' => url('/food-trip')],
    ]
]) !!}
</script>
@endsection

@section('content')
<section class="bg-gradient-to-br from-amber-50 via-white to-rose-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700">Food Trip</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">Food Trip across the Philippines</h1>
        <p class="text-lg text-slate-600 max-w-3xl">Restaurant guides by mall, district, city, and destination. Picks that skip the photo-only joints and stick to kitchens that deliver on what they promise.</p>

        <div class="flex flex-wrap gap-3 mt-6">
            <div class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm">
                <span class="font-bold text-amber-700">{{ number_format($stats['total_keywords']) }}</span>
                <span class="text-slate-600">restaurant guides</span>
            </div>
            <div class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm">
                <span class="font-bold text-amber-700">{{ $stats['total_areas'] }}</span>
                <span class="text-slate-600">regions covered</span>
            </div>
            <div class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm">
                <span class="font-bold text-amber-700">{{ $stats['featured_count'] }}</span>
                <span class="text-slate-600">featured restaurants</span>
            </div>
        </div>

        {{-- Typeahead search: filter tabs (All / Regions / Pages /
             Restaurants), big pill input with amber accent, popular chips.
             Reuses the same .rg-search CSS family as the destinations page
             with amber overrides scoped under .rg-search--food. --}}
        <div class="rg-search rg-search--food" data-rg-search>
            <div class="rg-search__tabs" role="tablist" aria-label="Filter food search">
                <button type="button" class="rg-search__tab is-active" role="tab" aria-selected="true" data-rg-filter="all">All</button>
                <button type="button" class="rg-search__tab" role="tab" aria-selected="false" data-rg-filter="region">Regions</button>
                <button type="button" class="rg-search__tab" role="tab" aria-selected="false" data-rg-filter="destination">Food pages</button>
                <button type="button" class="rg-search__tab" role="tab" aria-selected="false" data-rg-filter="restaurant">Restaurants</button>
            </div>

            <div class="rg-search__core">
                <label class="sr-only" for="rg-food-search">Search restaurants and food guides</label>
                <div class="rg-search__shell">
                    <input
                        id="rg-food-search"
                        type="search"
                        class="rg-search__input"
                        placeholder="Search a mall, area, or restaurant. Try BGC, Tagaytay, or Toyo"
                        autocomplete="off"
                        spellcheck="false"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="rg-food-search-panel"
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

                <div class="rg-search__panel" id="rg-food-search-panel" role="listbox" hidden></div>
            </div>

            <div class="rg-search__chips" aria-hidden="true">
                <span>Popular:</span>
                <button type="button" class="rg-search__chip" data-rg-quick="BGC">BGC</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Mall of Asia">Mall of Asia</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Tagaytay">Tagaytay</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Greenhills">Greenhills</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Cebu">Cebu</button>
                <button type="button" class="rg-search__chip" data-rg-quick="Baguio">Baguio</button>
            </div>
        </div>
    </div>
</section>

@if($featuredRestaurants->isNotEmpty())
<section class="py-12 md:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <p class="text-xs sm:text-sm uppercase tracking-[0.18em] text-amber-700 font-bold mb-2">Featured this week</p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">Restaurants worth a detour</h2>
            <p class="text-base md:text-lg text-slate-600 max-w-3xl mx-auto">A rotating shortlist of restaurants listed on the food guides below.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($featuredRestaurants as $r)
                @php
                    $c1 = $r->primary_color ?: '#0f172a';
                    $c2 = $r->secondary_color ?: '#fbbf24';
                @endphp
                <a href="#" class="group block rounded-xl overflow-hidden bg-white border border-slate-200 hover:shadow-lg">
                    <div class="aspect-[4/3] overflow-hidden" style="background: linear-gradient(135deg, {{ $c1 }} 0%, {{ $c2 }} 100%)">
                        @if($r->hero_path)
                            <img src="{{ asset('storage/' . $r->hero_path) }}" alt="{{ $r->name }}" loading="lazy" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl text-white/80">🍴</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-slate-900 group-hover:text-brand-600">{{ $r->name }}</h3>
                        @if($r->cuisine)
                            <p class="text-xs uppercase tracking-wide font-bold mt-1" style="color: {{ $c1 }}">{{ $r->cuisine }}</p>
                        @endif
                        <p class="text-sm text-slate-500 mt-1">{{ $r->city }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Browse by region</h2>

        @foreach($groups as $g)
            <section id="cluster-{{ \Illuminate\Support\Str::slug($g['cluster_tag']) }}" class="mb-12 scroll-mt-24">
                <div class="flex items-end justify-between mb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ $g['name'] }}</h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $g['count'] }} restaurant guide{{ $g['count'] === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($g['keywords'] as $k)
                        <a href="{{ url($k->slug) }}" class="group block p-4 rounded-lg border border-slate-200 hover:border-amber-300 hover:shadow-md hover:bg-amber-50/30">
                            <h3 class="font-semibold text-slate-900 group-hover:text-amber-700 capitalize">{{ $k->phrase }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ number_format($k->search_volume_monthly) }} people search this monthly</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Run a restaurant in any of these areas?</h2>
        <p class="text-slate-600 mb-5">List your restaurant on the food guides where your future diners are already searching.</p>
        <a href="{{ route('register') }}" class="inline-block px-6 py-3 rounded-md bg-amber-600 text-white font-semibold hover:bg-amber-700">List your restaurant</a>
    </div>
</section>

@push('head')
<style>
    /* Food-trip typeahead search — mirrors .rg-search from destinations but
       swaps the active-tab + accent palette from slate/blue to slate/amber
       so it reads as the food sibling. Scoped under .rg-search--food so it
       doesn't bleed into the destinations search if both ever appear on
       the same page. */
    .rg-search {
        max-width: 920px;
        width: 100%;
        margin: 2.5rem auto 0;
        position: relative;
        z-index: 30;
    }
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
    .rg-search__tab:hover {
        border-color: #94a3b8;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .rg-search--food .rg-search__tab.is-active {
        background: #0f172a;
        border-color: #0f172a;
        color: #fff;
        box-shadow: 0 6px 14px -4px rgba(15, 23, 42, 0.35);
    }
    @media (min-width: 768px) {
        .rg-search__tab { padding: 0.7rem 1.35rem; font-size: 0.95rem; }
    }
    .rg-search__core { position: relative; }
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
        .rg-search__shell { padding: 0.55rem 0.55rem 0.55rem 2.25rem; }
    }
    .rg-search--food .rg-search__shell:focus-within { border-color: #d97706; }
    .rg-search__input {
        flex: 1 1 auto;
        min-width: 0;
        background: transparent !important;
        border: 0 !important;
        outline: 0 !important;
        box-shadow: none !important;
        appearance: none !important;
        -webkit-appearance: none !important;
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
        box-shadow: none !important;
        border: 0 !important;
        background: transparent !important;
    }
    .rg-search__input:-webkit-autofill,
    .rg-search__input:-webkit-autofill:hover,
    .rg-search__input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
        -webkit-text-fill-color: #0f172a !important;
    }
    @media (min-width: 768px) {
        .rg-search__input { font-size: 1.4rem; padding: 1.15rem 0.5rem 1.15rem 0; }
    }
    .rg-search__input::placeholder { color: #94a3b8; font-weight: 400; }
    .rg-search__input::-webkit-search-cancel-button,
    .rg-search__input::-webkit-search-decoration { display: none !important; -webkit-appearance: none !important; }

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

    .rg-search--food .rg-search__submit {
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
        .rg-search--food .rg-search__submit { width: 4rem; height: 4rem; }
    }
    .rg-search__submit svg { width: 1.45rem; height: 1.45rem; }
    @media (min-width: 768px) { .rg-search__submit svg { width: 1.6rem; height: 1.6rem; } }
    .rg-search--food .rg-search__submit:hover {
        background: #d97706;
        transform: scale(1.06);
    }

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
    .rg-search__chips > span { font-weight: 700; letter-spacing: 0.02em; margin-right: 0.25rem; }
    .rg-search__chip {
        background: rgba(255, 255, 255, 0.85);
        border: 1.5px solid #e2e8f0;
        color: #334155;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
    }
    .rg-search--food .rg-search__chip:hover {
        background: #d97706;
        border-color: #d97706;
        color: #fff;
        transform: translateY(-1px);
    }

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
        animation: rgFoodSearchFade 0.18s ease-out;
    }
    @keyframes rgFoodSearchFade {
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
    .rg-search--food .rg-search__opt:hover,
    .rg-search--food .rg-search__opt.is-active { background: rgba(217, 119, 6, 0.08); }
    .rg-search__opt-thumb {
        flex: 0 0 auto;
        width: 3rem;
        height: 3rem;
        border-radius: 0.7rem;
        background: rgba(217, 119, 6, 0.1);
        color: #b45309;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .rg-search__opt-thumb svg { width: 1.35rem; height: 1.35rem; }
    .rg-search__opt-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .rg-search--food .rg-search__opt[data-type="region"] .rg-search__opt-thumb { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .rg-search--food .rg-search__opt[data-type="restaurant"] .rg-search__opt-thumb { background: rgba(220, 38, 38, 0.12); color: #b91c1c; }
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
        background: rgba(252, 211, 77, 0.55);
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
        background: #fef3c7;
        color: #92400e;
    }
    .rg-search--food .rg-search__opt[data-type="region"] .rg-search__opt-chip { background: rgba(16, 185, 129, 0.15); color: #047857; }
    .rg-search--food .rg-search__opt[data-type="restaurant"] .rg-search__opt-chip { background: rgba(220, 38, 38, 0.12); color: #b91c1c; }
    .rg-search__opt-arrow { flex: 0 0 auto; color: #cbd5e1; transition: transform 0.15s ease, color 0.15s ease; }
    .rg-search--food .rg-search__opt.is-active .rg-search__opt-arrow,
    .rg-search--food .rg-search__opt:hover .rg-search__opt-arrow {
        color: #d97706;
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
<script id="rg-food-search-data" type="application/json">{!! json_encode($searchIndex, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
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
    const dataEl = document.getElementById('rg-food-search-data');

    let index = [];
    try { index = JSON.parse(dataEl.textContent); } catch (e) { index = []; }

    const ORDER = { region: 0, destination: 1, restaurant: 2 };
    const LABELS = { region: 'Regions', destination: 'Food pages', restaurant: 'Restaurants' };
    const CHIP = { region: 'Region', destination: 'Page', restaurant: 'Restaurant' };
    const MAX_PER_GROUP = { region: 3, destination: 6, restaurant: 4 };

    let results = [];
    let activeIdx = -1;
    let debounceId = 0;
    let currentFilter = 'all';

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        }[c]));
    }
    function escapeRe(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
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
        if (new RegExp('\\b' + escapeRe(q)).test(h)) return 800;
        if (tokens.every(t => h.includes(t))) {
            const bonus = tokens.some(t => new RegExp('\\b' + escapeRe(t)).test(h)) ? 50 : 0;
            return 500 + bonus;
        }
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
        if (currentFilter !== 'all') return scored.slice(0, 12).map(r => r.item);
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
        if (type === 'restaurant' && image) return '';
        if (type === 'region') {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3z"/><path d="M9 4v13M15 7v13"/></svg>';
        }
        if (type === 'restaurant') {
            // Fork + knife (restaurant icon).
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3v8m0 0v10M5 3v6a3 3 0 0 0 3 3"/><path d="M16 3v18M19 3v6a3 3 0 0 1-3 3"/></svg>';
        }
        // Food page (destination type) — plate-with-utensils.
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3.5"/></svg>';
    }
    function render(q) {
        if (!results.length) {
            panel.innerHTML =
                '<div class="rg-search__empty">No matches for <strong>"' + escapeHtml(q) + '"</strong>.<br>' +
                'Try a mall (BGC, MOA), an area (Tagaytay, Baguio), or a restaurant name.</div>';
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
            const thumbStyle = (item.type === 'restaurant' && item.image)
                ? ' style="background-image:url(\'' + item.image.replace(/'/g, '%27').replace(/"/g, '%22') + '\');background-size:cover;background-position:center;background-repeat:no-repeat"'
                : '';
            parts.push(
                '<a class="rg-search__opt" role="option" data-type="' + item.type +
                '" data-idx="' + optIdx + '" id="rg-food-opt-' + optIdx + '" href="' + escapeHtml(item.url) + '">' +
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
    function runSearch(q) { results = search(q); render(q); }

    input.addEventListener('input', e => {
        const q = e.target.value;
        clearBtn.hidden = !q;
        clearTimeout(debounceId);
        if (!q.trim()) { close(); return; }
        debounceId = setTimeout(() => runSearch(q), 60);
    });
    input.addEventListener('focus', () => { if (input.value.trim()) runSearch(input.value); });
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
        if (input.value.trim()) runSearch(input.value);
        input.focus();
    });
    document.addEventListener('click', e => { if (!root.contains(e.target)) close(); });
})();
</script>
@endpush
@endsection
