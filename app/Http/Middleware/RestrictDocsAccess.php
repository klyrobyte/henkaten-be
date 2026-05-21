<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * RestrictDocsAccess Middleware
 *
 * Guards the /docs (Scribe API documentation) route in production.
 *
 * Rules:
 *   1. SCRIBE_ENABLED=false in .env → always 404 (docs not served)
 *   2. Caller IP must be in the SCRIBE_ALLOWED_IPS allowlist (comma-separated)
 *      If SCRIBE_ALLOWED_IPS is empty, defaults to localhost only (127.0.0.1, ::1)
 *   3. User must be authenticated (auth middleware handles this separately)
 *
 * .env configuration:
 *   SCRIBE_ENABLED=false          ← production default
 *   SCRIBE_ENABLED=true           ← local/staging
 *   SCRIBE_ALLOWED_IPS=127.0.0.1,192.168.1.0/24
 *
 * Added: 2026-05-10 | Security hardening patch | @RizkyDaffy
 */
class RestrictDocsAccess
{
    public function handle(Request $request, Closure $next): mixed
    {
        // ── Gate 1: SCRIBE_ENABLED must be true ──────────────────────────────
        if (!config('app.scribe_enabled', false)) {
            abort(404);
        }

        // ── Gate 2: IP allowlist check ────────────────────────────────────────
        $allowedRaw = config('app.scribe_allowed_ips', '');
        $allowed = array_filter(array_map('trim', explode(',', $allowedRaw)));

        // Default to localhost-only when no list is configured
        if (empty($allowed)) {
            $allowed = ['127.0.0.1', '::1'];
        }

        if (in_array('*', $allowed)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        foreach ($allowed as $cidr) {
            if ($this->ipMatches($clientIp, $cidr)) {
                return $next($request);
            }
        }

        // IP not on the allowlist  - treat as 404 (not 403) to avoid enumeration
        abort(404);
    }

    /**
     * Check if an IP matches a CIDR block or exact IP.
     * Supports both IPv4 (with /prefix notation) and exact strings.
     */
    private function ipMatches(string $ip, string $cidr): bool
    {
        // Exact match (handles IPv6 and simple IPv4)
        if ($ip === $cidr) {
            return true;
        }

        // CIDR notation (IPv4 only)
        if (str_contains($cidr, '/')) {
            [$subnet, $prefix] = explode('/', $cidr, 2);
            $prefix = (int) $prefix;

            $subnetLong = ip2long($subnet);
            $ipLong = ip2long($ip);

            if ($subnetLong === false || $ipLong === false) {
                return false;
            }

            $mask = $prefix > 0 ? (~0 << (32 - $prefix)) : 0;

            return ($ipLong & $mask) === ($subnetLong & $mask);
        }

        return false;
    }
}
