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
        Schema::create('data_uploads', function (Blueprint $table) {
    $table->id('id_upload');

    $table->unsignedBigInteger('id_data');
    $table->unsignedBigInteger('id_user');

    $table->string('periode');      // contoh: 2024 / 2024-TW1
    $table->string('file_path');    // lokasi file Excel

    $table->enum('status', [
        'draft',
        'diajukan',
        'disetujui',
        'ditolak'
    ])->default('draft');

    $table->text('catatan')->nullable();
    $table->timestamps();

    $table->foreign('id_data')
          ->references('id_data')
          ->on('data')
          ->onDelete('cascade');

    $table->foreign('id_user')
          ->references('id')
          ->on('users')
          ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_uploads');
    }
};
