<?php

namespace App\Http\Controllers;

use App\Models\PelaporanPenyakit;
use Illuminate\Http\Request;

/**
 * PelaporanController — Laporan Kasus Penyakit
 *
 * Migrasi dari dashboard.php Section 7 (POST handler aksi_tambah_laporan).
 */
class PelaporanController extends Controller
{
    /**
     * Simpan laporan kasus penyakit baru.
     *
     * Route: POST /pelaporan
     */
    public function store(Request $request)
    {
        // ── Validasi input ────────────────────────────────────────────
        $validated = $request->validate([
            'nama_pasien'       => 'required|string|max:255',
            'nik'               => 'required|string|regex:/^[0-9]+$/',
            'jenis_penyakit'    => 'required|string|max:255',
            'tgl_diagnosis'     => 'required|date|before_or_equal:today',
            'wilayah'           => 'required|string|max:255',
            'tingkat_keparahan' => 'required|string|in:Ringan,Sedang,Berat,Kritis',
            'catatan_klinis'    => 'nullable|string|max:1000',
        ], [
            'nama_pasien.required'       => 'Nama pasien wajib diisi!',
            'nik.required'               => 'NIK wajib diisi!',
            'nik.regex'                  => 'Format NIK tidak valid! Wajib berupa angka murni.',
            'jenis_penyakit.required'    => 'Jenis penyakit wajib dipilih!',
            'wilayah.required'           => 'Lokasi/wilayah wajib diisi!',
            'tgl_diagnosis.before_or_equal' => 'Tanggal diagnosis tidak boleh melebihi hari ini.',
        ]);

        // ── Simpan ke database via Eloquent ──────────────────────────
        PelaporanPenyakit::create($validated);

        return redirect('/dashboard?tab=pelaporan')
            ->with('status', 'sukses');
    }
}
