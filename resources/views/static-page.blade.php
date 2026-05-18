@extends('layouts.public')

@section('title') {{ $page->meta_title ?: $page->title }} @endsection
@section('meta_description') {{ $page->meta_description }} @endsection

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-6">{{ $page->title }}</h1>
    @php $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0; @endphp
    @if($hasBlocks)
        {!! $renderedBlocks !!}
    @else
        <div class="prose prose-slate max-w-none">{!! $page->content_html !!}</div>
    @endif
</article>
@endsection
