<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User — Tabel 'users' di database db_kesehatan_terpadu
 *
 * Menangani koneksi ke tabel users yang sudah ada di phpMyAdmin.
 * Primary key menggunakan 'id_user' (bukan default 'id' milik Laravel).
 * Kolom: id_user, username, password, nama_lengkap, role
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ─── KONFIGURASI TABEL ─────────────────────────────────────────────

    /**
     * Koneksi database ke Server Pusat (Server A).
     */
    protected $connection = 'mysql_pusat';

    /**
     * Nama tabel yang digunakan oleh model ini.
     * Wajib didefinisikan karena nama tabel bukan 'users' standar Laravel.
     */
    protected $table = 'users';

    /**
     * Primary key menggunakan kolom 'id_user' (bukan 'id' default).
     * Sesuai skema database yang sudah ada di phpMyAdmin.
     */
    protected $primaryKey = 'id_user';

    /**
     * Tipe primary key adalah integer auto-increment.
     */
    protected $keyType = 'int';

    /**
     * Aktifkan auto-increment pada primary key.
     */
    public $incrementing = true;

    /**
     * Nonaktifkan timestamp otomatis (created_at & updated_at).
     * Tabel users yang sudah ada tidak memiliki kolom timestamp.
     */
    public $timestamps = false;

    // ─── MASS ASSIGNMENT PROTECTION ────────────────────────────────────

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     * Melindungi dari serangan mass-assignment vulnerability.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'role',
    ];

    // ─── KEAMANAN SERIALISASI ──────────────────────────────────────────

    /**
     * Kolom yang disembunyikan saat model dikonversi ke array/JSON.
     * Mencegah password bocor ke response API atau view.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    // ─── ATTRIBUTE CASTING ─────────────────────────────────────────────

    /**
     * Casting otomatis untuk kolom tertentu.
     * 'password' => 'hashed' membuat Laravel otomatis hash password
     * saat di-set melalui model (menggunakan Hash::make secara internal).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
