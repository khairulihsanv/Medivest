{{--
    Dashboard Page — Medivest Health Logistics & Monitoring System

    Halaman terproteksi utama dengan navigasi tab-based.
    Migrasi dari dashboard.php (PHP native) — shell layout dengan sidebar + navbar + tab content.
    Dilindungi oleh middleware 'auth' di routes/web.php.
--}}

@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eef7ff', 100: '#d9edff', 200: '#bce0ff', 300: '#8ecdff',
                            400: '#59b0ff', 500: '#338dfc', 600: '#1d6ef1', 700: '#1558de',
                            800: '#1847b4', 900: '#1a3f8e', 950: '#152856',
                        },
                        surface: {
                            50:  '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 700: '#334155',
                            800: '#1e293b', 900: '#0f172a', 950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Sidebar active link highlight */
        .nav-link.active { background: rgba(51,141,252,0.12); color: #fff; }
        /* Status badge pulse */
        .pulse-dot { animation: pulse-ring 2s cubic-bezier(0.4,0,0.6,1) infinite; }
        @keyframes pulse-ring { 0%,100% { opacity:1; } 50% { opacity:0.5; } }
        /* Table row hover */
        .table-row-hover:hover { background:#f1f5f9; transition:background 0.15s ease; }
        /* Elevated button press effect */
        .btn-primary { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 4px 16px rgba(29,110,241,0.35); }
    </style>
@endpush

@section('content')

    {{-- ─── SIDEBAR ──────────────────────────────────────────────── --}}
    @include('components.sidebar', ['tab' => $tab])

    {{-- ─── MAIN CONTENT AREA ────────────────────────────────────── --}}
    <div class="flex-1 lg:ml-[270px] min-h-screen flex flex-col">

        {{-- ─── NAVBAR ───────────────────────────────────────────── --}}
        @include('components.navbar', ['tab' => $tab])

        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- ── SUCCESS / DELETE TOAST BANNERS ────────────────── --}}
            @if (session('status'))
                @php
                    $statusMap = [
                        'sukses'      => ['✅ Data berhasil disimpan ke database lokal.',   'emerald'],
                        'hapussukses' => ['🗑️ Data berhasil dihapus dari database lokal.', 'red'],
                    ];
                    $info = $statusMap[session('status')] ?? null;
                @endphp

                @if ($info)
                    <div id="statusBanner"
                         class="mb-5 flex items-center gap-3 px-5 py-3.5 bg-{{ $info[1] }}-50 border border-{{ $info[1] }}-200
                                text-{{ $info[1] }}-700 rounded-2xl text-sm font-medium shadow-sm">
                        <span>{{ $info[0] }}</span>
                        <button onclick="this.parentElement.remove()" class="ml-auto text-{{ $info[1] }}-400 hover:text-{{ $info[1] }}-700">✕</button>
                    </div>
                @endif
            @endif

            {{-- ── VALIDATION ERRORS (from redirected POST) ──────── --}}
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm">
                    <p class="font-bold mb-1">⚠️ Koreksi Inputan Anda:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── ROLE-BASED WIDGETS ────────────────────────────────── --}}
            @if ($role === 'Dokter' && isset($waspada_epidemi) && $waspada_epidemi->count() > 0)
                <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="alert-triangle" class="text-red-500 w-6 h-6"></i>
                        <h3 class="text-red-700 font-bold text-lg">WASPADA EPIDEMI</h3>
                    </div>
                    <p class="text-sm text-red-600 mb-3">Terdeteksi lonjakan kasus penyakit di wilayah berikut:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($waspada_epidemi as $alert)
                            <div class="bg-white p-3 rounded-lg shadow-sm border border-red-100 flex justify-between items-center">
                                <span class="font-semibold text-slate-700">{{ $alert->wilayah }}</span>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-md text-xs font-bold">+{{ $alert->total }} Kasus</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($role === 'Apoteker' && isset($restock_urgency) && $restock_urgency->count() > 0)
                <div class="mb-6 p-5 bg-amber-50 border-l-4 border-amber-500 rounded-r-xl shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="package-minus" class="text-amber-500 w-6 h-6"></i>
                        <h3 class="text-amber-700 font-bold text-lg">URGENSI RESTOCK (PREDIKSI MUSIMAN)</h3>
                    </div>
                    <p class="text-sm text-amber-600 mb-3">Obat berikut diprediksi akan kehabisan stok berdasarkan tren penyakit:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($restock_urgency->take(4) as $obat)
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-amber-100">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-slate-800">{{ $obat->nama_obat }}</h4>
                                        <p class="text-xs text-slate-500">Sisa Stok: {{ $obat->stok }} unit</p>
                                    </div>
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-md text-xs font-bold">Kritis</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2.5">
                                    <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ min(100, $obat->urgency_score) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($role === 'Petugas Imunisasi' && isset($vaksin_drop) && $vaksin_drop->count() > 0)
                <div class="mb-6 p-5 bg-blue-50 border-l-4 border-blue-500 rounded-r-xl shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="shield-alert" class="text-blue-500 w-6 h-6"></i>
                        <h3 class="text-blue-700 font-bold text-lg">CAKUPAN VAKSIN DROP</h3>
                    </div>
                    <p class="text-sm text-blue-600 mb-3">Daftar anak yang melewatkan dosis vaksin esensial (Usia > 9 bulan):</p>
                    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-blue-100">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="bg-blue-50/50 text-xs text-slate-700 uppercase">
                                <tr>
                                    <th class="px-4 py-3">Nama Anak</th>
                                    <th class="px-4 py-3">Usia (Bulan)</th>
                                    <th class="px-4 py-3">Vaksin Terlewat</th>
                                    <th class="px-4 py-3">Orang Tua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vaksin_drop->take(5) as $drop)
                                <tr class="border-b border-slate-100">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $drop->nama_anak }}</td>
                                    <td class="px-4 py-3 text-red-600 font-bold">{{ $drop->usia_bulan }} bln</td>
                                    <td class="px-4 py-3">{{ $drop->jenis_vaksin }}</td>
                                    <td class="px-4 py-3">{{ $drop->nama_orang_tua }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ── ADVANCED ANALYTICS (CHART.JS) — Hanya tampil di tab Overview ── --}}
            @if ($tab === 'overview')
            <div class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Multi-Axis Line Chart -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-surface-200">
                    <h3 class="font-bold text-slate-800 mb-4">Korelasi Kasus Aktif vs Stok Obat (6 Bulan)</h3>
                    <div class="relative h-64">
                        <canvas id="multiAxisChart"></canvas>
                    </div>
                </div>
                <!-- Stacked Bar Chart -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-surface-200">
                    <h3 class="font-bold text-slate-800 mb-4">Cakupan Imunisasi per Demografi</h3>
                    <div class="relative h-64">
                        <canvas id="stackedBarChart"></canvas>
                    </div>
                </div>
                <!-- Top Seasonal Diseases Chart -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-surface-200 lg:col-span-2">
                    <h3 class="font-bold text-slate-800 mb-4">Penyakit Musiman Teratas</h3>
                    <div class="relative h-72">
                        <canvas id="seasonalChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            {{-- ── TAB CONTENT LOADER ────────────────────────────── --}}
            <div class="tab-panel block">
                @include("dashboard.{$tab}")
            </div>

        </main>

        <footer class="border-t border-surface-200/60 bg-white/50 px-6 lg:px-8 py-4 text-xs text-surface-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Medivest — Sub-klaster Pemantauan &amp; Pencegahan. All rights reserved.</p>
            <p>Version 2.0.0 &bull; Laravel MVC Architecture</p>
        </footer>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Re-initialize Lucide icons after Blade partials render
        lucide.createIcons();

        function openModal(id) {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; lucide.createIcons(); }
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
        }

        // Auto-dismiss status banner after 4s
        const banner = document.getElementById('statusBanner');
        if (banner) setTimeout(() => banner.remove(), 4000);

        // -- Chart.js Initialization (hanya di tab overview) --
        @if ($tab === 'overview')
        const chartData = @json($chartData);

        // 1. Multi-Axis Line Chart
        new Chart(document.getElementById('multiAxisChart'), {
            type: 'line',
            data: {
                labels: chartData.multi_axis.labels,
                datasets: [
                    {
                        label: 'Kasus Penyakit',
                        data: chartData.multi_axis.cases,
                        borderColor: '#ef4444',
                        backgroundColor: '#ef4444',
                        yAxisID: 'y',
                    },
                    {
                        label: 'Stok Obat',
                        data: chartData.multi_axis.stock,
                        borderColor: '#3b82f6',
                        backgroundColor: '#3b82f6',
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { type: 'linear', display: true, position: 'left' },
                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });

        // 2. Stacked Bar Chart
        new Chart(document.getElementById('stackedBarChart'), {
            type: 'bar',
            data: {
                labels: chartData.stacked_bar.labels,
                datasets: [
                    {
                        label: 'Anak Diimunisasi',
                        data: chartData.stacked_bar.immunized,
                        backgroundColor: '#10b981',
                    },
                    {
                        label: 'Total Populasi Anak',
                        data: chartData.stacked_bar.population,
                        backgroundColor: '#94a3b8',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true }
                }
            }
        });

        // 3. Top Seasonal Diseases Chart
        new Chart(document.getElementById('seasonalChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(chartData.top_diseases),
                datasets: [{
                    label: 'Total Kasus',
                    data: Object.values(chartData.top_diseases),
                    backgroundColor: '#f59e0b',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
            }
        });
        @endif
    </script>
@endpush
