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

        // 1. Ambil ID aktivitas terbaru per indikator untuk Riwayat
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

        // 2. Metadata untuk Filter dan Dropdown di Spreadsheet
        $metadata = [
            'tema'         => Tema::orderBy('nama_tema')->get(),
            'urusan'       => Urusan::orderBy('nama_urusan')->get(),
            'bidang'       => Bidang::orderBy('nama_bidang')->get(),
            'frekuensi'    => Frekuensi::all(),
            'tahun_terbit' => Data::select('tahun_terbit')
                                ->whereNotNull('tahun_terbit')
                                ->distinct()
                                ->orderBy('tahun_terbit', 'desc')
                                ->pluck('tahun_terbit'),
        ];

        // 3. Query Utama Data Indikator (Spreadsheet)
        $query = Data::with(['values', 'tema', 'urusan', 'bidang', 'frekuensi', 'katakunci']);

        if (!$isAdmin) {
            $query->where('id_user', $userId);
        }

        // --- Logic Filtering ---
        $this->applyNamaDataSearch($query, request('search'));
        if (request('urusan')) $query->where('id_urusan', request('urusan'));
        if (request('bidang')) $query->where('id_bidang', request('bidang'));
        if (request('tema'))   $query->where('id_tema', request('tema'));
        if (request('frekuensi')) $query->where('id_frekuensi', request('frekuensi'));
        if (request('tahun_terbit')) $query->where('tahun_terbit', request('tahun_terbit'));

        $allData = $query->get();

        // 4. Grouping Data untuk View Spreadsheet
        $groupedData = [];
        $timeColumnsSet = [];
        $groupBy = request('group_by', 'tema'); 

        foreach ($allData as $item) {
            $groupName = 'Lainnya';
            if ($groupBy === 'tema')      $groupName = $item->tema->nama_tema ?? 'Tanpa Tema';
            if ($groupBy === 'urusan')    $groupName = $item->urusan->nama_urusan ?? 'Tanpa Urusan';
            if ($groupBy === 'bidang')    $groupName = $item->bidang->nama_bidang ?? 'Tanpa Bidang';
            if ($groupBy === 'frekuensi') $groupName = $item->frekuensi->nama_frekuensi ?? 'Tanpa Frekuensi';

            $groupedData[$groupName][] = $item;

            foreach ($item->values as $val) {
                $timeColumnsSet[$val->tahun] = true;
            }
        }

        $timeColumns = array_keys($timeColumnsSet);
        sort($timeColumns); 

        return Inertia::render('Inputer/Data/Index', [
            'recentUploads' => $recentUploads,
            'isAdmin'       => $isAdmin,    
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
            [
                'dataIndikator' => Data::with(['tema', 'urusan', 'bidang', 'frekuensi', 'katakunci', 'values'])->findOrFail($id)
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
