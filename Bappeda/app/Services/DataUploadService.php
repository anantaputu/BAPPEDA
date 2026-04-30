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
    private array $timeKeywords = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des', 'minggu', 'triwulan', 'semester', 'kondisi'];
    private array $ignoreHeaders = ['no', 'nomor'];
    private array $specialExtraHeaders = ['tahun terbit', 'keterangan', 'deskripsi', 'sumber', 'catatan'];

    private function normalizeCellValue(mixed $value): string
    {
        $string = str_replace(["\xC2\xA0", "\xA0"], ' ', (string) $value);
        $string = preg_replace('/\s+/', ' ', trim($string));

        return strtolower($string);
    }

    private function isTimeHeader(string $normalizedHeader): bool
    {
        if ($normalizedHeader === '') {
            return false;
        }

        if (preg_match('/^(19|20)\d{2}$/', $normalizedHeader) || preg_match('/^(19|20)\d{2}\s*[-\/]\s*(19|20)?\d{2}$/', $normalizedHeader)) {
            return true;
        }

        foreach ($this->timeKeywords as $keyword) {
            if (str_contains($normalizedHeader, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function classifyHeader(string $normalizedHeader): string
    {
        if ($normalizedHeader === '') {
            return 'empty';
        }

        if (in_array($normalizedHeader, ['nama data', 'nama indikator', 'indikator', 'uraian', 'rincian', 'indikator data'], true)) {
            return 'name';
        }

        if (in_array($normalizedHeader, ['satuan', 'unit', 'sat'], true)) {
            return 'unit';
        }

        if (in_array($normalizedHeader, $this->ignoreHeaders, true)) {
            return 'ignore';
        }

        if (in_array($normalizedHeader, $this->specialExtraHeaders, true)) {
            return 'extra';
        }

        if ($this->isTimeHeader($normalizedHeader)) {
            return 'time';
        }

        return 'candidate';
    }

    private function detectHeaderRow(array $rows): array
    {
        $bestMatch = null;

        foreach ($rows as $index => $row) {
            $score = 0;
            $colNama = null;
            $nonEmptyCount = 0;
            $timeColumnCount = 0;
            $candidateColumnCount = 0;

            foreach ($row as $colKey => $cellValue) {
                $normalized = $this->normalizeCellValue($cellValue);
                if ($normalized === '') {
                    continue;
                }

                $nonEmptyCount++;

                switch ($this->classifyHeader($normalized)) {
                    case 'name':
                        $score += 8;
                        $colNama ??= $colKey;
                        break;
                    case 'unit':
                        $score += 3;
                        break;
                    case 'time':
                        $score += 3;
                        $timeColumnCount++;
                        break;
                    case 'extra':
                        $score += 1;
                        break;
                    case 'candidate':
                        $candidateColumnCount++;
                        if ($colNama === null) {
                            $colNama = $colKey;
                        }
                        break;
                }
            }

            if ($colNama === null || $nonEmptyCount < 2) {
                continue;
            }

            if ($timeColumnCount >= 2) {
                $score += 4;
            }

            if ($candidateColumnCount > 0) {
                $score += 1;
            }

            if ($score < 5) {
                continue;
            }

            if ($bestMatch === null || $score > $bestMatch['score']) {
                $bestMatch = [
                    'index' => $index,
                    'headers' => $row,
                    'col_nama' => $colNama,
                    'score' => $score,
                ];
            }
        }

        if ($bestMatch !== null) {
            return $bestMatch;
        }

        $fallbackIndex = array_key_first($rows) ?? 1;
        $fallbackHeaders = $rows[$fallbackIndex] ?? [];
        $fallbackColNama = 'A';

        foreach ($fallbackHeaders as $key => $val) {
            if ($this->normalizeCellValue($val) !== '') {
                $fallbackColNama = $key;
                break;
            }
        }

        return [
            'index' => $fallbackIndex,
            'headers' => $fallbackHeaders,
            'col_nama' => $fallbackColNama,
            'score' => 0,
        ];
    }

    private function buildPreviewFromRows(array $rows): array
    {
        $headerDetection = $this->detectHeaderRow($rows);
        $headerRowIndex = $headerDetection['index'];
        $headers = $headerDetection['headers'];
        $colNama = $headerDetection['col_nama'];
        $colSatuan = null;
        $colTahun = [];
        $colExtra = [];

        foreach ($headers as $colKey => $cellValue) {
            if ($colKey === $colNama) {
                continue;
            }

            $normalized = $this->normalizeCellValue($cellValue);
            $original = trim((string) $cellValue);

            if ($normalized === '') {
                continue;
            }

            switch ($this->classifyHeader($normalized)) {
                case 'unit':
                    $colSatuan = $colKey;
                    break;
                case 'ignore':
                    break;
                case 'time':
                    $colTahun[$colKey] = $original;
                    break;
                default:
                    $colExtra[$colKey] = $original;
                    break;
            }
        }

        $previewData = [];

        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) {
                continue;
            }

            $namaIndikator = trim(str_replace(["\xA0", "\xc2\xa0"], ' ', (string) ($row[$colNama] ?? '')));

            // Jika kolom nama hasil tebakan ternyata kosong, fallback ke kolom teks pertama yang bukan time/ignore.
            if ($namaIndikator === '') {
                foreach ($headers as $fallbackColKey => $headerCellValue) {
                    if ($fallbackColKey === $colSatuan) {
                        continue;
                    }

                    $headerType = $this->classifyHeader($this->normalizeCellValue($headerCellValue));
                    if (in_array($headerType, ['time', 'ignore', 'unit', 'empty'], true)) {
                        continue;
                    }

                    $candidateValue = trim(str_replace(["\xA0", "\xc2\xa0"], ' ', (string) ($row[$fallbackColKey] ?? '')));
                    if ($candidateValue !== '') {
                        $namaIndikator = $candidateValue;
                        break;
                    }
                }
            }

            if ($namaIndikator === '') {
                continue;
            }

            $values = [];
            $hasAtLeastOneValue = false;
            foreach ($colTahun as $colKey => $tahun) {
                $cellValue = $row[$colKey] ?? null;
                $values[$tahun] = $cellValue;

                if ($cellValue !== null && trim((string) $cellValue) !== '') {
                    $hasAtLeastOneValue = true;
                }
            }

            $extraData = [];
            foreach ($colExtra as $colKey => $namaHeader) {
                $extraData[$namaHeader] = $row[$colKey] ?? '';
            }

            // Abaikan baris footer atau separator yang tidak punya nilai time-series sama sekali.
            if (!$hasAtLeastOneValue && !empty($colTahun)) {
                continue;
            }

            $previewData[] = [
                'nama_data' => $namaIndikator,
                'satuan' => $colSatuan ? (($row[$colSatuan] ?? '-') ?: '-') : '-',
                'id_tema' => null,
                'id_urusan' => null,
                'id_bidang' => null,
                'id_katakunci' => [],
                'values' => $values,
                'extra_fields' => $extraData,
            ];
        }

        return [
            'rows' => $previewData,
            'years' => array_values($colTahun),
            'extra_headers' => array_values($colExtra),
            'header_row' => $headerRowIndex,
            'detected_name_column' => $colNama,
        ];
    }

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

        $bestPreview = null;
        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $rows = $sheet->toArray(null, true, true, true);
            $preview = $this->buildPreviewFromRows($rows);

            if ($bestPreview === null || count($preview['rows']) > count($bestPreview['rows'])) {
                $bestPreview = $preview + ['sheet_name' => $sheet->getTitle()];
            }
        }

        return [
            'rows' => $bestPreview['rows'] ?? [],
            'years' => $bestPreview['years'] ?? [],
            'extra_headers' => $bestPreview['extra_headers'] ?? [],
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
