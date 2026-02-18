<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        // Mengambil jumlah indikator utama
        $totalIndikator = Data::count();

        // Menghitung OPD (Role 2) yang sudah terdaftar dan akunnya aktif
        $opdAktif = User::where('role_id', 2)
                        ->where('status_aktif', true)
                        ->count();

        // Menghitung upload yang statusnya sudah 'validated'
        // $dataValid = DataUpload::where('status', 'validated')->count();

        // Mengambil waktu update terakhir dari upload apapun (biar sistem gak kelihatan mati)
        $lastUpdate = DataUpload::latest('updated_at')->first();
        
        // Format tanggal: "2 jam yang lalu" atau "10 Februari 2026"
        $formattedDate = $lastUpdate ? ($lastUpdate->updated_at->diffInDays() > 7 ? $lastUpdate->updated_at->format('d M Y') : $lastUpdate->updated_at->diffForHumans()) : '-';

        return inertia('Public/Landing', [
            'stats' => [
                'total_indikator' => $totalIndikator,
                'opd_aktif'       => $opdAktif,
                // 'data_valid'      => $dataValid,
                'last_update'     => $formattedDate,
            ],
        ]);
    }
}