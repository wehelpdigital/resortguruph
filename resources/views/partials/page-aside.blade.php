{{-- Sticky sidebar for long pages (desktop >= 1280px): "On this page"
     (filled by page-kit JS from the main column's h2s), share buttons,
     a trip-planning card and an optional business card.
     Params: shareTitle, shareUrl, business ['title','text','url','label'], top (HTML above the TOC) --}}
@php
    $shareUrl = $shareUrl ?? url()->current();
    $shareTitle = $shareTitle ?? '';
    $enc = rawurlencode($shareUrl); $encT = rawurlencode($shareTitle);
@endphp
<aside class="pk-aside" aria-label="Page tools">
    {!! $top ?? '' !!}
    <nav class="pk-toc" data-pk-toc-box aria-label="On this page">
        <p class="pk-toc__title">On this page</p>
        <ol data-pk-toc></ol>
    </nav>

    <div class="pk-card pk-card--ink">
        <p class="text-[.7rem] font-extrabold tracking-[.18em] uppercase text-sun-300">Free trip planner</p>
        <p class="pk-card__title mt-2">Turn this guide into a plan</p>
        <p class="mt-2 text-sm text-white/75 leading-relaxed">Pick the spots, food stops and stays you like and get a day-by-day plan you can share.</p>
        <a href="{{ url('/create-your-adventure') }}" class="rg-btn rg-btn--coral w-full mt-4">Plan my trip <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a>
    </div>

    <div class="pk-card">
        <p class="text-[.7rem] font-extrabold tracking-[.18em] uppercase text-slate-500">Share this guide</p>
        <div class="pk-share mt-3">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $enc }}" target="_blank" rel="noopener" aria-label="Share on Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v1.7H7.6v3.3h2.7V22H14v-10.2h2.7l.4-3.3z"/></svg></a>
            <a href="https://twitter.com/intent/tweet?url={{ $enc }}&text={{ $encT }}" target="_blank" rel="noopener" aria-label="Share on X"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.8 3h3.1l-6.8 7.7 8 10.3h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.8 3h6.4l4.4 5.9zm-1.1 16.2h1.7L7.4 4.7H5.6z"/></svg></a>
            <a href="https://wa.me/?text={{ $encT }}%20{{ $enc }}" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-3.9-4.7-4.1-.1-.2-1.1-1.5-1.1-2.9s.7-2.1 1-2.4c.3-.3.6-.3.8-.3h.6c.2 0 .4 0 .6.5l.9 2.1c.1.2.1.4 0 .6l-.4.6-.4.4c-.1.2-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.3 2.4 1.5.3.1.5.1.7-.1l.9-1.1c.2-.3.4-.2.7-.1l2 1c.3.1.5.2.5.3.1.2.1.6-.1 1.2z"/></svg></a>
            <a href="https://t.me/share/url?url={{ $enc }}&text={{ $encT }}" target="_blank" rel="noopener" aria-label="Share on Telegram"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.3l-3.2 15.1c-.2 1-.9 1.3-1.7.8l-4.8-3.5-2.3 2.2c-.3.3-.5.5-1 .5l.3-4.9 8.9-8c.4-.3-.1-.5-.6-.2L6.5 13.2l-4.7-1.5c-1-.3-1-1 .2-1.5L20.5 3c.9-.3 1.6.2 1.4 1.3z"/></svg></a>
            <button type="button" data-pk-copy="{{ $shareUrl }}" aria-label="Copy link" title="Copy link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/></svg></button>
        </div>
    </div>

    @if(!empty($business))
        <div class="pk-card pk-card--sand">
            <p class="pk-card__title text-ink-900">{{ $business['title'] }}</p>
            <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $business['text'] }}</p>
            <a href="{{ $business['url'] }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-bold text-coral-600 hover:text-coral-700">{{ $business['label'] }} <span aria-hidden="true">&rarr;</span></a>
        </div>
    @endif
</aside>
