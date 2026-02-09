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
    Schema::create('data', function (Blueprint $table) {
    $table->id('id_data');
    $table->string('nama_indikator');
    $table->text('deskripsi')->nullable();
    $table->unsignedBigInteger('id_tema');
    $table->unsignedBigInteger('id_urusan');
    $table->unsignedBigInteger('id_bidang');
    $table->unsignedBigInteger('id_frekuensi');
    $table->string('satuan');
    $table->string('sumber');
    $table->string('kata_kunci')->nullable();
    $table->string('status')->default('aktif');
      $table->year('tahun');
    $table->timestamps();

    // Foreign Key
    $table->foreign('id_tema')
          ->references('id_tema')->on('tema');

    $table->foreign('id_urusan')
          ->references('id_urusan')->on('urusan');

    $table->foreign('id_bidang')
          ->references('id_bidang')->on('bidang');

    $table->foreign('id_frekuensi')
          ->references('id_frekuensi')->on('frekuensi');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
