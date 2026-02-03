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
    // 1. List Metadata (Halaman Utama)
    public function index()
{
    // Ambil data upload, urutkan dari yang terbaru
    // 'with('data')' penting agar kita bisa ambil nama_indikator dari tabel master
    $uploads = DataUpload::with('data')
        ->orderBy('created_at', 'desc')
        ->get();

    return Inertia::render('Inputer/Data/Index', [
        'uploads' => $uploads
    ]);
}

    // 2. Form Tambah Master Data Baru
    // (Diganti nama jadi createMasterData agar tidak bentrok dengan upload)
    public function createMasterData()
    {
        return inertia('Inputer/Data/Create', [
            'tema' => Tema::all(),
            'urusan' => Urusan::all(),
            'bidang' => Bidang::all(),
            'frekuensi' => Frekuensi::all(),
        ]);
    }

    // 3. Simpan Master Data Baru
    public function storeNewData(Request $request)
    {
        $validated = $request->validate([
            'nama_indikator' => 'required|string',
            'deskripsi' => 'nullable|string',
            'id_tema' => 'required',
            'id_urusan' => 'required',
            'id_bidang' => 'required',
            'id_frekuensi' => 'required',
            'satuan' => 'required|string',
            'sumber' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
        ]);

        $data = Data::create([
            'nama_indikator' => $validated['nama_indikator'],
            'deskripsi' => $validated['deskripsi'],
            'id_tema' => $validated['id_tema'],
            'id_urusan' => $validated['id_urusan'],
            'id_bidang' => $validated['id_bidang'],
            'id_frekuensi' => $validated['id_frekuensi'],
            'satuan' => $validated['satuan'],
            'sumber' => $validated['sumber'],
            'kata_kunci' => $validated['kata_kunci'],
            'status' => 'aktif',
            'tahun_data' => date('Y'),
        ]);

        // ALUR: Setelah simpan Data Master -> Lanjut ke Halaman Upload Excel
        return redirect()->route('inputer.create', $data->id_data)
            ->with('success', 'Master Data berhasil dibuat. Silakan upload file Excel.');
    }

    // 4. Form Upload Excel (Method 'create' yang asli untuk Route /{data})
    public function create(Data $data)
    {
        // Pastikan Anda nanti membuat file Vue: Inputer/Data/Upload.vue
        // Atau jika ingin menggunakan modal, sesuaikan logika ini.
        // Di sini kita asumsikan ada halaman khusus untuk upload.
        return inertia('Inputer/Data/Upload', [
            'data' => $data
        ]);
    }

    // 5. Simpan File Excel (Proses Upload)
    public function store(Request $request, Data $data)
    {
        $request->validate([
            'periode' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        // Cek apakah sudah ada data valid di periode yang sama
        $existingValid = DataUpload::where('id_data', $data->id_data)
            ->where('periode', $request->periode)
            ->where('status', 'valid') 
            ->exists();

        if ($existingValid) {
            return back()->withErrors(['periode' => 'Data Valid untuk periode ini sudah ada.']);
        }

        // Hapus data processing lama jika ada (untuk re-upload)
        DataUpload::where('id_data', $data->id_data)
            ->where('periode', $request->periode)
            ->where('status', 'processing')
            ->delete();

        $path = $request->file('file')->store('uploads/excel', 'private');

        $upload = DataUpload::create([
            'id_data' => $data->id_data,
            'id_user' => Auth::id(),
            'periode' => $request->periode,
            'file_path' => $path,
            'status' => 'processing'
        ]);

        // Lanjut ke Mapping
        return redirect()->route('inputer.mapping', $upload->id_upload);
    }

    // 6. Halaman Mapping
    public function mapping(DataUpload $upload)
    {
        if (!Storage::disk('private')->exists($upload->file_path)) {
            return back()->withErrors(['file' => 'File tidak ditemukan di server.']);
        }

        $path = Storage::disk('private')->path($upload->file_path);
        
        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $allRows = $sheet->toArray(null, true, true, true);
            
            if (empty($allRows)) {
                return back()->withErrors(['file' => 'File Excel kosong.']);
            }

            $header = array_shift($allRows); // Ambil baris pertama sebagai header
            $previewData = array_slice($allRows, 0, 5); // Ambil 5 baris data untuk preview

            $fields = DataField::where('id_data', $upload->id_data)->get();

            // Auto-mapping sederhana berdasarkan kesamaan nama
            $autoMap = [];
            foreach ($header as $colKey => $colName) {
                if (!$colName) continue;
                $normCol = $this->normalize($colName);

                foreach ($fields as $field) {
                    if ($normCol === $this->normalize($field->nama_field) 
                        || $normCol === $this->normalize($field->key_field)) {
                        $autoMap[$colKey] = $field->id_field;
                        break;
                    }
                }
            }

            return inertia('Inputer/Data/Mapping', [
                'upload'       => $upload,
                'excelColumns' => $header,
                'previewData'  => $previewData,
                'fields'       => $fields,
                'autoMap'      => $autoMap
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
        }
    }

    // 7. Simpan Mapping & Generate Field Baru
    // Simpan Mapping
   public function storeMapping(Request $request, DataUpload $upload)
    {

        $request->validate(['mapping' => 'nullable|array']);

        DB::transaction(function () use ($request, $upload) {
            // Hapus mapping lama
            DataFieldMapping::where('id_upload', $upload->id_upload)->delete();

            // Cek apakah ada data mapping yang dikirim
            if ($request->has('mapping') && is_array($request->mapping)) {
                
                foreach ($request->mapping as $excelCol => $fieldId) {
                    if (!$fieldId) continue; 

                    DataFieldMapping::create([
                        'id_upload' => $upload->id_upload,
                        'excel_column' => $excelCol,
                        'id_field' => $fieldId
                    ]);
                }
            }
        });

        return $this->parse($upload);
    }

    // 8. Parsing Data Excel ke JSONB (Finalisasi)
   public function parse(DataUpload $upload)
    {
        // PERBAIKAN: Pastikan ini 'data_mappings' (sesuai database Anda)
        // BUKAN 'data_field_mappings'
        $mappings = DB::table('data_mappings') 
            ->where('id_upload', $upload->id_upload)
            ->pluck('id_field', 'excel_column'); 

        // if ($mappings->isEmpty()) {
        //     return back()->withErrors(['mapping' => 'Gagal menyimpan mapping atau tabel kosong.']);
        // }

        
        $fieldsInfo = DataField::where('id_data', $upload->id_data)
            ->get()
            ->keyBy('id_field'); 

        $path = Storage::disk('private')->path($upload->file_path);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        
        $rows = $sheet->toArray(null, true, true, true);
        array_shift($rows); // Hapus header

        $jsonPayload = [];

        foreach ($rows as $row) {
            $rowData = [];
            $hasData = false;

            foreach ($mappings as $colKey => $fieldId) {
                $val = $row[$colKey] ?? null;
                $fieldInfo = $fieldsInfo[$fieldId] ?? null;
                $type = $fieldInfo->tipe_field ?? 'text';
                
                // Sanitasi sederhana
                if ($val !== null && trim($val) !== '' && $type === 'number') {
                    $cleanVal = preg_replace('/[^0-9]/', '', $val);
                    $val = $cleanVal === '' ? null : $cleanVal;
                }

                $rowData[$fieldId] = $val;
                $hasData = true;
            }

            if ($hasData) {
                $jsonPayload[] = $rowData;
            }
        }

        // Update DataUpload
        $upload->update([
            'value' => $jsonPayload, 
            'status' => 'valid' 
        ]);

        return redirect()
            ->route('inputer.index')
            ->with('success', 'Data berhasil diproses dan disimpan.');
    }
    
    // 9. Dashboard Statistik
    public function dashboard()
    {
        $userId = Auth::id();

        $stats = [
            'totalTugas' => Data::where('status', 'aktif')->count(),
            'pendingMapping' => DataUpload::where('id_user', $userId)->where('status', 'processing')->count(),
            'suksesUpload' => DataUpload::where('id_user', $userId)->where('status', 'valid')->count(),
        ];

        $recentUploads = DataUpload::with('data')
            ->where('id_user', $userId)
            ->latest()
            ->take(5)
            ->get();

        return inertia('Inputer/Data/Dashboard', [
            'stats' => $stats,
            'recentUploads' => $recentUploads
        ]);
    }

    // Helper: Normalize String
    private function normalize($value)
    {
        if (!$value) return '';
        return Str::slug($value, '');
    }
}