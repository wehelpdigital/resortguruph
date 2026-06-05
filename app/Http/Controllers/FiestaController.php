<?php

namespace App\Http\Controllers;

use App\Models\RgFiesta;
use App\Models\RgContentBlock;
use App\Services\BlockRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FiestaController extends Controller
{
    /**
     * GET /fiestas — list page, grouped by region cluster.
     *
     * Renders the published fiestas sorted alphabetically inside each
     * region. The grouping order on the page follows REGION_LABELS in
     * the model so the geographic flow matches the user's mental map
     * (north-to-south Luzon then Visayas-Mindanao then offshore).
     */
    public function index()
    {
        // Cache the grouping query — fiesta data changes infrequently
        // and the join + group-by is otherwise repeated on every hit.
        $grouped = Cache::remember('fiesta-list-by-region', 600, function () {
            $rows = RgFiesta::query()
                ->where('is_published', true)
                ->orderBy('name')
                ->get();
            $byRegion = [];
            foreach (array_keys(RgFiesta::REGION_LABELS) as $regionKey) {
                $byRegion[$regionKey] = $rows->where('region_cluster', $regionKey)->values();
            }
            return $byRegion;
        });

        return view('fiestas.index', [
            'grouped' => $grouped,
            'totalCount' => array_sum(array_map(fn ($c) => $c->count(), $grouped)),
        ]);
    }

    /**
     * GET /fiestas/{slug} — single fiesta page.
     *
     * The content blocks attach polymorphically via
     * (owner_type='fiesta', owner_id=$fiesta->id), so the existing
     * BlockRenderer service can render the page with zero adapter
     * code on its side.
     */
    public function show(Request $request, RgFiesta $fiesta)
    {
        if (!$fiesta->is_published) abort(404);

        // Best-effort pageview bump — same bot-filter approach the
        // keyword pages use so traffic figures stay roughly honest.
        $this->incrementPageviews($fiesta, (string) $request->userAgent());

        $blocks = RgContentBlock::forOwner('fiesta', $fiesta->id);

        return view('fiestas.show', [
            'fiesta' => $fiesta,
            'blocks' => $blocks,
            'renderer' => app(BlockRenderer::class),
        ]);
    }

    /**
     * Skip the pageview bump when the request looks like a bot. Same
     * UA list KeywordPageController uses so the two verticals stay in
     * sync without copying duplicate logic.
     */
    private function incrementPageviews(RgFiesta $fiesta, string $userAgent): void
    {
        $botPatterns = ['Googlebot', 'Bingbot', 'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'YandexBot'];
        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) return;
        }
        RgFiesta::query()->where('id', $fiesta->id)->increment('pageviews_30d');
    }
}
