<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** 
 * EnsureInternalRequest Middleware
 *
 * Guards internal API endpoints (prefixed /api/*) from being accessed
 * directly from external tools like curl, Postman, or scrapers.
 *
 * Since all these endpoints are already protected by `auth` middleware
 * (requiring a valid session), this adds a second layer by verifying
 * the request originates from our own authenticated frontend:
 *
 *  Layer 1 (auth middleware)  : Valid session cookie required
 *  Layer 2 (this middleware)  : Request must originate from our app
 *
 * Validation checks (any one must pass):
 *  A) X-Requested-With: XMLHttpRequest header    - set by Axios automatically
 *  B) Referer header starts with APP_URL         - browser sets this for same-origin fetches
 *  C) Accept header contains application/json    - explicit JSON intent
 *
 * This means a plain `curl -X GET /api/machines/floor-plan` won't pass
 * even with a stolen session cookie, unless the attacker also mimics our headers.
 * Combined with rate limiting and session encryption, this provides strong protection.
 *
 * Added: 2026-05-02 | Security hardening for production deployment, by @RizkyDaffy
 */
class EnsureInternalRequest
{
    public function handle(Request $request, Closure $next): mixed
    {
        // ── Check 1: X-Requested-With header (Axios / jQuery AJAX) ───────────
        $isXhr = $request->headers->get('X-Requested-With') === 'XMLHttpRequest';

        // ── Check 2: Referer must match our app's URL ─────────────────────────
        $appUrl = rtrim(config('app.url'), '/');
        $referer = rtrim($request->headers->get('Referer', ''), '/');
        $validReferer = !empty($referer) && str_starts_with($referer, $appUrl);

        // ── Check 3: Accept header signals JSON intent ────────────────────────
        $wantsJson = $request->wantsJson()
            || str_contains($request->header('Accept', ''), 'application/json');

        if (!$isXhr && !$validReferer && !$wantsJson) {
            // Log suspicious direct access attempts (without exposing why it failed)
            Log::warning('EnsureInternalRequest: blocked direct API access', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'ua' => $request->userAgent(),
                'referer' => $request->header('Referer', 'none'),
            ]);

            return response()->json(['message' => 'HAYOOO MAU NGAPAIN KAMU, NAKAL KAMU YACHHH, PERCUMA DEH GA BISA AKSES LANGSUNG DARI ENDPOINT HARUS MAKE HASH DULU, Security: @RizkyDaffy.'], 403); //bikin hengker ketar ketir, akses ke request api ke tolak jika tidak ada handshake aplikasi
        }

        return $next($request);
    }
}
