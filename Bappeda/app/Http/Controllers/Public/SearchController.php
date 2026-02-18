<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Data::with(['tema', 'urusan', 'bidang']);

        // Filter Keyword
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_indikator', 'like', "%{$request->search}%")
                  ->orWhere('kata_kunci', 'like', "%{$request->search}%");
            });
        }

        // Filter Dropdown
        if ($request->id_tema) $query->where('id_tema', $request->id_tema);
        if ($request->id_urusan) $query->where('id_urusan', $request->id_urusan);
        
        // Perhatikan: menggunakan 'tahun_data' sesuai controller Anda
        if ($request->tahun) $query->where('tahun_data', $request->tahun);
        
        // Filter Satuan
        if ($request->satuan) $query->where('satuan', $request->satuan);

        return Inertia::render('Public/Search', [
            'results' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'id_tema', 'id_urusan', 'tahun', 'satuan']),
            'temas'   => Tema::all(),
            'urusans' => Urusan::all(),
            // Ambil daftar tahun unik dari kolom tahun_data
            'list_tahun'  => Data::distinct()->whereNotNull('tahun')->orderBy('tahun', 'desc')->pluck('tahun'),
            // Ambil daftar satuan unik
            'list_satuan' => Data::distinct()->whereNotNull('satuan')->orderBy('satuan', 'asc')->pluck('satuan'),
        ]);
    }
}