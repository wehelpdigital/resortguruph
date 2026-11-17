<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            // Off: /storage/* is handled by StorageFileController (public
            // media with a bucket fallback), see bootstrap/app.php.
            'serve' => false,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // The Laravel Cloud bucket, reached from a local machine. Only used
        // by `php artisan rg:cloud-sync-media` to upload the media library.
        // Fill CLOUD_BUCKET_* in your local .env (values come from the
        // bucket's "Access keys" panel in the Cloud dashboard).
        'cloud_bucket' => [
            'driver' => 's3',
            'key' => env('CLOUD_BUCKET_KEY'),
            'secret' => env('CLOUD_BUCKET_SECRET'),
            'region' => 'auto',
            'bucket' => env('CLOUD_BUCKET_NAME'),
            'url' => env('CLOUD_BUCKET_URL'),
            'endpoint' => env('CLOUD_BUCKET_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'throw' => true,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Media disk
    |--------------------------------------------------------------------------
    |
    | Where user uploads (resort photos, logos, GCash screenshots) are
    | written. Locally that's the "public" disk. On Laravel Cloud the local
    | filesystem is wiped on every deploy, so uploads go to the attached
    | bucket: Cloud injects LARAVEL_CLOUD_DISK_CONFIG and we pick its default
    | (or first) disk. MEDIA_DISK overrides both.
    |
    */

    'media_disk' => env('MEDIA_DISK') ?: (static function () {
        $raw = $_SERVER['LARAVEL_CLOUD_DISK_CONFIG'] ?? $_ENV['LARAVEL_CLOUD_DISK_CONFIG'] ?? null;
        $disks = is_string($raw) ? json_decode($raw, true) : null;
        if (! is_array($disks) || $disks === []) {
            return 'public';
        }
        foreach ($disks as $d) {
            if (! empty($d['is_default']) && ! empty($d['disk'])) {
                return $d['disk'];
            }
        }
        return $disks[0]['disk'] ?? 'public';
    })(),

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
