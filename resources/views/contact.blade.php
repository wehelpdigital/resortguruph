@extends('layouts.public')

@section('title') Contact — {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection
@section('meta_description') Get in touch with Tourist Guide PH. Email us at contact@touristguide.ph or follow along on social media. @endsection

@section('content')
@php
    $hasBlocks = strlen(trim($renderedBlocks ?? '')) > 0;
    $contactEmail = 'contact@touristguide.ph';
    // Social links — placeholder profile URLs. Update the `url` values to
    // the real accounts when ready (icons stay the same).
    $socials = [
        ['name' => 'Facebook',  'url' => 'https://www.facebook.com/touristguideph',  'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
        ['name' => 'Instagram', 'url' => 'https://www.instagram.com/touristguideph', 'path' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z'],
        ['name' => 'TikTok',    'url' => 'https://www.tiktok.com/@touristguideph',    'path' => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z'],
        ['name' => 'X',         'url' => 'https://x.com/touristguideph',              'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
        ['name' => 'YouTube',   'url' => 'https://www.youtube.com/@touristguideph',   'path' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z'],
    ];
@endphp

<div class="page-contact">
    @if($hasBlocks)
        {{-- Editorial hero from the contact-page static page. --}}
        {!! $renderedBlocks !!}
    @else
        <section class="py-14 md:py-20">
            <div class="mx-auto max-w-2xl px-4 text-center">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.2em] text-brand-600">Contact Us</p>
                <h1 class="mb-4 text-4xl font-extrabold text-slate-900 md:text-5xl">Get in Touch</h1>
                <p class="text-lg text-slate-600">Questions, partnership ideas, or a place we should add? We would love to hear from you.</p>
            </div>
        </section>
    @endif

    <section class="mx-auto max-w-2xl px-4 pt-12 pb-16 md:pt-16 md:pb-24">
        {{-- Email card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm md:p-10">
            <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-brand-600">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Email us</p>
            <a href="mailto:{{ $contactEmail }}" class="break-all text-2xl font-extrabold text-slate-900 transition hover:text-brand-600 md:text-3xl">{{ $contactEmail }}</a>
            <p class="mx-auto mt-3 max-w-md text-slate-600">We read every message ourselves and usually reply within a day or two.</p>
        </div>

        {{-- Social links --}}
        <div class="mt-10 text-center">
            <p class="mb-4 text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Follow Along</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                @foreach($socials as $s)
                    <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $s['name'] }}" title="{{ $s['name'] }}" class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:-translate-y-0.5 hover:border-brand-300 hover:text-brand-600 hover:shadow-md">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{{ $s['path'] }}"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
