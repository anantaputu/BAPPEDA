<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataController extends Controller
{
    /**
     * INDEX
     */

    public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }
    
    public function index()
    {
        return Inertia::render('Admin/Data/Index', [
            // Menggunakan paginate() alih-alih get()
            'data' => Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])
                ->orderBy('id_data', 'desc')
                ->paginate(10) // Menampilkan 10 data per halaman
                ->withQueryString(), // Mempertahankan filter jika ada
        ]);
    }

    /**
     * CREATE
     */
    public function create()
    {
        return Inertia::render('Admin/Data/Create', [
            'tema'      => Tema::all(),
            'urusan'    => Urusan::all(),
            'bidang'    => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ]);
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_data' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',

            'id_tema'        => 'required|exists:tema,id_tema',
            'id_urusan'      => 'required|exists:urusan,id_urusan',
            'id_bidang'      => 'required|exists:bidang,id_bidang',
            'id_frekuensi'   => 'required|exists:frekuensi,id_frekuensi',

            'kata_kunci'     => 'nullable|string|max:255',
            'satuan'         => 'required|string|max:255',
            'sumber'         => 'required|string|max:255',
        ]);

        Data::create([
            'nama_data' => $validated['nama_data'],
            'deskripsi'      => $validated['deskripsi'],
            'id_tema'        => $validated['id_tema'],
            'id_urusan'      => $validated['id_urusan'],
            'id_bidang'      => $validated['id_bidang'],
            'id_frekuensi'   => $validated['id_frekuensi'],
            'kata_kunci'     => $validated['kata_kunci'],
            'satuan'         => $validated['satuan'],
            'sumber'         => $validated['sumber'],
            'status'         => 'aktif',
        ]);

        return redirect('/admin/data')
            ->with('success', 'Metadata indikator berhasil ditambahkan');
    }

    /**
     * EDIT
     */
    public function edit(Data $data)
    {
        return Inertia::render('Admin/Data/Edit', [
            'data'      => $data,
            'tema'      => Tema::all(),
            'urusan'    => Urusan::all(),
            'bidang'    => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, Data $data)
    {
        $validated = $request->validate([
            'nama_data' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',

            'id_tema'        => 'required|exists:tema,id_tema',
            'id_urusan'      => 'required|exists:urusan,id_urusan',
            'id_bidang'      => 'required|exists:bidang,id_bidang',
            'id_frekuensi'   => 'required|exists:frekuensi,id_frekuensi',

            'kata_kunci'     => 'nullable|string|max:255',
            'satuan'         => 'required|string|max:255',
            'sumber'         => 'required|string|max:255',
            'status'         => 'required|string',
        ]);

        $data->update($validated);

        return redirect('/admin/data')
            ->with('success', 'Metadata indikator berhasil diperbarui');
    }

    /**
     * DESTROY
     */
    public function destroy(Data $data)
    {
        $data->delete();

        return back()->with('success', 'Metadata indikator berhasil dihapus');
    }
}
