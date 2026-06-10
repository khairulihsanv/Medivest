<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Imunisasi — Tabel 'imunisasi' di database db_kesehatan_terpadu
 *
 * Migrasi dari class Imunisasi di PHP native.
 * Kolom: id_imunisasi, nama_anak, nama_orang_tua, usia_bulan,
 *        jenis_vaksin, dosis_ke, tgl_jadwal, no_hp, status_reminder
 */
class Imunisasi extends Model
{
    use SoftDeletes;

    // ─── KONFIGURASI TABEL ─────────────────────────────────────────────

    protected $table = 'imunisasi';
    protected $primaryKey = 'id_imunisasi';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // ─── MASS ASSIGNMENT PROTECTION ────────────────────────────────────

    protected $fillable = [
        'nama_anak',
        'nama_orang_tua',
        'usia_bulan',
        'jenis_vaksin',
        'dosis_ke',
        'tgl_jadwal',
        'no_hp',
        'status_reminder',
    ];

    // ─── ATTRIBUTE CASTING ─────────────────────────────────────────────

    protected $casts = [
        'usia_bulan' => 'integer',
        'dosis_ke'   => 'integer',
        'tgl_jadwal' => 'date',
    ];

    // ─── HELPER METHODS ────────────────────────────────────────────────

    /**
     * Normalisasi nomor HP:
     *   - Strip semua karakter non-digit
     *   - Konversi awalan '0' menjadi '62' (format internasional WhatsApp)
     */
    public static function normalizeNoHp(string $no_hp): string
    {
        $no_hp = preg_replace('/[^0-9]/', '', $no_hp);

        if (str_starts_with($no_hp, '0')) {
            $no_hp = '62' . substr($no_hp, 1);
        }

        return $no_hp;
    }

    /**
     * Generate WhatsApp deep-link URL dengan pesan pre-filled.
     */
    public function getWhatsappUrlAttribute(): string
    {
        $pesan = "Halo {$this->nama_orang_tua}, mengingatkan jadwal imunisasi untuk"
            . " {$this->nama_anak} (Usia {$this->usia_bulan} bulan)"
            . " untuk Vaksin {$this->jenis_vaksin} Dosis ke-{$this->dosis_ke}"
            . " pada tanggal " . date('d-m-Y', strtotime($this->tgl_jadwal))
            . ". Mohon datang tepat waktu ke Medivest.";

        return "https://api.whatsapp.com/send?phone=" . urlencode($this->no_hp)
            . "&text=" . urlencode($pesan);
    }

    /**
     * Tentukan status badge styling.
     *
     * @return array{badge:string, dot:string}
     */
    public function getStatusBadgeAttribute(): array
    {
        $is_sent = ($this->status_reminder ?? '') !== 'Belum Dikirim';

        return [
            'badge' => $is_sent
                ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
                : 'bg-amber-50 text-amber-700 border-amber-200/60',
            'dot' => $is_sent
                ? 'bg-emerald-500'
                : 'bg-amber-500 pulse-dot',
        ];
    }

    /**
     * Cari anak yang belum diingatkan vaksinasi terkait wabah penyakit aktif.
     * Query lintas-modul untuk prioritisasi reminder.
     */
    public static function dapatkanTargetTerancamWabah(string $wilayah_wabah, string $jenis_vaksin_terkait)
    {
        return self::select('nama_anak', 'nama_orang_tua', 'no_hp')
            ->where('status_reminder', 'Belum Dikirim')
            ->where('jenis_vaksin', $jenis_vaksin_terkait)
            ->get();
    }
}
