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
    Schema::table('data_values', function (Blueprint $table) {
        // Mengubah dari year (integer) menjadi string
        $table->string('tahun')->change(); 
    });
}

public function down(): void
{
    Schema::table('data_values', function (Blueprint $table) {
        $table->year('tahun')->change();
    });
}
};
