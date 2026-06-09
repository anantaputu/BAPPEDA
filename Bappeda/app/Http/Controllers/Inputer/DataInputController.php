<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\{Data, Tema, Urusan, Bidang, Frekuensi, Katakunci, DataUpload, ActivityLog};
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

    /**
     * Helper untuk mengambil data master yang konsisten di seluruh halaman input
     */
    private function getMetadata()
    {
        return [
            'tema'      => Tema::orderBy('nama_tema')->get(),
            'urusan'    => Urusan::orderBy('nama_urusan')->get(),
            'bidang'    => Bidang::orderBy('nama_bidang')->get(),
            'frekuensi' => Frekuensi::all(),
            'katakunci' => Katakunci::orderBy('nama_katakunci')->get(),
        ];
    }

    private function getDashboardRouteByRole(): string
    {
        $user = Auth::user();
        $isAdmin = optional($user->role)->nama_role === 'Admin';

        return $isAdmin ? 'admin.dashboard' : 'inputer.dashboard';
    }

    private function applyNamaDataSearch($query, ?string $search): void
    {
        $keyword = strtolower(trim((string) $search));
        if ($keyword === '') {
            return;
        }

        $query->whereRaw('LOWER(TRIM(nama_data)) LIKE ?', ['%' . $keyword . '%']);
    }

    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $isAdmin = optional($user->role)->nama_role === 'Admin'; 

        // Ambil ID aktivitas terbaru per indikator untuk Riwayat
        $latestUploadIds = DataUpload::selectRaw('MAX(id_upload) as id_upload')
            ->groupBy('id_data')
            ->pluck('id_upload');

        $recentUploadsQuery = DataUpload::with(['data.urusan', 'data.bidang', 'user'])
            ->whereIn('id_upload', $latestUploadIds)
            ->orderBy('created_at', 'desc');

        if (!$isAdmin) {
            $recentUploadsQuery->where('id_user', $userId);
        }
        $recentUploads = $recentUploadsQuery->get();

        return Inertia::render('Inputer/Data/Index', [
            'recentUploads' => $recentUploads,
            'isAdmin'       => $isAdmin,    
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
        $data = Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'katakunci', 'values'])->findOrFail($id);
        
        $user = Auth::user();
        $isAdmin = optional($user->role)->nama_role === 'Admin';
        
        if (!$isAdmin && $data->id_user !== $user->id) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki hak untuk mengedit data ini.');
        }

        return Inertia::render('Inputer/Data/Edit', array_merge(
            [
                'dataIndikator' => $data
            ],
            $this->getMetadata()
        ));
    }

    public function katalog(Request $request)
    {
        $query = Data::with(['tema', 'urusan', 'bidang', 'katakunci']);
        $this->applyNamaDataSearch($query, $request->search);
        if ($request->filled('tema')) $query->where('id_tema', $request->tema);

        return Inertia::render('Inputer/Data/Katalog', [
            'indicators' => $query->latest()->paginate(15)->withQueryString(),
            'filters'    => $request->only(['search', 'tema']),
            'listTema'   => Tema::orderBy('nama_tema')->get()
        ]);
    }

    public function storeSingle(Request $request)
    {
        $request->validate([
            'nama_data'      => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = Data::query()
                        ->whereRaw('LOWER(TRIM(nama_data)) = LOWER(TRIM(?))', [$value])
                        ->exists();

                    if ($exists) {
                        $fail('Nama indikator sudah ada. Gunakan nama lain.');
                    }
                },
            ],
            'id_tema'        => 'required',
            'id_urusan'      => 'required',
            'id_bidang'      => 'required',
            'id_frekuensi'   => 'required',
            'satuan'         => 'required|string',
            'sumber'         => 'required|string',
            'deskripsi'      => 'nullable|string',
            'values'         => 'required|array|min:1',
            'tahun_terbit'   => 'nullable|integer',
            'id_katakunci'   => 'nullable|array',
            'values.*.tahun' => 'required|string',
            'values.*.nilai' => 'required',
        ]);

        try {
            $this->uploadService->processSingleData($request->all(), Auth::id());
            return redirect()->route($this->getDashboardRouteByRole())->with('success', 'Data Indikator Berhasil Disimpan!');
        } catch (\Exception $e) {
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
            if (!$request->has('dataset') || empty($request->input('dataset'))) {
                throw new \Exception("Data Excel kosong atau gagal terbaca.");
            }
            $this->uploadService->processBulkData($request->input('dataset'), $request->input('years'), Auth::id());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $dataMaster = Data::findOrFail($id);

        $user = Auth::user();
        $isAdmin = optional($user->role)->nama_role === 'Admin';
        
        if (!$isAdmin && $dataMaster->id_user !== $user->id) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki hak untuk mengubah data ini.');
        }

        $request->validate([
            'nama_data'      => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($dataMaster) {
                    $exists = Data::query()
                        ->where('id_data', '!=', $dataMaster->id_data)
                        ->whereRaw('LOWER(TRIM(nama_data)) = LOWER(TRIM(?))', [$value])
                        ->exists();

                    if ($exists) {
                        $fail('Nama indikator sudah ada. Gunakan nama lain.');
                    }
                },
            ],
            'id_tema'        => 'required',
            'id_urusan'      => 'required',
            'id_bidang'      => 'required',
            'id_frekuensi'   => 'required',
            'satuan'         => 'required|string',
            'sumber'         => 'nullable|string',
            'deskripsi'      => 'nullable|string',
            'tahun_terbit'   => 'nullable|integer',
            'id_katakunci'   => 'nullable|array',
            'values'         => 'required|array|min:1',
            'values.*.tahun' => 'required|string',
            'values.*.nilai' => 'required',
        ]);

        try {
            $this->uploadService->updateSingleData($id, $request->all(), Auth::id());
            return redirect()->route($this->getDashboardRouteByRole())->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal Update Single: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $data = Data::findOrFail($id);

            $user = Auth::user();
            $isAdmin = optional($user->role)->nama_role === 'Admin';
            
            if (!$isAdmin && $data->id_user !== $user->id) {
                abort(403, 'Akses Ditolak. Anda tidak memiliki hak untuk menghapus data ini.');
            }

            $namaData = $data->nama_data;

            // CATAT LOG AKTIVITAS SEBELUM DELETE (Gunakan variabel yang ada di scope)
            ActivityLog::create([
                'id_user'     => Auth::id(),
                'id_data'     => $id,
                'action'      => 'DELETE',
                'target_name' => $namaData,
                'description' => 'Menghapus data indikator secara permanen',
                'ip_address'  => request()->ip()
            ]);

            $data->delete();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
