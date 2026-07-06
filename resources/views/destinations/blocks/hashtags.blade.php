{{-- destcluster_hashtags: dynamic #tags for the region (homepage #Tags style).
     Context: $meta, $hashtags. Payload $p: heading, description. --}}
@if(!empty($hashtags))
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-14 mb-8 border-t border-slate-200 pt-8">
        <h2 class="mb-2 flex items-center gap-2 text-2xl md:text-3xl font-bold text-slate-900">
            <svg class="h-6 w-6 text-brand-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>
            {{ $p['heading'] ?? 'Tags' }}
        </h2>
        <p class="mb-6 text-slate-600">{{ filled($p['description'] ?? null) ? $p['description'] : ('Popular searches around ' . $meta['name'] . '.') }}</p>
        <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-sm">
            @foreach($hashtags as $h)
                <a href="{{ $h['url'] }}" class="text-slate-500 hover:text-slate-900 no-underline transition-colors">#{{ $h['tag'] }}</a>
            @endforeach
        </div>
    </section>
@endif
