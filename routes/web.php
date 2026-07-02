<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImunisasiController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Medivest Health Logistics & Monitoring System
|--------------------------------------------------------------------------
|
| Route Groups:
| 1. Public  → Landing page (accessible by anyone)
| 2. Guest   → Login & Register (only for unauthenticated users)
| 3. Auth    → Dashboard + Business Modules (authenticated users only)
|     - Role-specific routes guarded by 'role' middleware
|
*/

// ═══════════════════════════════════════════════════════════════════════
//  1. PUBLIC ROUTE — Landing Page
// ═══════════════════════════════════════════════════════════════════════

Route::get('/', [LandingController::class, 'index'])->name('landing');

// ═══════════════════════════════════════════════════════════════════════
//  2. GUEST ROUTES — Login & Register (unauthenticated only)
// ═══════════════════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ═══════════════════════════════════════════════════════════════════════
//  3. AUTHENTICATED ROUTES — Dashboard & Business Modules
// ═══════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Dashboard Overview (all roles) ────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Monitoring Stok Obat (Farmasi) ────────────────────────────────
    Route::get('/dashboard/obat', [DashboardController::class, 'obat'])->name('dashboard.obat');
    Route::post('/obat', [ObatController::class, 'store'])->name('obat.store');
    Route::delete('/obat/{id}', [ObatController::class, 'destroy'])->name('obat.destroy');

    // ── Pelaporan Penyakit (Tenaga Medis) ─────────────────────────────
    Route::get('/dashboard/penyakit', [DashboardController::class, 'penyakit'])->name('dashboard.penyakit');
    Route::post('/pelaporan', [PelaporanController::class, 'store'])->name('pelaporan.store');

    // ── Imunisasi (Bidan) ─────────────────────────────────────────────
    Route::get('/dashboard/imunisasi', [DashboardController::class, 'imunisasi'])->name('dashboard.imunisasi');
    Route::post('/imunisasi', [ImunisasiController::class, 'store'])->name('imunisasi.store');

    // ── Manajemen User (Staf Admin) ───────────────────────────────────
    Route::get('/dashboard/users', [DashboardController::class, 'users'])->name('dashboard.users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // ── Logout ────────────────────────────────────────────────────────
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
