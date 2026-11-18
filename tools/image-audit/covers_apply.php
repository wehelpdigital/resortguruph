<?php
// Apply covers-plan.json except the entries the visual review marked bad.
//   php covers_apply.php <coversdir> [extra,bad,ids]
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$dir = rtrim($argv[1], '/\\');
$plan = json_decode(file_get_contents("$dir/covers-plan.json"), true);
$bad = array_map(fn ($b) => (int) $b['id'], json_decode(file_get_contents("$dir/review.json"), true)['bad'] ?? []);
$bad = array_merge($bad, array_map('intval', array_filter(explode(',', $argv[2] ?? ''))));
$backup = [];
DB::transaction(function () use ($plan, $bad, &$backup) {
    foreach ($plan as $r) {
        if (!$r['new'] || in_array((int) $r['id'], $bad, true)) continue;
        $backup[$r['id']] = $r['old'];
        DB::table('rg_blog_posts')->where('id', $r['id'])->update(['cover_path' => $r['new'], 'updated_at' => now()]);
    }
});
file_put_contents("$dir/covers-backup-" . date('His') . '.json', json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo 'updated ', count($backup), ' covers; skipped ', count(array_unique($bad)), " flagged\n";
