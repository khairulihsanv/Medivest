<div class="mb-8">
    <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Pelaporan Penyakit</h2>
    <p class="text-surface-500 text-sm mt-1">Formulir laporan kasus penyakit dan ringkasan data terkini.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

    <!-- ─── FORM PANEL ─────────────────────────────────────────────────────── -->
    <div class="xl:col-span-3 bg-white rounded-2xl border border-surface-200/60 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-100 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                <i data-lucide="file-plus" class="w-4 h-4 text-brand-600"></i>
            </div>
            <h3 class="font-semibold text-surface-900 text-sm">Formulir Laporan Baru</h3>
        </div>

        <form class="p-6 space-y-5" method="POST" action="{{ route('pelaporan.store') }}">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Nama Pasien</label>
                    <input type="text" name="nama_pasien" placeholder="Masukkan nama lengkap" required value="{{ old('nama_pasien') }}"
                           class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                  focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">NIK / No. Identitas</label>
                    <input type="text" name="nik" placeholder="16 digit NIK" required value="{{ old('nik') }}"
                           class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                  focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Jenis Penyakit</label>
                    <select name="jenis_penyakit" required
                            class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600 bg-white
                                   focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                        <option value="">— Pilih Penyakit —</option>
                        <option value="Demam Berdarah Dengue" {{ old('jenis_penyakit') == 'Demam Berdarah Dengue' ? 'selected' : '' }}>Demam Berdarah Dengue</option>
                        <option value="ISPA" {{ old('jenis_penyakit') == 'ISPA' ? 'selected' : '' }}>ISPA</option>
                        <option value="Diare" {{ old('jenis_penyakit') == 'Diare' ? 'selected' : '' }}>Diare</option>
                        <option value="TBC" {{ old('jenis_penyakit') == 'TBC' ? 'selected' : '' }}>TBC</option>
                        <option value="Malaria" {{ old('jenis_penyakit') == 'Malaria' ? 'selected' : '' }}>Malaria</option>
                        <option value="COVID-19" {{ old('jenis_penyakit') == 'COVID-19' ? 'selected' : '' }}>COVID-19</option>
                        <option value="Leptospirosis" {{ old('jenis_penyakit') == 'Leptospirosis' ? 'selected' : '' }}>Leptospirosis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Tanggal Diagnosis</label>
                    <input type="date" name="tgl_diagnosis" required
                           max="{{ date('Y-m-d') }}" value="{{ old('tgl_diagnosis') }}"
                           class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600
                                  focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Lokasi / Wilayah</label>
                    <input type="text" name="wilayah" placeholder="Kecamatan / Desa" required value="{{ old('wilayah') }}"
                           class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                  focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Tingkat Keparahan</label>
                    <select name="tingkat_keparahan" required
                            class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600 bg-white
                                   focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                        <option value="Ringan" {{ old('tingkat_keparahan') == 'Ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="Sedang" {{ old('tingkat_keparahan') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="Berat" {{ old('tingkat_keparahan') == 'Berat' ? 'selected' : '' }}>Berat</option>
                        <option value="Kritis" {{ old('tingkat_keparahan') == 'Kritis' ? 'selected' : '' }}>Kritis</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 mb-1.5">Catatan Klinis</label>
                <textarea name="catatan_klinis" rows="3"
                          placeholder="Gejala awal, riwayat kontak, atau tindakan medis..."
                          class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none
                                 transition-all resize-none">{{ old('catatan_klinis') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="btn-primary inline-flex items-center gap-2 px-6 py-2.5
                               bg-gradient-to-r from-brand-600 to-brand-700 text-white
                               rounded-xl text-sm font-semibold shadow-lg shadow-brand-500/25">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Kirim Laporan Kasus
                </button>
            </div>
        </form>
    </div>

    <!-- ─── RECENT REPORTS SIDEBAR ────────────────────────────────────────── -->
    <div class="xl:col-span-2 bg-white rounded-2xl border border-surface-200/60 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-amber-600"></i>
                </div>
                <h3 class="font-semibold text-surface-900 text-sm">Laporan Terkini</h3>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Real-time
            </span>
        </div>

        <div class="divide-y divide-surface-100 overflow-y-auto max-h-[520px]">
            @forelse ($dataLaporan as $laporan)
                <div class="px-6 py-4 table-row-hover transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-surface-900 truncate">
                                {{ $laporan->nama_pasien }}
                            </p>
                            <p class="text-xs text-surface-500 mt-0.5">
                                {{ $laporan->jenis_penyakit }} &mdash; {{ $laporan->wilayah }}
                            </p>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $laporan->severity_class }}">
                            {{ $laporan->tingkat_keparahan }}
                        </span>
                    </div>

                    @if ($laporan->catatan_klinis)
                        <p class="text-xs text-gray-500 italic mt-2 bg-gray-50 p-2 rounded-lg border border-gray-100">
                            "{{ $laporan->catatan_klinis }}"
                        </p>
                    @endif

                    <p class="text-[10px] text-surface-400 mt-2">
                        Diagnosis: {{ $laporan->tgl_diagnosis->format('d M Y') }}
                        &bull; NIK: {{ $laporan->nik }}
                    </p>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-sm text-surface-400">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                    <p>Belum ada riwayat pelaporan kasus penyakit.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
