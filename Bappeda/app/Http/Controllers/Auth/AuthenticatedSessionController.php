<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;    
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */

    protected function redirectToDashboard($user) {
        return match ($user->role_id) {
            // Sesuai: Route::name('admin.')->get('/dashboard', ...) -> 'admin.dashboard'
            1 => route('admin.dashboard'),

            // Sesuai: Route::name('inputer.')->get('/data', ...) -> 'inputer.index'
            // Anda juga bisa pakai 'inputer.dashboard' jika ingin ke dashboard inputer
            2 => route('inputer.dashboard'),

            // Karena di web.php Anda tidak memberi nama pada Route::get('/', ...),
            // maka kita gunakan url('/') saja agar aman.
            default => url('/'), 
        };
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->to(
            $this->redirectToDashboard(auth()->user())
        );

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
