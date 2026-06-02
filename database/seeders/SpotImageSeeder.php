<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Per-tourist-spot image seeder. Reads destinations.php, iterates every spot,
 * searches Wikimedia Commons for an image of that specific spot (e.g. "Pinto
 * Art Museum Antipolo"), downloads the best match, saves to
 * storage/app/public/rg-media/spots/{dest_key}-{spot_slug}.jpg, and registers
 * it in rg_media.
 *
 * The result is real per-spot photography for the upgraded card-grid layout,
 * with destination-level images used as fallback for spots Wikimedia does not
 * have. Idempotent: skips already-downloaded files.
 */
class SpotImageSeeder extends Seeder
{
    private string $localBase = 'rg-media/spots';
    private array $headers;

    public function __construct()
    {
        $this->headers = [
            'User-Agent' => 'ResortGuruPH/1.0 (https://resortguruph.test; admin@dummy.test)',
            'Accept' => 'application/json, image/jpeg, image/*',
        ];
    }

    public function run(): void
    {
        $this->ensureDir();
        $destinations = require database_path('data/destinations.php');

        $this->command->info('Fetching per-spot images from Wikimedia Commons...');
        $ok = 0; $fail = 0; $cached = 0;

        foreach ($destinations as $destKey => $info) {
            if ($destKey === '_default') continue;
            $spots = $info['spots'] ?? [];
            if (empty($spots)) continue;

            foreach ($spots as $idx => $spot) {
                $spotKey = Str::slug($spot['name']);
                $spotKey = substr($spotKey, 0, 50); // cap path length
                $localPath = $this->localBase . '/' . $destKey . '-' . $spotKey . '.jpg';
                $absolutePath = storage_path('app/public/' . $localPath);

                if (is_file($absolutePath) && filesize($absolutePath) > 5000) {
                    $cached++;
                    $this->ensureMediaRow($spot, $destKey, $idx, '(cached)', $localPath, $absolutePath, null);
                    continue;
                }

                $query = $this->buildQuery($spot['name'], $info['name']);
                $files = $this->searchCommons($query, 8);

                if (empty($files)) {
                    $files = $this->searchCommons($spot['name'] . ' Philippines', 6);
                }

                if (empty($files)) {
                    $fail++;
                    $this->command->warn(sprintf('  !! %-22s | %-40s | no results', $destKey, substr($spot['name'], 0, 40)));
                    continue;
                }

                // Province filter: drop candidate filenames that mention a
                // province from a different cluster. We do NOT fall back to
                // the unfiltered list when everything gets filtered — better
                // to have no image (cluster-landmark fallback at render time)
                // than to download a confidently-wrong photo.
                $cluster = $info['cluster'] ?? 'other';
                $tryFiles = array_values(array_filter(
                    $files,
                    fn ($f) => !LocalizedDestinationImageSeeder::isWrongCluster($f, $cluster)
                ));
                if (empty($tryFiles)) {
                    $fail++;
                    $this->command->warn(sprintf('  !! %-22s | %-40s | all results from wrong cluster', $destKey, substr($spot['name'], 0, 40)));
                    continue;
                }

                $saved = false;
                foreach ($tryFiles as $wikiFile) {
                    if ($this->downloadAndRegister($spot, $destKey, $idx, $wikiFile, $localPath, $absolutePath)) {
                        $saved = true; break;
                    }
                }

                if ($saved) {
                    $ok++;
                    $this->command->info(sprintf('  ok %-22s | %-40s', $destKey, substr($spot['name'], 0, 40)));
                } else {
                    $fail++;
                    $this->command->warn(sprintf('  !! %-22s | %-40s | download failed', $destKey, substr($spot['name'], 0, 40)));
                }
            }
        }

        $this->command->info('');
        $this->command->info("Spots with new images: $ok");
        $this->command->info("Spots cached (skipped): $cached");
        $this->command->info("Spots failed: $fail");
        $this->command->info('rg_media total: ' . DB::table('rg_media')->count());
    }

    private function buildQuery(string $spotName, string $destName): string
    {
        $clean = preg_replace('/\s*\(.*?\)\s*/', ' ', $spotName);
        $clean = trim($clean);
        $clean = preg_replace('/^(the |a |an )/i', '', $clean);
        // KEEP the parenthetical text (usually the province) for disambiguation.
        // Previous version stripped "Morong (Bataan)" -> "Morong", which made
        // Wikimedia search return Morong Rizal results instead of Bataan ones.
        $destClean = trim(str_replace(['(', ')'], ' ', $destName));
        $destClean = preg_replace('/\s+/', ' ', $destClean);
        return $clean . ' ' . $destClean . ' Philippines';
    }

    private function searchCommons(string $query, int $limit): array
    {
        try {
            $resp = Http::withHeaders($this->headers)->timeout(45)->get('https://commons.wikimedia.org/w/api.php', [
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
            $data = $resp->json();
            $pages = $data['query']['pages'] ?? [];

            $files = [];
            foreach ($pages as $page) {
                $title = $page['title'] ?? '';
                $info = $page['imageinfo'][0] ?? null;
                if (!$info) continue;
                $mime = $info['mime'] ?? '';
                if (!str_starts_with($mime, 'image/')) continue;
                if (str_starts_with($mime, 'image/svg')) continue;
                $w = $info['width'] ?? 0; $h = $info['height'] ?? 0;
                if ($w < 600 || $h < 400) continue;
                if (LocalizedDestinationImageSeeder::isJunkTitle($title)) continue;
                if (str_starts_with($title, 'File:')) {
                    $files[] = substr($title, 5);
                }
            }
            return $files;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function downloadAndRegister(array $spot, string $destKey, int $idx, string $wikiFile, string $localPath, string $absolutePath): bool
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
            file_put_contents($absolutePath, $body);
            $this->ensureMediaRow($spot, $destKey, $idx, $wikiFile, $localPath, $absolutePath, $url);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function ensureMediaRow(array $spot, string $destKey, int $idx, string $wikiFile, string $localPath, string $absolutePath, ?string $sourceUrl): void
    {
        if (DB::table('rg_media')->where('path', $localPath)->exists()) return;
        $width = $height = null;
        try {
            $sz = @getimagesize($absolutePath);
            if ($sz) { $width = $sz[0]; $height = $sz[1]; }
        } catch (\Throwable $e) {}
        DB::table('rg_media')->insert([
            'filename' => is_string($wikiFile) ? $wikiFile : 'spot.jpg',
            'path' => $localPath,
            'mime' => 'image/jpeg',
            'size_bytes' => is_file($absolutePath) ? filesize($absolutePath) : 0,
            'kind' => 'image',
            'width' => $width,
            'height' => $height,
            'alt' => $spot['name'] . ' in the Philippines',
            'caption' => $spot['name'],
            'source' => 'seeder-spots',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
            'source_url' => $sourceUrl,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureDir(): void
    {
        $path = storage_path('app/public/' . $this->localBase);
        if (!is_dir($path)) mkdir($path, 0755, true);
    }
}
