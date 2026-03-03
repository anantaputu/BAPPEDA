<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id('id_data');
            
            // --- TAMBAHAN BARU (Tracking) ---
            $table->unsignedBigInteger('id_user')->nullable()->index();   // Siapa yang input (Inputer)
            $table->unsignedBigInteger('id_upload')->nullable()->index(); // Dari batch upload mana
            
            $table->string('nama_indikator');
            $table->text('deskripsi')->nullable();
            
            // Relasi Metadata
            $table->unsignedBigInteger('id_tema');
            $table->unsignedBigInteger('id_urusan');
            $table->unsignedBigInteger('id_bidang');
            $table->unsignedBigInteger('id_frekuensi');
            
            $table->string('satuan')->nullable();
            $table->string('sumber')->nullable();
            $table->string('kata_kunci')->nullable();
            $table->string('status')->default('aktif');
            
            // Note: Kolom tahun di sini bisa dijadikan 'tahun dasar' atau 'tahun pembuatan'. 
            // Karena nilai tahun detailnya nanti masuk ke tabel 'data_values'.
            $table->year('tahun_terbit')->nullable(); 
            $table->text('informasi_tambahan')->nullable();
            $table->timestamps();

            // --- DEFINISI FOREIGN KEY ---
            
            // FK ke User (Opsional: aktifkan jika tabel users sudah dibuat sebelum tabel ini)
            // $table->foreign('id_user')->references('id')->on('users')->nullOnDelete();

            $table->foreign('id_tema')->references('id_tema')->on('tema');
            $table->foreign('id_urusan')->references('id_urusan')->on('urusan');
            $table->foreign('id_bidang')->references('id_bidang')->on('bidang');
            $table->foreign('id_frekuensi')->references('id_frekuensi')->on('frekuensi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};