<?php
// Give blog posts that share a cover photo (or have none) their own, better
// matching cover from the local media library. Usage:
//   php blog_covers.php <outdir> [--apply]
// Dry run writes <outdir>/covers-plan.json and contact sheets; --apply updates
// rg_blog_posts.cover_path and writes a rollback file.
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$out = rtrim($argv[1], '/\\');
$apply = in_array('--apply', $argv, true);
$root = storage_path('app/public/');

// ---- library
$folders = ['destinations', 'spots', 'places', 'landmarks', 'fiestas', 'foods', 'food-locations', 'blog-covers', 'adventures', 'buys', 'activities', 'attractions', 'cultures', 'transport', 'restaurants'];
$files = [];
foreach ($folders as $f) {
    foreach (glob($root . 'rg-media/' . $f . '/*.{jpg,jpeg,webp,png}', GLOB_BRACE) ?: [] as $path) {
        $base = strtolower(pathinfo($path, PATHINFO_FILENAME));
        if (filesize($path) < 25000 || preg_match('/generic|placeholder|logo|\bmap\b|-map-|sign/', $base)) continue;
        $rel = 'rg-media/' . $f . '/' . basename($path);
        $toks = array_values(array_unique(array_filter(preg_split('/[^a-z]+/', $base), fn ($t) => strlen($t) >= 3)));
        $files[$rel] = ['toks' => $toks, 'folder' => $f];
    }
}
$df = [];
foreach ($files as $f) foreach ($f['toks'] as $t) $df[$t] = ($df[$t] ?? 0) + 1;
$N = count($files);
$idf = fn ($t) => log(($N + 1) / (($df[$t] ?? 0) + 1));

$stop = array_flip(explode(' ', 'the and for from with your into over that this what when where which who why how are was were its our you not but out off via per vs plan plans day days calm diy read guide guides weekend weekends trip trips first timers timer honest one two three four five six seven eight nine ten week weeks month months philippines philippine best kid kids friendly family families couple couples honeymoon babymoon senior seniors digital nomad nomads slow quiet quieter list beyond standard route routes deep dive half multi most travelers traveler skip actually tested angle cool air pace controlled reunion multigenerational pregnancy new old top real full loop short long stop stops side north south east west central upper lower read alternative edition calmer workstation transport terminal airport ferry bus van'));
$tokensOf = function (string $s) use ($stop) {
    $s = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s);
    return array_values(array_unique(array_filter(preg_split('/[^a-z]+/', $s), fn ($t) => strlen($t) >= 3 && !isset($stop[$t]))));
};

// ---- posts
$posts = DB::table('rg_blog_posts')->where('status', 'published')->orderBy('id')->get(['id', 'title', 'tags', 'cover_path']);
$useCount = [];
foreach ($posts as $p) if ($p->cover_path) $useCount[$p->cover_path] = ($useCount[$p->cover_path] ?? 0) + 1;
$score = function ($p, $rel) use ($files, $tokensOf, $idf) {
    $pt = $tokensOf($p->title . ' ' . str_replace(',', ' ', (string) $p->tags));
    $s = 0;
    foreach (array_intersect($pt, $files[$rel]['toks']) as $t) $s += $idf($t);
    $isFestival = (bool) preg_match('/festival|fiesta|sinulog|masskara|panagbenga|ati-atihan|kadayawan|pahiyas/i', $p->title);
    $isFood = (bool) preg_match('/food|eat|lechon|bulalo|mango|puso|sisig|adobo|halal|vegan|vegetarian|gluten|cafe|coffee|dish/i', $p->title);
    if ($files[$rel]['folder'] === 'fiestas') $s += $isFestival ? 1.2 : -2;
    if ($files[$rel]['folder'] === 'foods') $s += $isFood ? 0.6 : -1.5;
    if (in_array($files[$rel]['folder'], ['food-locations', 'restaurants'], true)) $s -= $isFood ? 0 : 1;
    return $s;
};

// keep one post per shared cover: the one that matches it best
$keep = [];
$groups = [];
foreach ($posts as $p) if ($p->cover_path && isset($files[$p->cover_path])) $groups[$p->cover_path][] = $p;
foreach ($groups as $rel => $ps) {
    usort($ps, fn ($a, $b) => $score($b, $rel) <=> $score($a, $rel));
    $keep[$ps[0]->id] = true;
}
$taken = [];
foreach ($posts as $p) if (isset($keep[$p->id])) $taken[$p->cover_path] = true;
// covers used anywhere else in content are fine to reuse, but other posts' covers are not
$plan = [];
foreach ($posts as $p) {
    $needs = !$p->cover_path || !isset($files[$p->cover_path]) || !isset($keep[$p->id]);
    if (!$needs) continue;
    $best = null; $bestS = 0.0;
    foreach ($files as $rel => $f) {
        if (isset($taken[$rel])) continue;
        $s = $score($p, $rel);
        if ($s > $bestS + 1e-9) { $best = $rel; $bestS = $s; }
    }
    if ($best === null || $bestS < 1.0) { $plan[] = ['id' => $p->id, 'title' => $p->title, 'old' => $p->cover_path, 'new' => null, 'score' => round($bestS, 2)]; continue; }
    $taken[$best] = true;
    $plan[] = ['id' => $p->id, 'title' => $p->title, 'old' => $p->cover_path, 'new' => $best, 'score' => round($bestS, 2)];
}
file_put_contents("$out/covers-plan.json", json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$assigned = array_values(array_filter($plan, fn ($r) => $r['new']));
echo 'posts: ', count($posts), ' | to change: ', count($plan), ' | matched: ', count($assigned), ' | no good match: ', count($plan) - count($assigned), "\n";

// contact sheets: 5 x 6 per sheet, title under each
$W = 260; $H = 170; $T = 34; $per = 30;
foreach (array_chunk($assigned, $per) as $si => $chunk) {
    $im = imagecreatetruecolor($W * 5, ($H + $T) * 6);
    $white = imagecolorallocate($im, 255, 255, 255); imagefill($im, 0, 0, $white); $black = imagecolorallocate($im, 0, 0, 0);
    foreach ($chunk as $i => $r) {
        $x = ($i % 5) * $W; $y = intdiv($i, 5) * ($H + $T);
        $src = @imagecreatefromstring(@file_get_contents($root . $r['new']));
        if ($src) imagecopyresampled($im, $src, $x, $y, 0, 0, $W - 4, $H, imagesx($src), imagesy($src));
        imagestring($im, 2, $x + 2, $y + $H + 2, '#' . $r['id'] . ' ' . substr($r['title'], 0, 36), $black);
        imagestring($im, 2, $x + 2, $y + $H + 16, substr($r['title'], 36, 40), $black);
    }
    imagejpeg($im, sprintf('%s/covers-sheet-%02d.jpg', $out, $si + 1), 72);
}

if ($apply) {
    $backup = [];
    DB::transaction(function () use ($assigned, &$backup) {
        foreach ($assigned as $r) {
            $backup[$r['id']] = $r['old'];
            DB::table('rg_blog_posts')->where('id', $r['id'])->update(['cover_path' => $r['new'], 'updated_at' => now()]);
        }
    });
    file_put_contents("$out/covers-backup-" . date('His') . '.json', json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo 'applied ', count($backup), "\n";
}
