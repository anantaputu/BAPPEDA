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
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
        
        if (strtolower($file->getClientOriginalExtension()) === 'csv') {
            $content = file_get_contents($file->getPathname());
            $delimiter = (substr_count($content, ';') > substr_count($content, ',')) ? ';' : ',';
            $reader->setDelimiter($delimiter);
        }

        $spreadsheet = $reader->load($file->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // 1. CARI BARIS HEADER (Judul Kolom)
        // KODE BARU (PERBAIKAN)
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

        // Jika tidak ketemu, paksa baris 1 sebagai header, kolom A sebagai Nama Indikator
        if ($headerRowIndex === -1) {
            $headerRowIndex = 1;
            $headers = $rows[1];
            foreach ($headers as $key => $val) { if (!empty($val)) { $colNama = $key; break; } }
            if (!$colNama) $colNama = 'A';
        }

        // 2. KLASIFIKASI KOLOM (Waktu vs Ekstra)
        $waktuKeywords = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des', 'minggu', 'triwulan', 'semester', 'kondisi'];
        $ignoreHeaders = ['no', 'nomor'];

        $colTahun = []; // Untuk waktu/nilai
        $colExtra = []; // Untuk IUP, Keterangan, dll

        foreach ($headers as $colKey => $cellValue) {
            if ($colKey === $colNama) continue; // Lewati kolom Nama Indikator

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
                
                // Deteksi jika angka tahun (2024, 2025)
                if (preg_match('/(20\d{2})/', $val)) $isWaktu = true;
                
                // Deteksi jika mengandung nama bulan/minggu/semester
                foreach ($waktuKeywords as $kw) {
                    if (str_contains($val, $kw)) { $isWaktu = true; break; }
                }

                if ($isWaktu) {
                    $colTahun[$colKey] = $asli; // Masuk ke tabel nilai (Waktu)
                } else {
                    $colExtra[$colKey] = $asli; // Sisanya otomatis jadi Atribut Tambahan (IUP, dll)
                }
            }
        }

        // 3. SUSUN DATA PREVIEW
        $previewData = [];
        foreach ($rows as $index => $row) {
            if ($index <= $headerRowIndex) continue;

            $namaIndikator = trim(str_replace(["\xA0", "\xc2\xa0"], ' ', ($row[$colNama] ?? '')));
            if (empty($namaIndikator) || strlen($namaIndikator) < 2) continue;

            $values = [];
            foreach ($colTahun as $colKey => $tahun) $values[$tahun] = $row[$colKey] ?? null;

            $extraData = [];
            foreach ($colExtra as $colKey => $namaHeader) {
                $extraData[$namaHeader] = $row[$colKey] ?? '';
            }

            $previewData[] = [
                'nama_indikator' => $namaIndikator,
                'satuan'         => $colSatuan ? ($row[$colSatuan] ?? '-') : '-',
                'id_tema'        => null,
                'id_urusan'      => null,
                'id_bidang'      => null,
                'values'         => $values,
                'extra_fields'   => $extraData // Dikirim ke Vue
            ];
        }

        return [
            'rows'          => $previewData,
            'years'         => array_values($colTahun),
            'extra_headers' => array_values($colExtra)
        ];
    }

    // 3. FUNGSI SIMPAN BULK/MASSAL (Pindahan dari Controller)
   // ==========================================
    // FUNGSI SIMPAN BULK/MASSAL (DARI PREVIEW)
    // ==========================================
    public function processBulkData($dataset, $years, $userId, $fileName = 'Multi Data Excel')
    {
        DB::beginTransaction();
        try {
            // 1. Simpan Riwayat Upload ke data_uploads (sebagai JSONB Backup)
            $uploadLog = \App\Models\DataUpload::create([
                'id_user'   => $userId,
                'periode'   => date('Y'),
                'file_path' => $fileName,
                'status'    => 'valid',
                'value'     => json_encode(['years' => $years, 'dataset' => $dataset]) 
            ]);

            // 2. Simpan Data ke Tabel Master
            foreach ($dataset as $row) {
                $dataMaster = \App\Models\Data::updateOrCreate(
                    ['nama_indikator' => $row['nama_indikator']],
                    [
                        'id_user'   => $userId,
                        'id_upload' => $uploadLog->id_upload,
                        'id_tema'   => $row['id_tema'],
                        'id_urusan' => $row['id_urusan'],
                        'id_bidang' => $row['id_bidang'],
                        
                        // BERIKAN DEFAULT FREKUENSI (Misal: 1 untuk Tahunan) jika kosong agar tidak error 500
                        'id_frekuensi' => $row['id_frekuensi'] ?? 1, 
                        
                        'satuan'    => $row['satuan'] ?? '-',
                        
                        // TANGKAP EXTRA FIELDS (IUP, Keterangan, dll) KE DALAM JSON
                        // (Hanya jika Anda sudah membuat kolom 'informasi_tambahan' tipe json di tabel 'data')
                        'informasi_tambahan' => isset($row['extra_fields']) ? json_encode($row['extra_fields']) : null,
                        
                        'status'    => 'aktif',
                        'tahun'     => date('Y')
                    ]
                );

                // 3. Simpan Nilai Waktu ke data_values
              // 3. Simpan Nilai Waktu ke data_values
                   // 3. Simpan Nilai Waktu ke data_values
                    foreach ($years as $tahun) {
                        // $tahun disini berisi teks asli dari Excel (Misal: "Januari", "Kondisi Awal Januari 2024", atau "2024")
                        $nilai = $row['values'][$tahun] ?? null;

                        // Pastikan nilai tidak kosong/null
                        if ($nilai !== null && trim((string)$nilai) !== '') {
                            // Bersihkan koma menjadi titik (untuk format desimal database)
                            $nilaiClean = str_replace(',', '.', preg_replace('/[^0-9,\.\-]/', '', $nilai));
                            
                            \App\Models\DataValue::updateOrCreate(
                                // GUNAKAN $tahun SECARA LANGSUNG
                                ['id_data' => $dataMaster->id_data, 'tahun' => $tahun], 
                                ['nilai' => $nilaiClean ?: 0]
                            );
                        }
                    }
        
        // ... DB::commit() dan seterusnya ...
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Lempar pesan error aslinya agar bisa kita baca di Vue
            throw new \Exception($e->getMessage()); 
        }
    }

    // 4. FUNGSI SIMPAN SINGLE
    // 4. FUNGSI SIMPAN SINGLE
    public function processSingleData($formData, $userId)
    {
        DB::beginTransaction();
        try {
            // Ambil tahun pertama dari array values sebagai default tahun untuk master dan log
            $defaultTahun = $formData['values'][0]['tahun'] ?? date('Y');

            // 1. Simpan Master Data
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
                    'tahun'        => $defaultTahun,
                    
                    // [PERBAIKAN DI SINI] Tangkap extra_fields dan jadikan JSON
                    'informasi_tambahan' => isset($formData['extra_fields']) && !empty($formData['extra_fields']) 
                                            ? json_encode($formData['extra_fields']) 
                                            : null,
                ]
            );

            // 2. Looping array values dari Vue
            foreach ($formData['values'] as $item) {
                $nilaiClean = preg_replace('/[^0-9,\.\-]/', '', $item['nilai']); 
                
                if (strpos($nilaiClean, ',') !== false && strpos($nilaiClean, '.') !== false) {
                    $nilaiClean = str_replace('.', '', $nilaiClean);
                    $nilaiClean = str_replace(',', '.', $nilaiClean);
                } elseif (strpos($nilaiClean, ',') !== false) {
                    $nilaiClean = str_replace(',', '.', $nilaiClean);
                }
                
                // 3. Simpan Nilai ke data_values
                DataValue::updateOrCreate(
                    ['id_data' => $dataMaster->id_data, 'tahun' => $item['tahun']], 
                    ['nilai' => (float) $nilaiClean]
                );
            }
                
            // 4. Catat log ke tabel data_uploads
            DataUpload::create([
                'id_user'   => $userId,
                'id_data'   => $dataMaster->id_data,
                'periode'   => $defaultTahun, 
                'status'    => 'valid',
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

    // 5. FUNGSI UPDATE SINGLE DATA
    public function updateSingleData($id, $formData, $userId)
    {
        DB::beginTransaction();
        try {
            // 1. Update Master Data
            $dataMaster = Data::findOrFail($id);
            $dataMaster->update([
                'nama_indikator' => trim($formData['nama_indikator']),
                'id_tema'        => $formData['id_tema'],
                'id_urusan'      => $formData['id_urusan'],
                'id_bidang'      => $formData['id_bidang'],
                'id_frekuensi'   => $formData['id_frekuensi'] ?? 1,
                'satuan'         => $formData['satuan'] ?? '-',
                'deskripsi'      => $formData['deskripsi'] ?? null,
                'sumber'         => $formData['sumber'] ?? null,
                'status'         => $formData['status'] ?? 'aktif',
                
                // [PERBAIKAN DI SINI] Tangkap extra_fields yang diedit dan jadikan JSON
                // Jika tidak ada extra_fields baru yang dikirim, biarkan informasi_tambahan yang lama
                'informasi_tambahan' => isset($formData['extra_fields']) 
                                        ? json_encode($formData['extra_fields']) 
                                        : $dataMaster->informasi_tambahan,
            ]);

            // 2. Looping array values dari form edit
            $requestedYears = [];
            foreach ($formData['values'] as $item) {
                // Bersihkan nilai
                $nilaiClean = preg_replace('/[^0-9,\.\-]/', '', $item['nilai']); 
                if (strpos($nilaiClean, ',') !== false && strpos($nilaiClean, '.') !== false) {
                    $nilaiClean = str_replace('.', '', $nilaiClean);
                    $nilaiClean = str_replace(',', '.', $nilaiClean);
                } elseif (strpos($nilaiClean, ',') !== false) {
                    $nilaiClean = str_replace(',', '.', $nilaiClean);
                }
                
                $requestedYears[] = $item['tahun'];

                // 3. Simpan Nilai ke data_values (Update jika ada, Insert jika baru)
                DataValue::updateOrCreate(
                    ['id_data' => $dataMaster->id_data, 'tahun' => $item['tahun']], 
                    ['nilai' => (float) $nilaiClean]
                );
            }

            // Hapus nilai di database yang di-hapus user dari tampilan UI (Tong sampah ditekan)
            if (count($requestedYears) > 0) {
                DataValue::where('id_data', $dataMaster->id_data)
                    ->whereNotIn('tahun', $requestedYears)
                    ->delete();
            }
                
            // 4. Catat log ke tabel data_uploads untuk riwayat revisi
            $defaultTahun = count($requestedYears) > 0 ? $requestedYears[0] : date('Y');
            DataUpload::create([
                'id_user'   => $userId,
                'id_data'   => $dataMaster->id_data,
                'periode'   => $defaultTahun, 
                'status'    => 'valid',
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