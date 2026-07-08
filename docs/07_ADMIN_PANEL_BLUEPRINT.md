# ADMIN PANEL BLUEPRINT — BANK SAMPAH FAPERTA

Dokumen ini menjelaskan desain arsitektur, struktur UI, dan flow operasional admin panel berbasis Blade + Livewire + Bootstrap.

---

# 1. ADMIN PANEL OVERVIEW

Admin panel digunakan untuk:

- approval transaksi (deposit & withdrawal)
- manajemen user
- manajemen harga sampah
- monitoring aktivitas sistem
- reporting & cetak resi
- manajemen pickup request

---

# 2. ADMIN ARCHITECTURE PRINCIPLE

## Core Principles

- Reusable components first
- Server-rendered logic (Blade)
- Minimal frontend JS complexity
- Separation of UI and business logic
- Service layer tetap backend-driven

---

# 3. TECH STACK ADMIN

- Blade (templating)
- Livewire (interactivity)
- Bootstrap 5 (layout & UI)
- DataTables (data listing)
- CKEditor (content editor)
- ApexCharts (dashboard chart)
- Flatpickr (date input)

---

# 4. ADMIN FOLDER STRUCTURE

resources/views/admin/
├── layouts/
├── components/
├── dashboard/
├── users/
├── deposits/
├── withdrawals/
├── pickup-requests/
├── trash-prices/
├── articles/
├── badges/
├── savings-targets/
├── activity-logs/
└── print/

---

# 5. REUSABLE COMPONENT SYSTEM

## 5.1 Components Mandatory

- card.blade.php
- table.blade.php
- modal.blade.php
- form.blade.php
- input.blade.php
- button.blade.php
- stat-card.blade.php

---

## 5.2 Rules

- tidak boleh duplicate UI markup
- semua UI element harus component-based
- logic minimal di blade

---

# 6. ADMIN DASHBOARD FLOW

## Flow

Admin Login
↓
DashboardController
↓
DashboardService (cached)
↓
Blade View (ApexCharts + Stats)

---

## Dashboard Metrics

- total nasabah
- total saldo
- total deposit
- total withdrawal
- total sampah masuk
- active users

---

# 7. DEPOSIT MANAGEMENT FLOW

## Flow

1. List deposit (pending/approved)
2. Detail deposit
3. Input real weight
4. Approve / Reject
5. Trigger TransactionService

---

## Rules

- approval wajib validasi weight
- tidak boleh approve tanpa review
- mutation wajib dibuat

---

# 8. WITHDRAWAL MANAGEMENT FLOW

## Flow

1. List withdrawal request
2. Validate balance
3. Check admin fee
4. Approve / Reject
5. Update saldo + mutation

---

# 9. USER MANAGEMENT FLOW

## Features

- create user
- update role
- verify nasabah
- deactivate user

---

## Rules

- hanya admin boleh delete user
- petugas hanya view & limited update
- role change harus audited

---

# 10. PICKUP REQUEST FLOW

## Flow

1. view request
2. validate location (≤2km)
3. assign petugas
4. update status assigned → completed

---

# 11. TRASH PRICE MANAGEMENT

## Features

- CRUD harga sampah
- kategori management
- carbon factor update

---

## Rules

- perubahan harga harus audited
- tidak boleh delete jika digunakan transaksi aktif

---

# 12. ARTICLE MANAGEMENT

## Features

- create article
- edit article
- publish / draft
- image upload

---

## Rules

- slug unique
- image validation required
- delete file on removal

---

# 13. GAMIFICATION PANEL

## Features

- badge list
- user badge tracking
- eco points monitoring

---

## Rules

- system auto-calculated
- admin hanya monitoring

---

# 14. ACTIVITY LOG SYSTEM

## Features

- log semua aksi admin
- filter by user
- filter by action type

---

## Rules

- immutable log
- cannot be edited or deleted

---

# 15. PRINT SYSTEM

## Features

- print deposit receipt
- print withdrawal receipt

---

## Rules

- print view read-only
- must include transaction hash/id
- audit safe format

---

# 16. SECURITY IN ADMIN PANEL

## Checklist

- [ ] middleware role enforced
- [ ] no direct model mutation in blade
- [ ] no unauthorized access to CMS routes
- [ ] CSRF enabled
- [ ] input validation active

---

# 17. PERFORMANCE IN ADMIN PANEL

## Rules

- pagination wajib
- DataTables server-side mode
- eager loading mandatory
- cache dashboard metrics

---

# 18. ERROR HANDLING

- flash message system
- validation error display
- fallback UI for empty data

---

# 19. ADMIN UX PRINCIPLES

- cepat (fast access to actions)
- minimal click flow
- clear status labeling
- confirmation on destructive action

---

# 20. FINAL ADMIN PRINCIPLE

> Admin panel adalah sistem kontrol utama, bukan sekadar UI.

Semua aksi harus:

- traceable
- auditable
- reversible (jika memungkinkan)
