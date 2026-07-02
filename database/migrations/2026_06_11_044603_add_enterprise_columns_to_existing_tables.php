<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    /**
     * Run the migrations.
     * Menggunakan proteksi hasTable & hasColumn agar di-skip
     * jika tabel belum ada atau kolom sudah ada.
     */
    public function up(): void
    {
        if (Schema::hasTable('obat') && !Schema::hasColumn('obat', 'deleted_at')) {
            Schema::table('obat', function (Blueprint $table) { $table->softDeletes(); });
        }

        if (Schema::hasTable('imunisasi') && !Schema::hasColumn('imunisasi', 'deleted_at')) {
            Schema::table('imunisasi', function (Blueprint $table) { $table->softDeletes(); });
        }

        if (Schema::hasTable('pelaporan_penyakit') && !Schema::hasColumn('pelaporan_penyakit', 'deleted_at')) {
            Schema::table('pelaporan_penyakit', function (Blueprint $table) { $table->softDeletes(); });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('obat', 'deleted_at')) {
            Schema::table('obat', function (Blueprint $table) { $table->dropSoftDeletes(); });
        }

        if (Schema::hasColumn('imunisasi', 'deleted_at')) {
            Schema::table('imunisasi', function (Blueprint $table) { $table->dropSoftDeletes(); });
        }

        if (Schema::hasColumn('pelaporan_penyakit', 'deleted_at')) {
            Schema::table('pelaporan_penyakit', function (Blueprint $table) { $table->dropSoftDeletes(); });
        }
    }
};
