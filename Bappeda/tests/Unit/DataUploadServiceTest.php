<?php

use App\Services\DataUploadService;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('detects the actual header row when a title row appears above the table', function () {
    $csv = <<<CSV
DATA INDIKATOR PEMBANGUNAN KABUPATEN
No,Nama Data,Satuan,2022,2023,2024
1,Persentase Penduduk Miskin,%,11.24,10.58,9.91
2,Angka Harapan Hidup,Tahun,71.84,72.16,72.41
CSV;

    $path = tempnam(sys_get_temp_dir(), 'bulk-upload-');
    file_put_contents($path, $csv);

    $file = new UploadedFile(
        $path,
        'indikator.csv',
        'text/csv',
        null,
        true
    );

    $service = new DataUploadService();
    $result = $service->getPreviewData($file);

    expect($result['rows'])->toHaveCount(2)
        ->and($result['rows'][0]['nama_data'])->toBe('Persentase Penduduk Miskin')
        ->and($result['years'])->toBe(['2022', '2023', '2024']);
});

it('reads the sheet that actually contains the dataset', function () {
    $spreadsheet = new Spreadsheet();
    $coverSheet = $spreadsheet->getActiveSheet();
    $coverSheet->setTitle('Petunjuk');
    $coverSheet->setCellValue('A1', 'Silakan isi data pada sheet kedua');

    $dataSheet = $spreadsheet->createSheet();
    $dataSheet->setTitle('Data');
    $dataSheet->fromArray([
        ['Laporan Data Statistik'],
        ['No', 'Indikator', 'Satuan', '2023', '2024'],
        [1, 'Tingkat Pengangguran Terbuka', '%', 5.11, 4.88],
        [2, 'Angka Harapan Hidup', 'Tahun', 72.16, 72.41],
    ], null, 'A1');

    $path = tempnam(sys_get_temp_dir(), 'bulk-upload-') . '.xlsx';
    (new Xlsx($spreadsheet))->save($path);

    $file = new UploadedFile(
        $path,
        'indikator.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $service = new DataUploadService();
    $result = $service->getPreviewData($file);

    expect($result['rows'])->toHaveCount(2)
        ->and($result['rows'][0]['nama_data'])->toBe('Tingkat Pengangguran Terbuka')
        ->and($result['years'])->toBe(['2023', '2024']);
});
