<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\Tema;
use App\Models\Bidang;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_dataset' => Data::count(),
            'data_valid'    => DataUpload::where('status', 'validated')->count(), // Sesuaikan status DB
            'total_visual'  => 0, // Placeholder jika ada modul visualisasi nanti
            'total_org'     => Data::distinct('sumber')->count('sumber'),
        ];

        $themesData = Tema::withCount('data')->get();
        $temaChart = [
            'labels' => $themesData->pluck('nama_tema'),
            'values' => $themesData->pluck('data_count'),
        ];

        $bidangData = Bidang::withCount('data')
            ->orderBy('data_count', 'desc')
            ->limit(5)
            ->get();

        $totalTop5 = $bidangData->sum('data_count');
        $totalAll = Data::count();
        $othersCount = $totalAll - $totalTop5;

        $bidangLabels = $bidangData->pluck('nama_bidang')->toArray();
        $bidangValues = $bidangData->pluck('data_count')->toArray();

        if ($othersCount > 0) {
            $bidangLabels[] = 'Lainnya';
            $bidangValues[] = $othersCount;
        }

        $bidangChart = [
            'labels' => $bidangLabels,
            'values' => $bidangValues,
        ];

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

        $topics = Tema::limit(6)->get()->map(function($t) {
            return ['name' => $t->nama_tema];
        });

        return Inertia::render('Public/Dashboard', [
            'stats'       => $stats,
            'temaChart'   => $temaChart,
            'bidangChart' => $bidangChart,
            'datasets'    => [
                'popular' => $mapDataset(Data::inRandomOrder()),
                'latest'  => $mapDataset(Data::latest()),
            ],
            'topics'      => $topics
        ]);
    }
}