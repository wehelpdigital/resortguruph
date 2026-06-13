@extends('layouts.public')

@section('title') {{ ($page->meta_title ?? null) ?: 'Blog — ' . \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection
@section('meta_description') {{ $page->meta_description ?? 'Travel tips, destination guides, and resort stories.' }} @endsection

@section('content')
@php
    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
@endphp

@if($hasBlocks)
    <article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 page-blog-index">
        {!! $renderedBlocks !!}

        {{-- Post grid sits below the editorial intro --}}
        <section class="py-12 md:py-16">
            <div class="flex items-end justify-between mb-8 flex-wrap gap-3">
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Latest posts</h2>
                @if($posts->total())
                    <p class="text-sm text-slate-500">{{ $posts->total() }} posts</p>
                @endif
            </div>

            @if($posts->count() === 0)
                <p class="text-slate-500 text-center py-12 border border-dashed border-slate-200 rounded-lg">No posts yet.</p>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $p)
                        <article class="group">
                            <a href="{{ route('blog.show', $p->slug) }}" class="block">
                                <div class="aspect-[16/10] rounded-xl bg-slate-200 mb-4 overflow-hidden">
                                    @if($p->cover_path)
                                        <img src="{{ asset('storage/' . $p->cover_path) }}" alt="{{ $p->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @endif
                                </div>
                                <h3 class="font-bold text-lg text-slate-900 group-hover:text-blue-600 mb-1.5 leading-snug">{{ $p->title }}</h3>
                                <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed">{{ $p->excerpt }}</p>
                            </a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-10">{{ $posts->links() }}</div>
            @endif
        </section>
    </article>
@else
    {{-- Legacy fallback if blog-index has no blocks seeded --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-extrabold text-slate-900 mb-3">From the blog</h1>
        <p class="text-slate-600 mb-10">Travel tips, destination guides, and resort stories.</p>

        @if($posts->count() === 0)
            <p class="text-slate-500 text-center py-12">No posts yet.</p>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $p)
                    <article>
                        <a href="{{ route('blog.show', $p->slug) }}" class="block group">
                            <div class="aspect-[16/10] rounded-lg bg-slate-200 mb-3 overflow-hidden">
                                @if($p->cover_path)
                                    <img src="{{ asset('storage/' . $p->cover_path) }}" alt="{{ $p->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                @endif
                            </div>
                            <h2 class="font-bold text-lg text-slate-900 group-hover:text-brand-600 mb-1">{{ $p->title }}</h2>
                            <p class="text-sm text-slate-600 line-clamp-3 mb-2">{{ $p->excerpt }}</p>
                        </a>
                    </article>
                @endforeach
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        @endif
    </div>
@endif
@endsection
