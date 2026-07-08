# 00_PROJECT.md

## 1. Project Name

Bank Sampah Digital Faperta

---

## 2. Project Overview

Bank Sampah Digital Faperta adalah sistem digital berbasis web untuk mengelola aktivitas bank sampah di lingkungan Fakultas Pertanian (Faperta) dan masyarakat sekitar.

Sistem ini mencakup:

- Pendaftaran dan verifikasi nasabah
- Transaksi setor sampah (deposit)
- Penarikan saldo (withdrawal)
- Penjemputan sampah (pickup request)
- Pengelolaan harga sampah
- Sistem gamifikasi (eco-points, badge, level)
- Manajemen artikel edukasi lingkungan
- Dashboard operasional untuk admin dan petugas

---

## 3. Problem Statement

Sistem bank sampah manual memiliki masalah:

- Pencatatan transaksi tidak terpusat
- Risiko kesalahan perhitungan saldo
- Tidak ada transparansi riwayat transaksi
- Proses verifikasi dan approval lambat
- Tidak ada sistem insentif (gamifikasi)
- Sulit monitoring aktivitas operasional

---

## 4. Goals

### Business Goals

- Digitalisasi proses bank sampah
- Meningkatkan partisipasi masyarakat
- Transparansi transaksi
- Efisiensi operasional

### System Goals

- Akurasi perhitungan saldo 100%
- Audit trail setiap transaksi
- Sistem scalable dan maintainable
- Role-based access control yang ketat

---

## 5. Target Users

1. Nasabah
    - Mahasiswa
    - Dosen
    - Masyarakat umum

2. Petugas
    - Operasional bank sampah

3. Admin
    - Pengelola sistem

---

## 6. Scope

### In Scope

- Deposit sampah
- Withdrawal saldo
- Pickup request
- Manajemen harga sampah
- Artikel edukasi
- Gamifikasi
- Dashboard admin

### Out of Scope

- Payment gateway
- Mobile app native
- Integrasi bank API
- AI sorting sampah

---

## 7. Tech Stack

### Backend

- Laravel 13
- PHP 8.3
- MySQL / SQLite

### Frontend

- React 18
- Inertia.js
- TailwindCSS
- Vite

### Admin Panel

- Blade
- TailwindCSS
- (Legacy: Filament masih ada)

---

## 8. Core Modules

- Authentication & Authorization
- Deposit Management
- Withdrawal Management
- Pickup System
- Trash Price Management
- Gamification System
- Article Management
- Dashboard & Reporting
- Activity Logging

---

## 9. Core Principles

- Clean Architecture
- Service Layer Pattern
- Policy-based Authorization
- Event-driven for side effects
- Immutable financial records
- Transaction-safe operations

---

## 10. Source of Truth Rule

Semua keputusan teknis dan bisnis harus merujuk ke:

- 01_PRD.md (product behavior)
- 02_BUSINESS_RULES.md (rules engine)
- 03_ARCHITECTURE.md (system design)

Tidak boleh ada logic bisnis yang hanya berada di controller tanpa dokumentasi.

---

## 11. Notes

Dokumen ini adalah entry point untuk seluruh AI agent (Claude Code / 9Router / Kiro).

Semua task harus membaca file ini sebelum mengambil keputusan arsitektur.
