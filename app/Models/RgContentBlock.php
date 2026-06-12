<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RgContentBlock extends Model
{
    protected $table = 'rg_content_blocks';

    protected $fillable = ['owner_type', 'owner_id', 'sort_order', 'block_type', 'payload_json'];

    /**
     * Every block type the admin builder is allowed to create and the
     * BlockRenderer is allowed to render. Adding a new type here means:
     *   - BlockRenderer::renderBlock() has a matching match-arm
     *   - builder.blade.php has defaultPayload + editorForm + previewHtml entries
     * The mother-admin RgBlocksController::save() validates against this list,
     * so it must stay in sync with both ends.
     */
    public const ALLOWED_TYPES = [
        // Generic blocks (Phase 1)
        'heading', 'rich_text', 'image', 'gallery', 'video', 'faq',
        'cta', 'two_column', 'listing_slot', 'quote', 'divider', 'custom_html',
        // Custom Resort Guru elements that match what the food / keyword
        // page seeders render — admin can now author these directly.
        'hero_slider', 'quick_facts', 'editor_rating',
        'listing_block', 'attractions', 'how_to_get_to',
        // Structured prose — replaces rich_text for h2-bounded sections.
        // Authored as a heading + ordered list of paragraphs so the
        // editor stays list-based instead of a single Quill blob.
        'text_section',
        // Standardized section templates covering recurring patterns from
        // the seeded pages so every section has its own editor instead of
        // leaving them as raw custom_html.
        'short_version', 'pros_cons', 'summary_accordion',
        'image_text_pair', 'traveler_reviews', 'map_embed',
        'local_tip', 'related_guides', 'data_table',
        // Article-body section opener (H2 + small subtitle lede), replaces
        // the hardcoded "What's in [Area]?" header on keyword pages.
        'section_header',
        // Inline content extracted from seeded text blocks: pill rows for
        // cuisines/tags + a comparison grid for third-party guides.
        'tag_pills', 'external_guides',
        // Author / byline card — typically sits at the bottom of an SEO
        // page so the editorial voice is attributed without competing with
        // the listing band at the top.
        'author',
        // Cross-vertical cards: nearby destination keyword pages + related
        // blog posts. Both render on food / restaurant pages so visitors
        // can dive deeper after the main content stream.
        'nearby_destinations', 'related_blogs',
        // Vertical-list variant of quick_facts — destinations use this
        // instead of the 4-up grid because their facts (travel time,
        // best season, local rules) read better as a scannable list
        // than a card row.
        'facts_list',
        // Researched origin / history card for a venue or district.
        // Renders as a parchment-style framed section with eyebrow +
        // headline + multi-paragraph body. Authored from web-sourced
        // facts, not invented prose — used for SEO E-E-A-T signal and
        // to give visitors real context on what the place is.
        'place_history',
        // "Foods to try" grid for destinations — lists the actual
        // dishes (papaitan, pigar-pigar, pinakbet) not restaurants.
        // Each item carries a name, where-to-find it, blurb, image.
        'foods_to_try',
        // /destinations page custom block types. Each one renders
        // a distinct section of the legacy /destinations view as a
        // builder element so admins can edit the section in
        // /resort-guru-static-edit for the destinations row:
        //   dest_hero_search     — gradient hero + breadcrumb +
        //                          stats pills + powerful typeahead
        //                          search (filter tabs + chips +
        //                          grouped result panel).
        //   dest_featured_slider — Splide carousel of featured
        //                          tourist spots ("Tourist spots
        //                          worth the trip").
        //   dest_region_clusters — Sticky "Jump to region" pill nav
        //                          + cluster grids of keyword cards.
        'dest_hero_search', 'dest_featured_slider', 'dest_region_clusters',
        // Homepage custom block types — each reads live data from
        // HomeController context (featuredKeywords / regions /
        // featuredResorts / latestPosts / stats).
        //   home_hero_centered  → centered gradient hero + stats row
        //   home_keyword_grid   → 3-up popular destinations grid
        //   home_region_grid    → region-cluster summary cards
        //   home_resort_grid    → featured properties with images
        //   home_blog_strip     → 3-up blog post cards
        //   home_cta_band       → full-bleed brand-colored CTA band
        'home_hero_centered', 'home_keyword_grid', 'home_region_grid',
        'home_resort_grid', 'home_blog_strip', 'home_cta_band',
        // Page-header content elements migrated out of rg_seo_pages
        // columns into blocks so the admin can reorder / remove / add
        // them like any other content. subtitle_intro replaces the
        // hardcoded italic line under H1; tldr_card + wwww_card port
        // the partials/summary-blocks accordion into two independent
        // collapsible blocks.
        'subtitle_intro', 'tldr_card', 'wwww_card',
        // Page-top hardcoded includes converted to blocks so the
        // admin can reorder / remove / add them like any other
        // content. social_share = partials.social-share row;
        // we_recommend_band = the "We Recommend" listings band
        // header + listings partial (branches on keyword.category).
        'social_share', 'we_recommend_band',
        // Page-tail hardcoded sections converted to blocks. Each
        // only renders when its data is non-empty + the keyword
        // category matches (restaurant_recs + adventures only fire
        // on non-food keyword pages with active listings; reviews
        // only fires when at least one review is published).
        'restaurant_recs_band', 'adventures_band', 'reviews_band',
    ];

    public function getPayloadAttribute(): array
    {
        if (!$this->payload_json) return [];
        $decoded = json_decode($this->payload_json, true);
        return is_array($decoded) ? $decoded : [];
    }

    public static function forOwner(string $type, int $id)
    {
        return self::where('owner_type', $type)
            ->where('owner_id', $id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
