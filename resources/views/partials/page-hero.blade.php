{{-- Shared photo hero for sub pages. Sits under the transparent header.
     Params (all optional except title):
       slides  [['src','alt','title','caption','credit_url']]   photos (crossfade when 2+)
       crumbs  [[label, url|null]]                              breadcrumb trail, last = current
       kicker, title, lede                                      text
       chips   [['icon' => star|pin|calendar|clock|user|book|tag|sun|chat, 'text' => '', 'sun' => bool]]
       actions [['label','url','style' => coral|glass|white]]
       size    lg|md|sm        center  bool
       kickerAttrs / titleAttrs  raw attribute strings (live editor hooks)
       extra                    extra HTML under the actions --}}
@php
    $slides = array_values(array_filter($slides ?? [], fn ($s) => !empty($s['src'])));
    $size = $size ?? 'lg';
    $plain = !$slides;
    $icons = [
        'star' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>',
        'pin' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>',
        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>',
        'clock' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        'user' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
        'book' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/></svg>',
        'tag' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>',
        'sun' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>',
        'chat' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.6 7.1L3 21l1.9-6.4A8 8 0 1 1 21 12z"/></svg>',
        'peso' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 21V3h6a5 5 0 0 1 0 10H7M4 7h16M4 10h16"/></svg>',
        'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-4-6"/></svg>',
    ];
    $chev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>';
    $arrow = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $src = fn ($u) => preg_match('#^(https?:)?//|^/|^data:#', $u) ? (preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $u) ?: $u) : '/' . ltrim($u, '/');
@endphp
@section('header_overlay', '1')
<section class="pk-hero pk-hero--{{ $size }} {{ $plain ? 'pk-hero--plain' : '' }} {{ !empty($center) ? 'pk-hero--center' : '' }}" @if(count($slides) > 1) data-pk-slides @endif>
    @unless($plain)
        <div class="pk-hero__media" aria-hidden="true">
            @foreach(array_slice($slides, 0, 6) as $i => $s)
                <img @if($i === 0) src="{{ $src($s['src']) }}" class="is-on" fetchpriority="high" @else data-src="{{ $src($s['src']) }}" @endif data-pk-slide alt="">
            @endforeach
        </div>
        <div class="pk-hero__shade" aria-hidden="true"></div>
    @endunless

    <div class="pk-hero__inner">
        @if(!empty($crumbs))
            <nav class="pk-crumbs" aria-label="Breadcrumb">
                @foreach($crumbs as $i => [$label, $url])
                    @if($i) {!! $chev !!} @endif
                    @if($url && $i < count($crumbs) - 1)
                        <a href="{{ $url }}">{{ $label }}</a>
                    @else
                        <span aria-current="page">{{ $label }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <div class="pk-hero__body">
            @if(!empty($kicker))
                <p class="pk-kicker" {!! $kickerAttrs ?? '' !!}>{{ $kicker }}</p>
            @endif
            <h1 class="pk-h1" {!! $titleAttrs ?? '' !!}>{{ $title }}</h1>
            @if(!empty($lede))
                <p class="pk-lede">{{ $lede }}</p>
            @endif
            @if(!empty($chips))
                <div class="pk-chips">
                    @foreach($chips as $c)
                        @if(!empty($c['text']))
                            <span class="pk-chip {{ !empty($c['sun']) ? 'pk-chip--sun' : '' }}">{!! $icons[$c['icon'] ?? 'tag'] ?? '' !!}{{ $c['text'] }}</span>
                        @endif
                    @endforeach
                </div>
            @endif
            @if(!empty($actions))
                <div class="pk-actions">
                    @foreach($actions as $a)
                        <a href="{{ $a['url'] }}" class="rg-btn rg-btn--lg rg-btn--{{ $a['style'] ?? 'coral' }}" @if(!empty($a['attrs'])) {!! $a['attrs'] !!} @endif>{{ $a['label'] }}@if(($a['style'] ?? 'coral') === 'coral'){!! $arrow !!}@endif</a>
                    @endforeach
                </div>
            @endif
            {!! $extra ?? '' !!}
        </div>
    </div>

    @unless($plain)
        @foreach(array_slice($slides, 0, 6) as $i => $s)
            @php $capTitle = trim($s['title'] ?? ''); $cap = trim($s['caption'] ?? ''); @endphp
            <p class="pk-hero__cap" data-pk-cap @if($i) hidden @endif>
                @if($capTitle !== '')<strong>{{ $capTitle }}</strong>@endif
                @if($cap !== '')
                    @if(!empty($s['credit_url']))<a href="{{ $s['credit_url'] }}" target="_blank" rel="noopener nofollow">{{ $cap }}</a>@else{{ $cap }}@endif
                @endif
            </p>
        @endforeach
        @if(count($slides) > 1)
            <div class="pk-hero__dots">
                @foreach(array_slice($slides, 0, 6) as $i => $s)
                    <button type="button" data-pk-dot class="{{ $i ? '' : 'is-on' }}" aria-label="Photo {{ $i + 1 }}"></button>
                @endforeach
            </div>
        @endif
    @endunless
</section>
