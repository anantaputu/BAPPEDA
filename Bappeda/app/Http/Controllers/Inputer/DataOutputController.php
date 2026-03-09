<?php

namespace App\Http\Controllers\Inputer;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataValue;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Periode');
        $sheet->setCellValue('C1', 'Nilai');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        $currentRow = 2;
        foreach ($rows as $index => $item) {
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $item['periode']);
            $sheet->setCellValue('C' . $currentRow, $item['nilai']);
            $currentRow++;
        }

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $safeName = Str::slug($upload->data->nama_data ?? 'Data');
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
