<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Superadmin (Administrator)
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nama_lengkap' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'Administrator'
            ]
        );

        // 2. Pocoyo (Tim Medis)
        User::updateOrCreate(
            ['username' => 'pocoyo'],
            [
                'nama_lengkap' => 'Pocoyo Medis',
                'password' => Hash::make('poco123'),
                'role' => 'Tim Medis'
            ]
        );
    }
}
