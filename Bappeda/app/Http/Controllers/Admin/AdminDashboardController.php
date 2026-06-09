<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\Data;
use App\Models\Tema;
use App\Models\Bidang;
use App\Models\User;
use App\Models\Role;
use App\Models\Frekuensi;
use App\Models\ActivityLog;
use App\Models\Bookmark;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $totalDataset = Data::count();
        $datasetWithValues = Data::whereHas('values')->count();

        $stats = [
            'total_dataset' => $totalDataset,
            'total_org'     => Data::whereNotNull('sumber')->distinct('sumber')->count('sumber'),
            'total_user'    => User::count(),
            'user_active'   => User::where('status_aktif', true)->count(),
            'user_inactive' => User::where('status_aktif', false)->count(),
            'total_tema'    => Tema::count(),
            'total_bidang'  => Bidang::count(),
            'total_frekuensi' => Frekuensi::count(),
            'data_with_values' => $datasetWithValues,
            'data_without_values' => max($totalDataset - $datasetWithValues, 0),
            'input_today'   => Data::whereDate('created_at', $today)->count(),
            'updated_today' => Data::whereDate('updated_at', $today)->count(),
            'total_logs'    => ActivityLog::count(),
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
        $othersCount = $totalDataset - $totalTop5;

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

        $roleData = Role::withCount('users')->orderBy('users_count', 'desc')->get();
        $roleChart = [
            'labels' => $roleData->pluck('nama_role'),
            'values' => $roleData->pluck('users_count'),
        ];

        $sourceData = Data::query()
            ->select('sumber', DB::raw('COUNT(*) as total'))
            ->whereNotNull('sumber')
            ->groupBy('sumber')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $sourceChart = [
            'labels' => $sourceData->pluck('sumber'),
            'values' => $sourceData->pluck('total'),
        ];

        $mapDataset = function ($query) {
            return $query->with(['tema', 'bidang'])->limit(5)->get()->map(function ($item) {
                return [
                    'id'    => $item->id_data,
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

        $topics = Tema::limit(6)->get()->map(fn($t) => ['name' => $t->nama_tema]);

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

        $actionLabels = [
            'UPLOAD' => 'Menambahkan Data',
            'EDIT'   => 'Memperbarui Data',
            'DELETE' => 'Menghapus Data',
        ];

        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function ($log) use ($actionLabels) {
                return [
                    'id' => $log->id_log,
                    'user' => $log->user->name ?? 'System',
                    'action' => $actionLabels[$log->action] ?? ($log->description ?: $log->action),
                    'target' => $log->target_name ?? 'Data tidak diketahui',
                    'status' => 'Berhasil',
                    'type' => $log->action,
                    'time' => $log->created_at->diffForHumans(),
                    'date_raw' => $log->created_at->format('d M Y, H:i')
                ];
            });

        $recentDatasets = Data::with(['tema', 'bidang'])
            ->latest('updated_at')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id_data,
                    'name' => $item->nama_data,
                    'tema' => $item->tema->nama_tema ?? '-',
                    'bidang' => $item->bidang->nama_bidang ?? '-',
                    'sumber' => $item->sumber ?? '-',
                    'updated_at' => $item->updated_at?->diffForHumans() ?? '-',
                ];
            });

        $recentUsers = User::with('role')
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->nama_role ?? '-',
                    'status_aktif' => (bool) $user->status_aktif,
                    'created_at' => $user->created_at?->diffForHumans() ?? '-',
                ];
            });

        $dataHealth = [
            'coverage_value_percent' => $totalDataset > 0 ? round(($datasetWithValues / $totalDataset) * 100, 1) : 0,
            'without_source_count' => Data::whereNull('sumber')->orWhere('sumber', '')->count(),
            'without_tahun_count' => Data::whereNull('tahun_terbit')->count(),
        ];

        $userId = Auth::id();
        $pinnedDatasets = Bookmark::join('data', 'bookmark.dataset_id', '=', 'data.id_data')
            ->where('bookmark.user_id', $userId)
            ->select('data.id_data', 'data.nama_data', 'data.tahun_terbit')
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats'            => $stats,
            'temaChart'        => $temaChart,
            'bidangChart'      => $bidangChart,
            'roleChart'        => $roleChart,
            'sourceChart'      => $sourceChart,
            'datasets'         => $datasets,
            'topics'           => $topics,
            'growthChart'      => $growthChart,
            'recentActivities' => $recentActivities,
            'recentUsers'      => $recentUsers,
            'recentDatasets'   => $recentDatasets,
            'dataHealth'       => $dataHealth,
            'pinnedData'       => $pinnedDatasets,
        ]);
    }
}
