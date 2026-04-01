<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account is deactivated'], 403);
            }
            return redirect()->route('login')->withErrors(['email' => 'Account is deactivated.']);
        }

        return $next($request);
    }
}
