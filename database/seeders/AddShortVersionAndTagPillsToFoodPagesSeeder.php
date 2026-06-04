<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fills the last two gaps on food/restaurant keyword pages: a TLDR
 * `short_version` callout above the long-form copy, and a cuisine
 * `tag_pills` row inferred from the keyword slug (japanese →
 * ramen/sushi/izakaya, chinese → dim sum/noodles/hotpot, etc.).
 *
 * Each pair lands just above the existing `editor_rating` block so the
 * narrative reads: hero → TLDR callout → editor verdict → cuisine pills
 * → main content. Idempotent — pages that already have either block
 * are skipped.
 */
class AddShortVersionAndTagPillsToFoodPagesSeeder extends Seeder
{
    /**
     * Cuisine-keyword → (short_version tagline, tag_pills array) lookup.
     * The seeder scans the slug for each key in order and picks the first
     * hit, so multi-word cuisine markers (e.g. "fine-dining") should
     * precede single-word ones.
     */
    private array $cuisines = [
        'japanese' => [
            'tagline' => 'ramen counters, sushi bars, and izakaya nights — the Japanese spots locals queue at, not the rebranded mall chains.',
            'tags' => ['Japanese', 'ramen', 'sushi', 'izakaya', 'wagyu'],
        ],
        'korean' => [
            'tagline' => 'KBBQ unli, kimchi sides, and the bibimbap spots that taste like Itaewon — sorted by who locals actually book.',
            'tags' => ['Korean', 'KBBQ', 'kimchi', 'bibimbap', 'unli sets'],
        ],
        'chinese' => [
            'tagline' => 'dim sum trolleys, noodle houses, and Cantonese kitchens — the spots Manila families have been ordering from for decades.',
            'tags' => ['Chinese', 'dim sum', 'noodles', 'hotpot', 'roast'],
        ],
        'italian' => [
            'tagline' => 'wood-fired pizza, fresh pasta, and the wine bars that actually know the menu — Italian spots vetted past the marketing.',
            'tags' => ['Italian', 'pasta', 'pizza', 'risotto', 'wine bar'],
        ],
        'filipino' => [
            'tagline' => 'sinigang, adobo, kare-kare — the Filipino kitchens cooking it like lola does, not the tourist-trap versions.',
            'tags' => ['Filipino', 'sinigang', 'adobo', 'lechon', 'kare-kare'],
        ],
        'seafood' => [
            'tagline' => 'grilled prawns, fresh catch, and seaside kitchens — the seafood places worth driving for, not the freezer-section knockoffs.',
            'tags' => ['Seafood', 'grilled', 'prawns', 'crab', 'fresh catch'],
        ],
        'steak' => [
            'tagline' => 'wagyu cuts, ribeye, dry-aged steaks — the kitchens that know how to handle premium beef without ruining it.',
            'tags' => ['Steak', 'ribeye', 'wagyu', 'dry-aged', 'grill'],
        ],
        'fine-dining' => [
            'tagline' => 'tasting menus, wine pairings, and chef-driven kitchens — the fine-dining rooms worth the reservation effort.',
            'tags' => ['Fine dining', 'tasting menu', 'wine pairing', 'chef-driven', 'reservation'],
        ],
        'buffet' => [
            'tagline' => 'unlimited spreads, prime-cut carving stations, and dessert bars — buffets ranked by spread quality, not just price.',
            'tags' => ['Buffet', 'unli', 'carving station', 'dessert bar', 'eat-all-you-can'],
        ],
        'sushi' => [
            'tagline' => 'sushi bars, sashimi platters, and omakase counters — Japanese spots that take the fish seriously.',
            'tags' => ['Sushi', 'sashimi', 'omakase', 'nigiri', 'maki'],
        ],
        'fast-food' => [
            'tagline' => 'Jollibee, Mang Inasal, Chowking — the Filipino fast-food chains that always have a queue for a reason.',
            'tags' => ['Fast food', 'Jollibee', 'Mang Inasal', 'Chowking', 'McDo PH'],
        ],
        'family' => [
            'tagline' => 'big tables, kid-friendly menus, and parking that actually fits a van — restaurants that handle a family of six without drama.',
            'tags' => ['Family-friendly', 'kid menu', 'big tables', 'parking', 'group dining'],
        ],
        'floating' => [
            'tagline' => 'floating restaurants on the river — Filipino dining experiences you slow-cruise through, with seafood as the headliner.',
            'tags' => ['Floating restaurant', 'river cruise', 'seafood', 'Loboc', 'live music'],
        ],
        'overlooking' => [
            'tagline' => 'restaurants with the view — the spots where the sunset, the skyline, or the rice terraces are part of the menu.',
            'tags' => ['Scenic view', 'sunset', 'skyline', 'rooftop', 'al fresco'],
        ],
    ];

    public function run(): void
    {
        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->where('k.category', 'food')
            ->select('p.id as page_id', 'k.slug as keyword_slug', 'k.phrase as keyword_phrase', 'k.cluster_tag as cluster_tag')
            ->get();

        $this->command->info('Food pages to process: ' . $pages->count());
        $stats = ['short_version' => 0, 'tag_pills' => 0];

        foreach ($pages as $page) {
            [$tagline, $tags] = $this->resolveCuisine($page->keyword_slug);

            $this->insertIfMissing(
                $page->page_id,
                'short_version',
                fn () => $this->shortVersionPayload($tagline, $page->keyword_phrase),
                $stats
            );
            $this->insertIfMissing(
                $page->page_id,
                'tag_pills',
                fn () => $this->tagPillsPayload($tags),
                $stats
            );
        }

        foreach ($stats as $type => $count) {
            $this->command->info("  {$type}: {$count} inserted");
        }
    }

    /**
     * Returns [tagline, tagsArray] for the slug. Picks the first cuisine
     * key found, falling back to a generic restaurant tagline + pills.
     */
    private function resolveCuisine(string $slug): array
    {
        foreach ($this->cuisines as $key => $bundle) {
            if (str_contains($slug, $key)) return [$bundle['tagline'], $bundle['tags']];
        }
        return [
            'a mix of Filipino chains, casual spots, and the kind of cafes locals actually rebook — sorted by who serves real food, not just the photogenic ones.',
            ['Filipino chains', 'fast-casual', 'café spots', 'comfort food', 'merienda'],
        ];
    }

    /**
     * Anchor insertion above the editor_rating block — falling back
     * through related/nearby/author when editor_rating isn't there yet.
     */
    private function insertIfMissing(int $pageId, string $type, callable $payloadFactory, array &$stats): void
    {
        if (DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', $type)
            ->exists()) {
            return;
        }

        $anchorOrder = ['editor_rating', 'short_version', 'attractions', 'how_to_get_to', 'nearby_destinations', 'related_blogs', 'author'];
        $anchorRow = null;
        foreach ($anchorOrder as $anchorType) {
            if ($anchorType === $type) continue;
            $anchorRow = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', $anchorType)
                ->orderBy('sort_order')
                ->first();
            if ($anchorRow) break;
        }

        $targetSortOrder = $anchorRow
            ? (int) $anchorRow->sort_order
            : ((int) (DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->max('sort_order') ?? 0) + 1);

        DB::transaction(function () use ($pageId, $type, $payloadFactory, $anchorRow, $targetSortOrder) {
            if ($anchorRow) {
                DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $pageId)
                    ->where('sort_order', '>=', $targetSortOrder)
                    ->orderByDesc('sort_order')
                    ->get()
                    ->each(function ($row) {
                        DB::table('rg_content_blocks')
                            ->where('id', $row->id)
                            ->update(['sort_order' => $row->sort_order + 1]);
                    });
            }
            DB::table('rg_content_blocks')->insert([
                'owner_type' => 'seo_page',
                'owner_id' => $pageId,
                'sort_order' => $targetSortOrder,
                'block_type' => $type,
                'payload_json' => json_encode($payloadFactory()),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $stats[$type]++;
    }

    private function shortVersionPayload(string $tagline, string $phrase): array
    {
        return [
            'eyebrow' => 'The short version',
            'body' => 'Looking at the ' . $phrase . ' scene? In short, ' . $tagline,
            'accent_color' => 'amber',
        ];
    }

    private function tagPillsPayload(array $tags): array
    {
        $palettes = ['amber', 'rose', 'emerald', 'indigo', 'pink', 'cyan', 'violet', 'slate'];
        $items = [];
        foreach ($tags as $i => $text) {
            $items[] = [
                'text' => $text,
                'color' => $palettes[$i % count($palettes)],
            ];
        }
        return [
            'label' => 'What you will find',
            'items' => $items,
        ];
    }
}
