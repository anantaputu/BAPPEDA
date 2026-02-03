<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UrusanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('urusan')->insert([
            ['nama_urusan' => 'Pendidikan'],
            ['nama_urusan' => 'Kesehatan'],
            ['nama_urusan' => 'Pekerjaan Umum dan Penataan Ruang'],
            ['nama_urusan' => 'Perumahan dan Kawasan Permukiman'],
            ['nama_urusan' => 'Ketentraman dan Ketertiban Umum'],
            ['nama_urusan' => 'Perlindungan Masyarakat'],
            ['nama_urusan' => 'Sosial'],
            ['nama_urusan' => 'Tenaga Kerja'],
            ['nama_urusan' => 'Pemberdayaan Perempuan dan Perlindungan Anak'],
            ['nama_urusan' => 'Pangan'],
            ['nama_urusan' => 'Lainnya'],
        ]);
    }
}
