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
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => url('/destinations')],
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
@endsection

@section('content')
<section class="bg-gradient-to-br from-brand-50 via-white to-emerald-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ url('/destinations') }}" class="hover:text-brand-600">Destinations</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700">{{ $meta['name'] }}</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-3">Resorts in {{ $meta['name'] }}</h1>
        <p class="text-lg text-slate-600">{{ $meta['tagline'] }}</p>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <article class="prose prose-slate max-w-none mb-10">
        {!! $meta['intro_html'] !!}
    </article>

    <h2 class="text-2xl font-bold text-slate-900 mb-5">{{ $keywords->count() }} destinations in {{ $meta['name'] }}</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-12 rg-stagger">
        @foreach($keywords as $k)
            <a href="{{ url($k->slug) }}" class="group block p-5 rounded-xl border border-slate-200 hover:border-brand-300 hover:shadow-md hover:bg-brand-50/30 rg-card-lift">
                <h3 class="font-semibold text-slate-900 group-hover:text-brand-700 capitalize mb-1">{{ $k->phrase }}</h3>
                <p class="text-xs text-slate-500">{{ number_format($k->search_volume_monthly) }} monthly searches</p>
                <p class="text-sm text-brand-600 mt-2 font-medium">Browse options &rarr;</p>
            </a>
        @endforeach
    </div>

    <section class="bg-slate-50 rounded-xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Explore other regions</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 rg-stagger">
            @foreach($others as $o)
                <a href="{{ route('destinations.cluster', $o['slug']) }}" class="block px-4 py-3 rounded-md bg-white border border-slate-200 hover:border-brand-300 hover:text-brand-700 text-sm">
                    <strong class="block">{{ $o['name'] }}</strong>
                    <span class="text-xs text-slate-500">{{ $o['count'] }} destinations</span>
                </a>
            @endforeach
        </div>
        <p class="mt-4 text-sm"><a href="{{ url('/destinations') }}" class="text-brand-600 font-semibold hover:underline">View all destinations &rarr;</a></p>
    </section>

    <section class="mt-12 text-center bg-brand-50 rounded-xl p-8 border border-brand-100">
        <h2 class="text-xl font-bold text-slate-900 mb-2">Run a resort in {{ $meta['name'] }}?</h2>
        <p class="text-slate-600 mb-4">Get featured on any of the destination pages above.</p>
        <a href="{{ route('register') }}" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">List your property</a>
    </section>
</div>
@endsection
