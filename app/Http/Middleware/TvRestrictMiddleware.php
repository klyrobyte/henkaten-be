<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware ini di-append ke SEMUA route dalam group auth.
 * Khusus untuk role 'tv': hanya boleh akses tv picker & tv board.
 * Semua route lain → redirect ke tv.picker.
 * Fixed by Rizky
 */
class TvRestrictMiddleware
{
    // Route yang boleh diakses role tv
    private const ALLOWED = [
        'admin.tv.picker',
        'admin.tv',
        'admin.status',   // diperlukan TV board untuk auto-refresh JSON
        'logout',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && Auth::user()->role === 'tv') {
            $routeName = $request->route()?->getName();
            if (!in_array($routeName, self::ALLOWED)) {
                return redirect()->route('admin.tv.picker');
            }
        }

        return $next($request);
    }
}
