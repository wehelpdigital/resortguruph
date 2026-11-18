@extends('layouts.public')

{{-- Hand-designed region page (/tourist-spots-destinations-philippines/{cluster}).
     Data: DestinationsController@cluster + App\Services\DestinationShowcase::region().
     rg_settings.destinations_layout = blocks switches back to the block-builder template. --}}

@section('title') {{ $meta['name'] }} Travel Guide: Where to Stay, Tourist Spots and When to Go | Tourist Guide Ph @endsection
@section('meta_description') {{ $meta['meta_description'] }} @endsection
@section('canonical') {{ route('destinations.cluster', $cluster) }} @endsection
@section('og_image'){{ url($data['hero'][0] ?? '/images/hero-palawan-1600.webp') }}@endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => route('destinations.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $meta['name'], 'item' => route('destinations.cluster', $cluster)],
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $meta['name'] . ' travel guide',
    'description' => $meta['meta_description'],
    'url' => route('destinations.cluster', $cluster),
    'mainEntity' => [
        '@type' => 'ItemList',
        'numberOfItems' => count($data['guides']),
        'itemListElement' => collect($data['guides'])->take(20)->values()->map(fn ($g, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $g['label'], 'url' => $g['url']])->all(),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@if($data['spots'])
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Tourist spots in ' . $meta['name'],
    'itemListElement' => collect($data['spots'])->take(12)->values()->map(fn ($sp, $i) => [
        '@type' => 'ListItem', 'position' => $i + 1,
        'item' => array_filter([
            '@type' => 'TouristAttraction', 'name' => $sp['name'],
            'description' => $sp['desc'] !== '' ? $sp['desc'] : null,
            'image' => url($sp['image']),
            'address' => $sp['location'] !== '' ? ['@type' => 'PostalAddress', 'addressLocality' => $sp['location'], 'addressRegion' => $meta['name'], 'addressCountry' => 'PH'] : null,
        ]),
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
@endsection

@php
    $f = $data['facts'];
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $star = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>';
    $pin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>';
    $guides = $data['guides'];
    $spots = $data['spots'];
    $name = $meta['name'];
@endphp

@section('content')
@include('partials.page-hero', [
    'slides' => array_map(fn ($u) => ['src' => $u], $data['hero']),
    'crumbs' => [['Home', url('/')], ['Destinations', route('destinations.index')], [$name, null]],
    'kicker' => 'Region guide',
    'title' => $name,
    'lede' => $meta['tagline'] ?? '',
    'chips' => [
        ['icon' => 'book', 'text' => count($guides) . ' place' . (count($guides) === 1 ? '' : 's') . ' to stay'],
        ['icon' => 'pin', 'text' => $spots ? count($spots) . ' tourist spots' : ''],
        ['icon' => 'clock', 'text' => $f ? 'From Manila: ' . $f['from'] : ''],
        ['icon' => 'sun', 'sun' => true, 'text' => $f ? 'Best: ' . $f['best'] : ''],
    ],
    'actions' => [
        ['label' => 'Find a place to stay', 'url' => '#stay', 'style' => 'coral'],
        ['label' => 'See tourist spots', 'url' => '#spots', 'style' => 'glass'],
    ],
])

{{-- ============ QUICK FACTS ============ --}}
@if($f)
<section class="relative z-10 pt-6 md:pt-0 md:-mt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rx-facts">
            <div class="rx-fact"><span class="rx-fact__ico" style="--c:#13669c"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><p class="rx-fact__label">From Manila</p><p class="rx-fact__value">{{ $f['from'] }}</p></div></div>
            <div class="rx-fact"><span class="rx-fact__ico" style="--c:#d97706"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></span><div><p class="rx-fact__label">Best time</p><p class="rx-fact__value">{{ $f['best'] }}</p></div></div>
            <div class="rx-fact"><span class="rx-fact__ico" style="--c:#059669"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg></span><div><p class="rx-fact__label">Ideal stay</p><p class="rx-fact__value">{{ $f['stay'] }}</p></div></div>
            <div class="rx-fact rx-fact--wide"><span class="rx-fact__ico" style="--c:#ef5b3a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg></span><div><p class="rx-fact__label">Getting there</p><p class="rx-fact__value rx-fact__value--sm">{{ $f['getting'] }}</p></div></div>
        </div>
    </div>
</section>
@endif

{{-- ============ WHERE TO STAY ============ --}}
<section id="stay" class="py-16 md:py-20" style="scroll-margin-top:var(--rg-header-h,76px)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
            <div class="max-w-2xl">
                <p class="pk-eyebrow">Where to stay</p>
                <h2 class="pk-h2">Pick your base in {{ $name }}</h2>
                <p class="mt-3 text-slate-600 text-lg">Each guide compares the resorts and hotels in one town, with the spots, food and travel tips around it.</p>
            </div>
            @if($f && !empty($f['known']))
                <div class="flex flex-wrap gap-2 md:justify-end md:max-w-sm">
                    @foreach($f['known'] as $k)<span class="rounded-full bg-sand-100 border border-sand-200 px-3 py-1.5 text-sm font-semibold text-ink-900">{{ $k }}</span>@endforeach
                </div>
            @endif
        </div>

        <div class="rx-stays">
            @foreach(array_slice($guides, 0, 9) as $i => $g)
                <a href="{{ $g['url'] }}" class="rx-stay group {{ $i < 2 && count($guides) > 4 ? 'rx-stay--big' : '' }}">
                    <span class="rx-stay__img"><img src="{{ $g['image'] }}" alt="{{ $g['label'] }}" loading="lazy"></span>
                    <span class="rx-stay__body">
                        @if($g['popular'])<span class="rx-stay__tag">Popular</span>@endif
                        <span class="rx-stay__name">{{ $g['label'] }}</span>
                        <span class="rx-stay__more">Read the guide <span class="w-4 h-4 inline-flex">{!! $arrow !!}</span></span>
                    </span>
                </a>
            @endforeach
        </div>
        @if(count($guides) > 9)
            <details class="rx-more mt-6">
                <summary class="rg-btn rg-btn--outline">Show all {{ count($guides) }} places to stay</summary>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach(array_slice($guides, 9) as $g)
                        <a href="{{ $g['url'] }}" class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 hover:border-brand-300 hover:text-brand-700">{{ $g['label'] }}</a>
                    @endforeach
                </div>
            </details>
        @endif
    </div>
</section>

{{-- ============ ABOUT ============ --}}
@if(trim(strip_tags($data['intro'])) !== '')
<section class="bg-sand-100 py-16 md:py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div>
            <p class="pk-eyebrow">Know before you go</p>
            <h2 class="pk-h2">What {{ $name }} is like</h2>
            <div class="pk-prose mt-5">{!! \App\Support\PageKit::balance($data['intro']) !!}</div>
            <div class="mt-7 flex flex-wrap gap-3">
                <a href="{{ url('/create-your-adventure') }}" class="rg-btn rg-btn--coral rg-btn--lg">Plan my trip{!! $arrow !!}</a>
                <a href="{{ route('fiestas.index') }}" class="rg-btn rg-btn--outline rg-btn--lg">Festival calendar</a>
            </div>
        </div>
        @php $collage = array_slice(array_column($spots, 'image'), 0, 3); @endphp
        @if(count($collage) >= 2)
            <div class="relative h-[22rem] sm:h-[28rem]">
                <img src="{{ $collage[0] }}" alt="" loading="lazy" class="rx-collage rx-collage--a">
                <img src="{{ $collage[1] }}" alt="" loading="lazy" class="rx-collage rx-collage--b">
                @isset($collage[2])<img src="{{ $collage[2] }}" alt="" loading="lazy" class="rx-collage rx-collage--c">@endisset
                @if($f)
                    <div class="rx-float">
                        <p class="text-[.68rem] font-extrabold uppercase tracking-[.16em] text-coral-600">Best time to go</p>
                        <p class="mt-1 font-display font-extrabold text-ink-900 leading-snug">{{ $f['best'] }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ TOURIST SPOTS ============ --}}
@if($spots)
<section id="spots" class="py-16 md:py-24" style="scroll-margin-top:var(--rg-header-h,76px)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8">
            <p class="pk-eyebrow">Tourist spots</p>
            <h2 class="pk-h2">Things to see in {{ $name }}</h2>
            <p class="mt-3 text-slate-600 text-lg">{{ count($spots) }} spots, each with a map pin so you can plan the route.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5" data-rx-spots>
            @foreach($spots as $i => $sp)
                <article class="rx-spot" @if($i >= 9) hidden data-rx-extra @endif>
                    <span class="rx-spot__img"><img src="{{ $sp['image'] }}" alt="{{ $sp['name'] }}" loading="lazy"></span>
                    <div class="rx-spot__body">
                        <h3 class="font-display font-extrabold text-lg text-ink-900 leading-snug">{{ $sp['name'] }}</h3>
                        @if($sp['location'])<p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500"><span class="w-4 h-4 inline-flex text-coral-500">{!! $pin !!}</span>{{ $sp['location'] }}</p>@endif
                        @if($sp['desc'])<p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $sp['desc'] }}</p>@endif
                        <a href="{{ $sp['map'] }}" target="_blank" rel="noopener" class="mt-auto pt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-700 hover:text-brand-800">Open in Google Maps <span class="w-4 h-4 inline-flex">{!! $arrow !!}</span></a>
                    </div>
                </article>
            @endforeach
        </div>
        @if(count($spots) > 9)
            <div class="mt-8 text-center"><button type="button" class="rg-btn rg-btn--outline rg-btn--lg" data-rx-more>Show {{ count($spots) - 9 }} more spots</button></div>
        @endif
    </div>
</section>
@endif

{{-- ============ FIESTAS ============ --}}
@if($data['fiestas'])
<section class="relative overflow-hidden bg-ink-900 text-white py-16 md:py-20 isolate">
    <div class="absolute -right-40 -top-40 w-[34rem] h-[34rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(239,91,58,.32), transparent 65%)"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-8 flex-wrap">
            <div>
                <p class="pk-eyebrow !text-sun-300">Festivals</p>
                <h2 class="pk-h2 !text-white">Time your trip with a fiesta</h2>
            </div>
            <a href="{{ route('fiestas.index') }}" class="rg-btn rg-btn--glass">All festivals</a>
        </div>
        @php $fzCount = min(4, count($data['fiestas'])); @endphp
        <div class="grid gap-3 sm:gap-5 {{ $fzCount === 1 ? 'max-w-3xl' : ($fzCount === 2 ? 'sm:grid-cols-2 max-w-4xl' : 'grid-cols-2 lg:grid-cols-4') }}">
            @foreach(array_slice($data['fiestas'], 0, 4) as $fz)
                <a href="{{ $fz['url'] }}" class="group rounded-[1.25rem] overflow-hidden bg-white/5 border border-white/10 hover:bg-white/10 transition {{ $fzCount === 1 ? 'grid sm:grid-cols-2' : 'block' }}">
                    <span class="block {{ $fzCount === 1 ? 'aspect-[4/3] sm:aspect-auto sm:h-full' : 'aspect-[4/3]' }} overflow-hidden"><img src="{{ $fz['image'] }}" alt="{{ $fz['name'] }}" loading="lazy" class="w-full h-full object-cover transition duration-700 group-hover:scale-105"></span>
                    <span class="block {{ $fzCount === 1 ? 'p-6 sm:p-8 self-center' : 'p-4' }}">
                        <span class="block text-[.7rem] font-bold uppercase tracking-wider text-sun-300">{{ \Illuminate\Support\Str::limit($fz['when'], 40) }}</span>
                        <span class="block mt-1 font-display font-extrabold leading-snug">{{ $fz['name'] }}</span>
                        <span class="block mt-1 text-xs text-white/65">{{ $fz['place'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ REVIEWS ============ --}}
@if($data['reviews'])
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-8">
            <p class="pk-eyebrow">From travelers</p>
            <h2 class="pk-h2">What people say about {{ $name }}</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5 rg-swipe">
            @foreach($data['reviews'] as $rv)
                <figure class="flex flex-col rounded-[1.25rem] bg-white border border-slate-200 p-6">
                    <span class="flex gap-0.5 text-sun-400">@for($i = 0; $i < $rv['rating']; $i++)<span class="w-4 h-4 inline-flex">{!! $star !!}</span>@endfor</span>
                    <blockquote class="mt-3 text-ink-900 leading-relaxed flex-1">"{{ $rv['text'] }}"</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <img src="{{ $rv['avatar'] }}" alt="" class="w-10 h-10 rounded-full object-cover bg-slate-100" loading="lazy">
                        <span class="min-w-0">
                            <span class="block font-semibold text-ink-900 text-sm">{{ $rv['name'] }}@if($rv['from'])<span class="font-normal text-slate-500">, {{ $rv['from'] }}</span>@endif</span>
                            <a href="{{ $rv['url'] }}" class="block text-xs text-brand-700 hover:underline truncate">{{ $rv['about'] }}</a>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ OTHER REGIONS ============ --}}
@if($data['others'])
<section class="bg-sand-100 py-16 md:py-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div>
                <p class="pk-eyebrow">Keep exploring</p>
                <h2 class="pk-h2">More regions to discover</h2>
            </div>
            <a href="{{ route('destinations.index') }}" class="rg-btn rg-btn--outline hidden sm:inline-flex">All destinations</a>
        </div>
        <div class="rx-rail no-scrollbar">
            @foreach($data['others'] as $o)
                <a href="{{ $o['url'] }}" class="rx-other group">
                    <img src="{{ $o['image'] }}" alt="{{ $o['name'] }}" loading="lazy">
                    <span class="rx-other__shade"></span>
                    <span class="rx-other__body">
                        <span class="block font-display font-extrabold text-xl leading-tight">{{ $o['name'] }}</span>
                        <span class="block text-xs text-white/80 mt-1">{{ $o['count'] }} guides · {{ $o['from'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    .rx-facts { display: grid; gap: .75rem; border-radius: 1.5rem; background: #fff; box-shadow: 0 30px 60px -30px rgba(11, 27, 43, .35); border: 1px solid #e8edf3; padding: 1rem; }
    @media (min-width: 640px) { .rx-facts { grid-template-columns: repeat(3, minmax(0, 1fr)); } .rx-fact--wide { grid-column: 1 / -1; } }
    @media (min-width: 1024px) { .rx-facts { grid-template-columns: 1fr 1fr 1fr 2fr; padding: 1.25rem; } .rx-fact--wide { grid-column: auto; } }
    .rx-fact { display: flex; gap: .8rem; align-items: flex-start; padding: .5rem .4rem; }
    @media (min-width: 1024px) { .rx-fact + .rx-fact { border-left: 1px solid #eef2f6; padding-left: 1.1rem; } }
    .rx-fact__ico { flex-shrink: 0; width: 2.6rem; height: 2.6rem; border-radius: .9rem; display: inline-flex; align-items: center; justify-content: center; color: var(--c); background: color-mix(in srgb, var(--c) 11%, #fff); }
    .rx-fact__ico svg { width: 1.25rem; height: 1.25rem; }
    .rx-fact__label { font-size: .68rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #64748b; }
    .rx-fact__value { margin-top: .2rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; color: #0b1b2b; line-height: 1.3; }
    .rx-fact__value--sm { font-family: Inter, sans-serif; font-weight: 500; font-size: .9rem; color: #334155; }

    .rx-stays { display: grid; gap: 1rem; grid-template-columns: minmax(0, 1fr); }
    @media (min-width: 640px) { .rx-stays { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .rx-stays { grid-template-columns: repeat(3, minmax(0, 1fr)); } .rx-stay--big { grid-column: span 1; } }
    .rx-stay { display: flex; flex-direction: column; border-radius: 1.4rem; overflow: hidden; background: #fff; border: 1px solid #e3e9f0; transition: box-shadow .3s ease, transform .3s ease; }
    .rx-stay:hover { box-shadow: 0 24px 48px -28px rgba(11, 27, 43, .45); transform: translateY(-3px); }
    .rx-stay__img { display: block; aspect-ratio: 16 / 10; overflow: hidden; background: #e2e8f0; }
    .rx-stay__img img { width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .rx-stay:hover .rx-stay__img img { transform: scale(1.06); }
    .rx-stay__body { flex: 1; display: flex; flex-direction: column; padding: 1.1rem 1.2rem 1.15rem; }
    .rx-stay__tag { align-self: flex-start; border-radius: 999px; background: #fff3ef; color: #d9432a; font-size: .66rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; padding: .2rem .55rem; margin-bottom: .5rem; }
    .rx-stay__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.15rem; color: #0b1b2b; line-height: 1.25; }
    .rx-stay__more { margin-top: auto; padding-top: .9rem; display: inline-flex; align-items: center; gap: .35rem; font-size: .85rem; font-weight: 700; color: #d9432a; }
    @media (min-width: 1024px) {
        .rx-stays:has(.rx-stay--big) { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        .rx-stays:has(.rx-stay--big) .rx-stay { grid-column: span 2; }
        .rx-stays:has(.rx-stay--big) .rx-stay--big { grid-column: span 3; }
        .rx-stay--big .rx-stay__img { aspect-ratio: 16 / 9; }
        .rx-stay--big .rx-stay__name { font-size: 1.45rem; }
    }
    .rx-more > summary { list-style: none; cursor: pointer; }
    .rx-more > summary::-webkit-details-marker { display: none; }

    .rx-collage { position: absolute; object-fit: cover; border-radius: 1.5rem; box-shadow: 0 30px 60px -25px rgba(11, 27, 43, .55); border: 6px solid #fff; }
    .rx-collage--a { left: 0; top: 0; width: 66%; height: 70%; }
    .rx-collage--b { right: 0; top: 14%; width: 44%; height: 48%; }
    .rx-collage--c { left: 20%; bottom: 0; width: 46%; height: 42%; }
    .rx-float { position: absolute; right: 3%; bottom: 5%; background: #fff; border-radius: 1rem; padding: .85rem 1rem; box-shadow: 0 20px 40px -18px rgba(11, 27, 43, .45); max-width: 13rem; }

    .rx-spot { display: flex; flex-direction: column; border-radius: 1.4rem; overflow: hidden; background: #fff; border: 1px solid #e3e9f0; }
    .rx-spot[hidden] { display: none; }
    .rx-spot__img { display: block; aspect-ratio: 4 / 3; overflow: hidden; background: #e2e8f0; }
    .rx-spot__img img { width: 100%; height: 100%; object-fit: cover; }
    .rx-spot__body { flex: 1; display: flex; flex-direction: column; padding: 1.1rem 1.2rem 1.2rem; }

    .rx-rail { display: flex; gap: 1rem; overflow-x: auto; scroll-snap-type: x mandatory; margin: 0 -1rem; padding: .25rem 1rem 1rem; }
    @media (min-width: 640px) { .rx-rail { margin: 0 -1.5rem; padding-left: 1.5rem; padding-right: 1.5rem; } }
    @media (min-width: 1024px) { .rx-rail { margin: 0; padding: .25rem 0 1rem; } }
    .rx-other { position: relative; flex: 0 0 68%; aspect-ratio: 4 / 5; border-radius: 1.4rem; overflow: hidden; scroll-snap-align: start; background: #0c2438; isolation: isolate; color: #fff; }
    @media (min-width: 640px) { .rx-other { flex-basis: 14.5rem; } }
    .rx-other img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .rx-other:hover img { transform: scale(1.07); }
    .rx-other__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7, 18, 29, .05) 40%, rgba(7, 18, 29, .85) 100%); }
    .rx-other__body { position: absolute; left: 1.1rem; right: 1.1rem; bottom: 1.1rem; }
</style>
<script>
(function () {
    var b = document.querySelector('[data-rx-more]');
    if (b) b.addEventListener('click', function () { document.querySelectorAll('[data-rx-extra]').forEach(function (e) { e.hidden = false; }); b.parentNode.remove(); });
})();
</script>
@endsection
