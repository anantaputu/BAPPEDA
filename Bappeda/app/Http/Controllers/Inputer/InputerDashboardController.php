<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DataUpload; 

class InputerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. STATISTIK (Hanya milik user login)
        $stats = [
            'total_input'   => DataUpload::where('id_user', $userId)->count(),
            'data_approved' => DataUpload::where('id_user', $userId)->where('status', 'valid')->count(),
            'data_pending'  => DataUpload::where('id_user', $userId)
                                ->whereIn('status', ['processing', 'pending', 'draft'])
                                ->count(),
            'data_rejected' => DataUpload::where('id_user', $userId)->where('status', 'rejected')->count(),
        ];

        // 2. LOGIKA GROWTH CHART (Pola Admin)
        // Gunakan DATE_FORMAT(created_at, '%Y-%m') jika Anda menggunakan MySQL
        $growthRaw = DataUpload::select(
            DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"), 
            DB::raw('COUNT(*) as total')
        )
        ->where('id_user', $userId) 
        ->where('created_at', '>=', Carbon::now()->subMonths(11))
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get()
        ->pluck('total', 'month');

        $growthLabels = [];
        $growthValues = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m'); 
            $labelName = $date->locale('id')->isoFormat('MMM Y'); 
            
            $growthLabels[] = $labelName;
            $growthValues[] = $growthRaw[$monthKey] ?? 0;
        }

        // Ini adalah object yang akan diterima oleh GrowthLineChart
        $growthChart = [
            'labels' => $growthLabels,
            'values' => $growthValues
        ];

        // 3. LOG AKTIVITAS TERBARU
        $myRecentActivities = DataUpload::with(['data'])
            ->where('id_user', $userId)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id_upload,
                    'user' => Auth::user()->name,
                    'action' => 'Mengunggah Data',
                    'target' => $log->data->nama_indikator ?? 'Indikator tidak diketahui',
                    'status' => $log->status,
                    'time' => $log->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('Inputer/Dashboard', [
            'stats' => $stats,
            'growthChart' => $growthChart,
            'myRecentActivities' => $myRecentActivities,
        ]);
    }
}