{{--
    Block-stream wrapper for the homepage. Used by HomeController@index
    when the `home` static_page row has blocks attached. The
    block stream itself is full-bleed (each home_* block uses
    vw-based negative margins to span the viewport), so there's no
    article wrapper here — the body is just the rendered stream.
--}}
@extends('layouts.public')

@section('title', $page->meta_title ?: ($page->title ?? 'Tourist Guide Ph'))
@section('meta_description', $page->meta_description ?? '')
@section('jsonld') {!! $jsonld ?? '' !!} @endsection

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
{{-- Align the homepage section containers (max-w-6xl) with the header
     nav, which uses max-w-7xl (80rem). Same padding + same max-width
     means the content edges line up with the navigation edges. Scoped
     to .page-home so other pages keep their max-w-6xl content width. --}}
<style>.page-home .max-w-6xl{max-width:80rem}.page-home section h1,.page-home section h2{text-transform:capitalize}</style>
<div class="page-home">
    {!! $renderedBlocks !!}
</div>
@endsection
