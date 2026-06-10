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

        $role = \Illuminate\Support\Facades\Auth::user()->role;

        // ── Siapkan data berdasarkan tab aktif ───────────────────────────
        $data = [
            'tab' => $tab,
            'role' => $role,
            'chartData' => $this->getChartData(),
        ];

        match ($tab) {
            'overview'  => $data = array_merge($data, $this->getOverviewData()),
            'simosoba'  => $data = array_merge($data, $this->getSimosobaData()),
            'pelaporan' => $data = array_merge($data, $this->getPelaporanData()),
            'imunisasi' => $data = array_merge($data, $this->getImunisasiData()),
        };

        // ── Tambahkan data berdasarkan Role ──────────────────────────────
        if ($role === 'Dokter') {
            $data['waspada_epidemi'] = $this->getWaspadaEpidemiData();
        } elseif ($role === 'Apoteker') {
            $data['restock_urgency'] = $this->getRestockUrgencyData();
        } elseif ($role === 'Petugas Imunisasi') {
            $data['vaksin_drop'] = $this->getVaksinDropData();
        }

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

    private function getWaspadaEpidemiData()
    {
        return PelaporanPenyakit::select('wilayah', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('wilayah')
            ->having('total', '>=', 3)
            ->orderByDesc('total')
            ->get();
    }

    private function getRestockUrgencyData()
    {
        $obatKritis = Obat::where('stok', '<', 50)->get();
        return $obatKritis->map(function($obat) {
            $obat->urgency_score = max(0, 100 - ($obat->stok * 2)); // Calculate urgency
            return $obat;
        })->sortByDesc('urgency_score');
    }

    private function getVaksinDropData()
    {
        return Imunisasi::where('status_reminder', 'Belum Dikirim')
            ->where('usia_bulan', '>=', 9)
            ->orderBy('usia_bulan', 'desc')
            ->get();
    }

    private function getChartData()
    {
        return [
            'multi_axis' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'cases' => [12, 19, 25, 30, 45, 50],
                'stock' => [100, 90, 75, 50, 30, 10],
            ],
            'stacked_bar' => [
                'labels' => ['Manguharjo', 'Taman', 'Kartoharjo'],
                'population' => [500, 700, 600],
                'immunized' => [450, 680, 500],
            ],
            'top_diseases' => PelaporanPenyakit::select('jenis_penyakit', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
                ->groupBy('jenis_penyakit')
                ->orderByDesc('total')
                ->limit(5)
                ->pluck('total', 'jenis_penyakit')
                ->toArray(),
        ];
    }
}
