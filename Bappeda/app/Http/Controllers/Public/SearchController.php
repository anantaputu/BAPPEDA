<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan; // TAMBAHKAN BARIS INI
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Data::with(['tema', 'urusan', 'bidang']);

        // Filter berdasarkan Keyword (Nama Indikator atau Kata Kunci)
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_indikator', 'like', "%{$request->search}%")
                  ->orWhere('kata_kunci', 'like', "%{$request->search}%");
            });
        }

        // Filter Dropdown
        if ($request->id_tema) $query->where('id_tema', $request->id_tema);
        if ($request->id_urusan) $query->where('id_urusan', $request->id_urusan);
        if ($request->tahun) $query->where('tahun_data', $request->tahun);

        return Inertia::render('Public/Search', [
            'results' => $query->latest()->paginate(9)->withQueryString(),
            'filters' => $request->only(['search', 'id_tema', 'id_urusan', 'tahun']),
            'temas' => Tema::all(),
            'urusans' => Urusan::all(), // Sekarang class ini sudah terdeteksi
        ]);
    }
}