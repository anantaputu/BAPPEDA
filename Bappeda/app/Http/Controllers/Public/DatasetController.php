<?php

namespace App\Http\Controllers\Public; 
use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use Inertia\Inertia;

class DatasetController extends Controller
{
    // Method untuk menampilkan detail dataset
    public function show($id)
    {
        // 1. Ambil Metadata (Model Data)
        // Kita gunakan 'with' untuk mengambil relasi Tema, Urusan, dll sekaligus
        $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])
            ->where('id_data', $id)
            ->firstOrFail(); // Jika ID tidak ada, otomatis 404

        // 2. Ambil Isi Data Excel (Model DataUpload)
        // Cari file upload yang statusnya 'valid' untuk dataset ini
        $upload = DataUpload::where('id_data', $id)
            ->where('status', 'valid')
            ->latest() // Ambil yang paling baru diupload
            ->first();

        // Ambil JSON value-nya, jika kosong return array kosong
        // Pastikan kolom 'value' di database tipe-nya JSON/LongText dan sudah di-cast array di Model
        $tableData = $upload ? $upload->value : [];

        // 3. Kirim ke View (Inertia)
        return Inertia::render('Public/DatasetDetail', [
            'dataset' => $dataset,
            'tableData' => $tableData
        ]);
    }
}