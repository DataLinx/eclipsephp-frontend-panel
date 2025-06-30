<?php

namespace Eclipse\Frontend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectAfterLogin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('filament.frontend.auth.login')) {
            session()->put('url.intended', '/');
        }
        
        return $next($request);
    }
}