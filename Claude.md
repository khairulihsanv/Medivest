# Claude.md — Medivest: Absolute Source of Truth for AI Agents

> **MANDATORY READ.** Any AI agent operating in this repository MUST read this file in its entirety before generating any code, making architectural decisions, or proposing schema changes. Deviation from these specifications is a critical error.

---

## 1. PROJECT OVERVIEW

| Field | Value |
|---|---|
| **Project Name** | Medivest |
| **Tagline** | Sistem Logistik Kesehatan & Monitoring Terpadu |
| **Framework** | Laravel 12.x (confirmed via `composer.json`) |
| **PHP Version** | ^8.2 (strict typed, PHP 8.x features allowed) |
| **Primary Language** | PHP, Blade, Vanilla JS, Alpine.js |
| **Exam Context** | PBO (Pemrograman Berorientasi Objek) + UAS Basis Data Terdistribusi |

### 1.1 Core Value Proposition

Medivest eliminates manual medical administration by bridging three critical healthcare domains into a single, unified, interactive dashboard:

1. **Inventory Monitoring** — Real-time medicine stock tracking with auto-reorder alerts (SiMoSoBa)
2. **Disease Surveillance** — Clinical case timeline across distributed geographic nodes
3. **Immunization Management** — Pediatric schedule tracking with automated WhatsApp reminder links

### 1.2 Design Persona

| Attribute | Specification |
|---|---|
| **Design Language** | High-fidelity Premium Dark SaaS, Bento-Grid layout |
| **Visual Inspiration** | MotionSites.AI, gsap.com/UI |
| **CSS Framework** | Tailwind CSS (CDN or compiled via Vite) |
| **Color Palette** | Dark backgrounds (#0a0a0f, #111827), accent emerald/amber/crimson for status |
| **Typography** | Inter or similar geometric sans-serif (Google Fonts) |
| **Animation** | Subtle micro-animations; CSS transitions on hover; status pulse dots |
| **UI Pattern** | Tab-based single-page dashboard (no full page reloads for tabs) |

---

## 2. SYSTEM ARCHITECTURE & ENVIRONMENT

### 2.1 Development Environment

| Component | Specification |
|---|---|
| **IDE** | Antigravity IDE (VS Code fork) |
| **Local Server** | XAMPP on Windows — Apache + MySQL 8.x |
| **Local MySQL Port** | 3306 (both nodes use this port; differentiated by host IP) |
| **Network Topology** | Multi-PC Local Wi-Fi LAN during exam presentation |
| **Node A Host** | This machine (`127.0.0.1` in dev, real LAN IP during exam) |
| **Node B Host** | Peer laptop (`127.0.0.1` in dev, peer LAN IP during exam) |

### 2.2 Core Technology Stack

```
+------------------+--------------------------------------------------+
| Layer            | Technology                                       |
+------------------+--------------------------------------------------+
| Runtime          | PHP ^8.2 (required by composer.json)            |
| Framework        | Laravel 12.x (laravel/framework ^12.0)          |
| ORM              | Eloquent (SoftDeletes, Casts, Accessors)         |
| Auth             | Laravel built-in Auth + Sanctum (API tokens)    |
| View Engine      | Blade Templates                                  |
| Frontend         | Tailwind CSS + Vanilla JS + Alpine.js            |
| Charts           | Chart.js (CDN, used in dashboard overview)       |
| Build Tool       | Vite (vite.config.js present)                    |
| Queue / Logs     | Laravel Pail (dev-only), default sync driver     |
| API Auth         | Laravel Sanctum (HasApiTokens on User model)     |
+------------------+--------------------------------------------------+
| Database A       | MySQL 8.x — db_medivest_pusat  (Server A)       |
| Database B       | MySQL 8.x — db_medivest_klinik (Server B)       |
+------------------+--------------------------------------------------+
```

### 2.3 Deployment Strategy

| Phase | Target | Method |
|---|---|---|
| **Dev** | Local (`php artisan serve`) | Vite dev server + artisan |
| **Exam Demo** | Multi-PC LAN | Change `.env` `DB_HOST_KLINIK` to peer's LAN IP only |
| **Cloud** | Railway / Render | Environment variables via platform dashboard |

> **CRITICAL DEV COMMAND:** Run `composer run dev` to start concurrently: `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`.

---

## 3. DISTRIBUTED DATABASE SPECIFICATION

> **WARNING: ARCHITECTURAL MANDATE.** This is the single most critical constraint. Any AI that ignores this two-connection architecture generates WRONG code.

### 3.1 The Distributed Design — Overview

This system implements a **Horizontally Fragmented Distributed Database** across two independent MySQL instances. There is NO single monolithic database. Laravel communicates with both via two named connections in `config/database.php`.

```
+------------------------------------+    +------------------------------------+
|   NODE A — SERVER PUSAT            |    |   NODE B — SERVER KLINIK           |
|   Laptop 1 (This Machine)          |    |   Laptop 2 (Peer Machine)          |
+------------------------------------+    +------------------------------------+
| Connection: mysql_pusat            |    | Connection: mysql_klinik           |
| Database:   db_medivest_pusat      |    | Database:   db_medivest_klinik     |
| Host:       DB_HOST_PUSAT (.env)   |    | Host:       DB_HOST_KLINIK (.env)  |
| Port:       3306                   |    | Port:       3306                   |
+------------------------------------+    +------------------------------------+
| Tables:                            |    | Tables:                            |
|  - users                           |    |  - pelaporan_penyakit (Taman)      |
|  - obat                            |    |  - imunisasi                       |
|  - pelaporan_penyakit              |    |                                    |
|    (Manguharjo, Kartoharjo)        |    |                                    |
+------------------------------------+    +------------------------------------+
          |                                          |
          +-------------- LOGICAL JOIN --------------+
                   Done in PHP/Eloquent layer.
                   NO cross-server SQL JOINs possible.
```

### 3.2 Connection Configuration — `config/database.php` (actual, verified)

```php
'mysql_pusat' => [
    'driver'    => 'mysql',
    'host'      => env('DB_HOST_PUSAT', '127.0.0.1'),
    'port'      => env('DB_PORT_PUSAT', '3306'),
    'database'  => env('DB_DATABASE_PUSAT', 'db_medivest_pusat'),
    'username'  => env('DB_USERNAME_PUSAT', 'root'),
    'password'  => env('DB_PASSWORD_PUSAT', ''),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'strict'    => true,
],

'mysql_klinik' => [
    'driver'    => 'mysql',
    'host'      => env('DB_HOST_KLINIK', '127.0.0.1'),
    'port'      => env('DB_PORT_KLINIK', '3306'),
    'database'  => env('DB_DATABASE_KLINIK', 'db_medivest_klinik'),
    'username'  => env('DB_USERNAME_KLINIK', 'root'),
    'password'  => env('DB_PASSWORD_KLINIK', ''),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'strict'    => true,
],
```

### 3.3 Node A Schema — `db_medivest_pusat` (`mysql_pusat`)

#### Table: `users`

| Column | Type | Notes |
|---|---|---|
| `id_user` | `bigint UNSIGNED PK AI` | Custom PK — NOT `id` |
| `username` | `varchar(255) UNIQUE` | Login credential — NOT `email` |
| `password` | `varchar(255)` | Bcrypt hashed |
| `nama_lengkap` | `varchar(255)` | Display name |
| `role` | `varchar(255)` | Default: `'Tim Medis'`. Roles: `Admin Faskes`, `Dokter`, `Apoteker`, `Petugas Imunisasi`, `Tim Medis` |

> No `created_at`/`updated_at` — `$timestamps = false` on `User` model.

#### Table: `obat`

| Column | Type | Notes |
|---|---|---|
| `id_obat` | `bigint UNSIGNED PK AI` | Custom PK |
| `nama_obat` | `varchar(255)` | |
| `jenis_obat` | `varchar(255)` | Values: `'Vaksin'`, `'Tablet'`, `'Sirup'` etc. |
| `stok` | `int` | Default `0`. Kritis <=10, Rendah <=50, Aman >50 |
| `harga_beli` | `decimal(12,2)` | Default `0.00` |
| `tgl_kadaluarsa` | `date` | Cast to Carbon |
| `deleted_at` | `timestamp NULL` | SoftDeletes enabled |

> No `created_at`/`updated_at` — `$timestamps = false`.

#### Table: `pelaporan_penyakit` (Fragment A — Node A only)

| Column | Type | Notes |
|---|---|---|
| `id_laporan` | `bigint UNSIGNED PK AI` | |
| `nama_pasien` | `varchar(255)` | |
| `nik` | `varchar(255)` | Numeric string, validated `regex:/^[0-9]+$/` |
| `jenis_penyakit` | `varchar(255)` | e.g., `'Demam Berdarah Dengue'`, `'COVID-19'` |
| `tgl_diagnosis` | `date` | Cast to Carbon |
| `wilayah` | `varchar(255)` | ONLY `'Manguharjo'` or `'Kartoharjo'` in this node |
| `tingkat_keparahan` | `varchar(255)` | `Ringan`, `Sedang`, `Berat`, `Kritis` |
| `catatan_klinis` | `text NULL` | |
| `deleted_at` | `timestamp NULL` | SoftDeletes |

---

### 3.4 Node B Schema — `db_medivest_klinik` (`mysql_klinik`)

#### Table: `pelaporan_penyakit` (Fragment B — Node B only)

> **IDENTICAL DDL** to Node A. This IS Horizontal Fragmentation — same schema, data split by `wilayah`. IDs may collide between nodes; this is expected.

| Column | Type | Notes |
|---|---|---|
| `id_laporan` | `bigint UNSIGNED PK AI` | May overlap with Node A |
| `nama_pasien` | `varchar(255)` | |
| `nik` | `varchar(255)` | |
| `jenis_penyakit` | `varchar(255)` | |
| `tgl_diagnosis` | `date` | |
| `wilayah` | `varchar(255)` | ONLY `'Taman'` in this node |
| `tingkat_keparahan` | `varchar(255)` | |
| `catatan_klinis` | `text NULL` | |
| `deleted_at` | `timestamp NULL` | SoftDeletes |

#### Table: `imunisasi`

| Column | Type | Notes |
|---|---|---|
| `id_imunisasi` | `bigint UNSIGNED PK AI` | Custom PK |
| `nama_anak` | `varchar(255)` | |
| `nama_orang_tua` | `varchar(255)` | |
| `usia_bulan` | `int` | Age in months |
| `jenis_vaksin` | `varchar(255)` | e.g., `'BCG'`, `'Polio'`, `'MMR'` |
| `dosis_ke` | `int` | Dose sequence number |
| `tgl_jadwal` | `date` | Scheduled vaccination date |
| `no_hp` | `varchar(255)` | Raw; normalized via `normalizeNoHp()` |
| `status_reminder` | `varchar(255)` | Default: `'Belum Dikirim'`; updated to `'Sudah Dikirim'` post-send |
| `deleted_at` | `timestamp NULL` | SoftDeletes |

> No `created_at`/`updated_at` — `$timestamps = false`.

### 3.5 Cross-Node Relational Rules

| Rule | Verdict |
|---|---|
| Physical Foreign Keys across nodes | NEVER. MySQL cannot enforce FK across separate instances. |
| SQL JOIN across connections | IMPOSSIBLE. One Eloquent query = one connection. |
| Logical Relationships | Use `Collection::concat()` then sort/group/filter in PHP memory. |
| Indexing | Add `->index()` on `wilayah`, `jenis_penyakit` for PHP-side filter performance. |
| Data merging | Always done in the Controller layer. Never in SQL. |

---

## 4. APPLICATION MODELS — CANONICAL REFERENCE

### 4.1 `App\Models\User` (Node A)

```php
// Traits: HasApiTokens, HasFactory, Notifiable (Sanctum enabled)
protected $connection   = 'mysql_pusat';
protected $table        = 'users';
protected $primaryKey   = 'id_user';     // NOT 'id'
protected $keyType      = 'int';
public    $incrementing = true;
public    $timestamps   = false;
protected $fillable     = ['username', 'password', 'nama_lengkap', 'role'];
protected $hidden       = ['password'];
// casts(): ['password' => 'hashed']
// Auth::attempt() uses 'username' — NOT 'email'
```

### 4.2 `App\Models\Obat` (Node A)

```php
// Traits: SoftDeletes
protected $connection   = 'mysql_pusat';
protected $table        = 'obat';
protected $primaryKey   = 'id_obat';
public    $timestamps   = false;
protected $fillable     = ['nama_obat', 'jenis_obat', 'stok', 'harga_beli', 'tgl_kadaluarsa'];
protected $casts        = [
    'stok'           => 'integer',
    'harga_beli'     => 'float',
    'tgl_kadaluarsa' => 'date',
];
```

**Business Methods (canonical — do not rename):**

| Method | Signature | Returns | Purpose |
|---|---|---|---|
| `getStatusStok()` | instance | `array{label, class, dot}` | UI stock badge data |
| `hitungSisaHariKadaluarsa()` | instance | `int` | Days until expiry |
| `getKodeAttribute()` | accessor | `string` | `VAK-001` or `OBT-001` display code |
| `hitungRekomendasiStokOtomatis()` | static | `array` | Safety Stock + ROP calculation |

**ROP Formula (embedded in model):**
```
safety_stock = ceil(20 * (1 + (jumlah_kasus * 0.2)))
rop          = (5 units/day * 3 days lead_time) + safety_stock
```

### 4.3 `App\Models\PelaporanPenyakit` (Node A Fragment)

```php
// Traits: SoftDeletes, HasPelaporanHelpers
protected $connection = 'mysql_pusat';
protected $table      = 'pelaporan_penyakit'; // SAME name as Klinik model
protected $primaryKey = 'id_laporan';
public    $timestamps = false;
// Handles wilayah: 'Manguharjo', 'Kartoharjo'
```

### 4.4 `App\Models\PelaporanPenyakitKlinik` (Node B Fragment)

```php
// Traits: SoftDeletes, HasPelaporanHelpers
protected $connection = 'mysql_klinik';
protected $table      = 'pelaporan_penyakit'; // SAME name as Pusat model
protected $primaryKey = 'id_laporan';
public    $timestamps = false;
// Handles wilayah: 'Taman'
```

> **CRITICAL:** Both models use table name `'pelaporan_penyakit'` — distinguished ONLY by `$connection`. This IS Horizontal Fragmentation. Do NOT merge into one model. Do NOT rename either model.

### 4.5 `App\Traits\HasPelaporanHelpers` (Shared Logic)

Used by BOTH Pelaporan models. All shared logic belongs here.

| Method | Signature | Returns | Purpose |
|---|---|---|---|
| `hitungKasusPerJenis()` | static | `Collection` | Group by disease for Chart.js |
| `hitungKasusByJenis(string)` | static | `int` | Count of specific disease (ROP feeder) |
| `getSeverityClassAttribute()` | accessor | `string` | Tailwind badge CSS for severity |

### 4.6 `App\Models\Imunisasi` (Node B)

```php
// Traits: SoftDeletes
protected $connection   = 'mysql_klinik';
protected $table        = 'imunisasi';
protected $primaryKey   = 'id_imunisasi';
public    $timestamps   = false;
protected $fillable     = [
    'nama_anak', 'nama_orang_tua', 'usia_bulan', 'jenis_vaksin',
    'dosis_ke', 'tgl_jadwal', 'no_hp', 'status_reminder',
];
protected $casts = [
    'usia_bulan' => 'integer',
    'dosis_ke'   => 'integer',
    'tgl_jadwal' => 'date',
];
```

**Business Methods (canonical — do not rename):**

| Method | Signature | Returns | Purpose |
|---|---|---|---|
| `normalizeNoHp(string)` | static | `string` | Strip non-digits, `0xxx` to `62xxx` |
| `getWhatsappUrlAttribute()` | accessor | `string` | Full WA deep-link with pre-filled text |
| `getStatusBadgeAttribute()` | accessor | `array{badge, dot}` | Tailwind classes for reminder status |
| `dapatkanTargetTerancamWabah()` | static | `Collection` | Unnotified children by vaccine type |

**WhatsApp URL Format — CANONICAL (verified in source):**
```
https://api.whatsapp.com/send?phone={normalized_no_hp}&text={url_encoded_pesan}
```
> NOT `wa.me`. The actual code uses `api.whatsapp.com/send`.

---

## 5. CONTROLLER ARCHITECTURE

### 5.1 Route Map

| Method | URI | Controller@Method | Middleware | Purpose |
|---|---|---|---|---|
| GET | `/` | `LandingController@index` | public | Landing page with live stats |
| GET | `/login` | `AuthController@showLogin` | guest | Login form |
| POST | `/login` | `AuthController@login` | guest | Process authentication |
| GET | `/register` | `AuthController@showRegister` | guest | Register form |
| POST | `/register` | `AuthController@register` | guest | Create user account |
| GET | `/dashboard` | `DashboardController@index` | auth | Tab-based dashboard hub |
| POST | `/obat` | `ObatController@store` | auth | Create medicine record |
| DELETE | `/obat/{id}` | `ObatController@destroy` | auth | Soft-delete medicine |
| POST | `/pelaporan` | `PelaporanController@store` | auth | Create disease report |
| POST | `/imunisasi` | `ImunisasiController@store` | auth | Create immunization record |
| POST | `/logout` | `AuthController@logout` | auth | Destroy session |
| POST | `/api/login` | `AuthController@loginApi` | api | Sanctum Bearer token |

### 5.2 DashboardController — Cross-Server Aggregation Pattern

```php
// CORRECT: Query both, merge in PHP
$laporanA = PelaporanPenyakit::orderByDesc('id_laporan')->get();       // Server A
$laporanB = PelaporanPenyakitKlinik::orderByDesc('id_laporan')->get(); // Server B
$merged   = $laporanA->concat($laporanB)->sortByDesc('id_laporan')->values();

// CORRECT: Sum counts
$totalKasus = PelaporanPenyakit::count() + PelaporanPenyakitKlinik::count();

// WRONG: Any SQL JOIN/UNION across connections
// WRONG: Querying only one server and treating it as complete data
```

**Tab Whitelist** (`$allowedTabs` in DashboardController):

| Tab Key | Content |
|---|---|
| `overview` | 4 KPI cards + Chart.js (multi-axis + stacked bar + top diseases) |
| `simosoba` | Medicine inventory + ROP predictive analysis |
| `pelaporan` | Merged disease reports from both servers |
| `imunisasi` | Immunization queue ordered by `tgl_jadwal ASC` |

**Role-based Panels:**

| Role | Extra Data | Variable |
|---|---|---|
| `Dokter` | Areas >= 3 cases (epidemic alert) | `$waspada_epidemi` |
| `Apoteker` | Urgency score = 100 - (stok * 2) | `$restock_urgency` |
| `Petugas Imunisasi` | Unnotified children aged >= 9 months | `$vaksin_drop` |

### 5.3 PelaporanController — Geographic Routing Constants

```php
private const WILAYAH_PUSAT  = ['Manguharjo', 'Kartoharjo']; // -> Server A
private const WILAYAH_KLINIK = ['Taman'];                      // -> Server B
```

To add a new region: append to the appropriate constant. No other code changes needed.

### 5.4 AuthController — Key Notes

- `Auth::attempt()` uses `username`, NOT `email`
- Default registration role: `'Tim Medis'`
- Session fixation: `$request->session()->regenerate()` on login success
- Logout cleanup: `->invalidate()` + `->regenerateToken()`
- API token endpoint: `POST /api/login` returns Sanctum Bearer token

---

## 6. CROSS-SERVER ALERT SYSTEM

**Trigger:** Cases spike (both servers) AND `obat.stok < 20` on Server A -> crimson alert.

```php
$kasusTotal = PelaporanPenyakit::count() + PelaporanPenyakitKlinik::count();
$stokKritis = Obat::where('stok', '<', 20)->count();
$showAlert  = ($kasusTotal > 0 && $stokKritis > 0);
```

**Stock Status Thresholds:**

| Condition | Label | Badge Tailwind Classes | Dot Class |
|---|---|---|---|
| `stok <= 10` | Kritis | `bg-red-50 text-red-700 border-red-200/60` | `bg-red-500 pulse-dot` |
| `stok <= 50` | Rendah | `bg-amber-50 text-amber-700 border-amber-200/60` | `bg-amber-500` |
| `stok > 50` | Aman | `bg-emerald-50 text-emerald-700 border-emerald-200/60` | `bg-emerald-500` |

---

## 7. ENGINE CODE OPTIMIZATION RULES

> Critical: cross-server queries over Wi-Fi LAN carry real TCP latency. N+1 = N+1 network connections.

### 7.1 Performance

| Rule | Implementation |
|---|---|
| Eager Loading | Always `with()` for relations. Never lazy-load inside loops. |
| Collection Merge | `$collA->concat($collB)->sortByDesc('field')->values()` |
| Cross-server count | `ModelA::count() + ModelB::count()` (two queries, PHP summed) |
| Grouped data | `pluck('total','key')->toArray()` from each server, then PHP `array_merge` + manual sum |

### 7.2 Code Quality

| Rule | Implementation |
|---|---|
| Mass Assignment | Always `$fillable`. Never `$guarded = []`. |
| Attribute Casting | All date/numeric columns in `$casts`. |
| Soft Deletes | All domain tables. Use `->withTrashed()` / `->onlyTrashed()` for admin recovery. |
| SQL Injection | Eloquent QB or `DB::select()` with bound params. Never interpolate user input into raw SQL. |
| OOP | Business logic in Models/Traits. Controllers are thin. Shared logic in `app/Traits/`. |
| Prepared Statements | Eloquent uses PDO internally. If `DB::statement()` used, always use `?` or named bindings. |

### 7.3 Migration Standards

| Rule | Detail |
|---|---|
| Node B connection | All Node B migrations: `protected $connection = 'mysql_klinik'` AND `Schema::connection('mysql_klinik')` |
| Idempotency | Wrap `Schema::create()` in `if (!Schema::connection(...)->hasTable(...))` |
| Rollback | `down()` must use `Schema::connection(...)->dropIfExists(...)` |
| Running | Node A: `php artisan migrate`. Node B: `php artisan migrate --database=mysql_klinik` |

---

## 8. SECURITY CONSTRAINTS

| Concern | Implementation |
|---|---|
| Password | `Hash::make()` (bcrypt) + model cast `'hashed'` |
| CSRF | `@csrf` in every Blade form |
| Session Fixation | `$request->session()->regenerate()` after login |
| Input Validation | Always `$request->validate([...])`. Never trust raw `$request->input()`. |
| Route Protection | All CRUD inside `Route::middleware('auth')` |
| Role Checking | `auth()->user()->role`. Valid roles: `Admin Faskes`, `Dokter`, `Apoteker`, `Petugas Imunisasi`, `Tim Medis` |
| API Auth | Sanctum Bearer tokens. API routes: `middleware('auth:sanctum')` |
| XSS | Blade `{{ }}` auto-escapes. Only `{!! !!}` for explicitly sanitized HTML. |

---

## 9. FILE STRUCTURE — CANONICAL MAP

```
Medivest-Laravel/
+-- app/
|   +-- Http/
|   |   +-- Controllers/
|   |   |   +-- AuthController.php          # Login, Register, Logout, API Login
|   |   |   +-- DashboardController.php     # Cross-server aggregation hub (237 lines)
|   |   |   +-- ObatController.php          # SiMoSoBa CRUD (Server A)
|   |   |   +-- PelaporanController.php     # Disease report routing (A or B by wilayah)
|   |   |   +-- ImunisasiController.php     # Immunization CRUD (Server B)
|   |   |   +-- LandingController.php       # Public landing page
|   |   |   +-- Api/                        # API controllers
|   |   +-- Middleware/
|   |   +-- Requests/
|   |   +-- Resources/
|   +-- Models/
|   |   +-- User.php                        # Server A | Auth | Sanctum | timestamps=false
|   |   +-- Obat.php                        # Server A | SoftDeletes | ROP logic
|   |   +-- PelaporanPenyakit.php           # Server A fragment | Manguharjo, Kartoharjo
|   |   +-- PelaporanPenyakitKlinik.php     # Server B fragment | Taman
|   |   +-- Imunisasi.php                   # Server B | SoftDeletes | WA helper
|   |   +-- AuditLog.php                    # Audit trail
|   +-- Traits/
|   |   +-- HasPelaporanHelpers.php         # Shared trait for both Pelaporan models
|   +-- Observers/
|   +-- Providers/
+-- config/
|   +-- database.php                        # Dual MySQL connection config (verified)
+-- database/
|   +-- migrations/
|       +-- 2026_06_23_063100_create_users_table.php              # mysql_pusat
|       +-- 2026_06_23_061432_create_obat_table.php               # mysql_pusat (SoftDeletes)
|       +-- 2026_06_24_000001_create_pelaporan_penyakit_pusat_table.php # mysql_pusat fragment A
|       +-- 2026_06_23_062600_create_pelaporan_penyakit_table.php       # mysql_klinik fragment B
|       +-- 2026_06_23_062601_create_imunisasi_table.php          # mysql_klinik (SoftDeletes)
+-- routes/
|   +-- web.php                             # Public + guest + auth route groups
|   +-- api.php                             # Sanctum API routes
+-- resources/views/
|   +-- auth/
|   |   +-- login.blade.php
|   |   +-- register.blade.php
|   +-- dashboard.blade.php                 # Tab-based dashboard
|   +-- landing.blade.php                   # Public landing with Chart.js
+-- .env                                    # NOT committed to git
+-- .env.example                            # Template
+-- composer.json                           # PHP deps (laravel/framework ^12.0, sanctum ^4.3)
+-- package.json                            # Vite
+-- vite.config.js
+-- Claude.md                               # THIS FILE
```

---

## 10. ENVIRONMENT VARIABLES — CANONICAL `.env` KEYS

```dotenv
# === APPLICATION ===
APP_NAME=Medivest
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# === NODE A — SERVER PUSAT (This Machine) ===
DB_HOST_PUSAT=127.0.0.1
DB_PORT_PUSAT=3306
DB_DATABASE_PUSAT=db_medivest_pusat
DB_USERNAME_PUSAT=root
DB_PASSWORD_PUSAT=

# === NODE B — SERVER KLINIK (Peer Machine) ===
# EXAM DAY: change ONLY this to peer's LAN IP, then run php artisan config:clear
DB_HOST_KLINIK=127.0.0.1
DB_PORT_KLINIK=3306
DB_DATABASE_KLINIK=db_medivest_klinik
DB_USERNAME_KLINIK=root
DB_PASSWORD_KLINIK=

# === CACHE / QUEUE / SESSIONS ===
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# === SANCTUM (API Auth) ===
SANCTUM_STATEFUL_DOMAINS=localhost:8000,127.0.0.1:8000
```

---

## 11. ANTI-HALLUCINATION CHECKLIST FOR AI AGENTS

Before generating ANY code, verify ALL of the following:

- [ ] Connection names are exactly `'mysql_pusat'` or `'mysql_klinik'` (lowercase, underscore)
- [ ] Primary keys: `id_user`, `id_obat`, `id_laporan`, `id_imunisasi` — NOT the default `id`
- [ ] No cross-server SQL: no `JOIN`, `UNION`, or raw queries spanning both connections
- [ ] `$timestamps = false` on ALL domain models (User, Obat, PelaporanPenyakit, PelaporanPenyakitKlinik, Imunisasi)
- [ ] `SoftDeletes` trait declared on all domain models (except AuditLog)
- [ ] `Auth::attempt()` uses `username` — NOT `email`
- [ ] Wilayah routing constants in `PelaporanController` are respected
- [ ] WhatsApp URL: `https://api.whatsapp.com/send?phone=...&text=...` — NOT `wa.me`
- [ ] `$fillable` always declared — never `$guarded = []`
- [ ] Laravel version: **12.x** — do not use deprecated L8/L9 patterns
- [ ] PHP version: **^8.2** — PHP 8.2+ features available
- [ ] `pelaporan_penyakit` table exists on BOTH servers with identical schema

---

## 12. QUICK REFERENCE — ARTISAN COMMANDS

```bash
# Full dev environment (server + queue + logs + Vite — concurrent)
composer run dev

# Run all migrations (Node A tables)
php artisan migrate

# Run Node B migrations (when DB_HOST_KLINIK points to peer)
php artisan migrate --database=mysql_klinik

# Clear config cache — REQUIRED after .env changes
php artisan config:clear

# Check migration status
php artisan migrate:status
php artisan migrate:status --database=mysql_klinik

# Rollback last batch
php artisan migrate:rollback

# Generate Sanctum API token for testing
php artisan tinker
# >>> \App\Models\User::first()->createToken('test')->plainTextToken

# Run tests
composer test

# Code style fix (Laravel Pint)
./vendor/bin/pint
```

---

*Document Version: 1.0 | Generated: 2026-06-30 | Medivest-Laravel/*
*This is a living document. Update Section 3-4 on schema changes, Section 5 on route changes, Section 11 on new architectural constraints.*