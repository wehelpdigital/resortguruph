@extends('layouts.public')

@section('title') {{ ($page->meta_title ?? null) ?: 'Blog — ' . \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection
@section('meta_description') {{ $page->meta_description ?? 'Travel tips, destination guides, and resort stories.' }} @endsection

@section('content')
@php
    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
@endphp

<div class="page-blog-index">
    @if($hasBlocks)
        {{-- Editorial intro / hero block(s) from the blog-index static page. --}}
        {!! $renderedBlocks !!}
    @else
        <section class="py-14 md:py-20">
            <div class="mx-auto max-w-2xl px-4 text-center">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-brand-600">The Blog</p>
                <h1 class="mb-4 text-4xl font-extrabold text-slate-900 md:text-5xl">Stories &amp; Guides</h1>
                <p class="text-lg text-slate-600">Travel tips, destination guides, and resort stories from across the Philippines.</p>
            </div>
        </section>
    @endif

    {{-- Article cards --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 md:pb-24 lg:px-8">
        <div class="mb-8 flex items-center gap-4 md:mb-10">
            <h2 class="whitespace-nowrap text-2xl font-extrabold text-slate-900 md:text-3xl">All Articles</h2>
            <span class="h-px flex-1 bg-slate-200"></span>
            @if($posts->total())
                <span class="whitespace-nowrap text-sm font-medium text-slate-400">{{ number_format($posts->total()) }} articles</span>
            @endif
        </div>

        @include('blog.partials.cards')
    </section>
</div>
@endsection
