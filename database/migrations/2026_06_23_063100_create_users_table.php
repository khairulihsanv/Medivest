<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koneksi database: mysql_pusat (Server A - Users & Stok Obat)
     */
    protected $connection = 'mysql_pusat';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::connection('mysql_pusat')->hasTable('users')) {
            Schema::connection('mysql_pusat')->create('users', function (Blueprint $table) {
                $table->id('id_user');
                $table->string('username')->unique();
                $table->string('password');
                $table->string('nama_lengkap');
                $table->string('role')->default('petugas');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_pusat')->dropIfExists('users');
    }
};
