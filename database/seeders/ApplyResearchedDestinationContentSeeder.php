<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Apply destination-specific researched content from
 * database/data/researched_destination_venues.json to every resort-
 * category page whose slug contains the destination token. Mirrors
 * ApplyResearchedVenueContentSeeder but scoped to k.category=resort
 * so destination + food verticals stay separate and don't trample
 * each other.
 *
 * Each venue entry overwrites four blocks per page (short_version,
 * pros_cons, tag_pills, local_tip). Idempotent — re-runs replace
 * the same blocks and drop duplicates.
 */
class ApplyResearchedDestinationContentSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/researched_destination_venues.json');
        if (!is_file($path)) {
            $this->command->warn("Missing {$path} — skip.");
            return;
        }
        $venues = json_decode((string) file_get_contents($path), true) ?: [];
        $this->command->info('Researched destinations loaded: ' . count($venues));

        $palettes = ['amber', 'rose', 'emerald', 'indigo', 'pink', 'cyan', 'violet', 'slate'];
        $pages = 0;
        $blocks = 0;

        // Province-level slugs that need to point at a city-level
        // destination because the substring match is too narrow. E.g.
        // "resort-in-cebu" should use "cebu-city" content; "resort-in-
        // palawan" should use "el-nido" content. Listed token → array
        // of slug substrings to ALSO claim.
        $tokenAliases = [
            'cebu-city' => ['hotel-in-cebu', 'resort-in-cebu', 'in-cebu'],
            'mactan' => ['resort-in-lapu-lapu', 'lapu-lapu-city', 'mactan-cebu', 'mactan-cebu-philippines'],
            'el-nido' => ['beach-resort-in-palawan', 'resort-in-palawan', 'in-palawan'],
            'iloilo-city' => ['in-iloilo'],
            'davao-city' => ['resort-in-davao', 'in-davao'],
            'zamboanga-city' => ['resort-in-zamboanga'],
            'general-santos' => ['resort-in-gensan'],
            'glan-sarangani' => ['resort-in-glan'],
            'pangasinan-general' => ['resort-in-pangasinan', 'in-pangasinan', 'pangasinan-philippines', 'pangasinan-white-sand'],
            'alaminos-hundred-islands' => ['hundred-islands', 'in-alaminos'],
            'tarlac' => ['resort-in-tarlac', 'in-tarlac'],
            'naga-camarines-sur' => ['resort-in-naga', 'in-naga-city', 'camarines-sur'],
            'albay-legazpi' => ['resort-in-albay', 'in-legazpi'],
            'bataan-province' => ['resort-in-bataan', 'in-bataan'],
            'pampanga-province' => ['resort-in-pampanga', 'in-pampanga', 'pampanga-philippines'],
            'bulacan-province' => ['resort-in-bulacan', 'in-bulacan'],
            'batangas-city' => ['resort-in-batangas', 'in-batangas'],
            'lipa' => ['lipa-batangas', 'in-lipa', 'lipa-philippines'],
            'antipolo' => ['rizal-province', 'resort-in-rizal', 'rizal-philippines'],
            'pansol' => ['resort-in-laguna', 'in-laguna'],
            'tagaytay' => ['resort-in-cavite', 'in-cavite', 'cavite-philippines', 'in-dasma', 'dasmarinas-cavite', 'in-silang'],
            'lucena' => ['resort-in-quezon', 'in-quezon-province'],
            'marikina' => ['resort-in-marikina'],
            'rodriguez-montalban' => ['rodriguez-rizal', 'montalban-rizal'],
            'anilao-mabini' => ['mabini-batangas', 'resort-in-mabini-batangas'],
            'laiya' => ['san-juan-batangas'],
            'calatagan' => ['lobo-batangas'],
            'bacolod' => ['don-salvador-benedicto', 'silay-bacolod'],
            'samal-island' => ['island-garden-city-of-samal'],
        ];

        foreach ($venues as $venue) {
            $token = $venue['token'] ?? null;
            if (!$token) continue;

            $needles = array_merge([$token], $tokenAliases[$token] ?? []);
            $rows = DB::table('rg_seo_pages as p')
                ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
                ->where('k.category', 'resort')
                ->where(function ($q) use ($needles) {
                    foreach ($needles as $needle) $q->orWhere('k.slug', 'like', '%' . $needle . '%');
                })
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
