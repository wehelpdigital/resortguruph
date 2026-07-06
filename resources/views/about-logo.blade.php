@extends('layouts.public')

@php $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph'); @endphp

@section('title') About the Logo — {{ $siteName }} @endsection
@section('meta_description') Our logo is a Visayan warty pig with a backpack, an animal found nowhere on Earth but the Philippines. Here is the meaning behind the mascot, its symbolism, and its colors. @endsection
@section('canonical') {{ url('/about-the-logo') }} @endsection

@section('jsonld')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'AboutPage',
    'name' => 'About the Logo',
    'description' => 'The story, symbolism, and colours behind the ' . $siteName . ' logo, a Visayan warty pig with a backpack.',
    'url' => url('/about-the-logo'),
    'primaryImageOfPage' => asset('images/logo.webp'),
], JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'About the Logo', 'item' => url('/about-the-logo')],
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection

@section('content')
@php
    $symbols = [
        ['icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 4v8m0 0-3-3m3 3 3-3', 'title' => 'Endemic pride', 'body' => 'The Visayan warty pig lives nowhere else on Earth. Like the places you find here, it is one hundred percent Filipino and impossible to copy.'],
        ['icon' => 'M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z', 'title' => 'A wildly diverse home', 'body' => 'The Philippines is one of the most biodiverse places on the planet. Our mascot is a living reminder of the wild, rare beauty waiting across the islands.'],
        ['icon' => 'm3 11 19-9-9 19-2-8-8-2Z', 'title' => 'The joy of discovery', 'body' => 'A backpack means curiosity. It stands for wandering off the main road, following a hunch, and finding the small places that make a trip yours.'],
        ['icon' => 'M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75', 'title' => 'Travel is better together', 'body' => 'Warty pigs move in close family groups called sounders. It is a gentle nudge that the best journeys are the ones you share with the people you love.'],
        ['icon' => 'M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z', 'title' => 'A story worth protecting', 'body' => 'The species is critically endangered. Carrying it as our logo is a quiet promise to explore with care and to leave every island a little better than we found it.'],
        ['icon' => 'M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z M12 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z', 'title' => 'Local at heart', 'body' => 'It is not a lion or an eagle borrowed from somewhere else. It is a homegrown explorer, the same spirit behind our been-there, honest guides.'],
    ];
    $palette = [
        ['hex' => '#8840a8', 'name' => 'Native Violet', 'meaning' => 'The colour of the mascot himself. Rare in nature like the Visayan warty pig, it stands for imagination, individuality, and the road less traveled.'],
        ['hex' => '#2980b9', 'name' => 'Island Blue', 'meaning' => 'The seas and open skies that thread together more than 7,000 islands. It is trust, calm, and the water that carries every ferry, banca, and traveler.'],
        ['hex' => '#c0392b', 'name' => 'Fiesta Red', 'meaning' => 'Filipino warmth and hospitality, the heat of the grill, and the energy of a town fiesta. It is also the hand-painted brush stroke you see in our name.'],
        ['hex' => '#f39c12', 'name' => 'Golden Hour', 'meaning' => 'Sunshine, sand, and the light at the end of the day. It is the optimism of the open road and the promise of one more adventure.'],
        ['hex' => '#16a34a', 'name' => 'Wild Green', 'meaning' => 'The forests and grasslands the warty pig calls home. It stands for nature, growth, and traveling in a way that keeps those places wild.'],
    ];
@endphp

{{-- Hero --}}
<section class="bg-cover bg-center" style="background-image:linear-gradient(rgba(255,255,255,.86),rgba(255,255,255,.93)),url('{{ asset('images/mayon-compressed.webp') }}')">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:px-8 py-14 md:grid-cols-2 md:py-20">
        <div class="text-center md:text-left">
            <p class="mb-4"><span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100 px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em]">About Our Logo</span></p>
            <h1 class="text-4xl font-extrabold leading-tight text-slate-900 md:text-5xl">The Pig With a <span class="mt-3 block font-brand font-normal" style="color:#c0392b;font-size:1.6em;line-height:1">Backpack</span></h1>
            <p class="mx-auto mt-4 max-w-xl text-lg text-slate-600 md:mx-0">Our logo is a Visayan warty pig carrying a backpack, a little explorer you will not find anywhere else on Earth. Here is why we chose it, and what it stands for.</p>
        </div>
        <div class="flex justify-center">
            <div class="flex h-56 w-56 items-center justify-center rounded-full bg-white/70 md:h-72 md:w-72">
                <img src="{{ asset('images/logo.webp') }}" alt="{{ $siteName }} logo, a Visayan warty pig with a backpack" class="h-40 w-auto md:h-52" width="200" height="200">
            </div>
        </div>
    </div>
</section>

{{-- The animal --}}
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
    <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">A Truly <span class="font-brand font-normal" style="color:#c0392b;font-size:1.15em;line-height:1">Filipino</span> Animal</h2>
    <div class="mt-6 grid items-start gap-8 md:grid-cols-2 lg:gap-12">
        <img src="{{ asset('storage/rg-media/warty-pig.webp') }}" alt="A Visayan warty pig (Sus cebifrons) in the wild" width="2528" height="1684" loading="lazy" class="w-full rounded-2xl object-cover shadow-sm ring-1 ring-slate-900/5">
        <div class="prose prose-slate max-w-none">
            <p>The Visayan warty pig, known to scientists as <em>Sus cebifrons</em>, is one of the rarest wild pigs in the world and it belongs to the Philippines alone. Once found across the central Visayan islands, it now survives in only a few pockets of forest on Negros and Panay, which makes it both precious and unmistakably ours.</p>
            <p>It is a character. The males grow a spray of coarse hair into a wild mohawk during the breeding season, along with the fleshy warts that give the animal its name. They are clever, curious, and resourceful, rooting through forest and grassland for whatever the season offers and moving together in tight family groups. In a country that holds some of the richest biodiversity on the planet, the warty pig is a proud, homegrown flagship for it.</p>
            <p>We could have picked something grand and borrowed. Instead we chose an animal that could only come from here, because that is exactly the kind of travel this site is about.</p>
        </div>
    </div>
</section>

{{-- The backpack --}}
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
        <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">Why the <span class="font-brand font-normal" style="color:#c0392b;font-size:1.15em;line-height:1">Backpack</span>?</h2>
        <div class="prose prose-slate mt-5 max-w-none">
            <p>Give any creature a backpack and it stops being a homebody and becomes a traveler. The backpack is the oldest symbol of the road: curiosity, self-reliance, and the readiness to go and see for yourself. It says pack light, say yes, and step out the door.</p>
            <p>On our warty pig it means one lovely thing. Even the most Filipino animal there is cannot resist exploring its own home. From surf towns to sandbars, cool mountain mornings to island sunsets, there is always one more place worth the trip, and a backpack is all you really need to start.</p>
        </div>
    </div>
</section>

{{-- Symbolism --}}
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">What It <span class="font-brand font-normal" style="color:#c0392b;font-size:1.15em;line-height:1">Stands For</span></h2>
        <p class="mx-auto mt-3 max-w-2xl text-slate-600">Six ideas we packed into one small explorer.</p>
    </div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($symbols as $s)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $s['icon'] }}"/></svg>
                </div>
                <h3 class="mb-1.5 text-lg font-bold text-slate-900">{{ $s['title'] }}</h3>
                <p class="text-sm leading-relaxed text-slate-600">{{ $s['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Colors --}}
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">The <span class="font-brand font-normal" style="color:#c0392b;font-size:1.15em;line-height:1">Colors</span></h2>
            <p class="mx-auto mt-3 max-w-2xl text-slate-600">Every shade is a piece of the Philippine trip.</p>
        </div>
        <style>
            .rg-swatch{position:relative;overflow:hidden}
            .rg-swatch::before{content:"";position:absolute;inset:0;background:linear-gradient(160deg,rgba(255,255,255,.32),rgba(255,255,255,0) 46%);pointer-events:none}
            .rg-swatch::after{content:"";position:absolute;top:0;left:0;height:100%;width:38%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.72),transparent);transform:translateX(-160%) skewX(-18deg);pointer-events:none;animation:rgShine 4.5s ease-in-out infinite}
            .rg-color-card:nth-child(2) .rg-swatch::after{animation-delay:.6s}
            .rg-color-card:nth-child(3) .rg-swatch::after{animation-delay:1.2s}
            .rg-color-card:nth-child(4) .rg-swatch::after{animation-delay:1.8s}
            .rg-color-card:nth-child(5) .rg-swatch::after{animation-delay:2.4s}
            @keyframes rgShine{0%{transform:translateX(-160%) skewX(-18deg)}20%{transform:translateX(320%) skewX(-18deg)}100%{transform:translateX(320%) skewX(-18deg)}}
            @media (prefers-reduced-motion:reduce){.rg-swatch::after{animation:none}}
        </style>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            @foreach($palette as $c)
                <div class="rg-color-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                    <div class="rg-swatch h-24" style="background-color:{{ $c['hex'] }}"></div>
                    <div class="p-5">
                        <h3 class="font-bold text-slate-900">{{ $c['name'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $c['meaning'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Closing CTA --}}
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 text-center">
    <h2 class="text-2xl font-extrabold text-slate-900 md:text-3xl">Ready to follow the little explorer?</h2>
    <p class="mx-auto mt-3 max-w-xl text-slate-600">Grab your own backpack. The islands are closer than you think.</p>
    <a href="{{ route('destinations.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-rose-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-rose-700 no-underline">
        Explore destinations
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </a>
</section>
@endsection
