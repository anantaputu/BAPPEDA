<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function create()
    {
        return inertia('Data/Create', [
            'tema' => Tema::all(),
            'urusan' => Urusan::all(),
            'bidang' => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_indikator' => 'required|string',
            'deskripsi' => 'required|string',
            'id_tema' => 'required',
            'id_urusan' => 'required',
            'id_bidang' => 'required',
            'kata_kunci' => 'nullable|string',
            'satuan' => 'required|string',
            'id_frekuensi' => 'required',
            'sumber' => 'required|string',
        ]);

        Data::create([
            'nama_indikator' => $request->nama_indikator,
            'deskripsi' => $request->deskripsi,
            'id_tema' => $request->id_tema,
            'id_urusan' => $request->id_urusan,
            'id_bidang' => $request->id_bidang,
            'kata_kunci' => $request->kata_kunci,
            'satuan' => $request->satuan,
            'id_frekuensi' => $request->id_frekuensi,
            'sumber' => $request->sumber,
            'status' => 'aktif'
        ]);

        return redirect()->route('input-data.index')
            ->with('success', 'Metadata data berhasil ditambahkan');
    }
}
