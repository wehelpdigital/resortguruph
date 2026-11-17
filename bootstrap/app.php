<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Media fallback lives outside the "web" group: serving an image
            // must not start a session or touch the DB.
            Route::get('/storage/{path}', \App\Http\Controllers\StorageFileController::class)
                ->where('path', '.*')
                ->name('storage.file');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Laravel Cloud terminates TLS at its edge and forwards plain HTTP,
        // so trust the proxy headers or every url()/asset()/redirect is
        // generated as http:// (mixed-content images, insecure form posts).
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
