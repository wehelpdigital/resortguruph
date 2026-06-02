<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 rg-stagger">
    @foreach($listings as $l)
        @if($l->adventure)
            @php
                $a = $l->adventure;
                $c1 = $a->primary_color ?: '#7c2d12';
                $c2 = $a->secondary_color ?: '#fbbf24';
                $iconByType = [
                    'Surfing' => '🏄', 'ATV' => '🛺', 'Diving' => '🤿', 'Zipline' => '🪂',
                    'Paintball' => '🎯', 'Island hopping' => '🚤', 'Lake raft' => '🛶',
                    'Trekking' => '⛰',
                ];
                $icon = $iconByType[$a->activity_type] ?? '⭐';
            @endphp
            <a href="#"
               class="group block rounded-xl overflow-hidden bg-white border border-slate-200 hover:shadow-lg rg-card-lift">
                <div class="aspect-[16/10] overflow-hidden relative" style="background: linear-gradient(135deg, {{ $c1 }} 0%, {{ $c2 }} 100%)">
                    @if($a->hero_path)
                        <img src="{{ asset('storage/' . $a->hero_path) }}" alt="{{ $a->name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl">{{ $icon }}</div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-900 group-hover:text-brand-600 mb-1">{{ $a->name }}</h3>
                    @if($a->activity_type)
                        <p class="text-xs uppercase tracking-wide font-bold mb-1" style="color: {{ $c1 }}">{{ $a->activity_type }}{{ $a->difficulty ? ' · ' . ucfirst($a->difficulty) : '' }}</p>
                    @endif
                    <p class="text-sm text-slate-500 mb-2">{{ $a->city ?: $a->address }}{{ $a->province ? ', ' . $a->province : '' }}</p>
                    @if($a->tagline)
                        <p class="text-sm text-slate-700 line-clamp-2">{{ $a->tagline }}</p>
                    @endif
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @if($a->duration_minutes)
                            <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">⏱ {{ $a->duration_minutes }} min</span>
                        @endif
                        @if($a->min_age)
                            <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">{{ $a->min_age }}+</span>
                        @endif
                        @if($a->max_group)
                            <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">Up to {{ $a->max_group }}</span>
                        @endif
                    </div>
                </div>
            </a>
        @endif
    @endforeach
</div>
