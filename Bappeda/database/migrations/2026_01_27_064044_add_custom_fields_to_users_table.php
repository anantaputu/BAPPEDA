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
        Schema::table('users', function (Blueprint $table) {
    $table->string('username')->unique()->after('id');
    $table->unsignedBigInteger('role_id')->after('password');
    $table->string('nama_depan')->after('role_id');
    $table->string('nama_belakang')->nullable()->after('nama_depan');
    $table->boolean('status_aktif')->default(true)->after('nama_belakang');

    $table->foreign('role_id')
          ->references('id_role')
          ->on('roles')
          ->onDelete('restrict');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
    $table->dropForeign(['role_id']);
    $table->dropColumn([
        'username',
        'role_id',
        'nama_depan',
        'nama_belakang',
        'status_aktif',
    ]);
});
    }
};
