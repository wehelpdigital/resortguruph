@extends('layouts.public')
@php
    $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph');
    $s = $data['stats'];
    $fmt = fn ($n) => $n >= 1000 ? number_format(floor($n / 100) * 100) . '+' : number_format($n);
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $stars = fn ($n = 5) => str_repeat('<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>', $n);
@endphp

@section('title'){{ $siteName }}: Philippines Travel Guide to Destinations, Food and Stays @endsection
@section('meta_description')Plan your Philippines trip with {{ number_format($s['guides']) }} local guides to destinations, tourist spots, food and festivals, backed by {{ number_format($s['reviews']) }} traveler reviews. Free to use.@endsection
@section('canonical'){{ url('/') }}@endsection
@section('og_image'){{ asset('images/hero-palawan-1600.webp') }}@endsection
@section('jsonld')
{!! $jsonld !!}
@if(!empty($data['faqs']))
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], $data['faqs'])], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endsection

@section('content')
{{-- ============ HERO ============ --}}
<section class="sx-hero relative isolate overflow-hidden text-white">
    <div class="sx-hero__media" id="sxHeroMedia" aria-hidden="true">
        <video muted loop playsinline preload="none" disablepictureinpicture tabindex="-1" data-src="{{ asset('videos/hero-philippines.mp4') }}"></video>
        <div class="sx-hero__poster"></div>
    </div>
    <div class="sx-hero__shade" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10 sm:pt-20 md:pt-28 md:pb-16 lg:pt-32 lg:pb-24">
        <div class="max-w-2xl">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 ring-1 ring-white/25 backdrop-blur px-3.5 py-1.5 text-xs sm:text-sm font-semibold">
                <span class="sx-stars text-sun-300">{!! $stars() !!}</span>
                {{ $s['rating'] }} from {{ number_format($s['reviews']) }} traveler reviews
            </p>
            <h1 class="mt-6 font-extrabold tracking-tight text-[2.6rem] sm:text-6xl lg:text-[4.4rem]">7,641 islands.<br><span class="text-sun-300">One guide that knows them.</span></h1>
            <p class="mt-5 text-lg sm:text-xl leading-relaxed text-white/85 max-w-xl">Honest guides to where to go, what to eat and where to stay, written by travelers who have actually been there. Free to use, no sign-up.</p>

            <div class="relative mt-8 rg-ta--dropdown" data-rg-typeahead>
                <div class="sx-search">
                    <svg class="w-5 h-5 text-slate-400 shrink-0 ml-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>
                    <input data-ta-input type="search" autocomplete="off" spellcheck="false" placeholder="Where do you want to go?" aria-label="Search destinations, food and festivals" role="combobox" aria-expanded="false" aria-autocomplete="list" class="flex-1 min-w-0 bg-transparent border-0 focus:ring-0 text-slate-900 placeholder-slate-400 text-base sm:text-lg px-3 py-3">
                    <button data-ta-clear type="button" class="hidden" aria-label="Clear"></button>
                    <button data-ta-submit type="button" class="rg-btn rg-btn--coral rg-btn--lg shrink-0">Explore{!! $arrow !!}</button>
                </div>
                <div data-ta-panel class="rg-ta-panel hidden" role="listbox" aria-label="Search suggestions"></div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                <span class="text-white/70 font-medium mr-1">Popular:</span>
                @foreach(array_slice($data['trending'], 0, 5) as $t)
                    <a href="{{ $t['url'] }}" class="rounded-full bg-white/10 ring-1 ring-white/25 hover:bg-white hover:text-ink-900 px-3 py-1 font-semibold">{{ $t['name'] }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="relative border-t border-white/15 bg-ink-950/40 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4">
            @foreach([[$fmt($s['guides']), 'local travel guides'], [$fmt($s['spots']), 'tourist spots mapped'], [$s['fiestas'], 'fiestas and festivals'], ['Free', 'no sign-up needed']] as $i => [$v, $l])
                <div class="py-4 md:py-5 {{ $i % 2 ? 'pl-4 md:pl-6 border-l border-white/10' : ($i ? 'md:pl-6 md:border-l md:border-white/10' : '') }}">
                    <p class="font-display text-xl md:text-2xl font-extrabold">{{ $v }}</p>
                    <p class="text-xs md:text-sm text-white/70">{{ $l }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ TRENDING ============ --}}
@if(!empty($data['trending']))
<section class="py-16 md:py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <p class="sx-eyebrow text-coral-600">Trending now</p>
                <h2 class="sx-h2">Where travelers are heading</h2>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <button type="button" class="sx-arrow" data-sx-rail-prev="trend" aria-label="Previous"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 19l-7-7 7-7"/></svg></button>
                <button type="button" class="sx-arrow" data-sx-rail-next="trend" aria-label="Next"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></button>
            </div>
        </div>
        <div class="sx-rail no-scrollbar" data-sx-rail="trend">
            @foreach($data['trending'] as $t)
                <a href="{{ $t['url'] }}" class="sx-dest group">
                    <img src="{{ $t['image'] }}" alt="{{ $t['name'] }}, {{ $t['area'] }}" loading="lazy">
                    <span class="sx-dest__shade"></span>
                    <span class="sx-dest__pill">{{ $t['guides'] }} guide{{ $t['guides'] === 1 ? '' : 's' }}</span>
                    <span class="sx-dest__body">
                        <span class="sx-dest__name">{{ $t['name'] }}</span>
                        <span class="sx-dest__area">{{ $t['area'] }}</span>
                    </span>
                    <span class="sx-dest__go">{!! $arrow !!}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ TRAVEL STYLES ============ --}}
<section class="pb-16 md:pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8">
            <p class="sx-eyebrow text-brand-600">Travel your way</p>
            <h2 class="sx-h2">What kind of trip are you after?</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
            @foreach($data['styles'] as $i => $st)
                <a href="{{ $st['url'] }}" class="sx-style group" style="--c:{{ $st['c'] }}">
                    <img src="{{ $st['image'] }}" alt="{{ $st['title'] }} in the Philippines" loading="lazy">
                    <span class="sx-style__shade"></span>
                    <span class="sx-style__body">
                        <span class="sx-style__title">{{ $st['title'] }}</span>
                        <span class="sx-style__text">{{ $st['text'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ HOW IT WORKS ============ --}}
<section class="bg-sand-100 py-16 md:py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div class="relative order-2 lg:order-1 h-[22rem] sm:h-[28rem]">
            <img src="{{ asset('storage/rg-media/destinations/el-nido-1.jpg') }}" alt="Beach in El Nido, Palawan" loading="lazy" class="sx-collage sx-collage--a">
            <img src="{{ asset('storage/rg-media/foods/adobo-1.jpg') }}" alt="Filipino adobo" loading="lazy" class="sx-collage sx-collage--b">
            <img src="{{ asset('storage/rg-media/fiestas/ati-atihan.jpg') }}" alt="Ati-Atihan festival" loading="lazy" class="sx-collage sx-collage--c">
            <div class="sx-float">
                <span class="sx-stars text-sun-400">{!! $stars() !!}</span>
                <p class="mt-1 text-sm font-semibold text-slate-900">"Booked our return already."</p>
                <p class="text-xs text-slate-500">Traveler review, Bulacan</p>
            </div>
        </div>
        <div class="order-1 lg:order-2">
            <p class="sx-eyebrow text-coral-600">Plan in minutes</p>
            <h2 class="sx-h2">Your trip, sorted in three steps</h2>
            <ol class="mt-8 space-y-6">
                @foreach([['Pick a place', 'Browse destinations by region, style or season and find the spots worth the trip.'], ['Read the local take', 'Real guides on getting there, what to eat, where to stay and what to skip.'], ['Save your plan', 'Build a free itinerary with Create Your Adventure and take it with you.']] as $n => [$h, $t])
                    <li class="flex gap-4">
                        <span class="sx-step">{{ $n + 1 }}</span>
                        <span><span class="block text-lg font-bold text-slate-900 font-display">{{ $h }}</span><span class="block mt-1 text-slate-600 leading-relaxed">{{ $t }}</span></span>
                    </li>
                @endforeach
            </ol>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('create-your-adventure') }}" class="rg-btn rg-btn--coral rg-btn--lg">Create your free trip plan{!! $arrow !!}</a>
                <a href="{{ route('destinations.index') }}" class="rg-btn rg-btn--outline rg-btn--lg">Browse destinations</a>
            </div>
        </div>
    </div>
</section>

{{-- ============ FESTIVALS ============ --}}
@if(!empty($data['fiestas']['items']))
<section class="bg-ink-900 text-white py-16 md:py-24 relative overflow-hidden">
    <div class="sx-glow" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div class="max-w-2xl">
                <p class="sx-eyebrow text-sun-300">Happening in {{ $data['fiestas']['month'] }}</p>
                <h2 class="sx-h2 text-white">Catch a fiesta while you are there</h2>
                <p class="mt-3 text-white/70 text-lg">Street dancing, food stalls and the whole town out in color. Plan your dates around one of these.</p>
            </div>
            <a href="{{ route('fiestas.index') }}" class="rg-btn rg-btn--glass shrink-0 self-start md:self-auto">Full festival calendar{!! $arrow !!}</a>
        </div>
        <div class="rg-swipe grid sm:grid-cols-2 {{ count($data['fiestas']['items']) >= 4 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-5">
            @foreach($data['fiestas']['items'] as $f)
                <a href="{{ $f['url'] }}" class="sx-fiesta group">
                    <span class="sx-fiesta__img"><img src="{{ $f['image'] }}" alt="{{ $f['name'] }}" loading="lazy"></span>
                    <span class="block p-4">
                        <span class="block text-xs font-bold uppercase tracking-wider text-sun-300 line-clamp-1">{{ $f['when'] }}</span>
                        <span class="block mt-1 text-lg font-bold font-display">{{ $f['name'] }}</span>
                        <span class="block text-sm text-white/65">{{ $f['place'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ REGIONS ============ --}}
@if(!empty($data['regions']))
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <p class="sx-eyebrow text-brand-600">Explore by region</p>
                <h2 class="sx-h2">Start from the map</h2>
            </div>
            <a href="{{ route('destinations.index') }}" class="hidden sm:inline-flex rg-btn rg-btn--outline">All destinations{!! $arrow !!}</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($data['regions'] as $r)
                <a href="{{ $r['url'] }}" class="sx-region group">
                    <img src="{{ $r['image'] }}" alt="" loading="lazy">
                    <span class="min-w-0"><span class="block font-bold text-slate-900 truncate">{{ $r['name'] }}</span><span class="block text-xs text-slate-500">{{ number_format($r['count']) }} guides</span></span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ REVIEWS ============ --}}
@if(!empty($data['reviews']))
<section class="bg-slate-50 py-16 md:py-24 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-[20rem_1fr] gap-8 lg:gap-12 items-start">
        <div class="lg:sticky" style="top:calc(var(--rg-header-h) + 1.5rem)">
            <p class="sx-eyebrow text-coral-600">Traveler reviews</p>
            <h2 class="sx-h2">Real trips, honest tips</h2>
            <div class="mt-6 flex items-end gap-3">
                <span class="font-display text-6xl font-extrabold text-slate-900 leading-none">{{ $s['rating'] }}</span>
                <span class="pb-1"><span class="sx-stars text-sun-400">{!! $stars() !!}</span><span class="block text-sm text-slate-500">from {{ number_format($s['reviews']) }} reviews</span></span>
            </div>
            <p class="mt-4 text-slate-600 leading-relaxed">Travelers rate the places they visit and share what actually helped, so the next person plans a better trip.</p>
        </div>
        <div class="rg-swipe grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($data['reviews'] as $r)
                <figure class="sx-review">
                    <span class="sx-stars text-sun-400">{!! $stars($r['rating']) !!}</span>
                    <blockquote class="mt-3 text-slate-700 leading-relaxed">"{{ $r['text'] }}"</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <img src="{{ $r['avatar'] }}" alt="" class="w-10 h-10 rounded-full object-cover bg-slate-100" loading="lazy">
                        <span class="min-w-0 text-sm"><span class="block font-semibold text-slate-900">{{ $r['name'] }}</span>
                        <span class="block text-slate-500 truncate">{{ $r['from'] }}@if($r['about']) · <a href="{{ $r['url'] }}" class="text-brand-600 hover:underline">{{ $r['about'] }}</a>@endif</span></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ BLOG ============ --}}
@if(!empty($data['posts']))
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <p class="sx-eyebrow text-brand-600">From the blog</p>
                <h2 class="sx-h2">Fresh trip ideas</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="hidden sm:inline-flex rg-btn rg-btn--outline">Read the blog{!! $arrow !!}</a>
        </div>
        <div class="rg-swipe grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($data['posts'] as $p)
                <a href="{{ $p['url'] }}" class="sx-post group">
                    <span class="sx-post__img"><img src="{{ $p['image'] }}" alt="{{ $p['title'] }}" loading="lazy"></span>
                    <span class="block pt-4">
                        @if($p['date'])<span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $p['date'] }}</span>@endif
                        <span class="block mt-1 text-lg font-bold text-slate-900 font-display leading-snug group-hover:text-brand-600">{{ $p['title'] }}</span>
                        @if($p['excerpt'])<span class="block mt-2 text-sm text-slate-600 line-clamp-2">{{ $p['excerpt'] }}</span>@endif
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ PARTNER ============ --}}
<section class="pb-16 md:pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="sx-partner rounded-3xl px-6 py-8 sm:px-10 sm:py-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <p class="sx-eyebrow text-brand-600">For business owners</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Run a resort, restaurant or tour?</h2>
                <p class="mt-2 text-slate-600 leading-relaxed">Get in front of travelers who are already reading about your area. Listing is free, and verified partners earn our recommended badge.</p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--ocean rg-btn--lg">List your business free{!! $arrow !!}</a>
                <a href="{{ route('partner-directory') }}" class="rg-btn rg-btn--outline rg-btn--lg">See partners</a>
            </div>
        </div>
    </div>
</section>

{{-- ============ FAQ ============ --}}
@if(!empty($data['faqs']))
<section class="pb-20 md:pb-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <p class="sx-eyebrow text-brand-600">Good to know</p>
            <h2 class="sx-h2">Questions before you go</h2>
        </div>
        <div class="divide-y divide-slate-200 border-y border-slate-200">
            @foreach($data['faqs'] as $f)
                <details class="sx-faq group">
                    <summary class="flex items-center justify-between gap-4 py-5 cursor-pointer">
                        <span class="font-semibold text-slate-900 text-base sm:text-lg">{{ $f['q'] }}</span>
                        <span class="sx-faq__icon" aria-hidden="true"></span>
                    </summary>
                    <div class="pb-5 -mt-1 text-slate-600 leading-relaxed">{{ $f['a'] }}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    .sx-hero { background: #0c2438; }
    .sx-hero h1 { line-height: 1.04 !important; letter-spacing: -0.03em; text-shadow: 0 2px 30px rgba(7, 18, 29, .35); }
    .sx-hero__media { position: absolute; inset: 0; z-index: -2; }
    .sx-hero__media video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .sx-hero__poster { position: absolute; inset: 0; background: url('{{ asset('images/hero-palawan-960.webp') }}') center 60% / cover no-repeat; transition: opacity 1.2s ease; }
    @media (min-width: 768px) { .sx-hero__poster { background-image: url('{{ asset('images/hero-palawan-1600.webp') }}'); } }
    .sx-hero__poster.is-hidden { opacity: 0; }
    .sx-hero__shade { position: absolute; inset: 0; z-index: -1; background: linear-gradient(90deg, rgba(7, 18, 29, .86) 0%, rgba(7, 18, 29, .6) 45%, rgba(7, 18, 29, .15) 100%), linear-gradient(180deg, rgba(7, 18, 29, .35) 0%, rgba(7, 18, 29, 0) 30%, rgba(7, 18, 29, .45) 100%); }
    @media (max-width: 767px) { .sx-hero__shade { background: linear-gradient(180deg, rgba(7, 18, 29, .55) 0%, rgba(7, 18, 29, .7) 60%, rgba(7, 18, 29, .85) 100%); } }
    .sx-stars { display: inline-flex; gap: .1rem; }
    .sx-stars svg { width: .95rem; height: .95rem; }
    .sx-search { display: flex; align-items: center; background: #fff; border-radius: 1.1rem; padding: .4rem; box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .55), 0 0 0 6px rgba(255, 255, 255, .12); }
    .sx-search:focus-within { box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .55), 0 0 0 6px rgba(255, 209, 102, .5); }
    .sx-search input::-webkit-search-cancel-button { -webkit-appearance: none; display: none; }
    @media (max-width: 480px) { .sx-search .rg-btn { padding: .8rem 1rem; } .sx-search .rg-btn span { display: none; } }
    .sx-hero .rg-ta-panel { color: #0f172a; }

    .sx-eyebrow { font-size: .72rem; font-weight: 800; letter-spacing: .2em; text-transform: uppercase; margin-bottom: .6rem; }
    .sx-h2 { font-size: clamp(1.85rem, 3.4vw, 2.75rem); line-height: 1.1; font-weight: 800; color: #0b1b2b; letter-spacing: -0.025em; }

    .sx-arrow { display: inline-flex; align-items: center; justify-content: center; width: 2.9rem; height: 2.9rem; border-radius: 999px; border: 1.5px solid #e2e8f0; color: #0f172a; background: #fff; }
    .sx-arrow:hover { border-color: #0f172a; }
    .sx-arrow svg { width: 1.1rem; height: 1.1rem; }

    .sx-rail { display: flex; gap: 1rem; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; margin: 0 -1rem; padding: .25rem 1rem 1.25rem; }
    @media (min-width: 640px) { .sx-rail { margin: 0 -1.5rem; padding-left: 1.5rem; padding-right: 1.5rem; } }
    @media (min-width: 1024px) { .sx-rail { margin: 0; padding: .25rem 0 1.25rem; } }
    .sx-dest { position: relative; flex: 0 0 74%; aspect-ratio: 4 / 5; border-radius: 1.5rem; overflow: hidden; scroll-snap-align: start; background: #0c2438; isolation: isolate; }
    @media (min-width: 640px) { .sx-dest { flex-basis: 16.5rem; } }
    .sx-dest img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .sx-dest:hover img { transform: scale(1.07); }
    .sx-dest__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, .1) 40%, rgba(7, 18, 29, .85) 100%); }
    .sx-dest__pill { position: absolute; top: .9rem; left: .9rem; font-size: .72rem; font-weight: 700; color: #fff; background: rgba(7, 18, 29, .45); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: .25rem .6rem; border-radius: 999px; }
    .sx-dest__body { position: absolute; left: 1.15rem; right: 3.8rem; bottom: 1.15rem; color: #fff; }
    .sx-dest__name { display: block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 1.6rem; font-weight: 800; line-height: 1.05; letter-spacing: -0.02em; }
    .sx-dest__area { display: block; margin-top: .25rem; font-size: .85rem; color: rgba(255, 255, 255, .8); }
    .sx-dest__go { position: absolute; right: 1rem; bottom: 1.1rem; width: 2.5rem; height: 2.5rem; border-radius: 999px; background: #fff; color: #0b1b2b; display: inline-flex; align-items: center; justify-content: center; transition: transform .2s ease, background-color .2s ease; }
    .sx-dest__go svg { width: 1rem; height: 1rem; }
    .sx-dest:hover .sx-dest__go { background: #ffc74a; transform: translateX(2px); }

    .sx-style { position: relative; display: block; overflow: hidden; border-radius: 1.4rem; aspect-ratio: 1 / 1; isolation: isolate; background: #0c2438; }
    @media (min-width: 640px) { .sx-style { aspect-ratio: 4 / 3; } }
    @media (min-width: 1024px) { .sx-style.lg\:row-span-2 { aspect-ratio: auto; } }
    .sx-style img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .sx-style:hover img { transform: scale(1.06); }
    .sx-style__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, 0) 35%, rgba(7, 18, 29, .82) 100%); }
    .sx-style__body { position: absolute; left: 1rem; right: 1rem; bottom: 1rem; color: #fff; }
    @media (min-width: 640px) { .sx-style__body { left: 1.4rem; right: 1.4rem; bottom: 1.3rem; } }
    .sx-style__title { display: flex; align-items: center; gap: .5rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.1rem; line-height: 1.15; }
    .sx-style__title::before { content: ""; width: .6rem; height: .6rem; border-radius: 999px; background: var(--c); box-shadow: 0 0 0 3px rgba(255, 255, 255, .85); flex-shrink: 0; }
    @media (min-width: 640px) { .sx-style__title { font-size: 1.4rem; } }
    .sx-style__text { display: block; margin-top: .3rem; font-size: .85rem; color: rgba(255, 255, 255, .82); }
    @media (max-width: 639px) { .sx-style__text { display: none; } }

    .sx-collage { position: absolute; object-fit: cover; border-radius: 1.5rem; box-shadow: 0 30px 60px -25px rgba(11, 27, 43, .55); border: 6px solid #fff; }
    .sx-collage--a { left: 0; top: 0; width: 68%; height: 72%; }
    .sx-collage--b { right: 0; top: 12%; width: 42%; height: 46%; }
    .sx-collage--c { left: 22%; bottom: 0; width: 46%; height: 42%; }
    .sx-float { position: absolute; right: 4%; bottom: 6%; background: #fff; border-radius: 1rem; padding: .85rem 1rem; box-shadow: 0 20px 40px -18px rgba(11, 27, 43, .45); max-width: 14rem; }
    .sx-step { flex-shrink: 0; width: 2.6rem; height: 2.6rem; border-radius: 999px; background: #0b1b2b; color: #ffc74a; display: inline-flex; align-items: center; justify-content: center; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; }

    .sx-glow { position: absolute; right: -10rem; top: -10rem; width: 34rem; height: 34rem; border-radius: 999px; background: radial-gradient(circle, rgba(239, 91, 58, .35), transparent 65%); }
    .sx-fiesta { display: block; border-radius: 1.25rem; overflow: hidden; background: rgba(255, 255, 255, .06); border: 1px solid rgba(255, 255, 255, .1); transition: background-color .2s ease, transform .25s ease; }
    .sx-fiesta:hover { background: rgba(255, 255, 255, .1); transform: translateY(-3px); }
    .sx-fiesta__img { display: block; aspect-ratio: 4 / 3; overflow: hidden; }
    .sx-fiesta__img img { width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .sx-fiesta:hover .sx-fiesta__img img { transform: scale(1.06); }

    .sx-region { display: flex; align-items: center; gap: .75rem; padding: .6rem; border-radius: 1rem; border: 1px solid #e2e8f0; background: #fff; transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
    .sx-region:hover { border-color: #afd5ec; box-shadow: 0 12px 24px -16px rgba(11, 27, 43, .3); transform: translateY(-2px); }
    .sx-region img { width: 3.25rem; height: 3.25rem; border-radius: .75rem; object-fit: cover; flex-shrink: 0; }

    .sx-review { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.4rem; display: flex; flex-direction: column; justify-content: space-between; }

    .sx-post__img { display: block; aspect-ratio: 16 / 10; border-radius: 1.25rem; overflow: hidden; background: #e2e8f0; }
    .sx-post__img img { width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .sx-post:hover .sx-post__img img { transform: scale(1.05); }

    .sx-partner { background: linear-gradient(120deg, #eef6fb 0%, #fdfbf7 60%, #fff3ef 100%); border: 1px solid #d6eaf5; }

    .sx-faq > summary { list-style: none; }
    .sx-faq > summary::-webkit-details-marker { display: none; }
    .sx-faq__icon { position: relative; width: 1.75rem; height: 1.75rem; border-radius: 999px; border: 1.5px solid #cbd5e1; flex-shrink: 0; transition: transform .25s ease, background-color .2s ease; }
    .sx-faq__icon::before, .sx-faq__icon::after { content: ""; position: absolute; left: 50%; top: 50%; width: .7rem; height: 2px; background: #0f172a; transform: translate(-50%, -50%); }
    .sx-faq__icon::after { transform: translate(-50%, -50%) rotate(90deg); transition: opacity .2s ease; }
    .sx-faq[open] .sx-faq__icon { transform: rotate(180deg); background: #0b1b2b; border-color: #0b1b2b; }
    .sx-faq[open] .sx-faq__icon::before { background: #fff; }
    .sx-faq[open] .sx-faq__icon::after { opacity: 0; }
</style>

<script>
(function () {
    // Hero video: desktop only, motion allowed, no data saver; poster until playing.
    var wrap = document.getElementById('sxHeroMedia');
    if (wrap) {
        var v = wrap.querySelector('video'), p = wrap.querySelector('.sx-hero__poster');
        var mm = window.matchMedia || function () { return { matches: false }; };
        if (mm('(min-width: 768px)').matches && !mm('(prefers-reduced-motion: reduce)').matches && !(navigator.connection && navigator.connection.saveData)) {
            v.addEventListener('playing', function () { p.classList.add('is-hidden'); }, { once: true });
            v.src = v.getAttribute('data-src');
            var pr = v.play(); if (pr && pr.catch) pr.catch(function () {});
        }
    }
    // Rail arrows
    document.querySelectorAll('[data-sx-rail-prev],[data-sx-rail-next]').forEach(function (b) {
        b.addEventListener('click', function () {
            var key = b.getAttribute('data-sx-rail-prev') || b.getAttribute('data-sx-rail-next');
            var rail = document.querySelector('[data-sx-rail="' + key + '"]'); if (!rail) return;
            var card = rail.firstElementChild; var step = card ? card.getBoundingClientRect().width + 16 : 280;
            rail.scrollBy({ left: b.hasAttribute('data-sx-rail-prev') ? -step * 2 : step * 2, behavior: 'smooth' });
        });
    });
})();
</script>
@endsection
