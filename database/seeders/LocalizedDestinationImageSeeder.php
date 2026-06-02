<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Downloads per-destination images from Wikimedia Commons using the search API
 * (so we get real, existing files, not guessed filenames).
 *
 * For each destination key in database/data/destinations.php:
 *   1. Search Wikimedia Commons for "{destination name} Philippines"
 *   2. Take up to 3 image results
 *   3. Download each via Special:FilePath, save to storage/app/public/rg-media/destinations/
 *   4. Register in rg_media
 *
 * Idempotent: skips already-downloaded images and already-registered media rows.
 */
class LocalizedDestinationImageSeeder extends Seeder
{
    private string $localBase = 'rg-media/destinations';
    private int $perDestination = 3;
    private array $headers;

    public function __construct()
    {
        $this->headers = [
            'User-Agent' => 'ResortGuruPH/1.0 (https://resortguru.test; admin@dummy.test)',
            'Accept' => 'application/json, image/jpeg, image/*',
        ];
    }

    public function run(): void
    {
        $this->ensureDir();

        $destinations = require database_path('data/destinations.php');

        $this->command->info('Searching Wikimedia Commons for per-destination images...');

        $totalOk = 0;
        $totalSkipped = 0;
        $totalFailed = 0;

        foreach ($destinations as $key => $info) {
            if ($key === '_default') continue;

            $alreadyDownloaded = $this->countExistingForDestination($key);
            if ($alreadyDownloaded >= $this->perDestination) {
                $totalSkipped += $alreadyDownloaded;
                $this->command->info(sprintf('  -- %-32s cached (%d images)', $key, $alreadyDownloaded));
                continue;
            }

            $query = $this->buildSearchQuery($info['name']);
            $files = $this->searchCommons($query, $this->perDestination + 6);

            if (empty($files)) {
                $totalFailed++;
                $this->command->warn(sprintf('  !! %-32s no results for "%s"', $key, $query));
                continue;
            }

            // Province filter: drop candidates that mention another cluster's
            // place. We do NOT fall back to the unfiltered list — better to
            // fail this destination than to download a confidently-wrong photo.
            $cluster = $info['cluster'] ?? 'other';
            $files = array_values(array_filter(
                $files,
                fn ($f) => !self::isWrongCluster($f, $cluster)
            ));
            if (empty($files)) {
                $totalFailed++;
                $this->command->warn(sprintf('  !! %-32s all results from wrong cluster', $key));
                continue;
            }

            $saved = $alreadyDownloaded;
            foreach ($files as $wikiFile) {
                if ($saved >= $this->perDestination) break;
                $n = $saved + 1;

                $ok = $this->downloadAndRegister($key, $info, $wikiFile, $n);
                if ($ok) {
                    $saved++;
                    $totalOk++;
                }
            }

            if ($saved === 0) {
                $totalFailed++;
                $this->command->warn(sprintf('  !! %-32s all candidates failed', $key));
            } else {
                $this->command->info(sprintf('  ok %-32s saved %d images', $key, $saved));
            }
        }

        $this->command->info('');
        $this->command->info("Downloaded fresh: $totalOk");
        $this->command->info("Destinations already cached: $totalSkipped");
        $this->command->info("Destinations failed entirely: $totalFailed");
        $this->command->info('rg_media total rows: ' . DB::table('rg_media')->count());
    }

    private function buildSearchQuery(string $name): string
    {
        // KEEP the parenthetical text (usually the province) for disambiguation.
        // Previous version stripped "Morong (Bataan)" -> "Morong", which made
        // Wikimedia search return Morong Rizal results instead.
        $clean = trim(str_replace(['(', ')'], ' ', $name));
        $clean = preg_replace('/\s+/', ' ', $clean);
        if (!preg_match('/Philippines/i', $clean)) {
            $clean .= ' Philippines';
        }
        return $clean;
    }

    private function searchCommons(string $query, int $limit): array
    {
        $url = 'https://commons.wikimedia.org/w/api.php';
        try {
            $resp = Http::withHeaders($this->headers)->timeout(45)->get($url, [
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
                $imageinfo = $page['imageinfo'][0] ?? null;
                if (!$imageinfo) continue;
                $mime = $imageinfo['mime'] ?? '';
                if (!str_starts_with($mime, 'image/')) continue;
                if (str_starts_with($mime, 'image/svg')) continue;
                $w = $imageinfo['width'] ?? 0;
                $h = $imageinfo['height'] ?? 0;
                if ($w < 800 || $h < 500) continue;
                if ($this->isJunkTitle($title)) continue;

                if (str_starts_with($title, 'File:')) {
                    $files[] = substr($title, 5);
                }
            }
            return $files;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function downloadAndRegister(string $key, array $info, string $wikiFile, int $n): bool
    {
        $localPath = $this->localBase . '/' . $key . '-' . $n . '.jpg';
        $absolutePath = storage_path('app/public/' . $localPath);

        if (is_file($absolutePath) && filesize($absolutePath) > 5000) {
            $this->ensureMediaRow($info, $key, $wikiFile, $localPath, $absolutePath, null);
            return true;
        }

        $url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode($wikiFile) . '?width=1600';

        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(60)
                ->withOptions(['allow_redirects' => true])
                ->get($url);

            if (!$response->successful()) return false;
            $body = $response->body();
            if (strlen($body) < 5000) return false;

            file_put_contents($absolutePath, $body);
            $this->ensureMediaRow($info, $key, $wikiFile, $localPath, $absolutePath, $url);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function countExistingForDestination(string $key): int
    {
        $count = 0;
        for ($i = 1; $i <= $this->perDestination; $i++) {
            $p = storage_path('app/public/' . $this->localBase . '/' . $key . '-' . $i . '.jpg');
            if (is_file($p) && filesize($p) > 5000) $count++;
        }
        return $count;
    }

    private function ensureMediaRow(array $info, string $key, string $wikiFile, string $localPath, string $absolutePath, ?string $sourceUrl): void
    {
        if (DB::table('rg_media')->where('path', $localPath)->exists()) return;

        $width = $height = null;
        try {
            $sz = @getimagesize($absolutePath);
            if ($sz) { $width = $sz[0]; $height = $sz[1]; }
        } catch (\Throwable $e) {}

        $alt = $info['name'] . ' (' . pathinfo($wikiFile, PATHINFO_FILENAME) . ')';

        DB::table('rg_media')->insert([
            'filename' => $wikiFile,
            'path' => $localPath,
            'mime' => 'image/jpeg',
            'size_bytes' => is_file($absolutePath) ? filesize($absolutePath) : 0,
            'kind' => 'image',
            'width' => $width,
            'height' => $height,
            'alt' => $alt,
            'caption' => $info['name'],
            'source' => 'seeder-destinations',
            'credit' => 'Photo: Wikimedia Commons (CC-BY-SA)',
            'source_url' => $sourceUrl,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureDir(): void
    {
        $path = storage_path('app/public/' . $this->localBase);
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Reject municipal flags, seals, coats of arms, locator maps, and other
     * symbol/document files that show up in Wikimedia search but are not
     * photos of the actual place.
     */
    public static function isJunkTitle(string $title): bool
    {
        $patterns = [
            // Symbol/document files (flags, seals, coats of arms, maps)
            '/\bflag\s+of\b/i',
            '/\bseal\s+of\b/i',
            '/\bcoat[\s_-]+of[\s_-]+arms\b/i',
            '/\bemblem\s+of\b/i',
            '/\blogo\b/i',
            '/\binsignia\b/i',
            '/(location|locator|jurisdictional|sketch|outline)\s+map\b/i',
            '/\bmap\s+of\b/i',
            '/\bschematic\b/i',
            '/\binfographic\b/i',
            '/\bsignage\b/i',
            '/\bjeepney\s+route\b/i',
            '/\bofficial\s+(flag|seal|logo)\b/i',
            '/\bcity\s+seal\b/i',
            '/\bmunicipality\s+(of|seal)\b/i',
            '/\bbarangay\s+seal\b/i',
            // Diplomatic / political event photos (Wikimedia commons of US gov)
            '/\bSecretary\s+(of|Kerry|Pompeo|Austin|Clinton|Tillerson|Rubio|Blinken|Rice)\b/i',
            '/\b(Visit|visited|visiting)\s+to\s+[A-Z]/',
            '/\bShakes\s+Hands\b/i',
            '/\bMeets\s+with\b/i',
            '/\bMeeting\s+with\b/i',
            '/\bDelivers\s+Remarks\b/i',
            '/\bPress\s+Conference\b/i',
            '/\b(Department|Dept)\s+of\s+(State|Defense|Justice|Treasury)/i',
            '/\bSecretary[- ]designate\b/i',
            '/\bAmbassador\b/i',
            '/\bPresident\s+[A-Z][a-z]+\s+(visits|in|of)\b/i',
            '/\bU\.?S\.?\s+(Navy|Army|Marines|Air\s+Force|military)\b/i',
            // Person bio markers: "C. John Smith (1895-1968)", "Senator Foo", "Hon. Bar"
            '/^[A-Z]\.\s+[A-Z][a-z]+\s+[A-Z][a-z]+\s*\(\d{4}/',
            '/\bSenator\s+[A-Z]/',
            '/\bMayor\s+[A-Z]/',
            '/\bGovernor\s+[A-Z]/',
            '/\bHon\.\s+[A-Z]/',
            '/\bRepublican\s+(National|Senator|Representative)\b/i',
            '/\bDemocratic\s+(National|Senator|Representative)\b/i',
            // Other "commons of opportunity" non-scene patterns
            '/\bCorporate\s+headquarters\b/i',
            '/\bEarnings\s+call\b/i',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $title)) return true;
        }
        return false;
    }

    /**
     * Returns true if the Wikimedia file title mentions a Philippine province
     * or locality from a CLUSTER OTHER than the expected one. Used by the
     * spot and destination seeders to filter search results that look like
     * the right subject but came from the wrong geography (e.g. a Sabang
     * Beach search returning the Palawan Sabang when expecting Bataan).
     */
    public static function isWrongCluster(string $title, string $expectedCluster): bool
    {
        $clusterPlaces = [
            'rizal' => ['rizal'],
            'cavite' => ['cavite'],
            'batangas' => ['batangas'],
            'laguna' => ['laguna'],
            'quezon' => ['quezon province'],
            'bulacan' => ['bulacan'],
            'pampanga' => ['pampanga'],
            'metro-manila' => ['ncr', 'metropolitan manila'],
            'bicol' => ['bicol', 'albay', 'camarines', 'sorsogon', 'catanduanes', 'masbate', 'legazpi', 'legaspi'],
            'north-luzon' => ['pangasinan', 'la union', 'ilocos sur', 'ilocos norte', 'ilocos', 'cagayan', 'isabela', 'aurora', 'nueva ecija', 'nueva vizcaya', 'tarlac', 'zambales', 'bataan', 'benguet', 'baguio'],
            'mindanao' => ['davao', 'mindanao', 'cotabato', 'maguindanao', 'sarangani', 'zamboanga', 'lanao', 'agusan', 'surigao', 'misamis', 'bukidnon', 'camiguin'],
            'visayas' => ['cebu', 'iloilo', 'bohol', 'negros', 'panay', 'samar', 'leyte', 'aklan', 'capiz', 'antique', 'guimaras', 'siquijor', 'biliran', 'boracay'],
            'palawan' => ['palawan', 'el nido', 'coron', 'puerto princesa'],
            'other' => [],
        ];

        $expected = $clusterPlaces[$expectedCluster] ?? [];
        $low = strtolower($title);

        // If the title contains an expected place name, it's not wrong-cluster
        foreach ($expected as $p) {
            if (str_contains($low, $p)) return false;
        }

        // Otherwise, check if it mentions any OTHER cluster's place
        foreach ($clusterPlaces as $cluster => $places) {
            if ($cluster === $expectedCluster) continue;
            foreach ($places as $p) {
                if (preg_match('/\b' . preg_quote($p, '/') . '\b/i', $low)) return true;
            }
        }
        return false;
    }
}
