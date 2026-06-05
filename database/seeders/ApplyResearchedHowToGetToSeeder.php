<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies researched how_to_get_to content from
 * database/data/researched_how_to_get_to.json to destination pages
 * whose slug matches the entry's token (or one of its aliases). Each
 * entry rewrites the heading, intro, methods, and footer of a single
 * how_to_get_to block, in place. Pre-existing duplicates are dropped
 * during the same transaction.
 *
 * Idempotent: re-runs replace the same block payload with the same
 * researched content.
 */
class ApplyResearchedHowToGetToSeeder extends Seeder
{
    /**
     * Token → extra slug substrings that should also receive the same
     * researched payload. Mirrors the alias map in
     * ApplyResearchedDestinationContentSeeder so province-level pages
     * inherit the city-level researched content.
     */
    private array $tokenAliases = [
        'cebu-city' => ['hotel-in-cebu', 'resort-in-cebu', 'in-cebu'],
        'mactan' => ['lapu-lapu-city', 'mactan-cebu', 'mactan-cebu-philippines', 'resort-in-lapu-lapu'],
        'el-nido' => ['beach-resort-in-palawan', 'resort-in-palawan', 'in-palawan'],
        'iloilo-city' => ['in-iloilo'],
        'davao-city' => ['resort-in-davao', 'in-davao'],
        'general-santos' => ['resort-in-gensan'],
        'glan-sarangani' => ['resort-in-glan'],
        'pangasinan-general' => ['resort-in-pangasinan', 'in-pangasinan', 'pangasinan-philippines'],
        'alaminos-hundred-islands' => ['hundred-islands', 'in-alaminos'],
        'tarlac' => ['resort-in-tarlac', 'in-tarlac'],
        'naga-camarines-sur' => ['resort-in-naga', 'in-naga-city', 'camarines-sur'],
        'albay-legazpi' => ['resort-in-albay', 'in-legazpi'],
        'bataan-province' => ['resort-in-bataan', 'in-bataan'],
        'pampanga-province' => ['resort-in-pampanga', 'in-pampanga', 'pampanga-philippines'],
        'bulacan-province' => ['resort-in-bulacan', 'in-bulacan'],
        'batangas-city' => ['resort-in-batangas', 'in-batangas'],
        'lipa' => ['lipa-batangas', 'in-lipa', 'lipa-philippines'],
        'antipolo' => ['rizal-province', 'resort-in-rizal'],
        'pansol' => ['resort-in-laguna', 'in-laguna'],
        'tagaytay' => ['resort-in-cavite', 'in-cavite', 'in-dasma', 'dasmarinas-cavite', 'in-silang'],
        'lucena' => ['resort-in-quezon', 'in-quezon-province'],
        'marikina' => ['resort-in-marikina'],
        'anilao-mabini' => ['mabini-batangas', 'resort-in-mabini-batangas'],
        'laiya' => ['san-juan-batangas'],
        'calatagan' => ['lobo-batangas'],
        'bacolod' => ['don-salvador-benedicto'],
    ];

    public function run(): void
    {
        $path = database_path('data/researched_how_to_get_to.json');
        if (!is_file($path)) {
            $this->command->warn("Missing {$path} — skip.");
            return;
        }
        $entries = json_decode((string) file_get_contents($path), true) ?: [];
        $this->command->info('Researched how_to_get_to entries loaded: ' . count($entries));

        // Place-name lookup so the heading reads "How to get to <Place>".
        // Pulls from researched_destination_venues.json when the same
        // token has a curated proper name; otherwise we let the existing
        // FixHowToGetToHeadingsSeeder logic handle the heading downstream.
        $destLookup = [];
        $destPath = database_path('data/researched_destination_venues.json');
        if (is_file($destPath)) {
            foreach (json_decode((string) file_get_contents($destPath), true) ?: [] as $row) {
                if (!empty($row['token']) && !empty($row['name'])) {
                    $destLookup[$row['token']] = (string) $row['name'];
                }
            }
        }

        $pagesUpdated = 0;
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

            $placeName = $this->placeNameFromToken($token, $destLookup);
            $payload = $this->buildPayload($entry, $placeName);

            foreach ($rows as $row) {
                $this->writeBlock((int) $row->page_id, $payload);
                $pagesUpdated++;
            }
            $this->command->info(str_pad($token, 28) . " · {$rows->count()} pages");
        }

        $this->command->info("Pages updated: {$pagesUpdated}");
    }

    private function placeNameFromToken(string $token, array $destLookup): string
    {
        if (isset($destLookup[$token])) {
            // Strip any parenthetical clarification — keep just the main name.
            return trim((string) preg_replace('~\s*\(.*$~', '', $destLookup[$token]));
        }
        // Fallback: title-case the token.
        $words = explode('-', $token);
        $small = ['de', 'la', 'el', 'of', 'the', 'in', 'an', 'a'];
        $last = count($words) - 1;
        $out = [];
        foreach ($words as $i => $w) {
            $lower = mb_strtolower($w, 'UTF-8');
            $out[] = ($i === 0 || $i === $last || !in_array($lower, $small, true))
                ? mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($w, 1, null, 'UTF-8')
                : $lower;
        }
        return implode(' ', $out);
    }

    private function buildPayload(array $entry, string $placeName): array
    {
        return [
            'heading' => 'How to get to ' . $placeName,
            'intro' => (string) ($entry['intro'] ?? ''),
            'methods' => array_map(static function ($m) {
                return [
                    'title' => (string) ($m['title'] ?? ''),
                    'icon' => (string) ($m['icon'] ?? 'car'),
                    'color' => (string) ($m['color'] ?? 'amber'),
                    'subtitle' => (string) ($m['subtitle'] ?? ''),
                    'detail' => (string) ($m['detail'] ?? ''),
                ];
            }, (array) ($entry['methods'] ?? [])),
            'footer' => (string) ($entry['footer'] ?? ''),
        ];
    }

    private function writeBlock(int $pageId, array $payload): void
    {
        DB::transaction(function () use ($pageId, $payload) {
            $rows = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'how_to_get_to')
                ->orderBy('sort_order')
                ->get();
            if ($rows->isEmpty()) return;
            $first = $rows->shift();
            DB::table('rg_content_blocks')->where('id', $first->id)->update([
                'payload_json' => json_encode($payload),
                'updated_at' => now(),
            ]);
            foreach ($rows as $extra) {
                DB::table('rg_content_blocks')->where('id', $extra->id)->delete();
            }
        });
    }
}
