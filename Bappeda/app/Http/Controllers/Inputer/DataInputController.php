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
            ['dataIndikator' => Data::findOrFail($id)],
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
            'tahun'          => 'required|integer',
            'nilai'          => 'required',
            'satuan'         => 'required|string',
            'sumber'         => 'required|string', // Pastikan kolom ini divalidasi
            'deskripsi'      => 'nullable|string',
        ]);

        try {
            $this->uploadService->processSingleData($request->all(), Auth::id());
            return redirect()->route('inputer.dashboard')->with('message', 'Data Berhasil Disimpan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
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
        $validated = $request->validate([
            'nama_indikator' => 'required|string|max:255',
            'deskripsi'      => 'nullable|string',
            'id_tema'        => 'required',
            'id_urusan'      => 'required',
            'id_bidang'      => 'required',
            'id_frekuensi'   => 'required', // Tambahkan frekuensi di update
            'satuan'         => 'required|string',
            'sumber'         => 'nullable|string',
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        Data::findOrFail($id)->update($validated);
        return redirect()->route('inputer.dashboard')->with('message', 'Data berhasil diperbarui!');
    }
}