<?php
/**
 * Blog posts that share one cover photo (seven posts with cebu-city-1.jpg) look like the
 * same card twice in the blog list and in "More reads". Keeps the cover on the oldest post
 * and gives the others their own: the first photo in the post body that no other post uses
 * as its cover, else another photo of the same town (cebu-city-2.jpg, -3.jpg).
 *
 *   php tools/content-audit/blog_covers.php            dry run
 *   php tools/content-audit/blog_covers.php --apply    write (old values saved to storage/app/content-backups)
 */
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\GuideHtml;
use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$posts = DB::table('rg_blog_posts')->where('status', 'published')->whereNotNull('cover_path')->orderBy('published_at')->orderBy('id')->get(['id', 'slug', 'cover_path', 'content_html']);
$rel = fn (string $src) => ltrim((string) preg_replace('~^(https?://[^/]+)?/storage/~i', '', strtok(trim($src), '?')), '/');
$taken = [];
foreach ($posts as $p) $taken[GuideHtml::photoId('/storage/' . $rel($p->cover_path))][] = $p->id;

$generic = array_flip(['where', 'what', 'things', 'spots', 'tourist', 'town', 'center', 'trip', 'weekend', 'slow', 'calm', 'read', 'quiet', 'guide', 'first', 'timers', 'honest', 'beach', 'island', 'islands', 'city', 'province', 'route', 'plan', 'three', 'two', 'days', 'from', 'manila', 'with', 'side', 'south', 'north', 'east', 'west', 'coast', 'philippines', 'travel', 'traveler', 'travelers', 'family', 'kid', 'friendly', 'morning', 'overnight', 'hike', 'climb', 'trail', 'walk', 'loop', 'stop', 'stops', 'base', 'week', 'month', 'beyond', 'standard', 'list', 'picks', 'under', 'diy', 'tested', 'day', 'river', 'falls', 'lake', 'mount', 'festival', 'deep', 'dive', 'heritage', 'food', 'beginner', 'beginners', 'short', 'easy', 'connecting', 'flight', 'onward', 'ferry', 'schedule', 'crossing', 'practical', 'cultural', 'community', 'visit', 'surf', 'swim', 'snorkel', 'camping', 'sunrise', 'sunset', 'view', 'cruise', 'scooter', 'couple', 'honeymoon', 'babymoon', 'pregnancy', 'digital', 'nomad', 'ethical', 'interaction', 'memorial', 'peak', 'forest', 'pools', 'mossy', 'northern', 'range', 'combo', 'adventure', 'park', 'church']);
$province = array_flip(['quezon', 'batangas', 'cebu', 'bataan', 'pangasinan', 'pampanga', 'laguna', 'cavite', 'rizal', 'bulacan', 'palawan', 'davao', 'negros', 'oriental', 'occidental', 'albay', 'sorsogon', 'ilocos', 'benguet', 'camarines', 'norte', 'iloilo', 'bohol', 'leyte', 'samar', 'zambales', 'tarlac', 'mindoro', 'aurora', 'isabela', 'cagayan', 'union', 'luzon', 'visayas', 'mindanao', 'bicol', 'metro']);
// matched by name but of another place (Padre Burgos museum in Vigan, Davao City for Mati)
$keepShared = ['mati-dahican-surf-weekend-davao-oriental', 'padre-burgos-quezon-borawan-dampalitan-beach-combo'];
$library = [];
foreach (['places', 'spots', 'commons', 'blog-photos', 'attractions', 'fiestas', 'foods', 'adventures'] as $dir) {
    foreach (glob(storage_path('app/public/rg-media/' . $dir . '/*.{jpg,jpeg,png,webp}'), GLOB_BRACE) ?: [] as $f) $library[] = 'rg-media/' . $dir . '/' . basename($f);
}
$registry = json_decode((string) file_get_contents(base_path('database/data/subject_photos.json')), true) ?: [];
$used = [];
$backup = [];
$changed = 0;
$left = [];
foreach ($posts as $p) {
    $id = GuideHtml::photoId('/storage/' . $rel($p->cover_path));
    if (!isset($used[$id])) { $used[$id] = true; continue; }
    if (in_array($p->slug, $keepShared, true)) { $left[] = $p->slug; continue; }
    // a checked photo of a place / festival named in the slug first ("apo-island-dauin-..." => Apo Island),
    // then the same town's other photos, then body photos named after something in the slug, then the other body photos
    $cands = [];
    $slugAll = array_flip(explode('-', $p->slug));
    $hits = [];
    foreach ($registry as $kind => $rows) {
        if ($kind === '_blocked' || !is_array($rows)) continue;
        foreach ($rows as $key => $src) {
            $toks = array_filter(explode(' ', explode('|', (string) $key)[0]), fn ($t) => strlen($t) > 2);
            if (count($toks) < 2 && !(count($toks) === 1 && strlen(reset($toks)) > 6)) continue;
            if ($toks && count(array_intersect_key(array_flip($toks), $slugAll)) === count($toks)) $hits[(string) $src] = max($hits[(string) $src] ?? 0, count($toks));
        }
    }
    arsort($hits);
    foreach (array_keys($hits) as $src) $cands[] = $rel($src);
    // words that name the place ("sumilon", "camotes"), not the trip ("day", "slow", "read")
    $placeToks = array_values(array_filter(explode('-', $p->slug), fn ($t) => strlen($t) > 3 && !isset($generic[$t])));
    // province names are shared by many places ("quezon", "bataan"): they do not count as a match on their own
    $slugToks = array_values(array_filter($placeToks, fn ($t) => !isset($province[$t])));
    $fileToks = fn (string $file) => array_unique(explode('-', preg_replace('~\.\w+$~', '', basename($file))));
    $score = fn (string $file) => count(array_intersect($fileToks($file), $slugToks));
    // library photos (not town stand-ins) named after the place
    $lib = [];
    foreach ($library as $file) { $sc = $score($file); if ($sc >= 2) $lib[$file] = $sc; } // two place words: one shared word ("quezon", "shrine") is not enough
    // most shared words first, then the file with the fewest other words (the closest name)
    uksort($lib, fn ($x, $y) => ($lib[$y] <=> $lib[$x]) ?: (count($fileToks($x)) <=> count($fileToks($y))));
    foreach (array_keys($lib) as $file) $cands[] = $file;
    // the same town's other photos, when the post is about that town
    if (preg_match('~^(rg-media/destinations/(.+?))-\d+\.jpg$~', $rel($p->cover_path), $tm) && array_intersect($fileToks($tm[2]), $placeToks)) foreach ([1, 2, 3, 4] as $n) $cands[] = $tm[1] . '-' . $n . '.jpg';
    // photos in the post body that name something in the slug
    if (preg_match_all('~<img[^>]+src="([^"]+)"~i', (string) $p->content_html, $m)) foreach ($m[1] as $src) if (str_contains($src, '/storage/') && $score($rel($src)) >= 2) $cands[] = $rel($src);
    $pick = '';
    foreach ($cands as $c) {
        $cid = GuideHtml::photoId('/storage/' . $c);
        if (isset($used[$cid]) || isset($taken[$cid]) || !GuideHtml::usable('/storage/' . $c)) continue;
        $pick = $c;
        break;
    }
    if ($pick === '') { $left[] = $p->slug; continue; }
    $used[GuideHtml::photoId('/storage/' . $pick)] = true;
    $backup[] = ['id' => $p->id, 'cover_path' => $p->cover_path];
    $changed++;
    echo "{$p->slug}: {$p->cover_path} -> {$pick}\n";
    if ($apply) DB::table('rg_blog_posts')->where('id', $p->id)->update(['cover_path' => $pick]);
}
echo "$changed covers changed, " . count($left) . " still shared: " . implode(' ', $left) . "\n";
if ($apply && $backup) {
    @mkdir(storage_path('app/content-backups'), 0775, true);
    $f = storage_path('app/content-backups/blog-covers-' . date('Ymd-His') . '.json');
    file_put_contents($f, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "backup: $f\n";
}
