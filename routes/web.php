<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImunisasiController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PelaporanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Medivest Health Logistics & Monitoring System
|--------------------------------------------------------------------------
|
| Konfigurasi URL routing untuk seluruh halaman web Medivest.
| Menggunakan arsitektur MVC: setiap route mengarah ke method
| di Controller yang menangani logika bisnis.
|
| Route Groups:
| 1. Public  → Landing page (bisa diakses siapa saja, dengan data live)
| 2. Guest   → Login & Register (hanya untuk user yang BELUM login)
| 3. Auth    → Dashboard + Modul Bisnis (hanya untuk user yang SUDAH login)
|
*/

// ═══════════════════════════════════════════════════════════════════════
//  1. PUBLIC ROUTE — Halaman Landing Page dengan data real-time
// ═══════════════════════════════════════════════════════════════════════

Route::get('/', [LandingController::class, 'index'])->name('landing');

// ═══════════════════════════════════════════════════════════════════════
//  2. GUEST ROUTES — Hanya bisa diakses user yang BELUM login
//     Jika user sudah login dan mengakses /login atau /register,
//     middleware 'guest' akan redirect ke /dashboard secara otomatis.
// ═══════════════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {

    // ── Login ─────────────────────────────────────────────────────────
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // ── Register ──────────────────────────────────────────────────────
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ═══════════════════════════════════════════════════════════════════════
//  3. AUTHENTICATED ROUTES — Wajib login untuk mengakses
//     Jika user belum login dan mengakses /dashboard,
//     middleware 'auth' akan redirect ke /login secara otomatis.
// ═══════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Dashboard (Tab-based: overview, simosoba, pelaporan, imunisasi)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Modul SiMoSoBa (Obat CRUD) ───────────────────────────────────
    Route::post('/obat', [ObatController::class, 'store'])->name('obat.store');
    Route::delete('/obat/{id}', [ObatController::class, 'destroy'])->name('obat.destroy');

    // ── Modul Pelaporan Penyakit ──────────────────────────────────────
    Route::post('/pelaporan', [PelaporanController::class, 'store'])->name('pelaporan.store');

    // ── Modul Imunisasi ──────────────────────────────────────────────
    Route::post('/imunisasi', [ImunisasiController::class, 'store'])->name('imunisasi.store');

    // ── Logout ────────────────────────────────────────────────────────
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
