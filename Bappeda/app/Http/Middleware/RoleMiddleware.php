<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  (Bisa berupa "admin" atau "admin|inputer")
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        // 1. Cek Login
        if (!Auth::check()) {
            // Lebih baik redirect ke login daripada abort 401
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. AMBIL ROLE USER (LOGIKA PINTAR / SMART FETCH)
        // Kita cari role di berbagai kemungkinan tempat (Relasi atau Kolom Biasa)
        $dbRole = null;

        // Cek A: Apakah 'role' adalah Relasi (Object)?
        if (isset($user->role) && (is_object($user->role) || is_array($user->role))) {
            $dbRole = $user->role->nama_role ?? $user->role['nama_role'] ?? null;
        }

        // Cek B: Jika Cek A gagal, ambil dari kolom string biasa
        if (!$dbRole) {
            $dbRole = $user->nama_role ?? $user->NAMA_ROLE ?? $user->role;
        }

        // Jika role masih tidak ketemu juga
        if (!$dbRole) {
            abort(403, 'Akses Ditolak. Role User tidak ditemukan di database.');
        }

        // 3. NORMALISASI (Kecilkan huruf semua)
        $userRole = strtolower((string)$dbRole);

        // 4. PECAH STRING PARAMETER (Support pipa '|')
        // Mengubah "admin|inputer" menjadi array ["admin", "inputer"]
        $allowedRoles = explode('|', $roles);
        $allowedRoles = array_map('strtolower', $allowedRoles);

        // 5. CEK APAKAH COCOK
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // 6. JIKA GAGAL
        abort(403, "Akses Ditolak. Role Anda ($userRole) tidak memiliki izin masuk.");
    }
}