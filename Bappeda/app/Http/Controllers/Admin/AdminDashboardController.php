<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Model yang diimpor
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\Tema;
use App\Models\Bidang;
use App\Models\User; // Tambahkan ini

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. STATISTIK (Dinamis dari DB)
        $stats = [
            'total_dataset' => Data::count(),
            'data_valid'    => DataUpload::where('status', 'valid')->count(),
            'data_pending'  => DataUpload::where('status', 'pending')->count(), // Tambahan info pending
            'total_org'     => Data::distinct('sumber')->count('sumber'),
            
            // STATISTIK USER (Sesuai Model User Anda)
            'total_user'    => User::count(),
            'user_active'   => User::where('status_aktif', true)->count(),
            'user_inactive' => User::where('status_aktif', false)->count(),
        ];

        // 2. DATA CHART BAR (TEMA)
        $themesData = Tema::withCount('data')->get();
        $temaChart = [
            'labels' => $themesData->pluck('nama_tema'),
            'values' => $themesData->pluck('data_count'), 
        ];

        // 3. DATA CHART DOUGHNUT (BIDANG) - Top 5 Bidang
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
        $mapDataset = function ($query) {
            return $query->with(['tema', 'bidang'])->limit(5)->get()->map(function ($item) {
                return [
                    'id'    => $item->id_data,
                    'title' => $item->nama_indikator,
                    // Mengambil label tema secara dinamis
                    'tags'  => [$item->tema->nama_tema ?? 'Umum', $item->tahun], 
                    'org'   => $item->sumber ?? 'Bappeda NTB',
                    'date'  => $item->created_at->diffForHumans(),
                ];
            });
        };

        $datasets = [
            'popular' => $mapDataset(Data::inRandomOrder()), 
            'latest'  => $mapDataset(Data::latest()),
        ];

        // 5. TOPIK / KATEGORI (Dinamis dari Tabel Tema)
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

        // Log Aktivitas Terbaru (Data Upload)
        $recentActivities = DataUpload::with(['user', 'data'])
        ->latest('created_at')
        ->limit(8)
        ->get()
        ->map(function ($log) {
            return [
                'id' => $log->id_upload,
                'user' => $log->user->name ?? 'System',
                'action' => 'Mengunggah Data', // Secara default dari data_uploads
                'target' => $log->data->nama_indikator ?? 'Indikator tidak diketahui',
                'status' => $log->status, // pending, valid, atau rejected
                'time' => $log->created_at->diffForHumans(),
                'date_raw' => $log->created_at->format('d M Y, H:i')
            ];
        });

        return Inertia::render('Admin/Dashboard', [
            'stats'       => $stats,
            'temaChart'   => $temaChart,
            'bidangChart' => $bidangChart,
            'datasets'    => $datasets,
            'topics'      => $topics,
            'growthChart' => $growthChart,
            'recentActivities' => $recentActivities,
        ]);



    }
}