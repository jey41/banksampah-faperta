# Bank Sampah Faperta ♻️

Sistem Informasi Manajemen Bank Sampah untuk Fakultas Pertanian (Faperta). Aplikasi ini dibangun untuk mempermudah proses pengelolaan sampah, pencatatan transaksi nasabah, dan edukasi lingkungan melalui platform digital yang modern dan responsif.

> **✨ New!** Comprehensive documentation now available! See the [Documentation](#-documentation) section below.

## 🚀 Fitur Utama

### Untuk Nasabah
- **Dashboard Interaktif**: Ringkasan saldo, riwayat transaksi, dan metrik tabungan sampah
- **Transaksi Digital**: Pencatatan setor sampah (deposit) dan tarik saldo (withdraw) yang transparan
- **Gamifikasi**: Sistem poin ekologi (eco points), level, badge achievements, dan streak tracking
- **Layanan Jemput**: Request penjemputan sampah dengan tracking lokasi
- **Savings Targets**: Tetapkan target tabungan dan tracking progress
- **Portal Edukasi**: Akses artikel lingkungan dan tips pengelolaan sampah

### Untuk Admin/Petugas
- **Panel Admin Terintegrasi**: Manajemen pengguna, validasi transaksi menggunakan Filament Admin
- **Katalog Harga Dinamis**: Kelola harga sampah untuk 6 kategori berbeda
- **Activity Logs**: Audit trail lengkap untuk tracking perubahan sistem
- **Analytics Dashboard**: Laporan dan metrik komprehensif
- **Approval Workflow**: Sistem approval tiga tingkat untuk deposits dan withdrawals

### Fitur Publik
- **Landing Page Modern**: Hero section dengan animasi dan impact counter
- **Katalog Harga Publik**: Transparansi harga sampah untuk masyarakat umum
- **Direktori Artikel**: Portal edukasi lingkungan yang dapat diakses publik

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dikembangkan menggunakan *stack* teknologi modern:

**Backend:**
- [Laravel 11](https://laravel.com/) - PHP 8.3+
- [Laravel Sanctum](https://laravel.com/docs/sanctum) - API authentication
- [Filament PHP v5.6](https://filamentphp.com/) - Admin panel

**Frontend:**
- [React 18](https://reactjs.org/) - UI library
- [Inertia.js v2](https://inertiajs.com/) - Modern monolith approach
- [Tailwind CSS v4](https://tailwindcss.com/) - Utility-first CSS
- [Framer Motion](https://www.framer.com/motion/) - Animations
- [Lucide React](https://lucide.dev/) - Icon library
- [MapLibre GL](https://maplibre.org/) - Interactive maps

**Database & Infrastructure:**
- MySQL 8.0+ / PostgreSQL 13+ / SQLite (development)
- Redis (caching & sessions, recommended for production)

## ⚙️ Quick Start

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL (or use SQLite for development)

### Installation

**Option 1: Quick Setup (Recommended)**
```bash
git clone https://github.com/jey41/banksampah-faperta.git
cd banksampah-faperta
composer run setup
```

**Option 2: Manual Setup**
```bash
# Clone repository
git clone https://github.com/jey41/banksampah-faperta.git
cd banksampah-faperta

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database (for SQLite)
touch database/database.sqlite

# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build
```

### Running the Application

**Development Mode (All-in-one):**
```bash
composer run dev
```
This starts Laravel server, queue worker, log viewer, and Vite dev server concurrently.

**Access the Application:**
- **Public Site:** http://localhost:8000
- **Nasabah Dashboard:** http://localhost:8000/nasabah/dashboard
- **Admin Panel:** http://localhost:8000/cms

For detailed setup instructions, see the [Developer Guide](docs/DEVELOPER_GUIDE.md).

## 📚 Documentation

Comprehensive documentation is available to help you understand, develop, and deploy the application:

- **[Project Overview](PROJECT_OVERVIEW.md)** - Complete project documentation including architecture, features, and setup
- **[Developer Guide](docs/DEVELOPER_GUIDE.md)** - Development environment, coding standards, testing, and debugging
- **[Database Schema](docs/DATABASE_SCHEMA.md)** - Detailed database structure, relationships, and queries
- **[Deployment Guide](docs/DEPLOYMENT_GUIDE.md)** - Production deployment instructions for various platforms
- **[Contributing Guide](CONTRIBUTING.md)** - How to contribute to the project
- **[Documentation Index](docs/README.md)** - Complete documentation navigation

## 🏗️ Project Structure

```
banksampah-faperta/
├── app/
│   ├── Filament/          # Admin panel resources
│   ├── Http/Controllers/  # Application controllers
│   ├── Models/            # Eloquent models
│   └── Policies/          # Authorization policies
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/   # React components
│   │   ├── Layouts/      # Layout components
│   │   └── Pages/        # Inertia page components
│   └── views/            # Blade templates
├── routes/
│   ├── web.php           # Web routes
│   └── admin.php         # Admin routes
└── docs/                 # Documentation
```

## 🔑 Key Features Explained

### Multi-Role System
- **Admin**: Full system access and management
- **Petugas**: Transaction validation and user management
- **Nasabah**: Personal dashboard and transactions

### Gamification System
- **Eco Points**: Earned from deposits and activities
- **Levels**: Progressive achievement system
- **Badges**: Unlockable achievements (first deposit, eco champion, etc.)
- **Streaks**: Consecutive activity tracking

### Transaction Management
- **Deposits**: Multi-item waste deposits with weight tracking
- **Withdrawals**: Bank account withdrawal requests
- **Mutations**: Double-entry ledger for complete audit trail
- **Approval Workflow**: Three-stage validation (pending → approved/rejected)

### Educational Content
- Article management with rich content
- Public-facing educational portal
- Category-based organization

## 🤝 Contributing

We welcome contributions! Please read our [Contributing Guide](CONTRIBUTING.md) for details on:
- Code of conduct
- Development workflow
- Coding standards
- Pull request process
- Testing requirements

## 📝 License

This project is open-source software licensed under the [MIT license](LICENSE).

## 🐛 Bug Reports & Feature Requests

- **Bug Reports:** Please use the [issue tracker](https://github.com/jey41/banksampah-faperta/issues)
- **Feature Requests:** Submit proposals via issues with detailed use cases
- **Security Issues:** Email security concerns privately (see [CONTRIBUTING.md](CONTRIBUTING.md))

## 👥 Team

- **Maintainer:** Muhammad Hisyam Nugroho ([jey41](https://github.com/jey41))
- **Contributors:** See [CONTRIBUTORS.md](CONTRIBUTORS.md) (coming soon)

## 🙏 Acknowledgments

- Laravel community for the excellent framework
- Filament PHP for the powerful admin panel
- All contributors who help make this project better

---

**📞 Support & Contact**

For questions, discussions, or support:
- GitHub Issues: [github.com/jey41/banksampah-faperta/issues](https://github.com/jey41/banksampah-faperta/issues)
- Documentation: [Project Docs](docs/)

---

*Dibuat dengan ❤️ untuk lingkungan yang lebih bersih • Built with love for a cleaner environment*
