@extends('layouts.public')

@section('title', 'Philippine Tribes, Ethnic Groups & Cultures To Meet · Tourist Guide Ph')
@section('meta_description', 'The complete guide to the country\'s ethnolinguistic and indigenous groups. From the lowland Tagalog and Bisaya to the Cordillera highlands, MIMAROPA islands, Visayan IPs, Mindanao Lumad, and the Moro of Sulu.')
@section('canonical', url('/philippine-tribes-ethnic-groups-cultures-to-meet'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-14">

    {{-- Hero --}}
    <header class="mb-10">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-teal-700 mb-3">
            Cultures to Meet
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-6 max-w-4xl">
            Philippine Tribes, Ethnic Groups &amp;
            <span class="text-teal-700">Cultures to Meet</span>
        </h1>
        <div class="space-y-5 text-base sm:text-lg leading-relaxed text-slate-700">
            <p>
                The Philippines has 7,641 islands and around 175 living languages. The lowland Tagalog, Bisaya, and Ilokano cover most of the daily news, but the country is also home to the Cordillera highlands, the Mangyan of Mindoro, the Tagbanwa and Pala\'wan of Palawan, the Ati of Panay, the eighteen Mindanao Lumad, and the ten Moro groups of Sulu and southwest Mindanao. Each one has its own language, its own art and food, and its own version of how the country looks from where they stand.
            </p>
            <p>
                This page is the cultural map, sorted into seven categories so kababayan and visitor alike can find the people first, then plan a trip around them. The Cordillera Igorot built the Banaue rice terraces and keep the bodong peace pact alive in Kalinga. The Mangyan of Mindoro still use one of the country\'s last surviving pre-colonial scripts. The Bohol Eskaya have their own script too. The Tboli of Lake Sebu weave Tnalak from patterns their patron spirit gives them in dreams. The Tausug, Maranao, and Maguindanaon held three sultanates that pre-date the Spanish arrival. Each card opens with a short note on where the group lives, the language they speak, and one cultural feature you can actually see, learn from, or eat with them. Treat the visit with the respect a guest would.
            </p>
        </div>
    </header>

    {{-- Category jump nav (sticky) --}}
    <nav class="sticky top-28 z-10 bg-white/90 backdrop-blur border-y border-slate-200 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3 mb-10">
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mr-1">Jump to</span>
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat['key'] }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700 font-semibold text-slate-700 transition">
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
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ count($cat['items']) }} groups</p>
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
                               class="culture-card culture-theme-{{ $cat['theme'] }} group relative rounded-2xl overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 flex flex-col">

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
                                            <div class="culture-bg-layer culture-bg-layer-{{ $i + 1 }}"
                                                 style="background-image: url('{{ $img }}'); background-size: cover; background-position: center;"></div>
                                        @endforeach
                                    @else
                                        <div class="culture-bg-layer culture-bg-layer-1"></div>
                                        <div class="culture-bg-layer culture-bg-layer-2"></div>
                                        <div class="culture-bg-layer culture-bg-layer-3"></div>
                                    @endif

                                    <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/45 to-transparent pointer-events-none"></div>

                                    @if(!empty($item['where']))
                                        <div class="absolute top-2.5 right-2.5 z-10 inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/95 shadow-sm text-[11px] font-bold text-slate-800">
                                            <svg class="w-3 h-3 text-teal-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                                <circle cx="12" cy="10" r="3"/>
                                            </svg>
                                            {{ \Illuminate\Support\Str::limit($item['where'], 24) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="p-4 bg-white flex-1 flex flex-col">
                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg leading-snug mb-1.5">
                                        {{ $item['name'] }}
                                    </h3>
                                    @if($description)
                                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $description }}</p>
                                    @elseif(!empty($item['note']))
                                        <p class="text-sm text-slate-600 leading-relaxed flex-1">{{ $item['note'] }}</p>
                                    @else
                                        <p class="text-sm text-slate-400 leading-snug italic flex-1">Cultural notes coming soon.</p>
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
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3">Plan a culture-first Philippines trip</h2>
        <p class="text-slate-700 leading-relaxed max-w-3xl mb-5">
            Most cultural visits work best when timed around a real event. Sinulog and Dinagyang put the Cebuano and Ilonggo on display in January. Panagbenga and Adivay highlight the Ibaloi and the Cordillera in February and November. Kadayawan brings the Davao Lumad to the city in August. T\'nalak Festival in Koronadal shows the Tboli loom in July. Match the trip to the calendar, then add a destination guide for transport.
        </p>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ url('/destinations') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold">
                Destinations by region
            </a>
            <a href="{{ route('fiestas.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-amber-100 hover:bg-amber-200 text-amber-900 font-semibold">
                Fiestas calendar
            </a>
            <a href="{{ route('activities.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-indigo-100 hover:bg-indigo-200 text-indigo-900 font-semibold">
                Activities &amp; adventures
            </a>
            <a href="{{ route('buys.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-full bg-violet-100 hover:bg-violet-200 text-violet-900 font-semibold">
                Heritage pasalubong
            </a>
        </div>
    </div>
</div>

{{-- Card animation styles — same staggered 3-layer fade as the
     other hubs, with 7 themed gradient ramps mapping each category
     to a culturally-fitted palette. --}}
<style>
    details.rg-accordion[open] .rg-accordion-body {
        max-height: none;
    }

    .culture-card {
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.12);
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease-out;
        will-change: transform;
    }
    .culture-card:hover {
        transform: translateY(-4px) scale(1.012);
        box-shadow: 0 14px 32px -8px rgba(15, 23, 42, 0.28);
    }
    .culture-bg-layer {
        position: absolute;
        inset: 0;
        opacity: 0;
        animation: cultureFade 12s infinite ease-in-out;
    }
    .culture-bg-layer-1 { animation-delay: 0s; opacity: 1; }
    .culture-bg-layer-2 { animation-delay: 4s; }
    .culture-bg-layer-3 { animation-delay: 8s; }
    @keyframes cultureFade {
        0%, 28%  { opacity: 1; }
        33%, 95% { opacity: 0; }
        100%     { opacity: 1; }
    }

    .culture-card:nth-child(6n+1) .culture-bg-layer-1 { animation-delay: 0s; }
    .culture-card:nth-child(6n+1) .culture-bg-layer-2 { animation-delay: 4s; }
    .culture-card:nth-child(6n+1) .culture-bg-layer-3 { animation-delay: 8s; }
    .culture-card:nth-child(6n+2) .culture-bg-layer-1 { animation-delay: -2s; }
    .culture-card:nth-child(6n+2) .culture-bg-layer-2 { animation-delay: 2s; }
    .culture-card:nth-child(6n+2) .culture-bg-layer-3 { animation-delay: 6s; }
    .culture-card:nth-child(6n+3) .culture-bg-layer-1 { animation-delay: -4s; }
    .culture-card:nth-child(6n+3) .culture-bg-layer-2 { animation-delay: 0s; }
    .culture-card:nth-child(6n+3) .culture-bg-layer-3 { animation-delay: 4s; }
    .culture-card:nth-child(6n+4) .culture-bg-layer-1 { animation-delay: -6s; }
    .culture-card:nth-child(6n+4) .culture-bg-layer-2 { animation-delay: -2s; }
    .culture-card:nth-child(6n+4) .culture-bg-layer-3 { animation-delay: 2s; }
    .culture-card:nth-child(6n+5) .culture-bg-layer-1 { animation-delay: -8s; }
    .culture-card:nth-child(6n+5) .culture-bg-layer-2 { animation-delay: -4s; }
    .culture-card:nth-child(6n+5) .culture-bg-layer-3 { animation-delay: 0s; }
    .culture-card:nth-child(6n+6) .culture-bg-layer-1 { animation-delay: -10s; }
    .culture-card:nth-child(6n+6) .culture-bg-layer-2 { animation-delay: -6s; }
    .culture-card:nth-child(6n+6) .culture-bg-layer-3 { animation-delay: -2s; }

    /* Per-category palette: each theme picks a tight color family that
       reads as the region or its dominant cultural register. */
    .culture-theme-lowland .culture-bg-layer-1 { background: linear-gradient(135deg, #16a34a 0%, #14532d 100%); }
    .culture-theme-lowland .culture-bg-layer-2 { background: linear-gradient(135deg, #15803d 0%, #166534 100%); }
    .culture-theme-lowland .culture-bg-layer-3 { background: linear-gradient(135deg, #65a30d 0%, #365314 100%); }

    .culture-theme-cordillera .culture-bg-layer-1 { background: linear-gradient(135deg, #475569 0%, #1e293b 100%); }
    .culture-theme-cordillera .culture-bg-layer-2 { background: linear-gradient(135deg, #64748b 0%, #334155 100%); }
    .culture-theme-cordillera .culture-bg-layer-3 { background: linear-gradient(135deg, #525b6b 0%, #0f172a 100%); }

    .culture-theme-caraballo .culture-bg-layer-1 { background: linear-gradient(135deg, #b45309 0%, #7c2d12 100%); }
    .culture-theme-caraballo .culture-bg-layer-2 { background: linear-gradient(135deg, #92400e 0%, #451a03 100%); }
    .culture-theme-caraballo .culture-bg-layer-3 { background: linear-gradient(135deg, #a16207 0%, #422006 100%); }

    .culture-theme-mimaropa .culture-bg-layer-1 { background: linear-gradient(135deg, #06b6d4 0%, #155e75 100%); }
    .culture-theme-mimaropa .culture-bg-layer-2 { background: linear-gradient(135deg, #0891b2 0%, #164e63 100%); }
    .culture-theme-mimaropa .culture-bg-layer-3 { background: linear-gradient(135deg, #14b8a6 0%, #134e4a 100%); }

    .culture-theme-visayas-ip .culture-bg-layer-1 { background: linear-gradient(135deg, #0ea5e9 0%, #0c4a6e 100%); }
    .culture-theme-visayas-ip .culture-bg-layer-2 { background: linear-gradient(135deg, #0284c7 0%, #075985 100%); }
    .culture-theme-visayas-ip .culture-bg-layer-3 { background: linear-gradient(135deg, #38bdf8 0%, #1e3a8a 100%); }

    .culture-theme-lumad .culture-bg-layer-1 { background: linear-gradient(135deg, #78716c 0%, #292524 100%); }
    .culture-theme-lumad .culture-bg-layer-2 { background: linear-gradient(135deg, #57534e 0%, #1c1917 100%); }
    .culture-theme-lumad .culture-bg-layer-3 { background: linear-gradient(135deg, #a16207 0%, #422006 100%); }

    .culture-theme-moro .culture-bg-layer-1 { background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%); }
    .culture-theme-moro .culture-bg-layer-2 { background: linear-gradient(135deg, #6d28d9 0%, #581c87 100%); }
    .culture-theme-moro .culture-bg-layer-3 { background: linear-gradient(135deg, #9333ea 0%, #6b21a8 100%); }

    @media (prefers-reduced-motion: reduce) {
        .culture-bg-layer { animation: none; }
        .culture-bg-layer-1 { opacity: 1; }
        .culture-bg-layer-2, .culture-bg-layer-3 { opacity: 0; }
    }
</style>

@php
    $ld = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Philippine Tribes, Ethnic Groups & Cultures to Meet',
        'description' => 'The complete guide to Philippine ethnolinguistic and indigenous groups, organized by region. Major lowland groups, Cordillera Igorot, Caraballo / Sierra Madre, MIMAROPA islands, Visayan IPs, Mindanao Lumad, and Mindanao Moro.',
        'url' => url('/philippine-tribes-ethnic-groups-cultures-to-meet'),
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
