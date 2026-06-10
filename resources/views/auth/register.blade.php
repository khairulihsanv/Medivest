{{-- 
    Register Page — Medivest Health Logistics & Monitoring System

    Form registrasi Tim Medis baru dengan Tailwind CSS.
    Mengirim POST request ke /register yang ditangani oleh AuthController@register.
    
    Dimigrasi dari: /Medivest/register.php (native PHP)
--}}

@extends('layouts.app')

@section('title', 'Registrasi Tim Medis Baru')

@section('content')

<div class="bg-slate-900 flex items-center justify-center min-h-screen p-4 w-full">

    {{-- ─── DECORATIVE BACKGROUND ELEMENTS ──────────────────────────── --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-80 h-80 bg-emerald-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-80 h-80 bg-teal-600/10 rounded-full blur-3xl"></div>
    </div>

    {{-- ─── REGISTER CARD ───────────────────────────────────────────── --}}
    <div class="w-full max-w-md relative fade-in">
        <div class="bg-white rounded-2xl shadow-2xl shadow-black/20 p-8 space-y-6 border border-slate-100">

            {{-- ── Brand Header ──────────────────────────────────────── --}}
            <div class="text-center space-y-2">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl flex items-center justify-center text-white font-black text-2xl mx-auto shadow-lg shadow-emerald-500/30">
                    M
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Registrasi Tim Medis Baru</h1>
                <p class="text-sm text-slate-500">Buat akun kredensial internal klaster kesehatan.</p>
            </div>

            {{-- ── Validation Error Messages ─────────────────────────── --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-semibold fade-in">
                    <div class="flex items-center gap-2 mb-1">
                        <span>⚠️</span>
                        <span>Terdapat kesalahan pada form:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── Success Message ───────────────────────────────────── --}}
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-semibold text-center fade-in">
                    🎉 {{ session('success') }}
                </div>
            @endif

            {{-- ── Register Form ─────────────────────────────────────── --}}
            <form method="POST" action="{{ url('/register') }}" class="space-y-4" id="registerForm">
                @csrf {{-- Token CSRF wajib untuk setiap form POST di Laravel --}}

                {{-- Input: Nama Lengkap & Gelar --}}
                <div>
                    <label for="nama_lengkap" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
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
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm 
                               focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 
                               outline-none transition-all bg-slate-50/50 hover:bg-white"
                    >
                    @error('nama_lengkap')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input: Username --}}
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username Akun
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        placeholder="Gunakan huruf kecil tanpa spasi"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm 
                               focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 
                               outline-none transition-all bg-slate-50/50 hover:bg-white"
                    >
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input: Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm 
                               focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 
                               outline-none transition-all bg-slate-50/50 hover:bg-white"
                    >
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    id="registerButton"
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold 
                           rounded-xl text-sm shadow-lg shadow-emerald-500/20 transition-all 
                           transform active:scale-[0.98] hover:-translate-y-0.5
                           flex items-center justify-center gap-2"
                >
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Daftarkan Anggota Baru
                </button>
            </form>

            {{-- ── Link ke Login ──────────────────────────────────────── --}}
            <div class="text-center pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    Sudah punya kredensial? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline hover:text-blue-700 transition-colors">
                        Masuk Portal
                    </a>
                </p>
            </div>
        </div>

        {{-- ── Footer Credit ─────────────────────────────────────────── --}}
        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; {{ date('Y') }} Medivest — UNS PSDKU Madiun
        </p>
    </div>
    </div>

@endsection
