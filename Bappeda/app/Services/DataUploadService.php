<?php

namespace App\Services;

use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataValue;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class DataUploadService
{
    public function getPreviewData($file)
    {
        $reader = IOFactory::createReaderForFile($file->getPathname());
        if (strtolower($file->getClientOriginalExtension()) === 'csv') {
            $content = file_get_contents($file->getPathname());
            $delimiter = (substr_count($content, ';') > substr_count($content, ',')) ? ';' : ',';
            $reader->setDelimiter($delimiter);
        }

        $spreadsheet = $reader->load($file->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $headerRowIndex = -1;
        $colNama = null; $colSatuan = null; $colTahun = [];
        $headers = [];

        foreach ($rows as $index => $row) {
            foreach ($row as $colKey => $cellValue) {
                $cleanString = preg_replace('/\s+/', ' ', strtolower(trim((string)$cellValue)));
                if (str_contains($cleanString, 'indikator') || str_contains($cleanString, 'nama data') || str_contains($cleanString, 'uraian') || $cleanString === 'data') {
                    $headerRowIndex = $index;
                    $colNama = $colKey;
                    $headers = $row;
                    break 2;
                }
            }
        }

        if ($headerRowIndex === -1) throw new Exception('Header (Nama Indikator) tidak ditemukan. Pastikan ada judul kolom yang mengandung kata "Indikator".');

        foreach ($headers as $colKey => $cellValue) {
            $val = strtolower(trim((string)$cellValue));
            if (in_array($val, ['satuan', 'unit'])) $colSatuan = $colKey;
            if (preg_match('/(20\d{2})/', $val, $matches)) $colTahun[$colKey] = $matches[1];
        }

        $previewData = [];
        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) continue;

            $namaIndikator = trim(str_replace(["\xA0", "\xc2\xa0"], ' ', ($row[$colNama] ?? '')));
            if (empty($namaIndikator) || strlen($namaIndikator) < 3) continue;

            $values = [];
            foreach ($colTahun as $colKey => $tahun) {
                $values[$tahun] = $row[$colKey] ?? null;
            }

            $previewData[] = [
                'nama_indikator' => $namaIndikator,
                'satuan'         => $colSatuan ? ($row[$colSatuan] ?? '-') : '-',
                'id_tema'        => null, 'id_urusan' => null, 'id_bidang' => null,
                'values'         => $values
            ];
        }

        return ['rows' => $previewData, 'years' => array_values($colTahun)];
    }

    // 3. FUNGSI SIMPAN BULK/MASSAL (Pindahan dari Controller)
    public function processBulkData($dataset, $years, $userId)
    {
        DB::beginTransaction();
        try {
            foreach ($dataset as $row) {
                $dataMaster = Data::updateOrCreate(
                    ['nama_indikator' => $row['nama_indikator']],
                    [
                        'id_user'   => $userId,
                        'id_tema'   => $row['id_tema'],
                        'id_urusan' => $row['id_urusan'],
                        'id_bidang' => $row['id_bidang'],
                        'satuan'    => $row['satuan'] ?? '-',
                        'status'    => 'aktif',
                        'tahun'     => date('Y')
                    ]
                );

                foreach ($years as $tahun) {
                    $nilai = $row['values'][$tahun] ?? null;
                    if ($nilai !== null && trim((string)$nilai) !== '') {
                        $nilaiClean = str_replace(',', '.', preg_replace('/[^0-9,\.\-]/', '', $nilai));
                        DataValue::updateOrCreate(
                            ['id_data' => $dataMaster->id_data, 'tahun' => $tahun],
                            ['nilai' => $nilaiClean ?: 0]
                        );
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal menyimpan bulk data: " . $e->getMessage());
        }
    }
    public function processSingleData($formData, $userId)
    {
        DB::beginTransaction();
        try {
            // 1. Simpan atau Update Master Data (Tabel: data)
            $dataMaster = Data::updateOrCreate(
                ['nama_indikator' => trim($formData['nama_indikator'])],
                [
                    'id_user'      => $userId,
                    'id_tema'      => $formData['id_tema'],
                    'id_urusan'    => $formData['id_urusan'],
                    'id_bidang'    => $formData['id_bidang'],
                    'id_frekuensi' => $formData['id_frekuensi'] ?? 1,
                    'satuan'       => $formData['satuan'] ?? '-',
                    'deskripsi'    => $formData['deskripsi'] ?? null,
                    'sumber'       => $formData['sumber'] ?? null,
                    'status'       => 'aktif', 
                    'tahun'        => $formData['tahun']
                ]
            );

            // 2. Bersihkan Nilai (Ubah koma jadi titik untuk desimal database)
            $nilaiClean = preg_replace('/[^0-9,\.\-]/', '', $formData['nilai']);
            if (strpos($nilaiClean, ',') !== false && strpos($nilaiClean, '.') !== false) {
                $nilaiClean = str_replace('.', '', $nilaiClean);
                $nilaiClean = str_replace(',', '.', $nilaiClean);
            } elseif (strpos($nilaiClean, ',') !== false) {
                $nilaiClean = str_replace(',', '.', $nilaiClean);
            }

            // 3. Simpan Nilainya ke tabel anak (Tabel: data_values)
            DataValue::updateOrCreate(
                [
                    'id_data' => $dataMaster->id_data, 
                    'tahun'   => $formData['tahun']
                ],
                [
                    'nilai'   => (float) $nilaiClean
                ]
            );

            DB::commit();
            return $dataMaster; // Kembalikan objek data jika dibutuhkan

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal memproses data: " . $e->getMessage());
        }
    }
}