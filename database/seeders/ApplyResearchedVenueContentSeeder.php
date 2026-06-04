<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies the AI-research workflow output stored in
 * database/data/researched_food_venues.json to every food page whose
 * slug contains the venue token. Mirrors the one-off application script
 * the research workflow used in-session, so the work is reproducible
 * if the database is reset or new keywords are added.
 *
 * Each venue entry is shaped:
 *   { token, name, short_version, pros[], cons[], tags[], tip }
 *
 * Pages get their short_version, pros_cons, tag_pills, and local_tip
 * payloads rewritten in place. Idempotent: re-runs overwrite with the
 * same content.
 */
class ApplyResearchedVenueContentSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/researched_food_venues.json');
        if (!is_file($path)) {
            $this->command->warn("Missing {$path} — skip.");
            return;
        }
        $venues = json_decode((string) file_get_contents($path), true) ?: [];
        $this->command->info('Researched venues loaded: ' . count($venues));

        $palettes = ['amber', 'rose', 'emerald', 'indigo', 'pink', 'cyan', 'violet', 'slate'];
        $pages = 0;
        $blocks = 0;

        foreach ($venues as $venue) {
            $token = $venue['token'] ?? null;
            if (!$token) continue;

            $rows = DB::table('rg_seo_pages as p')
                ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
                ->where('k.category', 'food')
                ->where('k.slug', 'like', '%' . $token . '%')
                ->select('p.id as page_id')
                ->get();

            $payloads = $this->buildPayloads($venue, $palettes);
            foreach ($rows as $row) {
                $blocks += $this->applyToPage((int) $row->page_id, $payloads);
                $pages++;
            }
            $this->command->info(str_pad($token, 28) . " · {$rows->count()} pages");
        }

        $this->command->info("Pages updated: {$pages}, blocks written: {$blocks}");
    }

    /**
     * Build the four block payloads from one venue entry. Returned as a
     * keyed map so [[applyToPage]] can iterate without re-deriving them.
     */
    private function buildPayloads(array $venue, array $palettes): array
    {
        $tagItems = [];
        foreach (array_values($venue['tags'] ?? []) as $i => $t) {
            $tagItems[] = ['text' => $t, 'color' => $palettes[$i % count($palettes)]];
        }
        return [
            'short_version' => [
                'eyebrow' => 'The short version',
                'body' => $venue['short_version'] ?? '',
                'accent_color' => 'amber',
            ],
            'pros_cons' => [
                'pros_label' => 'Best for',
                'cons_label' => 'Skip if',
                'pros' => array_values($venue['pros'] ?? []),
                'cons' => array_values($venue['cons'] ?? []),
            ],
            'tag_pills' => ['label' => 'What you will find', 'items' => $tagItems],
            'local_tip' => [
                'eyebrow' => 'Local tip from ' . ($venue['name'] ?? 'a regular'),
                'body' => $venue['tip'] ?? '',
                'color' => 'amber',
            ],
        ];
    }

    /**
     * Overwrite the first block of each type on the given page; delete
     * any extras (so a page can't end up with two of the same type
     * after re-runs).
     */
    private function applyToPage(int $pageId, array $payloads): int
    {
        $written = 0;
        DB::transaction(function () use ($pageId, $payloads, &$written) {
            foreach ($payloads as $type => $payload) {
                $rows = DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $pageId)
                    ->where('block_type', $type)
                    ->orderBy('sort_order')
                    ->get();
                if ($rows->isEmpty()) continue;
                $first = $rows->shift();
                DB::table('rg_content_blocks')->where('id', $first->id)->update([
                    'payload_json' => json_encode($payload),
                    'updated_at' => now(),
                ]);
                foreach ($rows as $extra) {
                    DB::table('rg_content_blocks')->where('id', $extra->id)->delete();
                }
                $written++;
            }
        });
        return $written;
    }
}
