<?php

namespace App\Http\Controllers;

use App\Models\RgAuthor;
use App\Models\RgBlogComment;
use App\Models\RgBlogPost;
use App\Services\BlockRenderer;
use App\Services\SchemaGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index(BlockRenderer $renderer)
    {
        $posts = RgBlogPost::where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        // Read editorial intro / FAQ / CTA blocks from the
        // `blog-index` static_page row so the index reads as a
        // landing page, not just a card grid.
        $page = \DB::table('rg_static_pages')
            ->where('slug', 'blog-index')
            ->where('is_published', 1)
            ->first();
        $renderedBlocks = $page ? $renderer->renderFor('static_page', $page->id) : '';

        return view('blog.index', compact('posts', 'page', 'renderedBlocks'));
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

        $comments = RgBlogComment::where('blog_post_id', $post->id)
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->get();

        $author = $post->rg_author_id ? RgAuthor::find($post->rg_author_id) : null;

        // Comment-rating aggregate. Comments without a rating are excluded
        // from the average so a 0-star empty default doesn't drag it down.
        $ratedComments = $comments->whereNotNull('rating')->where('rating', '>', 0);
        $avgRating = $ratedComments->count() > 0 ? round($ratedComments->avg('rating'), 2) : null;
        $ratingCount = $ratedComments->count();

        $jsonld = $schema->emit($schema->blogPosting($post))
            . $schema->emit($schema->breadcrumb([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Blog', 'url' => route('blog.index')],
                ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
            ]));
        return view('blog.show', compact('post', 'related', 'renderedBlocks', 'jsonld', 'comments', 'author', 'avgRating', 'ratingCount'));
    }

    /**
     * Member-only comment submission. New comments default to status=pending
     * for admin moderation; admin approves via the Blog Comments admin in
     * the mother app before they appear on the public page.
     */
    public function storeComment(Request $request, RgBlogPost $post)
    {
        if (!Auth::guard('owner')->check()) abort(403);
        $data = $request->validate([
            'comment_text' => 'required|string|min:10|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);
        $user = Auth::guard('owner')->user();

        RgBlogComment::create([
            'blog_post_id' => $post->id,
            'commenter_name' => $user->name,
            'commenter_email' => $user->email,
            'commenter_avatar' => $user->avatar_path ? asset('storage/' . ltrim($user->avatar_path, '/')) : null,
            'comment_text' => trim($data['comment_text']),
            'rating' => $data['rating'] ?? null,
            'status' => 'pending',
            'is_seeded' => false,
        ]);

        return redirect()->route('blog.show', $post->slug)
            ->with('comment_status', 'Thanks. Your comment was submitted and is waiting for moderation.');
    }
}
