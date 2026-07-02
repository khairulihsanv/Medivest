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
 */
class AuthController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════
    //  1. REGISTER — Menampilkan form & memproses pendaftaran
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Menampilkan halaman form registrasi.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru.
     *
     * Alur logika:
     * 1. Validasi input (username, password, nama_lengkap, role)
     * 2. Hash password menggunakan Hash::make() (Bcrypt)
     * 3. Simpan data user baru ke tabel 'users' via Model User
     * 4. Redirect ke halaman login dengan pesan sukses
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username'     => 'required|string|max:255|unique:users,username',
            'password'     => 'required|string|min:6',
            'nama_lengkap' => 'required|string|max:255',
            'role'         => 'required|string|in:Staf Admin,Tenaga Medis,Farmasi,Bidan',
        ], [
            'username.required'     => 'Username wajib diisi!',
            'username.unique'       => 'Username sudah digunakan oleh staf lain!',
            'password.required'     => 'Password wajib diisi!',
            'password.min'          => 'Password minimal 6 karakter!',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi!',
            'role.required'         => 'Pilih role terlebih dahulu!',
        ]);

        User::create([
            'username'     => $validated['username'],
            'password'     => Hash::make($validated['password']),
            'nama_lengkap' => $validated['nama_lengkap'],
            'role'         => $validated['role'],
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  2. LOGIN — Menampilkan form & memproses autentikasi
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Menampilkan halaman form login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses autentikasi login.
     *
     * Supports "Remember Me" via $request->boolean('remember').
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi!',
            'password.required' => 'Password wajib diisi!',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')
                ->with('success', 'Berhasil Login — Selamat datang, ' . Auth::user()->nama_lengkap . '!');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah!',
        ])->onlyInput('username');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  3. LOGOUT — Menghapus session dan redirect
    // ═══════════════════════════════════════════════════════════════════

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  4. API LOGIN — Sanctum Token-based (for API)
    // ═══════════════════════════════════════════════════════════════════

    public function loginApi(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Kredensial salah'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 200);
    }
}
