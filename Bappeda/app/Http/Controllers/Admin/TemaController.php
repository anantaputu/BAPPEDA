<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tema;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Inertia\Inertia;


class TemaController extends Controller 
{
      public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        return Inertia::render('Admin/Tema/Index', [
            'tema' => Tema::orderBy('id_tema')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Tema/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tema' => 'required|string|max:255|unique:tema,nama_tema',
        ]);

        Tema::create($validated);

        return redirect('/admin/tema')->with('success', 'Tema berhasil ditambahkan');
    }

    public function edit(Tema $tema)
    {
        return Inertia::render('Admin/Tema/Edit', [
            'tema' => $tema,
        ]);
    }

    public function update(Request $request, Tema $tema)
    {
        $validated = $request->validate([
            'nama_tema' => 'required|string|max:255|unique:tema,nama_tema,' . $tema->id_tema . ',id_tema',
        ]);

        $tema->update($validated);

        return redirect('/admin/tema')->with('success', 'Tema berhasil diperbarui');
    }

    public function destroy(Tema $tema)
    {
        $usedCount = $tema->data()->count();
        if ($usedCount > 0) {
            return back()->with('error', "Tema '{$tema->nama_tema}' tidak dapat dihapus karena masih dipakai oleh {$usedCount} indikator.");
        }

        try {
            $tema->delete();
            return back()->with('success', 'Tema berhasil dihapus');
        } catch (QueryException $e) {
            return back()->with('error', "Tema '{$tema->nama_tema}' tidak dapat dihapus karena masih memiliki relasi data.");
        }
    }
}
