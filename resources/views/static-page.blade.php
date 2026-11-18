@extends('layouts.public')

@section('title') {{ $page->meta_title ?: $page->title }} @endsection
@section('meta_description') {{ $page->meta_description }} @endsection

@section('content')
@php
    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
    // Home-style streams (home_*/hub_*/dest_* blocks, flagged by the
    // controller) are full-bleed, section-based pages: render them like
    // the homepage. Other pages use the page-kit hero + reading column.
    $fullBleed = $hasBlocks && !empty($homeStyle);
    $pullHero = $fullBleed && \App\Support\PageKit::leadsWithDarkHero($renderedBlocks);
    $updated = $page->updated_at ? \Carbon\Carbon::parse($page->updated_at) : null;
    $isLegal = in_array($page->slug, ['terms', 'privacy'], true);
@endphp

@if($fullBleed)
    @if($pullHero) @section('header_overlay', '1') @endif
    {{-- Full-bleed homepage-style stream: each block spans the viewport or
         centers itself, so render bare (no article gutters) and align the
         max-w-6xl section containers with the max-w-7xl nav, like the home page. --}}
    <style>.page-{{ $page->slug }} .max-w-6xl{max-width:80rem}.page-{{ $page->slug }} section h1,.page-{{ $page->slug }} section h2{text-transform:capitalize}</style>
    <div class="page-{{ $page->slug }} {{ $pullHero ? 'pk-pull pk-pull--flush' : '' }}">
        {!! $renderedBlocks !!}
    </div>
@else
    @include('partials.page-hero', [
        'slides' => !empty($heroImage) ? [['src' => $heroImage]] : [],
        'size' => 'sm',
        'crumbs' => [['Home', url('/')], [$page->title, null]],
        'kicker' => $isLegal ? 'The fine print' : 'Tourist Guide Ph',
        'title' => $page->title,
        'lede' => $page->meta_description,
    ])

    @if($isLegal)
        <nav class="pk-jump pk-jump--auto" data-pk-toc-box aria-label="Jump to a section"><div class="pk-jump__row max-w-7xl mx-auto" data-pk-toc></div></nav>
    @endif

    <div class="pk-wrap pt-10 pb-16 lg:pt-14 page-{{ $page->slug }}">
        <div class="{{ $isLegal ? 'pk-grid pk-grid--narrow' : 'max-w-3xl mx-auto' }}">
            <article class="pk-main pk-prose" data-pk-main>
                {!! \App\Support\PageKit::balance($hasBlocks ? $renderedBlocks : $page->content_html) !!}
            </article>
            @if($isLegal)
                <aside class="pk-aside" aria-label="Page tools">
                    <nav class="pk-toc" data-pk-toc-box aria-label="On this page">
                        <p class="pk-toc__title">On this page</p>
                        <ol data-pk-toc></ol>
                    </nav>
                    <div class="pk-card pk-card--sand">
                        <p class="pk-card__title text-ink-900">Questions about this page?</p>
                        <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">We are happy to explain anything here in plain words.</p>
                        <a href="{{ route('contact') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-bold text-coral-600 hover:text-coral-700">Contact us <span aria-hidden="true">&rarr;</span></a>
                    </div>
                </aside>
            @endif
        </div>
    </div>
@endif
@endsection
