@extends('layouts.public')

@php $siteName = \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph'); @endphp

@section('title') Sitemap — {{ $siteName }} @endsection
@section('meta_description') Browse every page on {{ $siteName }} in one place: destinations by region, resorts, food finds, fiestas, blog articles, and more. @endsection
@section('canonical') {{ url('/sitemap') }} @endsection

@section('jsonld')
@php
    $mainLinks = collect($sections)->firstWhere('group', 'Explore')['links'] ?? [];
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Sitemap', 'item' => url('/sitemap')],
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Sitemap',
    'description' => 'Every public page on ' . $siteName . '.',
    'url' => url('/sitemap'),
    'mainEntity' => [
        '@type' => 'ItemList',
        'name' => 'Main pages',
        'itemListElement' => collect($mainLinks)->values()->map(fn ($l, $i) => [
            '@type' => 'SiteNavigationElement',
            'position' => $i + 1,
            'name' => $l['label'],
            'url' => $l['url'],
        ])->all(),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@section('content')
@php $totalLinks = collect($sections)->sum(fn ($s) => count($s['links'])); @endphp

<section class="bg-gradient-to-br from-brand-50 via-white to-emerald-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 md:py-16 text-center">
        <p class="mb-3"><span class="inline-flex items-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100 px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em]">Sitemap</span></p>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900">Everything on <span class="font-brand font-normal" style="color:#c0392b;font-size:1.1em;line-height:1">{{ $siteName }}</span></h1>
        <p class="mx-auto mt-3 max-w-2xl text-lg text-slate-600">A quick map of every page on the site, {{ number_format($totalLinks) }} links in all. Looking for the machine-readable version? <a href="{{ route('sitemap') }}" class="font-semibold text-brand-600 hover:underline">sitemap.xml</a>.</p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
    <div class="gap-6 [column-fill:_balance] sm:columns-2 lg:columns-3">
        @foreach($sections as $s)
            <section class="mb-6 break-inside-avoid rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="mb-1 text-[11px] font-bold uppercase tracking-wider text-brand-600">{{ $s['group'] }}</p>
                <h2 class="mb-3 flex items-baseline gap-2 text-lg font-bold text-slate-900">
                    {{ $s['title'] }}
                    <span class="text-xs font-normal text-slate-400">{{ count($s['links']) }}</span>
                </h2>
                <ul class="space-y-1.5 text-sm">
                    @foreach($s['links'] as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="capitalize hover:text-brand-600 hover:underline {{ ($link['strong'] ?? false) ? 'font-semibold text-slate-900' : 'text-slate-600' }}">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
</div>
@endsection
