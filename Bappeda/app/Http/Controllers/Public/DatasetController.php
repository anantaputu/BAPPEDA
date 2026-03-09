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
        if ($request->filled('search')) $query->where('nama_data', 'like', '%' . $request->search . '%');

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
        if ($request->filled('q')) $query->where('nama_data', 'like', '%' . $request->q . '%');

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
        $dataset = Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'katakunci', 'values', 'uploads'])->findOrFail($id);

        $sortedValues = $dataset->values
            ->filter(fn ($value) => !empty($value->tahun))
            ->sortBy('tahun')
            ->values();

        // Fallback: jika tahun_terbit kosong, ambil periode terakhir dari data values.
        $dataset->tahun = $dataset->tahun_terbit ?: optional($sortedValues->last())->tahun;

        // 2. Format Data Dasar
        $rowObject = [
            'id_data'        => $dataset->id_data,
            'nama_data'      => $dataset->nama_data,
            'Nama Indikator' => $dataset->nama_data,
            'Satuan'         => $dataset->satuan ?? '-',
        ];

        // ... (kode bongkar JSON informasi_tambahan Anda tetap sama)
        if (!empty($dataset->informasi_tambahan)) {
            $extraFields = json_decode($dataset->informasi_tambahan, true);
            if (is_array($extraFields)) {
                foreach ($extraFields as $key => $val) {
                    if (strtolower($key) !== 'nama data' && strtolower($key) !== 'nama indikator') {
                        $rowObject[$key] = $val;
                    }
                }
            }
        }

        // 4. Masukkan nilai waktu (tahun/periode)
        foreach ($sortedValues as $val) {
            $rowObject[$val->tahun] = $val->nilai;
        }

        $fullChartData = [$rowObject];
        $tableData = $fullChartData;

        $isPinned = false;
        if (auth()->check()) {
            $isPinned = DB::table('bookmark')
                ->where('user_id', auth()->id())
                ->where('dataset_id', $dataset->id_data)
                ->exists();
        }

        $dataset->is_pinned = $isPinned;

        $numericValues = $sortedValues
            ->map(fn ($v) => is_numeric($v->nilai) ? (float) $v->nilai : null)
            ->filter(fn ($v) => $v !== null)
            ->values();

        $valueStats = [
            'jumlah_periode' => $sortedValues->count(),
            'nilai_terkini' => optional($sortedValues->last())->nilai,
            'periode_terkini' => optional($sortedValues->last())->tahun,
            'nilai_terendah' => $numericValues->isNotEmpty() ? $numericValues->min() : null,
            'nilai_tertinggi' => $numericValues->isNotEmpty() ? $numericValues->max() : null,
            'rata_rata' => $numericValues->isNotEmpty() ? round($numericValues->avg(), 2) : null,
        ];

        $datasetMeta = [
            'nama_data' => $dataset->nama_data,
            'satuan' => $dataset->satuan ?: '-',
            'sumber' => $dataset->sumber ?: '-',
            'tahun_terbit' => $dataset->tahun_terbit ?: '-',
            'tema' => $dataset->tema?->nama_tema ?: '-',
            'urusan' => $dataset->urusan?->nama_urusan ?: '-',
            'bidang' => $dataset->bidang?->nama_bidang ?: '-',
            'frekuensi' => $dataset->frekuensi?->nama_frekuensi ?: '-',
            'deskripsi' => $dataset->deskripsi ?: null,
            'created_at' => optional($dataset->created_at)->format('d M Y, H:i'),
            'updated_at' => optional($dataset->updated_at)->format('d M Y, H:i'),
        ];

        return Inertia::render('Public/DatasetDetail', [
            'dataset'   => $dataset,
            'tableData' => $tableData,
            'allData'   => $fullChartData,
            'valueStats' => $valueStats,
            'datasetMeta' => $datasetMeta,
        ]);
    }
}
