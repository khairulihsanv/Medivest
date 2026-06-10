<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\PelaporanPenyakit;
use App\Models\Imunisasi;
use Illuminate\Support\Facades\DB;

/**
 * LandingController — Halaman publik dengan data real-time
 *
 * Migrasi dari index.php (PHP native).
 * Mengambil metrik agregat dan data Chart.js dari database
 * untuk portal surveilans publik.
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
     */
    public function index()
    {
        // ── 1. METRICS via Eloquent ───────────────────────────────────────
        $totalObat  = Obat::count();
        $totalKasus = PelaporanPenyakit::count();

        // ── 2. CHART DATA: Sebaran Penyakit (Bar Chart) ──────────────────
        $dataPenyakit = PelaporanPenyakit::hitungKasusPerJenis();

        $labelPenyakit  = $dataPenyakit->pluck('jenis_penyakit')->toArray();
        $jumlahPenyakit = $dataPenyakit->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // ── 3. CHART DATA: Cakupan Imunisasi (Doughnut Chart) ────────────
        $dataVaksin = Imunisasi::select('jenis_vaksin', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_vaksin')
            ->orderByDesc('total')
            ->get();

        $labelVaksin  = $dataVaksin->pluck('jenis_vaksin')
            ->map(fn($v) => $v . ' (Anak)')
            ->toArray();
        $jumlahVaksin = $dataVaksin->pluck('total')->map(fn($v) => (int)$v)->toArray();

        return view('landing', compact(
            'totalObat',
            'totalKasus',
            'labelPenyakit',
            'jumlahPenyakit',
            'labelVaksin',
            'jumlahVaksin'
        ));
    }
}
