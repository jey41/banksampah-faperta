# Laporan Audit QA — Admin Panel Bank Sampah Faperta

**Tanggal Audit:** 5 Juli 2026  
**Auditor:** Senior QA Engineer  
**Lingkup:** Seluruh modul Admin Panel (`/cms/*`)  
**Metode:** Analisis kode statis

---

## 1. Ringkasan Eksekutif

Admin Panel Bank Sampah Faperta telah dibangun dengan arsitektur yang solid dan pola pengembangan yang baik secara umum. Struktur kode terorganisir rapi, otorisasi berbasis Policy telah diterapkan secara konsisten di sebagian besar modul, dan alur transaksi (setoran, penarikan) telah menggunakan pessimistic locking untuk mencegah race condition.

Namun, terdapat **dua bug kritis** yang akan menyebabkan error runtime (RouteNotFoundException) pada halaman Dashboard. Selain itu, ditemukan beberapa validasi yang kurang ketat, inkonsistensi pola otorisasi pada modul tertentu, serta celah performa yang perlu diperhatikan sebelum rilis produksi.

Secara keseluruhan, aplikasi memiliki fondasi yang kuat tetapi memerlukan perbaikan pada beberapa area penting sebelum dinyatakan siap produksi.

---

## 2. Ringkasan Kelayakan

| Aspek | Skor | Keterangan |
|-------|------|------------|
| **Stabilitas** | 70/100 | 2 bug kritis pada Dashboard, beberapa N+1 query potensial |
| **Fungsionalitas** | 75/100 | Sebagian besar fitur berfungsi, ada fitur belum lengkap (upload foto, preview) |
| **UI/UX** | 80/100 | Konsisten secara visual, breadcrumb tidak berfungsi, loading state tidak ada |
| **Keamanan** | 85/100 | Otorisasi baik, CSRF terpasang, ada celah validasi ENUM |
| **Maintainability** | 80/100 | Kode terstruktur, ada inkonsistensi pola otorisasi (abort_unless vs Policy) |

---

## 3. Daftar Bug

### Critical

#### BUG-001: Dashboard — Seluruh link ringkasan dengan query parameter error

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Dashboard |
| **Lokasi** | `resources/views/admin/dashboard/index.blade.php:43-49` |
| **Deskripsi** | Link ke halaman setoran pending, penarikan pending, dll. menggunakan sintaks `route('cms.deposits.index?status=pending')`. Method `route()` menginterpretasi seluruh string sebagai nama route, bukan route name + parameter. Route `cms.deposits.index?status=pending` tidak terdaftar, sehingga akan melempar `Symfony\Component\Routing\Exception\RouteNotFoundException`. |
| **Dampak** | Halaman Dashboard tidak dapat diakses sama sekali — error 500 saat memuat. |
| **Severity** | **Critical** |

**Kode bermasalah:**
```php
// dashboard/index.blade.php line 43-45
['Setoran Pending', $pendingDeposits, 'deposits.index?status=pending', 'text-amber-600'],
['Penarikan Pending', $pendingWithdrawals, 'withdrawals.index?status=pending', 'text-red-600'],

// line 49
<a href="{{ route('cms.' . $r[2]) }}" ...>
```

**Fix:** Ubah menjadi `route('cms.deposits.index', ['status' => 'pending'])`.

---

#### BUG-002: Harga Sampah — Input kategori menggunakan free text untuk ENUM database

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Harga Sampah |
| **Lokasi** | `resources/views/admin/trash-prices/create.blade.php:13`, `app/Http/Requests/Admin/TrashPriceRequest.php:21` |
| **Deskripsi** | Field `category` pada form adalah text input bebas (`<input>`), namun kolom database `trash_prices.category` didefinisikan sebagai ENUM dengan nilai `['plastik', 'kertas', 'logam', 'kaca', 'minyak_jelantah', 'lainnya']` (lihat `migrations/2026_06_14_150826_create_bank_sampah_tables.php:17`). Validasi `TrashPriceRequest` hanya memeriksa `['required', 'string', 'max:255']` tanpa membatasi nilai ENUM. Input di luar ENUM akan menyebabkan error database di mode strict MySQL. |
| **Dampak** | Error database saat menyimpan kategori yang tidak terdaftar dalam ENUM. |
| **Severity** | **Critical** |

**Fix:** Ubah input text menjadi `<select>` dengan opsi sesuai ENUM, atau validasi dengan `Rule::in(['plastik', 'kertas', ...])` pada FormRequest.

---

### High

#### BUG-003: Target Tabungan — Field pencarian tidak ada di view

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Target Tabungan |
| **Lokasi** | `resources/views/admin/savings-targets/index.blade.php` (tidak ada input search), `app/Http/Controllers/Admin/SavingsTargetController.php:19-22` (ada logika search) |
| **Deskripsi** | Controller `SavingsTargetController@index` memiliki logika pencarian (`where('title', 'like', ...)`) tetapi tidak ada input pencarian pada view `savings-targets/index.blade.php`. Fitur search tidak dapat diakses oleh admin. |
| **Dampak** | Search tidak berfungsi meskipun kode sudah siap. Admin tidak bisa mencari target tabungan. |
| **Severity** | **High** |

---

#### BUG-004: Permintaan Jemput — Status "assigned" tanpa petugas ditugaskan

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Permintaan Jemput |
| **Lokasi** | `app/Http/Requests/Admin/UpdatePickupRequestRequest.php:17-20` |
| **Deskripsi** | Tidak ada validasi bahwa field `assigned_to` wajib diisi ketika status diubah menjadi `assigned`. Admin dapat mengubah status menjadi "Ditugaskan" tanpa memilih petugas. |
| **Dampak** | Permintaan jemput berstatus "Ditugaskan" tetapi tidak memiliki petugas yang bertanggung jawab. |
| **Severity** | **High** |

---

#### BUG-005: Target Tabungan — `is_achieved` tidak pernah diperbarui setelah transaksi

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Target Tabungan |
| **Lokasi** | `app/Http/Controllers/Admin/SavingsTargetController.php:40,58` |
| **Deskripsi** | `is_achieved` dihitung hanya saat create/update target berdasarkan saldo saat itu. Ketika nasabah melakukan setoran atau penarikan, status `is_achieved` tidak pernah direkalkulasi. Target yang sudah tercapai bisa menjadi "belum tercapai" lagi tanpa update manual dari admin. |
| **Dampak** | Status pencapaian target tidak akurat setelah transaksi baru terjadi. |
| **Severity** | **High** |

---

#### BUG-006: Harga Sampah — Harga jual (price_sell) tidak bisa dikosongkan

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Harga Sampah |
| **Lokasi** | `app/Http/Requests/Admin/TrashPriceRequest.php:32-36` |
| **Deskripsi** | Method `prepareForValidation` mengubah `price_sell` dan `carbon_factor` menjadi 0 jika tidak diisi. Ini menyebabkan input kosong tidak dapat dibedakan dari input sengaja 0. Jika admin ingin menghapus harga jual, sistem tetap menyimpan 0. |
| **Dampak** | Data tidak akurat secara semantik — 0 bisa berarti "gratis" atau "tidak diisi". |
| **Severity** | **High** |

---

### Medium

#### BUG-007: Dashboard — Query N+1 dan multiple separate queries

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Dashboard |
| **Lokasi** | `app/Http/Controllers/Admin/DashboardController.php:20-57` |
| **Deskripsi** | Dashboard menjalankan 10+ query SELECT terpisah untuk mengumpulkan statistik (total_donation_profit, total_weight, retained_balance, active_nasabah, deposits_today, dll). Tidak ada agregasi dalam satu query. Untuk dataset besar, ini bisa menyebabkan slow page load. |
| **Severity** | **Medium** |

---

#### BUG-008: Semua halaman — Breadcrumb tidak berfungsi

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Seluruh modul |
| **Lokasi** | `resources/views/admin/partials/breadcrumbs.blade.php` |
| **Deskripsi** | File breadcrumb menggunakan `@hasSection('breadcrumbs')` untuk menampilkan breadcrumb, tetapi tidak ada satu pun view admin yang mendefinisikan section `@section('breadcrumbs')`. Breadcrumb tidak pernah muncul. |
| **Dampak** | Navigasi breadcrumb tidak tersedia di seluruh halaman admin. |
| **Severity** | **Medium** |

---

#### BUG-009: Deposit — Approve form tidak memiliki konfirmasi

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Setoran Sampah — Approve |
| **Lokasi** | `resources/views/admin/deposits/show.blade.php:70` |
| **Deskripsi** | Form approve setoran dikirim langsung tanpa konfirmasi. Sebaliknya, form approve penarikan (`withdrawals/show.blade.php:11`) menggunakan `onsubmit="return confirm(...)"`. Inkonsisten dan berisiko admin tidak sengaja menyetujui setoran. |
| **Severity** | **Medium** |

---

#### BUG-010: Activity Log — Tidak ada mekanisme pruning

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Log Aktivitas |
| **Lokasi** | `app/Http/Controllers/Admin/ActivityLogController.php`, `app/Models/ActivityLog.php` |
| **Deskripsi** | Tidak ada mekanisme pembersihan (pruning) log otomatis. Setiap aksi admin dicatat tanpa batas waktu retensi. Tabel `activity_logs` akan tumbuh tanpa batas, memperlambat query seiring waktu. |
| **Severity** | **Medium** |

---

#### BUG-011: Gamification — EcoPoints dihitung ulang setiap saat tanpa cache

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Lencana / Gamification |
| **Lokasi** | `app/Services/GamificationService.php:102-126` |
| **Deskripsi** | `getEcoPoints()` menjalankan beberapa query setiap kali dipanggil (hitung weight, count deposits, hitung streak, hitung diversity). Untuk nasabah dengan banyak transaksi, ini akan lambat. Sebaiknya di-cache atau disimpan sebagai kolom di tabel users. |
| **Severity** | **Medium** |

---

#### BUG-012: Semua halaman — Tailwind CSS via CDN

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Layout Admin |
| **Lokasi** | `resources/views/layouts/admin.blade.php:9-24` |
| **Deskripsi** | Tailwind CSS dimuat dari CDN (`cdn.tailwindcss.com`), bukan dari build Vite. Ini menyebabkan: (1) dependensi pada koneksi internet, (2) performa lebih lambat, (3) tidak bisa menggunakan fitur Tailwind yang memerlukan compile (seperti `@apply`, purge CSS). |
| **Severity** | **Medium** |

---

### Low

#### BUG-013: Pengguna — Tidak ada upload foto profil

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Manajemen Pengguna |
| **Lokasi** | Seluruh view `admin/users/*` |
| **Deskripsi** | Tidak ada field upload foto pada form create/edit user, meskipun disebutkan dalam ruang lingkup audit. |
| **Severity** | **Low** |

---

#### BUG-014: Penarikan — Tidak ada filter metode penarikan di view

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Penarikan Saldo |
| **Lokasi** | `resources/views/admin/withdrawals/index.blade.php` |
| **Deskripsi** | Controller menyediakan filter `withdrawal_method` (lihat `WithdrawalController.php:25`), tetapi tidak ada dropdown filter metode di view. Fitur tidak bisa digunakan. |
| **Severity** | **Low** |

---

#### BUG-015: Artikel — Tidak ada preview

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Artikel |
| **Lokasi** | `resources/views/admin/articles/*` |
| **Deskripsi** | Tidak ada tombol "Preview" untuk melihat tampilan artikel sebelum dipublikasikan. Admin harus mengubah status ke "published" terlebih dahulu untuk melihat hasilnya. |
| **Severity** | **Low** |

---

#### BUG-016: Konsistensi — Breadcrumb tidak digunakan di view manapun

| Atribut | Nilai |
|---------|-------|
| **Fitur** | Seluruh modul |
| **Lokasi** | Semua view admin |
| **Deskripsi** | Tidak ada satu pun view yang mendefinisikan section breadcrumbs. Fitur ini mati total. |
| **Severity** | **Low** |

---

## 4. Fitur yang Tidak Berfungsi

| Fitur | Lokasi | Status | Keterangan |
|-------|--------|--------|------------|
| **Breadcrumb Navigasi** | `resources/views/admin/partials/breadcrumbs.blade.php` | ❌ Tidak berfungsi | Tidak ada view yang mendefinisikan `@section('breadcrumbs')`. |
| **Dashboard — Link Ringkasan dengan Query Param** | `dashboard/index.blade.php:43-49` | ❌ Error | RouteNotFoundException pada link ke setoran pending, penarikan pending, jemput menunggu. |
| **Target Tabungan — Pencarian** | `SavingsTargetController.php:19` | ❌ Tidak bisa diakses | Logika search ada di controller, tidak ada input di view. |
| **Penarikan — Filter Metode** | `WithdrawalController.php:25` | ❌ Tidak bisa diakses | Logika filter ada di controller, tidak ada dropdown di view. |
| **Lencana — CRUD** | `BadgeController.php` | ⚠️ Read-only | Badge didefinisikan secara programatik, tidak ada form CRUD. Ini desain, bukan bug. |

---

## 5. Validasi yang Kurang

| Modul | Field | Validasi Saat Ini | Seharusnya |
|-------|-------|-------------------|------------|
| **Harga Sampah** | `category` | `['required', 'string', 'max:255']` | `Rule::in(['plastik', 'kertas', 'logam', 'kaca', 'minyak_jelantah', 'lainnya'])` — menyesuaikan ENUM database |
| **Permintaan Jemput** | `assigned_to` saat status = `assigned` | `['nullable', 'exists:users,id']` | Validasi conditional: wajib diisi jika status adalah `assigned` |
| **Pengguna** | `password` (store) | `['required', 'string', 'min:8']` | Tambahkan `Password::defaults()` (huruf besar, angka, simbol) atau minimal `confirmed` |
| **Pengguna** | `password` (update) | `['nullable', 'string', 'min:8']` | Sama seperti di atas — password bisa diubah menjadi lemah |
| **Target Tabungan** | `target_amount` | `['required', 'integer', 'min:10000']` | Tambahkan `max:999999999` untuk mencegah input tidak realistis |
| **Artikel** | `image` | `['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']` | Sudah cukup baik, hanya perlu tambahan dimensi maksimal (misal `dimensions:max_width=1920`) |

---

## 6. Temuan UI/UX

### 6.1 Masalah Signifikan

| No | Temuan | Lokasi | Rekomendasi |
|----|--------|--------|-------------|
| 1 | **Tidak ada loading state** pada tombol approve/reject | `deposits/show.blade.php`, `withdrawals/show.blade.php` | Tambahkan disabled state + spinner pada tombol saat form disubmit untuk mencegah double-submit. |
| 2 | **Empty state sudah ada** tetapi hanya teks | Semua view `index.blade.php` | Tambahkan ilustrasi atau ikon pada empty state agar lebih informatif. |
| 3 | **Tidak ada pagination info** | Semua view dengan pagination | Tambahkan teks "Menampilkan 1-15 dari 30 data" di atas/bawah tabel. |
| 4 | **Pagination menggunakan DataTables** | `layouts/admin.blade.php:29-30` | DataTables di-load untuk semua halaman, bahkan yang tidak menggunakan tabel. Pindahkan ke stack `@push('scripts')` per halaman yang membutuhkan. |
| 5 | **Tidak ada konfirmasi approve setoran** | `deposits/show.blade.php` | Tambahkan konfirmasi dialog (konsisten dengan modul penarikan). |

### 6.2 Masalah Minor

| No | Temuan | Lokasi | Rekomendasi |
|----|--------|--------|-------------|
| 6 | **Navbar dropdown menggunakan CSS `focus`** | `navbar.blade.php:28` | Dropdown hanya muncul saat tombol di-focus (klik), bukan hover. Bisa menyulitkan pengguna mobile. |
| 7 | **Warna status "Tabungan" pada deposit** | `deposits/index.blade.php:49` | Deposit biasa (bukan donasi) hanya menampilkan teks `Tabungan` tanpa badge/warna — kurang menonjol. |
| 8 | **Tidak ada tooltip pada tombol aksi** | Semua view | Tombol "Edit", "Hapus", "Detail" tidak memiliki tooltip atau aria-label. |

---

## 7. Temuan Keamanan

| No | Temuan | Severity | Lokasi | Detail |
|----|--------|----------|--------|--------|
| 1 | **Validasi ENUM tidak memadai** | **High** | `TrashPriceRequest.php:21` | Input bebas untuk field ENUM database dapat menyebabkan error atau data inkonsisten. |
| 2 | **Password tanpa kompleksitas** | **Medium** | `StoreUserRequest.php:20` | Hanya min:8, tanpa syarat huruf besar, angka, atau simbol. Gunakan `Password::defaults()` Laravel. |
| 3 | **Otorisasi tidak konsisten** | **Medium** | `SavingsTargetController.php`, `BadgeController.php` | Menggunakan `abort_unless(auth()->user()->isStaff(), 403)` alih-alih Policy. Jika ada perubahan role definition, otorisasi bisa terlewat. |
| 4 | **CSRF sudah terpasang** ✅ | - | Semua form | Setiap form menggunakan `@csrf` — baik. |
| 5 | **Pessimistic locking pada transaksi** ✅ | - | `TransactionService.php` | Transaksi menggunakan `lockForUpdate()` — sangat baik untuk mencegah race condition. |
| 6 | **Middleware pemblokiran nasabah** ✅ | - | `bootstrap/app.php`, `BlockNasabahFromAdmin.php` | Nasabah diblokir dari akses `/admin*` dan `/cms*` — baik. |

---

## 8. Temuan Performa

| No | Temuan | Severity | Lokasi | Detail |
|----|--------|----------|--------|--------|
| 1 | **Multiple query dashboard** | **High** | `DashboardController.php` | 10+ query SELECT terpisah untuk statistik. Bisa di-agregasi atau di-cache untuk dataset besar. |
| 2 | **Tailwind via CDN** | **Medium** | `layouts/admin.blade.php:9` | Setiap halaman mengunduh ~3MB Tailwind CSS. Gunakan Vite build dengan Tailwind terkompilasi. |
| 3 | **jQuery + DataTables global** | **Medium** | `layouts/admin.blade.php:28-30` | Dua library JS di-load di semua halaman, bahkan yang tidak membutuhkan tabel interaktif. |
| 4 | **Gamification tanpa cache** | **Medium** | `GamificationService.php:102-126` | EcoPoints dihitung ulang setiap kali dari database. Untuk 1000+ nasabah, ini berat. |
| 5 | **ActivityLog tanpa pruning** | **Low** | `ActivityLogController.php` | Tidak ada mekanisme pembersihan. Log bisa mencapai jutaan baris dalam beberapa bulan. |

---

## 9. Pengujian Manual yang Masih Diperlukan

Beberapa aspek tidak dapat diverifikasi hanya melalui analisis kode:

### 9.1 Dashboard
- [ ] **Grafik Chart.js** — Apakah grafik bar dan pie merender dengan benar? Data kosong (trend 0 semua) apakah menyebabkan error JS?
- [ ] **Responsivitas** — Apakah layout 4 kolom stat-card collapse dengan benar di layar kecil?
- [ ] **Time zone** — Apakah `Carbon::today()` konsisten dengan timezone Indonesia (WITA)?

### 9.2 Setoran Sampah
- [ ] **Approve flow** — Apakah setelah approve, saldo nasabah bertambah sesuai perhitungan?
- [ ] **Reject flow** — Apakah deposit yang sudah di-reject benar-benar tidak bisa di-approve lagi?
- [ ] **Pessimistic lock test** — Coba approve deposit yang sama dari 2 tab berbeda secara bersamaan. Apakah hanya satu yang berhasil?

### 9.3 Penarikan Saldo
- [ ] **Admin fee** — Apakah fee Rp 2.500 benar hanya diterapkan untuk transfer non-BTN?
- [ ] **Saldo tidak cukup** — Apakah error handling muncul dengan benar jika saldo nasabah kurang?
- [ ] **Double submission** — Klik tombol approve 2x cepat-cepat. Apakah saldo dipotong sekali atau 2x?

### 9.4 Permintaan Jemput
- [ ] **Leaflet map** — Apakah peta merender dengan benar di halaman detail?
- [ ] **Status transisi** — Apakah status bisa diubah dari "completed" ke "pending" kembali? (Seharusnya tidak)
- [ ] **Auto-assign** — Apakah status otomatis berubah ke "assigned" ketika petugas dipilih?

### 9.5 Artikel
- [ ] **Upload gambar** — Apakah file gambar benar tersimpan di `storage/app/public/articles/`?
- [ ] **Slug unik** — Apakah slug benar-benar unik jika judul duplikat?
- [ ] **Hapus gambar** — Apakah gambar dihapus dari storage saat artikel dihapus?

### 9.6 Otentikasi & Otorisasi
- [ ] **Akses langsung** — Coba akses `/cms/deposits` sebagai nasabah. Harap redirect ke nasabah dashboard.
- [ ] **Akses langsung** — Coba akses `/cms/users/create` sebagai petugas (bukan admin). Harap dapat 403.
- [ ] **Logout** — Apakah session benar-benar dihapus setelah logout?

### 9.7 Perangkat & Browser
- [ ] **Mobile view** — Apakah sidebar collapse dan hamburger menu berfungsi?
- [ ] **Browser** — Apakah tampilan konsisten di Chrome, Firefox, Edge?

---

## 10. Rekomendasi Perbaikan

### Prioritas Tinggi — Wajib dikerjakan sebelum rilis

1. **[BUG-001] Perbaiki link dashboard** — Ubah `route('cms.deposits.index?status=pending')` menjadi `route('cms.deposits.index', ['status' => 'pending'])`. Berlaku untuk semua link ringkasan di dashboard.
2. **[BUG-002] Validasi ENUM kategori** — Ubah input category menjadi dropdown dengan nilai sesuai ENUM database, atau tambahkan `Rule::in(...)` pada TrashPriceRequest.
3. **[BUG-003] Tambah search input Target Tabungan** — Tambahkan form pencarian di `savings-targets/index.blade.php`.
4. **[BUG-004] Validasi assigned_to saat status assigned** — Tambahkan conditional validation pada `UpdatePickupRequestRequest` bahwa `assigned_to` wajib jika status adalah `assigned`.
5. **[BUG-005] Rekalkulasi is_achieved** — Tambahkan observer/event listener pada transaksi (setoran/penarikan) yang memperbarui status `is_achieved` target nasabah terkait.

### Prioritas Sedang

6. **[BUG-007] Optimasi query dashboard** — Agregasi query dashboard menjadi 1-2 query besar, atau cache statistik dengan cache TTL 5 menit.
7. **[BUG-009] Tambah konfirmasi approve deposit** — Tambahkan `onsubmit="return confirm(...)"` pada form approve deposit.
8. **[BUG-008] Implementasi breadcrumb** — Tambahkan `@section('breadcrumbs')` di setiap halaman admin, atau buat breadcrumb otomatis dari route name.
9. **Otorisasi konsisten** — Migrasi `SavingsTargetController` dan `BadgeController` dari `abort_unless` ke Policy yang proper.
10. **Password lebih kuat** — Gunakan `Password::defaults()` (min:8, huruf besar, angka) pada StoreUserRequest dan UpdateUserRequest.
11. **[BUG-011] Cache EcoPoints** — Simpan total eco points sebagai kolom di tabel users, update hanya saat transaksi baru.

### Prioritas Rendah

12. **[BUG-008/016] Breadcrumb** — Implementasi breadcrumb di semua halaman.
13. **[BUG-014] Filter metode penarikan** — Tambahkan dropdown filter `withdrawal_method` di view penarikan.
14. **[BUG-012] Migrasi Tailwind ke Vite build** — Pindahkan dari CDN ke Vite untuk performa lebih baik.
15. **Export data** — Tambahkan tombol export CSV/Excel untuk data tabel.
16. **Loading state** — Tambahkan disabled + spinner pada tombol submit di seluruh form.
17. **[BUG-010] Pruning log** — Tambahkan command scheduler untuk menghapus activity log > 90 hari.
18. **Article preview** — Tambahkan route publik dan tombol preview di halaman edit artikel.

---

## 11. Kesimpulan

| Status | Keterangan |
|--------|------------|
| ⚠️ **Siap Setelah Perbaikan Prioritas Tinggi** | Aplikasi memiliki fondasi yang baik secara arsitektur, keamanan, dan alur bisnis. Namun, 2 bug critical pada Dashboard (BUG-001, BUG-002) menyebabkan error runtime yang harus diperbaiki sebelum sistem dapat digunakan. |

### Ringkasan Temuan

| Kategori | Jumlah |
|----------|--------|
| Critical | 2 |
| High | 4 |
| Medium | 6 |
| Low | 4 |
| **Total** | **16** |

### Yang sudah baik ✅

- Arsitektur berbasis Policy untuk otorisasi (sebagian besar modul)
- Pessimistic locking (`lockForUpdate()`) pada transaksi keuangan
- Penggunaan FormRequest untuk validasi (sebagian besar modul)
- Double-entry ledger via tabel `mutations`
- Integrasi Leaflet map untuk detail lokasi jemput
- CSRF protection di seluruh form
- Middleware blocking nasabah dari admin panel
- Struktur kode yang rapi dan terorganisir

### Yang perlu diperbaiki ⚠️

- Bug routing pada Dashboard yang menyebabkan error
- Validasi ENUM database yang longgar
- Dua modul menggunakan `abort_unless` alih-alih Policy
- Query dashboard yang tidak efisien
- CDN dependencies yang mempengaruhi performa
- Beberapa fitur filter/search tidak dapat diakses dari UI
- Belum ada mekanisme cache untuk perhitungan poin gamification
