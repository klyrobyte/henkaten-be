<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',          // ← all /api/* JSON endpoints
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ── Middleware aliases ────────────────────────────────────────────────
        $middleware->alias([
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'tv.restrict'   => \App\Http\Middleware\TvRestrictMiddleware::class,
            'internal.request' => \App\Http\Middleware\EnsureInternalRequest::class,
            // Security patch 2026-05-10:
            'app.secret'    => \App\Http\Middleware\VerifyAppSecret::class,
            'log.deprecated'=> \App\Http\Middleware\LogDeprecatedRoute::class,
            'docs.restrict' => \App\Http\Middleware\RestrictDocsAccess::class,
            // Task 6 — Global Activity Logger alias (also appended to api group below)
            'global.log'    => \App\Http\Middleware\GlobalActivityLogger::class,
            'sc.guard'      => \App\Http\Middleware\ScContextGuard::class,
        ]);

        // ── Global web group middleware ───────────────────────────────────────
        // SecurityHeaders: inject security headers on every response
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);

        // TvRestrictMiddleware: intercept role=tv before reaching controllers
        $middleware->appendToGroup('web', \App\Http\Middleware\TvRestrictMiddleware::class);

        // GenerateApiNonce: mint a per-session nonce for browser API clients
        // Must run after session is started  - appended to web group ensures this
        $middleware->appendToGroup('web', \App\Http\Middleware\GenerateApiNonce::class);

        // ── API group middleware ──────────────────────────────────────────────
        // All /api/* routes must carry a valid X-App-Secret header.
        // auth:sanctum is already stripped from the api group (this app uses
        // session auth on web), so we replace it with our session + app.secret guard.
    
        // Since we migrated internal API routes to the 'api' group, we MUST inject
        // session state middlewares so that the 'auth' middleware can read the session.
        $middleware->prependToGroup('api', \Illuminate\Session\Middleware\StartSession::class);
        $middleware->prependToGroup('api', \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class);
        $middleware->prependToGroup('api', \Illuminate\Cookie\Middleware\EncryptCookies::class);

        $middleware->appendToGroup('api', \App\Http\Middleware\VerifyAppSecret::class);
        // Task 6: Log all authenticated API actions (append-only, encrypted PII)
        $middleware->appendToGroup('api', \App\Http\Middleware\GlobalActivityLogger::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();