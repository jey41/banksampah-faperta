# ARCHITECTURE BLUEPRINT — BANK SAMPAH FAPERTA

Dokumen ini menjelaskan blueprint arsitektur sistem, pemisahan layer, alur data, dan struktur modular untuk sistem Bank Sampah Faperta.

---

# 1. HIGH LEVEL ARCHITECTURE

Sistem menggunakan pendekatan:

- MVC (Laravel Core)
- Service Layer Architecture
- Event Driven Architecture
- Modular Monolith (bukan microservices)
- Inertia.js SPA-like frontend

---

## 1.1 Sistem Layer

[ React (Inertia) ]
↓
[ Controller Layer ]
↓
[ Service Layer ]
↓
[ Domain / Business Logic ]
↓
[ Model Layer ]
↓
[ Database Layer ]

---

# 2. CORE MODULE ARCHITECTURE

Sistem dibagi menjadi modul domain berikut:

## 2.1 User Module

- Registrasi
- Login
- Role management (admin, petugas, nasabah)
- Profil user
- Verifikasi akun

---

## 2.2 Transaction Module

- Deposit (setor sampah)
- Withdrawal (penarikan saldo)
- Mutation ledger (double-entry system)

---

## 2.3 Pickup Module

- Request pickup
- Assignment petugas
- Tracking status
- Geolocation validation

---

## 2.4 Waste Pricing Module

- Master data sampah
- kategori & harga
- carbon factor tracking

---

## 2.5 Gamification Module

- eco points engine
- level system
- badge system
- activity scoring

---

## 2.6 Content Module

- article management
- public education content

---

## 2.7 Admin Module

- dashboard analytics
- user management
- approval workflow
- reporting system

---

# 3. SERVICE LAYER DESIGN

## 3.1 Wajib Service

### TransactionService

Tanggung jawab:

- approve deposit
- approve withdrawal
- balance mutation
- financial integrity

---

### GamificationService

Tanggung jawab:

- calculate eco points
- level computation
- badge unlocking

---

### DashboardService

Tanggung jawab:

- aggregate statistics
- caching dashboard data
- analytics computation

---

### PickupService (optional extension)

- assignment logic
- distance validation
- scheduling logic

---

# 4. EVENT ARCHITECTURE

## 4.1 Event Flow

### Deposit Approved

DepositApproved Event
↓
Update Balance
↓
Mutation Log
↓
Gamification Sync
↓
Activity Log

---

### Withdrawal Approved

WithdrawalApproved Event
↓
Deduct Balance
↓
Mutation Log
↓
Activity Log

---

## 4.2 Event Rules

- Controller tidak boleh dispatch logic selain event trigger
- Listener tidak boleh mengubah request flow utama
- Event hanya untuk side effects

---

# 5. DATABASE ARCHITECTURE

## 5.1 Core Principle

- normalized structure (3NF minimum)
- audit trail mandatory
- immutable financial records

---

## 5.2 Mutation Ledger Pattern

Semua perubahan saldo harus melalui:

mutations table

Field penting:

- user_id
- type (debit/credit)
- amount
- balance_before
- balance_after
- sourceable (polymorphic)

---

## 5.3 Financial Integrity Rule

Tidak boleh ada:

- direct saldo manipulation tanpa mutation
- update saldo tanpa audit log
- bypass service layer

---

# 6. FRONTEND ARCHITECTURE

## 6.1 React (User Side)

Struktur:

resources/js/
Pages/
Components/
Layouts/

Rules:

- UI hanya untuk presentational logic
- data fetching via Inertia props
- tidak ada business logic di frontend

---

## 6.2 Blade (Admin Side)

Struktur:

resources/views/admin/
resources/views/components/

Rules:

- reusable component wajib
- no inline SQL
- logic tetap di controller/service

---

# 7. ROUTING ARCHITECTURE

## 7.1 Route Separation

### Public Routes

- landing page
- artikel
- harga sampah

---

### Auth Routes

- login
- register
- password reset

---

### Nasabah Routes

Prefix:

/nasabah

---

### Admin Routes

Prefix:

/cms

---

### Internal Filament

/admin

Legacy system (gradual migration)

---

# 8. SECURITY ARCHITECTURE

## 8.1 Access Control Flow

Request
↓
Auth Middleware
↓
Role Middleware
↓
Policy Check
↓
Controller
↓
Service Layer

---

## 8.2 Security Enforcement Points

- Middleware (first gate)
- Policy (fine-grained access)
- Service validation (business rule enforcement)

---

# 9. SCALABILITY DESIGN

## 9.1 Scaling Strategy

- queue for heavy tasks
- cache for dashboard
- pagination for large dataset
- indexing for transaction tables

---

## 9.2 Future Extension Ready

System prepared for:

- API layer (not implemented yet)
- mobile app integration
- microservices extraction (optional future)
- notification system (email, push, WA)

---

# 10. ANTI-PATTERN RULES

Dilarang:

- fat controller
- business logic di model
- direct DB query di controller
- bypass service layer
- update saldo langsung tanpa mutation
- duplicate gamification logic

---

# 11. DEPENDENCY FLOW RULE

Controller → Service → Model → DB

Tidak boleh:

- Controller → DB langsung
- Model → Service dependency circular
- Event → Controller call

---

# 12. SYSTEM GUARANTEE PRINCIPLE

Semua perubahan harus memenuhi:

- consistency
- traceability
- auditability
- scalability
- security compliance

---

# 13. FINAL ARCHITECTURE STATEMENT

Sistem ini adalah:

> Modular Monolith dengan Service Layer + Event Driven Architecture yang dirancang untuk financial-grade integrity dan scalability.

Tidak boleh ada perubahan yang merusak alur transaksi atau audit trail.
