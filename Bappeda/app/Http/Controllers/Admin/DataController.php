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
    public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }
    
    public function index()
    {
        return Inertia::render('Admin/Data/Index', [
            'data' => Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])
                ->orderBy('id_data', 'desc')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Data/Create', [
            'tema'      => Tema::all(),
            'urusan'    => Urusan::all(),
            'bidang'    => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_data' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = Data::query()
                        ->whereRaw('LOWER(TRIM(nama_data)) = LOWER(TRIM(?))', [$value])
                        ->exists();

                    if ($exists) {
                        $fail('Nama indikator sudah ada. Gunakan nama lain.');
                    }
                },
            ],
            'deskripsi'      => 'nullable|string',

            'id_tema'        => 'required|exists:tema,id_tema',
            'id_urusan'      => 'required|exists:urusan,id_urusan',
            'id_bidang'      => 'required|exists:bidang,id_bidang',
            'id_frekuensi'   => 'required|exists:frekuensi,id_frekuensi',

            'id_katakunci'   => 'nullable|array',
            'satuan'         => 'required|string|max:255',
            'sumber'         => 'required|string|max:255',
        ]);

        $dataMaster = Data::create([
            'nama_data' => trim($validated['nama_data']),
            'deskripsi'      => $validated['deskripsi'],
            'id_tema'        => $validated['id_tema'],
            'id_urusan'      => $validated['id_urusan'],
            'id_bidang'      => $validated['id_bidang'],
            'id_frekuensi'   => $validated['id_frekuensi'],
            'satuan'         => $validated['satuan'],
            'sumber'         => $validated['sumber'],
            'status'         => 'aktif',
        ]);

        if ($request->has('id_katakunci')) {
            $dataMaster->katakunci()->sync($request->input('id_katakunci'));
        }

        return redirect('/admin/data')
            ->with('success', 'Metadata indikator berhasil ditambahkan');
    }

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

    public function update(Request $request, Data $data)
    {
        $validated = $request->validate([
            'nama_data' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($data) {
                    $exists = Data::query()
                        ->where('id_data', '!=', $data->id_data)
                        ->whereRaw('LOWER(TRIM(nama_data)) = LOWER(TRIM(?))', [$value])
                        ->exists();

                    if ($exists) {
                        $fail('Nama indikator sudah ada. Gunakan nama lain.');
                    }
                },
            ],
            'deskripsi'      => 'nullable|string',

            'id_tema'        => 'required|exists:tema,id_tema',
            'id_urusan'      => 'required|exists:urusan,id_urusan',
            'id_bidang'      => 'required|exists:bidang,id_bidang',
            'id_frekuensi'   => 'required|exists:frekuensi,id_frekuensi',

            'id_katakunci'   => 'nullable|array',
            'satuan'         => 'required|string|max:255',
            'sumber'         => 'required|string|max:255',
            'status'         => 'required|string',
        ]);

        $data->update([
            'nama_data'    => trim($validated['nama_data']),
            'deskripsi'    => $validated['deskripsi'],
            'id_tema'      => $validated['id_tema'],
            'id_urusan'    => $validated['id_urusan'],
            'id_bidang'    => $validated['id_bidang'],
            'id_frekuensi' => $validated['id_frekuensi'],
            'satuan'       => $validated['satuan'],
            'sumber'       => $validated['sumber'],
            'status'       => $validated['status'],
        ]);

        if ($request->has('id_katakunci')) {
            $data->katakunci()->sync($request->input('id_katakunci'));
        }

        return redirect('/admin/data')
            ->with('success', 'Metadata indikator berhasil diperbarui');
    }

    public function destroy(Data $data)
    {
        $data->delete();

        return back()->with('success', 'Metadata indikator berhasil dihapus');
    }
}
