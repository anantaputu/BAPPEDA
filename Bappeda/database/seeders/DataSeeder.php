<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;
use App\Models\User;
use App\Models\DataValue;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil satu user untuk relasi id_user
        $user = User::first();
        $userId = $user ? $user->id : 1;

        // Ambil ID pertama dari masing-masing master sebagai default jika tidak ditentukan manual
        $defaultTema = Tema::first()->id_tema ?? 1;
        $defaultUrusan = Urusan::first()->id_urusan ?? 1;
        $defaultBidang = Bidang::first()->id_bidang ?? 1;
        $defaultFrekuensi = Frekuensi::where('nama_frekuensi', 'Tahunan')->first()->id_frekuensi ?? 1;

        // --- DATA 1 ---
        $data1 = Data::create([
            'nama_indikator'    => 'Persentase Penduduk Miskin',
            'deskripsi'         => 'Mengukur tingkat kemiskinan penduduk di wilayah tertentu.',
            'id_tema'           => $defaultTema,
            'id_urusan'         => 7, // Sosial (berdasarkan UrusanSeeder Anda)
            'id_bidang'         => 3, // P2M
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Persen (%)',
            'sumber'            => 'BPS (P3KE)',
            'kata_kunci'        => 'miskin, ekonomi, sosial',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);
        DataValue::create(['id_data' => $data1->id_data, 'tahun' => '2023', 'nilai' => '10.21']);
        DataValue::create(['id_data' => $data1->id_data, 'tahun' => '2024', 'nilai' => '9.85']);

        // --- DATA 2 ---
        $data2 = Data::create([
            'nama_indikator'    => 'Angka Harapan Hidup',
            'deskripsi'         => 'Rata-rata perkiraan tahun hidup yang dapat ditempuh oleh seseorang.',
            'id_tema'           => 2, // SDGs
            'id_urusan'         => 2, // Kesehatan
            'id_bidang'         => 1, // IK
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Tahun',
            'sumber'            => 'Dinas Kesehatan',
            'kata_kunci'        => 'kesehatan, hidup, umur',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);
        DataValue::create(['id_data' => $data2->id_data, 'tahun' => '2024', 'nilai' => '72.5']);

        // --- DATA 3 ---
        $data3 = Data::create([
            'nama_indikator'    => 'Tingkat Pengangguran Terbuka',
            'deskripsi'         => 'Persentase jumlah pengangguran terhadap jumlah angkatan kerja.',
            'id_tema'           => $defaultTema,
            'id_urusan'         => 8, // Tenaga Kerja
            'id_bidang'         => 2, // PSDA
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Persen (%)',
            'sumber'            => 'Disnaker',
            'kata_kunci'        => 'kerja, pengangguran, ekonomi',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);
        DataValue::create(['id_data' => $data3->id_data, 'tahun' => '2024', 'nilai' => '5.12']);

        // --- DATA 4 ---
        Data::create([
            'nama_indikator'    => 'Indeks Pembangunan Manusia (IPM)',
            'deskripsi'         => 'Menjelaskan bagaimana penduduk dapat mengakses hasil pembangunan.',
            'id_tema'           => 3, // RPJMD
            'id_urusan'         => $defaultUrusan,
            'id_bidang'         => $defaultBidang,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Indeks',
            'sumber'            => 'BPS',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);

        // --- DATA 5 ---
        Data::create([
            'nama_indikator'    => 'Rata-rata Lama Sekolah',
            'deskripsi'         => 'Jumlah tahun yang dihabiskan oleh penduduk dalam pendidikan formal.',
            'id_tema'           => $defaultTema,
            'id_urusan'         => 1, // Pendidikan
            'id_bidang'         => 1,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Tahun',
            'sumber'            => 'Disdik',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);

        // --- DATA 6 ---
        Data::create([
            'nama_indikator'    => 'Cakupan Layanan Air Minum',
            'deskripsi'         => 'Persentase rumah tangga yang memiliki akses air minum layak.',
            'id_tema'           => 1,
            'id_urusan'         => 3, // PU
            'id_bidang'         => 2,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Persen (%)',
            'sumber'            => 'PUPR',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);

        // --- DATA 7 ---
        Data::create([
            'nama_indikator'    => 'Jumlah Kematian Ibu',
            'deskripsi'         => 'Jumlah kasus kematian ibu saat persalinan.',
            'id_tema'           => 2,
            'id_urusan'         => 2,
            'id_bidang'         => 1,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Kasus',
            'sumber'            => 'Dinkes',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);

        // --- DATA 8 ---
        Data::create([
            'nama_indikator'    => 'Rasio Konektivitas Jalan',
            'deskripsi'         => 'Tingkat hubungan antar wilayah melalui infrastruktur jalan.',
            'id_tema'           => 5, // Program Unggulan
            'id_urusan'         => 3,
            'id_bidang'         => 2,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Rasio',
            'sumber'            => 'Dishub',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);

        // --- DATA 9 ---
        Data::create([
            'nama_indikator'    => 'Persentase Rumah Layak Huni',
            'deskripsi'         => 'Proporsi rumah tangga yang menempati rumah layak huni.',
            'id_tema'           => 4, // Renstra
            'id_urusan'         => 4, // Perumahan
            'id_bidang'         => 2,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Persen (%)',
            'sumber'            => 'Disperkim',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);

        // --- DATA 10 ---
        Data::create([
            'nama_indikator'    => 'Prevalensi Stunting',
            'deskripsi'         => 'Tingkat kejadian stunting pada balita.',
            'id_tema'           => 2,
            'id_urusan'         => 2,
            'id_bidang'         => 3,
            'id_frekuensi'      => $defaultFrekuensi,
            'id_user'           => $userId,
            'satuan'            => 'Persen (%)',
            'sumber'            => 'Dinkes',
            'status'            => 'aktif',
            'tahun'             => 2024,
        ]);
    }
}