<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bidang')->insert([
            ['nama_bidang' => 'IK'],
            ['nama_bidang' => 'PSDA'],
            ['nama_bidang' => 'P2M'],
        ]);
    }
}
