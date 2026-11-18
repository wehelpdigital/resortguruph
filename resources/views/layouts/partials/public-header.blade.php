@php
    $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph');

    // Active-state matching for the section nav. Each section spans more
    // than its hub URL: Where to Go also covers cluster + resort keyword
    // pages, What to Do covers fiestas, etc.
    $path = request()->path();
    $kwCategory = $path !== '/' && !str_contains($path, '/')
        ? \App\Models\RgKeyword::where('slug', $path)->value('category')
        : null;

    $sections = [
        [
            'label' => 'Where to Go', 'href' => route('destinations.index'), 'c' => '#047857', 'bg' => '#e7f6ef',
            'desc' => 'Destinations, beaches and tourist spots',
            'active' => request()->is('tourist-spots-destinations-philippines*') || $kwCategory === 'resort',
            'icon' => '<path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/>',
        ],
        [
            'label' => 'Where to Eat', 'href' => url('/food-trip'), 'c' => '#b45309', 'bg' => '#fdf3e2',
            'desc' => 'Restaurants and food trips by area',
            'active' => request()->is('food-trip*') || $kwCategory === 'food',
            'icon' => '<path d="M4.5 2v7"/><path d="M8 2v7"/><path d="M11.5 2v7"/><path d="M4.5 9h7"/><path d="M8 9v13"/><path d="M17 2c-1.7 0-3 2-3 4.5S15.3 11 17 11s3-2 3-4.5S18.7 2 17 2z"/><path d="M17 11v11"/>',
        ],
        [
            'label' => 'What to Eat', 'href' => route('foods.index'), 'c' => '#c0392b', 'bg' => '#fbecea',
            'desc' => 'Filipino dishes worth the trip',
            'active' => request()->is('filipino-food-dishes-what-to-eat'),
            'icon' => '<path d="M4 11h16a8 8 0 0 1-16 0z"/><path d="M9 7V4M12 7V4M15 7V4"/>',
        ],
        [
            'label' => 'What to Do', 'href' => route('activities.index'), 'c' => '#4338ca', 'bg' => '#eeeffd',
            'desc' => 'Activities, adventures and fiestas',
            'active' => request()->is('philippine-tourist-activities-adventures-what-to-do') || request()->is('philippine-fiestas-festivals-guide') || request()->is('fiestas/*'),
            'icon' => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1"/>',
        ],
        [
            'label' => 'What to Buy', 'href' => route('buys.index'), 'c' => '#7c3aed', 'bg' => '#f3eefe',
            'desc' => 'Pasalubong and local finds',
            'active' => request()->is('philippine-souvenirs-pasalubong-what-to-buy'),
            'icon' => '<path d="M5 8h14l-1.5 12.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 8z"/><path d="M9 8V5a3 3 0 0 1 6 0v3"/>',
        ],
        [
            'label' => 'Cultures to Meet', 'href' => route('cultures.index'), 'c' => '#0f766e', 'bg' => '#e5f5f3',
            'desc' => 'The peoples and traditions of the islands',
            'active' => request()->is('philippine-tribes-ethnic-groups-cultures-to-meet'),
            'icon' => '<circle cx="12" cy="8" r="4"/><path d="M5 21v-1a7 7 0 0 1 14 0v1"/>',
        ],
        [
            'label' => 'Partner Directory', 'href' => route('partner-directory'), 'c' => '#13669c', 'bg' => '#eef6fb',
            'desc' => 'Local businesses we work with',
            'active' => request()->is('partner-directory'),
            'icon' => '<path d="M4 4h16v16H4z"/><path d="M4 9h16M9 9v11"/>',
        ],
    ];
    $svg = fn ($paths, $cls = '') => '<svg class="' . $cls . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $star = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>';
@endphp

<header id="rgHeader" class="rg-header sticky top-0 z-50">
    {{-- Tier 1: brand, search, account + calls to action --}}
    <div class="rg-header__bar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 lg:h-[72px] flex items-center gap-3 lg:gap-5">
            <a href="{{ route('home') }}" class="flex items-center shrink-0 min-w-0" aria-label="{{ $siteName }} home">
                @include('layouts.partials.brand-logo')
            </a>

            {{-- Desktop typeahead --}}
            <div class="hidden lg:block relative flex-1 max-w-md rg-ta--dropdown" data-rg-typeahead>
                <label class="rg-hsearch">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input data-ta-input type="text" autocomplete="off" spellcheck="false" placeholder="Search places, food, festivals" aria-label="Search the site" role="combobox" aria-expanded="false" aria-autocomplete="list" class="ml-2.5 w-full bg-transparent border-0 appearance-none outline-none focus:ring-0 p-0 text-sm text-slate-800 placeholder-slate-500">
                    <button data-ta-clear type="button" aria-label="Clear search" class="hidden text-slate-400 hover:text-slate-700 shrink-0"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                </label>
                <div data-ta-panel class="rg-ta-panel hidden" role="listbox" aria-label="Search suggestions"></div>
            </div>

            <nav class="hidden lg:flex items-center gap-1 ml-auto shrink-0" aria-label="Account">
                <a href="{{ route('blog.index') }}" class="rg-toplink hidden xl:inline-flex {{ request()->is('blog*') ? 'is-active' : '' }}">Blog</a>
                @auth
                    <a href="{{ route('dashboard.index') }}" class="rg-toplink">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rg-toplink">Sign in</a>
                @endauth
                <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--outline hidden xl:inline-flex ml-1">{!! $star !!}Become a Partner</a>
                <a href="{{ route('create-your-adventure') }}" class="rg-btn rg-btn--coral ml-1">Create Your Adventure{!! $arrow !!}</a>
            </nav>

            <div class="flex lg:hidden items-center gap-0.5 ml-auto shrink-0">
                <button type="button" class="rg-iconbtn" data-rg-drawer-open="search" aria-label="Search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>
                <button type="button" class="rg-iconbtn" data-rg-drawer-open="menu" aria-label="Open menu" aria-controls="rgDrawer" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Tier 2: section nav. Scrolls sideways on small screens. --}}
    <div class="bg-white/60 border-b border-slate-200/70">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <nav class="rg-sections no-scrollbar flex items-center gap-1 overflow-x-auto h-12 px-2 lg:px-0 lg:justify-between" aria-label="Explore the Philippines">
                @foreach($sections as $s)
                    <a href="{{ $s['href'] }}" class="rg-sec {{ $s['active'] ? 'is-active' : '' }}" style="--c:{{ $s['c'] }};--bg:{{ $s['bg'] }}" @if($s['active']) aria-current="page" @endif>
                        <span class="rg-sec__ico">{!! $svg($s['icon']) !!}</span>{{ $s['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</header>

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
            <div class="relative rg-ta--inline" data-rg-typeahead>
                <label class="rg-hsearch">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input data-ta-input type="search" autocomplete="off" spellcheck="false" placeholder="Search places, food, festivals" aria-label="Search the site" role="combobox" aria-expanded="false" aria-autocomplete="list" class="ml-2.5 w-full bg-transparent border-0 appearance-none outline-none focus:ring-0 p-0 text-base text-slate-800 placeholder-slate-500">
                    <button data-ta-clear type="button" aria-label="Clear search" class="hidden text-slate-400 hover:text-slate-700 shrink-0"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
                </label>
                <div data-ta-panel class="rg-ta-panel hidden" role="listbox" aria-label="Search suggestions"></div>
            </div>

            <p class="mt-6 mb-2 px-1 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Explore</p>
            <nav class="space-y-0.5" aria-label="Explore the Philippines">
                @foreach($sections as $s)
                    <a href="{{ $s['href'] }}" class="rg-dlink {{ $s['active'] ? 'is-active' : '' }}" style="--c:{{ $s['c'] }};--bg:{{ $s['bg'] }}">
                        <span class="rg-dlink__ico">{!! $svg($s['icon']) !!}</span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-slate-900">{{ $s['label'] }}</span>
                            <span class="block text-xs text-slate-500 truncate">{{ $s['desc'] }}</span>
                        </span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-6 grid grid-cols-2 gap-2 text-sm font-semibold text-slate-700">
                <a href="{{ route('home') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">Home</a>
                <a href="{{ route('blog.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">Blog</a>
                <a href="{{ route('about') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">About</a>
                <a href="{{ route('contact') }}" class="px-3 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100">Contact</a>
            </div>
        </div>
        <div class="border-t border-slate-100 p-4 space-y-2.5 shrink-0" style="padding-bottom:max(1rem, env(safe-area-inset-bottom))">
            <a href="{{ route('create-your-adventure') }}" class="rg-btn rg-btn--coral rg-btn--lg w-full">Create Your Adventure for Free{!! $arrow !!}</a>
            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--outline w-full">{!! $star !!}Partner</a>
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
    // --- Header: shadow once scrolled + publish its height for sticky
    //     sub-navs and anchor offsets (--rg-header-h).
    var header = document.getElementById('rgHeader');
    var root = document.documentElement;
    function measure() { if (header) root.style.setProperty('--rg-header-h', header.offsetHeight + 'px'); }
    function onScroll() { if (header) header.classList.toggle('is-scrolled', window.scrollY > 4); }
    measure(); onScroll();
    window.addEventListener('resize', measure, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(measure);

    // Keep the active section pill visible on narrow screens.
    var activeSec = document.querySelector('.rg-sections .rg-sec.is-active');
    if (activeSec && activeSec.parentElement.scrollWidth > activeSec.parentElement.clientWidth) {
        activeSec.parentElement.scrollLeft = activeSec.offsetLeft - 16;
    }

    // --- Drawer
    var drawer = document.getElementById('rgDrawer');
    var lastFocus = null;
    function openDrawer(mode) {
        if (!drawer) return;
        lastFocus = document.activeElement;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        root.classList.add('rg-lock');
        document.querySelectorAll('[data-rg-drawer-open="menu"]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
        setTimeout(function () {
            var target = mode === 'search' ? drawer.querySelector('[data-ta-input]') : drawer.querySelector('[data-rg-drawer-close].rg-iconbtn');
            if (target) target.focus();
        }, mode === 'search' ? 320 : 50);
    }
    function closeDrawer() {
        if (!drawer || !drawer.classList.contains('is-open')) return;
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        root.classList.remove('rg-lock');
        document.querySelectorAll('[data-rg-drawer-open="menu"]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }
    document.querySelectorAll('[data-rg-drawer-open]').forEach(function (b) {
        b.addEventListener('click', function () { openDrawer(b.getAttribute('data-rg-drawer-open')); });
    });
    document.querySelectorAll('[data-rg-drawer-close]').forEach(function (b) { b.addEventListener('click', closeDrawer); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });
    window.addEventListener('resize', function () { if (window.innerWidth >= 1024) closeDrawer(); }, { passive: true });

    // --- Typeahead (header + drawer). Server-suggested via /search-suggest.
    var endpoint = @json(route('search.suggest'));
    var TYPE = {destination:{k:'pin',c:'#13669c',bg:'#d6eaf5'},resort:{k:'bed',c:'#047857',bg:'#d1fae5'},restaurant:{k:'cup',c:'#c0392b',bg:'#fbecea'},spot:{k:'cam',c:'#7c3aed',bg:'#ede9fe'},region:{k:'map',c:'#b45309',bg:'#fef3c7'},blog:{k:'doc',c:'#475569',bg:'#e2e8f0'}};
    var ICON = {pin:'<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',bed:'<path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/>',cup:'<path d="M10 2v2"/><path d="M14 2v2"/><path d="M6 2v2"/><path d="M16 8a1 1 0 0 1 1 1v8a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V9a1 1 0 0 1 1-1h14a4 4 0 1 1 0 8h-1"/>',cam:'<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>',map:'<path d="M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z"/><path d="M9 3v15"/><path d="M15 6v15"/>',doc:'<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v6h6"/>'};
    var GROUP_ORDER = ['region','destination','resort','restaurant','spot','blog'];
    var GROUP_LABEL = {region:'Regions',destination:'Destinations',resort:'Stays',restaurant:'Food finds',spot:'Tourist spots',blog:'Blog'};
    function esc(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];});}
    function svgIcon(k){return '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'+(ICON[k]||ICON.pin)+'</svg>';}

    document.querySelectorAll('[data-rg-typeahead]').forEach(function (wrap) {
        var input = wrap.querySelector('[data-ta-input]');
        var panel = wrap.querySelector('[data-ta-panel]');
        var clear = wrap.querySelector('[data-ta-clear]');
        if (!input || !panel) return;
        var timer = null, active = -1;
        function open(){panel.classList.remove('hidden');input.setAttribute('aria-expanded','true');}
        function close(){panel.classList.add('hidden');input.setAttribute('aria-expanded','false');active=-1;}
        function row(it){
            var tp=TYPE[it.type]||{k:'pin',c:'#475569',bg:'#e2e8f0'};
            var thumb=it.image?'<span class="rg-ns-ico" style="background:#f1f5f9"><img src="'+esc(it.image)+'" alt="" style="width:100%;height:100%;object-fit:cover;display:block"></span>':'<span class="rg-ns-ico" style="background:'+tp.bg+';color:'+tp.c+'">'+svgIcon(tp.k)+'</span>';
            return '<a href="'+esc(it.url)+'" class="rg-ns-item flex items-center gap-3 px-3 py-2 no-underline" role="option">'+thumb+
                '<span class="min-w-0 flex-1"><span class="block text-sm font-semibold text-slate-900 truncate">'+esc(it.label)+'</span>'+
                (it.sub?'<span class="block text-xs text-slate-500 truncate">'+esc(it.sub)+'</span>':'')+'</span></a>';
        }
        function render(res,q){
            res=res||[];
            if(!res.length){panel.innerHTML='<div class="px-4 py-4 text-sm text-slate-500">No matches for “'+esc(q)+'”</div>';open();return;}
            var groups={};res.forEach(function(it){var t=it.type||'other';(groups[t]=groups[t]||[]).push(it);});
            var order=GROUP_ORDER.slice();Object.keys(groups).forEach(function(t){if(order.indexOf(t)<0)order.push(t);});
            var h='';order.forEach(function(t){var arr=groups[t];if(!arr||!arr.length)return;h+='<div class="rg-ns-group">'+esc(GROUP_LABEL[t]||t)+'</div>';arr.forEach(function(it){h+=row(it);});});
            panel.innerHTML=h;active=-1;open();
        }
        function fetchSuggest(q){
            fetch(endpoint+'?q='+encodeURIComponent(q),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
                .then(function(r){return r.json();})
                .then(function(d){if(input.value.trim()===q)render(d.results,q);})
                .catch(function(){});
        }
        function hl(rows){rows.forEach(function(r,i){r.classList.toggle('is-active',i===active);if(i===active)r.scrollIntoView({block:'nearest'});});}
        input.addEventListener('input',function(){
            var q=input.value.trim();
            if(clear)clear.classList.toggle('hidden',q==='');
            if(timer)clearTimeout(timer);
            if(q.length<2){close();return;}
            timer=setTimeout(function(){fetchSuggest(q);},180);
        });
        input.addEventListener('keydown',function(e){
            var rows=panel.querySelectorAll('.rg-ns-item');
            if(e.key==='ArrowDown'){e.preventDefault();if(!rows.length)return;active=(active+1)%rows.length;hl(rows);}
            else if(e.key==='ArrowUp'){e.preventDefault();if(!rows.length)return;active=(active-1+rows.length)%rows.length;hl(rows);}
            else if(e.key==='Enter'){var sel=active>=0?rows[active]:rows[0];if(sel){e.preventDefault();window.location.href=sel.getAttribute('href');}}
            else if(e.key==='Escape'){if(!panel.classList.contains('hidden')){e.stopPropagation();close();}}
        });
        input.addEventListener('focus',function(){if(panel.innerHTML&&input.value.trim().length>=2)open();});
        if(clear)clear.addEventListener('click',function(e){e.preventDefault();input.value='';clear.classList.add('hidden');close();input.focus();});
        document.addEventListener('click',function(e){if(!wrap.contains(e.target))close();});
    });
})();
</script>
