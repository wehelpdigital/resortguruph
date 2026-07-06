<?php

namespace App\Http\Controllers;

use App\Services\UnifiedSearchIndex;
use Illuminate\Http\Request;

/**
 * Typeahead suggestions for the global nav search bar. Filters the
 * cached UnifiedSearchIndex server-side (so the ~1,100-item index is
 * never shipped on every page) and returns the top-ranked matches.
 */
class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $q = mb_strtolower(trim((string) $request->query('q', '')));
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $index = app(UnifiedSearchIndex::class)->build();

        $scored = [];
        foreach ($index as $item) {
            $hay = (string) ($item['haystack'] ?? '');
            $pos = mb_strpos($hay, $q);
            if ($pos === false) {
                continue;
            }
            // Rank: label-prefix > word-start > anywhere; then search
            // volume as a tiebreak, and a small earlier-position bonus.
            $label = mb_strtolower((string) ($item['label'] ?? ''));
            if (mb_strpos($label, $q) === 0) {
                $score = 1000;
            } elseif (mb_strpos(' ' . $hay, ' ' . $q) !== false) {
                $score = 500;
            } else {
                $score = 150;
            }
            $score += min((int) ($item['volume'] ?? 0), 200000) / 2000;
            $score -= min($pos, 50);
            $scored[] = ['s' => $score, 'i' => $item];
        }

        usort($scored, fn ($a, $b) => $b['s'] <=> $a['s']);

        // Cap per type so the grouped dropdown shows variety across
        // Regions / Destinations / Stays / Food / Spots / Blog rather
        // than 18 of one kind.
        $perType = [];
        $results = [];
        foreach ($scored as $row) {
            $it = $row['i'];
            $type = (string) ($it['type'] ?? '');
            $perType[$type] = ($perType[$type] ?? 0) + 1;
            if ($perType[$type] > 6) {
                continue;
            }
            $results[] = [
                'type' => $type,
                'label' => (string) ($it['label'] ?? ''),
                'sub' => (string) ($it['sub'] ?? ''),
                'url' => (string) ($it['url'] ?? '#'),
                'image' => $it['image'] ?? null,
            ];
            if (count($results) >= 18) {
                break;
            }
        }

        return response()->json(['results' => $results]);
    }
}
