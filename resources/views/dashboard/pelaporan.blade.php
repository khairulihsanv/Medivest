{{--
    [ARSITEKTUR TERDISTRIBUSI] Tab Pelaporan Penyakit
    Data di halaman ini diambil dari DUA server secara real-time:
      - Server A (mysql_pusat)  → PelaporanPenyakit        (Manguharjo, Kartoharjo)
      - Server B (mysql_klinik) → PelaporanPenyakitKlinik  (Taman)
    Keduanya di-concat() di DashboardController::getPelaporanData().
--}}

{{-- ─── BLOK PEMBUKTIAN TERDISTRIBUSI (UAS) ────────────────────────────────── --}}
{{-- Blok ini menampilkan secara eksplisit bahwa data berasal dari 2 sumber berbeda. --}}
{{-- Screenshot bagian ini untuk dokumentasi laporan UAS. --}}
<div class="mb-6 rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 via-indigo-50 to-blue-50 p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-blue-900 text-sm">Bukti Sistem Basis Data Terdistribusi</h3>
            <p class="text-blue-600 text-xs">Fragmentasi Horizontal — Data diambil dari 2 server secara real-time</p>
        </div>
        {{-- Badge status koneksi --}}
        <span class="ml-auto inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-200">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            Live
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Server A --}}
        <div class="bg-white rounded-xl border border-blue-200 p-4 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                    <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 11-2 0 1 1 0 012 0zM2 13a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2zm14 1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Server A</span>
            </div>
            <p class="text-2xl font-black text-blue-800 leading-none">{{ $kasusServerA }}</p>
            <p class="text-xs text-blue-600 mt-1 font-medium">kasus</p>
            <div class="mt-2 pt-2 border-t border-blue-100">
                <p class="text-[10px] text-blue-500 font-mono">mysql_pusat · db_medivest_pusat</p>
                <p class="text-[10px] text-blue-500 font-mono">Host: 127.0.0.1 (lokal)</p>
                <p class="text-[10px] text-blue-700 font-semibold mt-1">Wilayah: Manguharjo, Kartoharjo</p>
            </div>
        </div>

        {{-- Server B --}}
        <div class="bg-white rounded-xl border border-indigo-200 p-4 shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-md bg-indigo-100 flex items-center justify-center">
                    <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 11-2 0 1 1 0 012 0zM2 13a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2zm14 1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Server B</span>
                <span class="ml-auto text-[9px] font-mono bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded">
                    {{ $hostServerB }}
                </span>
            </div>
            <p class="text-2xl font-black text-indigo-800 leading-none">{{ $kasusServerB }}</p>
            <p class="text-xs text-indigo-600 mt-1 font-medium">kasus</p>
            <div class="mt-2 pt-2 border-t border-indigo-100">
                <p class="text-[10px] text-indigo-500 font-mono">mysql_klinik · db_medivest_klinik</p>
                <p class="text-[10px] text-indigo-500 font-mono">Host: {{ $hostServerB }}</p>
                <p class="text-[10px] text-indigo-700 font-semibold mt-1">Wilayah: Taman</p>
            </div>
        </div>

        {{-- Total Gabungan --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-4 shadow-md">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-blue-100 uppercase tracking-wider">Total Gabungan</span>
            </div>
            <p class="text-3xl font-black text-white leading-none">{{ $kasusServerA + $kasusServerB }}</p>
            <p class="text-xs text-blue-200 mt-1 font-medium">kasus (real-time)</p>
            <div class="mt-2 pt-2 border-t border-white/20">
                <p class="text-[10px] text-blue-200">Server A + Server B digabung</p>
                <p class="text-[10px] text-blue-200">di PHP layer via Collection::concat()</p>
                <p class="text-[10px] text-white font-semibold mt-1">= {{ $kasusServerA }} + {{ $kasusServerB }} kasus</p>
            </div>
        </div>

    </div>
</div>

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
                    {{--
                        [ARSITEKTUR TERDISTRIBUSI] Wilayah diubah menjadi dropdown.
                        Ini penting untuk memastikan routing ke server yang tepat:
                          Manguharjo / Kartoharjo → Server A (mysql_pusat)
                          Taman                   → Server B (mysql_klinik)
                        Dengan dropdown, tidak ada risiko typo yang menyebabkan mis-routing.
                    --}}
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">
                        Lokasi / Wilayah
                        <span class="ml-1 text-[10px] font-normal text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded">
                            menentukan server penyimpanan
                        </span>
                    </label>
                    <select name="wilayah" required
                            class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600 bg-white
                                   focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
                        <option value="">— Pilih Wilayah —</option>
                        <optgroup label="🖥️ Server A (Pusat)">
                            <option value="Manguharjo" {{ old('wilayah') == 'Manguharjo' ? 'selected' : '' }}>Manguharjo</option>
                            <option value="Kartoharjo" {{ old('wilayah') == 'Kartoharjo' ? 'selected' : '' }}>Kartoharjo</option>
                        </optgroup>
                        <optgroup label="🌐 Server B (Klinik)">
                            <option value="Taman" {{ old('wilayah') == 'Taman' ? 'selected' : '' }}>Taman</option>
                        </optgroup>
                    </select>
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
                                {{-- [ARSITEKTUR TERDISTRIBUSI] Tampilkan sumber server data --}}
                                @if(in_array($laporan->wilayah, ['Taman']))
                                    <span class="ml-1 text-[9px] bg-indigo-100 text-indigo-600 px-1 rounded font-mono">Srv-B</span>
                                @else
                                    <span class="ml-1 text-[9px] bg-blue-100 text-blue-600 px-1 rounded font-mono">Srv-A</span>
                                @endif
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
