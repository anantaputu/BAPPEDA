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

    public function spreadsheetView(Request $request)
{
    // 1. Ambil semua data indikator beserta nilai tahunannya
    $query = Data::with(['tema', 'urusan', 'bidang', 'values'])
        ->where('status', 'aktif');

    // Filter jika ada (Tema/Urusan/Bidang)
    if ($request->filled('tema')) $query->where('id_tema', $request->tema);

    $allData = $query->get();

    // 2. Ambil semua list tahun yang unik dari seluruh indikator untuk jadi Header Kolom
    $years = \App\Models\DataValue::whereIn('id_data', $allData->pluck('id_data'))
                ->distinct()
                ->orderBy('tahun', 'asc')
                ->pluck('tahun');

    // 3. Kelompokkan data berdasarkan Tema untuk tampilan "Grouping"
    $groupedData = $allData->groupBy(function($item) {
        return $item->tema->nama_tema ?? 'Tanpa Tema';
    });

    return Inertia::render('Public/SpreadsheetView', [
        'groupedData' => $groupedData,
        'allYears'    => $years,
        'listTema'    => \App\Models\Tema::all(),
        'filters'     => $request->only(['tema'])
    ]);
}

    
    public function index(Request $request)
{
    $query = Data::with(['tema', 'urusan', 'bidang'])->where('status', 'aktif');

    // Filter dinamis
    if ($request->filled('tema')) $query->where('id_tema', $request->tema);
    if ($request->filled('urusan')) $query->where('id_urusan', $request->urusan);
    if ($request->filled('bidang')) $query->where('id_bidang', $request->bidang);
    if ($request->filled('q')) $query->where('nama_indikator', 'like', '%' . $request->q . '%');

    return Inertia::render('Public/Katalog', [
        'indicators' => $query->latest()->paginate(12)->withQueryString(),
        'filters'    => $request->all(),
        // Kirim semua daftar metadata untuk dropdown filter
        'listTema'    => \App\Models\Tema::all(),
        'listUrusan'  => \App\Models\Urusan::all(),
        'listBidang'  => \App\Models\Bidang::all(),
    ]);
}

   public function show(Request $request, $id)
{
    $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi'])->where('id_data', $id)->firstOrFail();
    $upload = DataUpload::where('id_data', $id)->where('status', 'valid')->latest()->first();

    $fullChartData = [];
    $paginatedData = null;

    if ($upload && !empty($upload->value)) {
        $rawData = $upload->value; // Mengambil array dari kolom JSON
        $headerRow = array_shift($rawData); // Baris pertama dijadikan nama kolom

        foreach ($rawData as $row) {
            $newRow = [];
            foreach ($row as $key => $val) {
                // Mengubah kunci 'A', 'B' menjadi 'Nama Indikator', '2025'
                $fieldName = trim($headerRow[$key] ?? $key);
                $newRow[$fieldName] = $val;
            }
            $fullChartData[] = $newRow;
        }

        // Proses Paginasi untuk Tabel
        $perPage = 20;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $chunkData = array_slice($fullChartData, ($currentPage - 1) * $perPage, $perPage);

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $chunkData, count($fullChartData), $perPage, $currentPage,
            ['path' => $request->url()]
        );
    }

    return Inertia::render('Public/DatasetDetail', [
        'dataset' => $dataset,
        'tableData' => $paginatedData,
        'allData' => $fullChartData,
    ]);
}
}