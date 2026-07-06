# Developer Guide - Bank Sampah Faperta

This guide provides detailed information for developers working on the Bank Sampah Faperta project.

---

## 📚 Table of Contents

1. [Development Environment Setup](#development-environment-setup)
2. [Code Standards & Conventions](#code-standards--conventions)
3. [Git Workflow](#git-workflow)
4. [Testing Guidelines](#testing-guidelines)
5. [Debugging Tips](#debugging-tips)
6. [Common Tasks](#common-tasks)
7. [Troubleshooting](#troubleshooting)

---

## 🛠️ Development Environment Setup

### Required Software

- **PHP 8.3+** with extensions:
  - `ext-pdo`, `ext-mbstring`, `ext-tokenizer`, `ext-xml`, `ext-ctype`, `ext-json`, `ext-bcmath`
- **Composer 2.x**
- **Node.js 18+ and npm**
- **Git**
- **Database:** MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+

### IDE Recommendations

**VS Code Extensions:**
- PHP Intelephense
- Laravel Extension Pack
- ES7+ React/Redux/React-Native snippets
- Tailwind CSS IntelliSense
- ESLint
- Prettier

**PHPStorm:**
- Laravel plugin
- Tailwind CSS support
- JavaScript and TypeScript support

### Environment Configuration

#### 1. Clone and Setup

```bash
git clone https://github.com/jey41/banksampah-faperta.git
cd banksampah-faperta
composer install
npm install
cp .env.example .env
php artisan key:generate
```

#### 2. Database Setup

**SQLite (Quick Development):**
```bash
touch database/database.sqlite
```

**MySQL:**
```bash
# Create database
mysql -u root -p
CREATE DATABASE banksampah_faperta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Update .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=banksampah_faperta
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 3. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

Default seeded accounts will be created (check `DatabaseSeeder.php`).

#### 4. Storage Linking

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

#### 5. Start Development

**Option A: Concurrent Mode (Recommended)**
```bash
composer run dev
```
Runs Laravel server, queue worker, log viewer, and Vite simultaneously.

**Option B: Separate Terminals**
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite
npm run dev

# Terminal 3: Queue (optional)
php artisan queue:work

# Terminal 4: Logs (optional)
php artisan pail
```

---

## 📝 Code Standards & Conventions

### PHP / Laravel

#### Code Style
Follow **PSR-12** standards. Use Laravel Pint for automatic formatting:

```bash
./vendor/bin/pint
```

#### Naming Conventions

**Classes:**
- Models: Singular PascalCase (`User`, `Deposit`, `TrashPrice`)
- Controllers: Singular + `Controller` (`UserController`, `DepositController`)
- Resources (Filament): Singular + `Resource` (`ArticleResource`)
- Requests: Descriptive + `Request` (`StoreDepositRequest`)

**Methods:**
- camelCase: `getUserDeposits()`, `approveWithdrawal()`
- CRUD: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`

**Variables:**
- camelCase: `$totalPrice`, `$userData`, `$isApproved`

**Database:**
- Tables: plural snake_case (`users`, `trash_prices`, `deposit_items`)
- Columns: snake_case (`created_at`, `account_no`, `price_buy`)
- Foreign keys: singular + `_id` (`user_id`, `deposit_id`)

#### Controller Best Practices

```php
class DepositController extends Controller
{
    public function store(StoreDepositRequest $request)
    {
        // Use form requests for validation
        $validated = $request->validated();
        
        // Use DB transactions for multi-step operations
        DB::transaction(function () use ($validated) {
            $deposit = Deposit::create($validated);
            // ... other operations
        });
        
        // Return appropriate responses
        return redirect()
            ->route('nasabah.dashboard')
            ->with('success', 'Deposit berhasil dibuat.');
    }
}
```

#### Model Best Practices

```php
class User extends Authenticatable
{
    // Use PHP 8 attributes for fillable/hidden
    #[Fillable(['name', 'email', 'password'])]
    #[Hidden(['password', 'remember_token'])]
    
    // Type-hint relationships
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
    
    // Use accessors/mutators with new syntax
    protected function totalDeposits(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->deposits()->sum('total_price'),
        );
    }
    
    // Define casts
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'saldo' => 'integer',
        ];
    }
}
```

#### Query Optimization

```php
// BAD: N+1 Query Problem
$deposits = Deposit::all();
foreach ($deposits as $deposit) {
    echo $deposit->user->name; // Queries user for each deposit
}

// GOOD: Eager Loading
$deposits = Deposit::with('user')->get();
foreach ($deposits as $deposit) {
    echo $deposit->user->name; // User already loaded
}

// BETTER: Selective Loading
$deposits = Deposit::with('user:id,name')->get();
```

### JavaScript / React

#### Code Style
Use ESLint and Prettier. Run:

```bash
npm run lint
npm run format
```

#### Component Structure

```jsx
// PascalCase for components
// Props destructuring
// PropTypes or TypeScript

import { useState } from 'react';

export default function DepositCard({ deposit, onApprove }) {
    const [isLoading, setIsLoading] = useState(false);
    
    const handleApprove = async () => {
        setIsLoading(true);
        try {
            await onApprove(deposit.id);
        } finally {
            setIsLoading(false);
        }
    };
    
    return (
        <div className="bg-white rounded-lg shadow p-6">
            <h3 className="text-lg font-semibold">{deposit.user.name}</h3>
            <p className="text-gray-600">Rp {deposit.total_price.toLocaleString()}</p>
            <button 
                onClick={handleApprove}
                disabled={isLoading}
                className="mt-4 btn-primary"
            >
                {isLoading ? 'Processing...' : 'Approve'}
            </button>
        </div>
    );
}
```

#### Inertia.js Patterns

```jsx
// Page component
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Dashboard({ auth, deposits, stats }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Dashboard" />
            
            <div className="py-12">
                {/* Content */}
            </div>
        </AuthenticatedLayout>
    );
}
```

#### Inertia Form Handling

```jsx
import { useForm } from '@inertiajs/react';

export default function WithdrawForm() {
    const { data, setData, post, processing, errors } = useForm({
        amount: '',
        bank_name: '',
        account_number: '',
        account_name: '',
    });
    
    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('nasabah.withdraw.store'));
    };
    
    return (
        <form onSubmit={handleSubmit}>
            <input 
                type="number" 
                value={data.amount}
                onChange={e => setData('amount', e.target.value)}
            />
            {errors.amount && <span className="text-red-600">{errors.amount}</span>}
            
            <button type="submit" disabled={processing}>
                Submit
            </button>
        </form>
    );
}
```

### CSS / Tailwind

#### Utility-First Approach

```jsx
// Prefer Tailwind utilities
<div className="flex items-center justify-between p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
    <h2 className="text-xl font-bold text-gray-800">Title</h2>
</div>

// Extract to components when repeated
// components/Button.jsx
export function PrimaryButton({ children, ...props }) {
    return (
        <button 
            className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            {...props}
        >
            {children}
        </button>
    );
}
```

---

## 🔄 Git Workflow

### Branch Naming

```
main                    # Production-ready code
develop                 # Integration branch
feature/user-badges     # New features
bugfix/deposit-calc     # Bug fixes
hotfix/security-patch   # Urgent production fixes
refactor/controllers    # Code refactoring
docs/api-guide          # Documentation
```

### Commit Messages

Follow **Conventional Commits**:

```
feat: add user badge system
fix: correct deposit total calculation
docs: update API documentation
style: format code with Pint
refactor: extract deposit logic to service
test: add withdrawal validation tests
chore: update dependencies
```

### Pull Request Process

1. **Create Feature Branch**
   ```bash
   git checkout -b feature/your-feature
   ```

2. **Make Changes and Commit**
   ```bash
   git add .
   git commit -m "feat: add your feature"
   ```

3. **Push to Remote**
   ```bash
   git push -u origin feature/your-feature
   ```

4. **Create Pull Request**
   - Use descriptive title
   - Include detailed description
   - Reference related issues
   - Add screenshots for UI changes

5. **Code Review**
   - Address feedback
   - Keep commits clean

6. **Merge**
   - Squash commits if needed
   - Delete branch after merge

---

## 🧪 Testing Guidelines

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/DepositTest.php

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter testUserCanCreateDeposit
```

### Writing Feature Tests

```php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepositTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_nasabah_can_view_deposits(): void
    {
        $user = User::factory()->create(['role' => 'nasabah']);
        
        $response = $this->actingAs($user)
            ->get(route('nasabah.dashboard'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Nasabah/Dashboard')
            ->has('deposits')
        );
    }
    
    public function test_admin_can_approve_deposit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $deposit = Deposit::factory()->create(['status' => 'pending']);
        
        $response = $this->actingAs($admin)
            ->patch(route('admin.deposits.approve', $deposit));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => 'approved',
        ]);
    }
}
```

### Writing Unit Tests

```php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function test_user_has_role_method_works(): void
    {
        $admin = new User(['role' => 'admin']);
        
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('nasabah'));
    }
    
    public function test_user_balance_updates_correctly(): void
    {
        $user = User::factory()->create(['saldo' => 10000]);
        
        $user->saldo += 5000;
        $user->save();
        
        $this->assertEquals(15000, $user->fresh()->saldo);
    }
}
```

---

## 🐛 Debugging Tips

### Laravel Debugging

#### Using `dd()` and `dump()`

```php
// Die and dump
dd($deposits, $user);

// Dump without dying
dump($data);
```

#### Query Logging

```php
DB::enableQueryLog();

// Your queries here
$deposits = Deposit::with('user')->get();

dd(DB::getQueryLog());
```

#### Laravel Debugbar (Install for Development)

```bash
composer require barryvdh/laravel-debugbar --dev
```

#### Tinker (REPL)

```bash
php artisan tinker

>>> $user = User::find(1)
>>> $user->deposits()->count()
>>> Deposit::where('status', 'pending')->get()
```

### React Debugging

#### Console Logging

```jsx
console.log('Props:', { deposit, user });
console.table(deposits);
console.group('Form Submission');
console.log('Data:', data);
console.log('Errors:', errors);
console.groupEnd();
```

#### React DevTools

Install browser extension for component inspection.

#### Inertia Events

```jsx
import { router } from '@inertiajs/react';

router.on('navigate', (event) => {
    console.log('Navigating to:', event.detail.page.url);
});

router.on('success', (event) => {
    console.log('Request successful:', event.detail.page);
});

router.on('error', (event) => {
    console.error('Request failed:', event.detail.errors);
});
```

---

## 📋 Common Tasks

### Creating New Models

```bash
# Model with migration, factory, and seeder
php artisan make:model Badge -mfs

# Model with controller
php artisan make:model Badge -c

# Model with resource controller
php artisan make:model Badge -cr
```

### Creating Controllers

```bash
# Standard controller
php artisan make:controller BadgeController

# Resource controller
php artisan make:controller BadgeController --resource

# API controller
php artisan make:controller BadgeController --api
```

### Creating Migrations

```bash
# Create table
php artisan make:migration create_badges_table

# Modify table
php artisan make:migration add_level_to_users_table
```

### Creating Form Requests

```bash
php artisan make:request StoreDepositRequest
```

### Creating Policies

```bash
php artisan make:policy DepositPolicy --model=Deposit
```

### Creating Filament Resources

```bash
php artisan make:filament-resource Badge --generate
```

### Clearing Caches

```bash
# Clear all caches
php artisan optimize:clear

# Individual caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Operations

```bash
# Fresh migration
php artisan migrate:fresh

# Fresh with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. **Permission Errors (Storage/Cache)**

```bash
# Windows (Git Bash)
chmod -R 775 storage bootstrap/cache

# Or delete and recreate
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
```

#### 2. **Node Module Issues**

```bash
rm -rf node_modules
rm package-lock.json
npm install
```

#### 3. **Composer Issues**

```bash
composer clear-cache
composer install
```

#### 4. **Vite Not Loading**

Check `.env`:
```env
VITE_APP_NAME="${APP_NAME}"
APP_URL=http://localhost:8000
```

Restart Vite:
```bash
npm run dev
```

#### 5. **Database Connection Failed**

- Check `.env` database credentials
- Ensure database exists
- For SQLite, ensure `database/database.sqlite` exists

#### 6. **Queue Jobs Not Processing**

```bash
# Restart queue worker
php artisan queue:restart

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

#### 7. **Inertia Version Mismatch**

Clear cache and reload:
```bash
php artisan cache:clear
# Hard refresh browser (Ctrl+Shift+R)
```

---

## 🚀 Performance Tips

### Optimization Checklist

- [ ] Use eager loading for relationships
- [ ] Index frequently queried columns
- [ ] Cache expensive queries
- [ ] Optimize images (WebP, compression)
- [ ] Use CDN for static assets
- [ ] Enable OPcache in production
- [ ] Use queue for heavy tasks
- [ ] Implement pagination

### Production Deployment

```bash
# Optimize for production
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

---

*Happy coding! 🎉*
