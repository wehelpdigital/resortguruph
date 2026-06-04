<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Walks every content block payload and rewrites string fields to
 * remove AI-tell vocabulary, em-dashes, and double-hyphens. Mirrors
 * Resort Guru content rules #2 and #15 (banned-vocab + read-aloud
 * test) so seeded prose stops reading as machine-generated.
 *
 * The rewrite is conservative: only applies inside JSON string values,
 * never inside keys or numeric/array structure, so block schemas stay
 * intact. Re-running is safe — the second pass finds nothing because
 * the replacements collapse to clean text.
 */
class StripAiVocabAndEmDashSeeder extends Seeder
{
    /**
     * Word-level substitutions, applied with word-boundary regex.
     * Case-insensitive — original case is preserved on the replacement
     * (uppercase-first input keeps an uppercase-first output).
     */
    private array $vocab = [
        'delve' => 'look at',
        'tapestry' => 'mix',
        'vibrant' => 'lively',
        'bustling' => 'busy',
        'nestled' => 'set',
        'embark' => 'start',
        'unveil' => 'show',
        'unlock' => 'open',
        'elevate' => 'lift',
        'realm' => 'world',
        'breathtaking' => 'striking',
        'plethora' => 'lot',
        'myriad' => 'many',
        'robust' => 'solid',
        'seamless' => 'smooth',
        'leverage' => 'use',
        'hidden gem' => 'lesser-known spot',
        'must-visit' => 'worth a visit',
        'must-try' => 'worth trying',
        'dive into' => 'get into',
        // Editorial direction: "scene" reads as AI filler when paired
        // with a venue name (the short_version seeder generated
        // "Looking at the X scene?"); drop to "spots" which reads more
        // naturally.
        'scene' => 'spots',
    ];

    /**
     * Phrase-level substitutions. Each entry is [regex, replacement].
     * Order matters — multi-word patterns run first so the single-word
     * vocab pass doesn't mangle their prefixes.
     */
    private array $phrases = [
        ['~\bIn this article, we will explore\b~i', 'Here is the read on'],
        ['~\bIn conclusion\b~i', 'So'],
        ['~\bTo sum up\b~i', 'So'],
        ['~\bIn summary\b~i', 'So'],
        ['~\bPicture this:\s*~i', ''],
        ['~\bImagine:\s*~i', ''],
        ['~\bAh,\s*~i', ''],
        // "Looking at the X scene? In short, ..." became the canonical
        // short_version opener — strip the "Looking at the ... scene?
        // In short," prefix entirely since it parses as filler now.
        ['~Looking at the [^?]+\?\s*In short,\s*~i', 'In short, '],
    ];

    public function run(): void
    {
        $blocks = DB::table('rg_content_blocks')->get(['id', 'payload_json']);
        $this->command->info('Blocks to scan: ' . $blocks->count());

        $rewritten = 0;
        $emDashStripped = 0;
        $doubleHyphenStripped = 0;
        $vocabHits = 0;

        foreach ($blocks as $block) {
            $payload = json_decode((string) $block->payload_json, true);
            if (!is_array($payload)) continue;

            $orig = $payload;
            $stats = [
                'em' => 0,
                'dh' => 0,
                'vocab' => 0,
            ];

            $cleaned = $this->cleanStructure($payload, $stats);
            if ($cleaned === $orig) continue;

            DB::table('rg_content_blocks')->where('id', $block->id)->update([
                'payload_json' => json_encode($cleaned),
                'updated_at' => now(),
            ]);
            $rewritten++;
            $emDashStripped += $stats['em'];
            $doubleHyphenStripped += $stats['dh'];
            $vocabHits += $stats['vocab'];
        }

        $this->command->info("Blocks rewritten: {$rewritten}");
        $this->command->info("  em-dashes replaced: {$emDashStripped}");
        $this->command->info("  double-hyphens replaced: {$doubleHyphenStripped}");
        $this->command->info("  vocab substitutions: {$vocabHits}");
    }

    /**
     * Recurse through array structure, transforming any string leaf.
     * Keys are never touched. Numeric/bool/null leaves pass through.
     */
    private function cleanStructure(mixed $node, array &$stats): mixed
    {
        if (is_array($node)) {
            $out = [];
            foreach ($node as $k => $v) {
                $out[$k] = $this->cleanStructure($v, $stats);
            }
            return $out;
        }
        if (is_string($node)) {
            return $this->cleanString($node, $stats);
        }
        return $node;
    }

    /**
     * Apply all text transforms to one string. Stat counts are
     * accumulated by reference so the caller can report aggregate
     * numbers per seeder run.
     */
    private function cleanString(string $text, array &$stats): string
    {
        if ($text === '') return $text;

        // 1. em-dash → comma+space. Strip surrounding spaces in case
        //    the author wrote " — " so we don't leave a double space.
        $count = 0;
        $text = preg_replace('~\s*[\x{2014}\x{2013}]\s*~u', ', ', $text, -1, $count);
        $stats['em'] += $count;

        // 2. Standalone " -- " (with spaces, used as em-dash) → comma+space.
        //    Words-joined-by-hyphen like "world-class" or "five-hour" stay
        //    intact because they have no surrounding spaces.
        $count = 0;
        $text = preg_replace('~\s+--+\s+~', ', ', $text, -1, $count);
        $stats['dh'] += $count;

        // 3. Phrase substitutions (multi-word, ordered).
        foreach ($this->phrases as [$regex, $repl]) {
            $count = 0;
            $text = preg_replace($regex, $repl, $text, -1, $count);
            $stats['vocab'] += $count;
        }

        // 4. Single-word + bigram vocab substitutions. Word-boundary
        //    anchored. Case-preserving: when the matched token starts
        //    uppercase, the replacement keeps an uppercase-first form.
        foreach ($this->vocab as $bad => $good) {
            $pattern = '~\b' . preg_quote($bad, '~') . '\b~i';
            $text = preg_replace_callback($pattern, function ($m) use ($good, &$stats) {
                $stats['vocab']++;
                $token = $m[0];
                // Title-case match → title-case replacement; else lowercase.
                if (mb_strtoupper(mb_substr($token, 0, 1, 'UTF-8'), 'UTF-8') === mb_substr($token, 0, 1, 'UTF-8')) {
                    return mb_strtoupper(mb_substr($good, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($good, 1, null, 'UTF-8');
                }
                return $good;
            }, $text);
        }

        // 5. Collapse the doubled punctuation that em-dash removal can
        //    leave behind ("foo, , bar" → "foo, bar"; "foo, ." → "foo.").
        $text = preg_replace('~,\s*,~', ',', $text);
        $text = preg_replace('~,\s*\.~', '.', $text);

        return $text;
    }
}
