<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Adds the destination-specific content blocks that food/restaurant
 * pages already carry — pulling the per-destination data out of
 * database/data/destinations.php so each resort page renders with a
 * real `short_version`, `quick_facts`, `local_tip`, `attractions`,
 * `editor_rating`, and `tag_pills` row instead of just the auto-
 * resolved recommendation strips.
 *
 * Each block is added only when missing — re-runs are safe and pick up
 * any new destinations added later. All blocks land just above the
 * existing `nearby_destinations` block so the bottom narrative stays:
 *   content → enrichments → recommendations → byline.
 */
class EnrichDestinationContentBlocksSeeder extends Seeder
{
    /** Loaded once per run from destinations.php. */
    private array $destData = [];

    /** Sorted key list (longest first) used for slug→destination resolution. */
    private array $destKeysByLength = [];

    /** Fallback per cluster_tag when no slug match. */
    private array $clusterDefaults = [
        'rizal' => 'antipolo',
        'bulacan' => 'bulacan-province',
        'pampanga' => 'pampanga-province',
        'batangas' => 'batangas-city',
        'cavite' => 'tagaytay',
        'laguna' => 'pansol',
        'quezon' => 'lucena',
        'bicol' => 'albay-legazpi',
        'metro-manila' => 'manila',
        'mindanao' => 'davao-city',
        'visayas' => 'cebu-city',
        'palawan' => 'el-nido',
        'north-luzon' => 'la-union',
        'other' => '_default',
    ];

    public function run(): void
    {
        $this->destData = require database_path('data/destinations.php');
        $keys = array_keys($this->destData);
        usort($keys, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $this->destKeysByLength = $keys;

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->where('k.category', 'resort')
            ->select(
                'p.id as page_id',
                'p.h1 as h1',
                'k.slug as keyword_slug',
                'k.phrase as keyword_phrase',
                'k.cluster_tag as cluster_tag'
            )
            ->get();

        $this->command->info('Destination pages to enrich: ' . $pages->count());

        $stats = [
            'short_version' => 0,
            'editor_rating' => 0,
            'quick_facts' => 0,
            'attractions' => 0,
            'tag_pills' => 0,
            'local_tip' => 0,
        ];

        foreach ($pages as $page) {
            $destKey = $this->resolveDestination($page->keyword_slug, $page->cluster_tag);
            if (!isset($this->destData[$destKey])) continue;
            $dest = $this->destData[$destKey];
            $placeName = $dest['name'] ?? trim((string) ($page->h1 ?: $page->keyword_phrase));

            $this->insertIfMissing($page->page_id, 'short_version', fn () => $this->shortVersionPayload($dest, $placeName), $stats);
            $this->insertIfMissing($page->page_id, 'editor_rating', fn () => $this->editorRatingPayload($placeName), $stats);
            $this->insertIfMissing($page->page_id, 'quick_facts', fn () => $this->quickFactsPayload($dest), $stats);
            $this->insertIfMissing($page->page_id, 'attractions', fn () => $this->attractionsPayload($dest, $destKey, $placeName), $stats);
            $this->insertIfMissing($page->page_id, 'tag_pills', fn () => $this->tagPillsPayload($dest), $stats);
            $this->insertIfMissing($page->page_id, 'local_tip', fn () => $this->localTipPayload($dest, $placeName), $stats);
        }

        foreach ($stats as $type => $count) {
            $this->command->info("  {$type}: {$count} inserted");
        }
    }

    /**
     * Three-layer resolution: (1) exact slug-key match, (2) longest-key
     * substring scan, (3) cluster default. The middle pass uses keys
     * sorted longest-first so "san-juan-la-union" wins over plain
     * "la-union" when both could match.
     */
    private function resolveDestination(string $slug, ?string $cluster): string
    {
        $slug = strtolower($slug);
        if (isset($this->destData[$slug])) return $slug;

        foreach ($this->destKeysByLength as $key) {
            if ($key === '_default') continue;
            if ($key === '') continue;
            if (str_contains($slug, $key)) return $key;
        }

        return $this->clusterDefaults[$cluster] ?? '_default';
    }

    /**
     * Insert above the existing nearby_destinations block, falling back
     * to above the author block, falling back to end-of-page. Existing
     * blocks past that point all shift up by 1 so the new block slots
     * in cleanly.
     */
    private function insertIfMissing(int $pageId, string $type, callable $payloadFactory, array &$stats): void
    {
        $existing = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', $type)
            ->exists();
        if ($existing) return;

        // Anchor blocks tried in order — first match wins.
        $anchorOrder = ['nearby_destinations', 'related_blogs', 'author'];
        $anchorRow = null;
        foreach ($anchorOrder as $anchorType) {
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

    private function shortVersionPayload(array $dest, string $placeName): array
    {
        $body = trim((string) ($dest['voice_intro'] ?? ''));
        if ($body === '') {
            $body = $placeName . ' is a Philippines destination worth a weekend or a long itinerary, depending on how deep you want to go.';
        } else {
            // Cap at ~220 chars so the callout stays readable.
            $body = mb_strimwidth($body, 0, 220, '…', 'UTF-8');
        }
        return [
            'eyebrow' => 'The short version',
            'body' => $body,
            'accent_color' => 'amber',
        ];
    }

    private function editorRatingPayload(string $placeName): array
    {
        return [
            // Renderer reads `title` for the small uppercase eyebrow.
            'title' => 'Our take on ' . $placeName,
            'overall' => 4.2,
            'criteria' => [
                ['name' => 'Weekenders', 'score' => 4.5],
                ['name' => 'Family',     'score' => 4.0],
                ['name' => 'Food scene', 'score' => 4.2],
                ['name' => 'Walkable',   'score' => 3.8],
            ],
            'summary' => 'A solid pick for a weekend break with enough variety for a longer stay. Worth booking a couple of nights to do the area justice.',
        ];
    }

    /**
     * quick_facts renders a `cards[]` array. Each card is shaped
     * { icon, color, big, label, detail } — `big` is the one-glance
     * value (e.g. "2-3 hrs"), `label` is the eyebrow above it, `detail`
     * is the small line below.
     */
    private function quickFactsPayload(array $dest): array
    {
        $cards = [];
        if (!empty($dest['transit'])) {
            // First sentence as the "big" line, the rest folds into detail.
            [$big, $detail] = $this->splitFirstSentence((string) $dest['transit']);
            $cards[] = [
                'icon' => 'clock',
                'color' => 'blue',
                'label' => 'Travel time',
                'big' => $big,
                'detail' => $detail,
            ];
        }
        if (!empty($dest['season'])) {
            [$big, $detail] = $this->splitFirstSentence((string) $dest['season']);
            $cards[] = [
                'icon' => 'calendar',
                'color' => 'emerald',
                'label' => 'Best time',
                'big' => $big,
                'detail' => $detail,
            ];
        }
        if (!empty($dest['tip'])) {
            [$big, $detail] = $this->splitFirstSentence((string) $dest['tip']);
            $cards[] = [
                'icon' => 'warning',
                'color' => 'amber',
                'label' => 'Local rule',
                'big' => $big,
                'detail' => $detail,
            ];
        }
        // Pad to 4 cards so the grid is balanced.
        if (count($cards) < 4) {
            $cards[] = [
                'icon' => 'location',
                'color' => 'slate',
                'label' => 'Where it is',
                'big' => $dest['name'] ?? 'Philippines',
                'detail' => 'Philippines',
            ];
        }
        return ['cards' => array_slice($cards, 0, 4)];
    }

    /**
     * Split a sentence on the first full-stop so the quick-facts card
     * gets a short headline plus a smaller follow-up line. Falls back
     * to a length-based split when no period is present.
     */
    private function splitFirstSentence(string $text): array
    {
        $text = trim($text);
        if ($text === '') return ['', ''];
        // Prefer the period-based split when the first sentence is
        // short enough to fit the big-text slot.
        if (preg_match('~^(.{8,80}?[\.!?])\s+(.*)$~s', $text, $m)) {
            return [trim($m[1]), trim($m[2])];
        }
        // Length-based fallback — first ~60 chars headline, rest detail.
        if (mb_strlen($text) <= 80) return [$text, ''];
        $cut = mb_substr($text, 0, 60, 'UTF-8');
        // Backtrack to the last word boundary for a cleaner break.
        if (preg_match('~^(.+)\s\S+$~u', $cut, $m)) $cut = $m[1];
        return [trim($cut) . '…', trim(mb_substr($text, mb_strlen($cut), null, 'UTF-8'))];
    }

    private function attractionsPayload(array $dest, string $destKey, string $placeName): array
    {
        $spots = $dest['spots'] ?? [];
        if (!$spots) return ['items' => []];

        $items = [];
        foreach ($spots as $spot) {
            if (!is_array($spot) || empty($spot['name'])) continue;
            $slug = preg_replace('~[^a-z0-9]+~', '-', mb_strtolower($spot['name'], 'UTF-8'));
            $slug = trim($slug, '-');
            // Image precedence chain: per-spot → per-destination → none.
            $candidates = [
                "rg-media/spots/{$destKey}-{$slug}.jpg",
                "rg-media/spots/{$slug}.jpg",
                "rg-media/destinations/{$destKey}-1.jpg",
            ];
            $image = '';
            foreach ($candidates as $path) {
                $abs = public_path('storage/' . $path);
                if (file_exists($abs)) { $image = '/storage/' . $path; break; }
            }
            $items[] = [
                'name' => $spot['name'],
                'short' => '',
                'blurb' => $spot['desc'] ?? '',
                'image' => $image,
                'url' => $spot['url'] ?? '',
            ];
            if (count($items) >= 6) break;
        }
        return [
            'heading' => "What's in " . $placeName . '?',
            'intro' => '',
            'items' => $items,
        ];
    }

    private function tagPillsPayload(array $dest): array
    {
        $foods = $dest['food'] ?? [];
        if (!$foods) return ['items' => []];

        $palettes = ['amber', 'rose', 'emerald', 'indigo', 'pink', 'cyan', 'violet', 'slate'];
        $items = [];
        foreach (array_slice((array) $foods, 0, 6) as $i => $food) {
            // Some entries are strings, some are ['name' => 'X', 'desc' => '...'].
            $text = is_array($food) ? ($food['name'] ?? '') : (string) $food;
            $text = mb_strimwidth(trim($text), 0, 60, '…', 'UTF-8');
            if ($text === '') continue;
            $items[] = [
                'text' => $text,
                'color' => $palettes[$i % count($palettes)],
            ];
        }
        return [
            'label' => 'Local food finds',
            'items' => $items,
        ];
    }

    private function localTipPayload(array $dest, string $placeName): array
    {
        $body = trim((string) ($dest['tip'] ?? ''));
        if ($body === '') {
            $body = 'Locals in ' . $placeName . ' will tell you the same thing — leave early, eat at the small spots, and skip whatever has a long queue.';
        }
        return [
            'eyebrow' => 'Local tip from ' . $placeName,
            'body' => $body,
            'color' => 'amber',
        ];
    }
}
