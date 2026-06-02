<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Per-operator photo seeder for the transport recommendations block. Reads
 * transport_options.php, walks every unique operator name (Cebu Pacific,
 * Victory Liner, OceanJet, etc.), searches Wikimedia Commons for a real photo
 * of that operator's vehicle (aircraft/bus/ferry), and saves it to
 * storage/app/public/rg-media/transport/{operator-slug}.jpg.
 *
 * The transport block render upgrades from a small icon+text row to a bigger
 * sub-boxed card with a real photo once these images are on disk. Idempotent.
 * Junk filter from LocalizedDestinationImageSeeder reused to keep logos, seals,
 * and political event photos out.
 */
class TransportPhotoSeeder extends Seeder
{
    private string $localBase = 'rg-media/transport';
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
        $transport = require database_path('data/transport_options.php');

        // Build a unique set of operator names + their transport type so we know
        // what to search for. Same operator across destinations gets one photo.
        $unique = [];
        foreach ($transport as $key => $options) {
            foreach ($options as $opt) {
                $name = $opt['name'] ?? '';
                $type = $opt['type'] ?? 'bus';
                if ($name === '') continue;
                $unique[$name] = $type;
            }
        }

        $this->command->info('Fetching per-operator transport photos from Wikimedia (' . count($unique) . ' operators)...');
        $ok = 0; $cached = 0; $fail = 0;

        foreach ($unique as $name => $type) {
            $slug = substr(Str::slug($name), 0, 50);
            $localPath = $this->localBase . '/' . $slug . '.jpg';
            $absolutePath = storage_path('app/public/' . $localPath);

            if (is_file($absolutePath) && filesize($absolutePath) > 5000) {
                $cached++;
                $this->ensureMediaRow($name, $type, '(cached)', $localPath, $absolutePath, null);
                continue;
            }

            $query = $this->buildQuery($name, $type);
            $files = $this->searchCommons($query, 4);
            if (empty($files)) {
                // Try a simpler fallback: just the operator name
                $files = $this->searchCommons($name . ' Philippines', 3);
            }
            if (empty($files)) {
                // Fall back to a generic mode-of-transport search
                $files = $this->searchCommons($this->genericQuery($type), 3);
            }

            if (empty($files)) {
                $fail++;
                $this->command->warn(sprintf('  !! %-30s | %-12s | no results', substr($name, 0, 30), $type));
                continue;
            }

            $saved = false;
            foreach ($files as $wikiFile) {
                if ($this->downloadAndRegister($name, $type, $wikiFile, $localPath, $absolutePath)) {
                    $saved = true; break;
                }
            }
            if ($saved) {
                $ok++;
                $this->command->info(sprintf('  ok %-30s | %-12s', substr($name, 0, 30), $type));
            } else {
                $fail++;
            }
        }

        $this->command->info('');
        $this->command->info("Operator photos downloaded: $ok");
        $this->command->info("Cached (skipped): $cached");
        $this->command->info("Failed: $fail");
    }

    private function buildQuery(string $name, string $type): string
    {
        $clean = preg_replace('/\s*\(.*?\)\s*/', ' ', $name);
        $clean = trim($clean);
        $suffix = match ($type) {
            'airline' => ' aircraft',
            'bus' => ' bus Philippines',
            'ferry' => ' ferry Philippines',
            'rail' => ' train',
            'ride' => '',
            'car' => '',
            default => ' Philippines',
        };
        return $clean . $suffix;
    }

    private function genericQuery(string $type): string
    {
        return match ($type) {
            'airline' => 'commercial airliner Philippines',
            'bus' => 'Philippine bus transport',
            'ferry' => 'Philippines ferry inter-island',
            'rail' => 'LRT Manila train',
            'ride' => 'Grab Philippines',
            'car' => 'Philippine highway',
            default => 'Philippine transport',
        };
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

    private function downloadAndRegister(string $operator, string $type, string $wikiFile, string $localPath, string $absolutePath): bool
    {
        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($wikiFile) . '?width=1200';
        try {
            $resp = Http::withHeaders($this->headers)->timeout(60)->withOptions(['allow_redirects' => true])->get($url);
            if (!$resp->successful()) return false;
            $body = $resp->body();
            if (strlen($body) < 5000) return false;
            file_put_contents($absolutePath, $body);
            $this->ensureMediaRow($operator, $type, $wikiFile, $localPath, $absolutePath, $url);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function ensureMediaRow(string $operator, string $type, string $wikiFile, string $localPath, string $absolutePath, ?string $sourceUrl): void
    {
        if (DB::table('rg_media')->where('path', $localPath)->exists()) return;
        $width = $height = null;
        try {
            $sz = @getimagesize($absolutePath);
            if ($sz) { $width = $sz[0]; $height = $sz[1]; }
        } catch (\Throwable $e) {}
        DB::table('rg_media')->insert([
            'filename' => $wikiFile,
            'path' => $localPath,
            'mime' => 'image/jpeg',
            'size_bytes' => is_file($absolutePath) ? filesize($absolutePath) : 0,
            'kind' => 'image',
            'width' => $width,
            'height' => $height,
            'alt' => $operator . ' (' . $type . ')',
            'caption' => $operator,
            'source' => 'seeder-transport',
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
