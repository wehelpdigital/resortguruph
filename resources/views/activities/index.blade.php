@extends('layouts.public')

@section('title', 'Philippine Tourist Activities, Adventures & What To Do · Tourist Guide Ph')
@section('meta_description', 'The complete guide to tourist activities and adventures in the Philippines. Water sports, land treks, air rides, casinos, heritage tours, wellness retreats, and year-round fiestas — all in one place.')
@section('canonical', url('/philippine-tourist-activities-adventures-what-to-do'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-14">

    {{-- Hero — title is capped at a comfortable reading width. The
         intro splits into a 400px image column on the left and the
         prose on the right at lg+; on smaller screens the image stacks
         on top and crops to a landscape ratio so it doesn't dominate
         the phone viewport. --}}
    <header class="mb-10">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-emerald-700 mb-3">
            Philippines Activity Guide
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-6 max-w-4xl">
            Tourist Activities, Adventures &amp; What To Do
            <span class="text-emerald-700">in the Philippines</span>
        </h1>
        <div class="grid grid-cols-1 lg:grid-cols-[400px_1fr] gap-8 items-start">
            <figure class="m-0">
                <img src="{{ asset('storage/rg-media/activities/hero.jpg') }}"
                     alt="Tourist with a surfboard on the beach in Siargao, Philippines"
                     class="w-full aspect-[4/3] lg:aspect-[3/4] object-cover rounded-2xl shadow-md"
                     loading="lazy">
                <figcaption class="text-xs text-slate-400 mt-2">
                    Photo by Ian Panelo on Pexels &middot; Island-hopping bangka, El Nido, Palawan
                </figcaption>
            </figure>
            <div class="space-y-5 text-base sm:text-lg leading-relaxed text-slate-700">
                <p>
                    For DIY travelers heading into the Philippines, kababayan or foreigner alike, the real question is not what to do, but where to start. This is a country of 7,641 islands, three active volcanoes you can climb in a single weekend, a coastline longer than the continental United States, and a fiesta calendar so full that every barangay has its own patron-saint feast somewhere on it. Pick a province on a map, and there are already five worthwhile things to do this Saturday. So this page is your starting point. Six categories of Philippine tourist activities and adventures, each one a kind of neighborhood, with the cards as doorways. Tara, sa Coron for the scuba diving, sa Siargao for the surf breaks, sa Pampanga for the dawn balloon flights, sa Ilocos for the sand dunes, sa Marinduque for the Moriones, sa Lucban for the Pahiyas.
                </p>
                <p>
                    Each card opens into a kwento of where the activity sits in the country, what season works for it, and how to ride in by jeepney, tricycle, or shuttle van once you land in town. The country rewards specificity. A weekend planned around scuba diving puts you on completely different islands than a weekend planned around heritage walking. A trip shaped by fiesta tourism moves you to a different province every month. Knowing what you actually came for lets you plan around it instead of fighting the geography. Use this page that way. Sort first by what moves you, then cross-read the destination guides and food trip pages to ground the trip in where it lives best, then check the fiestas calendar so your dates line up with something worth catching while you are there.
                </p>
            </div>
        </div>
    </header>

    {{-- Category jump nav (sticky) --}}
    <nav class="sticky top-16 z-10 bg-white/90 backdrop-blur border-y border-slate-200 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 mb-10">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mr-1">Jump to</span>
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat['key'] }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700 font-semibold text-slate-700 transition">
                    <span aria-hidden="true">{{ $cat['icon'] }}</span>
                    <span>{{ $cat['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Category sections — each is its own collapsible accordion so
         the page does not dump all 92 cards at once. First category
         opens by default so the visitor sees the pattern; the rest
         start closed and reveal their card grid on tap. The layout
         layer's rg-accordion JS handles the smooth open/close. --}}
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
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ count($cat['items']) }} activities</p>
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
                        $isFestival = !empty($item['is_festival_card']) && !empty($fiestaCovers);
                        $href = $isFestival ? $fiestasUrl : ($item['href'] ?? '#');
                        $external = $isFestival ? false : (!empty($item['href']) && str_starts_with($item['href'], 'http'));
                        // Pick the image stream: festival card uses real fiesta covers,
                        // researched activities use their downloaded images, everything
                        // else falls back to the themed gradient layers.
                        $images = $isFestival ? $fiestaCovers : ($item['images'] ?? []);
                        $hasImages = !empty($images);
                        $description = $item['description'] ?? null;
                    @endphp
                    <a href="{{ $href }}"
                       @if($external) target="_blank" rel="noopener" @endif
                       class="activity-card activity-theme-{{ $cat['theme'] }} group relative rounded-2xl overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 flex flex-col">

                        {{-- Media strip: top half of the card. Three image
                             layers crossfade if photos exist; otherwise the
                             gradient backdrop fades for visual interest.
                             We always render exactly 3 layers — when fewer
                             than 3 photos are on disk we recycle them so
                             the 12s keyframe cycle never exposes a gap of
                             grey backdrop between layers. --}}
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
                                    <div class="activity-bg-layer activity-bg-layer-{{ $i + 1 }}"
                                         style="background-image: url('{{ $img }}'); background-size: cover; background-position: center;"></div>
                                @endforeach
                            @else
                                <div class="activity-bg-layer activity-bg-layer-1"></div>
                                <div class="activity-bg-layer activity-bg-layer-2"></div>
                                <div class="activity-bg-layer activity-bg-layer-3"></div>
                            @endif

                            {{-- Soft bottom scrim only over the media area --}}
                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/45 to-transparent pointer-events-none"></div>

                            @if($isFestival)
                                <div class="absolute top-3 left-3 px-2 py-1 rounded-full bg-white/95 text-[10px] uppercase tracking-wider font-bold text-amber-700 shadow-sm z-10">
                                    {{ \App\Models\RgFiesta::where('is_published', true)->count() }} fiestas
                                </div>
                            @endif
                        </div>

                        {{-- Body: white card area below the image. Title +
                             description + (optional) note. Festival card
                             gets its own outbound link cue. --}}
                        <div class="p-4 bg-white flex-1 flex flex-col">
                            <h3 class="font-bold text-slate-900 text-base sm:text-lg leading-snug mb-1.5">
                                {{ $item['name'] }}
                            </h3>
                            @if($description)
                                <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $description }}</p>
                            @elseif(!empty($item['note']))
                                <p class="text-sm text-slate-500 leading-snug italic flex-1">{{ $item['note'] }}</p>
                            @else
                                <p class="text-sm text-slate-400 leading-snug italic flex-1">Local notes coming soon.</p>
                            @endif
                            @if($isFestival)
                                <div class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-amber-700 group-hover:text-amber-900">
                                    View the fiesta guide
                                    <svg class="w-3 h-3 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                                </div>
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
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3">Planning the trip around an activity?</h2>
        <p class="text-slate-700 leading-relaxed max-w-3xl mb-5">
            Most Philippine adventures cluster in specific provinces. Once you have picked the activity, sort the trip around where it lives best. Browse our destination and food guides for the area, lock in the listing for your stay, and time the visit so a regional fiesta lands on your dates.
        </p>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Destinations by region
            </a>
            <a href="{{ url('/food-trip') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Food Trip
            </a>
            <a href="{{ $fiestasUrl }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold">
                Fiestas calendar
            </a>
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Travel blog
            </a>
        </div>
    </div>
</div>

{{-- Card animation styles. Each theme has its own 3-color gradient
     ramp; the layers cross-fade on a 12-second loop so the page has
     constant gentle motion without ever pulsing distractingly. --}}
<style>
    /* The layout's rg-accordion CSS defaults to max-height: 1500px when
       open, which clips the longer category grids (Water Adventures
       alone is 31 cards in a 3-col grid, well over 3000px tall). Lift
       the cap here so the JS opens the section and the final state lets
       the content size naturally. */
    details.rg-accordion[open] .rg-accordion-body {
        max-height: none;
    }

    .activity-card {
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.12);
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease-out;
        will-change: transform;
    }
    .activity-card:hover {
        transform: translateY(-4px) scale(1.012);
        box-shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.28);
    }
    .activity-bg-layer {
        position: absolute;
        inset: 0;
        opacity: 0;
        animation: activityFade 12s infinite ease-in-out;
    }
    .activity-bg-layer-1 { animation-delay: 0s; opacity: 1; }
    .activity-bg-layer-2 { animation-delay: 4s; }
    .activity-bg-layer-3 { animation-delay: 8s; }
    @keyframes activityFade {
        0%, 28%  { opacity: 1; }
        33%, 95% { opacity: 0; }
        100%     { opacity: 1; }
    }

    /* Per-card stagger: shift the entire 12s cycle by a different
       amount for each of 6 modulo groups so adjacent cards never
       cross-fade in sync. Layer-N inside a card keeps its 4s offset
       from layer-(N-1); the per-card delay only shifts where in the
       cycle that card starts. Negative delays = start the loop mid-
       cycle, which is fine for `animation-iteration-count: infinite`. */
    .activity-card:nth-child(6n+1) .activity-bg-layer-1 { animation-delay: 0s; }
    .activity-card:nth-child(6n+1) .activity-bg-layer-2 { animation-delay: 4s; }
    .activity-card:nth-child(6n+1) .activity-bg-layer-3 { animation-delay: 8s; }
    .activity-card:nth-child(6n+2) .activity-bg-layer-1 { animation-delay: -2s; }
    .activity-card:nth-child(6n+2) .activity-bg-layer-2 { animation-delay: 2s; }
    .activity-card:nth-child(6n+2) .activity-bg-layer-3 { animation-delay: 6s; }
    .activity-card:nth-child(6n+3) .activity-bg-layer-1 { animation-delay: -4s; }
    .activity-card:nth-child(6n+3) .activity-bg-layer-2 { animation-delay: 0s; }
    .activity-card:nth-child(6n+3) .activity-bg-layer-3 { animation-delay: 4s; }
    .activity-card:nth-child(6n+4) .activity-bg-layer-1 { animation-delay: -6s; }
    .activity-card:nth-child(6n+4) .activity-bg-layer-2 { animation-delay: -2s; }
    .activity-card:nth-child(6n+4) .activity-bg-layer-3 { animation-delay: 2s; }
    .activity-card:nth-child(6n+5) .activity-bg-layer-1 { animation-delay: -8s; }
    .activity-card:nth-child(6n+5) .activity-bg-layer-2 { animation-delay: -4s; }
    .activity-card:nth-child(6n+5) .activity-bg-layer-3 { animation-delay: 0s; }
    .activity-card:nth-child(6n+6) .activity-bg-layer-1 { animation-delay: -10s; }
    .activity-card:nth-child(6n+6) .activity-bg-layer-2 { animation-delay: -6s; }
    .activity-card:nth-child(6n+6) .activity-bg-layer-3 { animation-delay: -2s; }

    /* Theme gradients. Layer 1/2/3 cycle through three hues that
       belong to the same family, so the card never reads as a different
       color — it reads as the same scene shifting light. */
    .activity-theme-water .activity-bg-layer-1 { background: linear-gradient(135deg, #0ea5e9 0%, #0c4a6e 100%); }
    .activity-theme-water .activity-bg-layer-2 { background: linear-gradient(135deg, #06b6d4 0%, #134e4a 100%); }
    .activity-theme-water .activity-bg-layer-3 { background: linear-gradient(135deg, #38bdf8 0%, #1e3a8a 100%); }

    .activity-theme-land .activity-bg-layer-1 { background: linear-gradient(135deg, #65a30d 0%, #14532d 100%); }
    .activity-theme-land .activity-bg-layer-2 { background: linear-gradient(135deg, #a16207 0%, #422006 100%); }
    .activity-theme-land .activity-bg-layer-3 { background: linear-gradient(135deg, #16a34a 0%, #1e3a2c 100%); }

    .activity-theme-air .activity-bg-layer-1 { background: linear-gradient(135deg, #fb7185 0%, #be123c 100%); }
    .activity-theme-air .activity-bg-layer-2 { background: linear-gradient(135deg, #fbbf24 0%, #c2410c 100%); }
    .activity-theme-air .activity-bg-layer-3 { background: linear-gradient(135deg, #f472b6 0%, #831843 100%); }

    .activity-theme-entertainment .activity-bg-layer-1 { background: linear-gradient(135deg, #a855f7 0%, #581c87 100%); }
    .activity-theme-entertainment .activity-bg-layer-2 { background: linear-gradient(135deg, #ec4899 0%, #831843 100%); }
    .activity-theme-entertainment .activity-bg-layer-3 { background: linear-gradient(135deg, #6366f1 0%, #312e81 100%); }

    .activity-theme-cultural .activity-bg-layer-1 { background: linear-gradient(135deg, #c2410c 0%, #7c2d12 100%); }
    .activity-theme-cultural .activity-bg-layer-2 { background: linear-gradient(135deg, #b45309 0%, #78350f 100%); }
    .activity-theme-cultural .activity-bg-layer-3 { background: linear-gradient(135deg, #dc2626 0%, #7f1d1d 100%); }

    .activity-theme-leisure .activity-bg-layer-1 { background: linear-gradient(135deg, #14b8a6 0%, #134e4a 100%); }
    .activity-theme-leisure .activity-bg-layer-2 { background: linear-gradient(135deg, #f59e0b 0%, #78350f 100%); }
    .activity-theme-leisure .activity-bg-layer-3 { background: linear-gradient(135deg, #10b981 0%, #064e3b 100%); }

    @media (prefers-reduced-motion: reduce) {
        .activity-bg-layer { animation: none; }
        .activity-bg-layer-1 { opacity: 1; }
        .activity-bg-layer-2, .activity-bg-layer-3 { opacity: 0; }
    }
</style>

{{-- JSON-LD ItemList — each category is an item; the search engine sees
     the page as a structured directory of Philippine tourist activities. --}}
@php
    $ld = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Philippine Tourist Activities, Adventures & What To Do',
        'description' => 'The complete guide to tourist activities and adventures in the Philippines: water sports, land treks, air rides, casinos, heritage tours, wellness retreats, and year-round fiestas.',
        'url' => url('/philippine-tourist-activities-adventures-what-to-do'),
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
