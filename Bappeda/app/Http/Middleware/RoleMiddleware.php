<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    // RoleMiddleware.php
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            abort(401);
        }

        $userRole = strtolower(auth()->user()->role?->nama_role);

        if ($userRole !== strtolower($role)) {
            abort(403);
        }


        return $next($request);
    }
}
