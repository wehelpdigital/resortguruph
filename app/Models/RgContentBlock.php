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
