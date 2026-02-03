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
        $tema       = Tema::pluck('id_tema')->toArray();
        $urusan     = Urusan::pluck('id_urusan')->toArray();
        $bidang     = Bidang::pluck('id_bidang')->toArray();
        $frekuensi  = Frekuensi::pluck('id_frekuensi')->toArray();

        // Safety check
        if (
            empty($tema) ||
            empty($urusan) ||
            empty($bidang) ||
            empty($frekuensi)
        ) {
            $this->command->warn('Seeder dibatalkan: master data belum lengkap.');
            return;
        }

        $indikator = [
            'Persentase Kemiskinan',
            'Tingkat Pengangguran Terbuka',
            'Indeks Pembangunan Manusia',
            'Pertumbuhan Ekonomi',
            'Angka Harapan Hidup',
            'Rata-rata Lama Sekolah',
        ];

        foreach ($indikator as $nama) {
            Data::create([
                'nama_indikator' => $nama,
                'deskripsi'      => 'Indikator pembangunan daerah',
                'id_tema'        => fake()->randomElement($tema),
                'id_urusan'      => fake()->randomElement($urusan),
                'id_bidang'      => fake()->randomElement($bidang),
                'id_frekuensi'   => fake()->randomElement($frekuensi),
                'kata_kunci'     => strtolower(str_replace(' ', ',', $nama)),
                'satuan'         => 'Persen',
                'sumber'         => 'BAPPEDA Kota Mataram',
                'status'         => 'aktif',
            ]);
        }
    }
}
