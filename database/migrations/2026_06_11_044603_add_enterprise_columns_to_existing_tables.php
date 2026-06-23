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
     * Menggunakan proteksi hasTable & hasColumn agar di-skip
     * jika tabel belum ada atau kolom sudah ada.
     */
    public function up(): void
    {
        $pusat = Schema::connection('mysql_pusat');
        $klinik = Schema::connection('mysql_klinik');

        if ($pusat->hasTable('obat') && !$pusat->hasColumn('obat', 'deleted_at')) {
            $pusat->table('obat', function (Blueprint $table) { $table->softDeletes(); });
        }

        if ($klinik->hasTable('imunisasi') && !$klinik->hasColumn('imunisasi', 'deleted_at')) {
            $klinik->table('imunisasi', function (Blueprint $table) { $table->softDeletes(); });
        }

        if ($klinik->hasTable('pelaporan_penyakit') && !$klinik->hasColumn('pelaporan_penyakit', 'deleted_at')) {
            $klinik->table('pelaporan_penyakit', function (Blueprint $table) { $table->softDeletes(); });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $pusat = Schema::connection('mysql_pusat');
        $klinik = Schema::connection('mysql_klinik');

        if ($pusat->hasColumn('obat', 'deleted_at')) {
            $pusat->table('obat', function (Blueprint $table) { $table->dropSoftDeletes(); });
        }

        if ($klinik->hasColumn('imunisasi', 'deleted_at')) {
            $klinik->table('imunisasi', function (Blueprint $table) { $table->dropSoftDeletes(); });
        }

        if ($klinik->hasColumn('pelaporan_penyakit', 'deleted_at')) {
            $klinik->table('pelaporan_penyakit', function (Blueprint $table) { $table->dropSoftDeletes(); });
        }
    }
};
