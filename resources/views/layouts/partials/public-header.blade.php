@php
    $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph');

    // Active-state matching. Each section spans more than its hub URL:
    // Where to Go also covers cluster + resort keyword pages, What to Do
    // covers fiestas, etc.
    $path = request()->path();
    $kwCategory = $path !== '/' && !str_contains($path, '/')
        ? \App\Models\RgKeyword::where('slug', $path)->value('category')
        : null;
    $is = fn (...$p) => collect($p)->contains(fn ($x) => request()->is($x));
    $act = [
        'dest' => $is('tourist-spots-destinations-philippines*') || $kwCategory === 'resort',
        'eat' => $is('food-trip*') || $kwCategory === 'food',
        'dishes' => $is('filipino-food-dishes-what-to-eat'),
        'do' => $is('philippine-tourist-activities-adventures-what-to-do'),
        'fiestas' => $is('philippine-fiestas-festivals-guide', 'fiestas/*'),
        'buy' => $is('philippine-souvenirs-pasalubong-what-to-buy'),
        'cultures' => $is('philippine-tribes-ethnic-groups-cultures-to-meet'),
        'blog' => $is('blog*'),
        'biz' => $is('partner-directory', 'become-a-partner'),
    ];
    $overlay = trim($__env->yieldContent('header_overlay')) !== '';

    // Menu content (cached by the service).
    $menu = app(\App\Services\HomeShowcase::class)->data();
    $foodGuides = \Illuminate\Support\Facades\Cache::remember('hdr_food_guides_v1', 600, function () {
        return \Illuminate\Support\Facades\DB::table('rg_keywords as k')->join('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')
            ->where('p.is_published', 1)->where('k.category', 'food')->orderByDesc('k.search_volume_monthly')->limit(6)
            ->get(['k.phrase', 'k.slug'])->map(fn ($r) => ['label' => ucwords($r->phrase), 'url' => url($r->slug)])->all();
    });
    $img = fn ($p) => asset('storage/rg-media/' . $p);

    $svg = fn ($paths, $cls = '') => '<svg class="' . $cls . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
    $ico = [
        'pin' => '<path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/>',
        'fork' => '<path d="M4.5 2v7"/><path d="M8 2v7"/><path d="M11.5 2v7"/><path d="M4.5 9h7"/><path d="M8 9v13"/><path d="M17 2c-1.7 0-3 2-3 4.5S15.3 11 17 11s3-2 3-4.5S18.7 2 17 2z"/><path d="M17 11v11"/>',
        'bowl' => '<path d="M4 11h16a8 8 0 0 1-16 0z"/><path d="M9 7V4M12 7V4M15 7V4"/>',
        'wave' => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/>',
        'bag' => '<path d="M5 8h14l-1.5 12.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 8z"/><path d="M9 8V5a3 3 0 0 1 6 0v3"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M5 21v-1a7 7 0 0 1 14 0v1"/>',
        'spark' => '<path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/>',
        'grid' => '<path d="M4 4h16v16H4z"/><path d="M4 9h16M9 9v11"/>',
        'star' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/>',
        'doc' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v6h6"/>',
        'search' => '<circle cx="11" cy="11" r="7.5"/><path d="m20.5 20.5-4-4"/>',
        'chev' => '<path d="m6 9 6 6 6-6"/>',
        'arrow' => '<path d="M5 12h14M13 5l7 7-7 7"/>',
    ];
    $arrow = $svg($ico['arrow']);
    $fiestaNames = collect($menu['fiestas']['items'] ?? [])->pluck('name')->take(3)->implode(', ');

    // Drawer groups (phones / tablets).
    $groups = [
        'Destinations' => [
            ['Where to Go', route('destinations.index'), 'Regions, beaches and tourist spots', 'pin', '#047857', '#e7f6ef', $act['dest']],
        ],
        'Food' => [
            ['Where to Eat', url('/food-trip'), 'Restaurant guides by area', 'fork', '#b45309', '#fdf3e2', $act['eat']],
            ['What to Eat', route('foods.index'), 'Filipino dishes worth the trip', 'bowl', '#c0392b', '#fbecea', $act['dishes']],
        ],
        'Things to Do' => [
            ['Activities and Adventures', route('activities.index'), 'Treks, dives, falls and surf', 'wave', '#4338ca', '#eeeffd', $act['do']],
            ['Fiestas and Festivals', route('fiestas.index'), 'A festival somewhere every month', 'spark', '#be185d', '#fdeef5', $act['fiestas']],
            ['Cultures to Meet', route('cultures.index'), 'The peoples of the islands', 'user', '#0f766e', '#e5f5f3', $act['cultures']],
            ['What to Buy', route('buys.index'), 'Pasalubong and local finds', 'bag', '#7c3aed', '#f3eefe', $act['buy']],
        ],
    ];
@endphp

<header id="rgHeader" class="rg-hdr {{ $overlay ? 'rg-hdr--overlay' : '' }}">
    <div class="rg-hdr__bar relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 lg:h-[76px] flex items-center gap-2">
        <a href="{{ route('home') }}" class="rg-hdr__brand shrink-0 min-w-0" aria-label="{{ $siteName }} home">
            <span class="rg-hdr__logo rg-hdr__logo--light">@include('layouts.partials.brand-logo')</span>
            @if($overlay)<span class="rg-hdr__logo rg-hdr__logo--dark">@include('layouts.partials.brand-logo', ['variant' => 'dark'])</span>@endif
        </a>

        <nav class="hidden lg:flex items-center gap-0.5 ml-6 xl:ml-10" aria-label="Main">
            {{-- Destinations --}}
            <div class="rg-mm" data-mm>
                <button type="button" class="rg-nav {{ $act['dest'] ? 'is-active' : '' }}" data-mm-trigger aria-expanded="false" aria-haspopup="true">Destinations {!! $svg($ico['chev'], 'rg-nav__chev') !!}</button>
                <div class="rg-mm__panel" data-mm-panel>
                    <div class="grid grid-cols-12 gap-8">
                        <div class="col-span-5">
                            <p class="rg-mm__label">Explore by region</p>
                            <ul class="grid grid-cols-2 gap-x-6 gap-y-0.5 mt-3">
                                @foreach($menu['regions'] as $r)
                                    <li><a href="{{ $r['url'] }}" class="rg-mm__link"><span>{{ $r['name'] }}</span><span class="rg-mm__count">{{ number_format($r['count']) }}</span></a></li>
                                @endforeach
                            </ul>
                            <a href="{{ route('destinations.index') }}" class="rg-mm__more">All destinations and tourist spots {!! $arrow !!}</a>
                        </div>
                        <div class="col-span-7">
                            <p class="rg-mm__label">Trending now</p>
                            <div class="grid grid-cols-2 gap-3 mt-3">
                                @foreach(array_slice($menu['trending'], 0, 4) as $t)
                                    <a href="{{ $t['url'] }}" class="rg-mm__card">
                                        <img src="{{ $t['image'] }}" alt="" loading="lazy">
                                        <span><strong>{{ $t['name'] }}</strong><small>{{ $t['area'] }} · {{ $t['guides'] }} guide{{ $t['guides'] === 1 ? '' : 's' }}</small></span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Food --}}
            <div class="rg-mm" data-mm>
                <button type="button" class="rg-nav {{ $act['eat'] || $act['dishes'] ? 'is-active' : '' }}" data-mm-trigger aria-expanded="false" aria-haspopup="true">Food {!! $svg($ico['chev'], 'rg-nav__chev') !!}</button>
                <div class="rg-mm__panel" data-mm-panel>
                    <div class="grid grid-cols-12 gap-6">
                        <a href="{{ url('/food-trip') }}" class="rg-mm__feature col-span-4">
                            <img src="{{ $img('restaurants/zubuchon.jpg') }}" alt="" loading="lazy">
                            <span><strong>Where to Eat</strong><small>Restaurant guides by mall, district and city</small></span>
                        </a>
                        <a href="{{ route('foods.index') }}" class="rg-mm__feature col-span-4">
                            <img src="{{ $img('foods/adobo-1.jpg') }}" alt="" loading="lazy">
                            <span><strong>What to Eat</strong><small>Filipino dishes worth planning a trip around</small></span>
                        </a>
                        <div class="col-span-4">
                            <p class="rg-mm__label">Popular food guides</p>
                            <ul class="mt-3 space-y-0.5">
                                @foreach($foodGuides as $g)
                                    <li><a href="{{ $g['url'] }}" class="rg-mm__link"><span>{{ $g['label'] }}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Things to do --}}
            <div class="rg-mm" data-mm>
                <button type="button" class="rg-nav {{ $act['do'] || $act['fiestas'] || $act['buy'] || $act['cultures'] ? 'is-active' : '' }}" data-mm-trigger aria-expanded="false" aria-haspopup="true">Things to Do {!! $svg($ico['chev'], 'rg-nav__chev') !!}</button>
                <div class="rg-mm__panel" data-mm-panel>
                    <div class="grid grid-cols-4 gap-4">
                        @foreach([
                            ['Activities and Adventures', route('activities.index'), 'Treks, dives, falls, surf and island hopping', 'adventures/mt-pinatubo-crater-trek.jpg'],
                            ['Fiestas and Festivals', route('fiestas.index'), $fiestaNames ? 'This month: ' . $fiestaNames : 'A festival somewhere every month', 'fiestas/masskara.jpg'],
                            ['Cultures to Meet', route('cultures.index'), 'The peoples and traditions of the islands', 'cultures/ifugao-1.jpg'],
                            ['What to Buy', route('buys.index'), 'Pasalubong and where to find it', 'buys/guimaras-mangoes-2.jpg'],
                        ] as [$label, $href, $text, $photo])
                            <a href="{{ $href }}" class="rg-mm__feature rg-mm__feature--tall">
                                <img src="{{ $img($photo) }}" alt="" loading="lazy">
                                <span><strong>{{ $label }}</strong><small>{{ $text }}</small></span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a href="{{ route('blog.index') }}" class="rg-nav {{ $act['blog'] ? 'is-active' : '' }}">Blog</a>

            {{-- For business --}}
            <div class="rg-mm rg-mm--small" data-mm>
                <button type="button" class="rg-nav {{ $act['biz'] ? 'is-active' : '' }}" data-mm-trigger aria-expanded="false" aria-haspopup="true">For Business {!! $svg($ico['chev'], 'rg-nav__chev') !!}</button>
                <div class="rg-mm__panel" data-mm-panel>
                    @foreach([
                        ['Become a Partner', route('become-a-partner'), 'List your resort, restaurant or tour for free', 'star'],
                        ['Partner Directory', route('partner-directory'), 'Browse local businesses we work with', 'grid'],
                        ['Owner sign in', route('login'), 'Manage your listings and Gold Points', 'user'],
                    ] as [$label, $href, $text, $i])
                        <a href="{{ $href }}" class="rg-mm__row">
                            <span class="rg-mm__rowico">{!! $svg($ico[$i]) !!}</span>
                            <span><strong>{{ $label }}</strong><small>{{ $text }}</small></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="ml-auto flex items-center gap-1 sm:gap-2 shrink-0">
            <button type="button" class="rg-hdr__search" data-rg-search-open aria-label="Search">
                {!! $svg($ico['search']) !!}<span class="hidden xl:inline">Search</span><kbd class="hidden xl:inline-flex">/</kbd>
            </button>
            @auth
                <a href="{{ route('dashboard.index') }}" class="rg-nav rg-hdr__signin hidden lg:inline-flex">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rg-nav rg-hdr__signin hidden lg:inline-flex">Sign in</a>
            @endauth
            <a href="{{ route('create-your-adventure') }}" class="rg-btn rg-btn--coral hidden sm:inline-flex">Plan your trip{!! $arrow !!}</a>
            <button type="button" class="rg-iconbtn rg-hdr__menu lg:hidden" data-rg-drawer-open="menu" aria-label="Open menu" aria-controls="rgDrawer" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
            </button>
        </div>
    </div>
</header>

{{-- Search overlay (all screen sizes) --}}
<div id="rgSearch" class="rg-sov" role="dialog" aria-modal="true" aria-label="Search" aria-hidden="true">
    <div class="rg-sov__backdrop" data-rg-search-close></div>
    <div class="rg-sov__panel">
        <div class="relative rg-ta--inline" data-rg-typeahead>
            <label class="rg-sov__field">
                {!! $svg($ico['search'], 'w-5 h-5 text-slate-400 shrink-0') !!}
                <input data-ta-input type="search" autocomplete="off" spellcheck="false" placeholder="Search destinations, food, festivals" aria-label="Search the site" role="combobox" aria-expanded="false" aria-autocomplete="list">
                <button data-ta-clear type="button" class="hidden" aria-label="Clear"></button>
                <button type="button" class="rg-sov__esc" data-rg-search-close>Esc</button>
            </label>
            <div data-ta-panel class="rg-ta-panel hidden" role="listbox" aria-label="Search suggestions"></div>
        </div>
        <div class="rg-sov__suggest">
            <p class="rg-mm__label">Popular right now</p>
            <div class="flex flex-wrap gap-2 mt-3">
                @foreach($menu['trending'] as $t)
                    <a href="{{ $t['url'] }}" class="rg-sov__chip">{!! $svg($ico['pin'], 'w-3.5 h-3.5') !!}{{ $t['name'] }}</a>
                @endforeach
            </div>
            <p class="rg-mm__label mt-6">Browse</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-3">
                @foreach(array_merge(...array_values($groups)) as [$label, $href, $text, $i, $c, $bg])
                    <a href="{{ $href }}" class="rg-sov__cat" style="--c:{{ $c }};--bg:{{ $bg }}"><span class="rg-sov__catico">{!! $svg($ico[$i]) !!}</span>{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Mobile / tablet drawer --}}
<div id="rgDrawer" class="rg-drawer lg:hidden" aria-hidden="true">
    <div class="rg-drawer__backdrop" data-rg-drawer-close></div>
    <div class="rg-drawer__panel" role="dialog" aria-modal="true" aria-label="Menu">
        <div class="flex items-center justify-between px-5 h-16 border-b border-slate-100 shrink-0">
            @include('layouts.partials.brand-logo', ['size' => 'sm'])
            <button type="button" class="rg-iconbtn -mr-2" data-rg-drawer-close aria-label="Close menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="rg-drawer__body">
            <button type="button" class="rg-drawer__search" data-rg-search-open>{!! $svg($ico['search'], 'w-4 h-4') !!}Search places, food, festivals</button>
            @foreach($groups as $heading => $items)
                <p class="mt-6 mb-1.5 px-1 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ $heading }}</p>
                <nav class="space-y-0.5" aria-label="{{ $heading }}">
                    @foreach($items as [$label, $href, $text, $i, $c, $bg, $active])
                        <a href="{{ $href }}" class="rg-dlink {{ $active ? 'is-active' : '' }}" style="--c:{{ $c }};--bg:{{ $bg }}">
                            <span class="rg-dlink__ico">{!! $svg($ico[$i]) !!}</span>
                            <span class="min-w-0"><span class="block font-semibold text-slate-900">{{ $label }}</span><span class="block text-xs text-slate-500 truncate">{{ $text }}</span></span>
                        </a>
                    @endforeach
                </nav>
            @endforeach
            <div class="mt-6 grid grid-cols-2 gap-2 text-sm font-semibold text-slate-700">
                <a href="{{ route('blog.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">Blog</a>
                <a href="{{ route('partner-directory') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">Partner Directory</a>
                <a href="{{ route('about') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">About</a>
                <a href="{{ route('contact') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">Contact</a>
            </div>
        </div>
        <div class="border-t border-slate-100 p-4 space-y-2.5 shrink-0" style="padding-bottom:max(1rem, env(safe-area-inset-bottom))">
            <a href="{{ route('create-your-adventure') }}" class="rg-btn rg-btn--coral rg-btn--lg w-full">Plan your trip for free{!! $arrow !!}</a>
            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--outline w-full">{!! $svg($ico['star']) !!}Partner</a>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="rg-btn rg-btn--ocean w-full">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rg-btn rg-btn--ocean w-full">Sign in</a>
                @endauth
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var header = document.getElementById('rgHeader');
    var root = document.documentElement;

    // --- Height for sticky sub-navs / anchors, solid background once scrolled.
    function measure() { if (header) root.style.setProperty('--rg-header-h', header.offsetHeight + 'px'); }
    function onScroll() { if (header) header.classList.toggle('is-scrolled', window.scrollY > 8); }
    measure(); onScroll();
    window.addEventListener('resize', measure, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(measure);

    // --- Mega menus: hover with intent on pointer devices, click/keyboard everywhere.
    var menus = [].slice.call(document.querySelectorAll('[data-mm]'));
    var hoverable = window.matchMedia && matchMedia('(hover: hover)').matches;
    function setOpen(m, open) {
        m.classList.toggle('is-open', open);
        var t = m.querySelector('[data-mm-trigger]'); if (t) t.setAttribute('aria-expanded', open ? 'true' : 'false');
        header.classList.toggle('is-open', menus.some(function (x) { return x.classList.contains('is-open'); }));
    }
    function closeAll(except) { menus.forEach(function (m) { if (m !== except) setOpen(m, false); }); }
    menus.forEach(function (m) {
        var t = m.querySelector('[data-mm-trigger]'), timer;
        t.addEventListener('click', function (e) { e.preventDefault(); var open = !m.classList.contains('is-open'); closeAll(m); setOpen(m, open); });
        t.addEventListener('keydown', function (e) { if (e.key === 'ArrowDown') { e.preventDefault(); closeAll(m); setOpen(m, true); var f = m.querySelector('[data-mm-panel] a'); if (f) f.focus(); } });
        if (hoverable) {
            m.addEventListener('mouseenter', function () { clearTimeout(timer); timer = setTimeout(function () { closeAll(m); setOpen(m, true); }, 90); });
            m.addEventListener('mouseleave', function () { clearTimeout(timer); timer = setTimeout(function () { setOpen(m, false); }, 160); });
        }
        m.addEventListener('focusout', function (e) { if (!m.contains(e.relatedTarget)) setOpen(m, false); });
    });
    document.addEventListener('click', function (e) { if (!e.target.closest('[data-mm]')) closeAll(); });

    // --- Search overlay
    var sov = document.getElementById('rgSearch'), lastFocus = null;
    function openSearch() {
        if (!sov) return; closeAll(); closeDrawer();
        lastFocus = document.activeElement;
        sov.classList.add('is-open'); sov.setAttribute('aria-hidden', 'false'); root.classList.add('rg-lock');
        setTimeout(function () { var i = sov.querySelector('[data-ta-input]'); if (i) i.focus(); }, 60);
    }
    function closeSearch() {
        if (!sov || !sov.classList.contains('is-open')) return;
        sov.classList.remove('is-open'); sov.setAttribute('aria-hidden', 'true'); root.classList.remove('rg-lock');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }
    document.querySelectorAll('[data-rg-search-open]').forEach(function (b) { b.addEventListener('click', openSearch); });
    document.querySelectorAll('[data-rg-search-close]').forEach(function (b) { b.addEventListener('click', closeSearch); });
    document.addEventListener('keydown', function (e) {
        var typing = /input|textarea|select/i.test((e.target && e.target.tagName) || '') || (e.target && e.target.isContentEditable);
        if (e.key === '/' && !typing) { e.preventDefault(); openSearch(); }
        else if (e.key === 'Escape') { closeSearch(); closeDrawer(); closeAll(); }
    });

    // --- Drawer
    var drawer = document.getElementById('rgDrawer'), drawerFocus = null;
    function openDrawer() {
        if (!drawer) return;
        drawerFocus = document.activeElement;
        drawer.classList.add('is-open'); drawer.setAttribute('aria-hidden', 'false'); root.classList.add('rg-lock');
        document.querySelectorAll('[data-rg-drawer-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
        setTimeout(function () { var c = drawer.querySelector('[data-rg-drawer-close].rg-iconbtn'); if (c) c.focus(); }, 50);
    }
    function closeDrawer() {
        if (!drawer || !drawer.classList.contains('is-open')) return;
        drawer.classList.remove('is-open'); drawer.setAttribute('aria-hidden', 'true');
        if (!sov || !sov.classList.contains('is-open')) root.classList.remove('rg-lock');
        document.querySelectorAll('[data-rg-drawer-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
        if (drawerFocus && drawerFocus.focus) drawerFocus.focus();
    }
    document.querySelectorAll('[data-rg-drawer-open]').forEach(function (b) { b.addEventListener('click', openDrawer); });
    document.querySelectorAll('[data-rg-drawer-close]').forEach(function (b) { b.addEventListener('click', closeDrawer); });
    window.addEventListener('resize', function () { if (window.innerWidth >= 1024) closeDrawer(); }, { passive: true });

    // --- Typeahead (search overlay, homepage hero, any [data-rg-typeahead]).
    var endpoint = @json(route('search.suggest'));
    var TYPE = {destination:{k:'pin',c:'#13669c',bg:'#d6eaf5'},resort:{k:'bed',c:'#047857',bg:'#d1fae5'},restaurant:{k:'cup',c:'#c0392b',bg:'#fbecea'},spot:{k:'cam',c:'#7c3aed',bg:'#ede9fe'},region:{k:'map',c:'#b45309',bg:'#fef3c7'},blog:{k:'doc',c:'#475569',bg:'#e2e8f0'}};
    var ICON = {pin:'<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',bed:'<path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/>',cup:'<path d="M10 2v2"/><path d="M14 2v2"/><path d="M6 2v2"/><path d="M16 8a1 1 0 0 1 1 1v8a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V9a1 1 0 0 1 1-1h14a4 4 0 1 1 0 8h-1"/>',cam:'<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>',map:'<path d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z"/><path d="M9 3v15"/><path d="M15 6v15"/>',doc:'<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v6h6"/>'};
    var GROUP_ORDER = ['region','destination','resort','restaurant','spot','blog'];
    var GROUP_LABEL = {region:'Regions',destination:'Destinations',resort:'Stays',restaurant:'Food finds',spot:'Tourist spots',blog:'Blog'};
    function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];});}
    function svgIcon(k){return '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'+(ICON[k]||ICON.pin)+'</svg>';}
    function initTypeaheads() { document.querySelectorAll('[data-rg-typeahead]:not([data-ta-ready])').forEach(function (wrap) {
        wrap.setAttribute('data-ta-ready', '1');
        var input = wrap.querySelector('[data-ta-input]'), panel = wrap.querySelector('[data-ta-panel]'), clear = wrap.querySelector('[data-ta-clear]');
        if (!input || !panel) return;
        var timer = null, active = -1;
        function open(){panel.classList.remove('hidden');input.setAttribute('aria-expanded','true');}
        function close(){panel.classList.add('hidden');input.setAttribute('aria-expanded','false');active=-1;}
        function row(it){
            var tp=TYPE[it.type]||{k:'pin',c:'#475569',bg:'#e2e8f0'};
            var thumb=it.image?'<span class="rg-ns-ico" style="background:#f1f5f9"><img src="'+esc(it.image)+'" alt="" style="width:100%;height:100%;object-fit:cover;display:block"></span>':'<span class="rg-ns-ico" style="background:'+tp.bg+';color:'+tp.c+'">'+svgIcon(tp.k)+'</span>';
            return '<a href="'+esc(it.url)+'" class="rg-ns-item flex items-center gap-3 px-3 py-2 no-underline" role="option">'+thumb+'<span class="min-w-0 flex-1"><span class="block text-sm font-semibold text-slate-900 truncate">'+esc(it.label)+'</span>'+(it.sub?'<span class="block text-xs text-slate-500 truncate">'+esc(it.sub)+'</span>':'')+'</span></a>';
        }
        function render(res,q){
            res=res||[];
            if(!res.length){panel.innerHTML='<div class="px-4 py-4 text-sm text-slate-500">No matches for “'+esc(q)+'”</div>';open();return;}
            var groups={};res.forEach(function(it){var t=it.type||'other';(groups[t]=groups[t]||[]).push(it);});
            var order=GROUP_ORDER.slice();Object.keys(groups).forEach(function(t){if(order.indexOf(t)<0)order.push(t);});
            var h='';order.forEach(function(t){var arr=groups[t];if(!arr||!arr.length)return;h+='<div class="rg-ns-group">'+esc(GROUP_LABEL[t]||t)+'</div>';arr.forEach(function(it){h+=row(it);});});
            panel.innerHTML=h;active=-1;open();
        }
        function fetchSuggest(q, go){
            fetch(endpoint+'?q='+encodeURIComponent(q),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){return r.json();})
                .then(function(d){ if(input.value.trim()!==q) return; if (go && d.results && d.results[0]) { window.location.href = d.results[0].url; return; } render(d.results,q); })
                .catch(function(){});
        }
        function hl(rows){rows.forEach(function(r,i){r.classList.toggle('is-active',i===active);if(i===active)r.scrollIntoView({block:'nearest'});});}
        input.addEventListener('input',function(){
            var q=input.value.trim(); if(clear)clear.classList.toggle('hidden',q==='');
            if(timer)clearTimeout(timer); if(q.length<2){close();return;}
            timer=setTimeout(function(){fetchSuggest(q);},160);
        });
        input.addEventListener('keydown',function(e){
            var rows=panel.querySelectorAll('.rg-ns-item');
            if(e.key==='ArrowDown'){e.preventDefault();if(!rows.length)return;active=(active+1)%rows.length;hl(rows);}
            else if(e.key==='ArrowUp'){e.preventDefault();if(!rows.length)return;active=(active-1+rows.length)%rows.length;hl(rows);}
            else if(e.key==='Enter'){e.preventDefault();var sel=active>=0?rows[active]:rows[0];if(sel){window.location.href=sel.getAttribute('href');}else if(input.value.trim().length>=2){fetchSuggest(input.value.trim(), true);}}
            else if(e.key==='Escape'&&!panel.classList.contains('hidden')){e.stopPropagation();close();}
        });
        input.addEventListener('focus',function(){if(panel.innerHTML&&input.value.trim().length>=2)open();});
        if(clear)clear.addEventListener('click',function(e){e.preventDefault();input.value='';clear.classList.add('hidden');close();input.focus();});
        document.addEventListener('click',function(e){if(!wrap.contains(e.target))close();});
        var submit = wrap.querySelector('[data-ta-submit]');
        if (submit) submit.addEventListener('click', function () {
            var first = panel.querySelector('.rg-ns-item');
            if (first && !panel.classList.contains('hidden')) { window.location.href = first.getAttribute('href'); return; }
            var q = input.value.trim(); if (q.length >= 2) fetchSuggest(q, true); else input.focus();
        });
    }); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initTypeaheads); else initTypeaheads();
})();
</script>
