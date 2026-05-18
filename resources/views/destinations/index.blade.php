@extends('layouts.public')

@section('title') All Destinations Across the Philippines | {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }} @endsection
@section('meta_description') Browse all {{ $stats['total_destinations'] }} resort, hotel, and beach destinations across the Philippines, organized by region. From Batangas to Mindanao. @endsection
@section('canonical') {{ url('/destinations') }} @endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Destinations', 'item' => url('/destinations')],
    ]
]) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'All Destinations',
    'description' => 'Browse resort and hotel destinations across the Philippines',
    'url' => url('/destinations'),
]) !!}
</script>
@endsection

@section('content')
<section class="bg-gradient-to-br from-brand-50 via-white to-emerald-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <nav class="text-sm text-slate-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700">Destinations</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">All destinations across the Philippines</h1>
        <p class="text-lg text-slate-600 max-w-3xl">Browse every region we cover. {{ number_format($stats['total_destinations']) }} destinations organized into {{ $stats['total_regions'] }} regional clusters, from Luzon\'s ridge towns to Mindanao\'s quiet beaches.</p>
    </div>
</section>

<section class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Jump to region</h2>
        <div class="flex flex-wrap gap-2 mb-12">
            @foreach($orderedClusters as $c)
                <a href="#{{ $c['slug'] }}" class="px-3 py-1.5 text-sm rounded-full bg-slate-100 hover:bg-brand-100 hover:text-brand-700 text-slate-700 transition">
                    {{ $c['name'] }} <span class="text-slate-500">({{ $c['count'] }})</span>
                </a>
            @endforeach
        </div>

        @foreach($orderedClusters as $cluster)
            <section id="{{ $cluster['slug'] }}" class="mb-14 scroll-mt-20">
                <div class="flex items-end justify-between mb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">
                            <a href="{{ route('destinations.cluster', $cluster['slug']) }}" class="hover:text-brand-600">{{ $cluster['name'] }}</a>
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $cluster['tagline'] }}</p>
                    </div>
                    <a href="{{ route('destinations.cluster', $cluster['slug']) }}" class="text-sm text-brand-600 font-semibold hover:underline">{{ $cluster['count'] }} destinations &rarr;</a>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 rg-stagger">
                    @foreach($cluster['keywords'] as $k)
                        <a href="{{ url($k->slug) }}" class="group block p-4 rounded-lg border border-slate-200 hover:border-brand-300 hover:shadow-md hover:bg-brand-50/30 rg-card-lift">
                            <h3 class="font-semibold text-slate-900 group-hover:text-brand-700 capitalize">{{ $k->phrase }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ number_format($k->search_volume_monthly) }} monthly searches</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl font-bold text-slate-900 mb-3">Run a property in any of these regions?</h2>
        <p class="text-slate-600 mb-5">List your resort, hotel, or beach house on the destination pages your future guests are already searching.</p>
        <a href="{{ route('register') }}" class="inline-block px-6 py-3 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Start listing free</a>
    </div>
</section>
@endsection
