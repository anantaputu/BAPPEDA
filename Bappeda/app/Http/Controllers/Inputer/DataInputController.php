<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\{Data, Tema, Urusan, Bidang, Frekuensi,DataUpload};
use App\Services\DataUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataInputController extends Controller
{
    protected $uploadService;

    public function __construct(DataUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    private function getMetadata()
    {
        return [
            'tema'      => Tema::all(),
            'urusan'    => Urusan::all(),
            'bidang'    => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ];
    }

    // ==========================================
    // 1. RENDER HALAMAN (VIEWS)
    // ==========================================
   public function index()
{
    $userId = auth()->id();

    // 1. DATA UNTUK RIWAYAT UPLOAD (Tampilan CRUD)
    $recentUploads = DataUpload::with(['data.urusan', 'data.bidang', 'user'])
        ->where('id_user', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

    // 2. METADATA UNTUK FILTER SPREADSHEET
    $metadata = [
        'tema' => Tema::all(),
        'urusan' => Urusan::all(),
        'bidang' => Bidang::all(),
        'frekuensi' => Frekuensi::all(),
        'tahun_terbit' => Data::select('tahun_terbit')->distinct()->orderBy('tahun_terbit', 'desc')->pluck('tahun_terbit'),
    ];

    // 3. AMBIL DATA UTAMA UNTUK SPREADSHEET
   $query = \App\Models\Data::with(['values', 'tema', 'urusan', 'bidang', 'frekuensi'])
            ->where('id_user', $userId);

        // Terapkan filter jika ada request dari Vue
        if (request('search')) {
            $query->where('nama_indikator', 'ilike', '%' . request('search') . '%');
        }
        if (request('urusan')) $query->where('id_urusan', request('urusan'));
        if (request('bidang')) $query->where('id_bidang', request('bidang'));
        if (request('tema')) $query->where('id_tema', request('tema'));
        if (request('frekuensi')) $query->where('id_frekuensi', request('frekuensi'));
        if (request('tahun_terbit')) $query->where('tahun_terbit', request('tahun_terbit')); // Filter Tahun Terbit

        $allData = $query->get();
    // 4. GROUPING DATA (Untuk tampilan Spreadsheet)
    $groupedData = [];
    $timeColumnsSet = [];
    $groupBy = request('group_by', 'tema'); 

    foreach ($allData as $item) {
        $groupName = 'Lainnya';
        if ($groupBy === 'tema') $groupName = $item->tema->nama_tema ?? 'Tanpa Tema';
        if ($groupBy === 'urusan') $groupName = $item->urusan->nama_urusan ?? 'Tanpa Urusan';
        if ($groupBy === 'bidang') $groupName = $item->bidang->nama_bidang ?? 'Tanpa Bidang';
        if ($groupBy === 'frekuensi') $groupName = $item->frekuensi->nama_frekuensi ?? 'Tanpa Frekuensi';

        $groupedData[$groupName][] = $item;

        foreach ($item->values as $val) {
            $timeColumnsSet[$val->tahun] = true;
        }
    }

    $timeColumns = array_keys($timeColumnsSet);
    sort($timeColumns); 

    // RETURN KE HALAMAN INDEX / SPREADSHEET
    return Inertia::render('Inputer/Data/Index', [
        'recentUploads' => $recentUploads,
        'isAdmin'       => Auth::user()->role->nama_role === 'Admin',
        'groupedData'   => $groupedData,
        'timeColumns'   => $timeColumns,
        'metadata'      => $metadata,
        'filters'       => request()->all('search', 'tema', 'urusan', 'bidang', 'frekuensi', 'group_by', 'periode', 'tahun_terbit'),
    ]);
}

    public function createSingle()
    {
        return Inertia::render('Inputer/Data/SingleInput', $this->getMetadata());
    }

    public function createMulti()
    {
        return Inertia::render('Inputer/Data/MultiInput', $this->getMetadata());
    }

    public function edit($id)
    {
        return Inertia::render('Inputer/Data/Edit', array_merge(
            ['dataIndikator' => Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'values'])->findOrFail($id)],
            $this->getMetadata()
        ));
    }

    public function katalog(Request $request)
    {
        $query = Data::with(['tema', 'urusan', 'bidang']);
        if ($request->filled('search')) $query->where('nama_indikator', 'like', '%' . $request->search . '%');
        if ($request->filled('tema')) $query->where('id_tema', $request->tema);

        return Inertia::render('Inputer/Data/Katalog', [
            'indicators' => $query->latest()->paginate(15)->withQueryString(),
            'filters'    => $request->only(['search', 'tema']),
            'listTema'   => Tema::all()
        ]);
    }

    // ==========================================
    // 2. AKSI PROSES DATA (POST / PUT)
    // ==========================================
   public function storeSingle(Request $request)
{
    $request->validate([
        'nama_indikator' => 'required|string|max:255',
        'id_tema'        => 'required',
        'id_urusan'      => 'required',
        'id_bidang'      => 'required',
        'id_frekuensi'   => 'required',
        'satuan'         => 'required|string',
        'sumber'         => 'required|string',
        'deskripsi'      => 'nullable|string',
        'values'         => 'required|array|min:1',
        'tahun_terbit'   => 'nullable|integer',
        
        // Validasi tiap item di dalam array values
        'values.*.tahun' => 'required', // String agar support "Januari 2024"
        'values.*.nilai' => 'required', 
    ]);

    try {
        // Panggil service yang sudah kita perbaiki sebelumnya
        $this->uploadService->processSingleData($request->all(), Auth::id());
        
        return redirect()->route('inputer.dashboard')
            ->with('message', 'Data Indikator Berhasil Disimpan!');
            
    } catch (\Exception $e) {
        // Log error untuk debugging jika masih gagal
        \Log::error('Gagal Simpan Single: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
    }
}

    public function previewExcel(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv,txt']);
        try {
            $result = $this->uploadService->getPreviewData($request->file('file'));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
public function storeBulk(Request $request)
    {\Log::info($request->all());
        try {
            // 1. Cek apakah datanya benar-benar terkirim dari Vue
            if (!$request->has('dataset') || empty($request->input('dataset'))) {
                throw new \Exception("Data Excel kosong atau gagal terbaca oleh sistem.");
            }

            // 2. Eksekusi Service (Hanya kirim 3 parameter, karena parameter ke-4 file_name sudah otomatis default di service)
            $this->uploadService->processBulkData(
                $request->input('dataset'), 
                $request->input('years'), 
                Auth::id()
            );

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Jika error, kembalikan pesan error aslinya agar bisa dibaca di Vue
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

public function update(Request $request, $id)
{
    // 1. Validasi murni di Controller
    $request->validate([
        'nama_indikator' => 'required|string|max:255',
        'id_tema'        => 'required',
        'id_urusan'      => 'required',
        'id_bidang'      => 'required',
        'id_frekuensi'   => 'required',
        'satuan'         => 'required|string',
        'sumber'         => 'nullable|string',
        'status'         => 'required|in:aktif,nonaktif',
        'deskripsi'      => 'nullable|string',
        'tahun_terbit'   => 'nullable|integer',
        
        // Validasi Array Nilai
        'values'         => 'required|array|min:1',
        'values.*.tahun' => 'required|string',
        'values.*.nilai' => 'required',
    ]);

    try {
        $this->uploadService->updateSingleData($id, $request->all(), Auth::id());
        
        $user = Auth::user();
        
        // [DIPERBAIKI DI SINI] 👇
        $isAdmin = (optional($user->role)->nama_role === 'Admin');

        // Jika admin, ke dashboard admin, jika bukan (inputer), ke dashboard inputer
        $routeName = $isAdmin ? 'admin.dashboard' : 'inputer.dashboard';

        return redirect()->route($routeName)
            ->with('message', 'Data berhasil diperbarui!');

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Gagal Update Single: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
    }
}

    public function destroy($id)
    {
        try {
            $data = \App\Models\Data::findOrFail($id);
            $data->delete();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}