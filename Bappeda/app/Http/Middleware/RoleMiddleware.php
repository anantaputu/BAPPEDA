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

        // Perbaikan: Ambil string 'nama_role' dari relasi, bukan objek user->role
        $userRole = auth()->user()->role?->nama_role;

        if ($userRole !== $role) {
            abort(403);
        }

        return $next($request);
    }
}
