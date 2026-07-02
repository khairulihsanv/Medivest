{{-- 
    Layout Utama — Medivest Health Logistics & Monitoring System
    VENTRILOC Analytics Console Design System
    
    Design Tokens:
    - Canvas: #efefef (Mist)
    - Cards: #ffffff (white) with rounded-lg
    - Text Primary: #202020 | Secondary: #4d4d4d
    - Accent: #ff682c (Signal Orange) — sparingly
    - Buttons/Nav: rounded-full (pill)
    - Elevation: Canvas contrast only (no drop shadows)
--}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Medivest — Sistem Informasi Pemantauan & Pencegahan Penyakit serta Logistik Medis Terpadu.')">

    <title>@yield('title', 'Medivest') — Medivest</title>

    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mist: '#efefef',
                        ink: '#202020',
                        muted: '#4d4d4d',
                        signal: '#ff682c',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['"DM Sans"', 'Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Google Fonts: Inter + DM Sans (geometric) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- HTML5 QR Code Scanner --}}
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #efefef;
            color: #202020;
        }

        /* Scrollbar styling */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Subtle scrollbar for content areas */
        .slim-scrollbar::-webkit-scrollbar { width: 4px; }
        .slim-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .slim-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
        .slim-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* Slide-over transitions */
        .slide-over-backdrop {
            transition: opacity 300ms ease;
        }
        .slide-over-panel {
            transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Toast animations */
        @keyframes toast-in {
            from { transform: translateY(-1rem); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes toast-out {
            from { transform: translateY(0); opacity: 1; }
            to { transform: translateY(-1rem); opacity: 0; }
        }
        .toast-enter { animation: toast-in 300ms ease forwards; }
        .toast-leave { animation: toast-out 300ms ease forwards; }

        /* Pulse dot */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>

    @stack('styles')
</head>

<body class="antialiased selection:bg-signal/20 selection:text-signal">

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{--  GLOBAL ALPINE.JS TOAST NOTIFICATION                          --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div x-data="toastNotification()" x-init="init()" class="fixed top-6 right-6 z-[100] space-y-3" style="pointer-events: none;">
        <template x-if="show">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-[-1rem] opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-[-1rem] opacity-0"
                 class="flex items-center gap-3 px-5 py-3.5 bg-white rounded-lg max-w-sm"
                 style="pointer-events: auto;">
                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <p class="text-sm font-semibold text-ink leading-snug" x-text="message"></p>
                <button @click="dismiss()" class="ml-auto text-muted/60 hover:text-ink transition-colors flex-shrink-0">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </template>
    </div>

    <script>
        function toastNotification() {
            return {
                show: false,
                message: '',
                timeout: null,
                init() {
                    @if(session('success'))
                        this.fire('{{ session('success') }}');
                    @endif
                    @if(session('status') === 'sukses')
                        this.fire('Data berhasil disimpan.');
                    @endif
                    @if(session('status') === 'hapussukses')
                        this.fire('Data berhasil dihapus.');
                    @endif
                },
                fire(msg) {
                    this.message = msg;
                    this.show = true;
                    this.$nextTick(() => { lucide.createIcons(); });
                    this.timeout = setTimeout(() => { this.dismiss(); }, 4000);
                },
                dismiss() {
                    this.show = false;
                    if (this.timeout) clearTimeout(this.timeout);
                }
            }
        }
    </script>

    @auth
    @if(request()->is('dashboard*'))
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{--  AUTHENTICATED DASHBOARD LAYOUT                                --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="flex min-h-screen bg-mist">

        {{-- ── SIDEBAR NAVIGATION ─────────────────────────────────── --}}
        <aside class="w-[272px] bg-white flex-shrink-0 flex flex-col sticky top-0 h-screen">

            {{-- Logo --}}
            <div class="h-[72px] flex items-center px-6">
                <a href="/dashboard" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-lg bg-signal flex items-center justify-center text-white font-display font-black text-lg">
                        M
                    </div>
                    <div>
                        <h1 class="text-lg font-display font-bold text-ink tracking-tight">Medivest</h1>
                        <p class="text-[10px] text-muted uppercase tracking-[0.15em] font-semibold -mt-0.5">Analytics Console</p>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            @php
                $currentRoute = request()->route()?->getName() ?? 'dashboard';
                $userRole = Auth::user()->role;

                $menus = [
                    ['route' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Overview', 'roles' => ['*']],
                    ['route' => 'dashboard.obat', 'icon' => 'package-search', 'label' => 'Stok Obat', 'roles' => ['Farmasi', 'Staf Admin']],
                    ['route' => 'dashboard.penyakit', 'icon' => 'activity', 'label' => 'Pelaporan Penyakit', 'roles' => ['Tenaga Medis', 'Staf Admin']],
                    ['route' => 'dashboard.imunisasi', 'icon' => 'baby', 'label' => 'Imunisasi', 'roles' => ['Bidan', 'Staf Admin']],
                    ['route' => 'dashboard.users', 'icon' => 'users', 'label' => 'Manajemen User', 'roles' => ['Staf Admin']],
                ];
            @endphp

            <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 slim-scrollbar">
                <p class="px-3 text-[10px] font-bold text-muted/50 uppercase tracking-[0.15em] mb-3">Navigasi</p>
                @foreach($menus as $menu)
                    @if(in_array('*', $menu['roles']) || in_array($userRole, $menu['roles']))
                        <a href="{{ route($menu['route']) }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition-all duration-200
                                  {{ $currentRoute === $menu['route']
                                     ? 'bg-ink text-white'
                                     : 'text-muted hover:text-ink hover:bg-mist' }}">
                            <i data-lucide="{{ $menu['icon'] }}" class="w-[18px] h-[18px]
                                {{ $currentRoute === $menu['route'] ? 'text-white' : 'text-muted/60' }}"></i>
                            {{ $menu['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- User Profile & Logout --}}
            <div class="p-4 border-t border-mist">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-mist flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-muted">{{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-ink truncate">{{ Auth::user()->nama_lengkap ?? 'User' }}</p>
                        <p class="text-[11px] font-medium text-signal truncate">{{ Auth::user()->role ?? 'Unknown' }}</p>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                                class="flex items-center justify-center text-muted/40 hover:text-signal hover:bg-signal/10 p-2 rounded-full transition-colors"
                                title="Logout">
                            <i data-lucide="log-out" class="w-[18px] h-[18px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ── MAIN CONTENT AREA ──────────────────────────────────── --}}
        <main class="flex-1 min-w-0">
            <div class="p-8 lg:p-10 max-w-[1600px] mx-auto min-h-full">

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 bg-white rounded-lg p-4" x-data="{ show: true }" x-show="show">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-ink text-sm mb-1">Koreksi Inputan Anda:</p>
                                <ul class="text-sm text-muted space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 bg-red-400 rounded-full flex-shrink-0"></span>
                                            {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <button @click="show = false" class="text-muted/40 hover:text-ink transition-colors flex-shrink-0">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    @else
        {{-- Non-dashboard authenticated pages --}}
        @yield('content')
    @endif
    @else
        {{-- Guest pages (login, register, landing) --}}
        @yield('content')
    @endauth

    {{-- Initialize Lucide Icons --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });
        // Re-init after Alpine transitions
        document.addEventListener('alpine:initialized', () => { lucide.createIcons(); });
    </script>

    @stack('scripts')
</body>

</html>
