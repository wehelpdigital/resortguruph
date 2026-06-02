{{--
    Row-based restaurant listings — one full-width row per restaurant.
    Matches the destinations / resort listings format (NOT a card grid,
    NOT masonry). Image on left, content on right at desktop; stacked
    vertically on mobile. All images forced to landscape via aspect-ratio
    + object-fit: cover so portrait source files still render correctly.
--}}
<div class="space-y-6 rg-stagger">
    @foreach($listings as $idx => $l)
        @if($l->restaurant)
            @php
                $r = $l->restaurant;
                $c1 = $r->primary_color ?: '#0f172a';
                $c2 = $r->secondary_color ?: '#fbbf24';
                // Deterministic fake rating 4.4–4.9 per restaurant
                $seed = abs(crc32('restaurant_' . $r->id));
                $rating = round(4.4 + (($seed % 6) * 0.1), 1);
                $reviewCount = 60 + ($seed % 220);
            @endphp
            <article class="rg-restaurant-row rounded-2xl overflow-hidden bg-white border border-slate-200 hover:shadow-lg rg-card-lift">
                <div class="grid md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] gap-0">
                    {{-- IMAGE PANE (left on desktop, top on mobile). Strict
                         16:10 landscape aspect ratio. Portrait source files
                         get cropped to landscape via object-fit: cover. --}}
                    <div class="relative overflow-hidden" style="background: linear-gradient(135deg, {{ $c1 }} 0%, {{ $c2 }} 100%); aspect-ratio: 16/10;">
                        @if($r->hero_path)
                            <img src="{{ asset('storage/' . $r->hero_path) }}"
                                 alt="{{ $r->name }}"
                                 loading="lazy"
                                 class="absolute inset-0 w-full h-full"
                                 style="object-fit: cover; object-position: center;">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-6xl text-white/70">🍴</div>
                        @endif
                        {{-- Top-left rank chip --}}
                        <div class="absolute top-3 left-3 bg-white/95 backdrop-blur rounded-md px-2.5 py-1 text-xs font-bold text-slate-900 shadow">
                            #{{ $idx + 1 }} Recommended
                        </div>
                    </div>

                    {{-- CONTENT PANE --}}
                    <div class="p-5 md:p-7 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <h3 class="text-xl md:text-2xl font-bold text-slate-900 leading-tight mb-1">{{ $r->name }}</h3>
                                @if($r->cuisine)
                                    <p class="text-xs uppercase tracking-wide font-bold" style="color: {{ $c1 }}">{{ $r->cuisine }}</p>
                                @endif
                            </div>
                            {{-- Rating block --}}
                            <div class="flex-shrink-0 text-right">
                                <div class="flex items-center gap-1 justify-end">
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="#f59e0b"><path d="M9.05 2.927c.3-.922 1.6-.922 1.9 0l1.486 4.575a1 1 0 0 0 .95.69h4.812c.97 0 1.371 1.24.588 1.81l-3.893 2.83a1 1 0 0 0-.364 1.118l1.486 4.575c.3.922-.755 1.688-1.539 1.118l-3.893-2.83a1 1 0 0 0-1.176 0l-3.893 2.83c-.784.57-1.838-.196-1.539-1.118l1.486-4.575a1 1 0 0 0-.364-1.118L2.21 10.002c-.783-.57-.381-1.81.588-1.81h4.812a1 1 0 0 0 .95-.69L9.05 2.927z"/></svg>
                                    <span class="text-base font-bold text-slate-900">{{ $rating }}</span>
                                </div>
                                <p class="text-xs text-slate-500">{{ $reviewCount }} reviews</p>
                            </div>
                        </div>

                        {{-- Location + meta --}}
                        <p class="text-sm text-slate-500 mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span>{{ $r->city }}{{ $r->province ? ', ' . $r->province : '' }}</span>
                            @if($r->hours_summary)
                                <span class="mx-1.5 text-slate-300">·</span>
                                <span>Open {{ $r->hours_summary }}</span>
                            @endif
                        </p>

                        {{-- Description --}}
                        @if($r->tagline)
                            <p class="text-sm text-slate-700 leading-relaxed mb-4">{{ $r->tagline }}</p>
                        @endif

                        {{-- CTAs row --}}
                        <div class="mt-auto flex flex-wrap gap-2 pt-2">
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md text-white font-semibold text-sm hover:opacity-90" style="background: {{ $c1 }};">
                                Visit Restaurant
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50">View Menu</a>
                            <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-md border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50">Reviews</a>
                        </div>
                    </div>
                </div>
            </article>
        @endif
    @endforeach
</div>
