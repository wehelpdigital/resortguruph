<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Downloads real Philippines tourist-spot images from Wikimedia Commons for each
 * cluster, saves them under storage/app/public/rg-media/landmarks/, registers
 * them in the rg_media table, and swaps every existing SEO-page image block
 * (currently pointing at Picsum) to use the local landmark image instead.
 *
 * Also updates rg_seo_pages.og_image_path to the local landmark path.
 *
 * Idempotent: skips downloads that already exist on disk.
 */
class LandmarkImageSeeder extends Seeder
{
    private array $landmarks = [
        'bicol' => [
            'file' => 'Mayon Volcano in Daraga, Albay.JPG',
            'alt' => 'Mayon Volcano viewed from Daraga, Albay',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'north-luzon' => [
            'file' => 'Banaue Rice Terraces, Ifugao.JPG',
            'alt' => 'Banaue Rice Terraces, Ifugao',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'visayas' => [
            'file' => 'Chocolate Hills overview.JPG',
            'alt' => 'Chocolate Hills, Bohol',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'metro-manila' => [
            'file' => 'Fort Santiago, Intramuros.JPG',
            'alt' => 'Fort Santiago, Intramuros, Manila',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'mindanao' => [
            'file' => 'Mount Apo.JPG',
            'alt' => 'Mount Apo, the highest peak in the Philippines',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'rizal' => [
            'file' => 'Daranak Falls, Tanay, Rizal.jpg',
            'alt' => 'Daranak Falls in Tanay, Rizal',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'batangas' => [
            'file' => 'Panorama of Lake Taal & Volcano, Philippines.jpg',
            'alt' => 'Taal Volcano and Lake panorama, Batangas',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'cavite' => [
            'file' => 'Taal Lake view from Tagaytay 2015.jpg',
            'alt' => 'Taal Lake viewed from Tagaytay ridge, Cavite',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'laguna' => [
            'file' => '20150201 5일차 Pagsanjan Falls Tour - panoramio.jpg',
            'alt' => 'Pagsanjan Falls, Laguna',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'pampanga' => [
            'file' => 'Pinatubo Crater Lake (052005).jpg',
            'alt' => 'Mount Pinatubo crater lake, Pampanga',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'bulacan' => [
            'file' => '0344jfSanto Barasoain Church Malolos City Bulacanfvf 07.JPG',
            'alt' => 'Barasoain Church, Malolos, Bulacan',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'quezon' => [
            'file' => 'Pahiyas Festival 2023 Fidel Rada-Rizal Street (Lucban, Quezon; 05-14-2023).jpg',
            'alt' => 'Pahiyas Festival, Lucban, Quezon',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
        'other' => [
            'file' => 'Boracay Island 06.JPG',
            'alt' => 'Boracay Island, Aklan',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
        ],
    ];

    private string $localBase = 'rg-media/landmarks';
    private array $downloaded = [];

    public function run(): void
    {
        $this->ensureDir();

        $this->command->info('Downloading Philippines landmark images...');
        foreach ($this->landmarks as $cluster => $info) {
            $this->downloadOne($cluster, $info);
        }

        $this->command->info('');
        $this->command->info('Updating SEO page image blocks + og_image to use landmarks...');
        $blocksUpdated = 0;
        $ogUpdated = 0;

        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->select('p.id', 'p.og_image_path', 'k.cluster_tag', 'k.phrase')
            ->get();

        foreach ($pages as $page) {
            $cluster = $page->cluster_tag ?: 'other';
            $localPath = $this->downloaded[$cluster] ?? ($this->downloaded['other'] ?? null);
            if (!$localPath) continue;
            $alt = ($this->landmarks[$cluster]['alt'] ?? 'Philippines') . ' — near ' . ucwords($page->phrase);

            $imageBlocks = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $page->id)
                ->where('block_type', 'image')
                ->get();

            foreach ($imageBlocks as $block) {
                $payload = json_decode($block->payload_json, true) ?: [];
                if (empty($payload['src'])) continue;
                if (!str_contains($payload['src'], 'picsum.photos')) continue;

                $payload['src'] = '/storage/' . ltrim($localPath, '/');
                $payload['alt'] = $alt;
                $payload['caption'] = $this->landmarks[$cluster]['alt'] ?? '';
                DB::table('rg_content_blocks')->where('id', $block->id)->update([
                    'payload_json' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                $blocksUpdated++;
            }

            // og_image update
            if (!empty($page->og_image_path) && str_contains($page->og_image_path, 'picsum.photos')) {
                DB::table('rg_seo_pages')->where('id', $page->id)->update([
                    'og_image_path' => $localPath,
                    'updated_at' => now(),
                ]);
                $ogUpdated++;
            }
        }

        // Also update static-page image blocks (they use Picsum too)
        $staticBlocksUpdated = 0;
        $generic = $this->downloaded['other'] ?? null;
        if ($generic) {
            $staticImageBlocks = DB::table('rg_content_blocks')
                ->where('owner_type', 'static_page')
                ->where('block_type', 'image')
                ->get();
            foreach ($staticImageBlocks as $block) {
                $payload = json_decode($block->payload_json, true) ?: [];
                if (empty($payload['src']) || !str_contains($payload['src'], 'picsum.photos')) continue;
                $payload['src'] = '/storage/' . ltrim($generic, '/');
                $payload['alt'] = $payload['alt'] ?: 'Philippines destination';
                DB::table('rg_content_blocks')->where('id', $block->id)->update([
                    'payload_json' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                $staticBlocksUpdated++;
            }
        }

        $this->command->info('');
        $this->command->info("SEO image blocks updated: $blocksUpdated");
        $this->command->info("OG images updated: $ogUpdated");
        $this->command->info("Static page image blocks updated: $staticBlocksUpdated");
        $this->command->info('Media library entries: ' . DB::table('rg_media')->count());
    }

    private function ensureDir(): void
    {
        $path = storage_path('app/public/' . $this->localBase);
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function downloadOne(string $cluster, array $info): void
    {
        $filename = $cluster . '.jpg';
        $localPath = $this->localBase . '/' . $filename;
        $absolutePath = storage_path('app/public/' . $localPath);

        // Skip if cached
        if (is_file($absolutePath) && filesize($absolutePath) > 5000) {
            $this->downloaded[$cluster] = $localPath;
            $this->ensureMediaRow($info, $localPath, $absolutePath);
            $this->command->info(sprintf('  ✓ %-14s (cached, %d KB)', $cluster, round(filesize($absolutePath) / 1024)));
            return;
        }

        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($info['file']) . '?width=1600';

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'ResortGuruPH/1.0 (https://resortguru.test; admin@dummy.test)',
                'Accept' => 'image/jpeg,image/*',
            ])->timeout(60)->withOptions(['allow_redirects' => true])->get($url);

            if (!$response->successful()) {
                $this->command->warn(sprintf('  ✗ %-14s (HTTP %d — file not found on Wikimedia)', $cluster, $response->status()));
                return;
            }

            $body = $response->body();
            if (strlen($body) < 5000) {
                $this->command->warn(sprintf('  ✗ %-14s (too small: %d bytes — likely an error page)', $cluster, strlen($body)));
                return;
            }

            file_put_contents($absolutePath, $body);
            $this->downloaded[$cluster] = $localPath;
            $this->ensureMediaRow($info, $localPath, $absolutePath, $url);

            $this->command->info(sprintf('  ✓ %-14s %s (%d KB)', $cluster, $info['file'], round(strlen($body) / 1024)));
        } catch (\Throwable $e) {
            $this->command->warn(sprintf('  ✗ %-14s exception: %s', $cluster, $e->getMessage()));
        }
    }

    private function ensureMediaRow(array $info, string $localPath, string $absolutePath, ?string $sourceUrl = null): void
    {
        if (DB::table('rg_media')->where('path', $localPath)->exists()) return;
        $width = $height = null;
        try {
            $sz = @getimagesize($absolutePath);
            if ($sz) { $width = $sz[0]; $height = $sz[1]; }
        } catch (\Throwable $e) {}
        DB::table('rg_media')->insert([
            'filename' => $info['file'],
            'path' => $localPath,
            'mime' => 'image/jpeg',
            'size_bytes' => filesize($absolutePath),
            'kind' => 'image',
            'width' => $width,
            'height' => $height,
            'alt' => $info['alt'],
            'caption' => $info['alt'],
            'source' => 'seeder-landmarks',
            'credit' => $info['credit'] ?? 'Wikimedia Commons',
            'source_url' => $sourceUrl,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
