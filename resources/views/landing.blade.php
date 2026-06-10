{{-- 
    Landing Page — Medivest Health Logistics & Monitoring System

    Halaman publik utama yang menampilkan informasi tentang Medivest:
    hero section, statistik widget, fitur 3 modul klaster kesehatan,
    dan footer branding universitas.
    
    Dimigrasi dari: /Medivest/index.php (native PHP)
--}}

@extends('layouts.app')

@section('title', 'Portal Pemantauan & Pencegahan Penyakit Terpadu')
@section('meta_description', 'Medivest — Portal pemantauan dan pencegahan penyakit serta logistik medis terpadu untuk Sub-klaster UNS PSDKU Madiun.')

@section('content')

    {{-- ─── NAVIGATION ──────────────────────────────────────────────── --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80 px-6 lg:px-16 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg shadow-blue-500/20">M</div>
            <span class="font-bold text-xl text-slate-900 tracking-tight">Medivest</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('register') }}"
               class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 border border-slate-200 
                      text-slate-700 text-sm font-semibold rounded-xl 
                      transition-all hover:border-slate-300 hover:bg-slate-50">
                Daftar Akun
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700
                      text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20
                      transition-all transform active:scale-[0.98] hover:-translate-y-0.5">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Portal Tim Medis</span>
            </a>
        </div>
    </nav>

    {{-- ─── HERO SECTION ────────────────────────────────────────────── --}}
    <header class="hero-gradient relative px-6 lg:px-16 py-20 lg:py-28 flex flex-col items-center text-center max-w-4xl mx-auto space-y-6 fade-in">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-semibold">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            Sub-klaster Resmi UNS PSDKU Madiun
        </span>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
            Sistem Informasi <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Satu Pintu</span><br>
            Pantau Penyakit &amp; Logistik Medis
        </h1>

        <p class="text-slate-500 text-base sm:text-lg max-w-2xl">
            Medivest mempercepat koordinasi tanggap darurat wabah, transparansi stok obat kritis,
            serta otomasi pengingat imunisasi anak berbasis web lokal terpadu.
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 bg-blue-600 hover:bg-blue-700
                      text-white font-semibold rounded-2xl shadow-xl shadow-blue-500/25
                      transition-all hover:-translate-y-0.5 text-sm">
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                Masuk ke Dashboard
            </a>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 border border-slate-200
                      text-slate-700 font-semibold rounded-2xl 
                      transition-all hover:border-slate-300 hover:bg-white text-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                Daftarkan Anggota
            </a>
        </div>
    </header>

    {{-- ─── LIVE METRIC WIDGETS & CHARTS ───────────────────────────── --}}
    <section class="max-w-6xl mx-auto px-6 mb-24 fade-in" style="animation-delay: 0.15s">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-8 shadow-xl border border-slate-700 text-white relative overflow-hidden mb-8">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(37,99,235,0.12),transparent)]"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="relative space-y-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Obat & Alkes</p>
                    <p class="text-4xl font-black text-amber-400">{{ number_format($totalObat, 0, ',', '.') }} <span class="text-sm font-normal text-slate-300">Item</span></p>
                </div>
                <div class="relative space-y-1 md:border-x md:border-slate-700">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kasus Penyakit Tercatat</p>
                    <p class="text-4xl font-black text-blue-400">{{ number_format($totalKasus, 0, ',', '.') }} <span class="text-sm font-normal text-slate-300">Laporan</span></p>
                </div>
                <div class="relative space-y-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Konektivitas Sistem</p>
                    <p class="text-lg font-bold text-emerald-400 inline-flex items-center gap-1.5 pt-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> MySQL Connected
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-center h-80">
                <p class="text-sm font-bold text-slate-700 mb-2">Peta Persebaran Penyakit Menular</p>
                <div class="h-64 w-full relative">
                    <canvas id="penyakitChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm text-center h-80">
                <p class="text-sm font-bold text-slate-700 mb-2">Cakupan Imunisasi Pasien Anak</p>
                <div class="h-64 w-full relative flex justify-center">
                    <canvas id="vaksinChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── THREE MODULE CARDS ──────────────────────────────────────── --}}
    <section class="max-w-6xl mx-auto px-6 mb-24 space-y-12 fade-in" style="animation-delay: 0.3s">
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Tiga Klaster Proteksi Kesehatan</h2>
            <p class="text-sm text-slate-500">Arsitektur fungsional modular yang saling terintegrasi secara real-time.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card 1: SiMoSoBa --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 flex items-center justify-center rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-900">SiMoSoBa</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Sistem Monitoring Stok Barang. Memantau ketersediaan logistik obat esensial dan
                    mendeteksi dini masa kedaluwarsa secara otomatis.
                </p>
            </div>

            {{-- Card 2: Pelaporan Penyakit --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 flex items-center justify-center rounded-xl group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-900">Pelaporan Penyakit</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Mencatat epidemiologi persebaran penyakit menular di wilayah klaster untuk
                    mempercepat keputusan isolasi dari tim medis.
                </p>
            </div>

            {{-- Card 3: Reminder Imunisasi --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 flex items-center justify-center rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <i data-lucide="message-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-900">Reminder Imunisasi</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Otomasi pengiriman pesan pengingat jadwal wajib imunisasi bayi terintegrasi
                    langsung ke WhatsApp Web resmi orang tua.
                </p>
            </div>
        </div>
    </section>

    {{-- ─── TECH STACK SECTION ──────────────────────────────────────── --}}
    <section class="max-w-4xl mx-auto px-6 mb-24 fade-in" style="animation-delay: 0.45s">
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-3xl p-8 md:p-12 border border-blue-100 text-center space-y-6">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-sm border border-blue-100">
                <i data-lucide="code-2" class="w-7 h-7 text-blue-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Dibangun dengan Arsitektur Modern</h2>
            <p class="text-sm text-slate-600 max-w-xl mx-auto">
                Dimigrasi dari native PHP ke <strong>Laravel Framework</strong> dengan pola desain 
                <strong>Model-View-Controller (MVC)</strong>, dilengkapi autentikasi berbasis 
                <strong>Bcrypt hashing</strong> dan <strong>session management</strong> terenkripsi.
            </p>
            <div class="flex flex-wrap justify-center gap-3 text-xs font-semibold">
                <span class="px-3 py-1.5 bg-white text-blue-700 rounded-full border border-blue-200">Laravel 12</span>
                <span class="px-3 py-1.5 bg-white text-cyan-700 rounded-full border border-cyan-200">Tailwind CSS</span>
                <span class="px-3 py-1.5 bg-white text-purple-700 rounded-full border border-purple-200">Blade Templating</span>
                <span class="px-3 py-1.5 bg-white text-amber-700 rounded-full border border-amber-200">MySQL</span>
                <span class="px-3 py-1.5 bg-white text-emerald-700 rounded-full border border-emerald-200">Bcrypt Auth</span>
            </div>
        </div>
    </section>

    {{-- ─── FOOTER ──────────────────────────────────────────────────── --}}
    <footer class="border-t border-slate-200 bg-white py-8 text-center text-xs text-slate-400 space-y-1">
        <p>&copy; {{ date('Y') }} Medivest — Sub-klaster Pemantauan &amp; Pencegahan Penyakit. All rights reserved.</p>
        <p>Informatics Engineering — Sebelas Maret University (UNS) PSDKU Madiun</p>
    </footer>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Chart.js Default Config
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // ─── Peta Persebaran Penyakit (Bar Chart) ───
        const ctxPenyakit = document.getElementById('penyakitChart').getContext('2d');
        new Chart(ctxPenyakit, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelPenyakit) !!},
                datasets: [{
                    label: 'Jumlah Kasus',
                    data: {!! json_encode($jumlahPenyakit) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });

        // ─── Cakupan Imunisasi (Doughnut Chart) ───
        const ctxVaksin = document.getElementById('vaksinChart').getContext('2d');
        new Chart(ctxVaksin, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($labelVaksin) !!},
                datasets: [{
                    data: {!! json_encode($jumlahVaksin) !!},
                    backgroundColor: [
                        '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, usePointStyle: true, padding: 20 }
                    }
                }
            }
        });
    });
</script>
@endpush
