{{--
    Manajemen User — Medivest VENTRILOC Design System
    System configuration panel with CRUD slide-over + mock system status
--}}

@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')

<div x-data="{ slideOver: false, editMode: false, editId: null, editUsername: '', editNama: '', editRole: '' }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-display font-bold text-ink tracking-tight">Manajemen User</h2>
            <p class="text-muted text-sm mt-1">Konfigurasi sistem dan pengelolaan akun pengguna.</p>
        </div>
        <button @click="slideOver = true; editMode = false; editId = null; editUsername = ''; editNama = ''; editRole = '';"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-colors">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah User
        </button>
    </div>

    {{-- ─── SYSTEM STATUS CARDS ───────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        @foreach($systemStatus as $key => $sys)
            <div class="bg-white rounded-lg p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-lg bg-mist flex items-center justify-center">
                        @if($key === 'database')
                            <i data-lucide="database" class="w-[18px] h-[18px] text-muted"></i>
                        @elseif($key === 'workflow')
                            <i data-lucide="workflow" class="w-[18px] h-[18px] text-muted"></i>
                        @else
                            <i data-lucide="globe" class="w-[18px] h-[18px] text-muted"></i>
                        @endif
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold rounded-full px-2.5 py-1
                                 {{ $sys['online'] ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $sys['online'] ? 'bg-emerald-500' : 'bg-red-500 pulse-dot' }}"></span>
                        {{ $sys['status'] }}
                    </span>
                </div>
                <p class="font-display font-bold text-ink text-sm">{{ $sys['label'] }}</p>
                <p class="text-xs text-muted/60 mt-0.5">
                    @if(isset($sys['region'])) {{ $sys['region'] }}
                    @elseif(isset($sys['uptime'])) Uptime: {{ $sys['uptime'] }}
                    @elseif(isset($sys['latency'])) Latency: {{ $sys['latency'] }}
                    @endif
                </p>
            </div>
        @endforeach
    </div>

    {{-- ─── USER TABLE ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-mist">
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">ID</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Nama Lengkap</th>
                        <th class="text-left px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Username</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Role</th>
                        <th class="text-center px-6 py-4 text-[10px] font-bold text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allUsers as $user)
                        <tr class="border-b border-mist/60 hover:bg-mist/40 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-muted/60">#{{ $user->id_user }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-mist flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-muted">{{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-bold text-ink">{{ $user->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-muted font-medium">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $roleColors = [
                                        'Staf Admin'   => 'text-purple-700 bg-purple-50',
                                        'Tenaga Medis' => 'text-blue-700 bg-blue-50',
                                        'Farmasi'      => 'text-amber-700 bg-amber-50',
                                        'Bidan'        => 'text-emerald-700 bg-emerald-50',
                                    ];
                                    $rClass = $roleColors[$user->role] ?? 'text-muted bg-mist';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold {{ $rClass }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="slideOver = true; editMode = true; editId = {{ $user->id_user }}; editUsername = '{{ $user->username }}'; editNama = '{{ addslashes($user->nama_lengkap) }}'; editRole = '{{ $user->role }}';"
                                            class="p-2 rounded-full hover:bg-mist text-muted/40 hover:text-ink transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    <form method="POST" action="{{ route('users.destroy', $user->id_user) }}"
                                          onsubmit="return confirm('Hapus user {{ $user->nama_lengkap }}?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-full hover:bg-red-50 text-muted/40 hover:text-red-500 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-muted/40">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 text-muted/20"></i>
                                <p>Belum ada data user.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{--  RIGHT-SIDE SLIDE-OVER (Create & Edit)                        --}}
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
                                <i data-lucide="user-cog" class="w-[18px] h-[18px] text-ink"></i>
                            </div>
                            <h3 class="font-display font-bold text-ink text-lg" x-text="editMode ? 'Edit User' : 'Tambah User Baru'"></h3>
                        </div>
                        <button @click="slideOver = false" class="p-2 rounded-full hover:bg-mist transition-colors text-muted/40 hover:text-ink">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    {{-- Form --}}
                    <div class="flex-1 overflow-y-auto p-6 slim-scrollbar">

                        {{-- CREATE FORM --}}
                        <form x-show="!editMode" method="POST" action="{{ route('users.store') }}" class="space-y-5">
                            @csrf

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" placeholder="Nama lengkap & gelar" required value="{{ old('nama_lengkap') }}"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40 focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Username</label>
                                <input type="text" name="username" placeholder="username" required value="{{ old('username') }}"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40 focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Password</label>
                                <input type="password" name="password" placeholder="Min. 6 karakter" required
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40 focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Role</label>
                                <select name="role" required
                                        class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                    <option value="">— Pilih Role —</option>
                                    @foreach(['Staf Admin', 'Tenaga Medis', 'Farmasi', 'Bidan'] as $r)
                                        <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 border-t border-mist flex gap-3">
                                <button type="submit" class="flex-1 py-2.5 bg-ink hover:bg-ink/90 text-white rounded-full text-sm font-bold transition-all text-center">
                                    Simpan User
                                </button>
                                <button type="button" @click="slideOver = false" class="flex-1 py-2.5 bg-mist text-ink rounded-full text-sm font-bold hover:bg-mist/70 transition-all text-center">
                                    Batal
                                </button>
                            </div>
                        </form>

                        {{-- EDIT FORM --}}
                        <form x-show="editMode" method="POST" :action="'/users/' + editId" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" required x-model="editNama"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Username</label>
                                <input type="text" name="username" required x-model="editUsername"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Password Baru (opsional)</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                                       class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40 focus:ring-2 focus:ring-signal/30 outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-wider">Role</label>
                                <select name="role" required x-model="editRole"
                                        class="w-full px-4 py-2.5 bg-mist border-0 rounded-lg text-sm text-ink focus:ring-2 focus:ring-signal/30 outline-none">
                                    @foreach(['Staf Admin', 'Tenaga Medis', 'Farmasi', 'Bidan'] as $r)
                                        <option value="{{ $r }}">{{ $r }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 border-t border-mist flex gap-3">
                                <button type="submit" class="flex-1 py-2.5 bg-signal hover:bg-signal/90 text-white rounded-full text-sm font-bold transition-all text-center">
                                    Perbarui User
                                </button>
                                <button type="button" @click="slideOver = false" class="flex-1 py-2.5 bg-mist text-ink rounded-full text-sm font-bold hover:bg-mist/70 transition-all text-center">
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
