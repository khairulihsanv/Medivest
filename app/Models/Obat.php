<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model Obat — Tabel 'obat' di database db_kesehatan_terpadu
 *
 * Menggabungkan logika bisnis dari class Obat (base) dan DetailObat (child)
 * pada arsitektur PHP native ke dalam satu Eloquent Model.
 *
 * Kolom: id_obat, nama_obat, jenis_obat, stok, harga_beli, tgl_kadaluarsa
 */
class Obat extends Model
{
    // ─── KONFIGURASI TABEL ─────────────────────────────────────────────

    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // ─── MASS ASSIGNMENT PROTECTION ────────────────────────────────────

    protected $fillable = [
        'nama_obat',
        'jenis_obat',
        'stok',
        'harga_beli',
        'tgl_kadaluarsa',
    ];

    // ─── ATTRIBUTE CASTING ─────────────────────────────────────────────

    protected $casts = [
        'stok'           => 'integer',
        'harga_beli'     => 'float',
        'tgl_kadaluarsa' => 'date',
    ];

    // ─── BUSINESS LOGIC (migrated from DetailObat class) ───────────────

    /**
     * Menghitung total stok setelah penerimaan barang masuk.
     * (Polymorphism dari class Obat → DetailObat)
     */
    public static function hitungStokMasuk(int $stok_sekarang, int $jumlah_masuk): int
    {
        return (int) $stok_sekarang + (int) $jumlah_masuk;
    }

    /**
     * Hitung sisa hari hingga tanggal kadaluarsa.
     */
    public function hitungSisaHariKadaluarsa(): int
    {
        $selisih_detik = strtotime($this->tgl_kadaluarsa) - time();
        return (int) round($selisih_detik / 86400);
    }

    /**
     * Tentukan label status stok berdasarkan jumlah.
     *
     * @return array{label:string, class:string, dot:string}
     */
    public function getStatusStok(): array
    {
        $stok = (int) $this->stok;

        if ($stok <= 10) {
            return [
                'label' => 'Kritis',
                'class' => 'bg-red-50 text-red-700 border-red-200/60',
                'dot'   => 'bg-red-500 pulse-dot',
            ];
        }

        if ($stok <= 50) {
            return [
                'label' => 'Rendah',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                'dot'   => 'bg-amber-500',
            ];
        }

        return [
            'label' => 'Aman',
            'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
            'dot'   => 'bg-emerald-500',
        ];
    }

    /**
     * Generate kode prefix berdasarkan jenis obat.
     */
    public function getKodeAttribute(): string
    {
        $prefix = ($this->jenis_obat === 'Vaksin') ? 'VAK-' : 'OBT-';
        return $prefix . str_pad($this->id_obat, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung Safety Stock dan Reorder Point (ROP) secara dinamis
     * berdasarkan jumlah kasus penyakit aktif dari modul Pelaporan.
     *
     * Business Logic:
     *   - Safety Stock naik 20% per kasus penyakit aktif
     *   - ROP = (avg_demand_harian × lead_time) + safety_stock
     */
    public static function hitungRekomendasiStokOtomatis(int $id_obat, string $nama_obat, int $jumlah_kasus_terkait): array
    {
        // Konstanta logistik
        $stok_min_dasar    = 20;
        $lead_time         = 3;   // hari
        $avg_demand_harian = 5;   // unit/hari

        // Safety stock naik 20% per tiap kasus aktif
        $safety_stock = (int) ceil($stok_min_dasar * (1 + ($jumlah_kasus_terkait * 0.2)));

        // Reorder Point
        $rop = ($avg_demand_harian * $lead_time) + $safety_stock;

        // Ambil stok aktual dari database
        $obat = self::find($id_obat);
        $stok_aktual = $obat ? (int) $obat->stok : 0;

        $perlu_order_ulang    = false;
        $rekomendasi_tindakan = 'Aman — Stok Memadai';

        if ($stok_aktual <= $rop) {
            $rekomendasi_tindakan = '⚠️ REKOMENDASI ORDER ULANG SEKARANG! Kasus penyakit meningkat, stok kritis.';
            $perlu_order_ulang    = true;
        }

        return [
            'stok_aktual'   => $stok_aktual,
            'safety_stock'  => $safety_stock,
            'reorder_point' => $rop,
            'tindakan'      => $rekomendasi_tindakan,
            'perlu_order'   => $perlu_order_ulang,
        ];
    }
}
