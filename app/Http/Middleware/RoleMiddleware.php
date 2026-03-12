<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Roles (dari paling tinggi):
     *   admin    → full access + user management
     *   tl       → Team Leader
     *   gl       → Group Leader
     *   pengawas → Pengawas/Supervisor
     *   tv       → TV Only — hanya bisa akses /admin/tv
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

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
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}