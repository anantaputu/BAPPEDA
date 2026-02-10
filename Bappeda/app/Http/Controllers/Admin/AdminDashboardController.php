<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Import Model
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\Tema;
use App\Models\Bidang;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. STATISTIK KARTU (STATS)
        $stats = [
            'total_dataset' => Data::count(),
            'data_valid'    => DataUpload::where('status', 'valid')->count(),
            'total_visual'  => 0, // Bisa diisi logika real jika ada tabel visualisasi
            'total_org'     => Data::distinct('sumber')->count('sumber'),
        ];

        // 2. DATA CHART BAR (TEMA)
        $themesData = Tema::withCount('data')->get();
        $temaChart = [
            'labels' => $themesData->pluck('nama_tema'),
            'values' => $themesData->pluck('data_count'), 
        ];

        // 3. DATA CHART DOUGHNUT (BIDANG)
        $bidangData = Bidang::withCount('data')
            ->orderBy('data_count', 'desc')
            ->limit(5)
            ->get();

        $totalTop5 = $bidangData->sum('data_count');
        $totalAll = Data::count();
        $othersCount = $totalAll - $totalTop5;

        $bidangLabels = $bidangData->pluck('nama_bidang')->toArray();
        $bidangCounts = $bidangData->pluck('data_count')->toArray();

        if ($othersCount > 0) {
            $bidangLabels[] = 'Lainnya';
            $bidangCounts[] = $othersCount;
        }

        $bidangChart = [
            'labels' => $bidangLabels,
            'values' => $bidangCounts,
        ];

        // 4. DATA LIST (POPULER & TERBARU)
        // Helper function untuk format data list
        $mapDataset = function ($query) {
            return $query->with('tema')->limit(5)->get()->map(function ($item) {
                return [
                    'id'    => $item->id_data,
                    'title' => $item->nama_indikator,
                    'tags'  => ['XLSX', $item->tema->nama_tema ?? 'Umum'], 
                    'org'   => $item->sumber ?? 'Pemerintah',
                    'date'  => $item->created_at->diffForHumans(), // Tambahan info tanggal
                ];
            });
        };

        $datasets = [
            'popular' => $mapDataset(Data::inRandomOrder()), // Simulasi populer
            'latest'  => $mapDataset(Data::latest()),
        ];

        // 5. TOPIK
        $topics = Tema::limit(6)->get()->map(fn($t) => ['name' => $t->nama_tema]);

        $growthRaw = Data::select(
        DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
        DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(11)) // 12 bulan terakhir
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get()
        ->pluck('total', 'month');

    // Siapkan array kosong untuk 12 bulan agar grafik tidak loncat jika ada bulan kosong
    $growthLabels = [];
    $growthValues = [];
    
    for ($i = 11; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        $monthKey = $date->format('Y-m'); // Key untuk pencocokan: "2023-10"
        $labelName = $date->locale('id')->isoFormat('MMM Y'); // Label: "Okt 2023"
        
        $growthLabels[] = $labelName;
        // Ambil data dari query, jika tidak ada set 0
        $growthValues[] = $growthRaw[$monthKey] ?? 0;
    }

    $growthChart = [
        'labels' => $growthLabels,
        'values' => $growthValues
    ];

        // KIRIM SEMUA DATA KE VUE
        return Inertia::render('Admin/Dashboard', [
            'stats'       => $stats,
            'temaChart'   => $temaChart,
            'bidangChart' => $bidangChart,
            'datasets'    => $datasets,
            'topics'      => $topics,
            'growthChart' => $growthChart
        ]);



    }
}