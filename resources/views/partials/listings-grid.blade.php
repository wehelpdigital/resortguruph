<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 rg-stagger">
    @foreach($listings as $l)
        @if($l->resort)
            <a href="{{ route('resort.show', $l->resort->slug) }}" class="group block rounded-xl overflow-hidden bg-white border border-slate-200 hover:shadow-lg rg-card-lift">
                <div class="aspect-[16/10] bg-slate-200 overflow-hidden">
                    @if($l->resort->hero_path)
                        <img src="{{ asset('storage/' . $l->resort->hero_path) }}" alt="{{ $l->resort->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-5xl">🏖️</div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 mb-1">{{ $l->resort->name }}</h3>
                    <p class="text-sm text-slate-500 mb-2">{{ $l->resort->city }}{{ $l->resort->province ? ', ' . $l->resort->province : '' }}</p>
                    <p class="text-sm text-slate-700 line-clamp-2">{{ $l->resort->tagline }}</p>
                </div>
            </a>
        @endif
    @endforeach
</div>
@if(method_exists($listings, 'links'))
    <div class="mt-4">{{ $listings->links() }}</div>
@endif
