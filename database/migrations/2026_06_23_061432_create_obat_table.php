<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // JANGAN gunakan Schema::connection('mysql_pusat') di sini jika ingin bermigrasi ke server lain
    Schema::create('obat', function (Blueprint $table) {
        $table->id('id_obat');
        $table->string('nama_obat');
        $table->string('jenis_obat');
        $table->integer('stok')->default(0);
        $table->decimal('harga_beli', 12, 2)->default(0);
        $table->date('tgl_kadaluarsa');
        $table->softDeletes();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};