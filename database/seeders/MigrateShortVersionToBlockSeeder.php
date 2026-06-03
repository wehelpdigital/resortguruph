<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Extracts every "The short version" gradient summary card out of
 * custom_html / rich_text blocks and into a standalone short_version
 * block. Two cases:
 *   - Standalone: the entire source block IS the gradient card → swap
 *     block_type to short_version, replace payload.
 *   - Embedded: the gradient sits between other prose. Remove it from
 *     the source block's HTML, then INSERT a new short_version block
 *     right after the source (shifting subsequent sort_orders by +1).
 *
 * Idempotent: skips blocks that no longer contain the gradient pattern
 * on re-runs.
 */
class MigrateShortVersionToBlockSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();
        $standalone = 0;
        $embedded = 0;
        $skipped = 0;

        // Process blocks in REVERSE id order so the shifted sort_orders
        // don't collide with siblings we haven't processed yet.
        $rows = DB::table('rg_content_blocks')
            ->whereIn('block_type', ['rich_text', 'custom_html'])
            ->where('payload_json', 'like', '%linear-gradient(135deg,#0f172a%')
            ->orderByDesc('id')
            ->get(['id', 'owner_type', 'owner_id', 'sort_order', 'block_type', 'payload_json']);

        foreach ($rows as $row) {
            $payload = json_decode($row->payload_json, true);
            $html = $payload['html'] ?? '';
            $gradient = $this->extractGradientDiv($html);
            if ($gradient === null) {
                $skipped++;
                continue;
            }
            $svPayload = $this->parseGradient($gradient);
            if ($svPayload === null) {
                $skipped++;
                continue;
            }

            // Standalone if the whole HTML is the gradient div (modulo
            // surrounding whitespace).
            if (trim($html) === trim($gradient)) {
                DB::table('rg_content_blocks')
                    ->where('id', $row->id)
                    ->update([
                        'block_type'   => 'short_version',
                        'payload_json' => json_encode($svPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at'   => $now,
                    ]);
                $standalone++;
                continue;
            }

            // Embedded — extract + insert a new short_version block right
            // after the source.
            $newHtml = str_replace($gradient, '', $html);
            $newHtml = preg_replace('~\s+~', ' ', $newHtml);
            $newHtml = trim($newHtml);

            DB::transaction(function () use ($row, $payload, $newHtml, $svPayload, $now) {
                $payload['html'] = $newHtml;
                DB::table('rg_content_blocks')
                    ->where('id', $row->id)
                    ->update([
                        'payload_json' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'updated_at'   => $now,
                    ]);
                DB::table('rg_content_blocks')
                    ->where('owner_type', $row->owner_type)
                    ->where('owner_id', $row->owner_id)
                    ->where('sort_order', '>', $row->sort_order)
                    ->increment('sort_order');
                DB::table('rg_content_blocks')->insert([
                    'owner_type'   => $row->owner_type,
                    'owner_id'     => $row->owner_id,
                    'sort_order'   => $row->sort_order + 1,
                    'block_type'   => 'short_version',
                    'payload_json' => json_encode($svPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            });
            $embedded++;
            if (($standalone + $embedded) % 100 === 0) {
                $this->command->info("  processed " . ($standalone + $embedded) . "...");
            }
        }

        $this->command->info(
            "Done. standalone: {$standalone} | embedded extracted: {$embedded} | skipped: {$skipped}"
        );
    }

    /**
     * Find the first gradient div + walk balanced divs to its close.
     * Returns the entire `<div ...>...</div>` substring, or null.
     */
    private function extractGradientDiv(string $html): ?string
    {
        if (!preg_match(
            '~<div\s+class="not-prose my-8 p-6 rounded-2xl"\s+style="background:linear-gradient\(135deg,#0f172a[^"]*"[^>]*>~',
            $html,
            $m,
            PREG_OFFSET_CAPTURE
        )) {
            return null;
        }
        $start = $m[0][1];
        $afterOpen = $start + strlen($m[0][0]);
        $depth = 1;
        $pos = $afterOpen;
        $len = strlen($html);
        while ($depth > 0 && $pos < $len) {
            $no = stripos($html, '<div', $pos);
            $nc = stripos($html, '</div>', $pos);
            if ($nc === false) return null;
            if ($no !== false && $no < $nc) {
                $depth++;
                $pos = $no + 4;
            } else {
                $depth--;
                $pos = $nc + 6;
            }
        }
        return substr($html, $start, $pos - $start);
    }

    /**
     * Pull eyebrow + body out of the gradient div's inner content.
     */
    private function parseGradient(string $div): ?array
    {
        $eyebrow = '';
        if (preg_match(
            '~<div\s+class="text-\[10px\] uppercase tracking-\[0\.2em\] font-bold mb-3"\s+style="color:#fbbf24">\s*(.+?)\s*</div>~s',
            $div,
            $m
        )) {
            $eyebrow = trim(strip_tags($m[1]));
        }
        $body = '';
        if (preg_match(
            '~<p\s+class="text-base leading-relaxed m-0">\s*(.+?)\s*</p>~s',
            $div,
            $m
        )) {
            $body = trim(strip_tags($m[1]));
        }
        if ($eyebrow === '' && $body === '') return null;
        return [
            'eyebrow'      => $eyebrow !== '' ? $eyebrow : 'The short version',
            'body'         => $body,
            'accent_color' => 'amber',
        ];
    }
}
