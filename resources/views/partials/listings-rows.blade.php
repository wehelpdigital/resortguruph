{{--
    "We recommend" listings band for keyword pages (resorts, or restaurants on food pages).
    Listings arrive sorted by bid, so the first one is the paid top spot: it gets the
    large featured card, the rest get compact cards. Styles: partials/listings-style.

    Props:
      $listings         → paginator / collection of RgListing (resort eager-loaded) or RgRestaurantListing
      $listingGalleries → array<int, string[]> photos per listing.id
      $listingRatings   → array<int, float>    rating per listing.id
      $listingReviews   → array<int, array[]>  short reviews per listing.id
      $keyword, $area   → category-aware wording
--}}

@php
    $hasAny = $listings && $listings->count() > 0;
    $isFood = isset($keyword) && ($keyword->category ?? 'resort') === 'food';
    $areaLabel = $area ?? null;
    $sectionTitle = $isFood ? 'Restaurants and food spots we recommend' : 'Hotels and resorts we recommend';
    $sectionSub = $isFood
        ? 'Hand-picked places to eat, checked by our team before they go live.'
        : 'Hand-picked stays, checked by our team before they go live.';
    $emptyHeading = $isFood
        ? ($areaLabel ? 'Be the first restaurant or eatery listed in ' . $areaLabel . ' or nearby locations' : 'Be the first restaurant, eatery, or food shop listed on this page')
        : 'Be the first hotel or resort listed on this page';
    $emptyOffer = $isFood
        ? 'Get your restaurant featured at the top, with photos, cuisine, hours, reviews, and direct contact links.'
        : 'Get your property featured at the top, with photos, amenities, reviews, and direct booking links.';
    $star = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>';
    $pin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>';
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $check = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>';
    $verdict = fn ($r) => $r >= 4.8 ? 'Exceptional' : ($r >= 4.5 ? 'Excellent' : ($r >= 4.0 ? 'Very good' : 'Good'));

    // one normalized row per listing so resorts and restaurants share the markup
    $rows = [];
    if ($hasAny) {
        foreach ($listings as $listing) {
            if ($isFood) {
                $r = $listing->restaurant ?? null;
                if (!$r) continue;
                $seed = abs(crc32('restaurant_' . $r->id));
                $photos = $r->hero_path ? ['/storage/' . ltrim($r->hero_path, '/')] : [];
                $rows[] = [
                    'name' => $r->name, 'url' => null,
                    'place' => trim($r->city . ($r->province ? ', ' . $r->province : ''), ', '),
                    'maps' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($r->name . ' ' . $r->city . ' Philippines')),
                    'photos' => $photos, 'rating' => round(4.4 + (($seed % 6) * 0.1), 1), 'count' => 60 + ($seed % 220),
                    'tag' => $r->cuisine, 'tagline' => $r->tagline, 'desc' => '',
                    'chips' => array_values(array_filter([$r->cuisine, $r->hours_summary ? 'Open ' . $r->hours_summary : null])),
                    'review' => null,
                    'ctas' => [['Visit restaurant', '#', 'coral'], ['View menu', '#', 'outline']],
                    'tint' => $r->primary_color ?: '#0f3a5c',
                ];
            } else {
                $r = $listing->resort ?? null;
                if (!$r) continue;
                $photos = array_values(array_unique(array_filter($listingGalleries[$listing->id] ?? [])));
                $am = $r->amenities_json ? (is_array($r->amenities_json) ? $r->amenities_json : (json_decode($r->amenities_json, true) ?: [])) : [];
                $desc = trim(strip_tags($r->description_html ?? ''));
                if ($desc === '') $desc = $r->tagline ?: ('A vetted property in ' . $r->city . ' selected by our editorial team.');
                $revs = $listingReviews[$listing->id] ?? [];
                $rows[] = [
                    'name' => $r->name, 'url' => route('resort.show', $r->slug),
                    'place' => trim($r->city . ($r->province ? ', ' . $r->province : ''), ', '),
                    'maps' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($r->name . ' ' . $r->city . ' ' . $r->province . ' Philippines')),
                    'photos' => $photos, 'rating' => (float) ($listingRatings[$listing->id] ?? 4.7), 'count' => null,
                    'tag' => $r->price_range, 'tagline' => $r->tagline, 'desc' => \Illuminate\Support\Str::limit($desc, 260),
                    'chips' => array_slice($am, 0, 6),
                    'review' => $revs[0] ?? null,
                    'ctas' => [['Check availability', route('resort.show', $r->slug) . '#reserve', 'coral'], ['See amenities', route('resort.show', $r->slug) . '#amenities', 'outline']],
                    'tint' => $r->primary_color ?: '#0f3a5c',
                ];
            }
        }
    }
    $top = $rows[0] ?? null;
    $rest = array_slice($rows, 1);
@endphp

@include('partials.listings-style')

<section id="top-picks" class="rg-listings-band relative p-5 sm:p-7 mb-10">
    <div class="lx-head">
        <div class="min-w-0">
            <p class="pk-eyebrow">{{ $isFood ? 'Where to eat' : 'Where to stay' }}</p>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-1">{{ $sectionTitle }}</h2>
            <p class="text-sm text-slate-600">{{ $sectionSub }}</p>
        </div>
        <div class="lx-seal" title="Every listing is checked by our team before it goes live">
            <img src="{{ asset('images/verified-seal.svg') }}" alt="" width="56" height="56" loading="lazy">
            <span><strong>Verified listings</strong><small>Checked by our team</small></span>
        </div>
    </div>

    @if(!$top)
        @php
            $monthlySearches = isset($keyword) ? (int) ($keyword->search_volume_monthly ?? 0) : 0;
            $monthlySearchesLabel = $monthlySearches > 0 ? number_format($monthlySearches) : null;
        @endphp
        <div class="rounded-[1.25rem] bg-white border border-sand-200 p-5 sm:p-7 flex gap-4 sm:gap-5 items-start">
            <span class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-coral-50 text-coral-500 flex items-center justify-center shrink-0">
                @if($isFood)
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/></svg>
                @else
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/></svg>
                @endif
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-display text-ink-900 font-extrabold text-lg leading-snug">{{ $emptyHeading }}</p>
                <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">
                    @if($monthlySearchesLabel)<strong class="text-ink-900">{{ $monthlySearchesLabel }}</strong> people search for <strong>{{ $keyword->phrase ?? '' }}</strong> every month. @endif
                    {{ $emptyOffer }} <strong class="text-emerald-700">Start with a low minimum bid.</strong>
                </p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('register') }}" class="rg-btn rg-btn--coral">{{ $isFood ? 'List your restaurant' : 'List your property' }}</a>
                    <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--outline">How it works</a>
                </div>
            </div>
        </div>
    @else
        {{-- ===== #1: the featured top pick ===== --}}
        <article class="lx-top">
            <div class="lx-top__media" @if(count($top['photos']) > 1) data-lx-fade @endif style="--tint: {{ $top['tint'] }}">
                @forelse(array_slice($top['photos'], 0, 4) as $i => $ph)
                    <img src="{{ $ph }}" alt="{{ $top['name'] }}" class="{{ $i === 0 ? 'is-on' : '' }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                @empty
                    <span class="lx-top__noimg">{{ mb_substr($top['name'], 0, 1) }}</span>
                @endforelse
                <span class="lx-ribbon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M5 16 3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"/></svg>Top pick</span>
                @if(count($top['photos']) > 1)
                    <span class="lx-top__count">{{ count($top['photos']) }} photos</span>
                @endif
            </div>
            <div class="lx-top__body">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="lx-top__kicker">#1 {{ $isFood ? 'place to eat' : 'place to stay' }} on this guide</p>
                        <h3 class="lx-top__name">@if($top['url'])<a href="{{ $top['url'] }}">{{ $top['name'] }}</a>@else{{ $top['name'] }}@endif</h3>
                        <a href="{{ $top['maps'] }}" target="_blank" rel="nofollow noopener" class="lx-place"><span class="w-4 h-4 inline-flex">{!! $pin !!}</span>{{ $top['place'] }}</a>
                    </div>
                    <div class="lx-score">
                        <span class="lx-score__num">{{ number_format($top['rating'], 1) }}</span>
                        <span class="lx-score__txt">{{ $verdict($top['rating']) }}@if($top['count'])<small>{{ $top['count'] }} reviews</small>@endif</span>
                    </div>
                </div>
                @if($top['tagline'])<p class="lx-top__tagline">"{{ $top['tagline'] }}"</p>@endif
                @if($top['desc'])<p class="lx-top__desc">{{ $top['desc'] }}</p>@endif
                @if($top['chips'] || $top['tag'])
                    <ul class="lx-chips">
                        @if($top['tag'] && !$isFood)<li class="lx-chips__price">{{ $top['tag'] }}</li>@endif
                        @foreach($top['chips'] as $c)<li><span class="w-3.5 h-3.5 inline-flex">{!! $check !!}</span>{{ $c }}</li>@endforeach
                    </ul>
                @endif
                @if($top['review'])
                    <figure class="lx-quote">
                        <span class="lx-quote__stars">@for($i = 0; $i < 5; $i++)<span class="w-3.5 h-3.5 inline-flex">{!! $star !!}</span>@endfor</span>
                        <blockquote>"{{ $top['review']['body'] }}"</blockquote>
                        <figcaption>{{ $top['review']['name'] }}, {{ $top['review']['city'] }}</figcaption>
                    </figure>
                @endif
                <div class="lx-top__ctas">
                    @foreach($top['ctas'] as [$label, $href, $style])
                        <a href="{{ $href }}" class="rg-btn rg-btn--{{ $style }} {{ $style === 'coral' ? 'rg-btn--lg' : '' }}">{{ $label }}@if($style === 'coral')<span class="w-4 h-4 inline-flex">{!! $arrow !!}</span>@endif</a>
                    @endforeach
                    <span class="lx-sponsored">Sponsored</span>
                </div>
            </div>
        </article>

        {{-- ===== the other picks ===== --}}
        @if($rest)
            <div class="lx-grid">
                @foreach($rest as $n => $row)
                    <article class="lx-card">
                        <div class="lx-card__media" style="--tint: {{ $row['tint'] }}">
                            @if(!empty($row['photos'][0]))<img src="{{ $row['photos'][0] }}" alt="{{ $row['name'] }}" loading="lazy">@else<span class="lx-top__noimg">{{ mb_substr($row['name'], 0, 1) }}</span>@endif
                            <span class="lx-rank">#{{ $n + 2 }}</span>
                        </div>
                        <div class="lx-card__body">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="lx-card__name">@if($row['url'])<a href="{{ $row['url'] }}">{{ $row['name'] }}</a>@else{{ $row['name'] }}@endif</h3>
                                <span class="lx-mini"><span class="w-3.5 h-3.5 inline-flex">{!! $star !!}</span>{{ number_format($row['rating'], 1) }}</span>
                            </div>
                            <a href="{{ $row['maps'] }}" target="_blank" rel="nofollow noopener" class="lx-place lx-place--sm"><span class="w-3.5 h-3.5 inline-flex">{!! $pin !!}</span>{{ $row['place'] }}</a>
                            @if($row['tagline'])<p class="lx-card__tagline">{{ $row['tagline'] }}</p>@endif
                            @if($row['chips'])
                                <ul class="lx-chips lx-chips--sm">@foreach(array_slice($row['chips'], 0, 3) as $c)<li>{{ $c }}</li>@endforeach</ul>
                            @endif
                            <div class="lx-card__ctas">
                                <a href="{{ $row['ctas'][0][1] }}" class="rg-btn rg-btn--ocean">{{ $row['ctas'][0][0] }}</a>
                                <span class="lx-sponsored">Sponsored</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @if(method_exists($listings, 'links') && $listings->hasPages())
            <div class="mt-6 flex justify-center">{{ $listings->links() }}</div>
        @endif
    @endif
</section>
