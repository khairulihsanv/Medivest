{{--
    Monitoring Stok Obat — Medivest VENTRILOC Design System
    Modern data list + right-side slide-over for CRUD + Predictive Demand Alerts
--}}

@extends('layouts.app')

@section('title', 'Monitoring Stok Obat')

@section('content')

<div x-data="{ slideOver: false, scannerActive: false }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-display font-bold text-ink tracking-tight">Monitoring Stok Obat</h2>
            <p class="text-muted text-sm mt-1">Manajemen inventaris obat & alat kesehatan.</p>
        </div>
        <button @click="slideOver = true"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Stok
        </button>
    </div>

    {{-- ─── INTELLIGENT SUPPLY ENGINE ──────────────────────────────── --}}
    <div class="bg-white rounded-lg p-6 mb-5">
        <div class="flex items-center gap-2 mb-4">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <h4 class="text-[10px] font-bold tracking-[0.15em] uppercase text-muted">
                Medivest Intelligent Supply Engine
            </h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border-r border-mist pr-4">
                <p class="text-xs text-muted font-medium">Data Feeding (Pelaporan)</p>
                <p class="text-lg font-display font-bold mt-1 text-signal">{{ $kasus_dbd }} Kasus DBD</p>
                <p class="text-[11px] text-muted/60 mt-0.5">Sumber: Live Surveilans Penyakit</p>
            </div>
            <div class="border-r border-mist pr-4">
                <p class="text-xs text-muted font-medium">Batas Aman Dinamis</p>
                <p class="text-lg font-display font-bold mt-1 text-ink">Safety Stock:
                    <span class="text-signal">{{ $analisis_stok['safety_stock'] }}</span> pcs
                </p>
                <p class="text-[11px] text-muted/60 mt-0.5">ROP: {{ $analisis_stok['reorder_point'] }} pcs</p>
            </div>
            <div>
                <p class="text-xs text-muted font-medium">Rekomendasi Aksi</p>
                <p class="text-xs font-bold mt-1.5 {{ $analisis_stok['perlu_order'] ? 'text-signal' : 'text-emerald-600' }}">
                    {{ $analisis_stok['tindakan'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- ─── SEARCH & FILTER ───────────────────────────────────────── --}}
    <div class="bg-white rounded-lg p-4 mb-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-muted/40"></i>
            <input type="text" id="searchObat" placeholder="Cari nama obat atau alkes..."
                   oninput="filterTableObat(this.value)"
                   class="w-full pl-11 pr-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                          focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all">
        </div>
        <select id="filterStatus" onchange="filterTableObat(document.getElementById('searchObat').value)"
                class="px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink
                       focus:ring-2 focus:ring-signal/30 outline-none transition-all cursor-pointer">
            <option value="">Semua Status</option>
            <option value="aman">Aman</option>
            <option value="rendah">Rendah</option>
            <option value="kritis">Kritis</option>
        </select>
    </div>

    {{-- ─── DATA TABLE ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="tabelObat">
                <thead>
                    <tr class="border-b border-mist">
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Kode</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Nama Barang</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Kategori</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Stok</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Demand Alert</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Kadaluarsa</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyObat">
                    @forelse ($dataObat as $row)
                        @php
                            $sisa_hari = $row->hitungSisaHariKadaluarsa();
                            $status = $row->getStatusStok();

                            // Predictive Demand Alert: check if any linked disease is high
                            $demandHigh = false;
                            $linkedDisease = match($row->jenis_obat) {
                                'Vaksin' => 'Campak-Rubella',
                                'Tablet' => 'Demam Berdarah Dengue',
                                'Sirup'  => 'ISPA',
                                default  => null,
                            };
                            if ($linkedDisease && isset($diseaseCounts[$linkedDisease]) && $diseaseCounts[$linkedDisease] >= 3) {
                                $demandHigh = true;
                            }
                        @endphp
                        <tr class="border-b border-mist/60 hover:bg-mist/40 transition-colors"
                            data-nama="{{ strtolower($row->nama_obat) }}"
                            data-status="{{ strtolower($status['label']) }}">
                            <td class="px-6 py-4 font-mono text-xs text-muted/60">{{ $row->kode }}</td>
                            <td class="px-6 py-4 font-bold text-ink">{{ $row->nama_obat }}</td>
                            <td class="px-6 py-4 text-muted font-medium">{{ $row->jenis_obat }}</td>
                            <td class="px-6 py-4 text-center font-display font-black text-ink">{{ number_format($row->stok, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'Kritis' => 'text-red-700 bg-red-50',
                                        'Rendah' => 'text-amber-700 bg-amber-50',
                                        'Aman'   => 'text-emerald-700 bg-emerald-50',
                                    ];
                                    $sClass = $statusColors[$status['label']] ?? 'text-muted bg-mist';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold {{ $sClass }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($demandHigh)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold text-signal bg-signal/10">
                                        <i data-lucide="trending-up" class="w-3 h-3"></i> High Demand
                                    </span>
                                @else
                                    <span class="text-[10px] text-muted/40 font-medium">Normal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-muted font-medium">
                                @if ($sisa_hari < 0)
                                    <span class="text-xs font-bold text-signal">Expired</span>
                                @elseif ($sisa_hari <= 30)
                                    <span class="text-xs font-bold text-amber-600">{{ $sisa_hari }} hari</span>
                                @else
                                    <span class="text-xs text-muted/60">{{ $sisa_hari }} hari</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('obat.destroy', $row->id_obat) }}"
                                      onsubmit="return confirm('Hapus obat ini?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-full hover:bg-red-50 text-muted/40 hover:text-red-500 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-sm text-muted/60">
                                <i data-lucide="package-open" class="w-10 h-10 mx-auto mb-3 text-muted/20"></i>
                                <p>Belum ada data obat. Tambahkan stok pertama!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{--  RIGHT-SIDE SLIDE-OVER DRAWER (Alpine.js)                     --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div x-show="slideOver" x-cloak class="fixed inset-0 z-[60]" aria-labelledby="slide-over-title" role="dialog">
        {{-- Backdrop --}}
        <div x-show="slideOver"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-ink/30" @click="slideOver = false"></div>

        {{-- Panel --}}
        <div class="fixed inset-y-0 right-0 flex max-w-full">
            <div x-show="slideOver"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md">
                <div class="flex h-full flex-col bg-white">

                    {{-- Header --}}
                    <div class="px-6 py-5 border-b border-mist flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-mist flex items-center justify-center">
                                <i data-lucide="package-plus" class="w-[18px] h-[18px] text-ink"></i>
                            </div>
                            <h3 class="font-display font-bold text-ink text-lg" id="slide-over-title">Tambah Stok Baru</h3>
                        </div>
                        <button @click="slideOver = false" class="p-2 rounded-full hover:bg-mist transition-colors text-muted/40 hover:text-ink">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <div class="flex-1 overflow-y-auto p-6 slim-scrollbar">
                        <form method="POST" action="{{ route('obat.store') }}" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Kode</label>
                                    <input type="text" placeholder="Auto" disabled
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm cursor-not-allowed text-muted/40">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Kategori</label>
                                    <select name="jenis_obat" required
                                            class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                        <option value="Tablet" {{ old('jenis_obat') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                                        <option value="Vaksin" {{ old('jenis_obat') == 'Vaksin' ? 'selected' : '' }}>Vaksin</option>
                                        <option value="Sirup" {{ old('jenis_obat') == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                                        <option value="Infus" {{ old('jenis_obat') == 'Infus' ? 'selected' : '' }}>Infus</option>
                                        <option value="Kapsul" {{ old('jenis_obat') == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                                        <option value="Alkes" {{ old('jenis_obat') == 'Alkes' ? 'selected' : '' }}>Alat Kesehatan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Nama Barang</label>
                                <div class="flex gap-2">
                                    <input type="text" name="nama_obat" id="nama_obat_input" placeholder="Nama obat / alkes" required value="{{ old('nama_obat') }}"
                                           class="flex-1 px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                                  focus:ring-2 focus:ring-signal/30 outline-none transition-all">
                                    <button type="button" @click="scannerActive = !scannerActive"
                                            class="bg-signal/10 text-signal hover:bg-signal/20 px-4 rounded-lg flex items-center justify-center transition-colors"
                                            title="Scan Barcode">
                                        <i data-lucide="scan-barcode" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                {{-- QR Scanner --}}
                                <div x-show="scannerActive" x-transition class="w-full overflow-hidden rounded-lg bg-mist mt-2">
                                    <div id="qr-reader" class="w-full"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Jumlah</label>
                                    <input type="number" name="stok" placeholder="0" min="1" required value="{{ old('stok') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Harga (Rp)</label>
                                    <input type="number" name="harga_beli" placeholder="0" min="0" required value="{{ old('harga_beli') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Kadaluarsa</label>
                                    <input type="date" name="tgl_kadaluarsa" required value="{{ old('tgl_kadaluarsa') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-mist flex gap-3">
                                <button type="submit"
                                        class="flex-1 px-6 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-all text-center">
                                    Simpan Stok
                                </button>
                                <button type="button" @click="slideOver = false"
                                        class="flex-1 px-6 py-2.5 bg-mist text-ink rounded-full text-sm font-bold hover:bg-mist/70 text-center transition-all">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function filterTableObat(query) {
        const status  = document.getElementById('filterStatus').value.toLowerCase();
        const keyword = query.toLowerCase().trim();
        const rows    = document.querySelectorAll('#tbodyObat tr[data-nama]');

        rows.forEach(row => {
            const nama       = row.dataset.nama  || '';
            const rowStatus  = row.dataset.status || '';
            const matchNama   = nama.includes(keyword);
            const matchStatus = !status || rowStatus === status;
            row.style.display = (matchNama && matchStatus) ? '' : 'none';
        });
    }

    // Auto-open slide-over on validation error
    @if ($errors->any())
    document.addEventListener('alpine:init', () => {
        // Will be handled by Alpine x-data default
    });
    @endif
</script>
@endpush
