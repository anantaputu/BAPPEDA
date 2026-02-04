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
        $uploads = DataUpload::with('data')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Inputer/Data/Index', ['uploads' => $uploads]);
    }

    public function dashboard() { 
        // ... kode dashboard tetap sama ...
        return Inertia::render('Inputer/Data/Dashboard', [/*...*/]);
    }

    public function createWizard()
    {
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
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) throw new \Exception("File kosong");

            $header = array_shift($rows); 
            $preview = array_slice($rows, 0, 10); // Ambil 10 baris preview

            return response()->json([
                'status' => 'success',
                'temp_path' => $path,
                'headers' => $header,
                'preview' => $preview
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // STEP 2: SIMPAN TOTAL (Metadata + Mapping + Data)
    public function storeComplete(Request $request)
    {
        $request->validate([
            'nama_indikator' => 'required|string',
            'id_tema' => 'required',
            'mapping' => 'required|array',
            'file_path' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Pindahkan File
            $newPath = str_replace('uploads/temp', 'uploads/excel', $request->file_path);
            if (Storage::disk('private')->exists($request->file_path)) {
                Storage::disk('private')->move($request->file_path, $newPath);
            }

            // 2. Buat Metadata
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
                'status' => 'aktif',
            ]);

            // 3. Buat Data Upload
            $upload = DataUpload::create([
                'id_data' => $data->id_data,
                'id_user' => Auth::id(),
                'periode' => $request->periode,
                'file_path' => $newPath,
                'status' => 'processing'
            ]);

            // 4. Proses Field & Mapping
            $fieldIdMap = [];     // Key: Kolom Excel (A,B) -> Value: ID Field DB
            $fieldTypeMap = [];   // Key: ID Field DB -> Value: Tipe Data (number/text)

            foreach ($request->mapping as $colKey => $action) {
                // $action berisi: "__new__" atau null
                if (!$action) continue; // Jika null (abaikan)

                $fieldId = null;
                $fieldType = 'text';

                // Jika User memilih "Buat Baru"
                if ($action === '__new__') {
                    // Ambil detail konfigurasi field baru
                    $newData = $request->new_fields[$colKey] ?? [];
                    $fieldName = $newData['nama_field'] ?? $colKey;
                    $fieldType = $newData['tipe_field'] ?? 'text';

                    $field = DataField::create([
                        'id_data' => $data->id_data,
                        'nama_field' => $fieldName,
                        'key_field' => Str::snake($fieldName),
                        'tipe_field' => $fieldType,
                        'wajib' => false
                    ]);
                    $fieldId = $field->id_field;
                }

                // Simpan Mapping ke DB
                if ($fieldId) {
                    DataFieldMapping::create([
                        'id_upload' => $upload->id_upload,
                        'excel_column' => $colKey,
                        'id_field' => $fieldId
                    ]);
                    
                    // Simpan ke memory map untuk parsing
                    $fieldIdMap[$colKey] = $fieldId;
                    $fieldTypeMap[$fieldId] = $fieldType;
                }
            }

            // 5. Parsing Data (Langsung)
            $fullPath = Storage::disk('private')->path($newPath);
            $spreadsheet = IOFactory::load($fullPath);
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            array_shift($rows); // Hapus header

            $jsonPayload = [];
            foreach ($rows as $row) {
                $rowData = [];
                $hasData = false;

                foreach ($fieldIdMap as $colKey => $fieldId) {
                    $val = $row[$colKey] ?? null;
                    $type = $fieldTypeMap[$fieldId];

                    if ($val !== null && trim($val) !== '') {
                        if ($type === 'number') {
                            $val = str_replace(',', '.', $val);
                            $val = preg_replace('/[^0-9.\-]/', '', $val);
                            if (is_numeric($val)) $val = strpos($val, '.') !== false ? (float)$val : (int)$val;
                        }
                        $hasData = true;
                    }
                    $rowData[$fieldId] = $val;
                }

                if ($hasData) $jsonPayload[] = $rowData;
            }

            // 6. Update Hasil Akhir
            $upload->update([
                'value' => $jsonPayload,
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