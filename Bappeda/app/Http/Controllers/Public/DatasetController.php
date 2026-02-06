<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Tambahkan ini untuk menangkap ?page=
use Illuminate\Pagination\LengthAwarePaginator; // Tambahkan ini untuk paginasi manual
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataField;
use Inertia\Inertia;

class DatasetController extends Controller
{
    public function show(Request $request, $id) // Tambahkan Request $request
    {
        // 1. Ambil Metadata (Data Indikator)
        $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])
            ->where('id_data', $id)
            ->firstOrFail();

        // 2. Ambil Upload Terakhir yang Valid
        $upload = DataUpload::where('id_data', $id)
            ->where('status', 'valid')
            ->latest()
            ->first();

        $processedData = [];
        $total = 0;
        $perPage = 20; // Batas per halaman
        $currentPage = $request->input('page', 1); // Ambil halaman dari URL, default 1

        if ($upload && $upload->value) {
            // Ambil data mentah (Array besar)
            $rawData = $upload->value; 
            $total = count($rawData); // Hitung total baris

            // --- LOGIKA PAGINASI SERVER-SIDE ---
            // Kita potong array-nya DISINI sebelum diproses looping
            // Agar server tidak berat memproses ribuan data yang tidak ditampilkan
            $offset = ($currentPage - 1) * $perPage;
            
            // Ambil hanya 20 baris yang dibutuhkan
            $chunkData = array_slice($rawData, $offset, $perPage);

            // Ambil mapping nama field (ID -> Nama)
            $fields = DataField::where('id_data', $id)->pluck('nama_field', 'id_field');
            
            // Proses 20 baris tersebut
            foreach ($chunkData as $row) {
                $newRow = [];
                foreach ($row as $fieldId => $val) {
                    $fieldName = $fields[$fieldId] ?? $fieldId;
                    $newRow[$fieldName] = $val;
                }
                $processedData[] = $newRow;
            }
        }

        // 3. Buat Object Paginator Laravel
        // Ini akan membuat struktur data yang mirip dengan ->paginate() database biasa
        $paginatedData = new LengthAwarePaginator(
            $processedData, // Data yang sudah dipotong (hanya 20 baris)
            $total,         // Total seluruh data
            $perPage,       // Batas per halaman
            $currentPage,   // Halaman aktif
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return Inertia::render('Public/DatasetDetail', [
            'dataset' => $dataset,
            'tableData' => $paginatedData // Kirim object paginator, bukan array biasa
        ]);
    }
}