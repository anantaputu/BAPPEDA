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
use App\Models\DataValue;


class InputerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. STATISTIK (KHUSUS DATA MILIK INPUTER LOGIN)
        $myDataQuery = Data::query()->where('id_user', $userId);
        $totalIndikator = (clone $myDataQuery)->count();

        $stats = [
            'total_indikator'   => $totalIndikator,
            'total_upload'      => DataUpload::where('id_user', $userId)->count(),
            'total_nilai'       => DataValue::whereHas('data', fn ($q) => $q->where('id_user', $userId))->count(),
            'cakupan_tema'      => (clone $myDataQuery)->whereNotNull('id_tema')->distinct('id_tema')->count('id_tema'),
            'total_urusan'      => (clone $myDataQuery)->whereNotNull('id_urusan')->distinct('id_urusan')->count('id_urusan'),
            'total_bidang'      => (clone $myDataQuery)->whereNotNull('id_bidang')->distinct('id_bidang')->count('id_bidang'),
            'variasi_frekuensi' => (clone $myDataQuery)->whereNotNull('id_frekuensi')->distinct('id_frekuensi')->count('id_frekuensi'),
            'sumber_data'       => (clone $myDataQuery)->whereNotNull('sumber')->where('sumber', '!=', '')->distinct('sumber')->count('sumber'),
            'input_bulan_ini'   => (clone $myDataQuery)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'upload_bulan_ini'  => DataUpload::where('id_user', $userId)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
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
                    'action' => 'Menyimpan Data',
                    'target' => $log->data->nama_data ?? 'Indikator tidak diketahui',
                    'time' => $log->created_at->diffForHumans(),
                ];
            });

        // 4. DATA SPREADSHEET (Untuk Tampilan Grafik di Dashboard)
        $indikators = Data::with(['values', 'tema', 'urusan', 'bidang', 'frekuensi'])
            ->where('id_user', $userId)
            ->get();
        $allDataFormatted = [];
        foreach ($indikators as $dataset) {
            $rowObject = [
                'id_data'            => $dataset->id_data,
                'nama_data'          => $dataset->nama_data,
                'satuan'             => $dataset->satuan,
                'informasi_tambahan' => $dataset->informasi_tambahan,
                'tahun_terbit'       => $dataset->tahun_terbit,
            ];
            foreach ($dataset->values as $val) {
                $rowObject[$val->tahun] = $val->nilai;
            }
            $allDataFormatted[] = $rowObject;
        }

        // 5. DISTRIBUSI DATA (KHUSUS INPUTER LOGIN)
        $temaDistribution = Data::query()
            ->leftJoin('tema', 'data.id_tema', '=', 'tema.id_tema')
            ->selectRaw("COALESCE(tema.nama_tema, 'Tanpa Tema') as label, COUNT(*) as total")
            ->where('data.id_user', $userId)
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $frekuensiDistribution = Data::query()
            ->leftJoin('frekuensi', 'data.id_frekuensi', '=', 'frekuensi.id_frekuensi')
            ->selectRaw("COALESCE(frekuensi.nama_frekuensi, 'Tanpa Frekuensi') as label, COUNT(*) as total")
            ->where('data.id_user', $userId)
            ->groupBy('label')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // 6. DATA TERBARU MILIK INPUTER LOGIN
        $recentMyData = Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])
            ->withCount('values')
            ->where('id_user', $userId)
            ->latest('updated_at')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'id_data' => $item->id_data,
                    'nama_data' => $item->nama_data,
                    'tema' => $item->tema->nama_tema ?? '-',
                    'urusan' => $item->urusan->nama_urusan ?? '-',
                    'bidang' => $item->bidang->nama_bidang ?? '-',
                    'frekuensi' => $item->frekuensi->nama_frekuensi ?? '-',
                    'satuan' => $item->satuan ?? '-',
                    'tahun_terbit' => $item->tahun_terbit,
                    'values_count' => $item->values_count,
                    'updated_at' => $item->updated_at?->diffForHumans() ?? '-',
                ];
            });

        // 5. PINNED DATA (Data Favorit)
        $pinnedDatasets = Bookmark::join('data', 'bookmark.dataset_id', '=', 'data.id_data')
            ->where('bookmark.user_id', $userId)
            ->where('data.id_user', $userId)
            ->select('data.id_data', 'data.nama_data', 'data.tahun_terbit') 
            ->get();
    
        // RETURN KE HALAMAN DASHBOARD (Bukan Index Data)
        return Inertia::render('Inputer/Dashboard', [
            'stats'              => $stats,
            'growthChart'        => $growthChart,
            'myRecentActivities' => $myRecentActivities,
            'pinnedData'         => $pinnedDatasets,
            'allData'            => $allDataFormatted, 
            'temaDistribution'   => [
                'labels' => $temaDistribution->pluck('label'),
                'values' => $temaDistribution->pluck('total'),
            ],
            'frekuensiDistribution' => [
                'labels' => $frekuensiDistribution->pluck('label'),
                'values' => $frekuensiDistribution->pluck('total'),
            ],
            'recentMyData'       => $recentMyData,
        ]);
    }
}
