<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Cleanup of redundant prose blocks that duplicate content already
 * covered by canonical structured blocks. The seeder originally
 * produced both:
 *   - A canonical structured block (how_to_get_to, attractions,
 *     foods_to_try, place_history, local_tip), AND
 *   - A heading + text_section pair narrating the SAME topic in
 *     prose, which now reads as a duplicate when both render.
 *
 * Reported example: /resort-in-subic-zambales has BOTH a
 * how_to_get_to block ("How to get to Subic Bay Freeport") AND a
 * heading "Getting to Subic Without Wasting Half a Day" followed by
 * a text_section "For most travelers heading to Subic, the realistic
 * options are private car, bus, or a mix of both."
 *
 * STRATEGY
 *
 *   For each topic, on every page that has the canonical block:
 *     1. Find heading blocks whose text matches the topic regex.
 *     2. Find the IMMEDIATELY-FOLLOWING block (by sort_order) on the
 *        same page. If it's a text_section or rich_text, delete it
 *        too — it's the prose body that the redundant heading
 *        introduced.
 *     3. Delete the heading block itself.
 *     4. Also detect text_section blocks whose BODY matches the
 *        topic strongly (multiple keyword hits) even without a
 *        preceding redundant heading, and delete those.
 *
 *   Renormalize sort_order on every touched page so the ladder
 *   stays 1..N with no gaps.
 *
 * Idempotent. Re-running on a clean DB does nothing.
 */
class DedupRedundantProseSeeder extends Seeder
{
    /**
     * topic => [
     *   'canonical' => block_type that owns this topic,
     *   'heading_re' => regex matching a heading block's text/heading
     *                   field if the heading is "about" this topic,
     *   'body_re'    => regex matching a text_section body strongly,
     *   'body_min_hits' => how many distinct keyword matches in the
     *                      body to consider it "strongly about" the
     *                      topic (so the prose alone gets deleted
     *                      without a redundant heading attached).
     * ]
     */
    private const TOPICS = [
        'how_to_get_to' => [
            'canonical' => 'how_to_get_to',
            'heading_re' => '/\b(getting to|how to get|going to|drive to|driving to|reach\b.{0,30}\bby|travel to|way to reach|via NLEX|via SCTEX|via SLEX|by bus|by car|by jeepney|by van|by ferry|by plane|hours from|minutes from|coming from)\b/i',
            'body_re' => '/\b(via NLEX|via SCTEX|via SLEX|by bus|by car|by jeepney|by van|by ferry|by plane|grab works|tricycle|hours from|minutes from|terminal in|north luzon expressway|skyway)\b/i',
            'body_min_hits' => 3,
        ],
        'attractions' => [
            'canonical' => 'attractions',
            'heading_re' => '/\b(what to do|things to do|places to visit|tourist spots|attractions|sights|outdoor stops|nature and outdoor|family activities|day trips|spots around)\b/i',
            'body_re' => '/\b(museum|park|beach|trail|hiking|island hop|cathedral|church|tour|attractions|tourist spots|visit the)\b/i',
            'body_min_hits' => 4,
        ],
        'foods_to_try' => [
            'canonical' => 'foods_to_try',
            'heading_re' => '/\b(what to eat|where to eat|food finds|local food|dishes to try|filipino food|hungry guide|food trip)\b/i',
            'body_re' => '/\b(carinderia|kainan|palengke|signature dish|local cuisine|street food|filipino|dish|food finds)\b/i',
            'body_min_hits' => 4,
        ],
        'place_history' => [
            'canonical' => 'place_history',
            'heading_re' => '/\b(history of|historical .{0,15}(?:context|background|note)|founded in|established in|colonial era|spanish era|origin of|story of|past of|how this place came to be|war .{0,15}history|brief history)\b/i',
            'body_re' => '/\b(in 18\d\d|in 19\d\d|spanish era|american era|colonial|founded|established|war|revolution|heritage)\b/i',
            'body_min_hits' => 4,
        ],
    ];

    public function run(): void
    {
        $deletedHeading = 0;
        $deletedFollowing = 0;
        $deletedBodyMatch = 0;
        $touchedPages = collect();

        foreach (self::TOPICS as $topicKey => $cfg) {
            $canonical = $cfg['canonical'];
            $pagesWithCanonical = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('block_type', $canonical)
                ->pluck('owner_id')
                ->all();
            if (!$pagesWithCanonical) continue;

            // ---- Pattern 1: redundant heading + adjacent prose ----
            $headingCandidates = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->whereIn('owner_id', $pagesWithCanonical)
                ->where('block_type', 'heading')
                ->get(['id', 'owner_id', 'sort_order', 'payload_json']);

            foreach ($headingCandidates as $h) {
                $p = json_decode($h->payload_json, true) ?: [];
                $headingText = (string) ($p['text'] ?? '');
                if (!preg_match($cfg['heading_re'], $headingText)) continue;

                // Find the immediately following block (by sort_order).
                // If it's a prose block, queue it for deletion too.
                $followingId = DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $h->owner_id)
                    ->where('sort_order', '>', $h->sort_order)
                    ->whereIn('block_type', ['text_section', 'rich_text'])
                    ->orderBy('sort_order')
                    ->value('id');

                // Sanity check: is the following block ACTUALLY adjacent
                // (no other block sits between this heading and it)?
                $betweenCount = DB::table('rg_content_blocks')
                    ->where('owner_type', 'seo_page')
                    ->where('owner_id', $h->owner_id)
                    ->where('sort_order', '>', $h->sort_order)
                    ->where('sort_order', '<', function ($q) use ($followingId) {
                        $q->select('sort_order')->from('rg_content_blocks')->where('id', $followingId);
                    })
                    ->count();

                if ($followingId && $betweenCount === 0) {
                    DB::table('rg_content_blocks')->where('id', $followingId)->delete();
                    $deletedFollowing++;
                }
                DB::table('rg_content_blocks')->where('id', $h->id)->delete();
                $deletedHeading++;
                $touchedPages->push($h->owner_id);
            }

            // ---- Pattern 2: orphan text_sections whose body strongly matches ----
            $proseCandidates = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->whereIn('owner_id', $pagesWithCanonical)
                ->where('block_type', 'text_section')
                ->get(['id', 'owner_id', 'payload_json']);

            foreach ($proseCandidates as $tx) {
                $p = json_decode($tx->payload_json, true) ?: [];
                $body = (string) ($p['body'] ?? '');
                if (strlen($body) < 60) continue; // too short to judge

                preg_match_all($cfg['body_re'], $body, $m);
                $hits = count(array_unique(array_map('strtolower', $m[0] ?? [])));
                if ($hits >= $cfg['body_min_hits']) {
                    DB::table('rg_content_blocks')->where('id', $tx->id)->delete();
                    $deletedBodyMatch++;
                    $touchedPages->push($tx->owner_id);
                }
            }
        }

        // Renormalize sort_order on all touched pages.
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

        $this->command->info('Redundant heading blocks deleted:                  ' . $deletedHeading);
        $this->command->info('Adjacent text_section / rich_text blocks deleted:  ' . $deletedFollowing);
        $this->command->info('Orphan text_sections deleted by body keyword match:' . $deletedBodyMatch);
        $this->command->info('Total prose rows removed:                          ' . ($deletedHeading + $deletedFollowing + $deletedBodyMatch));
        $this->command->info('Pages renormalized:                                ' . $renormalized);
    }
}
