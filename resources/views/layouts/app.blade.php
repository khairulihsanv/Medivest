{{-- 
    Layout Utama — Medivest Health Logistics & Monitoring System
    
    Base layout yang digunakan oleh seluruh halaman Blade.
    Menyediakan: Tailwind CSS (CDN), Google Fonts, CSRF meta tag,
    dan section @yield untuk konten halaman.
--}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- CSRF Token untuk keamanan form POST --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Medivest — Sistem Informasi Pemantauan & Pencegahan Penyakit serta Logistik Medis Terpadu.')">

    <title>@yield('title', 'Medivest') — Medivest</title>

    {{-- Tailwind CSS via CDN (sesuai pendekatan project native PHP) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts: Plus Jakarta Sans (konsisten dengan design native) --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Animasi kustom ──────────────────────────────────────── */
        .fade-in {
            animation: fadeIn 0.6s ease-out both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(37,99,235,0.12) 0%, transparent 70%);
        }

        /* ── Glassmorphism card effect ───────────────────────────── */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
    @yield('content')

    {{-- Inisialisasi Lucide Icons --}}
    <script>lucide.createIcons();</script>

    @stack('scripts')
</body>

</html>
