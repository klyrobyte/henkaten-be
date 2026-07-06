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
        $reqFactory = $request->input('factory') ?? $request->query('factory');
        if ($reqFactory && $request->route() && $request->route()->getName() !== 'admin.set-sc') {
            $factoriesToCheck = is_array($reqFactory) ? $reqFactory : [$reqFactory];
            $decodedNames = [];
            foreach ($factoriesToCheck as $fName) {
                if (is_string($fName)) {
                    $decodedNames[] = html_entity_decode($fName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
            
            if (!empty($decodedNames)) {
                $existsCount = Factory::where('sc_id', $activeScId)
                    ->whereIn('name', $decodedNames)
                    ->count();
                    
                if ($existsCount !== count(array_unique($decodedNames))) {
                    abort(403, 'Unauthorized factory context.');
                }
            }
        }

        // 3. Guard Route Model Bindings to ensure entities belong to the active SC
        if ($request->route()) {
            foreach ($request->route()->parameters() as $param) {
                if (is_object($param)) {
                    if (isset($param->sc_id)) {
                        if ((int)$param->sc_id !== $activeScId) {
                            abort(403, 'Unauthorized Service Center access.');
                        }
                    } elseif ($param instanceof \App\Models\Section) {
                        $factory = $param->factory;
                        if ($factory && (int)$factory->sc_id !== $activeScId) {
                            abort(403, 'Unauthorized Service Center access.');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
