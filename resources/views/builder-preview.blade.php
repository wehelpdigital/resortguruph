<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Block preview · {{ $blockType }}</title>
    <meta name="viewport" content="width=1200">
    <meta name="robots" content="noindex,nofollow">

    {{-- Tailwind CDN gives us every utility class BlockRenderer emits.
         Splide assets so hero_slider mounts. Same versions as the public
         site so visual fidelity matches. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

    <style>
        html, body { margin: 0; padding: 0; background: #fff; }
        body {
            padding: 16px;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            /* Fixed canvas width so the parent iframe can scale predictably. */
            width: 1200px;
            min-height: 100px;
        }
        /* Disable interactions — the preview is for looking, not clicking. */
        body, body * { pointer-events: none !important; }
        .rg-preview-empty {
            color: #94a3b8;
            font-style: italic;
            text-align: center;
            padding: 48px 16px;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    @php
        $rendered = trim($rendered ?? '');
    @endphp
    @if($rendered === '')
        <div class="rg-preview-empty">
            Empty block ({{ $blockType }}) — add content in the editor to see it here.
        </div>
    @else
        {!! $rendered !!}
    @endif

    <script>
    (function () {
        // Mount every Splide carousel the renderer emitted. Each hero_slider
        // block embeds its own auto-init script too; this is a safety net
        // for any future splide-using block.
        function mountSplides() {
            if (typeof Splide === 'undefined') return;
            document.querySelectorAll('.splide').forEach(function (el) {
                if (el.classList.contains('is-initialized')) return;
                try { new Splide(el).mount(); } catch (e) {}
            });
        }

        // Report content height to the parent admin so the iframe wrapper
        // can grow to fit (no cropping). Two-step: fire once on initial
        // paint, then again after a short delay so async-mounted Splide
        // slides + lazy-loaded images settle before final measurement.
        function reportHeight() {
            var h = Math.max(
                document.documentElement.scrollHeight,
                document.body.scrollHeight
            );
            window.parent.postMessage({
                type: 'rg-preview-height',
                blockId: {{ (int) $blockId }},
                blockType: {!! json_encode($blockType) !!},
                height: h
            }, '*');
        }

        if (document.readyState !== 'loading') {
            mountSplides();
            reportHeight();
        } else {
            window.addEventListener('DOMContentLoaded', function () {
                mountSplides();
                reportHeight();
            });
        }
        window.addEventListener('load', function () {
            mountSplides();
            reportHeight();
            // One more pass after images decode — catches lazy-loaded thumbs
            // in attractions / hero_slider so their final natural height is
            // reflected in the wrapper.
            setTimeout(reportHeight, 250);
            setTimeout(reportHeight, 800);
        });
    })();
    </script>
</body>
</html>
