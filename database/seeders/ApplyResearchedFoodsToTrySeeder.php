<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies researched per-destination "foods to try" lists from
 * database/data/researched_foods_to_try.json. For each entry, finds the
 * destination resort-category pages whose slug matches the token (or
 * any of its aliases) and inserts a foods_to_try block immediately
 * after the place_history block on each page.
 *
 * Idempotent: existing foods_to_try blocks on the page get overwritten
 * with the new payload (no duplication).
 */
class ApplyResearchedFoodsToTrySeeder extends Seeder
{
    /**
     * Token → extra slug substrings that should also receive the same
     * content (province slugs inherit city content).
     */
    private array $tokenAliases = [
        'cebu-city' => ['hotel-in-cebu', 'resort-in-cebu', 'in-cebu'],
        'mactan' => ['lapu-lapu-city', 'mactan-cebu'],
        'pangasinan-general' => ['resort-in-pangasinan', 'in-pangasinan', 'pangasinan-philippines'],
        'tarlac' => ['resort-in-tarlac', 'in-tarlac'],
        'pampanga-province' => ['resort-in-pampanga', 'in-pampanga'],
        'bulacan-province' => ['resort-in-bulacan', 'in-bulacan'],
        'batangas-city' => ['resort-in-batangas', 'in-batangas'],
        'lipa' => ['lipa-batangas', 'in-lipa'],
        'antipolo' => ['rizal-province', 'resort-in-rizal'],
        'pansol' => ['resort-in-laguna', 'in-laguna'],
        'tagaytay' => ['resort-in-cavite', 'in-cavite'],
        'davao-city' => ['resort-in-davao', 'in-davao'],
        'albay-legazpi' => ['resort-in-albay', 'in-legazpi'],
        'naga-camarines-sur' => ['resort-in-naga', 'in-naga-city', 'camarines-sur'],
    ];

    public function run(): void
    {
        $path = database_path('data/researched_foods_to_try.json');
        if (!is_file($path)) {
            $this->command->warn("Missing {$path} — skip.");
            return;
        }
        $entries = json_decode((string) file_get_contents($path), true) ?: [];
        $this->command->info('Researched foods entries: ' . count($entries));

        $stats = ['pages' => 0, 'inserted' => 0, 'updated' => 0];

        foreach ($entries as $entry) {
            $token = $entry['token'] ?? null;
            if (!$token) continue;

            $needles = array_merge([$token], $this->tokenAliases[$token] ?? []);
            $rows = DB::table('rg_seo_pages as p')
                ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
                ->where('k.category', 'resort')
                ->where(function ($q) use ($needles) {
                    foreach ($needles as $needle) $q->orWhere('k.slug', 'like', '%' . $needle . '%');
                })
                ->select('p.id as page_id')
                ->get();

            $payload = [
                'heading' => $entry['heading'] ?? 'Foods to try',
                'intro' => $entry['intro'] ?? '',
                'items' => array_map(fn ($i) => [
                    'name' => $i['name'] ?? '',
                    'where' => $i['where'] ?? '',
                    'blurb' => $i['blurb'] ?? '',
                    'image' => '',
                ], $entry['items'] ?? []),
            ];

            foreach ($rows as $row) {
                $this->writeBlock((int) $row->page_id, $payload, $stats);
                $stats['pages']++;
            }
            $this->command->info(str_pad($token, 24) . " · {$rows->count()} pages");
        }

        $this->command->info("Pages updated: {$stats['pages']}, inserted: {$stats['inserted']}, updated: {$stats['updated']}");
    }

    /**
     * Upsert the foods_to_try block. When a block already exists we
     * overwrite in place; otherwise insert after the place_history
     * block (falling back to the end of the body if no place_history).
     */
    private function writeBlock(int $pageId, array $payload, array &$stats): void
    {
        DB::transaction(function () use ($pageId, $payload, &$stats) {
            $existing = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'foods_to_try')
                ->orderBy('sort_order')
                ->first();
            if ($existing) {
                DB::table('rg_content_blocks')->where('id', $existing->id)->update([
                    'payload_json' => json_encode($payload),
                    'updated_at' => now(),
                ]);
                $stats['updated']++;
                return;
            }

            $anchor = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->whereIn('block_type', ['place_history', 'text_section', 'short_version'])
                ->orderByDesc('sort_order')
                ->first();
            $target = $anchor
                ? (int) $anchor->sort_order + 1
                : ((int) (DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $pageId)
                    ->max('sort_order') ?? 0) + 1);

            DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('sort_order', '>=', $target)
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
                'sort_order' => $target,
                'block_type' => 'foods_to_try',
                'payload_json' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $stats['inserted']++;
        });
    }
}
