<div class="mb-8">
    <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Dashboard Overview</h2>
    <p class="text-surface-500 text-sm mt-1">Ringkasan status pemantauan dan pencegahan kesehatan terkini secara real-time.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-5 mb-8">
    
    <div class="metric-card bg-white rounded-2xl p-5 border border-surface-200/60 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-red-50 to-transparent rounded-bl-[60px]"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full pulse-dot"></span> Terpantau
                </span>
            </div>
            <p class="text-sm font-medium text-surface-500 mb-1">Stok Kritis</p>
            <p class="text-3xl font-extrabold text-surface-900 tracking-tight">{{ $stokKritis }}</p>
            <p class="text-xs text-surface-400 mt-1">dari {{ $totalObat }} total item logistik</p>
        </div>
    </div>

    <div class="metric-card bg-white rounded-2xl p-5 border border-surface-200/60 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-amber-50 to-transparent rounded-bl-[60px]"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                    <i data-lucide="flame" class="w-5 h-5 text-amber-500"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full pulse-dot"></span> Waspada
                </span>
            </div>
            <p class="text-sm font-medium text-surface-500 mb-1">Wabah Terlaporkan</p>
            <p class="text-3xl font-extrabold text-surface-900 tracking-tight">{{ $totalKasus }}</p>
            <p class="text-xs text-surface-400 mt-1">kasus tercatat aktif</p>
        </div>
    </div>

    <div class="metric-card bg-white rounded-2xl p-5 border border-surface-200/60 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-brand-50 to-transparent rounded-bl-[60px]"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-brand-50 flex items-center justify-center">
                    <i data-lucide="syringe" class="w-5 h-5 text-brand-500"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-600 bg-brand-50 px-2 py-1 rounded-full">Antrean</span>
            </div>
            <p class="text-sm font-medium text-surface-500 mb-1">Belum Di-Reminder</p>
            <p class="text-3xl font-extrabold text-surface-900 tracking-tight">{{ $totalAntrean }}</p>
            <p class="text-xs text-surface-400 mt-1">pasien menunggu pengingat WA</p>
        </div>
    </div>

    <div class="metric-card bg-white rounded-2xl p-5 border border-surface-200/60 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-50 to-transparent rounded-bl-[60px]"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <i data-lucide="database" class="w-5 h-5 text-emerald-500"></i>
                </div>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Online
                </span>
            </div>
            <p class="text-sm font-medium text-surface-500 mb-1">Infrastruktur Server</p>
            <p class="text-xl font-extrabold text-surface-900 tracking-tight">TiDB Cloud</p>
            <p class="text-xs text-surface-400 mt-1">AWS Cluster ap-southeast-1</p>
        </div>
    </div>
</div>
