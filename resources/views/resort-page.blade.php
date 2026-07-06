@extends('layouts.public')

@section('title') {{ $resort->name }} — {{ $resort->city }}, {{ $resort->province }} | Tourist Guide Ph @endsection
@section('meta_description') {{ $resort->tagline ?: ($resort->name . ' in ' . $resort->city . ', ' . $resort->province) }} @endsection

@push('head')
<style>
    .brand-primary { background-color: {{ $resort->primary_color }} !important; }
    .brand-primary-text { color: {{ $resort->primary_color }} !important; }
    .brand-primary-border { border-color: {{ $resort->primary_color }} !important; }
    .brand-secondary { background-color: {{ $resort->secondary_color }} !important; }
    .brand-secondary-text { color: {{ $resort->secondary_color }} !important; }
    .brand-primary-ring { box-shadow: 0 0 0 3px {{ $resort->primary_color }}33; }
    /* Cinematic hero — taller than the old h-96, with the title
       overlaid on the bottom of the photo. Photo gets a soft top
       gradient for the nav bar legibility and a stronger bottom
       gradient for the title text. */
    .rg-resort-hero {
        position: relative;
        height: 65vh;
        min-height: 28rem;
        max-height: 38rem;
        background: linear-gradient(180deg, {{ $resort->primary_color }} 0%, {{ $resort->secondary_color ?: '#0f172a' }} 100%);
        overflow: hidden;
    }
    .rg-resort-hero img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .rg-resort-hero__scrim {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.45) 0%, rgba(0,0,0,0.05) 30%, rgba(0,0,0,0.05) 55%, rgba(0,0,0,0.75) 100%);
        pointer-events: none;
    }
    .rg-resort-hero__content {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 2.5rem 1rem 2.5rem;
        color: #fff;
    }
    /* Sticky sidebar — sits below the nav (top-24) on desktop so
       the contact + CTA card stays visible while scrolling the
       long detail column. */
    @media (min-width: 1024px) {
        .rg-resort-sidebar { position: sticky; top: 5.5rem; }
    }
</style>
@endpush

@section('jsonld')
{!! $jsonld ?? '' !!}
@endsection

@section('content')
{{-- Cinematic full-bleed hero with title overlaid --}}
<section class="rg-resort-hero" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">
    @if($resort->hero_path)
        <img src="{{ asset('storage/' . $resort->hero_path) }}" alt="{{ $resort->name }}" loading="eager">
    @endif
    <div class="rg-resort-hero__scrim"></div>

    <div class="rg-resort-hero__content">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-xs sm:text-sm text-white/85 mb-4 flex items-center gap-1.5 flex-wrap">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-white/60">/</span>
                <a href="{{ route('destinations.index') }}" class="hover:text-white transition-colors">Destinations</a>
                <span class="text-white/60">/</span>
                <span class="text-white">{{ $resort->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end gap-5">
                @if($resort->logo_path)
                    <img src="{{ asset('storage/' . $resort->logo_path) }}" alt="" class="w-20 h-20 rounded-xl object-cover bg-white/10 ring-2 ring-white/30 shadow-lg flex-shrink-0">
                @endif
                <div class="flex-1 min-w-0">
                    @if($resort->price_range)
                        <span class="inline-block px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-sm text-xs font-bold uppercase tracking-wider text-white mb-3">{{ $resort->price_range }}</span>
                    @endif
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05] tracking-[-0.015em] text-white mb-3 max-w-3xl" style="text-shadow:0 2px 16px rgba(0,0,0,0.4)">{{ $resort->name }}</h1>
                    <div class="flex items-center gap-2 text-white/95 text-base sm:text-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        <span>{{ $resort->city }}, {{ $resort->province }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Tagline strip — sits just under the hero with a thin accent rule above --}}
@if($resort->tagline)
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex items-start gap-4">
            <span class="block w-1 brand-primary self-stretch rounded-full mt-1 flex-shrink-0" style="min-height:3rem"></span>
            <p class="text-xl md:text-2xl text-slate-700 font-serif italic leading-snug max-w-3xl">{{ $resort->tagline }}</p>
        </div>
    </div>
@endif

{{-- Quick-action chip row (capacity + social share + back to destinations) --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
    <div class="flex flex-wrap items-center gap-3">
        @if($resort->capacity)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m6-9.13a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm6 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                Capacity {{ $resort->capacity }}
            </span>
        @endif
        @include('partials.social-share', ['url' => url()->current(), 'title' => $resort->name . ' — Tourist Guide Ph', 'align' => 'start'])
    </div>
</div>

{{-- Main two-column layout: detail column left, sticky CTA sidebar right --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 grid lg:grid-cols-3 gap-10">
    <div class="lg:col-span-2 space-y-12">
        @if($resort->description_html)
            <section>
                <div class="mb-5 flex items-center gap-3">
                    <span class="block h-[3px] w-10 brand-primary rounded-full"></span>
                    <span class="text-xs uppercase tracking-[0.18em] font-bold brand-primary-text">Overview</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-5">About {{ $resort->name }}</h2>
                <div class="prose prose-lg prose-slate max-w-none leading-relaxed">{!! $resort->description_html !!}</div>
            </section>
        @endif

        @if($resort->amenities)
            <section>
                <div class="mb-5 flex items-center gap-3">
                    <span class="block h-[3px] w-10 brand-primary rounded-full"></span>
                    <span class="text-xs uppercase tracking-[0.18em] font-bold brand-primary-text">What's included</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-6">Amenities</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($resort->amenities as $a)
                        <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-white border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors">
                            <svg class="w-5 h-5 brand-primary-text flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            <span class="text-sm font-medium text-slate-800">{{ $a }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($resort->media->isNotEmpty())
            <section>
                <div class="mb-5 flex items-center gap-3">
                    <span class="block h-[3px] w-10 brand-primary rounded-full"></span>
                    <span class="text-xs uppercase tracking-[0.18em] font-bold brand-primary-text">See the property</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-6">Gallery</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($resort->media as $idx => $m)
                        <a href="{{ asset('storage/' . $m->path) }}" target="_blank" rel="noopener" class="block group relative aspect-square rounded-xl overflow-hidden bg-slate-200 {{ $idx === 0 ? 'md:col-span-2 md:row-span-2 md:aspect-square' : '' }}">
                            <img src="{{ asset('storage/' . $m->path) }}" alt="{{ $m->caption }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @if($m->caption)
                                <div class="absolute inset-x-0 bottom-0 p-3 text-xs text-white bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">{{ $m->caption }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if($resort->lat && $resort->lng)
            <section>
                <div class="mb-5 flex items-center gap-3">
                    <span class="block h-[3px] w-10 brand-primary rounded-full"></span>
                    <span class="text-xs uppercase tracking-[0.18em] font-bold brand-primary-text">Finding the place</span>
                </div>
                <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Location</h2>
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $resort->lat }},{{ $resort->lng }}" target="_blank" rel="noopener" class="text-sm font-semibold brand-primary-text inline-flex items-center gap-1.5 hover:underline">
                        Get directions
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0-7 7m7-7H3"/></svg>
                    </a>
                </div>
                @if($resort->address)
                    <p class="text-slate-600 mb-4 inline-flex items-start gap-2"><svg class="w-4 h-4 mt-0.5 brand-primary-text flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg><span>{{ $resort->address }}</span></p>
                @endif
                <div class="aspect-[16/9] rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                    <iframe class="w-full h-full" loading="lazy" allowfullscreen src="https://www.google.com/maps?q={{ $resort->lat }},{{ $resort->lng }}&output=embed"></iframe>
                </div>
            </section>
        @endif
    </div>

    {{-- Sticky CTA sidebar --}}
    <aside class="space-y-4 rg-resort-sidebar">
        {{-- Plan your stay (primary CTA card) --}}
        <div class="rounded-2xl brand-primary text-white p-6 shadow-lg">
            <h3 class="font-bold text-xl mb-2">Plan your stay</h3>
            <p class="text-sm opacity-90 mb-5 leading-relaxed">Reach out directly to confirm availability and current rates. We pass your message through the channels the owner actually checks.</p>
            <div class="space-y-2.5">
                @if($resort->phone)
                    <a href="tel:{{ $resort->phone }}" class="flex items-center justify-center gap-2 bg-white brand-primary-text font-bold py-3 rounded-lg hover:bg-slate-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372a1.125 1.125 0 0 0-.872-1.097l-5.156-1.146a1.125 1.125 0 0 0-1.221.439L13.05 17.66a18.005 18.005 0 0 1-6.71-6.71l1.21-1.456a1.125 1.125 0 0 0 .438-1.22l-1.146-5.156A1.125 1.125 0 0 0 5.745 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        Call now
                    </a>
                @endif
                @if($resort->email)
                    <a href="mailto:{{ $resort->email }}" class="flex items-center justify-center gap-2 bg-white/15 backdrop-blur-sm border border-white/30 text-white font-bold py-3 rounded-lg hover:bg-white/25 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        Send email
                    </a>
                @endif
            </div>
        </div>

        {{-- Contact details card --}}
        <div class="rounded-2xl border border-slate-200 p-5 bg-white shadow-sm">
            <h3 class="font-bold text-slate-900 mb-4 text-base">Contact details</h3>
            <ul class="text-sm space-y-3 text-slate-700">
                @if($resort->phone)
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372a1.125 1.125 0 0 0-.872-1.097l-5.156-1.146a1.125 1.125 0 0 0-1.221.439L13.05 17.66a18.005 18.005 0 0 1-6.71-6.71l1.21-1.456a1.125 1.125 0 0 0 .438-1.22l-1.146-5.156A1.125 1.125 0 0 0 5.745 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        <a href="tel:{{ $resort->phone }}" class="brand-primary-text hover:underline">{{ $resort->phone }}</a>
                    </li>
                @endif
                @if($resort->email)
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        <a href="mailto:{{ $resort->email }}" class="brand-primary-text hover:underline break-all">{{ $resort->email }}</a>
                    </li>
                @endif
                @if($resort->website)
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                        <a href="{{ $resort->website }}" target="_blank" rel="noopener nofollow" class="brand-primary-text hover:underline">Visit official site</a>
                    </li>
                @endif
                @if($resort->address)
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 mt-0.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        <span>{{ $resort->address }}</span>
                    </li>
                @endif
            </ul>

            @if($resort->fb || $resort->ig || $resort->tt)
                <div class="flex gap-2 mt-5 pt-5 border-t border-slate-100">
                    @if($resort->fb)
                        <a href="{{ $resort->fb }}" target="_blank" rel="noopener nofollow" class="w-10 h-10 inline-flex items-center justify-center rounded-lg brand-primary text-white hover:opacity-90 transition-opacity" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                    @endif
                    @if($resort->ig)
                        <a href="{{ $resort->ig }}" target="_blank" rel="noopener nofollow" class="w-10 h-10 inline-flex items-center justify-center rounded-lg brand-primary text-white hover:opacity-90 transition-opacity" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif
                    @if($resort->tt)
                        <a href="{{ $resort->tt }}" target="_blank" rel="noopener nofollow" class="w-10 h-10 inline-flex items-center justify-center rounded-lg brand-primary text-white hover:opacity-90 transition-opacity" aria-label="TikTok">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5.8 20.1a6.34 6.34 0 0 0 10.86-4.43V8.77a8.16 8.16 0 0 0 4.77 1.53V6.84a4.83 4.83 0 0 1-1.84-.15Z"/></svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </aside>
</div>

{{-- Bottom return CTA — proper button instead of text link --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 mb-12 text-center">
    <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Browse more destinations
    </a>
</div>
@endsection
