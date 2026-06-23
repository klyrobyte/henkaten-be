<?php

namespace App\Services;

use App\Models\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * ══ ScContext — Multi-Tenant SC Resolution Service  =>═══════
 *
 * Single source of truth for resolving the active Service Center (SC) context.
 *
 * WHY THIS EXISTS
 *        ─
 * Two competing patterns existed in the codebase:
 *   ❌  $user->sc_id ?? 1              → bypasses SuperAdmin session switching
 *   ✅  $user->getActiveScId()         → SC-aware, respects SA context switch
 *
 * This service standardises all SC resolution through one call: ScContext::id().
 * It also eliminates hardcoded 'Factory 2' fallbacks via ScContext::firstFactory().
 *
 * USAGE
 *   ─
 *   $scId   = ScContext::id();
 *   $first  = ScContext::firstFactory();
 *   $scoped = ScContext::resolveFactory($requested, $user);
 *
 * @author Rizky Daffy (architecture)
 */
class ScContext
{
    //  ─ SC Resolution                             

    /**
     * Resolve the currently active SC ID.
     *
     * For regular users  → their own $user->sc_id.
     * For SuperAdmins    → the session-switched active_sc_id, or their own sc_id
     *                      as the final fallback.
     *
     * The fallback integer (1) is only reached when called outside of an
     * authenticated context (e.g., console commands, unauthenticated API probes).
     */
    public static function id(): int
    {
        if (Auth::check()) {
            return Auth::user()->getActiveScId();
        }

        return 1;
    }

    //  ─ Factory Resolution                          ─

    /**
     * Return the name of the first factory for the currently active SC,
     * ordered by order_index. Returns null if the SC has zero factories.
     *
     * Replaces all hardcoded 'Factory 2' fallbacks.
     */
    public static function firstFactory(): ?string
    {
        return Factory::where('sc_id', static::id())
            ->orderBy('order_index')
            ->value('name');
    }

    /**
     * Return the list of factory names this user is allowed to access.
     *
     * SuperAdmins and admins with no factory restriction → empty array (= all allowed).
     * Scoped users (tl, gl, pengawas, etc.)             → their assigned factories.
     */
    public static function allowedFactories(User $user): array
    {
        if ($user->isSuperAdmin() || empty($user->factory)) {
            return [];
        }

        return (array) $user->factory;
    }

    /**
     * Full factory resolution chain for request handlers.
     *
     * Priority:
     *   1. Session factory (if valid for this SC + allowed scope)
     *   2. User's assigned factory (first element, safely)
     *   3. First factory in DB for this SC
     *   4. First in $allowedFactories (last resort before hard stop)
     *
     * Aborts with 403 if no factory can be resolved at all — prevents
     * null-data cascade queries on unconfigured SCs.
     *
     * @param  string|null  $sessionFactory  Value from session()->get('factory')
     * @param  User         $user
     * @return string       A valid, non-empty factory name
     */
    public static function resolveFactory(?string $sessionFactory, User $user): string
    {
        $allowed = static::allowedFactories($user);

        // Sanitise the session value — must be in the allowed list if user is scoped
        if ($sessionFactory && !empty($allowed) && !in_array($sessionFactory, $allowed)) {
            $sessionFactory = null;
        }

        // Snapshot the cast property into a plain PHP value — never call reset()
        // or pass-by-ref on an overloaded Eloquent property (causes ErrorException).
        $userFactoryRaw = $user->factory;
        $userFirstFactory = is_array($userFactoryRaw)
            ? (array_values($userFactoryRaw)[0] ?? null)
            : $userFactoryRaw;

        $resolved = $sessionFactory
            ?? $userFirstFactory
            ?? static::firstFactory()
            ?? (!empty($allowed) ? $allowed[0] : null);

        // Final isolation guard — ensure the resolved factory is in the allowed scope
        if (!empty($allowed) && $resolved !== null && !in_array($resolved, $allowed)) {
            $resolved = $allowed[0];
        }

        // Strict failsafe — block access for SCs with zero factory configuration
        // rather than cascading null-data queries downstream.
        if (empty($resolved)) {
            abort(403, 'No factory configuration found for this Service Center. Please contact your administrator.');
        }

        return $resolved;
    }

    /**
     * Resolve a factory from a raw request parameter (e.g., query string or
     * form input), enforcing SC isolation and the user's allowed scope.
     *
     * Falls back to resolveFactory() if the requested value is not provided
     * or is not permitted.
     *
     * @param  string|null  $requested      Raw value from $request->get('factory')
     * @param  string|null  $sessionFactory Value from session()->get('factory')
     * @param  User         $user
     * @return string
     */
    public static function resolveRequestFactory(?string $requested, ?string $sessionFactory, User $user): string
    {
        $allowed = static::allowedFactories($user);

        // Validate the incoming parameter against the allowed scope
        if ($requested && (empty($allowed) || in_array($requested, $allowed))) {
            return $requested;
        }

        // Fall through to the full session-based resolution chain
        return static::resolveFactory($sessionFactory, $user);
    }
}
