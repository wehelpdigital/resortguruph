{{--
    Block-stream rendering wrapper for /destinations. Used by
    DestinationsController@index when the `destinations` static_page
    row has blocks attached. Includes Live Editor asset injection
    when the request carried a valid HMAC _lt token validated by
    the controller.
--}}
@extends('layouts.public')

@section('title', $page->meta_title ?: ($page->title ?? 'Destinations'))
@section('meta_description', $page->meta_description ?? '')
@section('canonical', route('destinations.index'))

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => route('destinations.index')],
    ]
]) !!}
</script>
@endsection

@if(!empty($liveEdit))
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/rg-live-edit.css') }}?v=1">
        <script defer src="/vendor/sortable/Sortable.min.js"></script>
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
@php $pullHero = \App\Support\PageKit::leadsWithDarkHero($renderedBlocks ?? ''); @endphp
@if($pullHero) @section('header_overlay', '1') @endif
<article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 page-{{ $page->slug }} {{ $pullHero ? 'pk-pull' : '' }}">
    {!! $renderedBlocks !!}
</article>
@endsection
