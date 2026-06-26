<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * AuthController — Controller untuk autentikasi pengguna
 *
 * Menangani logika MVC untuk:
 * 1. Register: Validasi input → hash password → simpan ke DB
 * 2. Login: Validasi input → cek kredensial via Auth::attempt → redirect
 * 3. Logout: Hapus session → redirect ke halaman utama
 *
 * NOTE: Landing page dipindah ke LandingController,
 *       Dashboard dipindah ke DashboardController.
 */
class AuthController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════
    //  1. REGISTER — Menampilkan form & memproses pendaftaran
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Menampilkan halaman form registrasi.
     * Hanya bisa diakses oleh user yang BELUM login (guest).
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru.
     *
     * Alur logika:
     * 1. Validasi input (username, password, nama_lengkap) menggunakan Laravel Validator
     * 2. Cek duplikasi username secara otomatis via rule 'unique:users,username'
     * 3. Hash password menggunakan Hash::make() (Bcrypt)
     * 4. Simpan data user baru ke tabel 'users' via Model User
     * 5. Redirect ke halaman login dengan pesan sukses
     */
    public function register(Request $request)
    {
        // ── STEP 1: Validasi input dari form ──────────────────────────
        // Laravel akan otomatis redirect kembali ke form dengan error
        // jika validasi gagal (berkat fitur automatic redirect).
        $validated = $request->validate([
            'username'      => 'required|string|max:255|unique:users,username',
            'password'      => 'required|string|min:6',
            'nama_lengkap'  => 'required|string|max:255',
        ], [
            // Pesan error kustom dalam Bahasa Indonesia
            'username.required'     => 'Username wajib diisi!',
            'username.unique'       => 'Username sudah digunakan oleh staf lain!',
            'password.required'     => 'Password wajib diisi!',
            'password.min'          => 'Password minimal 6 karakter!',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi!',
        ]);

        // ── STEP 2: Simpan user baru ke database ─────────────────────
        // Password otomatis di-hash karena model User memiliki cast 'hashed'
        // pada kolom password. Namun kita tetap gunakan Hash::make()
        // secara eksplisit agar kode lebih jelas dan sesuai tugas.
        User::create([
            'username'      => $validated['username'],
            'password'      => Hash::make($validated['password']),
            'nama_lengkap'  => $validated['nama_lengkap'],
            'role'          => 'Tim Medis', // Default role sesuai skema DB
        ]);

        // ── STEP 3: Redirect ke login dengan flash message sukses ────
        return redirect('/login')->with('success', 'Akun Tim Medis berhasil dibuat! Silakan login.');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  3. LOGIN — Menampilkan form & memproses autentikasi
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Menampilkan halaman form login.
     * Hanya bisa diakses oleh user yang BELUM login (guest).
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses autentikasi login.
     *
     * Alur logika:
     * 1. Validasi input (username & password)
     * 2. Cek kredensial menggunakan Auth::attempt()
     *    - Laravel otomatis mencari user berdasarkan username
     *    - Laravel otomatis memverifikasi password dengan bcrypt (password_verify)
     * 3. Jika berhasil: Regenerate session → redirect ke /dashboard
     * 4. Jika gagal: Redirect kembali ke form login dengan pesan error
     */
    public function login(Request $request)
    {
        // ── STEP 1: Validasi input dari form ──────────────────────────
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi!',
            'password.required' => 'Password wajib diisi!',
        ]);

        // ── STEP 2: Autentikasi menggunakan Auth facade ──────────────
        // Auth::attempt() akan:
        // - SELECT * FROM users WHERE username = ? LIMIT 1
        // - Kemudian password_verify($input, $hashed_password)
        if (Auth::attempt($credentials)) {
            // ── STEP 3: Regenerate session untuk mencegah Session Fixation
            $request->session()->regenerate();

            // ── STEP 4: Redirect ke dashboard (halaman terproteksi)
            return redirect()->intended('/dashboard');
        }

        // ── STEP 5: Login gagal — kembalikan ke form dengan error ────
        return back()->withErrors([
            'username' => 'Username atau password salah!',
        ])->onlyInput('username'); // Isi ulang field username saja (bukan password)
    }

    // ═══════════════════════════════════════════════════════════════════
    //  4. LOGOUT — Menghapus session dan redirect
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Memproses logout user.
     *
     * Alur logika:
     * 1. Hapus data autentikasi dari session via Auth::logout()
     * 2. Invalidate seluruh session untuk keamanan
     * 3. Regenerate CSRF token untuk mencegah token reuse
     * 4. Redirect ke halaman utama (landing page)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    // Fungsi Khusus untuk Praktikum API
    public function loginApi(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'username' => 'required', // Harus username, bukan email
            'password' => 'required'
        ]);

        $user = \App\Models\User::where('username', $request->username)->first();

        // Cek apakah user ada dan password cocok
        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Kredensial salah'], 401);
        }

        // Generate Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }
}
