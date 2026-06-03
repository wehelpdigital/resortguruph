<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Converts custom_html blocks containing the seeded "[Area] (beyond
 * the food)" attractions section into structured attractions blocks
 * so each card becomes independently editable in the builder (image
 * picker + name + blurb + eyebrow per item) instead of a Quill blob.
 *
 * Pattern: h2 + intro <p> + a 3-col grid of attraction cards. Each
 * card = image wrapper + p-4 body containing h3 name + blurb p +
 * short-eyebrow p.
 *
 * Idempotent: blocks already typed attractions are skipped.
 */
class MigrateAttractionsToBlockSeeder extends Seeder
{
    public function run(): void
    {
        $rows = DB::table('rg_content_blocks')
            ->where('block_type', 'custom_html')
            ->where('payload_json', 'like', '%beyond the food%')
            ->select('id', 'payload_json')
            ->orderBy('id')
            ->get();

        $now = Carbon::now()->toDateTimeString();
        $migrated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $payload = json_decode($row->payload_json, true);
            $html = is_array($payload) ? ($payload['html'] ?? '') : '';
            $parsed = $this->tryParse($html);
            if ($parsed === null || empty($parsed['items'])) {
                $skipped++;
                continue;
            }

            DB::table('rg_content_blocks')
                ->where('id', $row->id)
                ->update([
                    'block_type'   => 'attractions',
                    'payload_json' => json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at'   => $now,
                ]);
            $migrated++;
        }

        $this->command->info("Done. Scanned: " . count($rows) . " | Migrated: {$migrated} | Skipped: {$skipped}");
    }

    /**
     * Pull heading, intro, and per-card data from the seeded markup.
     */
    private function tryParse(string $html): ?array
    {
        $payload = [
            'heading' => '',
            'intro'   => '',
            'items'   => [],
        ];

        if (preg_match('~<h2[^>]*>\s*(.+?)\s*</h2>~s', $html, $m)) {
            $payload['heading'] = trim(strip_tags($m[1]));
        }
        if (preg_match('~</h2>\s*<p[^>]*>\s*(.+?)\s*</p>~s', $html, $m)) {
            $payload['intro'] = trim(strip_tags($m[1]));
        }

        // Two card variants exist:
        //   - With image: <div class="rounded-xl overflow-hidden border border-slate-200 bg-white">
        //   - Text only:  <div class="rounded-xl border border-slate-200 bg-white p-5">
        // Both have h3 + blurb p + eyebrow p inside. Opener-to-next-opener
        // splitting handles malformed markup.
        $cardOpenPattern = '~<div\s+class="rounded-xl(?:\s+overflow-hidden)?\s+border\s+border-slate-200\s+bg-white(?:\s+p-5)?"[^>]*>~';
        if (!preg_match_all($cardOpenPattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $hits = count($matches[0]);
        for ($i = 0; $i < $hits; $i++) {
            $openStart = $matches[0][$i][1];
            $openLen = strlen($matches[0][$i][0]);
            $innerStart = $openStart + $openLen;
            $innerEnd = ($i + 1 < $hits) ? $matches[0][$i + 1][1] : strlen($html);
            $inner = substr($html, $innerStart, $innerEnd - $innerStart);
            $item = $this->parseCard($inner);
            if ($item !== null) $payload['items'][] = $item;
        }

        return $payload;
    }

    private function parseCard(string $inner): ?array
    {
        $item = ['name' => '', 'image' => '', 'short' => '', 'blurb' => '', 'url' => ''];

        if (preg_match('~<img\s+src="([^"]+)"\s+alt="([^"]*)"~', $inner, $m)) {
            $item['image'] = $this->normalizeMediaUrl($m[1]);
        }
        if (preg_match('~<h3[^>]*>\s*(.+?)\s*</h3>~s', $inner, $m)) {
            $item['name'] = trim(strip_tags($m[1]));
        }
        if (preg_match('~<p\s+class="text-sm text-slate-600[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $inner, $m)) {
            $item['blurb'] = trim(strip_tags($m[1]));
        }
        if (preg_match('~<p\s+class="text-xs text-slate-400[^"]*"[^>]*>\s*(.+?)\s*</p>~s', $inner, $m)) {
            $item['short'] = trim(strip_tags($m[1]));
        }

        if ($item['name'] === '') return null;
        return $item;
    }

    /**
     * Strip absolute host prefix so /storage URLs resolve on the current
     * domain (same helper the BlockRenderer uses).
     */
    private function normalizeMediaUrl(string $url): string
    {
        return preg_replace('~^https?://[^/]+(/storage/.*)$~i', '$1', $url) ?: $url;
    }
}
