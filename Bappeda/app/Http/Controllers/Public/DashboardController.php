<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\Tema;
use App\Models\Bidang; // Pastikan Model Bidang di-import
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. STATISTIK UTAMA
        $stats = [
            'total_dataset' => Data::count(),
            'data_valid'    => DataUpload::where('status', 'valid')->count(),
            'total_visual'  => 0,
            'total_org'     => Data::distinct('sumber')->count('sumber'),
            'total_upload'  => DataUpload::count(),
        ];

        // 2. CHART BAR (TEMA) - Ini yang sebelumnya missing
        $themesData = Tema::withCount('data')->get();
        $chartThemes = [
            'labels' => $themesData->pluck('nama_tema'),
            'data'   => $themesData->pluck('data_count'),
        ];

        // 3. CHART DOUGHNUT (BIDANG) - Logic Baru
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

        $chartBidang = [
            'labels' => $bidangLabels,
            'data'   => $bidangCounts,
        ];

        // 4. LIST DATASET (Terbaru & Populer)
        $mapDataset = function ($query) {
            return $query->with('tema')->limit(3)->get()->map(function ($item) {
                return [
                    'id'    => $item->id_data,
                    'title' => $item->nama_indikator,
                    'tags'  => ['XLSX', $item->tema->nama_tema ?? 'Umum'], 
                    'org'   => $item->sumber ?? 'Pemerintah',
                ];
            });
        };

        $latestDatasets = $mapDataset(Data::latest());
        $popularDatasets = $mapDataset(Data::inRandomOrder());

        // 5. KATEGORI / TOPIK
        $topics = Tema::limit(6)->get()->map(function($t) {
            return ['name' => $t->nama_tema];
        });

        // RETURN KE VUE
        return Inertia::render('Public/Dashboard', [
            'stats'       => $stats,
            'chartThemes' => $chartThemes, // Variabel ini sekarang sudah didefinisikan di poin no 2
            'chartBidang' => $chartBidang,
            'datasets'    => [
                'popular' => $popularDatasets,
                'latest'  => $latestDatasets,
            ],
            'topics'      => $topics
        ]);
    }
}