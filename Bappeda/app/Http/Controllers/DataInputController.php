<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataField;
use App\Models\DataFieldMapping;
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

        // --- UPDATE 1: CEK DUPLIKASI (Pindahkan ke Atas) ---
        $exists = DataUpload::where('id_data', $data->id_data)
            ->where('periode', $request->periode)
            ->exists();

        if ($exists) {
            return back()->withErrors(['periode' => 'Data untuk periode ini sudah pernah diupload! Mohon hapus data lama jika ingin mengganti.']);
        }
        $path = $request->file('file')->store('uploads/excel', 'private');

        $upload = DataUpload::create([
            'id_data' => $data->id_data,
            'id_user' => Auth::id(),
            'periode' => $request->periode,
            'file_path' => $path,
            'status' => 'processing'
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

        // Ambil data Excel
        $allRows = $sheet->toArray(null, true, true, true);
        
        $header = array_shift($allRows); 
        $previewData = array_slice($allRows, 0, 5); 

        $fields = DataField::where('id_data', $upload->id_data)->get();

        // Logic AutoMap
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
            DataFieldMapping::where('id_upload', $upload->id_upload)->delete();

            foreach ($request->mapping as $excelCol => $fieldId) {
                if (!$fieldId) continue; 

                if ($fieldId === '__new__') {
                    if(empty($request->new_fields[$excelCol])) continue;

                    $newFieldData = $request->new_fields[$excelCol];
                    
                    $field = DataField::create([
                        'id_data' => $upload->id_data,
                        'nama_field' => $newFieldData['nama_field'],
                        'key_field' => Str::snake($newFieldData['nama_field']),
                        'tipe_field' => $newFieldData['tipe_field'] ?? 'text',
                        'wajib' => false,
                    ]);
                    $fieldId = $field->id_field;
                }

                DataFieldMapping::create([
                    'id_upload' => $upload->id_upload,
                    'excel_column' => $excelCol,
                    'id_field' => $fieldId
                ]);
            }
        });

        return $this->parse($upload);
    }

    // STEP 6: Eksekusi Pindahkan Data Excel ke JSONB
    public function parse(DataUpload $upload)
    {
        // 1. Ambil Mapping
        $mappings = DB::table('data_mappings')
            ->where('id_upload', $upload->id_upload)
            ->pluck('id_field', 'excel_column'); 

        if ($mappings->isEmpty()) {
            return back()->withErrors(['mapping' => 'Mapping belum disimpan.']);
        }

        // --- UPDATE 2: LOAD TIPE FIELD UNTUK SANITASI ---
        $fieldTypes = DataField::where('id_data', $upload->id_data)
            ->pluck('tipe_field', 'id_field');

        $path = Storage::disk('private')->path($upload->file_path);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        
        $rows = $sheet->toArray(null, true, true, true);
        array_shift($rows); 

        $jsonPayload = [];

        foreach ($rows as $row) {
            $rowData = [];
            $hasData = false;

            foreach ($mappings as $colKey => $fieldId) {
                $val = $row[$colKey] ?? null;

                $type = $fieldTypes[$fieldId] ?? 'text';

                // --- LOGIC PEMBERSIHAN DATA (SANITASI) ---
                if ($val !== null && trim($val) !== '') {
                    
                    if ($type === 'number') {
                       
                        $cleanVal = preg_replace('/[^0-9]/', '', $val);
                        
                        $val = $cleanVal === '' ? null : $cleanVal;
                    }

                    if ($val !== null) {
                        $rowData[$fieldId] = $val;
                        $hasData = true;
                    }
                }
            }

            if ($hasData) {
                $jsonPayload[] = $rowData;
            }
        }

        // 4. Update tabel
        $upload->update([
            'value' => $jsonPayload, 
            'status' => 'valid' 
        ]);

        return redirect()
            ->route('input-data.index')
            ->with('success', 'Data berhasil diproses. Angka otomatis dibersihkan.');
    }

    private function normalize($value)
    {
        if (!$value) return '';
        return Str::slug($value, '');
    }
}