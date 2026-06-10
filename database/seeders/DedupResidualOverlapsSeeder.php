<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Final-pass cleanup for two residual duplication patterns left
 * after DedupPageBlocksSeeder + DedupRedundantProseSeeder ran.
 *
 *   Pattern 1: section_header.heading overlaps with the attractions
 *              block's heading on the same page. The seeder produced
 *              both an anchor section_header ("What's in Bulacan?")
 *              AND let the attractions block carry its own heading
 *              ("What's in Bulacan?") — they render as TWO identical
 *              H2 headings on the page.
 *
 *              Fix: delete the section_header. The attractions block
 *              keeps its own canonical heading.
 *
 *   Pattern 2: subtitle_intro.text appears verbatim as the OPENING
 *              of a text_section.body further down the page. Reader
 *              sees the same sentences twice — once as the italic
 *              intro under the H1, once at the top of a prose
 *              section.
 *
 *              Fix: trim the duplicate opening off the text_section
 *              body. If the body is JUST the duplicate text (no
 *              other content), delete the text_section entirely.
 *
 *   Then renormalize sort_order on every touched page.
 *
 * Idempotent. Re-runs harmlessly.
 */
class DedupResidualOverlapsSeeder extends Seeder
{
    public function run(): void
    {
        $deletedSectionHeaders = 0;
        $trimmedTextSections = 0;
        $deletedTextSections = 0;
        $touchedPages = collect();

        // ---------- Pattern 1: section_header vs attractions heading ----------
        $sectionRows = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('block_type', 'section_header')
            ->get(['id', 'owner_id', 'payload_json']);

        foreach ($sectionRows as $sh) {
            $shp = json_decode($sh->payload_json, true) ?: [];
            $shHead = strtolower(trim((string) ($shp['heading'] ?? '')));
            if ($shHead === '') continue;
            $shClean = trim(preg_replace('/[?\.\!,]+/u', '', $shHead));

            $attractionsRow = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $sh->owner_id)
                ->where('block_type', 'attractions')
                ->first(['payload_json']);
            if (!$attractionsRow) continue;

            $ap = json_decode($attractionsRow->payload_json, true) ?: [];
            $aHead = strtolower(trim((string) ($ap['heading'] ?? '')));
            if ($aHead === '') continue;
            $aClean = trim(preg_replace('/[?\.\!,]+/u', '', $aHead));

            // Overlap = one heading contains the other (case-insensitive)
            // OR both share a "what's in" / "whats in" question phrase.
            $overlap = false;
            if ($shClean === '' || $aClean === '') continue;
            if (str_contains($shClean, $aClean) || str_contains($aClean, $shClean)) $overlap = true;
            else {
                $sharedPhrases = ['whats in', 'what is in', 'what to eat', 'what to do', 'how to get', 'foods to try'];
                foreach ($sharedPhrases as $phr) {
                    if (str_contains($shClean, $phr) && str_contains($aClean, $phr)) { $overlap = true; break; }
                }
            }
            if (!$overlap) continue;

            DB::table('rg_content_blocks')->where('id', $sh->id)->delete();
            $deletedSectionHeaders++;
            $touchedPages->push($sh->owner_id);
        }

        // ---------- Pattern 2: subtitle_intro text duplicated in text_section ----------
        // Three sub-cases:
        //   (a) text_section body == subtitle text exactly → delete the whole text_section.
        //   (b) text_section body starts with subtitle text → trim the opening.
        //   (c) subtitle text appears as a middle paragraph (\n\n[sub]\n\n) → strip the
        //       middle occurrence and collapse the duplicate blank lines.
        $subtitleRows = DB::table('rg_content_blocks')
            ->where('owner_type', 'seo_page')
            ->where('block_type', 'subtitle_intro')
            ->get(['id', 'owner_id', 'payload_json']);

        foreach ($subtitleRows as $sub) {
            $sp = json_decode($sub->payload_json, true) ?: [];
            $subText = trim((string) ($sp['text'] ?? ''));
            if (strlen($subText) < 30) continue;

            $textSections = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $sub->owner_id)
                ->where('block_type', 'text_section')
                ->get(['id', 'payload_json']);

            foreach ($textSections as $ts) {
                $tp = json_decode($ts->payload_json, true) ?: [];
                $body = trim((string) ($tp['body'] ?? ''));
                if (!str_contains($body, $subText)) continue;

                // Case (a): the entire body is just the duplicate.
                if (trim($body) === $subText) {
                    DB::table('rg_content_blocks')->where('id', $ts->id)->delete();
                    $deletedTextSections++;
                    $touchedPages->push($sub->owner_id);
                    continue;
                }

                // Case (b) or (c): remove every occurrence of the subtitle
                // text from the body, then collapse any consecutive blank
                // lines that the removal left behind.
                $newBody = str_replace($subText, '', $body);
                // Collapse 3+ consecutive newlines down to a normal
                // paragraph break ("\n\n").
                $newBody = preg_replace('/(\r?\n){3,}/', "\n\n", $newBody);
                $newBody = trim($newBody);

                if ($newBody === '') {
                    DB::table('rg_content_blocks')->where('id', $ts->id)->delete();
                    $deletedTextSections++;
                } else {
                    $tp['body'] = $newBody;
                    DB::table('rg_content_blocks')->where('id', $ts->id)->update([
                        'payload_json' => json_encode($tp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]);
                    $trimmedTextSections++;
                }
                $touchedPages->push($sub->owner_id);
            }
        }

        // ---------- Renormalize sort_order on every touched page ----------
        $renormalized = 0;
        foreach ($touchedPages->unique() as $pageId) {
            $ids = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id');
            $i = 1;
            foreach ($ids as $id) {
                DB::table('rg_content_blocks')->where('id', $id)->update(['sort_order' => $i++]);
            }
            $renormalized++;
        }

        $this->command->info('section_header rows deleted (overlap w/ attractions):  ' . $deletedSectionHeaders);
        $this->command->info('text_section bodies trimmed (subtitle_intro overlap):   ' . $trimmedTextSections);
        $this->command->info('text_section rows deleted (entirely duplicate of sub):  ' . $deletedTextSections);
        $this->command->info('Pages renormalized:                                     ' . $renormalized);
    }
}
