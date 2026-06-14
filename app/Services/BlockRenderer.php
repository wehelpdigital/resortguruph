<?php

namespace App\Services;

use App\Models\RgContentBlock;
use App\Models\RgKeyword;
use App\Models\RgListing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BlockRenderer
{
    public function renderFor(string $ownerType, int $ownerId, array $context = []): string
    {
        $blocks = RgContentBlock::forOwner($ownerType, $ownerId);
        return $this->renderBlocks($blocks, $context);
    }

    public function renderBlocks(Collection $blocks, array $context = []): string
    {
        $out = '';
        foreach ($blocks as $block) {
            $out .= $this->renderBlock($block, $context);
        }
        return $out;
    }

    public function renderBlock(RgContentBlock $block, array $context = []): string
    {
        $p = $block->payload ?? [];
        $html = $this->renderBlockHtml($block, $p, $context);
        // Live-edit mode (driven by the mother super-admin's Live
        // Editor): wrap each block's rendered HTML in a container
        // that carries the block id, type, and sort order. The
        // iframe-side rg-live-edit.js uses these attributes to
        // attach hover toolbars and SortableJS drag-drop handles.
        if (!empty($context['live_edit'])) {
            $html = sprintf(
                '<div class="rg-live-block" data-rg-block-id="%d" data-rg-block-type="%s" data-rg-block-sort="%d">%s</div>',
                (int) $block->id,
                htmlspecialchars((string) $block->block_type, ENT_QUOTES),
                (int) ($block->sort_order ?? 0),
                $html
            );
        }
        return $html;
    }

    private function renderBlockHtml(RgContentBlock $block, array $p, array $context): string
    {
        return match ($block->block_type) {
            'heading' => $this->heading($p),
            'rich_text' => $this->richText($p),
            'image' => $this->image($p),
            'gallery' => $this->gallery($p),
            'video' => $this->video($p),
            'faq' => $this->faq($p),
            'cta' => $this->cta($p),
            'two_column' => $this->twoColumn($p),
            'listing_slot' => $this->listingSlot($p, $context),
            'quote' => $this->quote($p),
            'divider' => $this->divider($p),
            'custom_html' => $this->customHtml($p),
            // Custom Resort Guru elements.
            'hero_slider' => $this->heroSlider($p),
            'quick_facts' => $this->quickFacts($p),
            'editor_rating' => $this->editorRating($p),
            'listing_block' => $this->listingBlock($p, $context),
            'attractions' => $this->attractions($p),
            'how_to_get_to' => $this->howToGetTo($p),
            'text_section' => $this->textSection($p),
            // New standardized templates covering the section patterns
            // that previously lived as raw custom_html on seeded pages.
            'short_version' => $this->shortVersion($p),
            'pros_cons' => $this->prosCons($p),
            'summary_accordion' => $this->summaryAccordion($p),
            'image_text_pair' => $this->imageTextPair($p),
            'traveler_reviews' => $this->travelerReviews($p),
            'map_embed' => $this->mapEmbed($p),
            'local_tip' => $this->localTip($p),
            'related_guides' => $this->relatedGuides($p),
            'data_table' => $this->dataTable($p),
            'section_header' => $this->sectionHeader($p),
            'subtitle_intro' => $this->subtitleIntro($p),
            'tldr_card' => $this->tldrCard($p),
            'wwww_card' => $this->wwwwCard($p),
            'social_share' => $this->socialShare($p, $context),
            'we_recommend_band' => $this->weRecommendBand($p, $context),
            'restaurant_recs_band' => $this->restaurantRecsBand($p, $context),
            'adventures_band' => $this->adventuresBand($p, $context),
            'reviews_band' => $this->reviewsBand($p, $context),
            'tag_pills' => $this->tagPills($p),
            'external_guides' => $this->externalGuides($p),
            'author' => $this->author($p),
            'nearby_destinations' => $this->nearbyDestinations($p),
            'related_blogs' => $this->relatedBlogs($p),
            'facts_list' => $this->factsList($p),
            'place_history' => $this->placeHistory($p),
            'foods_to_try' => $this->foodsToTry($p),
            // /destinations page custom block types — combo hero+search,
            // featured-spots slider, jump-to-region clusters. Each is
            // self-contained (CSS+JS inline) and reads its live data
            // from $context (orderedClusters / stats / featuredSpots /
            // searchIndex passed by DestinationsController).
            'dest_hero_search' => $this->destHeroSearch($p, $context),
            'dest_featured_slider' => $this->destFeaturedSlider($p, $context),
            'dest_region_clusters' => $this->destRegionClusters($p, $context),
            // Homepage custom block types — each reads its live data
            // (featuredKeywords / regions / featuredResorts / latestPosts
            // / stats) from the HomeController context.
            'home_hero_centered' => $this->homeHeroCentered($p, $context),
            'home_keyword_grid' => $this->homeKeywordGrid($p, $context),
            'home_region_grid' => $this->homeRegionGrid($p, $context),
            'home_resort_grid' => $this->homeResortGrid($p, $context),
            'home_blog_strip' => $this->homeBlogStrip($p, $context),
            'home_cta_band' => $this->homeCtaBand($p, $context),
            // Phase-2 homepage blocks — editorial intro, experience
            // tiles, hub-link cards, seasonal guide, testimonials,
            // FAQ. Content seeded from Fable 5 content gen.
            'home_unified_search' => $this->homeUnifiedSearch($p, $context),
            'home_values_grid' => $this->homeValuesGrid($p, $context),
            // Hub-page custom block types (foods/activities/buys/
            // cultures). Each reads category data from $context.
            'hub_hero' => $this->hubHero($p, $context),
            'hub_category_nav' => $this->hubCategoryNav($p, $context),
            'hub_category_grid' => $this->hubCategoryGrid($p, $context),
            'hub_footer_rail' => $this->hubFooterRail($p, $context),
            'home_editorial_intro' => $this->homeEditorialIntro($p, $context),
            'home_experience_grid' => $this->homeExperienceGrid($p, $context),
            'home_hub_links' => $this->homeHubLinks($p, $context),
            'home_season_guide' => $this->homeSeasonGuide($p, $context),
            'home_testimonials' => $this->homeTestimonials($p, $context),
            'home_faq' => $this->homeFaq($p, $context),
            'home_owner_inline_band' => $this->homeOwnerInlineBand($p, $context),
            'home_how_it_works' => $this->homeHowItWorks($p, $context),
            'home_category_accordion' => $this->homeCategoryAccordion($p, $context),
            default => '',
        };
    }

    public function extractFaqs(string $ownerType, int $ownerId): array
    {
        $out = [];
        foreach (RgContentBlock::forOwner($ownerType, $ownerId) as $block) {
            if ($block->block_type !== 'faq') continue;
            foreach (($block->payload['items'] ?? []) as $item) {
                if (!empty($item['question'])) $out[] = $item;
            }
        }
        return $out;
    }

    public function plainText(string $ownerType, int $ownerId): string
    {
        $parts = [];
        foreach (RgContentBlock::forOwner($ownerType, $ownerId) as $b) {
            $p = $b->payload ?? [];
            $parts[] = match ($b->block_type) {
                'heading' => $p['text'] ?? '',
                'rich_text' => strip_tags($p['html'] ?? ''),
                'two_column' => strip_tags(($p['left_html'] ?? '') . ' ' . ($p['right_html'] ?? '')),
                'quote' => $p['text'] ?? '',
                'cta' => ($p['headline'] ?? '') . ' ' . ($p['text'] ?? ''),
                'faq' => implode(' ', array_map(fn($i) => ($i['question'] ?? '') . ' ' . ($i['answer'] ?? ''), $p['items'] ?? [])),
                default => '',
            };
        }
        return trim(implode(' ', $parts));
    }

    // --- Block renderers ---

    private function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function heading(array $p): string
    {
        $level = in_array($p['level'] ?? 'h2', ['h2', 'h3', 'h4'], true) ? $p['level'] : 'h2';
        $sizes = ['h2' => 'text-3xl md:text-4xl', 'h3' => 'text-2xl md:text-3xl', 'h4' => 'text-xl md:text-2xl'];
        $size = $sizes[$level] ?? 'text-2xl';
        return "<$level class=\"$size font-bold text-slate-900 mt-2 mb-3\">" . $this->e($p['text'] ?? '') . "</$level>";
    }

    private function richText(array $p): string
    {
        $html = $p['html'] ?? '';
        return '<div class="prose prose-slate max-w-none mb-5 text-slate-700 leading-relaxed">' . $html . '</div>';
    }

    private function image(array $p): string
    {
        if (empty($p['src'])) return '';
        $align = $p['align'] ?? 'center';
        $alignClass = ['left' => 'mr-auto', 'right' => 'ml-auto', 'center' => 'mx-auto'][$align] ?? 'mx-auto';
        $alt = $this->e($p['alt'] ?? '');
        $caption = !empty($p['caption']) ? '<figcaption class="text-sm text-slate-500 mt-2 text-center">' . $this->e($p['caption']) . '</figcaption>' : '';
        return '<figure class="my-6"><img src="' . $this->e($p['src']) . '" alt="' . $alt . '" class="rounded-lg max-w-full ' . $alignClass . '" loading="lazy">' . $caption . '</figure>';
    }

    private function gallery(array $p): string
    {
        $images = $p['images'] ?? [];
        if (!$images) return '';
        $cols = (int) ($p['columns'] ?? 3);
        $cols = min(max($cols, 2), 4);
        $gridClass = ['2' => 'grid-cols-2', '3' => 'grid-cols-2 md:grid-cols-3', '4' => 'grid-cols-2 md:grid-cols-4'][$cols] ?? 'grid-cols-3';
        $out = '<div class="grid ' . $gridClass . ' gap-3 my-6">';
        foreach ($images as $img) {
            if (empty($img['src'])) continue;
            $out .= '<img src="' . $this->e($img['src']) . '" alt="' . $this->e($img['alt'] ?? '') . '" class="rounded-lg w-full aspect-square object-cover" loading="lazy">';
        }
        $out .= '</div>';
        return $out;
    }

    private function video(array $p): string
    {
        if (!empty($p['youtube_id'])) {
            $yid = preg_replace('/[^a-zA-Z0-9_-]/', '', $p['youtube_id']);
            $caption = !empty($p['caption']) ? '<figcaption class="text-sm text-slate-500 mt-2 text-center">' . $this->e($p['caption']) . '</figcaption>' : '';
            return '<figure class="my-6"><div class="aspect-video"><iframe class="w-full h-full rounded-lg" src="https://www.youtube.com/embed/' . $yid . '" frameborder="0" allowfullscreen></iframe></div>' . $caption . '</figure>';
        }
        if (!empty($p['src'])) {
            return '<figure class="my-6"><video controls class="w-full rounded-lg"><source src="' . $this->e($p['src']) . '"></video></figure>';
        }
        return '';
    }

    private function faq(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $headingText = $p['heading'] ?? 'Frequently Asked Questions';
        $out = '<div class="my-8 space-y-3">';
        $out .= '<h2 class="text-2xl font-bold text-slate-900 mb-4">' . $this->e($headingText) . '</h2>';
        foreach ($items as $item) {
            if (empty($item['question'])) continue;
            $out .= '<details class="bg-white border border-slate-200 rounded-lg p-4 group">';
            $out .= '<summary class="font-semibold text-slate-900 cursor-pointer flex items-center justify-between">' . $this->e($item['question']) . '<span class="text-slate-400 group-open:rotate-180 transition">▾</span></summary>';
            $out .= '<div class="mt-2 text-slate-600 leading-relaxed">' . nl2br($this->e($item['answer'] ?? '')) . '</div>';
            $out .= '</details>';
        }
        $out .= '</div>';
        return $out;
    }

    private function cta(array $p): string
    {
        $style = $p['style'] ?? 'primary';
        $btnClass = match ($style) {
            'secondary' => 'bg-slate-700 hover:bg-slate-800 text-white',
            'outline' => 'border-2 border-brand-600 text-brand-600 hover:bg-brand-50',
            default => 'bg-brand-600 hover:bg-brand-700 text-white',
        };
        return '<div class="my-8 p-8 rounded-2xl bg-gradient-to-br from-brand-50 to-amber-50 text-center">'
            . ($p['headline'] ? '<h3 class="text-2xl font-bold text-slate-900 mb-2">' . $this->e($p['headline']) . '</h3>' : '')
            . ($p['text'] ? '<p class="text-slate-700 mb-5">' . $this->e($p['text']) . '</p>' : '')
            . '<a href="' . $this->e($p['button_url'] ?? '#') . '" class="inline-block px-6 py-3 rounded-lg font-semibold ' . $btnClass . '">' . $this->e($p['button_text'] ?? 'Click') . '</a>'
            . '</div>';
    }

    private function twoColumn(array $p): string
    {
        return '<div class="grid md:grid-cols-2 gap-6 my-6">'
            . '<div class="prose prose-slate max-w-none">' . ($p['left_html'] ?? '') . '</div>'
            . '<div class="prose prose-slate max-w-none">' . ($p['right_html'] ?? '') . '</div>'
            . '</div>';
    }

    private function listingSlot(array $p, array $context): string
    {
        // Wrap the entire output in sentinel comments so the keyword-page
        // view can strip this block when listings are rendered above the
        // hero slider via the new row-based layout. Keeps backwards-compat
        // for any other view that still wants inline listing_slot output.
        $sentinelStart = '<!--LISTING_SLOT_START-->';
        $sentinelEnd = '<!--LISTING_SLOT_END-->';

        $keywordId = $context['keyword_id'] ?? null;
        if (!$keywordId) return $sentinelStart . ($p['fallback_html'] ?? '') . $sentinelEnd;
        $listings = RgListing::where('keyword_id', $keywordId)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('bid_gp')
            ->orderBy('last_bid_at')
            ->with('resort')
            ->limit(10)
            ->get();

        if ($listings->isEmpty()) {
            return $sentinelStart . '<section class="my-8">' . ($p['fallback_html'] ?? '') . '</section>' . $sentinelEnd;
        }

        $label = $this->e($p['slot_label'] ?? 'Featured Properties');
        $out = $sentinelStart . '<section class="my-8">';
        $out .= '<h2 class="text-2xl font-bold text-slate-900 mb-5">' . $label . '</h2>';
        $out .= '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">';
        foreach ($listings as $listing) {
            $resort = $listing->resort;
            if (!$resort) continue;
            $img = $resort->hero_path ? '/storage/' . ltrim($resort->hero_path, '/') : 'https://placehold.co/400x250?text=' . urlencode($resort->name);
            $out .= '<a href="/listing/' . $this->e($resort->slug) . '" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-slate-200">';
            $out .= '<img src="' . $this->e($img) . '" alt="' . $this->e($resort->name) . '" class="w-full h-44 object-cover" loading="lazy">';
            $out .= '<div class="p-4"><h3 class="font-bold text-slate-900 mb-1">' . $this->e($resort->name) . '</h3>';
            if ($resort->tagline) $out .= '<p class="text-sm text-slate-600 mb-2 line-clamp-2">' . $this->e($resort->tagline) . '</p>';
            $loc = trim(($resort->city ?? '') . ($resort->province ? ', ' . $resort->province : ''));
            if ($loc) $out .= '<p class="text-xs text-slate-500">📍 ' . $this->e($loc) . '</p>';
            $out .= '</div></a>';
        }
        $out .= '</div></section>' . $sentinelEnd;
        return $out;
    }

    private function quote(array $p): string
    {
        return '<blockquote class="my-6 border-l-4 border-brand-500 bg-brand-50 pl-5 py-4 italic text-slate-700">'
            . '<p class="mb-1">"' . $this->e($p['text'] ?? '') . '"</p>'
            . ($p['author'] ? '<footer class="text-sm text-slate-500 not-italic">— ' . $this->e($p['author']) . '</footer>' : '')
            . '</blockquote>';
    }

    private function divider(array $p): string
    {
        $style = $p['style'] ?? 'line';
        return match ($style) {
            'dots' => '<div class="text-center my-8 text-slate-300 text-2xl tracking-[1em]">• • •</div>',
            'thick' => '<hr class="my-8 border-t-4 border-slate-200">',
            default => '<hr class="my-8 border-t border-slate-200">',
        };
    }

    private function customHtml(array $p): string
    {
        $html = (string) ($p['html'] ?? '');

        // Pattern detection: the seeder ships a "Tourist spots in X"
        // section as a custom_html block — a heading + an intro
        // paragraph + a `<div class="space-y-8">` wrapper holding 10+
        // <article> cards. We tag the wrapper with a CSS hook so the
        // sibling slider auto-wiring script (emitted once per page,
        // see bottom of this method) can convert the run of articles
        // into a swipeable autoplay slider at runtime. No DB
        // migration — purely a render-time injection.
        $isSpotsSection = (
            str_contains($html, 'Tourist spots in')
            && preg_match('#<div class="space-y-8">\s*<article#', $html) === 1
        );
        if ($isSpotsSection) {
            $html = preg_replace(
                '#<div class="space-y-8">#',
                '<div class="space-y-8" data-rg-spots-slider>',
                $html,
                1
            );
        }

        $out = '<div class="my-6">' . $html . '</div>';

        // Emit the slider CSS + auto-wiring JS exactly once per page.
        // Idempotent — `window.__rgSpotsSliderWired` guards re-execution
        // and the `data-rg-spots-inited` data attribute prevents
        // double-wrapping if multiple custom_html blocks on the same
        // page each match the pattern.
        if ($isSpotsSection) {
            $out .= '<style>'
                . '[data-rg-spots-slider]{position:relative}'
                // padding-bottom:2rem reserves a 32px clear zone at
                // the bottom of the slider so the progress-bar
                // overlay has room to sit without covering the cards.
                // align-items:flex-start so cards take their natural
                // height instead of stretching to the tallest. The
                // earlier flex-stretch behavior centered the content
                // div in over-sized cells (justify-center inside a
                // taller container created above + below whitespace
                // that read as "cut off on the bottom" on shorter
                // cards). Each card now sits at its own natural
                // height, no stretching, no whitespace artifacts.
                . '[data-rg-spots-slider].rg-spots-active{display:flex;align-items:flex-start;gap:1rem;overflow-x:auto;scroll-behavior:smooth;scroll-snap-type:x mandatory;padding-bottom:2rem;scrollbar-width:none;-ms-overflow-style:none;cursor:grab;}'
                . '[data-rg-spots-slider].rg-spots-active::-webkit-scrollbar{display:none}'
                . '[data-rg-spots-slider].rg-spots-active.is-dragging{cursor:grabbing;scroll-behavior:auto;user-select:none}'
                . '[data-rg-spots-slider].rg-spots-active.is-dragging img{pointer-events:none}'
                // One slide = one full-width article. The article's
                // own internal grid handles the 2-column image+text
                // layout INSIDE the slide. flex-basis 100% (not
                // calc-minus-gap) keeps the math clean: snap-mandatory
                // pins each slide to its own viewport without any
                // peek of the next card.
                . '[data-rg-spots-slider].rg-spots-active > article{flex:0 0 100%;width:100%;scroll-snap-align:start;scroll-snap-stop:always;margin-top:0;margin-bottom:0}'
                // Flex stretches every slide to match the tallest one.
                // The seeded card markup is <article> > <div class="grid
                // md:grid-cols-2"> > {image-link} + {text-div}, and the
                // inner grid has no h-full. On stretch-tall slides the
                // inner grid stays at its natural height which leaves a
                // white band at the bottom — the "image cut off"
                // look for short cards (the article extends below the
                // grid). Force the inner grid div + image link to
                // 100% of the article so the image cell stretches
                // with the slide. Applied at ALL breakpoints — earlier
                // version was md+ only which left mobile short-content
                // cards still showing the bottom gap.
                . '[data-rg-spots-slider].rg-spots-active > article > div{height:100%;display:grid}'
                . '[data-rg-spots-slider].rg-spots-active > article > div > a{height:100%;display:block}'
                . '[data-rg-spots-slider].rg-spots-active > article > div > a > img{height:100%;width:100%;object-fit:cover;object-position:center}'
                // Thin progress bar shown directly below the slider.
                // The bar fills from left to right over AUTOPLAY_MS,
                // restarts on every scroll-stop (autoplay or user
                // swipe), and pauses via animation-play-state in sync
                // with the existing pause flags.
                . '.rg-spots-progress{position:relative;height:3px;background:#e2e8f0;border-radius:999px;margin-top:14px;overflow:hidden}'
                . '.rg-spots-progress-bar{position:absolute;inset:0;background:linear-gradient(to right,#10b981,#34d399);border-radius:999px;transform-origin:left;transform:scaleX(0);animation:rgSpotsProgress 5500ms linear forwards}'
                . '@keyframes rgSpotsProgress{from{transform:scaleX(0)}to{transform:scaleX(1)}}'
                . '</style>'
                . '<script>(function(){'
                    . 'if(window.__rgSpotsSliderWired)return;window.__rgSpotsSliderWired=true;'
                    . 'var AUTOPLAY_MS=5500;'
                    . 'function wire(slider){'
                      . 'if(slider.dataset.rgSpotsInited==="1")return;slider.dataset.rgSpotsInited="1";'
                      . 'slider.classList.add("rg-spots-active");'
                      // Build the progress bar. Mounted as the next
                      // sibling AFTER the slider so it sits directly
                      // below the visible slide without affecting the
                      // horizontal scroll geometry.
                      . 'var progressWrap=document.createElement("div");progressWrap.className="rg-spots-progress";'
                      . 'var progressBar=document.createElement("div");progressBar.className="rg-spots-progress-bar";'
                      . 'progressWrap.appendChild(progressBar);'
                      . 'if(slider.parentNode){slider.parentNode.insertBefore(progressWrap,slider.nextSibling)}'
                      . 'function restartBar(){progressBar.style.animation="none";void progressBar.offsetWidth;progressBar.style.animation=""}'
                      . 'var paused=false,hovered=false,touching=false,visible=true;'
                      // Sync the bar play-state with any change to a
                      // pause flag. Wrap-and-call pattern: every place
                      // that mutates a flag below now calls syncPause().
                      . 'function syncPause(){progressBar.style.animationPlayState=(paused||hovered||touching||!visible||document.hidden)?"paused":"running"}'
                      . 'function slideWidth(){var c=slider.querySelector("article");if(!c)return 600;var gap=parseFloat(getComputedStyle(slider).gap||"16")||16;return c.offsetWidth+gap}'
                      . 'function updateEnd(){var atEnd=Math.ceil(slider.scrollLeft+slider.clientWidth)>=slider.scrollWidth-2;if(atEnd){slider.setAttribute("data-rg-end","1")}else{slider.removeAttribute("data-rg-end")}}'
                      // Advance the slider one card to the right. Loops
                      // back to slot 0 at end of track. NO pause-flag
                      // check here — this is only called from the bar
                      // animationend event, which only fires when the
                      // bar's animation has been running and reached
                      // its end. Pause state freezes the bar via
                      // animation-play-state, so animationend cannot
                      // fire during a pause.
                      . 'function advance(){'
                        . 'var w=slideWidth();var atEnd=slider.scrollLeft+slider.clientWidth>=slider.scrollWidth-4;'
                        . 'if(atEnd){slider.scrollTo({left:0,behavior:"smooth"})}else{slider.scrollBy({left:w,behavior:"smooth"})}'
                        . 'autoplayInProgress=true;'
                      . '}'
                      // The bar IS the timer. When its animation
                      // completes (animationend fires), advance the
                      // slide and reset the bar. This keeps the bar
                      // PERFECTLY in sync with the slide motion —
                      // the slide moves exactly when the bar reaches
                      // 100%, never before or after. No setInterval
                      // drift.
                      . 'var autoplayInProgress=false;'
                      . 'progressBar.addEventListener("animationend",function(){advance();restartBar()});'
                      . 'slider.addEventListener("mouseenter",function(){hovered=true;syncPause()});'
                      . 'slider.addEventListener("mouseleave",function(){hovered=false;syncPause()});'
                      . 'slider.addEventListener("touchstart",function(){touching=true;syncPause()},{passive:true});'
                      . 'slider.addEventListener("touchend",function(){setTimeout(function(){touching=false;syncPause()},1500)},{passive:true});'
                      . 'if("IntersectionObserver" in window){'
                        . 'var io=new IntersectionObserver(function(es){es.forEach(function(en){visible=en.isIntersecting;syncPause()})},{threshold:0.15});'
                        . 'io.observe(slider);'
                      . '}'
                      // Tab-hidden state changes flow through to the
                      // bar so it stops counting down on another tab.
                      . 'document.addEventListener("visibilitychange",syncPause);'
                      // mouse-drag scroll
                      . 'var dragStart=null,dragScroll=0,didDrag=false;'
                      . 'slider.addEventListener("mousedown",function(e){if(e.button!==0)return;dragStart=e.pageX;dragScroll=slider.scrollLeft;didDrag=false});'
                      . 'window.addEventListener("mousemove",function(e){if(dragStart===null)return;var dx=e.pageX-dragStart;if(Math.abs(dx)>4){if(!didDrag){slider.classList.add("is-dragging");didDrag=true}slider.scrollLeft=dragScroll-dx}});'
                      . 'window.addEventListener("mouseup",function(){if(dragStart!==null&&didDrag){slider.classList.remove("is-dragging")}dragStart=null});'
                      . 'slider.addEventListener("click",function(e){if(didDrag){e.preventDefault();e.stopPropagation();didDrag=false}},true);'
                      // Scroll listener: track end-of-track AND restart
                      // the bar on USER-initiated scroll-stops. We
                      // skip the restart when the scroll was kicked off
                      // by the autoplay advance() above (which already
                      // restarted the bar via animationend), tracked by
                      // the autoplayInProgress flag. 300ms debounce so
                      // the restart fires once after the smooth scroll
                      // settles.
                      . 'var scrollDebounce=null;'
                      . 'slider.addEventListener("scroll",function(){'
                        . 'updateEnd();'
                        . 'clearTimeout(scrollDebounce);'
                        . 'scrollDebounce=setTimeout(function(){'
                          . 'if(autoplayInProgress){autoplayInProgress=false;return}'
                          . 'restartBar();'
                        . '},300);'
                      . '},{passive:true});'
                      . 'updateEnd();syncPause();'
                    . '}'
                    . 'function init(){var ss=document.querySelectorAll("[data-rg-spots-slider]");for(var i=0;i<ss.length;i++)wire(ss[i])}'
                    . 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}'
                . '})();</script>';
        }

        return $out;
    }

    // ============================================================
    // Custom Resort Guru elements. Each one mirrors the markup a
    // food / keyword-page seeder ships today so admin-authored blocks
    // render identically to the seeded ones.
    // ============================================================

    /**
     * Splide hero carousel. Supports two visual styles:
     *   - card (default): the food-page rg-area-hero pattern with an
     *     eyebrow title row + figure/figcaption captions per slide.
     *   - fullbleed: the resort-page rg-hero-splide pattern with a
     *     dark gradient overlay + caption stacked at the bottom.
     * Splide JS/CSS is already loaded site-wide by public.blade.php.
     */
    private function heroSlider(array $p): string
    {
        $images = array_values(array_filter(
            $p['images'] ?? [],
            fn($i) => !empty($i['src'])
        ));
        if (!$images) return '';

        $style = ($p['style'] ?? 'card') === 'fullbleed' ? 'fullbleed' : 'card';
        return $style === 'fullbleed'
            ? $this->heroSliderFullbleed($p, $images)
            : $this->heroSliderCard($p, $images);
    }

    /**
     * Card-style hero (rg-area-hero). Used by the food-page seeder.
     * Eyebrow title + subtitle above the carousel, figcaption per slide
     * with optional credit link.
     */
    private function heroSliderCard(array $p, array $images): string
    {
        $eyebrowTitle = $this->e($p['eyebrow_title'] ?? '');
        $eyebrowSubtitle = $this->e($p['eyebrow_subtitle'] ?? '');
        $autoplay = !empty($p['autoplay']) ? 'true' : 'false';
        $interval = (int) ($p['interval'] ?? 6500);
        $sliderId = 'rg-area-hero-' . substr(md5(json_encode($images)), 0, 8);

        $eyebrowHtml = '';
        if ($eyebrowTitle !== '' || $eyebrowSubtitle !== '') {
            $eyebrowHtml = '<div class="flex items-baseline justify-between mb-3">';
            $eyebrowHtml .= $eyebrowTitle !== ''
                ? '<h2 class="text-xs uppercase tracking-[0.18em] font-bold text-brand-700 m-0">' . $eyebrowTitle . '</h2>'
                : '<span></span>';
            if ($eyebrowSubtitle !== '') {
                $eyebrowHtml .= '<span class="text-xs text-slate-500">' . $eyebrowSubtitle . '</span>';
            }
            $eyebrowHtml .= '</div>';
        }

        $slides = '';
        foreach ($images as $img) {
            $captionTitle = trim($img['caption_title'] ?? '');
            $caption = trim($img['caption'] ?? '');
            $creditUrl = trim($img['credit_url'] ?? '');

            $captionHtml = '';
            if ($captionTitle !== '' || $caption !== '') {
                $captionHtml = '<figcaption>';
                if ($captionTitle !== '') {
                    $captionHtml .= '<strong>' . $this->e($captionTitle) . '</strong>';
                }
                if ($caption !== '') {
                    if ($creditUrl !== '') {
                        $captionHtml .= '<small><a href="' . $this->e($creditUrl)
                            . '" rel="nofollow noopener" target="_blank" style="color:#fbbf24;text-decoration:underline">'
                            . $this->e($caption) . '</a></small>';
                    } else {
                        $captionHtml .= '<span>' . $this->e($caption) . '</span>';
                    }
                }
                $captionHtml .= '</figcaption>';
            }

            $slides .= '<li class="splide__slide"><figure class="rg-area-hero__slide">'
                . '<img src="' . $this->e($this->normalizeMediaUrl($img['src'])) . '" alt="' . $this->e($img['alt'] ?? '')
                . '" loading="lazy">' . $captionHtml . '</figure></li>';
        }

        return '<section class="rg-area-hero my-8 not-prose" aria-label="Photo gallery">'
            . $eyebrowHtml
            . '<div id="' . $sliderId . '" class="rg-area-hero__splide splide"'
            . ' data-splide-config="{&quot;type&quot;:&quot;loop&quot;,&quot;autoplay&quot;:' . $autoplay
            . ',&quot;interval&quot;:' . $interval . ',&quot;arrows&quot;:true,&quot;pagination&quot;:true}">'
            . '<div class="splide__track"><ul class="splide__list">' . $slides . '</ul></div>'
            . '</div>'
            . $this->splideAutoMount($sliderId)
            . '</section>';
    }

    /**
     * Fullbleed hero (rg-hero-splide). Used by the resort-page seeder.
     * Each slide is a darkened image with caption stack at the bottom.
     */
    private function heroSliderFullbleed(array $p, array $images): string
    {
        $autoplay = !empty($p['autoplay']) ? 'true' : 'false';
        $interval = (int) ($p['interval'] ?? 6500);
        $sliderId = 'rg-hero-' . substr(md5(json_encode($images)), 0, 8);

        $slides = '';
        foreach ($images as $img) {
            $captionTitle = $this->e($img['caption_title'] ?? '');
            $caption = $this->e($img['caption'] ?? '');
            $captionBlock = '';
            if ($captionTitle !== '' || $caption !== '') {
                $captionBlock = '<div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8 text-white">'
                    . ($captionTitle !== '' ? '<div class="text-lg sm:text-2xl font-bold leading-tight mb-1">' . $captionTitle . '</div>' : '')
                    . ($caption !== '' ? '<div class="text-sm sm:text-base opacity-90">' . $caption . '</div>' : '')
                    . '</div>';
            }
            $slides .= '<li class="splide__slide">'
                . '<div class="relative w-full h-full bg-slate-900">'
                . '<img src="' . $this->e($this->normalizeMediaUrl($img['src'])) . '" alt="' . $this->e($img['alt'] ?? '')
                . '" class="absolute inset-0 w-full h-full object-cover" loading="lazy">'
                . '<div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/5 to-transparent"></div>'
                . $captionBlock
                . '</div></li>';
        }

        // Dot pagination removed — the arrows already give navigation
        // and the dots were rendering outside the rounded clip, looking
        // like the slider was getting cropped at the bottom.
        //
        // `cover: true` was removed because it caused inconsistent
        // sizing between slide 1 and subsequent slides. With cover,
        // Splide finds the first <img> in each slide, sets it as the
        // slide's background-image, and visually hides the <img>. But
        // the slide markup ALSO has the <img> absolute-positioned with
        // `inset-0 w-full h-full object-cover` — so two competing
        // sizing systems were applied. On slide 1 Splide's pre-init
        // measurements + my CSS aligned. On slide 2+, Splide's post-
        // init height recomputation kicked in but the per-slide
        // background images were sized off the BACKGROUND-COVER
        // algorithm (which is NOT pixel-equivalent to object-fit
        // cover at certain image aspects), leaving a clipped band at
        // the bottom of every slide except the first.
        //
        // Drop cover: true. My custom markup already covers via
        // object-fit:cover on the <img>. heightRatio:0.5 still drives
        // the slide height deterministically. Plus an explicit
        // aspect-ratio:2/1 fallback so each slide is the same height
        // even if Splide hasn't finished its initial measurement when
        // the user clicks ahead.
        $css = '<style>'
            . '.rg-hero-splide .splide__slide{aspect-ratio:2/1}'
            . '@media(max-width:640px){.rg-hero-splide .splide__slide{aspect-ratio:16/10}}'
            . '.rg-hero-splide .splide__list{align-items:stretch}'
            . '.rg-hero-splide .splide__slide > div{height:100%}'
            . '</style>';

        return '<section id="' . $sliderId . '" class="rg-hero-splide splide not-prose my-6 overflow-hidden rounded-xl" aria-label="Hero gallery"'
            . ' data-splide-config="{&quot;type&quot;:&quot;loop&quot;,&quot;autoplay&quot;:' . $autoplay
            . ',&quot;interval&quot;:' . $interval . ',&quot;arrows&quot;:true,&quot;pagination&quot;:false,&quot;heightRatio&quot;:0.5}">'
            . '<div class="splide__track"><ul class="splide__list">' . $slides . '</ul></div>'
            . $css
            . $this->splideAutoMount($sliderId)
            . '</section>';
    }

    /**
     * Strip any protocol+host that points at /storage so image URLs
     * resolve relative to the current host. Absolute URLs stamped at
     * seed time (e.g. http://resortguruph.test/storage/...) otherwise
     * 404 when the page is browsed from a different domain (Valet vs
     * php artisan serve on localhost, prod-staging differences, etc.).
     */
    private function normalizeMediaUrl(string $url): string
    {
        return preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $url) ?: $url;
    }

    /**
     * Auto-mount script shared by both hero styles. The Splide CDN script
     * is `defer`-loaded by layouts/public.blade.php, so this inline
     * script may execute BEFORE window.Splide is defined. We wait for
     * DOMContentLoaded (after which deferred scripts have executed) and
     * fall back to polling if Splide still isn't there for any reason.
     */
    private function splideAutoMount(string $sliderId): string
    {
        $id = json_encode($sliderId);
        return '<script>(function(){'
            . 'function mount(){'
              . 'var el=document.getElementById(' . $id . ');'
              . 'if(!el||el.dataset.splideMounted)return;'
              . 'if(!window.Splide){return setTimeout(mount,50);}'
              . 'el.dataset.splideMounted=1;'
              . 'try{new Splide(el,JSON.parse(el.dataset.splideConfig.replace(/&quot;/g,String.fromCharCode(34)))).mount();}catch(e){console.error("Splide mount failed",e);}'
            . '}'
            . 'if(document.readyState==="loading"){'
              . 'document.addEventListener("DOMContentLoaded",mount);'
            . '}else{mount();}'
            . '})();</script>';
    }

    /**
     * 4-card quick-facts strip. Each card has an icon, big headline, small
     * label, and a one-line detail. Matches the food-page strip exactly
     * (Per person / Easiest window / Avoid / Traffic).
     */
    private function quickFacts(array $p): string
    {
        $cards = $p['cards'] ?? [];
        if (!$cards) return '';

        $palettes = [
            'blue'    => ['bg' => '#eff6ff', 'br' => '#bfdbfe', 'fg' => '#1d4ed8', 'sm' => '#1e3a8a'],
            'emerald' => ['bg' => '#ecfdf5', 'br' => '#a7f3d0', 'fg' => '#047857', 'sm' => '#064e3b'],
            'rose'    => ['bg' => '#fff1f2', 'br' => '#fecdd3', 'fg' => '#be123c', 'sm' => '#881337'],
            'amber'   => ['bg' => '#fffbeb', 'br' => '#fcd34d', 'fg' => '#b45309', 'sm' => '#78350f'],
            'violet'  => ['bg' => '#f5f3ff', 'br' => '#ddd6fe', 'fg' => '#6d28d9', 'sm' => '#4c1d95'],
            'slate'   => ['bg' => '#f8fafc', 'br' => '#cbd5e1', 'fg' => '#334155', 'sm' => '#0f172a'],
        ];

        $count = count($cards);
        $gridCols = match (true) {
            $count >= 4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
            $count === 3 => 'grid-cols-1 md:grid-cols-3',
            $count === 2 => 'grid-cols-1 md:grid-cols-2',
            default => 'grid-cols-1',
        };

        $out = '';
        if (!empty($p['heading'])) {
            $out .= '<h2 class="text-2xl font-bold text-slate-900 mt-8 mb-3">'
                . $this->e($p['heading']) . '</h2>';
        }
        $out .= '<div class="not-prose my-8 grid ' . $gridCols . ' gap-3">';
        foreach ($cards as $card) {
            $pal = $palettes[$card['color'] ?? 'blue'] ?? $palettes['blue'];
            $svg = $this->quickFactIconSvg($card['icon'] ?? 'info');
            $out .= '<div class="rounded-lg p-4 text-center"'
                . ' style="background:' . $pal['bg'] . ';border:1px solid ' . $pal['br'] . '">'
                . '<div class="flex justify-center mb-2" style="color:' . $pal['fg'] . '">' . $svg . '</div>'
                . '<div class="text-2xl font-bold" style="color:' . $pal['fg'] . '">'
                . $this->e($card['big'] ?? '') . '</div>'
                . '<div class="text-[10px] uppercase tracking-wide font-bold" style="color:' . $pal['sm'] . '">'
                . $this->e($card['label'] ?? '') . '</div>'
                . '<div class="text-xs text-slate-600 mt-1">' . $this->e($card['detail'] ?? '') . '</div>'
                . '</div>';
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Icon set for the quick_facts strip. Adding a new icon: add an `if`
     * branch here and add the slug to the builder's icon select.
     */
    private function quickFactIconSvg(string $icon): string
    {
        $base = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">';
        return $base . match ($icon) {
            'money'    => '<circle cx="12" cy="12" r="9"/><path d="M9 8.5h4.5a2.25 2.25 0 0 1 0 4.5H9m0-4.5v8m0-3.5h5"/>',
            'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
            'warning'  => '<path d="M12 9v4m0 3.5h.01M3.86 17.74 10.4 4.95a1.8 1.8 0 0 1 3.2 0l6.54 12.79c.66 1.3-.27 2.86-1.6 2.86H5.46c-1.33 0-2.26-1.56-1.6-2.86Z"/>',
            'traffic'  => '<rect x="9" y="3" width="6" height="18" rx="2"/><circle cx="12" cy="7.5" r="1.2"/><circle cx="12" cy="12" r="1.2"/><circle cx="12" cy="16.5" r="1.2"/>',
            'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/>',
            'location' => '<circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5-8 12-8 12S4 15 4 10a8 8 0 0 1 8-8z"/>',
            'food'     => '<path d="M8 3v18M16 3v18M3 10h6M15 6h6M5 14h4M16 14h4"/>',
            'star'     => '<path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/>',
            default    => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5m0 3h.01"/>',
        } . '</svg>';
    }

    /**
     * Resort Guru Editor Rating card. Big overall star score on the left,
     * per-criterion breakdown on the right, optional summary blurb below.
     */
    private function editorRating(array $p): string
    {
        $overall = (float) ($p['overall'] ?? 4.0);
        $overall = max(0, min(5, $overall));
        $criteria = $p['criteria'] ?? [];
        $title = $p['title'] ?? 'Resort Guru Editor Rating';
        $summary = $p['summary'] ?? '';

        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $fillPct = max(0, min(1, $overall - ($i - 1))) * 100;
            $stars .= '<span class="relative inline-block w-7 h-7" aria-hidden="true">'
                . '<svg viewBox="0 0 24 24" class="absolute inset-0 w-full h-full" fill="#e2e8f0"><path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/></svg>'
                . '<span class="absolute inset-0 overflow-hidden" style="width:' . $fillPct . '%">'
                . '<svg viewBox="0 0 24 24" class="absolute inset-0 w-7 h-7" fill="#f59e0b"><path d="M12 2l2.4 6.6L21 9.7l-5 4.6L17.4 21 12 17.6 6.6 21 8 14.3 3 9.7l6.6-1.1z"/></svg>'
                . '</span></span>';
        }

        $criteriaHtml = '';
        foreach ($criteria as $c) {
            $score = (float) ($c['score'] ?? 0);
            $pct = max(0, min(100, ($score / 5) * 100));
            $criteriaHtml .= '<div class="flex items-center gap-3 text-sm">'
                . '<div class="w-24 font-semibold text-slate-700">' . $this->e($c['name'] ?? '') . '</div>'
                . '<div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">'
                . '<div class="h-full" style="width:' . $pct . '%;background:#f59e0b"></div></div>'
                . '<div class="w-10 text-right font-bold text-slate-700 tabular-nums">'
                . number_format($score, 1) . '</div></div>';
        }

        $summaryHtml = $summary !== ''
            ? '<p class="text-sm text-slate-600 mt-5 leading-relaxed border-t border-slate-100 pt-4">'
                . $this->e($summary) . '</p>'
            : '';

        return '<div class="not-prose my-8 p-6 rounded-2xl bg-white border border-slate-200">'
            . '<div class="flex items-start justify-between gap-4 mb-5 flex-wrap">'
            . '<div class="min-w-0">'
            . '<div class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500 mb-1">'
            . $this->e($title) . '</div>'
            . '<div class="flex items-center gap-2"><div class="flex">' . $stars . '</div>'
            . '<div class="text-3xl font-extrabold text-slate-900 tabular-nums">'
            . number_format($overall, 1) . '</div>'
            . '<div class="text-sm text-slate-500 self-end pb-1">/ 5.0</div></div></div></div>'
            . ($criteriaHtml ? '<div class="space-y-2 mt-2">' . $criteriaHtml . '</div>' : '')
            . $summaryHtml
            . '</div>';
    }

    /**
     * Full "Restaurants/Resorts We Recommend" listing band. Defers to the
     * existing partials.listings-rows so it stays in sync with the rest of
     * the site if that partial changes. Context must carry $keyword for
     * category-aware wording (food vs resort).
     */
    private function listingBlock(array $p, array $context): string
    {
        $keyword = $context['keyword'] ?? null;
        if (!$keyword) {
            return '<!-- listing_block: missing keyword in context -->';
        }

        $isFood = ($keyword->category ?? 'resort') === 'food';
        $listings = $isFood
            ? ($context['restaurantListings'] ?? collect())
            : ($context['listings'] ?? collect());

        $view = view('partials.listings-rows', [
            'listings' => $listings,
            'listingGalleries' => $context['listingGalleries'] ?? [],
            'keyword' => $keyword,
            'area' => $p['area'] ?? ($context['areaForCta'] ?? null),
        ]);

        // Sentinel-wrap so the public keyword-page view can strip this block
        // when the explicit listings band is already shown above the content
        // body. Without the wrapper the same listings render twice.
        return '<!--LISTING_BLOCK_START-->' . (string) $view->render() . '<!--LISTING_BLOCK_END-->';
    }

    /**
     * "What's in X (beyond the food)" attraction-card grid. Each item has
     * name, image, short tagline, longer blurb, and an outbound URL with
     * rel="nofollow noopener" per Resort Guru Rule 11.
     */
    private function attractions(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $p['heading'] ?? "What's beyond the food";
        $intro = $p['intro'] ?? '';

        // Per-card hero is now a stack of fading images. Each item supports
        // either a single `image` string (legacy) or an `images[]` array.
        // When multiple images exist, the renderer emits a relative-
        // positioned <div> with absolutely-stacked <img>s that fade
        // through one another using CSS keyframes — animation-delay
        // staggers each layer by (cycleSeconds / count). Clicking any
        // image fires the rg-attr-lightbox handler emitted below.
        $perImageSeconds = 3.5;

        $out = '<section class="not-prose my-10" data-rg-attractions>';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $this->e($heading) . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-slate-600 mb-6 leading-relaxed">' . $this->e($intro) . '</p>';
        }
        // Horizontal swipeable slider. All cards stay in the DOM so
        // Googlebot can crawl every spot — only their viewport
        // position changes. Native CSS scroll-snap handles touch
        // swipe + smooth scrolling; the JS at the bottom of this
        // method adds autoplay, mouse-drag, and pause-on-hover.
        $out .= '<div class="rg-attr-slider relative" data-rg-attr-slider>';
        $out .= '<div class="rg-attr-track flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-3" data-rg-attr-track tabindex="0" aria-label="Tourist spots carousel" role="region">';

        $cycleStyles = []; // collected per-card CSS for the fade animation
        foreach ($items as $cardIndex => $item) {
            if (empty($item['name'])) continue;
            $name = $this->e($item['name']);

            // Resolve the image set: prefer the new images[] array, fall
            // back to the legacy single image. Strings are accepted but
            // arrays-of-strings are the canonical shape.
            $images = [];
            if (!empty($item['images']) && is_array($item['images'])) {
                foreach ($item['images'] as $im) {
                    if (is_string($im) && trim($im) !== '') $images[] = trim($im);
                    elseif (is_array($im) && !empty($im['src'])) $images[] = trim($im['src']);
                }
            }
            if (!$images && !empty($item['image'])) $images[] = (string) $item['image'];
            $images = array_values(array_unique($images));

            // Build the hero — empty container if no images, single still
            // if only one image, or the fading stack when 2+ images.
            if (!$images) {
                $hero = '';
            } else if (count($images) === 1) {
                $src = $this->e($this->normalizeMediaUrl($images[0]));
                $hero = '<button type="button" class="block w-full aspect-[16/10] overflow-hidden bg-slate-100 cursor-zoom-in"'
                    . ' data-rg-lightbox="' . $src . '" data-rg-lightbox-caption="' . $name . '" aria-label="Open ' . $name . ' image">'
                    . '<img src="' . $src . '" alt="' . $name . '" class="w-full h-full object-cover" loading="lazy">'
                    . '</button>';
            } else {
                $count = count($images);
                $cycle = number_format($perImageSeconds * $count, 2, '.', '');
                // Per-card phase offset (seconds) so the 6 cards on a
                // section don't all hit their crossfade at the same
                // moment. Without this, the whole section visibly blinks
                // every {$perImageSeconds} seconds. 1.3s gives a clean
                // spread across the cycle for up to ~8 cards before the
                // offset starts wrapping back near zero.
                $cardPhaseSec = 1.3;
                $phaseOffsetSec = $cardIndex * $cardPhaseSec;
                $layers = '';
                foreach ($images as $i => $src) {
                    $safeSrc = $this->e($this->normalizeMediaUrl($src));
                    // Negative animation-delay = animation appears to
                    // have been running for that many seconds already.
                    // animation-fill-mode: both (on the CSS rule below)
                    // makes positive-delay images render the 0% keyframe
                    // state (opacity 0) during the wait, so we no longer
                    // need the legacy opacity-{100,0} initial classes.
                    $delay = number_format($perImageSeconds * $i - $phaseOffsetSec, 2, '.', '');
                    $layers .= '<img src="' . $safeSrc . '" alt="' . $name . ($i === 0 ? '' : ' (' . ($i + 1) . ')') . '"'
                        . ' class="rg-attr-fade absolute inset-0 w-full h-full object-cover"'
                        . ' data-rg-fade-index="' . $i . '"'
                        . ' style="animation-delay:' . $delay . 's"'
                        . ' loading="lazy">';
                }
                $cardKey = $cardIndex;
                $cycleStyles[] = ".rg-attr-card-{$cardKey} .rg-attr-fade{animation:rgAttrFade {$cycle}s ease-in-out infinite;animation-fill-mode:both}";
                $imageJson = htmlspecialchars(json_encode(array_map(fn($s) => $this->normalizeMediaUrl($s), $images)), ENT_QUOTES, 'UTF-8');
                $hero = '<button type="button" class="block w-full aspect-[16/10] overflow-hidden bg-slate-100 cursor-zoom-in relative rg-attr-card-' . $cardKey . '"'
                    . ' data-rg-lightbox-group="' . $imageJson . '" data-rg-lightbox-caption="' . $name . '" aria-label="Open gallery for ' . $name . '">'
                    . $layers
                    // Tiny pip indicator showing how many images are in the stack.
                    . '<div class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full bg-black/55 text-white text-[10px] font-semibold pointer-events-none">'
                    . '<span class="inline-flex items-center gap-1">'
                    . '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 14l6-6 7 7M14 11l3-3 4 4"/></svg>'
                    . $count . '</span></div>'
                    . '</button>';
            }

            $url = $item['url'] ?? '';
            $link = $url
                ? '<a href="' . $this->e($url) . '" rel="noopener nofollow" target="_blank" '
                    . 'class="text-sm font-semibold text-brand-600 hover:text-brand-700 mt-3 inline-flex items-center gap-1">'
                    . 'Learn more <span aria-hidden="true">→</span></a>'
                : '';
            $out .= '<article class="rg-attr-card snap-start shrink-0 w-[280px] sm:w-[300px] md:w-[320px] lg:w-[340px] rounded-xl border border-slate-200 bg-white overflow-hidden flex flex-col shadow-sm hover:shadow-md transition-shadow">'
                . $hero
                . '<div class="p-4 flex-1 flex flex-col">'
                . '<div class="text-[10px] uppercase tracking-wide font-bold text-emerald-700 mb-1">'
                . $this->e($item['short'] ?? '') . '</div>'
                . '<h3 class="font-bold text-slate-900 mb-1">' . $name . '</h3>'
                . '<p class="text-sm text-slate-600 leading-relaxed">' . $this->e($item['blurb'] ?? '') . '</p>'
                . $link
                . '</div></article>';
        }
        // Close the .rg-attr-track (cards row) and the .rg-attr-slider
        // wrapper. The wrapper holds position:relative so the optional
        // edge fade overlay below can absolutely-position over the
        // track's right edge as a visual "more →" affordance.
        $out .= '</div>'; // .rg-attr-track
        // Soft edge fade on the right hinting there's more to scroll —
        // pointer-events:none so it doesn't steal taps from the cards.
        $out .= '<div class="pointer-events-none absolute top-0 right-0 bottom-3 w-12 bg-gradient-to-l from-white to-transparent rg-attr-fade-edge"></div>';
        $out .= '</div>'; // .rg-attr-slider

        // CSS keyframes — single shared keyframe, per-card duration set
        // via class above. 12% / 28% pin opacity:1 (visible window),
        // 33% / 95% drop back to 0 (next layer takes over). With N=3
        // images at 3.5s each (10.5s cycle), each layer sits visible for
        // ~1.7s and fades for ~0.8s. Tweak $perImageSeconds to taste.
        $out .= '<style>'
            . '@keyframes rgAttrFade{0%{opacity:0}5%{opacity:1}30%{opacity:1}38%{opacity:0}100%{opacity:0}}'
            . '.rg-attr-fade{transition:opacity .4s ease-in-out}'
            . implode('', $cycleStyles)
            // Lightbox styles.
            . '.rg-attr-lightbox{position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.92);display:none;align-items:center;justify-content:center;padding:1.5rem}'
            . '.rg-attr-lightbox.is-open{display:flex}'
            . '.rg-attr-lightbox__inner{position:relative;max-width:96vw;max-height:90vh}'
            . '.rg-attr-lightbox__img{max-width:96vw;max-height:80vh;display:block;border-radius:8px;box-shadow:0 25px 50px rgba(0,0,0,.5)}'
            . '.rg-attr-lightbox__caption{margin-top:.75rem;color:#e2e8f0;font-size:14px;font-weight:600;text-align:center}'
            . '.rg-attr-lightbox__close{position:absolute;top:-2.4rem;right:0;background:transparent;color:white;border:0;font-size:28px;line-height:1;cursor:pointer;padding:.25rem .6rem}'
            . '.rg-attr-lightbox__nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.1);color:white;border:0;width:44px;height:44px;border-radius:50%;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center}'
            . '.rg-attr-lightbox__nav:hover{background:rgba(255,255,255,.2)}'
            . '.rg-attr-lightbox__nav--prev{left:-3.5rem}'
            . '.rg-attr-lightbox__nav--next{right:-3.5rem}'
            . '@media (max-width:640px){.rg-attr-lightbox__nav--prev{left:.5rem}.rg-attr-lightbox__nav--next{right:.5rem}}'
            // Slider styles: hide native scrollbar (still scrollable),
            // grab-cursor while drag-scrolling, fade-edge hidden when
            // the track is scrolled all the way to the end, disable
            // hover scaling on cards while user is dragging.
            . '.rg-attr-track{scrollbar-width:none;-ms-overflow-style:none;cursor:grab}'
            . '.rg-attr-track::-webkit-scrollbar{display:none}'
            . '.rg-attr-track.is-dragging{cursor:grabbing;scroll-behavior:auto;user-select:none}'
            . '.rg-attr-track.is-dragging img{pointer-events:none}'
            . '.rg-attr-slider[data-rg-end] .rg-attr-fade-edge{opacity:0;transition:opacity .25s ease}'
            . '.rg-attr-fade-edge{transition:opacity .25s ease}'
            . '</style>';

        // Single global lightbox + JS. Idempotent — only the first
        // attractions block on the page injects the lightbox markup;
        // subsequent blocks just attach handlers to the shared overlay.
        $out .= '<div class="rg-attr-lightbox" data-rg-lightbox-overlay role="dialog" aria-modal="true" aria-label="Image gallery">'
            . '<div class="rg-attr-lightbox__inner">'
            . '<button type="button" class="rg-attr-lightbox__close" data-rg-lightbox-close aria-label="Close">&times;</button>'
            . '<button type="button" class="rg-attr-lightbox__nav rg-attr-lightbox__nav--prev" data-rg-lightbox-prev aria-label="Previous image">&larr;</button>'
            . '<button type="button" class="rg-attr-lightbox__nav rg-attr-lightbox__nav--next" data-rg-lightbox-next aria-label="Next image">&rarr;</button>'
            . '<img class="rg-attr-lightbox__img" data-rg-lightbox-img src="" alt="">'
            . '<div class="rg-attr-lightbox__caption" data-rg-lightbox-cap></div>'
            . '</div></div>';

        $out .= '<script>(function(){'
            . 'if(window.__rgAttrLightboxWired)return;window.__rgAttrLightboxWired=true;'
            . 'function init(){'
              . 'var overlay=document.querySelector("[data-rg-lightbox-overlay]");'
              . 'if(!overlay)return;'
              . 'var imgEl=overlay.querySelector("[data-rg-lightbox-img]");'
              . 'var capEl=overlay.querySelector("[data-rg-lightbox-cap]");'
              . 'var closeBtn=overlay.querySelector("[data-rg-lightbox-close]");'
              . 'var prevBtn=overlay.querySelector("[data-rg-lightbox-prev]");'
              . 'var nextBtn=overlay.querySelector("[data-rg-lightbox-next]");'
              . 'var state={list:[],i:0,caption:""};'
              . 'function render(){imgEl.src=state.list[state.i]||"";capEl.textContent=state.caption+(state.list.length>1?" ("+(state.i+1)+"/"+state.list.length+")":"");var multi=state.list.length>1;prevBtn.style.display=multi?"flex":"none";nextBtn.style.display=multi?"flex":"none"}'
              . 'function open(list,caption,i){state.list=list||[];state.caption=caption||"";state.i=Math.max(0,Math.min(i||0,state.list.length-1));render();overlay.classList.add("is-open");document.body.style.overflow="hidden"}'
              . 'function close(){overlay.classList.remove("is-open");document.body.style.overflow=""}'
              . 'function step(d){if(!state.list.length)return;state.i=(state.i+d+state.list.length)%state.list.length;render()}'
              . 'document.addEventListener("click",function(e){'
                . 'var single=e.target.closest("[data-rg-lightbox]");'
                . 'var group=e.target.closest("[data-rg-lightbox-group]");'
                . 'var t=single||group;'
                . 'if(!t)return;'
                . 'e.preventDefault();'
                . 'if(group){try{var imgs=JSON.parse(group.getAttribute("data-rg-lightbox-group"));open(imgs,group.getAttribute("data-rg-lightbox-caption")||"",0)}catch(err){}}'
                . 'else{open([single.getAttribute("data-rg-lightbox")],single.getAttribute("data-rg-lightbox-caption")||"",0)}'
              . '});'
              . 'closeBtn.addEventListener("click",close);'
              . 'prevBtn.addEventListener("click",function(){step(-1)});'
              . 'nextBtn.addEventListener("click",function(){step(1)});'
              . 'overlay.addEventListener("click",function(e){if(e.target===overlay)close()});'
              . 'document.addEventListener("keydown",function(e){if(!overlay.classList.contains("is-open"))return;if(e.key==="Escape")close();else if(e.key==="ArrowLeft")step(-1);else if(e.key==="ArrowRight")step(1)});'
            . '}'
            . 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}'
            . '})();</script>';

        // Slider behavior: autoplay every 4.5s, mouse-drag scroll on
        // desktop, pause-on-hover, pause-on-touch, IntersectionObserver
        // pause when the slider is off-screen, end-of-track edge fade
        // hidden via data attribute. All sliders on the page share one
        // IIFE that hands each one its own state object.
        $out .= '<script>(function(){'
            . 'if(window.__rgAttrSliderWired)return;window.__rgAttrSliderWired=true;'
            . 'var AUTOPLAY_MS=4500;'
            . 'function wire(slider){'
              . 'var track=slider.querySelector("[data-rg-attr-track]");'
              . 'if(!track||slider.dataset.rgAttrInit==="1")return;'
              . 'slider.dataset.rgAttrInit="1";'
              . 'var paused=false,hovered=false,touching=false,visible=true;'
              . 'function cardWidth(){'
                . 'var c=track.querySelector(".rg-attr-card");'
                . 'if(!c)return 320;'
                . 'var gap=parseFloat(getComputedStyle(track).gap||"16")||16;'
                . 'return c.offsetWidth+gap;'
              . '}'
              . 'function updateEnd(){'
                . 'var atEnd=Math.ceil(track.scrollLeft+track.clientWidth)>=track.scrollWidth-2;'
                . 'if(atEnd){slider.setAttribute("data-rg-end","1")}else{slider.removeAttribute("data-rg-end")}'
              . '}'
              . 'function tick(){'
                . 'if(paused||hovered||touching||!visible||document.hidden)return;'
                . 'var w=cardWidth();'
                . 'var atEnd=track.scrollLeft+track.clientWidth>=track.scrollWidth-4;'
                . 'if(atEnd){track.scrollTo({left:0,behavior:"smooth"})}'
                . 'else{track.scrollBy({left:w,behavior:"smooth"})}'
              . '}'
              . 'var timer=setInterval(tick,AUTOPLAY_MS);'
              // Pause on hover (desktop)
              . 'slider.addEventListener("mouseenter",function(){hovered=true});'
              . 'slider.addEventListener("mouseleave",function(){hovered=false});'
              // Pause on touch (mobile)
              . 'track.addEventListener("touchstart",function(){touching=true},{passive:true});'
              . 'track.addEventListener("touchend",function(){setTimeout(function(){touching=false},1500)},{passive:true});'
              // Pause when scrolled out of viewport
              . 'if("IntersectionObserver" in window){'
                . 'var io=new IntersectionObserver(function(es){es.forEach(function(en){visible=en.isIntersecting})},{threshold:0.15});'
                . 'io.observe(slider);'
              . '}'
              // Pause when tab is hidden — saves animation cycles
              . 'document.addEventListener("visibilitychange",function(){});'
              // Mouse-drag scroll (desktop swipe). Only kicks in on
              // primary button + after a small movement threshold so
              // single clicks on cards still propagate.
              . 'var dragStart=null,dragScrollLeft=0,didDrag=false;'
              . 'track.addEventListener("mousedown",function(e){'
                . 'if(e.button!==0)return;'
                . 'dragStart=e.pageX;dragScrollLeft=track.scrollLeft;didDrag=false;'
              . '});'
              . 'window.addEventListener("mousemove",function(e){'
                . 'if(dragStart===null)return;'
                . 'var dx=e.pageX-dragStart;'
                . 'if(Math.abs(dx)>4){'
                  . 'if(!didDrag){track.classList.add("is-dragging");didDrag=true}'
                  . 'track.scrollLeft=dragScrollLeft-dx;'
                . '}'
              . '});'
              . 'window.addEventListener("mouseup",function(){'
                . 'if(dragStart!==null&&didDrag){track.classList.remove("is-dragging");}'
                . 'dragStart=null;'
              . '});'
              // Suppress click bubbling immediately after a drag so the
              // lightbox does NOT open when the user just released a
              // drag gesture on top of an image.
              . 'track.addEventListener("click",function(e){if(didDrag){e.preventDefault();e.stopPropagation();didDrag=false}},true);'
              // Update edge-fade visibility as user scrolls
              . 'track.addEventListener("scroll",updateEnd,{passive:true});'
              . 'updateEnd();'
            . '}'
            . 'function init(){var sliders=document.querySelectorAll("[data-rg-attr-slider]");for(var i=0;i<sliders.length;i++)wire(sliders[i])}'
            . 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}'
            . '})();</script>';

        $out .= '</section>';
        return $out;
    }

    /**
     * "How to get to X" transport-method grid. Each card has a title,
     * detail, and one of a fixed set of icons (jeepney / bus / car etc.).
     */
    private function howToGetTo(array $p): string
    {
        $methods = $p['methods'] ?? [];
        if (!$methods) return '';
        $heading = $p['heading'] ?? 'How to get there';
        $intro = $p['intro'] ?? '';
        $footer = trim((string) ($p['footer'] ?? ''));

        // Per-method icon color palette. Matches the gradient swatch shown
        // on the destination "How to get to La Union" card so the food /
        // restaurant pages render with the same visual rhythm.
        $iconPalettes = [
            'amber'   => ['from' => '#fef3c7', 'to' => '#fffbeb'],
            'blue'    => ['from' => '#dbeafe', 'to' => '#eff6ff'],
            'emerald' => ['from' => '#d1fae5', 'to' => '#ecfdf5'],
            'rose'    => ['from' => '#ffe4e6', 'to' => '#fff1f2'],
            'violet'  => ['from' => '#ede9fe', 'to' => '#f5f3ff'],
            'slate'   => ['from' => '#e2e8f0', 'to' => '#f8fafc'],
        ];
        // Default colour rotation when admin didn't pick one explicitly so
        // each row looks distinct without needing a palette decision.
        $cycle = ['amber', 'blue', 'emerald', 'rose', 'violet', 'slate'];

        $out = '<section class="not-prose mt-6 mb-8 rounded-xl border border-slate-200 bg-white overflow-hidden">';

        // Card header — H2 + intro paragraph, capped at max-w-3xl so the
        // copy reads like a magazine lede instead of stretching edge-to-edge.
        $out .= '<div class="p-5 sm:p-6 pb-3 border-b border-slate-100">';
        $out .= '<h2 class="text-xl font-bold text-slate-900 mb-3">' . $this->e($heading) . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-sm text-slate-700 leading-relaxed max-w-3xl">' . $this->e($intro) . '</p>';
        }
        $out .= '</div>';

        // Method rows — collapsed by default using <details>. Click the
        // summary row to expand the operator list + detail paragraph. The
        // summary always shows: icon square + uppercase title + bold
        // subtitle + chevron. Body shows: operator list (when present) +
        // detail paragraph. Default-open the first method so the panel
        // reads "active" on first paint.
        $out .= '<div class="divide-y divide-slate-100">';
        $i = 0;
        foreach ($methods as $m) {
            if (empty($m['title'])) continue;
            $color = $m['color'] ?? $cycle[$i % count($cycle)];
            $pal = $iconPalettes[$color] ?? $iconPalettes['amber'];
            $icon = $m['icon'] ?? 'car';
            $emoji = $this->transportEmoji($icon);
            $title = $this->e($m['title']);
            $subtitle = $this->e(trim((string) ($m['subtitle'] ?? '')));
            $detail = trim((string) ($m['detail'] ?? ''));
            $operators = is_array($m['operators'] ?? null) ? array_values(array_filter(
                $m['operators'],
                fn($op) => !empty($op['name'])
            )) : [];

            // Subtitle defaults to operator count when not given. Falls
            // back to a plain "Recommended route" line if no operators
            // either — keeps the bolded second line from looking empty.
            if ($subtitle === '') {
                if ($operators) {
                    $count = count($operators);
                    $subtitle = $count === 1 ? '1 option worth booking' : ($count . ' options worth booking');
                } else {
                    $subtitle = 'Recommended route';
                }
            }

            $bodyHtml = '';
            if ($operators) {
                $bodyHtml .= '<ul class="divide-y divide-slate-100 border border-slate-200 rounded-lg overflow-hidden">';
                foreach ($operators as $op) {
                    $opName = $this->e($op['name']);
                    $opNote = $this->e($op['note'] ?? '');
                    $opUrl = trim((string) ($op['url'] ?? ''));
                    $imgPath = trim((string) ($op['image'] ?? ''));
                    $thumb = $imgPath !== ''
                        ? '<div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 shrink-0">'
                            . '<img src="' . $this->e($this->normalizeMediaUrl($imgPath)) . '" alt="' . $opName . '" class="w-full h-full object-cover" loading="lazy">'
                            . '</div>'
                        : '<div class="w-16 h-16 rounded-lg flex items-center justify-center text-2xl shrink-0"'
                            . ' style="background:linear-gradient(135deg,' . $pal['from'] . ' 0%,' . $pal['to'] . ' 100%)">'
                            . $emoji . '</div>';
                    $arrow = '<svg class="inline-block w-3 h-3 opacity-50 ml-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5h5v5m0-5L10 14"/></svg>';

                    $inner = $thumb
                        . '<div class="flex-1 min-w-0">'
                        . '<div class="font-semibold text-slate-900' . ($opUrl !== '' ? ' hover:underline' : '') . '">'
                        . $opName . ($opUrl !== '' ? $arrow : '') . '</div>'
                        . ($opNote !== '' ? '<p class="text-sm text-slate-600 leading-snug mt-1 m-0">' . $opNote . '</p>' : '')
                        . '</div>';

                    if ($opUrl !== '') {
                        $bodyHtml .= '<li class="bg-white"><a href="' . $this->e($opUrl) . '" target="_blank" rel="noopener nofollow" class="flex items-start gap-4 p-4 hover:bg-slate-50 transition">' . $inner . '</a></li>';
                    } else {
                        $bodyHtml .= '<li class="bg-white"><div class="flex items-start gap-4 p-4">' . $inner . '</div></li>';
                    }
                }
                $bodyHtml .= '</ul>';
                if ($detail !== '') {
                    $bodyHtml .= '<p class="text-sm text-slate-600 leading-relaxed mt-3 m-0">' . $this->e($detail) . '</p>';
                }
            } else if ($detail !== '') {
                $bodyHtml = '<p class="text-sm text-slate-700 leading-relaxed m-0">' . $this->e($detail) . '</p>';
            }

            // <details> handles the expand/collapse natively with no JS
            // dependency. summary::-webkit-details-marker hidden in the
            // public layout. Chevron rotates via group-open: utility so it
            // gives a visual affordance for the expandable state. All
            // methods start collapsed so the section reads as a compact
            // menu on first paint.
            $out .= '<details class="group p-5 sm:p-6">';
            $out .= '<summary class="rg-htgt-summary cursor-pointer list-none flex items-center gap-3 select-none">'
                . '<div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0"'
                . ' style="background:linear-gradient(135deg,' . $pal['from'] . ' 0%,' . $pal['to'] . ' 100%)">'
                . $emoji . '</div>'
                . '<div class="flex-1 min-w-0">'
                . '<div class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">' . $title . '</div>'
                . '<div class="font-bold text-slate-900 truncate">' . $subtitle . '</div>'
                . '</div>'
                . '<svg class="w-5 h-5 text-slate-400 transition-transform group-open:rotate-180 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>'
                . '</summary>';
            if ($bodyHtml !== '') {
                $out .= '<div class="mt-4">' . $bodyHtml . '</div>';
            }
            $out .= '</details>';
            $i++;
        }
        $out .= '</div>';

        // Inline stylesheet for the <details> summary — Tailwind doesn't
        // have a built-in for hiding the WebKit/Firefox default disclosure
        // triangle, so we knock it out here. Scoped via the wrapper class
        // so it doesn't leak into other <details> on the page.
        $out .= '<style>'
            . '.rg-htgt-summary::-webkit-details-marker{display:none}'
            . '.rg-htgt-summary::marker{display:none;content:""}'
            . '</style>';

        // Closing footer block — slate-tinted band with a closing prose
        // paragraph. Renders only when admin authored a footer.
        if ($footer !== '') {
            $out .= '<div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-100">'
                . '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $this->e($footer) . '</p>'
                . '</div>';
        }

        $out .= '</section>';
        return $out;
    }

    /**
     * Emoji glyph for the gradient icon square in howToGetTo. Mirrors
     * transportIconSvg's keys so admins can keep using the same `icon`
     * field while the renderer picks the right pictograph.
     */
    private function transportEmoji(string $icon): string
    {
        return match ($icon) {
            'bus'      => '🚌',
            'plane'    => '✈️',
            'car'      => '🚗',
            'jeepney'  => '🚐',
            'tricycle' => '🛺',
            'ferry'    => '⛴️',
            'train'    => '🚆',
            'walk'     => '🚶',
            'bike'     => '🚲',
            default    => '🧭',
        };
    }

    /**
     * Heading + ordered list of paragraphs. Authored via the structured
     * builder (heading text + add/remove paragraph cards), rendered as
     * <h2>/<h3> + chained <p> elements. Paragraph bodies are HTML so the
     * admin can wrap selections in <strong> / <em> / <a> if needed, but
     * the editor itself stays list-based (no Quill).
     */
    private function textSection(array $p): string
    {
        $heading = trim($p['heading'] ?? '');
        $rawLevel = $p['heading_level'] ?? 'h2';
        $level = in_array($rawLevel, ['h2', 'h3', 'h4'], true) ? $rawLevel : 'h2';
        $anchor = trim($p['anchor'] ?? '');

        // Body field (new canonical shape) takes precedence: a single
        // multi-line text field where blank lines separate paragraphs.
        // Legacy shape — paragraphs[] array — still resolves correctly so
        // pre-migration blocks keep rendering until the seeder catches up.
        $paragraphs = [];
        $body = trim((string) ($p['body'] ?? ''));
        if ($body !== '') {
            $chunks = preg_split('~\n\s*\n+~', $body) ?: [];
            foreach ($chunks as $chunk) {
                $chunk = trim($chunk);
                if ($chunk !== '') $paragraphs[] = $chunk;
            }
        } else {
            $paragraphs = array_values(array_filter(
                (array) ($p['paragraphs'] ?? []),
                fn ($x) => trim((string) $x) !== ''
            ));
        }

        if ($heading === '' && !$paragraphs) return '';

        $sizes = [
            'h2' => 'text-3xl md:text-4xl mt-8 mb-3',
            'h3' => 'text-2xl md:text-3xl mt-6 mb-2',
            'h4' => 'text-xl md:text-2xl mt-5 mb-2',
        ];
        $sizeClass = $sizes[$level] ?? $sizes['h2'];

        $headingHtml = '';
        if ($heading !== '') {
            $idAttr = $anchor !== ''
                ? ' id="' . $this->e(preg_replace('~[^a-z0-9-]+~i', '-', $anchor)) . '"'
                : '';
            $headingHtml = "<{$level}{$idAttr} class=\"{$sizeClass} font-bold text-slate-900\">"
                . $this->e($heading) . "</{$level}>";
        }

        $bodyHtml = '';
        foreach ($paragraphs as $para) {
            $inner = trim((string) $para);
            if ($inner === '') continue;
            // Items that are already block-level (figure/blockquote/list)
            // render raw. Plain text gets a fresh <p> wrapper. The outer <p>
            // is stripped if the admin pre-wrapped to avoid double-nest.
            if (preg_match('~^<(figure|blockquote|ul|ol|table|div)\b~i', $inner)) {
                $bodyHtml .= $inner;
            } else {
                if (preg_match('~^<p[^>]*>(.*?)</p>$~is', $inner, $m)) {
                    $inner = $m[1];
                }
                $bodyHtml .= '<p>' . $inner . '</p>';
            }
        }

        // Heading sits OUTSIDE the prose wrapper so its custom sizing
        // (text-3xl / text-2xl etc.) doesn't get overridden by Tailwind
        // Typography. The body sits INSIDE so paragraphs, lists, blockquotes,
        // and inline links inherit prose's auto-styling — same behaviour
        // the legacy rich_text block had.
        return '<section class="my-6">' . $headingHtml
            . '<div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">' . $bodyHtml . '</div>'
            . '</section>';
    }

    private function transportIconSvg(string $icon): string
    {
        $base = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">';
        return $base . match ($icon) {
            'bus'      => '<rect x="4" y="4" width="16" height="13" rx="2"/><circle cx="8" cy="19" r="1.5"/><circle cx="16" cy="19" r="1.5"/><path d="M4 11h16M8 8h8"/>',
            'plane'    => '<path d="M21 12l-9 5-9-5 9-9z"/><path d="M12 7v10"/>',
            'car'      => '<path d="M3 17h2m14 0h2M5 17V9l1.5-3.5h11L19 9v8M5 17h14M7 13h2m6 0h2"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>',
            'jeepney'  => '<path d="M3 16h18M4 16V9l3-3h10l3 3v7"/><circle cx="7" cy="18" r="1.5"/><circle cx="17" cy="18" r="1.5"/><path d="M7 12h10"/>',
            'tricycle' => '<circle cx="6" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M6 17l3-9h6l2 9M9 8l-1-3"/>',
            'train'    => '<rect x="5" y="3" width="14" height="14" rx="2"/><path d="M5 11h14M8 7h8"/><circle cx="9" cy="14" r="1"/><circle cx="15" cy="14" r="1"/><path d="M7 21l3-4M14 17l3 4"/>',
            'boat'     => '<path d="M3 14l9-9 9 9-3 6H6z"/><path d="M3 18c2 1 4 1 6 0s4-1 6 0 4 1 6 0"/>',
            default    => '<path d="M3 17h2m14 0h2M5 17V9l1.5-3.5h11L19 9v8"/>',
        } . '</svg>';
    }

    // ════════════════════════════════════════════════════════════════
    // Standardized section templates — each one renders a layout that
    // previously lived as raw custom_html on seeded pages. Now any
    // admin can author the same pattern through a structured editor.
    // ════════════════════════════════════════════════════════════════

    /**
     * "The short version" — dark gradient summary card with eyebrow
     * + one-line statement. Matches the food-page seeder's TLDR card.
     */
    private function shortVersion(array $p): string
    {
        $eyebrow = $this->e($p['eyebrow'] ?? 'The short version');
        $body = trim($p['body'] ?? '');
        if ($body === '') return '';
        $accent = $p['accent_color'] ?? 'amber';
        // Body colour is now pure white instead of slate-100 — earlier
        // contrast against the dark gradient was just under WCAG 4.5:1
        // and read as "washed-out" on smaller screens. Eyebrow keeps
        // its accent-coloured tint so the hierarchy still reads.
        $palettes = [
            'amber'  => ['bg' => 'linear-gradient(135deg,#0f172a 0%,#1e293b 100%)', 'fg' => '#ffffff', 'eyebrow' => '#fbbf24'],
            'emerald'=> ['bg' => 'linear-gradient(135deg,#064e3b 0%,#065f46 100%)', 'fg' => '#ffffff', 'eyebrow' => '#6ee7b7'],
            'rose'   => ['bg' => 'linear-gradient(135deg,#4c0519 0%,#881337 100%)', 'fg' => '#ffffff', 'eyebrow' => '#fda4af'],
            'blue'   => ['bg' => 'linear-gradient(135deg,#1e3a8a 0%,#1e40af 100%)', 'fg' => '#ffffff', 'eyebrow' => '#93c5fd'],
        ];
        $pal = $palettes[$accent] ?? $palettes['amber'];
        // Body text bumped from text-base (16px) to text-lg (18px) with
        // wider leading. m-0 keeps it flush against the eyebrow row.
        return '<div class="not-prose my-8 p-6 sm:p-7 rounded-2xl" style="background:' . $pal['bg'] . ';color:' . $pal['fg'] . '">'
            . '<div class="text-[11px] uppercase tracking-[0.2em] font-bold mb-3" style="color:' . $pal['eyebrow'] . '">' . $eyebrow . '</div>'
            . '<p class="text-lg sm:text-xl leading-relaxed font-medium m-0" style="color:' . $pal['fg'] . '">' . $this->e($body) . '</p>'
            . '</div>';
    }

    /**
     * "Best for / Skip if" two-column comparison list. Renders the
     * familiar emerald + rose chevron-bullet pattern from the seeders.
     */
    private function prosCons(array $p): string
    {
        $prosLabel = $this->e($p['pros_label'] ?? 'Best for');
        $consLabel = $this->e($p['cons_label'] ?? 'Skip if');
        $pros = array_values(array_filter((array) ($p['pros'] ?? []), fn ($x) => trim((string) $x) !== ''));
        $cons = array_values(array_filter((array) ($p['cons'] ?? []), fn ($x) => trim((string) $x) !== ''));
        if (!$pros && !$cons) return '';

        // SVG icons for the check / x marks. Inlined so the cards render
        // without any external asset dependency.
        $checkSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-none mt-0.5"><polyline points="20 6 9 17 4 12"/></svg>';
        $xSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-none mt-0.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

        $renderColumn = function (
            string $label,
            array $items,
            string $accent,
            string $accentDark,
            string $iconBg,
            string $headerIcon,
            string $rowIcon
        ): string {
            $lis = '';
            foreach ($items as $item) {
                $lis .= '<li class="flex items-start gap-3 text-sm text-slate-700 leading-snug">'
                    . '<span class="flex-none" style="color:' . $accent . '">' . $rowIcon . '</span>'
                    . '<span class="flex-1">' . $this->e($item) . '</span></li>';
            }
            return '<div class="relative rounded-2xl border border-slate-200 bg-white overflow-hidden"'
                . ' style="border-top:4px solid ' . $accent . '">'
                . '<div class="flex items-center gap-3 p-5 pb-3">'
                . '<div class="flex-none w-10 h-10 rounded-full flex items-center justify-center"'
                . ' style="background:' . $iconBg . ';color:' . $accentDark . '">' . $headerIcon . '</div>'
                . '<div class="text-base font-bold uppercase tracking-wide" style="color:' . $accentDark . '">'
                . $label . '</div></div>'
                . '<ul class="space-y-2.5 m-0 p-5 pt-2 list-none">' . $lis . '</ul></div>';
        };

        $headerCheck = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><polyline points="20 6 9 17 4 12"/></svg>';
        $headerX = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

        $left = $renderColumn($prosLabel, $pros, '#10b981', '#047857', '#d1fae5', $headerCheck, $checkSvg);
        $right = $renderColumn($consLabel, $cons, '#f43f5e', '#be123c', '#ffe4e6', $headerX, $xSvg);

        return '<div class="not-prose my-10 grid grid-cols-1 md:grid-cols-2 gap-4">' . $left . $right . '</div>';
    }

    /**
     * Expandable summary accordion ("At a glance" + "The short version"
     * pattern from the resort pages). Native <details>/<summary> so it
     * works without JS.
     */
    private function summaryAccordion(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $out = '<div class="not-prose my-8 space-y-2">';
        foreach ($items as $i => $item) {
            if (empty($item['title']) && empty($item['body'])) continue;
            $eyebrow = $this->e($item['eyebrow'] ?? '');
            $title = $this->e($item['title'] ?? '');
            $body = $item['body'] ?? '';
            $open = !empty($item['open']) || $i === 0 ? ' open' : '';
            $out .= '<details' . $open . ' class="bg-white border border-slate-200 rounded-xl p-5 group">'
                . '<summary class="flex items-center justify-between cursor-pointer list-none">'
                . '<div>'
                . ($eyebrow !== '' ? '<div class="text-[10px] uppercase tracking-wide font-bold text-amber-700 mb-1">' . $eyebrow . '</div>' : '')
                . '<div class="text-lg md:text-xl font-bold text-slate-900">' . $title . '</div>'
                . '</div>'
                . '<span class="text-slate-400 group-open:rotate-180 transition text-xl" aria-hidden="true">▾</span>'
                . '</summary>'
                . '<div class="mt-4 text-slate-700 leading-relaxed">' . $body . '</div>'
                . '</details>';
        }
        $out .= '</div>';
        return $out;
    }

    /**
     * Left/right image + text pair (tourist spot rows). Toggleable image
     * position (left|right) so a series of these alternates cleanly.
     *
     * Redesign: editorial-card look replaces the bare 2-column grid.
     * - Outer card with white bg, soft shadow, rounded-2xl border,
     *   overflow-hidden so the image bleeds clean to the edge.
     * - Image takes a full column with a slight zoom on hover.
     * - Text column has a left-accent strip in emerald, a chip-style
     *   eyebrow with badge background (instead of tiny uppercase
     *   text), bigger serif title, and a real button CTA with arrow.
     * - Whole card lifts on hover.
     * - On mobile (<md): image stacks on top of text; card framing
     *   stays.
     */
    private function imageTextPair(array $p): string
    {
        if (empty($p['image']) && empty($p['title']) && empty($p['body'])) return '';
        $position = ($p['image_position'] ?? 'left') === 'right' ? 'right' : 'left';
        $eyebrow = $this->e($p['eyebrow'] ?? '');
        $title = $this->e($p['title'] ?? '');
        $body = $p['body'] ?? '';
        $url = $p['url'] ?? '';
        $urlLabel = $this->e($p['url_label'] ?? 'Learn more');

        $img = !empty($p['image'])
            ? '<div class="rg-itp-imgwrap relative bg-slate-100 overflow-hidden md:aspect-auto aspect-[16/10]">'
                . '<img src="' . $this->e($p['image']) . '" alt="' . $title . '"'
                . ' class="rg-itp-img absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out"'
                . ' loading="lazy">'
                // subtle gradient overlay so any caption on top stays readable
                . '<div class="absolute inset-0 pointer-events-none bg-gradient-to-tr from-black/20 via-transparent to-transparent"></div>'
                . '</div>'
            : '<div class="bg-emerald-50/40 md:aspect-auto aspect-[16/10] flex items-center justify-center">'
                . '<svg class="w-12 h-12 text-emerald-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>'
                . '</div>';

        $cta = $url !== ''
            ? '<a href="' . $this->e($url) . '" rel="noopener nofollow" target="_blank"'
                . ' class="rg-itp-cta inline-flex items-center gap-2 mt-5 px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors">'
                . $urlLabel
                . '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>'
                . '</a>'
            : '';

        // Text column: vertical accent strip on the left edge,
        // chip-style eyebrow badge, serif-ish bold title, body
        // copy in slate-700, real button CTA.
        $eyebrowChip = $eyebrow !== ''
            ? '<div class="inline-flex items-center gap-1.5 mb-3 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold uppercase tracking-[0.08em]">'
                . '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>' . $eyebrow . '</div>'
            : '';

        $textInner = $eyebrowChip
            . ($title !== '' ? '<h3 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight mb-3 tracking-tight">' . $title . '</h3>' : '')
            . ($body !== '' ? '<div class="text-slate-600 leading-relaxed text-[0.95rem] md:text-base">' . $body . '</div>' : '')
            . $cta;

        // The text column carries its own padding; on the image-side
        // of the accent strip we draw a thin emerald bar via
        // `border-l-4 border-emerald-500` which becomes the editorial
        // accent for the card.
        $textCol = '<div class="rg-itp-text relative p-7 md:p-9 flex flex-col justify-center'
            . ($position === 'left' ? ' md:border-l-4 border-emerald-500' : ' md:border-r-4 border-emerald-500')
            . '">'
            . $textInner
            . '</div>';

        // Image column wrapper makes sure the image fills its grid
        // cell on md+ where the card uses a 2-col equal-height grid.
        $imgCol = '<div class="rg-itp-imgcol relative overflow-hidden">' . $img . '</div>';

        $cols = $position === 'right' ? $textCol . $imgCol : $imgCol . $textCol;

        return '<article class="rg-itp not-prose my-10 grid md:grid-cols-2 rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm hover:shadow-xl transition-shadow duration-300 hover:-translate-y-0.5 transform-gpu">'
            . $cols
            . '</article>'
            // Per-block hover effects (image zoom on card hover). Scoped
            // to .rg-itp so they don't bleed into other blocks.
            // Per-block styles + the slider mode CSS. The slider-mode
            // rules ONLY apply when the wrapping container .rg-itp-slider
            // exists — the JS at the bottom adds that wrapper at
            // runtime whenever it finds 2+ adjacent .rg-itp siblings.
            // Single-block pages stay in their normal editorial-card
            // layout, untouched.
            . '<style>'
                . '.rg-itp:hover .rg-itp-img{transform:scale(1.04)}'
                . '.rg-itp{transition:box-shadow .3s ease, transform .3s ease}'
                // ----- Slider mode -----
                . '.rg-itp-slider{position:relative}'
                . '.rg-itp-track{display:flex;gap:1rem;overflow-x:auto;scroll-behavior:smooth;scroll-snap-type:x mandatory;padding-bottom:.5rem;scrollbar-width:none;-ms-overflow-style:none;cursor:grab}'
                . '.rg-itp-track::-webkit-scrollbar{display:none}'
                . '.rg-itp-track.is-dragging{cursor:grabbing;scroll-behavior:auto;user-select:none}'
                . '.rg-itp-track.is-dragging img{pointer-events:none}'
                . '.rg-itp-track .rg-itp{flex:0 0 100%;width:100%;margin-top:0;margin-bottom:0;scroll-snap-align:start;scroll-snap-stop:always}'
                . '@media(min-width:768px){.rg-itp-track .rg-itp{flex:0 0 calc(100% - 2rem)}}'
                . '.rg-itp-fade-edge{position:absolute;top:0;right:0;bottom:.5rem;width:3rem;background:linear-gradient(to left,#fff,transparent);pointer-events:none;transition:opacity .25s ease}'
                . '.rg-itp-slider[data-rg-end] .rg-itp-fade-edge{opacity:0}'
            . '</style>'
            // ----- Slider auto-wiring JS (idempotent) -----
            // Runs once per page. Finds every .rg-itp element. Walks
            // groups of consecutive siblings (i.e. same parent, no
            // non-.rg-itp elements between them). For each group of 2
            // or more, wraps them in a slider container, then wires
            // autoplay (5s), mouse-drag scroll, touch pause, hover
            // pause, IntersectionObserver pause, and end-of-track
            // edge-fade toggle. The pattern matches the attractions
            // slider so they feel like the same control to the user.
            . '<script>(function(){'
                . 'if(window.__rgItpSliderWired)return;window.__rgItpSliderWired=true;'
                . 'var AUTOPLAY_MS=5000;'
                . 'function groupAdjacent(){'
                  . 'var nodes=document.querySelectorAll(".rg-itp");if(!nodes.length)return [];'
                  . 'var groups=[],current=null;'
                  . 'nodes.forEach(function(n){'
                    . 'if(current&&current[current.length-1].nextElementSibling===n){current.push(n)}'
                    . 'else{current=[n];groups.push(current)}'
                  . '});'
                  . 'return groups.filter(function(g){return g.length>=2})'
                . '}'
                . 'function wireOne(slider){'
                  . 'var track=slider.querySelector(".rg-itp-track");if(!track)return;'
                  . 'var paused=false,hovered=false,touching=false,visible=true,dragStart=null,dragScroll=0,didDrag=false;'
                  . 'function slideWidth(){var c=track.querySelector(".rg-itp");if(!c)return 600;var gap=parseFloat(getComputedStyle(track).gap||"16")||16;return c.offsetWidth+gap}'
                  . 'function updateEnd(){var atEnd=Math.ceil(track.scrollLeft+track.clientWidth)>=track.scrollWidth-2;if(atEnd){slider.setAttribute("data-rg-end","1")}else{slider.removeAttribute("data-rg-end")}}'
                  . 'function tick(){'
                    . 'if(paused||hovered||touching||!visible||document.hidden)return;'
                    . 'var w=slideWidth();var atEnd=track.scrollLeft+track.clientWidth>=track.scrollWidth-4;'
                    . 'if(atEnd){track.scrollTo({left:0,behavior:"smooth"})}else{track.scrollBy({left:w,behavior:"smooth"})}'
                  . '}'
                  . 'setInterval(tick,AUTOPLAY_MS);'
                  . 'slider.addEventListener("mouseenter",function(){hovered=true});'
                  . 'slider.addEventListener("mouseleave",function(){hovered=false});'
                  . 'track.addEventListener("touchstart",function(){touching=true},{passive:true});'
                  . 'track.addEventListener("touchend",function(){setTimeout(function(){touching=false},1500)},{passive:true});'
                  . 'if("IntersectionObserver" in window){'
                    . 'var io=new IntersectionObserver(function(es){es.forEach(function(en){visible=en.isIntersecting})},{threshold:0.15});'
                    . 'io.observe(slider);'
                  . '}'
                  . 'track.addEventListener("mousedown",function(e){if(e.button!==0)return;dragStart=e.pageX;dragScroll=track.scrollLeft;didDrag=false});'
                  . 'window.addEventListener("mousemove",function(e){if(dragStart===null)return;var dx=e.pageX-dragStart;if(Math.abs(dx)>4){if(!didDrag){track.classList.add("is-dragging");didDrag=true}track.scrollLeft=dragScroll-dx}});'
                  . 'window.addEventListener("mouseup",function(){if(dragStart!==null&&didDrag){track.classList.remove("is-dragging")}dragStart=null});'
                  . 'track.addEventListener("click",function(e){if(didDrag){e.preventDefault();e.stopPropagation();didDrag=false}},true);'
                  . 'track.addEventListener("scroll",updateEnd,{passive:true});'
                  . 'updateEnd();'
                . '}'
                . 'function init(){'
                  . 'var groups=groupAdjacent();'
                  . 'groups.forEach(function(g){'
                    . 'var parent=g[0].parentElement;'
                    . 'var slider=document.createElement("section");'
                    . 'slider.className="rg-itp-slider not-prose my-10";'
                    . 'slider.setAttribute("data-rg-itp-slider","");'
                    . 'slider.setAttribute("aria-label","Tourist spots carousel");'
                    . 'slider.setAttribute("role","region");'
                    . 'var track=document.createElement("div");'
                    . 'track.className="rg-itp-track";'
                    . 'track.tabIndex=0;'
                    . 'parent.insertBefore(slider,g[0]);'
                    . 'slider.appendChild(track);'
                    . 'g.forEach(function(n){track.appendChild(n)});'
                    . 'var edge=document.createElement("div");'
                    . 'edge.className="rg-itp-fade-edge";'
                    . 'slider.appendChild(edge);'
                    . 'wireOne(slider);'
                  . '});'
                . '}'
                . 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}'
            . '})();</script>';
    }

    /**
     * "What travelers are saying" — static testimonial grid. Each item:
     * name + avatar + rating + quote + optional date.
     */
    private function travelerReviews(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'What travelers are saying');
        $intro = $this->e($p['intro'] ?? '');

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        if ($intro !== '') $out .= '<p class="text-slate-600 mb-6">' . $intro . '</p>';
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            if (empty($item['quote'])) continue;
            $name = $this->e($item['name'] ?? 'Anonymous traveler');
            $date = $this->e($item['date'] ?? '');
            $rating = max(0, min(5, (int) ($item['rating'] ?? 5)));
            $avatar = !empty($item['avatar'])
                ? '<img src="' . $this->e($item['avatar']) . '" alt="' . $name . '" class="w-10 h-10 rounded-full object-cover" loading="lazy">'
                : '<div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">' . $this->e(mb_substr($name, 0, 1)) . '</div>';
            $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
            $out .= '<div class="p-5 rounded-xl border border-slate-200 bg-white">'
                . '<div class="flex items-center gap-3 mb-3">' . $avatar
                . '<div><div class="font-bold text-slate-900 text-sm">' . $name . '</div>'
                . ($date !== '' ? '<div class="text-xs text-slate-500">' . $date . '</div>' : '')
                . '</div></div>'
                . '<div class="text-amber-500 mb-2 text-sm" aria-label="' . $rating . ' of 5 stars">' . $stars . '</div>'
                . '<p class="text-sm text-slate-700 leading-relaxed m-0">"' . $this->e($item['quote']) . '"</p>'
                . '</div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Google Maps embed (or any iframe URL — the field accepts the raw
     * embed URL admins copy out of Google's Share -> Embed dialog).
     */
    private function mapEmbed(array $p): string
    {
        $url = trim($p['embed_url'] ?? '');
        if ($url === '') return '';
        $heading = $this->e($p['heading'] ?? '');
        $height = max(200, min(800, (int) ($p['height'] ?? 450)));

        // Defensive: only allow embed URLs from Google Maps + OpenStreetMap to
        // avoid arbitrary iframe injection from a paste. Google supports two
        // embed shapes — /maps/embed?... (iframe-only host) and the plain
        // /maps?q=...&output=embed query — both are valid for <iframe src>.
        if (!preg_match('~^https://(?:www\.)?(?:google\.[a-z.]+/maps|maps\.google\.[a-z.]+|www\.openstreetmap\.org)~i', $url)) {
            return '<div class="not-prose my-8 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900">'
                . 'Map embed URL must come from Google Maps or OpenStreetMap.</div>';
        }

        $out = '<section class="not-prose my-8">';
        if ($heading !== '') {
            $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-3">' . $heading . '</h2>';
        }
        $out .= '<div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100">'
            . '<iframe src="' . $this->e($url) . '" width="100%" height="' . $height . '" style="border:0;display:block" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>'
            . '</div>';
        $out .= '</section>';
        return $out;
    }

    /**
     * Local-tip / pull-quote callout. Color picker chooses between two
     * visual styles:
     *   - dark: dark gradient with opening &ldquo; quote, italic body,
     *     amber footer eyebrow. Matches the seeded "Local tip from a
     *     Pasay regular" pull-quote pattern.
     *   - amber/emerald/blue/rose: light-bg aside with emoji icon,
     *     coloured eyebrow above plain body text.
     */
    private function localTip(array $p): string
    {
        $body = trim($p['body'] ?? '');
        if ($body === '') return '';
        $eyebrow = $this->e($p['eyebrow'] ?? 'Local tip');
        $color = $p['color'] ?? 'amber';

        if ($color === 'dark') {
            return '<div class="not-prose my-10 px-6 py-6 rounded-xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#f1f5f9">'
                . '<div class="text-4xl leading-none mb-2" style="color:#fbbf24">&ldquo;</div>'
                . '<p class="text-lg md:text-xl italic leading-relaxed m-0">' . $this->e($body) . '</p>'
                . '<p class="text-xs uppercase tracking-wide mt-3 m-0" style="color:#fbbf24">' . $eyebrow . '</p>'
                . '</div>';
        }

        $palettes = [
            'amber'   => ['bg' => '#fef3c7', 'br' => '#f59e0b', 'eb' => '#92400e', 'ic' => '#b45309', 'icbg' => '#fde68a', 'emoji' => '💡'],
            'emerald' => ['bg' => '#ecfdf5', 'br' => '#10b981', 'eb' => '#065f46', 'ic' => '#047857', 'icbg' => '#a7f3d0', 'emoji' => '🌿'],
            'blue'    => ['bg' => '#eff6ff', 'br' => '#3b82f6', 'eb' => '#1e3a8a', 'ic' => '#1d4ed8', 'icbg' => '#bfdbfe', 'emoji' => '🧭'],
            'rose'    => ['bg' => '#fff1f2', 'br' => '#f43f5e', 'eb' => '#881337', 'ic' => '#be123c', 'icbg' => '#fecdd3', 'emoji' => '⚠️'],
        ];
        $pal = $palettes[$color] ?? $palettes['amber'];
        return '<aside class="not-prose my-8 p-5 rounded-2xl border-l-4" style="background:' . $pal['bg'] . ';border-color:' . $pal['br'] . '">'
            . '<div class="flex items-start gap-3">'
            . '<div class="flex-none w-10 h-10 rounded-full flex items-center justify-center text-xl" style="background:' . $pal['icbg'] . ';color:' . $pal['ic'] . '">' . $pal['emoji'] . '</div>'
            . '<div class="min-w-0">'
            . '<div class="text-[10px] uppercase tracking-wide font-bold mb-1" style="color:' . $pal['eb'] . '">' . $eyebrow . '</div>'
            . '<p class="text-slate-700 leading-relaxed m-0">' . $this->e($body) . '</p>'
            . '</div></div></aside>';
    }

    /**
     * Pill-row of cuisine / category tags. Each pill has its own
     * background + text color chosen from a small palette so the row
     * stays visually varied. Matches the seeded
     * "Japanese · ramen · sushi" rows.
     */
    private function tagPills(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $label = $p['label'] ?? '';
        // Each pill is now rendered as a hashtag chip — text gets PascalCased
        // (e.g. "Filipino chains" → "FilipinoChains"), prefixed with "#", and
        // wrapped in an outbound link to Facebook's hashtag search so visitors
        // can follow the trend on social. Twitter/X is offered as a secondary
        // mini link on hover for those who prefer that platform.

        $palettes = [
            'amber'   => ['bg' => '#fef3c7', 'fg' => '#78350f', 'hi' => '#b45309'],
            'rose'    => ['bg' => '#fee2e2', 'fg' => '#7f1d1d', 'hi' => '#be123c'],
            'emerald' => ['bg' => '#dcfce7', 'fg' => '#14532d', 'hi' => '#047857'],
            'indigo'  => ['bg' => '#e0e7ff', 'fg' => '#3730a3', 'hi' => '#4338ca'],
            'pink'    => ['bg' => '#fce7f3', 'fg' => '#831843', 'hi' => '#be185d'],
            'cyan'    => ['bg' => '#cffafe', 'fg' => '#155e75', 'hi' => '#0e7490'],
            'violet'  => ['bg' => '#ede9fe', 'fg' => '#5b21b6', 'hi' => '#6d28d9'],
            'slate'   => ['bg' => '#e2e8f0', 'fg' => '#1e293b', 'hi' => '#475569'],
        ];
        $cycle = array_keys($palettes);

        $pillHtml = '';
        foreach ($items as $i => $item) {
            $text = trim(is_array($item) ? ($item['text'] ?? '') : (string) $item);
            if ($text === '') continue;
            $color = is_array($item) ? ($item['color'] ?? '') : '';
            $pal = $palettes[$color] ?? $palettes[$cycle[$i % count($cycle)]];

            $hashtag = $this->hashtagify($text);
            $facebookUrl = 'https://www.facebook.com/hashtag/' . rawurlencode($hashtag);
            $twitterUrl = 'https://twitter.com/search?q=%23' . rawurlencode($hashtag) . '&src=hashtag_click';

            $pillHtml .= '<span class="group inline-flex items-center rounded-full overflow-hidden border transition hover:shadow-sm"'
                . ' style="background:' . $pal['bg'] . ';border-color:' . $pal['fg'] . '20">'
                // Main chip → Facebook hashtag search.
                . '<a href="' . $this->e($facebookUrl) . '" rel="noopener nofollow" target="_blank"'
                . ' class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold transition hover:opacity-80"'
                . ' style="color:' . $pal['fg'] . '"'
                . ' aria-label="Search Facebook for #' . $this->e($hashtag) . '">'
                . '<span class="opacity-60">#</span><span>' . $this->e($hashtag) . '</span>'
                . '</a>'
                // Twitter / X mini-button — small bird icon on the right.
                . '<a href="' . $this->e($twitterUrl) . '" rel="noopener nofollow" target="_blank"'
                . ' class="inline-flex items-center justify-center w-6 h-7 transition hover:opacity-80 border-l"'
                . ' style="color:' . $pal['hi'] . ';border-color:' . $pal['fg'] . '20"'
                . ' aria-label="Search X for #' . $this->e($hashtag) . '">'
                . '<svg viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3"><path d="M18.244 2H21.5l-7.5 8.57L23 22h-6.844l-5.36-7.01L4.7 22H1.44l8.04-9.19L1 2h7.014l4.847 6.41L18.244 2zm-1.198 18.273h1.83L7.045 3.61H5.082l11.964 16.663z"/></svg>'
                . '</a>'
                . '</span>';
        }
        $ariaLabel = $label !== '' ? ' aria-label="' . $this->e($label) . '"' : '';
        $headingHtml = $label !== ''
            ? '<div class="text-[10px] uppercase tracking-[0.18em] font-bold text-slate-500 mb-3">' . $this->e($label) . '</div>'
            : '';
        return '<div class="not-prose my-7"' . $ariaLabel . '>'
            . $headingHtml
            . '<div class="flex flex-wrap gap-2">' . $pillHtml . '</div>'
            . '</div>';
    }

    /**
     * Convert a free-text tag like "Filipino chains" or "fast-casual" into
     * a hashtag-safe PascalCased token: "FilipinoChains", "FastCasual".
     * Strips diacritics and punctuation; collapses runs of non-letters
     * into a single word boundary so the resulting tag is safe for
     * Facebook/Twitter hashtag URLs.
     */
    private function hashtagify(string $text): string
    {
        // Strip accents / fold to ASCII when possible.
        if (function_exists('transliterator_transliterate')) {
            $text = (string) transliterator_transliterate('Any-Latin; Latin-ASCII; [-翿] Remove', $text);
        }
        // Split on anything that's not a unicode letter or digit, drop empties.
        $parts = preg_split('~[^\p{L}\p{N}]+~u', $text) ?: [];
        $out = '';
        foreach ($parts as $part) {
            if ($part === '') continue;
            $out .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8')
                . mb_strtolower(mb_substr($part, 1, null, 'UTF-8'), 'UTF-8');
        }
        return $out !== '' ? $out : 'Tag';
    }

    /**
     * "Compare picks on third-party guides" card grid. Each card has
     * a platform name + accent + outbound URL + optional logo /
     * screenshot uploads. When no upload is provided, a styled gradient
     * placeholder with the platform name fills the preview slot so the
     * card never renders blank.
     */
    private function externalGuides(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'Compare picks on third-party guides');
        $intro = trim((string) ($p['intro'] ?? ''));
        $footnote = $this->e($p['footnote'] ?? 'External links open in a new tab. We do not get paid for clicks.');

        // Sensible fallback intro so the section always reads as more
        // than just a label. Admin can override per page via the intro
        // field; the fallback is generic enough to fit any keyword page.
        if ($intro === '') {
            $intro = 'A quick scan of how this place is covered across the major travel sites. Each one has its own slant — reviews, maps, menus, deals — so the picks rarely overlap. Open one or two before you book to triangulate.';
        }

        // Clean text-only card layout. No brand-color tile or logo
        // square — just the platform name (in its brand colour) + the
        // host caption + a short blurb describing what the link gets you.
        // Reads like a curated bookmark list instead of a logo wall.
        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        $out .= '<p class="text-slate-600 mb-6 leading-relaxed max-w-3xl">' . $this->e($intro) . '</p>';
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">';

        foreach ($items as $item) {
            if (empty($item['name']) || empty($item['url'])) continue;
            $rawUrl = (string) $item['url'];
            $rawName = (string) $item['name'];
            $platform = $this->detectGuidePlatform($rawUrl, $rawName);

            $name = $this->e($rawName);
            $url = $this->e($rawUrl);
            $brandColor = $platform['brandColor'] ?? '#475569';
            $brandColorDark = $this->darkenHex($brandColor, 18);
            $host = $platform['host'] ?? (parse_url($rawUrl, PHP_URL_HOST) ?: '');
            $hostLabel = $this->e(preg_replace('~^www\.~i', '', strtolower($host)));

            // Per-item blurb — admin-provided if present, otherwise the
            // canned platform default so every card has descriptive text.
            $blurbRaw = trim((string) ($item['blurb'] ?? ''));
            if ($blurbRaw === '' && !empty($platform['defaultBlurb'])) {
                $blurbRaw = $platform['defaultBlurb'];
            }
            $blurb = $this->e($blurbRaw);

            $out .= '<a href="' . $url . '" rel="noopener nofollow" target="_blank"'
                . ' class="group block rounded-xl border border-slate-200 bg-white p-5 transition hover:shadow-md hover:-translate-y-px"'
                . ' style="border-left:4px solid ' . $brandColor . '">'
                . '<div class="flex items-start justify-between gap-3 mb-2">'
                . '<div class="min-w-0 flex-1">'
                . '<h3 class="font-bold text-lg leading-tight m-0" style="color:' . $brandColorDark . '">' . $name . '</h3>'
                . ($hostLabel !== '' ? '<div class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mt-1">' . $hostLabel . '</div>' : '')
                . '</div>'
                . '<svg class="flex-none w-5 h-5 text-slate-400 mt-1 group-hover:translate-x-0.5 group-hover:text-slate-700 transition" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</div>'
                . ($blurb !== '' ? '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $blurb . '</p>' : '')
                . '</a>';
        }
        $out .= '</div>';
        if ($footnote !== '') {
            $out .= '<p class="text-xs text-slate-500 mt-3 m-0">' . $footnote . '</p>';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * Author / byline card. Renders at the bottom of an SEO page so the
     * editorial voice is attributed without competing for attention with
     * the listing band at the top. Supports avatar, name, role, bio,
     * outbound social links, and an optional "More from this author"
     * footer link.
     */
    private function author(array $p): string
    {
        $name = trim($p['name'] ?? '');
        $bio = trim($p['bio'] ?? '');
        if ($name === '' && $bio === '') return '';

        $role = $this->e($p['role'] ?? '');
        $avatar = trim($p['avatar'] ?? '');
        $eyebrow = $this->e($p['eyebrow'] ?? 'Written by');
        $links = (array) ($p['links'] ?? []);
        $moreUrl = trim((string) ($p['more_url'] ?? ''));
        $moreLabel = trim((string) ($p['more_label'] ?? ''));

        // Avatar tile — uploaded image, or initials on a slate gradient.
        $avatarHtml = '';
        if ($avatar !== '') {
            $avatarHtml = '<img src="' . $this->e($this->normalizeMediaUrl($avatar)) . '" alt="' . $this->e($name) . '"'
                . ' class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover shadow-md ring-4 ring-white" loading="lazy">';
        } else {
            $initials = '';
            foreach (preg_split('~\s+~', $name, -1, PREG_SPLIT_NO_EMPTY) as $word) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
                if (mb_strlen($initials) >= 2) break;
            }
            if ($initials === '') $initials = 'RG';
            $avatarHtml = '<div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full flex items-center justify-center shadow-md ring-4 ring-white"'
                . ' style="background:linear-gradient(135deg,#0f172a 0%,#475569 100%)">'
                . '<span class="text-2xl font-extrabold text-white">' . $this->e($initials) . '</span>'
                . '</div>';
        }

        // Social link icons — each entry is { type, url } where type maps
        // to a recognised platform (facebook/instagram/twitter/x/tiktok/
        // youtube/linkedin/website/email). Unknown types fall back to a
        // generic globe icon so admin-added rows still render.
        $linksHtml = '';
        foreach ($links as $l) {
            if (empty($l['url'])) continue;
            $type = strtolower((string) ($l['type'] ?? 'website'));
            $svg = $this->authorSocialSvg($type);
            $label = ucfirst($type);
            $linksHtml .= '<a href="' . $this->e($l['url']) . '" rel="noopener nofollow" target="_blank"'
                . ' class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-slate-900 hover:border-slate-400 transition"'
                . ' aria-label="' . $this->e($label) . '">' . $svg . '</a>';
        }

        $out = '<section class="not-prose my-12">'
            . '<div class="rounded-2xl overflow-hidden border border-slate-200 bg-gradient-to-br from-slate-50 to-white">'

            // Header band — slate gradient strip with the eyebrow label.
            . '<div class="px-6 py-3 text-[10px] uppercase tracking-[0.15em] font-bold text-slate-100"'
            . ' style="background:linear-gradient(90deg,#0f172a 0%,#1e293b 100%)">'
            . $eyebrow
            . '</div>'

            // Body — avatar + name + role + bio + socials + more link.
            . '<div class="p-6 sm:p-8">'
            . '<div class="flex flex-col sm:flex-row sm:items-start gap-5">'
            . '<div class="flex-none">' . $avatarHtml . '</div>'
            . '<div class="flex-1 min-w-0">'
            . ($name !== '' ? '<h3 class="text-xl sm:text-2xl font-bold text-slate-900 m-0 leading-tight">' . $this->e($name) . '</h3>' : '')
            . ($role !== '' ? '<div class="text-sm text-slate-500 mt-1">' . $role . '</div>' : '')
            . ($bio !== '' ? '<p class="text-sm sm:text-base text-slate-700 leading-relaxed mt-3 m-0">' . $this->e($bio) . '</p>' : '')
            . ($linksHtml !== '' ? '<div class="flex flex-wrap items-center gap-2 mt-4">' . $linksHtml . '</div>' : '')
            . '</div></div>';

        if ($moreUrl !== '') {
            $label = $moreLabel !== '' ? $moreLabel : 'More from ' . ($name !== '' ? $name : 'this author');
            $external = !str_starts_with($moreUrl, '/');
            $rel = $external ? ' rel="noopener nofollow" target="_blank"' : '';
            $out .= '<div class="mt-5 pt-4 border-t border-slate-200">'
                . '<a href="' . $this->e($moreUrl) . '"' . $rel . ' class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-900 hover:text-emerald-700 transition">'
                . $this->e($label)
                . '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</a></div>';
        }

        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * Brand icon for the author block's social-link row. Returns a tight
     * inline SVG sized for the 36px round button. Unknown types fall back
     * to a globe so admin-added rows still render with a sensible glyph.
     */
    private function authorSocialSvg(string $type): string
    {
        $base = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">';
        $stroke = '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
        return match ($type) {
            'facebook'  => $base . '<path d="M22 12a10 10 0 1 0-11.6 9.9V14.9H8v-2.9h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7 1 0 2.1.2 2.1.2v2.3h-1.2c-1.2 0-1.5.7-1.5 1.5V12h2.6l-.4 2.9h-2.2v6.9A10 10 0 0 0 22 12z"/></svg>',
            'instagram' => $stroke . '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>',
            'twitter', 'x' => $base . '<path d="M18.244 2H21.5l-7.5 8.57L23 22h-6.844l-5.36-7.01L4.7 22H1.44l8.04-9.19L1 2h7.014l4.847 6.41L18.244 2zm-1.198 18.273h1.83L7.045 3.61H5.082l11.964 16.663z"/></svg>',
            'tiktok'    => $base . '<path d="M19.6 6.4a4.5 4.5 0 0 1-3-2.4 4.5 4.5 0 0 1-.8-2H12v13.2a2.6 2.6 0 1 1-2.6-2.6c.3 0 .6 0 .8.1V9.4a6 6 0 1 0 5.2 5.9V8.7a7.8 7.8 0 0 0 4.4 1.4V6.7c-.2 0-.6-.1-.2-.3z"/></svg>',
            'youtube'   => $base . '<path d="M23.5 6.5a3 3 0 0 0-2.1-2.1C19.5 4 12 4 12 4s-7.5 0-9.4.4A3 3 0 0 0 .5 6.5 31.4 31.4 0 0 0 0 12a31.4 31.4 0 0 0 .5 5.5 3 3 0 0 0 2.1 2.1C4.5 20 12 20 12 20s7.5 0 9.4-.4a3 3 0 0 0 2.1-2.1A31.4 31.4 0 0 0 24 12a31.4 31.4 0 0 0-.5-5.5zM9.6 15.6V8.4l6.4 3.6-6.4 3.6z"/></svg>',
            'linkedin'  => $base . '<path d="M20.4 3H3.6A.6.6 0 0 0 3 3.6v16.8c0 .3.3.6.6.6h16.8c.3 0 .6-.3.6-.6V3.6a.6.6 0 0 0-.6-.6zM8.3 18.3H5.5V9.4h2.8v8.9zM6.9 8.2A1.6 1.6 0 1 1 8.5 6.6a1.6 1.6 0 0 1-1.6 1.6zm11.4 10.1h-2.8v-4.3c0-1 0-2.4-1.5-2.4s-1.7 1.1-1.7 2.3v4.4H9.5V9.4h2.7v1.2h.1a3 3 0 0 1 2.7-1.5c2.9 0 3.4 1.9 3.4 4.4v4.8z"/></svg>',
            'email'     => $stroke . '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>',
            default     => $stroke . '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a13 13 0 0 1 0 18M12 3a13 13 0 0 0 0 18"/></svg>',
        };
    }

    /**
     * Resolve a known third-party guide platform from its outbound URL
     * (and falling back to the display name) so the card can render with
     * brand colours, a wordmark, and a Google-favicon sneak-peek without
     * the admin having to upload assets per platform.
     *
     * Returns an associative array with:
     *   - host:        canonical host string used in the card caption
     *   - brandColor:  hex accent used on the wordmark + preview gradient
     *   - color:       palette key (matches $palettes) for the card border
     *   - wordmark:    HTML wordmark element shown in the bottom-left
     *   - faviconUrl:  Google s2-favicon URL for the preview tile (64px)
     * Returns an empty array when no platform matched.
     */
    private function detectGuidePlatform(string $url, string $name): array
    {
        $host = '';
        $parsed = parse_url($url);
        if (!empty($parsed['host'])) {
            $host = preg_replace('~^www\.~i', '', strtolower($parsed['host']));
        }
        $needle = strtolower($host . ' ' . $name);

        // Each entry: keys are substrings searched against host+name; brand
        // colour matches the platform's recognised primary; favicon falls
        // back to Google's generic favicon service so unknown TLDs (e.g.
        // .com.ph) still resolve.
        $platforms = [
            'tripadvisor' => [
                'host' => 'tripadvisor.com',
                'brandColor' => '#00aa6c',
                'color' => 'emerald',
                'wordmark' => 'Tripadvisor',
                'defaultBlurb' => 'Traveler reviews sorted by rating, with user-shot photos of the dining rooms and rooms you are about to book. Useful for cross-checking the marketing photos against what guests actually saw.',
            ],
            'booking' => [
                'host' => 'booking.com',
                'brandColor' => '#003580',
                'color' => 'blue',
                'wordmark' => 'Booking.com',
                'defaultBlurb' => 'Live availability and rate calendars with the largest hotel inventory in the Philippines. Filter by guest score to weed out the listings that look better than they read.',
            ],
            'agoda' => [
                'host' => 'agoda.com',
                'brandColor' => '#d72f60',
                'color' => 'rose',
                'wordmark' => 'agoda',
                'defaultBlurb' => 'Asia-leaning OTA that often surfaces local promos Booking misses. Pay attention to the cancellation-policy filter before locking in non-refundable rates.',
            ],
            'klook' => [
                'host' => 'klook.com',
                'brandColor' => '#ff5722',
                'color' => 'rose',
                'wordmark' => 'Klook',
                'defaultBlurb' => 'Activity and day-pass marketplace. Strong for transfers, ferry tickets, and tour bundles around the area when you want one booking instead of three.',
            ],
            'airbnb' => [
                'host' => 'airbnb.com',
                'brandColor' => '#ff385c',
                'color' => 'rose',
                'wordmark' => 'airbnb',
                'defaultBlurb' => 'Short-term rentals from condo units to private homes. Useful when you want a kitchen, more space, or a long-stay discount instead of a hotel room.',
            ],
            'zomato' => [
                'host' => 'zomato.com',
                'brandColor' => '#e23744',
                'color' => 'rose',
                'wordmark' => 'zomato',
                'defaultBlurb' => 'Restaurant database with menus, photo galleries, and opening hours. The cuisine filter is what you want for narrowing down by ramen vs grill vs Filipino.',
            ],
            'foursquare' => [
                'host' => 'foursquare.com',
                'brandColor' => '#f94877',
                'color' => 'rose',
                'wordmark' => 'Foursquare',
                'defaultBlurb' => 'Crowd-sourced tips from locals and frequent visitors, often more specific than long-form reviews. Skim for the one-line "best seat in the house" notes.',
            ],
            'yelp' => [
                'host' => 'yelp.com',
                'brandColor' => '#d32323',
                'color' => 'rose',
                'wordmark' => 'yelp',
                'defaultBlurb' => 'Western-leaning review platform with thinner Philippines coverage, but the listings it does have tend to have detailed write-ups worth reading.',
            ],
            'google.com/maps' => [
                'host' => 'maps.google.com',
                'brandColor' => '#1a73e8',
                'color' => 'blue',
                'wordmark' => 'Google Maps',
                'defaultBlurb' => 'Crowd-sourced reviews plus live traffic and crowd-busy indicators. The Popular Times graph is the best signal for when the place is quiet enough to walk in.',
            ],
            'maps.google' => [
                'host' => 'maps.google.com',
                'brandColor' => '#1a73e8',
                'color' => 'blue',
                'wordmark' => 'Google Maps',
                'defaultBlurb' => 'Crowd-sourced reviews plus live traffic and crowd-busy indicators. The Popular Times graph is the best signal for when the place is quiet enough to walk in.',
            ],
            'google maps' => [
                'host' => 'maps.google.com',
                'brandColor' => '#1a73e8',
                'color' => 'blue',
                'wordmark' => 'Google Maps',
                'defaultBlurb' => 'Crowd-sourced reviews plus live traffic and crowd-busy indicators. The Popular Times graph is the best signal for when the place is quiet enough to walk in.',
            ],
            'google' => [
                'host' => 'google.com',
                'brandColor' => '#4285f4',
                'color' => 'blue',
                'wordmark' => 'Google',
                'defaultBlurb' => 'Web-wide search results pulling in mentions from food blogs, Reddit threads, and local forums. Good for the off-the-beaten-path takes the big OTA sites miss.',
            ],
            'expedia' => [
                'host' => 'expedia.com',
                'brandColor' => '#fdc72f',
                'color' => 'amber',
                'wordmark' => 'Expedia',
                'defaultBlurb' => 'Bundled flight + hotel deals plus the Vrbo inventory under the hood. Better as a comparison tab than a primary booking site for Philippines stays.',
            ],
            'hotels.com' => [
                'host' => 'hotels.com',
                'brandColor' => '#d32f2f',
                'color' => 'rose',
                'wordmark' => 'Hotels.com',
                'defaultBlurb' => 'Stay-and-earn rewards programme. Useful if you book hotels often enough to bank a free night, otherwise the rates are usually the same as Expedia.',
            ],
            'lonelyplanet' => [
                'host' => 'lonelyplanet.com',
                'brandColor' => '#0089cf',
                'color' => 'blue',
                'wordmark' => 'Lonely Planet',
                'defaultBlurb' => 'Editorially curated long-form guides. Lighter on bookable inventory, heavier on context and what to do once you are there.',
            ],
            'wikitravel' => [
                'host' => 'wikitravel.org',
                'brandColor' => '#0066ff',
                'color' => 'blue',
                'wordmark' => 'Wikitravel',
                'defaultBlurb' => 'Volunteer-edited destination guides with the basic logistics — transit options, neighbourhoods, scams to avoid — written by frequent travellers.',
            ],
            'wikipedia' => [
                'host' => 'wikipedia.org',
                'brandColor' => '#36c',
                'color' => 'slate',
                'wordmark' => 'Wikipedia',
                'defaultBlurb' => 'Historical and geographic context for the area. Worth a 60-second skim before you go so the cultural references and place names land properly.',
            ],
            'reddit' => [
                'host' => 'reddit.com',
                'brandColor' => '#ff4500',
                'color' => 'rose',
                'wordmark' => 'Reddit',
                'defaultBlurb' => 'r/Philippines and r/Manila threads from people who actually live there. Search by venue name for the unvarnished current take on what is and is not worth it.',
            ],
            'facebook' => [
                'host' => 'facebook.com',
                'brandColor' => '#1877f2',
                'color' => 'blue',
                'wordmark' => 'Facebook',
                'defaultBlurb' => 'Official page with the latest announcements, holiday hours, and event posters. Filipino businesses post here more reliably than they update their websites.',
            ],
            'instagram' => [
                'host' => 'instagram.com',
                'brandColor' => '#e1306c',
                'color' => 'rose',
                'wordmark' => 'Instagram',
                'defaultBlurb' => 'Recent guest photos and the daily-special story posts. Filter by the location tag to see what the food and rooms actually look like right now, not in the brochure.',
            ],
            'youtube' => [
                'host' => 'youtube.com',
                'brandColor' => '#ff0000',
                'color' => 'rose',
                'wordmark' => 'YouTube',
                'defaultBlurb' => 'Walk-through videos from Filipino vloggers. The 10-minute room tour or food haul format gives you a better feel for the space than any photo gallery.',
            ],
        ];

        foreach ($platforms as $key => $meta) {
            if (str_contains($needle, $key)) {
                $faviconHost = $meta['host'];
                $meta['faviconUrl'] = 'https://www.google.com/s2/favicons?domain=' . $faviconHost . '&sz=64';
                return $meta;
            }
        }

        // Unknown platform — still hand back a usable favicon URL so the
        // preview tile doesn't fall back to a plain wordmark block when
        // the host is anything resolvable.
        if ($host !== '') {
            return [
                'host' => $host,
                'brandColor' => '#475569',
                'color' => 'slate',
                'wordmark' => '',
                'faviconUrl' => 'https://www.google.com/s2/favicons?domain=' . $host . '&sz=64',
            ];
        }
        return [];
    }

    /**
     * Darken a hex colour by a percentage (0-100). Used to build the
     * 2-stop preview gradient in externalGuides cards so the brand-colour
     * tile gets a subtle depth instead of looking flat.
     */
    private function darkenHex(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) return '#' . $hex;
        $r = max(0, hexdec(substr($hex, 0, 2)) - (int) (255 * $percent / 100));
        $g = max(0, hexdec(substr($hex, 2, 2)) - (int) (255 * $percent / 100));
        $b = max(0, hexdec(substr($hex, 4, 2)) - (int) (255 * $percent / 100));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Related-guides cross-link card grid. Each item links to another
     * keyword page or external guide.
     */
    private function relatedGuides(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'Related guides');

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4">' . $heading . '</h2>';
        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            if (empty($item['title']) || empty($item['url'])) continue;
            $eyebrow = $this->e($item['eyebrow'] ?? '');
            $title = $this->e($item['title']);
            $blurb = $this->e($item['blurb'] ?? '');
            $external = !str_starts_with($item['url'], '/');
            $rel = $external ? ' rel="noopener nofollow" target="_blank"' : '';
            $out .= '<a href="' . $this->e($item['url']) . '"' . $rel . ' class="block rounded-xl border border-slate-200 bg-white p-5 hover:shadow-md hover:border-emerald-300 transition">'
                . ($eyebrow !== '' ? '<div class="text-[10px] uppercase tracking-wide font-bold text-emerald-700 mb-1">' . $eyebrow . '</div>' : '')
                . '<h3 class="font-bold text-slate-900 mb-1">' . $title . '</h3>'
                . ($blurb !== '' ? '<p class="text-sm text-slate-600 m-0">' . $blurb . '</p>' : '')
                . '</a>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Section header: H2 + small subtitle lede. Replaces the hardcoded
     * "What's in [Area]?" template block on keyword pages so the section
     * opener becomes editable in the builder like every other element.
     */
    private function sectionHeader(array $p): string
    {
        $heading = $this->e(trim($p['heading'] ?? ''));
        $subtitle = $this->e(trim($p['subtitle'] ?? ''));
        $anchor = trim($p['anchor'] ?? '');
        if ($heading === '' && $subtitle === '') return '';

        $idAttr = $anchor !== ''
            ? ' id="' . $this->e(preg_replace('~[^a-z0-9-]+~i', '-', $anchor)) . '"'
            : '';

        $out = '<section class="not-prose my-8 mt-10 mb-2"' . $idAttr . '>';
        if ($heading !== '') {
            $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">'
                . $heading . '</h2>';
        }
        if ($subtitle !== '') {
            $out .= '<p class="text-sm text-slate-500 mt-1">' . $subtitle . '</p>';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * Subtitle / italic intro block — replaces the hardcoded
     * $page->subtitle <p> that used to render directly under the H1.
     * Single text field, italic styling, slate-600 color, mb-6
     * matching the legacy spacing so existing pages don't shift.
     */
    private function subtitleIntro(array $p): string
    {
        $text = trim((string) ($p['text'] ?? ''));
        if ($text === '') return '';
        return '<p class="italic text-base text-slate-600 mb-6 leading-relaxed" '
            . 'style="overflow: visible; white-space: normal; text-overflow: clip; max-width: 100%;">'
            . $this->e($text)
            . '</p>';
    }

    /**
     * TL;DR card — collapsible accordion that ports the first half of
     * the legacy partials/summary-blocks partial into a real block.
     * Accepts either a plain paragraph (string `body`) or bullet lines
     * (one per line, prefixed `-` or `*` or unicode `•`). Renders as a
     * native <details> so it works without Alpine/HTMX.
     */
    private function tldrCard(array $p): string
    {
        $body = trim((string) ($p['body'] ?? ''));
        if ($body === '') return '';

        $eyebrow = $this->e(trim($p['eyebrow'] ?? 'The short version'));
        $caption = $this->e(trim($p['caption'] ?? 'Tap to read the key takeaways before you scroll'));

        // Split into bullets if the body contains list-style lines, else
        // render as a single paragraph. Mirrors summary-blocks.blade.php
        // behavior for backwards compatibility with migrated data.
        $lines = preg_split('/\r?\n/', $body);
        $bullets = [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*[-*\x{2022}]\s+(.+)$/u', $line, $m)) {
                $bullets[] = trim($m[1]);
            }
        }

        $out = '<details class="rg-accordion rg-accordion-tldr not-prose my-4 rounded-xl border border-slate-200 bg-white overflow-hidden">';
        $out .= '<summary class="rg-accordion-head flex items-center gap-3 px-5 sm:px-6 py-4 cursor-pointer select-none">';
        $out .= '<span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-50 text-brand-600 shrink-0" aria-hidden="true">';
        $out .= '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>';
        $out .= '</span>';
        $out .= '<span class="flex-1 min-w-0">';
        $out .= '<span class="block text-[0.7rem] uppercase tracking-[0.18em] text-brand-700 font-bold">' . $eyebrow . '</span>';
        $out .= '<span class="block text-sm text-slate-600">' . $caption . '</span>';
        $out .= '</span>';
        $out .= '<svg class="rg-accordion-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>';
        $out .= '</summary>';
        $out .= '<div class="rg-accordion-body"><div class="rg-accordion-body-inner px-5 sm:px-6 pb-5 pt-1 border-t border-slate-100">';
        if (count($bullets) >= 2) {
            $out .= '<ul class="text-slate-700 text-[0.95rem] leading-relaxed space-y-2 mt-4">';
            foreach ($bullets as $b) {
                $out .= '<li class="flex items-start gap-2.5">';
                $out .= '<svg class="w-4 h-4 mt-1 text-brand-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 10 18l9.75-12"/></svg>';
                $out .= '<span>' . $this->e($b) . '</span>';
                $out .= '</li>';
            }
            $out .= '</ul>';
        } else {
            $out .= '<p class="text-slate-700 leading-relaxed text-[0.95rem] mt-4">' . $this->e($body) . '</p>';
        }
        $out .= '</div></div></details>';
        return $out;
    }

    /**
     * WWWW card — collapsible accordion with Why / When / Where / Whom
     * fields. Ports the second half of the legacy summary-blocks
     * partial. Each row gets a tinted icon and a label/body pair. Any
     * empty rows are skipped.
     */
    private function wwwwCard(array $p): string
    {
        $why = trim((string) ($p['why'] ?? ''));
        $when = trim((string) ($p['when'] ?? ''));
        $where = trim((string) ($p['where'] ?? ''));
        $whom = trim((string) ($p['whom'] ?? ''));
        if ($why === '' && $when === '' && $where === '' && $whom === '') return '';

        $eyebrow = $this->e(trim($p['eyebrow'] ?? 'At a glance'));
        $caption = $this->e(trim($p['caption'] ?? 'Why, when, where, and who this guide is for'));

        // Matched 1:1 to summary-blocks.blade.php colors + icons so a
        // migrated page renders identically to its pre-migration form.
        $rows = [
            'why' => [
                'label' => 'Why go', 'body' => $why,
                'tone_bg' => 'bg-orange-50', 'tone_fg' => 'text-orange-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.32.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .32-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>',
            ],
            'when' => [
                'label' => 'When to go', 'body' => $when,
                'tone_bg' => 'bg-cyan-50', 'tone_fg' => 'text-cyan-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>',
            ],
            'where' => [
                'label' => 'Where to go', 'body' => $where,
                'tone_bg' => 'bg-emerald-50', 'tone_fg' => 'text-emerald-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>',
            ],
            'whom' => [
                'label' => 'Whom to go with', 'body' => $whom,
                'tone_bg' => 'bg-fuchsia-50', 'tone_fg' => 'text-fuchsia-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 0 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>',
            ],
        ];
        $active = array_filter($rows, fn ($r) => $r['body'] !== '');

        $out = '<details class="rg-accordion rg-accordion-wwww not-prose my-4 rounded-xl border border-slate-200 bg-white overflow-hidden">';
        $out .= '<summary class="rg-accordion-head flex items-center gap-3 px-5 sm:px-6 py-4 cursor-pointer select-none">';
        $out .= '<span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 shrink-0" aria-hidden="true">';
        $out .= '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>';
        $out .= '</span>';
        $out .= '<span class="flex-1 min-w-0">';
        $out .= '<span class="block text-[0.7rem] uppercase tracking-[0.18em] text-emerald-700 font-bold">' . $eyebrow . '</span>';
        $out .= '<span class="block text-sm text-slate-600">' . $caption . '</span>';
        $out .= '</span>';
        $out .= '<svg class="rg-accordion-chevron w-5 h-5 text-slate-400 shrink-0 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>';
        $out .= '</summary>';
        $out .= '<div class="rg-accordion-body"><div class="rg-accordion-body-inner border-t border-slate-100">';
        $count = 0;
        $total = count($active);
        foreach ($active as $row) {
            $count++;
            $borderCls = $count < $total ? 'border-b border-slate-100' : '';
            $out .= '<div class="flex items-start gap-4 px-5 sm:px-6 py-4 ' . $borderCls . '">';
            $out .= '<span class="w-9 h-9 inline-flex items-center justify-center rounded-full ' . $row['tone_bg'] . ' ' . $row['tone_fg'] . ' shrink-0" aria-hidden="true">';
            $out .= '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">' . $row['icon'] . '</svg>';
            $out .= '</span>';
            $out .= '<div class="flex-1 min-w-0">';
            $out .= '<div class="text-[0.7rem] uppercase tracking-[0.18em] text-slate-500 font-bold mb-1">' . $this->e($row['label']) . '</div>';
            $out .= '<p class="text-slate-700 text-[0.95rem] leading-relaxed">' . $this->e($row['body']) . '</p>';
            $out .= '</div>';
            $out .= '</div>';
        }
        $out .= '</div></div></details>';
        return $out;
    }

    /**
     * Social share row block — replaces the hardcoded
     * @include('partials.social-share', …) that used to sit just under
     * the H1. Reuses the partial for rendering so brand-color buttons
     * + copy-link behavior stay in one place. Reads the page URL +
     * title from $context['page'] (set by KeywordPageController).
     */
    private function socialShare(array $p, array $context): string
    {
        $page = $context['page'] ?? null;
        $url = $context['page_url'] ?? null;
        if (!$url && $page) $url = url($page->slug);
        if (!$url) $url = url()->current();
        $title = $page->title ?? ($context['page_title'] ?? '');
        $align = $p['align'] ?? 'between';
        try {
            return view('partials.social-share', [
                'url' => $url,
                'title' => $title,
                'align' => in_array($align, ['start', 'end', 'between'], true) ? $align : 'between',
            ])->render();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * "We Recommend" listings band block — replaces the hardcoded
     * @if($keyword->category === 'food') ... @else ... @endif
     * @include('partials.listings-rows') that used to sit between the
     * social share and the TLDR/WWWW cards. Reuses the partial so the
     * card layout + empty-state "list your property" CTA + brand
     * colors stay in one place.
     *
     * The partial branches its own heading text on $keyword->category
     * (food → "Restaurants, Eateries & Food Destinations We Recommend",
     *  non-food → "We Recommend"), so the block doesn't have to
     * duplicate that logic — just pass the right `listings` collection
     * + the keyword + the area.
     *
     * Context required: keyword, listings, restaurantListings,
     * listingGalleries, areaForCta. KeywordPageController populates
     * all of these whenever a block of this type is in the page.
     */
    private function weRecommendBand(array $p, array $context): string
    {
        $keyword = $context['keyword'] ?? null;
        if (!$keyword) return '';

        $isFood = ($keyword->category ?? null) === 'food';
        $rows = $isFood
            ? ($context['restaurantListings'] ?? collect())
            : ($context['listings'] ?? collect());
        $galleries = $isFood ? [] : ($context['listingGalleries'] ?? []);
        $area = $context['areaForCta'] ?? null;

        try {
            return view('partials.listings-rows', [
                'listings' => $rows,
                'listingGalleries' => $galleries,
                'area' => $area,
            ])->render();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Restaurant Recommendations band block — replaces the hardcoded
     * "@if($keyword->category !== 'food' && $restaurantListings->isNotEmpty())"
     * <section> block that used to sit below the main content stream.
     * Renders only on non-food keyword pages that have at least one
     * active restaurant listing — otherwise returns empty so the
     * section disappears.
     */
    private function restaurantRecsBand(array $p, array $context): string
    {
        $keyword = $context['keyword'] ?? null;
        $rows = $context['restaurantListings'] ?? collect();
        if (!$keyword || ($keyword->category ?? null) === 'food' || $rows->isEmpty()) return '';

        $eyebrow = $this->e(trim($p['eyebrow'] ?? 'Eat nearby'));
        $heading = $this->e(trim($p['heading'] ?? 'Restaurant Recommendations'));
        $caption = $this->e(trim($p['caption'] ?? 'Paid placements where your guests will likely want to eat.'));

        $out = '<section class="my-14 pt-10 border-t border-slate-200">';
        $out .= '<div class="flex items-end justify-between mb-6 flex-wrap gap-2"><div>';
        $out .= '<p class="text-xs uppercase tracking-[0.18em] text-brand-700 font-bold mb-1">' . $eyebrow . '</p>';
        $out .= '<h2 class="text-2xl font-bold text-slate-900">' . $heading . '</h2>';
        $out .= '<p class="text-sm text-slate-500 mt-1">' . $caption . '</p>';
        $out .= '</div></div>';
        try {
            $out .= view('partials.restaurant-listings', ['listings' => $rows])->render();
        } catch (\Throwable $e) {
            return '';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * Memorable Adventures & Activities band block — replaces the
     * hardcoded "@if($keyword->category !== 'food' && $adventureListings
     * ->isNotEmpty())" <section> block. Same conditional behavior:
     * only renders on non-food keyword pages with at least one active
     * adventure listing.
     */
    private function adventuresBand(array $p, array $context): string
    {
        $keyword = $context['keyword'] ?? null;
        $rows = $context['adventureListings'] ?? collect();
        if (!$keyword || ($keyword->category ?? null) === 'food' || $rows->isEmpty()) return '';

        $eyebrow = $this->e(trim($p['eyebrow'] ?? 'Things to do'));
        $heading = $this->e(trim($p['heading'] ?? 'Memorable Adventures & Activities'));
        $caption = $this->e(trim($p['caption'] ?? 'Surf schools, ATV trails, island hops, and paintball arenas open in the area.'));

        $out = '<section class="my-14 pt-10 border-t border-slate-200">';
        $out .= '<div class="flex items-end justify-between mb-6 flex-wrap gap-2"><div>';
        $out .= '<p class="text-xs uppercase tracking-[0.18em] text-amber-700 font-bold mb-1">' . $eyebrow . '</p>';
        $out .= '<h2 class="text-2xl font-bold text-slate-900">' . $heading . '</h2>';
        $out .= '<p class="text-sm text-slate-500 mt-1">' . $caption . '</p>';
        $out .= '</div></div>';
        try {
            $out .= view('partials.adventure-listings', ['listings' => $rows])->render();
        } catch (\Throwable $e) {
            return '';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * Reviews band block — "What travelers are saying" section.
     * Renders the destination-review grid + the aggregate rating
     * eyebrow. Only renders when at least one published review is
     * scoped to this keyword.
     */
    private function reviewsBand(array $p, array $context): string
    {
        $reviews = $context['reviews'] ?? collect();
        if ($reviews->isEmpty()) return '';

        $heading = $this->e(trim($p['heading'] ?? 'What travelers are saying'));
        $avg = round($reviews->avg('rating'), 2);
        $cnt = $reviews->count();
        $filled = (int) floor($avg);
        $reviewWord = $cnt === 1 ? 'review' : 'reviews';

        $out = '<section class="my-14">';
        $out .= '<div class="flex items-baseline justify-between flex-wrap gap-2 mb-5">';
        $out .= '<h2 class="text-2xl font-bold text-slate-900">' . $heading . '</h2>';
        $out .= '<div class="text-sm text-slate-600 flex items-center gap-2">';
        $out .= '<span class="inline-flex items-center gap-1">';
        for ($i = 0; $i < $filled; $i++) {
            $out .= '<svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
        }
        $out .= '</span>';
        $out .= '<strong>' . $this->e((string) $avg) . '</strong> out of 5 · based on ' . $cnt . ' ' . $reviewWord;
        $out .= '</div></div>';

        $out .= '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($reviews as $r) {
            $name = $this->e((string) ($r->reviewer_name ?? ''));
            $location = $this->e((string) ($r->reviewer_location ?? ''));
            $text = $this->e((string) ($r->review_text ?? ''));
            $rating = (int) ($r->rating ?? 0);
            $stars = str_repeat('★', $rating);
            $avatar = method_exists($r, 'avatarUrl') ? $r->avatarUrl() : ($r->avatar_url ?? '');
            $date = '';
            if (!empty($r->review_date)) {
                try { $date = \Carbon\Carbon::parse($r->review_date)->format('M j, Y'); } catch (\Throwable $e) {}
            }
            $out .= '<article class="p-5 rounded-xl border border-slate-200 bg-white flex flex-col gap-3 hover:shadow-sm transition">';
            $out .= '<div class="flex items-start gap-3">';
            if ($avatar) {
                $out .= '<img src="' . $this->e($avatar) . '" alt="' . $name . '" class="w-10 h-10 rounded-full bg-slate-100 ring-1 ring-slate-200" loading="lazy">';
            }
            $out .= '<div class="flex-1 min-w-0">';
            $out .= '<div class="font-semibold text-slate-900 truncate">' . $name . '</div>';
            if ($location !== '') $out .= '<div class="text-xs text-slate-500 truncate">' . $location . '</div>';
            $out .= '</div>';
            $out .= '<div class="text-amber-400 text-sm">' . $stars . '</div>';
            $out .= '</div>';
            $out .= '<p class="text-sm text-slate-700 leading-relaxed">' . $text . '</p>';
            if ($date !== '') $out .= '<div class="text-xs text-slate-400 mt-auto">' . $this->e($date) . '</div>';
            $out .= '</article>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Generic data table. Headers + rows. Caller manages column ordering.
     * Mobile: horizontal scroll. NO price columns — Rule 3 means peso
     * mentions get caught by the audit even here.
     */
    private function dataTable(array $p): string
    {
        $headers = (array) ($p['headers'] ?? []);
        $rows = (array) ($p['rows'] ?? []);
        if (!$headers || !$rows) return '';
        $caption = $this->e($p['caption'] ?? '');

        $out = '<div class="not-prose my-8">';
        if ($caption !== '') {
            $out .= '<div class="text-[10px] uppercase tracking-wide font-bold text-slate-500 mb-2">' . $caption . '</div>';
        }
        $out .= '<div class="overflow-x-auto rounded-xl border border-slate-200">'
            . '<table class="w-full border-collapse text-sm"><thead><tr style="background:#0f172a;color:#fff">';
        foreach ($headers as $h) {
            $out .= '<th class="px-4 py-3 text-left font-bold">' . $this->e($h) . '</th>';
        }
        $out .= '</tr></thead><tbody style="background:#fff">';
        $alt = false;
        foreach ($rows as $row) {
            $bg = $alt ? 'background:#f8fafc;' : '';
            $out .= '<tr style="border-top:1px solid #e2e8f0;' . $bg . '">';
            foreach ($headers as $i => $_) {
                $cell = $row[$i] ?? '';
                $weight = $i === 0 ? 'font-bold text-slate-800' : 'text-slate-700';
                $out .= '<td class="px-4 py-3 ' . $weight . '">' . $this->e((string) $cell) . '</td>';
            }
            $out .= '</tr>';
            $alt = !$alt;
        }
        $out .= '</tbody></table></div></div>';
        return $out;
    }

    /**
     * Nearby destinations card grid. Each card links to a destination
     * keyword page (vertical=resort) the visitor might want to explore
     * after their meal. Items can be authored explicitly, or — when the
     * payload's `items` array is empty — auto-resolved at render time
     * from the page's own cluster_tag via [[fetchNearbyDestinations]].
     */
    private function nearbyDestinations(array $p): string
    {
        $heading = $p['heading'] ?? 'Stay nearby after the meal';
        $intro = trim((string) ($p['intro'] ?? ''));
        $items = $p['items'] ?? [];

        // Auto-resolve mode: admin left items blank, so we pull a list of
        // sibling destination keyword pages from the same cluster.
        if (!$items && !empty($p['auto_from_cluster'])) {
            $items = $this->fetchNearbyDestinations(
                (string) $p['auto_from_cluster'],
                (int) ($p['max'] ?? 6),
                (string) ($p['exclude_slug'] ?? '')
            );
        }
        if (!$items) return '';

        $clusterLabels = [
            'metro-manila' => 'Metro Manila',
            'cavite' => 'Cavite',
            'batangas' => 'Batangas',
            'laguna' => 'Laguna',
            'rizal' => 'Rizal',
            'bulacan' => 'Bulacan',
            'pampanga' => 'Pampanga',
            'north-luzon' => 'North Luzon',
            'bicol' => 'Bicol',
            'quezon' => 'Quezon Province',
            'visayas' => 'Visayas',
            'palawan' => 'Palawan',
            'mindanao' => 'Mindanao',
            'other' => 'Philippines',
        ];

        $out = '<section class="not-prose my-12">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $this->e($heading) . '</h2>';
        if ($intro === '') {
            $intro = 'Done eating? Here are the resorts, hotels, and weekend escapes within a comfortable drive from the area.';
        }
        $out .= '<p class="text-slate-600 mb-6 leading-relaxed max-w-3xl">' . $this->e($intro) . '</p>';

        $out .= '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            if ($title === '' || $url === '') continue;
            $img = trim((string) ($item['image'] ?? ''));
            $blurb = trim((string) ($item['blurb'] ?? ''));
            $clusterKey = (string) ($item['cluster'] ?? '');
            $eyebrow = $item['eyebrow'] ?? ($clusterLabels[$clusterKey] ?? '');
            $distance = trim((string) ($item['distance_label'] ?? ''));

            $hero = $img !== ''
                ? '<div class="relative aspect-[16/10] overflow-hidden bg-slate-100">'
                    . '<img src="' . $this->e($this->normalizeMediaUrl($img)) . '" alt="' . $this->e($title)
                    . '" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">'
                    . '<div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-slate-900/70 to-transparent pointer-events-none"></div>'
                    . ($distance !== ''
                        ? '<div class="absolute top-3 left-3 px-2 py-1 rounded-full bg-white/95 text-[10px] uppercase tracking-wider font-bold text-slate-700 shadow-sm">'
                            . $this->e($distance) . '</div>'
                        : '')
                    . '</div>'
                : '<div class="aspect-[16/10] bg-gradient-to-br from-emerald-50 to-slate-100"></div>';

            $external = !str_starts_with($url, '/');
            $rel = $external ? ' rel="noopener nofollow" target="_blank"' : '';
            $out .= '<a href="' . $this->e($url) . '"' . $rel
                . ' class="group block rounded-xl border border-slate-200 bg-white overflow-hidden transition hover:shadow-lg hover:-translate-y-px">'
                . $hero
                . '<div class="p-4">'
                . ($eyebrow ? '<div class="text-[10px] uppercase tracking-wider font-bold text-emerald-700 mb-1">' . $this->e($eyebrow) . '</div>' : '')
                . '<h3 class="font-bold text-slate-900 text-base mb-1 leading-tight group-hover:text-emerald-700 transition">' . $this->e($title)
                . ' <span class="inline-block transition group-hover:translate-x-0.5">&rarr;</span>'
                . '</h3>'
                . ($blurb !== '' ? '<p class="text-sm text-slate-600 leading-snug m-0">' . $this->e($blurb) . '</p>' : '')
                . '</div></a>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Look up to N resort-category keyword pages in the same cluster,
     * pulling cover image + h1 + cluster label so the block renders rich
     * without per-page authoring. Excludes the current page's own slug
     * to avoid self-recommendation. Caller may broaden the candidate
     * pool by passing additional cluster keys in the future; for now
     * we stick to exact-cluster matches and rank by search volume DESC
     * so the most-trafficked nearby destinations float to the top.
     */
    private function fetchNearbyDestinations(string $cluster, int $max, string $excludeSlug): array
    {
        if ($cluster === '') return [];
        $rows = DB::table('rg_keywords as k')
            ->leftJoin('rg_seo_pages as p', 'p.keyword_id', '=', 'k.id')
            ->where('k.category', 'resort')
            ->where('k.cluster_tag', $cluster)
            ->when($excludeSlug !== '', fn ($q) => $q->where('k.slug', '!=', $excludeSlug))
            ->orderByDesc('k.search_volume_monthly')
            ->orderBy('k.id')
            ->limit($max)
            ->get([
                'k.slug as slug',
                'k.phrase as phrase',
                'k.cluster_tag as cluster_tag',
                'p.og_image_path as og_image_path',
                'p.h1 as h1',
                'p.meta_description as meta_description',
            ]);

        $items = [];
        foreach ($rows as $r) {
            $items[] = [
                'title' => $r->h1 ?: ucwords(str_replace('-', ' ', (string) $r->phrase)),
                'url' => '/' . $r->slug,
                'image' => $r->og_image_path ? '/storage/' . ltrim($r->og_image_path, '/') : '',
                'blurb' => $r->meta_description ?: '',
                'cluster' => $r->cluster_tag,
            ];
        }
        return $items;
    }

    /**
     * Related blog posts strip. Three-col card grid with cover image +
     * tag eyebrow + title + excerpt + a thin meta row underneath. As
     * with [[nearbyDestinations]], items can be authored explicitly or
     * left empty to trigger auto-resolution by area keywords.
     */
    private function relatedBlogs(array $p): string
    {
        $heading = $p['heading'] ?? 'More reads on the area';
        $intro = trim((string) ($p['intro'] ?? ''));
        $items = $p['items'] ?? [];

        if (!$items && !empty($p['auto_from_keywords'])) {
            $items = $this->fetchRelatedBlogs(
                (array) $p['auto_from_keywords'],
                (int) ($p['max'] ?? 3)
            );
        }
        if (!$items) return '';

        $out = '<section class="not-prose my-12">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $this->e($heading) . '</h2>';
        if ($intro === '') {
            $intro = 'Long-form takes and trip plans from our editorial team on the same part of the country.';
        }
        $out .= '<p class="text-slate-600 mb-6 leading-relaxed max-w-3xl">' . $this->e($intro) . '</p>';

        $out .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">';
        foreach ($items as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            if ($title === '' || $url === '') continue;
            $cover = trim((string) ($item['cover'] ?? ''));
            $excerpt = trim((string) ($item['excerpt'] ?? ''));
            $eyebrow = trim((string) ($item['eyebrow'] ?? ''));
            $meta = trim((string) ($item['meta'] ?? ''));

            $hero = $cover !== ''
                ? '<div class="relative aspect-[16/9] overflow-hidden bg-slate-100">'
                    . '<img src="' . $this->e($this->normalizeMediaUrl($cover)) . '" alt="' . $this->e($title)
                    . '" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">'
                    . '</div>'
                : '<div class="aspect-[16/9] bg-gradient-to-br from-amber-50 to-slate-100"></div>';

            $external = !str_starts_with($url, '/');
            $rel = $external ? ' rel="noopener nofollow" target="_blank"' : '';
            $out .= '<a href="' . $this->e($url) . '"' . $rel
                . ' class="group rounded-xl border border-slate-200 bg-white overflow-hidden transition hover:shadow-lg hover:-translate-y-px flex flex-col">'
                . $hero
                . '<div class="p-5 flex-1 flex flex-col">'
                . ($eyebrow ? '<div class="text-[10px] uppercase tracking-wider font-bold text-amber-700 mb-2">' . $this->e($eyebrow) . '</div>' : '')
                . '<h3 class="font-bold text-slate-900 text-base leading-snug mb-2 group-hover:text-emerald-700 transition">' . $this->e($title) . '</h3>'
                . ($excerpt !== '' ? '<p class="text-sm text-slate-600 leading-relaxed m-0 flex-1">' . $this->e($excerpt) . '</p>' : '')
                . ($meta !== '' ? '<div class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100">' . $this->e($meta) . '</div>' : '')
                . '</div></a>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Search blog posts whose `tags` column (comma-separated string)
     * matches any of the provided area keywords. Scoring is a sum of
     * keyword hits per post, ties broken by published_at DESC so fresh
     * content surfaces. Falls back to recent posts overall when no
     * matches exist so the section never renders empty.
     */
    private function fetchRelatedBlogs(array $keywords, int $max): array
    {
        $keywords = array_values(array_filter(array_map('trim', $keywords), fn ($k) => $k !== ''));
        if (!$keywords) return [];

        $query = DB::table('rg_blog_posts')->where('status', 'published');
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $k) {
                $q->orWhere('tags', 'like', '%' . $k . '%');
            }
        });
        $posts = $query
            ->orderByDesc('published_at')
            ->limit($max * 3)  // overscan then rerank by hit-count
            ->get(['title', 'slug', 'excerpt', 'cover_path', 'tags', 'published_at']);

        // Rerank by match count.
        $scored = [];
        foreach ($posts as $post) {
            $hay = strtolower((string) $post->tags);
            $score = 0;
            $matchedTag = '';
            foreach ($keywords as $k) {
                if (str_contains($hay, strtolower($k))) {
                    $score++;
                    if ($matchedTag === '') $matchedTag = $k;
                }
            }
            if ($score > 0) {
                $scored[] = ['score' => $score, 'post' => $post, 'tag' => $matchedTag];
            }
        }
        usort($scored, function ($a, $b) {
            if ($a['score'] !== $b['score']) return $b['score'] <=> $a['score'];
            return strcmp((string) $b['post']->published_at, (string) $a['post']->published_at);
        });
        $scored = array_slice($scored, 0, $max);

        // Fallback: nothing matched — pull recent published posts so the
        // section still renders something useful.
        if (!$scored) {
            $recent = DB::table('rg_blog_posts')
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->limit($max)
                ->get(['title', 'slug', 'excerpt', 'cover_path', 'tags', 'published_at']);
            foreach ($recent as $r) {
                $tag = explode(',', (string) $r->tags)[0] ?? '';
                $scored[] = ['post' => $r, 'tag' => trim($tag)];
            }
        }

        $items = [];
        foreach ($scored as $row) {
            $post = $row['post'];
            $items[] = [
                'title' => $post->title,
                'url' => '/blog/' . $post->slug,
                'cover' => $post->cover_path ? '/storage/' . ltrim($post->cover_path, '/') : '',
                'excerpt' => $post->excerpt ? mb_strimwidth($post->excerpt, 0, 160, '…', 'UTF-8') : '',
                'eyebrow' => $row['tag'] ?? '',
                // Published date hidden per editorial direction so
                // evergreen pieces don't look stale. Leaving the field
                // wired into the payload as an empty string keeps the
                // shape stable for admins who might re-enable it.
                'meta' => '',
            ];
        }
        return $items;
    }

    /**
     * Vertical list variant of quick_facts. Designed for destination
     * pages where the facts (travel time, best season, local rules) are
     * each a full sentence — reads better as a scannable list with the
     * icon + eyebrow on one column and the prose on the other, than as
     * a 4-up grid where the prose gets truncated.
     *
     * Payload shape mirrors quick_facts: `cards[]` of
     * { icon, color, label, big, detail } — so existing quick_facts
     * payloads migrate to facts_list by changing block_type only.
     */
    private function factsList(array $p): string
    {
        $cards = $p['cards'] ?? [];
        if (!$cards) return '';

        $palettes = [
            'blue'    => ['bg' => '#eff6ff', 'br' => '#bfdbfe', 'fg' => '#1d4ed8', 'sm' => '#1e3a8a'],
            'emerald' => ['bg' => '#ecfdf5', 'br' => '#a7f3d0', 'fg' => '#047857', 'sm' => '#064e3b'],
            'rose'    => ['bg' => '#fff1f2', 'br' => '#fecdd3', 'fg' => '#be123c', 'sm' => '#881337'],
            'amber'   => ['bg' => '#fffbeb', 'br' => '#fcd34d', 'fg' => '#b45309', 'sm' => '#78350f'],
            'violet'  => ['bg' => '#f5f3ff', 'br' => '#ddd6fe', 'fg' => '#6d28d9', 'sm' => '#4c1d95'],
            'slate'   => ['bg' => '#f8fafc', 'br' => '#cbd5e1', 'fg' => '#334155', 'sm' => '#0f172a'],
        ];

        $out = '';
        if (!empty($p['heading'])) {
            $out .= '<h2 class="text-2xl font-bold text-slate-900 mt-8 mb-3">'
                . $this->e($p['heading']) . '</h2>';
        }

        $out .= '<div class="not-prose my-8 rounded-2xl border border-slate-200 bg-white overflow-hidden">';
        $out .= '<ul class="divide-y divide-slate-100 m-0 list-none p-0">';

        foreach ($cards as $card) {
            $pal = $palettes[$card['color'] ?? 'blue'] ?? $palettes['blue'];
            $svg = $this->quickFactIconSvg($card['icon'] ?? 'info');
            $label = $this->e($card['label'] ?? '');
            $big = $this->e($card['big'] ?? '');
            $detail = $this->e($card['detail'] ?? '');

            // Each row: gradient icon disc on the left + uppercase
            // eyebrow stacked over the big-text headline on the right,
            // with the optional detail wrapping below.
            $out .= '<li class="flex items-start gap-4 p-5 sm:p-6">';
            $out .= '<div class="flex-none w-12 h-12 rounded-xl flex items-center justify-center"'
                . ' style="background:' . $pal['bg'] . ';border:1px solid ' . $pal['br'] . ';color:' . $pal['fg'] . '">'
                . $svg . '</div>';
            $out .= '<div class="flex-1 min-w-0">';
            if ($label !== '') {
                $out .= '<div class="text-[10px] uppercase tracking-[0.15em] font-bold mb-1"'
                    . ' style="color:' . $pal['sm'] . '">' . $label . '</div>';
            }
            if ($big !== '') {
                $out .= '<div class="text-base sm:text-lg font-bold text-slate-900 leading-snug">'
                    . $big . '</div>';
            }
            if ($detail !== '') {
                $out .= '<p class="text-sm text-slate-600 leading-relaxed mt-1 m-0">' . $detail . '</p>';
            }
            $out .= '</div></li>';
        }

        $out .= '</ul></div>';
        return $out;
    }

    /**
     * Researched origin / history of a venue or district. Renders as a
     * parchment-toned framed section with a small "Local history"
     * eyebrow, an H2 headline, and multi-paragraph body. Authored from
     * web-sourced facts (NOT invented prose) — gives the page real
     * E-E-A-T context and helps the search engines distinguish this
     * page from the templated competition.
     *
     * Payload shape: { eyebrow, heading, body, founded, citation_label,
     * citation_url }. Body is blank-line separated paragraphs, same
     * convention as text_section. founded is an optional single-word
     * year shown as a small chip next to the heading.
     */
    private function placeHistory(array $p): string
    {
        $heading = trim((string) ($p['heading'] ?? ''));
        $body = trim((string) ($p['body'] ?? ''));
        if ($heading === '' && $body === '') return '';

        $eyebrow = $this->e($p['eyebrow'] ?? 'Local history');
        $founded = trim((string) ($p['founded'] ?? ''));
        $citationLabel = trim((string) ($p['citation_label'] ?? ''));
        $citationUrl = trim((string) ($p['citation_url'] ?? ''));

        // Split body on blank lines, render each chunk as a paragraph.
        $paragraphs = preg_split('~\n\s*\n+~', $body) ?: [];
        $bodyHtml = '';
        foreach ($paragraphs as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') continue;
            $bodyHtml .= '<p class="text-slate-700 leading-relaxed m-0 mb-3 last:mb-0 text-[15px] sm:text-base">'
                . $this->e($chunk) . '</p>';
        }

        // Citation footer (small "Source: NHCP" type line). Renders only
        // when a label is provided; URL is optional and adds the
        // rel="noopener nofollow" + target="_blank" per Rule 11.
        $citationHtml = '';
        if ($citationLabel !== '') {
            $linkHtml = $citationUrl !== ''
                ? '<a href="' . $this->e($citationUrl) . '" rel="noopener nofollow" target="_blank"'
                    . ' class="underline decoration-amber-700/40 hover:decoration-amber-700">'
                    . $this->e($citationLabel) . '</a>'
                : $this->e($citationLabel);
            $citationHtml = '<div class="mt-5 pt-4 border-t border-amber-700/15 text-xs text-amber-900/80 italic">'
                . 'Source: ' . $linkHtml . '</div>';
        }

        $foundedChip = $founded !== ''
            ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] uppercase tracking-wider font-bold bg-amber-100 text-amber-800 border border-amber-200">'
                . 'Since ' . $this->e($founded) . '</span>'
            : '';

        // Inline-SVG scroll icon. Sits in a circle ahead of the eyebrow
        // so the section visually telegraphs "history" at a glance.
        $scrollSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">'
            . '<path d="M8 3h11a2 2 0 0 1 2 2v3"/>'
            . '<path d="M21 8a3 3 0 0 1-3 3H7"/>'
            . '<path d="M5 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h13a3 3 0 0 0 3-3V8"/>'
            . '<path d="M8 9h6M8 13h6M8 17h4"/>'
            . '</svg>';

        return '<section class="not-prose my-10 rounded-2xl overflow-hidden border border-amber-200"'
            . ' style="background:linear-gradient(180deg,#fffbeb 0%,#fef3c7 100%)">'
            . '<div class="p-6 sm:p-8">'
            // Eyebrow row: icon circle + uppercase label + optional year chip.
            . '<div class="flex items-center gap-3 mb-3 flex-wrap">'
            . '<div class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-200/70 text-amber-800">'
            . $scrollSvg . '</div>'
            . '<div class="text-[11px] uppercase tracking-[0.2em] font-bold text-amber-800">'
            . $eyebrow . '</div>'
            . ($foundedChip !== '' ? '<div class="ml-auto">' . $foundedChip . '</div>' : '')
            . '</div>'
            . ($heading !== ''
                ? '<h2 class="text-2xl sm:text-3xl font-bold text-amber-950 mb-4 leading-tight" style="font-family:Georgia,Cambria,Times New Roman,serif">'
                    . $this->e($heading) . '</h2>'
                : '')
            . $bodyHtml
            . $citationHtml
            . '</div>'
            . '</section>';
    }

    /**
     * "Foods to try in <Place>" card grid. Each item lists an actual
     * dish — not a restaurant — so the per-destination food culture
     * surfaces as a researched gallery instead of being buried inside
     * prose blocks. Items: { name, where, blurb, image }.
     */
    private function foodsToTry(array $p): string
    {
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $heading = $this->e($p['heading'] ?? 'Foods to try');
        $intro = $this->e($p['intro'] ?? '');

        $out = '<section class="not-prose my-10">';
        $out .= '<h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        if ($intro !== '') {
            $out .= '<p class="text-slate-600 mb-6 leading-relaxed max-w-3xl">' . $intro . '</p>';
        }

        $out .= '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            if ($name === '') continue;
            $where = $this->e($item['where'] ?? '');
            $blurb = $this->e($item['blurb'] ?? '');
            $img = trim((string) ($item['image'] ?? ''));

            $hero = $img !== ''
                ? '<div class="aspect-[16/10] overflow-hidden bg-slate-100">'
                    . '<img src="' . $this->e($this->normalizeMediaUrl($img)) . '" alt="' . $this->e($name)
                    . '" class="w-full h-full object-cover" loading="lazy">'
                    . '</div>'
                : '<div class="aspect-[16/10] flex items-center justify-center bg-gradient-to-br from-amber-50 to-rose-50">'
                    . '<svg class="w-10 h-10 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
                    . '<path d="M3 11h18M6 11V8a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v3M5 11v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8"/>'
                    . '</svg>'
                    . '</div>';

            $out .= '<div class="rounded-xl border border-slate-200 bg-white overflow-hidden flex flex-col">'
                . $hero
                . '<div class="p-4 flex-1 flex flex-col">'
                . '<h3 class="font-bold text-slate-900 text-lg leading-tight mb-1">' . $this->e($name) . '</h3>'
                . ($where !== ''
                    ? '<div class="text-[11px] uppercase tracking-wider font-bold text-amber-700 mb-2">'
                        . 'Where: ' . $where . '</div>'
                    : '')
                . ($blurb !== ''
                    ? '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $blurb . '</p>'
                    : '')
                . '</div></div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /* ============================================================
     * /destinations page custom block types
     * ============================================================
     * The three blocks below reproduce the /destinations page's
     * three distinct sections and make each one a builder element:
     *
     *   1. dest_hero_search    — top hero with breadcrumb, title,
     *                            stats pills, and powerful typeahead
     *                            search (filter tabs + popular chips
     *                            + grouped result panel + fuzzy
     *                            match + mark highlighting + keyboard
     *                            nav).
     *   2. dest_featured_slider — "Tourist spots worth the trip"
     *                            Splide carousel of featured items.
     *   3. dest_region_clusters — "Jump to region" sticky pill nav
     *                            + cluster grid of keyword cards by
     *                            region.
     *
     * Each block reads its live data from $context, which
     * DestinationsController passes via the static-page block
     * renderer.
     * ============================================================ */

    /**
     * dest_hero_search — top hero with breadcrumb, title, stats
     * pills, and TripAdvisor-style typeahead search. Self-contained
     * (full CSS + JS inline).
     *
     * Payload:
     *   eyebrow              → small uppercase chip above title
     *   title                → H1 with {{accent}}…{{/accent}} markers
     *                          around the colored portion
     *   accent               → slate|amber|brand|emerald|rose|violet|teal
     *   paragraphs           → array of intro paragraphs (or string
     *                          with blank-line separators)
     *   bg_gradient          → none|amber-rose|brand-emerald|
     *                          rose-amber|violet-teal|teal-emerald
     *   breadcrumbs          → [{label, url}] (last item unlinked)
     *   stats_pills          → [{label, value, value_source}]
     *                          value_source: literal | stats.{key} |
     *                                        context.{key}.count
     *   search_placeholder
     *   search_tabs          → [{value, label}]
     *   search_chips         → string[]
     *   search_labels_json   → JSON string: {type:groupLabel}
     *   search_empty_hint
     *
     * Context (set by DestinationsController):
     *   stats              → ['total_destinations','total_regions','top_volume']
     *   searchIndex        → flat JSON for the typeahead
     */
    private function destHeroSearch(array $p, array $context): string
    {
        // Normalize JSON-textarea fields. The mother builder stores
        // breadcrumbs / stats_pills / search_tabs / search_chips as
        // raw JSON strings (textarea values) — coerce back to arrays
        // here so render code doesn't have to branch on type.
        foreach (['breadcrumbs', 'stats_pills', 'search_tabs', 'search_chips', 'paragraphs'] as $jsonField) {
            if (isset($p[$jsonField]) && is_string($p[$jsonField])) {
                $trimmed = trim($p[$jsonField]);
                if ($trimmed !== '' && ($trimmed[0] === '[' || $trimmed[0] === '{')) {
                    $decoded = json_decode($trimmed, true);
                    if (is_array($decoded)) $p[$jsonField] = $decoded;
                }
            }
        }

        $eyebrow = $this->e(trim($p['eyebrow'] ?? ''));
        $title = trim($p['title'] ?? '');
        $accent = in_array($p['accent'] ?? 'brand', ['slate', 'amber', 'brand', 'emerald', 'rose', 'violet', 'teal'], true)
            ? $p['accent'] : 'brand';
        $accentTextMap = [
            'slate' => 'text-slate-700',
            'amber' => 'text-amber-700',
            'brand' => 'text-blue-700',
            'emerald' => 'text-emerald-700',
            'rose' => 'text-rose-700',
            'violet' => 'text-violet-700',
            'teal' => 'text-teal-700',
        ];
        $accentText = $accentTextMap[$accent];

        $paragraphs = $p['paragraphs'] ?? [];
        if (is_string($paragraphs)) $paragraphs = array_filter(preg_split('/\n\n+/', $paragraphs));

        $bgGradient = $p['bg_gradient'] ?? 'brand-emerald';
        $bgClass = [
            'none' => '',
            'amber-rose' => 'bg-gradient-to-br from-amber-50 via-white to-rose-50',
            'brand-emerald' => 'bg-gradient-to-br from-blue-50 via-white to-emerald-50',
            'rose-amber' => 'bg-gradient-to-br from-rose-50 via-white to-amber-50',
            'violet-teal' => 'bg-gradient-to-br from-violet-50 via-white to-teal-50',
            'teal-emerald' => 'bg-gradient-to-br from-teal-50 via-white to-emerald-50',
        ][$bgGradient] ?? '';

        // Title rendering with {{accent}}…{{/accent}} support
        $titleHtml = '';
        if (preg_match('/(.*?)\{\{accent\}\}(.+?)\{\{\/accent\}\}(.*)/s', $title, $m)) {
            $titleHtml = $this->e(trim($m[1]))
                . ($m[1] !== '' ? ' ' : '')
                . '<span class="' . $accentText . '">' . $this->e(trim($m[2])) . '</span>'
                . ($m[3] !== '' ? ' ' : '')
                . $this->e(trim($m[3]));
        } else {
            $titleHtml = $this->e($title);
        }

        // Breadcrumb
        $breadcrumbHtml = '';
        $breadcrumbs = $p['breadcrumbs'] ?? [];
        if (is_array($breadcrumbs) && !empty($breadcrumbs)) {
            $crumbs = [];
            $n = count($breadcrumbs);
            foreach ($breadcrumbs as $i => $b) {
                $label = $this->e((string) ($b['label'] ?? ''));
                $url = trim((string) ($b['url'] ?? ''));
                if ($label === '') continue;
                if ($i < $n - 1 && $url !== '') {
                    $crumbs[] = '<a href="' . $this->e($url) . '" class="hover:text-slate-700">' . $label . '</a>';
                } else {
                    $crumbs[] = '<span class="text-slate-700">' . $label . '</span>';
                }
            }
            $breadcrumbHtml = '<nav class="text-sm text-slate-500 mb-4">' . implode('<span class="mx-2">/</span>', $crumbs) . '</nav>';
        }

        // Stats pills
        $statsHtml = '';
        $pills = $p['stats_pills'] ?? [];
        if (is_array($pills) && !empty($pills)) {
            $statsHtml = '<div class="flex flex-wrap gap-3 mt-6">';
            foreach ($pills as $pill) {
                $label = $this->e((string) ($pill['label'] ?? ''));
                $valueSource = (string) ($pill['value_source'] ?? 'literal');
                $value = $this->resolveStatValueSimple($pill, $valueSource, $context);
                if ($value === null) continue;
                $statsHtml .= '<div class="px-4 py-2 rounded-full bg-white border border-slate-200 text-sm">'
                    . '<span class="font-bold ' . $accentText . '">' . $this->e($value) . '</span> '
                    . '<span class="text-slate-600">' . $label . '</span></div>';
            }
            $statsHtml .= '</div>';
        }

        // Typeahead search
        $searchHtml = $this->renderTypeaheadInline($p, $context, $accent);

        // Full-bleed hero: matches the original destinations layout
        // where the gradient section spans the full viewport width
        // and the content inside is constrained to max-w-7xl. The
        // .rg-dest-hero--bleed class uses vw-based negative margins
        // to break out of any parent container (the blocks view
        // wraps blocks in max-w-7xl mx-auto, so this breaks out).
        $out = '<section class="rg-dest-hero rg-dest-hero--bleed ' . $bgClass . ' mb-10">';
        $out .= '<style>.rg-dest-hero--bleed{margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw}</style>';
        $out .= '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">';
        $out .= $breadcrumbHtml;
        if ($eyebrow !== '') {
            $out .= '<div class="text-[11px] uppercase tracking-[0.2em] font-bold ' . $accentText . ' mb-3">' . $eyebrow . '</div>';
        }
        if ($titleHtml !== '') {
            $out .= '<h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-4 max-w-4xl">' . $titleHtml . '</h1>';
        }
        if (!empty($paragraphs)) {
            $out .= '<div class="text-lg text-slate-600 max-w-3xl space-y-3 [&_p]:m-0">';
            foreach ($paragraphs as $para) {
                $para = trim((string) $para);
                if ($para === '') continue;
                $out .= '<p>' . $this->e($para) . '</p>';
            }
            $out .= '</div>';
        }
        $out .= $statsHtml;
        $out .= $searchHtml;
        $out .= '</div></section>';
        return $out;
    }

    /**
     * Internal helper: render the typeahead search shell + CSS + JS
     * for use inside dest_hero_search. Inlined here instead of a
     * separate block so the search sits visually inside the hero
     * (the user spec'd them as one block).
     */
    private function renderTypeaheadInline(array $p, array $context, string $accent): string
    {
        $searchIndex = $context['searchIndex'] ?? [];
        if (is_object($searchIndex) && method_exists($searchIndex, 'toArray')) {
            $searchIndex = $searchIndex->toArray();
        }

        $placeholder = $this->e($p['search_placeholder'] ?? 'Where to? Try Cebu, Palawan, or Mayon');
        $boxId = 'rg-dest-ts-' . substr(md5(json_encode([$placeholder])), 0, 6);
        $dataId = $boxId . '-data';
        $panelId = $boxId . '-panel';

        $tabs = $p['search_tabs'] ?? [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'region', 'label' => 'Regions'],
            ['value' => 'destination', 'label' => 'Destinations'],
            ['value' => 'spot', 'label' => 'Tourist spots'],
        ];
        if (!is_array($tabs)) $tabs = [];

        $chips = $p['search_chips'] ?? ['Cebu', 'Palawan', 'Tagaytay', 'La Union', 'Boracay', 'Bicol'];
        if (!is_array($chips)) $chips = [];

        $labelsJson = $p['search_labels_json'] ?? '{"region":"Regions","destination":"Destinations","spot":"Tourist spots"}';
        if (is_array($labelsJson)) $labelsJson = json_encode($labelsJson);

        $emptyHint = $this->e($p['search_empty_hint'] ?? 'Try a region, a destination, or a spot name.');

        // Accent color drives focus + submit + chip hover
        $accentHex = [
            'brand' => '#2563eb', 'amber' => '#d97706', 'emerald' => '#059669',
            'rose' => '#e11d48', 'violet' => '#7c3aed', 'teal' => '#0d9488',
            'slate' => '#475569',
        ][$accent] ?? '#2563eb';

        // margin: 2.5rem auto 0 → centers the search bar within the
        // hero (matches the original /destinations layout). Without
        // auto margins the bar sits left-aligned under the H1.
        //
        // z-index sits at 10 (below the sticky site header which is
        // z-50). The result panel below uses z-20 — still above page
        // content but below the nav so the brand + utility links
        // never get covered when the typeahead is open.
        $out = '<div class="rg-dest-ts ' . $accent . '" data-rg-search style="margin:2.5rem auto 0;max-width:920px;width:100%;position:relative;z-index:10;">';

        // Filter tabs
        $out .= '<div class="rg-dest-ts__tabs" role="tablist">';
        foreach ($tabs as $i => $tab) {
            $val = $this->e((string) ($tab['value'] ?? 'all'));
            $lab = $this->e((string) ($tab['label'] ?? ''));
            $active = $i === 0 ? ' is-active' : '';
            $sel = $i === 0 ? 'true' : 'false';
            $out .= '<button type="button" class="rg-dest-ts__tab' . $active . '" role="tab" aria-selected="' . $sel . '" data-rg-filter="' . $val . '">' . $lab . '</button>';
        }
        $out .= '</div>';

        // Core input + panel
        $out .= '<div class="rg-dest-ts__core">';
        $out .= '<div class="rg-dest-ts__shell">';
        $out .= '<input id="' . $boxId . '" type="search" class="rg-dest-ts__input" placeholder="' . $placeholder . '" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="' . $panelId . '">';
        $out .= '<button type="button" class="rg-dest-ts__clear" aria-label="Clear" hidden>'
            . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>'
            . '</button>';
        $out .= '<button type="button" class="rg-dest-ts__submit" aria-label="Search">'
            . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>'
            . '</button>';
        $out .= '</div>';
        $out .= '<div class="rg-dest-ts__panel" id="' . $panelId . '" role="listbox" hidden></div>';
        $out .= '</div>';

        // Popular chips
        if (!empty($chips)) {
            $out .= '<div class="rg-dest-ts__chips" aria-hidden="true"><span>Popular:</span>';
            foreach ($chips as $chip) {
                $cval = $this->e((string) $chip);
                $out .= '<button type="button" class="rg-dest-ts__chip" data-rg-quick="' . $cval . '">' . $cval . '</button>';
            }
            $out .= '</div>';
        }
        $out .= '</div>';

        // CSS — accent-color-aware via CSS custom property
        $out .= '<style>'
            . '.rg-dest-ts{--rg-acc:' . $accentHex . '}'
            . '.rg-dest-ts__tabs{display:flex;flex-wrap:wrap;justify-content:center;gap:.5rem;margin-bottom:1.25rem}'
            . '.rg-dest-ts__tab{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;background:#fff;border:1.5px solid #e2e8f0;border-radius:999px;font-size:.875rem;font-weight:600;color:#475569;cursor:pointer;transition:all .15s ease}'
            . '.rg-dest-ts__tab:hover{border-color:#94a3b8;color:#0f172a;transform:translateY(-1px)}'
            . '.rg-dest-ts__tab.is-active{background:#0f172a;border-color:#0f172a;color:#fff;box-shadow:0 6px 14px -4px rgba(15,23,42,.35)}'
            . '@media(min-width:768px){.rg-dest-ts__tab{padding:.7rem 1.35rem;font-size:.95rem}}'
            . '.rg-dest-ts__core{position:relative}'
            . '.rg-dest-ts__shell{position:relative;display:flex;align-items:center;background:#fff;border:2px solid #0f172a;border-radius:999px;padding:.45rem .45rem .45rem 1.6rem;transition:border-color .18s ease}'
            . '@media(min-width:768px){.rg-dest-ts__shell{padding:.55rem .55rem .55rem 2.25rem}}'
            . '.rg-dest-ts__shell:focus-within{border-color:var(--rg-acc)}'
            . '.rg-dest-ts__input{flex:1 1 auto;min-width:0;background:transparent!important;border:0!important;outline:0!important;box-shadow:none!important;font-size:1.05rem;line-height:1.25;color:#0f172a;padding:.95rem .5rem .95rem 0;font-weight:500}'
            . '@media(min-width:768px){.rg-dest-ts__input{font-size:1.4rem;padding:1.15rem .5rem 1.15rem 0}}'
            . '.rg-dest-ts__input::placeholder{color:#94a3b8;font-weight:400}'
            . '.rg-dest-ts__input::-webkit-search-cancel-button{display:none!important;-webkit-appearance:none!important}'
            . '.rg-dest-ts__clear{flex:0 0 auto;width:2.4rem;height:2.4rem;border-radius:999px;background:#f1f5f9;color:#475569;border:0;cursor:pointer;display:flex;align-items:center;justify-content:center;margin-right:.55rem;transition:all .15s ease}'
            // hidden attribute must beat the display:flex above —
            // otherwise the X shows on load before any input.
            . '.rg-dest-ts__clear[hidden]{display:none!important}'
            . '.rg-dest-ts__clear svg{width:.95rem;height:.95rem}'
            . '.rg-dest-ts__clear:hover{background:#e2e8f0;color:#0f172a}'
            . '.rg-dest-ts__submit{flex:0 0 auto;width:3.4rem;height:3.4rem;border-radius:999px;background:#0f172a;color:#fff;border:0;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s ease}'
            . '@media(min-width:768px){.rg-dest-ts__submit{width:4rem;height:4rem}}'
            . '.rg-dest-ts__submit svg{width:1.45rem;height:1.45rem}'
            . '@media(min-width:768px){.rg-dest-ts__submit svg{width:1.6rem;height:1.6rem}}'
            . '.rg-dest-ts__submit:hover{background:var(--rg-acc);transform:scale(1.06)}'
            . '.rg-dest-ts__chips{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:.45rem;margin-top:1.1rem;font-size:.8rem;color:#64748b}'
            . '.rg-dest-ts__chips>span{font-weight:700;letter-spacing:.02em;margin-right:.25rem}'
            . '.rg-dest-ts__chip{background:rgba(255,255,255,.85);border:1.5px solid #e2e8f0;color:#334155;padding:.4rem .9rem;border-radius:999px;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .15s ease}'
            . '.rg-dest-ts__chip:hover{background:var(--rg-acc);border-color:var(--rg-acc);color:#fff;transform:translateY(-1px)}'
            . '.rg-dest-ts__panel{position:absolute;top:calc(100% + .75rem);left:0;right:0;max-height:32rem;overflow-y:auto;background:#fff;border:1px solid #e2e8f0;border-radius:1.25rem;box-shadow:0 30px 70px -20px rgba(15,23,42,.3),0 10px 24px -8px rgba(15,23,42,.15);padding:.6rem;z-index:20;animation:rgDestTsFade .18s ease-out}'
            . '@keyframes rgDestTsFade{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}'
            . '.rg-dest-ts__group-label{font-size:.7rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#94a3b8;padding:.75rem 1rem .4rem}'
            . '.rg-dest-ts__opt{display:flex;align-items:center;gap:1rem;padding:.8rem 1rem;border-radius:.85rem;cursor:pointer;text-decoration:none;color:inherit;transition:background .12s ease}'
            . '.rg-dest-ts__opt:hover,.rg-dest-ts__opt.is-active{background:color-mix(in srgb,var(--rg-acc) 8%,transparent)}'
            . '.rg-dest-ts__opt-thumb{flex:0 0 auto;width:3rem;height:3rem;border-radius:.7rem;background:color-mix(in srgb,var(--rg-acc) 10%,transparent);color:var(--rg-acc);display:flex;align-items:center;justify-content:center;overflow:hidden}'
            . '.rg-dest-ts__opt-thumb svg{width:1.35rem;height:1.35rem}'
            . '.rg-dest-ts__opt-body{flex:1 1 auto;min-width:0;display:flex;flex-direction:column;gap:.2rem}'
            . '.rg-dest-ts__opt-label{display:block;font-size:1rem;font-weight:700;color:#0f172a;text-transform:capitalize;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}'
            . '.rg-dest-ts__opt-label mark{background:rgba(252,211,77,.55);color:inherit;padding:0 .12em;border-radius:.25em}'
            . '.rg-dest-ts__opt-sub{display:block;font-size:.78rem;color:#64748b}'
            . '.rg-dest-ts__opt-chip{flex:0 0 auto;font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;background:#f1f5f9;padding:.2rem .55rem;border-radius:999px}'
            . '.rg-dest-ts__opt-arrow{flex:0 0 auto;color:#cbd5e1;width:1rem;height:1rem}'
            . '.rg-dest-ts__empty{padding:2rem 1.5rem;text-align:center;color:#64748b;font-size:.95rem;line-height:1.45}'
            . '</style>';

        // JSON data + JS
        $out .= '<script id="' . $dataId . '" type="application/json">' . json_encode($searchIndex, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';

        $emptyHintJs = json_encode($emptyHint);
        $labelsJsonJs = $labelsJson;
        $boxIdJs = json_encode($boxId);
        $dataIdJs = json_encode($dataId);

        $out .= '<script>(function(){'
            . 'var input=document.getElementById(' . $boxIdJs . ');'
            . 'if(!input)return;'
            . 'var root=input.closest("[data-rg-search]");'
            . 'var panel=root.querySelector(".rg-dest-ts__panel");'
            . 'var clearBtn=root.querySelector(".rg-dest-ts__clear");'
            . 'var chips=root.querySelectorAll(".rg-dest-ts__chip");'
            . 'var tabs=root.querySelectorAll(".rg-dest-ts__tab");'
            . 'var submitBtn=root.querySelector(".rg-dest-ts__submit");'
            . 'var dataEl=document.getElementById(' . $dataIdJs . ');'
            . 'var index=[];try{index=JSON.parse(dataEl.textContent)}catch(e){index=[]}'
            . 'var LABELS=' . $labelsJsonJs . ';'
            . 'var EMPTY=' . $emptyHintJs . ';'
            . 'var results=[],activeIdx=-1,debounceId=0,currentFilter="all";'
            . 'function esc(s){return String(s).replace(/[&<>"\']/g,function(c){return{"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","\'":"&#39;"}[c]})}'
            . 'function escRe(s){return s.replace(/[.*+?^${}()|[\\]\\\\]/g,"\\\\$&")}'
            . 'function hl(t,toks){var o=esc(t);for(var i=0;i<toks.length;i++){if(!toks[i])continue;var r=new RegExp("("+escRe(toks[i])+")","gi");o=o.replace(r,"<mark>$1</mark>")}return o}'
            . 'function scoreItem(it,q,toks){var h=(it.haystack||it.label||"").toLowerCase();if(h.indexOf(q)===0)return 1000;if(new RegExp("\\\\b"+escRe(q)).test(h))return 800;if(toks.every(function(t){return h.indexOf(t)!==-1}))return 500;if(h.indexOf(q)!==-1)return 300;return 0}'
            . 'function doSearch(q){var ql=q.toLowerCase().trim();if(!ql)return[];var toks=ql.split(/\\s+/).filter(Boolean);var scored=[];for(var i=0;i<index.length;i++){var it=index[i];if(currentFilter!=="all"&&it.type!==currentFilter)continue;var s=scoreItem(it,ql,toks);if(s>0)scored.push({it:it,s:s})}scored.sort(function(a,b){return(b.s-a.s)||((b.it.volume||0)-(a.it.volume||0))});return scored.slice(0,12).map(function(r){return r.it})}'
            . 'function iconFor(type,hasImage){if(hasImage)return"";if(type==="region")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3z"/><path d="M9 4v13M15 7v13"/></svg>\';if(type==="restaurant")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3v8m0 0v10M5 3v6a3 3 0 0 0 3 3"/><path d="M16 3v18M19 3v6a3 3 0 0 1-3 3"/></svg>\';if(type==="spot")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8z"/></svg>\';return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="8"/></svg>\'}'
            . 'function thumbStyleFor(item){if(!item.image)return"";var u=String(item.image).replace(/\'/g,"%27").replace(/"/g,"%22");return\' style="background-image:url(\\\'\'+u+\'\\\');background-size:cover;background-position:center;background-repeat:no-repeat"\'}'
            . 'function render(q){if(!results.length){panel.innerHTML=\'<div class="rg-dest-ts__empty">No matches for <strong>"\'+esc(q)+\'"</strong>.<br>\'+esc(EMPTY)+\'</div>\';panel.hidden=false;input.setAttribute("aria-expanded","true");return}var toks=q.toLowerCase().trim().split(/\\s+/).filter(Boolean);var parts=[],lastType=null,optIdx=0;for(var i=0;i<results.length;i++){var it=results[i];if(it.type!==lastType){parts.push(\'<div class="rg-dest-ts__group-label">\'+esc(LABELS[it.type]||it.type)+\'</div>\');lastType=it.type}parts.push(\'<a class="rg-dest-ts__opt" role="option" data-type="\'+esc(it.type)+\'" data-idx="\'+optIdx+\'" href="\'+esc(it.url)+\'"><span class="rg-dest-ts__opt-thumb"\'+thumbStyleFor(it)+\'>\'+iconFor(it.type,!!it.image)+\'</span><span class="rg-dest-ts__opt-body"><span class="rg-dest-ts__opt-label">\'+hl(it.label||"",toks)+\'</span>\'+(it.sub?\'<span class="rg-dest-ts__opt-sub">\'+esc(it.sub)+\'</span>\':"")+\'</span><span class="rg-dest-ts__opt-chip">\'+esc(LABELS[it.type]||it.type)+\'</span><svg class="rg-dest-ts__opt-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></a>\');optIdx++}panel.innerHTML=parts.join("");panel.hidden=false;input.setAttribute("aria-expanded","true");setActive(-1)}'
            . 'function close(){panel.hidden=true;input.setAttribute("aria-expanded","false");activeIdx=-1}'
            . 'function setActive(i){var opts=panel.querySelectorAll(".rg-dest-ts__opt");if(!opts.length)return;opts.forEach(function(o){o.classList.remove("is-active")});if(i<0||i>=opts.length){activeIdx=-1;return}activeIdx=i;opts[i].classList.add("is-active");opts[i].scrollIntoView({block:"nearest"})}'
            . 'function runSearch(q){results=doSearch(q);render(q)}'
            . 'input.addEventListener("input",function(e){var q=e.target.value;clearBtn.hidden=!q;clearTimeout(debounceId);if(!q.trim()){close();return}debounceId=setTimeout(function(){runSearch(q)},60)});'
            . 'input.addEventListener("focus",function(){if(input.value.trim())runSearch(input.value)});'
            . 'input.addEventListener("keydown",function(e){if(e.key==="ArrowDown"){if(panel.hidden&&input.value.trim())runSearch(input.value);e.preventDefault();var opts=panel.querySelectorAll(".rg-dest-ts__opt");setActive(Math.min(activeIdx+1,opts.length-1))}else if(e.key==="ArrowUp"){e.preventDefault();var opts=panel.querySelectorAll(".rg-dest-ts__opt");setActive(activeIdx<=0?opts.length-1:activeIdx-1)}else if(e.key==="Enter"){var opts=panel.querySelectorAll(".rg-dest-ts__opt");if(!opts.length)return;e.preventDefault();var target=activeIdx>=0?opts[activeIdx]:opts[0];if(target)window.location.href=target.getAttribute("href")}else if(e.key==="Escape"){if(input.value){input.value="";clearBtn.hidden=true}close()}});'
            . 'panel.addEventListener("mousedown",function(e){var opt=e.target.closest(".rg-dest-ts__opt");if(!opt)return;e.preventDefault();window.location.href=opt.getAttribute("href")});'
            . 'panel.addEventListener("mouseover",function(e){var opt=e.target.closest(".rg-dest-ts__opt");if(!opt)return;setActive(parseInt(opt.dataset.idx,10))});'
            . 'clearBtn.addEventListener("click",function(){input.value="";clearBtn.hidden=true;close();input.focus()});'
            . 'chips.forEach(function(c){c.addEventListener("click",function(){var q=c.dataset.rgQuick;input.value=q;clearBtn.hidden=false;input.focus();runSearch(q)})});'
            . 'tabs.forEach(function(t){t.addEventListener("click",function(){tabs.forEach(function(x){x.classList.remove("is-active");x.setAttribute("aria-selected","false")});t.classList.add("is-active");t.setAttribute("aria-selected","true");currentFilter=t.dataset.rgFilter||"all";if(input.value.trim())runSearch(input.value);input.focus()})});'
            . 'submitBtn.addEventListener("click",function(){var opts=panel.querySelectorAll(".rg-dest-ts__opt");if(opts.length){var target=activeIdx>=0?opts[activeIdx]:opts[0];window.location.href=target.getAttribute("href");return}if(input.value.trim())runSearch(input.value);input.focus()});'
            . 'document.addEventListener("click",function(e){if(!root.contains(e.target))close()});'
        . '})();</script>';

        return $out;
    }

    /**
     * Stats-value resolver shared by dest_hero_search. Reads from
     * either literal payload value or $context['stats'][key] or
     * a dotted context.{key}.count path.
     */
    private function resolveStatValueSimple(array $pill, string $source, array $context): ?string
    {
        if ($source === 'literal') {
            return isset($pill['value']) && $pill['value'] !== '' ? (string) $pill['value'] : null;
        }
        if (preg_match('/^stats\.(\w+)$/', $source, $m)) {
            $stats = $context['stats'] ?? [];
            if (!is_array($stats) || !isset($stats[$m[1]])) return null;
            $v = $stats[$m[1]];
            return is_numeric($v) ? number_format((float) $v) : (string) $v;
        }
        if (preg_match('/^context\.(\w+)\.count$/', $source, $m)) {
            $col = $context[$m[1]] ?? null;
            if ($col === null) return null;
            if (is_array($col)) return number_format(count($col));
            if (is_object($col) && method_exists($col, 'count')) return number_format($col->count());
            return null;
        }
        return null;
    }

    /**
     * dest_featured_slider — Splide carousel of featured items.
     * Reads $context[source]. Each card has gradient backdrop + image
     * + name + cuisine/category + city + link.
     *
     * Payload:
     *   eyebrow, heading, subhead
     *   source           → context key (default "featuredSpots")
     *   slides_per_view  → 1|2|3|4 (default 3 on lg, 2 on md, 1 on sm)
     *   autoplay         → bool (default true)
     *   interval         → ms (default 5000)
     *   bg               → light|none
     *   accent           → amber|brand|slate|rose|emerald
     */
    private function destFeaturedSlider(array $p, array $context): string
    {
        $source = (string) ($p['source'] ?? 'featuredSpots');
        $items = $context[$source] ?? null;
        $itemArr = is_array($items) ? $items : (is_object($items) && method_exists($items, 'all') ? $items->all() : []);
        if (empty($itemArr)) return '';

        $eyebrow = $this->e(trim($p['eyebrow'] ?? ''));
        $heading = $this->e(trim($p['heading'] ?? ''));
        $subhead = $this->e(trim($p['subhead'] ?? ''));
        $accent = in_array($p['accent'] ?? 'brand', ['amber', 'brand', 'slate', 'rose', 'emerald'], true)
            ? $p['accent'] : 'brand';
        $bg = ($p['bg'] ?? 'light') === 'light' ? 'bg-slate-50' : '';
        $autoplay = !empty($p['autoplay']) || !isset($p['autoplay']) ? 'true' : 'false';
        $interval = (int) ($p['interval'] ?? 5500);
        $perView = (int) ($p['slides_per_view'] ?? 3);
        $perView = max(1, min(4, $perView));
        $eyebrowClass = [
            'amber' => 'text-amber-700',
            'brand' => 'text-blue-700',
            'slate' => 'text-slate-600',
            'rose' => 'text-rose-700',
            'emerald' => 'text-emerald-700',
        ][$accent];

        $sliderId = 'rg-dfs-' . substr(md5(json_encode([$source, count($itemArr)])), 0, 6);

        // Full-bleed band that breaks out of the article wrapper so
        // the slate background extends across the viewport. The
        // heading stays centered inside a max-w-7xl column, but
        // the carousel itself extends to the section's full width
        // (only side padding clamps the cards from the edge) so the
        // slider feels as expansive as the band beneath it.
        $out = '<section class="rg-dest-fslider ' . $bg . ' my-10 py-12 md:py-16" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 text-center">';
        if ($eyebrow !== '') $out .= '<p class="text-xs sm:text-sm uppercase tracking-[0.18em] ' . $eyebrowClass . ' font-bold mb-2">' . $eyebrow . '</p>';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600 max-w-3xl mx-auto">' . $subhead . '</p>';
        $out .= '</div>';
        $out .= '<div class="px-4 sm:px-8 lg:px-12 xl:px-16">';

        // fixedWidth (instead of perPage) keeps each card at a stable
        // size; the carousel just shows more cards as the viewport
        // widens. perView still drives the desktop card width — at
        // perView=3 cards come out ~400px (matches the prior 3-up
        // look on a 1280px container), at perView=4 they shrink to
        // ~300px so 4 fit, etc. Clamped 280-700 so admin can't
        // accidentally pick a value that breaks on common viewports.
        $fixedWidthPx = max(280, min(700, (int) round(1200 / $perView)));
        $config = [
            'type' => 'loop',
            'autoplay' => $autoplay === 'true',
            'interval' => $interval,
            'arrows' => true,
            'pagination' => true,
            'gap' => '1rem',
            'fixedWidth' => $fixedWidthPx . 'px',
            'perMove' => 1,
            'breakpoints' => [
                // Phones: card peeks past the edge so the user knows
                // there's more to swipe.
                640 => ['fixedWidth' => '85vw', 'gap' => '0.75rem'],
            ],
        ];
        $configJson = htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8');

        $out .= '<section id="' . $sliderId . '" class="rg-dfs-splide splide" aria-label="Featured tourist spots" data-splide-config="' . $configJson . '">';
        $out .= '<div class="splide__track"><ul class="splide__list">';
        foreach ($itemArr as $r) {
            $rArr = $this->toArrayShapeSimple($r);

            // featuredSpots data shape: name / location / region /
            // image (relative storage path) / slug (keyword slug).
            // Also tolerate restaurant-style shape: name / cuisine /
            // city / hero_path / url.
            $name = $this->e((string) ($rArr['name'] ?? ''));
            $location = $this->e((string) ($rArr['location'] ?? $rArr['city'] ?? ''));
            $region = $this->e((string) ($rArr['region'] ?? $rArr['cuisine'] ?? $rArr['category'] ?? ''));
            $rawImage = (string) ($rArr['image'] ?? $rArr['hero_path'] ?? $rArr['image_path'] ?? '');
            $slug = (string) ($rArr['slug'] ?? '');
            $url = (string) ($rArr['url'] ?? ($slug !== '' ? url('/' . $slug) : '#'));
            // Rating + review count: optional. When present, render a
            // glass-style badge on the photo top-right + a review-count
            // suffix in the location row. Spots with no reviews omit
            // both — the card gracefully degrades.
            $rating = isset($rArr['rating']) && is_numeric($rArr['rating']) ? (float) $rArr['rating'] : null;
            $reviewCount = isset($rArr['review_count']) && is_numeric($rArr['review_count']) ? (int) $rArr['review_count'] : 0;
            if ($name === '') continue;

            $imgUrl = '';
            if ($rawImage !== '') {
                $imgUrl = str_starts_with($rawImage, 'http') || str_starts_with($rawImage, '/')
                    ? $rawImage : '/storage/' . ltrim($rawImage, '/');
            }

            $out .= '<li class="splide__slide">';
            $out .= '<a href="' . $this->e($url) . '" class="rg-dfs-card">';

            // Photo block — landscape 4:3, no text overlay so the
            // image gets to breathe. Optional rating badge floats on
            // the top-right corner with backdrop-blur for legibility
            // on any photo.
            $out .= '<div class="rg-dfs-img-wrap">';
            if ($imgUrl !== '') {
                $alt = $name . ($location !== '' ? ' in ' . $location : '');
                $out .= '<img src="' . $this->e($imgUrl) . '" alt="' . $this->e($alt) . '" loading="lazy" class="rg-dfs-img">';
            } else {
                $out .= '<div class="rg-dfs-img rg-dfs-img--fallback">';
                $out .= '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8z"/></svg>';
                $out .= '</div>';
            }
            $out .= '<div class="rg-dfs-img-inner-grad"></div>';
            $out .= '</div>';

            // Caption strip — white card body. Region eyebrow + name
            // + location row + rating row + Explore link.
            $out .= '<div class="rg-dfs-body">';
            if ($region !== '') $out .= '<span class="rg-dfs-region">' . $region . '</span>';
            $out .= '<h3 class="rg-dfs-name">' . $name . '</h3>';
            if ($location !== '') {
                $out .= '<div class="rg-dfs-location">'
                    . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8z"/></svg>'
                    . '<span>' . $location . '</span></div>';
            }
            // Rating row — sits directly under the location line.
            // Star icon + bold rating number + middot + review count.
            // Hidden gracefully when no review data exists.
            if ($rating !== null && $rating > 0) {
                $out .= '<div class="rg-dfs-rating" aria-label="Rated ' . number_format($rating, 1) . ' out of 5">'
                    . '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'
                    . '<strong>' . number_format($rating, 1) . '</strong>';
                if ($reviewCount > 0) {
                    $out .= '<span class="rg-dfs-rating-count">&middot; ' . number_format($reviewCount) . ' review' . ($reviewCount === 1 ? '' : 's') . '</span>';
                }
                $out .= '</div>';
            }
            $out .= '<div class="rg-dfs-explore">'
                . '<span>Explore stays</span>'
                . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</div>';
            $out .= '</div></a></li>';
        }
        $out .= '</ul></div></section>';

        // Editorial split card: 4:3 landscape photo on top, clean
        // white caption strip below. Photo can breathe (no dark
        // scrim), text is unambiguously readable, hover feels
        // considered (card lift + image zoom + Explore arrow nudge).
        $out .= '<style>'
            . '.rg-dfs-splide{overflow:visible}'
            . '.rg-dfs-splide .splide__list{align-items:stretch}'
            . '.rg-dfs-splide .splide__slide{padding:.25rem 0;display:flex}'
            . '.rg-dfs-splide .splide__arrow{background:rgba(15,23,42,.85);width:2.75rem;height:2.75rem;opacity:.95;box-shadow:0 8px 18px -6px rgba(15,23,42,.4)}'
            . '.rg-dfs-splide .splide__arrow:hover{background:#2563eb}'
            . '.rg-dfs-splide .splide__arrow svg{fill:#fff;width:1rem;height:1rem}'
            . '.rg-dfs-splide .splide__arrow--prev{left:-.5rem}'
            . '.rg-dfs-splide .splide__arrow--next{right:-.5rem}'
            . '@media(min-width:768px){.rg-dfs-splide .splide__arrow--prev{left:-1.25rem}.rg-dfs-splide .splide__arrow--next{right:-1.25rem}}'
            . '.rg-dfs-splide .splide__pagination{bottom:-2rem}'
            . '.rg-dfs-splide .splide__pagination__page{background:#cbd5e1;opacity:1;width:.6rem;height:.6rem;margin:0 .25rem}'
            . '.rg-dfs-splide .splide__pagination__page.is-active{background:#2563eb;transform:scale(1.25);width:1.5rem;border-radius:.6rem}'
            // Card: flex column so caption fills remaining height
            // when slides line up at uniform height.
            . '.rg-dfs-card{display:flex;flex-direction:column;width:100%;background:#fff;border:1px solid #e2e8f0;border-radius:1.1rem;overflow:hidden;box-shadow:0 1px 3px rgba(15,23,42,.05),0 1px 2px rgba(15,23,42,.04);transition:transform .4s cubic-bezier(.22,1,.36,1),box-shadow .4s ease,border-color .35s ease;text-decoration:none;color:inherit}'
            . '.rg-dfs-card:hover{transform:translateY(-6px);box-shadow:0 24px 48px -16px rgba(15,23,42,.22),0 8px 16px -8px rgba(15,23,42,.12);border-color:#cbd5e1}'
            // Photo block
            . '.rg-dfs-img-wrap{position:relative;aspect-ratio:4/3;background:#f1f5f9;overflow:hidden}'
            . '.rg-dfs-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .85s cubic-bezier(.22,1,.36,1)}'
            . '.rg-dfs-card:hover .rg-dfs-img{transform:scale(1.06)}'
            // Faint bottom-edge gradient on the photo (8% opacity max)
            // for subtle depth at the photo/caption boundary. Sits
            // above the image but below any future overlay slot.
            . '.rg-dfs-img-inner-grad{position:absolute;inset:auto 0 0 0;height:35%;background:linear-gradient(180deg,transparent 0%,rgba(15,23,42,.08) 100%);pointer-events:none}'
            . '.rg-dfs-img--fallback{display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#475569 0%,#334155 60%,#1e293b 100%);color:rgba(255,255,255,.4)}'
            . '.rg-dfs-img--fallback svg{width:3.5rem;height:3.5rem}'
            // Rating row — sits in the white card body directly
            // under the location line. Amber star + slate-900 bold
            // numeric + slate-500 review count. No backdrop blur
            // needed (it's on white), so the typography reads
            // straight as part of the editorial caption.
            . '.rg-dfs-rating{display:flex;align-items:center;gap:.35rem;font-size:.85rem;color:#0f172a;margin-bottom:.95rem;line-height:1.2}'
            . '.rg-dfs-rating svg{width:.95rem;height:.95rem;color:#f59e0b;flex:0 0 auto}'
            . '.rg-dfs-rating strong{font-weight:800;color:#0f172a;letter-spacing:.01em}'
            . '.rg-dfs-rating-count{color:#64748b;font-weight:500;margin-left:.1rem}'
            // Caption strip — clean editorial type. Padding ramps up
            // a touch on tablet+ so the body breathes.
            . '.rg-dfs-body{display:flex;flex-direction:column;flex:1;padding:1.1rem 1.25rem 1.15rem;gap:0}'
            . '@media(min-width:768px){.rg-dfs-body{padding:1.25rem 1.4rem 1.35rem}}'
            . '.rg-dfs-region{font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;font-weight:800;color:#2563eb;margin-bottom:.5rem;display:block}'
            . '.rg-dfs-name{font-size:1.25rem;font-weight:800;line-height:1.2;color:#0f172a;margin:0 0 .55rem;letter-spacing:-.01em}'
            . '@media(min-width:768px){.rg-dfs-name{font-size:1.4rem}}'
            . '.rg-dfs-location{display:flex;align-items:center;gap:.4rem;font-size:.85rem;color:#64748b;margin-bottom:.95rem}'
            . '.rg-dfs-location svg{width:.95rem;height:.95rem;flex:0 0 auto;color:#2563eb}'
            // Explore link — a subtle CTA divided by a hairline rule.
            // The arrow nudges on hover (gap grows) for a small
            // "go deeper" affordance.
            . '.rg-dfs-explore{display:flex;align-items:center;justify-content:space-between;font-size:.82rem;font-weight:700;color:#2563eb;border-top:1px solid #e2e8f0;padding-top:.75rem;margin-top:auto;letter-spacing:.01em}'
            . '.rg-dfs-explore svg{width:1rem;height:1rem;transition:transform .25s cubic-bezier(.22,1,.36,1);flex:0 0 auto}'
            . '.rg-dfs-card:hover .rg-dfs-explore svg{transform:translateX(.35rem)}'
            . '</style>';

        $out .= $this->splideAutoMount($sliderId);
        $out .= '</div></section>';
        return $out;
    }

    /**
     * dest_region_clusters — Jump-to-region sticky pill nav + cluster
     * grid of keyword cards. Reads $context[source] (default
     * "orderedClusters" — Eloquent collection of arrays with name,
     * cluster_tag, count, keywords).
     *
     * Payload:
     *   heading        → H2 above the cluster sections
     *   jump_label     → label for the jump-nav (default "Jump to")
     *   source         → orderedClusters | groups
     *   accent         → brand|amber|slate|teal|rose|violet
     *   show_volume    → bool
     *   sticky_nav     → bool (default true)
     */
    private function destRegionClusters(array $p, array $context): string
    {
        $source = (string) ($p['source'] ?? 'orderedClusters');
        $clusters = $context[$source] ?? null;
        if (!$clusters) return '';
        $clusterArr = is_array($clusters) ? $clusters : (is_object($clusters) && method_exists($clusters, 'all') ? $clusters->all() : []);
        if (empty($clusterArr)) return '';

        $heading = $this->e(trim($p['heading'] ?? 'Browse by region'));
        $jumpLabel = $this->e(trim($p['jump_label'] ?? 'Jump to'));
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'slate', 'teal', 'rose', 'violet'], true)
            ? $p['accent'] : 'brand';
        $showVolume = !isset($p['show_volume']) || (bool) $p['show_volume'];
        $stickyNav = !isset($p['sticky_nav']) || (bool) $p['sticky_nav'];

        $cardHoverMap = [
            'amber' => 'hover:border-amber-300 hover:shadow-md hover:bg-amber-50/30',
            'brand' => 'hover:border-blue-300 hover:shadow-md hover:bg-blue-50/30',
            'slate' => 'hover:border-slate-400 hover:shadow-md hover:bg-slate-50',
            'teal' => 'hover:border-teal-300 hover:shadow-md hover:bg-teal-50/30',
            'rose' => 'hover:border-rose-300 hover:shadow-md hover:bg-rose-50/30',
            'violet' => 'hover:border-violet-300 hover:shadow-md hover:bg-violet-50/30',
        ][$accent];
        $titleHoverMap = [
            'amber' => 'group-hover:text-amber-700',
            'brand' => 'group-hover:text-blue-700',
            'slate' => 'group-hover:text-slate-700',
            'teal' => 'group-hover:text-teal-700',
            'rose' => 'group-hover:text-rose-700',
            'violet' => 'group-hover:text-violet-700',
        ][$accent];
        $pillHoverMap = [
            'amber' => 'hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700',
            'brand' => 'hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700',
            'slate' => 'hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700',
            'teal' => 'hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700',
            'rose' => 'hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700',
            'violet' => 'hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700',
        ][$accent];

        $out = '<section class="rg-dest-clusters my-10">';

        // Sticky pill nav (Jump to ...)
        if ($stickyNav) {
            $out .= '<nav class="rg-dest-clusters__nav sticky top-16 z-10 bg-white/90 backdrop-blur border-y border-slate-200 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 mb-8">';
            $out .= '<div class="flex flex-wrap items-center gap-2 text-sm">';
            $out .= '<span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mr-1">' . $jumpLabel . '</span>';
            foreach ($clusterArr as $g) {
                $arr = $this->toArrayShapeSimple($g);
                $name = $this->e((string) ($arr['name'] ?? ''));
                $tag = (string) ($arr['cluster_tag'] ?? $arr['slug'] ?? $name);
                if ($name === '') continue;
                $slug = $this->e(\Illuminate\Support\Str::slug($tag));
                $out .= '<a href="#cluster-' . $slug . '" '
                    . 'class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white ' . $pillHoverMap . ' font-semibold text-slate-700 transition">' . $name . '</a>';
            }
            $out .= '</div></nav>';
        }

        // Section heading
        if ($heading !== '') {
            $out .= '<h2 class="text-xl font-bold text-slate-900 mb-4">' . $heading . '</h2>';
        }

        // Cluster sections
        foreach ($clusterArr as $g) {
            $arr = $this->toArrayShapeSimple($g);
            $name = $this->e((string) ($arr['name'] ?? ''));
            $tag = (string) ($arr['cluster_tag'] ?? $arr['slug'] ?? $name);
            $clusterSlug = $this->e(\Illuminate\Support\Str::slug($tag));
            $count = (int) ($arr['count'] ?? 0);
            $keywords = $arr['keywords'] ?? [];

            $out .= '<section id="cluster-' . $clusterSlug . '" class="mb-12 scroll-mt-32">';
            $out .= '<div class="flex items-end justify-between mb-3 flex-wrap gap-2"><div>';
            if ($name !== '') $out .= '<h3 class="text-2xl md:text-3xl font-bold text-slate-900">' . $name . '</h3>';
            if ($count > 0) {
                $countLabel = $source === 'groups' ? 'restaurant guide' : 'destination';
                $out .= '<p class="text-sm text-slate-500 mt-1">' . number_format($count) . ' ' . $countLabel . ($count === 1 ? '' : 's') . '</p>';
            }
            $out .= '</div></div>';
            $out .= '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">';
            foreach ($keywords as $k) {
                $kArr = $this->toArrayShapeSimple($k);
                $phrase = $this->e((string) ($kArr['phrase'] ?? ''));
                $slug = (string) ($kArr['slug'] ?? '');
                $volume = (int) ($kArr['search_volume_monthly'] ?? 0);
                if ($phrase === '' || $slug === '') continue;
                $out .= '<a href="' . $this->e(url($slug)) . '" '
                    . 'class="group block p-4 rounded-lg border border-slate-200 ' . $cardHoverMap . '">'
                    . '<h4 class="font-semibold text-slate-900 ' . $titleHoverMap . ' capitalize">' . $phrase . '</h4>';
                if ($showVolume && $volume > 0) {
                    $out .= '<p class="text-xs text-slate-500 mt-1">' . number_format($volume) . ' people search this monthly</p>';
                }
                $out .= '</a>';
            }
            $out .= '</div></section>';
        }

        $out .= '</section>';
        return $out;
    }

    /**
     * Shape-normalize: array | Model | object → array. Used by the
     * /destinations blocks above so they can read fields uniformly
     * regardless of whether the controller passes Eloquent models,
     * Collections, or plain arrays.
     */
    private function toArrayShapeSimple($item): array
    {
        if (is_array($item)) return $item;
        if (is_object($item) && method_exists($item, 'toArray')) return $item->toArray();
        return (array) $item;
    }

    /* ============================================================
     * Homepage custom block types
     * ============================================================
     * 6 blocks that reproduce the home/ page sections as builder
     * elements so /resort-guru-static can edit the homepage row.
     * Each reads its live data (featuredKeywords / regions /
     * featuredResorts / latestPosts / stats) from $context.
     * ============================================================ */

    /**
     * home_hero_centered — full-bleed gradient hero with centered
     * title + tagline + dual CTA buttons + 3-stat row. Matches the
     * original home page hero.
     *
     * Payload:
     *   title, tagline, bg_gradient (none|brand-emerald|amber-rose|...),
     *   primary_cta { label, url }, secondary_cta { label, url },
     *   stats [{ label, value, value_source }],
     *   accent (brand|amber|emerald|rose|violet|teal)
     */
    private function homeHeroCentered(array $p, array $context): string
    {
        foreach (['stats'] as $j) {
            if (isset($p[$j]) && is_string($p[$j])) {
                $t = trim($p[$j]);
                if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                    $d = json_decode($t, true);
                    if (is_array($d)) $p[$j] = $d;
                }
            }
        }
        $title = trim((string) ($p['title'] ?? ''));
        $tagline = $this->e((string) ($p['tagline'] ?? ''));
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $bgGradient = in_array($p['bg_gradient'] ?? 'brand-emerald', ['none', 'brand-emerald', 'amber-rose', 'rose-amber', 'violet-teal', 'teal-emerald'], true) ? $p['bg_gradient'] : 'brand-emerald';

        $bgClass = [
            'none' => '',
            'brand-emerald' => 'bg-gradient-to-br from-blue-50 via-white to-emerald-50',
            'amber-rose' => 'bg-gradient-to-br from-amber-50 via-white to-rose-50',
            'rose-amber' => 'bg-gradient-to-br from-rose-50 via-white to-amber-50',
            'violet-teal' => 'bg-gradient-to-br from-violet-50 via-white to-teal-50',
            'teal-emerald' => 'bg-gradient-to-br from-teal-50 via-white to-emerald-50',
        ][$bgGradient];
        $accentBg = ['brand' => 'bg-blue-600 hover:bg-blue-700', 'amber' => 'bg-amber-600 hover:bg-amber-700', 'emerald' => 'bg-emerald-600 hover:bg-emerald-700', 'rose' => 'bg-rose-600 hover:bg-rose-700', 'violet' => 'bg-violet-600 hover:bg-violet-700', 'teal' => 'bg-teal-600 hover:bg-teal-700'][$accent];

        $titleHtml = $title !== '' ? str_replace('|', '<br class="hidden md:block">', $this->e($title)) : '';
        $pCta = $p['primary_cta'] ?? [];
        if (is_string($pCta)) $pCta = json_decode($pCta, true) ?: [];
        $sCta = $p['secondary_cta'] ?? [];
        if (is_string($sCta)) $sCta = json_decode($sCta, true) ?: [];

        $out = '<section class="rg-home-hero ' . $bgClass . '" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">';
        if ($titleHtml !== '') $out .= '<h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 mb-5">' . $titleHtml . '</h1>';
        if ($tagline !== '') $out .= '<p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-8">' . $tagline . '</p>';
        if (!empty($pCta['label']) || !empty($sCta['label'])) {
            $out .= '<div class="flex flex-wrap items-center justify-center gap-3">';
            if (!empty($pCta['label'])) {
                $out .= '<a href="' . $this->e($pCta['url'] ?? '#') . '" class="px-6 py-3 rounded-md ' . $accentBg . ' text-white font-semibold transition">' . $this->e($pCta['label']) . '</a>';
            }
            if (!empty($sCta['label'])) {
                $out .= '<a href="' . $this->e($sCta['url'] ?? '#') . '" class="px-6 py-3 rounded-md bg-white border border-slate-300 font-semibold hover:bg-slate-50 transition">' . $this->e($sCta['label']) . '</a>';
            }
            $out .= '</div>';
        }

        $stats = $p['stats'] ?? [];
        if (is_array($stats) && !empty($stats)) {
            $out .= '<div class="mt-10 flex flex-wrap justify-center gap-8 text-sm text-slate-600">';
            foreach ($stats as $s) {
                $label = $this->e((string) ($s['label'] ?? ''));
                $value = $this->resolveStatValueSimple($s, (string) ($s['value_source'] ?? 'literal'), $context);
                if ($value === null && !empty($s['value'])) $value = (string) $s['value'];
                if ($value === null) continue;
                $out .= '<div><strong class="text-2xl text-slate-900 block">' . $this->e($value) . '</strong> ' . $label . '</div>';
            }
            $out .= '</div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * home_keyword_grid — "Popular destinations" 3-up grid of keyword
     * cards. Reads $context[source] (default featuredKeywords).
     *
     * Payload: heading, subhead, view_all { label, url },
     *          source, columns (2|3|4), accent
     */
    private function homeKeywordGrid(array $p, array $context): string
    {
        $source = (string) ($p['source'] ?? 'featuredKeywords');
        $items = $context[$source] ?? null;
        $itemArr = is_array($items) ? $items : (is_object($items) && method_exists($items, 'all') ? $items->all() : []);
        if (empty($itemArr)) return '';

        $heading = $this->e(trim($p['heading'] ?? ''));
        $subhead = $this->e(trim($p['subhead'] ?? ''));
        $columns = max(2, min(4, (int) ($p['columns'] ?? 3)));
        $gridClass = [2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-2 lg:grid-cols-3', 4 => 'sm:grid-cols-2 lg:grid-cols-4'][$columns];
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $accentText = ['brand' => 'text-blue-600 group-hover:text-blue-700', 'amber' => 'text-amber-600 group-hover:text-amber-700', 'emerald' => 'text-emerald-600', 'rose' => 'text-rose-600', 'violet' => 'text-violet-600', 'teal' => 'text-teal-600'][$accent];
        $borderHover = ['brand' => 'hover:border-blue-300', 'amber' => 'hover:border-amber-300', 'emerald' => 'hover:border-emerald-300', 'rose' => 'hover:border-rose-300', 'violet' => 'hover:border-violet-300', 'teal' => 'hover:border-teal-300'][$accent];
        $viewAll = $p['view_all'] ?? [];
        if (is_string($viewAll)) $viewAll = json_decode($viewAll, true) ?: [];

        $sectionId = 'rg-hkg-' . substr(md5(json_encode([$heading, $source])), 0, 6);
        // Inner max-w-6xl matches the other home_* blocks; without
        // it the grid spans the page-home wrapper's full width.
        $out = '<section id="' . $sectionId . '" class="py-16">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="flex items-end justify-between mb-8 flex-wrap gap-3"><div>';
        if ($heading !== '') $out .= '<h2 class="text-3xl font-bold text-slate-900">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-slate-600 mt-1">' . $subhead . '</p>';
        $out .= '</div>';
        if (!empty($viewAll['label']) && !empty($viewAll['url'])) {
            $out .= '<a href="' . $this->e($viewAll['url']) . '" class="font-semibold hover:underline whitespace-nowrap ' . $accentText . '">' . $this->e($viewAll['label']) . ' &rarr;</a>';
        }
        $out .= '</div>';
        $out .= '<div class="grid ' . $gridClass . ' gap-5">';
        foreach ($itemArr as $k) {
            $kArr = $this->toArrayShapeSimple($k);
            $phrase = $this->e((string) ($kArr['phrase'] ?? ''));
            $slug = (string) ($kArr['slug'] ?? '');
            if ($phrase === '' || $slug === '') continue;
            $out .= '<a href="' . $this->e(url($slug)) . '" class="block group rounded-xl border border-slate-200 ' . $borderHover . ' hover:shadow-md p-5 transition">'
                . '<h3 class="font-semibold text-slate-900 mb-2 capitalize ' . $accentText . '">' . $phrase . '</h3>'
                . '<p class="text-sm text-slate-500">Browse top picks &rarr;</p>'
                . '</a>';
        }
        // Closes: grid </div>, max-w-6xl wrapper </div>, </section>.
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_region_grid — region-cluster summary cards. Reads
     * $context[source] (default regions). Each item shape:
     * { slug, name, tagline, count }
     */
    private function homeRegionGrid(array $p, array $context): string
    {
        $source = (string) ($p['source'] ?? 'regions');
        $items = $context[$source] ?? null;
        $itemArr = is_array($items) ? $items : (is_object($items) && method_exists($items, 'all') ? $items->all() : []);
        if (empty($itemArr)) return '';

        $heading = $this->e(trim($p['heading'] ?? ''));
        $subhead = $this->e(trim($p['subhead'] ?? ''));
        $bg = ($p['bg'] ?? 'soft') === 'soft' ? 'bg-gradient-to-b from-white to-slate-50' : '';
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $accentText = ['brand' => 'group-hover:text-blue-600', 'amber' => 'group-hover:text-amber-600', 'emerald' => 'group-hover:text-emerald-600', 'rose' => 'group-hover:text-rose-600', 'violet' => 'group-hover:text-violet-600', 'teal' => 'group-hover:text-teal-600'][$accent];
        $accentPill = ['brand' => 'bg-blue-100 text-blue-700', 'amber' => 'bg-amber-100 text-amber-700', 'emerald' => 'bg-emerald-100 text-emerald-700', 'rose' => 'bg-rose-100 text-rose-700', 'violet' => 'bg-violet-100 text-violet-700', 'teal' => 'bg-teal-100 text-teal-700'][$accent];
        $borderHover = ['brand' => 'hover:border-blue-300', 'amber' => 'hover:border-amber-300', 'emerald' => 'hover:border-emerald-300', 'rose' => 'hover:border-rose-300', 'violet' => 'hover:border-violet-300', 'teal' => 'hover:border-teal-300'][$accent];
        $viewAll = $p['view_all'] ?? [];
        if (is_string($viewAll)) $viewAll = json_decode($viewAll, true) ?: [];

        $out = '<section class="py-16 ' . $bg . '" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="flex items-end justify-between mb-8 flex-wrap gap-3"><div>';
        if ($heading !== '') $out .= '<h2 class="text-3xl font-bold text-slate-900">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-slate-600 mt-1">' . $subhead . '</p>';
        $out .= '</div>';
        if (!empty($viewAll['label']) && !empty($viewAll['url'])) {
            $out .= '<a href="' . $this->e($viewAll['url']) . '" class="font-semibold hover:underline whitespace-nowrap text-blue-600">' . $this->e($viewAll['label']) . ' &rarr;</a>';
        }
        $out .= '</div>';
        $out .= '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">';
        foreach ($itemArr as $r) {
            $rArr = $this->toArrayShapeSimple($r);
            $name = $this->e((string) ($rArr['name'] ?? ''));
            $tagline = $this->e((string) ($rArr['tagline'] ?? ''));
            $count = (int) ($rArr['count'] ?? 0);
            $slug = (string) ($rArr['slug'] ?? '');
            if ($name === '' || $slug === '') continue;
            $url = url('/destinations/' . $slug);
            $out .= '<a href="' . $this->e($url) . '" class="block group rounded-xl bg-white border border-slate-200 ' . $borderHover . ' hover:shadow-md p-5 transition">'
                . '<div class="flex items-start justify-between mb-2 gap-2">'
                . '<h3 class="font-bold text-lg text-slate-900 ' . $accentText . '">' . $name . '</h3>';
            if ($count > 0) $out .= '<span class="text-xs px-2 py-1 rounded-full ' . $accentPill . ' font-semibold whitespace-nowrap">' . $count . '</span>';
            $out .= '</div>';
            if ($tagline !== '') $out .= '<p class="text-sm text-slate-500 line-clamp-2">' . $tagline . '</p>';
            $out .= '</a>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_resort_grid — Featured properties grid with hero images.
     * Reads $context[source] (default featuredResorts).
     */
    private function homeResortGrid(array $p, array $context): string
    {
        $source = (string) ($p['source'] ?? 'featuredResorts');
        $items = $context[$source] ?? null;
        $itemArr = is_array($items) ? $items : (is_object($items) && method_exists($items, 'all') ? $items->all() : []);
        if (empty($itemArr)) return '';

        $heading = $this->e(trim($p['heading'] ?? ''));
        $subhead = $this->e(trim($p['subhead'] ?? ''));
        $bg = ($p['bg'] ?? 'slate') === 'slate' ? 'bg-slate-50' : '';

        $out = '<section class="py-16 ' . $bg . '" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        if ($heading !== '') $out .= '<h2 class="text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-slate-600 mb-8">' . $subhead . '</p>';
        $out .= '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">';
        foreach ($itemArr as $r) {
            $rArr = $this->toArrayShapeSimple($r);
            $name = $this->e((string) ($rArr['name'] ?? ''));
            $slug = (string) ($rArr['slug'] ?? '');
            $city = $this->e((string) ($rArr['city'] ?? ''));
            $province = $this->e((string) ($rArr['province'] ?? ''));
            $heroPath = (string) ($rArr['hero_path'] ?? '');
            if ($name === '' || $slug === '') continue;
            $resortUrl = url('/listing/' . $slug);
            $out .= '<a href="' . $this->e($resortUrl) . '" class="block group rounded-xl overflow-hidden bg-white border border-slate-200 hover:shadow-lg transition">';
            $out .= '<div class="aspect-[4/3] bg-slate-200 overflow-hidden">';
            if ($heroPath !== '') {
                $img = str_starts_with($heroPath, '/') || str_starts_with($heroPath, 'http') ? $heroPath : '/storage/' . ltrim($heroPath, '/');
                $out .= '<img src="' . $this->e($img) . '" alt="' . $name . '" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">';
            } else {
                $out .= '<div class="w-full h-full flex items-center justify-center text-5xl">🏖️</div>';
            }
            $out .= '</div><div class="p-4">';
            $out .= '<h3 class="font-semibold text-slate-900 mb-1">' . $name . '</h3>';
            $loc = trim($city . ($city !== '' && $province !== '' ? ', ' : '') . $province);
            if ($loc !== '') $out .= '<p class="text-sm text-slate-500">' . $loc . '</p>';
            $out .= '</div></a>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_blog_strip — "From the blog" 3-up post cards. Reads
     * $context[source] (default latestPosts).
     */
    private function homeBlogStrip(array $p, array $context): string
    {
        $source = (string) ($p['source'] ?? 'latestPosts');
        $items = $context[$source] ?? null;
        $itemArr = is_array($items) ? $items : (is_object($items) && method_exists($items, 'all') ? $items->all() : []);
        if (empty($itemArr)) return '';

        $heading = $this->e(trim($p['heading'] ?? ''));
        $subhead = $this->e(trim($p['subhead'] ?? ''));
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $accentText = ['brand' => 'group-hover:text-blue-600', 'amber' => 'group-hover:text-amber-600', 'emerald' => 'group-hover:text-emerald-600', 'rose' => 'group-hover:text-rose-600', 'violet' => 'group-hover:text-violet-600', 'teal' => 'group-hover:text-teal-600'][$accent];

        $out = '<section class="py-16">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        if ($heading !== '') $out .= '<h2 class="text-3xl font-bold text-slate-900 mb-2">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-slate-600 mb-8">' . $subhead . '</p>';
        $out .= '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">';
        foreach ($itemArr as $post) {
            $pArr = $this->toArrayShapeSimple($post);
            $title = $this->e((string) ($pArr['title'] ?? ''));
            $slug = (string) ($pArr['slug'] ?? '');
            $excerpt = $this->e((string) ($pArr['excerpt'] ?? ''));
            $coverPath = (string) ($pArr['cover_path'] ?? '');
            if ($title === '' || $slug === '') continue;
            $postUrl = url('/blog/' . $slug);
            $out .= '<a href="' . $this->e($postUrl) . '" class="block group">';
            $out .= '<div class="aspect-[16/10] rounded-lg bg-slate-200 mb-3 overflow-hidden">';
            if ($coverPath !== '') {
                $img = str_starts_with($coverPath, '/') || str_starts_with($coverPath, 'http') ? $coverPath : '/storage/' . ltrim($coverPath, '/');
                $out .= '<img src="' . $this->e($img) . '" alt="' . $title . '" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">';
            }
            $out .= '</div>';
            $out .= '<h3 class="font-semibold text-slate-900 mb-1 ' . $accentText . '">' . $title . '</h3>';
            if ($excerpt !== '') $out .= '<p class="text-sm text-slate-500 line-clamp-2">' . $excerpt . '</p>';
            $out .= '</a>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_cta_band — full-bleed brand-colored band with H2 +
     * paragraph + single CTA button.
     */
    private function homeCtaBand(array $p, array $context): string
    {
        $heading = $this->e(trim($p['heading'] ?? ''));
        $body = $this->e(trim($p['body'] ?? ''));
        $cta = $p['cta'] ?? [];
        if (is_string($cta)) $cta = json_decode($cta, true) ?: [];
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal', 'slate'], true) ? $p['accent'] : 'brand';

        $bandMap = ['brand' => ['bg' => 'bg-blue-600', 'sub' => 'text-blue-100', 'btn' => 'text-blue-700 hover:bg-blue-50'], 'amber' => ['bg' => 'bg-amber-600', 'sub' => 'text-amber-100', 'btn' => 'text-amber-700 hover:bg-amber-50'], 'emerald' => ['bg' => 'bg-emerald-600', 'sub' => 'text-emerald-100', 'btn' => 'text-emerald-700 hover:bg-emerald-50'], 'rose' => ['bg' => 'bg-rose-600', 'sub' => 'text-rose-100', 'btn' => 'text-rose-700 hover:bg-rose-50'], 'violet' => ['bg' => 'bg-violet-600', 'sub' => 'text-violet-100', 'btn' => 'text-violet-700 hover:bg-violet-50'], 'teal' => ['bg' => 'bg-teal-600', 'sub' => 'text-teal-100', 'btn' => 'text-teal-700 hover:bg-teal-50'], 'slate' => ['bg' => 'bg-slate-800', 'sub' => 'text-slate-300', 'btn' => 'text-slate-800 hover:bg-slate-100']][$accent];

        $out = '<section class="' . $bandMap['bg'] . ' py-16" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-bold mb-4">' . $heading . '</h2>';
        if ($body !== '') $out .= '<p class="' . $bandMap['sub'] . ' mb-6 text-lg">' . $body . '</p>';
        if (!empty($cta['label'])) {
            $out .= '<a href="' . $this->e($cta['url'] ?? '#') . '" class="inline-block px-7 py-3 rounded-md bg-white font-bold transition ' . $bandMap['btn'] . '">' . $this->e($cta['label']) . '</a>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /* ============================================================
     * Phase-2 homepage block types
     * ============================================================
     * Editorial intro, experience grid, hub-link cards, season
     * guide, testimonials, FAQ. Content seeded from Fable 5
     * (homepage-content-gen workflow).
     * ============================================================ */

    /**
     * home_unified_search — search-first hero block. Big title +
     * tagline + filter tabs + powerful typeahead pill + popular
     * chips + stats row. Reads the cross-site search index from
     * $context['unifiedSearchIndex'] (App\Services\UnifiedSearchIndex).
     *
     * Indexes: regions, destination pages, resorts, restaurants,
     * tourist spots, blog posts — ~1,100+ items total. Result panel
     * groups them and renders photos when available.
     *
     * Payload:
     *   eyebrow, title (with {{accent}} markers),
     *   tagline (string),
     *   accent (brand|amber|emerald|rose|violet|teal),
     *   bg_gradient (brand-emerald|amber-rose|...|none),
     *   placeholder,
     *   tabs [{ value, label }],
     *   chips [string],
     *   labels_json (type → group-header label map JSON string),
     *   empty_hint,
     *   stats [{ label, value, value_source }]
     */
    private function homeUnifiedSearch(array $p, array $context): string
    {
        foreach (['tabs', 'chips', 'stats'] as $j) {
            if (isset($p[$j]) && is_string($p[$j])) {
                $t = trim($p[$j]);
                if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                    $d = json_decode($t, true);
                    if (is_array($d)) $p[$j] = $d;
                }
            }
        }
        $searchIndex = $context['unifiedSearchIndex'] ?? [];
        if (is_object($searchIndex) && method_exists($searchIndex, 'toArray')) {
            $searchIndex = $searchIndex->toArray();
        }

        $eyebrow = $this->e(trim((string) ($p['eyebrow'] ?? '')));
        $title = trim((string) ($p['title'] ?? ''));
        $tagline = $this->e(trim((string) ($p['tagline'] ?? '')));
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $bgGradient = $p['bg_gradient'] ?? 'brand-emerald';
        $bgClass = [
            'none' => '',
            'brand-emerald' => 'bg-gradient-to-br from-blue-50 via-white to-emerald-50',
            'amber-rose' => 'bg-gradient-to-br from-amber-50 via-white to-rose-50',
            'rose-amber' => 'bg-gradient-to-br from-rose-50 via-white to-amber-50',
            'violet-teal' => 'bg-gradient-to-br from-violet-50 via-white to-teal-50',
            'teal-emerald' => 'bg-gradient-to-br from-teal-50 via-white to-emerald-50',
        ][$bgGradient] ?? '';
        $accentText = ['brand' => 'text-blue-700', 'amber' => 'text-amber-700', 'emerald' => 'text-emerald-700', 'rose' => 'text-rose-700', 'violet' => 'text-violet-700', 'teal' => 'text-teal-700'][$accent];
        $accentHex = ['brand' => '#2563eb', 'amber' => '#d97706', 'emerald' => '#059669', 'rose' => '#e11d48', 'violet' => '#7c3aed', 'teal' => '#0d9488'][$accent];
        // The brush underline color is independent of the accent
        // (which still drives the Search button bg, hover state,
        // etc). Defaults to accent hex but can be overridden via
        // the curve_color payload field for a totally different
        // brush tint.
        $curveColor = trim((string) ($p['curve_color'] ?? ''));
        if ($curveColor === '' || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $curveColor)) {
            $curveColor = $accentHex;
        }

        // Title with {{accent}}...{{/accent}} support, optional
        // line-break before the accent span, and optional curved
        // SVG underline on a chosen word in the pre-accent text.
        $breakBefore = (bool) ($p['title_break_before_accent'] ?? false);
        $curveWord = trim((string) ($p['title_curve_word'] ?? ''));
        $titleHtml = '';
        if ($title !== '') {
            if (preg_match('/(.*?)\{\{accent\}\}(.+?)\{\{\/accent\}\}(.*)/s', $title, $m)) {
                $preText = $this->e(trim($m[1]));
                if ($curveWord !== '' && $preText !== '') {
                    $escCurve = preg_quote($curveWord, '/');
                    // Filled brush silhouette (not stroked) — true
                    // brush taper: thin at BOTH tips, thick in the
                    // middle. Shape is a closed path: top edge cubic
                    // from (8,7) → (225,8), vertical right tip (225,8)
                    // → (225,10), bottom edge cubic back to (8,9),
                    // implicit close to (8,7).
                    // Widths along the brush:
                    //   left tip  (x=8):    9-7 = 2 units (thin)
                    //   apex      (x~104):  bottom y(.5) - top y(.5)
                    //                       = 27.5 - 21.75 = 5.75
                    //                       units (thick middle)
                    //   right tip (x=225):  10-8 = 2 units (thin)
                    // Controls pushed deeper (y=25/28 top, y=32/35
                    // bottom) for a more visible arc — brush apex
                    // now sits near the bottom of viewBox 28.
                    $curveSpan = '<span class="rg-uss-curve">' . $this->e($curveWord)
                        . '<svg class="rg-uss-curve-svg" viewBox="0 0 240 28" aria-hidden="true">'
                        . '<path d="M 8 7 C 60 25 140 28 225 8 L 225 10 C 140 35 60 32 8 9 Z" fill="currentColor"/>'
                        . '</svg>'
                        . '</span>';
                    $preText = preg_replace('/\b' . $escCurve . '\b/u', $curveSpan, $preText, 1);
                }
                $separator = $breakBefore ? '<span class="rg-uss-title-break" aria-hidden="true"></span>' : ($m[1] !== '' ? ' ' : '');
                $titleHtml = $preText
                    . $separator
                    . '<span class="' . $accentText . '">' . $this->e(trim($m[2])) . '</span>'
                    . ($m[3] !== '' ? ' ' : '')
                    . $this->e(trim($m[3]));
            } else {
                $titleHtml = $this->e($title);
            }
        }

        $placeholder = $this->e((string) ($p['placeholder'] ?? 'Try Coron, lechon, Sinulog, El Nido…'));
        $tabs = $p['tabs'] ?? [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'destination', 'label' => 'Destinations'],
            ['value' => 'resort', 'label' => 'Stays'],
            ['value' => 'restaurant', 'label' => 'Food'],
            ['value' => 'spot', 'label' => 'Spots'],
            ['value' => 'region', 'label' => 'Regions'],
            ['value' => 'blog', 'label' => 'Blog'],
        ];
        if (!is_array($tabs)) $tabs = [];
        $chips = $p['chips'] ?? ['Cebu', 'Palawan', 'Tagaytay', 'Boracay', 'Siargao', 'Vigan', 'Mt. Pulag'];
        if (!is_array($chips)) $chips = [];
        $labelsJson = $p['labels_json'] ?? '{"destination":"Destinations","resort":"Stays","restaurant":"Food finds","spot":"Tourist spots","region":"Regions","blog":"Blog"}';
        if (is_array($labelsJson)) $labelsJson = json_encode($labelsJson);
        $emptyHint = $this->e((string) ($p['empty_hint'] ?? 'Try a region, a city name, a dish, or a spot like Mt. Pulag.'));

        $boxId = 'rg-uss-' . substr(md5(json_encode([$title])), 0, 6);
        $dataId = $boxId . '-data';
        $panelId = $boxId . '-panel';

        // Optional background image. When set, a darker gradient
        // overlay covers the image and the eyebrow/title/tagline
        // switch from slate-900 to white for legibility.
        $bgImage = trim((string) ($p['background_image'] ?? ''));
        $bgImageUrl = '';
        if ($bgImage !== '') {
            $bgImageUrl = str_starts_with($bgImage, 'http') || str_starts_with($bgImage, '/')
                ? $bgImage : '/storage/' . ltrim($bgImage, '/');
        }
        $hasBgImage = $bgImageUrl !== '';

        // Optional background video. Accepts a YouTube URL (watch,
        // share, youtu.be, or /embed/ form). When set, a muted+looped
        // iframe sits behind the content with a heavy white scrim so
        // the text stays slate-900 (the video reads as a faded back-
        // ground watermark, not a hero photo).
        $bgVideo = trim((string) ($p['background_video'] ?? ''));
        $bgVideoId = '';
        if ($bgVideo !== '') {
            if (preg_match('#(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $bgVideo, $vm)) {
                $bgVideoId = $vm[1];
            } elseif (preg_match('/^[A-Za-z0-9_-]{11}$/', $bgVideo)) {
                $bgVideoId = $bgVideo;
            }
        }
        $hasBgVideo = $bgVideoId !== '';

        // No overflow:hidden on the section itself — .rg-uss-video
        // already clips the oversized iframe internally, and
        // overflow:hidden here would also clip the typeahead panel
        // that drops down below the search shell.
        $sectionStyle = 'margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw;position:relative';
        if ($hasBgImage) {
            $sectionStyle .= ';background-image:url(\'' . $this->e($bgImageUrl) . '\');background-size:cover;background-position:center';
        }
        // Text color flips to white only for photo backgrounds. The
        // video uses a light white scrim that fades the footage but
        // keeps slate-900 text readable through it.
        $titleColor = $hasBgImage ? 'text-white' : 'text-slate-900';
        $taglineColor = $hasBgImage ? 'text-white/90' : 'text-slate-600';
        $eyebrowColor = $hasBgImage ? 'text-white/85' : $accentText;
        $statsValueColor = $hasBgImage ? 'text-white' : 'text-slate-900';
        $statsLabelColor = $hasBgImage ? 'text-white/80' : 'text-slate-600';

        $out = '<section class="rg-uss ' . $bgClass . '" style="' . $sectionStyle . '">';
        // Photo-mode dark overlay — enough to keep large titles readable
        // on any photo without muddying the image too much. Title block
        // gets a slight extra text-shadow for crispness.
        if ($hasBgImage) {
            $out .= '<div class="absolute inset-0 pointer-events-none" style="background:linear-gradient(180deg,rgba(15,23,42,0.55) 0%,rgba(15,23,42,0.35) 30%,rgba(15,23,42,0.55) 100%)"></div>';
        }
        // Video-mode background — muted, looped, no controls,
        // pointer-events disabled. The iframe is over-sized so that
        // its 16:9 aspect covers any section aspect ratio. A heavy
        // white scrim above it fades the video so the slate-900 text
        // remains crisp and the page still reads as a clean hero.
        if ($hasBgVideo) {
            $ytStart = max(0, (int) ($p['video_start_seconds'] ?? 10));
            $ytSrc = 'https://www.youtube.com/embed/' . $bgVideoId
                . '?autoplay=1&mute=1&loop=1&playlist=' . $bgVideoId
                . '&controls=0&showinfo=0&modestbranding=1&rel=0'
                . '&playsinline=1&iv_load_policy=3&disablekb=1&fs=0'
                . '&start=' . $ytStart;
            $out .= '<div class="rg-uss-video" aria-hidden="true">';
            $out .= '<iframe src="' . $this->e($ytSrc) . '" frameborder="0"'
                . ' allow="autoplay; encrypted-media; picture-in-picture"'
                . ' loading="eager" referrerpolicy="strict-origin-when-cross-origin"'
                . ' title=""></iframe>';
            $out .= '</div>';
            $out .= '<div class="rg-uss-video-scrim" aria-hidden="true"></div>';
            // Init overlay — solid white for ~600ms then fades over
            // ~900ms. Masks the YouTube loading state (big center
            // play button and any control flicker) until the muted
            // autoplay has actually started.
            $out .= '<div class="rg-uss-video-init" aria-hidden="true"></div>';
        }
        $out .= '<div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">';
        if ($eyebrow !== '') $out .= '<div class="text-[13px] uppercase tracking-[0.22em] font-bold ' . $eyebrowColor . ' mb-4">' . $eyebrow . '</div>';
        if ($titleHtml !== '') $out .= '<h1 class="text-4xl md:text-6xl font-extrabold tracking-tight ' . $titleColor . ' mb-4 leading-[1.5] md:leading-[1.4]"' . ($hasBgImage ? ' style="text-shadow:0 2px 16px rgba(0,0,0,0.45)"' : '') . '>' . $titleHtml . '</h1>';
        if ($tagline !== '') $out .= '<p class="text-lg md:text-xl ' . $taglineColor . ' max-w-2xl mx-auto mb-8">' . $tagline . '</p>';

        // Search shell
        $out .= '<div class="rg-uss-search" data-rg-search>';
        $out .= '<div class="rg-uss__tabs" role="tablist">';
        foreach ($tabs as $i => $tab) {
            $val = $this->e((string) ($tab['value'] ?? 'all'));
            $lab = $this->e((string) ($tab['label'] ?? ''));
            $active = $i === 0 ? ' is-active' : '';
            $sel = $i === 0 ? 'true' : 'false';
            $out .= '<button type="button" class="rg-uss__tab' . $active . '" role="tab" aria-selected="' . $sel . '" data-rg-filter="' . $val . '">' . $lab . '</button>';
        }
        $out .= '</div>';
        $out .= '<div class="rg-uss__core">';
        $out .= '<div class="rg-uss__shell">';
        $out .= '<svg class="rg-uss__pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>';
        $out .= '<input id="' . $boxId . '" type="search" class="rg-uss__input" placeholder="' . $placeholder . '" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="' . $panelId . '">';
        $out .= '<button type="button" class="rg-uss__clear" aria-label="Clear" hidden><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>';
        $out .= '<button type="button" class="rg-uss__submit" aria-label="Search">Search</button>';
        // Alt CTA inside the shell, after Search. "or" sits as
        // plain text between Search and the red pill so the pill
        // reads as a clean "Create Your Adventure" CTA on its own.
        // Right-pointing arrow inside the pill nudges right
        // periodically. Click focuses the search input. On small
        // screens the "or" text + button label both hide; only
        // the animated arrow shows (aria-label preserves context).
        $out .= '<span class="rg-uss__alt-or" aria-hidden="true">or</span>';
        $out .= '<button type="button" class="rg-uss__alt" aria-label="Create Your Adventure" onclick="document.getElementById(\'' . $boxId . '\').focus()">';
        $out .= '<span class="rg-uss__alt-text">Create Your Adventure</span>';
        $out .= '<svg class="rg-uss__alt-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
        $out .= '</button>';
        $out .= '</div>';
        $out .= '<div class="rg-uss__panel" id="' . $panelId . '" role="listbox" hidden></div>';
        $out .= '</div>';
        if (!empty($chips)) {
            $out .= '<div class="rg-uss__chips" aria-hidden="true"><span>Popular:</span>';
            foreach ($chips as $chip) {
                $cval = $this->e((string) $chip);
                $out .= '<button type="button" class="rg-uss__chip" data-rg-quick="' . $cval . '">' . $cval . '</button>';
            }
            $out .= '</div>';
        }
        $out .= '</div>';

        // Stats row
        $stats = $p['stats'] ?? [];
        if (is_array($stats) && !empty($stats)) {
            $out .= '<div class="mt-10 flex flex-wrap justify-center gap-8 text-sm ' . $statsLabelColor . '">';
            foreach ($stats as $s) {
                $label = $this->e((string) ($s['label'] ?? ''));
                $value = $this->resolveStatValueSimple($s, (string) ($s['value_source'] ?? 'literal'), $context);
                if ($value === null && !empty($s['value'])) $value = (string) $s['value'];
                if ($value === null) continue;
                $out .= '<div><strong class="text-2xl ' . $statsValueColor . ' block">' . $this->e($value) . '</strong> ' . $label . '</div>';
            }
            $out .= '</div>';
        }
        $out .= '</div>';

        // CSS — search styling that matches modern travel sites
        $out .= '<style>'
            . '.rg-uss-search{max-width:1000px;width:100%;margin:0 auto;position:relative;z-index:10;--rg-acc:' . $accentHex . '}'
            . '.rg-uss__tabs{display:flex;flex-wrap:wrap;justify-content:center;gap:.4rem;margin-bottom:1.1rem}'
            . '.rg-uss__tab{display:inline-flex;align-items:center;padding:.55rem 1.05rem;background:rgba(255,255,255,.85);border:1.5px solid #e2e8f0;border-radius:999px;font-size:.85rem;font-weight:600;color:#475569;cursor:pointer;transition:all .15s ease;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}'
            . '.rg-uss__tab:hover{border-color:#94a3b8;color:#0f172a;transform:translateY(-1px)}'
            . '.rg-uss__tab.is-active{background:#0f172a;border-color:#0f172a;color:#fff;box-shadow:0 6px 14px -4px rgba(15,23,42,.35)}'
            . '@media(min-width:768px){.rg-uss__tab{padding:.65rem 1.25rem;font-size:.92rem}}'
            . '.rg-uss__core{position:relative}'
            . '.rg-uss__shell{position:relative;display:flex;align-items:center;background:#fff;border:2px solid #0f172a;border-radius:999px;padding:.45rem;transition:border-color .18s ease;box-shadow:0 12px 24px -12px rgba(15,23,42,.18)}'
            . '@media(min-width:768px){.rg-uss__shell{padding:.55rem}}'
            . '.rg-uss__shell:focus-within{border-color:var(--rg-acc)}'
            . '.rg-uss__pin{flex:0 0 auto;width:1.3rem;height:1.3rem;color:#475569;margin-left:1rem;margin-right:.5rem}'
            . '@media(min-width:768px){.rg-uss__pin{width:1.5rem;height:1.5rem;margin-left:1.4rem;margin-right:.7rem}}'
            . '.rg-uss__input{flex:1 1 auto;min-width:0;background:transparent!important;border:0!important;outline:0!important;box-shadow:none!important;font-size:1rem;line-height:1.2;color:#0f172a;padding:.85rem .3rem;font-weight:500}'
            . '.rg-uss__input::placeholder{color:#94a3b8;font-weight:400}'
            . '.rg-uss__input::-webkit-search-cancel-button{display:none!important;-webkit-appearance:none!important}'
            . '@media(min-width:768px){.rg-uss__input{font-size:1.2rem;padding:1rem .4rem}}'
            . '.rg-uss__clear{flex:0 0 auto;width:2.2rem;height:2.2rem;border-radius:999px;background:#f1f5f9;color:#475569;border:0;cursor:pointer;display:flex;align-items:center;justify-content:center;margin-right:.5rem;transition:all .15s ease}'
            . '.rg-uss__clear[hidden]{display:none!important}'
            . '.rg-uss__clear svg{width:.85rem;height:.85rem}'
            . '.rg-uss__clear:hover{background:#e2e8f0;color:#0f172a}'
            . '.rg-uss__submit{flex:0 0 auto;background:var(--rg-acc);color:#fff;border:0;border-radius:999px;padding:.85rem 1.4rem;font-weight:700;font-size:.95rem;cursor:pointer;transition:all .18s ease;letter-spacing:.01em}'
            . '@media(min-width:768px){.rg-uss__submit{padding:1rem 1.8rem;font-size:1rem}}'
            . '.rg-uss__submit:hover{transform:translateY(-1px) scale(1.02);box-shadow:0 12px 24px -8px rgba(0,0,0,.25)}'
            . '.rg-uss__alt-or{flex:0 0 auto;margin:0 .4rem;font-size:.95rem;font-weight:600;color:#475569;white-space:nowrap}'
            . '@media(min-width:768px){.rg-uss__alt-or{font-size:1rem;margin:0 .6rem}}'
            . '@media(max-width:639px){.rg-uss__alt-or{display:none}}'
            . '.rg-uss__alt{flex:0 0 auto;display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 1.4rem;font-size:.95rem;font-weight:700;color:#fff;background:#ef4444;border:0;cursor:pointer;border-radius:999px;transition:all .18s ease;white-space:nowrap;letter-spacing:.01em}'
            . '@media(min-width:768px){.rg-uss__alt{padding:1rem 1.6rem;font-size:1rem}}'
            . '.rg-uss__alt:hover{transform:translateY(-1px) scale(1.02);box-shadow:0 12px 24px -8px rgba(220,38,38,.5);background:#dc2626}'
            . '.rg-uss__alt-arrow{width:1.15rem;height:1.15rem;flex-shrink:0;animation:rgUssArrowNudge 1.6s ease-in-out infinite}'
            . '@keyframes rgUssArrowNudge{0%,100%{transform:translateX(0)}50%{transform:translateX(5px)}}'
            . '@media(max-width:639px){.rg-uss__alt-text{display:none}.rg-uss__alt{padding:.85rem .85rem;margin-left:.15rem}}'
            . '@media(prefers-reduced-motion:reduce){.rg-uss__alt-arrow{animation:none}}'
            . '.rg-uss__chips{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:.45rem;margin-top:1.1rem;font-size:.82rem;color:#64748b}'
            . '.rg-uss__chips>span{font-weight:700;letter-spacing:.02em;margin-right:.25rem}'
            . '.rg-uss__chip{background:rgba(255,255,255,.85);border:1.5px solid #e2e8f0;color:#334155;padding:.4rem .85rem;border-radius:999px;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .15s ease;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}'
            . '.rg-uss__chip:hover{background:var(--rg-acc);border-color:var(--rg-acc);color:#fff;transform:translateY(-1px)}'
            . '.rg-uss__panel{position:absolute;top:calc(100% + .85rem);left:0;right:0;max-height:30rem;overflow-y:auto;background:#fff;border:1px solid #e2e8f0;border-radius:1.25rem;box-shadow:0 30px 70px -20px rgba(15,23,42,.35),0 10px 24px -8px rgba(15,23,42,.15);padding:.6rem;z-index:100;animation:rgUssFade .18s ease-out;text-align:left}'
            . '@keyframes rgUssFade{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}'
            . '.rg-uss__group-label{font-size:.68rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:#94a3b8;padding:.75rem 1rem .35rem}'
            . '.rg-uss__opt{display:flex;align-items:center;gap:.95rem;padding:.7rem 1rem;border-radius:.85rem;cursor:pointer;text-decoration:none;color:inherit;transition:background .12s ease}'
            . '.rg-uss__opt:hover,.rg-uss__opt.is-active{background:color-mix(in srgb,var(--rg-acc) 8%,transparent)}'
            . '.rg-uss__opt-thumb{flex:0 0 auto;width:2.6rem;height:2.6rem;border-radius:.65rem;background:color-mix(in srgb,var(--rg-acc) 10%,transparent);color:var(--rg-acc);display:flex;align-items:center;justify-content:center;overflow:hidden}'
            . '.rg-uss__opt-thumb svg{width:1.2rem;height:1.2rem}'
            . '.rg-uss__opt-body{flex:1 1 auto;min-width:0;display:flex;flex-direction:column;gap:.1rem}'
            . '.rg-uss__opt-label{display:block;font-size:.95rem;font-weight:700;color:#0f172a;text-transform:capitalize;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}'
            . '.rg-uss__opt-label mark{background:rgba(252,211,77,.55);color:inherit;padding:0 .12em;border-radius:.25em}'
            . '.rg-uss__opt-sub{display:block;font-size:.75rem;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}'
            . '.rg-uss__opt-chip{flex:0 0 auto;font-size:.65rem;text-transform:uppercase;letter-spacing:.06em;font-weight:700;color:#64748b;background:#f1f5f9;padding:.2rem .5rem;border-radius:999px}'
            . '.rg-uss__opt-arrow{flex:0 0 auto;color:#cbd5e1;width:.95rem;height:.95rem}'
            . '.rg-uss__empty{padding:1.6rem 1.5rem;text-align:center;color:#64748b;font-size:.9rem;line-height:1.45}'
            . '.rg-uss-curve{position:relative;display:inline-block;color:inherit}'
            . '.rg-uss-curve-svg{position:absolute;left:-5px;width:100%;height:auto;bottom:calc(-.7rem - 10px);color:' . $curveColor . ';pointer-events:none;overflow:visible;filter:drop-shadow(0 1px 2px rgba(0,0,0,.18))}'
            . '@media(min-width:768px){.rg-uss-curve-svg{bottom:calc(-1rem - 10px)}}'
            . '.rg-uss-title-break{display:block;height:30px}'
            . '.rg-uss-video{position:absolute;inset:0;overflow:hidden;pointer-events:none;z-index:0}'
            . '.rg-uss-video iframe{position:absolute;top:50%;left:50%;width:177.78vh;height:100%;min-width:100%;min-height:56.25vw;transform:translate(-50%,-50%);border:0;pointer-events:none}'
            . '@media(min-aspect-ratio:16/9){.rg-uss-video iframe{width:100%;height:56.25vw;min-height:100%}}'
            . '.rg-uss-video-scrim{position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.82) 0%,rgba(255,255,255,.80) 50%,rgba(255,255,255,.88) 100%);pointer-events:none;z-index:1}'
            . '.rg-uss-video-init{position:absolute;inset:0;background:#ffffff;pointer-events:none;z-index:2;animation:rg-uss-video-init-out 1.5s ease-out forwards}'
            . '@keyframes rg-uss-video-init-out{0%,40%{opacity:1}100%{opacity:0}}'
            . '@media(prefers-reduced-motion:reduce){.rg-uss-video-init{animation-duration:.3s}}'
            . '</style>';

        // JSON index + JS
        $out .= '<script id="' . $dataId . '" type="application/json">' . json_encode($searchIndex, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . '</script>';

        $emptyHintJs = json_encode($emptyHint);
        $labelsJsonJs = $labelsJson;
        $boxIdJs = json_encode($boxId);
        $dataIdJs = json_encode($dataId);

        $out .= '<script>(function(){'
            . 'var input=document.getElementById(' . $boxIdJs . ');if(!input)return;'
            . 'var root=input.closest("[data-rg-search]");'
            . 'var panel=root.querySelector(".rg-uss__panel");'
            . 'var clearBtn=root.querySelector(".rg-uss__clear");'
            . 'var chips=root.querySelectorAll(".rg-uss__chip");'
            . 'var tabs=root.querySelectorAll(".rg-uss__tab");'
            . 'var submitBtn=root.querySelector(".rg-uss__submit");'
            . 'var dataEl=document.getElementById(' . $dataIdJs . ');'
            . 'var index=[];try{index=JSON.parse(dataEl.textContent)}catch(e){index=[]}'
            . 'var LABELS=' . $labelsJsonJs . ';'
            . 'var EMPTY=' . $emptyHintJs . ';'
            . 'var TYPE_ORDER={region:0,destination:1,resort:2,restaurant:3,spot:4,blog:5};'
            . 'var MAX_PER_GROUP={region:3,destination:5,resort:4,restaurant:4,spot:4,blog:3};'
            . 'var results=[],activeIdx=-1,debounceId=0,currentFilter="all";'
            . 'function esc(s){return String(s).replace(/[&<>"\']/g,function(c){return{"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","\'":"&#39;"}[c]})}'
            . 'function escRe(s){return s.replace(/[.*+?^${}()|[\\]\\\\]/g,"\\\\$&")}'
            . 'function hl(t,toks){var o=esc(t);for(var i=0;i<toks.length;i++){if(!toks[i])continue;var r=new RegExp("("+escRe(toks[i])+")","gi");o=o.replace(r,"<mark>$1</mark>")}return o}'
            . 'function score(it,q,toks){var h=(it.haystack||it.label||"").toLowerCase();if(h.indexOf(q)===0)return 1000;if(new RegExp("\\\\b"+escRe(q)).test(h))return 800;if(toks.every(function(t){return h.indexOf(t)!==-1})){var b=toks.some(function(t){return new RegExp("\\\\b"+escRe(t)).test(h)})?50:0;return 500+b}if(h.indexOf(q)!==-1)return 300;return 0}'
            . 'function search(q){var ql=q.toLowerCase().trim();if(!ql)return[];var toks=ql.split(/\\s+/).filter(Boolean);var scored=[];for(var i=0;i<index.length;i++){var it=index[i];if(currentFilter!=="all"&&it.type!==currentFilter)continue;var s=score(it,ql,toks);if(s>0)scored.push({it:it,s:s})}scored.sort(function(a,b){return(b.s-a.s)||((b.it.volume||0)-(a.it.volume||0))});if(currentFilter!=="all")return scored.slice(0,15).map(function(r){return r.it});var caps=Object.assign({},MAX_PER_GROUP);var picked=[];for(var j=0;j<scored.length;j++){var t=scored[j].it.type;if(caps[t]>0){caps[t]--;picked.push(scored[j].it)}if(picked.length>=15)break}picked.sort(function(a,b){return((TYPE_ORDER[a.type]||0)-(TYPE_ORDER[b.type]||0))||((b.volume||0)-(a.volume||0))});return picked}'
            . 'function iconFor(t){if(t==="region")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3z"/><path d="M9 4v13M15 7v13"/></svg>\';if(t==="resort"||t==="restaurant")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l8-4 8 4v14M9 9v.01M9 12v.01M9 15v.01M9 18v.01M15 9v.01M15 12v.01M15 15v.01M15 18v.01"/></svg>\';if(t==="spot")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 0-8 8c0 5.5 8 12 8 12s8-6.5 8-12a8 8 0 0 0-8-8z"/></svg>\';if(t==="blog")return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2z"/><path d="M7 8h10M7 12h10M7 16h6"/></svg>\';return\'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>\'}'
            . 'function thumbStyle(it){if(!it.image)return"";var u=String(it.image).replace(/\'/g,"%27").replace(/"/g,"%22");return\' style="background-image:url(\\\'\'+u+\'\\\');background-size:cover;background-position:center;background-repeat:no-repeat"\'}'
            . 'function render(q){if(!results.length){panel.innerHTML=\'<div class="rg-uss__empty">No matches for <strong>"\'+esc(q)+\'"</strong>.<br>\'+esc(EMPTY)+\'</div>\';panel.hidden=false;input.setAttribute("aria-expanded","true");return}var toks=q.toLowerCase().trim().split(/\\s+/).filter(Boolean);var parts=[],lastType=null,optIdx=0;for(var i=0;i<results.length;i++){var it=results[i];if(it.type!==lastType){parts.push(\'<div class="rg-uss__group-label">\'+esc(LABELS[it.type]||it.type)+\'</div>\');lastType=it.type}var icon=it.image?"":iconFor(it.type);parts.push(\'<a class="rg-uss__opt" role="option" data-idx="\'+optIdx+\'" href="\'+esc(it.url)+\'"><span class="rg-uss__opt-thumb"\'+thumbStyle(it)+\'>\'+icon+\'</span><span class="rg-uss__opt-body"><span class="rg-uss__opt-label">\'+hl(it.label||"",toks)+\'</span>\'+(it.sub?\'<span class="rg-uss__opt-sub">\'+esc(it.sub)+\'</span>\':"")+\'</span><span class="rg-uss__opt-chip">\'+esc(LABELS[it.type]||it.type)+\'</span><svg class="rg-uss__opt-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></a>\');optIdx++}panel.innerHTML=parts.join("");panel.hidden=false;input.setAttribute("aria-expanded","true");setActive(-1)}'
            . 'function close(){panel.hidden=true;input.setAttribute("aria-expanded","false");activeIdx=-1}'
            . 'function setActive(i){var opts=panel.querySelectorAll(".rg-uss__opt");if(!opts.length)return;opts.forEach(function(o){o.classList.remove("is-active")});if(i<0||i>=opts.length){activeIdx=-1;return}activeIdx=i;opts[i].classList.add("is-active");opts[i].scrollIntoView({block:"nearest"})}'
            . 'function runSearch(q){results=search(q);render(q)}'
            . 'input.addEventListener("input",function(e){var q=e.target.value;clearBtn.hidden=!q;clearTimeout(debounceId);if(!q.trim()){close();return}debounceId=setTimeout(function(){runSearch(q)},80)});'
            . 'input.addEventListener("focus",function(){if(input.value.trim())runSearch(input.value)});'
            . 'input.addEventListener("keydown",function(e){if(e.key==="ArrowDown"){if(panel.hidden&&input.value.trim())runSearch(input.value);e.preventDefault();var opts=panel.querySelectorAll(".rg-uss__opt");setActive(Math.min(activeIdx+1,opts.length-1))}else if(e.key==="ArrowUp"){e.preventDefault();var opts=panel.querySelectorAll(".rg-uss__opt");setActive(activeIdx<=0?opts.length-1:activeIdx-1)}else if(e.key==="Enter"){var opts=panel.querySelectorAll(".rg-uss__opt");if(!opts.length)return;e.preventDefault();var target=activeIdx>=0?opts[activeIdx]:opts[0];if(target)window.location.href=target.getAttribute("href")}else if(e.key==="Escape"){if(input.value){input.value="";clearBtn.hidden=true}close()}});'
            . 'panel.addEventListener("mousedown",function(e){var opt=e.target.closest(".rg-uss__opt");if(!opt)return;e.preventDefault();window.location.href=opt.getAttribute("href")});'
            . 'panel.addEventListener("mouseover",function(e){var opt=e.target.closest(".rg-uss__opt");if(!opt)return;setActive(parseInt(opt.dataset.idx,10))});'
            . 'clearBtn.addEventListener("click",function(){input.value="";clearBtn.hidden=true;close();input.focus()});'
            . 'chips.forEach(function(c){c.addEventListener("click",function(){var q=c.dataset.rgQuick;input.value=q;clearBtn.hidden=false;input.focus();runSearch(q)})});'
            . 'tabs.forEach(function(t){t.addEventListener("click",function(){tabs.forEach(function(x){x.classList.remove("is-active");x.setAttribute("aria-selected","false")});t.classList.add("is-active");t.setAttribute("aria-selected","true");currentFilter=t.dataset.rgFilter||"all";if(input.value.trim())runSearch(input.value);input.focus()})});'
            . 'submitBtn.addEventListener("click",function(){var opts=panel.querySelectorAll(".rg-uss__opt");if(opts.length){var target=activeIdx>=0?opts[activeIdx]:opts[0];window.location.href=target.getAttribute("href");return}if(input.value.trim())runSearch(input.value);input.focus()});'
            . 'document.addEventListener("click",function(e){if(!root.contains(e.target))close()});'
        . '})();</script>';

        $out .= '</section>';
        return $out;
    }

    /**
     * home_editorial_intro — editorial 2-col: H2 + paragraphs on
     * one side, featured photo + caption on the other. Sets the
     * site's voice right under the hero.
     *
     * Payload: heading, paragraphs[], image_src, image_alt,
     *          image_caption, image_position (left|right|none), accent
     */
    private function homeEditorialIntro(array $p, array $context): string
    {
        if (isset($p['paragraphs']) && is_string($p['paragraphs'])) {
            $t = trim($p['paragraphs']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['paragraphs'] = $d;
            } else {
                $p['paragraphs'] = array_values(array_filter(preg_split('/\n\n+/', $t)));
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $paragraphs = $p['paragraphs'] ?? [];
        if (!is_array($paragraphs)) $paragraphs = [];
        $imgSrc = trim((string) ($p['image_src'] ?? ''));
        $imgAlt = $this->e((string) ($p['image_alt'] ?? ''));
        $imgCaption = $this->e((string) ($p['image_caption'] ?? ''));
        $imgPosition = in_array($p['image_position'] ?? 'right', ['left', 'right', 'none'], true) ? $p['image_position'] : 'right';
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $accentBar = ['brand' => 'bg-blue-600', 'amber' => 'bg-amber-600', 'emerald' => 'bg-emerald-600', 'rose' => 'bg-rose-600', 'violet' => 'bg-violet-600', 'teal' => 'bg-teal-600'][$accent];

        $imgUrl = '';
        if ($imgSrc !== '') {
            $imgUrl = str_starts_with($imgSrc, 'http') || str_starts_with($imgSrc, '/') ? $imgSrc : '/storage/' . ltrim($imgSrc, '/');
        }

        $prose = '<div class="space-y-5 text-base sm:text-lg leading-relaxed text-slate-700 [&_p]:m-0">';
        foreach ($paragraphs as $para) {
            $para = trim((string) $para);
            if ($para === '') continue;
            $prose .= '<p>' . $this->e($para) . '</p>';
        }
        $prose .= '</div>';

        $imgHtml = '';
        if ($imgUrl !== '' && $imgPosition !== 'none') {
            $imgHtml = '<figure class="m-0">'
                . '<img src="' . $this->e($imgUrl) . '" alt="' . $imgAlt . '" loading="lazy" '
                . 'class="w-full aspect-[4/5] lg:aspect-[3/4] object-cover rounded-2xl shadow-md">'
                . ($imgCaption !== '' ? '<figcaption class="text-xs text-slate-400 mt-2">' . $imgCaption . '</figcaption>' : '')
                . '</figure>';
        }

        $out = '<section class="py-16 md:py-20">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-3 flex items-center gap-3">';
        $out .= '<span class="block h-[3px] w-12 ' . $accentBar . ' rounded-full"></span>';
        $out .= '<span class="text-xs uppercase tracking-[0.2em] font-bold text-slate-500">Editorial</span>';
        $out .= '</div>';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-8 max-w-3xl">' . $heading . '</h2>';

        if ($imgHtml !== '' && $imgPosition === 'left') {
            $out .= '<div class="grid grid-cols-1 lg:grid-cols-[400px_1fr] gap-8 lg:gap-12 items-start">' . $imgHtml . $prose . '</div>';
        } elseif ($imgHtml !== '' && $imgPosition === 'right') {
            $out .= '<div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 lg:gap-12 items-start">' . $prose . $imgHtml . '</div>';
        } else {
            $out .= '<div class="max-w-3xl">' . $prose . '</div>';
        }
        $out .= '</div></section>';
        return $out;
    }

    /**
     * home_experience_grid — 6 large image tiles, each linking to
     * a discovery hub. Photos with gradient overlay + name + tagline.
     *
     * Payload: heading, subhead, columns (2|3),
     *          categories[] of { name, tagline, url, image_src, image_alt }
     */
    private function homeExperienceGrid(array $p, array $context): string
    {
        if (isset($p['categories']) && is_string($p['categories'])) {
            $t = trim($p['categories']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['categories'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $categories = $p['categories'] ?? [];
        if (!is_array($categories) || empty($categories)) return '';
        $columns = max(2, min(3, (int) ($p['columns'] ?? 3)));
        $gridClass = [2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-2 lg:grid-cols-3'][$columns];

        $out = '<section class="py-16 md:py-20 bg-slate-50" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-8 text-center max-w-3xl mx-auto">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600">' . $subhead . '</p>';
        $out .= '</div>';
        $out .= '<div class="grid ' . $gridClass . ' gap-4 md:gap-5">';
        foreach ($categories as $cat) {
            $cArr = $this->toArrayShapeSimple($cat);
            $name = $this->e((string) ($cArr['name'] ?? ''));
            $tagline = $this->e((string) ($cArr['tagline'] ?? ''));
            $url = $this->e((string) ($cArr['url'] ?? '#'));
            $imgSrc = (string) ($cArr['image_src'] ?? '');
            $imgAlt = $this->e((string) ($cArr['image_alt'] ?? $name));
            if ($name === '') continue;
            $imgUrl = '';
            if ($imgSrc !== '') {
                $imgUrl = str_starts_with($imgSrc, 'http') || str_starts_with($imgSrc, '/') ? $imgSrc : '/storage/' . ltrim($imgSrc, '/');
            }
            $out .= '<a href="' . $url . '" class="rg-exp-card group">';
            if ($imgUrl !== '') {
                $out .= '<img src="' . $this->e($imgUrl) . '" alt="' . $imgAlt . '" loading="lazy" class="rg-exp-img">';
            } else {
                $out .= '<div class="rg-exp-img" style="background:linear-gradient(135deg,#475569 0%,#334155 60%,#1e293b 100%)"></div>';
            }
            $out .= '<div class="rg-exp-overlay"></div>';
            $out .= '<div class="rg-exp-body">';
            $out .= '<h3 class="rg-exp-name">' . $name . '</h3>';
            if ($tagline !== '') $out .= '<p class="rg-exp-tag">' . $tagline . '</p>';
            $out .= '<span class="rg-exp-cue">Explore '
                . '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</span>';
            $out .= '</div></a>';
        }
        $out .= '</div></div>';
        $out .= '<style>'
            . '.rg-exp-card{position:relative;display:block;height:340px;border-radius:1.1rem;overflow:hidden;background:#e2e8f0;box-shadow:0 4px 12px -4px rgba(15,23,42,.18);transition:transform .4s cubic-bezier(.22,1,.36,1),box-shadow .4s ease;text-decoration:none;color:inherit}'
            . '@media(min-width:768px){.rg-exp-card{height:380px}}'
            . '.rg-exp-card:hover{transform:translateY(-6px);box-shadow:0 24px 48px -16px rgba(15,23,42,.3)}'
            . '.rg-exp-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .9s cubic-bezier(.22,1,.36,1)}'
            . '.rg-exp-card:hover .rg-exp-img{transform:scale(1.05)}'
            . '.rg-exp-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(15,23,42,.05) 0%,rgba(15,23,42,.35) 45%,rgba(15,23,42,.85) 100%);pointer-events:none}'
            . '.rg-exp-body{position:absolute;left:0;right:0;bottom:0;padding:1.5rem 1.6rem;color:#fff}'
            . '.rg-exp-name{font-size:1.6rem;font-weight:800;line-height:1.15;text-shadow:0 2px 12px rgba(0,0,0,.4);margin:0 0 .35rem;letter-spacing:-.01em}'
            . '@media(min-width:768px){.rg-exp-name{font-size:1.85rem}}'
            . '.rg-exp-tag{font-size:.95rem;color:rgba(255,255,255,.92);margin:0 0 .85rem;line-height:1.4;max-width:30ch}'
            . '.rg-exp-cue{display:inline-flex;align-items:center;gap:.4rem;font-size:.85rem;font-weight:700;color:#fef3c7;letter-spacing:.04em;text-transform:uppercase;transition:gap .25s ease}'
            . '.rg-exp-card:hover .rg-exp-cue{gap:.7rem}'
            . '.rg-exp-cue svg{width:.95rem;height:.95rem;transition:transform .25s ease}'
            . '.rg-exp-card:hover .rg-exp-cue svg{transform:translateX(.2rem)}'
            . '</style>';
        $out .= '</section>';
        return $out;
    }

    /**
     * home_hub_links — 4 hub-page link cards (Eat / Do / Buy / Meet).
     * Compact card row with icon + label + tagline + arrow.
     *
     * Payload: heading, subhead, hubs[] of { label, url, icon, tagline }
     */
    private function homeHubLinks(array $p, array $context): string
    {
        if (isset($p['hubs']) && is_string($p['hubs'])) {
            $t = trim($p['hubs']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['hubs'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $hubs = $p['hubs'] ?? [];
        if (!is_array($hubs) || empty($hubs)) return '';

        $accentMap = [
            'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'hover:border-rose-300'],
            'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'hover:border-emerald-300'],
            'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'hover:border-violet-300'],
            'teal' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'hover:border-teal-300'],
            'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'hover:border-amber-300'],
            'brand' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'hover:border-blue-300'],
        ];

        $out = '<section class="py-16 md:py-20">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-8 max-w-3xl">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600">' . $subhead . '</p>';
        $out .= '</div>';
        $out .= '<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">';
        foreach ($hubs as $h) {
            $hArr = $this->toArrayShapeSimple($h);
            $label = $this->e((string) ($hArr['label'] ?? ''));
            $url = $this->e((string) ($hArr['url'] ?? '#'));
            $icon = (string) ($hArr['icon'] ?? '🌟');
            $tagline = $this->e((string) ($hArr['tagline'] ?? ''));
            $accent = $hArr['accent'] ?? 'brand';
            if (!isset($accentMap[$accent])) $accent = 'brand';
            $am = $accentMap[$accent];
            if ($label === '') continue;
            $out .= '<a href="' . $url . '" class="group block rounded-2xl border border-slate-200 ' . $am['border'] . ' bg-white hover:shadow-lg p-5 transition-all">';
            $out .= '<div class="w-12 h-12 rounded-xl ' . $am['bg'] . ' ' . $am['text'] . ' flex items-center justify-center text-2xl mb-4">' . $icon . '</div>';
            $out .= '<h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:' . $am['text'] . ' transition-colors">' . $label . '</h3>';
            if ($tagline !== '') $out .= '<p class="text-sm text-slate-600 leading-relaxed mb-4">' . $tagline . '</p>';
            $out .= '<span class="text-xs font-bold ' . $am['text'] . ' inline-flex items-center gap-1.5 uppercase tracking-wider">Open <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span>';
            $out .= '</a>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_season_guide — 3 season cards with months + blurb + tip.
     * Helps travelers plan when to come.
     *
     * Payload: heading, subhead, seasons[] of { name, months, blurb, tip }
     */
    private function homeSeasonGuide(array $p, array $context): string
    {
        if (isset($p['seasons']) && is_string($p['seasons'])) {
            $t = trim($p['seasons']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['seasons'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $seasons = $p['seasons'] ?? [];
        if (!is_array($seasons) || empty($seasons)) return '';
        $palettes = [
            ['accent' => 'amber', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '☀️'],
            ['accent' => 'brand', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => '🌧️'],
            ['accent' => 'emerald', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => '🌤️'],
        ];

        $out = '<section class="py-16 md:py-20 bg-gradient-to-b from-white to-slate-50" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-10 text-center max-w-3xl mx-auto">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600">' . $subhead . '</p>';
        $out .= '</div>';
        $out .= '<div class="grid md:grid-cols-3 gap-5">';
        foreach ($seasons as $idx => $s) {
            $sArr = $this->toArrayShapeSimple($s);
            $name = $this->e((string) ($sArr['name'] ?? ''));
            $months = $this->e((string) ($sArr['months'] ?? ''));
            $headline = $this->e((string) ($sArr['headline'] ?? ''));
            $blurb = $this->e((string) ($sArr['blurb'] ?? ''));
            $tip = $this->e((string) ($sArr['tip'] ?? ''));
            if ($name === '') continue;
            $pal = $palettes[$idx % count($palettes)];
            $out .= '<div class="rounded-2xl border ' . $pal['border'] . ' bg-white p-6 shadow-sm">';
            $out .= '<div class="flex items-start justify-between mb-3">';
            $out .= '<div>';
            $out .= '<div class="text-2xl mb-1">' . $pal['icon'] . '</div>';
            $out .= '<h3 class="text-xl font-extrabold text-slate-900">' . $name . '</h3>';
            if ($months !== '') $out .= '<div class="text-xs font-bold uppercase tracking-wider ' . $pal['text'] . ' mt-1">' . $months . '</div>';
            $out .= '</div></div>';
            if ($headline !== '') $out .= '<p class="text-sm md:text-[0.95rem] font-bold text-slate-900 leading-snug mb-3">' . $headline . '</p>';
            if ($blurb !== '') $out .= '<p class="text-sm text-slate-700 leading-relaxed mb-4">' . $blurb . '</p>';
            if ($tip !== '') {
                $out .= '<div class="' . $pal['bg'] . ' rounded-lg p-3 border-l-4 border-' . str_replace(['brand'], ['blue'], $pal['accent']) . '-400">';
                $out .= '<div class="text-[10px] uppercase tracking-wider font-bold ' . $pal['text'] . ' mb-1">Tip</div>';
                $out .= '<p class="text-sm text-slate-700 leading-snug m-0">' . $tip . '</p>';
                $out .= '</div>';
            }
            $out .= '</div>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_testimonials — 3-up traveler review cards with avatar,
     * star rating, location.
     *
     * Payload: heading, subhead, reviews[] of
     *          { text, author, location, rating, avatar_url }
     */
    private function homeTestimonials(array $p, array $context): string
    {
        if (isset($p['reviews']) && is_string($p['reviews'])) {
            $t = trim($p['reviews']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['reviews'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $reviews = $p['reviews'] ?? [];
        if (!is_array($reviews) || empty($reviews)) return '';

        $out = '<section class="py-16 md:py-20">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-10 text-center max-w-3xl mx-auto">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600">' . $subhead . '</p>';
        $out .= '</div>';
        $out .= '<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">';
        foreach ($reviews as $rev) {
            $rArr = $this->toArrayShapeSimple($rev);
            $text = $this->e((string) ($rArr['text'] ?? ''));
            $author = $this->e((string) ($rArr['author'] ?? ''));
            $location = $this->e((string) ($rArr['location'] ?? ''));
            $rating = (int) ($rArr['rating'] ?? 5);
            $rating = max(1, min(5, $rating));
            $avatar = (string) ($rArr['avatar_url'] ?? '');
            $initial = $author !== '' ? mb_strtoupper(mb_substr(strip_tags($author), 0, 1)) : '?';
            if ($text === '') continue;

            $out .= '<figure class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col">';
            $out .= '<div class="flex items-center gap-1 mb-3">';
            for ($i = 0; $i < 5; $i++) {
                $color = $i < $rating ? '#f59e0b' : '#e2e8f0';
                $out .= '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="' . $color . '"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
            }
            $out .= '</div>';
            $out .= '<blockquote class="text-sm text-slate-700 leading-relaxed mb-5 flex-1 m-0">';
            $out .= '<svg class="w-5 h-5 text-slate-300 mb-1 inline-block" viewBox="0 0 24 24" fill="currentColor"><path d="M6 17h3l2-4V7H5v6h3l-2 4zm10 0h3l2-4V7h-6v6h3l-2 4z"/></svg> ';
            $out .= $text;
            $out .= '</blockquote>';
            $out .= '<figcaption class="flex items-center gap-3 mt-auto">';
            if ($avatar !== '') {
                $imgUrl = str_starts_with($avatar, 'http') || str_starts_with($avatar, '/') ? $avatar : '/storage/' . ltrim($avatar, '/');
                $out .= '<img src="' . $this->e($imgUrl) . '" alt="' . $author . '" class="w-10 h-10 rounded-full object-cover bg-slate-200">';
            } else {
                $out .= '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center font-bold text-sm">' . $this->e($initial) . '</div>';
            }
            $out .= '<div>';
            if ($author !== '') $out .= '<div class="text-sm font-bold text-slate-900">' . $author . '</div>';
            if ($location !== '') $out .= '<div class="text-xs text-slate-500">' . $location . '</div>';
            $out .= '</div></figcaption>';
            $out .= '</figure>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_faq — FAQ accordion with schema.org markup baked-in.
     * Each Q is a native <details> for keyboard + screen-reader
     * support. Also emits FAQPage JSON-LD inline so Google can
     * pull the rich-result eligible markup straight from the
     * homepage.
     *
     * Payload: heading, subhead, faqs[] of { question, answer }
     */
    private function homeFaq(array $p, array $context): string
    {
        if (isset($p['faqs']) && is_string($p['faqs'])) {
            $t = trim($p['faqs']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['faqs'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $faqs = $p['faqs'] ?? [];
        if (!is_array($faqs) || empty($faqs)) return '';

        // Schema.org FAQPage JSON-LD for rich results
        $jsonLdItems = [];
        foreach ($faqs as $f) {
            $fArr = $this->toArrayShapeSimple($f);
            $q = trim((string) ($fArr['question'] ?? ''));
            $a = trim((string) ($fArr['answer'] ?? ''));
            if ($q === '' || $a === '') continue;
            $jsonLdItems[] = [
                '@type' => 'Question',
                'name' => $q,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
            ];
        }

        $out = '<section class="py-16 md:py-20 bg-slate-50" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-10 text-center">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600">' . $subhead . '</p>';
        $out .= '</div>';
        $out .= '<div class="space-y-3">';
        foreach ($faqs as $f) {
            $fArr = $this->toArrayShapeSimple($f);
            $q = $this->e(trim((string) ($fArr['question'] ?? '')));
            $a = $this->e(trim((string) ($fArr['answer'] ?? '')));
            if ($q === '' || $a === '') continue;
            $out .= '<details class="group rounded-xl border border-slate-200 bg-white overflow-hidden">';
            $out .= '<summary class="flex items-center justify-between gap-4 p-5 cursor-pointer select-none hover:bg-slate-50 transition-colors">';
            $out .= '<h3 class="text-base sm:text-lg font-bold text-slate-900 m-0">' . $q . '</h3>';
            $out .= '<svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>';
            $out .= '</summary>';
            $out .= '<div class="px-5 pb-5 pt-1 text-slate-700 leading-relaxed text-sm sm:text-base border-t border-slate-100">' . $a . '</div>';
            $out .= '</details>';
        }
        $out .= '</div></div>';
        if (!empty($jsonLdItems)) {
            $out .= '<script type="application/ld+json">' . json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $jsonLdItems,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
        }
        $out .= '</section>';
        return $out;
    }

    /**
     * home_owner_inline_band — mid-page lighter owner-conversion
     * surface. Sits between traveler-discovery blocks without
     * stealing the closing CTA's job. Soft warm tint (amber) so
     * it signals a different audience without breaking flow.
     *
     * Payload:
     *   eyebrow, heading, body, cta_label, cta_url, accent
     *   (amber | emerald | teal | brand — default amber)
     */
    private function homeOwnerInlineBand(array $p, array $context): string
    {
        $eyebrow = $this->e(trim((string) ($p['eyebrow'] ?? '')));
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $body = $this->e(trim((string) ($p['body'] ?? '')));
        $ctaLabel = $this->e(trim((string) ($p['cta_label'] ?? '')));
        $ctaUrl = $this->e(trim((string) ($p['cta_url'] ?? '#')));
        $accent = in_array($p['accent'] ?? 'amber', ['amber', 'emerald', 'teal', 'brand'], true) ? $p['accent'] : 'amber';
        if ($heading === '') return '';

        $tint = [
            'amber'   => ['wash' => 'bg-gradient-to-br from-amber-50 to-orange-50', 'ring' => 'border-amber-200', 'eyebrow' => 'text-amber-700', 'btn' => 'bg-amber-600 hover:bg-amber-700'],
            'emerald' => ['wash' => 'bg-gradient-to-br from-emerald-50 to-teal-50', 'ring' => 'border-emerald-200', 'eyebrow' => 'text-emerald-700', 'btn' => 'bg-emerald-600 hover:bg-emerald-700'],
            'teal'    => ['wash' => 'bg-gradient-to-br from-teal-50 to-cyan-50', 'ring' => 'border-teal-200', 'eyebrow' => 'text-teal-700', 'btn' => 'bg-teal-600 hover:bg-teal-700'],
            'brand'   => ['wash' => 'bg-gradient-to-br from-blue-50 to-indigo-50', 'ring' => 'border-blue-200', 'eyebrow' => 'text-blue-700', 'btn' => 'bg-blue-600 hover:bg-blue-700'],
        ][$accent];

        $out = '<section class="py-10 md:py-12">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="' . $tint['wash'] . ' border ' . $tint['ring'] . ' rounded-2xl px-6 py-7 md:px-10 md:py-8 flex flex-col md:flex-row md:items-center md:justify-between gap-5 md:gap-8 shadow-sm">';
        $out .= '<div class="flex-1 min-w-0">';
        if ($eyebrow !== '') {
            $out .= '<div class="text-[11px] uppercase tracking-[0.18em] font-bold ' . $tint['eyebrow'] . ' mb-2">' . $eyebrow . '</div>';
        }
        $out .= '<h2 class="text-xl md:text-2xl font-extrabold text-slate-900 leading-snug tracking-[-0.01em] mb-2">' . $heading . '</h2>';
        if ($body !== '') {
            $out .= '<p class="text-slate-700 text-sm md:text-base leading-relaxed m-0 max-w-2xl">' . $body . '</p>';
        }
        $out .= '</div>';
        if ($ctaLabel !== '') {
            $out .= '<a href="' . $ctaUrl . '" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg ' . $tint['btn'] . ' text-white font-bold transition-colors shadow-sm hover:shadow shrink-0 whitespace-nowrap">';
            $out .= $ctaLabel;
            $out .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>';
            $out .= '</a>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_how_it_works — trust block. Three pillars in a row,
     * each with emoji icon + bold title + short body. Anchors
     * editorial credibility for first-time visitors.
     *
     * Payload:
     *   heading, subhead
     *   pillars[] of { icon, title, body }
     */
    private function homeHowItWorks(array $p, array $context): string
    {
        if (isset($p['pillars']) && is_string($p['pillars'])) {
            $t = trim($p['pillars']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['pillars'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $pillars = $p['pillars'] ?? [];
        if (!is_array($pillars) || empty($pillars)) return '';

        $palettes = [
            ['bg' => 'bg-blue-50',    'fg' => 'text-blue-700',    'ring' => 'ring-blue-100'],
            ['bg' => 'bg-emerald-50', 'fg' => 'text-emerald-700', 'ring' => 'ring-emerald-100'],
            ['bg' => 'bg-amber-50',   'fg' => 'text-amber-700',   'ring' => 'ring-amber-100'],
            ['bg' => 'bg-rose-50',    'fg' => 'text-rose-700',    'ring' => 'ring-rose-100'],
            ['bg' => 'bg-violet-50',  'fg' => 'text-violet-700',  'ring' => 'ring-violet-100'],
        ];

        $out = '<section class="py-16 md:py-20 bg-white">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        $out .= '<div class="mb-10 text-center max-w-3xl mx-auto">';
        if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight tracking-[-0.01em] mb-3">' . $heading . '</h2>';
        if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600 leading-relaxed">' . $subhead . '</p>';
        $out .= '</div>';
        $cols = count($pillars) === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-2 lg:grid-cols-3';
        $out .= '<div class="grid ' . $cols . ' gap-5 md:gap-6">';
        foreach ($pillars as $idx => $pi) {
            $pArr = $this->toArrayShapeSimple($pi);
            $title = $this->e((string) ($pArr['title'] ?? ''));
            $body = $this->e((string) ($pArr['body'] ?? ''));
            $icon = (string) ($pArr['icon'] ?? '✨');
            if ($title === '') continue;
            $pal = $palettes[$idx % count($palettes)];
            $out .= '<div class="rounded-2xl bg-white border border-slate-200 p-7 shadow-sm hover:shadow-md transition-shadow flex flex-col">';
            $out .= '<div class="w-14 h-14 rounded-2xl ' . $pal['bg'] . ' ' . $pal['fg'] . ' flex items-center justify-center text-3xl mb-5 ring-4 ' . $pal['ring'] . '">' . $icon . '</div>';
            $out .= '<h3 class="text-lg font-bold text-slate-900 leading-tight mb-2">' . $title . '</h3>';
            if ($body !== '') $out .= '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $body . '</p>';
            $out .= '</div>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * home_category_accordion — horizontal image-strip accordion.
     * Six vertical image cards in a row; clicking one expands its
     * width (desktop) or height (tablet/mobile) while the others
     * shrink. The inner content (kicker + title + description +
     * CTA) fades in 0.4s after the size transition starts.
     *
     * Pattern follows the classic "vertical accordion card"
     * (Stripe / Apple style):
     *   - Desktop  (≥1025px): horizontal flex row, expand on
     *                        click via flex-grow ratio.
     *   - Tablet   (≤1024px): vertical stack, expand on click
     *                        via height transition (500px).
     *   - Mobile   (≤480px):  same as tablet but expand to 380px.
     *
     * Payload:
     *   eyebrow, heading, subhead
     *   items[] of {
     *     label, eyebrow, description, cta_label, url,
     *     image (path), image_alt, accent (brand|amber|emerald|
     *     rose|violet|teal|sky)
     *   }
     */
    private function homeCategoryAccordion(array $p, array $context): string
    {
        if (isset($p['items']) && is_string($p['items'])) {
            $t = trim($p['items']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['items'] = $d;
            }
        }
        $eyebrow = $this->e(trim((string) ($p['eyebrow'] ?? '')));
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $items = $p['items'] ?? [];
        if (!is_array($items) || empty($items)) return '';

        $accentRgb = [
            'brand'   => '37,99,235',
            'amber'   => '217,119,6',
            'emerald' => '5,150,105',
            'rose'    => '225,29,72',
            'violet'  => '124,58,237',
            'teal'    => '13,148,136',
            'sky'     => '14,165,233',
        ];

        // Category-specific icon SVG paths (Heroicons-style).
        // Keyed by label so the payload doesn't need to ship them.
        $iconPaths = [
            'Where to Go'      => '<path d="M12 21s-7-7.5-7-13a7 7 0 0 1 14 0c0 5.5-7 13-7 13z"/><circle cx="12" cy="8" r="2.5"/>',
            'Where to Eat'     => '<path d="M3 11h18M5 11V8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v3M6 11v6a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-6"/>',
            'What to Eat'      => '<path d="M4 11h16a8 8 0 0 1-16 0z"/><path d="M9 7V4M12 7V4M15 7V4"/>',
            'What to Do'       => '<polygon points="12 2 15 9 22 9 17 14 19 22 12 18 5 22 7 14 2 9 9 9 12 2"/>',
            'What to Buy'      => '<path d="M5 8h14l-1.5 12.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 8z"/><path d="M9 8V5a3 3 0 0 1 6 0v3"/>',
            'Cultures to Meet' => '<path d="M5 9c0-3 2.5-5 6-5s6 2 6 5c0 3-1.5 6-6 6S5 12 5 9z"/><path d="M9 19l3 3 3-3"/>',
        ];

        $out = '<section class="rg-cat-acc py-16 md:py-24" style="background:linear-gradient(180deg,#f8fafc 0%,#ffffff 100%)" aria-label="' . ($heading !== '' ? $heading : 'Explore the Philippines') . '">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';

        if ($eyebrow !== '' || $heading !== '' || $subhead !== '') {
            $out .= '<div class="mb-10 md:mb-14 text-center max-w-2xl mx-auto">';
            if ($eyebrow !== '') $out .= '<p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500 mb-3">' . $eyebrow . '</p>';
            if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-[-0.015em] leading-tight mb-3">' . $heading . '</h2>';
            if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600 leading-relaxed">' . $subhead . '</p>';
            $out .= '</div>';
        }

        $out .= '<div class="rg-cat-acc__row">';
        foreach ($items as $idx => $item) {
            $arr = $this->toArrayShapeSimple($item);
            $label = (string) ($arr['label'] ?? '');
            if ($label === '') continue;
            $labelEsc = $this->e($label);
            $kicker = $this->e((string) ($arr['eyebrow'] ?? ''));
            $desc = $this->e((string) ($arr['description'] ?? ''));
            $ctaLabel = $this->e((string) ($arr['cta_label'] ?? 'Open'));
            $url = $this->e((string) ($arr['url'] ?? '#'));
            $image = (string) ($arr['image'] ?? '');
            $imageAlt = $this->e((string) ($arr['image_alt'] ?? $label));
            $accent = $arr['accent'] ?? 'brand';
            if (!isset($accentRgb[$accent])) $accent = 'brand';
            $rgb = $accentRgb[$accent];
            $iconSvg = $iconPaths[$label] ?? '<circle cx="12" cy="12" r="9"/>';

            $imgUrl = '';
            if ($image !== '') {
                $imgUrl = (str_starts_with($image, 'http') || str_starts_with($image, '/'))
                    ? $image
                    : '/storage/' . ltrim($image, '/');
            }
            $isFirst = $idx === 0;
            $cardClass = 'rg-cat-acc__card' . ($isFirst ? ' expanded' : '');
            $innerClass = 'rg-cat-acc__inner' . ($isFirst ? ' active' : '');
            $cardStyle = '--acc-rgb:' . $rgb;
            if ($imgUrl !== '') {
                $cardStyle .= ';background-image:url(\'' . $this->e($imgUrl) . '\')';
            }

            $out .= '<div class="' . $cardClass . '" style="' . $cardStyle . '" role="button" tabindex="0" aria-label="' . $labelEsc . '">';
            // Always-visible label (icon + title) — fades out when card is expanded
            $out .= '<div class="rg-cat-acc__cardlabel">';
            $out .= '<div class="rg-cat-acc__cardicon" aria-hidden="true">';
            $out .= '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' . $iconSvg . '</svg>';
            $out .= '</div>';
            $out .= '<span class="rg-cat-acc__cardtitle">' . $labelEsc . '</span>';
            $out .= '</div>';

            // Inner content — fades in when card is expanded
            $out .= '<div class="' . $innerClass . '">';
            $out .= '<div class="rg-cat-acc__innerbox">';
            if ($kicker !== '') $out .= '<span class="rg-cat-acc__kicker">' . $kicker . '</span>';
            $out .= '<h3 class="rg-cat-acc__heading">' . $labelEsc . '</h3>';
            if ($desc !== '') $out .= '<p class="rg-cat-acc__desc">' . $desc . '</p>';
            $out .= '<a href="' . $url . '" class="rg-cat-acc__cta">' . $ctaLabel
                . '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
                . '</a>';
            $out .= '</div></div>';
            $out .= '</div>';
        }
        $out .= '</div></div>';

        // CSS — horizontal slider accordion (flex-grow expand on
        // desktop ≥1025px) that collapses to a vertical accordion
        // (height transition) below 1025px.
        $out .= '<style>'
            . '.rg-cat-acc__row{display:flex;flex-direction:row;gap:.5rem;height:520px;border-radius:1rem;overflow:hidden}'
            . '.rg-cat-acc__card{flex:1;position:relative;background-size:cover;background-position:center;background-color:#0f172a;cursor:pointer;transition:flex .5s cubic-bezier(.4,0,.2,1),height .5s cubic-bezier(.4,0,.2,1);overflow:hidden;min-width:0}'
            . '.rg-cat-acc__card::before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(15,23,42,.08) 0%,rgba(15,23,42,.55) 65%,rgba(15,23,42,.85) 100%);pointer-events:none;z-index:1;transition:opacity .35s ease}'
            . '.rg-cat-acc__card.expanded::before{opacity:0}'
            . '.rg-cat-acc__card:focus-visible{outline:3px solid rgb(var(--acc-rgb));outline-offset:-3px}'
            . '@media(min-width:1025px){.rg-cat-acc__card.expanded{flex:3.5}}'
            // Always-visible card label (icon + title at bottom)
            . '.rg-cat-acc__cardlabel{position:absolute;left:1.25rem;right:1.25rem;bottom:1.25rem;z-index:2;color:#fff;display:flex;flex-direction:column;gap:.55rem;transition:opacity .25s ease,transform .35s cubic-bezier(.22,1,.36,1)}'
            . '.rg-cat-acc__card.expanded .rg-cat-acc__cardlabel{opacity:0;transform:translateY(.5rem);pointer-events:none}'
            . '.rg-cat-acc__cardicon{width:2.4rem;height:2.4rem;border-radius:.55rem;background:rgba(255,255,255,.18);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center;color:#fff;border:1px solid rgba(255,255,255,.22)}'
            . '.rg-cat-acc__cardtitle{font-size:1rem;font-weight:800;letter-spacing:-.01em;text-shadow:0 2px 10px rgba(0,0,0,.65)}'
            . '@media(min-width:1025px){.rg-cat-acc__cardtitle{font-size:1.1rem}}'
            // Inner content panel — right column (60%) with white
            // bg + dark editorial text. Image stays visible on the
            // left 40% of the expanded card.
            . '.rg-cat-acc__inner{position:absolute;top:0;right:0;bottom:0;width:60%;z-index:2;display:flex;align-items:center;padding:1.5rem;background:#fff;color:#0f172a;opacity:0;transform:translateX(20px);transition:opacity 0s,transform 0s;pointer-events:none;box-shadow:-12px 0 28px -10px rgba(15,23,42,.18)}'
            . '.rg-cat-acc__inner.active{opacity:1;transform:translateX(0);transition:opacity .5s .4s,transform .55s .4s cubic-bezier(.22,1,.36,1)}'
            . '@media(min-width:1025px){.rg-cat-acc__inner{width:58%;padding:2.25rem 2.5rem}}'
            . '.rg-cat-acc__innerbox{flex:1;max-width:48ch}'
            . '.rg-cat-acc__kicker{display:inline-block;font-size:.65rem;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:#fff;background:rgb(var(--acc-rgb));padding:.3rem .75rem;border-radius:999px;margin-bottom:.9rem;box-shadow:0 4px 12px -2px rgba(var(--acc-rgb),.45)}'
            . '.rg-cat-acc__rule-accent{display:block;width:2.5rem;height:3px;border-radius:2px;background:rgb(var(--acc-rgb));margin-bottom:1rem}'
            . '.rg-cat-acc__heading{font-size:1.5rem;font-weight:800;color:#0f172a;margin:0 0 .85rem;letter-spacing:-.02em;line-height:1.2}'
            . '@media(min-width:1025px){.rg-cat-acc__heading{font-size:2rem}}'
            . '.rg-cat-acc__desc{font-size:.9rem;line-height:1.7;margin:0 0 1.25rem;color:#475569}'
            . '@media(min-width:1025px){.rg-cat-acc__desc{font-size:.95rem}}'
            . '.rg-cat-acc__cta{display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.3rem;background:rgb(var(--acc-rgb));color:#fff;border-radius:.6rem;font-weight:700;font-size:.85rem;letter-spacing:.01em;text-decoration:none;box-shadow:0 8px 22px -6px rgba(var(--acc-rgb),.65);transition:transform .18s ease,box-shadow .18s ease}'
            . '.rg-cat-acc__cta:hover{transform:translateX(4px);box-shadow:0 10px 26px -4px rgba(var(--acc-rgb),.75)}'
            . '.rg-cat-acc__cta svg{width:.9rem;height:.9rem;flex-shrink:0}'
            // Tablet (≤1024px): vertical stack, height-based expand.
            // Inner panel keeps 2-column layout: image visible on
            // left ~40%, content panel on right ~60%.
            . '@media(max-width:1024px){'
            . '.rg-cat-acc__row{flex-direction:column;height:auto;gap:.4rem;border-radius:1rem;overflow:hidden}'
            . '.rg-cat-acc__card{flex:none;width:100%;height:110px;transition:height .5s cubic-bezier(.4,0,.2,1)}'
            . '.rg-cat-acc__card.expanded{height:480px}'
            . '.rg-cat-acc__cardlabel{flex-direction:row;align-items:center;gap:.85rem;bottom:auto;top:50%;transform:translateY(-50%);left:1.25rem;right:1.25rem}'
            . '.rg-cat-acc__card.expanded .rg-cat-acc__cardlabel{transform:translateY(-50%) translateY(.5rem)}'
            . '.rg-cat-acc__cardicon{width:2.2rem;height:2.2rem}'
            . '.rg-cat-acc__cardtitle{font-size:1rem}'
            . '.rg-cat-acc__inner{width:60%;padding:1.5rem 1.75rem}'
            . '}'
            // Mobile (≤480px): same stack but content column larger
            // (65%) so heading + description aren't cramped. Image
            // column stays at 35% — still visually present.
            . '@media(max-width:480px){'
            . '.rg-cat-acc__card.expanded{height:420px}'
            . '.rg-cat-acc__inner{width:65%;padding:1.25rem 1.25rem}'
            . '.rg-cat-acc__heading{font-size:1.35rem}'
            . '.rg-cat-acc__desc{font-size:.85rem;line-height:1.6;margin-bottom:1rem}'
            . '.rg-cat-acc__kicker{font-size:.6rem;padding:.25rem .6rem;margin-bottom:.7rem}'
            . '.rg-cat-acc__cta{padding:.6rem 1rem;font-size:.8rem}'
            . '}'
            // Reduced motion
            . '@media(prefers-reduced-motion:reduce){'
            . '.rg-cat-acc__card,.rg-cat-acc__cardlabel,.rg-cat-acc__inner,.rg-cat-acc__cta{transition:none!important}'
            . '}'
            . '</style>';

        // JS — click/keyboard expands the chosen card and collapses
        // siblings; links inside the card bubble normally so the
        // CTA still navigates.
        $out .= '<script>(function(){var rows=document.querySelectorAll(".rg-cat-acc__row");rows.forEach(function(row){var cards=row.querySelectorAll(".rg-cat-acc__card");var expand=function(card){cards.forEach(function(c){var inner=c.querySelector(".rg-cat-acc__inner");if(c===card){c.classList.add("expanded");if(inner)inner.classList.add("active")}else{c.classList.remove("expanded");if(inner)inner.classList.remove("active")}})};cards.forEach(function(card){card.addEventListener("click",function(e){if(e.target.closest("a"))return;expand(card)});card.addEventListener("keydown",function(e){if(e.key==="Enter"||e.key===" "){if(e.target.closest("a"))return;e.preventDefault();expand(card)}})})})})();</script>';

        $out .= '</section>';
        return $out;
    }

    /* ============================================================
     * Hub-page block types (foods/activities/buys/cultures)
     * ============================================================ */

    /**
     * home_values_grid — 4-card grid of editorial principles or
     * mission values. Each card: emoji icon tile + title + body.
     * Per-card accents cycle through a palette so the row reads
     * colorful without competing palettes.
     *
     * Payload:
     *   heading, subhead
     *   values [] of { title, body, icon }
     *   columns (2|3|4 — default 4 on lg+)
     *   bg (none | light | gradient)
     */
    private function homeValuesGrid(array $p, array $context): string
    {
        if (isset($p['values']) && is_string($p['values'])) {
            $t = trim($p['values']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['values'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $subhead = $this->e(trim((string) ($p['subhead'] ?? '')));
        $values = $p['values'] ?? [];
        if (!is_array($values) || empty($values)) return '';
        $columns = max(2, min(4, (int) ($p['columns'] ?? 4)));
        $gridClass = [2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-2 lg:grid-cols-3', 4 => 'sm:grid-cols-2 lg:grid-cols-4'][$columns];
        $bg = $p['bg'] ?? 'light';
        $bgClass = $bg === 'light' ? 'bg-slate-50' : ($bg === 'gradient' ? 'bg-gradient-to-b from-white to-slate-50' : '');

        // Per-card accent palette — cycles through to give the row
        // some color without overwhelming.
        $palettes = [
            ['bg' => 'bg-blue-50', 'fg' => 'text-blue-700', 'ring' => 'ring-blue-100'],
            ['bg' => 'bg-emerald-50', 'fg' => 'text-emerald-700', 'ring' => 'ring-emerald-100'],
            ['bg' => 'bg-amber-50', 'fg' => 'text-amber-700', 'ring' => 'ring-amber-100'],
            ['bg' => 'bg-rose-50', 'fg' => 'text-rose-700', 'ring' => 'ring-rose-100'],
            ['bg' => 'bg-violet-50', 'fg' => 'text-violet-700', 'ring' => 'ring-violet-100'],
            ['bg' => 'bg-teal-50', 'fg' => 'text-teal-700', 'ring' => 'ring-teal-100'],
        ];

        $out = '<section class="rg-home-values py-16 md:py-20 ' . $bgClass . '" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">';
        $out .= '<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">';
        if ($heading !== '' || $subhead !== '') {
            $out .= '<div class="mb-10 text-center max-w-3xl mx-auto">';
            if ($heading !== '') $out .= '<h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-3">' . $heading . '</h2>';
            if ($subhead !== '') $out .= '<p class="text-base md:text-lg text-slate-600">' . $subhead . '</p>';
            $out .= '</div>';
        }
        $out .= '<div class="grid ' . $gridClass . ' gap-5">';
        foreach ($values as $idx => $v) {
            $vArr = $this->toArrayShapeSimple($v);
            $title = $this->e((string) ($vArr['title'] ?? ''));
            $body = $this->e((string) ($vArr['body'] ?? ''));
            $icon = (string) ($vArr['icon'] ?? '✨');
            if ($title === '') continue;
            $pal = $palettes[$idx % count($palettes)];
            $out .= '<div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col">';
            $out .= '<div class="w-14 h-14 rounded-2xl ' . $pal['bg'] . ' ' . $pal['fg'] . ' flex items-center justify-center text-3xl mb-4 ring-4 ' . $pal['ring'] . '">' . $icon . '</div>';
            $out .= '<h3 class="text-lg font-bold text-slate-900 mb-2 leading-tight">' . $title . '</h3>';
            if ($body !== '') $out .= '<p class="text-sm text-slate-600 leading-relaxed m-0">' . $body . '</p>';
            $out .= '</div>';
        }
        $out .= '</div></div></section>';
        return $out;
    }

    /**
     * hub_hero — eyebrow + colored H1 + paragraphs. Used as the
     * top of each hub page. Title supports {{accent}}…{{/accent}}.
     */
    private function hubHero(array $p, array $context): string
    {
        if (isset($p['paragraphs']) && is_string($p['paragraphs'])) {
            $t = trim($p['paragraphs']);
            $d = json_decode($t, true);
            if (is_array($d)) $p['paragraphs'] = $d;
            elseif ($t !== '') $p['paragraphs'] = array_values(array_filter(preg_split('/\n\n+/', $t)));
        }
        $eyebrow = $this->e(trim((string) ($p['eyebrow'] ?? '')));
        $title = trim((string) ($p['title'] ?? ''));
        $paragraphs = $p['paragraphs'] ?? [];
        if (!is_array($paragraphs)) $paragraphs = [];
        $accent = in_array($p['accent'] ?? 'brand', ['brand', 'amber', 'emerald', 'rose', 'violet', 'teal'], true) ? $p['accent'] : 'brand';
        $accentText = ['brand' => 'text-blue-700', 'amber' => 'text-amber-700', 'emerald' => 'text-emerald-700', 'rose' => 'text-rose-700', 'violet' => 'text-violet-700', 'teal' => 'text-teal-700'][$accent];

        $titleHtml = '';
        if ($title !== '') {
            if (preg_match('/(.*?)\{\{accent\}\}(.+?)\{\{\/accent\}\}(.*)/s', $title, $m)) {
                $titleHtml = $this->e(trim($m[1]))
                    . ($m[1] !== '' ? ' ' : '')
                    . '<span class="' . $accentText . '">' . $this->e(trim($m[2])) . '</span>'
                    . ($m[3] !== '' ? ' ' : '')
                    . $this->e(trim($m[3]));
            } else {
                $titleHtml = $this->e($title);
            }
        }

        $out = '<header class="rg-hub-hero mb-10">';
        if ($eyebrow !== '') $out .= '<div class="text-[11px] uppercase tracking-[0.2em] font-bold ' . $accentText . ' mb-3">' . $eyebrow . '</div>';
        if ($titleHtml !== '') $out .= '<h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 leading-[1.1] mb-6 max-w-4xl">' . $titleHtml . '</h1>';
        if (!empty($paragraphs)) {
            $out .= '<div class="space-y-5 text-base sm:text-lg leading-relaxed text-slate-700 max-w-3xl [&_p]:m-0">';
            foreach ($paragraphs as $para) {
                $p2 = trim((string) $para);
                if ($p2 === '') continue;
                $out .= '<p>' . $this->e($p2) . '</p>';
            }
            $out .= '</div>';
        }
        $out .= '</header>';
        return $out;
    }

    /**
     * hub_category_nav — sticky horizontal pill nav jumping to
     * each category section anchor. Pulls categories from
     * $context['categories'].
     */
    private function hubCategoryNav(array $p, array $context): string
    {
        $cats = $context['categories'] ?? [];
        if (!is_array($cats) || empty($cats)) return '';
        $accent = in_array($p['accent'] ?? 'slate', ['rose', 'emerald', 'violet', 'teal', 'amber', 'slate'], true) ? $p['accent'] : 'slate';
        $hover = [
            'rose' => 'hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700',
            'emerald' => 'hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700',
            'violet' => 'hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700',
            'teal' => 'hover:border-teal-300 hover:bg-teal-50 hover:text-teal-700',
            'amber' => 'hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700',
            'slate' => 'hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700',
        ][$accent];
        $sticky = !isset($p['sticky']) || (bool) $p['sticky'];
        $stickyClass = $sticky ? 'sticky top-16 z-10 bg-white/90 backdrop-blur border-y border-slate-200' : '';

        $out = '<nav class="rg-hub-cat-nav ' . $stickyClass . ' -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 mb-10">';
        $out .= '<div class="flex flex-wrap items-center gap-2 text-sm">';
        $out .= '<span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 mr-1">' . $this->e($p['label'] ?? 'Jump to') . '</span>';
        foreach ($cats as $cat) {
            $key = (string) ($cat['key'] ?? '');
            $label = $this->e((string) ($cat['label'] ?? ''));
            $icon = trim((string) ($cat['icon'] ?? ''));
            if ($key === '' || $label === '') continue;
            $out .= '<a href="#cat-' . $this->e($key) . '" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-slate-200 bg-white ' . $hover . ' font-semibold text-slate-700 transition">';
            if ($icon !== '') $out .= '<span aria-hidden="true">' . $icon . '</span>';
            $out .= '<span>' . $label . '</span></a>';
        }
        $out .= '</div></nav>';
        return $out;
    }

    /**
     * hub_category_grid — renders ONE category as a collapsible
     * accordion containing a grid of item cards. Looks up the
     * category by category_key from $context['categories'] (which
     * the hub controllers pass to BlockRenderer).
     */
    private function hubCategoryGrid(array $p, array $context): string
    {
        $key = (string) ($p['category_key'] ?? '');
        $cats = $context['categories'] ?? [];
        if ($key === '' || !is_array($cats) || empty($cats)) return '';
        $match = null;
        foreach ($cats as $cat) {
            if (($cat['key'] ?? null) === $key) { $match = $cat; break; }
        }
        if (!$match) return '';

        $urlPrefix = rtrim((string) ($p['item_url_prefix'] ?? ''), '/');
        $perRow = max(2, min(6, (int) ($p['cards_per_row'] ?? 4)));
        $gridClass = [
            2 => 'grid-cols-2',
            3 => 'grid-cols-2 md:grid-cols-3',
            4 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
            5 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-5',
            6 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-6',
        ][$perRow];
        $asAccordion = !isset($p['as_accordion']) || (bool) $p['as_accordion'];

        $label = $this->e(trim((string) ($match['label'] ?? '')));
        $icon = trim((string) ($match['icon'] ?? ''));
        $intro = $this->e(trim((string) ($match['intro'] ?? '')));
        $items = $match['items'] ?? [];

        if ($asAccordion) {
            $out = '<details class="rg-accordion rg-hub-category mb-4 scroll-mt-32 rounded-2xl border border-slate-200 bg-white overflow-hidden" id="cat-' . $this->e($key) . '" data-rg-accordion-group="categories">';
            $out .= '<summary class="flex items-center justify-between gap-4 p-5 sm:p-6 cursor-pointer select-none hover:bg-slate-50 transition">';
            $out .= '<div class="flex items-center gap-3 min-w-0">';
            if ($icon !== '') $out .= '<span class="text-2xl sm:text-3xl shrink-0" aria-hidden="true">' . $icon . '</span>';
            $out .= '<div class="min-w-0">';
            if ($label !== '') $out .= '<div class="text-lg sm:text-xl font-extrabold text-slate-900 truncate">' . $label . '</div>';
            if ($intro !== '') $out .= '<div class="text-sm text-slate-500 truncate">' . $intro . '</div>';
            $out .= '</div></div>';
            $out .= '<svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>';
            $out .= '</summary>';
            $out .= '<div class="p-5 sm:p-6 pt-2 border-t border-slate-100">';
            if (!empty($items)) {
                $out .= '<div class="grid ' . $gridClass . ' gap-3">';
                foreach ($items as $item) {
                    $slug = (string) ($item['slug'] ?? '');
                    $name = $this->e((string) ($item['name'] ?? ''));
                    $note = $this->e((string) ($item['note'] ?? $item['description'] ?? ''));
                    if ($slug === '' || $name === '') continue;
                    $href = $urlPrefix !== '' ? $urlPrefix . '/' . $slug : '#';
                    $out .= '<a href="' . $this->e($href) . '" class="block rounded-xl border border-slate-200 bg-white p-4 hover:border-slate-300 hover:shadow-md transition-all">';
                    $out .= '<div class="font-bold text-slate-900 text-sm mb-1">' . $name . '</div>';
                    if ($note !== '') $out .= '<div class="text-xs text-slate-500 line-clamp-3 leading-snug">' . $note . '</div>';
                    $out .= '</a>';
                }
                $out .= '</div>';
            }
            $out .= '</div></details>';
        } else {
            $out = '<section class="rg-hub-category my-12" id="cat-' . $this->e($key) . '">';
            $out .= '<div class="mb-6">';
            if ($icon !== '') $out .= '<div class="text-3xl mb-2" aria-hidden="true">' . $icon . '</div>';
            if ($label !== '') $out .= '<h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">' . $label . '</h2>';
            if ($intro !== '') $out .= '<p class="text-slate-600 leading-relaxed mt-2 max-w-3xl">' . $intro . '</p>';
            $out .= '</div>';
            $out .= '<div class="grid ' . $gridClass . ' gap-3">';
            foreach ($items as $item) {
                $slug = (string) ($item['slug'] ?? '');
                $name = $this->e((string) ($item['name'] ?? ''));
                $note = $this->e((string) ($item['note'] ?? $item['description'] ?? ''));
                if ($slug === '' || $name === '') continue;
                $href = $urlPrefix !== '' ? $urlPrefix . '/' . $slug : '#';
                $out .= '<a href="' . $this->e($href) . '" class="block rounded-xl border border-slate-200 bg-white p-4 hover:border-slate-300 hover:shadow-md transition-all">'
                    . '<div class="font-bold text-slate-900 text-sm mb-1">' . $name . '</div>';
                if ($note !== '') $out .= '<div class="text-xs text-slate-500 line-clamp-3 leading-snug">' . $note . '</div>';
                $out .= '</a>';
            }
            $out .= '</div></section>';
        }
        return $out;
    }

    /**
     * hub_footer_rail — heading + body + pill link rail. Closes
     * out a hub page with related-pages CTAs.
     */
    private function hubFooterRail(array $p, array $context): string
    {
        if (isset($p['links']) && is_string($p['links'])) {
            $t = trim($p['links']);
            if ($t !== '' && ($t[0] === '[' || $t[0] === '{')) {
                $d = json_decode($t, true);
                if (is_array($d)) $p['links'] = $d;
            }
        }
        $heading = $this->e(trim((string) ($p['heading'] ?? '')));
        $body = $this->e(trim((string) ($p['body'] ?? '')));
        $links = $p['links'] ?? [];
        if (!is_array($links)) $links = [];
        $themeMap = [
            'slate' => 'bg-slate-100 hover:bg-slate-200 text-slate-800',
            'amber' => 'bg-amber-100 hover:bg-amber-200 text-amber-900',
            'indigo' => 'bg-indigo-100 hover:bg-indigo-200 text-indigo-900',
            'rose' => 'bg-rose-100 hover:bg-rose-200 text-rose-900',
            'emerald' => 'bg-emerald-100 hover:bg-emerald-200 text-emerald-900',
            'violet' => 'bg-violet-100 hover:bg-violet-200 text-violet-900',
            'teal' => 'bg-teal-100 hover:bg-teal-200 text-teal-900',
        ];

        $out = '<section class="rg-hub-footer mt-12 pt-10 border-t border-slate-200">';
        if ($heading !== '') $out .= '<h2 class="text-xl sm:text-2xl font-bold text-slate-900 mb-3">' . $heading . '</h2>';
        if ($body !== '') $out .= '<p class="text-slate-700 leading-relaxed max-w-3xl mb-5">' . $body . '</p>';
        if (!empty($links)) {
            $out .= '<div class="flex flex-wrap gap-2 text-sm">';
            foreach ($links as $link) {
                $label = $this->e((string) ($link['label'] ?? ''));
                $url = $this->e((string) ($link['url'] ?? '#'));
                $theme = $link['theme'] ?? 'slate';
                $cls = $themeMap[$theme] ?? $themeMap['slate'];
                if ($label === '') continue;
                $out .= '<a href="' . $url . '" class="inline-flex items-center gap-1 px-4 py-2 rounded-full ' . $cls . ' font-semibold">' . $label . '</a>';
            }
            $out .= '</div>';
        }
        $out .= '</section>';
        return $out;
    }
}
