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

        // =========================================================
        // 4. [BARU] QUERY UNTUK GRAFIK "PIN DATA" SELURUH INDIKATOR
        // =========================================================
        $indikators = \App\Models\Data::with('values')->where('id_user', $userId)->get();
        
        $allDataFormatted = [];
        foreach ($indikators as $dataset) {
            $rowObject = [
                'id_data'            => $dataset->id_data,
                'nama_indikator'     => $dataset->nama_indikator,
                'satuan'             => $dataset->satuan,
                'informasi_tambahan' => $dataset->informasi_tambahan,
            ];
            
            // Susun nilai per tahun menjadi format mendatar ke samping
            foreach ($dataset->values as $val) {
                $rowObject[$val->tahun] = $val->nilai;
            }
            $allDataFormatted[] = $rowObject;
        }
        // =========================================================

        return Inertia::render('Inputer/Dashboard', [
            'stats'              => $stats,
            'growthChart'        => $growthChart,
            'myRecentActivities' => $myRecentActivities,
            // [BARU] Lempar data ini agar dibaca oleh Chart.js di Vue
            'allData'            => $allDataFormatted, 
        ]);
    }
}