# Bank Sampah Faperta - Project Overview

**Sistem Informasi Manajemen Bank Sampah untuk Fakultas Pertanian**

---

## 📋 Table of Contents

1. [Introduction](#introduction)
2. [Technology Stack](#technology-stack)
3. [Key Features](#key-features)
4. [System Architecture](#system-architecture)
5. [Database Schema](#database-schema)
6. [User Roles & Permissions](#user-roles--permissions)
7. [Project Structure](#project-structure)
8. [Setup & Installation](#setup--installation)
9. [Development Workflow](#development-workflow)
10. [Routes Overview](#routes-overview)
11. [Recent Updates](#recent-updates)

---

## 🎯 Introduction

Bank Sampah Faperta adalah aplikasi web modern untuk mengelola bank sampah di lingkungan Fakultas Pertanian. Sistem ini memfasilitasi:

- **Pencatatan transaksi** sampah secara digital dan transparan
- **Manajemen nasabah** dengan sistem akun dan saldo
- **Edukasi lingkungan** melalui portal artikel
- **Gamifikasi** untuk meningkatkan partisipasi nasabah
- **Analitik dan pelaporan** untuk administrator

### Tujuan Utama
- Mempermudah proses pengelolaan sampah di lingkungan kampus
- Meningkatkan kesadaran masyarakat tentang pengelolaan sampah
- Memberikan insentif finansial bagi nasabah yang menyetor sampah
- Menyediakan data dan analitik untuk pengambilan keputusan

---

## 🛠️ Technology Stack

### Backend
- **Framework:** Laravel 11 (PHP 8.3+)
- **Admin Panel:** Filament PHP v5.6
- **Authentication:** Laravel Breeze + Sanctum
- **Queue System:** Database-based queue
- **Database:** SQLite (dev), MySQL/PostgreSQL (production-ready)

### Frontend
- **Library:** React 18.2
- **Bridge:** Inertia.js v2.0
- **Styling:** Tailwind CSS v4 + @tailwindcss/forms
- **UI Components:** 
  - Headless UI for accessible components
  - Lucide React for icons
  - Framer Motion for animations
- **Build Tool:** Vite 8.0
- **Routing Helper:** Ziggy (Laravel routes in JavaScript)

### Additional Tools
- **MapLibre GL** v5.24 for location/mapping features
- **Concurrently** for running multiple dev processes
- **Pail** for Laravel log monitoring
- **Pint** for PHP code style

---

## ✨ Key Features

### 1. **Multi-Role User Management**
- **Admin:** Full system access, user management, report generation
- **Petugas:** Transaction validation, deposit/withdrawal processing
- **Nasabah:** View balance, submit deposits, request withdrawals, pickup scheduling

### 2. **Deposit Management**
- Detailed waste transaction recording
- Multi-item deposits with different trash types
- Weight tracking per item
- Three-stage approval workflow (pending → approved/rejected)
- Donation option (waste value donated instead of credited)
- Automatic balance updates on approval

### 3. **Withdrawal System**
- Bank account integration (bank name, account number, account holder)
- Withdrawal request submission
- Admin/petugas validation
- Withdrawal history tracking
- Balance deduction on approval

### 4. **Trash Price Catalog**
Six waste categories with buy/sell pricing:
- Plastik (plastic)
- Kertas (paper)
- Logam (metal)
- Kaca (glass)
- Minyak Jelantah (used cooking oil)
- Lainnya (others)

Unit-based pricing (kg/L) with separate buy/sell rates.

### 5. **Pickup Request System**
- Schedule waste collection from nasabah location
- Location tracking with coordinates
- Status management (pending → scheduled → completed)
- Integration with deposit system

### 6. **Gamification System**
- **Eco Points:** Earned from deposits and withdrawals
- **Level System:** Progressive levels based on total eco points
- **Badges:** Achievement unlocking (first deposit, consistent contributor, etc.)
- **Streak Tracking:** Consecutive activity tracking
- **Leaderboards:** Competitive engagement (planned)

### 7. **Educational Content**
- Article management with rich text content
- Image upload support
- Slug-based URLs for SEO
- Draft/published workflow
- Public article directory and detail pages

### 8. **Savings Targets**
- Personal financial goals for nasabah
- Track progress toward targets
- Achievement notifications

### 9. **Financial Tracking**
- **Mutations Table:** Double-entry ledger system
  - Debit entries (withdrawals)
  - Kredit entries (deposits)
  - Balance before/after tracking
  - Polymorphic relationships to source transactions
- **Transaction History:** Complete audit trail

### 10. **Activity Logging**
- Comprehensive audit trail
- User action tracking
- Admin/petugas activity monitoring
- System event logging

### 11. **Public Portal**
- Landing page with impact metrics
- Price catalog (public access)
- Article directory (educational content)
- Responsive design with animations

### 12. **Analytics & Reporting**
- Dashboard metrics for admin
- Transaction summaries
- User statistics
- Carbon footprint calculations (planned)

---

## 🏗️ System Architecture

### Application Layers

```
┌─────────────────────────────────────────────────────┐
│                    PUBLIC LAYER                      │
│  Landing Page, Price Catalog, Article Directory     │
└─────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────┐
│                 AUTHENTICATION LAYER                 │
│         Laravel Breeze + Sanctum (Inertia)          │
└─────────────────────────────────────────────────────┘
                         ↓
        ┌────────────────┴────────────────┐
        ↓                                  ↓
┌──────────────────┐           ┌──────────────────────┐
│  NASABAH PORTAL  │           │    ADMIN PANEL       │
│  (React/Inertia) │           │  (Filament PHP)      │
│                  │           │                      │
│  - Dashboard     │           │  - User Management   │
│  - Deposit View  │           │  - Deposit Approval  │
│  - Withdraw Form │           │  - Withdrawal Approval│
│  - History       │           │  - Trash Prices      │
│  - Pickup Request│           │  - Articles (CMS)    │
│  - Profile       │           │  - Activity Logs     │
└──────────────────┘           │  - Analytics         │
                               └──────────────────────┘
                                         ↓
                    ┌────────────────────────────────┐
                    │      BUSINESS LOGIC LAYER      │
                    │   Controllers & Services       │
                    └────────────────────────────────┘
                                         ↓
                    ┌────────────────────────────────┐
                    │       DATA ACCESS LAYER        │
                    │  Eloquent ORM & Models         │
                    └────────────────────────────────┘
                                         ↓
                    ┌────────────────────────────────┐
                    │         DATABASE LAYER         │
                    │  MySQL/PostgreSQL/SQLite       │
                    └────────────────────────────────┘
```

### Key Architectural Patterns

1. **MVC (Model-View-Controller)**
   - Models: Eloquent ORM entities
   - Views: React components (Inertia)
   - Controllers: HTTP request handlers

2. **Role-Based Access Control (RBAC)**
   - Middleware: `EnsureUserHasRole`
   - Policies: Resource-level authorization
   - Guards: Panel access control (Filament)

3. **Repository Pattern** (Filament Resources)
   - Abstraction for data operations
   - Reusable forms and tables
   - Consistent admin interface

4. **Event-Driven Architecture** (Planned)
   - Laravel events for business logic
   - Observers for model lifecycle
   - Queue jobs for async processing

---

## 💾 Database Schema

### Core Tables

#### **users**
```sql
- id, name, email, password
- role (enum: admin, petugas, nasabah)
- status (enum: pending, verified, blocked)
- phone, address, account_no
- saldo (balance in smallest currency unit)
- demografi: umur, gender, status_pekerjaan, universitas, fakultas, pendidikan_terakhir
- timestamps
```

#### **trash_prices**
```sql
- id, name
- category (enum: plastik, kertas, logam, kaca, minyak_jelantah, lainnya)
- price_buy (harga beli dari nasabah)
- price_sell (harga jual ke pabrik)
- unit (default: kg)
- timestamps
```

#### **deposits**
```sql
- id, user_id (FK)
- total_price, weight_total
- status (enum: pending, approved, rejected)
- is_donation (boolean)
- notes, validated_by (FK)
- timestamps
```

#### **deposit_items**
```sql
- id, deposit_id (FK), trash_price_id (FK)
- weight, price_per_unit, total_price
- timestamps
```

#### **withdrawals**
```sql
- id, user_id (FK)
- amount, bank_name, account_number, account_name
- status (enum: pending, approved, rejected)
- notes, validated_by (FK)
- timestamps
```

#### **withdrawal_history**
```sql
- id, withdrawal_id (FK)
- old_status, new_status
- changed_by (FK), changed_at
```

#### **mutations** (Double-Entry Ledger)
```sql
- id, user_id (FK)
- type (enum: debit, kredit)
- amount, balance_before, balance_after
- sourceable (polymorphic: Deposit/Withdrawal)
- timestamps
```

#### **articles**
```sql
- id, title, slug (unique)
- content (text), image_url
- status (enum: draft, published)
- timestamps
```

#### **pickup_requests**
```sql
- id, user_id (FK)
- pickup_date, pickup_time, address
- latitude, longitude
- status (enum: pending, scheduled, completed, cancelled)
- notes
- timestamps
```

#### **savings_targets**
```sql
- id, user_id (FK)
- title, target_amount
- is_achieved (boolean)
- timestamps
```

#### **user_badges**
```sql
- id, user_id (FK)
- badge_key (string, indexed)
- unlocked_at (timestamp)
- unique(user_id, badge_key)
```

#### **activity_logs**
```sql
- id, user_id (FK, nullable)
- action (string), description (text)
- timestamps
```

### Database Relationships

- **User** hasMany: Deposits, Withdrawals, Mutations, PickupRequests, SavingsTargets, UserBadges
- **Deposit** hasMany: DepositItems, belongsTo: User, Validator (User)
- **DepositItem** belongsTo: Deposit, TrashPrice
- **Withdrawal** belongsTo: User, Validator (User)
- **Mutation** belongsTo: User, morphsTo: Sourceable (Deposit/Withdrawal)

---

## 👥 User Roles & Permissions

### Admin
- **Full Access** to all system features
- User management (create, edit, delete, verify)
- View all transactions and reports
- Manage trash prices
- Publish articles
- Access activity logs
- Configure system settings

### Petugas (Officer)
- Validate deposits (approve/reject)
- Validate withdrawals (approve/reject)
- View user information
- Manage pickup requests
- Limited admin panel access

### Nasabah (Customer)
- View personal dashboard
- View balance and transaction history
- Request pickup service
- Submit withdrawal requests
- View trash prices
- Read educational articles
- Set savings targets
- View badges and achievements

### Public (Unauthenticated)
- View landing page
- Browse price catalog
- Read published articles
- Register as nasabah

---

## 📁 Project Structure

```
banksampah-faperta/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Filament/
│   │   ├── Resources/             # Filament admin resources
│   │   │   ├── ActivityLogs/
│   │   │   ├── Articles/
│   │   │   ├── Deposits/
│   │   │   ├── PickupRequests/
│   │   │   ├── TrashPrices/
│   │   │   ├── Users/
│   │   │   └── Withdrawals/
│   │   └── Pages/                 # Custom Filament pages
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Admin-specific controllers
│   │   │   ├── Auth/              # Breeze auth controllers
│   │   │   ├── NasabahController.php
│   │   │   ├── ProfileController.php
│   │   │   └── WelcomeController.php
│   │   ├── Middleware/
│   │   │   ├── BlockNasabahFromAdmin.php
│   │   │   └── EnsureUserHasRole.php
│   │   └── Requests/              # Form requests
│   ├── Models/                    # Eloquent models
│   ├── Policies/                  # Authorization policies
│   └── Providers/
│       └── AuthServiceProvider.php
├── bootstrap/
├── config/                        # Configuration files
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── public/                        # Public assets
├── resources/
│   ├── css/
│   ├── js/
│   │   ├── Components/            # Reusable React components
│   │   │   ├── Auth/
│   │   │   └── ui/
│   │   ├── Layouts/               # Layout components
│   │   └── Pages/                 # Inertia page components
│   │       ├── Auth/
│   │       ├── Nasabah/
│   │       ├── Profile/
│   │       └── Public/
│   └── views/                     # Blade templates
│       ├── admin/
│       ├── app.blade.php
│       └── print/                 # Print templates
├── routes/
│   ├── admin.php                  # Admin routes
│   ├── auth.php                   # Authentication routes
│   └── web.php                    # Web routes
├── storage/                       # Storage for logs, cache, uploads
├── tests/                         # PHPUnit tests
├── .env.example                   # Environment template
├── composer.json                  # PHP dependencies
├── package.json                   # Node dependencies
├── phpunit.xml                    # PHPUnit configuration
├── tailwind.config.js             # Tailwind configuration
└── vite.config.js                 # Vite configuration
```

---

## ⚙️ Setup & Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL (or use SQLite for dev)
- Git

### Step-by-Step Installation

```bash
# 1. Clone the repository
git clone https://github.com/jey41/banksampah-faperta.git
cd banksampah-faperta

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
# For SQLite (development):
DB_CONNECTION=sqlite
# Create empty database file
touch database/database.sqlite

# For MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=banksampah_faperta
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Build frontend assets
npm run build

# 8. Start development servers
composer run dev
# This runs: Laravel server, Queue worker, Pail logs, and Vite dev server
```

### Alternative: Quick Setup Script

```bash
composer run setup
```

This single command will:
- Install Composer dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run migrations
- Install npm packages
- Build assets

---

## 🚀 Development Workflow

### Running the Development Environment

The project uses **Concurrently** to run multiple processes:

```bash
composer run dev
```

This starts four concurrent processes:
1. **Laravel Server** (`php artisan serve`) - Port 8000
2. **Queue Worker** (`php artisan queue:listen`)
3. **Log Viewer** (`php artisan pail`)
4. **Vite Dev Server** (`npm run dev`) - HMR for React

### Individual Commands

```bash
# Laravel development server
php artisan serve

# Vite dev server (React HMR)
npm run dev

# Queue worker
php artisan queue:work

# Build for production
npm run build

# Run tests
composer run test
# or
php artisan test

# Code formatting (Laravel Pint)
./vendor/bin/pint
```

### Accessing the Application

- **Public Site:** http://localhost:8000
- **Nasabah Dashboard:** http://localhost:8000/nasabah/dashboard (requires login as nasabah)
- **Admin Panel:** http://localhost:8000/cms (requires login as admin/petugas)

### Default Credentials (After Seeding)

Check `database/seeders/DatabaseSeeder.php` for default accounts.

---

## 🗺️ Routes Overview

### Public Routes
```
GET  /                      → Landing page
GET  /harga                 → Public price catalog
GET  /artikel               → Article directory
GET  /artikel/{slug}        → Single article view
```

### Authentication Routes (Laravel Breeze)
```
GET|POST  /login           → Login
GET|POST  /register        → Register
POST      /logout          → Logout
GET|POST  /forgot-password → Password reset
GET|POST  /reset-password  → Password reset confirmation
GET|POST  /verify-email    → Email verification
```

### Nasabah Routes
```
Prefix: /nasabah
Middleware: auth, role:nasabah

GET   /dashboard           → Nasabah dashboard
GET   /jemput              → Pickup request form
POST  /jemput              → Submit pickup request
GET   /tarik               → Withdrawal form
POST  /tarik               → Submit withdrawal
GET   /riwayat             → Transaction history
POST  /target              → Create savings target
DELETE /target/{id}        → Delete savings target
```

### Admin Routes (Filament)
```
Prefix: /cms
Middleware: auth, filament panel check

Dashboard, User Management, Deposits, Withdrawals,
Trash Prices, Articles, Pickup Requests, Activity Logs
```

### Profile Routes
```
Middleware: auth

GET    /profile            → Edit profile
PATCH  /profile            → Update profile
DELETE /profile            → Delete account
```

### Print Routes (Admin/Petugas only)
```
GET  /admin-print/deposit/{id}/print      → Deposit receipt
GET  /admin-print/withdrawal/{id}/print   → Withdrawal receipt
```

---

## 📈 Recent Updates

### Latest Features (June-July 2026)

1. **Public Landing Page** (Jun 16-23)
   - Animated hero section with Framer Motion
   - Interactive impact counter
   - Responsive design
   - Call-to-action sections

2. **Gamification System** (Jun 22)
   - Eco points calculation
   - Level progression system
   - Badge unlocking mechanism
   - Streak tracking
   - Achievement notifications

3. **Public Layout Component** (Jun 15)
   - Dynamic navigation
   - Smooth scroll animations
   - Mobile-responsive menu
   - Footer with social links

4. **Enhanced Demographics** (Jun 19)
   - User age, gender, occupation status
   - University and faculty affiliation
   - Education level tracking

5. **Pickup Request Location** (Jun 20)
   - MapLibre integration
   - Latitude/longitude storage
   - Address autocomplete (planned)

6. **Withdrawal History Tracking** (Jun 19)
   - Status change logging
   - Audit trail for withdrawals
   - Changed-by user tracking

7. **Advanced Financial Features** (Jun 15)
   - Mutation table (double-entry ledger)
   - Activity logs for audit
   - Savings targets
   - Donation option for deposits

### Technical Improvements

- Upgraded to Laravel 11
- Upgraded to Filament 5.6
- Upgraded to Tailwind CSS v4
- Upgraded to Vite 8.0
- Added Concurrently for dev process management
- Implemented Ziggy for route helpers in React

---

## 🔮 Future Considerations

### Planned Features
- **Carbon Footprint Calculator:** Show environmental impact
- **Leaderboards:** Public ranking of top contributors
- **Notification System:** Real-time alerts for transactions
- **Mobile App:** React Native or PWA
- **QR Code Integration:** Quick deposit scanning
- **Export Reports:** PDF/Excel generation
- **Multi-language Support:** i18n implementation
- **Payment Gateway:** Direct bank transfer integration

### Technical Debt & Improvements
- Implement comprehensive test coverage
- Add API documentation (OpenAPI/Swagger)
- Implement caching strategies (Redis)
- Add real-time features (WebSockets/Pusher)
- Implement advanced analytics dashboard
- Add automated backup system
- Implement CDN for static assets
- Add monitoring and error tracking (Sentry)

---

## 📞 Contact & Support

For issues, questions, or contributions:
- **GitHub:** https://github.com/jey41/banksampah-faperta
- **Maintainer:** Muhammad Hisyam Nugroho

---

*Dibuat dengan ❤️ untuk lingkungan yang lebih bersih.*
