<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_mappings', function (Blueprint $table) {
            $table->id(); // id (primary key)

            // Relasi ke data_uploads
            $table->unsignedBigInteger('id_upload');
            
            // Relasi ke data_fields (Kolom master data)
            $table->unsignedBigInteger('id_field');

            // Kolom Excel (misal: 'A', 'B', 'AB')
            $table->string('excel_column');

            $table->timestamps();

            // --- FOREIGN KEYS (PENTING AGAR RELASI AMAN) ---
            
            // Jika data upload dihapus, mappingnya ikut terhapus
            $table->foreign('id_upload')
                  ->references('id_upload') // Pastikan ini sesuai PK di data_uploads
                  ->on('data_uploads')
                  ->onDelete('cascade');

            // Jika field master dihapus, mapping ikut terhapus
            $table->foreign('id_field')
                  ->references('id_field') // Pastikan ini sesuai PK di data_fields
                  ->on('data_fields')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_mappings');
    }
};