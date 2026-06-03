<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rewrites any rich_text or custom_html block whose HTML starts with the
 * canonical quick-facts strip (a not-prose grid wrapper containing 3-4
 * "rounded-lg p-4 text-center" cards) into a structured quick_facts
 * block. Effect:
 *   - Public render switches to the canonical quickFacts() renderer
 *     (consistent grid-cols-1 md:grid-cols-2 lg:grid-cols-4 layout).
 *   - Mother-admin builder shows it as a structured quick_facts editor
 *     instead of a Quill blob, so each card is independently editable.
 *
 * Idempotent: blocks already of type quick_facts are skipped.
 */
class MigrateQuickFactsToBlockSeeder extends Seeder
{
    /** Map quick-facts card background hex → palette key. */
    private const COLOR_BY_BG = [
        '#eff6ff' => 'blue',
        '#ecfdf5' => 'emerald',
        '#fff1f2' => 'rose',
        '#fffbeb' => 'amber',
        '#f5f3ff' => 'violet',
        '#f8fafc' => 'slate',
    ];

    /** Map card label text → icon slug. */
    private const ICON_BY_LABEL = [
        'per person'       => 'money',
        'easiest window'   => 'clock',
        'avoid'            => 'warning',
        'avoid (weekends)' => 'warning',
        'traffic'          => 'traffic',
        'food zones'       => 'food',
        'season'           => 'calendar',
        'best for'         => 'star',
    ];

    public function run(): void
    {
        $rows = DB::table('rg_content_blocks')
            ->whereIn('block_type', ['rich_text', 'custom_html'])
            ->select('id', 'block_type', 'payload_json')
            ->orderBy('id')
            ->get();

        $now = Carbon::now()->toDateTimeString();
        $scanned = 0;
        $migrated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $scanned++;
            $payload = json_decode($row->payload_json, true);
            $html = is_array($payload) ? ($payload['html'] ?? '') : '';
            $parsed = $this->tryParseStrip($html);
            if ($parsed === null) {
                $skipped++;
                continue;
            }

            DB::table('rg_content_blocks')
                ->where('id', $row->id)
                ->update([
                    'block_type'   => 'quick_facts',
                    'payload_json' => json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at'   => $now,
                ]);
            $migrated++;
        }

        $this->command->info("Done. Scanned: {$scanned} | Migrated: {$migrated} | Skipped: {$skipped}");
    }

    /**
     * Return a quick_facts payload if $html matches the canonical strip,
     * or null to skip.
     */
    private function tryParseStrip(string $html): ?array
    {
        // Strip must start with (after optional whitespace) the not-prose
        // grid wrapper with 2 or 4 cols on md.
        if (!preg_match('~^\s*<div\s[^>]*class="not-prose my-8 grid grid-cols-[12] md:grid-cols-(?:[234])[^"]*"[^>]*>~', $html, $m)) {
            return null;
        }
        // Cards are extracted opener-by-opener: each card's content runs
        // from one opener to the start of the next opener (or end of strip).
        // This survives malformed strips with missing </div> tags — a real
        // bug I hit on MOA where my earlier hand-patch left the Avoid card
        // unclosed and a balanced-div walker over-counted the Traffic card
        // as nested inside Avoid.
        $cards = [];
        $cardOpenPattern = '~<div\s+(?:data-traffic-card\s+)?class="rounded-lg p-4 text-center"\s+style="background:(#[0-9a-fA-F]+);[^"]*"[^>]*>~';
        if (!preg_match_all($cardOpenPattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $hits = count($matches[0]);
        for ($i = 0; $i < $hits; $i++) {
            $bg = strtolower($matches[1][$i][0]);
            $openStart = $matches[0][$i][1];
            $openLen = strlen($matches[0][$i][0]);
            $innerStart = $openStart + $openLen;
            $innerEnd = ($i + 1 < $hits) ? $matches[0][$i + 1][1] : strlen($html);
            $inner = substr($html, $innerStart, $innerEnd - $innerStart);
            $card = $this->parseCard($bg, $inner);
            if ($card !== null) $cards[] = $card;
        }

        if (count($cards) < 3) return null;

        return [
            'heading' => '',
            'cards' => $cards,
        ];
    }

    private function parseCard(string $bg, string $inner): ?array
    {
        $color = self::COLOR_BY_BG[$bg] ?? 'blue';

        $big = '';
        if (preg_match('~<div\s+class="text-2xl font-bold"\s+style="color:#[0-9a-fA-F]+">\s*(.+?)\s*</div>~s', $inner, $m)) {
            $big = $this->clean($m[1]);
        }
        $label = '';
        if (preg_match('~<div\s+class="text-\[10px\] uppercase[^"]*"\s+style="color:#[0-9a-fA-F]+">\s*(.+?)\s*</div>~s', $inner, $m)) {
            $label = $this->clean($m[1]);
        }
        $detail = '';
        if (preg_match('~<div\s+class="text-xs text-slate-600 mt-1">\s*(.+?)\s*</div>~s', $inner, $m)) {
            $detail = $this->clean($m[1]);
        }

        if ($big === '' && $label === '') return null;

        $iconKey = strtolower($label);
        $icon = self::ICON_BY_LABEL[$iconKey] ?? 'info';

        return compact('icon', 'color', 'big', 'label', 'detail');
    }

    private function clean(string $s): string
    {
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = str_replace(['&amp;', '&nbsp;', '&ndash;'], ['&', ' ', '-'], $s);
        return trim($s);
    }
}
