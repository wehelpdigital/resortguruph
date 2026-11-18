{{--
    Site footer. Expects $footerRegions, $footerAllKeywords,
    $footerTopKeywords and $footerHashtags from layouts/public.blade.php.
    The keyword clouds are the sitewide internal-link surface for SEO, so
    they stay in the HTML (collapsed behind <details>).
--}}
@php $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph'); @endphp
<footer class="rg-footer mt-auto">
    {{-- Call to action band --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16">
        <div class="rg-footer-cta rounded-3xl px-6 py-9 sm:px-10 md:px-12 md:py-12 flex flex-col lg:flex-row lg:items-center justify-between gap-7">
            <div class="relative z-10 max-w-2xl">
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-sun-300">Plan it your way</p>
                <h2 class="mt-2 text-2xl sm:text-3xl md:text-[2.1rem] font-extrabold text-white leading-tight">Your next island trip starts here</h2>
                <p class="mt-3 text-white/80 leading-relaxed">Build a free trip plan from the places, food, and festivals on this guide, or put your business in front of travelers who are already planning a visit.</p>
            </div>
            <div class="relative z-10 flex flex-col sm:flex-row gap-3 shrink-0">
                <a href="{{ route('create-your-adventure') }}" class="rg-btn rg-btn--coral rg-btn--lg">Create Your Adventure<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a>
                <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--glass rg-btn--lg">Become a Partner</a>
            </div>
        </div>
    </div>

    {{-- Regional links --}}
    @if($footerRegions->isNotEmpty())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10 border-b border-slate-800">
            <div class="flex items-end justify-between mb-6 flex-wrap gap-2">
                <h4 class="text-white font-bold text-lg">Browse by region</h4>
                <a href="{{ route('destinations.index') }}" class="text-sm font-semibold text-brand-300">View all destinations &rarr;</a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-6 sm:gap-x-8 gap-y-7 text-sm">
                @foreach($footerRegions as $region)
                    <div class="min-w-0">
                        <h5 class="text-white font-semibold mb-2.5">
                            <a href="{{ route('destinations.cluster', $region['slug']) }}">{{ $region['name'] }}</a>
                        </h5>
                        <ul class="space-y-1.5">
                            @foreach($region['top_keywords'] as $k)
                                <li><a href="{{ url($k->slug) }}" class="block truncate capitalize text-slate-400">{{ $k->phrase }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Main footer --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-2 md:grid-cols-[1.6fr_1fr_1fr_1fr] gap-x-8 gap-y-10 text-sm">
        <div class="col-span-2 md:col-span-1 max-w-sm">
            <a href="{{ route('home') }}" class="inline-flex items-center mb-4" aria-label="{{ $siteName }} home">
                @include('layouts.partials.brand-logo', ['variant' => 'dark'])
            </a>
            <p class="text-slate-400 leading-relaxed">Your one-stop guide to exploring the Philippines. Find where to stay, what to eat, and where to go next across more than 7,000 islands, with picks from travelers who have actually been there.</p>
            <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-slate-300">
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('about.logo') }}">About the Logo</a>
                <a href="{{ route('contact') }}">Contact</a>
            </div>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3.5">Explore</h5>
            <ul class="space-y-2 text-slate-400">
                <li><a href="{{ route('destinations.index') }}">Where to Go</a></li>
                <li><a href="{{ url('/food-trip') }}">Where to Eat</a></li>
                <li><a href="{{ route('foods.index') }}">What to Eat</a></li>
                <li><a href="{{ route('activities.index') }}">What to Do</a></li>
                <li><a href="{{ route('fiestas.index') }}">Fiestas and Festivals</a></li>
                <li><a href="{{ route('buys.index') }}">What to Buy</a></li>
                <li><a href="{{ route('cultures.index') }}">Cultures to Meet</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3.5">Discover</h5>
            <ul class="space-y-2 text-slate-400">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('destinations.index') }}">All destinations</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li><a href="{{ route('partner-directory') }}">Partner Directory</a></li>
                <li><a href="{{ route('create-your-adventure') }}">Create Your Adventure</a></li>
            </ul>
        </div>
        <div>
            <h5 class="text-white font-semibold mb-3.5">For Business Owners</h5>
            <ul class="space-y-2 text-slate-400">
                <li><a href="{{ route('become-a-partner') }}">Become a Partner</a></li>
                <li><a href="{{ route('register') }}">Create an account</a></li>
                <li><a href="{{ route('login') }}">Sign in</a></li>
            </ul>
            <h5 class="text-white font-semibold mt-7 mb-3.5">Legal</h5>
            <ul class="space-y-2 text-slate-400">
                <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('sitemap.page') }}">Sitemap</a></li>
            </ul>
        </div>
    </div>

    {{-- Hashtag cloud --}}
    @if($footerHashtags->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <details class="group rg-details">
                    <summary class="flex items-center justify-between cursor-pointer select-none py-1">
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-sun-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
                            Trending tags
                        </p>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="group-open:hidden">Show all</span>
                            <span class="hidden group-open:inline">Hide</span>
                            <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </summary>
                    <div class="flex flex-wrap gap-x-2.5 gap-y-1 text-xs leading-5 text-slate-500 pt-2">
                        @foreach($footerHashtags as $h)<a href="{{ url($h['slug']) }}" class="text-slate-400">#{{ $h['tag'] }}</a>@endforeach
                    </div>
                </details>
            </div>
        </div>
    @endif

    {{-- Every resort keyword page, grouped by region --}}
    @if($footerAllKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <details class="group rg-details">
                    <summary class="flex items-center justify-between cursor-pointer select-none py-1">
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">All destinations on Tourist Guide Ph</p>
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="group-open:hidden">Show all</span>
                            <span class="hidden group-open:inline">Hide</span>
                            <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </span>
                    </summary>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-1 pt-2 items-start">
                        @foreach($footerAllKeywords->sortKeys() as $cluster => $kws)
                            @php $label = \App\Support\RegionResolver::label($cluster); @endphp
                            <details class="group/cl rg-details rg-region border-b border-slate-800/70">
                                <summary class="flex items-center justify-between cursor-pointer select-none py-2">
                                    <h6 class="text-white font-semibold text-sm flex items-center gap-2">
                                        {{ $label }}
                                        <span class="text-[11px] font-normal text-slate-500">{{ $kws->count() }}</span>
                                    </h6>
                                    <svg class="w-3 h-3 text-slate-500 shrink-0 transition-transform group-open/cl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </summary>
                                <ul class="list-none columns-2 sm:columns-3 lg:columns-4 gap-x-6 pt-1 pb-3 text-[11px] leading-relaxed">
                                    @foreach($kws as $k)
                                        <li class="break-inside-avoid"><a href="{{ url($k->slug) }}" class="block text-slate-400 capitalize">{{ $k->phrase }}</a></li>
                                    @endforeach
                                </ul>
                            </details>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>
    @endif

    {{-- Most popular destinations --}}
    @if($footerTopKeywords->isNotEmpty())
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-xs uppercase tracking-wider text-slate-500 mb-2.5">Check what's popular</p>
                <ul class="list-none columns-2 sm:columns-3 lg:columns-4 gap-x-6 text-xs leading-relaxed">
                    @foreach($footerTopKeywords as $k)
                        <li class="break-inside-avoid"><a href="{{ url($k->slug) }}" class="block py-0.5 text-slate-400 capitalize">{{ $k->phrase }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
            <p>Made with love in the Philippines.</p>
        </div>
    </div>

    {{-- Smooth height animation for the collapsible footer sections. --}}
    @verbatim
    <style>
      footer details.rg-details > summary { list-style: none; }
      footer details.rg-details > summary::-webkit-details-marker { display: none; }
      footer details.rg-details > summary + * { overflow: hidden; will-change: height; transition: height .3s cubic-bezier(.4, 0, .2, 1); }
      @media (prefers-reduced-motion: reduce) { footer details.rg-details > summary + * { transition: none; } }
      footer details.rg-region[open] { grid-column: 1 / -1; }
    </style>
    <script>
    (function () {
        var motionOK = !(window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches);
        document.querySelectorAll('footer details.rg-details').forEach(function (d) {
            var summary = d.querySelector(':scope > summary');
            if (!summary) return;
            var panel = summary.nextElementSibling;
            if (!panel) return;
            summary.addEventListener('click', function (e) {
                e.preventDefault();
                if (d.dataset.animating) return;
                if (!motionOK) { d.open = !d.open; return; }
                d.dataset.animating = '1';
                if (d.open) {
                    panel.style.height = panel.scrollHeight + 'px';
                    panel.getBoundingClientRect();
                    panel.style.height = '0px';
                    panel.addEventListener('transitionend', function te(ev) {
                        if (ev.propertyName !== 'height' || ev.target !== panel) return;
                        panel.removeEventListener('transitionend', te);
                        d.open = false; panel.style.height = ''; delete d.dataset.animating;
                    });
                } else {
                    d.open = true;
                    var target = panel.scrollHeight;
                    panel.style.height = '0px';
                    panel.getBoundingClientRect();
                    panel.style.height = target + 'px';
                    panel.addEventListener('transitionend', function te(ev) {
                        if (ev.propertyName !== 'height' || ev.target !== panel) return;
                        panel.removeEventListener('transitionend', te);
                        panel.style.height = ''; delete d.dataset.animating;
                    });
                }
            });
        });
    })();
    </script>
    @endverbatim
</footer>
