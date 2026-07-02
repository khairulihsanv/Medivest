<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * UserController — Manajemen User (Staf Admin Only)
 *
 * CRUD operations for user accounts in the system.
 * Access restricted to 'Staf Admin' role via route middleware.
 */
class UserController extends Controller
{
    /**
     * Simpan user baru ke database.
     *
     * Route: POST /users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'     => 'required|string|max:255|unique:users,username',
            'password'     => 'required|string|min:6',
            'nama_lengkap' => 'required|string|max:255',
            'role'         => 'required|string|in:Staf Admin,Tenaga Medis,Farmasi,Bidan',
        ], [
            'username.required'     => 'Username wajib diisi!',
            'username.unique'       => 'Username sudah digunakan!',
            'password.required'     => 'Password wajib diisi!',
            'password.min'          => 'Password minimal 6 karakter!',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi!',
            'role.required'         => 'Role wajib dipilih!',
        ]);

        User::create([
            'username'     => $validated['username'],
            'password'     => Hash::make($validated['password']),
            'nama_lengkap' => $validated['nama_lengkap'],
            'role'         => $validated['role'],
        ]);

        return redirect()->route('dashboard.users')
            ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Update data user yang sudah ada.
     *
     * Route: PUT /users/{id}
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username'     => 'required|string|max:255|unique:users,username,' . $id . ',id_user',
            'nama_lengkap' => 'required|string|max:255',
            'role'         => 'required|string|in:Staf Admin,Tenaga Medis,Farmasi,Bidan',
            'password'     => 'nullable|string|min:6',
        ], [
            'username.required'     => 'Username wajib diisi!',
            'username.unique'       => 'Username sudah digunakan!',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi!',
            'role.required'         => 'Role wajib dipilih!',
        ]);

        $user->username     = $validated['username'];
        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->role         = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('dashboard.users')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Hapus user dari database.
     *
     * Route: DELETE /users/{id}
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('dashboard.users')
            ->with('success', 'User berhasil dihapus dari sistem.');
    }
}
