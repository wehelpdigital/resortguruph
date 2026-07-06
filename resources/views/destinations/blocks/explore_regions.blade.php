{{-- destcluster_explore_regions: homepage-style region cards with crossfading
     circle thumbnails. Context: $others, $otherImages. Payload $p: heading. --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900">{{ $p['heading'] ?? 'Explore Other Regions' }}</h2>
        <a href="{{ route('destinations.index') }}" class="whitespace-nowrap font-semibold text-brand-600 hover:underline">View all destinations &rarr;</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($others as $o)
            @php $imgs = $otherImages[$o['slug']] ?? []; @endphp
            <a href="{{ route('destinations.cluster', $o['slug']) }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-brand-300 hover:shadow-md">
                <span class="rg-fadetile relative h-14 w-14 flex-none overflow-hidden rounded-full bg-slate-200 ring-1 ring-slate-900/5">
                    @forelse($imgs as $i => $u)
                        <div class="rg-fadecard absolute inset-0{{ $i === 0 ? ' is-on' : '' }}"><img src="{{ $u }}" alt="{{ $o['name'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover"></div>
                    @empty
                        <span class="absolute inset-0 flex items-center justify-center text-lg font-extrabold text-slate-500">{{ mb_substr($o['name'], 0, 1) }}</span>
                    @endforelse
                </span>
                <div class="min-w-0 flex-1">
                    <div class="mb-1 flex items-start justify-between gap-2">
                        <h3 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $o['name'] }}</h3>
                        <span class="whitespace-nowrap rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">{{ $o['count'] }}</span>
                    </div>
                    <p class="line-clamp-2 text-sm text-slate-500">{{ $o['tagline'] ?? '' }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
