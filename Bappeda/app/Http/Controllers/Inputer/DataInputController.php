<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataField;
use App\Models\DataFieldMapping;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Inertia\Inertia;

class DataInputController extends Controller
{
   public function index()
{
    $user = Auth::user();
    
    // Normalisasi Role
    $roleName = strtolower(
        is_object($user->role) ? ($user->role->nama_role ?? '') : ($user->nama_role ?? $user->role)
    );
    $isAdmin = ($roleName === 'admin');

    // --- 1. QUERY BUILDER ---
    // Siapkan query dasar
    $query = DataUpload::with(['data', 'user']); // Load 'user' juga biar Admin tahu siapa yg upload

    // Jika BUKAN Admin (Inputer biasa), batasi hanya data dia sendiri
    if (!$isAdmin) {
        $query->where('id_user', $user->id);
    }

    // --- 2. STATISTIK ---
    // Clone query agar filter di atas tetap terbawa ke statistik
    $statsQuery = clone $query; 
    
    $stats = [
        'total_upload' => (clone $statsQuery)->count(),
        'valid'        => (clone $statsQuery)->where('status', 'valid')->count(),
        'pending'      => (clone $statsQuery)->whereIn('status', ['processing', 'pending', 'draft'])->count(),
    ];

    // --- 3. DATA LIST (RECENT) ---
    $recentUploads = $query->latest()
        ->limit(10) // Tampilkan 10 terakhir
        ->get();

    return Inertia::render('Inputer/Data/Index', [
        'stats' => $stats,
        'recentUploads' => $recentUploads,
        'isAdmin' => $isAdmin // Kirim info role ke Vue untuk atur tampilan
    ]);
}
    public function createWizard()
    {
        // Mengirim data Master untuk Dropdown di Form Wizard
        return Inertia::render('Inputer/Data/Wizard', [
            'tema' => Tema::all(),
            'urusan' => Urusan::all(),
            'bidang' => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ]);
    }
    // STEP 1: Analisa File (Baca Header Excel)
    public function analyzeFile(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv']);

        $path = $request->file('file')->store('uploads/temp', 'private');
        $fullPath = Storage::disk('private')->path($path);

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Baca Excel dengan index kolom Huruf (A, B, C...)
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) throw new \Exception("File kosong");

            // Ambil Header (Baris 1) & Bersihkan kolom kosong
            $headerRaw = array_shift($rows);
            $headers = array_filter($headerRaw, function($val) {
                return !empty($val);
            });

            // Ambil Preview (5 Baris)
            $previewRaw = array_slice($rows, 0, 5);
            $preview = [];
            foreach($previewRaw as $row) {
                // Hanya ambil data pada kolom yang memiliki header
                $preview[] = array_intersect_key($row, $headers);
            }

            // --- LOGIKA MAPPING DI PINDAH KE SINI (BACKEND) ---
            $defaultMapping = [];
            $defaultNewFields = [];

            foreach ($headers as $colKey => $headerName) {
                // Default: Semua kolom dianggap "Field Baru" (__new__)
                $defaultMapping[$colKey] = '__new__';

                // Default: Config field baru (Nama sesuai header, tipe text)
                $defaultNewFields[$colKey] = [
                    'nama_field' => $headerName,
                    'tipe_field' => 'text'
                ];
            }

            // Tebak Nama Indikator dari Nama File
            $filename = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
            $suggestedName = Str::title(str_replace(['_', '-'], ' ', $filename));

            return response()->json([
                'status' => 'success',
                'temp_path' => $path,
                'headers' => $headers,
                'preview' => $preview,
                // Kirim data yang sudah matang ke Vue
                'default_mapping' => $defaultMapping,
                'default_new_fields' => $defaultNewFields,
                'suggested_name' => $suggestedName
            ]);

        } catch (\Exception $e) {
            Storage::disk('private')->delete($path);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
    // --- LOGIKA 2: SIMPAN DATA (Baca Ulang File & Insert DB) ---
    public function storeComplete(Request $request)
    {
        // Validasi Config
        $request->validate([
            'nama_indikator' => 'required',
            'file_path' => 'required', // Path file temp yang dikirim balik
            'new_fields' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            // 1. Pindahkan File dari Temp ke Permanent
            $oldPath = $request->file_path;
            $newPath = str_replace('uploads/temp', 'uploads/excel', $oldPath);
            
            if (Storage::disk('private')->exists($oldPath)) {
                Storage::disk('private')->move($oldPath, $newPath);
            } else {
                throw new \Exception("File expired atau tidak ditemukan. Silakan upload ulang.");
            }

            // 2. Simpan Metadata Utama
            $data = Data::create([
                'nama_indikator' => $request->nama_indikator,
                'deskripsi' => $request->deskripsi,
                'id_tema' => $request->id_tema,
                'id_urusan' => $request->id_urusan,
                'id_bidang' => $request->id_bidang,
                'id_frekuensi' => $request->id_frekuensi,
                'satuan' => $request->satuan,
                'sumber' => $request->sumber,
                'kata_kunci' => $request->kata_kunci,
                'tahun' => $request->periode,
                'status' => 'aktif',
            ]);

            // 3. Simpan Definisi Kolom (DataField)
            $columnMap = []; // Mapping: Huruf Excel (A) => ID Field Database (15)
            $typeMap = [];   // Mapping: ID Field => Tipe Data (number/text)

            foreach ($request->new_fields as $colKey => $config) {
                // $colKey = 'A', $config = ['nama_field' => '...', 'tipe_field' => '...']
                
                $field = DataField::create([
                    'id_data' => $data->id_data,
                    'nama_field' => $config['nama_field'],
                    'key_field' => Str::slug($config['nama_field'], '_'),
                    'tipe_field' => $config['tipe_field'] ?? 'text',
                ]);

                $columnMap[$colKey] = $field->id_field;
                $typeMap[$field->id_field] = $config['tipe_field'] ?? 'text';
            }

            // 4. Baca Ulang File Excel untuk Ambil Semua Data
            $fullPath = Storage::disk('private')->path($newPath);
            $spreadsheet = IOFactory::load($fullPath);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            
            array_shift($rows); // Buang Baris Header (Baris 1)

            // 5. Susun JSON Data
            $jsonPayload = [];
            foreach ($rows as $row) {
                $rowData = [];
                $hasData = false;

                foreach ($columnMap as $colKey => $fieldId) {
                    $val = $row[$colKey] ?? null;
                    
                    // Bersihkan & Format Data
                    if ($val !== null && trim($val) !== '') {
                        $hasData = true;
                        
                        // Jika tipe number, bersihkan format angka (misal: "1.000,50" -> 1000.50)
                        if (($typeMap[$fieldId] ?? 'text') === 'number') {
                            // Hapus titik ribuan, ganti koma desimal jadi titik
                            // Logic sederhana: hapus semua non-angka/koma/titik/minus
                            $valClean = preg_replace('/[^0-9,\.\-]/', '', $val);
                            // Asumsi standar Indonesia: titik = ribuan, koma = desimal
                            // Jika ada koma, ganti jadi titik agar dibaca float oleh PHP
                            if (strpos($valClean, ',') !== false && strpos($valClean, '.') !== false) {
                                // Ada titik dan koma (misal 1.000,50) -> hapus titik, ganti koma
                                $valClean = str_replace('.', '', $valClean);
                                $valClean = str_replace(',', '.', $valClean);
                            } elseif (strpos($valClean, ',') !== false) {
                                // Hanya koma (10,5) -> ganti titik
                                $valClean = str_replace(',', '.', $valClean);
                            }
                            $val = $valClean; 
                        }
                    }
                    
                    $rowData[$fieldId] = $val;
                }

                // Hanya simpan baris yang tidak kosong total
                if ($hasData) {
                    $jsonPayload[] = $rowData;
                }
            }

            // 6. Simpan Data Upload (JSON)
            DataUpload::create([
                'id_data' => $data->id_data,
                'id_user' => Auth::id(),
                'periode' => $request->periode,
                'file_path' => $newPath,
                'value' => $jsonPayload, // Simpan array hasil parsing
                'status' => 'valid'
            ]);

            DB::commit();
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}