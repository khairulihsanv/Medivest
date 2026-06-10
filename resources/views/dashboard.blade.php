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
    </script>
@endpush
