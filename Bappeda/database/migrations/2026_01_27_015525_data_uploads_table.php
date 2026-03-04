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

    $table->unsignedBigInteger('id_data')->nullable();
    $table->unsignedBigInteger('id_user');
    $table->string('periode');      
    $table->string('file_path');    
    $table->jsonb('value')->nullable();
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
