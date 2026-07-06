{{--
    Block-stream wrapper for /food-trip. Used by
    FoodTripController@index when the `food-trip` static_page row
    has blocks attached. Includes Live Editor asset injection
    when the request carried a valid HMAC _lt token.
--}}
@extends('layouts.public')

@section('title', $page->meta_title ?: ($page->title ?? 'Food Trip'))
@section('meta_description', $page->meta_description ?? '')
@section('canonical', url('/food-trip'))

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Food Trip', 'item' => url('/food-trip')],
    ]
]) !!}
</script>
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
<article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 page-food-trip">
    {!! $renderedBlocks !!}
</article>
@endsection
