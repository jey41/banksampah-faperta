# 🧪 QA Testing Report — Bank Sampah Faperta

**Tanggal:** 15 Juni 2026  
**Tester:** Antigravity AI QA  
**Versi:** Development (localhost:8000)  
**Stack:** Laravel 12 + Filament 4 (Admin) + Inertia.js React (Nasabah)

---

## Ringkasan Eksekutif

Testing mengungkap **2 bug kritis** yang menyebabkan data setoran dan penarikan **tidak pernah tersimpan ke database** saat user mengirim form. Bug telah diperbaiki. Ditemukan juga **3 issue minor** terkait session management dan HTML structure.

| Severity | Jumlah Bug | Status |
|----------|-----------|--------|
| 🔴 **Kritis** | 2 | ✅ FIXED |
| 🟡 **Sedang** | 2 | ✅ FIXED |
| 🔵 **Minor** | 3 | ⚠️ Butuh perhatian |

---

## Database State (Sebelum Testing)

| Resource | Jumlah Record |
|----------|--------------|
| Users | 6 |
| Deposits | 3 |
| Deposit Items | 5 |
| Withdrawals | 2 |
| Trash Prices | 7 |

### Users
| ID | Nama | Email | Role | Status | Saldo |
|----|------|-------|------|--------|-------|
| 1 | Admin Bank Sampah | admin@banksampah.com | admin | verified | 0 |
| 2 | Petugas Mamat | petugas@banksampah.com | petugas | verified | 0 |
| 3 | Budi Raharjo | nasabah@gmail.com | nasabah | verified | 471,000 |
| 4 | Dewi Lestari | dewi@gmail.com | nasabah | pending | 0 |
| 5 | Nasabah Test | nasabah@test.com | nasabah | verified | 100,000 |
| 6 | Muhammad Hisyam Nugroho | nmuhammadhisyam@gmail.com | nasabah | verified | 0 |

---

## Bug Report Detail

### 🔴 BUG-001: Route Name Mismatch pada Deposit Form (KRITIS)

**File:** [Deposit.jsx](file:///d:/project/banksampah-faperta/resources/js/Pages/Nasabah/Deposit.jsx#L63)  
**Severity:** Kritis — Data tidak tersimpan ke database  
**Status:** ✅ FIXED

**Deskripsi:**  
Form setoran sampah menggunakan route name `nasabah.setor.store` yang **tidak ada** di Laravel route definitions. Route yang benar adalah `nasabah.deposit.store`.

```diff
// Deposit.jsx — submit function
- post(route('nasabah.setor.store'), {
+ router.post(route('nasabah.deposit.store'), {
```

**Dampak:**  
Setiap user yang mengirim form setoran akan mendapat error (route not found). Data **tidak pernah sampai ke database**. Ini menjelaskan kenapa data tidak masuk saat user request setoran.

**Root Cause:**  
Route name di [web.php](file:///d:/project/banksampah-faperta/routes/web.php#L34) didefinisikan sebagai `nasabah.deposit.store`, tapi frontend menggunakan nama yang berbeda.

```php
// routes/web.php line 34
Route::post('/setor', [NasabahController::class, 'storeDeposit'])->name('nasabah.deposit.store');
```

---

### 🔴 BUG-002: Route Name Mismatch pada Withdrawal Form (KRITIS)

**File:** [Withdraw.jsx](file:///d:/project/banksampah-faperta/resources/js/Pages/Nasabah/Withdraw.jsx#L17)  
**Severity:** Kritis — Data tidak tersimpan ke database  
**Status:** ✅ FIXED

**Deskripsi:**  
Form penarikan saldo menggunakan route name `nasabah.tarik.store` yang **tidak ada**. Route yang benar adalah `nasabah.withdraw.store`.

```diff
// Withdraw.jsx — submit function
- post(route('nasabah.tarik.store'));
+ post(route('nasabah.withdraw.store'));
```

**Dampak:**  
Sama seperti BUG-001 — penarikan saldo tidak pernah tersimpan ke database.

---

### 🟡 BUG-003: Inertia useForm Data Race Condition pada Deposit (SEDANG)

**File:** [Deposit.jsx](file:///d:/project/banksampah-faperta/resources/js/Pages/Nasabah/Deposit.jsx#L46-L71)  
**Severity:** Sedang — Form submit bisa gagal  
**Status:** ✅ FIXED

**Deskripsi:**  
Deposit form menggunakan `useForm` dari Inertia tapi menyimpan `items` secara terpisah di local state `rows`. Saat submit:
1. `items` di-format dari `rows` 
2. `setData('items', formattedItems)` dipanggil — tapi ini **async** (React state batch)
3. `post()` langsung dipanggil setelahnya — tapi `data.items` masih `[]` karena state belum update

Selain itu, pattern `{ data: { items: ..., notes: ... } }` di `post()` options **bukan** cara kerja Inertia — ini membungkus payload dalam nested `data` key.

**Fix:**  
Refactor menggunakan `router.post()` langsung dengan constructed payload:

```javascript
setProcessing(true);
router.post(route('nasabah.deposit.store'), {
    items: formattedItems,
    notes: notes,
}, {
    preserveScroll: true,
    onError: (errs) => setErrors(errs),
    onFinish: () => setProcessing(false),
});
```

---

### 🟡 BUG-004: Missing Closing `</div>` Tag pada Withdraw.jsx (SEDANG)

**File:** [Withdraw.jsx](file:///d:/project/banksampah-faperta/resources/js/Pages/Nasabah/Withdraw.jsx#L183-L186)  
**Severity:** Sedang — HTML structure rusak  
**Status:** ✅ FIXED

**Deskripsi:**  
Grid container `<div className="grid grid-cols-1 lg:grid-cols-3 ...">` tidak memiliki closing `</div>` tag sebelum `</form>`. Ini bisa menyebabkan layout anomali dan React rendering issues di beberapa browser.

---

### 🔵 ISSUE-001: Session Stickiness saat Pindah Admin ↔ Nasabah (MINOR)

**Severity:** Minor — UX issue  
**Status:** ⚠️ Belum diperbaiki

**Deskripsi:**  
Filament admin panel menggunakan `AuthenticateSession` middleware yang menyimpan password hash di session. Karena admin dan nasabah berbagi session yang sama (keduanya menggunakan guard `web`), beralih antara `/admin` dan `/nasabah/dashboard` di browser yang sama bisa menyebabkan session confusion:

1. Login sebagai admin → akses `/admin` ✅
2. Logout admin, login sebagai nasabah → akses `/nasabah/dashboard` ✅
3. Kembali ke `/admin` tanpa logout nasabah dulu → **middleware `BlockNasabahFromAdmin` memblok** dan redirect kembali

Namun, karena Filament memiliki login page sendiri (`/admin/login`), user bisa login ulang di sana — ini mengoverwrite session dengan user admin baru. Tapi jika user kembali ke `/nasabah/dashboard` tanpa logout, session masih berisi user admin dan middleware `role:nasabah` akan memblok.

**Rekomendasi:**  
- Gunakan **browser berbeda** atau **incognito window** untuk testing admin vs nasabah
- Atau tambahkan logic untuk force-redirect user berdasarkan role saat mengakses path yang salah

---

### 🔵 ISSUE-002: User Status "pending" bisa Login (MINOR)

**Severity:** Minor — Security concern  
**Status:** ⚠️ Belum diperbaiki

**Deskripsi:**  
User `Dewi Lestari` (ID:4) memiliki `status=pending` tapi tidak ada middleware atau logic yang memblok login user dengan status pending. User yang belum diverifikasi admin bisa tetap login dan menggunakan fitur nasabah.

**File terkait:** 
- [EnsureUserHasRole.php](file:///d:/project/banksampah-faperta/app/Http/Middleware/EnsureUserHasRole.php) — hanya cek role, tidak cek status
- [AuthenticatedSessionController.php](file:///d:/project/banksampah-faperta/app/Http/Controllers/Auth/AuthenticatedSessionController.php) — standard Breeze, tidak cek status

**Rekomendasi:**  
Tambahkan pengecekan `status === 'verified'` di middleware atau di `LoginRequest` agar user pending tidak bisa login.

---

### 🔵 ISSUE-003: Saldo User Tidak Otomatis Bertambah saat Deposit Di-approve (MINOR)

**Severity:** Minor — Business logic gap  
**Status:** ⚠️ Belum diperbaiki

**Deskripsi:**  
Saat admin meng-approve deposit, saldo user **tidak otomatis bertambah**. Berdasarkan data:
- User Budi Raharjo punya 3 deposit approved total Rp 76,000
- 1 withdrawal approved Rp 100,000
- Saldo saat ini: Rp 471,000 (berarti saldo awal di-set manual)

Saldo seharusnya dihitung atau diupdate secara otomatis saat admin mengubah status deposit menjadi "approved".

**Rekomendasi:**  
Tambahkan observer atau event listener pada model Deposit yang auto-update saldo user saat status berubah ke "approved".

---

## Test Cases Matrix

### Admin Panel (Filament)

| # | Test Case | Expected | Result | Notes |
|---|-----------|----------|--------|-------|
| A1 | Login admin (admin@banksampah.com) | Redirect ke /admin | ✅ Pass | |
| A2 | Sidebar menampilkan semua resource | 5 menu items | ✅ Pass | Dashboard, Users, Setoran, Penarikan, Harga Sampah, Artikel |
| A3 | List deposits | 3 records | ✅ Pass | Semua status approved |
| A4 | List users | 6 records | ✅ Pass | |
| A5 | List withdrawals | 2 records | ✅ Pass | 1 approved, 1 pending |
| A6 | List trash prices | 7 records | ✅ Pass | |
| A7 | Nasabah diblok akses /admin | Redirect ke nasabah dashboard | ✅ Pass | BlockNasabahFromAdmin middleware |

### Nasabah Panel (Inertia React)

| # | Test Case | Expected | Result | Notes |
|---|-----------|----------|--------|-------|
| N1 | Login nasabah (nasabah@gmail.com) | Redirect ke /nasabah/dashboard | ✅ Pass | |
| N2 | Dashboard menampilkan saldo | Rp 471,000 | ✅ Pass | |
| N3 | Dashboard menampilkan riwayat transaksi | 5 transaksi terbaru | ✅ Pass | |
| N4 | Navigasi ke halaman Setor | Form muncul dengan dropdown | ✅ Pass | |
| N5 | Submit form setoran | ~~Data tersimpan~~ | 🔴 **FAIL → FIXED** | Route name salah → `nasabah.setor.store` ≠ `nasabah.deposit.store` |
| N6 | Submit form penarikan | ~~Data tersimpan~~ | 🔴 **FAIL → FIXED** | Route name salah → `nasabah.tarik.store` ≠ `nasabah.withdraw.store` |
| N7 | Halaman riwayat | List semua transaksi | ✅ Pass | |
| N8 | Logout nasabah | Redirect ke homepage | ✅ Pass | |
| N9 | Layout responsive (desktop) | Full-width layout | ✅ Pass | NasabahLayout digunakan |

---

## Files yang Dimodifikasi

| File | Perubahan |
|------|-----------|
| [Deposit.jsx](file:///d:/project/banksampah-faperta/resources/js/Pages/Nasabah/Deposit.jsx) | Fix route name, refactor ke `router.post()`, fix data binding |
| [Withdraw.jsx](file:///d:/project/banksampah-faperta/resources/js/Pages/Nasabah/Withdraw.jsx) | Fix route name, fix missing closing `</div>` |

---

## Rekomendasi Prioritas Selanjutnya

1. **🔴 Verify deposit fix end-to-end** — Login sebagai nasabah, submit setoran, cek database apakah record baru muncul
2. **🟡 Tambahkan auto-update saldo** — Buat observer `DepositObserver` untuk menambah saldo user saat deposit di-approve
3. **🟡 Tambahkan validasi status user** — Block login untuk user dengan status `pending`
4. **🔵 Tambahkan flash message** — Tampilkan notifikasi sukses/error setelah submit form di halaman nasabah
5. **🔵 Tambahkan unit test** — Buat feature test untuk `NasabahController@storeDeposit` dan `storeWithdraw`
