<?php

namespace App\Http\Controllers;

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
        return view('static-page', compact('page', 'renderedBlocks'));
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
