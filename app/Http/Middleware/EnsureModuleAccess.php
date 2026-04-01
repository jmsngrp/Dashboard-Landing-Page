<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string $module)
    {
        $user = $request->user();

        if (!$user || !in_array($module, $user->accessibleModules(), true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Module access denied'], 403);
            }
            abort(403, 'You do not have access to this module.');
        }

        // Page-level access check: extract page slug from route name
        $routeName = $request->route()?->getName();
        if ($routeName && str_starts_with($routeName, $module . '.')) {
            $page = substr($routeName, strlen($module) + 1);
            if ($page && !$user->canAccessPage($module, $page)) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Page access denied'], 403);
                }
                abort(403, 'You do not have access to this page.');
            }
        }

        return $next($request);
    }
}
