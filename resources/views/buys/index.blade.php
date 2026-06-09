@extends('layouts.public')

@section('title', 'Philippine Souvenirs, Pasalubong & What To Buy · Tourist Guide Ph')
@section('meta_description', 'The complete pasalubong guide. Heritage salts, regional textiles, artisanal crafts, and packaged sweets from every province. 46 unique Filipino finds worth bringing home.')
@section('canonical', url('/philippine-souvenirs-pasalubong-what-to-buy'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-14">

    {{-- Hero --}}
    <header class="mb-10">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-violet-700 mb-3">
            Pasalubong Guide
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-6 max-w-4xl">
            Philippine Souvenirs, Pasalubong &amp;
            <span class="text-violet-700">What To Buy in the Philippines</span>
        </h1>
        <div class="space-y-5 text-base sm:text-lg leading-relaxed text-slate-700">
            <p>
                For travelers heading home from the Philippines, the pasalubong list is the last to-do before the flight. The country runs on regional specialties. Every province has at least one thing it does better than anywhere else. Bohol's asin tibuok is salt aged in clay over a wood fire. Guimaras mangoes carry the country's only geographical indication status. Vigan's burnay jars are still spun on a wooden wheel by potters who learned from their lolas. South Cotabato's T'nalak cloth is woven by T'boli dreamweavers who say their patron spirit gives them the patterns at night. Davao runs the country's only world-award-winning bean-to-bar chocolate. Marikina makes the leather shoes. Bacolod stamps muscovado piaya. Sagada burns barako-style arabica at 1,500 meters. Every weave, salt block, and pasalubong tin tells you where the maker came from.
            </p>
            <p>
                This page sorts the country's signature finds into four categories. Heritage salts and specialty agriculture covers what comes off the land, from Camiguin lanzones to Bicol pili nuts to the Sulu civet coffee that takes three days to brew. Traditional textiles and weaves cover the cloth traditions, from Aklan's piña to Iloilo's hablon to the Yakan tennun of Basilan. Heritage crafts and artisanal goods cover the burnay jars, the Capiz shells, the Paete woodcarvings, the Palawan pearls. Iconic regional packaged sweets and savory finds cover the binagol, the calamay, the chicharon, and the Good Shepherd ube jam from Baguio. Each card opens with a short note on the item, where in the country to buy it from the source, and what makes it different from the supermarket version. Tara, sa palengke.
            </p>
        </div>
    </header>

    {{-- Category jump nav (sticky) --}}
    <nav class="sticky top-28 z-10 bg-white/90 backdrop-blur border-y border-slate-200 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 mb-10">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mr-1">Jump to</span>
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat['key'] }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700 font-semibold text-slate-700 transition">
                    <span aria-hidden="true">{{ $cat['icon'] }}</span>
                    <span>{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Category sections — collapsible. First opens by default. --}}
    @foreach($categories as $catIdx => $cat)
        <details
            id="cat-{{ $cat['key'] }}"
            class="rg-accordion mb-4 scroll-mt-32 rounded-2xl border border-slate-200 bg-white overflow-hidden"
            @if($catIdx === 0) open @endif>
            <summary class="flex items-center justify-between gap-4 p-5 sm:p-6 cursor-pointer select-none hover:bg-slate-50 transition">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <span class="text-2xl sm:text-3xl shrink-0" aria-hidden="true">{{ $cat['icon'] }}</span>
                    <div class="min-w-0">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">{{ $cat['label'] }}</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ count($cat['items']) }} finds</p>
                    </div>
                </div>
                <svg class="rg-accordion-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9l6 6 6-6"/>
                </svg>
            </summary>
            <div class="rg-accordion-body">
                <div class="rg-accordion-body-inner px-5 sm:px-6 pb-6">
                    <p class="text-slate-600 leading-relaxed text-[15px] sm:text-base mb-5 max-w-3xl">{{ $cat['intro'] }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                        @foreach($cat['items'] as $item)
                            @php
                                $images = $item['images'] ?? [];
                                $hasImages = !empty($images);
                                $description = $item['description'] ?? null;
                            @endphp
                            <a href="#"
                               class="buy-card buy-theme-{{ $cat['theme'] }} group relative rounded-2xl overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 flex flex-col">

                                {{-- Media strip with 3 always-rendered layers (recycled if fewer images exist). --}}
                                <div class="relative w-full aspect-[16/10] overflow-hidden bg-slate-100">
                                    @if($hasImages)
                                        @php
                                            $imgCount = count($images);
                                            $layerImages = [];
                                            for ($n = 0; $n < 3; $n++) {
                                                $layerImages[] = $images[$n % $imgCount];
                                            }
                                        @endphp
                                        @foreach($layerImages as $i => $img)
                                            <div class="buy-bg-layer buy-bg-layer-{{ $i + 1 }}"
                                                 style="background-image: url('{{ $img }}'); background-size: cover; background-position: center;"></div>
                                        @endforeach
                                    @else
                                        <div class="buy-bg-layer buy-bg-layer-1"></div>
                                        <div class="buy-bg-layer buy-bg-layer-2"></div>
                                        <div class="buy-bg-layer buy-bg-layer-3"></div>
                                    @endif

                                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/45 to-transparent pointer-events-none"></div>

                                    {{-- Province badge (top-right) --}}
                                    @if(!empty($item['where']))
                                        <div class="absolute top-2.5 right-2.5 z-10 inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/95 shadow-sm text-[11px] font-bold text-slate-800">
                                            <svg class="w-3 h-3 text-violet-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            {{ \Illuminate\Support\Str::limit($item['where'], 22) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Body: white area below the image. Name + description + (optional) note. --}}
                                <div class="p-4 bg-white flex-1 flex flex-col">
                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg leading-snug mb-1.5">
                                        {{ $item['name'] }}
                                    </h3>
                                    @if($description)
                                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $description }}</p>
                                    @elseif(!empty($item['note']))
                                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $item['note'] }}</p>
                                    @else
                                        <p class="text-sm text-slate-400 leading-snug italic flex-1">Pasalubong notes coming soon.</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </details>
    @endforeach

    {{-- Footer note + internal-link rail --}}
    <div class="mt-12 pt-10 border-t border-slate-200">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3">Plan the trip around the pasalubong run</h2>
        <p class="text-slate-700 leading-relaxed max-w-3xl mb-5">
            Most of these are best bought from the source. Bohol calamay tastes different from Manila grocery shelves. Sagada arabica roasted yesterday beats any chain coffee. Burnay jars from the kiln in Vigan are half the price of the Manila reseller. Sort the trip around the destination first, then add a pasalubong stop on the way out.
        </p>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ url('/destinations') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Destinations by region
            </a>
            <a href="{{ route('foods.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-rose-100 hover:bg-rose-200 text-rose-900 font-semibold">
                Filipino dishes guide
            </a>
            <a href="{{ url('/food-trip') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold">
                Restaurants by city
            </a>
            <a href="{{ route('activities.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-indigo-100 hover:bg-indigo-200 text-indigo-900 font-semibold">
                Activities &amp; adventures
            </a>
        </div>
    </div>
</div>

{{-- Card animation styles — same staggered 3-layer fade as the
     activities + foods hubs, retuned to violet/heritage palette. --}}
<style>
    details.rg-accordion[open] .rg-accordion-body {
        max-height: none;
    }

    .buy-card {
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.12);
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease-out;
        will-change: transform;
    }
    .buy-card:hover {
        transform: translateY(-4px) scale(1.012);
        box-shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.28);
    }
    .buy-bg-layer {
        position: absolute;
        inset: 0;
        opacity: 0;
        animation: buyFade 12s infinite ease-in-out;
    }
    .buy-bg-layer-1 { animation-delay: 0s; opacity: 1; }
    .buy-bg-layer-2 { animation-delay: 4s; }
    .buy-bg-layer-3 { animation-delay: 8s; }
    @keyframes buyFade {
        0%, 28%  { opacity: 1; }
        33%, 95% { opacity: 0; }
        100%     { opacity: 1; }
    }

    /* Per-card stagger via nth-child(6n+K), same idea as foods/activities */
    .buy-card:nth-child(6n+1) .buy-bg-layer-1 { animation-delay: 0s; }
    .buy-card:nth-child(6n+1) .buy-bg-layer-2 { animation-delay: 4s; }
    .buy-card:nth-child(6n+1) .buy-bg-layer-3 { animation-delay: 8s; }
    .buy-card:nth-child(6n+2) .buy-bg-layer-1 { animation-delay: -2s; }
    .buy-card:nth-child(6n+2) .buy-bg-layer-2 { animation-delay: 2s; }
    .buy-card:nth-child(6n+2) .buy-bg-layer-3 { animation-delay: 6s; }
    .buy-card:nth-child(6n+3) .buy-bg-layer-1 { animation-delay: -4s; }
    .buy-card:nth-child(6n+3) .buy-bg-layer-2 { animation-delay: 0s; }
    .buy-card:nth-child(6n+3) .buy-bg-layer-3 { animation-delay: 4s; }
    .buy-card:nth-child(6n+4) .buy-bg-layer-1 { animation-delay: -6s; }
    .buy-card:nth-child(6n+4) .buy-bg-layer-2 { animation-delay: -2s; }
    .buy-card:nth-child(6n+4) .buy-bg-layer-3 { animation-delay: 2s; }
    .buy-card:nth-child(6n+5) .buy-bg-layer-1 { animation-delay: -8s; }
    .buy-card:nth-child(6n+5) .buy-bg-layer-2 { animation-delay: -4s; }
    .buy-card:nth-child(6n+5) .buy-bg-layer-3 { animation-delay: 0s; }
    .buy-card:nth-child(6n+6) .buy-bg-layer-1 { animation-delay: -10s; }
    .buy-card:nth-child(6n+6) .buy-bg-layer-2 { animation-delay: -6s; }
    .buy-card:nth-child(6n+6) .buy-bg-layer-3 { animation-delay: -2s; }

    /* Per-category gradient ramps (heritage/violet palette). */
    .buy-theme-agriculture .buy-bg-layer-1 { background: linear-gradient(135deg, #65a30d 0%, #365314 100%); }
    .buy-theme-agriculture .buy-bg-layer-2 { background: linear-gradient(135deg, #a16207 0%, #422006 100%); }
    .buy-theme-agriculture .buy-bg-layer-3 { background: linear-gradient(135deg, #ca8a04 0%, #713f12 100%); }

    .buy-theme-textiles .buy-bg-layer-1 { background: linear-gradient(135deg, #c026d3 0%, #581c87 100%); }
    .buy-theme-textiles .buy-bg-layer-2 { background: linear-gradient(135deg, #a855f7 0%, #6b21a8 100%); }
    .buy-theme-textiles .buy-bg-layer-3 { background: linear-gradient(135deg, #db2777 0%, #831843 100%); }

    .buy-theme-crafts .buy-bg-layer-1 { background: linear-gradient(135deg, #b45309 0%, #7c2d12 100%); }
    .buy-theme-crafts .buy-bg-layer-2 { background: linear-gradient(135deg, #92400e 0%, #451a03 100%); }
    .buy-theme-crafts .buy-bg-layer-3 { background: linear-gradient(135deg, #c2410c 0%, #7c2d12 100%); }

    .buy-theme-packaged .buy-bg-layer-1 { background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%); }
    .buy-theme-packaged .buy-bg-layer-2 { background: linear-gradient(135deg, #9333ea 0%, #581c87 100%); }
    .buy-theme-packaged .buy-bg-layer-3 { background: linear-gradient(135deg, #6366f1 0%, #312e81 100%); }

    @media (prefers-reduced-motion: reduce) {
        .buy-bg-layer { animation: none; }
        .buy-bg-layer-1 { opacity: 1; }
        .buy-bg-layer-2, .buy-bg-layer-3 { opacity: 0; }
    }
</style>

@php
    $ld = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Philippine Souvenirs, Pasalubong & What To Buy',
        'description' => 'The complete pasalubong guide. Heritage salts, regional textiles, artisanal crafts, and packaged sweets from every Philippine province.',
        'url' => url('/philippine-souvenirs-pasalubong-what-to-buy'),
        'itemListElement' => [],
    ];
    foreach ($categories as $i => $cat) {
        $ld['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $cat['label'],
            'description' => $cat['intro'],
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
