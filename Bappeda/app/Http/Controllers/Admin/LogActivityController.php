<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataUpload;
use Inertia\Inertia;
use Carbon\Carbon;

class LogActivityController extends Controller
{
    public function index()
    {
        $logs = DataUpload::with(['user', 'data'])
            ->latest()
            ->paginate(20);

        $formattedLogs = collect($logs->items())->map(function ($log) {
            return [
                'id' => $log->id_upload,
                'user' => $log->user->name ?? 'Sistem',
                'time' => Carbon::parse($log->created_at)->diffForHumans(),
                'date_full' => Carbon::parse($log->created_at)->format('d M Y, H:i'),
                'action' => 'Melakukan upload indikator',
                'target' => $log->data->nama_indikator ?? 'Indikator Dihapus',
                'status' => $log->status, // valid, pending, rejected
            ];
        });

        return Inertia::render('Admin/Logs/Index', [
            'activities' => $formattedLogs,
            'pagination' => [
                'links' => $logs->linkCollection(),
                'current_page' => $logs->currentPage(),
            ]
        ]);
    }
}