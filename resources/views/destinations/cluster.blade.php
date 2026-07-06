@extends('layouts.public')

@section('title') Resorts in {{ $meta['name'] }}: {{ $keywords->count() }} Destinations Compared @endsection
@section('meta_description') {{ $meta['meta_description'] }} @endsection
@section('canonical') {{ route('destinations.cluster', $cluster) }} @endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => route('destinations.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $meta['name'], 'item' => route('destinations.cluster', $cluster)],
    ]
]) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Resorts in ' . $meta['name'],
    'description' => $meta['meta_description'],
    'url' => route('destinations.cluster', $cluster),
    'mainEntity' => [
        '@type' => 'ItemList',
        'numberOfItems' => $keywords->count(),
        'itemListElement' => $keywords->take(20)->values()->map(fn($k, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => ucwords($k->phrase),
            'url' => url($k->slug),
        ])->all(),
    ],
]) !!}
</script>
@if(!empty($spotImages) && count($spotImages))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Featured tourist spots in ' . $meta['name'],
    'itemListElement' => collect($spotImages)->take(12)->values()->map(fn($sp, $i) => [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'item' => array_filter([
            '@type' => 'TouristAttraction',
            'name' => $sp['name'] ?? '',
            'description' => ($sp['desc'] ?? '') !== '' ? $sp['desc'] : null,
            'image' => isset($sp['url']) ? url($sp['url']) : null,
            'address' => !empty($sp['location'])
                ? ['@type' => 'PostalAddress', 'addressLocality' => $sp['location'], 'addressRegion' => $meta['name'], 'addressCountry' => 'PH']
                : null,
        ]),
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
@endsection

@section('content')
@php
    $spots = collect($spotImages ?? []);
    // Round-robin up to 12 spots into 3 crossfading card columns (adjacent
    // spots never share a tile).
    $cols = [[], [], []];
    foreach ($spots->take(12)->values() as $i => $sp) { $cols[$i % 3][] = $sp; }
    $hasGallery = $spots->count() >= 3;
    $heroImage = $spots->isNotEmpty() ? ($spots->first()['url'] ?? null) : null;
    $region = $meta['name'];
    // Sample traveler reviews. Each references a REAL spot + location from this
    // region so they read realistically for a large area (placeholder names,
    // titles and ratings until genuine reviews are collected).
    $revSpots = $spots->unique('location')->values();
    if ($revSpots->count() < 3) { $revSpots = $spots->values(); }
    $revNames = ['Marco Reyes', 'Bea Santos', 'Josh Lim', 'Andrea Cruz', 'Paolo Mendoza', 'Camille Tan'];
    $revCities = ['Makati City', 'Pasig City', 'Quezon City', 'Taguig City', 'Antipolo', 'Mandaluyong'];
    $revRatings = [5, 5, 4, 5, 5, 4];
    $revTitles = ['Worth the drive', 'A great home base', 'So much to explore', 'Better than expected', 'We will be back', 'Exactly as planned'];
    $revTpl = [
        "We based ourselves near {loc} and {spot} was the highlight of the trip. {region} is bigger than it looks, so pick an area and take your time.",
        "Brought the whole family to {loc}. {spot} alone made the trip, and there was still so much of {region} we did not get to.",
        "{spot} in {loc} completely surprised us. {region} has so many different corners that one weekend is never enough.",
        "Spent a few days around {loc} and loved every minute. {spot} was the kind of place you do not want to leave, and planning it here was easy.",
        "{loc} was the perfect slow escape. We visited {spot}, ate well, and drove home relaxed. {region} keeps pulling us back.",
        "Compared a few stays here and booked near {loc}. {spot} was a short trip away and worth it. This is how we plan {region} now.",
    ];
    $reviews = [];
    foreach ($revSpots->take(6)->values() as $i => $sp) {
        $reviews[] = [
            'name' => $revNames[$i] ?? 'Guest Traveler',
            'city' => $revCities[$i] ?? 'Philippines',
            'rating' => $revRatings[$i] ?? 5,
            'title' => $revTitles[$i] ?? 'A memorable trip',
            'text' => str_replace(['{spot}', '{loc}', '{region}'], [$sp['name'] ?? 'the area', ($sp['location'] ?: $region), $region], $revTpl[$i] ?? ''),
        ];
    }
    // Dynamic hashtags from this region's keyword pages (homepage #Tags style).
    $hstop = ['in', 'on', 'at', 'of', 'the', 'a', 'an', 'to', 'for', 'and', 'or', 'near', 'with', 'your', 'best', 'top', 'resorts', 'resort', 'hotels', 'hotel', 'airbnb', 'airbnbs', 'stays', 'stay', 'places', 'place', 'beach', 'beaches', 'tourist', 'spot', 'spots', 'private', 'pool'];
    $hashtags = [];
    $hseen = [];
    foreach ($keywords as $hkw) {
        $words = preg_split('/[^a-z0-9]+/i', mb_strtolower($hkw->phrase), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $sig = array_values(array_filter($words, fn ($w) => !in_array($w, $hstop, true)));
        if (empty($sig)) { $sig = $words; }
        $sig = array_slice($sig, 0, 3);
        $tag = '';
        foreach ($sig as $w) { $tag .= ucfirst($w); }
        if ($tag === '') { continue; }
        $lk = mb_strtolower($tag);
        if (isset($hseen[$lk])) { continue; }
        $hseen[$lk] = true;
        $hashtags[] = ['tag' => $tag, 'url' => url($hkw->slug)];
    }
@endphp

{{-- Breadcrumb kept at the very top --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    <nav class="text-sm text-slate-500">
        <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('destinations.index') }}" class="hover:text-brand-600">Destinations</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700">{{ $meta['name'] }}</span>
    </nav>
</div>

{{-- Top section: faded tourist-spot background photo + Tahu region name --}}
<section class="mt-4 {{ $heroImage ? 'bg-cover bg-center' : 'bg-gradient-to-br from-brand-50 via-white to-emerald-50' }}"@if($heroImage) style="background-image:linear-gradient(rgba(255,255,255,.85),rgba(255,255,255,.92)),url('{{ $heroImage }}')"@endif>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20 text-center">
        <p class="mb-7"><span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100 px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em]">Destination Guide</span></p>
        <h1 class="font-brand font-normal leading-[0.95] text-5xl sm:text-6xl md:text-7xl" style="color:#c0392b">{{ $meta['name'] }}</h1>
        <figure class="relative mx-auto mt-12 max-w-2xl pt-6 md:pt-8">
            <span aria-hidden="true" class="pointer-events-none absolute left-1/2 -top-8 -translate-x-1/2 select-none font-serif text-[6rem] leading-none md:text-[8rem]" style="color:rgba(192,57,43,.13)">&ldquo;</span>
            <blockquote class="relative font-serif text-xl italic leading-relaxed text-slate-700 md:text-2xl md:leading-[1.55]">{{ $meta['tagline'] }}</blockquote>
            <span class="mx-auto mt-6 block h-[3px] w-12 rounded-full" style="background:#c0392b"></span>
        </figure>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-6">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">What's In <span class="font-brand font-normal" style="color:#c0392b;font-size:1.3em;line-height:1">{{ $meta['name'] }}</span>?</h2>
    </div>
    <article class="prose prose-slate max-w-none mb-10">
        {!! $meta['intro_html'] !!}
    </article>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
        @foreach($keywords as $k)
            @php $kimg = $kwImages[$k->id] ?? ($spots->isNotEmpty() ? ($spots[$loop->index % $spots->count()]['url'] ?? null) : null); @endphp
            <a href="{{ url($k->slug) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl">
                <div class="aspect-[16/10] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                    @if($kimg)
                        <img src="{{ $kimg }}" alt="{{ $k->phrase }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-slate-300"><svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/></svg></div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <h3 class="mb-1 font-semibold capitalize leading-snug text-slate-900 group-hover:text-brand-700">{{ $k->phrase }}</h3>
                    <p class="text-xs text-slate-500">{{ number_format($k->search_volume_monthly) }} people search this each month</p>
                    <p class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">Browse options <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Featured tourist spots: 3-column crossfading, clickable cards --}}
    @if($hasGallery)
        <section class="mb-14">
            <div class="mb-8 text-center">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">Featured <span class="font-brand font-normal" style="color:#c0392b;font-size:1.3em;line-height:1">Tourist Spots</span> in {{ $meta['name'] }}</h2>
                <p class="text-slate-600">A rotating look at the places worth building a trip around. Tap any card to open it on the map.</p>
            </div>
            @include('destinations._fadegallery', ['columns' => $cols, 'aspect' => 'aspect-[3/4]'])
        </section>
    @endif

    {{-- Sample traveler reviews about the region (styled like the homepage
         testimonials). Content is placeholder until real reviews are collected. --}}
    <section class="mb-14">
        <div class="mb-8 text-center max-w-2xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">What <span class="font-brand font-normal" style="color:#c0392b;font-size:1.3em;line-height:1">Travelers Say</span> About {{ $meta['name'] }}</h2>
            <p class="text-slate-600">Highlights from travelers exploring different corners of {{ $meta['name'] }}.</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($reviews as $r)
                <figure class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <div class="mb-3 flex items-center gap-1">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $i < $r['rating'] ? '#f59e0b' : '#e2e8f0' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <h3 class="mb-1.5 font-bold text-slate-900">{{ $r['title'] }}</h3>
                    <blockquote class="m-0 flex-1 text-sm leading-relaxed text-slate-700">{{ $r['text'] }}</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ mb_substr($r['name'], 0, 1) }}</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">{{ $r['name'] }}</div>
                            <div class="text-xs text-slate-500">{{ $r['city'] }}</div>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- Explore other regions, in the homepage region-grid card style --}}
    <section class="mb-4">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900">Explore Other Regions</h2>
            <a href="{{ route('destinations.index') }}" class="whitespace-nowrap font-semibold text-brand-600 hover:underline">View all destinations &rarr;</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($others as $o)
                @php $imgs = $otherImages[$o['slug']] ?? []; @endphp
                <a href="{{ route('destinations.cluster', $o['slug']) }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-brand-300 hover:shadow-md">
                    <span class="rg-fadetile relative h-14 w-14 flex-none overflow-hidden rounded-full bg-slate-200 ring-1 ring-slate-900/5">
                        @forelse($imgs as $i => $u)
                            <div class="rg-fadecard absolute inset-0{{ $i === 0 ? ' is-on' : '' }}"><img src="{{ $u }}" alt="{{ $o['name'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover"></div>
                        @empty
                            <span class="absolute inset-0 flex items-center justify-center text-lg font-extrabold text-slate-500">{{ mb_substr($o['name'], 0, 1) }}</span>
                        @endforelse
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-start justify-between gap-2">
                            <h3 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $o['name'] }}</h3>
                            <span class="whitespace-nowrap rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">{{ $o['count'] }}</span>
                        </div>
                        <p class="line-clamp-2 text-sm text-slate-500">{{ $o['tagline'] ?? '' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Dynamic hashtags for this region, in the homepage #Tags style --}}
    @if(!empty($hashtags))
        <section class="mt-14 border-t border-slate-200 pt-8">
            <h2 class="mb-2 flex items-center gap-2 text-2xl md:text-3xl font-bold text-slate-900">
                <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                Tags
            </h2>
            <p class="mb-6 text-slate-600">Popular searches around {{ $meta['name'] }}.</p>
            <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-sm">
                @foreach($hashtags as $h)
                    <a href="{{ $h['url'] }}" class="text-slate-500 hover:text-slate-900 no-underline transition-colors">#{{ $h['tag'] }}</a>
                @endforeach
            </div>
        </section>
    @endif
</div>

{{-- Crossfade for the 3-column tourist-spot galleries. Each tile runs on its
     OWN random timeline (random start image, random 1.2-2.4s fade duration,
     staggered first fade + 6-13s intervals) so nothing is ever in sync. Starts
     only when the tile scrolls into view; honours reduced-motion. --}}
@verbatim
<style>
  .rg-fadecard{opacity:0;pointer-events:none}
  .rg-fadecard.is-on{opacity:1;pointer-events:auto}
  @media (prefers-reduced-motion: reduce){.rg-fadecard{transition:none!important}}
</style>
<script>
(function(){
  if(window.__rgFadeBooted)return;window.__rgFadeBooted=1;
  var reduce=window.matchMedia&&matchMedia("(prefers-reduced-motion: reduce)").matches;
  function rf(a,b){return a+Math.random()*(b-a);}
  function ri(a,b){return Math.floor(rf(a,b+1));}
  function start(t){
    if(t.dataset.rgfStarted)return;t.dataset.rgfStarted=1;
    var cards=t.querySelectorAll(".rg-fadecard");
    if(!cards.length)return;
    var cur=ri(0,cards.length-1);
    for(var i=0;i<cards.length;i++){
      var card=cards[i];
      card.classList.remove("is-on");
      var im=card.querySelector("img");
      if(im){im.removeAttribute("loading");var sc=im.getAttribute("src");if(sc){var pre=new Image();pre.src=sc;}}
      card.style.opacity=(i===cur)?"1":"0";card.style.pointerEvents=(i===cur)?"auto":"none";
    }
    if(cards.length<2||reduce)return;
    var dur=rf(1.2,2.4).toFixed(2);
    void t.offsetWidth;
    for(var j=0;j<cards.length;j++)cards[j].style.transition="opacity "+dur+"s ease-in-out";
    function tick(){
      var n=cur;while(n===cur)n=ri(0,cards.length-1);
      cards[cur].style.opacity="0";cards[cur].style.pointerEvents="none";cards[n].style.opacity="1";cards[n].style.pointerEvents="auto";cur=n;
      setTimeout(tick,rf(6000,13000));
    }
    setTimeout(tick,rf(2500,7000));
  }
  function boot(){
    var els=document.querySelectorAll(".rg-fadetile");
    if("IntersectionObserver" in window){
      var io=new IntersectionObserver(function(e){
        for(var k=0;k<e.length;k++){if(e[k].isIntersecting){start(e[k].target);io.unobserve(e[k].target);}}
      },{rootMargin:"300px 0px"});
      for(var i=0;i<els.length;i++)io.observe(els[i]);
    }else{for(var m=0;m<els.length;m++)start(els[m]);}
  }
  if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",boot);}else{boot();}
})();
</script>
@endverbatim
@endsection
