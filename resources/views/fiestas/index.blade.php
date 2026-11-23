@extends('layouts.public')

@section('title', 'Philippine Fiestas and Festivals Guide: Year-Round Calendar by Region | Tourist Guide Ph')
@section('meta_description')The complete guide to Philippine fiestas and festivals. Sinulog, MassKara, Panagbenga, Kadayawan, Ati-Atihan, Pahiyas, and {{ $totalCount }} more, organized by region and month.@endsection
@section('canonical'){{ route('fiestas.index') }}@endsection

@section('content')
@php
    $all = collect($grouped)->flatten(1);
    $nowMonth = (int) now()->format('n');
    $thisMonth = $all->where('month', $nowMonth)->filter(fn ($f) => $f->cover_image_path)->sortByDesc('pageviews_30d')->take(4)->values();
    if ($thisMonth->count() < 2) {
        $nowMonth = $nowMonth === 12 ? 1 : $nowMonth + 1;
        $thisMonth = $all->where('month', $nowMonth)->filter(fn ($f) => $f->cover_image_path)->sortByDesc('pageviews_30d')->take(4)->values();
    }
    $monthName = \App\Models\RgFiesta::MONTH_LABELS[$nowMonth] ?? '';
    $byMonth = $all->groupBy('month')->map->count();
    $heroSlides = $all->filter(fn ($f) => $f->cover_image_path)->sortByDesc('pageviews_30d')->take(4)
        ->map(fn ($f) => ['src' => $f->coverUrl(), 'title' => preg_replace('/\s*\(.*\)$/', '', $f->name), 'caption' => trim(($f->city_or_town ?: '') . ($f->province ? ', ' . $f->province : ''), ', ')])->values()->all();
    $regionCount = collect($grouped)->filter(fn ($c) => $c->isNotEmpty())->count();
    $cleanName = fn ($n) => preg_replace('/\s*\(.*\)$/', '', $n);
@endphp

@include('partials.page-hero', [
    'slides' => $heroSlides,
    'crumbs' => [['Home', url('/')], ['Things to Do', route('activities.index')], ['Fiestas and Festivals', null]],
    'kicker' => 'Festival calendar',
    'title' => 'Philippine Fiestas and Festivals',
    'lede' => 'Every town has at least one, every region has a dozen. Here are ' . $totalCount . ' fiestas across the islands, sorted by region and month so a trip lines up with the celebration that fits your dates.',
    'chips' => [
        ['icon' => 'calendar', 'sun' => true, 'text' => $totalCount . ' festivals'],
        ['icon' => 'pin', 'text' => $regionCount . ' regions'],
        ['icon' => 'sun', 'text' => 'Something on every month'],
    ],
    'actions' => [
        ['label' => 'What is on in ' . $monthName, 'url' => '#this-month', 'style' => 'coral'],
        ['label' => 'Browse the calendar', 'url' => '#calendar', 'style' => 'glass'],
    ],
])

@if($thisMonth->isNotEmpty())
<section id="this-month" class="relative overflow-hidden bg-ink-900 text-white py-16 md:py-20 isolate" style="scroll-margin-top:var(--rg-header-h,76px)">
    <div class="absolute -left-40 -bottom-40 w-[34rem] h-[34rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(239,91,58,.3), transparent 65%)"></div>
    <div class="pk-wrap">
        <p class="pk-eyebrow !text-sun-300">Happening in {{ $monthName }}</p>
        <h2 class="pk-h2 !text-white max-w-2xl">Time your trip with a fiesta</h2>
        {{-- a compact list: the same fiestas have their photo cards in the calendar below --}}
        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($thisMonth as $f)
                <a href="{{ route('fiestas.show', $f->slug) }}" class="group flex items-center gap-4 rounded-2xl bg-white/5 border border-white/10 px-4 py-3.5 hover:bg-white/10 transition">
                    <span class="shrink-0 w-14 h-14 rounded-xl bg-white/10 flex flex-col items-center justify-center leading-none">
                        <span class="text-[.62rem] font-extrabold uppercase tracking-wider text-sun-300">{{ substr(\App\Models\RgFiesta::MONTH_LABELS[(int) $f->month] ?? $monthName, 0, 3) }}</span>
                        <span class="mt-1 font-display font-extrabold text-lg">{{ preg_match('~\b(\d{1,2})\b~', (string) $f->date_label, $dm) ? $dm[1] : 'All' }}</span>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block font-display font-extrabold leading-snug group-hover:text-sun-200">{{ $cleanName($f->name) }}</span>
                        <span class="block mt-0.5 text-xs text-white/65 truncate">{{ trim(($f->city_or_town ?: '') . ($f->province ? ', ' . $f->province : ''), ', ') }}@if($f->date_label) · {{ \Illuminate\Support\Str::limit($f->date_label, 34) }}@endif</span>
                    </span>
                    <span class="w-4 h-4 inline-flex text-white/50 group-hover:text-white shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<div id="calendar" style="scroll-margin-top:var(--rg-header-h,76px)"></div>
<div class="pk-jump" data-fz-bar>
    <div class="pk-jump__row max-w-7xl mx-auto" role="toolbar" aria-label="Filter by month">
        <a href="#calendar" class="is-on" data-fz-month="0">All months</a>
        @foreach(\App\Models\RgFiesta::MONTH_LABELS as $m => $label)
            @if(($byMonth[$m] ?? 0) > 0)
                <a href="#calendar" data-fz-month="{{ $m }}">{{ substr($label, 0, 3) }} <span class="opacity-60">{{ $byMonth[$m] }}</span></a>
            @endif
        @endforeach
    </div>
</div>

<div class="pk-wrap py-12 md:py-16">
    <div class="flex flex-wrap gap-2 mb-12">
        @foreach(\App\Models\RgFiesta::REGION_LABELS as $key => $label)
            @if(($grouped[$key] ?? collect())->isNotEmpty())
                <a href="#region-{{ $key }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:border-ink-900 hover:text-ink-900 transition">
                    {{ $label }} <span class="text-xs text-slate-400">{{ $grouped[$key]->count() }}</span>
                </a>
            @endif
        @endforeach
    </div>

    @foreach(\App\Models\RgFiesta::REGION_LABELS as $key => $label)
        @php $regionRows = $grouped[$key] ?? collect(); @endphp
        @if($regionRows->isEmpty()) @continue @endif

        <section id="region-{{ $key }}" class="mb-16 last:mb-0" data-fz-region style="scroll-margin-top:calc(var(--rg-header-h,76px) + 4rem)">
            <div class="flex items-end justify-between gap-3 mb-6 flex-wrap">
                <div>
                    <p class="pk-eyebrow">Region</p>
                    <h2 class="pk-h2 !text-2xl md:!text-3xl">{{ $label }}</h2>
                </div>
                <p class="text-sm text-slate-500"><span data-fz-count>{{ $regionRows->count() }}</span> fiesta{{ $regionRows->count() === 1 ? '' : 's' }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($regionRows as $fiesta)
                    <a href="{{ route('fiestas.show', $fiesta->slug) }}" data-fz-card data-month="{{ (int) $fiesta->month }}"
                       class="group flex flex-col rounded-[1.25rem] border border-slate-200 bg-white overflow-hidden hover:shadow-xl hover:-translate-y-1 hover:border-slate-300 transition duration-300">
                        <span class="relative block aspect-[16/10] overflow-hidden bg-gradient-to-br from-amber-50 via-rose-50 to-sky-50">
                            @if($fiesta->coverUrl() !== '')
                                <img src="{{ $fiesta->coverUrl() }}" alt="{{ $fiesta->name }}" class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                            @endif
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-2.5 py-1 text-[11px] font-bold text-ink-900 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-coral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                                {{ \App\Models\RgFiesta::MONTH_LABELS[(int) $fiesta->month] ?? 'Movable' }}
                            </span>
                        </span>
                        <span class="flex-1 flex flex-col p-5">
                            <span class="text-xs font-semibold text-slate-500">{{ $fiesta->city_or_town ?? 'Philippines' }}@if($fiesta->province), {{ $fiesta->province }}@endif</span>
                            <span class="mt-1 font-display font-extrabold text-lg text-ink-900 leading-snug group-hover:text-brand-700 transition">{{ $fiesta->name }}</span>
                            @if($fiesta->summary)
                                <span class="mt-2 text-sm text-slate-600 leading-relaxed line-clamp-3">{{ $fiesta->summary }}</span>
                            @endif
                            @if($fiesta->date_label)
                                <span class="mt-auto pt-4 text-xs font-semibold text-coral-600">{{ \Illuminate\Support\Str::limit($fiesta->date_label, 60) }}</span>
                            @endif
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach

    <p class="hidden mt-4 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500" data-fz-empty>No fiestas listed for that month yet.</p>

    <div class="mt-16 rounded-[1.75rem] bg-sand-100 border border-sand-200 p-7 sm:p-10 flex flex-col md:flex-row md:items-center gap-6">
        <div class="flex-1">
            <p class="pk-eyebrow">Know one we missed?</p>
            <h2 class="pk-h2 !text-2xl sm:!text-3xl">Every town has a fiesta worth the trip</h2>
            <p class="mt-2 text-slate-600">The list grows as we add more. Big provincial festivals, small barangay feasts and movable Holy Week celebrations are all welcome.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('contact') }}" class="rg-btn rg-btn--coral rg-btn--lg">Suggest a fiesta</a>
            <a href="{{ url('/create-your-adventure') }}" class="rg-btn rg-btn--outline rg-btn--lg">Plan my trip</a>
        </div>
    </div>
</div>

<script>
(function () {
    var bar = document.querySelector('[data-fz-bar]');
    if (!bar) return;
    var pills = bar.querySelectorAll('[data-fz-month]');
    var empty = document.querySelector('[data-fz-empty]');
    pills.forEach(function (p) {
        p.addEventListener('click', function (e) {
            e.preventDefault();
            var m = p.getAttribute('data-fz-month'), shown = 0;
            pills.forEach(function (q) { q.classList.toggle('is-on', q === p); });
            document.querySelectorAll('[data-fz-region]').forEach(function (sec) {
                var n = 0;
                sec.querySelectorAll('[data-fz-card]').forEach(function (c) { var on = m === '0' || c.getAttribute('data-month') === m; c.style.display = on ? '' : 'none'; if (on) n++; });
                sec.style.display = n ? '' : 'none';
                var cnt = sec.querySelector('[data-fz-count]'); if (cnt) cnt.textContent = n;
                shown += n;
            });
            if (empty) empty.classList.toggle('hidden', shown > 0);
            var top = document.getElementById('calendar').getBoundingClientRect().top + window.scrollY - (parseInt(getComputedStyle(document.documentElement).getPropertyValue('--rg-header-h')) || 70);
            if (window.scrollY > top) window.scrollTo({ top: top, behavior: 'smooth' });
        });
    });
})();
</script>
@endsection
