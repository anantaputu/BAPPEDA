<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\{Data, Tema, Urusan, Bidang, Frekuensi};
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
        $user = Auth::user();
        $isAdmin = (strtolower($user->role->nama_role ?? '') === 'admin');

        // PERBAIKAN: Query harus ke model DataUpload agar riwayatnya muncul
        // Kita panggil relasi 'data' (untuk nama indikator) dan 'user' (untuk operator)
        $query = \App\Models\DataUpload::with(['data', 'user']); 

        // Jika bukan admin, hanya lihat upload miliknya sendiri
        if (!$isAdmin) {
            $query->where('id_user', $user->id);
        }

        // Ambil riwayat terbaru
        $recentUploads = $query->latest()->limit(10)->get();

        // Query terpisah untuk statistik (opsional jika masih ingin menghitung di backend)
        $statsQuery = \App\Models\Data::query();
        if (!$isAdmin) $statsQuery->where('id_user', $user->id);

        return Inertia::render('Inputer/Data/Index', [
            'stats' => [
                'total_upload' => (clone $statsQuery)->count(),
                'valid'        => (clone $statsQuery)->where('status', 'aktif')->count(),
                'pending'      => (clone $statsQuery)->where('status', 'nonaktif')->count(),
            ],
            'recentUploads' => $recentUploads,
            'isAdmin'       => $isAdmin
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
    {
        try {
            $this->uploadService->processBulkData($request->input('dataset'), $request->input('years'), Auth::id());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
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
            
            // Validasi Array Nilai
            'values'         => 'required|array|min:1',
            'values.*.tahun' => 'required|string',
            'values.*.nilai' => 'required',
        ]);

        try {
            // 2. Lempar data ke Service yang baru kita buat
            $this->uploadService->updateSingleData($id, $request->all(), Auth::id());
            
            return redirect()->route('inputer.dashboard')
                ->with('message', 'Data berhasil diperbarui!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal Update Single: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }
}