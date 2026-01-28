<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\DataUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\DataFieldMapping;
use App\Models\DataValue;
use App\Models\DataField;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class DataInputController extends Controller
{
    // STEP 1: list metadata
    public function index()
    {
        return inertia('DataInput/Index', [
            'dataList' => Data::where('status', 'aktif')->get()
        ]);
    }

    // STEP 2: form upload
    public function create(Data $data)
    {
        return inertia('DataInput/Create', [
            'data' => $data
        ]);
    }

    // STEP 3: simpan upload
   public function store(Request $request, Data $data)
{
    $request->validate([
        'periode' => 'required|string',
        'file' => 'required|file|mimes:xlsx,xls'
    ]);

    $path = $request->file('file')->store('uploads/excel');

    // ⬅️ SIMPAN KE VARIABEL
    $upload = DataUpload::create([
        'id_data' => $data->id_data,
        'id_user' => Auth::id(),
        'periode' => $request->periode,
        'file_path' => $path,
        'status' => 'draft'
    ]);

    // ⬅️ BARU BOLEH DIPAKAI
    return redirect()
        ->route('input-data.mapping', $upload->id_upload);
}

   public function mapping(DataUpload $upload)
{
    // 1. Pastikan File Ada
    if (!Storage::disk('private')->exists($upload->file_path)) {
        return back()->withErrors([
            'file' => 'File tidak ditemukan di storage private'
        ]);
    }

    $path = Storage::disk('private')->path($upload->file_path);

    // 2. Load Spreadsheet
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();

    // 3. Ambil Semua Data ke dalam Array
    // toArray(null, null_value, calculate_formulas, format_data)
    $allRows = $sheet->toArray(null, true, true, true);

    // 4. Pisahkan Header dan Preview Data
    // Ambil baris pertama sebagai header
    $header = array_shift($allRows); 
    
    // Ambil 5 baris pertama saja untuk preview agar loading tidak berat
    $previewData = array_slice($allRows, 0, 5);

    // 5. Ambil Field Master untuk Mapping
    $fields = DataField::where('id_data', $upload->id_data)->get();

    // 6. Logic AutoMap (Tetap dipertahankan)
    $autoMap = [];
   foreach ($header as $colKey => $colName) {
            $normCol = $this->normalize($colName); // Panggil dengan $this->

            foreach ($fields as $field) {
                if ($normCol === $this->normalize($field->nama_field) 
                    || $normCol === $this->normalize($field->key_field)) {
                    $autoMap[$colKey] = $field->id_field;
                    break;
                }
            }
        }

    // 7. Kirim ke Inertia
    return inertia('DataInput/Mapping', [
        'upload'       => $upload,
        'excelColumns' => $header,      // Array: ['A' => 'Nama', 'B' => 'NIP']
        'previewData'  => $previewData,   // Array of 5 rows pertama
        'fields'       => $fields,
        'autoMap'      => $autoMap
    ]);
}

public function storeMapping(Request $request, DataUpload $upload)
{
    $request->validate(['mapping' => 'required|array']);

    // Gunakan Transaction agar jika satu gagal, semua batal (aman untuk data besar)
    \DB::transaction(function () use ($request, $upload) {
        foreach ($request->mapping as $excelCol => $fieldId) {

    if ($fieldId === '__new__') {

        $field = DataField::create([
            'id_data' => $upload->id_data,
            'nama_field' => $request->new_fields[$excelCol]['nama_field'],
            'key_field' => Str::snake($request->new_fields[$excelCol]['nama_field']),
            'tipe_field' => $request->new_fields[$excelCol]['tipe_field'],
            'wajib' => false,
        ]);

        $fieldId = $field->id_field;
    }

    if ($fieldId) {
        DataFieldMapping::create([
            'id_upload' => $upload->id_upload,
            'excel_column' => $excelCol,
            'id_field' => $fieldId
        ]);
    }
}
    });

    // LANJUTKAN OTOMATIS KE PROSES PARSE (PEMINDAHAN DATA)
    return redirect()->route('input-data.parse', $upload->id_upload);
}

public function parse(DataUpload $upload)
{
    // ambil mapping
    $mappings = \DB::table('data_mappings')
        ->where('id_upload', $upload->id_upload)
        ->get();

    // mapping: excel_column => id_field
    $map = [];
    foreach ($mappings as $m) {
        $map[$m->excel_column] = $m->id_field;
    }

    // baca excel
    $path = storage_path('app/' . $upload->file_path);
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    $header = array_shift($rows); // buang header

    foreach ($rows as $rowIndex => $row) {
        foreach ($header as $colKey => $colName) {
            if (!isset($map[$colName])) continue;

            DataValue::create([
                'id_upload' => $upload->id_upload,
                'id_field' => $map[$colName],
                'row_index' => $rowIndex + 1,
                'value' => $row[$colKey]
            ]);
        }
    }

    $upload->update(['status' => 'submitted']);

    return redirect()
        ->route('input-data.index')
        ->with('success', 'Data berhasil disimpan');
}


private function normalize($value)
    {
        if (!$value) return '';
        
        // Str::slug akan mengubah "Nama Lengkap" menjadi "namalengkap"
        // Parameter kedua ('') memastikan tidak ada tanda hubung (-)
        return Str::slug($value, '');
    }

}
