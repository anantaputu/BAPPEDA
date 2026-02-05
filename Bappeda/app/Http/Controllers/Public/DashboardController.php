<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // App/Http/Controllers/Public/DashboardController.php
    public function index()
    {
        // Distribusi Indikator berdasarkan Tema
        $temaStats = DB::table('tema')
            ->leftJoin('data', 'tema.id_tema', '=', 'data.id_tema')
            ->select('tema.nama_tema', DB::raw('count(data.id_data) as total'))
            ->groupBy('tema.id_tema', 'tema.nama_tema')
            ->get();

        return inertia('Public/Dashboard', [
            'stats' => [
                'total_indikator' => \App\Models\Data::count(),
                'data_valid' => \App\Models\Data::where('status', 'valid')->count(),
                'total_tema' => \App\Models\Tema::count(),
                'total_urusan' => \App\Models\Urusan::count(),
                'last_update' => \App\Models\Data::latest('updated_at')->first()?->updated_at->format('d M Y') ?? '-',
            ],
            'temaChart' => [
                'labels' => $temaStats->pluck('nama_tema'),
                'values' => $temaStats->pluck('total'),
            ],
        ]);
    }
}
