<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Roles (dari paling tinggi):
     *   superadmin → full access to everything
     *   admin    → access scoped to assigned factories
     *   tl       → Team Leader
     *   gl       → Group Leader
     *   pengawas → Pengawas/Supervisor
     *   tv       → TV Only  - hanya bisa akses /admin/tv
     *   develop by rizky daffy
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // Superadmin bypasses all role checks
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        // Role tv hanya boleh akses route admin.tv dan admin.status (untuk auto-refresh)
        // Jika coba akses halaman lain → redirect ke TV picker
        if ($userRole === 'tv') {
            $allowedRoutes = ['admin.tv', 'admin.status', 'admin.tv.picker', 'logout'];
            if (!in_array($request->route()?->getName(), $allowedRoutes)) {
                return redirect()->route('admin.tv.picker');
            }
        }

        // Jika tidak ada role yang disyaratkan, izinkan semua authenticated user
        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($userRole, $roles)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }
            // Redirect ke dashboard  - halaman seolah tidak ada untuk role ini
            return redirect()->route('admin.dashboard')
                ->with('toast_error', 'Halaman tidak tersedia untuk role Anda.');
        }

        return $next($request);
    }
}
