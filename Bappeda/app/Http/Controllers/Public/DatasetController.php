<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataField;
use Inertia\Inertia;

class DatasetController extends Controller
{
    public function show(Request $request, $id)
    {
        // 1. Ambil Metadata (Master Data)
        $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])
            ->where('id_data', $id)
            ->firstOrFail();

        // 2. Ambil Upload Terakhir yang Valid
        $upload = DataUpload::where('id_data', $id)
            ->where('status', 'valid')
            ->latest()
            ->first();

        // Variabel Default
        $finalSatuan = $dataset->satuan; // Default pakai satuan dari Master Data
        $fullChartData = []; // Data lengkap untuk Chart
        $paginatedData = null; // Data potong untuk Tabel

        if ($upload && $upload->value) {
            $rawData = $upload->value; 
            $total = count($rawData);

            // Ambil mapping field (ID -> Nama Field)
            $fields = DataField::where('id_data', $id)->pluck('nama_field', 'id_field');

            // --- A. PROSES SEMUA DATA (MAPPING ID KE NAMA) ---
            foreach ($rawData as $row) {
                $newRow = [];
                foreach ($row as $fieldId => $val) {
                    // Ubah Key ID (1, 2, 3) jadi Nama (Tahun, Nilai, Satuan)
                    $fieldName = $fields[$fieldId] ?? $fieldId;
                    $newRow[$fieldName] = $val;
                }
                $fullChartData[] = $newRow;
            }

            // --- B. CARI SATUAN DARI EXCEL (LOGIKA BARU) ---
            if (!empty($fullChartData)) {
                $firstRow = $fullChartData[0]; // Ambil baris pertama yang sudah dimapping
                
                // Cari key yang namanya 'satuan' (case-insensitive)
                $foundSatuanKey = collect(array_keys($firstRow))
                    ->first(function ($key) {
                        return strtolower($key) === 'satuan';
                    });

                if ($foundSatuanKey && !empty($firstRow[$foundSatuanKey])) {
                    $finalSatuan = $firstRow[$foundSatuanKey];
                }
            }

            // --- C. PAGINATION SERVER-SIDE (UNTUK TABEL) ---
            $perPage = 20;
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            // Ambil potongan data dari $fullChartData
            $chunkData = array_slice($fullChartData, $offset, $perPage);

            $paginatedData = new LengthAwarePaginator(
                $chunkData, 
                $total, 
                $perPage, 
                $currentPage, 
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return Inertia::render('Public/DatasetDetail', [
            'dataset' => $dataset,
            'tableData' => $paginatedData, // Data Paginasi (Tabel)
            'allData' => $fullChartData,   // Data Full (Chart & Selector)
            'customSatuan' => $finalSatuan // Satuan (Excel atau Master)
        ]);
    }
}