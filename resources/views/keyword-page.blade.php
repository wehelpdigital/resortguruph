@extends('layouts.public')

@section('title') {{ $page->meta_title ?: $page->title }} @endsection
@section('meta_description') {{ $page->meta_description }} @endsection
@section('meta_keywords') {{ $page->meta_keywords }} @endsection
@section('canonical') {{ $page->canonical_url ?: url($page->slug) }} @endsection
@if($page->robots) <meta name="robots" content="{{ $page->robots }}"> @endif

{{-- Live Editor chrome (only when a valid HMAC-signed _lt token was
     presented by the mother super-admin's Live Editor view). Loads
     SortableJS + the rg-live-edit assets and exposes a small
     window.__rgLiveEdit config object that the iframe-side script
     uses to identify the page + emit postMessage events to the
     parent admin shell. --}}
@if(!empty($liveEdit))
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/rg-live-edit.css') }}?v=1">
        <script defer src="/vendor/sortable/Sortable.min.js"></script>
        <script>
            window.__rgLiveEdit = {
                pageId: {{ (int) $page->id }},
                slug: {!! json_encode($page->slug) !!},
                ownerType: 'seo_page'
            };
        </script>
        <script defer src="{{ asset('js/rg-live-edit.js') }}?v=2"></script>
    @endpush
@endif
@if($page->og_image_path)
    @php $ogUrl = preg_match('#^https?://#i', $page->og_image_path) ? $page->og_image_path : asset('storage/' . ltrim($page->og_image_path, '/')); @endphp
    @section('og_image'){{ $ogUrl }}@endsection
@endif

@section('jsonld') {!! $jsonld ?? '' !!} @endsection


@section('content')
@php
    // The stored h1 column packs the eyebrow + main heading together,
    // separated by " ~~ ". Older rows without the sentinel fall back to
    // showing just the H1 (eyebrow defaults to the capitalized keyword).
    $stored = $page->h1 ?: $page->title;
    if (str_contains($stored, ' ~~ ')) {
        [$eyebrowText, $h1Display] = explode(' ~~ ', $stored, 2);
    } else {
        $eyebrowText = ucwords($keyword->phrase);
        $h1Display = $stored;
    }

    // Smart title case: capitalizes each word but keeps short
    // prepositions/articles lowercase ("Mall of Asia", not "Mall Of Asia"),
    // and uppercases common Philippine acronyms ("BGC", "MOA", "QC", etc.).
    $properTitle = function ($s) {
        $small = ['of','the','in','at','on','and','a','an','to','for','by','from','with'];
        $words = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($words as $i => $w) {
            if ($w === '') continue;
            $words[$i] = ($i === 0 || !in_array($w, $small, true)) ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8') : $w;
        }
        $result = implode(' ', $words);
        $acronyms = [
            'Bgc' => 'BGC', 'Moa' => 'MOA', 'Qc' => 'QC', 'Cdo' => 'CDO',
            'Sm' => 'SM', 'Atc' => 'ATC', 'Bf' => 'BF', 'Up' => 'UP',
            'Ust' => 'UST', 'Edsa' => 'EDSA', 'Naia' => 'NAIA', 'Ncr' => 'NCR',
            'Uptc' => 'UPTC', 'Pitx' => 'PITX',
        ];
        return preg_replace_callback('/\b(' . implode('|', array_keys($acronyms)) . ')\b/', fn($m) => $acronyms[$m[1]], $result);
    };

    $isFood = $keyword->category === 'food';
    $areaForCta = null;
    if ($isFood) {
        $stripped = preg_replace(
            '/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/i',
            '', $keyword->phrase
        );
        if (preg_match('/(?:restaurant|to\s+eat)\s+(?:in|at|near)\s+(.+)$/i', $stripped, $m)) {
            $areaForCta = trim(preg_replace('/\s+(philippines|with\s+view)$/i', '', $m[1]));
        } elseif (preg_match('/^where\s+to\s+eat\s+(.+)$/i', $stripped, $m)) {
            $areaForCta = trim($m[1]);
        }
        $areaForCta = $areaForCta ? $properTitle($areaForCta) : null;
    }
    $areaForHeader = $areaForCta ?? ($cluster['name'] ?? $properTitle(preg_replace('/^(resort|hotel|airbnb|beach resort) in /i', '', $keyword->phrase)));
    $placeName = $cluster['name'] ?? $properTitle($keyword->phrase);

    // ---- hero ----
    $crumbs = [['Home', url('/')]];
    if ($isFood) {
        $crumbs[] = ['Food Trip', url('/food-trip')];
    } else {
        $crumbs[] = ['Destinations', route('destinations.index')];
        if ($cluster) $crumbs[] = [$cluster['name'], route('destinations.cluster', $keyword->cluster_tag)];
    }
    $crumbs[] = [$properTitle($keyword->phrase), null];

    // When the eyebrow just repeats the H1, show where the place is instead.
    $kicker = mb_strtolower(trim($eyebrowText)) === mb_strtolower(trim($h1Display))
        ? ($isFood ? 'Food guide' . ($areaForCta ? ' · ' . $areaForCta : '') : 'Travel guide' . ($cluster ? ' · ' . $cluster['name'] : ''))
        : $eyebrowText;
    $ownReviews = isset($reviews) ? $reviews->where('keyword_id', $keyword->id)->count() : 0;
    $chips = [
        ['icon' => 'star', 'sun' => true, 'text' => !empty($hero['rating']) ? number_format($hero['rating'], 1) . ' editor rating' : ''],
        ['icon' => 'pin', 'text' => $isFood ? ($areaForCta ?: '') : ($cluster['name'] ?? '')],
        ['icon' => 'chat', 'text' => $ownReviews ? $ownReviews . ' traveler review' . ($ownReviews === 1 ? '' : 's') : ''],
        ['icon' => 'calendar', 'text' => $page->updated_at ? 'Updated ' . $page->updated_at->format('M Y') : ''],
    ];
    $hasPicks = $isFood ? ($restaurantListings ?? collect())->isNotEmpty() : $listings->count() > 0;
    $actions = [
        $hasPicks
            ? ['label' => $isFood ? 'See where to eat' : 'See where to stay', 'url' => '#top-picks', 'style' => 'coral']
            : ['label' => 'Read the guide', 'url' => '#guide', 'style' => 'coral'],
        ['label' => 'Plan my trip', 'url' => url('/create-your-adventure'), 'style' => 'glass'],
    ];
    $meta = fn ($k, $l) => !empty($liveEdit) ? 'data-rg-page-meta="' . $k . '" data-rg-meta-label="' . $l . '"' : '';

    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
@endphp

@include('partials.page-hero', [
    'slides' => $hero['slides'] ?? [],
    'crumbs' => $crumbs,
    'kicker' => $kicker,
    'kickerAttrs' => $meta('h1_eyebrow', 'Eyebrow (above H1)'),
    'title' => $h1Display,
    'titleAttrs' => $meta('h1', 'H1 Heading'),
    'lede' => $hero['lede'] ?? '',
    'chips' => $chips,
    'actions' => $actions,
])

<nav class="pk-jump pk-jump--auto" data-pk-toc-box aria-label="Jump to a section"><div class="pk-jump__row max-w-7xl mx-auto" data-pk-toc></div></nav>

<div class="pk-wrap pt-8 pb-14 lg:pt-12" id="guide">
    <div class="pk-grid">
        <article class="pk-main pk-guide page-{{ $page->slug }}" data-pk-main>
            @if($hasBlocks)
                {{-- Strip listing_slot AND listing_block markers: the listings
                     band (we_recommend_band) already renders them. --}}
                @php
                    $cleanedBlocks = preg_replace(
                        ['#<!--LISTING_SLOT_START-->.*?<!--LISTING_SLOT_END-->#s', '#<!--LISTING_BLOCK_START-->.*?<!--LISTING_BLOCK_END-->#s'],
                        ['', ''],
                        $renderedBlocks
                    );
                @endphp
                {!! \App\Support\PageKit::balance($cleanedBlocks) !!}
            @else
                {{-- Legacy fallback (intro_html + body_html). --}}
                @if($page->intro_html)
                    <div class="pk-prose max-w-none mb-10">{!! $page->intro_html !!}</div>
                @endif
                @if($page->body_html)
                    <div class="pk-prose max-w-none">{!! $page->body_html !!}</div>
                @endif
            @endif

            @if(!empty($faqs) && !$hasBlocks)
                <section class="my-12">
                    <h2 class="pk-h2 mb-6">Frequently asked questions</h2>
                    <div class="space-y-3">
                        @foreach($faqs as $i => $f)
                            <details class="border border-slate-200 rounded-2xl group bg-white" {{ $i === 0 ? 'open' : '' }}>
                                <summary class="cursor-pointer p-5 font-semibold text-slate-900 flex items-center justify-between gap-4">
                                    <span>{{ $f['question'] ?? '' }}</span>
                                    <svg class="w-5 h-5 shrink-0 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </summary>
                                <div class="px-5 pb-5 text-slate-700 prose max-w-none">{!! nl2br(e($f['answer'] ?? '')) !!}</div>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Member review submission (display lives in the reviews_band block). --}}
            @isset($reviews)
                <section class="mt-12" data-pk-notoc>
                    @if(session('review_status'))
                        <div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">{{ session('review_status') }}</div>
                    @endif
                    <div class="pk-card pk-card--sand sm:p-7 flex flex-col sm:flex-row gap-5 sm:items-center">
                        <div class="w-12 h-12 rounded-2xl bg-white text-sun-500 flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            @auth('owner')
                                <h3 class="pk-card__title text-ink-900">Leave your review of {{ $placeName }}</h3>
                                <p class="text-sm text-slate-600 mt-1 mb-4">Signed in as <strong>{{ Auth::guard('owner')->user()->name }}</strong>. Reviews are moderated before they go live.</p>
                                <form method="POST" action="{{ route('keyword.review.store') }}" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="keyword_id" value="{{ $keyword->id }}">
                                    <input type="hidden" name="redirect_to" value="{{ url($page->slug) }}">
                                    <div class="flex items-center gap-2">
                                        <label class="pk-label mb-0" for="rg-review-rating">Rating</label>
                                        <select id="rg-review-rating" name="rating" class="rounded-xl border-slate-300 text-sm">
                                            @for($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }})</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <textarea name="comment_text" disabled hidden></textarea>
                                    <textarea name="review_text" rows="4" required minlength="20" maxlength="2000" class="pk-field" placeholder="Share what you actually experienced. Real specifics help future travelers more than generic praise."></textarea>
                                    @error('review_text')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror
                                    <button type="submit" class="rg-btn rg-btn--coral">Submit review</button>
                                </form>
                            @else
                                <h3 class="pk-card__title text-ink-900">Been to {{ $placeName }}?</h3>
                                <p class="text-sm text-slate-600 mt-1">Tell other travelers what it was really like. Members can leave a review, and every review is checked before it goes live.</p>
                            @endauth
                        </div>
                        @guest('owner')
                            <div class="flex flex-wrap gap-2 shrink-0">
                                <a href="{{ route('login') }}" class="rg-btn rg-btn--ocean">Sign in to review</a>
                                <a href="{{ route('register') }}" class="rg-btn rg-btn--outline">Join free</a>
                            </div>
                        @endguest
                    </div>
                </section>
            @endisset
        </article>

        @include('partials.page-aside', [
            'shareTitle' => $h1Display,
            'shareUrl' => url($page->slug),
            'business' => $isFood
                ? ['title' => 'Run a restaurant' . ($areaForCta ? ' in ' . $areaForCta : '') . '?', 'text' => 'Get featured at the top of this guide, in front of people deciding where to eat.', 'url' => route('register'), 'label' => 'List your restaurant']
                : ['title' => 'Own a place in ' . $areaForHeader . '?', 'text' => 'Get your resort or hotel featured at the top of this guide, in front of travelers planning a trip here.', 'url' => route('register'), 'label' => 'List your property'],
        ])
    </div>
</div>

@if($related->isNotEmpty() && $cluster)
    <section class="bg-sand-100 py-14 md:py-20">
        <div class="pk-wrap">
            <div class="flex items-end justify-between gap-4 mb-8 flex-wrap">
                <div>
                    <p class="pk-eyebrow">Keep exploring</p>
                    <h2 class="pk-h2">More of {{ $cluster['name'] }}</h2>
                </div>
                <a href="{{ route('destinations.cluster', $keyword->cluster_tag) }}" class="rg-btn rg-btn--outline">All {{ $cluster['name'] }} guides</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($related as $r)
                    <a href="{{ url($r->slug) }}" class="group flex items-center gap-3 rounded-2xl bg-white border border-sand-200 p-4 hover:border-brand-300 hover:shadow-lg hover:-translate-y-0.5 transition">
                        <span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-ink-900 text-sm leading-snug group-hover:text-brand-700">{{ $properTitle($r->phrase) }}</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Travel guide</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="py-14 md:py-20 {{ ($related->isNotEmpty() && $cluster) ? 'bg-sand-100 !pt-0' : '' }}">
    <div class="pk-wrap">
        <div class="relative overflow-hidden rounded-[1.75rem] bg-ink-900 text-white p-7 sm:p-10 lg:p-12 isolate">
            <div class="absolute -right-24 -top-24 w-[26rem] h-[26rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(239,91,58,.4), transparent 65%)"></div>
            <div class="grid lg:grid-cols-[1fr_auto] gap-6 items-center">
                <div>
                    <p class="pk-eyebrow !text-sun-300">For business owners</p>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $isFood ? 'Run a restaurant' . ($areaForCta ? ' in ' . $areaForCta : '') . '?' : 'Run a resort or hotel in ' . $areaForHeader . '?' }}</h2>
                    <p class="mt-2 text-white/75 max-w-2xl">Travelers read this guide while they decide. Put your place at the top with photos, reviews and direct contact links.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('register') }}" class="rg-btn rg-btn--coral rg-btn--lg">List your {{ $isFood ? 'restaurant' : 'property' }}</a>
                    <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--glass rg-btn--lg">How it works</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
