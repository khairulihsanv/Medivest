{{--
    Components/navbar.blade.php — Header navigasi atas dashboard
    
    Migrasi dari Components/navbar.php (PHP native).
    Menerima variabel $tab dari DashboardController.
--}}

@php
    $tabTitles = [
        'overview'  => 'Dashboard Overview',
        'simosoba'  => 'Sistem Monitoring Stok Obat (SiMoSoBa)',
        'pelaporan' => 'Pelaporan Penyakit',
        'imunisasi' => 'Reminder Imunisasi',
    ];
@endphp

<header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-surface-200/60">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
        
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden p-2 rounded-lg hover:bg-surface-100 transition-colors" id="hamburgerBtn">
                <i data-lucide="menu" class="w-5 h-5 text-surface-700"></i>
            </button>
            
            <h2 class="text-md font-bold text-surface-900 tracking-tight hidden sm:block">
                {{ $tabTitles[$tab] ?? ucfirst($tab) }}
            </h2>
        </div>

        <div class="flex items-center gap-3">
            <button class="relative p-2 rounded-lg hover:bg-surface-100 transition-colors">
                <i data-lucide="bell" class="w-5 h-5 text-surface-600"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>
            
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full text-xs font-semibold border border-brand-200/60">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>{{ Auth::user()->role ?? 'Tim Medis Terpadu' }}</span>
            </div>
            
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white text-sm font-bold shadow-md shadow-brand-500/20 cursor-pointer ring-2 ring-white">
                {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'A', 0, 1)) }}
            </div>
        </div>

    </div>
</header>
