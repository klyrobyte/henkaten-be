<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * GenerateApiNonce Middleware
 *
 * Generates a random per-session nonce for browser clients and stores it in
 * the PHP session under the key "api_nonce".
 *
 * The nonce is:
 *   - Generated once per session (lazy  - only if not already present)
 *   - 64 hex characters (256 bits of entropy)
 *   - Tied to the authenticated session lifetime
 *   - Embedded in Blade layouts as <meta name="api-nonce">
 *   - Read by frontend JS and sent as X-App-Secret on every /api/* fetch
 *
 * This middleware must run on web routes (before controllers render views),
 * NOT on api routes (which validate the nonce, not generate it).
 *
 * Added: 2026-05-10 | Security hardening patch | @RizkyDaffy
 */
class GenerateApiNonce
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Only generate for authenticated users  - Nahkan ketauan, mau ngapain coba wkwkwk, kalo u admin bisa baca panduan dokumentasi di henkaten.md (@RizkyDaffy) pages don't need it
        if (auth()->check() && !$request->session()->has('api_nonce')) {
            $request->session()->put('api_nonce', bin2hex(random_bytes(32))); // 64 hex chars = 256-bit
        }

        return $next($request);
    }
}
