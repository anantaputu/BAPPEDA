<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frekuensi;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Inertia\Inertia;

class FrekuensiController extends Controller
{
    public function __construct()
    {
        // Middleware ini akan mencegat Inputer jika mencoba Edit atau Hapus
        // 'role:admin' artinya HANYA Admin yang boleh masuk ke method tersebut
        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }
    public function index()
    {
        return Inertia::render('Admin/Frekuensi/Index', [
            'frekuensi' => Frekuensi::orderBy('id_frekuensi')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Frekuensi/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_frekuensi' => 'required|string|max:255|unique:frekuensi,nama_frekuensi',
        ]);

        Frekuensi::create($validated);

        return redirect('/admin/frekuensi')
            ->with('success', 'Frekuensi berhasil ditambahkan');
    }

    public function edit(Frekuensi $frekuensi)
    {
        return Inertia::render('Admin/Frekuensi/Edit', [
            'frekuensi' => $frekuensi,
        ]);
    }

    public function update(Request $request, Frekuensi $frekuensi)
    {
        $validated = $request->validate([
            'nama_frekuensi' =>
                'required|string|max:255|unique:frekuensi,nama_frekuensi,' .
                $frekuensi->id_frekuensi . ',id_frekuensi',
        ]);

        $frekuensi->update($validated);

        return redirect('/admin/frekuensi')
            ->with('success', 'Frekuensi berhasil diperbarui');
    }

    public function destroy(Frekuensi $frekuensi)
    {
        $usedCount = $frekuensi->data()->count();
        if ($usedCount > 0) {
            return back()->with('error', "Frekuensi '{$frekuensi->nama_frekuensi}' tidak dapat dihapus karena masih dipakai oleh {$usedCount} indikator.");
        }

        try {
            $frekuensi->delete();
            return back()->with('success', 'Frekuensi berhasil dihapus');
        } catch (QueryException $e) {
            return back()->with('error', "Frekuensi '{$frekuensi->nama_frekuensi}' tidak dapat dihapus karena masih memiliki relasi data.");
        }
    }
}
