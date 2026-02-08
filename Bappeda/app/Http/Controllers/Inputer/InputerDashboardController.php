<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\DataUpload; // Pastikan Model DataUpload di-import

class InputerDashboardController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Inputer
     */
    public function index()
    {
        // 1. Ambil ID User yang sedang login
        $userId = Auth::id();

        // 2. Ambil Statistik Khusus User Ini
        $stats = [
            // Hitung semua upload milik user ini
            'total_upload' => DataUpload::where('id_user', $userId)->count(),
            
            // Hitung data yang statusnya 'valid'
            'valid'        => DataUpload::where('id_user', $userId)->where('status', 'valid')->count(),
            
            // Hitung data yang statusnya masih 'processing', 'pending', atau 'draft'
            'pending'      => DataUpload::where('id_user', $userId)
                                ->whereIn('status', ['processing', 'pending', 'draft'])
                                ->count(),
        ];

        // 3. Ambil 5 Riwayat Upload Terakhir
        $recentUploads = DataUpload::with('data') // Eager load relasi ke tabel 'data' (indikator)
            ->where('id_user', $userId)
            ->latest() // Urutkan dari yang terbaru
            ->limit(5)
            ->get();

        // 4. Kirim data ke Tampilan Vue (Inputer/Dashboard.vue)
        return Inertia::render('Inputer/Dashboard', [
            'stats' => $stats,
            'recentUploads' => $recentUploads
        ]);
    }

    // ... method create, store, dll biarkan kosong atau hapus jika tidak dipakai
}