# 01_PRD.md

## Bank Sampah Digital Faperta

---

# 1. Product Vision

Membangun sistem digital bank sampah yang transparan, terukur, dan terotomatisasi untuk meningkatkan partisipasi pengelolaan sampah berbasis komunitas akademik dan masyarakat sekitar.

Sistem ini menjadi pusat pengelolaan:

- transaksi sampah
- insentif ekonomi
- edukasi lingkungan
- monitoring operasional

---

# 2. Product Objectives

## 2.1 Objectives Utama

- Digitalisasi penuh proses bank sampah
- Menghilangkan pencatatan manual
- Menjamin akurasi saldo 100%
- Menyediakan audit trail setiap transaksi

## 2.2 Objectives Teknis

- Sistem scalable
- Modular architecture
- Secure role-based system
- Transaction-safe (ACID compliant)

---

# 3. Success Metrics

- 0 mismatch saldo
- 100% transaksi memiliki audit log
- < 2 detik response time untuk dashboard
- > 70% partisipasi nasabah aktif
- Pengurangan kesalahan pencatatan manual hingga 100%

---

# 4. User Roles

## 4.1 Admin

- Full access system
- Manage users
- Manage pricing
- Approve/reject transaksi
- System configuration

## 4.2 Petugas

- Operasional transaksi
- Verifikasi deposit
- Verifikasi withdrawal
- Pickup management

## 4.3 Nasabah

- Setor sampah
- Request pickup
- Withdrawal saldo
- View history
- Gamification progress

---

# 5. Core Features

## 5.1 Authentication

- Register nasabah
- Login/logout
- Email verification
- Role-based access control

---

## 5.2 Deposit (Setor Sampah)

### Flow:

1. Nasabah menyerahkan sampah
2. Petugas input data sementara
3. Admin/petugas melakukan approval final
4. Sistem menghitung:
    - berat total
    - harga total
5. Jika bukan donasi:
    - saldo nasabah bertambah
6. Semua transaksi dicatat ke mutation ledger

### Rules:

- Tidak boleh double approval
- Harus ada item breakdown
- Wajib audit log

---

## 5.3 Withdrawal (Penarikan)

### Flow:

1. Nasabah request withdrawal
2. Status: pending
3. Admin approve/reject
4. Jika approve:
    - saldo berkurang
    - admin fee diterapkan jika transfer non-BTN

### Rules:

- Minimum withdrawal Rp10.000
- Tidak boleh melebihi saldo
- Harus ada histori status

---

## 5.4 Pickup System

### Flow:

1. Nasabah request pickup
2. Input lokasi + waktu
3. Admin assign petugas
4. Petugas melakukan pickup
5. Bisa dikonversi menjadi deposit

### Rules:

- Maksimal radius 2 km
- Time slot wajib dipilih
- Status flow wajib dijaga

---

## 5.5 Trash Pricing

- Harga per kategori sampah
- Dapat berubah oleh admin
- Digunakan untuk kalkulasi deposit

Kategori:

- plastik
- kertas
- logam
- kaca
- minyak jelantah
- lainnya

---

## 5.6 Gamification System

### Eco Points Formula:

- berat × faktor
- bonus donasi
- streak aktivitas

### Level:

- Pemula Hijau
- Pejuang Lingkungan
- Ksatria Hijau
- Pahlawan Bumi
- Legenda Faperta

### Badge:

- First Deposit
- Frequent User
- Heavy Contributor
- Donation Hero
- Pickup Master

---

## 5.7 Article System

- Artikel edukasi lingkungan
- Publish / draft
- SEO-friendly slug
- Image upload

---

## 5.8 Dashboard

### Admin:

- total transaksi
- total sampah
- active users
- pending approval

### Nasabah:

- saldo
- history transaksi
- badge & level
- target tabungan

---

# 6. Business Rules Summary

Semua logic bisnis harus:

- konsisten
- terstruktur
- tidak duplikasi
- tidak ada hardcode di controller

---

# 7. Data Integrity Rules

- Semua transaksi harus immutable
- Semua perubahan saldo harus lewat mutation table
- Tidak boleh update saldo langsung tanpa ledger
- Semua approval harus tercatat

---

# 8. Non-Functional Requirements

## Performance

- dashboard cepat (<2s)
- query harus optimized

## Security

- RBAC wajib
- CSRF protected
- validation ketat

## Reliability

- transaksi harus atomic
- rollback jika gagal

---

# 9. Out of Scope

- payment gateway
- AI sorting
- mobile app native
- integrasi bank

---

# 10. Source of Truth Rule

Jika terjadi konflik:

1. BUSINESS_RULES.md (tertinggi)
2. PRD.md
3. ARCHITECTURE.md

---

# 11. Notes

PRD ini adalah pusat perilaku sistem.
Semua implementasi harus mengikuti dokumen ini tanpa pengecualian.
