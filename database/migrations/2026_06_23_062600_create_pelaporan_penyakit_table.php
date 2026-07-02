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
        if (!Schema::hasTable('pelaporan_penyakit')) {
            Schema::create('pelaporan_penyakit', function (Blueprint $table) {
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
        Schema::dropIfExists('pelaporan_penyakit');
    }
};
