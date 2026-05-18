@extends('layouts.public')

@section('title') {{ $post->meta_title ?: $post->title }} @endsection
@section('meta_description') {{ $post->meta_description ?: $post->excerpt }} @endsection
@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a>
    </nav>

    <h1 class="text-4xl font-extrabold text-slate-900 mb-4">{{ $post->title }}</h1>
    <p class="text-slate-500 mb-8">{{ optional($post->published_at)->format('F j, Y') }}</p>

    @if($post->cover_path)
        <div class="aspect-[16/9] rounded-lg bg-slate-200 mb-8 overflow-hidden">
            <img src="{{ asset('storage/' . $post->cover_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
    @endif

    @php $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0; @endphp
    @if($hasBlocks)
        {!! $renderedBlocks !!}
    @else
        <div class="prose prose-slate max-w-none">{!! $post->content_html !!}</div>
    @endif
</article>

@if($related->isNotEmpty())
<section class="bg-slate-50 py-12 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Keep reading</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach($related as $r)
                <a href="{{ route('blog.show', $r->slug) }}" class="block group">
                    <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 mb-2">{{ $r->title }}</h3>
                    <p class="text-sm text-slate-500 line-clamp-2">{{ $r->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
