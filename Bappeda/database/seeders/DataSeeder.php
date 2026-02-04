<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Data;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID dari master data
        $tema       = Tema::pluck('id_tema')->toArray();
        $urusan     = Urusan::pluck('id_urusan')->toArray();
        $bidang     = Bidang::pluck('id_bidang')->toArray();
        $frekuensi  = Frekuensi::pluck('id_frekuensi')->toArray();

        // Safety check agar tidak error jika tabel master kosong
        if (empty($tema) || empty($urusan) || empty($bidang) || empty($frekuensi)) {
            $this->command->warn('Seeder dibatalkan: master data (Tema/Urusan/Bidang/Frekuensi) masih kosong.');
            return;
        }

        // 1. Tambahkan data spesifik utama (opsional, data yang Anda buat sebelumnya)
        $indikatorUtama = [
            'Persentase Kemiskinan',
            'Tingkat Pengangguran Terbuka',
            'Indeks Pembangunan Manusia',
            'Pertumbuhan Ekonomi',
            'Angka Harapan Hidup',
            'Rata-rata Lama Sekolah',
        ];

        foreach ($indikatorUtama as $nama) {
            // Bagian Indikator Utama
            Data::create([
                'nama_indikator' => $nama,
                'deskripsi'      => 'Indikator prioritas pembangunan daerah.',
                'id_tema'        => fake()->randomElement($tema),
                'id_urusan'      => fake()->randomElement($urusan),
                'id_bidang'      => fake()->randomElement($bidang),
                'id_frekuensi'   => fake()->randomElement($frekuensi),
                'kata_kunci'     => strtolower(str_replace(' ', ',', $nama)),
                'satuan'         => 'Persen',
                'sumber'         => 'BAPPEDA Kota Mataram',
                'status'         => 'valid',
                // HAPUS BARIS TAHUN_DATA DI SINI
            ]);

            // Bagian Loop 100 Data
            for ($i = 1; $i <= 100; $i++) {
                Data::create([
                    'nama_indikator' => rtrim(fake()->sentence(3), '.'),
                    'deskripsi'      => fake()->paragraph(),
                    'id_tema'        => fake()->randomElement($tema),
                    'id_urusan'      => fake()->randomElement($urusan),
                    'id_bidang'      => fake()->randomElement($bidang),
                    'id_frekuensi'   => fake()->randomElement($frekuensi),
                    'kata_kunci'     => fake()->words(3, true),
                    'satuan'         => fake()->randomElement(['Persen', 'Jiwa', 'Km']),
                    'sumber'         => 'Dinas ' . fake()->company(),
                    'status'         => fake()->randomElement(['valid', 'pending', 'invalid']),
                    // HAPUS BARIS TAHUN_DATA DI SINI JUGA
                ]);
            }
        }

        $this->command->info('Berhasil menambahkan 106 data indikator.');
    }
}