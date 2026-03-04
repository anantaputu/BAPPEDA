<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_values', function (Blueprint $table) {
            $table->id('id_value');
       
            $table->unsignedBigInteger('id_data'); 
            
            $table->string('tahun'); 
            $table->double('nilai')->nullable(); 
            $table->timestamps();

   
            $table->foreign('id_data')
                  ->references('id_data')->on('data')
                  ->onDelete('cascade'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_values');
    }
};