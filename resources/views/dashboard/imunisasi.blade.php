{{--
    Imunisasi — Medivest VENTRILOC Design System
    Digital patient registry with WA pill buttons + slide-over drawer
--}}

@extends('layouts.app')

@section('title', 'Imunisasi')

@section('content')

<div x-data="{ slideOver: false }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-display font-bold text-ink tracking-tight">Registri Imunisasi</h2>
            <p class="text-muted text-sm mt-1">Buku KIA digital — penjadwalan & pengingat otomatis via WhatsApp.</p>
        </div>
        <button @click="slideOver = true"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Jadwal
        </button>
    </div>

    {{-- ─── PATIENT REGISTRY TABLE ────────────────────────────────── --}}
    <div class="bg-white rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-mist">
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Jadwal</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Nama Anak</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Usia</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Vaksin / Dosis</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Orang Tua</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataAntrean as $row)
                        @php
                            $isSent = ($row->status_reminder ?? '') !== 'Belum Dikirim';
                        @endphp
                        <tr class="border-b border-mist/60 hover:bg-mist/40 transition-colors">
                            <td class="px-6 py-4 text-ink font-bold whitespace-nowrap text-sm">
                                {{ $row->tgl_jadwal->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-ink">{{ $row->nama_anak }}</td>
                            <td class="px-6 py-4 text-muted font-medium">{{ $row->usia_bulan }} bln</td>
                            <td class="px-6 py-4 text-muted font-medium">
                                {{ $row->jenis_vaksin }}
                                <span class="text-muted/40 text-xs ml-1">(Dosis ke-{{ $row->dosis_ke }})</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-ink font-medium text-sm">{{ $row->nama_orang_tua }}</p>
                                <p class="text-xs text-signal font-medium">{{ $row->no_hp }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isSent)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold text-emerald-700 bg-emerald-50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Sudah Diingatkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold text-signal bg-signal/10">
                                        <span class="w-1.5 h-1.5 rounded-full bg-signal pulse-dot"></span>
                                        Belum Dikirim
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ $row->whatsapp_url }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold transition-all
                                          bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                        <path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a7.963 7.963 0 01-4.105-1.132l-.295-.176-2.847.846.846-2.847-.176-.295A7.963 7.963 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/>
                                    </svg>
                                    WA Reminder
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-sm text-muted/40">
                                <i data-lucide="calendar-x" class="w-10 h-10 mx-auto mb-3 text-muted/20"></i>
                                <p>Belum ada jadwal imunisasi dalam antrean.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                                <i data-lucide="calendar-plus" class="w-[18px] h-[18px] text-ink"></i>
                            </div>
                            <h3 class="font-display font-bold text-ink text-lg">Jadwal Imunisasi Baru</h3>
                        </div>
                        <button @click="slideOver = false" class="p-2 rounded-full hover:bg-mist transition-colors text-muted/40 hover:text-ink">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <div class="flex-1 overflow-y-auto p-6 slim-scrollbar">
                        <form method="POST" action="{{ route('imunisasi.store') }}" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Nama Anak</label>
                                    <input type="text" name="nama_anak" placeholder="Nama lengkap" required value="{{ old('nama_anak') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                                  focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Usia (Bulan)</label>
                                    <input type="number" name="usia_bulan" placeholder="0" min="0" required value="{{ old('usia_bulan') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Nama Orang Tua</label>
                                    <input type="text" name="nama_orang_tua" placeholder="Nama ayah/ibu" required value="{{ old('nama_orang_tua') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                                  focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">No. WhatsApp</label>
                                    <input type="text" name="no_hp" placeholder="0812345..." required value="{{ old('no_hp') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                                  focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="col-span-2 space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Jenis Vaksin</label>
                                    <select name="jenis_vaksin" required
                                            class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                        @foreach(['BCG', 'DPT-HB-Hib', 'Campak-Rubella', 'Polio', 'Hepatitis B'] as $v)
                                            <option value="{{ $v }}" {{ old('jenis_vaksin') == $v ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Dosis Ke</label>
                                    <input type="number" name="dosis_ke" placeholder="1" min="1" max="4" required value="{{ old('dosis_ke') }}"
                                           class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Tanggal Jadwal</label>
                                <input type="date" name="tgl_jadwal" required value="{{ old('tgl_jadwal') }}"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="pt-4 border-t border-mist flex gap-3">
                                <button type="submit"
                                        class="flex-1 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-all text-center">
                                    Simpan Jadwal
                                </button>
                                <button type="button" @click="slideOver = false"
                                        class="flex-1 py-2.5 bg-mist text-ink rounded-full text-sm font-bold hover:bg-mist/70 transition-all text-center">
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
