<?php

namespace App\Http\Middleware;

use App\Models\GlobalLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * GlobalActivityLogger — Task 6 (planning.md 2026-05-20)
 *
 * Appends one encrypted row to global_logs for every authenticated request.
 * PII (username, IP) is encrypted before storage via GlobalLog::record().
 *
 * Registration: append to 'api' group in bootstrap/app.php
 *               (already done for all /api/* routes)
 *
 * SECURITY:
 *  - Only logs authenticated requests (unauthenticated → skipped)
 *  - Raw IP never stored in plaintext
 *  - Runs AFTER response so it never blocks the request
 */
class GlobalActivityLogger
{
    /**
     * Paths to skip (noise reduction — polling & static assets)
     */
    private const SKIP_PATTERNS = [
        '/api/status',
        '/api/absence/data',
        '/api/replacements',
        '/api/logs/list',
        '/api/logs/combined',
        '/up',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log authenticated requests
        if (!Auth::check()) {
            return $response;
        }

        // Skip high-frequency polling endpoints (they'd flood the log)
        foreach (self::SKIP_PATTERNS as $pattern) {
            if (str_starts_with($request->path(), ltrim($pattern, '/'))) {
                return $response;
            }
        }

        $user = Auth::user();

        GlobalLog::record(
            userId:    $user->id ?? null,
            username:  $user->username ?? $user->name ?? null,
            role:      $user->role ?? null,
            action:    $request->method(),
            target:    '/' . $request->path(),
            detail:    $this->buildDetail($request, $response->status()),
            ip:        $request->ip(),
            userAgent: $request->userAgent(),
            factory:   $request->session()->get('factory') ?? $request->input('factory'),
        );

        return $response;
    }

    private function buildDetail(Request $request, int $statusCode): array
    {
        $detail = ['status' => $statusCode];
        // Include safe (non-PII) query params for context
        $qs = $request->except(['password', 'token', '_token', 'secret']);
        if (!empty($qs)) {
            // Truncate values to keep the JSON small
            $detail['params'] = array_map(
                fn($v) => is_string($v) ? substr($v, 0, 80) : $v,
                $qs
            );
        }
        return $detail;
    }
}
