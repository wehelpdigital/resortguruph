<?php
// Builds an inventory of every (image file -> intended subject) pair used on the public site.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$out = $argv[1];
$norm = function ($src) {
    $src = trim((string) $src);
    if ($src === '') return null;
    $src = preg_replace('~^https?://(resortguruph\.test|127\.0\.0\.1:800\d|localhost:800\d)~', '', $src);
    if (preg_match('~^https?://~', $src)) return null;            // external, handled elsewhere
    $src = '/' . ltrim($src, '/');
    if (!str_starts_with($src, '/storage/')) $src = '/storage/' . ltrim($src, '/');
    return $src;
};
$items = [];   // src => [labels=>[], pages=>[], kinds=>[]]
$add = function ($src, $label, $page, $kind) use (&$items, $norm) {
    $s = $norm($src); if (!$s) return;
    $label = trim(html_entity_decode(strip_tags((string) $label)));
    $items[$s]['labels'][$label] = ($items[$s]['labels'][$label] ?? 0) + 1;
    $items[$s]['pages'][$page] = 1;
    $items[$s]['kinds'][$kind] = 1;
};
$pageTitles = DB::table('rg_seo_pages')->pluck('title', 'id')->all();
$staticTitles = DB::table('rg_static_pages')->pluck('slug', 'id')->all();
foreach (DB::table('rg_content_blocks')->select('id', 'owner_type', 'owner_id', 'block_type', 'payload_json')->orderBy('id')->cursor() as $b) {
    $p = json_decode($b->payload_json, true); if (!is_array($p)) continue;
    $page = $b->owner_type === 'seo_page' ? ($pageTitles[$b->owner_id] ?? "seo#$b->owner_id") : ($b->owner_type === 'static_page' ? 'static:' . ($staticTitles[$b->owner_id] ?? $b->owner_id) : "$b->owner_type#$b->owner_id");
    switch ($b->block_type) {
        case 'hero_slider':
            foreach (($p['images'] ?? []) as $im) if (is_array($im)) $add($im['src'] ?? '', ($im['caption_title'] ?? '') ?: ($im['alt'] ?? $page), $page, 'hero_slider');
            break;
        case 'image':
            $add($p['src'] ?? '', ($p['alt'] ?? '') ?: $page, $page, 'image');
            break;
        default:
            // generic walk: any array with an image-ish key + a name-ish key
            $walk = function ($node) use (&$walk, $add, $page, $b) {
                if (!is_array($node)) return;
                $img = null;
                foreach (['image', 'image_src', 'img', 'src', 'cover', 'photo', 'background_image'] as $k) if (isset($node[$k]) && is_string($node[$k]) && preg_match('~\.(jpe?g|png|webp|gif|avif)(\?|$)~i', $node[$k])) { $img = $node[$k]; break; }
                if ($img) {
                    $label = '';
                    foreach (['name', 'title', 'caption_title', 'heading', 'alt', 'image_alt', 'label'] as $k) if (!empty($node[$k]) && is_string($node[$k])) { $label = $node[$k]; break; }
                    $add($img, $label ?: $page, $page, $b->block_type);
                }
                foreach ($node as $v) if (is_array($v)) $walk($v);
            };
            $walk($p);
    }
}
foreach (DB::table('rg_tourist_spots as s')->join('rg_media as m', 'm.id', '=', 's.media_id')->select('s.name', 's.location', 'm.path')->get() as $r) $add($r->path, "$r->name ($r->location)", 'spots', 'tourist_spot');
foreach (DB::table('rg_fiestas')->select('name', 'province', 'cover_image_path')->get() as $r) $add($r->cover_image_path, "$r->name festival ($r->province)", 'fiestas', 'fiesta');
foreach (DB::table('rg_blog_posts')->where('status', 'published')->select('title', 'cover_path')->get() as $r) $add($r->cover_path, "Blog: $r->title", 'blog', 'blog_cover');
$rows = [];
foreach ($items as $src => $d) {
    $file = storage_path('app/public/' . substr($src, strlen('/storage/')));
    arsort($d['labels']);
    $rows[] = ['src' => $src, 'exists' => is_file($file), 'labels' => array_keys($d['labels']), 'n_labels' => count($d['labels']), 'n_pages' => count($d['pages']), 'kinds' => array_keys($d['kinds'])];
}
usort($rows, fn ($a, $b) => $b['n_pages'] <=> $a['n_pages']);
file_put_contents($out, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$missing = count(array_filter($rows, fn ($r) => !$r['exists']));
echo "unique images: ", count($rows), "  missing files: $missing\n";
echo "used under >3 different subjects: ", count(array_filter($rows, fn ($r) => $r['n_labels'] > 3)), "\n";
foreach (array_slice($rows, 0, 15) as $r) printf("%4d pages %3d subjects  %s  e.g. %s\n", $r['n_pages'], $r['n_labels'], $r['src'], implode(' | ', array_slice($r['labels'], 0, 3)));
