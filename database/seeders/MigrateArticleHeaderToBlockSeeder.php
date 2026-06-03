<?php

namespace Database\Seeders;

use App\Http\Controllers\DestinationsController;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Promotes the hardcoded "What's in [Area]?" article header from the
 * keyword-page template into a real section_header block so it becomes
 * editable in the builder. Also nulls the hero_html column on pages
 * where the slider has already been migrated to a hero_slider block —
 * removes the duplicate hero render the user was seeing.
 *
 * One insertion per page, idempotent on re-run (skips pages that
 * already have a section_header block).
 */
class MigrateArticleHeaderToBlockSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $insertedHeaders = 0;
        $nulledHeroHtml = 0;
        $skipped = 0;

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->select('p.id', 'p.slug', 'p.hero_html', 'k.phrase', 'k.category', 'k.cluster_tag')
            ->orderBy('p.id')
            ->get();

        foreach ($pages as $page) {
            $blocksExist = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->exists();
            if (!$blocksExist) {
                $skipped++;
                continue;
            }

            // 1. Null out hero_html on pages whose slider already lives as a
            //    hero_slider block — kills the duplicate the user saw.
            $hasHeroBlock = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->where('block_type', 'hero_slider')
                ->exists();
            if ($hasHeroBlock && !empty($page->hero_html)) {
                DB::table('rg_seo_pages')
                    ->where('id', $page->id)
                    ->update(['hero_html' => null, 'updated_at' => $now]);
                $nulledHeroHtml++;
            }

            // 2. Insert a section_header block right after the hero_slider
            //    (sort_order = hero's + 1, others shifted) unless one
            //    already exists.
            $alreadyHasHeader = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->where('block_type', 'section_header')
                ->exists();
            if ($alreadyHasHeader) {
                $skipped++;
                continue;
            }

            $area = $this->areaName($page);
            $heading = "What's in {$area}?";
            $subtitle = "The local read on the area, who it's for, and how to plan a trip that actually works.";
            $payload = ['heading' => $heading, 'subtitle' => $subtitle, 'anchor' => ''];

            // Find the hero_slider's sort_order (if any) to figure out
            // where to insert.
            $heroBlock = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->where('block_type', 'hero_slider')
                ->first(['sort_order']);
            $insertAt = $heroBlock ? ((int) $heroBlock->sort_order + 1) : 1;

            DB::transaction(function () use ($page, $insertAt, $payload, $now) {
                // Shift everything at or after the insertion point down by 1.
                DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $page->id)
                    ->where('sort_order', '>=', $insertAt)
                    ->increment('sort_order');

                DB::table('rg_content_blocks')->insert([
                    'owner_type'   => 'seo_page',
                    'owner_id'     => $page->id,
                    'sort_order'   => $insertAt,
                    'block_type'   => 'section_header',
                    'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            });

            $insertedHeaders++;
            if ($insertedHeaders % 100 === 0) {
                $this->command->info("  inserted {$insertedHeaders} so far...");
            }
        }

        $this->command->info("Done. section_header inserted: {$insertedHeaders} | hero_html nulled: {$nulledHeroHtml} | skipped: {$skipped}");
    }

    /**
     * Replicates keyword-page.blade.php's $areaForHeader logic so the
     * header reads exactly the same as the hardcoded template did.
     */
    private function areaName(object $page): string
    {
        $phrase = $page->phrase ?? '';
        $category = $page->category ?? '';

        $area = null;

        // Food pages: extract from "restaurant in X" or "where to eat X" patterns.
        if ($category === 'food') {
            $stripped = preg_replace(
                '/^(affordable|best|top(?:\s+10)?|famous|fast\s+food|fine(?:\s+dining)?|floating|good\s+taste|hotel|michelin\s+star|new|overlooking|seafood|steak|sushi|filipino|japanese|korean|chinese|italian|mexican|spanish|mediterranean|24\s+hours?|buffet)\s+/i',
                '',
                $phrase
            );
            if (preg_match('/(?:restaurant|to\s+eat)\s+(?:in|at|near)\s+(.+)$/i', $stripped, $m)) {
                $area = trim(preg_replace('/\s+(philippines|with\s+view)$/i', '', $m[1]));
            } elseif (preg_match('/^where\s+to\s+eat\s+(.+)$/i', $stripped, $m)) {
                $area = trim($m[1]);
            }
        }

        // Resort pages: strip "resort in" / "hotel in" etc.
        if ($area === null) {
            $stripped = preg_replace('/^(resort|hotel|airbnb|beach resort)\s+in\s+/i', '', $phrase);
            if ($stripped !== $phrase) $area = trim($stripped);
        }

        // Cluster fallback.
        if ($area === null && !empty($page->cluster_tag)) {
            $clusters = DestinationsController::clusterMetadata();
            if (isset($clusters[$page->cluster_tag]['name'])) {
                $area = $clusters[$page->cluster_tag]['name'];
            }
        }

        // Last resort: just use the phrase.
        if ($area === null || trim($area) === '') {
            $area = trim($phrase);
        }

        return $this->properTitle($area);
    }

    private function properTitle(string $s): string
    {
        $small = ['of', 'the', 'in', 'at', 'on', 'and', 'a', 'an', 'to', 'for', 'by', 'from', 'with'];
        $words = preg_split('/\s+/', mb_strtolower(trim($s)));
        foreach ($words as $i => $w) {
            if ($w === '') continue;
            $words[$i] = ($i === 0 || !in_array($w, $small, true))
                ? mb_convert_case($w, MB_CASE_TITLE, 'UTF-8')
                : $w;
        }
        $result = implode(' ', $words);
        $acronyms = [
            'Bgc' => 'BGC', 'Moa' => 'MOA', 'Qc' => 'QC', 'Cdo' => 'CDO',
            'Sm' => 'SM', 'Atc' => 'ATC', 'Bf' => 'BF', 'Up' => 'UP',
            'Ust' => 'UST', 'Edsa' => 'EDSA', 'Naia' => 'NAIA', 'Ncr' => 'NCR',
            'Uptc' => 'UPTC', 'Pitx' => 'PITX',
        ];
        return preg_replace_callback(
            '/\b(' . implode('|', array_keys($acronyms)) . ')\b/',
            fn($m) => $acronyms[$m[1]],
            $result
        );
    }
}
