{{-- 3-column crossfading, CLICKABLE tourist-spot cards. Each tile stacks
     several cards (photo + name + location + what-it-is blurb) that crossfade
     on an independent random timer (see the .rg-fadetile script in
     cluster.blade.php). Only the visible card is clickable (pointer-events
     toggled with opacity). $columns = up to 3 arrays of
     ['url','name','location','desc','link']. --}}
@php $columns = array_values(array_filter($columns ?? [], fn ($c) => !empty($c))); @endphp
@if(!empty($columns))
    <div class="grid grid-cols-3 gap-2 md:gap-4">
        @foreach($columns as $col)
            <div class="rg-fadetile relative overflow-hidden rounded-xl md:rounded-2xl bg-slate-200 shadow-sm ring-1 ring-slate-900/5 {{ $aspect ?? 'aspect-[3/4]' }}">
                @foreach($col as $i => $sp)
                    <a href="{{ $sp['link'] ?? '#' }}" target="_blank" rel="noopener" class="rg-fadecard group absolute inset-0 block{{ $i === 0 ? ' is-on' : '' }}">
                        <img src="{{ $sp['url'] }}" alt="{{ $sp['name'] ?? '' }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <span class="pointer-events-none absolute inset-x-0 bottom-0 h-3/5 bg-gradient-to-t from-black/95 via-black/60 to-transparent"></span>
                        <span class="pointer-events-none absolute inset-x-0 bottom-0 p-2.5 md:p-4" style="text-shadow:0 1px 4px rgba(0,0,0,.8)">
                            <span class="block text-xs font-bold leading-tight text-white line-clamp-2 md:text-base">{{ $sp['name'] ?? '' }}</span>
                            @if(!empty($sp['location']))
                                <span class="mt-0.5 flex items-center gap-1 text-[10px] text-white/85 line-clamp-1 md:text-xs">
                                    <svg class="h-3 w-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $sp['location'] }}
                                </span>
                            @endif
                            @if(!empty($sp['desc']))
                                <span class="mt-1.5 hidden text-xs leading-snug text-white/90 line-clamp-2 md:block">{{ ucfirst($sp['desc']) }}</span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
@endif
