<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Per-food-item image seeder. For every food string in destinations.php, search
 * Wikimedia Commons for a real photo of that dish and save it to
 * storage/app/public/rg-media/foods/{destKey}-{food-slug}.jpg.
 *
 * The food card grid in keyword pages becomes a big-photo feature instead of
 * an emoji-plus-text mini-card once these images are on disk. Idempotent:
 * skips already-downloaded files. Misses fall back to destination-level images
 * via the rewrite seeder.
 */
class FoodImageSeeder extends Seeder
{
    private string $localBase = 'rg-media/foods';
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

        $this->command->info('Fetching per-food images from Wikimedia Commons...');
        $ok = 0; $fail = 0; $cached = 0;

        foreach ($destinations as $destKey => $info) {
            if ($destKey === '_default') continue;
            $foods = $info['foods'] ?? $info['food'] ?? [];
            if (empty($foods)) continue;

            foreach ($foods as $idx => $foodString) {
                $dish = $this->extractDishName($foodString);
                $foodSlug = substr(Str::slug($dish), 0, 50);
                if ($foodSlug === '') continue;

                $localPath = $this->localBase . '/' . $destKey . '-' . $foodSlug . '.jpg';
                $absolutePath = storage_path('app/public/' . $localPath);

                if (is_file($absolutePath) && filesize($absolutePath) > 5000) {
                    $cached++;
                    $this->ensureMediaRow($dish, $foodString, $destKey, '(cached)', $localPath, $absolutePath, null);
                    continue;
                }

                $files = $this->searchCommons($dish . ' Filipino food', 4);
                if (empty($files)) {
                    $files = $this->searchCommons($dish . ' Philippine cuisine', 3);
                }
                if (empty($files)) {
                    $files = $this->searchCommons($dish, 3);
                }

                if (empty($files)) {
                    $fail++;
                    $this->command->warn(sprintf('  !! %-22s | %-40s | no results', $destKey, substr($dish, 0, 40)));
                    continue;
                }

                $saved = false;
                foreach ($files as $wikiFile) {
                    if ($this->downloadAndRegister($dish, $foodString, $destKey, $wikiFile, $localPath, $absolutePath)) {
                        $saved = true; break;
                    }
                }

                if ($saved) {
                    $ok++;
                    $this->command->info(sprintf('  ok %-22s | %-40s', $destKey, substr($dish, 0, 40)));
                } else {
                    $fail++;
                    $this->command->warn(sprintf('  !! %-22s | %-40s | download failed', $destKey, substr($dish, 0, 40)));
                }
            }
        }

        $this->command->info('');
        $this->command->info("Foods with new images: $ok");
        $this->command->info("Foods cached (skipped): $cached");
        $this->command->info("Foods failed: $fail");
        $this->command->info('rg_media total: ' . DB::table('rg_media')->count());
    }

    /**
     * Pull just the dish name out of a string like
     * "bulalo at Mahogany Market eateries (Diner's Court, Leslie's, Bulalo Point)"
     * → "bulalo".
     */
    private function extractDishName(string $food): string
    {
        // Strip parenthetical context
        $food = preg_replace('/\s*\([^)]*\)\s*/', ' ', $food);

        // Strip leading articles
        $food = preg_replace('/^(the |a |an )/i', '', trim($food));

        // Split on first separator that introduces context
        $separators = [' at ', ' from ', ' with ', ' along ', ' by ', ' inside ', ' beside ', ' near ', ' served ', ' fried ', ', ', ' breakfast', ' dinner', ' lunch'];
        foreach ($separators as $sep) {
            $pos = mb_stripos($food, $sep);
            if ($pos !== false && $pos > 0) {
                $food = mb_substr($food, 0, $pos);
                break;
            }
        }

        // If still long, take the first 5 words to keep the query focused
        $words = preg_split('/\s+/', trim($food));
        if (count($words) > 5) $words = array_slice($words, 0, 5);

        return trim(implode(' ', $words));
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

    private function downloadAndRegister(string $dish, string $foodString, string $destKey, string $wikiFile, string $localPath, string $absolutePath): bool
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
            $this->ensureMediaRow($dish, $foodString, $destKey, $wikiFile, $localPath, $absolutePath, $url);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function ensureMediaRow(string $dish, string $foodString, string $destKey, string $wikiFile, string $localPath, string $absolutePath, ?string $sourceUrl): void
    {
        if (DB::table('rg_media')->where('path', $localPath)->exists()) return;
        $width = $height = null;
        try {
            $sz = @getimagesize($absolutePath);
            if ($sz) { $width = $sz[0]; $height = $sz[1]; }
        } catch (\Throwable $e) {}
        DB::table('rg_media')->insert([
            'filename' => is_string($wikiFile) ? $wikiFile : 'food.jpg',
            'path' => $localPath,
            'mime' => 'image/jpeg',
            'size_bytes' => is_file($absolutePath) ? filesize($absolutePath) : 0,
            'kind' => 'image',
            'width' => $width,
            'height' => $height,
            'alt' => $dish . ' (Filipino food)',
            'caption' => ucfirst($dish),
            'source' => 'seeder-foods',
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
