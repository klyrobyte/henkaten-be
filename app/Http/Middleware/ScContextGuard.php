<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Factory;

class ScContextGuard
{
    /**
     * Enforce Service Center (SC) data isolation.
     *
     * Isolation contract:
     *  - Non-superadmin users MUST have a non-null sc_id. If missing, abort.
     *  - Session factory must belong to the user's active SC.
     *  - Query-string factory parameter must belong to the user's active SC.
     *  - Route model bindings with sc_id must match the user's active SC.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $activeScId = $user->getActiveScId();

        // 0. Guard: non-superadmin users must have a valid sc_id (defence-in-depth)
        //    A null sc_id would cause every where('sc_id', ...) query to silently
        //    return wrong results, potentially leaking cross-SC data.
        if (!$user->isSuperAdmin() && $activeScId === 0) {
            abort(403, 'Invalid Service Center context. Please contact an administrator.');
        }

        // 1. Clean/Force reset session factory if it does not belong to active SC
        if ($request->hasSession()) {
            $sessionFactory = $request->session()->get('factory');
            if ($sessionFactory) {
                $exists = Factory::where('sc_id', $activeScId)->where('name', $sessionFactory)->exists();
                if (!$exists) {
                    $request->session()->forget('factory');
                }
            }
        }

        // 2. Guard Request factory parameter if it is passed in query/input
        $reqFactoryName = $request->input('factory') ?? $request->query('factory');
        if ($reqFactoryName && $request->route() && $request->route()->getName() !== 'admin.set-sc') {
            $exists = Factory::where('sc_id', $activeScId)->where('name', $reqFactoryName)->exists();
            if (!$exists) {
                abort(403, 'Unauthorized factory context.');
            }
        }

        // 3. Guard Route Model Bindings to ensure entities belong to the active SC
        if ($request->route()) {
            foreach ($request->route()->parameters() as $param) {
                if (is_object($param)) {
                    if (isset($param->sc_id)) {
                        if (!$user->canAccessSc((int) $param->sc_id)) {
                            abort(403, 'Unauthorized Service Center access.');
                        }
                    } elseif ($param instanceof \App\Models\Section) {
                        $factory = $param->factory;
                        if ($factory && !$user->canAccessSc((int) $factory->sc_id)) {
                            abort(403, 'Unauthorized Service Center access.');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
