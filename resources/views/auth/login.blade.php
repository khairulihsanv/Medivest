{{-- 
    Login Page — Medivest VENTRILOC Design System
    Clean white card on #efefef Mist canvas
--}}

@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="bg-mist flex items-center justify-center min-h-screen p-4 w-full">

    {{-- Login Card --}}
    <div class="w-full max-w-md relative z-10">
        <div class="bg-white rounded-lg p-10 space-y-8">

            {{-- Brand Header --}}
            <div class="text-center space-y-3">
                <div class="w-14 h-14 bg-signal rounded-lg flex items-center justify-center text-white font-display font-black text-2xl mx-auto">
                    M
                </div>
                <div>
                    <h1 class="text-2xl font-display font-bold text-ink tracking-tight mt-4">Portal Tim Medis</h1>
                    <p class="text-sm text-muted mt-1">Masuk untuk mengelola logistik obat & pantauan wabah.</p>
                </div>
            </div>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm font-semibold text-center flex items-center justify-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ url('/login') }}" class="space-y-5" id="loginForm">
                @csrf

                {{-- Username --}}
                <div class="space-y-1.5">
                    <label for="username" class="block text-xs font-bold text-muted uppercase tracking-wider">
                        Username
                    </label>
                    <div class="relative">
                        <i data-lucide="user" class="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-muted/40"></i>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            placeholder="Masukkan username"
                            class="w-full pl-11 pr-4 py-3 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                   focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-muted uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-muted/40"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                            class="w-full pl-11 pr-4 py-3 bg-mist border-0 rounded-lg text-sm text-ink placeholder-muted/40
                                   focus:ring-2 focus:ring-signal/30 focus:bg-white outline-none transition-all"
                        >
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-muted/20 text-signal focus:ring-signal/30 bg-mist">
                    <label for="remember" class="text-sm text-muted font-medium cursor-pointer">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    id="loginButton"
                    class="w-full py-3.5 mt-2 bg-signal hover:bg-signal/90 text-white font-bold
                           rounded-full text-sm transition-all transform active:scale-[0.98]
                           flex items-center justify-center gap-2"
                >
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk ke Dashboard
                </button>
            </form>

            {{-- Register Link --}}
            <div class="text-center pt-4 border-t border-mist">
                <p class="text-sm text-muted">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-signal font-semibold hover:underline transition-colors">
                        Daftar Akun Baru
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
