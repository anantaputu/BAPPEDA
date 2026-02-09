<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KataKunci;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KataKunciController extends Controller
{
    public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }
    public function index()
    {
        return Inertia::render('Admin/KataKunci/Index', [
            'tema' => KataKunci::orderBy('id_tema')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/KataKunci/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tema' => 'required|string|max:255|unique:tema,nama_tema',
        ]);

        KataKunci::create($validated);

        return redirect('/admin/tema')->with('success', 'KataKunci berhasil ditambahkan');
    }

    public function edit(KataKunci $tema)
    {
        return Inertia::render('Admin/KataKunci/Edit', [
            'tema' => $tema,
        ]);
    }

    public function update(Request $request, KataKunci $tema)
    {
        $validated = $request->validate([
            'nama_tema' => 'required|string|max:255|unique:tema,nama_tema,' . $tema->id_tema . ',id_tema',
        ]);

        $tema->update($validated);

        return redirect('/admin/tema')->with('success', 'KataKunci berhasil diperbarui');
    }

    public function destroy(KataKunci $tema)
    {
        $tema->delete();

        return back()->with('success', 'KataKunci berhasil dihapus');
    }
}
