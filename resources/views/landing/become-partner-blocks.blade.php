{{--
    Block-stream wrapper for the /become-a-partner page. Used by
    PartnerPageController when the `become-a-partner` static_page row has
    blocks attached. The partner_* blocks are full-bleed sections that
    manage their own width, so the stream renders directly in <main> with
    no article/max-width wrapper.
--}}
@extends('layouts.public')

@section('title', $page->meta_title ?: 'Become a Partner · Tourist Guide Ph')
@section('meta_description', $page->meta_description ?? '')
@section('canonical', url('/become-a-partner'))

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
{!! $renderedBlocks !!}
@endsection
