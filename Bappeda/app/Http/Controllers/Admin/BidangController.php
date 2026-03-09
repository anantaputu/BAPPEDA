<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Inertia\Inertia;

class BidangController extends Controller
{

    public function __construct()
    {

        $this->middleware('role:admin')->only(['edit', 'update', 'destroy']);
    }
    
    public function index()
    {
        return Inertia::render('Admin/Bidang/Index', [
            'bidang' => Bidang::orderBy('id_bidang')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Bidang/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:bidang,nama_bidang',
        ]);

        Bidang::create($validated);

        return redirect('/admin/bidang')
            ->with('success', 'Bidang berhasil ditambahkan');
    }

    public function edit(Bidang $bidang)
    {
        return Inertia::render('Admin/Bidang/Edit', [
            'bidang' => $bidang,
        ]);
    }

    public function update(Request $request, Bidang $bidang)
    {
        $validated = $request->validate([
            'nama_bidang' =>
                'required|string|max:255|unique:bidang,nama_bidang,' .
                $bidang->id_bidang . ',id_bidang',
        ]);

        $bidang->update($validated);

        return redirect('/admin/bidang')
            ->with('success', 'Bidang berhasil diperbarui');
    }

    public function destroy(Bidang $bidang)
    {
        $usedCount = $bidang->data()->count();
        if ($usedCount > 0) {
            return back()->with('error', "Bidang '{$bidang->nama_bidang}' tidak dapat dihapus karena masih dipakai oleh {$usedCount} indikator.");
        }

        try {
            $bidang->delete();
            return back()->with('success', 'Bidang berhasil dihapus');
        } catch (QueryException $e) {
            return back()->with('error', "Bidang '{$bidang->nama_bidang}' tidak dapat dihapus karena masih memiliki relasi data.");
        }
    }
}
