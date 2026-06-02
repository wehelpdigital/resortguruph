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
