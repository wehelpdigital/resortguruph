@extends('layouts.public')

@section('title') {{ $post->meta_title ?: $post->title }} @endsection
@section('meta_description') {{ $post->meta_description ?: $post->excerpt }} @endsection
@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a>
    </nav>

    <h1 class="text-4xl font-extrabold text-slate-900 mb-3 leading-tight">{{ $post->title }}</h1>
    @if(!empty($post->subtitle))
        <p class="italic text-base text-slate-600 mb-6 leading-relaxed" style="overflow: visible; white-space: normal; text-overflow: clip; max-width: 100%;">{{ $post->subtitle }}</p>
    @else
        <div class="mb-6"></div>
    @endif

    {{-- Aggregate rating chip from approved comments, if any --}}
    @if(!empty($avgRating))
        <div class="flex items-center gap-2 mb-6 text-sm">
            <span class="rg-stars">
                @for($i = 1; $i <= 5; $i++)<span class="{{ $i <= round($avgRating) ? 'text-amber-400' : 'text-slate-200' }}">★</span>@endfor
            </span>
            <span class="text-slate-600"><strong>{{ $avgRating }}</strong>/5 from {{ $ratingCount }} reader {{ $ratingCount === 1 ? 'rating' : 'ratings' }}</span>
        </div>
    @endif

    @if($post->cover_path)
        <div class="aspect-[16/9] rounded-xl bg-slate-200 mb-8 overflow-hidden">
            <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    @endif

    {{-- TL;DR + WWWW summary cards: editable in the mother system, render only
         when populated so unedited posts degrade gracefully. --}}
    @include('partials.summary-blocks', ['tldr' => $post->tldr, 'wwww' => $post->wwww_json])

    {{-- Social share row, top of article --}}
    @include('partials.social-share', ['url' => url()->current(), 'title' => $post->title])

    @php $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0; @endphp
    @if($hasBlocks)
        <div class="prose prose-slate max-w-none">{!! $renderedBlocks !!}</div>
    @else
        <div class="prose prose-slate max-w-none">{!! $post->content_html !!}</div>
    @endif

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
                            @if($author->home_base && $post->updated_at) · @endif
                            @if($post->updated_at)Updated {{ \Carbon\Carbon::parse($post->updated_at)->format('M j, Y') }}@endif
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
<section class="bg-slate-50 py-12 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Keep reading</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach($related as $r)
                <a href="{{ route('blog.show', $r->slug) }}" class="block group">
                    <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 mb-2">{{ $r->title }}</h3>
                    <p class="text-sm text-slate-500 line-clamp-2">{{ $r->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
