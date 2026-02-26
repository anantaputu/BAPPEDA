<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Bidang;
use App\Models\Urusan;
use App\Models\Frekuensi;
use App\Models\Sumber;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_dataset'   => Data::count(),
            'total_tema'      => Tema::count(),
            'total_urusan'    => Urusan::count(),
            'total_bidang'    => Bidang::count(),
            'total_sumber'    => Data::distinct('sumber')->count('sumber'),
            'total_frekuensi' => Frekuensi::count(),
        ];

        $themesData = Tema::withCount('data')->get();
        $temaChart = [
            'labels' => $themesData->pluck('nama_tema'),
            'values' => $themesData->pluck('data_count'),
        ];

        $frekuensiData = Frekuensi::withCount('data')->get();
        $frekuensiChart = [
            'labels' => $frekuensiData->pluck('nama_frekuensi'),
            'values' => $frekuensiData->pluck('data_count'),
        ];

        $bidangData = Bidang::withCount('data')
            ->orderBy('data_count', 'desc')
            ->limit(5)
            ->get();

        $totalTop5 = $bidangData->sum('data_count');
        $othersCount = $stats['total_dataset'] - $totalTop5;

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

        $trenData = Data::select(
                DB::raw("COUNT(*) as jumlah"),
                DB::raw("TRIM(TO_CHAR(created_at, 'Month')) as bulan"), 
                DB::raw("EXTRACT(MONTH FROM created_at) as bulan_num")
            )
            ->whereYear('created_at', date('Y')) 
            ->groupBy(DB::raw("bulan"), DB::raw("bulan_num"))
            ->orderBy('bulan_num')
            ->get();

        $trenChart = [
            'labels' => $trenData->pluck('bulan')->toArray(),
            'values' => $trenData->pluck('jumlah')->toArray(),
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

        return Inertia::render('Public/Dashboard', [
            'stats'          => $stats,
            'temaChart'      => $temaChart,
            'bidangChart'    => $bidangChart,
            'frekuensiChart' => $frekuensiChart,
            'trenChart'      => $trenChart,
            'datasets'       => [
                'popular' => $mapDataset(Data::inRandomOrder()),
                'latest'  => $mapDataset(Data::latest()),
            ],
            'topics'         => Tema::limit(6)->get()->map(fn($t) => ['name' => $t->nama_tema])
        ]);
    }
}