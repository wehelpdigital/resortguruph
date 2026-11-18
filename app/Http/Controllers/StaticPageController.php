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
        $blocks = RgContentBlock::forOwner('static_page', $page->id);
        // Home-style streams (home_*/hub_*/dest_* blocks) are full-bleed,
        // section-based pages. Render them edge-to-edge like the homepage
        // rather than inside the centered prose article. Prose pages
        // (terms/privacy: text_section/heading) keep the reading column.
        $homeStyle = $blocks->contains(fn ($b) => str_starts_with($b->block_type, 'home_')
            || str_starts_with($b->block_type, 'hub_')
            || str_starts_with($b->block_type, 'dest_'));
        // Prose pages that open with an image block use it as the page hero photo.
        $heroImage = null;
        $first = $blocks->first();
        if (!$homeStyle && $first && $first->block_type === 'image' && !empty($first->payload['src'])) {
            $heroImage = $first->payload['src'];
            $blocks = $blocks->slice(1)->values();
        }
        $renderedBlocks = $renderer->renderBlocks($blocks);
        return view('static-page', compact('page', 'renderedBlocks', 'homeStyle', 'heroImage'));
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
