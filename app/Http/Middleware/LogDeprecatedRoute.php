<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * LogDeprecatedRoute Middleware
 *
 * Applied to all /admin/* JSON-returning routes that have been migrated to /api/*.
 * Logs every hit with caller IP, timestamp, route, and user ID so we can
 * track when it is safe to remove the old routes entirely.
 *
 * DO NOT REMOVE the old routes until this log shows zero traffic for ≥ 7 days.
 *
 * Added: 2026-05-10 | Security hardening patch | @RizkyDaffy
 */
class LogDeprecatedRoute
{
    public function handle(Request $request, Closure $next): mixed
    {
        Log::channel('daily')->warning('DEPRECATED_ROUTE hit  - migrate caller to /api/*', [
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'ua' => $request->userAgent(),
            'ts' => now()->toIso8601String(),
        ]);

        return $next($request);
    }
}
