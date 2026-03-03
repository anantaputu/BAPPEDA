<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataField;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Bookmark;

class DatasetController extends Controller
{

    public function spreadsheetView(Request $request)
    {
        // 1. Query Dasar
        $query = Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'values'])
            ->where('status', 'aktif');

        // 2. Terapkan Filter (Jika user memilih dari dropdown)
        if ($request->filled('tema')) $query->where('id_tema', $request->tema);
        if ($request->filled('urusan')) $query->where('id_urusan', $request->urusan);
        if ($request->filled('bidang')) $query->where('id_bidang', $request->bidang);
        if ($request->filled('frekuensi')) $query->where('id_frekuensi', $request->frekuensi);
        if ($request->filled('search')) $query->where('nama_indikator', 'like', '%' . $request->search . '%');

        $allData = $query->get();

        // 3. Ambil Header Kolom Waktu (Tahun/Bulan/Hari)
        // Kita ambil dari data yang sudah difilter agar kolomnya relevan
        $timeColumns = \App\Models\DataValue::whereIn('id_data', $allData->pluck('id_data'))
            ->distinct()
            ->select('tahun') // Asumsi kolom waktu di DB bernama 'tahun' atau 'dimensi'
            // Gunakan logic sorting yang cerdas (agar Jan 2024 urut, atau 2024, 2025 urut)
            ->orderBy('tahun', 'asc') 
            ->pluck('tahun');

        // 4. Logika Grouping Dinamis
        // Default group by 'tema', tapi user bisa ganti jadi 'urusan' atau 'bidang'
        $groupByParam = $request->input('group_by', 'tema');
        
        $groupedData = $allData->groupBy(function($item) use ($groupByParam) {
            switch ($groupByParam) {
                case 'urusan':
                    return $item->urusan->nama_urusan ?? 'Tanpa Urusan';
                case 'bidang':
                    return $item->bidang->nama_bidang ?? 'Tanpa Bidang';
                case 'frekuensi':
                    return $item->frekuensi->nama_frekuensi ?? 'Tanpa Frekuensi';
                default:
                    return $item->tema->nama_tema ?? 'Tanpa Tema';
            }
        });

        // 5. Kirim ke Vue
        return Inertia::render('Public/SpreadsheetView', [
            'groupedData' => $groupedData,
            'timeColumns' => $timeColumns,
            'filters'     => $request->all(),
            // Kirim semua opsi untuk dropdown filter
            'metadata'    => [
                'tema'      => \App\Models\Tema::all(),
                'urusan'    => \App\Models\Urusan::all(),
                'bidang'    => \App\Models\Bidang::all(),
                'frekuensi' => \App\Models\Frekuensi::all(),
            ]
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
    // DatasetController.php
public function toggleBookmark($id)
{
    $user = auth()->user();
    
    // Cek apakah sudah di-pin
    $existingBookmark = Bookmark::where('user_id', $user->id)
        ->where('user_id', $user->id)
        ->where('dataset_id', $id)
        ->first();

   if ($existingBookmark) {
        // Hapus jika ada
        $existingBookmark->delete();
    } else {

        Bookmark::create([
            'user_id' => $user->id,
            'dataset_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return back(); 
}

   public function show(Request $request, $id)
    {
      
        $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'values', 'uploads'])->findOrFail($id);

        // 2. Format Data Dasar
        $rowObject = [
            'Nama Indikator' => $dataset->nama_indikator,
            'Satuan'         => $dataset->satuan ?? '-',
        ];

        // ========================================================
        // [PERBAIKAN] 3. BONGKAR JSON INFORMASI TAMBAHAN KE KOLOM
        // ========================================================
        if (!empty($dataset->informasi_tambahan)) {
            $extraFields = json_decode($dataset->informasi_tambahan, true);
            if (is_array($extraFields)) {
                foreach ($extraFields as $key => $val) {
                    // Hindari duplikasi kolom Nama Data
                    if (strtolower($key) !== 'nama data' && strtolower($key) !== 'nama indikator') {
                        $rowObject[$key] = $val;
                    }
                }
            }
        }

        // 4. Masukkan nilai waktu (Tahun/Bulan)
        $sortedValues = $dataset->values->sortBy('tahun');
        foreach ($sortedValues as $val) {
            $rowObject[$val->tahun] = $val->nilai;
        }

        $fullChartData = [$rowObject];

        // 5. Paginasi statis
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $fullChartData, 1, 20, 1,
            ['path' => $request->url()]
        );

    $isPinned = DB::table('bookmark')
    ->where('user_id', auth()->id())
    ->where('dataset_id', $dataset->id_data)
    ->exists();

    $dataset->is_pinned = $isPinned;

        return Inertia::render('Public/DatasetDetail', [
            'dataset'   => $dataset,
            'tableData' => $paginatedData,
            'allData'   => $fullChartData,
        ]);
    }
}