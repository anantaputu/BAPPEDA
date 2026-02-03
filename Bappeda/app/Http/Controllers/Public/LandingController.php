<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\User;
use Inertia\Inertia;

class LandingController extends Controller
{
    // public function index()
    // {
    //     return Inertia::render('Public/Landing', [
    //         'stats' => [
    //             'total_indikator' => Data::count(),

    //             'opd_aktif' => User::whereHas('role', function ($q) {
    //                 $q->where('nama_role', 'Inputer');
    //             })
    //             ->where('status_aktif', true)
    //             ->count(),

    //             'data_valid' => DataUpload::where('status', 'validated')->count(),

    //             'last_update' => DataUpload::max('updated_at'),
    //         ],
    //     ]);
    // }

    public function index()
    {
        return inertia('Public/Landing', [
            'stats' => [
                'total_indikator' => Data::count(),
                'opd_aktif'       => 5,
                'data_valid' => DataUpload::where('status', 'validated')->count(),
                'last_update'     => now(),
            ],
        ]);
    }
}
