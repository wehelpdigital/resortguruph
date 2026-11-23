@extends('layouts.public')
@php $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph'); @endphp

@section('title')About the Logo | {{ $siteName }}@endsection
@section('meta_description')Our logo is a guide pin holding the eight-rayed sun of the Philippine flag, set over the waves that link more than 7,000 islands. Here is what each part means.@endsection
@section('canonical'){{ url('/about-the-logo') }}@endsection
@section('og_image'){{ asset('images/brand/icon-512.png') }}@endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    "\x40context" => 'https://schema.org',
    '@type' => 'AboutPage',
    'name' => 'About the Logo',
    'description' => 'The meaning behind the ' . $siteName . ' logo: a guide pin, the eight-rayed sun of the Philippine flag, and the sea.',
    'url' => url('/about-the-logo'),
    'primaryImageOfPage' => asset('images/brand/icon-512.png'),
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection

@section('content')
@php
    $symbols = [
        ['title' => 'The guide pin', 'color' => '#13669c', 'bg' => '#eef6fb',
         'icon' => '<path d="M12 22s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="9" r="2.5"/>',
         'body' => 'Every page on this site answers one question: where should you go next? The pin stands for that, a trusted marker you can follow to the right beach, town, or table.'],
        ['title' => 'The eight-rayed sun', 'color' => '#c26a06', 'bg' => '#fff9eb',
         'icon' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
         'body' => 'Taken from the Philippine flag, where the eight rays honor the first provinces that rose for independence. For us it is also the warmth of Filipino hospitality and the golden light at the end of an island day.'],
        ['title' => 'The waves', 'color' => '#0f766e', 'bg' => '#e5f5f3',
         'icon' => '<path d="M2 8c2 0 2-1.5 4-1.5S8 8 10 8s2-1.5 4-1.5S16 8 18 8s2-1.5 4-1.5"/><path d="M2 14c2 0 2-1.5 4-1.5s2 1.5 4 1.5 2-1.5 4-1.5 2 1.5 4 1.5 2-1.5 4-1.5"/>',
         'body' => 'Two lines of sea for the waters that link 7,641 islands. Ferries, bangkas, and island hops are part of almost every trip here, so the sea sits at the base of everything.'],
        ['title' => 'Made here', 'color' => '#b33421', 'bg' => '#fff3ef',
         'icon' => '<path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7z"/>',
         'body' => 'The name is set in a clean, friendly type with the .ph in fiesta coral, a reminder that this guide is written here, by people who have actually made the trip.'],
    ];
    $palette = [
        ['hex' => '#13669c', 'name' => 'Island Blue', 'meaning' => 'Open sea and sky, and the trust a good guide earns.'],
        ['hex' => '#0f4c78', 'name' => 'Deep Sea', 'meaning' => 'The quiet depth of the reefs and channels between the islands.'],
        ['hex' => '#ffc74a', 'name' => 'Golden Sun', 'meaning' => 'The flag sun, warm afternoons, and long beach sunsets.'],
        ['hex' => '#ef5b3a', 'name' => 'Fiesta Coral', 'meaning' => 'Town fiestas, grilled food, and the energy of a trip about to start.'],
        ['hex' => '#7bb9de', 'name' => 'Shallow Water', 'meaning' => 'Clear lagoons and the first swim of the day.'],
    ];
@endphp

<section class="rg-hero-dark relative overflow-hidden" style="background-image:linear-gradient(180deg,rgba(7,18,29,.66) 0%,rgba(12,36,56,.5) 50%,rgba(7,18,29,.8) 100%),url('{{ asset('images/hero-palawan-1600.webp') }}');background-size:cover;background-position:center">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:px-8 py-16 md:grid-cols-[1.2fr_1fr] md:py-24">
        <div class="text-center md:text-left">
            <p class="mb-5"><span class="inline-flex items-center rounded-full bg-white/10 ring-1 ring-white/25 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-[0.2em] text-white/90">About our logo</span></p>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white">A pin, a sun, <span class="font-brand font-normal" style="color:#ffd166;font-size:1.25em">and the sea</span></h1>
            <p class="mx-auto mt-5 max-w-xl text-lg leading-relaxed text-white/85 md:mx-0">Our logo is a guide pin holding the eight-rayed sun of the Philippine flag, set over the waves that connect more than 7,000 islands. It is a simple promise: we help you find the right place under the Philippine sun.</p>
        </div>
        <div class="flex justify-center">
            <img src="{{ asset('images/brand/logo-mark.svg') }}" alt="{{ $siteName }} logo: a guide pin with the Philippine sun over the sea" class="h-48 w-48 md:h-64 md:w-64 drop-shadow-2xl" width="256" height="256">
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 md:py-20">
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-600">What it means</p>
    <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">Four ideas in one mark</h2>
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($symbols as $s)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $s['icon'] !!}</svg>
                </span>
                <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $s['title'] }}</h3>
                <p class="mt-2 text-[15px] leading-relaxed text-slate-600">{{ $s['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="bg-slate-50 border-y border-slate-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-600">Our colors</p>
        <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">Taken from a day in the islands</h2>
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach($palette as $c)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <div class="h-24" style="background:{{ $c['hex'] }}"></div>
                    <div class="p-4">
                        <p class="font-bold text-slate-900">{{ $c['name'] }}</p>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $c['hex'] }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $c['meaning'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 md:py-20">
    <div class="grid items-center gap-10 md:grid-cols-2">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-600">The wordmark</p>
            <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">Easy to read, anywhere</h2>
            <p class="mt-4 text-lg leading-relaxed text-slate-600">The name sits next to the pin in Plus Jakarta Sans, a modern typeface that stays clear on a phone screen, a printed map, or a resort's front desk sign. On dark backgrounds the mark keeps its colors and the name turns white.</p>
            <p class="mt-4 text-lg leading-relaxed text-slate-600">Before this mark, our logo was a Visayan warty pig with a backpack, a nod to wildlife found only in the Philippines. That explorer spirit still runs through every guide on the site.</p>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-8">
                @include('layouts.partials.brand-logo')
            </div>
            <div class="flex items-center justify-center rounded-2xl bg-ink-900 p-8">
                @include('layouts.partials.brand-logo', ['variant' => 'dark'])
            </div>
        </div>
    </div>
</section>
@endsection
