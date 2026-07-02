{{--
    Dashboard Overview — Medivest VENTRILOC Design System
    Bento-grid layout: Critical Medicine Stock, Disease Spikes, Immunization Schedule
--}}

@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')

{{-- Page Header --}}
<div class="mb-8">
    <h2 class="text-2xl font-display font-bold text-ink tracking-tight">Dashboard Overview</h2>
    <p class="text-muted text-sm mt-1">Ringkasan status pemantauan dan pencegahan kesehatan terkini.</p>
</div>

{{-- ─── METRIC SUMMARY CARDS (Bento Grid) ─────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

    {{-- Stok Kritis --}}
    <div class="bg-white rounded-lg p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-signal bg-signal/10 px-2.5 py-1 rounded-full uppercase tracking-wider">
                <span class="w-1.5 h-1.5 bg-signal rounded-full pulse-dot"></span> Kritis
            </span>
        </div>
        <p class="text-sm font-semibold text-muted mb-0.5">Stok Kritis</p>
        <p class="text-3xl font-display font-black text-ink tracking-tight">{{ $stokKritis }}</p>
        <p class="text-xs text-muted/60 mt-1">dari {{ $totalObat }} total item logistik</p>
    </div>

    {{-- Wabah Terlaporkan --}}
    <div class="bg-white rounded-lg p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <i data-lucide="flame" class="w-5 h-5 text-amber-500"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full pulse-dot"></span> Waspada
            </span>
        </div>
        <p class="text-sm font-semibold text-muted mb-0.5">Kasus Terlaporkan</p>
        <p class="text-3xl font-display font-black text-ink tracking-tight">{{ $totalKasus }}</p>
        <p class="text-xs text-muted/60 mt-1">kasus tercatat aktif</p>
    </div>

    {{-- Belum Di-Reminder --}}
    <div class="bg-white rounded-lg p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <i data-lucide="baby" class="w-5 h-5 text-blue-500"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                Antrean
            </span>
        </div>
        <p class="text-sm font-semibold text-muted mb-0.5">Belum Di-Reminder</p>
        <p class="text-3xl font-display font-black text-ink tracking-tight">{{ $totalAntrean }}</p>
        <p class="text-xs text-muted/60 mt-1">pasien menunggu pengingat WA</p>
    </div>

    {{-- Infrastruktur --}}
    <div class="bg-white rounded-lg p-5 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <i data-lucide="database" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Online
            </span>
        </div>
        <p class="text-sm font-semibold text-muted mb-0.5">Infrastruktur</p>
        <p class="text-xl font-display font-black text-ink tracking-tight">Railway DB</p>
        <p class="text-xs text-muted/60 mt-1">Railway Cloud Network</p>
    </div>
</div>

{{-- ─── BENTO DATA GRID ───────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">

    {{-- Top Penyakit --}}
    <div class="bg-white rounded-lg p-6">
        <h3 class="font-display font-bold text-ink mb-4 flex items-center gap-2 text-sm">
            <i data-lucide="activity" class="w-4 h-4 text-red-500"></i>
            Penyakit Tertinggi
        </h3>
        <div class="space-y-2.5">
            @forelse ($topDiseases as $penyakit => $total)
                <div class="flex justify-between items-center p-3 rounded-lg bg-mist">
                    <span class="text-sm font-semibold text-ink">{{ $penyakit }}</span>
                    <span class="text-xs font-bold text-signal bg-signal/10 px-2.5 py-1 rounded-full">{{ $total }} kasus</span>
                </div>
            @empty
                <p class="text-sm text-muted/60 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>

    {{-- Top Wilayah --}}
    <div class="bg-white rounded-lg p-6">
        <h3 class="font-display font-bold text-ink mb-4 flex items-center gap-2 text-sm">
            <i data-lucide="map-pin" class="w-4 h-4 text-blue-500"></i>
            Wilayah Terdampak
        </h3>
        <div class="space-y-2.5">
            @forelse ($topWilayah as $wilayah => $total)
                <div class="flex justify-between items-center p-3 rounded-lg bg-mist">
                    <span class="text-sm font-semibold text-ink">{{ $wilayah ?: 'Tidak Diketahui' }}</span>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">{{ $total }} laporan</span>
                </div>
            @empty
                <p class="text-sm text-muted/60 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>

    {{-- Stok Kritis Items --}}
    <div class="bg-white rounded-lg p-6">
        <h3 class="font-display font-bold text-ink mb-4 flex items-center gap-2 text-sm">
            <i data-lucide="package-minus" class="w-4 h-4 text-amber-500"></i>
            Stok Paling Kritis
        </h3>
        <div class="space-y-2.5">
            @forelse ($stokKritisItems as $obat)
                <div class="flex justify-between items-center p-3 rounded-lg bg-mist">
                    <div>
                        <span class="text-sm font-semibold text-ink block truncate max-w-[140px]">{{ $obat->nama_obat }}</span>
                        <span class="text-[10px] text-muted/60 font-medium">{{ $obat->jenis_obat }}</span>
                    </div>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">{{ $obat->stok }} pcs</span>
                </div>
            @empty
                <p class="text-sm text-muted/60 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>

{{-- ─── TODAY'S IMMUNIZATION SCHEDULE ─────────────────────────── --}}
<div class="bg-white rounded-lg p-6">
    <h3 class="font-display font-bold text-ink mb-4 flex items-center gap-2 text-sm">
        <i data-lucide="calendar-check" class="w-4 h-4 text-signal"></i>
        Jadwal Imunisasi Hari Ini
        <span class="text-[10px] font-bold text-muted/50 bg-mist px-2.5 py-1 rounded-full ml-auto">{{ now()->format('d M Y') }}</span>
    </h3>
    @if($todaySchedule->count() > 0)
        <div class="space-y-2.5">
            @foreach($todaySchedule as $jadwal)
                <div class="flex items-center justify-between p-3 rounded-lg bg-mist">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center">
                            <i data-lucide="baby" class="w-4 h-4 text-muted/60"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-ink">{{ $jadwal->nama_anak }}</p>
                            <p class="text-xs text-muted/60">{{ $jadwal->jenis_vaksin }} · Dosis ke-{{ $jadwal->dosis_ke }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-muted/60">{{ $jadwal->usia_bulan }} bln</span>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i data-lucide="calendar-x" class="w-10 h-10 text-muted/20 mx-auto mb-2"></i>
            <p class="text-sm text-muted/60">Tidak ada jadwal imunisasi hari ini.</p>
        </div>
    @endif
</div>

@endsection
