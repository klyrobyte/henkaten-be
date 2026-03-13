<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactoryShiftMiddleware
{
    /**
     * Middleware: batasi akses factory+shift untuk role TL / GL / Pengawas.
     *
     * Admin → tidak terpengaruh sama sekali, bisa akses semua.
     * TV    → tidak relevan (sudah ditangani TvRestrictMiddleware).
     *
     * Untuk non-admin dengan factory+shift yang di-assign:
     *  1. Paksa session factory & shift ke nilai user
     *  2. Blokir setContext yang mencoba ganti factory/shift
     *  3. Blokir request yang menyertakan factory/shift yg tidak cocok
     *
     * CATATAN PENTING: Normalisasi factory sebelum perbandingan karena
     * "Factory 3 & 4" bisa datang dari form sebagai "Factory 3 &amp; 4".
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Admin: full access — tidak dibatasi sama sekali
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Jika user tidak punya factory/shift (mis. tv, atau belum di-assign) → lewat saja
        if (!$user->factory || !$user->shift) {
            return $next($request);
        }

        $allowedFactory = $user->factory;
        $allowedShift   = $user->shift;

        // ── 1. Selalu sinkronkan session ke factory/shift milik user ──────────
        $request->session()->put('factory', $allowedFactory);
        $request->session()->put('shift',   $allowedShift);

        // ── 2. Blokir setContext yang mencoba ganti ke factory/shift lain ─────
        $routeName = $request->route()?->getName();
        if ($routeName === 'admin.context') {
            $reqFactory = $this->normalizeFactory($request->input('factory'));
            $reqShift   = $request->input('shift');

            if ($reqFactory !== $allowedFactory || $reqShift !== $allowedShift) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'error'   => 'access_denied',
                        'message' => 'Akses ditolak: Anda hanya bisa mengakses ' . $allowedFactory . ' Shift ' . $allowedShift . '.',
                    ], 403);
                }
                return back()->with('error', 'Akses ditolak: Anda hanya bisa mengakses ' . $allowedFactory . ' Shift ' . $allowedShift . '.');
            }
        }

        // ── 3. Blokir request yang menyertakan factory/shift yang tidak cocok ─
        // Normalisasi dulu sebelum dibandingkan (handle &amp; vs & dll.)
        $reqFactory = $request->input('factory');
        $reqShift   = $request->input('shift');

        if ($reqFactory !== null && $this->normalizeFactory($reqFactory) !== $allowedFactory) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error'   => 'access_denied',
                    'message' => 'Akses ditolak: factory tidak sesuai.',
                ], 403);
            }
            abort(403, 'Akses ditolak: factory tidak sesuai.');
        }

        if ($reqShift !== null && $reqShift !== $allowedShift) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error'   => 'access_denied',
                    'message' => 'Akses ditolak: shift tidak sesuai.',
                ], 403);
            }
            abort(403, 'Akses ditolak: shift tidak sesuai.');
        }

        return $next($request);
    }

    /**
     * Normalisasi factory name: decode HTML entities dan trim whitespace.
     * "Factory 3 &amp; 4" → "Factory 3 & 4"
     */
    private function normalizeFactory(?string $factory): string
    {
        return html_entity_decode(trim($factory ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
