<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Obat — Tabel 'obat' di database db_medivest_pusat (Server A)
 *
 * Menggabungkan logika bisnis logistik dan arsitektur data terdistribusi.
 * Kolom: id_obat, nama_obat, jenis_obat, stok, harga_beli, tgl_kadaluarsa
 */
class Obat extends Model
{
    use SoftDeletes;

    // ─── KONFIGURASI BASIS DATA TERDISTRIBUSI ───────────────────────────
    // Dihapus: sekarang menggunakan koneksi default 'mysql'
    // ─── KONFIGURASI TABEL LAMA (LEGACY SYSTEM INTEGRATION) ─────────────
    protected $table = 'obat';
    protected $primaryKey = 'id_obat';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false; // Karena tabel bawaan awal tidak pakai created_at/updated_at

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

    // ─── LOGIKA BISNIS (LOGISTIK & PREDIKSI TERINTEGRASI) ────────────────

    /**
     * Menghitung total stok setelah penerimaan barang masuk.
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
        if (!$this->tgl_kadaluarsa) {
            return 0;
        }
        $selisih_detik = strtotime($this->tgl_kadaluarsa->format('Y-m-d')) - time();
        return (int) round($selisih_detik / 86400);
    }

    /**
     * Tentukan label status stok berdasarkan jumlah kuantitas faskes.
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
     * Accessor: Generate kode prefix berdasarkan jenis tipe obat.
     */
    public function getKodeAttribute(): string
    {
        $prefix = ($this->jenis_obat === 'Vaksin') ? 'VAK-' : 'OBT-';
        return $prefix . str_pad($this->id_obat, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung Safety Stock dan Reorder Point (ROP) secara dinamis
     * berdasarkan jumlah kasus penyakit aktif dari modul Pelaporan (Server B).
     */
    public static function hitungRekomendasiStokOtomatis(int $id_obat, string $nama_obat, int $jumlah_kasus_terkait): array
    {
        $stok_min_dasar    = 20;
        $lead_time         = 3;   // hari
        $avg_demand_harian = 5;   // unit/hari

        // Safety stock otomatis naik 20% per tiap kasus aktif yang dilaporkan faskes cabang
        $safety_stock = (int) ceil($stok_min_dasar * (1 + ($jumlah_kasus_terkait * 0.2)));

        // Perhitungan rumus Reorder Point (ROP)
        $rop = ($avg_demand_harian * $lead_time) + $safety_stock;

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