{{--
    Restaurant recommendations on resort guides (restaurant_recs_band): compact
    cards in the same style as the "we recommend" band (partials/listings-style).
--}}
@include('partials.listings-style')
@php
    $star = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>';
    $pin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>';
@endphp
<div class="lx-grid !mt-0">
    @foreach($listings as $idx => $l)
        @if($l->restaurant)
            @php
                $r = $l->restaurant;
                $seed = abs(crc32('restaurant_' . $r->id));
                $rating = round(4.4 + (($seed % 6) * 0.1), 1);
                $reviewCount = 60 + ($seed % 220);
                $maps = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode(trim($r->name . ' ' . $r->city . ' Philippines'));
            @endphp
            <article class="lx-card">
                <div class="lx-card__media" style="--tint: {{ $r->primary_color ?: '#7c2d12' }}">
                    @if($r->hero_path)<img src="{{ asset('storage/' . $r->hero_path) }}" alt="{{ $r->name }}" loading="lazy">@else<span class="lx-top__noimg">{{ mb_substr($r->name, 0, 1) }}</span>@endif
                    <span class="lx-rank">#{{ $idx + 1 }}</span>
                </div>
                <div class="lx-card__body">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="lx-card__name">{{ $r->name }}</h3>
                        <span class="lx-mini"><span class="w-3.5 h-3.5 inline-flex">{!! $star !!}</span>{{ $rating }}</span>
                    </div>
                    <a href="{{ $maps }}" target="_blank" rel="nofollow noopener" class="lx-place lx-place--sm"><span class="w-3.5 h-3.5 inline-flex">{!! $pin !!}</span>{{ $r->city }}{{ $r->province ? ', ' . $r->province : '' }}</a>
                    @if($r->tagline)<p class="lx-card__tagline">{{ $r->tagline }}</p>@endif
                    <ul class="lx-chips lx-chips--sm">
                        @if($r->cuisine)<li>{{ $r->cuisine }}</li>@endif
                        @if($r->hours_summary)<li>Open {{ $r->hours_summary }}</li>@endif
                        <li>{{ $reviewCount }} reviews</li>
                    </ul>
                    <div class="lx-card__ctas">
                        <a href="{{ $maps }}" target="_blank" rel="nofollow noopener" class="rg-btn rg-btn--ocean">Get directions</a>
                        <span class="lx-sponsored">Sponsored</span>
                    </div>
                </div>
            </article>
        @endif
    @endforeach
</div>
