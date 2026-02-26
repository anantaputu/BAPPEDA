<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Buat tabel master kata kunci
        Schema::create('katakunci', function (Blueprint $table) {
            $table->id('id_katakunci');
            $table->string('nama_katakunci')->unique();
            $table->timestamps();
        });

        // 2. Buat tabel pivot (Many-to-Many)
        // Ini memungkinkan 1 data punya banyak kata kunci
        Schema::create('data_katakunci_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_data')->constrained('data', 'id_data')->onDelete('cascade');
            $table->foreignId('id_katakunci')->constrained('katakunci', 'id_katakunci')->onDelete('cascade');
        });

        // 3. Hapus kolom lama di tabel data
        Schema::table('data', function (Blueprint $table) {
            $table->dropColumn('kata_kunci');
        });
    }

    public function down()
    {
        Schema::table('data', function (Blueprint $table) {
            $table->string('kata_kunci')->nullable();
        });
        Schema::dropIfExists('data_katakunci_pivot');
        Schema::dropIfExists('katakunci');
    }
};
