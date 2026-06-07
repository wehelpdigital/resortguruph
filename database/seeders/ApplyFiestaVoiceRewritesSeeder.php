<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Applies the fiesta-content voice-rewrite batch produced by the
 * content-audit agent. The JSON dump lives at
 * database/data/fiesta_voice_rewrites.json. The bulk of the rewrites
 * are literal-`\n` → real-newline fixes, plus a handful of stray
 * quote-wrapper strips, one leaked </parameter> tag, and a small
 * number of human-voice tweaks for blocks that read as templated.
 *
 * The seeder is defensive about which key inside the block's `data`
 * JSON column holds the body text. Some seeded blocks use `body`,
 * some use `text`. We update whichever one matches the rewrite's
 * declared field, falling back to whichever is present.
 */
class ApplyFiestaVoiceRewritesSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/fiesta_voice_rewrites.json');
        if (!is_file($path)) {
            $this->command->warn("Missing rewrites file: {$path}");
            return;
        }
        $payload = json_decode(file_get_contents($path), true);
        if (!is_array($payload) || !isset($payload['rewrites'])) {
            $this->command->warn('Invalid rewrites JSON: no `rewrites` key');
            return;
        }

        $applied = 0;
        $skipped = 0;
        $missing = 0;
        foreach ($payload['rewrites'] as $rw) {
            $blockId = $rw['block_id'] ?? null;
            $newValue = $rw['new_value'] ?? null;
            if (!$blockId || $newValue === null) {
                $skipped++;
                continue;
            }
            $row = DB::table('rg_content_blocks')->where('id', $blockId)->first();
            if (!$row) {
                $missing++;
                continue;
            }
            $data = json_decode($row->payload_json ?? '{}', true) ?: [];

            // Pick which key holds the body. Prefer the field the
            // rewrite declared; fall back to the only text-bearing
            // key on the row if that one doesn't exist.
            $field = $rw['field'] ?? 'body';
            $candidates = ['body', 'text', 'content'];
            if (!array_key_exists($field, $data)) {
                $found = null;
                foreach ($candidates as $k) {
                    if (array_key_exists($k, $data) && is_string($data[$k])) {
                        $found = $k;
                        break;
                    }
                }
                if (!$found) {
                    $skipped++;
                    continue;
                }
                $field = $found;
            }

            $data[$field] = $newValue;
            DB::table('rg_content_blocks')
                ->where('id', $blockId)
                ->update([
                    'payload_json' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
            $applied++;
        }

        $this->command->info("Voice rewrites applied: {$applied}");
        if ($skipped) $this->command->warn("Skipped (no field match / bad row): {$skipped}");
        if ($missing) $this->command->warn("Missing blocks: {$missing}");
    }
}
