<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\DataUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataInputController extends Controller
{
    // STEP 1: list metadata
    public function index()
    {
        return inertia('DataInput/Index', [
            'dataList' => Data::where('status', 'aktif')->get()
        ]);
    }

    // STEP 2: form upload
    public function create(Data $data)
    {
        return inertia('DataInput/Create', [
            'data' => $data
        ]);
    }

    // STEP 3: simpan upload
    public function store(Request $request, Data $data)
    {
        $request->validate([
            'periode' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $path = $request->file('file')->store('uploads/excel');

        DataUpload::create([
            'id_data' => $data->id_data,
            'id_user' => Auth::id(),
            'periode' => $request->periode,
            'file_path' => $path,
            'status' => 'draft'
        ]);

        return redirect()
            ->route('input-data.index')
            ->with('success', 'Data berhasil diupload');
    }
}
