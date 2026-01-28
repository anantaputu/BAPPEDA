<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AutoLoginAdmin
{
    public function handle($request, Closure $next)
    {
        if (app()->environment('local') && !Auth::check()) {
            $admin = User::where('email', 'admin@bappeda.go.id')->first();

            if ($admin) {
                Auth::login($admin);
            }
        }

        return $next($request);
    }
}
