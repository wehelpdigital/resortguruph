@extends('layouts.public')

@section('title', $fiesta->meta_title ?: ($fiesta->name . ' — ' . ($fiesta->city_or_town ?? '') . ' · Resort Guru PH'))
@section('meta')
    <meta name="description" content="{{ $fiesta->meta_description ?: $fiesta->summary }}">
    <link rel="canonical" href="{{ route('fiestas.show', $fiesta->slug) }}">
    @if($fiesta->og_image_path ?: $fiesta->cover_image_path)
        <meta property="og:image" content="{{ url($fiesta->og_image_path ?: $fiesta->coverUrl()) }}">
    @endif
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $fiesta->h1 ?: $fiesta->name }}">
    <meta property="og:description" content="{{ $fiesta->summary }}">
    <meta property="og:url" content="{{ route('fiestas.show', $fiesta->slug) }}">
@endsection

@section('content')
<article class="page-fiesta-{{ $fiesta->slug }} max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-slate-500 mb-5">
        <a href="/" class="hover:text-emerald-700">Home</a>
        <span class="mx-1.5">/</span>
        <a href="{{ route('fiestas.index') }}" class="hover:text-emerald-700">Fiestas</a>
        <span class="mx-1.5">/</span>
        <span class="text-slate-700">{{ $fiesta->name }}</span>
    </nav>

    {{-- Page header --}}
    <header class="mb-8">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-emerald-700 mb-2">
            {{ $fiesta->regionLabel() }}
            @if($fiesta->city_or_town)
                · {{ $fiesta->city_or_town }}@if($fiesta->province), {{ $fiesta->province }}@endif
            @endif
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-tight mb-3">
            {{ $fiesta->h1 ?: $fiesta->name }}
        </h1>
        @if($fiesta->date_label)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 text-amber-900 text-sm font-semibold mb-4">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/>
                </svg>
                {{ $fiesta->date_label }}
            </div>
        @endif
        @if($fiesta->summary)
            <p class="text-lg sm:text-xl text-slate-700 leading-relaxed max-w-3xl">
                {{ $fiesta->summary }}
            </p>
        @endif
    </header>

    {{-- Cover image --}}
    @if($fiesta->cover_image_path)
        <figure class="rounded-2xl overflow-hidden bg-slate-100 mb-10">
            <img src="{{ $fiesta->coverUrl() }}"
                 alt="{{ $fiesta->name }}"
                 class="w-full h-auto block">
        </figure>
    @endif

    {{-- Content blocks --}}
    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">
        @foreach($blocks as $block)
            {!! $renderer->renderBlock($block, ['fiesta' => $fiesta]) !!}
        @endforeach
    </div>

    {{-- JSON-LD --}}
    @php
        $ld = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $fiesta->name,
            'description' => $fiesta->summary,
            'url' => route('fiestas.show', $fiesta->slug),
            'location' => [
                '@type' => 'Place',
                'name' => trim(($fiesta->city_or_town ?? '') . ($fiesta->province ? (', ' . $fiesta->province) : '')) . ', Philippines',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $fiesta->city_or_town,
                    'addressRegion' => $fiesta->province,
                    'addressCountry' => 'PH',
                ],
            ],
        ];
        if ($fiesta->cover_image_path) $ld['image'] = url($fiesta->coverUrl());
    @endphp
    <script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

</article>
@endsection
