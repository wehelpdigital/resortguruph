@extends('layouts.public')

@section('title') {{ ($page->meta_title ?? null) ?: 'Travel Blog: Philippine Itineraries, Tips and Stories | Tourist Guide Ph' }} @endsection
@section('meta_description') {{ ($page->meta_description ?? null) ?: 'Itineraries, travel tips and destination stories from across the Philippines, written by travelers who have actually made the trip.' }} @endsection

@section('content')
@php
    $intro = \App\Support\PageKit::intro('blog-index');
    $first = $posts->onFirstPage() ? $posts->first() : null;
    $rest = $first ? $posts->getCollection()->slice(1) : $posts->getCollection();
    $cover = fn ($p) => $p && $p->cover_path ? '/storage/' . ltrim($p->cover_path, '/') : null;
    $slides = collect($heroPosts ?? [])->map(fn ($p) => ['src' => $cover($p), 'title' => $p->title])->values()->all();
    $totalPosts = $totalPosts ?? $posts->total();
@endphp

@include('partials.page-hero', [
    'slides' => $slides,
    'size' => 'md',
    'crumbs' => [['Home', url('/')], ['Blog', null]],
    'kicker' => $intro['eyebrow'] ?: 'The Blog',
    'title' => $intro['heading'] ?: 'Travel stories from people who went',
    'lede' => $intro['lede'] ?: 'Real routes, honest tips and the local detail you only get from someone who has been there.',
    'chips' => [
        ['icon' => 'book', 'text' => number_format($totalPosts) . ' articles'],
        ['icon' => 'user', 'text' => 'Written by travelers'],
    ],
])

<div class="pk-wrap py-12 md:py-16 page-blog-index">
    @if($first)
        <a href="{{ route('blog.show', $first->slug) }}" class="group grid lg:grid-cols-[1.25fr_1fr] gap-6 lg:gap-10 items-center mb-14 md:mb-20">
            <span class="relative block aspect-[16/10] rounded-[1.5rem] overflow-hidden bg-slate-200">
                @if($cover($first))<img src="{{ $cover($first) }}" alt="{{ $first->title }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">@endif
                <span class="absolute top-4 left-4 rounded-full bg-coral-500 text-white text-xs font-bold px-3 py-1">Latest</span>
            </span>
            <span class="block">
                @if($first->published_at)<span class="block text-sm font-semibold text-slate-500">{{ \Carbon\Carbon::parse($first->published_at)->format('F j, Y') }}</span>@endif
                <span class="block mt-2 font-display font-extrabold text-ink-900 text-2xl sm:text-3xl lg:text-[2.4rem] leading-[1.12] tracking-tight group-hover:text-brand-700 transition">{{ $first->title }}</span>
                @if($first->excerpt)<span class="block mt-4 text-slate-600 text-lg leading-relaxed line-clamp-4">{{ $first->excerpt }}</span>@endif
                <span class="mt-6 rg-btn rg-btn--coral">Read the story <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>
            </span>
        </a>
    @endif

    <div class="flex items-end justify-between gap-4 mb-8 flex-wrap">
        <div>
            <p class="pk-eyebrow">{{ $posts->onFirstPage() ? 'More to read' : 'Page ' . $posts->currentPage() }}</p>
            <h2 class="pk-h2">All articles</h2>
        </div>
        @if($totalPosts)
            <p class="text-sm font-medium text-slate-500">{{ number_format($totalPosts) }} articles</p>
        @endif
    </div>

    @if($posts->count() === 0)
        <div class="rounded-2xl border border-dashed border-slate-200 py-16 text-center text-slate-500">No articles yet. Check back soon.</div>
    @else
        <div class="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($rest as $p)
                <a href="{{ route('blog.show', $p->slug) }}" class="group block">
                    <span class="block aspect-[16/10] rounded-[1.25rem] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                        @if($cover($p))<img src="{{ $cover($p) }}" alt="{{ $p->title }}" loading="lazy" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">@endif
                    </span>
                    @if($p->published_at)<span class="block mt-4 text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($p->published_at)->format('M j, Y') }}</span>@endif
                    <span class="block mt-1.5 font-display font-extrabold text-lg text-ink-900 leading-snug line-clamp-2 group-hover:text-brand-700 transition">{{ $p->title }}</span>
                    @if($p->excerpt)<span class="block mt-1.5 text-sm text-slate-600 leading-relaxed line-clamp-2">{{ $p->excerpt }}</span>@endif
                </a>
            @endforeach
        </div>
        @if($posts->hasPages())
            <div class="mt-14">{{ $posts->links('pagination.theme') }}</div>
        @endif
    @endif
</div>
@endsection
