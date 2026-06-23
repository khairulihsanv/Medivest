<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat tabel 'obat' khusus di database pusat (Server A)
        Schema::connection('mysql_pusat')->create('obat', function (Blueprint $table) {
            $table->id('id_obat'); // Cocok dengan primary key di Model Obat
            $table->string('nama_obat');
            $table->string('jenis_obat');
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->date('tgl_kadaluarsa')->index(); // Tambah Index untuk optimasi pencarian exp date
            $table->softDeletes(); // Wajib karena Model Obat menggunakan trait SoftDeletes
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_pusat')->dropIfExists('obat');
    }
};