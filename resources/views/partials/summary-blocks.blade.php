{{--
    TLDR and WWWW are now TWO independent collapsible sections.
    - Each renders as a native <details> with a clickable summary header.
    - Both default CLOSED.
    - A small JS helper animates max-height smoothly on toggle and rotates
      the chevron icon — works without Alpine/HTMX.
    - Each section has its own card; they're spaced with my-4 between them
      (no longer share a container).

    Required props:
      $tldr  → string|null. Plain paragraph OR `* bullet\n* bullet` lines.
      $wwww  → array|null with keys: why, when, where, whom.
--}}

@php
    $hasTldr = !empty(trim($tldr ?? ''));
    $hasWwww = !empty($wwww) && is_array($wwww) && count(array_filter($wwww)) > 0;
@endphp

@if($hasTldr)
    @php
        $lines = preg_split('/\r?\n/', trim($tldr));
        $bullets = array_values(array_filter(array_map(fn($l) => preg_match('/^\s*[-*\x{2022}]\s+(.+)$/u', $l, $m) ? trim($m[1]) : null, $lines)));
    @endphp
    <details class="rg-accordion rg-accordion-tldr not-prose my-4 rounded-xl border border-slate-200 bg-white overflow-hidden">
        <summary class="rg-accordion-head flex items-center gap-3 px-5 sm:px-6 py-4 cursor-pointer select-none">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-50 text-brand-600 shrink-0" aria-hidden="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </span>
            <span class="flex-1 min-w-0">
                <span class="block text-[0.7rem] uppercase tracking-[0.18em] text-brand-700 font-bold">The short version</span>
                <span class="block text-sm text-slate-600">Tap to read the key takeaways before you scroll</span>
            </span>
            <svg class="rg-accordion-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
        </summary>
        <div class="rg-accordion-body">
            <div class="rg-accordion-body-inner px-5 sm:px-6 pb-5 pt-1 border-t border-slate-100">
                @if(count($bullets) >= 2)
                    <ul class="text-slate-700 text-[0.95rem] leading-relaxed space-y-2 mt-4">
                        @foreach($bullets as $b)
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 mt-1 text-brand-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 10 18l9.75-12"/></svg>
                                <span>{{ $b }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-slate-700 leading-relaxed text-[0.95rem] mt-4">{{ trim($tldr) }}</p>
                @endif
            </div>
        </div>
    </details>
@endif

@if($hasWwww)
    @php
        $items = [
            'why'   => ['label' => 'Why go',
                        'tone'  => ['bg' => 'bg-orange-50', 'fg' => 'text-orange-600'],
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.32.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .32-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>'],
            'when'  => ['label' => 'When to go',
                        'tone'  => ['bg' => 'bg-cyan-50', 'fg' => 'text-cyan-700'],
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>'],
            'where' => ['label' => 'Where to go',
                        'tone'  => ['bg' => 'bg-emerald-50', 'fg' => 'text-emerald-700'],
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>'],
            'whom'  => ['label' => 'Whom to go with',
                        'tone'  => ['bg' => 'bg-fuchsia-50', 'fg' => 'text-fuchsia-700'],
                        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 0 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>'],
        ];
        $activeItems = array_filter(array_keys($items), fn($k) => !empty(trim($wwww[$k] ?? '')));
    @endphp
    <details class="rg-accordion rg-accordion-wwww not-prose my-4 rounded-xl border border-slate-200 bg-white overflow-hidden">
        <summary class="rg-accordion-head flex items-center gap-3 px-5 sm:px-6 py-4 cursor-pointer select-none">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 shrink-0" aria-hidden="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
            </span>
            <span class="flex-1 min-w-0">
                <span class="block text-[0.7rem] uppercase tracking-[0.18em] text-emerald-700 font-bold">At a glance</span>
                <span class="block text-sm text-slate-600">Why, when, where, and who this guide is for</span>
            </span>
            <svg class="rg-accordion-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
        </summary>
        <div class="rg-accordion-body">
            <div class="rg-accordion-body-inner border-t border-slate-100">
                @foreach($activeItems as $key)
                    @php $meta = $items[$key]; @endphp
                    <div class="flex items-start gap-4 px-5 sm:px-6 py-4 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                        <span class="w-9 h-9 inline-flex items-center justify-center rounded-full {{ $meta['tone']['bg'] }} {{ $meta['tone']['fg'] }} shrink-0" aria-hidden="true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">{!! $meta['icon'] !!}</svg>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="text-[0.7rem] uppercase tracking-[0.18em] text-slate-500 font-bold mb-1">{{ $meta['label'] }}</div>
                            <p class="text-slate-700 text-[0.95rem] leading-relaxed">{{ trim($wwww[$key]) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </details>
@endif
