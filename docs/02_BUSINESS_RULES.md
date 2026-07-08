# BUSINESS RULES — BANK SAMPAH FAPERTA

Dokumen ini berisi aturan bisnis, arsitektur, coding standard, security guideline, dan workflow development untuk sistem Bank Sampah Faperta.

---

# 1. PROJECT CONTEXT

## 1.1 Overview

Bank Sampah Faperta adalah sistem pengelolaan sampah berbasis digital untuk:

- Nasabah (masyarakat / civitas akademika)
- Petugas operasional
- Admin sistem

Fungsi utama:

- Setor sampah (deposit)
- Penarikan saldo (withdrawal)
- Penjemputan sampah (pickup request)
- Gamifikasi lingkungan
- Edukasi & artikel
- Manajemen bank sampah

---

## 1.2 Tech Stack

### Backend

- Laravel 11+
- PHP 8.3

### Frontend Public & User

- React 18
- Inertia.js
- Tailwind CSS
- Vite

### Admin Panel

- Blade
- Livewire
- Bootstrap 5
- DataTables
- CKEditor
- ApexCharts
- Flatpickr

---

# 2. ARCHITECTURE PRINCIPLES

Wajib mengikuti:

- SOLID Principles
- DRY (Don't Repeat Yourself)
- KISS (Keep It Simple Stupid)
- Clean Architecture
- Service Layer Pattern
- Repository Pattern (jika diperlukan)
- Event Driven Architecture
- Transactional Integrity
- Separation of Concerns

---

## 2.1 Layered Architecture

### Controller Layer

- hanya menerima request
- tidak boleh berisi business logic

### Service Layer

- semua business logic wajib di sini
- contoh:
    - TransactionService
    - GamificationService

### Repository Layer (optional)

- hanya jika query kompleks

### Event Layer

- untuk side effects:
    - logging
    - notification
    - badge update

---

# 3. CODING RULES

## 3.1 Controller Rules

- HARUS tipis
- tidak boleh ada logic transaksi
- hanya:
    - validate
    - call service
    - return response

---

## 3.2 Service Rules

- semua logic bisnis di sini
- wajib menggunakan DB transaction untuk financial logic
- tidak boleh mengakses request langsung

---

## 3.3 Model Rules

- hanya relasi & accessor/mutator
- tidak boleh business logic kompleks

---

## 3.4 Validation Rules

- wajib pakai FormRequest untuk admin
- wajib validasi input user

---

## 3.5 Naming Convention

- Controller: `XxxController`
- Service: `XxxService`
- Model: singular PascalCase
- Table: plural snake_case
- Column: snake_case
- Route name: dot notation (`cms.deposits.approve`)

---

# 4. DATABASE RULES

## 4.1 Wajib

- foreign key
- index pada field penting
- cascade delete jika relevan
- transactional consistency

## 4.2 Dilarang

- duplicate data tanpa alasan
- hardcoded enum tanpa pertimbangan
- logic bisnis di database layer

---

## 4.3 Financial Integrity Rules

Untuk semua transaksi:

- deposit
- withdrawal

WAJIB:

- DB transaction
- locking (lockForUpdate)
- mutation log (audit trail)
- balance consistency check

---

# 5. SECURITY RULES

## 5.1 Authentication

- session-based auth (Breeze)
- email verification mandatory

## 5.2 Authorization

- RBAC (admin, petugas, nasabah)
- Policy wajib digunakan
- Middleware role wajib

## 5.3 Protection

Wajib aman dari:

- SQL Injection (Eloquent only)
- XSS (escape output)
- CSRF protection aktif
- Broken Access Control
- Mass Assignment protection

## 5.4 File Upload

- validasi file type
- max size limit
- storage terisolasi
- delete orphan file

## 5.5 Rate Limiting

- email verification throttled
- login protection aktif

---

# 6. PERFORMANCE RULES

Wajib optimasi:

- eager loading (hindari N+1)
- pagination untuk list
- cache untuk dashboard stats
- queue untuk job berat
- index database untuk query besar
- lazy load hanya jika perlu

---

# 7. FRONTEND RULES

## 7.1 Public & User (React + Inertia)

- semua UI user di React
- gunakan Tailwind
- state minimal
- tidak boleh mixing Blade

## 7.2 Admin Panel

- Blade + Livewire + Bootstrap
- reusable component wajib
- jangan campur React

---

# 8. BUSINESS LOGIC RULES

## 8.1 Deposit Flow

1. input deposit (pending)
2. timbang ulang (validation)
3. approve
4. update saldo user
5. create mutation (credit)
6. log activity
7. trigger event

---

## 8.2 Withdrawal Flow

1. user request withdrawal
2. pending state
3. admin approval
4. validate balance + admin fee
5. deduct balance
6. mutation (debit)
7. history log

---

## 8.3 Pickup Flow

1. user request pickup
2. validate distance max 2km
3. assign petugas
4. status completed → optional convert to deposit

---

## 8.4 Gamification

- eco-points calculation
- level system (5 level)
- badge system (10+ badge)
- auto sync via event

---

# 9. EVENT SYSTEM RULES

Event wajib digunakan untuk:

- deposit approved
- withdrawal approved
- badge update
- logging activity
- notification (future)

Dilarang:

- menaruh logic event di controller

---

# 10. TRANSACTION RULES

Semua financial action:

WAJIB:

- DB transaction
- lockForUpdate
- mutation record
- rollback safety

---

# 11. ERROR HANDLING

- gunakan exception handling Laravel
- log semua error penting
- jangan expose internal error ke user

---

# 12. QA EXPECTATION

Sebelum fitur dianggap selesai:

- auth check
- role check
- edge case test
- negative test
- validation test
- performance check

---

# 13. ARCHITECTURE WARNING

Dilarang:

- fat controller
- business logic di model
- duplicate service logic
- bypass policy
- direct DB query di controller

---

# 14. FINAL PRINCIPLE

> Sistem ini adalah production-grade system.
> Semua perubahan harus mempertimbangkan dampak ke seluruh modul.

Tidak ada perubahan lokal tanpa analisis sistemik.
