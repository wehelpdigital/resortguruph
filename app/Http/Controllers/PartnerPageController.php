<?php

namespace App\Http\Controllers;

use App\Models\RgContentBlock;
use App\Services\BlockRenderer;
use App\Support\LiveEditToken;
use Illuminate\Support\Facades\DB;

/**
 * The public "Become a Partner" page. Block-driven when the
 * `become-a-partner` rg_static_pages row has blocks attached (the
 * partner_* custom blocks + reused home_faq / home_cta_band), so it
 * is fully editable in the mother-app block builder. Falls back to
 * the hardcoded landing view when no blocks exist. Mirrors the hub
 * and destinations controllers' block-render pattern.
 */
class PartnerPageController extends Controller
{
    public function index()
    {
        $page = DB::table('rg_static_pages')
            ->where('slug', 'become-a-partner')
            ->where('is_published', 1)
            ->first();

        if ($page) {
            $blocks = RgContentBlock::forOwner('static_page', $page->id);
            if ($blocks->isNotEmpty()) {
                $liveEdit = false;
                $request = request();
                if ($request && $request->query('_lt')) {
                    $liveEdit = LiveEditToken::valid('become-a-partner', $request->query('_lt'));
                }
                $renderer = app(BlockRenderer::class);
                $renderedBlocks = $renderer->renderBlocks($blocks, [
                    'static_page_id' => $page->id,
                    'live_edit' => $liveEdit,
                ]);
                return view('landing.become-partner-blocks', [
                    'page' => $page,
                    'renderedBlocks' => $renderedBlocks,
                    'liveEdit' => $liveEdit,
                ]);
            }
        }

        return view('landing.become-partner');
    }
}
