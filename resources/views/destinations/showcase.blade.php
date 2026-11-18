@extends('layouts.public')

{{-- Hand-designed destinations index. Data: App\Services\DestinationShowcase::index().
     rg_settings.destinations_layout = blocks switches back to the block-builder page. --}}

@section('title', 'Where to Go in the Philippines: Regions, Islands and Tourist Spots | Tourist Guide Ph')
@section('meta_description', 'Plan where to go in the Philippines. Compare ' . $data['stats']['regions'] . ' regions and ' . $data['stats']['guides'] . ' destination guides, from weekend trips near Manila to Palawan, the Visayas and Mindanao.')
@section('canonical', route('destinations.index'))
@section('header_overlay', '1')

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => route('destinations.index')],
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Where to go in the Philippines',
    'url' => route('destinations.index'),
    'mainEntity' => [
        '@type' => 'ItemList',
        'itemListElement' => collect($data['regions'])->values()->map(fn ($r, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $r['name'], 'url' => $r['url']])->all(),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@php
    $s = $data['stats'];
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $star = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>';
    $nowMonth = (int) now()->format('n');
    $heroSlides = collect($data['regions'])->whereIn('slug', ['palawan', 'visayas', 'north-luzon', 'bicol'])->values();
    $styleIcons = [
        'beach' => '<path d="M17.6 17.6A8 8 0 1 0 6.4 6.4"/><path d="M2 22c3-2 6-2 9 0s6 2 9 0M12 12 4.5 4.5"/>',
        'cool' => '<path d="m8 3 4 8 5-5 5 15H2L8 3z"/>',
        'heritage' => '<path d="M3 21h18M5 21V10l7-6 7 6v11M9 21v-5h6v5"/>',
        'dive' => '<path d="M2 12c2-2 4-2 6 0s4 2 6 0 4-2 6 0M2 18c2-2 4-2 6 0s4 2 6 0 4-2 6 0M12 3v4"/>',
        'nature' => '<path d="M12 22V12M7 12a5 5 0 1 1 10 0Z"/><path d="M4 22h16"/>',
        'weekend' => '<path d="M5 17h14l-1.5-6.5A2 2 0 0 0 15.6 9H8.4a2 2 0 0 0-1.9 1.5L5 17zM7 17v2M17 17v2M3 13h2M19 13h2"/>',
    ];
@endphp

@section('content')
{{-- ============ HERO ============ --}}
<section class="pk-hero dx-hero" data-pk-slides>
    <div class="pk-hero__media" aria-hidden="true">
        @foreach($heroSlides as $i => $r)
            <img @if($i === 0) src="{{ $r['image'] }}" class="is-on" fetchpriority="high" @else data-src="{{ $r['image'] }}" @endif data-pk-slide alt="">
        @endforeach
    </div>
    <div class="pk-hero__shade" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-10 sm:pt-16 lg:pt-24 lg:pb-14">
        <nav class="pk-crumbs mb-8" aria-label="Breadcrumb"><a href="{{ url('/') }}">Home</a><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg><span aria-current="page">Destinations</span></nav>
        <div class="max-w-3xl">
            <p class="pk-kicker">{{ $s['regions'] }} regions · {{ number_format($s['guides']) }} destination guides</p>
            <h1 class="pk-h1">Where to go in the <span class="text-sun-300">Philippines</span></h1>
            <p class="pk-lede">Island lagoons, surf towns, cool mountain cities and old Spanish streets. Pick a region, a kind of trip or a place you already have in mind.</p>

            <div class="relative mt-8 max-w-2xl rg-ta--dropdown" data-rg-typeahead>
                <div class="dx-search">
                    <svg class="w-5 h-5 text-slate-400 shrink-0 ml-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>
                    <input data-ta-input type="search" autocomplete="off" spellcheck="false" placeholder="Search a town, island or tourist spot" aria-label="Search destinations" role="combobox" aria-expanded="false" aria-autocomplete="list" class="flex-1 min-w-0 bg-transparent border-0 focus:ring-0 text-slate-900 placeholder-slate-400 text-base sm:text-lg px-3 py-3">
                    <button data-ta-clear type="button" class="hidden" aria-label="Clear"></button>
                    <button data-ta-submit type="button" class="rg-btn rg-btn--coral rg-btn--lg shrink-0"><span>Search</span>{!! $arrow !!}</button>
                </div>
                <div data-ta-panel class="rg-ta-panel hidden" role="listbox" aria-label="Search suggestions"></div>
            </div>
        </div>
    </div>

    {{-- region strip --}}
    <div class="relative border-t border-white/15 bg-ink-950/45 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto">
            <div class="dx-strip no-scrollbar">
                @foreach($data['regions'] as $r)
                    <a href="{{ $r['url'] }}" class="dx-strip__item">
                        <img src="{{ $r['image'] }}" alt="" loading="lazy">
                        <span><strong>{{ $r['name'] }}</strong><small>{{ $r['count'] }} guide{{ $r['count'] === 1 ? '' : 's' }}</small></span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============ THE CLASSICS ============ --}}
@if(count($data['classics']) >= 5)
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div class="max-w-2xl">
                <p class="pk-eyebrow">The classics</p>
                <h2 class="pk-h2">Places that earn the hype</h2>
                <p class="mt-3 text-slate-600 text-lg">First trip to the Philippines? Start with one of these and you cannot go wrong.</p>
            </div>
        </div>
        <div class="dx-bento">
            @foreach(array_slice($data['classics'], 0, count($data['classics']) >= 9 ? 9 : 5) as $i => $t)
                <a href="{{ $t['url'] }}" class="dx-tile group {{ $i === 0 ? 'dx-tile--big' : '' }}">
                    <img src="{{ $t['image'] }}" alt="{{ $t['name'] }}, {{ $t['area'] }}" loading="lazy">
                    <span class="dx-tile__shade"></span>
                    @if($i === 0)<span class="dx-tile__tag">Most searched</span>@endif
                    <span class="dx-tile__body">
                        <span class="dx-tile__name">{{ $t['name'] }}</span>
                        <span class="dx-tile__meta">{{ $t['area'] }} · {{ $t['guides'] }} guide{{ $t['guides'] === 1 ? '' : 's' }}</span>
                    </span>
                    <span class="dx-tile__go">{!! $arrow !!}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ TRIP STYLES ============ --}}
@if($data['styles'])
<section class="bg-sand-100 py-16 md:py-24" id="trip-styles">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8">
            <p class="pk-eyebrow">Travel your way</p>
            <h2 class="pk-h2">Find your kind of trip</h2>
        </div>
        <div class="dx-tabs no-scrollbar" role="tablist" aria-label="Kinds of trip">
            @foreach($data['styles'] as $i => $st)
                <button type="button" role="tab" id="dx-tab-{{ $st['key'] }}" aria-controls="dx-panel-{{ $st['key'] }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" class="dx-tab {{ $i === 0 ? 'is-on' : '' }}" data-dx-tab="{{ $st['key'] }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $styleIcons[$st['key']] ?? '' !!}</svg>{{ $st['title'] }}
                </button>
            @endforeach
        </div>
        @foreach($data['styles'] as $i => $st)
            <div class="dx-panel" id="dx-panel-{{ $st['key'] }}" role="tabpanel" aria-labelledby="dx-tab-{{ $st['key'] }}" data-dx-panel="{{ $st['key'] }}" @if($i) hidden @endif>
                <div class="dx-panel__photo">
                    <img src="{{ $st['image'] }}" alt="{{ $st['title'] }} in the Philippines" loading="lazy">
                    <span class="dx-panel__shade"></span>
                    <div class="dx-panel__cap">
                        <p class="font-display text-2xl sm:text-3xl font-extrabold">{{ $st['title'] }}</p>
                        <p class="mt-1 text-white/85">{{ $st['blurb'] }}</p>
                    </div>
                </div>
                <div class="dx-panel__list">
                    <p class="text-xs font-extrabold uppercase tracking-[.18em] text-slate-500 mb-3">Where to go</p>
                    @foreach($st['links'] as $n => $l)
                        <a href="{{ $l['url'] }}" class="dx-row group">
                            <span class="dx-row__n">{{ str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="dx-row__label">{{ $l['label'] }}</span>
                            <span class="dx-row__go">{!! $arrow !!}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ============ REGIONS ============ --}}
<section class="py-16 md:py-24" id="regions">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
            <div class="max-w-2xl">
                <p class="pk-eyebrow">Region by region</p>
                <h2 class="pk-h2">How far do you want to go?</h2>
                <p class="mt-3 text-slate-600 text-lg">Travel times are from Manila, so you can match a region to the days you have.</p>
            </div>
            <div class="flex flex-wrap gap-2" role="toolbar" aria-label="Filter regions">
                <button type="button" class="dx-chip is-on" data-dx-group="all">All regions</button>
                @foreach(\App\Services\DestinationShowcase::GROUPS as $g => [$label, $sub])
                    <button type="button" class="dx-chip" data-dx-group="{{ $g }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($data['regions'] as $r)
                <article class="dx-region" data-group="{{ $r['facts']['group'] }}">
                    <a href="{{ $r['url'] }}" class="dx-region__photo">
                        <img src="{{ $r['image'] }}" alt="{{ $r['name'] }}" loading="lazy">
                        <span class="dx-region__shade"></span>
                        <span class="dx-region__from"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ $r['facts']['from'] }}</span>
                        <span class="dx-region__name">{{ $r['name'] }}</span>
                    </a>
                    <div class="dx-region__body">
                        <p class="text-slate-600 leading-relaxed">{{ $r['tagline'] }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach(array_slice($r['facts']['known'], 0, 3) as $k)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $k }}</span>
                            @endforeach
                        </div>
                        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div><dt class="text-[.68rem] font-extrabold uppercase tracking-[.14em] text-coral-600">Best time</dt><dd class="mt-0.5 font-semibold text-ink-900 leading-snug">{{ $r['facts']['best'] }}</dd></div>
                            <div><dt class="text-[.68rem] font-extrabold uppercase tracking-[.14em] text-coral-600">Ideal stay</dt><dd class="mt-0.5 font-semibold text-ink-900 leading-snug">{{ $r['facts']['stay'] }}</dd></div>
                        </dl>
                        <div class="mt-auto pt-5 flex items-center justify-between gap-3 border-t border-slate-100">
                            <span class="text-sm text-slate-500">{{ $r['count'] }} guide{{ $r['count'] === 1 ? '' : 's' }}</span>
                            <a href="{{ $r['url'] }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-coral-600 hover:text-coral-700">Explore {{ $r['name'] }} <span class="w-4 h-4 inline-flex">{!! $arrow !!}</span></a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ WHEN TO GO ============ --}}
<section class="relative overflow-hidden bg-ink-900 text-white py-16 md:py-24 isolate">
    <div class="absolute -right-40 -top-40 w-[36rem] h-[36rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(255,199,74,.22), transparent 65%)"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-[1fr_1.6fr] gap-10 lg:gap-16 items-start">
            <div>
                <p class="pk-eyebrow !text-sun-300">When to go</p>
                <h2 class="pk-h2 !text-white">Two seasons, and a fiesta every month</h2>
                <p class="mt-4 text-white/75 text-lg leading-relaxed">The dry months from November to May bring calm seas and blue skies. June to October is the rainy season, with typhoons most likely from July to September. Prices drop and crowds thin out when it rains.</p>
                <ul class="mt-6 space-y-3 text-white/85">
                    <li class="flex gap-3"><span class="dx-dot dx-dot--cool"></span><span><strong class="text-white">Dec to Feb:</strong> cool, dry and busy, book ahead for Christmas and New Year.</span></li>
                    <li class="flex gap-3"><span class="dx-dot dx-dot--hot"></span><span><strong class="text-white">Mar to May:</strong> hot and dry, the best beach weather. Holy Week fills every resort.</span></li>
                    <li class="flex gap-3"><span class="dx-dot dx-dot--storm"></span><span><strong class="text-white">Jun to Oct:</strong> green and quiet. Keep plans flexible and watch storm advisories.</span></li>
                </ul>
            </div>
            <div>
                <div class="dx-months">
                    @foreach($data['months'] as $m)
                        <a href="{{ route('fiestas.index') }}" class="dx-month dx-month--{{ $m['season'] }} {{ $m['n'] === $nowMonth ? 'is-now' : '' }}" title="{{ $m['name'] }}: {{ $m['label'] }}">
                            <span class="dx-month__bar"></span>
                            <span class="dx-month__name">{{ $m['short'] }}</span>
                            <span class="dx-month__label">{{ $m['label'] }}</span>
                            @if($m['fiestas'])<span class="dx-month__fiesta">{{ $m['fiestas'] }} fiesta{{ $m['fiestas'] === 1 ? '' : 's' }}</span>@endif
                            @if($m['n'] === $nowMonth)<span class="dx-month__now">Now</span>@endif
                        </a>
                    @endforeach
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('fiestas.index') }}" class="rg-btn rg-btn--coral">See the festival calendar{!! $arrow !!}</a>
                    <a href="{{ url('/create-your-adventure') }}" class="rg-btn rg-btn--glass">Plan my trip</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ SPOTS ============ --}}
@if(count($data['spots']) >= 4)
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8">
            <p class="pk-eyebrow">Tourist spots</p>
            <h2 class="pk-h2">Worth the detour</h2>
            <p class="mt-3 text-slate-600 text-lg">{{ number_format($s['spots']) }} tourist spots mapped so far. These are the ones travelers keep coming back to.</p>
        </div>
        <div class="dx-spots">
            @foreach($data['spots'] as $i => $sp)
                <a href="{{ $sp['url'] }}" class="dx-spot group {{ in_array($i, [0, 5], true) ? 'dx-spot--tall' : (in_array($i, [3, 6], true) ? 'dx-spot--wide' : '') }}">
                    <img src="{{ $sp['image'] }}" alt="{{ $sp['name'] }}" loading="lazy">
                    <span class="dx-tile__shade"></span>
                    <span class="dx-spot__body">
                        @if($sp['rating'])<span class="dx-spot__rating"><span class="w-3.5 h-3.5 text-sun-300 inline-flex">{!! $star !!}</span>{{ number_format($sp['rating'], 1) }}</span>@endif
                        <span class="dx-spot__name">{{ $sp['name'] }}</span>
                        <span class="dx-spot__loc">{{ $sp['location'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ REVIEWS ============ --}}
@if($data['reviews'])
<section class="bg-sand-100 py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8">
            <p class="pk-eyebrow">From travelers</p>
            <h2 class="pk-h2">Trips planned with these guides</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5 rg-swipe">
            @foreach($data['reviews'] as $rv)
                <figure class="flex flex-col rounded-[1.25rem] bg-white border border-sand-200 p-6">
                    <span class="flex gap-0.5 text-sun-400">@for($i = 0; $i < $rv['rating']; $i++)<span class="w-4 h-4 inline-flex">{!! $star !!}</span>@endfor</span>
                    <blockquote class="mt-3 text-ink-900 leading-relaxed flex-1">"{{ $rv['text'] }}"</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <img src="{{ $rv['avatar'] }}" alt="" class="w-10 h-10 rounded-full object-cover bg-slate-100" loading="lazy">
                        <span class="min-w-0">
                            <span class="block font-semibold text-ink-900 text-sm">{{ $rv['name'] }}@if($rv['from'])<span class="font-normal text-slate-500">, {{ $rv['from'] }}</span>@endif</span>
                            @if($rv['about'])<a href="{{ $rv['url'] }}" class="block text-xs text-brand-700 hover:underline truncate">{{ $rv['about'] }}</a>@endif
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ A-Z ============ --}}
<section class="py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-6 flex-wrap">
            <div>
                <p class="pk-eyebrow">Every guide</p>
                <h2 class="pk-h2 !text-2xl sm:!text-3xl">All {{ number_format($s['guides']) }} destination guides</h2>
            </div>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($data['az'] as $g)
                <details class="dx-az">
                    <summary><span class="font-semibold text-ink-900">{{ $g['name'] }}</span><span class="text-sm text-slate-500">{{ count($g['guides']) }}</span></summary>
                    <ul>
                        @foreach($g['guides'] as $l)
                            <li><a href="{{ $l['url'] }}">{{ $l['label'] }}</a></li>
                        @endforeach
                        <li><a href="{{ $g['url'] }}" class="!font-bold !text-coral-600">All of {{ $g['name'] }} &rarr;</a></li>
                    </ul>
                </details>
            @endforeach
        </div>
    </div>
</section>

<style>
    .dx-hero .pk-h1 { font-size: clamp(2.5rem, 6vw, 4.6rem); }
    .dx-search { display: flex; align-items: center; background: #fff; border-radius: 1.1rem; padding: .4rem; box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .55), 0 0 0 6px rgba(255, 255, 255, .12); }
    .dx-search:focus-within { box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .55), 0 0 0 6px rgba(255, 209, 102, .5); }
    .dx-search input::-webkit-search-cancel-button { -webkit-appearance: none; display: none; }
    @media (max-width: 480px) { .dx-search .rg-btn { padding: .8rem 1rem; } .dx-search .rg-btn span { display: none; } }
    .dx-hero .rg-ta-panel { color: #0f172a; }

    .dx-strip { display: flex; gap: .5rem; overflow-x: auto; padding: .85rem 1rem; scroll-snap-type: x proximity; }
    @media (min-width: 640px) { .dx-strip { padding: .9rem 1.5rem; } }
    @media (min-width: 1024px) { .dx-strip { padding: 1rem 2rem; } }
    .dx-strip__item { flex-shrink: 0; scroll-snap-align: start; display: flex; align-items: center; gap: .6rem; padding: .35rem .9rem .35rem .35rem; border-radius: 999px; color: #fff; background: rgba(255, 255, 255, .08); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .14); transition: background-color .2s ease; }
    .dx-strip__item:hover { background: rgba(255, 255, 255, .2); }
    .dx-strip__item img { width: 2.4rem; height: 2.4rem; border-radius: 999px; object-fit: cover; }
    .dx-strip__item strong { display: block; font-size: .85rem; line-height: 1.15; white-space: nowrap; }
    .dx-strip__item small { display: block; font-size: .7rem; color: rgba(255, 255, 255, .65); }

    .dx-bento { display: grid; gap: .9rem; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: 11rem; }
    @media (min-width: 768px) { .dx-bento { grid-template-columns: repeat(4, minmax(0, 1fr)); grid-auto-rows: 13.5rem; gap: 1.1rem; } }
    .dx-tile { position: relative; display: block; overflow: hidden; border-radius: 1.4rem; background: #0c2438; isolation: isolate; }
    .dx-tile--big { grid-column: span 2; grid-row: span 2; }
    .dx-tile img, .dx-spot img, .dx-panel__photo img, .dx-region__photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .dx-tile:hover img, .dx-spot:hover img, .dx-region__photo:hover img { transform: scale(1.06); }
    .dx-tile__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, 0) 40%, rgba(7, 18, 29, .85) 100%); }
    .dx-tile__tag { position: absolute; top: 1rem; left: 1rem; border-radius: 999px; background: #ef5b3a; color: #fff; font-size: .72rem; font-weight: 800; padding: .3rem .7rem; }
    .dx-tile__body { position: absolute; left: 1.1rem; right: 3.8rem; bottom: 1.05rem; color: #fff; }
    .dx-tile__name { display: block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.3rem; line-height: 1.05; letter-spacing: -0.02em; }
    .dx-tile--big .dx-tile__name { font-size: clamp(2rem, 4vw, 3rem); }
    .dx-tile__meta { display: block; margin-top: .3rem; font-size: .8rem; color: rgba(255, 255, 255, .8); }
    .dx-tile__go { position: absolute; right: .9rem; bottom: 1rem; width: 2.3rem; height: 2.3rem; border-radius: 999px; background: #fff; color: #0b1b2b; display: inline-flex; align-items: center; justify-content: center; transition: background-color .2s ease, transform .2s ease; }
    .dx-tile__go svg { width: .95rem; height: .95rem; }
    .dx-tile:hover .dx-tile__go { background: #ffc74a; transform: translateX(2px); }
    @media (max-width: 767px) { .dx-tile__go { display: none; } .dx-tile__body { right: 1rem; } .dx-tile__name { font-size: 1.05rem; } }

    .dx-tabs { display: flex; gap: .5rem; overflow-x: auto; margin: 0 -1rem 1.5rem; padding: 0 1rem .25rem; }
    @media (min-width: 640px) { .dx-tabs { margin: 0 0 1.75rem; padding: 0 0 .25rem; flex-wrap: wrap; } }
    .dx-tab { flex-shrink: 0; display: inline-flex; align-items: center; gap: .5rem; border-radius: 999px; border: 1.5px solid #e9dcc5; background: #fff; padding: .6rem 1rem; font-weight: 700; font-size: .9rem; color: #334155; transition: all .2s ease; }
    .dx-tab svg { width: 1.1rem; height: 1.1rem; }
    .dx-tab:hover { border-color: #0b1b2b; color: #0b1b2b; }
    .dx-tab.is-on { background: #0b1b2b; border-color: #0b1b2b; color: #fff; }
    .dx-panel { display: grid; gap: 1.25rem; }
    .dx-panel[hidden] { display: none; }
    @media (min-width: 1024px) { .dx-panel { grid-template-columns: 1.35fr 1fr; gap: 2rem; align-items: stretch; } }
    .dx-panel__photo { position: relative; min-height: 18rem; border-radius: 1.5rem; overflow: hidden; background: #0c2438; isolation: isolate; }
    @media (min-width: 1024px) { .dx-panel__photo { min-height: 26rem; } }
    .dx-panel__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, 0) 35%, rgba(7, 18, 29, .85) 100%); }
    .dx-panel__cap { position: absolute; left: 1.5rem; right: 1.5rem; bottom: 1.4rem; color: #fff; }
    .dx-panel__list { border-radius: 1.5rem; background: #fff; border: 1px solid #f0e4cf; padding: 1.4rem; display: flex; flex-direction: column; }
    .dx-row { display: flex; align-items: center; gap: 1rem; padding: .9rem .25rem; border-top: 1px solid #f1ece2; }
    .dx-row:first-of-type { border-top: 0; }
    .dx-row__n { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; color: #ef5b3a; font-size: .95rem; width: 1.8rem; }
    .dx-row__label { flex: 1; font-weight: 700; color: #0b1b2b; font-size: 1.05rem; }
    .dx-row__go { width: 2.1rem; height: 2.1rem; border-radius: 999px; border: 1.5px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center; color: #0b1b2b; transition: all .2s ease; }
    .dx-row__go svg { width: .9rem; height: .9rem; }
    .dx-row:hover .dx-row__go { background: #0b1b2b; border-color: #0b1b2b; color: #fff; }
    .dx-row:hover .dx-row__label { color: #0f5788; }

    .dx-chip { border-radius: 999px; border: 1.5px solid #e2e8f0; background: #fff; padding: .5rem .95rem; font-size: .85rem; font-weight: 700; color: #334155; transition: all .2s ease; }
    .dx-chip:hover { border-color: #0b1b2b; }
    .dx-chip.is-on { background: #0b1b2b; border-color: #0b1b2b; color: #fff; }
    .dx-region { display: flex; flex-direction: column; border-radius: 1.5rem; overflow: hidden; background: #fff; border: 1px solid #e3e9f0; transition: box-shadow .3s ease, transform .3s ease; }
    .dx-region:hover { box-shadow: 0 24px 48px -28px rgba(11, 27, 43, .45); transform: translateY(-3px); }
    .dx-region[hidden] { display: none; }
    .dx-region__photo { position: relative; display: block; aspect-ratio: 16 / 10; overflow: hidden; background: #0c2438; isolation: isolate; }
    .dx-region__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, .15) 0%, rgba(7, 18, 29, 0) 40%, rgba(7, 18, 29, .8) 100%); }
    .dx-region__from { position: absolute; top: .9rem; left: .9rem; display: inline-flex; align-items: center; gap: .35rem; border-radius: 999px; background: rgba(255, 255, 255, .95); color: #0b1b2b; font-size: .75rem; font-weight: 700; padding: .3rem .65rem; }
    .dx-region__from svg { width: .85rem; height: .85rem; color: #ef5b3a; }
    .dx-region__name { position: absolute; left: 1.2rem; bottom: 1rem; color: #fff; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.6rem; letter-spacing: -0.02em; line-height: 1.05; }
    .dx-region__body { flex: 1; display: flex; flex-direction: column; padding: 1.2rem 1.25rem 1.2rem; }

    .dx-dot { width: .8rem; height: .8rem; border-radius: 999px; margin-top: .4rem; flex-shrink: 0; }
    .dx-dot--cool, .dx-month--cool .dx-month__bar { background: #5fb3e6; }
    .dx-dot--hot, .dx-month--hot .dx-month__bar { background: #ffc74a; }
    .dx-month--wet .dx-month__bar { background: #6b7f99; }
    .dx-dot--storm, .dx-month--storm .dx-month__bar { background: #ef5b3a; }
    .dx-months { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .5rem; }
    @media (min-width: 640px) { .dx-months { grid-template-columns: repeat(6, minmax(0, 1fr)); } }
    .dx-month { position: relative; display: flex; flex-direction: column; border-radius: 1rem; background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .1); padding: .8rem .75rem .75rem; min-height: 7.2rem; transition: background-color .2s ease; }
    .dx-month:hover { background: rgba(255, 255, 255, .1); }
    .dx-month.is-now { background: rgba(255, 255, 255, .14); border-color: rgba(255, 199, 74, .6); }
    .dx-month__bar { height: .35rem; border-radius: 999px; margin-bottom: .6rem; }
    .dx-month__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.1rem; }
    .dx-month__label { font-size: .72rem; color: rgba(255, 255, 255, .7); line-height: 1.3; margin-top: .1rem; }
    .dx-month__fiesta { margin-top: auto; padding-top: .4rem; font-size: .7rem; font-weight: 700; color: #ffc74a; }
    .dx-month__now { position: absolute; top: .55rem; right: .55rem; border-radius: 999px; background: #ffc74a; color: #0b1b2b; font-size: .6rem; font-weight: 800; padding: .1rem .4rem; text-transform: uppercase; }

    .dx-spots { display: grid; gap: .9rem; grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: 12rem; grid-auto-flow: dense; }
    @media (min-width: 1024px) { .dx-spots { grid-template-columns: repeat(4, minmax(0, 1fr)); grid-auto-rows: 14rem; gap: 1.1rem; } .dx-spot--tall { grid-row: span 2; } .dx-spot--wide { grid-column: span 2; } }
    .dx-spot { position: relative; display: block; overflow: hidden; border-radius: 1.3rem; background: #0c2438; isolation: isolate; }
    .dx-spot__body { position: absolute; left: 1rem; right: 1rem; bottom: .95rem; color: #fff; }
    .dx-spot__rating { display: inline-flex; align-items: center; gap: .25rem; border-radius: 999px; background: rgba(7, 18, 29, .55); padding: .15rem .5rem; font-size: .72rem; font-weight: 800; margin-bottom: .4rem; }
    .dx-spot__name { display: block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; line-height: 1.15; font-size: 1.05rem; }
    .dx-spot__loc { display: block; font-size: .78rem; color: rgba(255, 255, 255, .78); margin-top: .15rem; }

    .dx-az { border-radius: 1rem; border: 1px solid #e3e9f0; background: #fff; }
    .dx-az > summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .9rem 1.1rem; }
    .dx-az > summary::-webkit-details-marker { display: none; }
    .dx-az[open] > summary { border-bottom: 1px solid #eef2f6; }
    .dx-az ul { padding: .7rem 1.1rem 1rem; columns: 1; }
    .dx-az li { padding: .2rem 0; }
    .dx-az a { font-size: .9rem; color: #334155; }
    .dx-az a:hover { color: #0f5788; text-decoration: underline; }
</style>

<script>
(function () {
    // trip-style tabs
    var tabs = document.querySelectorAll('[data-dx-tab]');
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            var k = t.getAttribute('data-dx-tab');
            tabs.forEach(function (o) { var on = o === t; o.classList.toggle('is-on', on); o.setAttribute('aria-selected', on ? 'true' : 'false'); });
            document.querySelectorAll('[data-dx-panel]').forEach(function (p) { p.hidden = p.getAttribute('data-dx-panel') !== k; });
        });
    });
    // region group filter
    var chips = document.querySelectorAll('[data-dx-group]');
    chips.forEach(function (c) {
        c.addEventListener('click', function () {
            var g = c.getAttribute('data-dx-group');
            chips.forEach(function (o) { o.classList.toggle('is-on', o === c); });
            document.querySelectorAll('.dx-region').forEach(function (r) { r.hidden = g !== 'all' && r.getAttribute('data-group') !== g; });
        });
    });
})();
</script>
@endsection
