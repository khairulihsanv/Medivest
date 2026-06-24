<?php

namespace App\Http\Controllers;

use App\Models\PelaporanPenyakit;
use App\Models\PelaporanPenyakitKlinik;
use Illuminate\Http\Request;

/**
 * PelaporanController — Laporan Kasus Penyakit
 *
 * [ARSITEKTUR TERDISTRIBUSI] Controller ini bertanggung jawab
 * merutekan data ke fragmen database yang tepat berdasarkan wilayah.
 *
 * Migrasi dari dashboard.php Section 7 (POST handler aksi_tambah_laporan).
 */
class PelaporanController extends Controller
{
    /**
     * [ARSITEKTUR TERDISTRIBUSI] Mapping wilayah ke server penyimpanan.
     *
     * WILAYAH_PUSAT  → disimpan di PelaporanPenyakit       (mysql_pusat  / Server A)
     * WILAYAH_KLINIK → disimpan di PelaporanPenyakitKlinik (mysql_klinik / Server B)
     *
     * Untuk menambah wilayah baru:
     *   - Tambahkan nama wilayah ke array yang sesuai di bawah.
     *   - Tidak ada perubahan lain yang diperlukan.
     */
    private const WILAYAH_PUSAT = [
        'Manguharjo',
        'Kartoharjo',
    ];

    private const WILAYAH_KLINIK = [
        'Taman',
    ];

    /**
     * Simpan laporan kasus penyakit baru.
     * Data dirutekan ke Server A atau Server B berdasarkan wilayah.
     *
     * Route: POST /pelaporan
     */
    public function store(Request $request)
    {
        // ── Validasi input ────────────────────────────────────────────
        $allWilayah = array_merge(self::WILAYAH_PUSAT, self::WILAYAH_KLINIK);

        $validated = $request->validate([
            'nama_pasien'       => 'required|string|max:255',
            'nik'               => 'required|string|regex:/^[0-9]+$/',
            'jenis_penyakit'    => 'required|string|max:255',
            'tgl_diagnosis'     => 'required|date|before_or_equal:today',
            'wilayah'           => 'required|string|in:' . implode(',', $allWilayah),
            'tingkat_keparahan' => 'required|string|in:Ringan,Sedang,Berat,Kritis',
            'catatan_klinis'    => 'nullable|string|max:1000',
        ], [
            'nama_pasien.required'          => 'Nama pasien wajib diisi!',
            'nik.required'                  => 'NIK wajib diisi!',
            'nik.regex'                     => 'Format NIK tidak valid! Wajib berupa angka murni.',
            'jenis_penyakit.required'       => 'Jenis penyakit wajib dipilih!',
            'wilayah.required'              => 'Lokasi/wilayah wajib dipilih!',
            'wilayah.in'                    => 'Wilayah yang dipilih tidak valid.',
            'tgl_diagnosis.before_or_equal' => 'Tanggal diagnosis tidak boleh melebihi hari ini.',
        ]);

        // ── [ARSITEKTUR TERDISTRIBUSI] Routing ke fragmen yang tepat ──
        // Tentukan model berdasarkan wilayah, lalu simpan ke server yang sesuai.
        if (in_array($validated['wilayah'], self::WILAYAH_KLINIK, true)) {
            // Wilayah Taman → Server B (mysql_klinik / db_medivest_klinik)
            PelaporanPenyakitKlinik::create($validated);
        } else {
            // Wilayah Manguharjo, Kartoharjo → Server A (mysql_pusat / db_medivest_pusat)
            // Fallback: wilayah tidak dikenal juga masuk ke Server A
            PelaporanPenyakit::create($validated);
        }

        return redirect('/dashboard?tab=pelaporan')
            ->with('status', 'sukses');
    }
}
