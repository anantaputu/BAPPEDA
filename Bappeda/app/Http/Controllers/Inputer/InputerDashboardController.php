<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DataUpload; 
use App\Models\Bookmark;
use App\Models\Data;

class InputerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. STATISTIK (Angka untuk 6 Kartu di Atas Dashboard)
        $stats = [
            'total_indikator'   => \App\Models\Data::count(), 
            'cakupan_tema'      => \App\Models\Tema::count(),
            'total_urusan'      => \App\Models\Urusan::count(),
            'total_bidang'      => \App\Models\Bidang::count(),
            'variasi_frekuensi' => \App\Models\Frekuensi::count(),
            'sumber_data'       => \App\Models\Data::whereNotNull('sumber')->distinct('sumber')->count('sumber'), 
        ];

        // 2. LOGIKA GROWTH CHART (Grafik Upload)
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

        // 3. LOG AKTIVITAS TERBARU (Untuk Dashboard)
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

        // 4. DATA SPREADSHEET (Untuk Tampilan Grafik di Dashboard)
        $indikators = \App\Models\Data::with('values')->where('id_user', $userId)->get();
        $allDataFormatted = [];
        foreach ($indikators as $dataset) {
            $rowObject = [
                'id_data'            => $dataset->id_data,
                'nama_indikator'     => $dataset->nama_indikator,
                'satuan'             => $dataset->satuan,
                'informasi_tambahan' => $dataset->informasi_tambahan,
                'tahun_terbit' => $dataset->tahun_terbit,
            ];
            foreach ($dataset->values as $val) {
                $rowObject[$val->tahun] = $val->nilai;
            }
            $allDataFormatted[] = $rowObject;
        }

        // 5. PINNED DATA (Data Favorit)
        $pinnedDatasets = Bookmark::join('data', 'bookmark.dataset_id', '=', 'data.id_data')
            ->where('bookmark.user_id', $userId)
            ->select('data.id_data', 'data.nama_indikator', 'data.tahun_terbit') 
            ->get();
    
        // RETURN KE HALAMAN DASHBOARD (Bukan Index Data)
        return Inertia::render('Inputer/Dashboard', [
            'stats'              => $stats,
            'growthChart'        => $growthChart,
            'myRecentActivities' => $myRecentActivities,
            'pinnedData'         => $pinnedDatasets,
            'allData'            => $allDataFormatted, 
        ]);
    }
}