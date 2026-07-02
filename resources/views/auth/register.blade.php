{{-- 
    Register Page — Medivest VENTRILOC Design System
    Clean white card on #efefef Mist canvas with role selector
--}}

@extends('layouts.app')

@section('title', 'Registrasi Akun Baru')

@section('content')

<div class="bg-mist flex items-center justify-center min-h-screen p-4 w-full">

    {{-- Register Card --}}
    <div class="w-full max-w-md relative z-10">
        <div class="bg-white rounded-lg p-10 space-y-7">

            {{-- Brand Header --}}
            <div class="text-center space-y-2">
                <div class="w-14 h-14 bg-ink rounded-lg flex items-center justify-center text-white font-display font-black text-2xl mx-auto">
                    M
                </div>
                <h1 class="text-2xl font-display font-bold text-ink tracking-tight">Registrasi Akun Baru</h1>
                <p class="text-sm text-muted">Buat kredensial internal untuk klaster kesehatan.</p>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-xs font-semibold">
                    <div class="flex items-center gap-2 mb-1">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        <span>Terdapat kesalahan pada form:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Register Form --}}
            <form method="POST" action="{{ url('/register') }}" class="space-y-4" id="registerForm">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="space-y-1.5">
                    <label for="nama_lengkap" class="block text-xs font-bold text-muted uppercase tracking-wider">
                        Nama Lengkap & Gelar
                    </label>
                    <input
                        type="text"
                        id="nama_lengkap"
                        name="nama_lengkap"
                        value="{{ old('nama_lengkap') }}"
                        required
                        autofocus
                        placeholder="Contoh: dr. Eko Prasetyo"
                        class="w-full px-4 py-3 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                               focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all"
                    >
                </div>

                {{-- Username --}}
                <div class="space-y-1.5">
                    <label for="username" class="block text-xs font-bold text-muted uppercase tracking-wider">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        placeholder="Huruf kecil tanpa spasi"
                        class="w-full px-4 py-3 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                               focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all"
                    >
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-muted uppercase tracking-wider">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-3 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                               focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all"
                    >
                </div>

                {{-- Role Selector --}}
                <div class="space-y-1.5">
                    <label for="role" class="block text-xs font-bold text-muted uppercase tracking-wider">
                        Peran / Role
                    </label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full px-4 py-3 bg-mist border-0 rounded-lg text-sm text-ink
                               focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all appearance-none cursor-pointer"
                    >
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>— Pilih Role —</option>
                        <option value="Staf Admin" {{ old('role') == 'Staf Admin' ? 'selected' : '' }}>Staf Admin</option>
                        <option value="Tenaga Medis" {{ old('role') == 'Tenaga Medis' ? 'selected' : '' }}>Tenaga Medis</option>
                        <option value="Farmasi" {{ old('role') == 'Farmasi' ? 'selected' : '' }}>Farmasi</option>
                        <option value="Bidan" {{ old('role') == 'Bidan' ? 'selected' : '' }}>Bidan</option>
                    </select>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    id="registerButton"
                    class="w-full py-3.5 mt-2 bg-ink hover:bg-ink/90 text-white font-bold
                           rounded-full text-sm transition-all transform active:scale-[0.98]
                           flex items-center justify-center gap-2"
                >
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Daftarkan Akun
                </button>
            </form>

            {{-- Login Link --}}
            <div class="text-center pt-3 border-t border-mist">
                <p class="text-sm text-muted">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-signal font-semibold hover:underline transition-colors">
                        Masuk Portal
                    </a>
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-muted/60 mt-8">
            &copy; {{ date('Y') }} Medivest — UNS PSDKU Madiun
        </p>
    </div>
</div>

@endsection
