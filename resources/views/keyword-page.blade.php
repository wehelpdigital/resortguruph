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
        <script defer src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            window.__rgLiveEdit = {
                pageId: {{ (int) $page->id }},
                slug: {!! json_encode($page->slug) !!},
                ownerType: 'seo_page'
            };
        </script>
        <script defer src="{{ asset('js/rg-live-edit.js') }}?v=1"></script>
    @endpush
@endif
@if($page->og_image_path)
    @php $ogUrl = preg_match('#^https?://#i', $page->og_image_path) ? $page->og_image_path : asset('storage/' . ltrim($page->og_image_path, '/')); @endphp
    @section('og_image'){{ $ogUrl }}@endsection
@endif

@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@section('content')
<article class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 page-{{ $page->slug }}">
    {{-- Breadcrumb — category-aware:
         food pages: Home / Food Trip / Food Destinations / restaurant in X
         resort pages: Home / Destinations / Cluster / X (original behaviour). --}}
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
        <span class="mx-2">/</span>
        @if($keyword->category === 'food')
            <a href="{{ url('/food-trip') }}" class="hover:text-brand-600">Food Trip</a>
            <span class="mx-2">/</span>
            <a href="{{ url('/food-trip') }}" class="hover:text-brand-600">Food Destinations</a>
        @else
            <a href="{{ url('/destinations') }}" class="hover:text-brand-600">Destinations</a>
            @if($cluster)
                <span class="mx-2">/</span>
                <a href="{{ route('destinations.cluster', $keyword->cluster_tag) }}" class="hover:text-brand-600">{{ $cluster['name'] }}</a>
            @endif
        @endif
        <span class="mx-2">/</span>
        <span class="text-slate-700 capitalize">{{ $keyword->phrase }}</span>
    </nav>

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
    @endphp
    {{-- Eyebrow + H1 read as one flowing sentence: eyebrow is the lede
         ("Looking for a hotel in Cebu?"), H1 completes the thought
         ("Here Are the Honest Picks We Would Make"). normal-case keeps the
         grammar intact instead of the prior all-caps treatment. --}}
    <div class="text-base sm:text-lg text-slate-600 mb-2 font-medium">{{ $eyebrowText }}</div>
    <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-3 leading-tight">{{ $h1Display }}</h1>

    {{-- Italic intro: 1-2 sentences positioning the page below the H1. Pulls
         from rg_seo_pages.subtitle when admin-edited; otherwise the seeder
         populates a default from the destination's voice_intro.
         `white-space: normal; overflow: visible` defends against parent CSS
         that clipped the line on mobile widths (user reported cut-off). --}}
    @if(!empty($page->subtitle ?? null))
        <p class="italic text-base text-slate-600 mb-6 leading-relaxed" style="overflow: visible; white-space: normal; text-overflow: clip; max-width: 100%;">{{ $page->subtitle }}</p>
    @endif

    {{-- Social share row, top of page --}}
    @include('partials.social-share', ['url' => url()->current(), 'title' => $page->title])

    {{-- Pull the location name out of the phrase. For food pages this is
         the actual venue ("Mall of Asia", "BGC", "Tagaytay"); for resort
         pages it falls back to the cluster name. The same value drives
         both the listing offer's CTA ("List your restaurant in X") and
         the "What's in X" article header below. --}}
    @php
        // Smart title case — capitalizes each word but keeps short
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

        $areaForCta = null;
        if ($keyword->category === 'food') {
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
    @endphp

    {{-- Top "We Recommend" band — same partial for both categories. The
         partial detects $keyword->category and switches its heading,
         subhead, empty-state offer, and CTA wording (hotels/resorts vs
         restaurants/eateries/food shops). On food pages it dispatches
         to the restaurant-card grid for the filled state. --}}
    @if($keyword->category === 'food')
        @include('partials.listings-rows', ['listings' => $restaurantListings, 'listingGalleries' => [], 'area' => $areaForCta])
    @else
        @include('partials.listings-rows', ['listings' => $listings, 'listingGalleries' => $listingGalleries ?? []])
    @endif

    {{-- The hero slider AND the "What's in [Area]?" section header were
         previously rendered here from the rg_seo_pages.hero_html column
         and a hardcoded <section> block. Both are now real
         rg_content_blocks rows (hero_slider + section_header) so they
         render through $cleanedBlocks below — single source of truth,
         editable in the admin builder. --}}

    {{-- TL;DR + WWWW summary cards moved here (below the listings + below the
         section H2). Editable in the mother system; render only when populated. --}}
    @include('partials.summary-blocks', ['tldr' => $page->tldr ?? null, 'wwww' => $page->wwww_json ?? null])

    @php $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0; @endphp

    @if($hasBlocks)
        {{-- New block builder output. Strip listing_slot AND listing_block
             markers since the listings band is already rendered above this
             section via partials.listings-rows. The blocks still exist in
             the admin builder so editors can reorder around them; the
             public render just elides the duplicate. --}}
        @php
            $cleanedBlocks = preg_replace(
                ['#<!--LISTING_SLOT_START-->.*?<!--LISTING_SLOT_END-->#s', '#<!--LISTING_BLOCK_START-->.*?<!--LISTING_BLOCK_END-->#s'],
                ['', ''],
                $renderedBlocks
            );
        @endphp
        {!! $cleanedBlocks !!}
    @else
        {{-- Legacy fallback (intro_html + body_html). Listings live in the
             top "We Recommend" wrapper above — rendering them again here
             would duplicate the section. --}}
        @if($page->intro_html)
            <div class="prose prose-slate max-w-none mb-10">{!! $page->intro_html !!}</div>
        @endif

        @if($page->body_html)
            <div class="prose prose-slate max-w-none">{!! $page->body_html !!}</div>
        @endif
    @endif

    {{-- Restaurant Recommendations + Memorable Adventures: lifted OUT of
         the @if($hasBlocks) branch so they render on every resort page
         regardless of whether the page uses block-builder content. --}}
    @if($keyword->category !== 'food' && $restaurantListings->isNotEmpty())
        <section class="my-14 pt-10 border-t border-slate-200">
            <div class="flex items-end justify-between mb-6 flex-wrap gap-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-brand-700 font-bold mb-1">Eat nearby</p>
                    <h2 class="text-2xl font-bold text-slate-900">Restaurant Recommendations</h2>
                    <p class="text-sm text-slate-500 mt-1">Paid placements where your guests will likely want to eat.</p>
                </div>
            </div>
            @include('partials.restaurant-listings', ['listings' => $restaurantListings])
        </section>
    @endif

    @if($keyword->category !== 'food' && $adventureListings->isNotEmpty())
        <section class="my-14 pt-10 border-t border-slate-200">
            <div class="flex items-end justify-between mb-6 flex-wrap gap-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-amber-700 font-bold mb-1">Things to do</p>
                    <h2 class="text-2xl font-bold text-slate-900">Memorable Adventures &amp; Activities</h2>
                    <p class="text-sm text-slate-500 mt-1">Surf schools, ATV trails, island hops, and paintball arenas open in the area.</p>
                </div>
            </div>
            @include('partials.adventure-listings', ['listings' => $adventureListings])
        </section>
    @endif

    @if(!empty($faqs) && !$hasBlocks)
        <section class="my-12">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Frequently asked questions</h2>
            <div class="space-y-3">
                @foreach($faqs as $i => $f)
                    <details class="border border-slate-200 rounded-lg group" {{ $i === 0 ? 'open' : '' }}>
                        <summary class="cursor-pointer p-4 font-semibold text-slate-900 flex items-center justify-between">
                            <span>{{ $f['question'] ?? '' }}</span>
                            <svg class="w-5 h-5 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="px-4 pb-4 text-slate-700 prose max-w-none">{!! nl2br(e($f['answer'] ?? '')) !!}</div>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

    @isset($reviews)
        @if($reviews->isNotEmpty())
            @php
                $avg = round($reviews->avg('rating'), 2);
                $cnt = $reviews->count();
            @endphp
            <section class="my-14">
                <div class="flex items-baseline justify-between flex-wrap gap-2 mb-5">
                    <h2 class="text-2xl font-bold text-slate-900">What travelers are saying</h2>
                    <div class="text-sm text-slate-600 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1">
                            @for($i = 0; $i < floor($avg); $i++)<svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                        </span>
                        <strong>{{ $avg }}</strong> out of 5 · based on {{ $cnt }} {{ $cnt === 1 ? 'review' : 'reviews' }}
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($reviews as $r)
                        <article class="p-5 rounded-xl border border-slate-200 bg-white flex flex-col gap-3 hover:shadow-sm transition">
                            <div class="flex items-start gap-3">
                                <img src="{{ $r->avatarUrl() }}" alt="{{ $r->reviewer_name }}" class="w-10 h-10 rounded-full bg-slate-100 ring-1 ring-slate-200" loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-900 truncate">{{ $r->reviewer_name }}</div>
                                    @if($r->reviewer_location)
                                        <div class="text-xs text-slate-500 truncate">{{ $r->reviewer_location }}</div>
                                    @endif
                                </div>
                                <div class="text-amber-400 text-sm">
                                    @for($i = 0; $i < (int) $r->rating; $i++)★@endfor
                                </div>
                            </div>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $r->review_text }}</p>
                            @if($r->review_date)
                                <div class="text-xs text-slate-400 mt-auto">{{ \Carbon\Carbon::parse($r->review_date)->format('M j, Y') }}</div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Member-only review submission. Owners (members) can leave a rating
             + review; guests see a sign-in CTA. Submissions go in as
             status='draft' for admin moderation. --}}
        <section class="my-12">
            @if(session('review_status'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">{{ session('review_status') }}</div>
            @endif
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                @auth('owner')
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Leave your review of {{ $cluster['name'] ?? ucwords($keyword->phrase) }}</h3>
                    <p class="text-sm text-slate-500 mb-4">Signed in as <strong>{{ Auth::guard('owner')->user()->name }}</strong>. Reviews are moderated before they go live.</p>
                    <form method="POST" action="{{ route('keyword.review.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="keyword_id" value="{{ $keyword->id }}">
                        <input type="hidden" name="redirect_to" value="{{ url($page->slug) }}">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-slate-700 font-medium">Rating</label>
                            <select name="rating" class="rounded-md border-slate-300 text-sm">
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }})</option>
                                @endfor
                            </select>
                        </div>
                        <textarea name="comment_text" disabled hidden></textarea>
                        <textarea name="review_text" rows="4" required minlength="20" maxlength="2000" class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Share what you actually experienced. Real specifics help future travelers more than generic praise."></textarea>
                        @error('review_text')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror
                        <button type="submit" class="px-5 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Submit review</button>
                    </form>
                @else
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Have you been to {{ $cluster['name'] ?? ucwords($keyword->phrase) }}?</h3>
                    <p class="text-slate-600 mb-4">Members can leave a review here. Reviews go through quick moderation before showing up on this page.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="px-5 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Sign in to review</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 rounded-md border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">Create an account</a>
                    </div>
                @endauth
            </div>
        </section>
    @endisset

    {{-- Author byline now renders as an `author` content block via the
         BlockRenderer (one per page, seeded at the bottom of the content
         stream). The hardcoded byline that used to live here was redundant
         once the block existed, so it was removed. --}}

    @if($related->isNotEmpty() && $cluster)
        <section class="mt-14 pt-10 border-t border-slate-200">
            <div class="flex items-end justify-between mb-5 flex-wrap gap-2">
                <h2 class="text-2xl font-bold text-slate-900">Other destinations in {{ $cluster['name'] }}</h2>
                <a href="{{ route('destinations.cluster', $keyword->cluster_tag) }}" class="text-sm text-brand-600 font-semibold hover:underline">All {{ $cluster['name'] }} destinations &rarr;</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($related as $r)
                    <a href="{{ url($r->slug) }}" class="block group p-4 rounded-lg border border-slate-200 hover:border-brand-300 hover:bg-brand-50/30 transition">
                        <h3 class="font-semibold text-slate-900 group-hover:text-brand-700 capitalize text-sm">{{ $r->phrase }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ number_format($r->search_volume_monthly) }} people search this monthly</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="mt-12 p-6 rounded-xl bg-slate-50 border border-slate-200 text-center">
        <h3 class="text-xl font-bold mb-2">Run a resort here?</h3>
        <p class="text-slate-600 mb-4">Get your property featured on this page.</p>
        <a href="{{ route('register') }}" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">List your property</a>
    </div>
</article>
@endsection
