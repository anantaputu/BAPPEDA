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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_data')->nullable(); // Tetap simpan ID data meskipun datanya dihapus
            
            // Kolom kunci untuk kejelasan aksi
            $table->string('action'); // 'UPLOAD', 'EDIT', 'DELETE'
            $table->string('target_name'); // Simpan nama indikator di sini sebagai backup jika data dihapus
            
            $table->text('description')->nullable(); // Detail tambahan (misal: "Mengubah nilai tahun 2024")
            $table->json('payload')->nullable(); // Opsional: simpan data lama/baru untuk perbandingan
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
