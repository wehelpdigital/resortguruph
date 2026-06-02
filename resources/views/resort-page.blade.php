@extends('layouts.public')

@section('title') {{ $resort->name }} — {{ $resort->city }}, {{ $resort->province }} @endsection
@section('meta_description') {{ $resort->tagline ?: ($resort->name . ' in ' . $resort->city . ', ' . $resort->province) }} @endsection

@push('head')
<style>
    .brand-primary { background-color: {{ $resort->primary_color }} !important; }
    .brand-primary-text { color: {{ $resort->primary_color }} !important; }
    .brand-primary-border { border-color: {{ $resort->primary_color }} !important; }
    .brand-secondary { background-color: {{ $resort->secondary_color }} !important; }
    .brand-secondary-text { color: {{ $resort->secondary_color }} !important; }
</style>
@endpush

@section('jsonld')
{!! $jsonld ?? '' !!}
@endsection

@section('content')
<section class="relative">
    @if($resort->hero_path)
        <div class="h-72 md:h-96 bg-slate-300 overflow-hidden">
            <img src="{{ asset('storage/' . $resort->hero_path) }}" alt="{{ $resort->name }}" class="w-full h-full object-cover">
        </div>
    @else
        <div class="h-48 md:h-64 brand-primary"></div>
    @endif
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 md:-mt-20 relative z-10">
    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border-t-4 brand-primary-border">
        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
            @if($resort->logo_path)
                <img src="{{ asset('storage/' . $resort->logo_path) }}" alt="" class="w-20 h-20 rounded-lg object-cover">
            @endif
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-1">{{ $resort->name }}</h1>
                <p class="text-slate-600">{{ $resort->city }}, {{ $resort->province }}, Philippines</p>
            </div>
        </div>
        @if($resort->tagline)
            <p class="text-lg text-slate-700 italic mb-2">{{ $resort->tagline }}</p>
        @endif

        <div class="flex flex-wrap gap-2 mt-4 text-sm">
            @if($resort->price_range)<span class="px-3 py-1 rounded-full bg-slate-100">{{ $resort->price_range }}</span>@endif
            @if($resort->capacity)<span class="px-3 py-1 rounded-full bg-slate-100">Capacity: {{ $resort->capacity }}</span>@endif
        </div>

        @include('partials.social-share', ['url' => url()->current(), 'title' => $resort->name . ' — Resort Guru PH', 'align' => 'start'])
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-10">
        @if($resort->description_html)
            <section>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">About</h2>
                <div class="prose prose-slate max-w-none">{!! $resort->description_html !!}</div>
            </section>
        @endif

        @if($resort->amenities)
            <section>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Amenities</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($resort->amenities as $a)
                        <span class="px-3 py-2 rounded-md border border-slate-200 text-sm">✓ {{ $a }}</span>
                    @endforeach
                </div>
            </section>
        @endif

        @if($resort->media->isNotEmpty())
            <section>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Gallery</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($resort->media as $m)
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-200">
                            <img src="{{ asset('storage/' . $m->path) }}" alt="{{ $m->caption }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($resort->lat && $resort->lng)
            <section>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Location</h2>
                <div class="aspect-[16/9] rounded-lg overflow-hidden border border-slate-200">
                    <iframe class="w-full h-full" loading="lazy" allowfullscreen src="https://www.google.com/maps?q={{ $resort->lat }},{{ $resort->lng }}&output=embed"></iframe>
                </div>
            </section>
        @endif
    </div>

    <aside class="space-y-5">
        <div class="rounded-xl border border-slate-200 p-5 bg-white shadow-sm">
            <h3 class="font-bold text-slate-900 mb-4">Contact</h3>
            <ul class="text-sm space-y-2">
                @if($resort->phone)<li><strong>Phone:</strong> <a href="tel:{{ $resort->phone }}" class="brand-primary-text">{{ $resort->phone }}</a></li>@endif
                @if($resort->email)<li><strong>Email:</strong> <a href="mailto:{{ $resort->email }}" class="brand-primary-text">{{ $resort->email }}</a></li>@endif
                @if($resort->website)<li><strong>Website:</strong> <a href="{{ $resort->website }}" target="_blank" rel="noopener" class="brand-primary-text">Visit site</a></li>@endif
                @if($resort->address)<li><strong>Address:</strong> {{ $resort->address }}</li>@endif
            </ul>
            <div class="flex gap-2 mt-4">
                @if($resort->fb)<a href="{{ $resort->fb }}" target="_blank" class="w-9 h-9 inline-flex items-center justify-center rounded-full brand-primary text-white">f</a>@endif
                @if($resort->ig)<a href="{{ $resort->ig }}" target="_blank" class="w-9 h-9 inline-flex items-center justify-center rounded-full brand-primary text-white">IG</a>@endif
                @if($resort->tt)<a href="{{ $resort->tt }}" target="_blank" class="w-9 h-9 inline-flex items-center justify-center rounded-full brand-primary text-white">TT</a>@endif
            </div>
        </div>

        <div class="rounded-xl brand-primary text-white p-5">
            <h3 class="font-bold text-lg mb-2">Plan your stay</h3>
            <p class="text-sm opacity-90 mb-4">Reach out directly to confirm availability and rates.</p>
            @if($resort->phone)
                <a href="tel:{{ $resort->phone }}" class="block text-center bg-white brand-primary-text font-semibold py-2.5 rounded-md hover:bg-slate-50">Call now</a>
            @endif
        </div>
    </aside>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pb-12">
    <a href="{{ url('/') }}" class="text-slate-500 hover:text-brand-600">&larr; Back to all destinations</a>
</div>
@endsection
