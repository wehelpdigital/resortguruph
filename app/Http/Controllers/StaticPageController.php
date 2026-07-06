<?php

namespace App\Http\Controllers;

use App\Models\RgContentBlock;
use App\Models\RgStaticPage;
use App\Services\BlockRenderer;

class StaticPageController extends Controller
{
    public function show(string $slug, ?BlockRenderer $renderer = null)
    {
        $page = RgStaticPage::where('slug', $slug)->where('is_published', true)->first();
        if (!$page) abort(404);
        $renderer = $renderer ?: app(BlockRenderer::class);
        $renderedBlocks = $renderer->renderFor('static_page', $page->id);
        // Home-style streams (home_*/hub_*/dest_* blocks) are full-bleed,
        // section-based pages. Render them edge-to-edge like the homepage
        // rather than inside the centered prose article. Prose pages
        // (terms/privacy: text_section/heading) keep the reading column.
        $types = RgContentBlock::where('owner_type', 'static_page')
            ->where('owner_id', $page->id)
            ->pluck('block_type');
        $homeStyle = $types->contains(fn ($t) => str_starts_with($t, 'home_')
            || str_starts_with($t, 'hub_')
            || str_starts_with($t, 'dest_'));
        return view('static-page', compact('page', 'renderedBlocks', 'homeStyle'));
    }

    public function contact(?BlockRenderer $renderer = null)
    {
        $renderer = $renderer ?: app(BlockRenderer::class);
        $page = RgStaticPage::where('slug', 'contact')->where('is_published', true)->first();
        $contactPageContent = RgStaticPage::where('slug', 'contact-page')->where('is_published', true)->first();
        $renderedBlocks = $contactPageContent ? $renderer->renderFor('static_page', $contactPageContent->id) : '';
        return view('contact', compact('page', 'renderedBlocks'));
    }
}
