<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * SecurityHeaders Middleware
 *
 * Injects hardened HTTP security headers on every response to protect
 * against common web vulnerabilities: clickjacking, MIME sniffing, XSS,
 * information leakage, and HTTPS downgrade attacks.
 *
 * Headers applied:
 *  - X-Frame-Options           : prevents clickjacking
 *  - X-Content-Type-Options    : prevents MIME-type sniffing
 *  - X-XSS-Protection          : legacy XSS filter for older browsers
 *  - Referrer-Policy           : limits referrer data leakage
 *  - Permissions-Policy        : disables browser features not used by the app
 *  - Content-Security-Policy   : restricts resource loading to trusted origins
 *  - Strict-Transport-Security : enforces HTTPS (production only)
 *
 * Added: 2026-05-02 | Security hardening for production deployment by RizkyDaffy
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        //   Anti-Clickjacking                         ─
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        //   Prevent MIME Sniffing                       ─
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        //   Legacy XSS Protection (IE/old Chrome)               ─
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        //   Referrer Policy                          ─
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        //   Permissions Policy  - disable unused browser APIs          
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), magnetometer=(), gyroscope=()'
        );

        //   Content Security Policy                      ─
        // Tailored to Henkaten: uses CDN for Alpine.js, chart libs, font icons.
        // 'unsafe-inline' required for existing inline scripts/styles in Blade views.
        //
        // Environment strategy:
        //   local / development  → CSP allows all localhost:* ports (artisan serve)
        //   production + http:// → CSP locks to APP_URL only, NO https upgrade
        //   production + https:// → CSP locks to APP_URL, enables https upgrade + HSTS
        $appUrl = rtrim(config('app.url', 'http://localhost'), '/');
        $env = app()->environment();
        $isHttps = str_starts_with($appUrl, 'https://');

        // Build connect-src: dev allows all localhost ports, production locks to APP_URL
        $connectSources = "'self' {$appUrl}";
        if (in_array($env, ['local', 'development'])) {
            $connectSources .= ' http://localhost:* ws://localhost:* http://127.0.0.1:* ws://127.0.0.1:*';
        }

        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: blob:",
            "connect-src {$connectSources}",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ];

        // Only force HTTPS upgrade when the app is ACTUALLY served over HTTPS
        if ($isHttps) {
            $cspDirectives[] = "upgrade-insecure-requests";
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        //   HSTS  - only when APP_URL is https://               ─
        // Telling a browser to only use HTTPS on an HTTP-only server would
        // permanently break access. Only send HSTS when we're actually on HTTPS.
        if ($isHttps) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        //   Remove server fingerprinting headers                
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
