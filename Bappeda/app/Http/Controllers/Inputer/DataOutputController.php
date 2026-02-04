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
    public function export(DataUpload $upload)
    {
        // 1. Ambil Metadata Field (Header Kolom)
        $fields = DataField::where('id_data', $upload->id_data)->get();

        // 2. Ambil Data Values (Isi Baris)
        // Pengecekan aman: Jika di model belum di-cast array, kita decode manual
        $dataRows = is_string($upload->value) ? json_decode($upload->value, true) : $upload->value;

        if (empty($dataRows) || !is_array($dataRows)) {
            return back()->withErrors(['file' => 'Data kosong atau format rusak, tidak ada yang bisa didownload.']);
        }

        // 3. Setup Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Judul Sheet (Optional)
        $sheet->setTitle('Data Export');

        // --- BAGIAN HEADER (BARIS 1) ---
        $col = 1;
        $rowHeader = 1;

        foreach ($fields as $field) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellAddress = $columnLetter . $rowHeader;

            // Set Nama Header
            $sheet->setCellValue($cellAddress, $field->nama_field);
            
            // Style Header: Bold & Warna Abu-abu
            $style = $sheet->getStyle($cellAddress);
            $style->getFont()->setBold(true);
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
            $style->getAlignment()->setHorizontal('center');
            
            $col++;
        }

        // --- BAGIAN ISI DATA (BARIS 2 dst) ---
        $currentRow = 2;

        foreach ($dataRows as $rowData) {
            $col = 1;
            
            foreach ($fields as $field) {
                // Ambil value berdasarkan ID Field. 
                // Gunakan (string) ID karena kadang key JSON disimpan sebagai string
                $val = $rowData[$field->id_field] ?? $rowData[(string)$field->id_field] ?? ''; 
                
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellAddress = $columnLetter . $currentRow;

                $sheet->setCellValue($cellAddress, $val);
                
                $col++;
            }
            $currentRow++;
        }

        // --- FINALISASI TAMPILAN ---
        
        // 1. Auto Size Kolom
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // 2. Beri Border ke Seluruh Tabel
        $highestRow = $sheet->getHighestRow();
        $tableRange = 'A1:' . $highestColumn . $highestRow;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // --- DOWNLOAD ---
        
        // Load relasi data agar bisa ambil nama indikator untuk nama file
        $upload->load('data');
        
        // Nama File: "Data_Jumlah_Penduduk_2024.xlsx"
        $safeName = Str::slug($upload->data->nama_indikator ?? 'Data');
        $fileName = 'Data_' . $safeName . '_' . Str::slug($upload->periode) . '.xlsx';

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