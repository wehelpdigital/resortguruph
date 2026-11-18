{{--
    Site logo. Props: $variant = 'light' (on white) | 'dark' (on dark),
    $size = 'sm' | 'md' (default). Set rg_settings.logo_style = 'classic'
    to bring back the original purple pig + Tahu script wordmark.
--}}
@php
    $logoStyle = \App\Models\RgSetting::get('logo_style', 'modern');
    $variant = $variant ?? 'light';
    $size = $size ?? 'md';
@endphp
@if($logoStyle === 'classic')
    <span class="inline-flex items-center">
        <img src="{{ asset('images/logo.webp') }}" alt="" class="{{ $size === 'sm' ? 'h-9' : 'h-10 sm:h-11 lg:h-12' }} w-auto" width="160" height="100" @if($variant === 'dark') style="filter:brightness(0) invert(1)" @endif>
        <span class="font-brand {{ $size === 'sm' ? 'text-3xl' : 'text-[1.85rem] sm:text-[2.1rem] lg:text-[2.35rem]' }} leading-none font-light pt-1.5 whitespace-nowrap">
            @if($variant === 'dark')
                <span class="text-white">TouristGuide.Ph</span>
            @else
                <span style="color:#2980b9">Tourist</span><span style="color:#c0392b">Guide</span><span style="color:#f39c12">.Ph</span>
            @endif
        </span>
    </span>
@else
    <span class="rg-logo rg-logo--{{ $variant }} rg-logo--{{ $size }}">
        <img src="{{ asset('images/brand/logo-mark.svg') }}" alt="" class="rg-logo__mark" width="48" height="48">
        <span class="rg-logo__wm"><span class="rg-logo__t">Tourist</span><span class="rg-logo__g">Guide</span><span class="rg-logo__p">.ph</span></span>
    </span>
@endif
