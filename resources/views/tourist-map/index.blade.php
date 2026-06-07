@extends('layouts.public')

@section('title', 'Philippine Tourist Map · Resort Guru PH')
@section('meta_description', 'Interactive map of the Philippines. Draw a circle anywhere and see every tourist destination, restaurant, activity, food, and fiesta inside it. Plan your trip by geography.')
@section('canonical', url('/philippine-tourist-map'))

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script defer src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-12">

    {{-- Hero --}}
    <header class="mb-8">
        <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-sky-700 mb-3">
            Philippine Tourist Map
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-5 max-w-4xl">
            Explore the Philippines
            <span class="text-sky-700">by geography</span>
        </h1>
        <p class="text-base sm:text-lg text-slate-700 leading-relaxed max-w-3xl">
            Drag a circle anywhere on the map and we will show you every destination, restaurant, activity, food, and fiesta inside it. {{ number_format($totalCount) }} pinned spots across the country. Useful when you have a base in mind and want to know what is around you within a day trip.
        </p>
    </header>

    {{-- Instructions banner --}}
    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 sm:p-5 mb-5 flex flex-col sm:flex-row gap-4 items-start">
        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-sky-600 text-white font-bold text-sm">1</span>
            <span class="text-sm text-slate-700">Click <span class="font-bold text-sky-700">Draw Search Area</span></span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-sky-600 text-white font-bold text-sm">2</span>
            <span class="text-sm text-slate-700">Drag on the map to size your circle</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-sky-600 text-white font-bold text-sm">3</span>
            <span class="text-sm text-slate-700">Release to see everything inside</span>
        </div>
    </div>

    {{-- Map container --}}
    <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-md mb-6">
        <div id="map" class="w-full" style="height: 65vh; min-height: 480px;"></div>

        {{-- Draw mode toggle button (top-right floating) --}}
        <button id="drawBtn" type="button"
                class="absolute top-3 right-3 z-[1000] inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-600 text-white text-sm font-bold shadow-lg hover:bg-sky-700 transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 3v18M3 12h18"/>
            </svg>
            <span>Draw Search Area</span>
        </button>

        {{-- Draw-mode banner --}}
        <div id="drawBanner" class="hidden absolute top-3 left-3 right-3 sm:left-1/2 sm:right-auto sm:-translate-x-1/2 z-[1000] inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900 text-white text-sm font-semibold shadow-lg max-w-fit mx-auto">
            <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
            Drag on the map to draw your circle. Press <kbd class="px-1.5 py-0.5 bg-slate-700 rounded text-xs">ESC</kbd> to cancel.
        </div>

        {{-- Last-drawn circle stats overlay --}}
        <div id="circleStats" class="hidden absolute bottom-3 left-3 z-[1000] px-3 py-2 rounded-xl bg-white shadow-lg text-xs sm:text-sm">
            <div class="font-bold text-slate-900"><span id="circleRadius"></span> km circle</div>
            <div class="text-slate-500"><span id="circleCount"></span> pinned spots inside</div>
        </div>
    </div>

    {{-- Footer summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
            <div class="text-xs uppercase tracking-wider text-emerald-700 font-bold mb-1">Destinations</div>
            <div class="text-2xl font-extrabold text-emerald-900">{{ number_format($countsByType['destination'] ?? 0) }}</div>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
            <div class="text-xs uppercase tracking-wider text-amber-700 font-bold mb-1">Restaurants</div>
            <div class="text-2xl font-extrabold text-amber-900">{{ number_format($countsByType['restaurant'] ?? 0) }}</div>
        </div>
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-3">
            <div class="text-xs uppercase tracking-wider text-indigo-700 font-bold mb-1">Activities</div>
            <div class="text-2xl font-extrabold text-indigo-900">{{ number_format($countsByType['activity'] ?? 0) }}</div>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3">
            <div class="text-xs uppercase tracking-wider text-rose-700 font-bold mb-1">Foods</div>
            <div class="text-2xl font-extrabold text-rose-900">{{ number_format($countsByType['food'] ?? 0) }}</div>
        </div>
        <div class="rounded-xl border border-purple-200 bg-purple-50 p-3 col-span-2 sm:col-span-1">
            <div class="text-xs uppercase tracking-wider text-purple-700 font-bold mb-1">Fiestas</div>
            <div class="text-2xl font-extrabold text-purple-900">{{ number_format($countsByType['fiesta'] ?? 0) }}</div>
        </div>
    </div>

    <p class="text-sm text-slate-500 leading-relaxed">
        Map tiles by <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener" class="underline">OpenStreetMap</a>. Pins are placed at the city or region centroid; actual venues may sit a few kilometers off the marker. Some activities and dishes are listed by their most-associated city even though they happen across the country.
    </p>
</div>

{{-- ============ MODAL ============ --}}
<div id="resultsModal" class="hidden fixed inset-0 z-[2000]">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeResultsModal()"></div>
    <div class="absolute inset-0 flex items-start sm:items-center justify-center p-4 pointer-events-none">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col pointer-events-auto">

            {{-- Modal header --}}
            <div class="p-5 sm:p-6 border-b border-slate-200 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight mb-1">
                        Inside your <span id="modalRadius" class="text-sky-700"></span> circle
                    </h2>
                    <p class="text-sm text-slate-500"><span id="modalCount"></span> pinned spots</p>
                </div>
                <button type="button" onclick="closeResultsModal()" class="shrink-0 w-10 h-10 inline-flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 transition" aria-label="Close">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M6 18L18 6"/></svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div id="modalBody" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">
                {{-- populated by JS --}}
            </div>

            {{-- Modal footer --}}
            <div class="p-4 sm:p-5 border-t border-slate-200 flex items-center justify-between gap-3 flex-wrap">
                <button type="button" onclick="closeResultsModal()" class="text-sm text-slate-600 hover:text-slate-900 font-semibold">
                    Close
                </button>
                <button type="button" onclick="closeResultsModal();document.getElementById('drawBtn').click();" class="px-4 py-2 rounded-full bg-sky-600 text-white text-sm font-bold hover:bg-sky-700 transition inline-flex items-center gap-2">
                    Draw a new circle
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // Wait for Leaflet to load (defer-loaded above).
    const start = () => {
        if (typeof L === 'undefined') { setTimeout(start, 50); return; }
        initMap();
    };

    const POINTS = @json($points);
    const SECTIONS = [
        { key: 'destination', label: 'Destinations',   color: 'emerald', emoji: '🏖️' },
        { key: 'restaurant',  label: 'Restaurants',    color: 'amber',   emoji: '🍽️' },
        { key: 'activity',    label: 'Activities',     color: 'indigo',  emoji: '🎪' },
        { key: 'food',        label: 'Local Dishes',   color: 'rose',    emoji: '🥘' },
        { key: 'fiesta',      label: 'Fiestas',        color: 'purple',  emoji: '🎉' },
    ];
    const MAX_PER_SECTION = 30;

    let map, circleLayer, drawCenter, drawing = false;

    function initMap() {
        map = L.map('map', { scrollWheelZoom: true, zoomControl: true })
               .setView([12.8797, 121.7740], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
        }).addTo(map);

        // Draw-button toggle
        document.getElementById('drawBtn').addEventListener('click', enterDrawMode);

        // ESC cancels mid-draw
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && drawing) exitDrawMode(/*hideCircle*/ true);
        });

        // Map event hooks
        map.on('mousedown', onMouseDown);
        map.on('mousemove', onMouseMove);
        map.on('mouseup',   onMouseUp);
    }

    function enterDrawMode() {
        drawing = true;
        document.getElementById('drawBanner').classList.remove('hidden');
        document.getElementById('drawBtn').classList.add('hidden');
        document.getElementById('map').style.cursor = 'crosshair';
        map.dragging.disable();
        if (circleLayer) { map.removeLayer(circleLayer); circleLayer = null; }
        document.getElementById('circleStats').classList.add('hidden');
    }

    function exitDrawMode(hideCircle) {
        drawing = false;
        drawCenter = null;
        document.getElementById('drawBanner').classList.add('hidden');
        document.getElementById('drawBtn').classList.remove('hidden');
        document.getElementById('map').style.cursor = '';
        map.dragging.enable();
        if (hideCircle && circleLayer) {
            map.removeLayer(circleLayer);
            circleLayer = null;
        }
    }

    function onMouseDown(e) {
        if (!drawing) return;
        drawCenter = e.latlng;
        if (circleLayer) map.removeLayer(circleLayer);
        circleLayer = L.circle(drawCenter, {
            radius: 1,
            color: '#0284c7', weight: 2, fillColor: '#0ea5e9', fillOpacity: 0.18,
        }).addTo(map);
    }

    function onMouseMove(e) {
        if (!drawing || !drawCenter) return;
        const r = drawCenter.distanceTo(e.latlng);
        circleLayer.setRadius(r);
    }

    function onMouseUp(e) {
        if (!drawing || !drawCenter) return;
        const radiusM = drawCenter.distanceTo(e.latlng);
        // Ignore stray clicks with radius < 1km (probably a misclick).
        if (radiusM < 1000) {
            exitDrawMode(true);
            return;
        }
        exitDrawMode(false);
        showResults(drawCenter, radiusM);
    }

    function showResults(center, radiusM) {
        const inside = POINTS.filter(p => {
            return center.distanceTo(L.latLng(p.lat, p.lng)) <= radiusM;
        });
        const km = Math.round(radiusM / 1000);

        // Update circle-stats overlay
        document.getElementById('circleRadius').textContent = km;
        document.getElementById('circleCount').textContent = inside.length;
        document.getElementById('circleStats').classList.remove('hidden');

        // Build modal
        document.getElementById('modalRadius').textContent = km + ' km';
        document.getElementById('modalCount').textContent = inside.length;
        const body = document.getElementById('modalBody');
        body.innerHTML = '';

        if (inside.length === 0) {
            body.innerHTML = '<div class="text-center py-12"><p class="text-slate-500">No pinned spots inside this circle. Try drawing a wider one, or center it over a populated province.</p></div>';
        } else {
            for (const section of SECTIONS) {
                const items = inside.filter(p => p.type === section.key);
                if (items.length === 0) continue;
                body.appendChild(renderSection(section, items));
            }
        }

        document.getElementById('resultsModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function renderSection(section, items) {
        const wrap = document.createElement('section');
        const shown = items.slice(0, MAX_PER_SECTION);
        const overflow = items.length - shown.length;
        const colorMap = {
            emerald: 'bg-emerald-100 text-emerald-800 border-emerald-200',
            amber:   'bg-amber-100 text-amber-800 border-amber-200',
            indigo:  'bg-indigo-100 text-indigo-800 border-indigo-200',
            rose:    'bg-rose-100 text-rose-800 border-rose-200',
            purple:  'bg-purple-100 text-purple-800 border-purple-200',
        };
        const pillCls = colorMap[section.color] || 'bg-slate-100 text-slate-700 border-slate-200';

        wrap.innerHTML = `
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xl" aria-hidden="true">${section.emoji}</span>
                <h3 class="text-lg font-extrabold text-slate-900">${section.label}</h3>
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-full border ${pillCls}">${items.length}</span>
            </div>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2"></ul>
            ${overflow > 0 ? `<p class="text-xs text-slate-500 mt-2">… and ${overflow} more. Narrow the circle to see fewer.</p>` : ''}
        `;
        const ul = wrap.querySelector('ul');
        for (const item of shown) {
            const li = document.createElement('li');
            li.innerHTML = `
                <a href="${item.url}" class="block px-3 py-2.5 rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition">
                    <div class="text-sm font-bold text-slate-900 leading-tight">${escapeHtml(item.name)}</div>
                    <div class="text-xs text-slate-500 mt-0.5">${escapeHtml(item.city)}</div>
                </a>
            `;
            ul.appendChild(li);
        }
        return wrap;
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
        })[c]);
    }

    window.closeResultsModal = function () {
        document.getElementById('resultsModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    start();
})();
</script>
@endpush
@endsection
