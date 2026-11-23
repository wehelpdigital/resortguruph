<?php
/**
 * Blog "at a glance" tiles (rg_blog_posts.wwww_json) were filled from the nearest guide's
 * destination, so a Baguio post showed La Union's surf tiles and a Siargao post Samal's.
 * Removes the tiles when the place they describe ("La Union is ...") is not in the post's
 * title or slug. The post's own short version (tldr) stays.
 *
 *   php tools/content-audit/blog_tiles.php            dry run
 *   php tools/content-audit/blog_tiles.php --apply    write (old values saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$backup = [];
foreach (DB::table('rg_blog_posts')->whereNotNull('wwww_json')->get(['id', 'slug', 'title', 'wwww_json']) as $p) {
    $w = json_decode((string) $p->wwww_json, true);
    if (!$w) continue;
    $why = trim((string) ($w['why'] ?? ''));
    if (!preg_match('~^([A-Z][\w\'-]+(?:\s+(?:[A-Z][\w\'-]+|de|del|City))*)\s+(?:is|has|sits|was|makes|offers)\b~u', $why, $m)) continue;
    $hay = strtolower($p->slug . ' ' . $p->title);
    $toks = array_filter(preg_split('~[^a-z]+~', strtolower($m[1])), fn ($t) => strlen($t) > 3 && !in_array($t, ['city', 'island', 'islands', 'province', 'freeport', 'zone', 'bay'], true));
    if (!$toks) continue;
    foreach ($toks as $t) if (str_contains($hay, $t)) continue 2;
    echo "{$p->slug}  (tiles about {$m[1]})\n";
    $backup[] = ['id' => $p->id, 'wwww_json' => $p->wwww_json];
    if ($apply) DB::table('rg_blog_posts')->where('id', $p->id)->update(['wwww_json' => null]);
}
echo count($backup) . " posts lose tiles about another place\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/blog-tiles-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo "backup: $f\n";
}
