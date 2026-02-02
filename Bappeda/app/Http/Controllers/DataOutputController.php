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
        // 1. Ambil Data
        $fields = DataField::where('id_data', $upload->id_data)->get();
        $dataRows = $upload->value; 

        if (empty($dataRows)) {
            return back()->with('error', 'Data kosong, tidak ada yang bisa didownload.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $col = 1;
        $rowHeader = 1;

        foreach ($fields as $field) {

            $columnLetter = Coordinate::stringFromColumnIndex($col);
            
            $cellAddress = $columnLetter . $rowHeader;

            $sheet->setCellValue($cellAddress, $field->nama_field);
            
            $sheet->getStyle($cellAddress)->getFont()->setBold(true);
            
            $col++;
        }

        $currentRow = 2; 

        foreach ($dataRows as $rowData) {
            $col = 1; 
            
            foreach ($fields as $field) {
                $val = $rowData[$field->id_field] ?? ''; 
                
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellAddress = $columnLetter . $currentRow;

                $sheet->setCellValue($cellAddress, $val);
                
                $col++;
            }
            $currentRow++;
        }

        $highestColumn = $sheet->getHighestColumn(); 
        foreach (range('A', $highestColumn) as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
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