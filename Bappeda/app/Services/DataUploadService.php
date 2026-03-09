<?php

namespace App\Services;

use App\Models\Data;
use App\Models\DataUpload;
use App\Models\DataValue;
use App\Models\ActivityLog; // Pastikan model ini diimport
use Illuminate\Support\Facades\DB;
use Exception;

class DataUploadService
{
    private function hasDuplicateNamaData(string $namaData, ?int $ignoreId = null): bool
    {
        $query = Data::query()
            ->whereRaw('LOWER(TRIM(nama_data)) = LOWER(TRIM(?))', [$namaData]);

        if ($ignoreId !== null) {
            $query->where('id_data', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function getPreviewData($file)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
        
        if (strtolower($file->getClientOriginalExtension()) === 'csv') {
            $content = file_get_contents($file->getPathname());
            $delimiter = (substr_count($content, ';') > substr_count($content, ',')) ? ';' : ',';
            $reader->setDelimiter($delimiter);
        }

        $spreadsheet = $reader->load($file->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $keywordsNama = ['nama data', 'uraian', 'indikator', 'data', 'kegiatan', 'program', 'sasaran', 'rincian'];
        $headerRowIndex = -1;
        $colNama = null; $colSatuan = null;
        $headers = [];

        foreach ($rows as $index => $row) {
            foreach ($row as $colKey => $cellValue) {
                $cleanString = preg_replace('/\s+/', ' ', strtolower(trim((string)$cellValue)));
                foreach ($keywordsNama as $keyword) {
                    if (str_contains($cleanString, $keyword)) {
                        $headerRowIndex = $index;
                        $colNama = $colKey;
                        $headers = $row;
                        break 2;
                    }
                }
            }
        }

        if ($headerRowIndex === -1) {
            $headerRowIndex = 1;
            $headers = $rows[1];
            foreach ($headers as $key => $val) { if (!empty($val)) { $colNama = $key; break; } }
            if (!$colNama) $colNama = 'A';
        }

        $waktuKeywords = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des', 'minggu', 'triwulan', 'semester', 'kondisi'];
        $ignoreHeaders = ['no', 'nomor'];

        $colTahun = [];
        $colExtra = [];

        foreach ($headers as $colKey => $cellValue) {
            if ($colKey === $colNama) continue;
            $val = strtolower(trim((string)$cellValue));
            $asli = trim((string)$cellValue);
            if (empty($val)) continue;

            if (in_array($val, ['satuan', 'unit', 'sat'])) {
                $colSatuan = $colKey; continue;
            }

            $isIgnored = false;
            foreach ($ignoreHeaders as $ignore) {
                if (str_contains($val, $ignore)) { $isIgnored = true; break; }
            }

            if (!$isIgnored) {
                $isWaktu = false;
                if (preg_match('/(20\d{2})/', $val)) $isWaktu = true;
                foreach ($waktuKeywords as $kw) {
                    if (str_contains($val, $kw)) { $isWaktu = true; break; }
                }

                if ($isWaktu) { $colTahun[$colKey] = $asli; } 
                else { $colExtra[$colKey] = $asli; }
            }
        }

        $previewData = [];
        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) continue;
            $namaIndikator = trim(str_replace(["\xA0", "\xc2\xa0"], ' ', ($row[$colNama] ?? '')));
            if (empty($namaIndikator) || strlen($namaIndikator) < 2) continue;

            $values = [];
            foreach ($colTahun as $colKey => $tahun) $values[$tahun] = $row[$colKey] ?? null;

            $extraData = [];
            foreach ($colExtra as $colKey => $namaHeader) { $extraData[$namaHeader] = $row[$colKey] ?? ''; }

            $previewData[] = [
                'nama_data' => $namaIndikator,
                'satuan'    => $colSatuan ? ($row[$colSatuan] ?? '-') : '-',
                'id_tema'   => null,
                'id_urusan' => null,
                'id_bidang' => null,
                'id_katakunci' => [],
                'values'    => $values,
                'extra_fields' => $extraData
            ];
        }

        return [
            'rows' => $previewData,
            'years' => array_values($colTahun),
            'extra_headers' => array_values($colExtra)
        ];
    }

    /**
     * PROSES SIMPAN MASSAL (BULK) + LOG
     */
    public function processBulkData($dataset, $years, $userId, $fileName = 'Multi Data Excel')
    {
        DB::beginTransaction();
        try {
            $seenNames = [];

            foreach ($dataset as $row) {
                $namaData = trim($row['nama_data'] ?? '');
                if ($namaData === '') {
                    throw new Exception('Nama indikator tidak boleh kosong.');
                }

                $normalizedName = strtolower($namaData);
                if (isset($seenNames[$normalizedName])) {
                    throw new Exception("Duplikasi pada file upload: indikator '{$namaData}' muncul lebih dari sekali.");
                }
                $seenNames[$normalizedName] = true;

                if ($this->hasDuplicateNamaData($namaData)) {
                    throw new Exception("Indikator '{$namaData}' sudah ada. Gunakan menu edit untuk memperbarui data.");
                }

                $dataMaster = Data::create([
                    'nama_data' => $namaData,
                    'id_user'      => $userId,
                    'id_tema'      => $row['id_tema'],
                    'id_urusan'    => $row['id_urusan'],
                    'id_bidang'    => $row['id_bidang'],
                    'id_frekuensi' => $row['id_frekuensi'] ?? 1,
                    'satuan'       => $row['satuan'] ?? '-',
                    'informasi_tambahan' => isset($row['extra_fields']) ? json_encode($row['extra_fields']) : null,
                    'tahun_terbit' => $row['tahun_terbit'] ?? date('Y'),
                ]);

                if (isset($row['id_katakunci']) && is_array($row['id_katakunci'])) {
                    $dataMaster->katakunci()->sync($row['id_katakunci']);
                }

                // CATAT ACTIVITY LOG
                ActivityLog::create([
                    'id_user' => $userId,
                    'id_data' => $dataMaster->id_data,
                    'action' => 'UPLOAD',
                    'target_name' => $dataMaster->nama_data,
                    'description' => 'Unggah indikator baru via Bulk Excel',
                    'ip_address' => request()->ip()
                ]);

                DataUpload::create([
                    'id_user'   => $userId,
                    'id_data'   => $dataMaster->id_data,
                    'periode'   => date('Y'),
                    'file_path' => $fileName,
                    'value'     => json_encode(['years' => $years, 'nilai' => $row['values'] ?? []])
                ]);

                foreach ($years as $tahun) {
                    $nilai = $row['values'][$tahun] ?? null;
                    if ($nilai !== null && trim((string)$nilai) !== '') {
                        $nilaiString = (string)$nilai;
                        $nilaiClean = preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', $nilaiString));
                        if ($nilaiClean !== '') {
                            DataValue::updateOrCreate(
                                ['id_data' => $dataMaster->id_data, 'tahun' => $tahun],
                                ['nilai' => (float) $nilaiClean]
                            );
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * PROSES SIMPAN SINGLE + LOG
     */
    public function processSingleData($formData, $userId)
    {
        DB::beginTransaction();
        try {
            $namaData = trim($formData['nama_data']);
            if ($this->hasDuplicateNamaData($namaData)) {
                throw new Exception("Indikator '{$namaData}' sudah ada. Gunakan menu edit untuk memperbarui data.");
            }

            $dataMaster = Data::create([
                'nama_data' => $namaData,
                'id_user'      => $userId,
                'id_tema'      => $formData['id_tema'],
                'id_urusan'    => $formData['id_urusan'],
                'id_bidang'    => $formData['id_bidang'],
                'id_frekuensi' => $formData['id_frekuensi'] ?? 1,
                'satuan'       => $formData['satuan'] ?? '-',
                'deskripsi'    => $formData['deskripsi'] ?? null,
                'sumber'       => $formData['sumber'] ?? null,
                'tahun_terbit' => $formData['tahun_terbit'] ?? date('Y'),
            ]);

            if (isset($formData['id_katakunci']) && is_array($formData['id_katakunci'])) {
                $dataMaster->katakunci()->sync($formData['id_katakunci']);
            }

            // CATAT ACTIVITY LOG
            ActivityLog::create([
                'id_user' => $userId,
                'id_data' => $dataMaster->id_data,
                'action' => 'UPLOAD',
                'target_name' => $dataMaster->nama_data,
                'description' => 'Input manual indikator baru',
                'ip_address' => request()->ip()
            ]);

            foreach ($formData['values'] as $item) {
                $nilaiClean = preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', $item['nilai']));
                DataValue::updateOrCreate(
                    ['id_data' => $dataMaster->id_data, 'tahun' => $item['tahun']],
                    ['nilai' => (float) $nilaiClean]
                );
            }

            DataUpload::create([
                'id_user'   => $userId,
                'id_data'   => $dataMaster->id_data,
                'periode'   => $formData['values'][0]['tahun'] ?? date('Y'),
                'file_path' => 'manual_input',
                'value'     => json_encode(['values' => $formData['values']]),
            ]);

            DB::commit();
            return $dataMaster;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal memproses data: " . $e->getMessage());
        }
    }

    /**
     * PROSES UPDATE DATA + LOG
     */
    public function updateSingleData($id, $formData, $userId)
    {
        DB::beginTransaction();
        try {
            $dataMaster = Data::findOrFail($id);
            $namaData = trim($formData['nama_data']);

            if ($this->hasDuplicateNamaData($namaData, $dataMaster->id_data)) {
                throw new Exception("Indikator '{$namaData}' sudah ada. Gunakan nama indikator yang berbeda.");
            }

            $dataMaster->update([
                'nama_data'    => $namaData,
                'id_tema'      => $formData['id_tema'],
                'id_urusan'    => $formData['id_urusan'],
                'id_bidang'    => $formData['id_bidang'],
                'id_frekuensi' => $formData['id_frekuensi'] ?? 1,
                'satuan'       => $formData['satuan'] ?? '-',
                'deskripsi'    => $formData['deskripsi'] ?? null,
                'sumber'       => $formData['sumber'] ?? null,
                'tahun_terbit' => $formData['tahun_terbit'] ?? $dataMaster->tahun_terbit,
                'informasi_tambahan' => isset($formData['extra_fields']) ? json_encode($formData['extra_fields']) : $dataMaster->informasi_tambahan,
            ]);

            if (isset($formData['id_katakunci']) && is_array($formData['id_katakunci'])) {
                $dataMaster->katakunci()->sync($formData['id_katakunci']);
            }

            // CATAT ACTIVITY LOG
            ActivityLog::create([
                'id_user' => $userId,
                'id_data' => $dataMaster->id_data,
                'action' => 'EDIT',
                'target_name' => $dataMaster->nama_data,
                'description' => 'Memperbarui rincian indikator',
                'ip_address' => request()->ip()
            ]);

            $requestedYears = [];
            foreach ($formData['values'] as $item) {
                $nilaiClean = preg_replace('/[^0-9\.\-]/', '', str_replace(',', '.', $item['nilai']));
                $requestedYears[] = $item['tahun'];
                DataValue::updateOrCreate(
                    ['id_data' => $dataMaster->id_data, 'tahun' => $item['tahun']],
                    ['nilai' => (float) $nilaiClean]
                );
            }

            if (count($requestedYears) > 0) {
                DataValue::where('id_data', $dataMaster->id_data)->whereNotIn('tahun', $requestedYears)->delete();
            }

            DataUpload::create([
                'id_user'   => $userId,
                'id_data'   => $dataMaster->id_data,
                'periode'   => $requestedYears[0] ?? date('Y'),
                'file_path' => 'edit_manual',
                'value'     => json_encode(['values' => $formData['values']]),
            ]);

            DB::commit();
            return $dataMaster;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Gagal memperbarui data: " . $e->getMessage());
        }
    }
}
