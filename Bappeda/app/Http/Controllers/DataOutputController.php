<?php

namespace App\Http\Controllers;

use App\Models\DataUpload;
use App\Models\DataField;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; 
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataOutputController extends Controller
{
    public function export(DataUpload $upload)
    {
        // 1. Ambil Definisi Kolom & Data
        $fields = DataField::where('id_data', $upload->id_data)->get();
        $dataRows = $upload->value; 

        if (empty($dataRows)) {
            return back()->with('error', 'Data kosong, tidak ada yang bisa didownload.');
        }

        // 2. Setup Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // --- BAGIAN A: HEADER (Baris 1) ---
        // Menggunakan ide kamu: Konversi Angka -> Huruf (1 -> A, 2 -> B)
        $col = 1;
        $rowHeader = 1;

        foreach ($fields as $field) {
            // Ubah index 1 jadi 'A', 2 jadi 'B', dst.
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            
            // Gabung jadi alamat sel, misal 'A1'
            $cellAddress = $columnLetter . $rowHeader;

            $sheet->setCellValue($cellAddress, $field->nama_field);
            
            // Style Bold
            $sheet->getStyle($cellAddress)->getFont()->setBold(true);
            
            $col++;
        }

        // --- BAGIAN B: ISI DATA (Mulai Baris 2) ---
        $currentRow = 2; 

        foreach ($dataRows as $rowData) {
            $col = 1; // Reset ke kolom awal untuk baris baru
            
            foreach ($fields as $field) {
                // Ambil value
                $val = $rowData[$field->id_field] ?? ''; 
                
                // Konversi koordinat lagi (Contoh: A2, B2, lalu A3, B3...)
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellAddress = $columnLetter . $currentRow;

                $sheet->setCellValue($cellAddress, $val);
                
                $col++;
            }
            $currentRow++;
        }

        // 3. Auto Size Kolom (Opsional, biar rapi)
        // Kita loop dari kolom 'A' sampai kolom terakhir yang terisi
        $highestColumn = $sheet->getHighestColumn(); 
        foreach (range('A', $highestColumn) as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // 4. Download
        $fileName = 'Export_Data_' . $upload->periode . '.xlsx';
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