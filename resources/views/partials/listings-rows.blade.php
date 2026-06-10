{{--
    Row-based "What we recommend" listings layout for keyword pages.

    Per-listing structure:
      ┌─────────────────────────────────────────────────────────────┐
      │  Resort name                                                 │
      │  📍 City, Province (Google Maps link)                        │
      │  ★ 4.8 rating                                                │
      ├──────┬──────┬──────┬──────┐                                  │
      │ img1 │ img2 │ img3 │ img4 │ (4 fading strips)                │
      ├──────┴──────┴──────┴──────┘                                  │
      │  [Pool] [Wi-Fi] [Parking] (amenity badges row)               │
      │  Description text                                            │
      │  Recent review (cycles through 3 fakes with fade)            │
      │  [Reserve and Book] [Amenities] [Experiences]   (sponsored)  │
      └─────────────────────────────────────────────────────────────┘

    Container header carries a "verified / scam protect" badge image in
    the upper-left, the section title "What we recommend", and a one-line
    explanation. Listings are pre-sorted by fake rating descending.

    Props:
      $listings         → paginator of RgListing (resort eager-loaded, sorted)
      $listingGalleries → array<int, string[]> 6+ images per listing.id
      $listingRatings   → array<int, float>    fake 4.5-4.9 rating per listing.id
      $listingReviews   → array<int, array[]>  3 fake reviews per listing.id
--}}

@php
    $hasAny = $listings && $listings->count() > 0;
    // Category-aware wording: resort pages use "hotels and resorts"; food
    // pages use "restaurants, eateries, and food shops". Detected from the
    // $keyword in scope (always passed by the parent view).
    $isFood = isset($keyword) && ($keyword->category ?? 'resort') === 'food';
    // Optional $area override (e.g. "Mall of Asia") lets the CTA invite
    // owners to list at the specific location instead of generic wording.
    $areaLabel = $area ?? null;
    $sectionTitle = $isFood
        ? 'Restaurants, Eateries & Food Destinations We Recommend'
        : 'Hotels and Resorts that We Recommend';
    $sectionSub = $isFood
        ? 'We are recommending these restaurants, eateries, and food shops.'
        : 'We are recommending these hotels and resorts.';
    $emptyIcon = $isFood ? '🍽️' : '🏝️';
    $emptyHeading = $isFood
        ? ($areaLabel
            ? 'Be the first restaurant or eatery listed in ' . $areaLabel . ' or nearby locations'
            : 'Be the first restaurant, eatery, or food shop listed on this page')
        : 'Be the first hotel or resort listed on this page';
    $emptyOffer = $isFood
        ? 'Get your restaurant featured at the top, with photos, cuisine, hours, reviews, and direct contact links.'
        : 'Get your property featured at the top, with photos, amenities, reviews, and direct booking links.';
    $ctaLabel = $isFood
        ? ($areaLabel
            ? 'List your restaurant in ' . $areaLabel . ' or nearby'
            : 'List your restaurant, eatery, or food shop')
        : 'List your property';
@endphp

<section class="rg-listings-band relative px-4 sm:px-6 lg:px-8 pt-6 pb-8 mb-10">

    {{-- Verified / scam-protect badge, upper-right of container (placeholder
         circle image). Absolutely positioned so it doesn't push the heading.
         top: 1.5rem keeps it tucked inside the corner instead of overhanging. --}}
    <img src="{{ asset('images/verified-badge.svg') }}" alt="Verified and scam-protect badge" class="absolute right-3 sm:right-6 w-16 h-16 sm:w-20 sm:h-20 rounded-full shadow-md pointer-events-none select-none" style="top: 1.5rem;" loading="lazy">

    <div class="mb-6 pr-20 sm:pr-24">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-1">{{ $sectionTitle }}</h2>
        <p class="text-sm text-slate-600">{{ $sectionSub }}</p>
    </div>

    @if(!$hasAny)
        @php
            // Pull the search-volume teaser from the keyword the page is on so
            // operators can see the demand they'd be plugging into.
            $monthlySearches = isset($keyword) ? (int) ($keyword->search_volume_monthly ?? 0) : 0;
            $monthlySearchesLabel = $monthlySearches > 0 ? number_format($monthlySearches) : null;
        @endphp
        <div class="rounded-2xl bg-white border border-slate-200 p-6 sm:p-8 text-center">
            <div class="text-3xl mb-3">{{ $emptyIcon }}</div>
            <p class="text-slate-900 font-bold text-lg mb-1">{{ $emptyHeading }}</p>
            @if($monthlySearchesLabel)
                <p class="text-sm text-slate-600 mb-1">
                    <strong class="text-brand-700">{{ $monthlySearchesLabel }}</strong> Filipinos search for
                    <strong>{{ $keyword->phrase ?? '' }}</strong> every month on Google.
                </p>
            @endif
            <p class="text-sm text-slate-600 mb-5">
                {{ $emptyOffer }}
                <strong class="text-emerald-700">Start with a low minimum bid.</strong>
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    {{ $ctaLabel }}
                </a>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                    How it works
                </a>
            </div>
        </div>
    @elseif($isFood)
        {{-- Food pages: dispatch to the restaurant-card grid. The resort
             fading-strip layout doesn't fit restaurant data shape. --}}
        @include('partials.restaurant-listings', ['listings' => $listings])
    @else
        <div class="space-y-8">
            @foreach($listings as $idx => $listing)
                @php
                    $resort = $listing->resort;
                    if (!$resort) continue;
                    $gallery = $listingGalleries[$listing->id] ?? [];
                    // Pad to at least 8 images for 4 strips of 2 each
                    while (count($gallery) < 8 && !empty($gallery)) { $gallery[] = $gallery[count($gallery) % max(count($gallery),1)]; }
                    $strips = [
                        [$gallery[0] ?? null, $gallery[4] ?? null],
                        [$gallery[1] ?? null, $gallery[5] ?? null],
                        [$gallery[2] ?? null, $gallery[6] ?? null],
                        [$gallery[3] ?? null, $gallery[7] ?? null],
                    ];
                    $amenities = $resort->amenities_json
                        ? (is_array($resort->amenities_json) ? $resort->amenities_json : (json_decode($resort->amenities_json, true) ?: []))
                        : [];
                    $rating = $listingRatings[$listing->id] ?? 4.7;
                    $reviews = $listingReviews[$listing->id] ?? [];
                    $shortDesc = trim(strip_tags($resort->description_html ?? ''));
                    if ($shortDesc === '') $shortDesc = $resort->tagline ?: ('A vetted property in ' . $resort->city . ' selected by our editorial team.');
                    if (mb_strlen($shortDesc) > 320) $shortDesc = mb_substr($shortDesc, 0, 317) . '...';

                    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($resort->name . ' ' . $resort->city . ' ' . $resort->province . ' Philippines'));
                @endphp
                <article class="rg-listing-row relative rounded-2xl bg-white border border-slate-200 overflow-hidden hover:border-slate-300 transition-colors">

                    {{-- Header: name, location (linked to Google Maps), rating --}}
                    <header class="px-5 sm:px-7 pt-5 pb-4 border-b border-slate-100 bg-slate-50/50">
                        <a href="{{ route('resort.show', $resort->slug) }}" class="block">
                            <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 hover:text-brand-600 transition-colors leading-tight">{{ $resort->name }}</h3>
                        </a>
                        <div class="flex flex-wrap items-center gap-3 mt-1.5 text-sm">
                            <a href="{{ $mapsUrl }}" target="_blank" rel="nofollow noopener" class="inline-flex items-center gap-1 text-slate-600 hover:text-brand-600 transition-colors">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span class="underline decoration-dotted decoration-slate-300">{{ $resort->city }}{{ $resort->province ? ', ' . $resort->province : '' }}</span>
                            </a>
                            <span class="inline-flex items-center gap-1 text-amber-500 font-semibold">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-slate-900">{{ number_format($rating, 1) }}</span>
                                <span class="text-xs text-slate-500 font-normal">(verified)</span>
                            </span>
                            @if($resort->tagline)
                                <span class="text-slate-500 italic hidden sm:inline">"{{ $resort->tagline }}"</span>
                            @endif
                        </div>
                    </header>

                    {{-- 4 fading image strips, full-width --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 p-3 sm:p-4 pb-2">
                        @foreach($strips as $sIdx => $strip)
                            <a href="{{ route('resort.show', $resort->slug) }}" class="rg-fade-strip block aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 relative" data-strip-index="{{ $sIdx }}">
                                @foreach($strip as $img)
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $resort->name }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
                                    @endif
                                @endforeach
                            </a>
                        @endforeach
                    </div>

                    {{-- Amenities row, full-width --}}
                    @if(!empty($amenities))
                        <div class="px-3 sm:px-4 pt-1 pb-3">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_slice($amenities, 0, 8) as $a)
                                    <span class="inline-block px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">{{ $a }}</span>
                                @endforeach
                                @if(count($amenities) > 8)
                                    <span class="inline-block px-2.5 py-1 rounded-full text-slate-400 text-xs">+{{ count($amenities) - 8 }} more</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Description row --}}
                    <div class="px-5 sm:px-7 py-4 border-t border-slate-100">
                        <p class="text-slate-700 leading-relaxed text-[0.95rem]">{{ $shortDesc }}</p>
                    </div>

                    {{-- Fading review section (cycles 3 reviews) --}}
                    @if(!empty($reviews))
                        <div class="rg-review-fader px-5 sm:px-7 py-4 bg-slate-50 border-t border-slate-100 min-h-[90px]" data-review-count="{{ count($reviews) }}">
                            @foreach($reviews as $rIdx => $rev)
                                <div class="rg-review-slide @if($rIdx === 0) is-active @endif" data-review-index="{{ $rIdx }}">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex w-8 h-8 rounded-full bg-brand-100 text-brand-700 items-center justify-center font-bold text-sm shrink-0" aria-hidden="true">{{ strtoupper(substr($rev['name'], 0, 1)) }}</span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-baseline gap-2 flex-wrap text-sm">
                                                <span class="font-semibold text-slate-900">{{ $rev['name'] }}</span>
                                                <span class="text-amber-400 text-xs">★★★★★</span>
                                                <span class="text-xs text-slate-400">{{ $rev['city'] }} · {{ $rev['days_ago'] }} days ago</span>
                                            </div>
                                            <p class="text-sm text-slate-700 leading-relaxed mt-1">"{{ $rev['body'] }}"</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- CTA buttons row, full-width --}}
                    <div class="px-5 sm:px-7 py-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
                        <a href="{{ route('resort.show', $resort->slug) }}#reserve" class="rg-cta rg-cta-reserve">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/></svg>
                            Reserve and Book
                        </a>
                        <a href="{{ route('resort.show', $resort->slug) }}#amenities" class="rg-cta rg-cta-amenities">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            Amenities
                        </a>
                        <a href="{{ route('resort.show', $resort->slug) }}#experiences" class="rg-cta rg-cta-experiences">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.32.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .32-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                            Experiences
                        </a>
                        <span class="ml-auto text-[10px] uppercase tracking-wider text-slate-400 font-medium">Sponsored</span>
                    </div>
                </article>
            @endforeach
        </div>

        @if(method_exists($listings, 'links') && $listings->hasPages())
            <div class="mt-6 flex justify-center">{{ $listings->links() }}</div>
        @endif
    @endif
</section>
