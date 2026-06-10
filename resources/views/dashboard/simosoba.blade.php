<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-surface-900 tracking-tight">SiMoSoBa</h2>
        <p class="text-surface-500 text-sm mt-1">Sistem Monitoring Stok Barang — Manajemen inventaris obat &amp; alkes.</p>
    </div>
    <button onclick="openModal('addStockModal')"
            class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-brand-500/25">
        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Stok
    </button>
</div>

<!-- ─── INTELLIGENT SUPPLY ENGINE PANEL ─────────────────────────────────── -->
<div class="bg-gradient-to-r from-brand-900 to-slate-900 rounded-2xl p-6 text-white mb-6 border border-brand-700/40 shadow-xl">
    <div class="flex items-center gap-2 mb-3">
        <span class="flex h-2 w-2 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        <h4 class="text-xs font-bold tracking-wider uppercase text-brand-400">
            🤖 Medivest Intelligent Supply Engine (Real-time Prediction)
        </h4>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
        <!-- Data Feeder Column -->
        <div class="border-r border-white/10 pr-4">
            <p class="text-xs text-slate-400 font-medium">Data Feeding (Modul Pelaporan)</p>
            <p class="text-lg font-bold mt-1 text-amber-400">{{ $kasus_dbd }} Kasus DBD Terdeteksi</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Sumber: Live Surveilans Penyakit.</p>
        </div>
        <!-- Safety Stock Column -->
        <div class="border-r border-white/10 pr-4">
            <p class="text-xs text-slate-400 font-medium">Batas Aman Dinamis (SiMoSoBa)</p>
            <p class="text-lg font-bold mt-1">Safety Stock:
                <span class="text-emerald-400">{{ $analisis_stok['safety_stock'] }}</span> pcs
            </p>
            <p class="text-[11px] text-slate-400 mt-0.5">
                Reorder Point (ROP): {{ $analisis_stok['reorder_point'] }} pcs
            </p>
        </div>
        <!-- Action Column -->
        <div>
            <p class="text-xs text-slate-400 font-medium">Rekomendasi Aksi Konkrit</p>
            <p class="text-xs font-semibold mt-1.5 {{ $analisis_stok['perlu_order'] ? 'text-red-400 animate-pulse' : 'text-emerald-400' }}">
                {{ $analisis_stok['tindakan'] }}
            </p>
            @if ($analisis_stok['perlu_order'] && $dataObat->isNotEmpty())
                <button type="button"
                        onclick="alert('Draf Purchase Order (PO) Elektronik sukses dikirim ke Supplier via API!')"
                        class="mt-2 text-[11px] bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded-lg transition-all shadow-md block">
                    Kirim PO Digital ke Supplier
                </button>
            @endif
        </div>
    </div>
</div>

<!-- ─── SEARCH & FILTER BAR ────────────────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-surface-200/60 shadow-sm p-4 mb-5 flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-surface-400"></i>
        <input type="text" id="searchObat" placeholder="Cari nama obat atau alkes..."
               oninput="filterTableObat(this.value)"
               class="w-full pl-10 pr-4 py-2.5 border border-surface-200 rounded-xl text-sm
                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none transition-all">
    </div>
    <select id="filterStatus" onchange="filterTableObat(document.getElementById('searchObat').value)"
            class="px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600 bg-white
                   focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
        <option value="">Semua Status</option>
        <option value="aman">Aman</option>
        <option value="rendah">Rendah</option>
        <option value="kritis">Kritis</option>
    </select>
</div>

<!-- ─── MAIN DATA TABLE ───────────────────────────────────────────────────── -->
<div class="bg-white rounded-2xl border border-surface-200/60 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="tabelObat">
            <thead>
                <tr class="bg-surface-50/80">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Kode</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-center px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Stok</th>
                    <th class="text-center px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Kadaluarsa</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Sisa Hari</th>
                    <th class="text-center px-6 py-3.5 text-xs font-semibold text-surface-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-100" id="tbodyObat">
                @forelse ($dataObat as $row)
                    @php
                        $sisa_hari = $row->hitungSisaHariKadaluarsa();
                        $status = $row->getStatusStok();
                    @endphp
                    <tr class="table-row-hover"
                        data-nama="{{ strtolower($row->nama_obat) }}"
                        data-status="{{ strtolower($status['label']) }}">
                        <td class="px-6 py-3.5 font-mono text-xs text-surface-500">{{ $row->kode }}</td>
                        <td class="px-6 py-3.5 font-medium text-surface-900">{{ $row->nama_obat }}</td>
                        <td class="px-6 py-3.5 text-surface-600">{{ $row->jenis_obat }}</td>
                        <td class="px-6 py-3.5 text-center font-semibold text-surface-900">{{ number_format($row->stok, 0, ',', '.') }}</td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $status['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-surface-600">
                            {{ $row->tgl_kadaluarsa ? $row->tgl_kadaluarsa->format('d M Y') : '—' }}
                        </td>
                        <td class="px-6 py-3.5">
                            @if ($sisa_hari < 0)
                                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Kadaluarsa!</span>
                            @elseif ($sisa_hari <= 30)
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">{{ $sisa_hari }} hari</span>
                            @else
                                <span class="text-xs text-surface-400">{{ $sisa_hari }} hari</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <form method="POST" action="{{ route('obat.destroy', $row->id_obat) }}"
                                  onsubmit="return confirm('Hapus obat ini secara permanen?');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg hover:bg-red-50 text-surface-400 hover:text-red-600 transition-colors"
                                        title="Hapus Obat">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-surface-400">
                            <i data-lucide="package-open" class="w-8 h-8 mx-auto mb-2 text-surface-300"></i>
                            <p>Belum ada data obat di database. Tambahkan stok pertama!</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ─── MODAL: TAMBAH STOK ───────────────────────────────────────────────── -->
<div id="addStockModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addStockModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-surface-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                        <i data-lucide="package-plus" class="w-4 h-4 text-brand-600"></i>
                    </div>
                    <h3 class="font-semibold text-surface-900">Tambah Stok Baru</h3>
                </div>
                <button type="button" onclick="closeModal('addStockModal')"
                        class="p-1.5 rounded-lg hover:bg-surface-100 transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-surface-400"></i>
                </button>
            </div>

            <form class="p-6 space-y-4" method="POST" action="{{ route('obat.store') }}">
                @csrf

                @if ($errors->any())
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            openModal('addStockModal');
                        });
                    </script>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Kode Barang</label>
                        <input type="text" placeholder="Auto-Increment" disabled
                               class="w-full px-4 py-2.5 bg-gray-100 border border-surface-200 rounded-xl text-sm cursor-not-allowed text-gray-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Kategori</label>
                        <select name="jenis_obat" required
                                class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600 bg-white
                                       focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                            <option value="Tablet" {{ old('jenis_obat') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Vaksin" {{ old('jenis_obat') == 'Vaksin' ? 'selected' : '' }}>Vaksin</option>
                            <option value="Sirup" {{ old('jenis_obat') == 'Sirup' ? 'selected' : '' }}>Sirup</option>
                            <option value="Infus" {{ old('jenis_obat') == 'Infus' ? 'selected' : '' }}>Infus</option>
                            <option value="Kapsul" {{ old('jenis_obat') == 'Kapsul' ? 'selected' : '' }}>Kapsul</option>
                            <option value="Alkes" {{ old('jenis_obat') == 'Alkes' ? 'selected' : '' }}>Alat Kesehatan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Nama Barang</label>
                    <input type="text" name="nama_obat" placeholder="Nama obat / alat kesehatan" required value="{{ old('nama_obat') }}"
                           class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                  focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Jumlah Stok</label>
                        <input type="number" name="stok" placeholder="0" min="1" required value="{{ old('stok') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Harga Beli (Rp)</label>
                        <input type="number" name="harga_beli" placeholder="0" min="0" required value="{{ old('harga_beli') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Kadaluarsa</label>
                        <input type="date" name="tgl_kadaluarsa" required value="{{ old('tgl_kadaluarsa') }}"
                               class="w-full px-4 py-2.5 border border-surface-200 rounded-xl text-sm text-surface-600
                                      focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 outline-none">
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="btn-primary flex-1 px-6 py-2.5 bg-gradient-to-r from-brand-600 to-brand-700
                                   text-white rounded-xl text-sm font-semibold shadow-lg text-center">
                        Simpan Stok
                    </button>
                    <button type="button" onclick="closeModal('addStockModal')"
                            class="flex-1 px-6 py-2.5 border border-surface-200 text-surface-600 rounded-xl
                                   text-sm font-medium hover:bg-surface-50 text-center">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
</script>
