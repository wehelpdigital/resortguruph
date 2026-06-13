@extends('layouts.public')

@section('title') {{ $post->meta_title ?: $post->title }} @endsection
@section('meta_description') {{ $post->meta_description ?: $post->excerpt }} @endsection
@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@section('content')
@php
    // Reading time estimate based on word count of the rendered
    // content (blocks or content_html). Average reader = 220wpm.
    $contentSource = strlen(trim($renderedBlocks ?? '')) > 0 ? $renderedBlocks : ($post->content_html ?? '');
    $wordCount = str_word_count(strip_tags($contentSource));
    $readingMinutes = max(1, (int) ceil($wordCount / 220));
@endphp
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 page-blog-post">
    <nav class="text-sm text-slate-500 mb-8 flex items-center gap-1">
        <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">Home</a>
        <span class="text-slate-300 mx-1">/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-blue-600 transition-colors">Blog</a>
    </nav>

    {{-- Title — bigger, tighter leading, slight negative letter-spacing for editorial weight --}}
    <h1 class="text-4xl md:text-5xl lg:text-[3.25rem] font-extrabold text-slate-900 mb-4 leading-[1.1] tracking-[-0.015em]">{{ $post->title }}</h1>
    @if(!empty($post->subtitle))
        <p class="text-lg md:text-xl text-slate-600 mb-6 leading-relaxed font-serif italic" style="overflow: visible; white-space: normal;">{{ $post->subtitle }}</p>
    @endif

    {{-- Article meta row: reading time + (optional) rating chip --}}
    <div class="flex items-center gap-4 flex-wrap text-sm text-slate-500 mb-8">
        <span class="inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>
            {{ $readingMinutes }} min read
        </span>
        @if(!empty($avgRating))
            <span class="text-slate-300">·</span>
            <span class="inline-flex items-center gap-2">
                <span class="rg-stars">
                    @for($i = 1; $i <= 5; $i++)<span class="{{ $i <= round($avgRating) ? 'text-amber-400' : 'text-slate-200' }}">★</span>@endfor
                </span>
                <strong class="text-slate-700">{{ $avgRating }}</strong>
                <span class="text-slate-500">from {{ $ratingCount }} {{ $ratingCount === 1 ? 'rating' : 'ratings' }}</span>
            </span>
        @endif
    </div>

    @if($post->cover_path)
        <div class="relative aspect-[16/9] rounded-2xl bg-slate-200 mb-10 overflow-hidden shadow-md">
            <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}" loading="eager" class="w-full h-full object-cover">
            {{-- Subtle bottom-edge gradient gives the photo cinematic depth without darkening the photo content itself --}}
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-900/15 to-transparent pointer-events-none"></div>
        </div>
    @endif

    {{-- TL;DR + WWWW summary cards: editable in the mother system, render only
         when populated so unedited posts degrade gracefully. --}}
    @include('partials.summary-blocks', ['tldr' => $post->tldr, 'wwww' => $post->wwww_json])

    {{-- Social share row, top of article --}}
    @include('partials.social-share', ['url' => url()->current(), 'title' => $post->title])

    @php $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0; @endphp
    {{-- Modern prose: lg sizing for comfortable reading, slightly heavier
         h2/h3 styling, and a drop-cap on the first paragraph for that
         editorial-magazine feel. --}}
    <div class="prose prose-lg prose-slate max-w-none rg-blog-prose">
        @if($hasBlocks)
            {!! $renderedBlocks !!}
        @else
            {!! $post->content_html !!}
        @endif
    </div>

    {{-- Share again at the bottom for readers who finished the post --}}
    @include('partials.social-share', ['url' => url()->current(), 'title' => $post->title])

    {{-- Author byline at bottom of post, mirrors keyword page pattern. Uses
         rg_author_id (Filipino travel-writer persona) if set, else hidden. --}}
    @isset($author)
        @if($author)
            <section class="mt-12 pt-8 border-t border-slate-200">
                <div class="flex items-start gap-4 flex-wrap">
                    <img src="{{ $author->avatarUrl() }}" alt="{{ $author->name }}" class="w-16 h-16 rounded-full ring-2 ring-slate-100 bg-slate-50 object-cover">
                    <div class="flex-1 min-w-0 text-sm">
                        <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-0.5">Written by</div>
                        <div class="text-slate-900 font-bold text-base">
                            {{ $author->name }}
                            @if($author->role)<span class="font-normal text-slate-500"> · {{ $author->role }}</span>@endif
                        </div>
                        @if($author->bio)
                            <p class="text-slate-600 mt-2 max-w-2xl leading-relaxed">{{ $author->bio }}</p>
                        @endif
                        <div class="text-slate-400 text-xs mt-2">
                            @if($author->home_base)Based in {{ $author->home_base }}@endif
                            {{-- "Updated <date>" hidden per editorial direction
                                 so evergreen posts don't look stale. --}}
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endisset

    {{-- Comments section: reads from rg_blog_comments (status=approved). Form
         below is member-only; guests see a sign-in CTA instead. --}}
    @php
        $comments = isset($comments) ? $comments : collect();
    @endphp
    <section class="mt-14 pt-10 border-t border-slate-200">
        <div class="flex items-baseline justify-between flex-wrap gap-2 mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Comments</h2>
            <div class="text-sm text-slate-500">{{ $comments->count() }} {{ $comments->count() === 1 ? 'comment' : 'comments' }}</div>
        </div>

        @if($comments->isEmpty())
            <div class="text-center py-8 text-slate-500 border border-dashed border-slate-200 rounded-lg">
                No comments yet. Be the first to share your take.
            </div>
        @else
            <div class="space-y-5">
                @foreach($comments as $c)
                    <article class="flex gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <img src="{{ $c->avatarUrl() }}" alt="{{ $c->commenter_name }}" class="w-10 h-10 rounded-full bg-slate-100 ring-1 ring-slate-200 shrink-0" loading="lazy">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 flex-wrap">
                                <span class="font-semibold text-slate-900">{{ $c->commenter_name }}</span>
                                @if(!empty($c->rating))
                                    <span class="rg-stars" aria-label="{{ $c->rating }} of 5">
                                        @for($i = 1; $i <= 5; $i++)<span class="{{ $i <= $c->rating ? 'text-amber-400' : 'text-slate-300' }}">★</span>@endfor
                                    </span>
                                @endif
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($c->created_at)->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 leading-relaxed mt-1">{{ $c->comment_text }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <div class="mt-8 pt-6 border-t border-slate-100">
            @auth('owner')
                <form method="POST" action="{{ route('blog.comments.store', $post->slug) }}" class="space-y-3">
                    @csrf
                    @if($errors->any())
                        <div class="text-sm text-rose-600">{{ $errors->first() }}</div>
                    @endif
                    @if(session('comment_status'))
                        <div class="text-sm text-emerald-700">{{ session('comment_status') }}</div>
                    @endif
                    <div class="text-sm text-slate-600">
                        Signed in as <strong>{{ Auth::guard('owner')->user()->name }}</strong>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-medium text-slate-700">Your rating:</span>
                        <span class="rg-star-input">
                            @foreach([5,4,3,2,1] as $n)
                                <input type="radio" name="rating" id="rg-rate-{{ $n }}" value="{{ $n }}" @if($n === 5) checked @endif>
                                <label for="rg-rate-{{ $n }}" title="{{ $n }} star{{ $n === 1 ? '' : 's' }}">★</label>
                            @endforeach
                        </span>
                        <span class="text-xs text-slate-400">(optional)</span>
                    </div>

                    <textarea name="comment_text" rows="4" required class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Share your experience or ask a question..."></textarea>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-500">Comments are reviewed before they go live.</p>
                        <button type="submit" class="px-5 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Post comment</button>
                    </div>
                </form>
            @else
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-6 text-center">
                    <p class="text-slate-700 mb-3">Sign in as a member to leave a comment.</p>
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('login') }}" class="px-5 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Sign in</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 rounded-md border border-slate-300 text-slate-700 font-semibold hover:bg-white">Create an account</a>
                    </div>
                </div>
            @endauth
        </div>
    </section>
</article>

@if($related->isNotEmpty())
<section class="bg-slate-50 py-14 md:py-16 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 font-bold mb-1">Keep reading</p>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">More from the blog</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($related as $r)
                <a href="{{ route('blog.show', $r->slug) }}" class="block group">
                    <div class="aspect-[16/10] rounded-xl bg-slate-200 mb-4 overflow-hidden">
                        @if($r->cover_path)
                            <img src="{{ asset('storage/' . $r->cover_path) }}" alt="{{ $r->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 group-hover:text-blue-600 mb-1.5 leading-snug">{{ $r->title }}</h3>
                    <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed">{{ $r->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Drop-cap + editorial-prose polish, scoped under .rg-blog-prose so
     other prose blocks elsewhere on the site stay untouched. --}}
@push('head')
<style>
    .rg-blog-prose > *:first-child::first-letter {
        float: left;
        font-size: 3.6rem;
        line-height: 0.95;
        font-weight: 800;
        margin: 0.3rem 0.55rem 0 0;
        color: #0f172a;
        font-family: Georgia, 'Times New Roman', serif;
    }
    .rg-blog-prose h2 {
        font-weight: 800;
        letter-spacing: -0.015em;
        margin-top: 2.2rem;
        margin-bottom: 0.7rem;
    }
    .rg-blog-prose h3 {
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    .rg-blog-prose p {
        line-height: 1.78;
    }
    .rg-blog-prose blockquote {
        font-style: italic;
        border-left-width: 3px;
        border-left-color: #cbd5e1;
        color: #475569;
        font-size: 1.1em;
    }
    .rg-blog-prose img {
        border-radius: 0.75rem;
    }
    /* Disable drop-cap on small screens where it gets awkward */
    @media (max-width: 640px) {
        .rg-blog-prose > *:first-child::first-letter {
            float: none;
            font-size: inherit;
            margin: 0;
            font-family: inherit;
        }
    }
</style>
@endpush
@endsection
