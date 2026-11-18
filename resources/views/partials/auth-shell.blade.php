{{-- Split auth layout: photo + value points on the left (desktop), form on the right.
     Usage: @component('partials.auth-shell', ['image' => '', 'kicker' => '', 'heading' => '', 'points' => []]) form @endcomponent --}}
@php
    $s = app(\App\Services\HomeShowcase::class)->data()['stats'] ?? [];
    $fmt = fn ($n) => $n >= 1000 ? number_format(floor($n / 100) * 100) . '+' : (string) $n;
    $points = $points ?? [];
@endphp
<section class="pk-auth">
    <div class="pk-auth__art">
        <img src="{{ $image ?? asset('storage/rg-media/destinations/el-nido-1.jpg') }}" alt="" aria-hidden="true">
        <div class="max-w-lg">
            <p class="pk-kicker">{{ $kicker ?? 'Tourist Guide Ph' }}</p>
            <h2 class="mt-5 font-display text-4xl xl:text-[2.75rem] font-extrabold leading-[1.08] tracking-tight">{{ $heading ?? '' }}</h2>
            @if($points)
                <ul class="mt-6 space-y-3 text-white/90">
                    @foreach($points as $pt)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 w-6 h-6 rounded-full bg-sun-400 text-ink-900 flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg></span>
                            <span>{{ $pt }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if(!empty($s['guides']))
                <div class="mt-10 grid grid-cols-3 gap-4 border-t border-white/20 pt-6">
                    @foreach([[$fmt($s['guides']), 'travel guides'], [$fmt($s['spots'] ?? 0), 'tourist spots'], ['7,641', 'islands']] as [$v, $l])
                        <div>
                            <p class="font-display text-2xl font-extrabold">{{ $v }}</p>
                            <p class="text-xs text-white/70">{{ $l }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="pk-auth__panel">
        <div class="pk-auth__card">
            {{ $slot }}
        </div>
    </div>
</section>
