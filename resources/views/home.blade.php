@extends('layouts.public')

@section('title') {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }} — Find resorts, hotels, and beach getaways in the Philippines @endsection
@section('meta_description') Compare resorts, hotels, and Airbnb stays across the Philippines. Curated guides for every region, from Palawan beaches to Tagaytay mountain views. @endsection

@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@section('content')
<section class="bg-gradient-to-br from-brand-50 via-white to-emerald-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 mb-5">
            Discover your next stay <br class="hidden md:block"> across the Philippines.
        </h1>
        <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-8">
            {{ \App\Models\RgSetting::get('site_tagline', 'Compare resorts, hotels, and Airbnb stays across the islands.') }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="#destinations" class="px-6 py-3 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Browse destinations</a>
            <a href="{{ route('register') }}" class="px-6 py-3 rounded-md bg-white border border-slate-300 font-semibold hover:bg-slate-50">List your resort</a>
        </div>
        <div class="mt-10 flex flex-wrap justify-center gap-8 text-sm text-slate-600">
            <div><strong class="text-2xl text-slate-900 block">{{ number_format($stats['pages']) }}+</strong> destinations</div>
            <div><strong class="text-2xl text-slate-900 block">{{ number_format($stats['resorts']) }}+</strong> verified properties</div>
            <div><strong class="text-2xl text-slate-900 block">7,641</strong> islands</div>
        </div>
    </div>
</section>

<section id="destinations" class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 flex-wrap gap-3">
            <div>
                <h2 class="text-3xl font-bold text-slate-900">Popular destinations</h2>
                <p class="text-slate-600 mt-1">The most-searched places we cover this month.</p>
            </div>
            <a href="{{ url('/destinations') }}" class="text-brand-600 font-semibold hover:underline whitespace-nowrap">View all {{ number_format($stats['pages']) }} destinations &rarr;</a>
        </div>
        @if($featuredKeywords->isEmpty())
            <p class="text-slate-500 text-center py-12 border border-dashed border-slate-200 rounded-lg">No destinations published yet. Check back soon.</p>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 rg-stagger">
                @foreach($featuredKeywords as $k)
                    <a href="{{ url($k->slug) }}" class="block group rounded-xl border border-slate-200 hover:border-brand-300 hover:shadow-md p-5 rg-card-lift">
                        <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 mb-2 capitalize">{{ $k->phrase }}</h3>
                        <p class="text-sm text-slate-500">Browse top picks &rarr;</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if(isset($regions) && $regions->isNotEmpty())
<section class="py-16 rg-reveal bg-gradient-to-b from-white to-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8 flex-wrap gap-3">
            <div>
                <h2 class="text-3xl font-bold text-slate-900">Browse by region</h2>
                <p class="text-slate-600 mt-1">{{ $regions->count() }} regional clusters across Luzon, Visayas, Mindanao, and Palawan.</p>
            </div>
            <a href="{{ url('/destinations') }}" class="text-brand-600 font-semibold hover:underline whitespace-nowrap">All regions &rarr;</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 rg-stagger">
            @foreach($regions as $region)
                <a href="{{ route('destinations.cluster', $region['slug']) }}" class="block group rounded-xl bg-white border border-slate-200 hover:border-brand-300 hover:shadow-md p-5 rg-card-lift">
                    <div class="flex items-start justify-between mb-2 gap-2">
                        <h3 class="font-bold text-lg text-slate-900 group-hover:text-brand-600">{{ $region['name'] }}</h3>
                        <span class="text-xs px-2 py-1 rounded-full bg-brand-100 text-brand-700 font-semibold whitespace-nowrap">{{ $region['count'] }}</span>
                    </div>
                    <p class="text-sm text-slate-500 line-clamp-2">{{ $region['tagline'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($featuredResorts->isNotEmpty())
<section class="py-16 rg-reveal bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">Featured properties</h2>
        <p class="text-slate-600 mb-8">Recently approved resorts, hotels, and beach houses.</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 rg-stagger">
            @foreach($featuredResorts as $r)
                <a href="{{ route('resort.show', $r->slug) }}" class="block group rounded-xl overflow-hidden bg-white border border-slate-200 hover:shadow-lg rg-card-lift">
                    <div class="aspect-[4/3] bg-slate-200 overflow-hidden">
                        @if($r->hero_path)
                            <img src="{{ asset('storage/' . $r->hero_path) }}" alt="{{ $r->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl">🏖️</div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-slate-900 mb-1">{{ $r->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $r->city }}, {{ $r->province }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($latestPosts->isNotEmpty())
<section class="py-16 rg-reveal">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">From the blog</h2>
        <p class="text-slate-600 mb-8">Travel tips, destination guides, and resort stories.</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 rg-stagger">
            @foreach($latestPosts as $p)
                <a href="{{ route('blog.show', $p->slug) }}" class="block group">
                    <div class="aspect-[16/10] rounded-lg bg-slate-200 mb-3 overflow-hidden">
                        @if($p->cover_path)
                            <img src="{{ asset('storage/' . $p->cover_path) }}" alt="{{ $p->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                    </div>
                    <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 mb-1">{{ $p->title }}</h3>
                    <p class="text-sm text-slate-500 line-clamp-2">{{ $p->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="bg-brand-600 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Run a resort, hotel, or beach house?</h2>
        <p class="text-brand-100 mb-6 text-lg">Get featured on the destination pages your future guests are already searching.</p>
        <a href="{{ route('register') }}" class="inline-block px-7 py-3 rounded-md bg-white text-brand-700 font-bold hover:bg-brand-50">Start listing free</a>
    </div>
</section>
@endsection
