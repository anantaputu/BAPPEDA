<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data; // Pastikan ini mengarah ke model Data Anda
use App\Models\Tema;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil query pencarian dari URL (?search=...)
        $search = $request->input('search');
        $temaId = $request->input('tema');

        // 2. Gunakan model Data sesuai nama file Model Anda
        $results = Data::query()
            // Relasi disesuaikan dengan method yang ada di file Data.php
            ->with(['tema', 'urusan', 'bidang', 'frekuensi']) 
            ->when($search, function ($query, $search) {
                // Kolom di model Data adalah 'nama_indikator'
                $query->where('nama_indikator', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%")
                      ->orWhere('kata_kunci', 'like', "%{$search}%");
            })
            ->when($temaId, function ($query, $temaId) {
                // Foreign key di model Data adalah 'id_tema'
                $query->where('id_tema', $temaId);
            })
            ->where('status', 'valid') // Filter agar publik hanya melihat data tervalidasi
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 3. Kirim data ke Search.vue
        return Inertia::render('Public/Search', [
            'results' => $results,
            'filters' => [
                'search' => $search,
                'tema'   => $temaId,
            ],
            // Primary key di model Tema adalah 'id_tema'
            'temas' => Tema::all(['id_tema', 'nama_tema']),
        ]);
    }
}