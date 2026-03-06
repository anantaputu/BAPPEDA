<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            'total_org'     => Data::distinct('sumber')->count('sumber'),
            'total_user'    => User::count(),
            // (Anda bisa menambahkan 'data_valid' dll sesuai kebutuhan Anda)
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
                    // CATATAN: Pastikan ini 'nama_indikator' jika DB Anda menggunakan nama itu
                    'title' => $item->nama_indikator ?? $item->nama_data, 
                    'tags'  => [$item->tema->nama_tema ?? 'Umum', $item->tahun_terbit ?? ''], 
                    'org'   => $item->sumber ?? 'Bappeda NTB',
                    'date'  => $item->created_at->diffForHumans(),
                ];
            });
        };

        $datasets = [
            'popular' => $mapDataset(Data::inRandomOrder()), 
            'latest'  => $mapDataset(Data::latest()),
        ];

        // 5. TOPIK / KATEGORI
        $topics = Tema::limit(6)->get()->map(fn($t) => ['name' => $t->nama_tema]);

        // 6. GROWTH CHART (12 Bulan Terakhir)
        $growthRaw = Data::select(
            DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
            DB::raw('COUNT(*) as total')
        )
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

        // 7. LOG AKTIVITAS TERBARU
        $recentActivities = DataUpload::with(['user', 'data'])
        ->latest('created_at')
        ->limit(8)
        ->get()
        ->map(function ($log) {
            return [
                'id' => $log->id_upload,
                'user' => $log->user->name ?? 'System',
                'action' => 'Mengunggah Data',
                // UBAH 'nama_data' JADI 'nama_indikator' JIKA PERLU
                'target' => $log->data->nama_indikator ?? 'Indikator tidak diketahui', 
                
                // KARENA KOLOM STATUS SUDAH DIHAPUS, GANTI DENGAN TEKS STATIS
                'status' => 'Berhasil', 
                
                'time' => $log->created_at->diffForHumans(),
                'date_raw' => $log->created_at->format('d M Y, H:i')
            ];
        });

        // 8. [BARU] AMBIL DATA BOOKMARK UNTUK ADMIN YANG SEDANG LOGIN
        $userId = Auth::id();
        // Ganti 'Bookmark' dengan nama Model bookmark Anda (Bookmark / Bookmarks)
        $pinnedDatasets = \App\Models\Bookmark::join('data', 'bookmark.dataset_id', '=', 'data.id_data')
            ->where('bookmark.user_id', $userId)
            // UBAH BARIS SELECT INI:
            // Ambil kolom nama_data tapi samarkan sebagai nama_indikator
            ->select('data.id_data', 'data.nama_data as nama_indikator', 'data.tahun_terbit') 
            ->get();

        // 9. RETURN KE VUE
        return Inertia::render('Admin/Dashboard', [
            'stats'            => $stats,
            'temaChart'        => $temaChart,
            'bidangChart'      => $bidangChart,
            'datasets'         => $datasets,
            'topics'           => $topics,
            'growthChart'      => $growthChart,
            'recentActivities' => $recentActivities,
            
            // [BARU] KIRIM KE VUE
            'pinnedData'       => $pinnedDatasets, 
        ]);
    }
}