<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Urusan;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Inertia\Inertia;

class UrusanController extends Controller
{
    public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }
    
    public function index()
    {
        return Inertia::render('Admin/Urusan/Index', [
            'urusan' => Urusan::orderBy('id_urusan')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Urusan/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_urusan' => 'required|string|max:255|unique:urusan,nama_urusan',
        ]);

        Urusan::create($validated);

        return redirect('/admin/urusan')
            ->with('success', 'Urusan berhasil ditambahkan');
    }

    public function edit(Urusan $urusan)
    {
        return Inertia::render('Admin/Urusan/Edit', [
            'urusan' => $urusan,
        ]);
    }

    public function update(Request $request, Urusan $urusan)
    {
        $validated = $request->validate([
            'nama_urusan' =>
                'required|string|max:255|unique:urusan,nama_urusan,' .
                $urusan->id_urusan . ',id_urusan',
        ]);

        $urusan->update($validated);

        return redirect('/admin/urusan')
            ->with('success', 'Urusan berhasil diperbarui');
    }

    public function destroy(Urusan $urusan)
    {
        $usedCount = $urusan->data()->count();
        if ($usedCount > 0) {
            return back()->with('error', "Urusan '{$urusan->nama_urusan}' tidak dapat dihapus karena masih dipakai oleh {$usedCount} indikator.");
        }

        try {
            $urusan->delete();
            return back()->with('success', 'Urusan berhasil dihapus');
        } catch (QueryException $e) {
            return back()->with('error', "Urusan '{$urusan->nama_urusan}' tidak dapat dihapus karena masih memiliki relasi data.");
        }
    }
}
