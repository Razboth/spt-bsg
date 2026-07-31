<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next)
    {
        $segments = $request->segments();

        if (count($segments) >= 2) {
            $module = $segments[0];
            $menu = $segments[1];
            $permission = "view $module/$menu";
        } elseif (count($segments) === 1) {
            $module = $segments[0];
            $permission = "view $module";
        } else {
            return $next($request);
        }

        if (!$request->user()?->can($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
