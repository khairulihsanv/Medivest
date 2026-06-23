<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koneksi database yang digunakan oleh migrasi ini.
     */
    protected $connection = 'mysql_pusat';

    /**
     * Run the migrations.
     * Menggunakan proteksi hasTable agar migrasi di-skip
     * jika tabel 'audit_logs' sudah ada di mysql_pusat.
     */
    public function up(): void
    {
        if (!Schema::connection('mysql_pusat')->hasTable('audit_logs')) {
            Schema::connection('mysql_pusat')->create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('id_user');
                $table->string('activity');
                $table->string('target_table');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_pusat')->dropIfExists('audit_logs');
    }
};
