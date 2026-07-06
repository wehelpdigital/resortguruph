{{-- destcluster_testimonials: "What Travelers Say About {region}" review cards
     (placeholder, spot-referencing). Context: $meta, $reviews.
     Payload $p: heading_prefix, tahu_word, heading_suffix, description. --}}
@if(!empty($reviews))
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">
        <div class="mb-8 text-center max-w-2xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">{{ $p['heading_prefix'] ?? 'What' }} <span class="font-brand font-normal" style="color:#c0392b;font-size:1.3em;line-height:1">{{ $p['tahu_word'] ?? 'Travelers Say' }}</span> {{ $p['heading_suffix'] ?? 'About' }} {{ $meta['name'] }}</h2>
            <p class="text-slate-600">{{ filled($p['description'] ?? null) ? $p['description'] : ('Highlights from travelers exploring different corners of ' . $meta['name'] . '.') }}</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($reviews as $r)
                <figure class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <div class="mb-3 flex items-center gap-1">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $i < $r['rating'] ? '#f59e0b' : '#e2e8f0' }}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <h3 class="mb-1.5 font-bold text-slate-900">{{ $r['title'] }}</h3>
                    <blockquote class="m-0 flex-1 text-sm leading-relaxed text-slate-700">{{ $r['text'] }}</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ mb_substr($r['name'], 0, 1) }}</div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">{{ $r['name'] }}</div>
                            <div class="text-xs text-slate-500">{{ $r['city'] }}</div>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>
@endif
