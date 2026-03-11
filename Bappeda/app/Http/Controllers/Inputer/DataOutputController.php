<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataValue;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // [BARU] Import untuk kolom dinamis
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class DataOutputController extends Controller
{
    public function export(int $id)
    {
        $upload = $this->resolveUploadFromReference($id);

        if (!$upload) {
            return abort(404, 'Data export tidak ditemukan untuk ID tersebut.');
        }

        $rows = $this->resolveRowsFromUploadValue($upload->id_data, $upload->value);
        if (empty($rows)) {
            return abort(404, 'Data nilai untuk indikator ini belum tersedia.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Export');

        // 1. Ekstrak Data Tambahan (informasi_tambahan)
        $extraFields = [];
        $rawExtra = $upload->data->informasi_tambahan ?? null;
        if ($rawExtra) {
            $decodedExtra = is_string($rawExtra) ? json_decode($rawExtra, true) : $rawExtra;
            if (is_array($decodedExtra)) {
                // Saring field yang bukan nama data
                foreach ($decodedExtra as $key => $val) {
                    if (!in_array(strtolower(trim($key)), ['nama data', 'nama indikator'])) {
                        $extraFields[$key] = $val;
                    }
                }
            }
        }

        // 2. Susun Header Secara Dinamis
        $headers = ['No', 'Periode', 'Nilai'];
        foreach (array_keys($extraFields) as $extraKey) {
            $headers[] = ucwords($extraKey); // Tambahkan header ekstra
        }

        // 3. Tulis Header ke Baris Pertama
        foreach ($headers as $colIndex => $headerText) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1); // 1 = A, 2 = B, dst
            $sheet->setCellValue($colLetter . '1', $headerText);
            
            // Set AutoSize agar rapi
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Cetak tebal (Bold) pada baris Header
        $lastColLetter = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColLetter . '1')->getFont()->setBold(true);

        // 4. Looping Baris Data
        $currentRow = 2;
        foreach ($rows as $index => $item) {
            // Tulis Kolom Utama (No, Periode, Nilai)
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValueExplicit('B' . $currentRow, $item['periode'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $currentRow, $item['nilai']);

            // Tulis Kolom Ekstra
            $colIndex = 4; // Kolom ekstra dimulai dari D (indeks 4)
            foreach ($extraFields as $key => $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($colLetter . $currentRow, $val);
                $colIndex++;
            }
            
            $currentRow++;
        }

        // Nama file aman
        $safeName = Str::slug($upload->data->nama_indikator ?? $upload->data->nama_data ?? 'Data');
        $fileName = 'Data_' . $safeName . '_' . ($upload->periode ?? now()->format('Y')) . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function resolveUploadFromReference(int $id): ?DataUpload
    {
        $isDataId = Data::where('id_data', $id)->exists();
        if ($isDataId) {
            return DataUpload::with('data')
                ->where('id_data', $id)
                ->latest()
                ->first();
        }

        return DataUpload::with('data')
            ->where('id_upload', $id)
            ->first();
    }

    private function resolveRowsFromUploadValue(int $idData, $rawValue): array
    {
        $decoded = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;
        $rows = [];

        if (is_array($decoded) && isset($decoded['values']) && is_array($decoded['values'])) {
            foreach ($decoded['values'] as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $periode = trim((string) ($item['tahun'] ?? ''));
                $nilai = $item['nilai'] ?? '';
                if ($periode !== '') {
                    $rows[] = ['periode' => $periode, 'nilai' => $nilai];
                }
            }
        }

        if (empty($rows) && is_array($decoded) && isset($decoded['nilai']) && is_array($decoded['nilai'])) {
            foreach ($decoded['nilai'] as $periode => $nilai) {
                $periodeText = trim((string) $periode);
                if ($periodeText !== '') {
                    $rows[] = ['periode' => $periodeText, 'nilai' => $nilai];
                }
            }
        }

        if (empty($rows)) {
            $fallback = DataValue::where('id_data', $idData)->orderBy('tahun')->get(['tahun', 'nilai']);
            foreach ($fallback as $item) {
                $rows[] = ['periode' => (string) $item->tahun, 'nilai' => $item->nilai];
            }
        }

        usort($rows, fn ($a, $b) => strnatcasecmp((string) $a['periode'], (string) $b['periode']));
        return $rows;
    }
}   