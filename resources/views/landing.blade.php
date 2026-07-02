{{-- 
    Landing Page — Medivest VENTRILOC Design System
    Public-facing portal with live metrics, benefits, and news articles
--}}

@extends('layouts.app')

@section('title', 'Portal Pemantauan & Pencegahan Penyakit Terpadu')
@section('meta_description', 'Medivest — Portal pemantauan dan pencegahan penyakit serta logistik medis terpadu.')

@push('styles')
<style>
    @keyframes count-up {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .count-animate { animation: count-up 0.6s ease-out forwards; }
</style>
@endpush

@section('content')

    {{-- ─── NAVIGATION ────────────────────────────────────────────── --}}
    <nav class="fixed w-full z-50 top-0 bg-white/80 backdrop-blur-md" id="navbar">
        <div class="max-w-7xl mx-auto px-6 lg:px-16 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-signal rounded-lg flex items-center justify-center text-white font-display font-black text-lg">M</div>
                <span class="font-display font-bold text-xl text-ink tracking-tight">Medivest</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('register') }}" class="hidden sm:inline-block text-sm font-semibold text-muted hover:text-ink transition-colors">
                    Daftar Akun
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink hover:bg-ink/90 text-white text-sm font-bold rounded-full transition-colors">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Portal Medis</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- ─── HERO SECTION ──────────────────────────────────────────── --}}
    <header class="bg-white relative min-h-[88vh] flex flex-col items-center justify-center text-center px-6 pt-32 pb-20">
        <div class="relative z-10 max-w-4xl mx-auto space-y-6 flex flex-col items-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold text-signal bg-signal/10">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-signal opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-signal"></span>
                </span>
                Live Monitoring · {{ date('Y') }}
            </div>

            <h1 class="text-5xl md:text-6xl lg:text-7xl font-display font-black text-ink tracking-tight leading-[1.1]">
                Sistem Informasi <br/>
                Kesehatan <span class="text-signal">Terpadu</span>
            </h1>

            <p class="text-muted text-lg md:text-xl max-w-2xl font-medium leading-relaxed">
                Pantau penyakit & logistik medis dalam satu platform real-time. Mempercepat tanggap darurat wabah dengan arsitektur modern.
            </p>

            <div class="flex items-center gap-4 pt-4">
                <a href="#metrics" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-signal text-white font-bold rounded-full hover:bg-signal/90 transition-all">
                    Lihat Data Live
                    <i data-lucide="arrow-down" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-mist text-ink font-bold rounded-full hover:bg-mist/70 transition-all">
                    Masuk Portal
                </a>
            </div>
        </div>
    </header>

    {{-- ─── LIVE METRIC COUNTERS ──────────────────────────────────── --}}
    <section id="metrics" class="bg-mist py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14 space-y-2">
                <p class="text-xs font-bold text-signal uppercase tracking-[0.2em]">Real-time Data</p>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-ink tracking-tight">Live Metric Counter</h2>
                <p class="text-muted font-medium max-w-lg mx-auto">Data metrik agregat yang ditarik langsung dari database.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5" x-data="metricCounter()" x-init="startCounting()">
                {{-- Metric 1: Total Kasus --}}
                <div class="bg-white rounded-lg p-8 text-center">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="activity" class="w-6 h-6 text-red-500"></i>
                    </div>
                    <p class="text-4xl font-display font-black text-ink tracking-tight" x-text="animatedKasus">0</p>
                    <p class="text-sm font-semibold text-muted mt-2">Kasus Penyakit Terlaporkan</p>
                    <p class="text-xs text-muted/60 mt-1">Surveilans aktif seluruh wilayah</p>
                </div>

                {{-- Metric 2: Total Obat --}}
                <div class="bg-white rounded-lg p-8 text-center">
                    <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="package" class="w-6 h-6 text-blue-500"></i>
                    </div>
                    <p class="text-4xl font-display font-black text-ink tracking-tight" x-text="animatedObat">0</p>
                    <p class="text-sm font-semibold text-muted mt-2">Item Logistik Terpantau</p>
                    <p class="text-xs text-muted/60 mt-1">Stok obat & alat kesehatan</p>
                </div>

                {{-- Metric 3: Platform Uptime --}}
                <div class="bg-white rounded-lg p-8 text-center">
                    <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                    <p class="text-4xl font-display font-black text-ink tracking-tight">99.9<span class="text-xl">%</span></p>
                    <p class="text-sm font-semibold text-muted mt-2">Platform Uptime</p>
                    <p class="text-xs text-muted/60 mt-1">Infrastruktur Railway</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── BENEFITS / FEATURES ───────────────────────────────────── --}}
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14 space-y-2">
                <p class="text-xs font-bold text-signal uppercase tracking-[0.2em]">Keunggulan Platform</p>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-ink tracking-tight">Tiga Klaster Proteksi</h2>
                <p class="text-muted font-medium max-w-lg mx-auto">Arsitektur modular yang terintegrasi secara real-time.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Benefit 1 --}}
                <div class="bg-mist rounded-lg p-8 space-y-4 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-11 h-11 rounded-lg bg-white flex items-center justify-center">
                        <i data-lucide="package-search" class="w-5 h-5 text-ink"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink">Monitoring Stok Obat</h3>
                    <p class="text-muted text-sm leading-relaxed">
                        Prediksi kebutuhan stok secara otomatis berdasarkan tren penyakit. Sistem peringatan dini sebelum stok kritis.
                    </p>
                </div>

                {{-- Benefit 2 --}}
                <div class="bg-mist rounded-lg p-8 space-y-4 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-11 h-11 rounded-lg bg-white flex items-center justify-center">
                        <i data-lucide="activity" class="w-5 h-5 text-ink"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink">Pelaporan Penyakit</h3>
                    <p class="text-muted text-sm leading-relaxed">
                        Pencatatan epidemiologi per wilayah dengan deteksi otomatis potensi wabah berdasarkan klaster kasus.
                    </p>
                </div>

                {{-- Benefit 3 --}}
                <div class="bg-mist rounded-lg p-8 space-y-4 hover:-translate-y-1 transition-transform duration-300">
                    <div class="w-11 h-11 rounded-lg bg-white flex items-center justify-center">
                        <i data-lucide="baby" class="w-5 h-5 text-ink"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink">Reminder Imunisasi</h3>
                    <p class="text-muted text-sm leading-relaxed">
                        Buku KIA digital dengan pengingat jadwal vaksinasi otomatis via WhatsApp langsung ke orang tua.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── HEALTHCARE NEWS / ARTICLES ────────────────────────────── --}}
    <section class="bg-mist py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14 space-y-2">
                <p class="text-xs font-bold text-signal uppercase tracking-[0.2em]">Informasi</p>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-ink tracking-tight">Artikel Kesehatan Terkini</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Article 1 --}}
                <div class="bg-white rounded-lg overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="h-48 bg-gradient-to-br from-red-50 to-orange-50 flex items-center justify-center">
                        <i data-lucide="bug" class="w-16 h-16 text-red-200 group-hover:text-red-300 transition-colors"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-bold text-signal bg-signal/10 px-2.5 py-1 rounded-full uppercase tracking-wider">Wabah</span>
                            <span class="text-[10px] text-muted/60 font-medium">{{ now()->format('d M Y') }}</span>
                        </div>
                        <h4 class="font-display font-bold text-ink mb-2">Pencegahan DBD Musim Hujan</h4>
                        <p class="text-sm text-muted line-clamp-2">Langkah 3M Plus untuk mencegah perkembangbiakan nyamuk Aedes aegypti di lingkungan tempat tinggal.</p>
                    </div>
                </div>

                {{-- Article 2 --}}
                <div class="bg-white rounded-lg overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center">
                        <i data-lucide="syringe" class="w-16 h-16 text-blue-200 group-hover:text-blue-300 transition-colors"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Vaksinasi</span>
                            <span class="text-[10px] text-muted/60 font-medium">{{ now()->subDays(3)->format('d M Y') }}</span>
                        </div>
                        <h4 class="font-display font-bold text-ink mb-2">Jadwal Imunisasi Nasional 2026</h4>
                        <p class="text-sm text-muted line-clamp-2">Pahami pentingnya vaksinasi dini bagi balita untuk membentuk herd immunity melawan penyakit mematikan.</p>
                    </div>
                </div>

                {{-- Article 3 --}}
                <div class="bg-white rounded-lg overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="h-48 bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center">
                        <i data-lucide="package" class="w-16 h-16 text-emerald-200 group-hover:text-emerald-300 transition-colors"></i>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Logistik</span>
                            <span class="text-[10px] text-muted/60 font-medium">{{ now()->subDays(7)->format('d M Y') }}</span>
                        </div>
                        <h4 class="font-display font-bold text-ink mb-2">Manajemen Stok Obat Cerdas</h4>
                        <p class="text-sm text-muted line-clamp-2">Pelajari cara sistem prediktif memprediksi lonjakan kebutuhan obat berdasarkan tren cuaca dan laporan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── FOOTER ────────────────────────────────────────────────── --}}
    <footer class="bg-white py-10 text-center space-y-2">
        <p class="text-sm font-semibold text-ink">Medivest</p>
        <p class="text-xs text-muted">&copy; {{ date('Y') }} Sub-klaster Pemantauan & Pencegahan Penyakit. All rights reserved.</p>
        <p class="text-xs text-muted/60">Informatics Engineering — Sebelas Maret University (UNS) PSDKU Madiun</p>
    </footer>

@endsection

@push('scripts')
<script>
    function metricCounter() {
        return {
            targetKasus: {{ $totalKasus ?? 0 }},
            targetObat: {{ $totalObat ?? 0 }},
            animatedKasus: 0,
            animatedObat: 0,
            startCounting() {
                this.countUp('animatedKasus', this.targetKasus);
                this.countUp('animatedObat', this.targetObat);
            },
            countUp(prop, target) {
                const duration = 1500;
                const steps = 60;
                const increment = target / steps;
                let current = 0;
                const interval = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        this[prop] = target;
                        clearInterval(interval);
                    } else {
                        this[prop] = Math.floor(current);
                    }
                }, duration / steps);
            }
        }
    }
</script>
@endpush
