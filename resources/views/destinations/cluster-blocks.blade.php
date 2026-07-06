{{--
    Block-stream wrapper for a destination cluster page. Used by
    DestinationsController@cluster when the shared `destination-cluster`
    template row has blocks attached. The blocks are rendered per-cluster
    with the cluster context; this wrapper only carries the page chrome,
    dynamic JSON-LD schema, and Live Editor asset injection.
--}}
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
    ],
], JSON_UNESCAPED_SLASHES) !!}
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
        'itemListElement' => $keywords->take(20)->values()->map(fn ($k, $i) => [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => ucwords($k->phrase),
            'url' => url($k->slug),
        ])->all(),
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
@if(!empty($spotImages) && count($spotImages))
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Featured tourist spots in ' . $meta['name'],
    'itemListElement' => collect($spotImages)->take(12)->values()->map(fn ($sp, $i) => [
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

@if(!empty($liveEdit))
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/rg-live-edit.css') }}?v=1">
        <script defer src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            window.__rgLiveEdit = {
                pageId: {{ (int) $page->id }},
                slug: {!! json_encode($page->slug) !!},
                ownerType: 'static_page'
            };
        </script>
        <script defer src="{{ asset('js/rg-live-edit.js') }}?v=2"></script>
    @endpush
@endif

@section('content')
<div class="page-{{ $page->slug }}">
    {!! $renderedBlocks !!}
</div>

{{-- Crossfade for the block-rendered tourist-spot tiles / region circles.
     Each tile runs its own random timeline; only the visible card is clickable. --}}
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
