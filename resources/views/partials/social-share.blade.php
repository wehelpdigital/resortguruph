{{--
    Social share row. Props:
      $url      → absolute share URL (defaults to current).
      $title    → string to seed share text.
      $align    → 'start'|'end'|'between' (default 'between').
    Each platform button uses its brand color for the icon (matches the
    recognizable Facebook blue, Twitter/X black, LinkedIn blue, WhatsApp
    green, Telegram blue).
--}}
@php
    $shareUrl = $url ?? url()->current();
    $shareTitle = trim($title ?? \App\Models\RgSetting::get('site_name', 'Resort Guru PH'));
    $encUrl = rawurlencode($shareUrl);
    $encTitle = rawurlencode($shareTitle);
    $align = $align ?? 'between';
    $justifyClass = ['start' => 'justify-start', 'end' => 'justify-end', 'between' => 'justify-between'][$align] ?? 'justify-start';
@endphp
<div class="not-prose flex items-center gap-3 flex-wrap {{ $justifyClass }} my-6 py-4 border-y border-slate-200">
    <span class="text-sm text-slate-600 font-medium">Share this:</span>
    <div class="flex items-center gap-2 flex-wrap">
        <a class="rg-share-btn rg-share-fb" href="https://www.facebook.com/sharer/sharer.php?u={{ $encUrl }}" target="_blank" rel="noopener" aria-label="Share on Facebook">
            <svg viewBox="0 0 24 24" fill="#1877F2"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46H15.19c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.128 22 16.991 22 12Z"/></svg>
            <span>Facebook</span>
        </a>
        <a class="rg-share-btn rg-share-x" href="https://twitter.com/intent/tweet?url={{ $encUrl }}&text={{ $encTitle }}" target="_blank" rel="noopener" aria-label="Share on X">
            <svg viewBox="0 0 24 24" fill="#000000"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77Z"/></svg>
            <span>X</span>
        </a>
        <a class="rg-share-btn rg-share-li" href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encUrl }}" target="_blank" rel="noopener" aria-label="Share on LinkedIn">
            <svg viewBox="0 0 24 24" fill="#0A66C2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286ZM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124ZM7.119 20.452H3.554V9H7.12v11.452ZM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003Z"/></svg>
            <span>LinkedIn</span>
        </a>
        <a class="rg-share-btn rg-share-wa" href="https://wa.me/?text={{ $encTitle }}%20{{ $encUrl }}" target="_blank" rel="noopener" aria-label="Share on WhatsApp">
            <svg viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
            <span>WhatsApp</span>
        </a>
        <a class="rg-share-btn rg-share-tg" href="https://t.me/share/url?url={{ $encUrl }}&text={{ $encTitle }}" target="_blank" rel="noopener" aria-label="Share on Telegram">
            <svg viewBox="0 0 24 24" fill="#26A5E4"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
            <span>Telegram</span>
        </a>
        <button type="button" class="rg-share-btn rg-share-copy" data-rg-copy="{{ $shareUrl }}" aria-label="Copy link">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
            <span>Copy link</span>
        </button>
    </div>
</div>
<script>
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-rg-copy]');
        if (!btn) return;
        e.preventDefault();
        navigator.clipboard.writeText(btn.getAttribute('data-rg-copy')).then(function () {
            const span = btn.querySelector('span');
            const original = span ? span.textContent : 'Copy link';
            if (span) span.textContent = 'Copied!';
            setTimeout(function () { if (span) span.textContent = original; }, 1600);
        });
    });
</script>
