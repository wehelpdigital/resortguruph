<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Searches Wikimedia Commons for representative photos of each seeded
 * restaurant (by cuisine) and adventure (by activity type), downloads
 * the best non-junk result, and wires it up as the hero image.
 *
 * Files land in:
 *   storage/app/public/rg-media/restaurants/{slug}.jpg
 *   storage/app/public/rg-media/adventures/{slug}.jpg
 *
 * Idempotent: skips rows whose hero_path is already populated and whose
 * file exists on disk.
 */
class RestaurantAdventureImagesSeeder extends Seeder
{
    private array $headers = [
        'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
        'Accept' => 'application/json, image/jpeg, image/*',
    ];

    /** Cuisine-to-Wikimedia-search-query for restaurants. */
    private array $cuisineQueries = [
        'Modern Filipino'    => ['Filipino cuisine sinigang', 'Filipino food adobo'],
        'Japanese Ramen'     => ['Tonkotsu ramen', 'Ramen noodles bowl'],
        'Cafe / Bakery'      => ['Bakery croissant', 'Cafe pastry interior'],
        'Comfort Filipino'   => ['Kare-kare', 'Sinigang na baboy'],
        'All-day Cafe'       => ['Cafe interior breakfast', 'Coffee shop pastry'],
        'Fine Dining'        => ['Plated tasting menu', 'Fine dining plate'],
        'Cordillera Cuisine' => ['Pinikpikan', 'Philippine highland food'],
        'Filipino + Spanish' => ['Paella valenciana', 'Spanish tapas'],
        'Cebu Lechon'        => ['Lechon de leche', 'Cebu lechon belly'],
        'Mediterranean'      => ['Hummus mezze', 'Mediterranean platter'],
        'Specialty Coffee'   => ['Latte art espresso', 'Pour over coffee'],
        'Filipino BBQ'       => ['Chicken inasal', 'Filipino BBQ skewer'],
    ];

    /** Activity-type-to-Wikimedia-search-query for adventures. */
    private array $activityQueries = [
        'Surfing'        => ['Surfing Philippines wave', 'Surfboard ocean wave'],
        'ATV'            => ['ATV all terrain vehicle trail', 'Quad bike forest trail'],
        'Diving'         => ['Scuba diving coral reef', 'Diver underwater Philippines'],
        'Zipline'        => ['Zipline canopy tour', 'Zip line forest'],
        'Paintball'      => ['Paintball game player', 'Paintball field'],
        'Island hopping' => ['Coron Palawan island', 'Philippines island banca'],
        'Lake raft'      => ['Bamboo raft Lake Pandin', 'Lake bamboo raft Philippines'],
        'Trekking'       => ['Mount Pinatubo crater', 'Hiking trail Philippines'],
    ];

    public function run(): void
    {
        $this->command->info('Fetching restaurant + adventure images from Wikimedia Commons...');

        $this->ensureDir(storage_path('app/public/rg-media/restaurants'));
        $this->ensureDir(storage_path('app/public/rg-media/adventures'));

        $rOk = $rFail = $rSkip = 0;
        foreach (DB::table('rg_restaurants')->get() as $row) {
            $localPath = 'rg-media/restaurants/' . $row->slug . '.jpg';
            $absPath = storage_path('app/public/' . $localPath);

            if (is_file($absPath) && filesize($absPath) > 5000 && $row->hero_path) {
                $rSkip++; continue;
            }

            $queries = $this->cuisineQueries[$row->cuisine] ?? ['Filipino food'];
            $saved = false;
            foreach ($queries as $q) {
                $files = $this->searchCommons($q, 8);
                foreach ($files as $f) {
                    if ($this->downloadAndAttach($f, $absPath, $localPath, $row, 'restaurant')) {
                        $saved = true; break 2;
                    }
                }
            }

            if ($saved) {
                $rOk++;
                $this->command->info(sprintf('  ok  restaurant | %s', $row->name));
            } else {
                $rFail++;
                $this->command->warn(sprintf('  !!  restaurant | %s | no usable result', $row->name));
            }
        }

        $aOk = $aFail = $aSkip = 0;
        foreach (DB::table('rg_adventures')->get() as $row) {
            $localPath = 'rg-media/adventures/' . $row->slug . '.jpg';
            $absPath = storage_path('app/public/' . $localPath);

            if (is_file($absPath) && filesize($absPath) > 5000 && $row->hero_path) {
                $aSkip++; continue;
            }

            $queries = $this->activityQueries[$row->activity_type] ?? ['Philippines outdoor'];
            $saved = false;
            foreach ($queries as $q) {
                $files = $this->searchCommons($q, 8);
                foreach ($files as $f) {
                    if ($this->downloadAndAttach($f, $absPath, $localPath, $row, 'adventure')) {
                        $saved = true; break 2;
                    }
                }
            }

            if ($saved) {
                $aOk++;
                $this->command->info(sprintf('  ok  adventure  | %s', $row->name));
            } else {
                $aFail++;
                $this->command->warn(sprintf('  !!  adventure  | %s | no usable result', $row->name));
            }
        }

        $this->command->info('');
        $this->command->info("Restaurants — ok: $rOk | skipped: $rSkip | failed: $rFail");
        $this->command->info("Adventures  — ok: $aOk | skipped: $aSkip | failed: $aFail");
    }

    private function searchCommons(string $query, int $limit): array
    {
        try {
            $resp = Http::withHeaders($this->headers)
                ->timeout(45)
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action' => 'query',
                    'format' => 'json',
                    'generator' => 'search',
                    'gsrnamespace' => 6,
                    'gsrlimit' => $limit,
                    'gsrsearch' => $query,
                    'prop' => 'imageinfo',
                    'iiprop' => 'url|mime|size',
                ]);
            if (!$resp->successful()) return [];
            $pages = $resp->json()['query']['pages'] ?? [];
            $files = [];
            foreach ($pages as $page) {
                $title = $page['title'] ?? '';
                $info  = $page['imageinfo'][0] ?? null;
                if (!$info) continue;
                $mime = $info['mime'] ?? '';
                if (!str_starts_with($mime, 'image/')) continue;
                if (str_starts_with($mime, 'image/svg')) continue;
                $w = $info['width'] ?? 0;
                $h = $info['height'] ?? 0;
                if ($w < 600 || $h < 400) continue;
                if (str_starts_with($title, 'File:')) {
                    $files[] = substr($title, 5);
                }
            }
            return $files;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function downloadAndAttach(string $wikiFile, string $absPath, string $localPath, $row, string $kind): bool
    {
        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($wikiFile) . '?width=1200';
        try {
            $resp = Http::withHeaders($this->headers)
                ->timeout(60)
                ->withOptions(['allow_redirects' => true])
                ->get($url);
            if (!$resp->successful()) return false;
            $body = $resp->body();
            if (strlen($body) < 5000) return false;
            file_put_contents($absPath, $body);

            // Insert rg_media row + wire to the restaurant/adventure
            $width = $height = null;
            try {
                $sz = @getimagesize($absPath);
                if ($sz) { $width = $sz[0]; $height = $sz[1]; }
            } catch (\Throwable $e) {}

            $mediaId = DB::table('rg_media')->updateOrInsert(
                ['path' => $localPath],
                [
                    'filename'   => $wikiFile,
                    'path'       => $localPath,
                    'mime'       => 'image/jpeg',
                    'size_bytes' => filesize($absPath),
                    'kind'       => 'image',
                    'width'      => $width,
                    'height'     => $height,
                    'alt'        => $row->name,
                    'caption'    => $row->name,
                    'source'     => 'seeder-' . $kind . 's',
                    'credit'     => 'Photo: Wikimedia Commons (CC-BY-SA)',
                    'source_url' => $url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($kind === 'restaurant') {
                DB::table('rg_restaurants')->where('id', $row->id)->update([
                    'hero_path'  => $localPath,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('rg_adventures')->where('id', $row->id)->update([
                    'hero_path'  => $localPath,
                    'updated_at' => now(),
                ]);
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function ensureDir(string $path): void
    {
        if (!is_dir($path)) mkdir($path, 0755, true);
    }
}
