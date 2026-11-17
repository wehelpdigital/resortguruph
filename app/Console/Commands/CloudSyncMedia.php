<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

/**
 * Upload the local media library (storage/app/public) to the Laravel Cloud
 * bucket (the "cloud_bucket" disk, configured from CLOUD_BUCKET_* in .env).
 *
 *   php artisan rg:cloud-sync-media                 # rg-media + rg-blocks
 *   php artisan rg:cloud-sync-media --dir=rg-media/spots --dry-run
 *
 * Files already in the bucket with the same size are skipped, so it is safe
 * to re-run after adding new images in the admin.
 */
class CloudSyncMedia extends Command
{
    protected $signature = 'rg:cloud-sync-media
        {--disk=cloud_bucket : Target disk}
        {--dir=* : Sub-directories of storage/app/public to upload (default: rg-media, rg-blocks)}
        {--dry-run : List what would be uploaded without uploading}';

    protected $description = 'Upload storage/app/public media to the Laravel Cloud bucket';

    public function handle(): int
    {
        $diskName = (string) $this->option('disk');
        $cfg = config("filesystems.disks.{$diskName}");
        if (! is_array($cfg) || empty($cfg['bucket']) || empty($cfg['key'])) {
            $this->error("Disk [{$diskName}] is not configured. Set CLOUD_BUCKET_NAME, CLOUD_BUCKET_KEY, CLOUD_BUCKET_SECRET, CLOUD_BUCKET_ENDPOINT and CLOUD_BUCKET_URL in .env.");
            return self::FAILURE;
        }

        $disk = Storage::disk($diskName);
        $root = storage_path('app/public');
        $dirs = $this->option('dir') ?: ['rg-media', 'rg-blocks'];

        $total = 0;
        $uploaded = 0;
        $skipped = 0;
        $failed = [];

        foreach ($dirs as $dir) {
            $dir = trim(str_replace('\\', '/', $dir), '/');
            if (! is_dir("{$root}/{$dir}")) {
                $this->warn("  skip {$dir}: not a directory");
                continue;
            }

            // One LIST call per directory instead of a HEAD per file.
            $existing = [];
            foreach ($disk->listContents($dir, true) as $item) {
                if ($item->isFile()) {
                    $existing[$item->path()] = $item->fileSize();
                }
            }

            $files = iterator_to_array(
                Finder::create()->files()->in("{$root}/{$dir}")->ignoreDotFiles(true)->notName('.gitignore'),
                false
            );
            $this->info(sprintf('%s: %d local files, %d already in bucket', $dir, count($files), count($existing)));
            $bar = $this->output->createProgressBar(count($files));

            foreach ($files as $file) {
                $total++;
                $rel = $dir . '/' . str_replace('\\', '/', $file->getRelativePathname());
                if (isset($existing[$rel]) && (int) $existing[$rel] === $file->getSize()) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                if ($this->option('dry-run')) {
                    $this->line("  would upload {$rel}");
                    $uploaded++;
                    $bar->advance();
                    continue;
                }
                try {
                    $stream = fopen($file->getPathname(), 'rb');
                    $disk->writeStream($rel, $stream, [
                        'CacheControl' => 'public, max-age=31536000, immutable',
                        'ContentType' => mime_content_type($file->getPathname()) ?: 'application/octet-stream',
                    ]);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                    $uploaded++;
                } catch (\Throwable $e) {
                    $failed[] = "{$rel}: " . $e->getMessage();
                }
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
        }

        $this->info("Done. {$total} files: {$uploaded} uploaded" . ($this->option('dry-run') ? ' (dry run)' : '') . ", {$skipped} already there, " . count($failed) . ' failed.');
        foreach (array_slice($failed, 0, 20) as $f) {
            $this->error('  ' . $f);
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
