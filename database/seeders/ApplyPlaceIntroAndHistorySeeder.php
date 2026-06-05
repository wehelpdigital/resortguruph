<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies researched place_intro + place_history content from
 * database/data/researched_place_intro_history.json to food and resort
 * pages whose slug contains the venue token. Two writes per page:
 *
 *   1. The first text_section block (the post-header intro paragraph)
 *      gets its body replaced with the researched place_intro — a
 *      description of WHAT the place is, NOT food.
 *   2. A new `place_history` block is inserted immediately after that
 *      text_section, carrying the heading, body, founded year, and
 *      citation. If a place_history block already exists on the page
 *      we overwrite it instead of duplicating.
 *
 * Sort_order positioning: place_history slots directly after the first
 * text_section so the narrative reads:
 *   header → TLDR cluster → listings → place intro (about the PLACE)
 *   → place history → existing food content → recommendations → byline
 *
 * Idempotent — re-runs replace the same blocks with the same content
 * and never duplicate.
 */
class ApplyPlaceIntroAndHistorySeeder extends Seeder
{
    /**
     * Token → extra slug substrings that should also receive the same
     * content (shared with the apply destination seeder so province
     * slugs inherit the city-level researched content).
     */
    private array $tokenAliases = [
        'cebu' => ['cebu-city', 'cebu-ayala', 'cebu-it-park', 'ayala-cebu', 'in-cebu'],
        'manila' => ['old-manila', 'binondo', 'malate', 'ermita', 'intramuros'],
        'bgc' => ['bgc-high-street', 'high-street-bgc', 'in-bgc', 'uptown-bgc'],
        'high-street' => ['bonifacio-high-street'],
        'mall-of-asia' => ['mall-of-asia-seaside', 'moa-seaside', 'in-moa'],
        'megamall' => ['sm-megamall'],
        'shangri-la' => ['shangrila-mall', 'edsa-shangrila'],
    ];

    public function run(): void
    {
        $path = database_path('data/researched_place_intro_history.json');
        if (!is_file($path)) {
            $this->command->warn("Missing {$path} — skip.");
            return;
        }
        $entries = json_decode((string) file_get_contents($path), true) ?: [];
        $this->command->info('Researched place entries: ' . count($entries));

        $stats = ['pages' => 0, 'intros' => 0, 'histories' => 0];

        foreach ($entries as $entry) {
            $token = $entry['token'] ?? null;
            if (!$token) continue;

            $needles = array_merge([$token], $this->tokenAliases[$token] ?? []);
            $rows = DB::table('rg_seo_pages as p')
                ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
                ->where(function ($q) use ($needles) {
                    foreach ($needles as $needle) $q->orWhere('k.slug', 'like', '%' . $needle . '%');
                })
                ->select('p.id as page_id', 'k.slug as slug')
                ->get();

            foreach ($rows as $row) {
                $stats['pages']++;
                $this->writePlaceIntro((int) $row->page_id, (string) ($entry['place_intro'] ?? ''), $stats);
                $this->writePlaceHistory((int) $row->page_id, (array) ($entry['history'] ?? []), $stats);
            }
            $this->command->info(str_pad($token, 24) . " · {$rows->count()} pages");
        }

        $this->command->info("Pages updated: {$stats['pages']}");
        $this->command->info("  intros written: {$stats['intros']}");
        $this->command->info("  histories written: {$stats['histories']}");
    }

    /**
     * Find the lowest-sort_order text_section on the page and overwrite
     * its body with the researched place_intro. Heading stays whatever
     * the prior seed set (often empty for the opener). Pages that lack
     * any text_section are skipped quietly — the intro lives in the
     * existing text stream so we never invent a new structural slot.
     */
    private function writePlaceIntro(int $pageId, string $intro, array &$stats): void
    {
        $intro = trim($intro);
        if ($intro === '') return;
        $row = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->where('block_type', 'text_section')
            ->orderBy('sort_order')
            ->first();
        if (!$row) return;

        $payload = json_decode((string) $row->payload_json, true) ?: [];
        $payload['body'] = $intro;
        DB::table('rg_content_blocks')->where('id', $row->id)->update([
            'payload_json' => json_encode($payload),
            'updated_at' => now(),
        ]);
        $stats['intros']++;
    }

    /**
     * Insert or update a single place_history block on the page,
     * positioned immediately after the first text_section (so the
     * intro → history → main content flow holds). When a block already
     * exists we overwrite it in place; when not, we shift subsequent
     * blocks to make room.
     */
    private function writePlaceHistory(int $pageId, array $history, array &$stats): void
    {
        $heading = trim((string) ($history['heading'] ?? ''));
        $body = trim((string) ($history['body'] ?? ''));
        if ($heading === '' || $body === '') return;

        $payload = [
            'eyebrow' => 'Local history',
            'heading' => $heading,
            'body' => $body,
            'founded' => trim((string) ($history['founded'] ?? '')),
            'citation_label' => trim((string) ($history['citation_label'] ?? '')),
            'citation_url' => trim((string) ($history['citation_url'] ?? '')),
        ];

        DB::transaction(function () use ($pageId, $payload, &$stats) {
            // Overwrite the existing block when one exists already.
            $existing = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'place_history')
                ->orderBy('sort_order')
                ->first();
            if ($existing) {
                DB::table('rg_content_blocks')->where('id', $existing->id)->update([
                    'payload_json' => json_encode($payload),
                    'updated_at' => now(),
                ]);
                $stats['histories']++;
                return;
            }

            // Otherwise insert just after the first text_section. If no
            // text_section, slot it at the end of the body (before the
            // recommendation tail blocks).
            $anchor = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'text_section')
                ->orderBy('sort_order')
                ->first();
            $targetSortOrder = $anchor
                ? (int) $anchor->sort_order + 1
                : ((int) (DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $pageId)
                    ->max('sort_order') ?? 0) + 1);

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

            DB::table('rg_content_blocks')->insert([
                'owner_type' => 'seo_page',
                'owner_id' => $pageId,
                'sort_order' => $targetSortOrder,
                'block_type' => 'place_history',
                'payload_json' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $stats['histories']++;
        });
    }
}
