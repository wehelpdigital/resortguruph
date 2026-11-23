@extends('layouts.public')

@section('title') {{ $post->meta_title ?: $post->title }} @endsection
@section('meta_description') {{ $post->meta_description ?: $post->excerpt }} @endsection
@section('jsonld') {!! $jsonld ?? '' !!} @endsection
@if($post->cover_path)
    @section('og_image'){{ asset('storage/' . ltrim($post->cover_path, '/')) }}@endsection
@endif

@section('content')
@php
    // Reading time estimate based on word count of the rendered
    // content (blocks or content_html). Average reader = 220wpm.
    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
    $contentSource = $hasBlocks ? $renderedBlocks : ($post->content_html ?? '');
    $wordCount = str_word_count(strip_tags($contentSource));
    $readingMinutes = max(1, (int) ceil($wordCount / 220));
    $tags = array_values(array_filter(array_map('trim', preg_split('/[,;]/', (string) $post->tags))));
    $published = $post->published_at ? \Carbon\Carbon::parse($post->published_at) : null;
    $comments = isset($comments) ? $comments : collect();
@endphp

@include('partials.page-hero', [
    'slides' => $post->cover_path ? [['src' => '/storage/' . ltrim($post->cover_path, '/')]] : [],
    'size' => 'md',
    'crumbs' => [['Home', url('/')], ['Blog', route('blog.index')], [\Illuminate\Support\Str::limit($post->title, 48), null]],
    'kicker' => $tags ? ucwords($tags[0]) : 'Travel story',
    'title' => $post->title,
    'lede' => $post->subtitle ?: $post->excerpt,
    'chips' => [
        ['icon' => 'user', 'text' => $author->name ?? ''],
        ['icon' => 'clock', 'text' => $readingMinutes . ' min read'],
        ['icon' => 'calendar', 'text' => $published ? $published->format('M j, Y') : ''],
        ['icon' => 'star', 'sun' => true, 'text' => !empty($avgRating) ? $avgRating . ' from ' . $ratingCount . ' ' . ($ratingCount === 1 ? 'rating' : 'ratings') : ''],
    ],
])

<nav class="pk-jump pk-jump--auto" data-pk-toc-box aria-label="Jump to a section"><div class="pk-jump__row max-w-7xl mx-auto" data-pk-toc></div></nav>

<div class="pk-wrap pt-10 pb-16 lg:pt-14">
    <div class="pk-grid pk-grid--narrow">
        <article class="pk-main pk-guide page-blog-post" data-pk-main>
            {{-- TL;DR + WWWW summary cards: editable in the mother system, render only
                 when populated so unedited posts degrade gracefully. --}}
            @include('partials.summary-blocks', ['tldr' => $post->tldr, 'wwww' => $post->wwww_json])

            <div class="prose prose-lg prose-slate max-w-none rg-blog-prose">
                @if($hasBlocks)
                    {!! \App\Support\PageKit::balance($renderedBlocks) !!}
                @else
                    {!! \App\Support\PageKit::balance(\App\Support\BlogHtml::transform($post->content_html, $post->cover_path ? '/storage/' . ltrim($post->cover_path, '/') : '')) !!}
                @endif
            </div>

            @if($tags)
                <div class="mt-10 flex flex-wrap gap-2" data-pk-notoc>
                    @foreach(array_slice($tags, 0, 8) as $t)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">#{{ \Illuminate\Support\Str::slug($t, '') }}</span>
                    @endforeach
                </div>
            @endif

            <div class="xl:hidden">
                @include('partials.social-share', ['url' => url()->current(), 'title' => $post->title])
            </div>

            {{-- Author byline (rg_author_id persona), hidden when not set. --}}
            @if(!empty($author))
                <section class="mt-10 pk-card pk-card--sand sm:p-6 flex items-start gap-4" data-pk-notoc>
                    <img src="{{ $author->avatarUrl() }}" alt="{{ $author->name }}" class="w-16 h-16 rounded-2xl ring-4 ring-white bg-white object-cover shrink-0">
                    <div class="flex-1 min-w-0 text-sm">
                        <p class="text-[.7rem] font-extrabold tracking-[.18em] uppercase text-coral-600">Written by</p>
                        <p class="pk-card__title text-ink-900 mt-1">{{ $author->name }}@if($author->role)<span class="font-normal text-slate-500 text-sm"> · {{ $author->role }}</span>@endif</p>
                        @if($author->bio)
                            <p class="text-slate-600 mt-2 leading-relaxed">{{ $author->bio }}</p>
                        @endif
                        @if($author->home_base)
                            <p class="text-slate-500 text-xs mt-2">Based in {{ $author->home_base }}</p>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Comments: rg_blog_comments (status=approved). Member-only form. --}}
            <section class="mt-14" id="comments">
                <div class="flex items-end justify-between flex-wrap gap-2 mb-6">
                    <h2 class="pk-h2 !text-2xl sm:!text-3xl" data-toc="Comments">Comments</h2>
                    <p class="text-sm text-slate-500">{{ $comments->count() }} {{ $comments->count() === 1 ? 'comment' : 'comments' }}</p>
                </div>

                @if($comments->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($comments as $c)
                            <article class="flex gap-4 p-5 rounded-2xl bg-white border border-slate-200">
                                <img src="{{ $c->avatarUrl() }}" alt="{{ $c->commenter_name }}" class="w-11 h-11 rounded-full bg-slate-100 ring-1 ring-slate-200 shrink-0" loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-baseline gap-2 flex-wrap">
                                        <span class="font-semibold text-ink-900">{{ $c->commenter_name }}</span>
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

                <div class="{{ $comments->isNotEmpty() ? 'mt-6' : '' }}">
                    @auth('owner')
                        <form method="POST" action="{{ route('blog.comments.store', $post->slug) }}" class="pk-card sm:p-6 space-y-3">
                            @csrf
                            @if($errors->any())
                                <div class="text-sm text-rose-600">{{ $errors->first() }}</div>
                            @endif
                            @if(session('comment_status'))
                                <div class="text-sm text-emerald-700">{{ session('comment_status') }}</div>
                            @endif
                            <div class="text-sm text-slate-600">Signed in as <strong>{{ Auth::guard('owner')->user()->name }}</strong></div>
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
                            <textarea name="comment_text" rows="4" required class="pk-field" placeholder="Share your experience or ask a question..."></textarea>
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <p class="text-xs text-slate-500">Comments are reviewed before they go live.</p>
                                <button type="submit" class="rg-btn rg-btn--coral">Post comment</button>
                            </div>
                        </form>
                    @else
                        <div class="pk-card pk-card--sand sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1">
                                <p class="pk-card__title text-ink-900">{{ $comments->isEmpty() ? 'Start the conversation' : 'Join the conversation' }}</p>
                                <p class="text-sm text-slate-600 mt-1">Members can comment and rate this guide. Comments are reviewed before they go live.</p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <a href="{{ route('login') }}" class="rg-btn rg-btn--ocean">Sign in</a>
                                <a href="{{ route('register') }}" class="rg-btn rg-btn--outline">Join free</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </section>
        </article>

        @include('partials.page-aside', ['shareTitle' => $post->title, 'shareUrl' => url()->current()])
    </div>
</div>

@if($related->isNotEmpty())
<section class="bg-sand-100 py-16 md:py-20">
    <div class="pk-wrap">
        <div class="flex items-end justify-between gap-4 mb-8 flex-wrap">
            <div>
                <p class="pk-eyebrow">Keep reading</p>
                <h2 class="pk-h2">More travel stories</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="rg-btn rg-btn--outline">All articles</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($related as $r)
                <a href="{{ route('blog.show', $r->slug) }}" class="group block">
                    <span class="block aspect-[16/10] rounded-[1.25rem] overflow-hidden bg-slate-200">
                        @if($r->cover_path)
                            <img src="{{ asset('storage/' . $r->cover_path) }}" alt="{{ $r->title }}" loading="lazy" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                        @endif
                    </span>
                    @if($r->published_at)<span class="block mt-4 text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($r->published_at)->format('M j, Y') }}</span>@endif
                    <span class="block mt-1.5 font-display font-extrabold text-lg text-ink-900 leading-snug group-hover:text-brand-700">{{ $r->title }}</span>
                    <span class="block mt-1.5 text-sm text-slate-600 line-clamp-2 leading-relaxed">{{ $r->excerpt }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('head')
<style>
    .rg-blog-prose { font-size: 1.1rem; }
    .rg-blog-prose > *:first-child { margin-top: 0; }
    .rg-blog-prose > p:first-child::first-letter, .rg-blog-prose > .bx-intro > p:first-child::first-letter { float: left; font-size: 3.9rem; line-height: .9; font-weight: 800; margin: .35rem .6rem 0 0; color: var(--rg-coral, #ef5b3a); font-family: "Plus Jakarta Sans", Inter, sans-serif; }
    .rg-blog-prose h2 { font-weight: 800; letter-spacing: -0.02em; margin-top: 2.4rem; margin-bottom: .7rem; color: #0b1b2b; }
    .rg-blog-prose h3 { font-weight: 700; letter-spacing: -0.01em; color: #0b1b2b; }
    .rg-blog-prose p { line-height: 1.8; }
    .rg-blog-prose a { color: #0f5788; text-decoration-color: rgba(19, 102, 156, .35); text-underline-offset: 3px; }
    .rg-blog-prose ul > li::marker { color: var(--rg-coral, #ef5b3a); }
    .rg-blog-prose blockquote { font-style: normal; border-left: 4px solid var(--rg-coral, #ef5b3a); background: #fbf6ee; border-radius: 0 1rem 1rem 0; padding: .9rem 1.25rem; color: #0b1b2b; }
    .rg-blog-prose img { border-radius: 1.25rem; }
    /* ---------- post body re-layout (App\Support\BlogHtml) ---------- */
    .bx-intro > p:first-child { font-size: 1.16rem; color: #1e293b; }
    .bx-sec { margin: 2.9rem 0; }
    .pk-guide .rg-blog-prose h2.bx-sec__title { display: flex; align-items: center; gap: .9rem; margin: 0 0 1rem !important; }
    .bx-ico { flex: none; width: 2.8rem; height: 2.8rem; border-radius: .95rem; display: inline-flex; align-items: center; justify-content: center; background: #eef6fc; color: #0f5788; box-shadow: inset 0 0 0 1px #d4e7f5; }
    .bx-ico svg { width: 1.35rem; height: 1.35rem; margin: 0 !important; }
    .bx-sec--card { border-radius: 1.5rem; padding: 1.5rem 1.35rem 1.25rem; border: 1px solid #f0e4cf; background: linear-gradient(160deg, #fbf6ee, #fffdf9); }
    @media (min-width: 640px) { .bx-sec--card { padding: 2rem 2.15rem 1.6rem; } }
    .bx-sec--card .bx-ico { background: #fff; color: #d9472a; box-shadow: inset 0 0 0 1px #f0e4cf, 0 8px 18px -12px rgba(217, 71, 42, .5); }
    .bx-sec--t1 { border-color: #d3e8f6; background: linear-gradient(160deg, #eef7fd, #fbfdff); }
    .bx-sec--t1 .bx-ico { color: #0f5788; box-shadow: inset 0 0 0 1px #d3e8f6, 0 8px 18px -12px rgba(15, 87, 136, .5); }
    .bx-sec--t2 { border-color: #cdeedd; background: linear-gradient(160deg, #effaf5, #fbfefc); }
    .bx-sec--t2 .bx-ico { color: #0f7a55; box-shadow: inset 0 0 0 1px #cdeedd; }
    .bx-sec--card > :last-child, .bx-day__body > :last-child { margin-bottom: 0 !important; }

    .bx-days { margin: 2.9rem 0; }
    .bx-day { position: relative; display: grid; grid-template-columns: 2.8rem minmax(0, 1fr); gap: .85rem; padding-bottom: 2.4rem; }
    .bx-day::before { content: ""; position: absolute; left: 1.4rem; top: 3.4rem; bottom: .6rem; width: 2px; transform: translateX(-1px); background: repeating-linear-gradient(180deg, #f2c3b0 0 7px, transparent 7px 13px); }
    .bx-day:last-child { padding-bottom: 0; }
    .bx-day:last-child::before { display: none; }
    .bx-day__node { width: 2.8rem; height: 2.8rem; border-radius: .9rem; background: #0b1b2b; color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1; box-shadow: 0 14px 28px -16px rgba(11, 27, 43, .65); }
    .bx-day__node span { font-size: .56rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #ffc74a; }
    .bx-colon { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
    .pk-guide .bx-day__node strong { color: #fff !important; }
    .bx-day__node strong { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 1.3rem; font-weight: 800; margin-top: .2rem; color: #fff; white-space: nowrap; }
    @media (min-width: 640px) {
        .bx-day { grid-template-columns: 4.4rem minmax(0, 1fr); gap: 1.6rem; }
        .bx-day::before { left: 2.2rem; top: 5.1rem; }
        .bx-day__node { width: 4.4rem; height: 4.4rem; border-radius: 1.3rem; }
        .bx-day__node strong { font-size: 1.75rem; }
    }
    .bx-day--hi .bx-day__node { background: linear-gradient(140deg, #ef5b3a, #f5a524); }
    .bx-day--hi .bx-day__node span { color: #fff; }
    .bx-day__body { min-width: 0; padding-top: .15rem; }
    .bx-day--hi .bx-day__body { border-radius: 1.35rem; padding: 1.25rem 1.3rem 1.2rem; background: linear-gradient(160deg, #fbf6ee, #fffdf9); border: 1px solid #f0e4cf; }
    @media (min-width: 640px) { .bx-day--hi .bx-day__body { padding: 1.6rem 1.75rem 1.4rem; } }
    .pk-guide .rg-blog-prose h2.bx-day__title { margin: 0 0 .8rem !important; font-size: clamp(1.3rem, 2.2vw, 1.65rem) !important; line-height: 1.2 !important; }
    .bx-day__tag { display: block; font-family: Inter, sans-serif; font-size: .72rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #d9472a; margin-bottom: .4rem; }

    .rg-blog-prose figure.rg-figure { margin: 1.9rem 0; }
    .rg-blog-prose figure.rg-figure img { display: block; width: 100%; aspect-ratio: 16 / 10; object-fit: cover; margin: 0; border-radius: 1.25rem; box-shadow: 0 26px 50px -36px rgba(11, 27, 43, .6); }
    .rg-blog-prose figure.rg-figure figcaption { margin-top: .7rem; text-align: left; font-size: .84rem; line-height: 1.55; color: #64748b; padding-left: .85rem; border-left: 3px solid #ffc74a; }

    .bx-sec--stays { margin-top: 3.5rem; padding-top: 2.5rem; border-top: 1px solid #e3e9f0; }
    .bx-stays { display: grid; gap: .85rem; margin: 1.5rem 0; }
    .bx-stay { display: flex; border-radius: 1.2rem; overflow: hidden; border: 1px solid #e3e9f0; background: #fff; text-decoration: none !important; transition: box-shadow .3s ease, transform .3s ease; }
    .bx-stay:hover { box-shadow: 0 22px 44px -28px rgba(11, 27, 43, .45); transform: translateY(-2px); }
    .bx-stay__img { position: relative; display: block; flex: none; width: 7.5rem; min-height: 6.5rem; overflow: hidden; background: linear-gradient(135deg, #e0f0fa, #fdf3e3); }
    .bx-stay__img img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; margin: 0 !important; border-radius: 0 !important; transition: transform .8s cubic-bezier(.22, 1, .36, 1); }
    .bx-stay:hover .bx-stay__img img { transform: scale(1.05); }
    .bx-stay__img.gh-ph { align-items: center; justify-content: center; padding: 0; }
    .bx-stay__body { display: flex; flex-direction: column; justify-content: center; gap: .25rem; padding: .85rem 1rem; min-width: 0; }
    .bx-stay__kicker { font-size: .64rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: #94a3b8; }
    .bx-stay__title { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1rem; line-height: 1.3; color: #0b1b2b; }
    .bx-stay__go { display: inline-flex; align-items: center; gap: .3rem; margin-top: .2rem; font-size: .8rem; font-weight: 700; color: #d9432a; }
    .bx-stay__go svg { width: .9rem; height: .9rem; }
    @media (min-width: 640px) {
        .bx-stays { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
        .bx-stay { flex-direction: column; }
        .bx-stay__img { width: auto; min-height: 0; aspect-ratio: 4 / 3; }
        .bx-stay__body { justify-content: flex-start; padding: 1rem 1.1rem 1.1rem; }
    }
    @media (max-width: 640px) { .rg-blog-prose > p:first-child::first-letter, .rg-blog-prose > .bx-intro > p:first-child::first-letter { float: none; font-size: inherit; margin: 0; color: inherit; font-family: inherit; } }
</style>
@endpush
@endsection
