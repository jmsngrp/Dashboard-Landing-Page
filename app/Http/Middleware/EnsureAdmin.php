<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Admin access required'], 403);
            }
            abort(403, 'Admin access required');
        }

        // Eager-load organization and compute super-admin flag once
        $request->user()->loadMissing('organization');
        $isSuperAdmin = (bool) $request->user()->organization->is_super_admin;

        // Stash on request for controller use
        $request->attributes->set('isSuperAdmin', $isSuperAdmin);

        // Share to all views rendered in this request
        view()->share('isSuperAdmin', $isSuperAdmin);
        view()->share('adminOrgId', $request->user()->organization_id);

        return $next($request);
    }
}
