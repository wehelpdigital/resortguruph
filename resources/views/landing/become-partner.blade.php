@extends('layouts.public')

@section('title', 'Become a Partner · Join the Tourist Guide PH Directory (Free)')
@section('meta_description', 'List your Philippine tourism business on Tourist Guide PH for free. Tour guides, hotels, resorts, restaurants, spas, and surf schools get found by traveling guests. The first step to becoming a verified partner.')
@section('canonical', url('/become-a-partner'))

@section('content')
@php
    $bizTypes = [
        ['t' => 'Tour Guides', 's' => 'Solo and licensed', 'd' => 'M12 2a5 5 0 0 1 5 5c0 3.5-5 11-5 11S7 10.5 7 7a5 5 0 0 1 5-5z M12 7.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z'],
        ['t' => 'Tour Operators', 's' => 'Day trips and packages', 'd' => 'M3 7h13l2 3h3v6h-2 M3 7v9h2 M5 16a2 2 0 1 0 4 0 2 2 0 0 0-4 0z M15 16a2 2 0 1 0 4 0 2 2 0 0 0-4 0z'],
        ['t' => 'Hotels & Resorts', 's' => 'Stays of every size', 'd' => 'M3 21V5l9-3 9 3v16 M9 21v-5h6v5 M8 8h.01 M12 8h.01 M16 8h.01 M8 12h.01 M12 12h.01 M16 12h.01'],
        ['t' => 'Homestays & Rentals', 's' => 'Airbnb and BnB', 'd' => 'M3 11l9-7 9 7 M5 10v10h14V10 M10 20v-6h4v6'],
        ['t' => 'Restaurants & Cafes', 's' => 'Food and coffee spots', 'd' => 'M4 3v7a3 3 0 0 0 6 0V3 M7 3v18 M17 3c-1.5 0-3 1.8-3 4.5S15.5 12 17 12s3 3 3 3v6'],
        ['t' => 'Massage & Spa', 's' => 'Wellness and healing', 'd' => 'M12 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6z M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6'],
        ['t' => 'Surf & Dive Schools', 's' => 'Lessons and gear', 'd' => 'M2 18c2 0 2-1.5 4-1.5S8 18 10 18s2-1.5 4-1.5 2 1.5 4 1.5 M6 15c4-8 9-9 14-8-1 5-4 9-11 9'],
        ['t' => 'Transport & Car Hire', 's' => 'Vans, boats, rentals', 'd' => 'M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11m0 0v6H5v-6m14 0H5 M7.5 14h.01 M16.5 14h.01'],
        ['t' => 'Anything Tourism', 's' => 'If guests look for it', 'd' => 'M12 2v4 M12 18v4 M2 12h4 M18 12h4 M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z'],
    ];
    $perks = [
        ['t' => 'Get found on Google', 'd' => 'Your listing lives on pages built to rank for what travelers actually search.', 'i' => 'M11 3a8 8 0 1 0 5.3 14 M21 21l-4.3-4.3 M11 7a4 4 0 0 0-4 4'],
        ['t' => 'Reach real travelers', 'd' => 'Guests planning trips across the islands see your place while they decide.', 'i' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M23 21v-2a4 4 0 0 0-3-3.9 M16 3.1a4 4 0 0 1 0 7.8'],
        ['t' => 'Your own listing page', 'd' => 'Photos, story, map, and contact, all on a page that is yours to shape.', 'i' => 'M4 4h16v16H4z M4 9h16 M9 9v11'],
        ['t' => 'A badge guests trust', 'd' => 'Verified partners carry the We Highly Recommend mark that reassures guests.', 'i' => 'm9 12 2 2 4-4 M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z'],
        ['t' => 'Free to start', 'd' => 'Listing costs nothing. Set it up today and grow from there.', 'i' => 'M12 2v20 M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
        ['t' => 'Grow your bookings', 'd' => 'Turn the traffic we bring in into calls, messages, and paying guests.', 'i' => 'M3 3v18h18 M7 15l4-4 3 3 5-6'],
    ];
    $faqs = [
        ['q' => 'Is it really free to list?', 'a' => 'Yes. Setting up your listing on the directory costs nothing. You can start today with no credit card. The verified partner badge is the next step once your place is checked.'],
        ['q' => 'Who can become a partner?', 'a' => 'Any legitimate tourism business or individual in the Philippines. Tour guides, tour operators, hotels, resorts, homestays, restaurants, cafes, spas, surf and dive schools, transport, and more.'],
        ['q' => 'How do I become verified?', 'a' => 'List first, then apply for the We Highly Recommend badge. We do a simple check of your place so travelers know Tourist Guide PH stands behind it.'],
        ['q' => 'How will travelers find me?', 'a' => 'Your listing sits inside our directory and on SEO pages that already pull in travelers planning their trips. That is the traffic you tap into.'],
    ];
@endphp

{{-- HERO --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-emerald-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div>
                <div class="inline-flex items-center gap-2 text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 bg-white/70 border border-brand-100 rounded-full px-3 py-1.5 mb-5">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                    Partner Directory · Free to Join
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-[3.4rem] font-extrabold text-slate-900 leading-[1.05] mb-5">
                    Join Our Community,<br>
                    <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.5em;line-height:1;display:inline-block;margin-top:.12em">Become a Partner</span>
                </h1>
                <p class="text-lg text-slate-600 leading-relaxed max-w-xl mb-7">
                    Run a tourism business in the Philippines? List it on Tourist Guide PH for free and get in front of the travelers already planning their trip here. It is the first step to becoming a verified partner.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-lg shadow-brand-600/20 transition">
                        List Your Business for Free
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white text-slate-800 font-bold text-base border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition">
                        Talk to Us First
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-6 text-sm text-slate-500 font-medium">
                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Free to list</span>
                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>No credit card</span>
                    <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Verified badge once approved</span>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-tr from-brand-200/40 to-emerald-200/40 rounded-[2rem] blur-2xl" aria-hidden="true"></div>
                <img src="{{ asset('storage/rg-media/business-with-badge.webp') }}" width="1200" height="600"
                     alt="A smiling cafe owner serving coffee, carrying the Tourist Guide PH We Highly Recommend badge"
                     class="relative w-full rounded-3xl shadow-2xl ring-1 ring-black/5 object-cover">
                <div class="hidden sm:flex absolute -bottom-5 -left-5 items-center gap-3 bg-white rounded-2xl shadow-xl ring-1 ring-black/5 px-4 py-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-bold text-slate-900">Verified Partner</div>
                        <div class="text-xs text-slate-500">We Highly Recommend</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- WHO CAN JOIN --}}
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10 md:mb-12">
            <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 mb-3">Who Can Join</div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-3">
                Built for <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.35em">every kind</span> of tourism business
            </h2>
            <p class="text-lg text-slate-600">If a traveler would look for you, you belong in the directory. Big or small, solo or a whole team.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4">
            @foreach($bizTypes as $b)
                <div class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 hover:border-brand-300 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 group-hover:bg-brand-600 group-hover:text-white transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $b['d'] }}"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 leading-tight">{{ $b['t'] }}</div>
                        <div class="text-sm text-slate-500 mt-0.5">{{ $b['s'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="py-16 md:py-24 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 mb-3">How It Works</div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">From a free listing to a <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.3em">verified partner</span></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @php
                $steps = [
                    ['n' => '1', 'c' => 'brand', 't' => 'List for free', 'd' => 'Send us your details and we set up your listing at no cost. This is your first step in.'],
                    ['n' => '2', 'c' => 'amber', 't' => 'Get discovered', 'd' => 'Your listing sits on pages built to rank, so travelers find you while they plan their trip.'],
                    ['n' => '3', 'c' => 'emerald', 't' => 'Become verified', 'd' => 'Meet our simple standards and earn the We Highly Recommend badge that guests trust.'],
                ];
                $stepColor = [
                    'brand' => 'bg-brand-600', 'amber' => 'bg-amber-500', 'emerald' => 'bg-emerald-600',
                ];
            @endphp
            @foreach($steps as $s)
                <div class="relative rounded-2xl bg-white border border-slate-200 p-7 shadow-sm">
                    <div class="w-12 h-12 rounded-full {{ $stepColor[$s['c']] }} text-white flex items-center justify-center text-xl font-extrabold mb-5 shadow-md">{{ $s['n'] }}</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $s['t'] }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $s['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- VERIFIED BADGE --}}
<section class="py-16 md:py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            <div class="order-2 lg:order-1 relative">
                <div class="absolute -inset-3 bg-gradient-to-br from-amber-200/40 to-brand-200/40 rounded-[2rem] blur-2xl" aria-hidden="true"></div>
                <img src="{{ asset('storage/rg-media/business-with-badge.webp') }}" width="1200" height="600"
                     alt="The We Highly Recommend badge on a partner cafe listing"
                     class="relative w-full rounded-3xl shadow-xl ring-1 ring-black/5 object-cover">
            </div>
            <div class="order-1 lg:order-2">
                <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-amber-700 mb-3">The Verified Badge</div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-4">
                    The <span class="font-brand" style="color:#c0392b;font-weight:400;font-size:1.3em">We Highly Recommend</span> badge
                </h2>
                <p class="text-lg text-slate-600 leading-relaxed mb-6">
                    Once your place is approved, you carry our We Highly Recommend badge on your listing and at your storefront. It tells travelers that Tourist Guide PH has checked your business and stands behind it, so guests book with trust.
                </p>
                <ul class="space-y-3">
                    @foreach(['A clear mark of trust on your listing', 'A window sticker for your storefront', 'A badge number guests can look up'] as $point)
                        <li class="flex items-start gap-3 text-slate-700">
                            <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- PERKS --}}
<section class="py-16 md:py-24 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 mb-3">Why Partner With Us</div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">Everything you get, from day one</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($perks as $p)
                <div class="rounded-2xl bg-white border border-slate-200 p-6 hover:shadow-md transition">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $p['i'] }}"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1.5">{{ $p['t'] }}</h3>
                    <p class="text-slate-600 leading-relaxed text-[15px]">{{ $p['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 mb-3">Good to Know</div>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">Questions partners ask</h2>
        </div>
        <div class="space-y-3">
            @foreach($faqs as $f)
                <details class="group rounded-2xl border border-slate-200 bg-white overflow-hidden">
                    <summary class="flex items-center justify-between gap-4 p-5 cursor-pointer select-none hover:bg-slate-50 font-bold text-slate-900">
                        {{ $f['q'] }}
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </summary>
                    <div class="px-5 pb-5 -mt-1 text-slate-600 leading-relaxed">{{ $f['a'] }}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-blue-800">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 text-center">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
            Ready to be found by your next guest?
        </h2>
        <p class="text-lg text-brand-50/90 max-w-2xl mx-auto mb-8">
            Join the directory for free and start showing up while travelers plan their Philippine trip. Becoming a verified partner starts with one simple listing.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-full bg-white text-brand-700 font-bold text-base hover:bg-brand-50 shadow-xl transition">
                List Your Business for Free
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-full bg-white/10 text-white font-bold text-base ring-1 ring-white/40 hover:bg-white/20 transition">
                Ask a Question
            </a>
        </div>
    </div>
</section>
@endsection
