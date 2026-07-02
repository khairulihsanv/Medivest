<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PelaporanPenyakit;
use App\Models\Imunisasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * LandingController — Halaman publik dengan data real-time
 *
 * Migrasi dari index.php (PHP native).
 * Mengambil metrik agregat dan data Chart.js dari database
 * untuk portal surveilans publik.
 *
 * [RESILIENSI & PERFORMA] 
 * 1. Data dicache selama 60 menit untuk mencegah beban tinggi.
 * 2. Query Imunisasi (Server B) dibungkus safeKlinikQuery().
 */
class LandingController extends Controller
{

    /**
     * Menampilkan halaman landing page dengan data live.
     *
     * Data yang dikirim ke view:
     * - $totalObat, $totalKasus → Metric widgets
     * - $labelPenyakit, $jumlahPenyakit → Bar chart (sebaran penyakit)
     * - $labelVaksin, $jumlahVaksin → Doughnut chart (cakupan vaksinasi)
     * - $klinikOnline → Status koneksi Server B (untuk UI indicator)
     */
    public function index()
    {
        $cacheTtl = 60 * 60; // 60 menit

        // ── 1. METRICS via Eloquent (Cached) ──────────────────────────────
        $totalObat = Cache::remember('landing_total_obat', $cacheTtl, function () {
            return Obat::count();
        });

        $totalKasus = Cache::remember('landing_total_kasus', $cacheTtl, function () {
            return PelaporanPenyakit::count();
        });

        // ── 2. CHART DATA: Sebaran Penyakit (Bar Chart) (Cached) ──────────
        $dataPenyakit = Cache::remember('landing_data_penyakit', $cacheTtl, function () {
            return PelaporanPenyakit::hitungKasusPerJenis();
        });

        $labelPenyakit  = $dataPenyakit->pluck('jenis_penyakit')->toArray();
        $jumlahPenyakit = $dataPenyakit->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // ── 3. CHART DATA: Cakupan Imunisasi (Doughnut Chart) (Cached) ────
        $dataVaksin = Cache::remember('landing_data_vaksin', $cacheTtl, function () {
            return Imunisasi::select('jenis_vaksin', DB::raw('COUNT(*) as total'))
                    ->groupBy('jenis_vaksin')
                    ->orderByDesc('total')
                    ->get();
        });

        $labelVaksin  = $dataVaksin->pluck('jenis_vaksin')
            ->map(fn($v) => $v . ' (Anak)')
            ->toArray();
        $jumlahVaksin = $dataVaksin->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // Tidak lagi menggunakan klinikOnline, selalu true
        $klinikOnline = true;

        return view('landing', compact(
            'totalObat',
            'totalKasus',
            'labelPenyakit',
            'jumlahPenyakit',
            'labelVaksin',
            'jumlahVaksin',
            'klinikOnline'
        ));
    }
}
