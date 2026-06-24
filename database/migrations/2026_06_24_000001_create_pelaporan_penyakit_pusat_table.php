<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [ARSITEKTUR TERDISTRIBUSI] Migration Fragmentasi Horizontal — Server A
 *
 * Membuat tabel 'pelaporan_penyakit' di mysql_pusat (Server A).
 * Tabel ini menyimpan fragmen wilayah: Manguharjo, Kartoharjo.
 *
 * Tabel yang sama juga ada di mysql_klinik (Server B), menyimpan wilayah Taman.
 * Ini adalah implementasi Fragmentasi Horizontal: schema identik, data dipisah per baris.
 *
 * Cara menjalankan migration ini saja (tanpa migration lain):
 *   php artisan migrate --path=database/migrations/2026_06_24_000001_create_pelaporan_penyakit_pusat_table.php
 *
 * Atau jalankan semua migration sekaligus (direkomendasikan):
 *   php artisan migrate
 */
return new class extends Migration
{
    /**
     * [ARSITEKTUR TERDISTRIBUSI] Koneksi Server A — Database Pusat.
     */
    protected $connection = 'mysql_pusat';

    public function up(): void
    {
        // Dilindungi hasTable() agar migration aman dijalankan ulang
        if (!Schema::connection('mysql_pusat')->hasTable('pelaporan_penyakit')) {
            Schema::connection('mysql_pusat')->create('pelaporan_penyakit', function (Blueprint $table) {
                $table->id('id_laporan');
                $table->string('nama_pasien');
                $table->string('nik');
                $table->string('jenis_penyakit');
                $table->date('tgl_diagnosis');
                $table->string('wilayah');
                $table->string('tingkat_keparahan');
                $table->text('catatan_klinis')->nullable();
                $table->softDeletes(); // Wajib: Model PelaporanPenyakit memakai SoftDeletes
            });
        }
    }

    public function down(): void
    {
        Schema::connection('mysql_pusat')->dropIfExists('pelaporan_penyakit');
    }
};
