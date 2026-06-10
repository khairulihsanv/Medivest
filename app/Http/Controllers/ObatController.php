<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

/**
 * ObatController — CRUD Obat untuk modul SiMoSoBa
 *
 * Migrasi dari dashboard.php Section 6 (POST handlers).
 */
class ObatController extends Controller
{
    /**
     * Simpan obat baru ke database.
     *
     * Migrasi dari: if (isset($_POST['aksi_tambah_obat'])) { ... }
     * Route: POST /obat
     */
    public function store(Request $request)
    {
        // ── Validasi input ────────────────────────────────────────────
        $validated = $request->validate([
            'nama_obat'      => 'required|string|max:255',
            'jenis_obat'     => 'required|string|in:Tablet,Vaksin,Sirup,Infus,Kapsul,Alkes',
            'stok'           => 'required|integer|min:1',
            'harga_beli'     => 'required|numeric|min:0.01',
            'tgl_kadaluarsa' => 'required|date',
        ], [
            'nama_obat.required'      => 'Nama obat tidak boleh kosong!',
            'stok.min'                => 'Jumlah stok harus lebih dari 0.',
            'harga_beli.min'          => 'Harga beli harus lebih dari 0.',
            'tgl_kadaluarsa.required' => 'Tanggal kadaluarsa wajib diisi.',
        ]);

        // ── Hitung stok final (dari 0 + input) ──────────────────────
        $validated['stok'] = Obat::hitungStokMasuk(0, $validated['stok']);

        // ── Simpan ke database via Eloquent ──────────────────────────
        Obat::create($validated);

        return redirect('/dashboard?tab=simosoba')
            ->with('status', 'sukses');
    }

    /**
     * Hapus obat berdasarkan ID.
     *
     * Migrasi dari: if (isset($_POST['aksi_hapus_obat'])) { ... }
     * Route: DELETE /obat/{obat}
     */
    public function destroy(int $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect('/dashboard?tab=simosoba')
            ->with('status', 'hapussukses');
    }
}
