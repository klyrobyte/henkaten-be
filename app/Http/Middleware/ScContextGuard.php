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
        // Decode HTML entities (e.g. "Factory 3 &amp; 4" → "Factory 3 & 4") sent by
        // Blade-rendered JS variables that may be HTML-encoded.
        if ($reqFactoryName) {
            $reqFactoryName = html_entity_decode($reqFactoryName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

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
                        if ((int) $param->sc_id !== $activeScId) {
                            abort(403, 'Unauthorized Service Center access.');
                        }
                    } elseif ($param instanceof \App\Models\Section) {
                        $factory = $param->factory;
                        if ($factory && (int) $factory->sc_id !== $activeScId) {
                            abort(403, 'Unauthorized Service Center access.');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
