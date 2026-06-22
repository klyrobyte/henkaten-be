<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Max login attempts before lockout (Task 5 — Login Rate Limiter)
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in seconds (60s = 1 menit)
     */
    private const DECAY_SECONDS = 60;

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses login
     *
     * PENTING: Auth::attempt() harus menerima key yang SAMA dengan
     * getAuthIdentifierName() di User model, yaitu 'username'.
     * Laravel's EloquentUserProvider::retrieveByCredentials() akan
     * query WHERE username = ? secara otomatis.
     *
     * Task 5: Rate-limited to 5 attempts per IP per 60 seconds.
     * Key is hashed (SHA-256) so raw IP is never stored in cache.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // ── TASK 5: Rate Limiter ─────────────────────────────────────────────
        // Use SHA-256 of IP so the raw IP address is never stored in cache keys
        $rateLimiterKey = 'login|' . hash('sha256', $request->ip());

        if (RateLimiter::tooManyAttempts($rateLimiterKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimiterKey);
            Log::warning('[LoginRateLimiter] Percobaan login diblokir — terlalu banyak upaya', [
                'ip_hash'  => hash('sha256', $request->ip()),
                'username' => $request->input('username'),
                'retry_in' => $seconds,
            ]);

            // Return 429 with Indonesian message for both web (redirect) and API (JSON)
            $message = "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.";
            if ($request->expectsJson()) {
                return response()->json([
                    'error'      => $message,
                    'retryAfter' => $seconds,
                ], 429);
            }

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "🔒 {$message}"])
                ->withHeaders(['Retry-After' => $seconds]);
        }

        $credentials = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // ── Successful login: clear rate limit counter ───────────────────
            RateLimiter::clear($rateLimiterKey);
            $request->session()->regenerate();

            // Seed factory/shift context so the dashboard opens on the correct factory.
            // - For role-scoped users (gl, tl, pengawas), always use their assigned factory.
            // - For admin/tv, only set if session doesn't already have a value.
            $user = Auth::user();
            if ($user->factory && !in_array($user->role, ['admin'])) {
                $request->session()->put('factory', is_array($user->factory) ? ($user->factory[0] ?? null) : $user->factory);
                if ($user->shift) {
                    $request->session()->put('shift', $user->shift);
                }
            } elseif (!$request->session()->has('factory') && $user->factory) {
                // Admin logging in fresh  - seed from their profile factory if set
                $request->session()->put('factory', is_array($user->factory) ? ($user->factory[0] ?? null) : $user->factory);
            } elseif (!$request->session()->has('factory')) {
                // Admin with no assigned factory  - use the first factory in DB
                $firstFactory = \App\Models\Factory::orderBy('order_index')->first();
                if ($firstFactory) {
                    $request->session()->put('factory', $firstFactory->name);
                }
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        // ── Failed login: increment rate limiter ─────────────────────────────
        RateLimiter::hit($rateLimiterKey, self::DECAY_SECONDS);
        $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($rateLimiterKey);
        $remainingMsg = $remaining > 0 ? " (sisa {$remaining} percobaan)" : '';

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => "❌ Username atau password salah!{$remainingMsg}"]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
