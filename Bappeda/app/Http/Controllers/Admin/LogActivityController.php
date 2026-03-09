<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog; // Menggunakan model ActivityLog yang baru
use Inertia\Inertia;
use Carbon\Carbon;

class LogActivityController extends Controller
{
    public function index()
    {
        // 1. Ambil data dari tabel activity_logs dengan relasi user
        // Relasi 'data' tidak wajib karena kita sudah punya kolom 'target_name'
        $logs = ActivityLog::with(['user'])
            ->latest('created_at')
            ->paginate(20);

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