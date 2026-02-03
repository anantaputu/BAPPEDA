<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrekuensiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('frekuensi')->insert([
            ['nama_frekuensi' => 'Harian'],
            ['nama_frekuensi' => 'Mingguan'],
            ['nama_frekuensi' => 'Bulanan'],
            ['nama_frekuensi' => 'Triwulanan'],
            ['nama_frekuensi' => 'Semesteran'],
            ['nama_frekuensi' => 'Tahunan'],
            ['nama_frekuensi' => 'Lainnya'],
        ]);
    }
}
