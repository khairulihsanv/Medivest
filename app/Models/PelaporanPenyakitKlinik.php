<?php

namespace App\Models;

use App\Traits\HasPelaporanHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model PelaporanPenyakitKlinik — Fragmen Server B (mysql_klinik)
 *
 * [ARSITEKTUR TERDISTRIBUSI — FRAGMENTASI HORIZONTAL]
 * Model ini menyimpan dan membaca data pelaporan penyakit
 * untuk wilayah yang tergabung dalam GRUP SERVER B:
 *   → Taman (dan wilayah lain yang nanti ditambahkan ke grup ini)
 *
 * Nama tabel IDENTIK dengan PelaporanPenyakit ('pelaporan_penyakit'),
 * namun berada di database yang berbeda (db_medivest_klinik / Server B).
 * Inilah prinsip Fragmentasi Horizontal: schema sama, data dipecah berdasarkan baris.
 *
 * Kolom: id_laporan, nama_pasien, nik, jenis_penyakit, tgl_diagnosis,
 *        wilayah, tingkat_keparahan, catatan_klinis, deleted_at
 */
class PelaporanPenyakitKlinik extends Model
{
    use SoftDeletes, HasPelaporanHelpers;

    // ─── KONFIGURASI TABEL ─────────────────────────────────────────────

    /**
     * [ARSITEKTUR TERDISTRIBUSI] Koneksi Server B — Database Klinik.
     * Wilayah yang disimpan di sini: Taman.
     * Host dikonfigurasi via env('DB_HOST_KLINIK') di config/database.php —
     * TIDAK ada IP yang di-hardcode di kode PHP.
     * Saat Fase B: cukup ganti DB_HOST_KLINIK di .env ke IP asli PC teman.
     */
    protected $connection = 'mysql_klinik';
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
