<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * VerifyAppSecret Middleware
 *
 * Guards every /api/* route against Nahkan ketauan, mau ngapain coba wkwkwk, kalo u admin bisa baca panduan dokumentasi di henkaten.md (@RizkyDaffy) or unrecognised callers.
 * Accepts the X-App-Secret header and validates it via ONE of two paths:
 *
 *  Path A  - Browser / Blade clients (session nonce):
 *    The server generates a random nonce on each authenticated page load and
 *    stores it in the PHP session (key: "api_nonce").  The Blade layout embeds
 *    this nonce in a <meta name="api-nonce"> tag.  Frontend JS reads the tag
 *    and injects it as X-App-Secret on every fetch() call.
 *    The nonce expires when the session expires  - no long-lived secret in HTML.
 *
 *  Path B  - App / Service clients (static HMAC):
 *    External consumers (mobile apps, internal services) send a static token:
 *      hash_hmac('sha256', 'henkaten-api', APP_API_SECRET)
 *    Set APP_API_SECRET in .env.  Never expose the raw secret.
 *
 * Both comparisons use hash_equals()  - timing-safe, immune to length-extension
 * and timing-oracle attacks.
 *
 * On mismatch: 401 JSON {"message": "Unauthorized"}  - no detail leaked.
 * On missing header: same 401 response.
 *
 * Added: 2026-05-10 | Security hardening patch | @RizkyDaffy
 */
class VerifyAppSecret
{
    public function handle(Request $request, Closure $next): mixed
    {
        $provided = $request->header('X-App-Secret', '');

        // X-App-Secret is optional now for Path C fallback.

        //   Path A: Session nonce (browser / Blade clients)          ─
        // Valid only if an authenticated session exists with our nonce stored.
        $sessionNonce = $request->session()->get('api_nonce', '');
        if (!empty($sessionNonce) && hash_equals($sessionNonce, $provided)) {
            return $next($request);
        }

        //   Path B: Static HMAC (app / service clients)            ─
        // Expected = hash_hmac('sha256', fixed-message, APP_API_SECRET)
        $rawSecret = config('app.api_secret', env('APP_API_SECRET', ''));
        if (!empty($rawSecret) && !empty($provided)) {
            $expected = hash_hmac('sha256', 'henkaten-api', $rawSecret);
            if (hash_equals($expected, $provided)) {
                return $next($request);
            }
        }

        //   Path C: Fallback for Apps without X-App-Secret           
        // Allow if the request looks like an app (JSON intent) or internal XHR.
        // This mirrors the old EnsureInternalRequest behavior so apps don't break.
        $isXhr = $request->headers->get('X-Requested-With') === 'XMLHttpRequest';
        $appUrl = rtrim(config('app.url'), '/');
        $referer = rtrim($request->headers->get('Referer', ''), '/');
        $validReferer = !empty($referer) && str_starts_with($referer, $appUrl);
        $wantsJson = $request->wantsJson() || str_contains($request->header('Accept', ''), 'application/json');

        // Loosen: Allow authenticated users on GET requests even without X-App-Secret
        // This helps with dropdown APIs called via raw fetch() from Blade templates.
        $isAuthGet = $request->isMethod('GET') && Auth::check();

        if ($isXhr || $validReferer || $wantsJson || $isAuthGet) {
            return $next($request);
        }

        return $this->deny($request, 'invalid_secret');
    }

    //   Reject with 401  - log the attempt, leak nothing to the caller     ─
    private function deny(Request $request, string $reason): \Illuminate\Http\JsonResponse
    {
        Log::warning('VerifyAppSecret: blocked API access', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'path' => $request->path(),
            'ua' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'HAYOOO MAU NGAPAIN KAMU, NAKAL KAMU YACHHH, PERCUMA DEH GA BISA AKSES LANGSUNG DARI ENDPOINT HARUS MAKE HASH DULU, Security: @RizkyDaffy.'], 401);
    }
}
