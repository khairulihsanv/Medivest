<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Model PelaporanPenyakit — Tabel 'pelaporan_penyakit' di database db_kesehatan_terpadu
 *
 * Migrasi dari class Penyakit di PHP native.
 * Kolom: id_laporan, nama_pasien, nik, jenis_penyakit, tgl_diagnosis,
 *        wilayah, tingkat_keparahan, catatan_klinis
 */
class PelaporanPenyakit extends Model
{
    use SoftDeletes;

    // ─── KONFIGURASI TABEL ─────────────────────────────────────────────

    protected $connection = 'mysql_klinik'; // Server B — Pelaporan & Imunisasi
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

    // ─── QUERY METHODS (migrated from Penyakit class) ──────────────────

    /**
     * Hitung jumlah kasus dikelompokkan per jenis penyakit.
     * Dipakai oleh landing page untuk merender grafik batang Chart.js.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function hitungKasusPerJenis()
    {
        return self::select('jenis_penyakit', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_penyakit')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Hitung kasus berdasarkan jenis penyakit tertentu.
     * DATA FEEDER utama ke modul SiMoSoBa untuk kalkulasi Safety Stock.
     */
    public static function hitungKasusByJenis(string $jenis_penyakit): int
    {
        return (int) self::where('jenis_penyakit', $jenis_penyakit)->count();
    }

    /**
     * Tentukan class CSS untuk badge tingkat keparahan.
     */
    public function getSeverityClassAttribute(): string
    {
        $sev = $this->tingkat_keparahan ?? '';

        if (in_array($sev, ['Kritis', 'Berat'])) {
            return 'bg-red-50 text-red-600 border-red-100';
        }

        if ($sev === 'Sedang') {
            return 'bg-amber-50 text-amber-600 border-amber-100';
        }

        return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    }
}
