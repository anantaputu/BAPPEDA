<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog; // Menggunakan model ActivityLog yang baru
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class LogActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user']);

        // Filter Action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter User
        if ($request->filled('user_id')) {
            $query->where('id_user', $request->user_id);
        }

        // Pencarian target atau deskripsi
        if ($request->filled('search')) {
            $keyword = strtolower(trim($request->search));
            $query->where(function($q) use ($keyword) {
                $q->whereRaw('LOWER(target_name) LIKE ?', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
            });
        }

        $logs = $query->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // 2. Format data agar selaras dengan UI Dashboard dan Logs Index
        $formattedLogs = collect($logs->items())->map(function ($log) {
            // Tentukan label deskriptif berdasarkan kode action
            $actionLabels = [
                'UPLOAD' => 'Mengunggah Data Indikator',
                'EDIT'   => 'Memperbarui Data Indikator',
                'DELETE' => 'Menghapus Data Indikator',
            ];

            return [
                'id'        => $log->id_log,
                'user'      => $log->user->name ?? 'System',
                'time'      => $log->created_at->diffForHumans(),
                'date_full' => $log->created_at->format('d M Y, H:i'),
                'action'    => $actionLabels[$log->action] ?? $log->action,
                
                // Menggunakan target_name yang tersimpan permanen di tabel log
                'target'    => $log->target_name ?? 'Indikator tidak diketahui',
                
                // Status statis atau deskripsi tambahan
                'status'    => 'Berhasil',
                'ip'        => $log->ip_address,
                'type'      => $log->action, // Berguna untuk styling warna di Vue (misal: merah untuk DELETE)
            ];
        });

        // 3. Return ke Vue dengan data aktivitas dan pagination lengkap
        return Inertia::render('Admin/Logs/Index', [
            'activities' => $formattedLogs,
            'users'      => User::orderBy('name')->get(['id', 'name', 'username']),
            'filters'    => $request->only(['action', 'user_id', 'search']),
            'pagination' => [
                'links'        => $logs->linkCollection(),
                'current_page' => $logs->currentPage(),
                'total'        => $logs->total(),
                'from'         => $logs->firstItem(),
                'to'           => $logs->lastItem(),
            ]
        ]);
    }
}