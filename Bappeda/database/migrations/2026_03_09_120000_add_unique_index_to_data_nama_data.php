<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('data')
            ->selectRaw('LOWER(TRIM(nama_data)) as normalized_name, COUNT(*) as total')
            ->groupByRaw('LOWER(TRIM(nama_data))')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('normalized_name')
            ->all();

        if (!empty($duplicates)) {
            $sample = implode(', ', array_slice($duplicates, 0, 5));
            throw new RuntimeException(
                "Terdapat duplikasi nama indikator pada tabel data. Bersihkan dulu sebelum migrasi unique index. Contoh: {$sample}"
            );
        }

        DB::statement('CREATE UNIQUE INDEX data_nama_data_unique_ci_trim ON data (LOWER(TRIM(nama_data)))');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS data_nama_data_unique_ci_trim');
    }
};
