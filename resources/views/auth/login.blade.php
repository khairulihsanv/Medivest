{{-- 
    Login Page — Medivest Health Logistics & Monitoring System

    Form login untuk Tim Medis dengan Tailwind CSS.
    Mengirim POST request ke /login yang ditangani oleh AuthController@login.
    
    Dimigrasi dari: /Medivest/login.php (native PHP)
--}}

@extends('layouts.app')

@section('title', 'Login Tim Medis')

@section('content')

<div class="bg-slate-900 flex items-center justify-center min-h-screen p-4 w-full">

    {{-- ─── DECORATIVE BACKGROUND ELEMENTS ──────────────────────────── --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-cyan-600/10 rounded-full blur-3xl"></div>
    </div>

    {{-- ─── LOGIN CARD ──────────────────────────────────────────────── --}}
    <div class="w-full max-w-md relative fade-in">
        <div class="bg-white rounded-2xl shadow-2xl shadow-black/20 p-8 space-y-6 border border-slate-100">

            {{-- ── Brand Header ──────────────────────────────────────── --}}
            <div class="text-center space-y-2">
                <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center text-white font-black text-2xl mx-auto shadow-lg shadow-blue-500/30">
                    M
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Portal Tim Medis</h1>
                <p class="text-sm text-slate-500">Masuk untuk mengelola logistik obat dan pantauan wabah.</p>
            </div>

            {{-- ── Error Messages (dari Laravel validation) ──────────── --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-semibold text-center fade-in">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            {{-- ── Success Message (dari redirect register) ──────────── --}}
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-semibold text-center fade-in">
                    🎉 {{ session('success') }}
                </div>
            @endif

            {{-- ── Login Form ────────────────────────────────────────── --}}
            <form method="POST" action="{{ url('/login') }}" class="space-y-4" id="loginForm">
                @csrf {{-- Token CSRF wajib untuk setiap form POST di Laravel --}}

                {{-- Input: Username --}}
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        placeholder="Masukkan username"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm 
                               focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                               outline-none transition-all bg-slate-50/50 hover:bg-white"
                    >
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
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm 
                               focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 
                               outline-none transition-all bg-slate-50/50 hover:bg-white"
                    >
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    id="loginButton"
                    class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold 
                           rounded-xl text-sm shadow-lg shadow-blue-500/20 transition-all 
                           transform active:scale-[0.98] hover:-translate-y-0.5 
                           flex items-center justify-center gap-2"
                >
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk ke Dashboard
                </button>
            </form>

            {{-- ── Link ke Register ──────────────────────────────────── --}}
            <div class="text-center pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    Belum punya akun medis? 
                    <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline hover:text-blue-700 transition-colors">
                        Daftar Akun Baru
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
