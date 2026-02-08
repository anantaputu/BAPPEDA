<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Inputer\InputerDashboardController;

class OverviewController extends Controller
{
  
     public function index()
    {
        $user = Auth::user();
        
        // Ambil Role
        $roleData = $user->role; 
        $roleName = is_object($roleData) || is_array($roleData) 
            ? ($roleData->nama_role ?? $roleData['nama_role'] ?? null)
            : ($user->nama_role ?? $user->role);
        
        $role = strtolower((string)$roleName);

        // --- HANYA REDIRECT, JANGAN RENDER VIEW DI SINI ---
        
        if ($role === 'inputer') {
            // Panggil Controller Inputer yang ASLI
            return app(InputerDashboardController::class)->index();
        }

        if ($role === 'admin' || $role === 'admin super') {
            // Panggil Controller Admin yang ASLI
            return app(AdminDashboardController::class)->index();
        } 

        abort(403);
    }
}