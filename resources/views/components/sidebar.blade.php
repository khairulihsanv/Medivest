{{--
    Components/sidebar.blade.php — Navigasi sidebar dashboard
    
    Migrasi dari Components/sidebar.php (PHP native).
    Menggunakan Auth::user() sebagai pengganti $_SESSION['user'].
    Tab links mengarah ke /dashboard?tab=xxx (bukan dashboard.php?tab=xxx).
--}}

<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-[270px] bg-surface-900 text-slate-300 border-r border-surface-800 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0">
    
    <div class="px-4 py-6 flex flex-col gap-8 flex-1 overflow-y-auto">
        
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-lg shadow-brand-500/20">
                M
            </div>
            <div>
                <h1 class="font-bold text-white tracking-tight">Medivest</h1>
                <p class="text-[10px] text-surface-400">Pemantauan & Pencegahan</p>
            </div>
        </div>

        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}?tab=overview" class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-left text-slate-400 hover:text-white {{ $tab === 'overview' ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard Overview</span>
            </a>
            
            <a href="{{ route('dashboard') }}?tab=simosoba" class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-left text-slate-400 hover:text-white {{ $tab === 'simosoba' ? 'active' : '' }}">
                <i data-lucide="package" class="w-5 h-5"></i>
                <span>SiMoSoBa</span>
            </a>
            
            <a href="{{ route('dashboard') }}?tab=pelaporan" class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-left text-slate-400 hover:text-white {{ $tab === 'pelaporan' ? 'active' : '' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Pelaporan Penyakit</span>
            </a>
            
            <a href="{{ route('dashboard') }}?tab=imunisasi" class="nav-link w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-left text-slate-400 hover:text-white {{ $tab === 'imunisasi' ? 'active' : '' }}">
                <i data-lucide="syringe" class="w-5 h-5"></i>
                <span>Reminder Imunisasi</span>
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-surface-800 bg-surface-950/40 space-y-4">
        
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-xl bg-surface-800 flex items-center justify-center text-xs font-bold text-brand-400 border border-surface-700">
                {{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'TM', 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold text-white truncate">
                    {{ Auth::user()->nama_lengkap ?? 'Tim Medis Terpadu' }}
                </p>
                <p class="text-[10px] text-surface-400 truncate">
                    {{ Auth::user()->username ?? 'medis' }}@medivest.id
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    onclick="return confirm('Yakin ingin keluar dari sistem Medivest?');"
                    class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all font-medium text-sm w-full">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
