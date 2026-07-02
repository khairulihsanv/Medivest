{{--
    Pelaporan Penyakit — Medivest VENTRILOC Design System
    Clinical reporting with slide-over drawer + outbreak detection
--}}

@extends('layouts.app')

@section('title', 'Pelaporan Penyakit')

@section('content')

<div x-data="{ slideOver: false }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-display font-bold text-ink tracking-tight">Pelaporan Penyakit</h2>
            <p class="text-muted text-sm mt-1">Pencatatan epidemiologi dan surveilans wilayah.</p>
        </div>
        <button @click="slideOver = true"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Laporan Baru
        </button>
    </div>

    {{-- ─── OUTBREAK ALERT BANNER ─────────────────────────────────── --}}
    @if(isset($outbreakAlerts) && $outbreakAlerts->count() > 0)
        <div class="bg-signal/5 rounded-lg p-5 mb-6 border-l-4 border-signal">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-signal/10 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-signal"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-ink text-sm">WASPADA — Potensi Wabah Terdeteksi</h3>
                    <p class="text-xs text-muted mt-0.5">Wilayah dengan ≥3 kasus tercatat dalam sistem</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-3">
                @foreach($outbreakAlerts as $alert)
                    <div class="bg-white rounded-lg p-3 flex justify-between items-center">
                        <span class="font-semibold text-sm text-ink">{{ $alert->wilayah }}</span>
                        <span class="text-[10px] font-bold text-signal bg-signal/10 px-2.5 py-1 rounded-full">+{{ $alert->total }} Kasus</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ─── REPORT LIST ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-lg bg-mist flex items-center justify-center">
                <i data-lucide="clipboard-list" class="w-[18px] h-[18px] text-muted"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-ink text-sm">Laporan Terkini</h3>
                <p class="text-[10px] text-muted font-medium">{{ $dataLaporan->count() }} data kasus</p>
            </div>
        </div>

        <div class="space-y-3 max-h-[600px] overflow-y-auto slim-scrollbar pr-1">
            @forelse ($dataLaporan as $laporan)
                <div class="bg-mist rounded-lg p-4 hover:bg-mist/70 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-ink truncate">{{ $laporan->nama_pasien }}</p>
                            <p class="text-xs text-muted mt-0.5 font-medium">
                                {{ $laporan->jenis_penyakit }} — {{ $laporan->wilayah }}
                            </p>
                        </div>
                        @php
                            $sevColors = [
                                'Ringan' => 'text-emerald-700 bg-emerald-50',
                                'Sedang' => 'text-amber-700 bg-amber-50',
                                'Berat'  => 'text-signal bg-signal/10',
                                'Kritis' => 'text-red-700 bg-red-50',
                            ];
                            $sev = $sevColors[$laporan->tingkat_keparahan] ?? $sevColors['Ringan'];
                        @endphp
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $sev }}">
                            {{ $laporan->tingkat_keparahan }}
                        </span>
                    </div>

                    @if ($laporan->catatan_klinis)
                        <p class="text-xs text-muted italic mt-3 bg-white p-3 rounded-lg">
                            "{{ $laporan->catatan_klinis }}"
                        </p>
                    @endif

                    <p class="text-[10px] text-muted/50 mt-3 font-medium">
                        Diagnosis: {{ $laporan->tgl_diagnosis->format('d M Y') }}
                        &bull; NIK: {{ $laporan->nik }}
                    </p>
                </div>
            @empty
                <div class="text-center py-16 text-muted/40">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-muted/20"></i>
                    <p class="text-sm">Belum ada riwayat pelaporan kasus penyakit.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{--  RIGHT-SIDE SLIDE-OVER DRAWER                                 --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div x-show="slideOver" x-cloak class="fixed inset-0 z-[60]" role="dialog">
        <div x-show="slideOver"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-ink/30" @click="slideOver = false"></div>

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
                                <i data-lucide="file-plus" class="w-[18px] h-[18px] text-ink"></i>
                            </div>
                            <h3 class="font-display font-bold text-ink text-lg">Formulir Laporan</h3>
                        </div>
                        <button @click="slideOver = false" class="p-2 rounded-full hover:bg-mist transition-colors text-muted/40 hover:text-ink">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <div class="flex-1 overflow-y-auto p-6 slim-scrollbar">
                        <form method="POST" action="{{ route('pelaporan.store') }}" class="space-y-5">
                            @csrf

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Nama Pasien</label>
                                <input type="text" name="nama_pasien" placeholder="Nama lengkap" required value="{{ old('nama_pasien') }}"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                              focus:ring-2 focus:ring-signal/30 outline-none transition-all">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">NIK / No. Identitas</label>
                                <input type="text" name="nik" placeholder="16 digit NIK" required value="{{ old('nik') }}"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                              focus:ring-2 focus:ring-signal/30 outline-none transition-all">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Jenis Penyakit</label>
                                <select name="jenis_penyakit" required
                                        class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                    <option value="">— Pilih —</option>
                                    @foreach(['Demam Berdarah Dengue', 'ISPA', 'Diare', 'TBC', 'Malaria', 'COVID-19', 'Leptospirosis'] as $p)
                                        <option value="{{ $p }}" {{ old('jenis_penyakit') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Tanggal Diagnosis</label>
                                <input type="date" name="tgl_diagnosis" required max="{{ date('Y-m-d') }}" value="{{ old('tgl_diagnosis') }}"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">
                                    Wilayah (Kecamatan)
                                    <span class="ml-1 text-[9px] font-bold text-signal bg-signal/10 px-2 py-0.5 rounded-full">API</span>
                                </label>
                                <select name="wilayah" required
                                        class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                    <option value="">— Pilih Wilayah —</option>
                                    @if(isset($apiWilayah) && count($apiWilayah) > 0)
                                        @foreach($apiWilayah as $kecamatan)
                                            @php
                                                $nama = ucwords(strtolower($kecamatan['name']));
                                                if ($nama === 'Mangu Harjo') $nama = 'Manguharjo';
                                            @endphp
                                            <option value="{{ $nama }}" {{ old('wilayah') == $nama ? 'selected' : '' }}>{{ $nama }}</option>
                                        @endforeach
                                    @else
                                        <option value="Manguharjo" {{ old('wilayah') == 'Manguharjo' ? 'selected' : '' }}>Manguharjo</option>
                                        <option value="Kartoharjo" {{ old('wilayah') == 'Kartoharjo' ? 'selected' : '' }}>Kartoharjo</option>
                                        <option value="Taman" {{ old('wilayah') == 'Taman' ? 'selected' : '' }}>Taman</option>
                                    @endif
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Tingkat Keparahan</label>
                                <select name="tingkat_keparahan" required
                                        class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                    @foreach(['Ringan', 'Sedang', 'Berat', 'Kritis'] as $tk)
                                        <option value="{{ $tk }}" {{ old('tingkat_keparahan') == $tk ? 'selected' : '' }}>{{ $tk }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Catatan Klinis (Opsional)</label>
                                <textarea name="catatan_klinis" rows="3" placeholder="Gejala, riwayat kontak, tindakan medis..."
                                          class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                                 focus:ring-2 focus:ring-signal/30 outline-none resize-none">{{ old('catatan_klinis') }}</textarea>
                            </div>

                            <div class="pt-4 border-t border-mist flex gap-3">
                                <button type="submit"
                                        class="flex-1 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4"></i> Kirim Laporan
                                </button>
                                <button type="button" @click="slideOver = false"
                                        class="flex-1 py-2.5 bg-mist text-ink rounded-full text-sm font-bold hover:bg-mist/70 transition-all">
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
