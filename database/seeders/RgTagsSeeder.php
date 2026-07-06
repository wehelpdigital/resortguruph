<?php

namespace Database\Seeders;

use App\Models\RgKeyword;
use App\Models\RgTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Populates rg_tags from every PUBLISHED resort keyword page.
 *
 * Each keyword phrase is reduced to a short CamelCase tag (filler words and
 * listing-type words dropped) so it reads like a place hashtag (#Cebu,
 * #ElNido) and collapses duplicates down to the place. Re-runnable: existing
 * tags are updated in place, new ones inserted.
 */
class RgTagsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('rg_tags') || !Schema::hasTable('rg_keywords')) {
            return;
        }

        $stop = ['in', 'on', 'at', 'of', 'the', 'a', 'an', 'to', 'for', 'and', 'or', 'near', 'with', 'your', 'best', 'top',
                 'resorts', 'resort', 'hotels', 'hotel', 'airbnb', 'airbnbs', 'stays', 'stay', 'places', 'place',
                 'beach', 'beaches', 'tourist', 'spot', 'spots', 'private', 'pool'];

        $rows = RgKeyword::query()
            ->where('category', 'resort')
            ->whereHas('seoPage', fn ($q) => $q->where('is_published', true))
            ->orderByDesc('search_volume_monthly')
            ->get(['id', 'slug', 'phrase', 'search_volume_monthly']);

        $seen = [];
        $position = 0;

        foreach ($rows as $kw) {
            $slug = (string) $kw->slug;
            if ($slug === '') {
                continue;
            }

            $words = preg_split('/[^a-z0-9]+/i', mb_strtolower((string) $kw->phrase), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $sig = array_values(array_filter($words, fn ($w) => !in_array($w, $stop, true)));
            if (empty($sig)) {
                $sig = $words;
            }
            $sig = array_slice($sig, 0, 3);

            $tag = '';
            foreach ($sig as $w) {
                $tag .= ucfirst($w);
            }
            if ($tag === '') {
                continue;
            }

            $key = mb_strtolower($tag);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            RgTag::updateOrCreate(
                ['tag' => $tag],
                [
                    'slug' => $slug,
                    'keyword_id' => $kw->id,
                    'position' => $position++,
                    'search_volume_monthly' => (int) $kw->search_volume_monthly,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info("rg_tags populated: {$position} tags.");
    }
}
