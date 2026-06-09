<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Katakunci;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KataKunciController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        return Inertia::render('Admin/Katakunci/Index', [
            'katakunci' => Katakunci::orderBy('id_katakunci', 'desc')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Katakunci/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_katakunci' => 'required|string|max:255|unique:katakunci,nama_katakunci',
        ]);

        Katakunci::create($validated);

        return redirect('/admin/katakunci')->with('success', 'Kata Kunci berhasil ditambahkan');
    }

    public function edit($id)
    {
        // Menggunakan find agar lebih aman dengan Primary Key id_katakunci
        $katakunci = Katakunci::findOrFail($id);

        return Inertia::render('Admin/Katakunci/Edit', [
            'katakunci' => $katakunci,
        ]);
    }

    public function update(Request $request, $id)
    {
        $katakunci = Katakunci::findOrFail($id);

        // Validasi unique kecuali untuk ID saat ini
        $validated = $request->validate([
            'nama_katakunci' => 'required|string|max:255|unique:katakunci,nama_katakunci,' . $id . ',id_katakunci',
        ]);

        $katakunci->update($validated);

        return redirect('/admin/katakunci')->with('success', 'Kata Kunci berhasil diperbarui');
    }

    public function destroy($id)
    {
        $katakunci = Katakunci::findOrFail($id);

        $usedCount = DB::table('data_katakunci_pivot')
            ->where('id_katakunci', $katakunci->id_katakunci)
            ->count();

        if ($usedCount > 0) {
            return redirect('/admin/katakunci')->with('error', "Kata kunci '{$katakunci->nama_katakunci}' tidak dapat dihapus karena masih dipakai oleh {$usedCount} indikator.");
        }

        $katakunci->delete();

        return redirect('/admin/katakunci')->with('success', 'Kata Kunci berhasil dihapus');
    }
}
