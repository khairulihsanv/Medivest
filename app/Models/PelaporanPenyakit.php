<?php

namespace App\Models;

use App\Traits\HasPelaporanHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model PelaporanPenyakit — Fragmen Server A (mysql_pusat)
 *
 * [ARSITEKTUR TERDISTRIBUSI — FRAGMENTASI HORIZONTAL]
 * Model ini hanya menyimpan dan membaca data pelaporan penyakit
 * untuk wilayah yang tergabung dalam GRUP SERVER A:
 *   → Manguharjo, Kartoharjo
 *
 * Data wilayah Taman dikelola oleh PelaporanPenyakitKlinik (mysql_klinik / Server B).
 * Routing wilayah ke model yang tepat dilakukan di PelaporanController@store.
 *
 * Kolom: id_laporan, nama_pasien, nik, jenis_penyakit, tgl_diagnosis,
 *        wilayah, tingkat_keparahan, catatan_klinis, deleted_at
 */
class PelaporanPenyakit extends Model
{
    use SoftDeletes, HasPelaporanHelpers;

    // ─── KONFIGURASI TABEL ─────────────────────────────────────────────

    /**
     * Tabel pelaporan_penyakit menggunakan koneksi default 'mysql'.
     */
    protected $table = 'pelaporan_penyakit';
    protected $primaryKey = 'id_laporan';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // ─── MASS ASSIGNMENT PROTECTION ────────────────────────────────────

    protected $fillable = [
        'nama_pasien',
        'nik',
        'jenis_penyakit',
        'tgl_diagnosis',
        'wilayah',
        'tingkat_keparahan',
        'catatan_klinis',
    ];

    // ─── ATTRIBUTE CASTING ─────────────────────────────────────────────

    protected $casts = [
        'tgl_diagnosis' => 'date',
    ];
}
