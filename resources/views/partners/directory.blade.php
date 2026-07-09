@extends('layouts.public')

@section('title', 'Partner Directory · Tourism Businesses Across the Philippines')
@section('meta_description', 'Browse the Tourist Guide PH partner directory. Hotels, resorts, tour guides, travel and tours, massage and spa, surf and dive schools, and more. Filter by type or type a place to find partners for your trip.')
@section('canonical', url('/partner-directory'))

@section('content')
@php
    $grad = [
        'brand' => 'from-brand-500 to-brand-700', 'emerald' => 'from-emerald-500 to-emerald-700',
        'amber' => 'from-amber-400 to-amber-600', 'rose' => 'from-rose-500 to-rose-700',
        'orange' => 'from-orange-400 to-orange-600', 'violet' => 'from-violet-500 to-violet-700',
        'sky' => 'from-sky-400 to-sky-600', 'cyan' => 'from-cyan-400 to-cyan-600',
        'indigo' => 'from-indigo-500 to-indigo-700', 'teal' => 'from-teal-500 to-teal-700',
        'slate' => 'from-slate-500 to-slate-700',
    ];
    $slides = [
        'rg-media/business-with-badge.webp',
        'rg-media/feature-friends.webp',
        'rg-media/editorial-ph-1.webp',
        'rg-media/spots/el-nido-big-lagoon-tour-a.jpg',
    ];
@endphp

<style>
    .pd-tab{display:inline-flex;align-items:center;gap:.45rem;padding:.42rem .9rem;border-radius:9999px;font-size:.8rem;font-weight:600;border:1px solid #e2e8f0;background:#fff;color:#334155;cursor:pointer;transition:all .15s}
    .pd-tab:hover{border-color:#cbd5e1;background:#f8fafc}
    .pd-tab.tab-active{background:#0f172a;border-color:#0f172a;color:#fff}
    .pd-tab.tab-active .pd-count{color:#cbd5e1}
    .pd-slide{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;animation:pdCross 20s ease-in-out infinite}
    .pd-slide:nth-child(1){animation-delay:0s}
    .pd-slide:nth-child(2){animation-delay:5s}
    .pd-slide:nth-child(3){animation-delay:10s}
    .pd-slide:nth-child(4){animation-delay:15s}
    @keyframes pdCross{0%{opacity:0}5%{opacity:1}25%{opacity:1}30%{opacity:0}100%{opacity:0}}
    @keyframes pdGridFade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
    .pd-page{min-width:2.5rem;height:2.5rem;padding:0 .6rem;display:inline-flex;align-items:center;justify-content:center;border-radius:9999px;font-size:.85rem;font-weight:600;border:1px solid #e2e8f0;background:#fff;color:#334155;cursor:pointer;transition:all .15s}
    .pd-page:hover{border-color:#cbd5e1;background:#f8fafc}
    .pd-page-active,.pd-page-active:hover{background:#2980b9;border-color:#2980b9;color:#fff}
    .pd-page-disabled{opacity:.4;pointer-events:none}
    .pd-ellipsis{min-width:1.5rem;text-align:center;color:#94a3b8;align-self:center}
    @media(prefers-reduced-motion:reduce){.pd-slide{animation:none}.pd-slide:nth-child(1){opacity:1}}
</style>

{{-- HERO — background image + fading slider on the right --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0" style="background-image:url('{{ asset('storage/rg-media/editorial-ph-2.webp') }}');background-size:cover;background-position:center" aria-hidden="true"></div>
    <div class="absolute inset-0" style="background:linear-gradient(100deg,rgba(255,255,255,.95) 0%,rgba(255,255,255,.84) 44%,rgba(255,255,255,.5) 70%,rgba(236,253,245,.32) 100%)" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div>
                <div class="inline-flex items-center gap-2 text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 bg-white/80 border border-brand-100 rounded-full px-3 py-1.5 mb-5 shadow-sm">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M4 9h16 M9 9v11"/></svg>
                    Partner Directory
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-5 max-w-xl">
                    Here's a Directory of Businesses That Might Help You On Your Adventure
                </h1>
                <p class="text-lg text-slate-700 leading-relaxed max-w-xl mb-6">
                    Hotels, resorts, tour guides, travel and tours, spas, surf and dive schools, and everything tourism, all in one place. Pick a type or type a place to find the right people for your trip.
                </p>
                <a href="{{ route('become-a-partner') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-700 hover:text-brand-800">
                    Run a tourism business? Get listed free
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-tr from-brand-200/50 to-emerald-200/50 rounded-[2rem] blur-2xl" aria-hidden="true"></div>
                <div class="relative aspect-[4/3] rounded-3xl overflow-hidden shadow-2xl ring-1 ring-black/5 bg-brand-100">
                    @foreach($slides as $i => $s)
                        <img class="pd-slide" src="{{ asset('storage/' . $s) }}" alt="Philippine travel and tourism scene {{ $i + 1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                    @endforeach
                </div>
                <div class="hidden sm:flex absolute -bottom-5 -left-5 items-center gap-3 bg-white rounded-2xl shadow-xl ring-1 ring-black/5 px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-bold text-slate-900">Verified Partners</div>
                        <div class="text-xs text-slate-500">We Highly Recommend</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- DIRECTORY --}}
<section class="py-10 md:py-14 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Search + type tabs --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5 mb-8">
            <div class="relative mb-4">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input id="pdSearch" type="text" autocomplete="off" spellcheck="false"
                       placeholder="Search by name, type, or place — try El Nido, Cebu, dive, or spa"
                       aria-label="Search partners"
                       class="w-full rounded-full border border-slate-300 bg-white pl-11 pr-4 h-12 text-[15px] text-slate-800 placeholder-slate-400 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 outline-none transition">
            </div>

            <div class="flex items-center justify-between mb-2.5">
                <span class="text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Filter by type</span>
                <button id="pdClear" type="button" style="display:none" class="items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    <svg class="w-3.5 h-3.5 inline align-[-2px] mr-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    Clear
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="pd-tab tab-active" data-type="">All types</button>
                @foreach($types as $t)
                    <button type="button" class="pd-tab" data-type="{{ $t['key'] }}">
                        <span class="w-2 h-2 rounded-full bg-{{ $t['color'] }}-500"></span>
                        {{ $t['label'] }}<span class="pd-count text-slate-400 font-medium">{{ $t['count'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Results grid: photo thumbnail + name + location --}}
        <div id="partnerGrid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($partners as $p)
                @php
                    $m = $typeMeta[$p->type] ?? $typeMeta['other'];
                    $g = $grad[$m['color']] ?? $grad['slate'];
                    $hay = \Illuminate\Support\Str::lower($p->name . ' ' . $p->city . ' ' . $p->region . ' ' . $m['label'] . ' ' . $p->tagline);
                @endphp
                <article class="partner-card group flex items-center gap-4 rounded-2xl bg-white border border-slate-200 p-3.5 hover:border-slate-300 hover:shadow-md transition"
                         data-type="{{ $p->type }}" data-region="{{ $p->region }}"
                         data-search="{{ $hay }}"
                         @if($loop->index >= 24) style="display:none" @endif>
                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 shadow-sm bg-slate-100">
                        @if($p->image_path)
                            <img src="{{ asset('storage/' . $p->image_path) }}" alt="{{ $p->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br {{ $g }} flex items-center justify-center">
                                <svg class="w-7 h-7 text-white/95" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $m['icon'] }}"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h3 class="font-bold text-slate-900 leading-tight truncate">{{ $p->name }}</h3>
                            @if($p->is_verified)
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><title>Verified partner</title><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 text-sm text-slate-500 mt-1">
                            <svg class="w-3.5 h-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="truncate">{{ $p->city }}</span>
                        </div>
                        <div class="text-xs font-semibold text-{{ $m['color'] }}-600 mt-1.5">{{ $m['label'] }}</div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Empty state --}}
        <div id="partnerEmpty" style="display:none" class="text-center py-20">
            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No partners match your search</h3>
            <p class="text-slate-500 mt-1">Try a different type, or another place name.</p>
        </div>

        {{-- Pagination --}}
        <nav id="pdPager" class="flex flex-wrap justify-center items-center gap-1.5 mt-10" style="display:none" aria-label="Directory pages"></nav>
    </div>
</section>

<script>
(function () {
    var grid = document.getElementById('partnerGrid');
    if (!grid) return;
    var cards = Array.prototype.slice.call(grid.querySelectorAll('.partner-card'));
    var search = document.getElementById('pdSearch');
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.pd-tab'));
    var emptyEl = document.getElementById('partnerEmpty');
    var clearBtn = document.getElementById('pdClear');
    var pager = document.getElementById('pdPager');
    var PER = 24, page = 1, fType = '', fQuery = '';

    function isDefault() { return !fQuery && !fType; }
    function match(c) {
        if (fType && c.dataset.type !== fType) return false;
        if (fQuery && c.dataset.search.indexOf(fQuery) < 0) return false;
        return true;
    }
    function pageItems(total, cur) {
        var out = [], i;
        if (total <= 7) { for (i = 1; i <= total; i++) out.push(i); return out; }
        if (cur <= 4) return [1, 2, 3, 4, 5, '…', total];
        if (cur >= total - 3) return [1, '…', total - 4, total - 3, total - 2, total - 1, total];
        return [1, '…', cur - 1, cur, cur + 1, '…', total];
    }
    function renderPager(totalPages) {
        pager.innerHTML = '';
        if (totalPages <= 1) { pager.style.display = 'none'; return; }
        pager.style.display = '';
        function chevron(dir) {
            return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="' + (dir === 'prev' ? 'm15 18-6-6 6-6' : 'm9 18 6-6-6-6') + '"/></svg>';
        }
        function addBtn(html, pg, opts) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'pd-page' + (opts && opts.active ? ' pd-page-active' : '') + (opts && opts.disabled ? ' pd-page-disabled' : '');
            b.innerHTML = html;
            if (!(opts && opts.disabled)) b.addEventListener('click', function () { page = pg; render(true); });
            pager.appendChild(b);
        }
        addBtn(chevron('prev'), page - 1, { disabled: page <= 1 });
        pageItems(totalPages, page).forEach(function (it) {
            if (it === '…') {
                var s = document.createElement('span'); s.className = 'pd-ellipsis'; s.textContent = '…'; pager.appendChild(s);
            } else {
                addBtn(String(it), it, { active: it === page });
            }
        });
        addBtn(chevron('next'), page + 1, { disabled: page >= totalPages });
    }
    function render(animate) {
        var matched = cards.filter(match);
        var total = matched.length;
        var totalPages = Math.max(1, Math.ceil(total / PER));
        if (page > totalPages) page = totalPages;
        if (page < 1) page = 1;
        var start = (page - 1) * PER;
        cards.forEach(function (c) { c.style.display = 'none'; });
        matched.slice(start, start + PER).forEach(function (c) { c.style.display = ''; });
        emptyEl.style.display = total === 0 ? '' : 'none';
        clearBtn.style.display = isDefault() ? 'none' : 'inline-flex';
        renderPager(totalPages);
        if (animate) { grid.style.animation = 'none'; void grid.offsetWidth; grid.style.animation = 'pdGridFade .35s ease'; }
    }

    var t = null;
    search.addEventListener('input', function () {
        if (t) clearTimeout(t);
        t = setTimeout(function () { fQuery = search.value.trim().toLowerCase(); page = 1; render(true); }, 120);
    });
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.remove('tab-active'); });
            tab.classList.add('tab-active');
            fType = tab.dataset.type || '';
            page = 1; render(true);
        });
    });
    clearBtn.addEventListener('click', function () {
        fQuery = ''; fType = ''; search.value = '';
        tabs.forEach(function (x) { x.classList.remove('tab-active'); });
        if (tabs[0]) tabs[0].classList.add('tab-active');
        page = 1; render(true);
    });

    (function initFromUrl() {
        try {
            var params = new URLSearchParams(window.location.search);
            var q = params.get('q'); if (q) { search.value = q; fQuery = q.trim().toLowerCase(); }
            var ty = params.get('type'); if (ty) { fType = ty; }
            tabs.forEach(function (x) { x.classList.toggle('tab-active', (x.dataset.type || '') === fType); });
        } catch (e) {}
    })();

    render(false);
})();
</script>
@endsection
