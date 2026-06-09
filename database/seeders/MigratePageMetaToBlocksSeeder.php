<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RgSeoPage;
use App\Models\RgContentBlock;

/**
 * One-time migration: every rg_seo_pages row that has a non-empty
 * subtitle / tldr / wwww_json column gets a corresponding
 * subtitle_intro / tldr_card / wwww_card block inserted at the very
 * top of its block list (so they render where they used to render —
 * right under the H1 / above the listings band).
 *
 * Idempotent: each insert checks whether a block of that type already
 * exists for the page. Re-running the seeder won't duplicate. After
 * each page is migrated the source columns are kept untouched — the
 * keyword-page view stops reading them in this commit, but leaving
 * the data in place gives us an out if we ever need to roll back.
 */
class MigratePageMetaToBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $pages = RgSeoPage::query()
            ->where(function ($q) {
                $q->whereNotNull('subtitle')
                  ->orWhereNotNull('tldr')
                  ->orWhereNotNull('wwww_json');
            })
            ->get(['id', 'subtitle', 'tldr', 'wwww_json']);

        $created = ['subtitle_intro' => 0, 'tldr_card' => 0, 'wwww_card' => 0];
        $skipped = ['subtitle_intro' => 0, 'tldr_card' => 0, 'wwww_card' => 0];

        foreach ($pages as $page) {
            // Compute the resolved payloads first so we know how many
            // top seats we need. sort_order is unsigned so we bump
            // existing blocks up by N before inserting the new ones
            // at positions 1..N at the front.
            $payloads = [];
            $sub = trim((string) $page->subtitle);
            if ($sub !== '') $payloads['subtitle_intro'] = ['text' => $sub];

            $tldrBody = trim((string) $page->tldr);
            if ($tldrBody !== '') $payloads['tldr_card'] = ['body' => $tldrBody];

            $raw = $page->wwww_json;
            if (!empty($raw)) {
                $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);
                if (is_array($decoded)) {
                    $wwww = [
                        'why' => trim((string) ($decoded['why'] ?? '')),
                        'when' => trim((string) ($decoded['when'] ?? '')),
                        'where' => trim((string) ($decoded['where'] ?? '')),
                        'whom' => trim((string) ($decoded['whom'] ?? '')),
                    ];
                    if (array_filter($wwww, fn ($v) => $v !== '')) {
                        $payloads['wwww_card'] = $wwww;
                    }
                }
            }

            if (!$payloads) continue;

            // Filter out any type already present so re-running is
            // idempotent.
            $existingTypes = RgContentBlock::query()
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->whereIn('block_type', array_keys($payloads))
                ->pluck('block_type')
                ->all();
            foreach ($existingTypes as $t) {
                $skipped[$t]++;
                unset($payloads[$t]);
            }
            if (!$payloads) continue;

            $shift = count($payloads);

            // Bump existing block sort_orders up so the new top-N
            // can claim positions 1..N. Use raw expr (no negative
            // intermediate values).
            DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->update(['sort_order' => DB::raw('sort_order + ' . $shift)]);

            // Insert in the canonical order subtitle → tldr → wwww
            // so they read top-down the same way the partial used to.
            $seat = 1;
            foreach (['subtitle_intro', 'tldr_card', 'wwww_card'] as $type) {
                if (!isset($payloads[$type])) continue;
                RgContentBlock::create([
                    'owner_type' => 'seo_page',
                    'owner_id' => $page->id,
                    'block_type' => $type,
                    'payload_json' => json_encode($payloads[$type], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'sort_order' => $seat++,
                ]);
                $created[$type]++;
            }
        }

        $this->command->info('subtitle_intro: ' . $created['subtitle_intro'] . ' created, ' . $skipped['subtitle_intro'] . ' skipped (already had one).');
        $this->command->info('tldr_card     : ' . $created['tldr_card'] . ' created, ' . $skipped['tldr_card'] . ' skipped.');
        $this->command->info('wwww_card     : ' . $created['wwww_card'] . ' created, ' . $skipped['wwww_card'] . ' skipped.');
    }

    private function migrateOne(
        $page,
        string $type,
        int $sortSeat,
        array &$created,
        array &$skipped,
        callable $payloadResolver
    ): void {
        $payload = $payloadResolver($page);
        if ($payload === null) return;

        $existing = RgContentBlock::query()
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $page->id)
            ->where('block_type', $type)
            ->first();
        if ($existing) {
            $skipped[$type]++;
            return;
        }

        RgContentBlock::create([
            'owner_type' => 'seo_page',
            'owner_id' => $page->id,
            'block_type' => $type,
            'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'sort_order' => $sortSeat,
        ]);
        $created[$type]++;
    }

    private function renormalize(int $pageId): void
    {
        $blocks = RgContentBlock::query()
            ->where('owner_type', 'seo_page')
            ->where('owner_id', $pageId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id']);
        $i = 1;
        foreach ($blocks as $b) {
            DB::table('rg_content_blocks')->where('id', $b->id)->update(['sort_order' => $i++]);
        }
    }
}
