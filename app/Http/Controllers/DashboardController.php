<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PelaporanPenyakit;
use App\Models\Imunisasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * DashboardController — Route-based Dashboard Pages
 *
 * Each page has its own method and Blade view.
 * Data is scoped per-page to avoid unnecessary DB queries.
 */
class DashboardController extends Controller
{
    /**
     * Dashboard Overview — accessible by all roles.
     *
     * Route: GET /dashboard
     */
    public function index(Request $request)
    {
        // Backwards compatibility: redirect old ?tab= URLs
        $tab = $request->query('tab');
        if ($tab && $tab !== 'overview') {
            $tabMap = [
                'simosoba'  => 'dashboard.obat',
                'pelaporan' => 'dashboard.penyakit',
                'imunisasi' => 'dashboard.imunisasi',
            ];
            if (isset($tabMap[$tab])) {
                return redirect()->route($tabMap[$tab]);
            }
        }

        $totalObat    = Obat::count();
        $stokKritis   = Obat::where('stok', '<=', 10)->count();
        $totalKasus   = PelaporanPenyakit::count();
        $totalAntrean = Imunisasi::where('status_reminder', 'Belum Dikirim')->count();

        // Bento grid data
        $topDiseases = PelaporanPenyakit::select('jenis_penyakit', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_penyakit')
            ->orderByDesc('total')
            ->take(5)
            ->pluck('total', 'jenis_penyakit')
            ->toArray();

        $topWilayah = PelaporanPenyakit::select('wilayah', DB::raw('COUNT(*) as total'))
            ->groupBy('wilayah')
            ->orderByDesc('total')
            ->take(5)
            ->pluck('total', 'wilayah')
            ->toArray();

        $stokKritisItems = Obat::orderBy('stok', 'asc')->take(5)->get();

        // Today's immunization schedule
        $todaySchedule = Imunisasi::whereDate('tgl_jadwal', today())->get();

        return view('dashboard.overview', compact(
            'totalObat', 'stokKritis', 'totalKasus', 'totalAntrean',
            'topDiseases', 'topWilayah', 'stokKritisItems', 'todaySchedule'
        ));
    }

    /**
     * Monitoring Stok Obat — primary for Farmasi role.
     *
     * Route: GET /dashboard/obat
     */
    public function obat()
    {
        $dataObat = Obat::orderByDesc('id_obat')->get();

        $kasus_dbd = PelaporanPenyakit::hitungKasusByJenis('Demam Berdarah Dengue');

        // Predictive demand analysis
        $id_obat_uji   = $dataObat->isNotEmpty() ? (int) $dataObat->first()->id_obat : 0;
        $nama_obat_uji = $dataObat->isNotEmpty() ? (string) $dataObat->first()->nama_obat : 'Obat Simulasi';
        $analisis_stok  = Obat::hitungRekomendasiStokOtomatis($id_obat_uji, $nama_obat_uji, $kasus_dbd);

        // Disease count per type for predictive demand alert per-row
        $diseaseCounts = PelaporanPenyakit::select('jenis_penyakit', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_penyakit')
            ->pluck('total', 'jenis_penyakit')
            ->toArray();

        return view('dashboard.simosoba', compact('dataObat', 'kasus_dbd', 'analisis_stok', 'diseaseCounts'));
    }

    /**
     * Pelaporan Penyakit — primary for Tenaga Medis role.
     *
     * Route: GET /dashboard/penyakit
     */
    public function penyakit()
    {
        $dataLaporan = PelaporanPenyakit::orderByDesc('id_laporan')->get();

        // Indonesian Region API data (server-side fetch) untuk Kabupaten Madiun (3519)
        $apiWilayah = [];
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->get('https://emsifa.github.io/api-wilayah-indonesia/api/districts/3519.json');
            if ($response->successful()) {
                $apiWilayah = $response->json();
            }
        } catch (\Exception $e) {
            // Silently fallback if API is down
        }

        // Outbreak detection: regions with >= 3 entries
        $outbreakAlerts = PelaporanPenyakit::select('wilayah', DB::raw('COUNT(*) as total'))
            ->groupBy('wilayah')
            ->having('total', '>=', 3)
            ->orderByDesc('total')
            ->get();

        return view('dashboard.pelaporan', compact('dataLaporan', 'apiWilayah', 'outbreakAlerts'));
    }

    /**
     * Imunisasi — primary for Bidan role.
     *
     * Route: GET /dashboard/imunisasi
     */
    public function imunisasi()
    {
        $dataAntrean = Imunisasi::orderBy('tgl_jadwal', 'asc')->get();

        return view('dashboard.imunisasi', compact('dataAntrean'));
    }

    /**
     * Manajemen User — Staf Admin only.
     *
     * Route: GET /dashboard/users
     */
    public function users()
    {
        $allUsers = User::orderByDesc('id_user')->get();

        // Mock system status data
        $systemStatus = [
            'database'   => [
                'label'  => 'TiDB Cloud',
                'status' => 'Connected',
                'region' => 'AWS ap-southeast-1',
                'online' => true,
            ],
            'workflow'   => [
                'label'  => 'Workflow Engine',
                'status' => 'Healthy',
                'uptime' => '99.97%',
                'online' => true,
            ],
            'api'        => [
                'label'  => 'API Gateway',
                'status' => 'Operational',
                'latency'=> '42ms',
                'online' => true,
            ],
        ];

        return view('dashboard.users', compact('allUsers', 'systemStatus'));
    }
}
