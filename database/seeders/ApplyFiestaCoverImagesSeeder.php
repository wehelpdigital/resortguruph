<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Walks public/storage/rg-media/fiestas/ and links any image matching
 * a fiesta slug to rg_fiestas.cover_image_path. Idempotent: re-runs
 * pick up newly-downloaded images and update the matching row.
 *
 * Accepts .jpg / .jpeg / .png / .webp. Path written into the DB uses
 * the storage-relative form ("rg-media/fiestas/sinulog.jpg") so the
 * RgFiesta::coverUrl() helper can prepend "/storage/" cleanly.
 */
class ApplyFiestaCoverImagesSeeder extends Seeder
{
    public function run(): void
    {
        $dir = public_path('storage/rg-media/fiestas');
        if (!is_dir($dir)) {
            $this->command->warn("Missing directory: {$dir}");
            return;
        }

        $updates = 0;
        $unmatched = [];
        foreach (scandir($dir) as $file) {
            if (in_array($file, ['.', '..'], true)) continue;
            $info = pathinfo($file);
            $ext = strtolower($info['extension'] ?? '');
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) continue;

            $slug = $info['filename'];
            $relPath = 'rg-media/fiestas/' . $file;

            $affected = DB::table('rg_fiestas')
                ->where('slug', $slug)
                ->update(['cover_image_path' => $relPath, 'updated_at' => now()]);
            if ($affected) {
                $updates++;
            } else {
                $unmatched[] = $file;
            }
        }

        $this->command->info("Fiestas linked with cover image: {$updates}");
        if ($unmatched) {
            $this->command->warn('Images with no matching fiesta slug: ' . count($unmatched));
            foreach (array_slice($unmatched, 0, 10) as $f) {
                $this->command->line('  ' . $f);
            }
        }

        // Clear the cached fiesta-list-by-region so the cover images
        // show up on the next list-page hit without an artisan cache:clear.
        \Illuminate\Support\Facades\Cache::forget('fiesta-list-by-region');
    }
}
