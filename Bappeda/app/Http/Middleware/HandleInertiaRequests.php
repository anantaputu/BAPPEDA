<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'username' => $request->user()->username,
                    'nama_depan' => $request->user()->nama_depan, // Dari migration
                    'nama_belakang' => $request->user()->nama_belakang, // Dari migration
                    'role_id' => $request->user()->role_id, // Penting untuk cek ID
                    
                    // Ini yang dibaca oleh "const namaRole" di Vue kamu
                    // Kita ambil nama_role dari tabel roles
                    'role' => $request->user()->role?->nama_role, 
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'message' => $request->session()->get('message'),
            ],
        ];
    }
}
