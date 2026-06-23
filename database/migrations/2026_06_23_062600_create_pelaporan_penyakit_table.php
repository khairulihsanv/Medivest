<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koneksi database: mysql_klinik (Server B - Pelaporan Penyakit & Imunisasi)
     */
    protected $connection = 'mysql_klinik';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::connection('mysql_klinik')->hasTable('pelaporan_penyakit')) {
            Schema::connection('mysql_klinik')->create('pelaporan_penyakit', function (Blueprint $table) {
                $table->id('id_laporan');
                $table->string('nama_pasien');
                $table->string('nik');
                $table->string('jenis_penyakit');
                $table->date('tgl_diagnosis');
                $table->string('wilayah');
                $table->string('tingkat_keparahan');
                $table->text('catatan_klinis')->nullable();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_klinik')->dropIfExists('pelaporan_penyakit');
    }
};
