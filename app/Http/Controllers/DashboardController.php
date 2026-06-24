<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PelaporanPenyakit;
use App\Models\PelaporanPenyakitKlinik;
use App\Models\Imunisasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * DashboardController — Tab-based Dashboard Router
 *
 * [ARSITEKTUR TERDISTRIBUSI]
 * Controller ini mengquery data dari DUA server secara bersamaan,
 * menggabungkan hasilnya di PHP layer (bukan JOIN cross-server — tidak mungkin di MySQL/Eloquent),
 * lalu meneruskan data gabungan ke view.
 *
 *   Server A (mysql_pusat)  → PelaporanPenyakit        (Manguharjo, Kartoharjo)
 *   Server B (mysql_klinik) → PelaporanPenyakitKlinik  (Taman)
 *
 * Migrasi dari dashboard.php (PHP native) Sections 4–9.
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
            'tab'  => $tab,
            'role' => $role,
        ];

        // Chart hanya dimuat di tab overview
        if ($tab === 'overview') {
            $data['chartData'] = $this->getChartData();
        }

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
     *
     * [ARSITEKTUR TERDISTRIBUSI] totalKasus = jumlah dari Server A + Server B.
     */
    private function getOverviewData(): array
    {
        $totalObat  = Obat::count();
        $stokKritis = Obat::where('stok', '<=', 10)->count();

        // [ARSITEKTUR TERDISTRIBUSI] Hitung kasus dari kedua server lalu jumlahkan
        $totalKasus   = PelaporanPenyakit::count() + PelaporanPenyakitKlinik::count();
        $totalAntrean = Imunisasi::where('status_reminder', 'Belum Dikirim')->count();

        return compact('totalObat', 'stokKritis', 'totalKasus', 'totalAntrean');
    }

    /**
     * SiMoSoBa data — daftar obat + analisis prediktif.
     *
     * [ARSITEKTUR TERDISTRIBUSI] hitungKasusByJenis dijumlahkan dari kedua server.
     */
    private function getSimosobaData(): array
    {
        $dataObat = Obat::orderByDesc('id_obat')->get();

        // [ARSITEKTUR TERDISTRIBUSI] Ambil jumlah kasus DBD dari KEDUA server lalu jumlahkan
        $kasus_dbd = PelaporanPenyakit::hitungKasusByJenis('Demam Berdarah Dengue')
                   + PelaporanPenyakitKlinik::hitungKasusByJenis('Demam Berdarah Dengue');

        // Tentukan obat rujukan untuk analisis prediktif
        $id_obat_uji   = $dataObat->isNotEmpty() ? (int)    $dataObat->first()->id_obat     : 0;
        $nama_obat_uji = $dataObat->isNotEmpty() ? (string) $dataObat->first()->nama_obat   : 'Obat Simulasi';

        // Hitung Safety Stock & ROP
        $analisis_stok = Obat::hitungRekomendasiStokOtomatis($id_obat_uji, $nama_obat_uji, $kasus_dbd);

        return compact('dataObat', 'kasus_dbd', 'analisis_stok');
    }

    /**
     * Pelaporan data — semua laporan penyakit dari kedua server.
     *
     * [ARSITEKTUR TERDISTRIBUSI]
     * Query ke Server A dan Server B dilakukan terpisah, lalu di-concat()
     * menjadi satu Collection di PHP layer.
     * Sort dilakukan di PHP (bukan SQL) karena JOIN cross-server tidak mungkin.
     */
    private function getPelaporanData(): array
    {
        // Query Server A (Manguharjo, Kartoharjo)
        $laporanServerA = PelaporanPenyakit::orderByDesc('id_laporan')->get();

        // Query Server B (Taman)
        $laporanServerB = PelaporanPenyakitKlinik::orderByDesc('id_laporan')->get();

        // Gabungkan & sort ulang berdasarkan tgl_diagnosis (terbaru dulu)
        // [ARSITEKTUR TERDISTRIBUSI] concat() menggabungkan dua Collection Eloquent
        $dataLaporan = $laporanServerA->concat($laporanServerB)
            ->sortByDesc('id_laporan')
            ->values();

        // [BUKTI TERDISTRIBUSI] Jumlah per server — ditampilkan di blok pembuktian UAS
        $kasusServerA  = $laporanServerA->count();
        $kasusServerB  = $laporanServerB->count();
        $hostServerB   = env('DB_HOST_KLINIK', '127.0.0.1');

        return compact('dataLaporan', 'kasusServerA', 'kasusServerB', 'hostServerB');
    }

    /**
     * Imunisasi data — antrian pasien.
     */
    private function getImunisasiData(): array
    {
        $dataAntrean = Imunisasi::orderBy('tgl_jadwal', 'asc')->get();

        return compact('dataAntrean');
    }

    /**
     * Waspada Epidemi — wilayah dengan kasus tinggi (>= 3).
     *
     * [ARSITEKTUR TERDISTRIBUSI] Data digabung dari kedua server,
     * lalu di-group & filter di PHP layer.
     */
    private function getWaspadaEpidemiData()
    {
        // Ambil semua data dari kedua server
        $semuaLaporan = PelaporanPenyakit::select('wilayah')->get()
            ->concat(PelaporanPenyakitKlinik::select('wilayah')->get());

        // Group by wilayah di PHP, filter yang >= 3 kasus, urutkan terbanyak
        return $semuaLaporan
            ->groupBy('wilayah')
            ->map(fn($grup) => (object)['wilayah' => $grup->first()->wilayah, 'total' => $grup->count()])
            ->filter(fn($item) => $item->total >= 3)
            ->sortByDesc('total')
            ->values();
    }

    private function getRestockUrgencyData()
    {
        $obatKritis = Obat::where('stok', '<', 50)->get();
        return $obatKritis->map(function ($obat) {
            $obat->urgency_score = max(0, 100 - ($obat->stok * 2));
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

    /**
     * Chart data — termasuk top diseases dari kedua server.
     *
     * [ARSITEKTUR TERDISTRIBUSI] top_diseases digabungkan dari kedua server
     * lalu di-merge & sort di PHP layer.
     */
    private function getChartData()
    {
        // Ambil top diseases dari Server A
        $diseasesA = PelaporanPenyakit::select('jenis_penyakit', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_penyakit')
            ->pluck('total', 'jenis_penyakit')
            ->toArray();

        // Ambil top diseases dari Server B
        $diseasesB = PelaporanPenyakitKlinik::select('jenis_penyakit', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_penyakit')
            ->pluck('total', 'jenis_penyakit')
            ->toArray();

        // Gabungkan: jika penyakit yang sama ada di keduanya, jumlahkan totalnya
        $merged = array_merge($diseasesA, []);
        foreach ($diseasesB as $jenis => $total) {
            $merged[$jenis] = ($merged[$jenis] ?? 0) + $total;
        }
        arsort($merged);
        $topDiseases = array_slice($merged, 0, 5, true);

        return [
            'multi_axis' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'cases'  => [12, 19, 25, 30, 45, 50],
                'stock'  => [100, 90, 75, 50, 30, 10],
            ],
            'stacked_bar' => [
                'labels'      => ['Manguharjo', 'Taman', 'Kartoharjo'],
                'population'  => [500, 700, 600],
                'immunized'   => [450, 680, 500],
            ],
            'top_diseases' => $topDiseases,
        ];
    }
}
