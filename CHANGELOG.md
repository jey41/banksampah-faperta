# Changelog

All notable changes to Bank Sampah Faperta will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Comprehensive documentation suite (PROJECT_OVERVIEW.md, docs/, CONTRIBUTING.md)
- Developer guide with setup, coding standards, testing, debugging
- Database schema documentation with ERD and common queries
- Deployment guide for VPS, Docker, and PaaS platforms
- Contributing guidelines with PR process and templates
- Documentation index with quick-start navigation
- Updated main README with documentation links

### Documentation
- Complete project architecture documentation
- Role-based access control documentation
- API routes documentation
- Gamification system documentation

---

## [1.0.0] - 2026-06-28

### Added
- **Landing Page** - Animated hero section with impact counters
- **Public Layout** - Dynamic navigation with smooth scroll animations
- **Gamification System** - Eco points, level thresholds, badge unlocking, streak tracking
- **Article Portal** - Public article directory and detail views
- **Price Catalog** - Public waste price listing

### Features
- Multi-role user system (admin, petugas, nasabah)
- Deposit management with approval workflow
- Withdrawal system with bank integration
- Pickup request scheduling with location tracking
- Savings targets for nasabah
- Double-entry mutation ledger
- Activity logging for audit trail
- Filament admin panel with resource management

### Technical
- Laravel 11 with PHP 8.3
- React 18 + Inertia.js v2
- Tailwind CSS v4
- Filament PHP v5.6
- SQLite/MySQL/PostgreSQL support
- Concurrent development server

---

## [0.9.0] - 2026-06-20

### Added
- User demographics (age, gender, occupation, education)
- Pickup request location fields (latitude, longitude)
- Withdrawal history tracking
- Trash price category types

### Changed
- Enhanced deposit model with donation option
- Improved article image upload handling
- Updated migration structure

---

## [0.8.0] - 2026-06-15

### Added
- Initial public landing page structure
- Welcome page with React components
- Basic authentication (Laravel Breeze)
- Profile management

### Changed
- Migrated to Inertia.js for frontend
- Implemented role-based dashboard redirection

---

## [0.7.0] - 2026-06-14

### Added
- Core database schema (users, deposits, withdrawals, trash_prices, articles)
- User roles and permissions system
- Basic deposit/withdrawal CRUD
- Filament admin resources

### Infrastructure
- Laravel 11 project initialization
- Database migrations and seeders
- Basic project structure

---

## Migration Guide

### Upgrading to 1.0.0

**Breaking Changes:**
- Minimum PHP version: 8.3
- Laravel 11 required
- Filament 5.x required
- Tailwind CSS v4 (different config structure)

**Required Actions:**
```bash
# Update dependencies
composer update
npm install

# Run new migrations
php artisan migrate

# Rebuild assets
npm run build

# Clear caches
php artisan optimize:clear
```

---

## Support Policy

| Version | Status | Security Fixes | Bug Fixes |
|---------|--------|----------------|-----------|
| 1.x     | Active | Yes            | Yes       |
| 0.x     | EOL    | No             | No        |

---

*For detailed release notes, see [GitHub Releases](https://github.com/jey41/banksampah-faperta/releases).*