@extends('layouts.public')

@php
    $place = trim(($fiesta->city_or_town ?? '') . ($fiesta->province ? (($fiesta->city_or_town ? ', ' : '') . $fiesta->province) : ''));
@endphp
@section('title', $fiesta->meta_title ?: ($fiesta->name . ($fiesta->city_or_town ? ' in ' . $fiesta->city_or_town : '') . ' | Tourist Guide Ph'))
@section('meta_description'){{ $fiesta->meta_description ?: $fiesta->summary }}@endsection
@section('canonical'){{ route('fiestas.show', $fiesta->slug) }}@endsection
@if($fiesta->og_image_path ?: $fiesta->cover_image_path)
    @section('og_image'){{ url($fiesta->og_image_path ? '/storage/' . ltrim($fiesta->og_image_path, '/') : $fiesta->coverUrl()) }}@endsection
@endif

@section('content')
@include('partials.page-hero', [
    'slides' => $fiesta->cover_image_path ? [['src' => $fiesta->coverUrl()]] : [],
    'crumbs' => [['Home', url('/')], ['Festivals', route('fiestas.index')], [$fiesta->name, null]],
    'kicker' => $fiesta->regionLabel() . ($place ? ' · ' . $place : ''),
    'title' => $fiesta->h1 ?: $fiesta->name,
    'lede' => $fiesta->summary,
    'chips' => [
        ['icon' => 'calendar', 'sun' => true, 'text' => $fiesta->date_label ?: $fiesta->monthLabel()],
        ['icon' => 'pin', 'text' => $place],
    ],
    'actions' => [
        ['label' => 'Plan my trip', 'url' => url('/create-your-adventure'), 'style' => 'coral'],
        ['label' => 'All festivals', 'url' => route('fiestas.index'), 'style' => 'glass'],
    ],
])

<nav class="pk-jump pk-jump--auto" data-pk-toc-box aria-label="Jump to a section"><div class="pk-jump__row max-w-7xl mx-auto" data-pk-toc></div></nav>

<div class="pk-wrap pt-10 pb-16 lg:pt-14">
    <div class="pk-grid pk-grid--narrow">
        <article class="pk-main pk-guide page-fiesta-{{ $fiesta->slug }}" data-pk-main>
            <div class="prose prose-slate prose-lg max-w-none text-slate-700 leading-relaxed rg-fiesta-prose">
                @php $fiestaHtml = ''; foreach ($blocks as $block) { $fiestaHtml .= $renderer->renderBlock($block, ['fiesta' => $fiesta]); } @endphp
                {!! \App\Support\PageKit::balance($fiestaHtml) !!}
            </div>
            <div class="xl:hidden">
                @include('partials.social-share', ['url' => route('fiestas.show', $fiesta->slug), 'title' => $fiesta->name])
            </div>
        </article>
        @include('partials.page-aside', ['shareTitle' => $fiesta->name, 'shareUrl' => route('fiestas.show', $fiesta->slug)])
    </div>
</div>

@if(!empty($more) && $more->isNotEmpty())
<section class="relative overflow-hidden bg-ink-900 text-white py-16 md:py-20 isolate">
    <div class="absolute -right-40 -top-40 w-[34rem] h-[34rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(239,91,58,.32), transparent 65%)"></div>
    <div class="pk-wrap">
        <div class="flex items-end justify-between gap-4 mb-8 flex-wrap">
            <div>
                <p class="pk-eyebrow !text-sun-300">Keep celebrating</p>
                <h2 class="pk-h2 !text-white">More festivals to plan around</h2>
            </div>
            <a href="{{ route('fiestas.index') }}" class="rg-btn rg-btn--glass">Festival calendar</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($more as $f)
                <a href="{{ route('fiestas.show', $f->slug) }}" class="group block rounded-[1.25rem] overflow-hidden bg-white/5 border border-white/10 hover:bg-white/10 transition">
                    <span class="block aspect-[4/3] overflow-hidden"><img src="{{ $f->coverUrl() }}" alt="{{ $f->name }}" loading="lazy" class="w-full h-full object-cover transition duration-700 group-hover:scale-105"></span>
                    <span class="block p-4">
                        <span class="block text-[.7rem] font-bold uppercase tracking-wider text-sun-300">{{ $f->monthLabel() }}</span>
                        <span class="block mt-1 font-display font-extrabold leading-snug">{{ preg_replace('/\s*\(.*\)$/', '', $f->name) }}</span>
                        <span class="block mt-1 text-xs text-white/65">{{ trim(($f->city_or_town ?: '') . ($f->province ? ', ' . $f->province : ''), ', ') }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

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

@push('head')
<style>
    .rg-fiesta-prose > *:first-child { margin-top: 0; }
    .rg-fiesta-prose h2 { font-weight: 800; letter-spacing: -0.02em; color: #0b1b2b; }
    .rg-fiesta-prose p { line-height: 1.8; }
</style>
@endpush
@endsection
