<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ObatApiController;
use App\Http\Controllers\AuthController;

// 1. Endpoint Publik untuk Login API (Mendapatkan Token)
Route::post('/login', [AuthController::class, 'loginApi']);

// 2. Endpoint yang Diproteksi Sanctum (Wajib bawa Bearer Token di Postman)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('obat', ObatApiController::class);

Route::get('/fix-password/{username}', function ($username) {
    $user = \App\Models\User::where('username', $username)->first();
    
    if (!$user) {
        return response()->json(['message' => 'Username tidak ditemukan di database.']);
    }
    
    // Paksa update password menggunakan enkripsi Bcrypt Laravel
    $user->password = \Illuminate\Support\Facades\Hash::make('rahasia123');
    $user->save();
    
    return response()->json([
        'message' => "Password untuk user '{$username}' berhasil diperbarui ke format Bcrypt.",
        'instruksi' => "Sekarang gunakan password 'rahasia123' di Postman untuk login."
    ]);
});
});
