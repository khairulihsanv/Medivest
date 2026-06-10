<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PelaporanPenyakit;
use App\Models\Imunisasi;
use Illuminate\Http\Request;

/**
 * DashboardController — Tab-based Dashboard Router
 *
 * Migrasi dari dashboard.php (PHP native) Sections 4–9.
 * Menangani routing tab (overview, simosoba, pelaporan, imunisasi)
 * dan menyiapkan data yang diperlukan oleh masing-masing view partial.
 */
class DashboardController extends Controller
{
    /**
     * Tab yang diizinkan (whitelist untuk mencegah LFI).
     */
    private array $allowedTabs = ['overview', 'simosoba', 'pelaporan', 'imunisasi'];

    /**
     * Halaman utama dashboard — menampilkan tab aktif.
     *
     * Route: GET /dashboard?tab={tab}
     */
    public function index(Request $request)
    {
        // ── Tab routing dengan whitelist ──────────────────────────────────
        $tab = in_array($request->query('tab', ''), $this->allowedTabs, true)
            ? $request->query('tab')
            : 'overview';

        // ── Siapkan data berdasarkan tab aktif ───────────────────────────
        $data = ['tab' => $tab];

        match ($tab) {
            'overview'  => $data = array_merge($data, $this->getOverviewData()),
            'simosoba'  => $data = array_merge($data, $this->getSimosobaData()),
            'pelaporan' => $data = array_merge($data, $this->getPelaporanData()),
            'imunisasi' => $data = array_merge($data, $this->getImunisasiData()),
        };

        return view('dashboard', $data);
    }

    /**
     * Overview metrics — 4 kartu metrik utama.
     */
    private function getOverviewData(): array
    {
        $totalObat    = Obat::count();
        $stokKritis   = Obat::where('stok', '<=', 10)->count();
        $totalKasus   = PelaporanPenyakit::count();
        $totalAntrean = Imunisasi::where('status_reminder', 'Belum Dikirim')->count();

        return compact('totalObat', 'stokKritis', 'totalKasus', 'totalAntrean');
    }

    /**
     * SiMoSoBa data — daftar obat + analisis prediktif.
     */
    private function getSimosobaData(): array
    {
        $dataObat = Obat::orderByDesc('id_obat')->get();

        // DATA FEEDER: Ambil jumlah kasus DBD dari modul Pelaporan
        $kasus_dbd = PelaporanPenyakit::hitungKasusByJenis('Demam Berdarah Dengue');

        // Tentukan obat rujukan untuk analisis prediktif
        $id_obat_uji   = $dataObat->isNotEmpty() ? (int) $dataObat->first()->id_obat     : 0;
        $nama_obat_uji = $dataObat->isNotEmpty() ? (string) $dataObat->first()->nama_obat : 'Obat Simulasi';

        // Hitung Safety Stock & ROP
        $analisis_stok = Obat::hitungRekomendasiStokOtomatis($id_obat_uji, $nama_obat_uji, $kasus_dbd);

        return compact('dataObat', 'kasus_dbd', 'analisis_stok');
    }

    /**
     * Pelaporan data — semua laporan penyakit.
     */
    private function getPelaporanData(): array
    {
        $dataLaporan = PelaporanPenyakit::orderByDesc('id_laporan')->get();

        return compact('dataLaporan');
    }

    /**
     * Imunisasi data — antrian pasien.
     */
    private function getImunisasiData(): array
    {
        $dataAntrean = Imunisasi::orderBy('tgl_jadwal', 'asc')->get();

        return compact('dataAntrean');
    }
}
