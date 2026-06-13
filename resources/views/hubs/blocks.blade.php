{{--
    Shared block-stream wrapper for the 4 hub pages
    (foods, activities, buys, cultures). Used by each hub
    controller via RendersBlockableHub trait.
--}}
@extends('layouts.public')

@section('title', $page->meta_title ?: ($page->title ?? 'Tourist Guide Ph'))
@section('meta_description', $page->meta_description ?? '')

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
        <script defer src="{{ asset('js/rg-live-edit.js') }}?v=1"></script>
    @endpush
@endif

@section('content')
<article class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 hub-{{ $page->slug }}">
    {!! $renderedBlocks !!}
</article>
@endsection
