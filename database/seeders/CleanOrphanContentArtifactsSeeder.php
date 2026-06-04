<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Cleans two artefact patterns left over from the multi-pass content
 * migrations:
 *
 *   1. Empty text_section blocks (no heading, no body) — produced when
 *      the pros_cons extractor pulled the only content out of a section.
 *      These render as nothing visible but still occupy a sort_order
 *      slot, so we delete them outright.
 *
 *   2. Orphan curly-quote + "Local tip" footer pattern inside
 *      text_section bodies — these are leftover stubs from an earlier
 *      blockquote extractor that pulled the quote text but never
 *      removed the surrounding markers. The prose between the curly
 *      quote and the Local tip label is the *intended* tip content;
 *      where the page doesn't already have a local_tip block we lift
 *      that prose into a new one, otherwise we just strip the orphan.
 *
 * Idempotent: re-runs find no matches once the database is clean.
 */
class CleanOrphanContentArtifactsSeeder extends Seeder
{
    public function run(): void
    {
        $this->stripOrphanLocalTipPattern();
        $this->deleteEmptyTextSections();
    }

    /**
     * Match the trailing "<curly-quote>...<prose>...Local tip" pattern
     * at the end of a text_section body. Extract the prose, drop it
     * into a local_tip block where possible, then strip the orphan from
     * the text_section.
     */
    private function stripOrphanLocalTipPattern(): void
    {
        $candidates = DB::table('rg_content_blocks')
            ->where('block_type', 'text_section')
            ->where('payload_json', 'like', '%Local tip%')
            ->get();

        $this->command->info('Candidate text_section blocks with "Local tip": ' . $candidates->count());

        // Curly opening quote + arbitrary prose + literal "Local tip"
        // sitting at the end of the body. The (?<quote>.) capture lets
        // us recognise either curly (“) or straight (") openers.
        $pattern = '~\s*(?<quote>[\x{201C}"\x{201D}])\s*\n+(?<prose>[\s\S]*?)\n+\s*Local\s+tip\s*$~u';

        $stripped = 0;
        $tipsCreated = 0;
        foreach ($candidates as $block) {
            $payload = json_decode((string) $block->payload_json, true) ?: [];
            $body = (string) ($payload['body'] ?? '');
            if (!preg_match($pattern, $body, $m)) continue;

            $prose = trim((string) $m['prose']);
            // Strip the orphan from the body (everything from the
            // curly quote onward).
            $matchStart = mb_strrpos($body, $m['quote'], 0, 'UTF-8');
            if ($matchStart === false) continue;
            $newBody = rtrim(mb_substr($body, 0, $matchStart, 'UTF-8'));
            $payload['body'] = $newBody;

            DB::transaction(function () use ($block, $payload, $prose, &$tipsCreated) {
                DB::table('rg_content_blocks')->where('id', $block->id)->update([
                    'payload_json' => json_encode($payload),
                    'updated_at' => now(),
                ]);

                // Upsert a local_tip with the orphan prose. Skip when
                // the page already has one carrying the same body so we
                // don't duplicate.
                if (strlen($prose) < 25) return; // too short to be useful
                $existing = DB::table('rg_content_blocks')
                    ->where('owner_type', $block->owner_type)
                    ->where('owner_id', $block->owner_id)
                    ->where('block_type', 'local_tip')
                    ->get();
                foreach ($existing as $row) {
                    $rowPayload = json_decode((string) $row->payload_json, true) ?: [];
                    if (trim((string) ($rowPayload['body'] ?? '')) === $prose) return;
                }

                $insertSort = (int) $block->sort_order + 1;
                DB::table('rg_content_blocks')
                    ->where('owner_type', $block->owner_type)
                    ->where('owner_id', $block->owner_id)
                    ->where('sort_order', '>=', $insertSort)
                    ->orderByDesc('sort_order')
                    ->get()
                    ->each(function ($row) {
                        DB::table('rg_content_blocks')
                            ->where('id', $row->id)
                            ->update(['sort_order' => $row->sort_order + 1]);
                    });
                DB::table('rg_content_blocks')->insert([
                    'owner_type' => $block->owner_type,
                    'owner_id' => $block->owner_id,
                    'sort_order' => $insertSort,
                    'block_type' => 'local_tip',
                    'payload_json' => json_encode([
                        'eyebrow' => 'Local tip',
                        'body' => $prose,
                        'color' => 'amber',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tipsCreated++;
            });

            $stripped++;
        }

        $this->command->info("Orphan local-tip patterns stripped: {$stripped}");
        $this->command->info("New local_tip blocks created: {$tipsCreated}");
    }

    /**
     * Delete every text_section block whose heading and body are both
     * empty after the cleanup. These are typically the leftovers from
     * earlier extractors that pulled the only content out without
     * removing the wrapper.
     */
    private function deleteEmptyTextSections(): void
    {
        $candidates = DB::table('rg_content_blocks')
            ->where('block_type', 'text_section')
            ->get(['id', 'payload_json']);

        $toDelete = [];
        foreach ($candidates as $b) {
            $p = json_decode((string) $b->payload_json, true) ?: [];
            $heading = trim((string) ($p['heading'] ?? ''));
            $body = trim((string) ($p['body'] ?? ''));
            if ($heading === '' && $body === '') $toDelete[] = $b->id;
        }

        $this->command->info('Empty text_section blocks to delete: ' . count($toDelete));
        foreach (array_chunk($toDelete, 500) as $chunk) {
            DB::table('rg_content_blocks')->whereIn('id', $chunk)->delete();
        }
        $this->command->info('Deleted.');
    }
}
