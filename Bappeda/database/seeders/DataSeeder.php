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
        // if (empty($tema) || empty($urusan) || empty($bidang) || empty($frekuensi)) {
        //     $this->command->warn('Seeder dibatalkan: master data (Tema/Urusan/Bidang/Frekuensi) masih kosong.');
        //     return;
        // }

        // // 1. Tambahkan data spesifik utama (opsional, data yang Anda buat sebelumnya)
        // $indikatorUtama = [
        //     'Persentase Kemiskinan',
        //     'Tingkat Pengangguran Terbuka',
        //     'Indeks Pembangunan Manusia',
        //     'Pertumbuhan Ekonomi',
        //     'Angka Harapan Hidup',
        //     'Rata-rata Lama Sekolah',
        // ];


    }
}