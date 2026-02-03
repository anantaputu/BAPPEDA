<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // $temaDistribution = DB::table('tema')
        //     ->leftJoin('data', 'tema.id_tema', '=', 'data.id_tema')
        //     ->select(
        //         'tema.nama_tema',
        //         DB::raw('COUNT(data.id_data) as total')
        //     )
        //     ->groupBy('tema.id_tema', 'tema.nama_tema')
        //     ->orderBy('total', 'desc')
        //     ->get();

        return inertia('Public/Dashboard', [
            'stats' => [
                'total_indikator' => 120,
                'data_valid' => 95,
                'total_tema' => 8,
                'total_urusan' => 12,
                'last_update' => now()->format('d M Y'),
            ],
            'temaChart' => [
                'labels' => ['Pendidikan', 'Kesehatan', 'Ekonomi'],
                'values' => [30, 45, 20],
            ],
        ]);
    }
}
