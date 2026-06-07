@extends('layouts.public')

@section('title', 'Filipino Food, Dishes & What To Eat in the Philippines · Resort Guru PH')
@section('meta_description', 'The complete guide to Filipino food. Popular staples, street food, daredevil exotics, plus regional specialties from Luzon, Visayas, and Mindanao, and traditional sweets. Over 120 dishes by category.')
@section('canonical', url('/filipino-food-dishes-what-to-eat'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-14">

    {{-- Hero --}}
    <header class="mb-10">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-rose-700 mb-3">
            Filipino Food Guide
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-6 max-w-4xl">
            Filipino Food, Dishes &amp;
            <span class="text-rose-700">What To Eat in the Philippines</span>
        </h1>
        <div class="space-y-5 text-base sm:text-lg leading-relaxed text-slate-700">
            <p>
                Walang Pilipino na hindi alam ang adobo, sinigang, o lechon. But the country's food map stretches way past those three. Every province cooks a little differently, every barangay has its own merienda staple, and every roadside vendor knows the right vinegar dip for whatever skewer is on the grill. This page is the full menu, sorted into seven categories so kababayan or foreigner alike can find the dish first, then plan the trip around it.
            </p>
            <p>
                Start with the popular staples if you are new to Filipino food. Drop into the street food section once the sun goes down. The daredevil exotics column is what travel vloggers chase, and the regional specialties from Luzon, Visayas, and Mindanao are where the kitchen gets specific. Finish at the sweets and snacks for merienda. Each card opens with a one-liner on what the dish is, where to eat it best, and what to dip it in. Use this page as a starting point, then sort the itinerary by what you want on the plate.
            </p>
        </div>
    </header>

    {{-- Category jump nav (sticky) --}}
    <nav class="sticky top-28 z-10 bg-white/90 backdrop-blur border-y border-slate-200 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 mb-10">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mr-1">Jump to</span>
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat['key'] }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700 font-semibold text-slate-700 transition">
                    <span aria-hidden="true">{{ $cat['icon'] }}</span>
                    <span>{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Category sections — collapsible <details> blocks. First one
         opens by default. --}}
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
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ count($cat['items']) }} dishes</p>
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
                               class="food-card food-theme-{{ $cat['theme'] }} group relative rounded-2xl overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 flex flex-col">

                                {{-- Media strip with 3 always-rendered layers
                                     (recycled if fewer images exist). --}}
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
                                            <div class="food-bg-layer food-bg-layer-{{ $i + 1 }}"
                                                 style="background-image: url('{{ $img }}'); background-size: cover; background-position: center;"></div>
                                        @endforeach
                                    @else
                                        <div class="food-bg-layer food-bg-layer-1"></div>
                                        <div class="food-bg-layer food-bg-layer-2"></div>
                                        <div class="food-bg-layer food-bg-layer-3"></div>
                                    @endif

                                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/45 to-transparent pointer-events-none"></div>

                                    {{-- Decorative rating badge. The
                                         number is hand-set by feel
                                         per dish in FoodsController::
                                         ratings() — it's not from a
                                         review platform, this is a
                                         directory page. --}}
                                    @if(!empty($item['rating']))
                                        <div class="absolute top-2.5 right-2.5 z-10 inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/95 shadow-sm text-[11px] font-bold text-slate-800">
                                            <svg class="w-3 h-3 text-amber-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                            {{ number_format($item['rating'], 1) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Body: white area below the image. Dish
                                     name + description + (optional) note. --}}
                                <div class="p-4 bg-white flex-1 flex flex-col">
                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg leading-snug mb-1.5">
                                        {{ $item['name'] }}
                                    </h3>
                                    @if($description)
                                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $description }}</p>
                                    @elseif(!empty($item['note']))
                                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $item['note'] }}</p>
                                    @else
                                        <p class="text-sm text-slate-400 leading-snug italic flex-1">Local notes coming soon.</p>
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
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3">Planning the trip around the food?</h2>
        <p class="text-slate-700 leading-relaxed max-w-3xl mb-5">
            Filipino food is the country's strongest argument for visiting more than one province. Pinakbet tastes different in Ilocos. Lechon in Cebu hits a different note. Adobo is a different dish in every household. Use this page as the menu, then check the destination guides for the area, the restaurant directory for where to eat it, and time your trip so a regional fiesta lands on your dates.
        </p>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ url('/destinations') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Destinations by region
            </a>
            <a href="{{ url('/food-trip') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold">
                Restaurants by city
            </a>
            <a href="{{ route('activities.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-indigo-100 hover:bg-indigo-200 text-indigo-900 font-semibold">
                Activities &amp; adventures
            </a>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Travel blog
            </a>
        </div>
    </div>
</div>

{{-- Card animation styles. Same pattern as the activities hub:
     three layers cross-fade on a 12s loop, staggered per card via
     nth-child so adjacent cards never crossfade in sync. Each food
     category gets its own three-color gradient ramp for the image
     fallback case. --}}
<style>
    /* The layout's rg-accordion CSS caps max-height at 1500px when
       open. The Sweets section alone has 25 dishes which goes well
       past that. Lift the cap so all rows are visible. */
    details.rg-accordion[open] .rg-accordion-body {
        max-height: none;
    }

    .food-card {
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.12);
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease-out;
        will-change: transform;
    }
    .food-card:hover {
        transform: translateY(-4px) scale(1.012);
        box-shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.28);
    }
    .food-bg-layer {
        position: absolute;
        inset: 0;
        opacity: 0;
        animation: foodFade 12s infinite ease-in-out;
    }
    .food-bg-layer-1 { animation-delay: 0s; opacity: 1; }
    .food-bg-layer-2 { animation-delay: 4s; }
    .food-bg-layer-3 { animation-delay: 8s; }
    @keyframes foodFade {
        0%, 28%  { opacity: 1; }
        33%, 95% { opacity: 0; }
        100%     { opacity: 1; }
    }

    /* Per-card stagger — shift each card's 12s loop by a different
       offset using nth-child(6n+K). */
    .food-card:nth-child(6n+1) .food-bg-layer-1 { animation-delay: 0s; }
    .food-card:nth-child(6n+1) .food-bg-layer-2 { animation-delay: 4s; }
    .food-card:nth-child(6n+1) .food-bg-layer-3 { animation-delay: 8s; }
    .food-card:nth-child(6n+2) .food-bg-layer-1 { animation-delay: -2s; }
    .food-card:nth-child(6n+2) .food-bg-layer-2 { animation-delay: 2s; }
    .food-card:nth-child(6n+2) .food-bg-layer-3 { animation-delay: 6s; }
    .food-card:nth-child(6n+3) .food-bg-layer-1 { animation-delay: -4s; }
    .food-card:nth-child(6n+3) .food-bg-layer-2 { animation-delay: 0s; }
    .food-card:nth-child(6n+3) .food-bg-layer-3 { animation-delay: 4s; }
    .food-card:nth-child(6n+4) .food-bg-layer-1 { animation-delay: -6s; }
    .food-card:nth-child(6n+4) .food-bg-layer-2 { animation-delay: -2s; }
    .food-card:nth-child(6n+4) .food-bg-layer-3 { animation-delay: 2s; }
    .food-card:nth-child(6n+5) .food-bg-layer-1 { animation-delay: -8s; }
    .food-card:nth-child(6n+5) .food-bg-layer-2 { animation-delay: -4s; }
    .food-card:nth-child(6n+5) .food-bg-layer-3 { animation-delay: 0s; }
    .food-card:nth-child(6n+6) .food-bg-layer-1 { animation-delay: -10s; }
    .food-card:nth-child(6n+6) .food-bg-layer-2 { animation-delay: -6s; }
    .food-card:nth-child(6n+6) .food-bg-layer-3 { animation-delay: -2s; }

    /* Per-category gradient ramps for the no-image fallback. Each
       theme keeps a tight color family so the card reads as one
       scene shifting, not three different colors. */
    .food-theme-staples .food-bg-layer-1 { background: linear-gradient(135deg, #f59e0b 0%, #78350f 100%); }
    .food-theme-staples .food-bg-layer-2 { background: linear-gradient(135deg, #ea580c 0%, #7c2d12 100%); }
    .food-theme-staples .food-bg-layer-3 { background: linear-gradient(135deg, #d97706 0%, #92400e 100%); }

    .food-theme-street .food-bg-layer-1 { background: linear-gradient(135deg, #ef4444 0%, #7f1d1d 100%); }
    .food-theme-street .food-bg-layer-2 { background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); }
    .food-theme-street .food-bg-layer-3 { background: linear-gradient(135deg, #f97316 0%, #9a3412 100%); }

    .food-theme-exotic .food-bg-layer-1 { background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%); }
    .food-theme-exotic .food-bg-layer-2 { background: linear-gradient(135deg, #a855f7 0%, #581c87 100%); }
    .food-theme-exotic .food-bg-layer-3 { background: linear-gradient(135deg, #6366f1 0%, #312e81 100%); }

    .food-theme-luzon .food-bg-layer-1 { background: linear-gradient(135deg, #16a34a 0%, #14532d 100%); }
    .food-theme-luzon .food-bg-layer-2 { background: linear-gradient(135deg, #65a30d 0%, #365314 100%); }
    .food-theme-luzon .food-bg-layer-3 { background: linear-gradient(135deg, #059669 0%, #064e3b 100%); }

    .food-theme-visayas .food-bg-layer-1 { background: linear-gradient(135deg, #06b6d4 0%, #155e75 100%); }
    .food-theme-visayas .food-bg-layer-2 { background: linear-gradient(135deg, #0ea5e9 0%, #0c4a6e 100%); }
    .food-theme-visayas .food-bg-layer-3 { background: linear-gradient(135deg, #14b8a6 0%, #134e4a 100%); }

    .food-theme-mindanao .food-bg-layer-1 { background: linear-gradient(135deg, #be123c 0%, #4c0519 100%); }
    .food-theme-mindanao .food-bg-layer-2 { background: linear-gradient(135deg, #e11d48 0%, #881337 100%); }
    .food-theme-mindanao .food-bg-layer-3 { background: linear-gradient(135deg, #db2777 0%, #831843 100%); }

    .food-theme-sweets .food-bg-layer-1 { background: linear-gradient(135deg, #ec4899 0%, #831843 100%); }
    .food-theme-sweets .food-bg-layer-2 { background: linear-gradient(135deg, #f472b6 0%, #9d174d 100%); }
    .food-theme-sweets .food-bg-layer-3 { background: linear-gradient(135deg, #fb7185 0%, #be123c 100%); }

    @media (prefers-reduced-motion: reduce) {
        .food-bg-layer { animation: none; }
        .food-bg-layer-1 { opacity: 1; }
        .food-bg-layer-2, .food-bg-layer-3 { opacity: 0; }
    }
</style>

{{-- JSON-LD ItemList — each category is one item. --}}
@php
    $ld = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Filipino Food, Dishes & What To Eat in the Philippines',
        'description' => 'The complete guide to Filipino food: popular staples, street food, exotic dishes, regional specialties from Luzon, Visayas, and Mindanao, and traditional sweets.',
        'url' => url('/filipino-food-dishes-what-to-eat'),
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
