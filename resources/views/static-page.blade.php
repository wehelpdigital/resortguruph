@extends('layouts.public')

@section('title') {{ $page->meta_title ?: $page->title }} @endsection
@section('meta_description') {{ $page->meta_description }} @endsection

@section('content')
@php
    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
    // When the block stream includes a hero-style block, it brings
    // its own H1 + layout chrome; suppress the generic title H1.
    $bringsOwnHero = $hasBlocks && (
        str_contains($renderedBlocks, 'rg-hub-hero') ||
        str_contains($renderedBlocks, 'rg-home-hero') ||
        str_contains($renderedBlocks, 'rg-uss-search') ||
        str_contains($renderedBlocks, '<h1')
    );
@endphp

@if($hasBlocks)
    <article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 page-{{ $page->slug }}">
        @if(!$bringsOwnHero)
            <header class="mb-10">
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900">{{ $page->title }}</h1>
            </header>
        @endif
        {!! $renderedBlocks !!}
    </article>
@else
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-6">{{ $page->title }}</h1>
        <div class="prose prose-slate max-w-none">{!! $page->content_html !!}</div>
    </article>
@endif
@endsection
