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
        if (!Schema::connection('mysql_klinik')->hasTable('imunisasi')) {
            Schema::connection('mysql_klinik')->create('imunisasi', function (Blueprint $table) {
                $table->id('id_imunisasi');
                $table->string('nama_anak');
                $table->string('nama_orang_tua');
                $table->integer('usia_bulan');
                $table->string('jenis_vaksin');
                $table->integer('dosis_ke');
                $table->date('tgl_jadwal');
                $table->string('no_hp');
                $table->string('status_reminder')->default('Belum Dikirim');
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_klinik')->dropIfExists('imunisasi');
    }
};
