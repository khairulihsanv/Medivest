<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * HasPelaporanHelpers — Shared logic untuk fragmentasi horizontal tabel pelaporan_penyakit.
 *
 * [ARSITEKTUR TERDISTRIBUSI]
 * Trait ini di-share antara dua model yang mengakses fragmen berbeda:
 *   - PelaporanPenyakit        → Server A (mysql_pusat)  — wilayah Manguharjo, Kartoharjo
 *   - PelaporanPenyakitKlinik  → Server B (mysql_klinik) — wilayah Taman
 *
 * Dengan memakai trait, perubahan logika cukup dilakukan di satu tempat.
 */
trait HasPelaporanHelpers
{
    /**
     * Hitung jumlah kasus dikelompokkan per jenis penyakit.
     * Dipakai oleh landing page dan chart untuk merender grafik batang Chart.js.
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
     * Digunakan di view pelaporan.blade.php via $laporan->severity_class.
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
