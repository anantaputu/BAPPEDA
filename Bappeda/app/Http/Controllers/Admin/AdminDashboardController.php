<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Ganti 'Admin/Dashboard' sesuai lokasi file Vue Dashboard Admin Anda
        return inertia('Admin/Dashboard'); 
    }
}