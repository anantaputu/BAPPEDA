<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tema')->insert([
            ['nama_tema' => 'Metadata 921'],
            ['nama_tema' => 'SDGs'],
            ['nama_tema' => 'RPJMD'],
            ['nama_tema' => 'Renstra'],
            ['nama_tema' => 'Program Unggulan'],
            ['nama_tema' => 'Kewilayahan Kab/Kota'],
            ['nama_tema' => 'LKPJ'],
            ['nama_tema' => 'LPPD'],
        ]);
    }
}
