<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Enforces Rule 2 across all food rg_seo_pages:
 *   - em-dash `—` and double-hyphen `--` → ", "
 *   - banned-vocabulary auto-replacements
 *
 * Touches body_html, hero_html, tldr, wwww_json, faq_json on every food
 * keyword page so the smoke test (curl + grep) returns 0 em-dashes per page.
 */
class StripEmDashesFromFoodPagesSeeder extends Seeder
{
    /** @var array<string,string> banned word → replacement */
    private array $bannedWords = [
        'delve' => 'look at',
        'tapestry' => 'mix',
        'vibrant' => 'lively',
        'bustling' => 'busy',
        'nestled' => 'set',
        'embark' => 'start',
        'unveil' => 'show',
        'elevate' => 'lift',
        'plethora' => 'lot',
        'myriad' => 'many',
        'seamless' => 'smooth',
        'leverage' => 'use',
        'dive into' => 'get into',
        'hidden gem' => 'lesser-known spot',
        'must-visit' => 'worth a visit',
        'must-try' => 'worth trying',
        'breathtaking' => 'striking',
    ];

    public function run(): void
    {
        $foodKeywordIds = DB::table('rg_keywords')
            ->where('category', 'food')
            ->pluck('id');

        $emBefore = 0;
        $emAfter = 0;
        $processed = 0;

        foreach ($foodKeywordIds as $keywordId) {
            $page = DB::table('rg_seo_pages')->where('keyword_id', $keywordId)->first();
            if (!$page) continue;

            $textCols = ['title', 'meta_title', 'meta_description', 'meta_keywords',
                         'h1', 'intro_html', 'body_html', 'hero_html', 'tldr'];
            $jsonCols = ['wwww_json', 'faq_json'];

            $beforeBlob = '';
            foreach ($textCols as $c) $beforeBlob .= ($page->{$c} ?? '');
            foreach ($jsonCols as $c) $beforeBlob .= ($page->{$c} ?? '');
            $emBefore += substr_count($beforeBlob, '—') + substr_count($beforeBlob, '--');

            $update = [];
            foreach ($textCols as $c) $update[$c] = $this->clean($page->{$c} ?? null);
            foreach ($jsonCols as $c) $update[$c] = $this->cleanJson($page->{$c} ?? null);

            DB::table('rg_seo_pages')
                ->where('id', $page->id)
                ->update($update);

            $afterBlob = '';
            foreach (array_merge($textCols, $jsonCols) as $c) {
                $afterBlob .= ($update[$c] ?? '');
            }
            $emAfter += substr_count($afterBlob, '—') + substr_count($afterBlob, '--');

            $processed++;
            if ($processed % 50 === 0) {
                $this->command->info("  {$processed} processed...");
            }
        }

        $this->command->info("Done. Processed: {$processed}");
        $this->command->info("Em-dash + double-hyphen mentions — before: {$emBefore} | after: {$emAfter}");
    }

    private function clean(?string $text): ?string
    {
        if ($text === null || $text === '') return $text;

        // Em-dash → ", " (with intelligent surrounding-space cleanup)
        $text = preg_replace('/\s*—\s*/u', ', ', $text);
        // Double hyphen → ", "
        $text = preg_replace('/\s*--\s*/u', ', ', $text);
        // En-dash between words (not numeric ranges like "6-8")
        $text = preg_replace('/(?<=\w)\s+–\s+(?=\w)/u', ', ', $text);

        // Banned vocabulary (case-insensitive, word-boundary)
        foreach ($this->bannedWords as $bad => $good) {
            $pattern = '/\b' . preg_quote($bad, '/') . '\b/iu';
            $text = preg_replace($pattern, $good, $text);
        }

        // Collapse runs of duplicated commas/spaces left by replacements
        $text = preg_replace('/,\s*,/u', ',', $text);
        $text = preg_replace('/\s{2,}/u', ' ', $text);
        $text = preg_replace('/,\s+([.!?])/u', '$1', $text);

        return $text;
    }

    private function cleanJson(?string $json): ?string
    {
        if ($json === null || $json === '') return $json;
        $data = json_decode($json, true);
        if ($data === null) return $this->clean($json);

        array_walk_recursive($data, function (&$v) {
            if (is_string($v)) $v = $this->clean($v);
        });

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
