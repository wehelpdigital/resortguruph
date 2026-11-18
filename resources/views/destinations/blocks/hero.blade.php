{{-- destcluster_hero: photo hero with the region name, in the shared
     page-kit style (partials/page-hero). Context: $meta, $heroImage,
     $spots, $keywords. Payload $p: eyebrow, show_breadcrumb. --}}
@php
    $eyebrow = trim((string) ($p['eyebrow'] ?? 'Destination Guide'));
    $slides = [];
    // Curated region landmark photo first (same one the homepage region cards use).
    $landmarkFile = isset($cluster) && is_string($cluster) ? 'rg-media/landmarks/' . $cluster . '.jpg' : null;
    if ($landmarkFile && is_file(storage_path('app/public/' . $landmarkFile))) $slides[] = ['src' => '/storage/' . $landmarkFile, 'title' => $meta['name']];
    foreach (array_slice($spots ?? [], 0, 4) as $sp) {
        if (!empty($sp['url'])) $slides[] = ['src' => $sp['url'], 'title' => $sp['name'] ?? '', 'caption' => $sp['location'] ?? ''];
    }
    if (!$slides && !empty($heroImage)) $slides[] = ['src' => $heroImage];
    $guideCount = isset($keywords) ? count($keywords) : 0;
    $spotCount = isset($spots) ? count($spots) : 0;
@endphp
@include('partials.page-hero', [
    'slides' => $slides,
    'crumbs' => ($p['show_breadcrumb'] ?? true) ? [['Home', url('/')], ['Destinations', route('destinations.index')], [$meta['name'], null]] : [],
    'kicker' => $eyebrow !== '' ? ucwords(strtolower($eyebrow)) : null,
    'title' => $meta['name'],
    'lede' => $meta['tagline'] ?? '',
    'chips' => [
        ['icon' => 'book', 'text' => $guideCount ? $guideCount . ' travel guide' . ($guideCount === 1 ? '' : 's') : ''],
        ['icon' => 'pin', 'text' => $spotCount ? $spotCount . ' tourist spot' . ($spotCount === 1 ? '' : 's') : ''],
    ],
    'actions' => [
        ['label' => 'Find a place to stay', 'url' => '#whats-in', 'style' => 'coral'],
        ['label' => 'Plan my trip', 'url' => url('/create-your-adventure'), 'style' => 'glass'],
    ],
])
