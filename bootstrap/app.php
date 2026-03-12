<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias untuk dipakai manual di route tertentu
        $middleware->alias([
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'tv.restrict' => \App\Http\Middleware\TvRestrictMiddleware::class,
        ]);

        // Append ke semua web request — intercept role tv sebelum sampai controller
        $middleware->appendToGroup('web', \App\Http\Middleware\TvRestrictMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();