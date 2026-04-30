<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Data;
use App\Models\DataValue;
use App\Models\Frekuensi;
use App\Models\Katakunci;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        $temaIds = Tema::pluck('id_tema')->all();
        $urusanIds = Urusan::pluck('id_urusan')->all();
        $bidangIds = Bidang::pluck('id_bidang')->all();
        $inputerIds = User::where('role_id', 2)->pluck('id')->all();

        $defaultTema = $temaIds[0] ?? 1;
        $defaultUrusan = $urusanIds[0] ?? 1;
        $defaultBidang = $bidangIds[0] ?? 1;
        $defaultFrekuensi = Frekuensi::where('nama_frekuensi', 'Tahunan')->value('id_frekuensi') ?? 1;
        $fallbackUserId = User::value('id') ?? 1;

        $datasets = [
            [
                'nama_data' => 'Persentase Penduduk Miskin',
                'deskripsi' => 'Persentase penduduk yang berada di bawah garis kemiskinan daerah.',
                'id_tema' => 2,
                'id_urusan' => 7,
                'id_bidang' => 3,
                'satuan' => 'Persen',
                'sumber' => 'BPS Kabupaten',
                'kata_kunci' => 'kemiskinan sosial kesejahteraan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan untuk evaluasi program penanggulangan kemiskinan.',
                'values' => ['2022' => 11.24, '2023' => 10.58, '2024' => 9.91],
            ],
            [
                'nama_data' => 'Angka Harapan Hidup',
                'deskripsi' => 'Perkiraan rata-rata lamanya hidup penduduk sejak lahir.',
                'id_tema' => 2,
                'id_urusan' => 2,
                'id_bidang' => 1,
                'satuan' => 'Tahun',
                'sumber' => 'Dinas Kesehatan',
                'kata_kunci' => 'kesehatan umur harapan hidup',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Menjadi salah satu komponen perhitungan IPM.',
                'values' => ['2022' => 71.84, '2023' => 72.16, '2024' => 72.41],
            ],
            [
                'nama_data' => 'Tingkat Pengangguran Terbuka',
                'deskripsi' => 'Persentase angkatan kerja yang belum memperoleh pekerjaan.',
                'id_tema' => 3,
                'id_urusan' => 8,
                'id_bidang' => 2,
                'satuan' => 'Persen',
                'sumber' => 'Dinas Tenaga Kerja',
                'kata_kunci' => 'tenaga kerja pengangguran ekonomi',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Biasa dipakai sebagai indikator kondisi pasar kerja.',
                'values' => ['2022' => 5.47, '2023' => 5.11, '2024' => 4.88],
            ],
            [
                'nama_data' => 'Indeks Pembangunan Manusia',
                'deskripsi' => 'Indeks komposit yang menggambarkan kualitas hidup masyarakat.',
                'id_tema' => 3,
                'id_urusan' => 11,
                'id_bidang' => 1,
                'satuan' => 'Indeks',
                'sumber' => 'BPS Kabupaten',
                'kata_kunci' => 'ipm pembangunan manusia kualitas hidup',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Disajikan untuk pemantauan target RPJMD.',
                'values' => ['2022' => 74.12, '2023' => 74.86, '2024' => 75.39],
            ],
            [
                'nama_data' => 'Rata-rata Lama Sekolah',
                'deskripsi' => 'Rata-rata tahun sekolah yang telah ditempuh penduduk usia 25 tahun ke atas.',
                'id_tema' => 3,
                'id_urusan' => 1,
                'id_bidang' => 1,
                'satuan' => 'Tahun',
                'sumber' => 'Dinas Pendidikan',
                'kata_kunci' => 'pendidikan sekolah ipm',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Komponen pendidikan dalam IPM.',
                'values' => ['2022' => 8.76, '2023' => 8.91, '2024' => 9.05],
            ],
            [
                'nama_data' => 'Harapan Lama Sekolah',
                'deskripsi' => 'Lama sekolah yang diharapkan akan dirasakan anak pada masa mendatang.',
                'id_tema' => 3,
                'id_urusan' => 1,
                'id_bidang' => 1,
                'satuan' => 'Tahun',
                'sumber' => 'Dinas Pendidikan',
                'kata_kunci' => 'pendidikan sekolah anak',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan bersama RLS dalam analisis pendidikan.',
                'values' => ['2022' => 13.24, '2023' => 13.41, '2024' => 13.57],
            ],
            [
                'nama_data' => 'Cakupan Layanan Air Minum Layak',
                'deskripsi' => 'Persentase rumah tangga dengan akses terhadap air minum layak.',
                'id_tema' => 4,
                'id_urusan' => 3,
                'id_bidang' => 2,
                'satuan' => 'Persen',
                'sumber' => 'Dinas PUPR',
                'kata_kunci' => 'air minum sanitasi infrastruktur',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Menggambarkan akses dasar masyarakat.',
                'values' => ['2022' => 83.55, '2023' => 85.18, '2024' => 86.72],
            ],
            [
                'nama_data' => 'Cakupan Sanitasi Layak',
                'deskripsi' => 'Persentase rumah tangga yang memiliki akses terhadap sanitasi layak.',
                'id_tema' => 4,
                'id_urusan' => 3,
                'id_bidang' => 2,
                'satuan' => 'Persen',
                'sumber' => 'Dinas PUPR',
                'kata_kunci' => 'sanitasi kesehatan lingkungan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan pada evaluasi kualitas permukiman.',
                'values' => ['2022' => 78.64, '2023' => 80.03, '2024' => 81.44],
            ],
            [
                'nama_data' => 'Jumlah Kematian Ibu',
                'deskripsi' => 'Jumlah kasus kematian ibu pada masa kehamilan, persalinan, dan nifas.',
                'id_tema' => 2,
                'id_urusan' => 2,
                'id_bidang' => 1,
                'satuan' => 'Kasus',
                'sumber' => 'Dinas Kesehatan',
                'kata_kunci' => 'ibu kesehatan persalinan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Indikator prioritas pelayanan kesehatan ibu.',
                'values' => ['2022' => 7, '2023' => 6, '2024' => 5],
            ],
            [
                'nama_data' => 'Jumlah Kematian Bayi',
                'deskripsi' => 'Jumlah kematian bayi usia di bawah satu tahun dalam satu tahun berjalan.',
                'id_tema' => 2,
                'id_urusan' => 2,
                'id_bidang' => 1,
                'satuan' => 'Kasus',
                'sumber' => 'Dinas Kesehatan',
                'kata_kunci' => 'bayi kesehatan kematian',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan untuk pemantauan kesehatan anak.',
                'values' => ['2022' => 42, '2023' => 37, '2024' => 33],
            ],
            [
                'nama_data' => 'Prevalensi Stunting Balita',
                'deskripsi' => 'Persentase balita yang mengalami stunting berdasarkan hasil pengukuran.',
                'id_tema' => 2,
                'id_urusan' => 2,
                'id_bidang' => 3,
                'satuan' => 'Persen',
                'sumber' => 'Dinas Kesehatan',
                'kata_kunci' => 'stunting balita gizi',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Masuk indikator prioritas penanganan gizi.',
                'values' => ['2022' => 19.84, '2023' => 18.21, '2024' => 16.95],
            ],
            [
                'nama_data' => 'Persentase Rumah Layak Huni',
                'deskripsi' => 'Persentase rumah tangga yang menempati rumah sesuai standar kelayakan.',
                'id_tema' => 4,
                'id_urusan' => 4,
                'id_bidang' => 2,
                'satuan' => 'Persen',
                'sumber' => 'Dinas Perumahan',
                'kata_kunci' => 'perumahan rumah layak huni',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan pada evaluasi kualitas permukiman.',
                'values' => ['2022' => 81.33, '2023' => 82.74, '2024' => 84.06],
            ],
            [
                'nama_data' => 'Panjang Jalan Kabupaten Kondisi Baik',
                'deskripsi' => 'Total panjang jalan kabupaten yang berada dalam kondisi baik.',
                'id_tema' => 5,
                'id_urusan' => 3,
                'id_bidang' => 2,
                'satuan' => 'Kilometer',
                'sumber' => 'Dinas PUPR',
                'kata_kunci' => 'jalan infrastruktur konektivitas',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Mendukung analisis konektivitas wilayah.',
                'values' => ['2022' => 412.5, '2023' => 428.7, '2024' => 441.2],
            ],
            [
                'nama_data' => 'Rasio Elektrifikasi Rumah Tangga',
                'deskripsi' => 'Persentase rumah tangga yang telah menikmati akses listrik.',
                'id_tema' => 5,
                'id_urusan' => 11,
                'id_bidang' => 2,
                'satuan' => 'Persen',
                'sumber' => 'Dinas ESDM',
                'kata_kunci' => 'listrik energi rumah tangga',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Mengukur pemerataan akses energi rumah tangga.',
                'values' => ['2022' => 97.82, '2023' => 98.34, '2024' => 98.91],
            ],
            [
                'nama_data' => 'Produksi Padi',
                'deskripsi' => 'Jumlah produksi padi gabah kering giling dalam satu tahun.',
                'id_tema' => 6,
                'id_urusan' => 10,
                'id_bidang' => 2,
                'satuan' => 'Ton',
                'sumber' => 'Dinas Pertanian',
                'kata_kunci' => 'pertanian padi pangan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Dipakai untuk analisis ketahanan pangan daerah.',
                'values' => ['2022' => 53820, '2023' => 55140, '2024' => 54760],
            ],
            [
                'nama_data' => 'Produksi Jagung',
                'deskripsi' => 'Jumlah produksi jagung pipilan kering dalam satu tahun.',
                'id_tema' => 6,
                'id_urusan' => 10,
                'id_bidang' => 2,
                'satuan' => 'Ton',
                'sumber' => 'Dinas Pertanian',
                'kata_kunci' => 'pertanian jagung pangan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Salah satu komoditas utama pangan daerah.',
                'values' => ['2022' => 18740, '2023' => 19480, '2024' => 20120],
            ],
            [
                'nama_data' => 'Jumlah UMKM Aktif',
                'deskripsi' => 'Jumlah pelaku usaha mikro kecil menengah yang aktif berusaha.',
                'id_tema' => 5,
                'id_urusan' => 11,
                'id_bidang' => 3,
                'satuan' => 'Unit',
                'sumber' => 'Dinas Koperasi dan UMKM',
                'kata_kunci' => 'umkm usaha ekonomi',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan untuk memantau pertumbuhan ekonomi kerakyatan.',
                'values' => ['2022' => 12480, '2023' => 13140, '2024' => 13895],
            ],
            [
                'nama_data' => 'Persentase Penduduk Bekerja di Sektor Formal',
                'deskripsi' => 'Persentase penduduk bekerja yang terserap pada sektor formal.',
                'id_tema' => 3,
                'id_urusan' => 8,
                'id_bidang' => 3,
                'satuan' => 'Persen',
                'sumber' => 'Dinas Tenaga Kerja',
                'kata_kunci' => 'tenaga kerja formal pekerjaan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Menunjukkan kualitas penyerapan tenaga kerja.',
                'values' => ['2022' => 41.82, '2023' => 43.19, '2024' => 44.77],
            ],
            [
                'nama_data' => 'Indeks Pemberdayaan Gender',
                'deskripsi' => 'Indeks yang menggambarkan partisipasi perempuan dalam ekonomi dan politik.',
                'id_tema' => 2,
                'id_urusan' => 9,
                'id_bidang' => 1,
                'satuan' => 'Indeks',
                'sumber' => 'Dinas P3A',
                'kata_kunci' => 'gender perempuan pemberdayaan',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Digunakan dalam evaluasi kesetaraan gender.',
                'values' => ['2022' => 72.14, '2023' => 73.28, '2024' => 74.03],
            ],
            [
                'nama_data' => 'Cakupan Kepesertaan Jaminan Kesehatan',
                'deskripsi' => 'Persentase penduduk yang telah terdaftar pada program jaminan kesehatan.',
                'id_tema' => 2,
                'id_urusan' => 2,
                'id_bidang' => 3,
                'satuan' => 'Persen',
                'sumber' => 'Dinas Kesehatan',
                'kata_kunci' => 'jaminan kesehatan bpjs',
                'tahun_terbit' => 2024,
                'informasi_tambahan' => 'Menggambarkan cakupan perlindungan kesehatan masyarakat.',
                'values' => ['2022' => 91.42, '2023' => 93.07, '2024' => 94.31],
            ],
        ];

        foreach ($datasets as $index => $dataset) {
            $userId = $inputerIds[$index % max(count($inputerIds), 1)] ?? $fallbackUserId;

            $data = Data::updateOrCreate(
                ['nama_data' => $dataset['nama_data']],
                [
                    'id_user' => $userId,
                    'id_tema' => in_array($dataset['id_tema'], $temaIds, true) ? $dataset['id_tema'] : $defaultTema,
                    'id_urusan' => in_array($dataset['id_urusan'], $urusanIds, true) ? $dataset['id_urusan'] : $defaultUrusan,
                    'id_bidang' => in_array($dataset['id_bidang'], $bidangIds, true) ? $dataset['id_bidang'] : $defaultBidang,
                    'id_frekuensi' => $defaultFrekuensi,
                    'deskripsi' => $dataset['deskripsi'],
                    'satuan' => $dataset['satuan'],
                    'sumber' => $dataset['sumber'],
                    'tahun_terbit' => $dataset['tahun_terbit'],
                    'informasi_tambahan' => $dataset['informasi_tambahan'],
                ]
            );

            $keywordIds = collect(explode(' ', $dataset['kata_kunci']))
                ->filter()
                ->map(function (string $keyword) {
                    return Katakunci::firstOrCreate([
                        'nama_katakunci' => trim($keyword),
                    ])->id_katakunci;
                })
                ->values()
                ->all();

            $data->katakunci()->sync($keywordIds);

            foreach ($dataset['values'] as $tahun => $nilai) {
                DataValue::updateOrCreate(
                    [
                        'id_data' => $data->id_data,
                        'tahun' => (string) $tahun,
                    ],
                    ['nilai' => $nilai]
                );
            }
        }
    }
}
