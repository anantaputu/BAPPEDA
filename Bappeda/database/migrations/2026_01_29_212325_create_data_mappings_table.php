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
            $table->id(); 
            $table->unsignedBigInteger('id_upload');
            $table->unsignedBigInteger('id_field');
            $table->string('excel_column');
            $table->timestamps();

            // --- FOREIGN KEYS 
            
            $table->foreign('id_upload')
                  ->references('id_upload') 
                  ->on('data_uploads')
                  ->onDelete('cascade');

          
            $table->foreign('id_field')
                  ->references('id_field') 
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