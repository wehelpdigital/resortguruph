<?php

namespace Database\Seeders;

use App\Services\BlogContentEnhancer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Imports the 6 rewritten blog posts from database/data/blog_posts.php into
 * rg_blog_posts (upsert by slug) and generates 4-6 fake comments per post in
 * rg_blog_comments. Cover image falls back to the destination-1 photo when
 * the source blog's image URL was not verified.
 *
 * The mother-app blog editor reads rg_blog_posts so this seed shows up there
 * for further editing. Re-running upserts content but preserves comments.
 */
class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        // Load the curated 6-post seed file plus any of the batch files written
        // by the parallel research agents. This lets the seeder scale to 100+
        // blog posts without needing to merge them into a single file by hand.
        $posts = require database_path('data/blog_posts.php');
        foreach (glob(database_path('data/blog_posts_batch*.php')) as $batchFile) {
            $batch = require $batchFile;
            if (is_array($batch)) {
                $posts = array_merge($posts, $batch);
            }
        }
        $destinations = require database_path('data/destinations.php');
        $now = now();
        $authorByCluster = $this->buildAuthorByClusterMap();

        // Load bespoke per-post image overlays written by the parallel
        // image-enhancement agents. Each batch file is an array
        // slug => [['anchor' => '...', 'position' => 'before|after', 'html' => '...']].
        // We merge by slug (later batches override earlier ones intentionally).
        $imageOverlays = [];
        foreach (glob(database_path('data/blog_image_overlays_batch*.php')) as $overlayFile) {
            $batch = require $overlayFile;
            if (is_array($batch)) $imageOverlays = array_replace($imageOverlays, $batch);
        }

        $upserted = 0;
        $enhancer = new BlogContentEnhancer();
        foreach ($posts as $p) {
            $coverPath = $this->resolveCoverPath($p['cover_destination_key'] ?? null, $destinations);
            $cluster = $destinations[$p['cover_destination_key'] ?? '']['cluster'] ?? 'other';
            $rgAuthorId = $authorByCluster[$cluster] ?? $authorByCluster['_default'] ?? null;

            // Strip the "Inspired by and adapted from X" source-credit paragraph
            // from the content (user directive). Backlink data stays in the
            // source_url field on the data file in case we want to render it
            // differently later — only the rendered prose drops the credit.
            $cleanedHtml = preg_replace(
                '#<p[^>]*class=["\']source-credit["\'][^>]*>.*?</p>#is',
                '',
                $p['content_html']
            );

            // Apply per-post image overlays (figure inserts at named anchors)
            // before the enhancer runs, so the anchors match the raw HTML the
            // agents read from the data files.
            if (isset($imageOverlays[$p['slug']])) {
                $cleanedHtml = $this->applyImageOverlays($cleanedHtml, $imageOverlays[$p['slug']]);
            }

            $postWithCleanContent = array_merge($p, ['content_html' => $cleanedHtml]);

            $row = [
                'title' => $p['title'],
                'excerpt' => $p['excerpt'],
                'content_html' => $enhancer->enhance($this->appendStaySection($postWithCleanContent, $destinations)),
                'cover_path' => $coverPath,
                'meta_title' => $p['meta_title'],
                'meta_description' => $p['meta_description'],
                'status' => 'published',
                'published_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('rg_blog_posts', 'tags')) {
                $row['tags'] = $p['tags'] ?? null;
            }
            // rg_blog_posts.rg_author_id added in the authors migration. The
            // mother-app user 'author_id' stays for compatibility with the
            // existing schema; our Filipino-persona byline uses rg_author_id.
            if (Schema::hasColumn('rg_blog_posts', 'rg_author_id')) {
                $row['rg_author_id'] = $rgAuthorId;
            }

            // Subtitle + TLDR + WWWW are mother-system-editable fields. The
            // seeder generates a sensible default from the post's existing
            // metadata + destination context so unedited posts already show
            // something on the public page. Admins can override per-post.
            $destForPost = $destinations[$p['cover_destination_key'] ?? ''] ?? null;
            if (Schema::hasColumn('rg_blog_posts', 'subtitle')) {
                $row['subtitle'] = $this->deriveSubtitle($p, $destForPost);
            }
            if (Schema::hasColumn('rg_blog_posts', 'tldr')) {
                $row['tldr'] = $this->deriveTldr($p, $destForPost);
            }
            if (Schema::hasColumn('rg_blog_posts', 'wwww_json')) {
                $row['wwww_json'] = json_encode($this->deriveWwww($p, $destForPost));
            }

            $existing = DB::table('rg_blog_posts')->where('slug', $p['slug'])->first();
            if ($existing) {
                DB::table('rg_blog_posts')->where('id', $existing->id)->update($row);
                $postId = $existing->id;
            } else {
                $row['slug'] = $p['slug'];
                $row['created_at'] = $now;
                $row['author_id'] = $this->getDefaultAuthorId();
                $postId = DB::table('rg_blog_posts')->insertGetId($row);
            }
            $upserted++;

            $this->seedComments($postId, $p['slug'], $p['title']);
        }

        $this->backfillCommentRatings();

        $this->command->info("Blog posts upserted: $upserted");
        $this->command->info("Total comments now: " . DB::table('rg_blog_comments')->count());
    }

    /**
     * Existing seeded comments predate the rating column; this back-fills a
     * weighted-random rating so the public rating chip + per-comment stars
     * have data on the very first reseed after the migration ran.
     */
    private function backfillCommentRatings(): void
    {
        if (!Schema::hasColumn('rg_blog_comments', 'rating')) return;
        $needBackfill = DB::table('rg_blog_comments')
            ->where('is_seeded', 1)
            ->whereNull('rating')
            ->select(['id', 'blog_post_id'])
            ->get();
        if ($needBackfill->isEmpty()) return;

        $weights = [5, 5, 5, 5, 5, 5, 4, 4, 4, 3];
        foreach ($needBackfill as $row) {
            $seed = abs(crc32($row->blog_post_id . '_' . $row->id));
            $rating = $weights[$seed % count($weights)];
            DB::table('rg_blog_comments')->where('id', $row->id)->update(['rating' => $rating]);
        }
        $this->command->info("Backfilled ratings on " . $needBackfill->count() . " seeded comments.");
    }

    /**
     * Maps each region cluster to an RgAuthor id so blog posts get a region-
     * appropriate byline (Joaquin for Visayas/Palawan, Migs for Mindanao, etc.).
     */
    private function buildAuthorByClusterMap(): array
    {
        $authors = DB::table('rg_authors')->where('status', 'active')->orderBy('sort_order')->get(['id', 'covers_clusters']);
        $map = [];
        foreach ($authors as $author) {
            foreach (explode(',', $author->covers_clusters ?? '') as $cluster) {
                $cluster = trim($cluster);
                if ($cluster !== '' && !isset($map[$cluster])) {
                    $map[$cluster] = $author->id;
                }
            }
        }
        $map['_default'] = $authors->first()->id ?? null;
        return $map;
    }

    /**
     * Appends a "Plan your stay in {Location}" backlink section to the blog
     * content_html. Picks 2-3 related keyword pages from the destination's
     * cluster + the cover destination's own keyword pages, with a 200+ word
     * content paragraph before the links (internal-link SEO + reader utility).
     */
    private function appendStaySection(array $post, array $destinations): string
    {
        $destKey = $post['cover_destination_key'] ?? null;
        if (!$destKey || !isset($destinations[$destKey])) return $post['content_html'];

        $dest = $destinations[$destKey];
        $location = $dest['name'];
        $cluster = $dest['cluster'] ?? 'other';

        // Pick 3 related keyword pages: same cluster, prioritize higher volume.
        $related = DB::table('rg_keywords')
            ->where('status', 'active')
            ->where('cluster_tag', $cluster)
            ->orderByDesc('search_volume_monthly')
            ->limit(3)
            ->get(['slug', 'phrase', 'search_volume_monthly']);

        if ($related->isEmpty()) return $post['content_html'];

        $firstSpot = $dest['spots'][0]['name'] ?? 'the local spots';
        $firstFood = isset($dest['food'][0]) ? (preg_match('/^([^(]+)\(/', $dest['food'][0], $m) ? trim($m[1]) : trim(explode(' at ', $dest['food'][0])[0])) : 'the local food';

        // Wrap the whole section in .rg-stay-block so the public layout's CSS
        // gives it real padding + a soft tint (user feedback: plain links
        // looked stranded). Each card carries the keyword's cover image.
        $html = '<div class="rg-stay-block not-prose"><h2 class="text-2xl font-bold text-slate-900 mb-3">Where to stay near ' . htmlspecialchars($location) . '</h2>';
        $html .= '<p class="text-slate-700 leading-relaxed mb-3">If this guide pushed ' . htmlspecialchars($location) . ' onto your shortlist, the next question is where to sleep. Properties near ' . htmlspecialchars($firstSpot) . ' open up at every price point, from family-runs to the bigger names. Pick by the weekend you are planning: a reunion with a function hall, a quiet couple stay near a heritage walk, or a barkada trip where the pool is the main event.</p>';
        $html .= '<p class="text-slate-700 leading-relaxed mb-5">Our destination guides cover the picks that hold up across recent guest feedback, paired with notes on which barangay to base in. If you are coming for the food, you will land close to ' . htmlspecialchars($firstFood) . ' no matter which property you book.</p>';

        // Image card grid replacing the prior text-link list
        $html .= '<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">';
        foreach ($related as $kw) {
            $imgPath = $this->resolveKeywordCoverPath($kw->slug);
            $imgSrc = $imgPath ? asset('storage/' . $imgPath) : 'https://placehold.co/600x400/e2e8f0/64748b?text=' . urlencode($kw->phrase);
            $html .= '<a href="' . url($kw->slug) . '" class="block rounded-xl overflow-hidden border border-slate-200 bg-white hover:border-slate-300 hover:shadow-md transition group">';
            $html .= '<div class="aspect-[4/3] bg-slate-100 overflow-hidden">';
            $html .= '<img src="' . htmlspecialchars($imgSrc) . '" alt="' . htmlspecialchars(ucwords($kw->phrase)) . '" class="w-full h-full object-cover group-hover:scale-105 transition" loading="lazy">';
            $html .= '</div>';
            $html .= '<div class="p-4">';
            $html .= '<div class="text-xs uppercase tracking-wider text-slate-500 font-semibold mb-1">Curated picks</div>';
            $html .= '<div class="font-bold text-slate-900 group-hover:text-brand-600 transition leading-tight">' . htmlspecialchars(ucwords($kw->phrase)) . '</div>';
            $html .= '</div></a>';
        }
        $html .= '</div>';

        $html .= '<p class="text-slate-700 leading-relaxed">Each page above has its own tourist spots, transport breakdown, Google Maps embed, and recent traveler reviews. Use them to cross-reference, then book directly with the property to skip the aggregator commission.</p></div>';

        return $post['content_html'] . $html;
    }

    /**
     * Find a cover image for a keyword's destination (uses the same cluster
     * lookup the seeder uses elsewhere). Returns a relative storage path or
     * null when no image is on disk.
     */
    private function resolveKeywordCoverPath(string $slug): ?string
    {
        $page = DB::table('rg_seo_pages')->where('slug', $slug)->first(['og_image_path']);
        if ($page && !empty($page->og_image_path)) {
            $candidate = ltrim($page->og_image_path, '/');
            $candidate = preg_replace('#^storage/#', '', $candidate);
            if (is_file(storage_path('app/public/' . $candidate))) return $candidate;
        }
        return null;
    }

    /**
     * Apply a list of image overlay inserts to a content_html string.
     * Each overlay has anchor (substring to find — must match exactly once),
     * position (before|after), and html (the figure block to inject).
     * Silently skips overlays whose anchor doesn't match (lets agents err on
     * the side of best-effort without breaking the whole import).
     */
    private function applyImageOverlays(string $html, array $overlays): string
    {
        foreach ($overlays as $ov) {
            $anchor = $ov['anchor'] ?? null;
            $position = $ov['position'] ?? 'after';
            $injectHtml = $ov['html'] ?? '';
            if (!$anchor || $injectHtml === '') continue;
            $pos = strpos($html, $anchor);
            if ($pos === false) continue;
            if ($position === 'before') {
                $html = substr_replace($html, $injectHtml, $pos, 0);
            } else { // after
                $end = $pos + strlen($anchor);
                $html = substr_replace($html, $injectHtml, $end, 0);
            }
        }
        return $html;
    }

    /**
     * Derive a one-line subtitle from the post: prefer the excerpt's first
     * sentence (trimmed to ~180 chars); fall back to a destination-flavored
     * tagline when the excerpt is missing.
     */
    private function deriveSubtitle(array $p, ?array $dest): string
    {
        $excerpt = trim($p['excerpt'] ?? '');
        if ($excerpt !== '') {
            // First sentence, capped to 180 chars
            if (preg_match('/^(.+?[.!?])(?:\s|$)/u', $excerpt, $m)) {
                $first = trim($m[1]);
                return mb_strlen($first) > 180 ? mb_substr($first, 0, 177) . '…' : $first;
            }
            return mb_strlen($excerpt) > 180 ? mb_substr($excerpt, 0, 177) . '…' : $excerpt;
        }
        $location = $dest['name'] ?? 'the Philippines';
        return "A working traveler's notes on planning a real trip to {$location}, with timing, transit, and the small stuff that matters.";
    }

    /**
     * Derive a 3-4 bullet TLDR list (renders as `* bullet` markdown lines).
     * Pulls from the post's content_html where possible (first sentence of
     * first 3 H2 sections) and falls back to destination data.
     */
    private function deriveTldr(array $p, ?array $dest): string
    {
        $bullets = [];
        if (preg_match_all('#<h2[^>]*>(.+?)</h2>\s*<p[^>]*>(.+?)</p>#is', $p['content_html'] ?? '', $matches, PREG_SET_ORDER)) {
            foreach (array_slice($matches, 0, 3) as $m) {
                $heading = trim(strip_tags($m[1]));
                $body = trim(strip_tags($m[2]));
                if (preg_match('/^(.+?[.!?])(?:\s|$)/u', $body, $first)) {
                    $line = $heading . ': ' . trim($first[1]);
                    $bullets[] = mb_strlen($line) > 180 ? mb_substr($line, 0, 177) . '…' : $line;
                }
            }
        }
        if (empty($bullets) && $dest) {
            $location = $dest['name'] ?? 'the destination';
            $bullets[] = "Best for: a relaxed weekend in {$location} with a mix of food, scenery, and one heritage stop.";
            if (!empty($dest['transit'])) $bullets[] = "Getting there: " . trim(explode('.', $dest['transit'])[0]) . ".";
            if (!empty($dest['tip'])) $bullets[] = "Local rule: " . trim(explode('.', $dest['tip'])[0]) . ".";
        }
        return implode("\n", array_map(fn($b) => '* ' . $b, $bullets));
    }

    /**
     * Build the WWWW (Why/When/Where/Whom) summary array from destination data
     * + post tags. Returns an associative array; the partial renders only
     * non-empty entries, so partial coverage is fine.
     */
    private function deriveWwww(array $p, ?array $dest): array
    {
        $location = $dest['name'] ?? 'the area';
        $tags = strtolower(($p['tags'] ?? '') . ' ' . ($p['title'] ?? ''));

        $why = $dest['voice_intro'] ?? "{$location} delivers a high-value weekend without the long haul, with a mix of food, scenery, and small heritage stops you can string together at a relaxed pace.";
        $why = preg_replace('/\s+/', ' ', trim($why));
        if (mb_strlen($why) > 260) $why = mb_substr($why, 0, 257) . '…';

        $when = !empty($dest['season']) ? trim($dest['season']) : "Best between November and February when the weather is dry and crowds thin out. Avoid Holy Week if you want quiet.";
        if (mb_strlen($when) > 260) $when = mb_substr($when, 0, 257) . '…';

        $whereParts = [];
        if (!empty($dest['spots'])) {
            $names = array_slice(array_column($dest['spots'], 'name'), 0, 3);
            $whereParts[] = "Anchor your visit around " . implode(', ', $names) . ".";
        }
        if (!empty($dest['food'][0])) {
            $whereParts[] = "Plan a meal stop for " . trim(explode(' at ', $dest['food'][0])[0]) . ".";
        }
        $where = $whereParts ? implode(' ', $whereParts) : "Most travelers base in the town center for the easiest access to food stops, jeepney routes, and the main heritage sites.";
        if (mb_strlen($where) > 260) $where = mb_substr($where, 0, 257) . '…';

        $isFamily = preg_match('/family|kids|children/', $tags);
        $isCouple = preg_match('/couple|honeymoon|romantic/', $tags);
        $isBarkada = preg_match('/barkada|friends|group|party/', $tags);
        $isSolo = preg_match('/solo|solo travel|alone/', $tags);
        if ($isFamily) {
            $whom = "Families with kids, especially school-age children who can handle a half-day of walking and an early lunch. Bring water shoes if any spot involves wading.";
        } elseif ($isCouple) {
            $whom = "Couples planning a quiet long weekend. Time the trip mid-week if possible — the spots that matter open earlier than the crowds arrive.";
        } elseif ($isBarkada) {
            $whom = "A barkada of three to six. Big enough to split a tricycle charter, small enough that food stops stay easy. Add buffer time for group decisions.";
        } elseif ($isSolo) {
            $whom = "Solo travelers comfortable with a single backpack, ride-hail or bus connections, and asking locals for directions. Pack light, stay flexible.";
        } else {
            $whom = "Works for a wide mix: couples, small barkada groups, or families with school-age kids. Adjust the pace based on who you're traveling with.";
        }

        return [
            'why'   => $why,
            'when'  => $when,
            'where' => $where,
            'whom'  => $whom,
        ];
    }

    private function resolveCoverPath(?string $destKey, ?array $destinations = null): ?string
    {
        if (!$destKey) return null;
        $base = storage_path('app/public/');
        // Prefer destinations/{key}-{1..3}.jpg in order
        for ($i = 1; $i <= 3; $i++) {
            $candidate = 'rg-media/destinations/' . $destKey . '-' . $i . '.jpg';
            if (is_file($base . $candidate) && filesize($base . $candidate) > 5000) {
                return $candidate;
            }
        }
        // Fall back to the first available spot image for this destination
        $matches = glob($base . 'rg-media/spots/' . $destKey . '-*.jpg');
        foreach ($matches as $abs) {
            if (filesize($abs) > 5000) {
                return 'rg-media/spots/' . basename($abs);
            }
        }
        // Final fallback: cluster landmark
        $dest = $destinations[$destKey] ?? null;
        if ($dest && !empty($dest['cluster'])) {
            $candidate = 'rg-media/landmarks/' . $dest['cluster'] . '.jpg';
            if (is_file($base . $candidate)) return $candidate;
        }
        return null;
    }

    private function getDefaultAuthorId(): ?int
    {
        return DB::table('users')->where('email', 'like', '%admin%')->orWhere('id', 1)->value('id');
    }

    /**
     * Generate 4-6 plausible positive comments per post. Reads naturally,
     * varied across openers and locations. Idempotent: skips if seeded
     * comments already exist for this post.
     */
    private function seedComments(int $postId, string $slug, string $title): void
    {
        $existing = DB::table('rg_blog_comments')->where('blog_post_id', $postId)->where('is_seeded', 1)->count();
        if ($existing > 0) return;

        $commenters = [
            'Mark Anthony Lim', 'Sheryl Magno', 'Patricia delos Santos', 'Renzo Aquino',
            'Aileen Bautista', 'Carlo Mendoza', 'Jessa Ramirez', 'Daniel Pascual',
            'Hannah Reyes', 'Joan Villaruel', 'Bryan Tan', 'Carmela Yulo',
            'Aldous Cabrera', 'Ynna Domingo', 'Edwin Castillo', 'Mara Hernandez',
            'Kim Esguerra', 'Liza Rivera', 'Jonathan Cruz', 'Rina Sandoval',
        ];
        $locations = [
            'Quezon City', 'Makati', 'Pasig', 'Cebu City', 'Davao City',
            'Iloilo', 'Marikina', 'Antipolo', 'Mandaluyong', 'Caloocan',
        ];
        $openers = [
            "Thank you for this guide, used it last weekend and it held up.",
            "Saved this for our trip next month. The honest tips are appreciated.",
            "This is exactly the kind of writeup we needed before booking.",
            "Did this route a few months ago and it tracks with your experience.",
            "Bookmarking for our upcoming family trip. Sulit ang research.",
            "Great breakdown, especially the part about timing the day.",
            "Read this before our trip and it saved us from the usual mistakes.",
            "Honestly the most useful guide I found before going.",
            "Lovely write-up, the small details made the difference.",
        ];
        $bodies = [
            "We followed the morning loop and were already settled by lunch.",
            "The food stops were the highlight, none of them were overpriced.",
            "The transit tip alone saved us a lot of time on the road.",
            "Sending this to my barkada, they have been planning the same trip.",
            "The image you painted of the place matches what we found there.",
            "Spent a couple of nights doing exactly what you described, no regrets.",
            "We had a similar experience, especially the part about going early.",
            "The recommendations were spot on, particularly the food picks.",
            "Will be back to read your other posts before our next trip.",
        ];

        $seed = abs(crc32($slug));
        $count = 4 + ($seed % 3);  // 4, 5, or 6 comments
        $now = now();

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $commenter = $commenters[($seed + $i) % count($commenters)];
            $location = $locations[($seed + $i + 2) % count($locations)];
            $opener = $openers[($seed + $i) % count($openers)];
            $body = $bodies[($seed + $i + 3) % count($bodies)];

            // Rating distribution skewed toward 4-5 stars (organic comments
            // from people who enjoyed the trip enough to leave a comment).
            // Weights: 5★ x 6, 4★ x 3, 3★ x 1 → ~60/30/10
            $ratingWeights = [5, 5, 5, 5, 5, 5, 4, 4, 4, 3];
            $rating = $ratingWeights[($seed + $i * 11) % count($ratingWeights)];

            $rows[] = [
                'blog_post_id' => $postId,
                'commenter_name' => $commenter,
                'commenter_email' => null,
                'commenter_avatar' => 'https://i.pravatar.cc/200?img=' . (1 + (($seed + $i * 7) % 70)),
                'comment_text' => $opener . ' ' . $body,
                'rating' => $rating,
                'parent_id' => null,
                'status' => 'approved',
                'is_seeded' => true,
                'created_at' => $now->copy()->subDays(($seed + $i * 5) % 90)->subHours($i),
                'updated_at' => $now,
            ];
        }
        DB::table('rg_blog_comments')->insert($rows);
    }
}
