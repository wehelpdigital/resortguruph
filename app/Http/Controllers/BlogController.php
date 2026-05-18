<?php

namespace App\Http\Controllers;

use App\Models\RgBlogPost;
use App\Services\BlockRenderer;
use App\Services\SchemaGenerator;

class BlogController extends Controller
{
    public function index()
    {
        $posts = RgBlogPost::where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);
        return view('blog.index', compact('posts'));
    }

    public function show(RgBlogPost $post, BlockRenderer $renderer, SchemaGenerator $schema)
    {
        if ($post->status !== 'published') abort(404);
        $related = RgBlogPost::where('status', 'published')
            ->where('id', '<>', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
        $renderedBlocks = $renderer->renderFor('blog_post', $post->id);
        $jsonld = $schema->emit($schema->blogPosting($post))
            . $schema->emit($schema->breadcrumb([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Blog', 'url' => route('blog.index')],
                ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
            ]));
        return view('blog.show', compact('post', 'related', 'renderedBlocks', 'jsonld'));
    }
}
