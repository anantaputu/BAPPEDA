<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        $dataIndikator = Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])->latest()->get();
        
        return inertia('Admin/Data/Index', [
            'dataIndikator' => $dataIndikator
        ]);
    }
}
