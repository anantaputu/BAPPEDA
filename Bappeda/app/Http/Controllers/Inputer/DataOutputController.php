<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\DataUpload;
use App\Models\DataField;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class DataOutputController extends Controller
{
    // PERUBAHAN 1: Parameter menerima $id (id_data), bukan model binding
    public function export($id)
    {
        // PERUBAHAN 2: Cari Upload Terakhir yang Valid milik Dataset ini
        $upload = DataUpload::where('id_data', $id)
            ->where('status', 'valid')
            ->latest() // Ambil yang paling baru
            ->first();

        // Validasi jika belum ada file
        if (!$upload) {
            return abort(404, 'Belum ada data valid untuk indikator ini.');
        }

        // --- DARI SINI KE BAWAH LOGIKANYA SAMA, TINGGAL COPY ---

        // 1. Ambil Metadata Field
        $fields = DataField::where('id_data', $upload->id_data)->get();

        // 2. Ambil Data Values
        $dataRows = is_string($upload->value) ? json_decode($upload->value, true) : $upload->value;

        if (empty($dataRows) || !is_array($dataRows)) {
            return abort(500, 'Data kosong atau format rusak.');
        }

        // 3. Setup Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Export');

        // --- HEADER ---
        $col = 1;
        $rowHeader = 1;

        // Header Nomor Urut
        $sheet->setCellValue('A1', 'NO');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $col++;

        foreach ($fields as $field) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellAddress = $columnLetter . $rowHeader;

            $sheet->setCellValue($cellAddress, $field->nama_field);
            
            // Style Header
            $style = $sheet->getStyle($cellAddress);
            $style->getFont()->setBold(true);
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
            $style->getAlignment()->setHorizontal('center');
            
            $col++;
        }

        // --- ISI DATA ---
        $currentRow = 2;
        $no = 1;

        foreach ($dataRows as $rowData) {
            $col = 1;
            
            // Kolom Nomor
            $sheet->setCellValue('A' . $currentRow, $no++);
            $col++;

            foreach ($fields as $field) {
                // Ambil value berdasarkan ID Field
                $val = $rowData[$field->id_field] ?? $rowData[(string)$field->id_field] ?? ''; 
                
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellAddress = $columnLetter . $currentRow;

                $sheet->setCellValue($cellAddress, $val);
                $col++;
            }
            $currentRow++;
        }

        // --- FINISHING ---
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // Load relasi data untuk nama file
        $upload->load('data');
        
        // Nama File yang Rapi
        $safeName = Str::slug($upload->data->nama_indikator ?? 'Data');
        $fileName = 'Data_' . $safeName . '_' . $upload->tahun . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}