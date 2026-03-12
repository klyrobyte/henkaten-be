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
     *  1. Paksa session factory & shift ke nilai user (agar dashboard selalu menampilkan miliknya)
     *  2. Blokir POST /admin/context yang mencoba ganti factory/shift ke value lain
     *  3. Blokir request JSON/form yang menyertakan factory/shift yang tidak cocok
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

        // Jika user tidak punya factory/shift (mis. tv, atau non-admin belum di-assign) → lewat saja
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
            $reqFactory = $request->input('factory');
            $reqShift   = $request->input('shift');

            if ($reqFactory !== $allowedFactory || $reqShift !== $allowedShift) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Akses ditolak: Anda hanya bisa mengakses ' . $allowedFactory . ' Shift ' . $allowedShift . '.'], 403);
                }
                // Abaikan permintaan ganti context, kembali tanpa ubah session
                return back()->with('error', 'Anda hanya bisa mengakses ' . $allowedFactory . ' Shift ' . $allowedShift . '.');
            }
        }

        // ── 3. Blokir request yang menyertakan factory/shift yang tidak cocok ─
        // Hanya cek jika ada parameter factory atau shift di request
        $reqFactory = $request->input('factory');
        $reqShift   = $request->input('shift');

        if ($reqFactory !== null && $reqFactory !== $allowedFactory) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akses ditolak: factory tidak sesuai.'], 403);
            }
            abort(403, 'Akses ditolak: factory tidak sesuai.');
        }

        if ($reqShift !== null && $reqShift !== $allowedShift) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akses ditolak: shift tidak sesuai.'], 403);
            }
            abort(403, 'Akses ditolak: shift tidak sesuai.');
        }

        return $next($request);
    }
}
