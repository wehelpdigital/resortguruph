@extends('layouts.public')

@section('title', 'Philippine Fiestas & Festivals Guide — Year-Round Calendar by Region · Resort Guru PH')
@section('meta')
    <meta name="description" content="The complete guide to Philippine fiestas and festivals. Sinulog, MassKara, Panagbenga, Kadayawan, Ati-Atihan, Pahiyas, and {{ $totalCount }} more, organized by region. When each one happens, why to go, and what makes it different.">
    <link rel="canonical" href="{{ route('fiestas.index') }}">
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-500 mb-4">
        <a href="/" class="hover:text-emerald-700">Home</a>
        <span class="mx-1.5">/</span>
        <a href="{{ route('activities.index') }}" class="hover:text-emerald-700">Activities</a>
        <span class="mx-1.5">/</span>
        <span class="text-slate-700">Fiestas &amp; Festivals</span>
    </nav>

    {{-- Page header --}}
    <header class="mb-10">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-emerald-700 mb-2">Activities · Cultural &amp; Heritage</div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-tight mb-3">
            Philippine Fiestas &amp; Festivals Guide
        </h1>
        <div class="prose prose-slate text-base sm:text-lg leading-relaxed text-slate-700 mb-2 max-w-3xl">
            <p>
                The Philippines runs on fiestas. Every town has at least one, every region has a dozen, and the calendar is so packed you could chase a different celebration every weekend of the year and still not see them all. This is the complete regional guide: {{ $totalCount }} fiestas across the archipelago, sorted by region so a trip lines up cleanly with whichever one fits your dates.
            </p>
        </div>
    </header>

    {{-- Sticky region table of contents (desktop), inline on mobile --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 mb-10">
        <div class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500 mb-2">Jump to region</div>
        <div class="flex flex-wrap gap-2">
            @foreach(\App\Models\RgFiesta::REGION_LABELS as $key => $label)
                @if(($grouped[$key] ?? collect())->isNotEmpty())
                    <a href="#region-{{ $key }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-white border border-slate-200 text-slate-700 hover:border-emerald-300 hover:text-emerald-700 transition">
                        {{ $label }}
                        <span class="ml-2 text-xs text-slate-400">{{ $grouped[$key]->count() }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Region sections --}}
    @foreach(\App\Models\RgFiesta::REGION_LABELS as $key => $label)
        @php $regionRows = $grouped[$key] ?? collect(); @endphp
        @if($regionRows->isEmpty()) @continue @endif

        <section id="region-{{ $key }}" class="mb-14 scroll-mt-24">
            <div class="flex items-end justify-between gap-3 mb-5 flex-wrap pb-3 border-b border-slate-200">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $label }}</h2>
                <div class="text-sm text-slate-500">{{ $regionRows->count() }} fiesta{{ $regionRows->count() === 1 ? '' : 's' }}</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($regionRows as $fiesta)
                    <a href="{{ route('fiestas.show', $fiesta->slug) }}"
                       class="group block rounded-xl border border-slate-200 bg-white overflow-hidden hover:shadow-lg hover:-translate-y-px transition">

                        {{-- Cover --}}
                        @if($fiesta->cover_image_path)
                            <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                                <img src="{{ $fiesta->coverUrl() }}"
                                     alt="{{ $fiesta->name }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                     loading="lazy">
                                <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-slate-900/60 to-transparent pointer-events-none"></div>
                                @if($fiesta->date_label)
                                    <div class="absolute top-3 left-3 px-2 py-1 rounded-full bg-white/95 text-[10px] uppercase tracking-wider font-bold text-slate-700 shadow-sm">
                                        {{ Str::limit($fiesta->date_label, 32) }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-amber-50 via-rose-50 to-emerald-50">
                                <svg class="w-12 h-12 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 8l9-5 9 5M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8M9 22V12h6v10"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Body --}}
                        <div class="p-4">
                            <div class="text-[10px] uppercase tracking-wider font-bold text-emerald-700 mb-1">
                                {{ $fiesta->city_or_town ?? 'Philippines' }}@if($fiesta->province), {{ $fiesta->province }}@endif
                            </div>
                            <h3 class="font-bold text-slate-900 text-base mb-2 leading-tight group-hover:text-emerald-700 transition">
                                {{ $fiesta->name }}
                                <span class="inline-block transition group-hover:translate-x-0.5">&rarr;</span>
                            </h3>
                            @if($fiesta->summary)
                                <p class="text-sm text-slate-600 leading-snug m-0">
                                    {{ Str::limit($fiesta->summary, 160) }}
                                </p>
                            @endif
                            @if(!$fiesta->cover_image_path && $fiesta->date_label)
                                <div class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100">
                                    {{ $fiesta->date_label }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- Footer note --}}
    <div class="mt-10 pt-8 border-t border-slate-200 text-sm text-slate-500 leading-relaxed">
        Know a fiesta we are missing? The list updates as we add new ones across the country. Big provincial festivals, small barangay feasts, and movable Holy Week celebrations all welcome.
    </div>
</div>
@endsection
