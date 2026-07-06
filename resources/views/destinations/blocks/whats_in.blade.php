{{-- destcluster_whats_in: centered "What's In {region}?" heading + intro +
     keyword photo cards. Context: $meta, $keywords, $kwImages, $spots.
     Payload $p: heading_prefix, show_intro. --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-6">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">{{ $p['heading_prefix'] ?? "What's In" }} <span class="font-brand font-normal" style="color:#c0392b;font-size:1.3em;line-height:1">{{ $meta['name'] }}</span>?</h2>
    </div>
    @if(($p['show_intro'] ?? true) && !empty($meta['intro_html']))
        <article class="prose prose-slate max-w-none mb-10">
            {!! $meta['intro_html'] !!}
        </article>
    @endif
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">
        @foreach($keywords as $k)
            @php $kimg = $kwImages[$k->id] ?? (count($spots) ? ($spots[$loop->index % count($spots)]['url'] ?? null) : null); @endphp
            <a href="{{ url($k->slug) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl">
                <div class="aspect-[16/10] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                    @if($kimg)
                        <img src="{{ $kimg }}" alt="{{ $k->phrase }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-slate-300"><svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/></svg></div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <h3 class="mb-1 font-semibold capitalize leading-snug text-slate-900 group-hover:text-brand-700">{{ $k->phrase }}</h3>
                    <p class="text-xs text-slate-500">{{ number_format($k->search_volume_monthly) }} people search this each month</p>
                    <p class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-600">Browse options <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></p>
                </div>
            </a>
        @endforeach
    </div>
</div>
