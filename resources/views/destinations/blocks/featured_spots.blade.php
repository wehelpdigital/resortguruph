{{-- destcluster_featured_spots: "Featured Tourist Spots in {region}" heading +
     3-column crossfading clickable cards. Context: $meta, $galleryCols.
     Payload $p: heading_prefix, tahu_word, description. --}}
@php $hasGallery = collect($galleryCols ?? [])->flatten(1)->isNotEmpty(); @endphp
@if($hasGallery)
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">
        <div class="mb-8 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">{{ $p['heading_prefix'] ?? 'Featured' }} <span class="font-brand font-normal" style="color:#c0392b;font-size:1.3em;line-height:1">{{ $p['tahu_word'] ?? 'Tourist Spots' }}</span> in {{ $meta['name'] }}</h2>
            <p class="text-slate-600">{{ $p['description'] ?? 'A rotating look at the places worth building a trip around. Tap any card to open it on the map.' }}</p>
        </div>
        @include('destinations._fadegallery', ['columns' => $galleryCols, 'aspect' => 'aspect-[3/4]'])
    </section>
@endif
