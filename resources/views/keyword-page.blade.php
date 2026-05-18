@extends('layouts.public')

@section('title') {{ $page->meta_title ?: $page->title }} @endsection
@section('meta_description') {{ $page->meta_description }} @endsection
@section('meta_keywords') {{ $page->meta_keywords }} @endsection
@section('canonical') {{ $page->canonical_url ?: url($page->slug) }} @endsection
@if($page->robots) <meta name="robots" content="{{ $page->robots }}"> @endif
@if($page->og_image_path) @section('og_image') {{ asset('storage/' . $page->og_image_path) }} @endsection @endif

@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="text-sm text-slate-500 mb-6">
        <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ url('/destinations') }}" class="hover:text-brand-600">Destinations</a>
        @if($cluster)
            <span class="mx-2">/</span>
            <a href="{{ route('destinations.cluster', $keyword->cluster_tag) }}" class="hover:text-brand-600">{{ $cluster['name'] }}</a>
        @endif
        <span class="mx-2">/</span>
        <span class="text-slate-700 capitalize">{{ $keyword->phrase }}</span>
    </nav>

    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-6 capitalize">{{ $page->h1 ?: $page->title }}</h1>

    @php $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0; @endphp

    @if($hasBlocks)
        {{-- New block builder output (includes inline listing_slot blocks) --}}
        {!! $renderedBlocks !!}

        @if(!$hasListingSlot)
            {{-- No listing_slot block — render listings section separately --}}
            <section class="my-12">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Featured properties</h2>
                @if($listings->count() === 0)
                    <div class="border border-slate-200 rounded-lg p-8 bg-slate-50 text-center">
                        <p class="text-slate-600 mb-4">No featured properties for <strong>{{ $keyword->phrase }}</strong> just yet.</p>
                        <a href="{{ route('register') }}" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Be the first to list here</a>
                    </div>
                @else
                    @include('partials.listings-grid', ['listings' => $listings])
                @endif
            </section>
        @endif
    @else
        {{-- Legacy fallback (intro_html + listings + body_html) --}}
        @if($page->intro_html)
            <div class="prose prose-slate max-w-none mb-10">{!! $page->intro_html !!}</div>
        @endif

        <section class="my-12">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Featured properties</h2>
            @if($listings->count() === 0)
                @if($page->fallback_listing_html)
                    <div class="prose prose-slate max-w-none border border-slate-200 rounded-lg p-6 bg-slate-50">{!! $page->fallback_listing_html !!}</div>
                @else
                    <div class="border border-slate-200 rounded-lg p-8 bg-slate-50 text-center">
                        <p class="text-slate-600 mb-4">No featured properties for <strong>{{ $keyword->phrase }}</strong> just yet.</p>
                        <a href="{{ route('register') }}" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Be the first to list here</a>
                    </div>
                @endif
            @else
                @include('partials.listings-grid', ['listings' => $listings])
            @endif
        </section>

        @if($page->body_html)
            <div class="prose prose-slate max-w-none">{!! $page->body_html !!}</div>
        @endif
    @endif

    @if(!empty($faqs) && !$hasBlocks)
        <section class="my-12">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Frequently asked questions</h2>
            <div class="space-y-3">
                @foreach($faqs as $i => $f)
                    <details class="border border-slate-200 rounded-lg group" {{ $i === 0 ? 'open' : '' }}>
                        <summary class="cursor-pointer p-4 font-semibold text-slate-900 flex items-center justify-between">
                            <span>{{ $f['question'] ?? '' }}</span>
                            <svg class="w-5 h-5 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="px-4 pb-4 text-slate-700 prose max-w-none">{!! nl2br(e($f['answer'] ?? '')) !!}</div>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

    @if($related->isNotEmpty() && $cluster)
        <section class="mt-14 pt-10 border-t border-slate-200">
            <div class="flex items-end justify-between mb-5 flex-wrap gap-2">
                <h2 class="text-2xl font-bold text-slate-900">Other destinations in {{ $cluster['name'] }}</h2>
                <a href="{{ route('destinations.cluster', $keyword->cluster_tag) }}" class="text-sm text-brand-600 font-semibold hover:underline">All {{ $cluster['name'] }} destinations &rarr;</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($related as $r)
                    <a href="{{ url($r->slug) }}" class="block group p-4 rounded-lg border border-slate-200 hover:border-brand-300 hover:bg-brand-50/30 transition">
                        <h3 class="font-semibold text-slate-900 group-hover:text-brand-700 capitalize text-sm">{{ $r->phrase }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ number_format($r->search_volume_monthly) }} searches/mo</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="mt-12 p-6 rounded-xl bg-slate-50 border border-slate-200 text-center">
        <h3 class="text-xl font-bold mb-2">Run a resort here?</h3>
        <p class="text-slate-600 mb-4">Get your property featured on this page.</p>
        <a href="{{ route('register') }}" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">List your property</a>
    </div>
</article>
@endsection
