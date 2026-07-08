# Dokumentasi Proyek — Bank Sampah Digital Faperta

> **Dokumen ini dibuat berdasarkan analisis source code, konfigurasi, migration, routing, dan file-file yang ada di repository.**
> Tanggal analisis: 2026-07-07

---

# 1. Project Overview

| Item | Detail |
|------|--------|
| **Nama Proyek** | Bank Sampah Digital Faperta (Bank Sampah Faperta) |
| **Tujuan** | Platform digital pengelolaan bank sampah untuk civitas akademika Faperta dan masyarakat umum. Memfasilitasi setor sampah, penjemputan, pencatatan saldo, penarikan tunai, serta edukasi lingkungan. |
| **Masalah yang Diselesaikan** | (1) Tidak ada sistem terpadu pencatatan setor sampah dan saldo nasabah. (2) Proses verifikasi dan approval setor/penarikan masih manual. (3) Tidak ada sistem jemput sampah terjadwal. (4) Kurangnya kesadaran dan gamifikasi untuk partisipasi daur ulang. |
| **Target Pengguna** | `nasabah` (masyarakat umum/civitas akademika), `petugas` (staff operasional), `admin` (pengelola system) |
| **Fitur Utama** | Registrasi & verifikasi nasabah, setor sampah (deposit), penimbangan & approval, penarikan saldo (withdrawal), penjemputan sampah (pickup), gamifikasi (eco-points, level, badge), katalog harga sampah, artikel edukasi, dashboard statistik, cetak resi, mutation ledger |

**Referensi:** `app/Models/User.php`, `database/migrations/`, `routes/web.php`, `routes/admin.php`, `PROJECT_OVERVIEW.md`

---

# 2. Tech Stack

| Layer | Teknologi | Versi/Keterangan |
|-------|-----------|-----------------|
| **Backend** | Laravel | ^13.8 |
| **PHP** | PHP | ^8.3 |
| **Frontend** | React + Inertia.js | React ^18.2, Inertia ^2.0 (Laravel + React) |
| **Admin Panel** | Filament | ^5.6 (Legacy, masih ada di `/admin`) |
| **Admin CMS (Baru)** | Blade + TailwindCSS | Di-mount di `/cms` |
| **Database** | SQLite (default) | MySQL/MariaDB/PgSQL via env |
| **CSS Framework** | TailwindCSS | ^3.2.1 (dengan `@tailwindcss/forms`, `@tailwindcss/vite` ^4.0) |
| **JavaScript Library** | React ^18.2, Framer Motion ^12.40, Lucide React, MapLibre GL ^5.24 |
| **Build Tools** | Vite ^8.0, Vite Laravel Plugin ^3.1, PostCSS ^8.4 |
| **Auth** | Laravel Breeze (Inertia React stack), Sanctum ^4.0 |
| **Other Backend** | Ziggy ^2.0 (route helper JS), Filament ^5.6, Tinker ^3.0 |
| **Queue** | Database-based (default), Redis sebagai opsi |
| **Cache** | Database-based (default), Redis sebagai opsi |
| **Mail** | Log driver (default) |
| **Storage** | Local (public disk), S3 sebagai opsi |

**Referensi:** `composer.json`, `package.json`, `.env.example`, `config/app.php`, `config/database.php`

---

# 3. Struktur Project

```
banksampah-faperta/
├── app/
│   ├── Console/           # Artisan commands (tidak ada custom command)
│   ├── Events/            # Event classes (Deposit/Withdrawal/SavingsTarget)
│   ├── Exceptions/        # Exception handler
│   ├── Filament/          # Filament resources, widgets, pages (Legacy Admin)
│   │   ├── Resources/     #  CRUD resources: Users, Deposits, Withdrawals, TrashPrices, Articles, PickupRequests, ActivityLogs
│   │   └── Widgets/       #  StatsOverview, TrashCategoryChart, TransactionTrendChart, DepositCategoryChart
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/     #  Controller untuk CMS Blade (new admin)
│   │   │   ├── Auth/      #  Breeze Inertia auth controllers
│   │   │   ├── NasabahController.php   #  Nasabah dashboard & actions (Inertia)
│   │   │   ├── ProfileController.php   #  Breeze profile
│   │   │   └── WelcomeController.php   #  Public pages (Inertia)
│   │   ├── Middleware/    #  HandleInertiaRequests, EnsureUserHasRole, BlockNasabahFromAdmin
│   │   ├── Requests/     #  Form request validations (Auth + Admin)
│   │   └── Responses/    #  LogoutResponse (Filament override)
│   ├── Jobs/             #  Queue jobs (tidak ada custom job)
│   ├── Listeners/        #  Event listeners (Deposit, Withdrawal, SavingsTarget)
│   ├── Models/           #  12 models (lihat DB section)
│   ├── Policies/         #  8 policy classes
│   ├── Providers/        #  AppServiceProvider, AuthServiceProvider, EventServiceProvider, Filament\AdminPanelProvider
│   └── Services/         #  TransactionService, GamificationService, Dashboard\DashboardService
├── bootstrap/
│   ├── app.php           #  App config: routing, middleware, exception handler
│   └── providers.php     #  Service providers registration
├── config/               #  Laravel config files
├── database/
│   ├── factories/        #  UserFactory
│   ├── migrations/       #  14 migration files
│   └── seeders/          #  DatabaseSeeder (seed data)
├── docs/                 #  Documentation
├── public/               #  Public assets, index.php
├── resources/
│   ├── js/               #  React (Inertia) — Pages, Components, Layouts
│   │   ├── Components/   #  UI components (ui/button, table, dropdown-menu), Auth modals, Layouts
│   │   ├── Layouts/      #  AuthenticatedLayout, GuestLayout, NasabahLayout, PublicLayout
│   │   └── Pages/        #  Welcome, Auth, Nasabah, Public, Profile, Dashboard
│   └── views/            #  Blade views (admin CMS, layouts, print, components)
│       ├── admin/        #  Dashboard, deposits, withdrawals, articles, users, trash-prices, pickup-requests, badges, savings-targets, activity-logs, profile
│       ├── components/   #  Blade admin components (card, box, btn, modal, pagination, stat-card, table-wrapper, form/input)
│       ├── layouts/      #  admin.blade.php
│       └── print/        #  deposit.blade.php, withdrawal.blade.php
├── routes/
│   ├── web.php           #  Main routes: public, nasabah, dashboard, profile, print
│   ├── auth.php          #  Breeze auth routes (login, register, password reset, email verif)
│   ├── admin.php         #  Blade CMS routes (/cms prefix)
│   └── console.php       #  Artisan inspire command
├── storage/              #  Logs, cache, session, uploads
├── tests/                #  PHPUnit tests
├── vendor/               #  Composer dependencies
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── postcss.config.js
```

---

# 4. Architecture

## 4.1. Pola Arsitektur

| Pattern | Keterangan | Referensi |
|---------|-----------|-----------|
| **MVC** | Model-View-Controller standar Laravel | Seluruh struktur |
| **Service Layer** | Logic transaksi dipisah ke `TransactionService`, gamifikasi ke `GamificationService`, dashboard ke `DashboardService` | `app/Services/` |
| **Repository/Policies** | Authorization dipisah ke Policy classes | `app/Policies/` |
| **Event-Driven** | Deposit/Withdrawal approval/rejection memicu event → listener | `app/Events/`, `app/Listeners/` |
| **Form Request Validation** | Validasi dipisah ke Request classes | `app/Http/Requests/` |
| **Middleware** | Role-based access (`EnsureUserHasRole`), nasabah blocker (`BlockNasabahFromAdmin`), Inertia (`HandleInertiaRequests`) | `app/Http/Middleware/` |
| **Inertia.js** | Frontend React dirender via Inertia (SPA-like tanpa API penuh) | `resources/js/` |
| **Double-Entry Ledger** | Mutation table mencatat perubahan saldo (debit/kredit) dengan balance_before dan balance_after | `database/migrations/2026_06_15_222000_create_bi_and_advanced_features_tables.php` |
| **Polymorphic Relation** | Mutation.sourceable polymorphic ke Deposit atau Withdrawal | `app/Models/Mutation.php` |
| **Filament Panel** | Panel admin lama di `/admin` dengan Resource architecture | `app/Filament/` |

## 4.2. Alur Request

```
User → Browser → HTTP Request
  ├─ [Public Routes] → WelcomeController (Inertia) → React Page (resources/js/Pages/Welcome.jsx)
  ├─ [Auth Routes] → Auth Controllers (Inertia) → React Auth Pages
  ├─ [Nasabah Routes] → NasabahController (Inertia) → React Nasabah Pages
  │     └─ Middleware: auth, role:nasabah
  ├─ [CMS Routes (/cms)] → Admin Controllers → Blade Views (resources/views/admin/)
  │     └─ Middleware: auth, role:admin,petugas + BlockNasabahFromAdmin
  │     └─ Authorization: Policy classes
  │     └─ TransactionService → DB (SQLite/MySQL) + Events → Listeners
  └─ [Filament (/admin)] → Filament Resources → Filament UI
        └─ Middleware: auth:web + Filament auth
        └─ FilamentUser interface (role admin/petugas)
```

## 4.3. Alur Transaksi Keuangan

```
Nasabah setor sampah → Petugas/Admin input deposit items (pending)
  → CMS: Approve dengan real weight → TransactionService::approveDeposit()
    → DB Transaction (lockForUpdate)
      → Hitung ulang weight_total & total_price
      → Update deposit status = approved
      → Jika bukan donasi: tambah saldo user
      → Buat Mutation record (kredit, double-entry)
      → Buat ActivityLog
    → Dispatch DepositApproved event
    → Sync gamification badges
  → Saldo bertambah

Nasabah ajukan penarikan (Withdraw) → Pending
  → CMS: Approve → TransactionService::approveWithdrawal()
    → DB Transaction (lockForUpdate)
      → Hitung admin fee (Rp 2.500 untuk transfer non-BTN)
      → Cek saldo cukup
      → Kurangi saldo (amount + admin_fee)
      → Buat Mutation record (debit)
      → Buat WithdrawalHistory
      → Buat ActivityLog
    → Dispatch WithdrawalApproved event
```

---

# 5. Database

## 5.1. Tabel & Kolom

### `users`
Sumber: `0001_01_01_000000_create_users_table.php`, `2026_06_14_150825_add_fields_to_users_table.php`, `2026_06_19_000001_add_demografi_to_users_table.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto increment |
| name | string | |
| email | string (unique) | |
| email_verified_at | timestamp nullable | |
| password | string | Hashed |
| remember_token | string nullable | |
| role | enum('admin','petugas','nasabah') | Default: 'nasabah' |
| status | enum('pending','verified','rejected') | Default: 'pending' |
| phone | string nullable | |
| address | text nullable | |
| saldo | bigint | Default: 0 (simpanan nasabah) |
| account_no | string unique nullable | Format BS-xxxxx |
| umur | integer nullable | |
| gender | enum('L','P') nullable | |
| status_pekerjaan | string nullable | mahasiswa/dosen/umum/dll |
| universitas | string nullable | |
| fakultas | string nullable | |
| pendidikan_terakhir | string nullable | |
| created_at / updated_at | timestamps | |

### `deposits`
Sumber: `2026_06_14_150826_create_bank_sampah_tables.php`, `2026_06_15_222000_create_bi_and_advanced_features_tables.php`, `2026_06_19_000003_add_category_type_to_trash_prices.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id | Cascade delete |
| total_price | bigint | Default: 0 |
| weight_total | decimal(10,2) | Default: 0.00 |
| status | enum('pending','approved','rejected') | Default: 'pending' |
| notes | text nullable | |
| validated_by | bigint FK → users.id nullable | Set null on delete |
| is_donation | boolean | Default: false (added later) |
| donation_category | enum('umum','donasi') | Default: 'umum' |
| created_at / updated_at | timestamps | |

### `deposit_items`
Sumber: `2026_06_14_150826_create_bank_sampah_tables.php`, `2026_06_16_153720_add_carbon_fields_to_tables.php`, `2026_06_19_000003_add_category_type_to_trash_prices.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| deposit_id | bigint FK → deposits.id | Cascade delete |
| trash_price_id | bigint FK → trash_prices.id | Restrict delete |
| weight | decimal(10,2) | |
| price_per_unit | bigint | |
| total_price | bigint | |
| total_carbon | decimal(10,2) | Weight × carbon_factor |
| item_name | string nullable | Snapshot dari trash_price |
| item_category | string nullable | Snapshot |
| item_category_type | enum('umum','donasi') nullable | Snapshot |

### `trash_prices`
Sumber: `2026_06_14_150826_create_bank_sampah_tables.php`, `2026_06_16_153720_add_carbon_fields_to_tables.php`, `2026_06_19_000003_add_category_type_to_trash_prices.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| name | string | |
| category | enum('plastik','kertas','logam','kaca','minyak_jelantah','lainnya') | |
| category_type | enum('umum','donasi') | Default: 'umum' |
| price_buy | bigint | Harga beli dari nasabah |
| price_sell | bigint | Harga jual ke pabrik |
| unit | string | Default: 'kg' |
| carbon_factor | decimal(8,2) | Default: 0.00 (kg CO₂e per unit) |

### `withdrawals`
Sumber: `2026_06_14_150826_create_bank_sampah_tables.php`, `2026_06_19_000002_add_withdrawal_fields_and_history.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id | Cascade delete |
| amount | bigint | |
| withdrawal_method | enum('tunai','transfer_bank') | Default: 'transfer_bank' |
| admin_fee | bigint | Default: 0 |
| bank_name | string | |
| bank_type | string nullable | 'btn' / 'lainnya' / null |
| account_number | string | |
| account_name | string | |
| status | enum('pending','approved','rejected') | Default: 'pending' |
| notes | text nullable | |
| validated_by | bigint FK → users.id nullable | Set null on delete |

### `withdrawal_history`
Sumber: `2026_06_19_000002_add_withdrawal_fields_and_history.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| withdrawal_id | bigint FK → withdrawals.id | Cascade delete |
| status | enum('pending','approved','rejected','processed','completed') | |
| notes | text nullable | |
| processed_by | bigint FK → users.id | Set null on delete |
| processed_at | timestamp nullable | |

### `pickup_requests`
Sumber: `2026_06_20_000001_create_pickup_requests_table.php`, `2026_06_20_000002_add_location_to_pickup_requests_table.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id | Cascade delete |
| pickup_address | text | |
| pickup_phone | string | |
| latitude | decimal(10,7) nullable | Geolocation |
| longitude | decimal(10,7) nullable | |
| estimated_distance | decimal(8,2) nullable | Distance in km |
| pickup_date | date | |
| pickup_time | string | Format "08:00-10:00" dll |
| notes | text nullable | |
| status | enum('pending','assigned','completed','cancelled') | Default: 'pending' |
| assigned_to | bigint FK → users.id nullable | Null on delete |
| deposit_id | bigint FK → deposits.id nullable | Null on delete |

### `mutations`
Sumber: `2026_06_15_222000_create_bi_and_advanced_features_tables.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id | Cascade delete |
| type | enum('debit','kredit') | debit=penarikan, kredit=setoran |
| amount | bigint | |
| sourceable_id | bigint nullable (morph) | Polymorphic ke Deposit/Withdrawal |
| sourceable_type | string nullable (morph) | |
| balance_before | bigint | |
| balance_after | bigint | |

### `articles`
Sumber: `2026_06_14_150826_create_bank_sampah_tables.php`, `2026_06_16_180000_update_articles_image_upload.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| title | string | |
| slug | string (unique) | |
| content | text | |
| image_path | text nullable | Renamed from 'image_url' |
| status | enum('draft','published') | Default: 'draft' |

### `activity_logs`
Sumber: `2026_06_15_222000_create_bi_and_advanced_features_tables.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id nullable | Set null on delete |
| action | string | e.g. 'approve_deposit', 'reject_withdrawal' |
| description | text | |

### `savings_targets`
Sumber: `2026_06_15_222000_create_bi_and_advanced_features_tables.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id | Cascade delete |
| title | string | |
| target_amount | bigint | |
| is_achieved | boolean | Default: false |

### `user_badges`
Sumber: `2026_06_23_000001_create_user_badges_table.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| user_id | bigint FK → users.id | Cascade delete |
| badge_key | string (index) | e.g. 'first_deposit', 'frequent_depositor' |
| unlocked_at | timestamp nullable | |
| UNIQUE | (user_id, badge_key) | |

### Tabel Laravel Built-in

| Tabel | Sumber |
|-------|--------|
| `password_reset_tokens` | `0001_01_01_000000_create_users_table.php` — email PK, token, created_at |
| `sessions` | `0001_01_01_000000_create_users_table.php` — id PK, user_id FK index, ip, user_agent, payload, last_activity |
| `cache` | `0001_01_01_000001_create_cache_table.php` — key PK, value, expiration |
| `jobs` | `0001_01_01_000002_create_jobs_table.php` — queue job |
| `job_batches` | `0001_01_01_000002_create_jobs_table.php` |
| `failed_jobs` | `0001_01_01_000002_create_jobs_table.php` |

## 5.2. Relasi Antar Tabel

```
users ──hasMany──> deposits
users ──hasMany──> withdrawals
users ──hasMany──> deposit_items  (via deposits)
users ──hasMany──> pickup_requests
users ──hasMany──> savings_targets
users ──hasMany──> user_badges
users ──hasMany──> mutations
users ──hasMany──> activity_logs  (sebagai pelaku)
users ──hasMany──> withdrawal_history (sebagai processor)

deposits ──hasMany──> deposit_items
deposits ──belongsTo──> users (user_id, validated_by)
deposits ──belongsTo──> pickup_requests (deposit_id, nullable)

deposit_items ──belongsTo──> trash_prices

withdrawals ──belongsTo──> users (user_id, validated_by)
withdrawals ──hasMany──> withdrawal_history

pickup_requests ──belongsTo──> users (user_id, assigned_to)

mutations ──morphTo──> sourceable (Deposit/Withdrawal)
```

---

# 6. User Role

## 6.1. Role & Hak Akses

| Role | Deskripsi | Hak Akses |
|------|-----------|-----------|
| **admin** | Administrator penuh | Semua akses CMS, manage users, manage harga, approve/reject setor & penarikan, kelola artikel, lihat activity logs, cetak resi, Filament panel |
| **petugas** | Staff operasional | Akses CMS (kecuali delete user/prices), approve/reject setor & penarikan, edit pickup requests, lihat activity logs, cetak resi, Filament panel |
| **nasabah** | Nasabah/masyarakat | Dashboard sendiri, setor sampah (via petugas), ajukan penjemputan, ajukan penarikan, lihat riwayat, atur target tabungan, lihat badge/level |

**Referensi:** `app/Models/User.php` (role enum, hasRole(), isAdmin(), isStaff()), `app/Policies/` (setiap policy membedakan isStaff/isAdmin)

## 6.2. Middleware

| Middleware | Fungsi | Referensi |
|-----------|--------|-----------|
| `EnsureUserHasRole` (alias: `role`) | Memeriksa apakah user punya role tertentu. Redirect/logout jika nasabah belum verified. | `app/Http/Middleware/EnsureUserHasRole.php`, `bootstrap/app.php` |
| `BlockNasabahFromAdmin` | Mencegah nasabah mengakses `/admin*` dan `/cms*`, redirect ke nasabah.dashboard | `app/Http/Middleware/BlockNasabahFromAdmin.php` |
| `HandleInertiaRequests` | Share data Inertia (auth user) ke semua React pages | `app/Http/Middleware/HandleInertiaRequests.php` |

## 6.3. Policy

| Policy | Metode | Akses |
|--------|--------|-------|
| `UserPolicy` | viewAny, view, create, update, delete | isStaff untuk read/write, isAdmin untuk delete |
| `DepositPolicy` | viewAny, view, create, update, delete, approve, reject | isStaff untuk view/create/update, isAdmin untuk delete, approve/reject butuh status pending |
| `WithdrawalPolicy` | viewAny, view, create, update, delete, approve, reject | Sama seperti DepositPolicy |
| `PickupRequestPolicy` | viewAny, view, create, update, delete | isStaff |
| `ArticlePolicy` | viewAny, view, create, update, delete | isStaff |
| `TrashPricePolicy` | viewAny, view, create, update, delete | isStaff untuk read/write, isAdmin untuk delete |
| `ActivityLogPolicy` | viewAny | isStaff |
| `SavingsTargetPolicy` | viewAny, view, create, update, delete | isStaff |

**Referensi:** `app/Policies/`, `app/Providers/AuthServiceProvider.php`

## 6.4. Gate

| Gate | Fungsi |
|------|--------|
| `manage-users` | Hanya admin yang bisa manage users (Gate::define) |

**Referensi:** `app/Providers/AuthServiceProvider.php:44`

## 6.5. Filament Panel Access

```
User::canAccessPanel() → return in_array($this->role, ['admin', 'petugas']);
```

Nasabah tidak bisa akses Filament panel.

---

# 7. Business Flow

## 7.1. Registrasi Nasabah

```
1. Nasabah buka halaman /register
2. Isi form: name, email, password, phone, address, umur, gender, status_pekerjaan, universitas (jika relevan), fakultas, pendidikan_terakhir
3. Submit → RegisteredUserController::store
4. Validasi form request (inline)
5. User created: role = 'nasabah', status = 'pending', saldo = 0
6. Event Registered → email verification (opsional di env)
7. Redirect ke login dengan flash: "Akun Anda akan aktif setelah diverifikasi admin."
8. Admin di CMS (/cms/users) verifikasi user (status: pending → verified/rejected)
```

**Referensi:** `app/Http/Controllers/Auth/RegisteredUserController.php`, `routes/auth.php`, `resources/js/Pages/Auth/Register.jsx`

## 7.2. Verifikasi Nasabah

```
1. Admin/Petugas login → /cms/users
2. Lihat daftar user dengan status 'pending'
3. Edit user → ubah status menjadi 'verified' atau 'rejected'
4. User yang verified bisa login
5. User yang belum verified (pending) akan di-logout oleh middleware EnsureUserHasRole jika mencoba akses route nasabah
```

**Referensi:** `app/Http/Controllers/Admin/UserController.php`, `routes/admin.php`, `app/Http/Middleware/EnsureUserHasRole.php`

## 7.3. Deposit (Setor Sampah)

```
Nasabah:
1. Datang ke loket bank sampah dengan sampah
2. Petugas/Admin mencatat setoran via Filament panel atau CMS

Petugas/Admin via CMS:
1. Buka /cms/deposits
2. Klik "Tambah Setoran" (via Filament: CreateDeposit)
3. Pilih nasabah, pilih jenis sampah dari trash_prices, input estimasi berat
4. Status deposit = 'pending'

Approval:
1. Petugas/Admin buka detail deposit di /cms/deposits/{id}
2. Timbang ulang sampah → input real weight per item
3. Klik "Setujui" → TransactionService::approveDeposit()
   - Hitung ulang total_price & weight_total
   - Jika bukan donasi: tambah saldo nasabah
   - Buat record Mutation (kredit)
   - Buat ActivityLog
   - Dispatch DepositApproved event
   - Sync badges (GamificationService)
4. Atau "Tolak" → reject, buat ActivityLog
```

**Referensi:** `app/Http/Controllers/Admin/DepositController.php`, `app/Services/TransactionService.php`, `routes/admin.php`, `resources/views/admin/deposits/`

## 7.4. Withdrawal (Penarikan Saldo)

```
Nasabah:
1. Login → dashboard → klik "Tarik Saldo" (/nasabah/tarik)
2. Pilih metode: Tunai atau Transfer Bank
3. Input amount (min Rp 10.000, max = saldo)
4. Jika transfer: isi bank_name, account_number, account_name, bank_type (btn/lainnya)
5. Submit: validasi jam operasional 08:00-16:00
6. Status = 'pending'

Approval (Admin/Petugas):
1. Buka /cms/withdrawals
2. Lihat detail penarikan
3. Klik "Setujui" → TransactionService::approveWithdrawal()
   - Hitung admin_fee: Rp 2.500 untuk transfer non-BTN
   - Cek saldo cukup (amount + admin_fee)
   - Kurangi saldo nasabah
   - Buat Mutation (debit)
   - Buat WithdrawalHistory
   - Buat ActivityLog
   - Dispatch WithdrawalApproved event
4. Atau "Tolak" → reject, buat WithdrawalHistory + ActivityLog
```

**Referensi:** `app/Http/Controllers/NasabahController.php` (withdraw/storeWithdraw), `app/Http/Controllers/Admin/WithdrawalController.php`, `app/Services/TransactionService.php`, `routes/web.php`, `routes/admin.php`

## 7.5. Pickup Request (Jemput Sampah)

```
Nasabah:
1. Login → dashboard → "Jemput Sampah" (/nasabah/jemput)
2. Pilih alamat, telepon, tanggal, waktu slot (08:00-10:00 / 10:00-12:00 / 13:00-15:00)
3. Opsional: lokasi (latitude/longitude) via MapLibre GL
4. Validasi: jarak maksimal 2 km (estimated_distance > 2 = ditolak)
5. Submit → status = 'pending'

Penugasan (Admin/Petugas):
1. Buka /cms/pickup-requests
2. Lihat daftar request pending
3. Edit pickup request → assign petugas (assigned_to), ubah status jadi 'assigned'
4. Setelah penjemputan selesai dan sampah ditimbang → status 'completed' (bisa link ke deposit)

Pembatalan:
- Nasabah bisa batalkan (via admin) → status 'cancelled'
```

**Referensi:** `app/Http/Controllers/NasabahController.php` (pickupRequest/storePickupRequest), `app/Http/Controllers/Admin/PickupRequestController.php`, `routes/admin.php` (edit/update), `database/migrations/2026_06_20_000001_create_pickup_requests_table.php`

## 7.6. Gamification

```
1. Setiap approved deposit trigger syncBadges()
2. EcoPoints = (weight_total × 5) + (deposit_count × 3) + (donation_weight × 3) + streak_bonus + diversity_bonus
3. Level system (5 level):
   - Pemula Hijau (0 pts) → Pejuang Lingkungan (150) → Ksatria Hijau (400) → Pahlawan Bumi (800) → Legenda Faperta (1500)
4. Badge system (10 badges):
   - first_deposit, frequent_depositor (15x), heavy_lifter (75kg), plastic_hero (30kg plastik)
   - tree_friend (50kg CO₂e), noble_donor (3x donasi), streak_master (5 bulan)
   - green_millionaire (saldo ≥100k), pickup_captain (5 jemput selesai), campus_legend (level max)
5. Badge otomatis ter-unlock saat kondisi terpenuhi
6. Data ditampilkan di dashboard nasabah (level, points, badge progress)
```

**Referensi:** `app/Services/GamificationService.php`

## 7.7. Dashboard Admin

```
1. Login admin/petugas → /cms
2. DashboardService::getOverviewStats() — cached 5 menit:
   - total_donation_profit (total nilai donasi)
   - total_weight (total berat semua setoran approved)
   - retained_balance (total saldo semua nasabah)
   - active_ratio (% nasabah aktif 30 hari)
   - active_nasabah, total_nasabah
3. Today summary: deposits today, withdrawals today, pending pickups, pending deposits, pending withdrawals
4. Deposit trend (7 hari): weight per hari
5. Donation breakdown: savings vs donation
6. Recent activity logs (8 terakhir)
```

**Referensi:** `app/Services/Dashboard/DashboardService.php`, `app/Http/Controllers/Admin/DashboardController.php`

## 7.8. Cetak Resi

```
1. Admin/Petugas buka deposit atau withdrawal detail
2. Klik "Cetak Resi" → /admin-print/deposit/{id} atau /admin-print/withdrawal/{id}
3. Render Blade view → print.deposit atau print.withdrawal
4. Role check: hanya admin/petugas (inline di route closure)
```

**Referensi:** `routes/web.php:54-68`, `resources/views/print/`

---

# 8. Feature List

## Authentication
- Registrasi nasabah dengan data demografi lengkap (`routes/auth.php`, `RegisteredUserController`)
- Login/Logout (`AuthenticatedSessionController`)
- Password reset (via email) (`PasswordResetLinkController`, `NewPasswordController`)
- Email verification (`VerifyEmailController`, `EmailVerificationPromptController`)
- Password confirmation (`ConfirmablePasswordController`)

## Public Pages
- Landing page (Welcome) dengan total sampah & karbon dihitung dari DB (`WelcomeController::index`)
- Katalog harga sampah (`/harga`) — publik, semua kategori (`WelcomeController::prices`)
- Daftar artikel edukasi (`/artikel`) dengan pagination (`WelcomeController::articles`)
- Detail artikel publik (`/artikel/{slug}`) (`WelcomeController::article`)

## Dashboard
- Dashboard admin (Blade CMS): overview stats, trending chart, recent activities (`admin/dashboard/index.blade.php`, `DashboardController`)
- Dashboard nasabah (React Inertia): saldo, transaksi terkini, target tabungan, pickups pending, gamification data (`Nasabah/Dashboard.jsx`)
- Dashboard redirect: admin/petugas → /cms, nasabah → /nasabah/dashboard (`routes/web.php`)

## Master Data — Harga Sampah
- CRUD harga sampah (Filament + CMS Blade) — category, category_type, price_buy, price_sell, unit, carbon_factor
- Kategori: plastik, kertas, logam, kaca, minyak_jelantah, lainnya
- Tipe kategori: umum / donasi

## Master Data — Users
- CRUD user (Admin only via `manage-users` gate)
- Verifikasi status nasabah (pending → verified/rejected)
- Data demografi: umur, gender, status_pekerjaan, universitas, fakultas, pendidikan_terakhir

## Transaksi — Deposit
- Buat setoran sampah (Filament)
- List deposit dengan filter status, kategori donasi, search user
- Detail deposit dengan items
- Approve: input real weight, hitung ulang total & karbon → tambah saldo
- Reject: rollback jika sudah approved
- Donasi: kategori umum/donasi, jika donasi saldo tidak bertambah

## Transaksi — Withdrawal
- Ajukan penarikan (nasabah) — min Rp 10.000, jam operasional 08:00-16:00
- List withdrawal dengan filter
- Approve: hitung admin fee (Rp 2.500 transfer non-BTN, gratis BTN/tunai)
- Reject: batalkan tanpa pengembalian (karena belum diproses)
- Riwayat status ('WithdrawalHistory')

## Transaksi — Pickup Request
- Ajukan jemput sampah — max jarak 2 km, pilih waktu slot
- Edit pickup request (assign petugas, ubah status)
- Geolokasi (latitude/longitude) via MapLibre
- Status: pending → assigned → completed / cancelled
- Link ke deposit saat selesai

## Gamification
- Eco-points dengan breakdown (weight, transaction, donation, streak, diversity)
- 5 level dengan threshold exponential
- 10 badge dengan kondisi spesifik
- Auto-sync badges setiap deposit approval

## Target Tabungan
- Nasabah buat target tabungan (title, target_amount)
- Otomatis update is_achieved saat saldo mencapai target
- Admin bisa manage target (CMS)

## Artikel
- CRUD artikel (admin/petugas)
- Status: draft / published
- Image upload ke storage public atau URL eksternal
- Auto-slug dari title
- Hapus image file saat artikel dihapus

## Aktivitas & Audit
- Activity Log untuk setiap aksi penting (approve/reject deposit/withdrawal)
- Lihat log di CMS (admin/petugas)

## Cetak Resi
- Resi deposit (Blade printable view)
- Resi withdrawal (Blade printable view)

## Profile
- Edit profile (name, email)
- Update password
- Delete account
- CMS profile (admin/petugas)

---

# 9. Route Analysis

## 9.1. Public Routes (`routes/web.php`)

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| GET | `/` | WelcomeController@index | welcome | web |
| GET | `/harga` | WelcomeController@prices | public.prices | web |
| GET | `/artikel` | WelcomeController@articles | public.articles | web |
| GET | `/artikel/{slug}` | WelcomeController@article | public.article | web |

## 9.2. Dashboard Redirect (`routes/web.php`)

| Method | URI | Closure | Name | Middleware |
|--------|-----|---------|------|------------|
| GET | `/dashboard` | Redirect by role (admin/petugas → `/cms`, nasabah → nasabah.dashboard) | dashboard | auth |

## 9.3. Nasabah Routes (`routes/web.php`)

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| GET | /nasabah/dashboard | NasabahController@dashboard | nasabah.dashboard | auth, role:nasabah |
| GET | /nasabah/jemput | NasabahController@pickupRequest | nasabah.pickup | auth, role:nasabah |
| POST | /nasabah/jemput | NasabahController@storePickupRequest | nasabah.pickup.store | auth, role:nasabah |
| GET | /nasabah/tarik | NasabahController@withdraw | nasabah.withdraw | auth, role:nasabah |
| POST | /nasabah/tarik | NasabahController@storeWithdraw | nasabah.withdraw.store | auth, role:nasabah |
| GET | /nasabah/riwayat | NasabahController@history | nasabah.history | auth, role:nasabah |
| POST | /nasabah/target | NasabahController@storeTarget | nasabah.target.store | auth, role:nasabah |
| DELETE | /nasabah/target/{target} | NasabahController@deleteTarget | nasabah.target.delete | auth, role:nasabah |

## 9.4. Profile Routes (`routes/web.php`)

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| GET | /profile | ProfileController@edit | profile.edit | auth |
| PATCH | /profile | ProfileController@update | profile.update | auth |
| DELETE | /profile | ProfileController@destroy | profile.destroy | auth |

## 9.5. Print Routes (`routes/web.php`)

| Method | URI | Closure/Role Check | Name | Middleware |
|--------|-----|--------------------|------|------------|
| GET | /admin-print/deposit/{deposit}/print | View print.deposit | admin.deposit.print | auth |
| GET | /admin-print/withdrawal/{withdrawal}/print | View print.withdrawal | admin.withdrawal.print | auth |

## 9.6. Auth Routes (`routes/auth.php`)

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| GET | /register | RegisteredUserController@create | register | guest |
| POST | /register | RegisteredUserController@store | — | guest |
| GET | /login | AuthenticatedSessionController@create | login | guest |
| POST | /login | AuthenticatedSessionController@store | — | guest |
| GET | /forgot-password | PasswordResetLinkController@create | password.request | guest |
| POST | /forgot-password | PasswordResetLinkController@store | password.email | guest |
| GET | /reset-password/{token} | NewPasswordController@create | password.reset | guest |
| POST | /reset-password | NewPasswordController@store | password.store | guest |
| GET | /verify-email | EmailVerificationPromptController | verification.notice | auth |
| GET | /verify-email/{id}/{hash} | VerifyEmailController | verification.verify | auth, signed, throttle:6,1 |
| POST | /email/verification-notification | EmailVerificationNotificationController@store | verification.send | auth, throttle:6,1 |
| GET | /confirm-password | ConfirmablePasswordController@show | password.confirm | auth |
| POST | /confirm-password | ConfirmablePasswordController@store | — | auth |
| PUT | /password | PasswordController@update | password.update | auth |
| POST | /logout | AuthenticatedSessionController@destroy | logout | auth |

## 9.7. Admin CMS Routes (`routes/admin.php`)

Didaftarkan via `bootstrap/app.php` → `then` closure dengan middleware `web`.

Group: `middleware = ['auth', 'role:admin,petugas']`, `prefix = 'cms'`, `name = cms.`

| Method | URI | Controller@Method | Name |
|--------|-----|-------------------|------|
| GET | /cms | DashboardController@index | cms.dashboard |
| GET | /cms/trash-prices | TrashPriceController@index | cms.trash-prices.index |
| GET | /cms/trash-prices/create | TrashPriceController@create | cms.trash-prices.create |
| POST | /cms/trash-prices | TrashPriceController@store | cms.trash-prices.store |
| GET | /cms/trash-prices/{trash_price}/edit | TrashPriceController@edit | cms.trash-prices.edit |
| PUT | /cms/trash-prices/{trash_price} | TrashPriceController@update | cms.trash-prices.update |
| DELETE | /cms/trash-prices/{trash_price} | TrashPriceController@destroy | cms.trash-prices.destroy |
| GET | /cms/badges | BadgeController@index | cms.badges.index |
| GET | /cms/deposits | DepositController@index | cms.deposits.index |
| GET | /cms/deposits/{deposit} | DepositController@show | cms.deposits.show |
| POST | /cms/deposits/{deposit}/approve | DepositController@approve | cms.deposits.approve |
| POST | /cms/deposits/{deposit}/reject | DepositController@reject | cms.deposits.reject |
| GET | /cms/withdrawals | WithdrawalController@index | cms.withdrawals.index |
| GET | /cms/withdrawals/{withdrawal} | WithdrawalController@show | cms.withdrawals.show |
| POST | /cms/withdrawals/{withdrawal}/approve | WithdrawalController@approve | cms.withdrawals.approve |
| POST | /cms/withdrawals/{withdrawal}/reject | WithdrawalController@reject | cms.withdrawals.reject |
| GET | /cms/pickup-requests | PickupRequestController@index | cms.pickup-requests.index |
| GET | /cms/pickup-requests/{pickup_request} | PickupRequestController@show | cms.pickup-requests.show |
| GET | /cms/pickup-requests/{pickup_request}/edit | PickupRequestController@edit | cms.pickup-requests.edit |
| PUT | /cms/pickup-requests/{pickup_request} | PickupRequestController@update | cms.pickup-requests.update |
| GET | /cms/articles | ArticleController@index | cms.articles.index |
| GET | /cms/articles/create | ArticleController@create | cms.articles.create |
| POST | /cms/articles | ArticleController@store | cms.articles.store |
| GET | /cms/articles/{article}/edit | ArticleController@edit | cms.articles.edit |
| PUT | /cms/articles/{article} | ArticleController@update | cms.articles.update |
| DELETE | /cms/articles/{article} | ArticleController@destroy | cms.articles.destroy |
| GET | /cms/users | UserController@index | cms.users.index |
| GET | /cms/users/create | UserController@create | cms.users.create |
| POST | /cms/users | UserController@store | cms.users.store |
| GET | /cms/users/{user} | UserController@show | cms.users.show |
| GET | /cms/users/{user}/edit | UserController@edit | cms.users.edit |
| PUT | /cms/users/{user} | UserController@update | cms.users.update |
| DELETE | /cms/users/{user} | UserController@destroy | cms.users.destroy |
| GET | /cms/savings-targets | SavingsTargetController@index | cms.savings-targets.index |
| GET | /cms/savings-targets/create | SavingsTargetController@create | cms.savings-targets.create |
| POST | /cms/savings-targets | SavingsTargetController@store | cms.savings-targets.store |
| GET | /cms/savings-targets/{savings_target}/edit | SavingsTargetController@edit | cms.savings-targets.edit |
| PUT | /cms/savings-targets/{savings_target} | SavingsTargetController@update | cms.savings-targets.update |
| DELETE | /cms/savings-targets/{savings_target} | SavingsTargetController@destroy | cms.savings-targets.destroy |
| GET | /cms/activity-logs | ActivityLogController@index | cms.activity-logs.index |
| GET | /cms/profile | ProfileController@edit | cms.profile.edit |
| PATCH | /cms/profile | ProfileController@update | cms.profile.update |
| PUT | /cms/profile/password | ProfileController@updatePassword | cms.profile.password |

## 9.8. Filament Panel (auto-discovered, routes generated Filament)

| Prefix | Keterangan |
|--------|-----------|
| `/admin` | Filament panel, hanya accessible oleh admin/petugas (`canAccessPanel`) |

## 9.9. Console Routes (`routes/console.php`)

| Command | Closure |
|---------|---------|
| `inspire` | Output quote inspiratif |

---

# 10. Packages

## 10.1. Composer (`composer.json`)

| Package | Fungsi |
|---------|--------|
| `laravel/framework` ^13.8 | Core Laravel framework |
| `laravel/sanctum` ^4.0 | API token authentication / cookie-based SPA auth |
| `laravel/tinker` ^3.0 | Interactive REPL untuk Artisan |
| `filament/filament` ^5.6 | Admin panel builder (legacy, masih digunakan) |
| `inertiajs/inertia-laravel` ^2.0 | Server-side adapter untuk Inertia.js |
| `tightenco/ziggy` ^2.0 | Generate Laravel route names ke JavaScript |
| `laravel/breeze` ^2.4 (dev) | Starter kit auth scaffolding (Inertia React stack) |
| `fakerphp/faker` ^1.23 (dev) | Fake data generator untuk testing/seed |
| `laravel/pail` ^1.2.5 (dev) | Log viewer command line |
| `laravel/pao` ^1.0.6 (dev) | Tidak ditemukan dokumentasi resmi — mungkin internal Laravel |
| `laravel/pint` ^1.27 (dev) | PHP coding style fixer |
| `mockery/mockery` ^1.6 (dev) | Mocking framework untuk PHPUnit |
| `nunomaduro/collision` ^8.6 (dev) | Error handling CLI yang lebih readable |
| `phpunit/phpunit` ^12.5.12 (dev) | Testing framework |

## 10.2. NPM (`package.json`)

| Package | Fungsi |
|---------|--------|
| `react` ^18.2 | UI library |
| `react-dom` ^18.2 | React DOM renderer |
| `@inertiajs/react` ^2.0 | Inertia.js React adapter |
| `@headlessui/react` ^2.0 | Unstyled accessible UI components |
| `framer-motion` ^12.40 | Animation library |
| `lucide-react` ^1.21 | SVG icon library |
| `maplibre-gl` ^5.24 | Map renderer (geolokasi pickup) |
| `tailwindcss` ^3.2.1 | Utility-first CSS |
| `@tailwindcss/forms` ^0.5 | Form reset styles for Tailwind |
| `@tailwindcss/vite` ^4.0 | Tailwind Vite plugin |
| `@vitejs/plugin-react` ^4.2 | React Vite plugin |
| `vite` ^8.0 | Build tool |
| `laravel-vite-plugin` ^3.1 | Laravel + Vite integration |
| `postcss` ^8.4 | PostCSS processor |
| `autoprefixer` ^10.4 | CSS vendor prefixer |
| `concurrently` ^9.0 | Run multiple npm scripts in parallel |

---

# 11. Environment

Berdasarkan `.env.example` dan konfigurasi:

## Essential

| Variable | Default | Keterangan |
|----------|---------|------------|
| `APP_NAME` | "Laravel" | Nama aplikasi |
| `APP_ENV` | "local" | Environment (local/production) |
| `APP_KEY` | — | Generate via `php artisan key:generate` |
| `APP_DEBUG` | true | Debug mode (false di production) |
| `APP_URL` | "http://localhost" | Base URL |
| `DB_CONNECTION` | "sqlite" | Database driver (sqlite/mysql/pgsql) |
| `SESSION_DRIVER` | "database" | Session storage (file/cookie/database/redis) |
| `QUEUE_CONNECTION` | "database" | Queue driver (sync/database/redis) |
| `CACHE_STORE` | "database" | Cache driver (database/file/redis) |
| `FILESYSTEM_DISK` | "local" | Storage disk (local/s3) |

## Optional / Advanced

| Variable | Default | Keterangan |
|----------|---------|------------|
| `DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD` | — | MySQL config |
| `SESSION_LIFETIME` | 120 | Session timeout (menit) |
| `MAIL_MAILER` | "log" | Mail driver (log/smtp/sendmail) |
| `MAIL_FROM_ADDRESS` | "admin@bsfp.com" | Email pengirim |
| `AWS_ACCESS_KEY_ID/AWS_SECRET_ACCESS_KEY/AWS_BUCKET` | — | S3 storage config |
| `REDIS_HOST/REDIS_PASSWORD/REDIS_PORT` | — | Redis config |
| `BROADCAST_CONNECTION` | "log" | Broadcast driver |
| `VITE_APP_NAME` | "${APP_NAME}" | Vite app name untuk frontend |

## Storage

Public storage untuk upload gambar artikel: `storage/app/public/` → symlink `public/storage`.

## Queue

Queue default: database (via `jobs` table). Di `composer.json` dev script, `php artisan queue:listen --tries=1 --timeout=0` jalan bersamaan dengan server.

## Cache

Dashboard overview stats cache: 5 menit via `Cache::remember()` di `DashboardService`.

---

# 12. Security

| Aspek | Implementasi | Referensi |
|-------|-------------|-----------|
| **Authentication** | Laravel Breeze + Sanctum (session-based). Login, register, password reset, email verification | `routes/auth.php`, `config/auth.php` |
| **Authorization** | Role-based (admin/petugas/nasabah) via middleware `role`. Policy classes untuk per-action authorization. Gate `manage-users` hanya admin | `app/Http/Middleware/EnsureUserHasRole.php`, `app/Policies/`, `app/Providers/AuthServiceProvider.php` |
| **Middleware** | `auth` (session auth), `role:admin,petugas` (role check), `BlockNasabahFromAdmin` (blokir nasabah dari admin URL), `HandleInertiaRequests`, `signed` (signed URLs), `throttle:6,1` (rate limit) | `bootstrap/app.php` |
| **Validation** | Form Request classes untuk admin operations. Inline `$request->validate()` untuk public/nasabah | `app/Http/Requests/Admin/`, controllers |
| **CSRF** | Laravel built-in CSRF protection (PreventRequestForgery middleware) | `bootstrap/app.php`, meta tag di blade/Inertia |
| **XSS** | Blade: `{{ }}` auto-escape. React/Inertia: auto-escape by default | — |
| **SQL Injection** | Eloquent ORM & query builder parameter binding | Seluruh models menggunakan Eloquent |
| **Password** | `Hash::make()` / bcrypt via `Rules\Password::defaults()` | `RegisteredUserController`, `User::$casts` |
| **Session** | Database session driver, HTTP only, SameSite=lax, JSON serialization | `config/session.php` |
| **File Upload** | Artikel image: upload ke storage/public, validasi path, delete file saat artikel dihapus. Hanya admin/petugas | `app/Models/Article.php` (boot deleting) |
| **Rate Limit** | Email verification: `throttle:6,1` (6 requests per menit) | `routes/auth.php` |
| **Guest Block** | Nasabah pending → di-logout oleh middleware jika coba akses route nasabah | `EnsureUserHasRole.php` |

---

# 13. Coding Convention

Berdasarkan analisis source code:

## Penamaan

| Elemen | Convention | Contoh |
|--------|-----------|--------|
| **Controllers** | PascalCase, suffix Controller | `NasabahController`, `DepositController` |
| **Admin Controllers** | Namespace Admin\ | `Admin\DepositController` |
| **Auth Controllers** | Namespace Auth\ | `Auth\RegisteredUserController` |
| **Models** | PascalCase, singular | `User`, `TrashPrice`, `PickupRequest` |
| **Services** | PascalCase, suffix Service | `TransactionService`, `GamificationService` |
| **Policies** | PascalCase, suffix Policy | `DepositPolicy`, `UserPolicy` |
| **Events** | PascalCase, by domain directory | `Deposit\DepositApproved`, `Withdrawal\WithdrawalRejected` |
| **Requests** | PascalCase, descriptive | `ApproveDepositRequest`, `StoreUserRequest` |
| **Middleware** | PascalCase | `EnsureUserHasRole`, `BlockNasabahFromAdmin` |
| **Migrations** | `YYYY_MM_DD_HHMMSS_descriptive` | `2026_06_14_150825_add_fields_to_users_table.php` |
| **Routes** | kebab-case URI, snake_case dot-notation name | `/nasabah/dashboard`, `cms.deposits.approve` |
| **Blade Views** | kebab-case | `admin/deposits/index.blade.php` |
| **React Pages** | PascalCase, match route | `Nasabah/Dashboard.jsx`, `Public/ArticleDirectory.jsx` |
| **DB Columns** | snake_case | `total_price`, `pickup_address`, `carbon_factor` |
| **Attributes** | PHP 8 attributes | `#[Fillable([...])]`, `#[Hidden([...])]` |

## Architecture Patterns

- **Service Layer**: Business logic dipisah dari Controller ke Service class.
- **Form Request**: Validasi dipisah ke dedicated Request class untuk admin operations.
- **Policy**: Authorization per action dipisah ke Policy class.
- **Event-driven**: Side effects (logging, notification, badge sync) via Events + Listeners.
- **Blade CMS baru**: Controller return `view()` → Blade dengan Blade components.
- **Inertia Pages**: Controller return `Inertia::render()` → React component.
- **Dependency Injection**: Constructor injection untuk services (e.g., `DepositController` inject `TransactionService`).

## Code Style

- PHP 8.3 features: attributes, readonly property promotion, match expression.
- Laravel 13 conventions.
- Filament Resources menggunakan struktur standard Filament.
- React menggunakan functional components + hooks.
- TailwindCSS utility classes, no custom CSS files ditemukan selain Tailwind config.

---

# 14. API

**Tidak ditemukan REST API endpoints pada implementasi project.**

Yang ditemukan:
- Semua komunikasi data via server-rendered pages (Blade) atau Inertia.js (server-side rendering React).
- Tidak ada route dengan prefix `/api/`.
- Sanctum terinstall tetapi hanya digunakan untuk session-based auth (Breeze default), bukan token API.
- Exception handler di `bootstrap/app.php` mengatur `shouldRenderJsonWhen` untuk request `api/*`, tetapi tidak ada route yang menggunakannya.

**Catatan**: Tidak ada API endpoint yang terdaftar. Semua interaksi terjadi melalui web routes.

---

# 15. Folder Penting untuk Developer

| Folder | Fungsi | Sering Diakses? |
|--------|--------|----------------|
| `app/Http/Controllers/` | Controller logic — entry point untuk setiap request | Ya |
| `app/Http/Controllers/Admin/` | Controller untuk CMS admin (Blade) | Ya |
| `app/Models/` | Eloquent models — definisi relasi, casts, fillable | Ya |
| `app/Services/` | Business logic layer — TransactionService, GamificationService, DashboardService | Ya |
| `app/Policies/` | Authorization rules per model | Kadang |
| `app/Http/Requests/` | Form validation classes | Kadang |
| `app/Providers/` | Service providers, registrasi event/policy/gate | Jarang |
| `app/Events/` + `app/Listeners/` | Event-driven side effects | Kadang |
| `app/Filament/` | Filament resources, widgets (legacy admin) | Jarang (migrasi ke CMS) |
| `routes/` | Semua route definitions | Ya |
| `database/migrations/` | Schema definitions | Ya |
| `database/seeders/` | Data awal/aplikasi | Kadang |
| `resources/js/` | React frontend (Inertia) — Pages, Components, Layouts | Ya |
| `resources/js/Pages/` | React page components per route | Ya |
| `resources/js/Components/` | Shared React components | Kadang |
| `resources/js/Layouts/` | Layout wrappers (NasabahLayout, PublicLayout, AuthenticatedLayout, GuestLayout) | Kadang |
| `resources/views/` | Blade templates — admin CMS, layouts, components, print | Ya |
| `resources/views/admin/` | CMS admin Blade views (dashboard, deposits, withdrawals, dll) | Ya |
| `resources/views/components/admin/` | Blade admin components (card, table, modal, form/input, dll) | Kadang |
| `config/` | Laravel configuration files | Jarang |
| `bootstrap/app.php` | App bootstrap: routing loading, middleware registration, exception handling | Kadang |
| `tailwind.config.js` | TailwindCSS configuration | Jarang |
| `vite.config.js` | Vite build configuration | Jarang |
| `composer.json` | PHP dependencies | Jarang |
| `package.json` | JS dependencies | Jarang |
| `public/` | Public assets, entry point | Jarang (kecuali assets statis) |
| `storage/` | Logs, cache, session, uploaded files (link ke public/storage) | Kadang |
| `docs/` | Project documentation | Kadang |

---

# 16. Ringkasan

**Bank Sampah Digital Faperta** adalah aplikasi web berbasis Laravel 13 + React (Inertia) untuk pengelolaan bank sampah di lingkungan Fakultas Pertanian (Faperta) dan masyarakat umum.

## Arsitektur

Aplikasi menggunakan arsitektur **MVC dengan Service Layer** dan **Event-Driven pattern**. Terdapat dua panel admin: (1) **Filament** (legacy, `/admin` — masih berfungsi) dan (2) **Blade CMS baru** (`/cms` — sedang dalam migrasi). Frontend publik dan nasabah menggunakan **Inertia.js + React** (SPA-like), sementara admin menggunakan Blade tradisional dengan komponen reusable.

## Database

Database terdiri dari **12 tabel aplikasi** + 6 tabel Laravel built-in. Sistem menggunakan **double-entry ledger** (`mutations` table) untuk mencatat perubahan saldo nasabah secara immutable. Setiap perubahan saldo (deposit approved = kredit, withdrawal approved = debit) tercatat dengan balance_before dan balance_after.

## Role System

Tiga role: **admin** (full akses), **petugas** (operasional), **nasabah** (end-user). Authorization diatur via middleware `role:` dan Policy classes per-model. Nasabah harus diverifikasi admin (status pending → verified) sebelum bisa menggunakan aplikasi.

## Alur Bisnis Utama

1. **Deposit**: Nasabah setor sampah → petugas catat (pending) → timbang real weight → approve → saldo nasabah bertambah (jika bukan donasi) → catat mutation → log aktivitas
2. **Withdrawal**: Nasabah ajukan penarikan (pending) → admin/petugas approve → potong saldo + admin fee (Rp 2.500 non-BTN transfer) → catat mutation → withdrawal history
3. **Pickup**: Nasabah ajukan jemput sampah (max 2 km) → admin assign petugas → petugas jemput → selesai → bisa link ke deposit
4. **Gamifikasi**: Setiap deposit approved hitung EcoPoints → 5 level → 10 badge auto-unlock

## Keamanan

Menggunakan authentication session-based (Breeze), authorization role + policy, validasi form request, CSRF protection, auto-escape output (Blade + React), Eloquent ORM untuk proteksi SQL injection, serta middleware blocker untuk nasabah di admin area.

## Catatan Development

- **Default database**: SQLite (file-based, zero config). Cocok untuk development.
- **Queue**: Database-based. Wajib jalan `php artisan queue:listen` untuk proses async.
- **Cache**: Database-based. Dashboard overview stats cache 5 menit.
- **Map**: MapLibre GL untuk geolokasi pickup request.
- **Migrasi aktif**: Filament ke Blade CMS (`/admin` → `/cms`). Kedua sistem masih berfungsi.
- **Tidak ada REST API**: Komunikasi data via server-rendered pages dan Inertia.
- **Session**: Database sessions, bukan file.
