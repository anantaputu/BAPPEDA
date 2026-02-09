<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sumber;

class SumberController extends Controller
{
    

      public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }

    public function index()
    {
        return Inertia::render('Admin/Sumber/Index', [
            'sumber' => Sumber::orderBy('id_sumber')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Sumber/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:255|unique:sumber,nama_sumber',
        ]);

        Sumber::create($validated);

        return redirect('/admin/sumber')->with('success', 'Sumber berhasil ditambahkan');
    }

    public function edit(Sumber $sumber)
    {
        return Inertia::render('Admin/Sumber/Edit', [
            'sumber' => $sumber,
        ]);
    }

    public function update(Request $request, Sumber $sumber)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:255|unique:sumber,nama_sumber,' . $sumber->id_sumber . ',id_sumber',
        ]);

        $sumber->update($validated);

        return redirect('/admin/sumber')->with('success', 'Sumber berhasil diperbarui');
    }

    public function destroy(Sumber $sumber)
    {
        $sumber->delete();
        return back()->with('success', 'Sumber berhasil dihapus');
    }
}


