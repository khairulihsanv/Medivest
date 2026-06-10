<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Antrean Imunisasi</h2>
        <p class="text-surface-500 text-sm mt-1">Sistem penjadwalan & pengingat otomatis via WhatsApp Bot.</p>
    </div>
    <button onclick="openModal('addQueueModal')"
            class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-brand-500/25">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Antrean
    </button>
</div>

<!-- ─── QUEUE TABLE ─────────────────────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-surface-200/60 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-50/80">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Jadwal</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Nama Anak</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Usia</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Vaksin / Dosis</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Orang Tua</th>
                    <th class="text-center px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Status WA</th>
                    <th class="text-center px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Aksi Bot</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100">
                @forelse ($dataAntrean as $row)
                    @php
                        $badge = $row->status_badge;
                    @endphp
                    <tr class="table-row-hover">
                        <td class="px-6 py-3.5 text-surface-900 font-medium whitespace-nowrap">
                            <i data-lucide="calendar-clock" class="w-3.5 h-3.5 inline-block text-surface-400 mr-1.5"></i>
                            {{ $row->tgl_jadwal->format('d M Y') }}
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-surface-900">
                            {{ $row->nama_anak }}
                        </td>
                        <td class="px-6 py-3.5 text-surface-600">
                            {{ $row->usia_bulan }} bulan
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex flex-col">
                                <span class="font-medium text-surface-900">{{ $row->jenis_vaksin }}</span>
                                <span class="text-xs text-surface-500">Dosis ke-{{ $row->dosis_ke }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex flex-col">
                                <span class="text-surface-900">{{ $row->nama_orang_tua }}</span>
                                <span class="text-xs text-brand-600">{{ $row->no_hp }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold border {{ $badge['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                {{ $row->status_reminder }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <a href="{{ $row->whatsapp_url }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#25D366] hover:bg-[#128C7E] text-white rounded-lg text-xs font-semibold transition-colors shadow-sm">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Kirim
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-surface-400">
                            <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                            <p>Belum ada jadwal imunisasi dalam antrean.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ─── MODAL: TAMBAH ANTREAN ───────────────────────────────────────────── -->
<div id="addQueueModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addQueueModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-surface-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i data-lucide="calendar-plus" class="w-4 h-4 text-brand-600"></i>
                    </div>
                    <h3 class="font-semibold text-surface-900">Jadwal Imunisasi Baru</h3>
                </div>
                <button type="button" onclick="closeModal('addQueueModal')"
                        class="p-1.5 rounded-lg hover:bg-surface-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-surface-400"></i>
                </button>
            </div>

            <form class="p-6 space-y-4" method="POST" action="{{ route('imunisasi.store') }}">
                @csrf

                @if ($errors->any())
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            openModal('addQueueModal');
                        });
                    </script>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Nama Anak</label>
                        <input type="text" name="nama_anak" placeholder="Nama lengkap anak" required value="{{ old('nama_anak') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Usia (Bulan)</label>
                        <input type="number" name="usia_bulan" placeholder="0" min="0" required value="{{ old('usia_bulan') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Nama Orang Tua</label>
                        <input type="text" name="nama_orang_tua" placeholder="Nama ayah / ibu" required value="{{ old('nama_orang_tua') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">No. WhatsApp</label>
                        <input type="text" name="no_hp" placeholder="0812345..." required value="{{ old('no_hp') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Jenis Vaksin</label>
                        <select name="jenis_vaksin" required
                                class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600 bg-white
                                       focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                            <option value="BCG" {{ old('jenis_vaksin') == 'BCG' ? 'selected' : '' }}>BCG</option>
                            <option value="DPT-HB-Hib" {{ old('jenis_vaksin') == 'DPT-HB-Hib' ? 'selected' : '' }}>DPT-HB-Hib</option>
                            <option value="Campak-Rubella" {{ old('jenis_vaksin') == 'Campak-Rubella' ? 'selected' : '' }}>Campak-Rubella</option>
                            <option value="Polio" {{ old('jenis_vaksin') == 'Polio' ? 'selected' : '' }}>Polio</option>
                            <option value="Hepatitis B" {{ old('jenis_vaksin') == 'Hepatitis B' ? 'selected' : '' }}>Hepatitis B</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Dosis Ke</label>
                        <input type="number" name="dosis_ke" placeholder="1" min="1" max="4" required value="{{ old('dosis_ke') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Tanggal Jadwal</label>
                    <input type="date" name="tgl_jadwal" required value="{{ old('tgl_jadwal') }}"
                           class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600
                                  focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="btn-primary flex-1 px-6 py-2.5 bg-gradient-to-r from-brand-600 to-brand-700 text-white
                                   rounded-xl text-sm font-semibold shadow-lg text-center">
                        Simpan Jadwal
                    </button>
                    <button type="button" onclick="closeModal('addQueueModal')"
                            class="flex-1 px-6 py-2.5 border border-surface-200 text-surface-600 rounded-xl
                                   text-sm font-medium hover:bg-surface-50 text-center">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
