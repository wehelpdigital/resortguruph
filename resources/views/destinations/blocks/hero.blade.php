{{-- destcluster_hero: breadcrumb + faded-photo hero with the Tahu region name.
     Context: $meta, $heroImage. Payload $p: eyebrow, show_breadcrumb. --}}
@php $eyebrow = trim((string) ($p['eyebrow'] ?? 'Destination Guide')); @endphp
@if(($p['show_breadcrumb'] ?? true))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <nav class="text-sm text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-brand-600">Home</a>
            <span class="mx-2">/</span>
            <a href="{{ route('destinations.index') }}" class="hover:text-brand-600">Destinations</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700">{{ $meta['name'] }}</span>
        </nav>
    </div>
@endif
<section class="mt-4 {{ $heroImage ? 'bg-cover bg-center' : 'bg-gradient-to-br from-brand-50 via-white to-emerald-50' }}"@if($heroImage) style="background-image:linear-gradient(rgba(255,255,255,.85),rgba(255,255,255,.92)),url('{{ $heroImage }}')"@endif>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20 text-center">
        @if($eyebrow !== '')<p class="mb-7"><span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100 px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em]">{{ $eyebrow }}</span></p>@endif
        <h1 class="font-brand font-normal leading-[0.95] text-5xl sm:text-6xl md:text-7xl" style="color:#c0392b">{{ $meta['name'] }}</h1>
        <figure class="relative mx-auto mt-12 max-w-2xl pt-6 md:pt-8">
            <span aria-hidden="true" class="pointer-events-none absolute left-1/2 -top-8 -translate-x-1/2 select-none font-serif text-[6rem] leading-none md:text-[8rem]" style="color:rgba(192,57,43,.13)">&ldquo;</span>
            <blockquote class="relative font-serif text-xl italic leading-relaxed text-slate-700 md:text-2xl md:leading-[1.55]">{{ $meta['tagline'] }}</blockquote>
            <span class="mx-auto mt-6 block h-[3px] w-12 rounded-full" style="background:#c0392b"></span>
        </figure>
    </div>
</section>
