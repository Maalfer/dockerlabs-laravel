<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: [
        __DIR__.'/../routes/dockerlabs.php',   // ra�z (DockerLabs)
        __DIR__.'/../routes/bunkerlabs.php',   // prefijo /bunkerlabs
        __DIR__.'/../routes/web.php',          // (solo rutas compartidas)
    ],
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
