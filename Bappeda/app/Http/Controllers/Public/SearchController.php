<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Frekuensi;
use App\Models\Katakunci;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Kita muat relasi 'frekuensi'
        $query = Data::with(['tema', 'urusan', 'frekuensi', 'katakunci']);

        // Filter Keyword
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_indikator', 'like', "%{$request->search}%")
                  ->orWhereHas('katakunci', function($queryK) use ($request) {
                      $queryK->where('nama_katakunci', 'like', "%{$request->search}%");
                  });
            });
        }

        // Filter Dropdown
        if ($request->id_tema) $query->where('id_tema', $request->id_tema);
        if ($request->id_urusan) $query->where('id_urusan', $request->id_urusan);
        if ($request->id_frekuensi) $query->where('id_frekuensi', $request->id_frekuensi);

        if ($request->id_katakunci) {
            $query->whereHas('katakunci', function($q) use ($request) {
                $q->where('katakunci.id_katakunci', $request->id_katakunci);
            });
        }

        return Inertia::render('Public/Search', [
            'results' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'id_tema', 'id_urusan', 'id_frekuensi']),
            'temas'   => Tema::all(),
            'urusans' => Urusan::all(),
            'frekuensis' => Frekuensi::all(), // Kirim data master frekuensi untuk dropdown
            'katakuncis' => Katakunci::orderBy('nama_katakunci', 'asc')->get(),
            'list_satuan' => Data::distinct()->whereNotNull('satuan')->orderBy('satuan', 'asc')->pluck('satuan'),
        ]);
    }
}