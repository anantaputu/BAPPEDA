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
        Schema::create('data_fields', function (Blueprint $table) {
    $table->id('id_field');

    $table->unsignedBigInteger('id_data');
    $table->string('nama_field');      // Nama kolom di Excel
    $table->string('key_field');       // snake_case (jumlah_sekolah)
    $table->enum('tipe_field', ['number', 'text', 'date']);
    $table->boolean('wajib')->default(false);

    $table->timestamps();

    $table->foreign('id_data')
          ->references('id_data')
          ->on('data')
          ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_fields');
    }
};
