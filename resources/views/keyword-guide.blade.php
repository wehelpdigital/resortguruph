@extends('layouts.public')

{{-- Designed layout for keyword guide pages (/beach-resort-in-palawan, /restaurant-in-bgc ...).
     KeywordPageController renders each content block separately ($parts) and this view
     places them in sections: overview, where to stay, things to do, the full guide,
     food, getting there, FAQ, reviews, keep exploring. Every block's content is still on
     the page (same H1, headings, text, links and JSON-LD), only the arrangement changes.
     The live editor and rg_settings.guide_layout = classic use keyword-page.blade.php. --}}

@section('title') {{ $page->meta_title ?: $page->title }} @endsection
@section('meta_description') {{ $page->meta_description }} @endsection
@section('meta_keywords') {{ $page->meta_keywords }} @endsection
@section('canonical') {{ $page->canonical_url ?: url($page->slug) }} @endsection
@if($page->og_image_path)
    @php $ogUrl = preg_match('#^https?://#i', $page->og_image_path) ? $page->og_image_path : asset('storage/' . ltrim($page->og_image_path, '/')); @endphp
    @section('og_image'){{ $ogUrl }}@endsection
@elseif(!empty($hero['slides'][0]['src']))
    @section('og_image'){{ url(preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $hero['slides'][0]['src'])) }}@endsection
@endif
@section('jsonld') {!! $jsonld ?? '' !!} @endsection

@php
    // ---------- names ----------
    $properTitle = function ($s) {
        $small = ['of','the','in','at','on','and','a','an','to','for','by','from','with'];
        $words = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($words as $i => $w) {
            if ($w === '') continue;
            $words[$i] = ($i === 0 || !in_array($w, $small, true)) ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8') : $w;
        }
        $acr = ['Bgc' => 'BGC', 'Moa' => 'MOA', 'Qc' => 'QC', 'Cdo' => 'CDO', 'Sm' => 'SM', 'Atc' => 'ATC', 'Up' => 'UP', 'Ust' => 'UST', 'Edsa' => 'EDSA', 'Naia' => 'NAIA', 'Ncr' => 'NCR', 'Pitx' => 'PITX'];
        return preg_replace_callback('/\b(' . implode('|', array_keys($acr)) . ')\b/', fn ($m) => $acr[$m[1]], implode(' ', $words));
    };
    $isFood = $keyword->category === 'food';

    $stored = $page->h1 ?: $page->title;
    [$eyebrowText, $h1Display] = str_contains($stored, ' ~~ ') ? explode(' ~~ ', $stored, 2) : [ucwords($keyword->phrase), $stored];

    // ---------- blocks by role ----------
    $parts = collect($parts);
    $of = fn (array $types) => $parts->filter(fn ($p) => in_array($p['type'], $types, true));
    $first = fn (string $type) => $parts->firstWhere('type', $type);
    $htmlOf = fn (array $types) => \App\Support\PageKit::balance($of($types)->pluck('html')->implode(''));

    $placed = ['hero_slider', 'subtitle_intro', 'tldr_card', 'wwww_card', 'editor_rating', 'facts_list', 'quick_facts', 'local_tip',
        'we_recommend_band', 'listing_slot', 'listing_block', 'attractions', 'adventures_band', 'foods_to_try', 'restaurant_recs_band', 'short_version',
        'how_to_get_to', 'map_embed', 'faq', 'reviews_band', 'nearby_destinations', 'related_blogs', 'external_guides', 'tag_pills', 'author'];
    $streamHtml = \App\Support\PageKit::figures(\App\Support\PageKit::balance($parts->reject(fn ($p) => in_array($p['type'], $placed, true))->pluck('html')->implode('')));

    // the summary: the TL;DR card, or the older "short version" block on pages that only have that
    $tldr = $first('tldr_card')['payload'] ?? ($first('short_version')['payload'] ?? []);
    $wwww = $first('wwww_card')['payload'] ?? [];
    $rating = $first('editor_rating')['payload'] ?? null;
    $facts = $of(['facts_list', 'quick_facts'])->flatMap(fn ($p) => $p['payload']['cards'] ?? [])->values()->all();
    // seeded text writes some acronyms as words ("Bgc packs ...") and carries the odd em dash in labels
    $fixText = fn ($v) => is_string($v) ? str_replace([' — ', '—'], [', ', ', '], preg_replace_callback('~\b(Bgc|Moa|Cdo|Qc|Nlex|Slex|Tplex|Sctex|Naia|Uptc)\b~', fn ($m) => strtoupper($m[1]), $v)) : $v;
    // a seeded summary cut off mid-word ("Bauang next door h...") ends at its last whole sentence instead
    $whole = function ($v) { $v = trim((string) $v); if (!preg_match('~(\.\.\.|…)$~u', $v)) return $v; $cut = mb_strrpos($v, '. '); return $cut !== false && $cut > 40 ? mb_substr($v, 0, $cut + 1) : $v; };
    $tips = $of(['local_tip'])->pluck('payload')->filter(fn ($t) => trim($t['body'] ?? '') !== '')->map(fn ($t) => array_map($fixText, (array) $t))->values()->all();
    $attr = $first('attractions')['payload'] ?? null;
    // some seeded payloads hold pre-escaped text ("People&#039;s Park"): decode once so Blade escapes it only once
    $attrItems = array_values(array_filter(array_map(
        fn ($i) => array_map(fn ($v) => is_string($v) ? html_entity_decode($v, ENT_QUOTES | ENT_HTML5, 'UTF-8') : $v, (array) $i),
        $attr['items'] ?? []
    ), fn ($i) => trim($i['name'] ?? '') !== ''));
    // The seeded "Tourist spots in X" list (GuideHtml .gh-spots) is the richest things-to-do
    // content: it leads the Things to do section instead of sitting deep in the full guide,
    // and the short attractions grid (the same places) is dropped once the list covers it.
    $spotsHtml = '';
    if (preg_match('~<section class="gh-spots not-prose">.*?</section>~s', $streamHtml, $sm)) {
        $spotsHtml = $sm[0];
        $streamHtml = trim(str_replace($sm[0], '', $streamHtml));
        // a heading that introduced the list ("Why X Is Worth The Trip") and now leads straight into the next titled section goes too
        $streamHtml = (string) preg_replace('~<div class="my-6">\s*</div>~', '', $streamHtml);
        $streamHtml = trim((string) preg_replace('~<h2 class="rg-h[^"]*">[^<]*</h2>\s*(?=<div class="my-6">\s*<section class="gh-(?:history|fests|reads|map|spots)|<h2|$)~', '', $streamHtml));
    }
    $spotsText = mb_strtolower(html_entity_decode(strip_tags($spotsHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $attrCovered = $attrItems && $spotsHtml !== ''
        && count(array_filter($attrItems, fn ($i) => str_contains($spotsText, mb_strtolower(trim($i['name']))))) >= ceil(count($attrItems) * 0.8);
    $showAttrGrid = $attrItems && !$attrCovered;
    if ($attrCovered && trim($attr['heading'] ?? '') !== '') {
        // keep the attractions heading ("What's in La Union?") on the page as the list's kicker
        $spotsHtml = preg_replace('~<p class="gh-eyebrow">Things to see</p>~', '<p class="gh-eyebrow">' . e(html_entity_decode(trim($attr['heading']), ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</p>', $spotsHtml, 1);
    }
    // on "tourist spot / where to go / things to do" guides the spots come before the stays
    $spotsFirst = ($spotsHtml !== '' || $attrItems) && preg_match('~tourist spots?|where to go|things to do|attractions?|what to do|places to visit|itinerary~i', $keyword->phrase);
    $getting = $first('how_to_get_to')['payload'] ?? null;
    $map = $first('map_embed')['payload'] ?? null;
    // one map per page: the Getting there map replaces the seeded "Where X sits on the map" card in the guide
    if (!empty($map['embed_url'])) $streamHtml = trim((string) preg_replace('~(<div class="my-6">\s*)?<section class="gh-map not-prose">.*?</section>(\s*</div>)?~s', '', $streamHtml));
    $faqBlock = $first('faq')['payload'] ?? null;
    $faqItems = $of(['faq'])->flatMap(fn ($p) => $p['payload']['items'] ?? [])->filter(fn ($i) => !empty($i['question']))->values()->all();
    $author = $first('author')['payload'] ?? null;

    $stayHtml = $htmlOf(['we_recommend_band']);
    $advHtml = $htmlOf(['adventures_band']);
    $foodHtml = $htmlOf(['foods_to_try', 'restaurant_recs_band']);
    $reviewsHtml = $htmlOf(['reviews_band']);
    $exploreHtml = $htmlOf(['nearby_destinations', 'related_blogs']);
    $tagHtml = $htmlOf(['tag_pills']);
    $guidesHtml = $htmlOf(['external_guides']);
    $extHtml = $tagHtml . $guidesHtml;

    // the place this guide is about: "How to get to El Nido" / "What's in El Nido?" / "Our take on El Nido"
    $place = null;
    foreach ([[$getting['heading'] ?? '', '/^How to get to (.+)$/i'], [$attr['heading'] ?? '', "/^What['’]?s in (.+?)\\??$/i"], [$rating['title'] ?? '', '/^Our take on (.+)$/i']] as [$h, $re]) {
        if ($h && preg_match($re, trim($h), $m)) { $place = trim($m[1]); break; }
    }
    // else the place named in the keyword ("restaurant in moalboal"), then the region
    $kwPlace = preg_match('/\b(?:in|at|near)\s+(.+)$/i', $keyword->phrase, $kpm) ? $properTitle($kpm[1]) : '';
    $place = $place ?: ($kwPlace ?: ($cluster['name'] ?? $properTitle(preg_replace('/^(beach\s+resort|resort|hotel|hotels|airbnb|restaurant|restaurants|where to eat|tourist spots?)\s+(in|at|near)\s+/i', '', $keyword->phrase))));
    // seeded getting-there text that still carries its template placeholder
    if (is_array($getting)) array_walk_recursive($getting, function (&$v) use ($place) { if (is_string($v)) $v = str_replace('$area', $place, $v); });
    $gridItems = [];
    if ($showAttrGrid) {
        // every card shows a photo: the checked photo, else its own; cards without one are left out
        $gridItems = [];
        foreach ($attrItems as $it) {
            // the checked photo, its own photo, then the library photo of that place; the first not shown yet
            $cands = [
                \App\Support\GuideHtml::subjectPhoto('place', (string) $it['name'], $place ?? ''),
                \App\Support\GuideHtml::attractionPhoto($it, $place ?? ''),
                \App\Support\GuideHtml::libraryPhoto((string) $it['name']),
            ];
            foreach ($cands as $img) {
                $img = $img !== '' ? (preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $img) ?: $img) : '';
                if ($img === '' || \App\Support\GuideHtml::isPlaceholder($img) || str_contains($img, '/rg-media/destinations/')) continue;
                if (\App\Support\GuideHtml::claim($img)) { $gridItems[] = $it + ['_img' => $img]; break; }
            }
        }
    }
    // one card per place, dish, festival or story: later repeats (in display order) are dropped
    ['spots' => $spotsHtml, 'adv' => $advHtml, 'stream' => $streamHtml, 'food' => $foodHtml, 'explore' => $exploreHtml]
        = \App\Support\CardDedupe::run(['spots' => $spotsHtml, 'adv' => $advHtml, 'stream' => $streamHtml, 'food' => $foodHtml, 'explore' => $exploreHtml]);
    if ($gridItems && $spotsHtml !== '') {
        preg_match_all('~class="gh-spot__name">(.*?)</~s', $spotsHtml, $snm);
        $spotKeys = array_flip(array_map([\App\Support\CardDedupe::class, 'key'], $snm[1] ?? []));
        $gridItems = array_values(array_filter($gridItems, fn ($it) => !isset($spotKeys[\App\Support\CardDedupe::key((string) $it['name'])])));
    }
    $showAttrGrid = $gridItems !== [];

    // ---------- hero ----------
    $crumbs = [['Home', url('/')]];
    if ($isFood) { $crumbs[] = ['Food Trip', url('/food-trip')]; }
    else { $crumbs[] = ['Destinations', route('destinations.index')]; if ($cluster) $crumbs[] = [$cluster['name'], route('destinations.cluster', $keyword->cluster_tag)]; }
    $crumbs[] = [$properTitle($keyword->phrase), null];
    $kicker = mb_strtolower(trim($eyebrowText)) === mb_strtolower(trim($h1Display))
        ? ($isFood ? 'Food guide · ' . $place : 'Travel guide · ' . ($cluster['name'] ?? $place))
        : $eyebrowText;
    $ownReviews = isset($reviews) ? $reviews->where('keyword_id', $keyword->id)->count() : 0;
    $score = $rating ? max(0, min(5, (float) ($rating['overall'] ?? 0))) : null;
    $verdict = $score === null ? null : ($score >= 4.6 ? 'Outstanding' : ($score >= 4.2 ? 'Excellent' : ($score >= 3.8 ? 'Very good' : ($score >= 3.2 ? 'Good' : 'Fair'))));
    $hasPicks = $isFood ? ($restaurantListings ?? collect())->isNotEmpty() : $listings->count() > 0;

    // ---------- section nav ----------
    // With real picks, the recommendations sit right under the hero; the
    // "be the first to list" box (no picks) stays below the overview.
    $stayFirst = $hasPicks && $stayHtml !== '';
    $stayLabel = $stayHtml !== '' ? ($isFood ? 'Where to eat' : 'Where to stay') : null;
    $todoLabel = ($showAttrGrid || $spotsHtml !== '' || $advHtml !== '') ? ($spotsFirst ? 'Tourist spots' : 'Things to do') : null;
    $nav = array_filter([
        'stay-top' => $stayFirst ? $stayLabel : null,
        'overview' => 'Overview',
    ] + ($spotsFirst ? ['todo' => $todoLabel] : []) + [
        'stay' => $stayFirst ? null : $stayLabel,
        'todo' => $todoLabel,
        'guide' => $streamHtml !== '' ? 'The guide' : null,
        'food' => (!$isFood && $foodHtml !== '') ? 'Food' : ($isFood && $foodHtml !== '' ? 'Dishes' : null),
        'getting-there' => ($getting || $map) ? 'Getting there' : null,
        'faq' => $faqItems ? 'FAQ' : null,
        'reviews' => 'Reviews',
    ]);

    $star = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/></svg>';
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';
    $toneHex = fn ($n) => ['blue' => '#13669c', 'emerald' => '#059669', 'rose' => '#e0462a', 'amber' => '#d97706', 'violet' => '#7c3aed', 'slate' => '#334155', 'cyan' => '#0891b2', 'orange' => '#ea580c'][$n] ?? '#13669c';
    $wwwwRows = array_values(array_filter([
        ['Why go', $whole($fixText($wwww['why'] ?? '')), '#ef5b3a', '<path d="M12 2.8l2.8 5.9 6.4.8-4.7 4.4 1.2 6.4L12 17.2l-5.7 3.1 1.2-6.4L2.8 9.5l6.4-.8z"/>'],
        ['When to go', $whole($fixText($wwww['when'] ?? '')), '#13669c', '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>'],
        ['Where to base', $whole($fixText($wwww['where'] ?? '')), '#059669', '<path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/>'],
        ['Who it suits', $whole($fixText($wwww['whom'] ?? '')), '#7c3aed', '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M21.5 20a6.5 6.5 0 0 0-4-6"/>'],
    ], fn ($r) => $r[1] !== ''));
    $tldrBody = trim((string) ($tldr['body'] ?? ''));
    $tldrBullets = [];
    foreach (preg_split('/\r?\n/', $tldrBody) as $line) if (preg_match('/^\s*[-*\x{2022}]\s+(.+)$/u', $line, $m)) $tldrBullets[] = trim($m[1]);
    $methodIcon = [
        'plane' => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',
        'ferry' => '<path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1 .6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M19.4 17 21 12H3l1.6 5M12 12V4M8 8h8l-1-4H9z"/>',
        'boat' => '<path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1 .6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M19.4 17 21 12H3l1.6 5M12 12V4M8 8h8l-1-4H9z"/>',
        'bus' => '<path d="M8 6v6M16 6v6M2 12h20M5 18h14a2 2 0 0 0 2-2V6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v10a2 2 0 0 0 2 2zM7 18v2M17 18v2"/>',
        'car' => '<path d="M5 17h14l-1.5-6.5A2 2 0 0 0 15.6 9H8.4a2 2 0 0 0-1.9 1.5L5 17zM7 17v2M17 17v2M3 13h2M19 13h2"/>',
        'train' => '<rect x="4" y="3" width="16" height="14" rx="3"/><path d="M4 11h16M8 21l2-4M16 21l-2-4M8 14h.01M16 14h.01"/>',
    ];
@endphp

@section('content')
@include('partials.page-hero', [
    'slides' => $hero['slides'] ?? [],
    'thumbs' => true,
    'size' => 'xl',
    'crumbs' => $crumbs,
    'kicker' => $kicker,
    'title' => $h1Display,
    'lede' => $hero['lede'] ?? '',
    'chips' => [
        ['icon' => 'star', 'sun' => true, 'text' => $score !== null ? number_format($score, 1) . ' · ' . $verdict : ''],
        ['icon' => 'pin', 'text' => $place],
        ['icon' => 'chat', 'text' => $ownReviews ? $ownReviews . ' traveler review' . ($ownReviews === 1 ? '' : 's') : ''],
        ['icon' => 'calendar', 'text' => $page->updated_at ? 'Updated ' . $page->updated_at->format('M Y') : ''],
    ],
    'actions' => [
        $hasPicks ? ['label' => $isFood ? 'See where to eat' : 'See where to stay', 'url' => '#stay-top', 'style' => 'coral']
                  : ['label' => 'Start with the overview', 'url' => '#overview', 'style' => 'coral'],
        ['label' => 'Plan my trip', 'url' => url('/create-your-adventure'), 'style' => 'glass'],
    ],
])

{{-- ============ SECTION NAV ============ --}}
<nav class="pk-jump gx-nav" aria-label="On this page" data-gx-nav>
    <div class="pk-jump__row max-w-7xl mx-auto">
        @foreach($nav as $id => $label)
            <a href="#{{ $id }}" data-gx-link="{{ $id }}">{{ $label }}</a>
        @endforeach
    </div>
</nav>

{{-- ============ WHERE TO STAY / EAT (top, when there are picks) ============ --}}
@if($stayFirst)
<section id="stay-top" class="gx-sec gx-stay gx-stay--top py-10 md:py-14">
    <div class="pk-wrap gx-blocks">{!! $stayHtml !!}</div>
</section>
@endif

{{-- ============ OVERVIEW ============ --}}
<section id="overview" class="gx-sec py-14 md:py-20">
    {{-- Balanced layout: the summary sits next to the score card; the at-a-glance tiles and the
         local tips flow together in a masonry row below (no stretched, half-empty cards). Pages
         without tiles keep their tips under the summary so both columns end level. --}}
    @php $tipsInMasonry = (bool) $wwwwRows; $tipsBeside = !$wwwwRows && $rating; @endphp
    <div class="pk-wrap grid {{ ($rating || !$tipsBeside && $tips) ? 'lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]' : '' }} gap-8 lg:gap-14 items-start">
        <div>
            <p class="pk-eyebrow">{{ $tldr['eyebrow'] ?? 'The short version' }}</p>
            <h2 class="pk-h2">{{ $place }} at a glance</h2>
            @if($tldrBody !== '')
                @if(count($tldrBullets) >= 2)
                    <ul class="gx-short">
                        @foreach($tldrBullets as $b)
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg><span>{{ $b }}</span></li>
                        @endforeach
                    </ul>
                @else
                    <p class="gx-short__text">{{ $tldrBody }}</p>
                @endif
            @endif

            @if($tipsBeside && $tips)
                <div class="mt-8 space-y-4">
                @foreach($tips as $t)
                    <aside class="gx-tip">
                        <span class="gx-tip__ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/></svg></span>
                        <div><p class="gx-tip__eyebrow">{{ $t['eyebrow'] ?? 'Local tip' }}</p><p class="gx-tip__text">{{ $t['body'] }}</p></div>
                    </aside>
                @endforeach
                </div>
            @endif
        </div>

        @if($rating || (!$tipsInMasonry && !$tipsBeside && $tips))
        <div class="space-y-5">
            @if($rating)
                <div class="gx-score">
                    <div class="gx-score__top">
                        <div>
                            <p class="gx-score__label">{{ trim(str_replace(['Resort Guru ', 'Resort Guru'], '', (string) ($rating['title'] ?? ''))) ?: 'Our rating' }}</p>
                            <p class="gx-score__big">{{ number_format($score, 1) }}<span>/5</span></p>
                        </div>
                        <div class="text-right">
                            <span class="gx-score__stars" role="img" aria-label="{{ number_format($score, 1) }} out of 5">
                                @for($i = 1; $i <= 5; $i++)<span class="gx-star"><span style="width:{{ max(0, min(1, $score - ($i - 1))) * 100 }}%">{!! $star !!}</span>{!! $star !!}</span>@endfor
                            </span>
                            <p class="gx-score__verdict">{{ $verdict }}</p>
                        </div>
                    </div>
                    @if(!empty($rating['criteria']))
                        <div class="gx-score__rows">
                            @foreach($rating['criteria'] as $c)
                                @php $cs = (float) ($c['score'] ?? 0); @endphp
                                <div class="gx-score__row"><span>{{ $c['name'] ?? '' }}</span><span class="gx-score__bar"><span style="width:{{ max(0, min(100, $cs / 5 * 100)) }}%"></span></span><strong>{{ number_format($cs, 1) }}</strong></div>
                            @endforeach
                        </div>
                    @endif
                    @if(!empty($rating['summary']))<p class="gx-score__summary">{{ $rating['summary'] }}</p>@endif
                </div>
            @endif
            @if(!$tipsInMasonry && !$tipsBeside)
                @foreach($tips as $t)
                    <aside class="gx-tip">
                        <span class="gx-tip__ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/></svg></span>
                        <div><p class="gx-tip__eyebrow">{{ $t['eyebrow'] ?? 'Local tip' }}</p><p class="gx-tip__text">{{ $t['body'] }}</p></div>
                    </aside>
                @endforeach
            @endif
        </div>
        @endif
    </div>

    @if($wwwwRows)
        <div class="pk-wrap mt-10">
            <div class="gx-glance">
                        @foreach($wwwwRows as [$label, $body, $color, $icon])
                            <div class="rg-glance__tile" style="--c:{{ $color }}">
                                <span class="rg-glance__ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg></span>
                                <div class="min-w-0"><h3 class="rg-glance__label">{{ $label }}</h3><p class="rg-glance__text">{{ $body }}</p></div>
                            </div>
                        @endforeach
                @if($tipsInMasonry)
                    @foreach($tips as $t)
                        <aside class="gx-tip">
                            <span class="gx-tip__ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/></svg></span>
                            <div><p class="gx-tip__eyebrow">{{ $t['eyebrow'] ?? 'Local tip' }}</p><p class="gx-tip__text">{{ $t['body'] }}</p></div>
                        </aside>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    @if($facts)
        <div class="pk-wrap mt-12 md:mt-14">
            <div class="gx-facts">
                <div class="gx-facts__head">
                    <div>
                        <p class="pk-eyebrow !mb-1">Before you go</p>
                        <h3 class="font-display font-extrabold text-ink-900 text-xl sm:text-2xl tracking-tight">Key facts for {{ $place }}</h3>
                    </div>
                    @if($getting || $map)<a href="#getting-there" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-bold text-coral-600 hover:text-coral-700">How to get there <span class="w-4 h-4 inline-flex">{!! $arrow !!}</span></a>@endif
                </div>
                <div class="gx-facts__grid {{ count($facts) % 2 ? 'gx-facts__grid--odd' : '' }} {{ count($facts) === 3 ? 'gx-facts__grid--three' : '' }}">
                    @foreach($facts as $card)
                        <div class="gx-fact" style="--c:{{ $toneHex($card['color'] ?? 'blue') }}">
                            <span class="gx-fact__ico">{!! app(\App\Services\BlockRenderer::class)->factIcon($card['icon'] ?? 'info') !!}</span>
                            <div class="min-w-0">
                                @if(!empty($card['label']))<p class="gx-fact__label">{{ $card['label'] }}</p>@endif
                                @if(!empty($card['big']))<p class="gx-fact__big">{{ $card['big'] }}</p>@endif
                                @if(!empty($card['detail']))<p class="gx-fact__detail">{{ $card['detail'] }}</p>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>

{{-- ============ WHERE TO STAY / EAT (no picks yet: owner invite) ============ --}}
@php ob_start(); @endphp
@if($stayHtml !== '' && !$stayFirst)
<section id="stay" class="gx-sec gx-stay bg-sand-100 py-14 md:py-20">
    <div class="pk-wrap gx-blocks">{!! $stayHtml !!}</div>
</section>
@endif
@php $staySection = ob_get_clean(); ob_start(); @endphp

{{-- ============ THINGS TO DO ============ --}}
@if($showAttrGrid || $spotsHtml !== '' || $advHtml !== '')
<section id="todo" class="gx-sec gx-todo py-14 md:py-20">
    <div class="pk-wrap">
        @if($spotsHtml !== '')
            @php $spotCount = substr_count($spotsHtml, '<article class="gh-spot'); @endphp
            <div class="gx-todo__spots" data-gx-spots>
                {!! $spotsHtml !!}
                @if($spotCount > 5)
                    <button type="button" class="gx-todo__more rg-btn rg-btn--outline" data-gx-more>Show all {{ $spotCount }} spots</button>
                    {{-- phones: the first 5 spots, the rest one tap away (all of them stay in the page) --}}
                    <script>(function(){var w=document.currentScript.parentNode;w.classList.add('is-collapsible');w.querySelector('[data-gx-more]').addEventListener('click',function(){w.classList.add('is-open')});})();</script>
                @endif
            </div>
        @endif
        @if($showAttrGrid)
            <div class="max-w-2xl mb-8 {{ $spotsHtml !== '' ? 'mt-16' : '' }}">
                <p class="pk-eyebrow">{{ $spotsHtml !== '' ? 'More to see' : 'Things to do' }}</p>
                <h2 class="pk-h2">{{ $attr['heading'] ?? 'Tourist spots nearby' }}</h2>
                @if(!empty($attr['intro']))<p class="mt-3 text-slate-600 text-lg">{{ $attr['intro'] }}</p>@endif
            </div>
            <div class="gx-spots">
                @foreach($gridItems as $n => $it)
                    @php
                        $img = $it['_img'];
                        $tag = !empty($it['url']) ? 'a' : 'div';
                    @endphp
                    @php
                        // fill the last row of the 3-column grid (the lead card takes 2x2 from 4 items up)
                        $cnt = count($gridItems);
                        $rem = $cnt >= 4 ? ($cnt - 3) % 3 : $cnt % 3;
                        $fill = $n === $cnt - 1 ? ($rem === 1 ? 'gx-spot--row' : ($rem === 2 ? 'gx-spot--wide' : '')) : '';
                    @endphp
                    <{{ $tag }} @if($tag === 'a') href="{{ $it['url'] }}" @endif class="gx-spot group {{ $n === 0 && $cnt >= 4 ? 'gx-spot--big' : '' }} {{ $fill }}">
                        <span class="gx-spot__img"><img src="{{ $img }}" alt="{{ $it['name'] }}" loading="lazy"><span class="gx-spot__n">{{ $n + 1 }}</span></span>
                        <span class="gx-spot__body">
                            <h3 class="gx-spot__name">{{ $it['name'] }}</h3>
                            @if(!empty($it['short']))<span class="block text-xs font-semibold text-coral-600 mt-1">{{ $it['short'] }}</span>@endif
                            @if(!empty($it['blurb']))<p class="gx-spot__blurb">{{ ucfirst($it['blurb']) }}</p>@endif
                        </span>
                    </{{ $tag }}>
                @endforeach
            </div>
        @endif
        @if($advHtml !== '')<div class="gx-blocks {{ ($showAttrGrid || $spotsHtml !== '') ? 'mt-14' : '' }}">{!! $advHtml !!}</div>@endif
    </div>
</section>
@endif
@php $todoSection = ob_get_clean(); @endphp
{!! $spotsFirst ? $todoSection . $staySection : $staySection . $todoSection !!}

{{-- ============ THE FULL GUIDE ============ --}}
@if($streamHtml !== '')
<section id="guide" class="gx-sec gx-guide py-14 md:py-20 border-t border-slate-100">
    <div class="pk-wrap">
        <div class="max-w-2xl mb-10">
            <p class="pk-eyebrow">The full guide</p>
            <h2 class="pk-h2">Everything to know before you go</h2>
        </div>
        <div class="pk-grid">
            <article class="pk-main pk-guide page-{{ $page->slug }}" data-pk-main>
                {!! $streamHtml !!}
            </article>
            @include('partials.page-aside', [
                'shareTitle' => $h1Display,
                'shareUrl' => url($page->slug),
                'business' => $isFood
                    ? ['title' => 'Run a restaurant in ' . $place . '?', 'text' => 'Get featured at the top of this guide, in front of people deciding where to eat.', 'url' => route('register'), 'label' => 'List your restaurant']
                    : ['title' => 'Own a place in ' . $place . '?', 'text' => 'Get your resort or hotel featured at the top of this guide, in front of travelers planning a trip here.', 'url' => route('register'), 'label' => 'List your property'],
            ])
        </div>
    </div>
</section>
@endif

{{-- ============ FOOD ============ --}}
@if($foodHtml !== '')
<section id="food" class="gx-sec bg-sand-100 py-14 md:py-20">
    <div class="pk-wrap gx-blocks">{!! $foodHtml !!}</div>
</section>
@endif

{{-- ============ GETTING THERE ============ --}}
@if($getting || $map)
<section id="getting-there" class="gx-sec py-14 md:py-20">
    <div class="pk-wrap grid {{ $getting ? 'lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)]' : '' }} gap-10 lg:gap-14 items-start lg:items-stretch">
        @if($getting)
        <div>
            <p class="pk-eyebrow">Getting there</p>
            <h2 class="pk-h2">{{ $getting['heading'] ?? ('How to get to ' . $place) }}</h2>
            @if(!empty($getting['intro']))<p class="mt-4 text-slate-600 text-lg leading-relaxed">{{ $getting['intro'] }}</p>@endif
            @if(!empty($getting['methods']))
                <div class="mt-8 space-y-3">
                    @foreach($getting['methods'] as $i => $m)
                        <details class="gx-way" style="--c:{{ $toneHex($m['color'] ?? 'blue') }}" @if($i === 0) open @endif>
                            <summary>
                                <span class="gx-way__ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $methodIcon[$m['icon'] ?? ''] ?? $methodIcon['car'] !!}</svg></span>
                                <span class="min-w-0 flex-1">
                                    <h3 class="gx-way__title">{{ $m['title'] ?? '' }}</h3>
                                    @if(!empty($m['subtitle']))<span class="block text-sm text-slate-500">{{ $m['subtitle'] }}</span>@endif
                                </span>
                                <span class="gx-way__chev" aria-hidden="true"></span>
                            </summary>
                            @if(!empty($m['detail']))<p class="gx-way__detail">{{ $m['detail'] }}</p>@endif
                        </details>
                    @endforeach
                </div>
            @endif
            @if(!empty($getting['footer']))<p class="mt-5 text-sm text-slate-500">{{ $getting['footer'] }}</p>@endif
        </div>
        @endif
        @if(!empty($map['embed_url']))
            @php
                $mapQuery = '';
                if (preg_match('~[?&]q=([^&]+)~', $map['embed_url'], $mq)) $mapQuery = urldecode($mq[1]);
            @endphp
            <div class="gx-map">
                <div class="gx-map__head">
                    <h2 class="font-display font-extrabold text-ink-900 text-lg">{{ $map['heading'] ?? 'On the map' }}</h2>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($mapQuery ?: $place . ', Philippines') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-700 hover:text-brand-800">Open in Google Maps <span class="w-4 h-4 inline-flex">{!! $arrow !!}</span></a>
                </div>
                <iframe src="{{ $map['embed_url'] }}" title="Map of {{ $place }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        @endif
    </div>
</section>
@endif

{{-- ============ FAQ ============ --}}
@if($faqItems)
<section id="faq" class="gx-sec relative overflow-hidden bg-ink-900 text-white py-16 md:py-24 isolate">
    <div class="absolute -left-40 -top-40 w-[34rem] h-[34rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(239,91,58,.28), transparent 65%)"></div>
    <div class="pk-wrap grid lg:grid-cols-[minmax(0,.8fr)_minmax(0,1.2fr)] gap-10 lg:gap-16 items-start">
        <div class="lg:sticky lg:top-[calc(var(--rg-header-h,76px)+4.5rem)]">
            <p class="pk-eyebrow !text-sun-300">Good to know</p>
            <h2 class="pk-h2 !text-white">{{ $faqBlock['heading'] ?? 'Frequently asked questions' }}</h2>
            <p class="mt-4 text-white/70">Still deciding? Ask us anything about {{ $place }} and we will point you in the right direction.</p>
            <a href="{{ route('contact') }}" class="rg-btn rg-btn--glass mt-6">Ask a question</a>
        </div>
        <div class="gx-faq">
            @foreach($faqItems as $i => $q)
                <details class="gx-faq__item" @if($i === 0) open @endif>
                    <summary><h3>{{ $q['question'] }}</h3><span class="gx-faq__icon" aria-hidden="true"></span></summary>
                    <div class="gx-faq__answer">{!! nl2br(e($q['answer'] ?? '')) !!}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ REVIEWS ============ --}}
<section id="reviews" class="gx-sec py-14 md:py-20">
    <div class="pk-wrap">
        @if($reviewsHtml !== '')<div class="gx-blocks">{!! $reviewsHtml !!}</div>@endif
        @isset($reviews)
            <div class="mt-8">
                @if(session('review_status'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">{{ session('review_status') }}</div>
                @endif
                <div class="pk-card pk-card--sand sm:p-7 flex flex-col sm:flex-row gap-5 sm:items-center">
                    <div class="w-12 h-12 rounded-2xl bg-white text-sun-500 flex items-center justify-center shrink-0 shadow-sm"><span class="w-6 h-6 inline-flex [&_svg]:fill-current">{!! $star !!}</span></div>
                    <div class="flex-1 min-w-0">
                        @auth('owner')
                            <h3 class="pk-card__title text-ink-900">Leave your review of {{ $place }}</h3>
                            <p class="text-sm text-slate-600 mt-1 mb-4">Signed in as <strong>{{ Auth::guard('owner')->user()->name }}</strong>. Reviews are moderated before they go live.</p>
                            <form method="POST" action="{{ route('keyword.review.store') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="keyword_id" value="{{ $keyword->id }}">
                                <input type="hidden" name="redirect_to" value="{{ url($page->slug) }}">
                                <div class="flex items-center gap-2">
                                    <label class="pk-label mb-0" for="rg-review-rating">Rating</label>
                                    <select id="rg-review-rating" name="rating" class="rounded-xl border-slate-300 text-sm">
                                        @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }})</option>@endfor
                                    </select>
                                </div>
                                <textarea name="comment_text" disabled hidden></textarea>
                                <textarea name="review_text" rows="4" required minlength="20" maxlength="2000" class="pk-field" placeholder="Share what you actually experienced. Real specifics help future travelers more than generic praise."></textarea>
                                @error('review_text')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror
                                <button type="submit" class="rg-btn rg-btn--coral">Submit review</button>
                            </form>
                        @else
                            <h3 class="pk-card__title text-ink-900">Been to {{ $place }}?</h3>
                            <p class="text-sm text-slate-600 mt-1">Tell other travelers what it was really like. Members can leave a review, and every review is checked before it goes live.</p>
                        @endauth
                    </div>
                    @guest('owner')
                        <div class="flex flex-wrap gap-2 shrink-0">
                            <a href="{{ route('login') }}" class="rg-btn rg-btn--ocean">Sign in to review</a>
                            <a href="{{ route('register') }}" class="rg-btn rg-btn--outline">Join free</a>
                        </div>
                    @endguest
                </div>
            </div>
        @endisset
    </div>
</section>

{{-- ============ KEEP EXPLORING ============ --}}
@if($exploreHtml !== '' || $extHtml !== '' || $author)
<section id="explore" class="gx-sec bg-sand-100 py-14 md:py-20">
    <div class="pk-wrap">
        @if($exploreHtml !== '')<div class="gx-blocks">{!! $exploreHtml !!}</div>@endif
        {{-- tags, then the cross-check cards across the full width, then the byline as a wide band:
             no column sits beside a much taller one --}}
        @if($tagHtml !== '')<div class="gx-blocks gx-explore-tags">{!! $tagHtml !!}</div>@endif
        @if($guidesHtml !== '')<div class="gx-blocks gx-xg-wide mt-12">{!! $guidesHtml !!}</div>@endif
        @if($author && !empty($author['name']))
            <div class="gx-author mt-10">
                <div class="gx-author__who">
                    <p class="gx-author__eyebrow">{{ $author['eyebrow'] ?? 'Written by' }}</p>
                    <div class="flex items-center gap-4 mt-3">
                        @if(!empty($author['avatar']))<img src="{{ $author['avatar'] }}" alt="{{ $author['name'] }}" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white/10" loading="lazy">@endif
                        <div>
                            <p class="font-display font-extrabold text-lg text-white">{{ $author['name'] }}</p>
                            @if(!empty($author['role']))<p class="text-sm text-white/65">{{ $author['role'] }}</p>@endif
                        </div>
                    </div>
                </div>
                <div class="gx-author__bio">
                    @if(!empty($author['bio']))<p class="text-white/80 leading-relaxed">{{ $author['bio'] }}</p>@endif
                    @if(!empty($author['more_url']))<a href="{{ $author['more_url'] }}" class="mt-3 inline-flex text-sm font-bold text-sun-300 hover:text-sun-200">{{ $author['more_label'] ?: 'More from this writer' }} &rarr;</a>@endif
                </div>
            </div>
        @endif
    </div>
</section>
@endif

@if($related->isNotEmpty() && $cluster)
    <section class="py-14 md:py-20">
        <div class="pk-wrap">
            <div class="flex items-end justify-between gap-4 mb-8 flex-wrap">
                <div>
                    <p class="pk-eyebrow">Keep exploring</p>
                    <h2 class="pk-h2">More of {{ $cluster['name'] }}</h2>
                </div>
                <a href="{{ route('destinations.cluster', $keyword->cluster_tag) }}" class="rg-btn rg-btn--outline">All {{ $cluster['name'] }} guides</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($related as $r)
                    <a href="{{ url($r->slug) }}" class="group flex items-center gap-3 rounded-2xl bg-white border border-slate-200 p-4 hover:border-brand-300 hover:shadow-lg hover:-translate-y-0.5 transition">
                        <span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0"><span class="w-5 h-5 inline-flex">{!! '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/></svg>' !!}</span></span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-ink-900 text-sm leading-snug group-hover:text-brand-700">{{ $properTitle($r->phrase) }}</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Travel guide</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="pb-16 md:pb-20 {{ ($related->isNotEmpty() && $cluster) ? '' : 'pt-14 md:pt-20' }}">
    <div class="pk-wrap">
        <div class="relative overflow-hidden rounded-[1.75rem] bg-ink-900 text-white p-7 sm:p-10 lg:p-12 isolate">
            <div class="absolute -right-24 -top-24 w-[26rem] h-[26rem] rounded-full -z-10" style="background: radial-gradient(circle, rgba(239,91,58,.4), transparent 65%)"></div>
            <div class="grid lg:grid-cols-[1fr_auto] gap-6 items-center">
                <div>
                    <p class="pk-eyebrow !text-sun-300">For business owners</p>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $isFood ? 'Run a restaurant in ' . $place . '?' : 'Run a resort or hotel in ' . $place . '?' }}</h2>
                    <p class="mt-2 text-white/75 max-w-2xl">Travelers read this guide while they decide. Put your place at the top with photos, reviews and direct contact links.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('register') }}" class="rg-btn rg-btn--coral rg-btn--lg">List your {{ $isFood ? 'restaurant' : 'property' }}</a>
                    <a href="{{ route('become-a-partner') }}" class="rg-btn rg-btn--glass rg-btn--lg">How it works</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .gx-sec { scroll-margin-top: calc(var(--rg-header-h, 76px) + 3.25rem); }
    .gx-nav .pk-jump__row { justify-content: flex-start; }
    @media (min-width: 1024px) { .gx-nav .pk-jump__row { justify-content: center; } }

    .gx-short { margin-top: 1.5rem; display: grid; gap: .8rem; }
    .gx-short li { display: flex; gap: .75rem; align-items: flex-start; font-size: 1.12rem; line-height: 1.55; color: #0b1b2b; font-weight: 500; }
    .gx-short svg { width: 1.35rem; height: 1.35rem; padding: .2rem; margin-top: .1rem; border-radius: 999px; background: #fff3ef; color: #ef5b3a; flex-shrink: 0; }
    .gx-short__text { margin-top: 1.25rem; font-size: 1.15rem; line-height: 1.7; color: #1e293b; }
    .gx-tip { margin-top: 0; display: flex; gap: .9rem; border-radius: 1.25rem; background: #fbf6ee; border: 1px solid #f0e4cf; padding: 1.1rem 1.25rem; }
    .gx-tip__ico { width: 2.5rem; height: 2.5rem; border-radius: .85rem; background: #fff; color: #d97706; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .gx-tip__ico svg { width: 1.2rem; height: 1.2rem; }
    .gx-tip__eyebrow { font-size: .7rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #b45309; }
    .gx-tip__text { margin-top: .25rem; color: #1e293b; line-height: 1.65; }

    .gx-score { position: relative; isolation: isolate; overflow: hidden; border-radius: 1.5rem; background: #0b1b2b; color: #fff; padding: 1.5rem; }
    .gx-score::before { content: ""; position: absolute; right: -5rem; top: -6rem; width: 15rem; height: 15rem; border-radius: 999px; z-index: -1; background: radial-gradient(circle, rgba(255, 199, 74, .35), transparent 65%); }
    .gx-score__top { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; }
    .gx-score__label { font-size: .7rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: rgba(255, 255, 255, .75); }
    .gx-score__big { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 3.6rem; line-height: 1; letter-spacing: -0.04em; margin-top: .3rem; }
    .gx-score__big span { font-size: 1.1rem; color: rgba(255, 255, 255, .55); margin-left: .2rem; letter-spacing: 0; }
    .gx-score__stars { display: inline-flex; gap: .15rem; }
    .gx-star { position: relative; width: 1.15rem; height: 1.15rem; display: inline-block; }
    .gx-star > svg { width: 100%; height: 100%; fill: rgba(255, 255, 255, .22); }
    .gx-star > span { position: absolute; inset: 0; overflow: hidden; }
    .gx-star > span svg { width: 1.15rem; height: 1.15rem; fill: #ffc74a; }
    .gx-score__verdict { margin-top: .35rem; font-weight: 800; color: #ffc74a; }
    .gx-score__rows { margin-top: 1.25rem; display: grid; gap: .65rem; }
    .gx-score__row { display: grid; grid-template-columns: 6.5rem minmax(0, 1fr) 2rem; gap: .75rem; align-items: center; font-size: .88rem; color: rgba(255, 255, 255, .82); }
    .gx-score__row strong { text-align: right; color: #fff; font-variant-numeric: tabular-nums; }
    .gx-score__bar { height: .45rem; border-radius: 999px; background: rgba(255, 255, 255, .12); overflow: hidden; }
    .gx-score__bar > span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #ffc74a, #ef5b3a); }
    .gx-score__summary { margin-top: 1.1rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, .12); font-size: .92rem; line-height: 1.6; color: rgba(255, 255, 255, .78); }

    .gx-stay .rg-listings-band { background: #fff; border-color: #efe3cc; }
    .gx-stay--top { background: linear-gradient(180deg, #fbf6ee 0%, #fdfbf7 70%, #fff 100%); }
    .gx-stay--top .rg-listings-band { margin-bottom: 0; box-shadow: 0 40px 80px -50px rgba(11, 27, 43, .45); }

    .gx-facts { border-radius: 1.5rem; border: 1px solid #e3e9f0; background: #fff; overflow: hidden; box-shadow: 0 30px 60px -48px rgba(11, 27, 43, .5); }
    .gx-facts__head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; padding: 1.3rem 1.5rem 1.15rem; border-bottom: 1px solid #eef2f6; background: linear-gradient(180deg, #fbfcfe, #fff); }
    .gx-facts__grid { display: grid; gap: 1px; background: #eef2f6; }
    @media (min-width: 768px) { .gx-facts__grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .gx-facts__grid--odd > :last-child { grid-column: 1 / -1; } }
    @media (min-width: 1024px) { .gx-facts__grid--three { grid-template-columns: repeat(3, minmax(0, 1fr)); } .gx-facts__grid--three > :last-child { grid-column: auto; } }
    .gx-fact { display: flex; gap: 1rem; align-items: flex-start; background: #fff; padding: 1.35rem 1.5rem 1.45rem; }
    .gx-fact__ico { flex-shrink: 0; width: 2.9rem; height: 2.9rem; border-radius: 1rem; display: inline-flex; align-items: center; justify-content: center; color: var(--c); background: color-mix(in srgb, var(--c) 11%, #fff); }
    .gx-fact__ico svg { width: 1.4rem !important; height: 1.4rem !important; }
    .gx-fact__label { font-size: .7rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--c); }
    .gx-fact__big { margin-top: .3rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.12rem; line-height: 1.35; color: #0b1b2b; letter-spacing: -0.01em; }
    .gx-fact__detail { margin-top: .4rem; font-size: .93rem; line-height: 1.6; color: #5b6b80; }
    .gx-blocks h2 { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; letter-spacing: -0.025em; color: #0b1b2b; font-size: clamp(1.55rem, 2.7vw, 2.15rem); line-height: 1.15; }
    .gx-blocks > section:first-child, .gx-blocks > div:first-child > section:first-child { margin-top: 0; }
    .gx-blocks section.border-t { border-top: 0; padding-top: 0; }

    /* the full tourist-spot list, moved up into Things to do (full width: 3 columns) */
    .gx-todo__spots .gh-spots { margin: 0; }
    .gx-todo__spots .gh-head { max-width: 46rem; margin-bottom: 2rem; }
    .gx-todo__spots .gh-eyebrow { font-size: .72rem !important; letter-spacing: .2em; }
    .gx-todo__spots .gh-title { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: clamp(1.7rem, 3.2vw, 2.5rem); line-height: 1.12; font-weight: 800; color: #0b1b2b; letter-spacing: -0.025em; }
    .gx-todo__spots .gh-intro { font-size: 1.05rem; max-width: 44rem; }
    .gx-todo__more { display: none; }
    @media (max-width: 767px) {
        .gx-todo__spots.is-collapsible:not(.is-open) .gh-spot:nth-child(n+6) { display: none; }
        .gx-todo__spots.is-collapsible:not(.is-open) .gx-todo__more { display: flex; width: 100%; justify-content: center; margin-top: 1.25rem; }
    }
    @media (min-width: 1100px) {
        .gx-todo__spots .gh-spots__grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.4rem; }
        .gx-todo__spots .gh-spot--lead { grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); }
        .gx-todo__spots .gh-spot--lead .gh-spot__img { min-height: 26rem; }
        .gx-todo__spots .gh-spot--lead .gh-spot__name { font-size: 2rem; }
        .gx-todo__spots .gh-spot--lead .gh-spot__body { padding: 2.25rem 2.5rem; }
        /* 3 columns after the lead: fill the last row (one card left: full row, two cards: the last spans two) */
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:nth-child(even):not(.gh-spot--lead) { grid-column: auto; display: flex; }
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:nth-child(even):not(.gh-spot--lead) .gh-spot__img { aspect-ratio: 16 / 10; min-height: 0; }
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:nth-child(even):not(.gh-spot--lead) .gh-spot__body { padding: 1.25rem 1.35rem 1.35rem; justify-content: flex-start; }
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:nth-child(3n+2):not(.gh-spot--lead) { grid-column: 1 / -1; display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr); }
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:nth-child(3n):not(.gh-spot--lead) { grid-column: span 2; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:is(:nth-child(3n+2), :nth-child(3n)):not(.gh-spot--lead) .gh-spot__img { aspect-ratio: auto; min-height: 16rem; }
        .gx-todo__spots .gh-spots__grid > .gh-spot:last-child:is(:nth-child(3n+2), :nth-child(3n)):not(.gh-spot--lead) .gh-spot__body { justify-content: center; padding: 1.75rem 2rem; }
    }

    .gx-glance { columns: 1; column-gap: 1rem; }
    @media (min-width: 640px) { .gx-glance { columns: 2; } }
    @media (min-width: 1100px) { .gx-glance { columns: 3; } }
    .gx-glance > * { break-inside: avoid; margin-bottom: 1rem; }
    .gx-glance > .gx-tip { display: flex; }
    .gx-spots { display: grid; gap: 1rem; grid-template-columns: minmax(0, 1fr); }
    @media (min-width: 640px) { .gx-spots { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .gx-spots { grid-template-columns: repeat(3, minmax(0, 1fr)); } .gx-spot--big { grid-column: span 2; grid-row: span 2; } }
    .gx-spot { display: flex; flex-direction: column; border-radius: 1.4rem; overflow: hidden; background: #fff; border: 1px solid #e3e9f0; transition: box-shadow .3s ease, transform .3s ease; }
    a.gx-spot:hover { box-shadow: 0 24px 48px -28px rgba(11, 27, 43, .45); transform: translateY(-3px); }
    .gx-spot__img { position: relative; display: block; aspect-ratio: 16 / 10; overflow: hidden; background: linear-gradient(135deg, #e0f0fa, #fdf3e3); }
    .gx-spot--big .gx-spot__img { flex: 1; aspect-ratio: auto; min-height: 16rem; }
    .gx-spot__img img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .9s cubic-bezier(.22, 1, .36, 1); }
    .gx-spot:hover .gx-spot__img img { transform: scale(1.05); }
    .gx-spot__n { position: absolute; top: .8rem; left: .8rem; width: 2.1rem; height: 2.1rem; border-radius: 999px; background: #fff; color: #0b1b2b; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: .9rem; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 6px 16px -6px rgba(0, 0, 0, .35); }
    .gx-spot__body { display: block; padding: 1rem 1.15rem 1.15rem; }
    .gx-spot__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.08rem; line-height: 1.3; color: #0b1b2b; }
    .gx-spot--big .gx-spot__name { font-size: 1.45rem; }
    @media (min-width: 1024px) {
        .gx-spot--wide { grid-column: span 2; }
        .gx-spot--wide .gx-spot__img { aspect-ratio: auto; flex: 1; min-height: 12rem; }
        .gx-spot--row { grid-column: 1 / -1; flex-direction: row; }
        .gx-spot--row .gx-spot__img { aspect-ratio: auto; width: 40%; min-height: 15rem; flex: none; }
        .gx-spot--row .gx-spot__body { flex: 1; align-self: center; padding: 1.5rem 2rem; }
        .gx-spot--row .gx-spot__name { font-size: 1.3rem; }
    }
    .gx-spot__blurb { margin-top: .4rem; font-size: .92rem; line-height: 1.6; color: #5b6b80; }

    .gx-way { border-radius: 1.2rem; border: 1px solid #e3e9f0; background: #fff; transition: border-color .2s ease, box-shadow .2s ease; }
    .gx-way[open] { border-color: color-mix(in srgb, var(--c) 35%, #fff); box-shadow: 0 16px 32px -24px rgba(11, 27, 43, .35); }
    .gx-way > summary { list-style: none; cursor: pointer; display: flex; align-items: center; gap: .9rem; padding: 1rem 1.15rem; }
    .gx-way > summary::-webkit-details-marker { display: none; }
    .gx-way__ico { width: 2.6rem; height: 2.6rem; border-radius: .9rem; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; color: var(--c); background: color-mix(in srgb, var(--c) 11%, #fff); }
    .gx-way__ico svg { width: 1.25rem; height: 1.25rem; }
    .gx-way__title { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; color: #0b1b2b; font-size: 1.02rem; line-height: 1.3; }
    .gx-way__chev { width: 1.75rem; height: 1.75rem; border-radius: 999px; border: 1.5px solid #cbd5e1; flex-shrink: 0; position: relative; transition: transform .25s ease; }
    .gx-way__chev::before { content: ""; position: absolute; left: 50%; top: 45%; width: .45rem; height: .45rem; border-right: 2px solid #0f172a; border-bottom: 2px solid #0f172a; transform: translate(-50%, -50%) rotate(45deg); }
    .gx-way[open] .gx-way__chev { transform: rotate(180deg); }
    .gx-way__detail { padding: 0 1.15rem 1.15rem 4.65rem; color: #475569; line-height: 1.7; }
    @media (max-width: 639px) { .gx-way__detail { padding-left: 1.15rem; } }
    .gx-map { border-radius: 1.5rem; overflow: hidden; border: 1px solid #e3e9f0; background: #fff; box-shadow: 0 30px 60px -36px rgba(11, 27, 43, .45); }
    .gx-map__head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.2rem; flex-wrap: wrap; }
    .gx-map iframe { display: block; width: 100%; height: 22rem; border: 0; }
    /* the map fills the height of the directions column beside it, so neither side leaves a hole */
    @media (min-width: 1024px) { .gx-map { display: flex; flex-direction: column; min-height: 30rem; } .gx-map iframe { flex: 1 1 auto; height: auto; min-height: 26rem; } }

    .gx-faq { display: grid; gap: .6rem; }
    .gx-faq__item { border-radius: 1.1rem; background: rgba(255, 255, 255, .05); border: 1px solid rgba(255, 255, 255, .1); }
    .gx-faq__item[open] { background: rgba(255, 255, 255, .08); }
    .gx-faq__item > summary { list-style: none; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.1rem 1.25rem; }
    .gx-faq__item > summary::-webkit-details-marker { display: none; }
    .gx-faq__item h3 { font-weight: 700; font-size: 1.02rem; line-height: 1.4; color: #fff; }
    .gx-faq__icon { position: relative; width: 1.75rem; height: 1.75rem; border-radius: 999px; border: 1.5px solid rgba(255, 255, 255, .35); flex-shrink: 0; transition: transform .25s ease, background-color .2s ease; }
    .gx-faq__icon::before, .gx-faq__icon::after { content: ""; position: absolute; left: 50%; top: 50%; width: .7rem; height: 2px; background: #fff; transform: translate(-50%, -50%); }
    .gx-faq__icon::after { transform: translate(-50%, -50%) rotate(90deg); transition: opacity .2s ease; }
    .gx-faq__item[open] .gx-faq__icon { transform: rotate(180deg); background: #ffc74a; border-color: #ffc74a; }
    .gx-faq__item[open] .gx-faq__icon::before { background: #0b1b2b; }
    .gx-faq__item[open] .gx-faq__icon::after { opacity: 0; }
    .gx-faq__answer { padding: 0 1.25rem 1.2rem; color: rgba(255, 255, 255, .78); line-height: 1.7; }

    .gx-author { border-radius: 1.5rem; background: #0b1b2b; padding: 1.5rem; display: grid; gap: 1.25rem; }
    @media (min-width: 768px) { .gx-author { grid-template-columns: minmax(15rem, 20rem) minmax(0, 1fr); align-items: center; gap: 2.5rem; padding: 1.75rem 2rem; } .gx-author__bio { border-left: 1px solid rgba(255, 255, 255, .12); padding-left: 2.5rem; } }
    .gx-explore-tags .rg-tags { margin: 2.5rem 0 1rem; }
    @media (min-width: 1280px) { .gx-xg-wide .xg__grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } } .gx-xg-wide .xg .gh-intro { max-width: 46rem; }
    .gx-author__eyebrow { font-size: .7rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #ffc74a; }
</style>
<script>
(function () {
    // section nav: highlight the section in view and keep its chip visible
    var nav = document.querySelector('[data-gx-nav]');
    if (!nav) return;
    var links = nav.querySelectorAll('[data-gx-link]');
    var secs = [].map.call(links, function (a) { return document.getElementById(a.getAttribute('data-gx-link')); });
    var current = null, ticking = false;
    function mark() {
        var off = (parseInt(getComputedStyle(document.documentElement).getPropertyValue('--rg-header-h')) || 70) + 90, idx = 0;
        secs.forEach(function (s, i) { if (s && s.getBoundingClientRect().top - off <= 0) idx = i; });
        if (idx === current) return; current = idx;
        links.forEach(function (a, i) { a.classList.toggle('is-on', i === idx); });
        var row = nav.querySelector('.pk-jump__row'), a = links[idx];
        if (row && a && row.scrollWidth > row.clientWidth) row.scrollTo({ left: a.offsetLeft - 16, behavior: 'smooth' });
    }
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(function () { ticking = false; mark(); }); } }, { passive: true });
    mark();
})();
</script>
@endsection
