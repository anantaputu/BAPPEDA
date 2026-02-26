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

    public function show(Request $request, $id)
    {
        // 1. Ambil Data Master berserta relasi Metadata dan Values (Nilainya)
        $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'katakunci'])->findOrFail($id);

        // 2. Format Data untuk Tabel dan Grafik Vue
        // Karena ini halaman detail 1 indikator, kita jadikan 1 baris (row)
        $rowObject = [
            'Nama Indikator' => $dataset->nama_indikator,
            'Satuan'         => $dataset->satuan ?? '-',
        ];

        // 3. Masukkan nilai tahun dan angka dari DataValue ke dalam baris tersebut
        // Urutkan berdasarkan tahun agar grafik dari kiri ke kanan (Masa lalu ke masa depan)
        $sortedValues = $dataset->values->sortBy('tahun');
        foreach ($sortedValues as $val) {
            $rowObject[$val->tahun] = $val->nilai;
        }

        // Bungkus dalam array karena Vue mengharapkan bentuk List of Objects
        $fullChartData = [$rowObject];

        // 4. Paginasi buatan (karena hanya 1 baris, kita buat paginator statis agar Vue tidak error)
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $fullChartData, 1, 20, 1,
            ['path' => $request->url()]
        );

        return Inertia::render('Public/DatasetDetail', [
            'dataset'   => $dataset,
            'tableData' => $paginatedData,
            'allData'   => $fullChartData,
        ]);
    }
}