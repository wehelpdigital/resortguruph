<?php

namespace App\Http\Controllers\Concerns;

use App\Models\RgContentBlock;
use App\Services\BlockRenderer;
use App\Support\LiveEditToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared block-render logic for the 4 hub-page controllers
 * (FoodsController, ActivitiesController, BuysController,
 * CulturesController). Each hub has a corresponding
 * rg_static_pages row keyed by slug; when blocks are attached
 * the controller's index() renders the block stream + passes
 * categories context. Otherwise the controller falls through to
 * its legacy hardcoded view.
 *
 * Backwards-compatible: a hub with zero blocks renders unchanged.
 * Live Editor support: the request's _lt query is validated
 * against the static_page slug and live_edit is passed into
 * BlockRenderer context.
 */
trait RendersBlockableHub
{
    protected function renderHubBlocks(string $slug, array $hubData = [])
    {
        $page = DB::table('rg_static_pages')->where('slug', $slug)->where('is_published', 1)->first();
        if (!$page) return null;

        $blocks = RgContentBlock::forOwner('static_page', $page->id);
        if ($blocks->isEmpty()) return null;

        $liveEdit = false;
        $request = request();
        if ($request instanceof Request && $request->query('_lt')) {
            $liveEdit = LiveEditToken::valid($slug, $request->query('_lt'));
        }

        $renderer = app(BlockRenderer::class);
        $rendered = $renderer->renderBlocks($blocks, array_merge([
            'static_page_id' => $page->id,
            'hub_slug' => $slug,
            'live_edit' => $liveEdit,
        ], $hubData));

        return view('hubs.blocks', [
            'page' => $page,
            'renderedBlocks' => $rendered,
            'liveEdit' => $liveEdit,
        ]);
    }
}
