<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', \App\Models\RgSetting::get('site_tagline', 'Find the best resorts and hotels in the Philippines'))">
    @hasSection('meta_keywords')<meta name="keywords" content="@yield('meta_keywords')">@endif
    @hasSection('canonical')<link rel="canonical" href="@yield('canonical')">@endif

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', config('app.name'))">
    <meta property="og:description" content="@yield('meta_description', \App\Models\RgSetting::get('site_tagline', ''))">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')<meta property="og:image" content="@yield('og_image')">@endif

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8F%96%EF%B8%8F%3C/text%3E%3C/svg%3E">

    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eef4ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' },
                    },
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Global UX animations + smooth scrolling --}}
    <style>
        html { scroll-behavior: smooth; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
        }
        /* Page entrance fade */
        body { animation: rgFadeIn 0.5s ease-out both; }
        @keyframes rgFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* Reveal on scroll (toggled by IntersectionObserver) */
        .rg-reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease-out, transform 0.7s ease-out; }
        .rg-reveal.is-visible { opacity: 1; transform: translateY(0); }

        /* Stagger children — each child gets an incremental delay */
        .rg-stagger > * { opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
        .rg-stagger.is-visible > *:nth-child(1) { opacity: 1; transform: translateY(0); transition-delay: 0.05s; }
        .rg-stagger.is-visible > *:nth-child(2) { opacity: 1; transform: translateY(0); transition-delay: 0.12s; }
        .rg-stagger.is-visible > *:nth-child(3) { opacity: 1; transform: translateY(0); transition-delay: 0.19s; }
        .rg-stagger.is-visible > *:nth-child(4) { opacity: 1; transform: translateY(0); transition-delay: 0.26s; }
        .rg-stagger.is-visible > *:nth-child(5) { opacity: 1; transform: translateY(0); transition-delay: 0.33s; }
        .rg-stagger.is-visible > *:nth-child(6) { opacity: 1; transform: translateY(0); transition-delay: 0.40s; }
        .rg-stagger.is-visible > *:nth-child(7) { opacity: 1; transform: translateY(0); transition-delay: 0.47s; }
        .rg-stagger.is-visible > *:nth-child(8) { opacity: 1; transform: translateY(0); transition-delay: 0.54s; }
        .rg-stagger.is-visible > *:nth-child(9) { opacity: 1; transform: translateY(0); transition-delay: 0.61s; }
        .rg-stagger.is-visible > *:nth-child(10) { opacity: 1; transform: translateY(0); transition-delay: 0.68s; }
        .rg-stagger.is-visible > *:nth-child(n+11) { opacity: 1; transform: translateY(0); transition-delay: 0.75s; }

        /* Card hover lift */
        .rg-card-lift { transition: transform 0.25s ease-out, box-shadow 0.25s ease-out, border-color 0.25s ease-out; will-change: transform; }
        .rg-card-lift:hover { transform: translateY(-3px); box-shadow: 0 12px 28px -8px rgba(15, 23, 42, 0.18); }

        /* Button micro-interaction */
        a, button { transition: background-color 0.18s ease-out, color 0.18s ease-out, transform 0.12s ease-out, box-shadow 0.18s ease-out; }
        a:active, button:active { transform: translateY(1px); }

        /* Image lazy-load fade */
        img[loading="lazy"] { opacity: 0; transition: opacity 0.5s ease-out; }
        img[loading="lazy"].loaded, img[loading="lazy"][complete] { opacity: 1; }

        /* Top progress bar shown briefly during navigation */
        #rgTopBar { position: fixed; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, #2563eb, transparent); transform: scaleX(0); transform-origin: left; z-index: 100; pointer-events: none; transition: transform 0.6s ease-out; }
        #rgTopBar.active { transform: scaleX(1); }

        /* View Transitions API (Chromium 111+) */
        @view-transition { navigation: auto; }
        ::view-transition-old(root) { animation: rgFadeOut 0.2s ease-in both; }
        ::view-transition-new(root) { animation: rgFadeIn 0.35s ease-out both; }
        @keyframes rgFadeOut { to { opacity: 0; transform: translateY(-6px); } }
    </style>

    @stack('head')
    @yield('jsonld')
</head>
<body class="bg-white text-slate-800 antialiased min-h-screen flex flex-col">

<div id="rgTopBar"></div>

<header class="border-b border-slate-200 bg-white sticky top-0 z-30 backdrop-blur bg-white/85">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-lg">
            <span class="text-2xl">🏖️</span>
            <span>{{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }}</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a>
            <a href="{{ url('/destinations') }}" class="hover:text-brand-600">Destinations</a>
            <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a>
            <a href="{{ route('about') }}" class="hover:text-brand-600">About</a>
            <a href="{{ route('contact') }}" class="hover:text-brand-600">Contact</a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="px-3 py-1.5 rounded-md bg-brand-600 text-white hover:bg-brand-700">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="hover:text-brand-600">Sign in</a>
                <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-md bg-brand-600 text-white hover:bg-brand-700">List your resort</a>
            @endauth
        </nav>
        <button class="md:hidden" onclick="document.getElementById('mobileNav').classList.toggle('hidden')" aria-label="Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div id="mobileNav" class="hidden md:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-3 space-y-2 text-sm font-medium">
            <a href="{{ route('home') }}" class="block">Home</a>
            <a href="{{ url('/destinations') }}" class="block">Destinations</a>
            <a href="{{ route('blog.index') }}" class="block">Blog</a>
            <a href="{{ route('about') }}" class="block">About</a>
            <a href="{{ route('contact') }}" class="block">Contact</a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="block text-brand-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block">Sign in</a>
                <a href="{{ route('register') }}" class="block text-brand-600">List your resort</a>
            @endauth
        </div>
    </div>
</header>

<main class="flex-1">
    @yield('content')
</main>

@php
    $footerRegions = \Illuminate\Support\Facades\Cache::remember('footer_regions_v1', 600, function () {
        $meta = \App\Http\Controllers\DestinationsController::clusterMetadata();
        return \App\Models\RgKeyword::query()
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->get()
            ->groupBy('cluster_tag')
            ->map(function ($kws, $slug) use ($meta) {
                if (!isset($meta[$slug])) return null;
                return [
                    'slug' => $slug,
                    'name' => $meta[$slug]['name'],
                    'top_keywords' => $kws->sortByDesc('search_volume_monthly')->take(4)->values(),
                    'total_volume' => $kws->sum('search_volume_monthly'),
                ];
            })
            ->filter()
            ->sortByDesc('total_volume')
            ->take(6)
            ->values();
    });
    $footerTopKeywords = \Illuminate\Support\Facades\Cache::remember('footer_top_keywords_v1', 600, function () {
        return \App\Models\RgKeyword::query()
            ->whereHas('seoPage', fn($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->limit(20)
            ->get();
    });
@endphp

<footer class="bg-slate-900 text-slate-300 mt-16 mt-auto">
    {{-- Mega-footer: regional links (sitewide internal linking for SEO) --}}
    @if($footerRegions->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8 border-b border-slate-800">
            <div class="flex items-end justify-between mb-5 flex-wrap gap-2">
                <h4 class="text-white font-bold text-lg">Browse by region</h4>
                <a href="{{ url('/destinations') }}" class="text-sm text-brand-300 hover:text-white">View all destinations &rarr;</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6 text-sm">
                @foreach($footerRegions as $region)
                    <div>
                        <h5 class="text-white font-semibold mb-2">
                            <a href="{{ route('destinations.cluster', $region['slug']) }}" class="hover:text-brand-300">{{ $region['name'] }}</a>
                        </h5>
                        <ul class="space-y-1">
                            @foreach($region['top_keywords'] as $k)
                                <li><a href="{{ url($k->slug) }}" class="hover:text-white capitalize">{{ $k->phrase }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Standard footer --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid md:grid-cols-4 gap-8 text-sm">
        <div>
            <h4 class="text-white font-bold mb-3 flex items-center gap-2">🏖️ {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }}</h4>
            <p>{{ \App\Models\RgSetting::get('site_tagline', '') }}</p>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">Discover</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                <li><a href="{{ url('/destinations') }}" class="hover:text-white">All destinations</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-white">About</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">For Resort Owners</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('register') }}" class="hover:text-white">List your resort</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">Sign in</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Get help</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3">Legal</h5>
            <ul class="space-y-1.5">
                <li><a href="{{ route('terms') }}" class="hover:text-white">Terms of Service</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
            </ul>
        </div>
    </div>

    {{-- Most popular destinations strip (extra internal link surface) --}}
    @if($footerTopKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2">Most popular destinations</p>
                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs">
                    @foreach($footerTopKeywords as $k)
                        <a href="{{ url($k->slug) }}" class="text-slate-400 hover:text-white capitalize">{{ $k->phrase }}</a>
                        @if(!$loop->last)<span class="text-slate-600">·</span>@endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="border-t border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }}. All rights reserved.</p>
            <p>Made with love in the Philippines.</p>
        </div>
    </div>
</footer>

@stack('scripts')

{{-- UX animations: reveal-on-scroll, smooth nav, image fade-in --}}
<script>
(function() {
    // 1. Reveal-on-scroll (IntersectionObserver)
    const revealEls = document.querySelectorAll('.rg-reveal, .rg-stagger');
    if ('IntersectionObserver' in window && revealEls.length > 0) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        revealEls.forEach(el => io.observe(el));
    } else {
        // No IO support — just show everything
        revealEls.forEach(el => el.classList.add('is-visible'));
    }

    // 2. Image lazy-load fade-in
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        if (img.complete && img.naturalHeight !== 0) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
            img.addEventListener('error', () => img.classList.add('loaded'), { once: true });
        }
    });

    // 3. Top progress bar on internal navigation
    const topBar = document.getElementById('rgTopBar');
    if (topBar) {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank') return;
            try {
                const url = new URL(href, location.href);
                if (url.origin !== location.origin) return;
                if (url.pathname === location.pathname && url.search === location.search) return;
                // Trigger progress bar
                topBar.classList.add('active');
            } catch (_) {}
        });
        // Hide bar when navigated away (handled by pageshow on bfcache return)
        window.addEventListener('pageshow', () => topBar.classList.remove('active'));
    }

    // 4. Smooth-scroll for ALL same-page anchor links (fallback to CSS scroll-behavior)
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href').slice(1);
            if (!id) return;
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            history.replaceState(null, '', '#' + id);
        });
    });
})();
</script>
</body>
</html>
