<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

/**
 * Fallback for /storage/{path}.
 *
 * Normally public/storage is a symlink to storage/app/public (created by
 * `php artisan storage:link` locally, and by composer's post-install hook on
 * Laravel Cloud), so the web server serves media files directly and this
 * controller never runs. It only kicks in when:
 *
 *  - the symlink is missing: the file is streamed from storage/app/public;
 *  - the file isn't on local disk at all: it was uploaded on Cloud (owner
 *    dashboard), so it lives in the bucket and we redirect there.
 */
class StorageFileController extends Controller
{
    public function __invoke(string $path)
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($path === '' || str_contains($path, '..') || str_contains($path, "\0")) {
            abort(404);
        }

        $local = storage_path('app/public/' . $path);
        if (is_file($local)) {
            return response()->file($local, [
                'Cache-Control' => 'public, max-age=2592000',
            ]);
        }

        $disk = (string) config('filesystems.media_disk', 'public');
        if ($disk !== 'public' && config("filesystems.disks.{$disk}.driver") === 's3') {
            return redirect()->away(Storage::disk($disk)->url($path), 302)
                ->header('Cache-Control', 'public, max-age=86400');
        }

        abort(404);
    }
}
