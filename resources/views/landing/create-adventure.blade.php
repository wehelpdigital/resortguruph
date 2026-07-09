@extends('layouts.public')

@section('title', 'Create Your Adventure for Free · Plan Your Philippine Trip')
@section('meta_description', 'Plan your whole Philippine trip in one place, for free. Where to go, where to eat, what to do, what to buy, and the cultures to meet, with real picks from travelers who have been there.')
@section('canonical', url('/create-your-adventure'))

@section('content')
@php
    $pillars = [
        ['t' => 'Where to Go', 's' => 'Regions, islands, and stays', 'url' => route('destinations.index'), 'c' => 'emerald',
         'i' => 'M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z M12 8a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z'],
        ['t' => 'Where to Eat', 's' => 'Restaurants by city', 'url' => url('/food-trip'), 'c' => 'amber',
         'i' => 'M4.5 2v7 M8 2v7 M11.5 2v7 M4.5 9h7 M8 9v13 M17 2c-1.7 0-3 2-3 4.5S15.3 11 17 11s3-2 3-4.5S18.7 2 17 2z M17 11v11'],
        ['t' => 'What to Eat', 's' => 'Dishes and street food', 'url' => route('foods.index'), 'c' => 'rose',
         'i' => 'M4 11h16a8 8 0 0 1-16 0z M9 7V4 M12 7V4 M15 7V4'],
        ['t' => 'What to Do', 's' => 'Adventures and activities', 'url' => route('activities.index'), 'c' => 'indigo',
         'i' => 'M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1 M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1 M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 1.3 0 1.9-.5 2.5-1'],
        ['t' => 'What to Buy', 's' => 'Pasalubong and crafts', 'url' => route('buys.index'), 'c' => 'violet',
         'i' => 'M5 8h14l-1.5 12.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 8z M9 8V5a3 3 0 0 1 6 0v3'],
        ['t' => 'Cultures to Meet', 's' => 'Tribes and traditions', 'url' => route('cultures.index'), 'c' => 'teal',
         'i' => 'M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8z M5 21v-1a7 7 0 0 1 14 0v1'],
    ];
    $colorMap = [
        'emerald' => ['chip' => 'bg-emerald-50 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white', 'ring' => 'hover:border-emerald-300', 'arrow' => 'text-emerald-600'],
        'amber'   => ['chip' => 'bg-amber-50 text-amber-700 group-hover:bg-amber-500 group-hover:text-white', 'ring' => 'hover:border-amber-300', 'arrow' => 'text-amber-600'],
        'rose'    => ['chip' => 'bg-rose-50 text-rose-700 group-hover:bg-rose-600 group-hover:text-white', 'ring' => 'hover:border-rose-300', 'arrow' => 'text-rose-600'],
        'indigo'  => ['chip' => 'bg-indigo-50 text-indigo-700 group-hover:bg-indigo-600 group-hover:text-white', 'ring' => 'hover:border-indigo-300', 'arrow' => 'text-indigo-600'],
        'violet'  => ['chip' => 'bg-violet-50 text-violet-700 group-hover:bg-violet-600 group-hover:text-white', 'ring' => 'hover:border-violet-300', 'arrow' => 'text-violet-600'],
        'teal'    => ['chip' => 'bg-teal-50 text-teal-700 group-hover:bg-teal-600 group-hover:text-white', 'ring' => 'hover:border-teal-300', 'arrow' => 'text-teal-600'],
    ];
    $steps = [
        ['n' => '1', 'c' => 'bg-emerald-600', 't' => 'Pick a region', 'd' => 'Start with where you want to be. Browse by region and see the stays, beaches, and towns in each one.'],
        ['n' => '2', 'c' => 'bg-amber-500', 't' => 'Build your days', 'd' => 'Add what to eat, what to do, and what to bring home. Every pick comes with a note on why it is worth it.'],
        ['n' => '3', 'c' => 'bg-rose-600', 't' => 'Go, it is all free', 'd' => 'No account needed and no paywall. Save your favorites, follow the guide, and enjoy the trip.'],
    ];
@endphp

{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-br from-rose-50 via-white to-amber-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div>
                <div class="inline-flex items-center gap-2 text-[11px] uppercase tracking-[0.2em] font-bold text-rose-700 bg-white/70 border border-rose-100 rounded-full px-3 py-1.5 mb-5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                    Your Free Travel Guide
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-[3.4rem] font-extrabold text-slate-900 leading-[1.05] mb-5">
                    Create Your Adventure<br>
                    <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.5em;line-height:1;display:inline-block;margin-top:.12em">for Free</span>
                </h1>
                <p class="text-lg text-slate-600 leading-relaxed max-w-xl mb-7">
                    Plan your whole Philippine trip in one place. Where to go, where to eat, what to do, what to buy, and the cultures to meet, all with real picks from travelers who have been there.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-red-500 text-white font-bold text-base hover:bg-red-600 shadow-lg shadow-red-500/20 transition">
                        Start Exploring
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                    <a href="#plan" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white text-slate-800 font-bold text-base border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition">
                        See How It Works
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-6 text-sm text-slate-500 font-medium">
                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Always free</span>
                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>No sign-up needed</span>
                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Picked by real travelers</span>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-tr from-amber-200/40 to-rose-200/40 rounded-[2rem] blur-2xl" aria-hidden="true"></div>
                <img src="{{ asset('storage/rg-media/editorial-ph-1.webp') }}" width="900" height="1200"
                     alt="Aerial view of the Chocolate Hills of Bohol under a bright Philippine sky"
                     class="relative w-full max-h-[30rem] rounded-3xl shadow-2xl ring-1 ring-black/5 object-cover">
                <div class="hidden sm:flex absolute -bottom-5 -right-5 items-center gap-3 bg-white rounded-2xl shadow-xl ring-1 ring-black/5 px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-bold text-slate-900">7,000+ islands</div>
                        <div class="text-xs text-slate-500">one place to plan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- THE SIX PILLARS --}}
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-rose-700 mb-3">Everything in One Place</div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-3">
                One trip, <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.3em">everything</span> sorted
            </h2>
            <p class="text-lg text-slate-600">Six guides that work together, so you can plan the whole thing without leaving the site.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($pillars as $p)
                @php $cm = $colorMap[$p['c']]; @endphp
                <a href="{{ $p['url'] }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 {{ $cm['ring'] }} hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl {{ $cm['chip'] }} flex items-center justify-center shrink-0 transition-colors">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $p['i'] }}"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-slate-900 text-lg leading-tight">{{ $p['t'] }}</div>
                        <div class="text-sm text-slate-500 mt-0.5">{{ $p['s'] }}</div>
                    </div>
                    <svg class="w-5 h-5 {{ $cm['arrow'] }} shrink-0 opacity-0 group-hover:opacity-100 -translate-x-1 group-hover:translate-x-0 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- HOW TO PLAN --}}
<section id="plan" class="py-16 md:py-24 bg-slate-50 border-y border-slate-100 scroll-mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-rose-700 mb-3">How to Plan</div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">Three steps to a full <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.3em">itinerary</span></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($steps as $s)
                <div class="rounded-2xl bg-white border border-slate-200 p-7 shadow-sm">
                    <div class="w-12 h-12 rounded-full {{ $s['c'] }} text-white flex items-center justify-center text-xl font-extrabold mb-5 shadow-md">{{ $s['n'] }}</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $s['t'] }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $s['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- COMMUNITY BAND --}}
<section class="py-16 md:py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            <div class="relative">
                <div class="absolute -inset-3 bg-gradient-to-br from-rose-200/40 to-amber-200/40 rounded-[2rem] blur-2xl" aria-hidden="true"></div>
                <img src="{{ asset('storage/rg-media/feature-friends.webp') }}" width="1200" height="670"
                     alt="Travelers skating and hanging out at a beach night market in the Philippines"
                     class="relative w-full rounded-3xl shadow-xl ring-1 ring-black/5 object-cover">
            </div>
            <div>
                <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-rose-700 mb-3">You Are in Good Company</div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-4">
                    Real trips, <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.3em">real picks</span>
                </h2>
                <p class="text-lg text-slate-600 leading-relaxed mb-6">
                    Every spot, dish, and stay here is chosen by people who actually went. No photo-only places, no filler. Just what is worth your time, from the north coast to the southern islands.
                </p>
                <div class="grid grid-cols-3 gap-4">
                    @foreach([['n' => '17', 'l' => 'Regions'], ['n' => '1,000+', 'l' => 'Guides'], ['n' => 'Free', 'l' => 'Always']] as $stat)
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4 text-center">
                            <div class="text-2xl md:text-3xl font-extrabold text-slate-900">{{ $stat['n'] }}</div>
                            <div class="text-xs uppercase tracking-wide font-bold text-slate-500 mt-1">{{ $stat['l'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="relative overflow-hidden bg-gradient-to-br from-rose-600 via-red-500 to-amber-500">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 text-center">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
            Your next trip starts here
        </h2>
        <p class="text-lg text-white/90 max-w-2xl mx-auto mb-8">
            Pick a region, build your days, and go. It is all free, and it is all in one place.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-full bg-white text-rose-700 font-bold text-base hover:bg-rose-50 shadow-xl transition">
                Start Your Adventure
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('become-a-partner') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-full bg-white/10 text-white font-bold text-base ring-1 ring-white/40 hover:bg-white/20 transition">
                Own a tourism business? Partner with us
            </a>
        </div>
    </div>
</section>
@endsection
