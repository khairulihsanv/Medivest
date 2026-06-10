<?php

namespace App\Http\Controllers;

use App\Models\Imunisasi;
use Illuminate\Http\Request;

/**
 * ImunisasiController — Antrian Imunisasi & Reminder WhatsApp
 *
 * Migrasi dari dashboard.php Section 8 (POST handler aksi_tambah_antrean).
 */
class ImunisasiController extends Controller
{
    /**
     * Simpan data antrean imunisasi baru.
     *
     * Route: POST /imunisasi
     */
    public function store(Request $request)
    {
        // ── Validasi input ────────────────────────────────────────────
        $validated = $request->validate([
            'nama_anak'      => 'required|string|max:255',
            'nama_orang_tua' => 'required|string|max:255',
            'no_hp'          => 'required|string|regex:/^[0-9]+$/',
            'usia_bulan'     => 'required|integer|min:0',
            'jenis_vaksin'   => 'required|string|in:BCG,DPT-HB-Hib,Campak-Rubella,Polio,Hepatitis B',
            'dosis_ke'       => 'required|integer|min:1|max:4',
            'tgl_jadwal'     => 'required|date',
        ], [
            'nama_anak.required'      => 'Nama anak wajib diisi!',
            'nama_orang_tua.required' => 'Nama orang tua wajib diisi!',
            'no_hp.required'          => 'No. HP wajib diisi!',
            'no_hp.regex'             => 'Nomor HP wajib berisi angka saja (tanpa spasi atau simbol +).',
            'usia_bulan.min'          => 'Input usia bulan tidak valid!',
            'dosis_ke.min'            => 'Input dosis ke tidak valid!',
        ]);

        // ── Normalisasi nomor HP (0 → 62) ────────────────────────────
        $validated['no_hp'] = Imunisasi::normalizeNoHp($validated['no_hp']);

        // ── Set status default ───────────────────────────────────────
        $validated['status_reminder'] = 'Belum Dikirim';

        // ── Simpan ke database via Eloquent ──────────────────────────
        Imunisasi::create($validated);

        return redirect('/dashboard?tab=imunisasi')
            ->with('status', 'sukses');
    }
}
