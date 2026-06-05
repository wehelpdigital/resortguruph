<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies researched fiesta content (place_intro, why_go, what_unique,
 * history, key_events, tip, tags, facts) from
 * database/data/researched_fiestas.json to the matching rg_fiestas row.
 *
 * For each fiesta we wipe its existing content blocks and rebuild the
 * block stream in canonical order:
 *
 *   1.  section_header — name + date_label as subtitle
 *   2.  short_version — place_intro (TLDR card)
 *   3.  facts_list — facts array (When/Where/Patron/Crowd)
 *   4.  text_section — Why go
 *   5.  text_section — What makes it unique
 *   6.  place_history — researched origin
 *   7.  attractions — key events (named highlights)
 *   8.  local_tip — insider tip
 *   9.  tag_pills — hashtag terms
 *  10.  map_embed — auto-built from city + province
 *  11.  external_guides — DOT / local-government search shortcuts
 *  12.  author — Resort Guru editorial team
 *
 * Idempotent: re-runs wipe and rebuild from the JSON so any edits to
 * the source data flow through cleanly.
 */
class ApplyResearchedFiestaContentSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/researched_fiestas.json');
        if (!is_file($path)) {
            $this->command->warn("Missing {$path} — skip.");
            return;
        }
        $entries = json_decode((string) file_get_contents($path), true) ?: [];
        $this->command->info('Researched fiestas: ' . count($entries));

        $count = 0;
        foreach ($entries as $entry) {
            $slug = $entry['slug'] ?? null;
            if (!$slug) continue;

            $fiesta = DB::table('rg_fiestas')->where('slug', $slug)->first();
            if (!$fiesta) {
                $this->command->warn("  {$slug}: no rg_fiestas row, skip");
                continue;
            }

            DB::transaction(function () use ($entry, $fiesta) {
                // Wipe existing blocks for this fiesta — every re-run is
                // a full rebuild, so the block stream stays canonical and
                // we never accumulate orphans from earlier seeds.
                DB::table('rg_content_blocks')
                    ->where('owner_type', 'fiesta')
                    ->where('owner_id', $fiesta->id)
                    ->delete();

                $blocks = $this->buildBlocks($entry, $fiesta);
                $sortOrder = 1;
                foreach ($blocks as $block) {
                    DB::table('rg_content_blocks')->insert([
                        'owner_type' => 'fiesta',
                        'owner_id' => $fiesta->id,
                        'sort_order' => $sortOrder++,
                        'block_type' => $block['type'],
                        'payload_json' => json_encode($block['payload']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            $count++;
            $this->command->info("  {$slug} · " . ($fiesta->name ?? $slug));
        }
        $this->command->info("Fiestas updated: {$count}");
    }

    /**
     * Build the canonical block stream from a researched entry. Each
     * returned element is [type, payload], to be inserted in order.
     */
    private function buildBlocks(array $entry, object $fiesta): array
    {
        $blocks = [];

        // 1. Section header — name + date_label
        $blocks[] = ['type' => 'section_header', 'payload' => [
            'heading' => $fiesta->name . ' at a glance',
            'subtitle' => trim(implode(' · ', array_filter([
                $fiesta->date_label,
                $fiesta->city_or_town . ($fiesta->province ? ', ' . $fiesta->province : ''),
            ]))),
        ]];

        // 2. Short version (place_intro becomes the TLDR card)
        $intro = (string) ($entry['place_intro'] ?? '');
        if ($intro !== '') {
            // Use the first paragraph for the short version card.
            $firstPara = preg_split('~\n\s*\n~', $intro)[0] ?? $intro;
            $blocks[] = ['type' => 'short_version', 'payload' => [
                'eyebrow' => 'The short version',
                'body' => $firstPara,
                'accent_color' => 'amber',
            ]];
        }

        // 3. Facts list
        $facts = $entry['facts'] ?? [];
        if ($facts) {
            $blocks[] = ['type' => 'facts_list', 'payload' => [
                'heading' => '',
                'cards' => array_map(fn ($f) => [
                    'icon' => $f['icon'] ?? 'info',
                    'color' => $f['color'] ?? 'amber',
                    'label' => $f['label'] ?? '',
                    'big' => $f['big'] ?? '',
                    'detail' => $f['detail'] ?? '',
                ], $facts),
            ]];
        }

        // 4. Why go (the rest of place_intro + dedicated why_go)
        $whyGo = trim((string) ($entry['why_go'] ?? ''));
        if ($whyGo !== '') {
            $blocks[] = ['type' => 'text_section', 'payload' => [
                'heading' => 'Why ' . $fiesta->name . ' is worth the trip',
                'heading_level' => 'h2',
                'anchor' => 'why-go',
                'body' => $whyGo,
            ]];
        }

        // 5. What unique
        $whatUnique = trim((string) ($entry['what_unique'] ?? ''));
        if ($whatUnique !== '') {
            $blocks[] = ['type' => 'text_section', 'payload' => [
                'heading' => 'What makes ' . $fiesta->name . ' different',
                'heading_level' => 'h2',
                'anchor' => 'what-unique',
                'body' => $whatUnique,
            ]];
        }

        // 6. Place history — origin / founding / cultural roots
        $history = $entry['history'] ?? [];
        if (!empty($history['body'])) {
            $blocks[] = ['type' => 'place_history', 'payload' => [
                'eyebrow' => 'Festival history',
                'heading' => $history['heading'] ?? ('How ' . $fiesta->name . ' began'),
                'body' => $history['body'],
                'founded' => $history['founded'] ?? '',
                'citation_label' => $history['citation_label'] ?? '',
                'citation_url' => $history['citation_url'] ?? '',
            ]];
        }

        // 7. Attractions = key events
        $events = $entry['key_events'] ?? [];
        if ($events) {
            $blocks[] = ['type' => 'attractions', 'payload' => [
                'heading' => 'Highlights at ' . $fiesta->name,
                'intro' => '',
                'items' => array_map(fn ($e) => [
                    'name' => $e['name'] ?? '',
                    'short' => '',
                    'blurb' => $e['blurb'] ?? '',
                    'image' => '',
                    'images' => [],
                    'url' => '',
                ], $events),
            ]];
        }

        // 8. Local tip
        $tip = trim((string) ($entry['tip'] ?? ''));
        if ($tip !== '') {
            $blocks[] = ['type' => 'local_tip', 'payload' => [
                'eyebrow' => 'Local tip from ' . ($fiesta->city_or_town ?: 'a regular'),
                'body' => $tip,
                'color' => 'amber',
            ]];
        }

        // 9. Tag pills
        $tags = $entry['tags'] ?? [];
        if ($tags) {
            $palettes = ['amber', 'rose', 'emerald', 'indigo', 'pink', 'cyan', 'violet', 'slate'];
            $items = [];
            foreach (array_values($tags) as $i => $t) {
                $items[] = ['text' => $t, 'color' => $palettes[$i % count($palettes)]];
            }
            $blocks[] = ['type' => 'tag_pills', 'payload' => [
                'label' => 'Themes', 'items' => $items,
            ]];
        }

        // 10. Map embed — derived from city + province
        $place = trim(($fiesta->city_or_town ?? '') . ', ' . ($fiesta->province ?? ''));
        if ($place !== ', ') {
            $blocks[] = ['type' => 'map_embed', 'payload' => [
                'heading' => 'Where ' . $fiesta->name . ' happens',
                'embed_url' => 'https://www.google.com/maps?q=' . rawurlencode($place . ', Philippines') . '&output=embed',
                'height' => 420,
            ]];
        }

        // 11. External guides — search shortcuts for verification
        $blocks[] = ['type' => 'external_guides', 'payload' => [
            'heading' => 'Plan + cross-check ' . $fiesta->name,
            'intro' => '',
            'footnote' => 'External links open in a new tab. We do not get paid for clicks.',
            'items' => [
                ['name' => 'Google search', 'url' => 'https://www.google.com/search?q=' . rawurlencode($fiesta->name . ' ' . ($fiesta->city_or_town ?? '') . ' schedule'), 'color' => 'blue', 'blurb' => ''],
                ['name' => 'Department of Tourism', 'url' => 'https://philippines.travel/?search=' . rawurlencode($fiesta->name), 'color' => 'emerald', 'blurb' => ''],
                ['name' => 'Wikipedia', 'url' => 'https://en.wikipedia.org/wiki/Special:Search?search=' . rawurlencode($fiesta->name), 'color' => 'slate', 'blurb' => ''],
                ['name' => 'Facebook page search', 'url' => 'https://www.facebook.com/search/pages/?q=' . rawurlencode($fiesta->name), 'color' => 'blue', 'blurb' => ''],
            ],
        ]];

        // 12. Author byline
        $blocks[] = ['type' => 'author', 'payload' => [
            'eyebrow' => 'Written by',
            'name' => 'Resort Guru Editorial',
            'role' => 'DIY travel writers, Philippines',
            'bio' => 'Our small team writes about resorts, food finds, and the year-round fiesta calendar across the Philippines.',
            'avatar' => '',
            'links' => [],
            'more_url' => '/fiestas',
            'more_label' => 'See more Philippine fiestas',
        ]];

        return $blocks;
    }
}
