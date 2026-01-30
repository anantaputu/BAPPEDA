<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataField;
use App\Models\DataFieldMapping; // <--- WAJIB ADA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class DataInputController extends Controller
{
    // STEP 1: List Metadata (Master Data)
    public function index()
    {
        return inertia('DataInput/Index', [
            'dataList' => Data::where('status', 'aktif')->get()
        ]);
    }

    // STEP 2: Form Upload
    public function create(Data $data)
    {
        return inertia('DataInput/Create', [
            'data' => $data
        ]);
    }

    // STEP 3: Simpan File Excel (Status: Processing)
    public function store(Request $request, Data $data)
    {
        $request->validate([
            'periode' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        // Simpan di disk 'private'
        $path = $request->file('file')->store('uploads/excel', 'private');

        $upload = DataUpload::create([
            'id_data' => $data->id_data,
            'id_user' => Auth::id(),
            'periode' => $request->periode,
            'file_path' => $path,
            'status' => 'processing' // Default status menunggu mapping
        ]);

        return redirect()->route('input-data.mapping', $upload->id_upload);
    }

    // STEP 4: Halaman Mapping Columns
    public function mapping(DataUpload $upload)
    {
        if (!Storage::disk('private')->exists($upload->file_path)) {
            return back()->withErrors(['file' => 'File tidak ditemukan.']);
        }

        $path = Storage::disk('private')->path($upload->file_path);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        // Ambil data Excel: ['A' => 'Nama', 'B' => 'Alamat']
        $allRows = $sheet->toArray(null, true, true, true);
        
        $header = array_shift($allRows); // Baris 1 jadi Header
        $previewData = array_slice($allRows, 0, 5); // 5 Baris contoh preview

        $fields = DataField::where('id_data', $upload->id_data)->get();

        // Logic AutoMap (Mencocokkan nama header dengan field database)
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

        return inertia('DataInput/Mapping', [
            'upload'       => $upload,
            'excelColumns' => $header,
            'previewData'  => $previewData,
            'fields'       => $fields,
            'autoMap'      => $autoMap
        ]);
    }

    // STEP 5: Simpan Konfigurasi Mapping ke Database
    public function storeMapping(Request $request, DataUpload $upload)
    {
        $request->validate(['mapping' => 'required|array']);

        DB::transaction(function () use ($request, $upload) {
            // 1. Hapus mapping lama jika ada (untuk re-mapping)
            DataFieldMapping::where('id_upload', $upload->id_upload)->delete();

            // 2. Loop setiap kolom yang dikirim dari Vue
            foreach ($request->mapping as $excelCol => $fieldId) {
                if (!$fieldId) continue; // Skip jika user pilih "Abaikan"

                // 3. Handle jika user memilih "+ Tambah Field Baru"
                if ($fieldId === '__new__') {
                    // Validasi input field baru
                    if(empty($request->new_fields[$excelCol])) continue;

                    $newFieldData = $request->new_fields[$excelCol];
                    
                    // Buat Master Field Baru
                    $field = DataField::create([
                        'id_data' => $upload->id_data,
                        'nama_field' => $newFieldData['nama_field'],
                        'key_field' => Str::snake($newFieldData['nama_field']),
                        'tipe_field' => $newFieldData['tipe_field'] ?? 'text',
                        'wajib' => false,
                    ]);
                    $fieldId = $field->id_field;
                }

                // 4. Simpan ke tabel data_mappings
                DataFieldMapping::create([
                    'id_upload' => $upload->id_upload,
                    'excel_column' => $excelCol,
                    'id_field' => $fieldId
                ]);
            }
        });

        // Lanjut ke proses parsing
        return $this->parse($upload);
    }

    // STEP 6: Eksekusi Pindahkan Data Excel ke JSONB (Status -> Valid)
    public function parse(DataUpload $upload)
    {
        // 1. Ambil Mapping yang sudah disimpan
        $mappings = DB::table('data_mappings')
            ->where('id_upload', $upload->id_upload)
            ->pluck('id_field', 'excel_column'); 

        if ($mappings->isEmpty()) {
            return back()->withErrors(['mapping' => 'Mapping belum disimpan.']);
        }

        // 2. Baca Excel Lagi
        $path = Storage::disk('private')->path($upload->file_path);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        
        $rows = $sheet->toArray(null, true, true, true);
        array_shift($rows); // Buang header

        $jsonPayload = [];

        // 3. Loop baris data Excel
        foreach ($rows as $row) {
            $rowData = [];
            $hasData = false;

            // Loop berdasarkan mapping yang kita punya
            foreach ($mappings as $colKey => $fieldId) {
                // Ambil value dari kolom Excel (misal kolom 'A')
                $val = $row[$colKey] ?? null;

                // Hanya simpan jika ada isinya
                if ($val !== null && trim($val) !== '') {
                    $rowData[$fieldId] = $val;
                    $hasData = true;
                }
            }

            // Jika baris tidak kosong, masukkan ke payload
            if ($hasData) {
                $jsonPayload[] = $rowData;
            }
        }

        // 4. Update tabel data_uploads
        // Simpan ke kolom 'value' dan ubah status jadi 'valid'
        $upload->update([
            'value' => $jsonPayload, 
            'status' => 'valid' 
        ]);

        return redirect()
            ->route('input-data.index')
            ->with('success', 'Data berhasil diproses dan disimpan.');
    }

    // Helper: Membersihkan string untuk pencocokan otomatis
    private function normalize($value)
    {
        if (!$value) return '';
        return Str::slug($value, '');
    }
}