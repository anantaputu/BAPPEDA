<?php

namespace App\Http\Controllers;

use App\Models\DataUpload;
use App\Models\DataField;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataOutputController extends Controller
{
    public function export(DataUpload $upload)
    {
        $fields = DataField::where('id_data', $upload->id_data)->get();
        $dataRows = $upload->value; 

        if (empty($dataRows)) {
            return back()->with('error', 'Data kosong, tidak ada yang bisa didownload.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // --- BAGIAN A: TULIS HEADER (Baris 1) ---
        $colIndex = 1; 
        foreach ($fields as $field) {
            // PERBAIKAN: Gunakan getCellByColumnAndRow -> setValue
            // Ini cara paling aman dan anti-error
            $sheet->getCellByColumnAndRow($colIndex, 1)->setValue($field->nama_field);
            
            // Style Bold
            $sheet->getStyleByColumnAndRow($colIndex, 1)->getFont()->setBold(true);
            $colIndex++;
        }

        // --- BAGIAN B: TULIS ISI DATA (Mulai Baris 2) ---
        $rowIndex = 2; 
        foreach ($dataRows as $row) {
            $colIndex = 1;
            foreach ($fields as $field) {
                $val = $row[$field->id_field] ?? ''; 
                
                // PERBAIKAN DI SINI JUGA
                $sheet->getCellByColumnAndRow($colIndex, $rowIndex)->setValue($val);
                
                $colIndex++;
            }
            $rowIndex++;
        }

        // Auto Size Kolom
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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