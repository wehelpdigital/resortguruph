<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Strips the AI-generated marketing patterns out of rg_seo_pages.h1
 * values (e.g. "Our shortlist for X, ~~ Vetted Against Real Guest
 * Feedback") and replaces them with the keyword phrase rendered in
 * clean title-case. Idempotent — re-runs only touch rows still
 * carrying the slop patterns.
 *
 * Detection key: the literal "~~" separator inserted by the original
 * h1 generator. Any row that still has it is rewritten; any row that
 * doesn't is left alone (so manual overrides survive).
 */
class CleanDestinationH1sSeeder extends Seeder
{
    /** Words that stay lowercase in title-case unless first or last. */
    private array $small = [
        'a', 'an', 'the',
        'in', 'of', 'on', 'at', 'by', 'to', 'for', 'with',
        'and', 'or', 'as', 'but', 'so',
    ];

    public function run(): void
    {
        $rows = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->where('p.h1', 'like', '%~~%')
            ->select('p.id as page_id', 'p.h1 as h1', 'k.phrase as phrase')
            ->get();

        $this->command->info('Pages with AI-slop h1: ' . $rows->count());

        $fixed = 0;
        foreach ($rows as $row) {
            $clean = $this->titleCase((string) $row->phrase);
            DB::table('rg_seo_pages')
                ->where('id', $row->page_id)
                ->update([
                    'h1' => $clean,
                    'updated_at' => now(),
                ]);
            $fixed++;
        }

        $this->command->info("Cleaned h1 on {$fixed} pages.");
    }

    /**
     * Convert a phrase like "beach and resort in la union" to "Beach and
     * Resort in La Union". First and last words always capitalise;
     * connectors in the middle stay lowercase.
     */
    private function titleCase(string $phrase): string
    {
        $phrase = trim(preg_replace('~\s+~', ' ', $phrase));
        if ($phrase === '') return '';

        $words = explode(' ', $phrase);
        $last = count($words) - 1;
        $out = [];
        foreach ($words as $i => $word) {
            $lower = mb_strtolower($word, 'UTF-8');
            $isEdge = ($i === 0 || $i === $last);
            if (!$isEdge && in_array($lower, $this->small, true)) {
                $out[] = $lower;
                continue;
            }
            $out[] = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8')
                . mb_substr($word, 1, null, 'UTF-8');
        }
        return implode(' ', $out);
    }
}
