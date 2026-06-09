<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Bidang;
use App\Models\Urusan;
use App\Models\Frekuensi;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil filter dari request
        $temaFilter      = $request->input('tema');
        $urusanFilter    = $request->input('urusan');
        $bidangFilter    = $request->input('bidang');
        $frekuensiFilter = $request->input('frekuensi');
        $searchFilter    = $request->input('search');

        // Helper untuk mempermudah reuse query data pembangunan dengan filter yang sama
        $applyFilters = function ($query) use ($temaFilter, $urusanFilter, $bidangFilter, $frekuensiFilter, $searchFilter) {
            $query->when($temaFilter, fn($q) => $q->where('id_tema', $temaFilter))
                  ->when($urusanFilter, fn($q) => $q->where('id_urusan', $urusanFilter))
                  ->when($bidangFilter, fn($q) => $q->where('id_bidang', $bidangFilter))
                  ->when($frekuensiFilter, fn($q) => $q->where('id_frekuensi', $frekuensiFilter));

            if ($searchFilter) {
                $keyword = strtolower(trim($searchFilter));
                $query->where(function($q) use ($keyword) {
                    $q->whereRaw('LOWER(nama_data) LIKE ?', ['%' . $keyword . '%'])
                      ->orWhere('satuan', 'like', "%{$keyword}%")
                      ->orWhere('sumber', 'like', "%{$keyword}%");
                });
            }
            return $query;
        };

        // 2. STATISTIK UTAMA (Terfilter secara penuh)
        $stats = [
            'total_dataset'   => $applyFilters(Data::query())->count(),
            'total_tema'      => Tema::count(),
            'total_urusan'    => Urusan::count(),
            'total_bidang'    => Bidang::count(),
            'total_sumber'    => $applyFilters(Data::query())->whereNotNull('sumber')->distinct('sumber')->count('sumber'),
            'total_frekuensi' => Frekuensi::count(),
        ];

        // 3. TEMA CHART (Selalu tampilkan semua tema untuk grafik distribusi utama)
        $themesData = Tema::withCount('data')->get();
        $temaChart = [
            'labels' => $themesData->pluck('nama_tema'),
            'values' => $themesData->pluck('data_count'),
        ];

        // 4. FREKUENSI CHART (Terfilter)
        $frekuensiData = Frekuensi::withCount(['data' => function($q) use ($applyFilters) {
            $applyFilters($q);
        }])->get();
        
        $frekuensiChart = [
            'labels' => $frekuensiData->pluck('nama_frekuensi'),
            'values' => $frekuensiData->pluck('data_count'),
        ];

        // 5. BIDANG CHART (Terfilter)
        $bidangData = Bidang::withCount(['data' => function($q) use ($applyFilters) {
            $applyFilters($q);
        }])
        ->orderBy('data_count', 'desc')
        ->limit(6)
        ->get();

        $totalDatasetFiltered = $applyFilters(Data::query())->count();
        $totalTop6 = $bidangData->sum('data_count');
        $othersCount = max($totalDatasetFiltered - $totalTop6, 0);

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

        // 6. TREN DATA BULANAN (Terfilter)
        $trenQuery = Data::select(
                DB::raw("COUNT(*) as jumlah"),
                DB::raw("EXTRACT(MONTH FROM created_at) as bulan_num")
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy(DB::raw("bulan_num"))
            ->orderBy('bulan_num');

        $trenQuery = $applyFilters($trenQuery);
        $trenDataRaw = $trenQuery->get()->pluck('jumlah', 'bulan_num');

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $finalLabels = [];
        $finalValues = [];

        foreach ($months as $num => $name) {
            $finalLabels[] = $name;
            $finalValues[] = $trenDataRaw->get($num) ?? 0;
        }

        $trenChart = [
            'labels' => $finalLabels,
            'values' => $finalValues,
        ];

        // 7. DATASET PILIHAN (Terfilter)
        $popularQuery = Data::withCount('bookmarks')
            ->orderByDesc('bookmarks_count')
            ->orderByDesc('updated_at');

        $popularQuery = $applyFilters($popularQuery);
        
        $latestQuery = Data::query();
        $latestQuery = $applyFilters($latestQuery)->latest();

        if (DB::getDriverName() === 'pgsql') {
            $popularQuery->orderByRaw('LOWER(nama_data) ASC');
        } else {
            $popularQuery->orderBy('nama_data');
        }

        $mapDataset = function ($query) {
            return $query->with('tema')->limit(4)->get()->map(function ($item) {
                return [
                    'id' => $item->id_data,
                    'title' => $item->nama_data,
                    'tags' => ['XLSX', $item->tema->nama_tema ?? 'Umum'],
                    'org' => $item->sumber ?? 'Bappeda NTB',
                    'pin_count' => (int) ($item->bookmarks_count ?? 0),
                ];
            });
        };

        return Inertia::render('Public/Visualisasi', [
            'stats'          => $stats,
            'temaChart'      => $temaChart,
            'bidangChart'    => $bidangChart,
            'frekuensiChart' => $frekuensiChart,
            'trenChart'      => $trenChart,
            'datasets'       => [
                'popular' => $mapDataset($popularQuery),
                'latest'  => $mapDataset($latestQuery),
            ],
            'topics'         => Tema::limit(6)->get()->map(fn($t) => ['name' => $t->nama_tema]),
            
            // Metadata dropdown lengkap
            'listTema'       => Tema::orderBy('nama_tema')->get(),
            'listUrusan'     => Urusan::orderBy('nama_urusan')->get(),
            'listBidang'     => Bidang::orderBy('nama_bidang')->get(),
            'listFrekuensi'  => Frekuensi::orderBy('nama_frekuensi')->get(),
            
            'filters'        => $request->only(['tema', 'urusan', 'bidang', 'frekuensi', 'search']),
        ]);
    }
}
