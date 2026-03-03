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
       Schema::create('bookmark', function (Blueprint $table) {
    $table->id('id_bookmark');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->unsignedBigInteger('dataset_id'); // Sesuaikan tipe data id_data Anda
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
