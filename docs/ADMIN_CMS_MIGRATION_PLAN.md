# Migration Plan — Filament Admin → Custom Laravel Blade CMS

**Project:** Bank Sampah Faperta
**Scope:** Replace the Filament 5 admin panel (`/admin`) with a traditional Laravel MVC + Blade CMS.
**Out of scope:** The React + Inertia Nasabah/Public portal (must remain unchanged), the database schema, and all business logic (services, models, relationships).

> **Status: PLAN ONLY — awaiting approval.** No code is changed by this document.

---

## 1. Current Filament Architecture

The admin panel is a single Filament Panel mounted at `/admin` via `App\Providers\Filament\AdminPanelProvider`.

**Panel provider** (`app/Providers/Filament/AdminPanelProvider.php`)
- `->path('admin')`, brand "Bank Sampah Faperta", Emerald/Slate theme.
- Auto-discovers Resources, Pages, Widgets under `app/Filament/*`.
- Auth handled by Filament's own `Authenticate` middleware — **not** the app's `role` middleware. Access control for admin/petugas relies on `BlockNasabahFromAdmin` (global web middleware) + a 403 handler in `bootstrap/app.php`.

**Resources** (each is a folder with Resource + Pages + Schemas + Tables):

| Filament Resource | Model | Navigation label | Notes |
|---|---|---|---|
| `Users/UserResource` | `User` | Nasabah & Pengguna | CRUD |
| `TrashPrices/TrashPriceResource` | `TrashPrice` | (waste prices) | CRUD |
| `Deposits/DepositResource` | `Deposit` | Setoran Sampah | List/Create/Edit + custom `approve`/`reject`/`print` row actions calling `TransactionService` |
| `Withdrawals/WithdrawalResource` | `Withdrawal` | (withdrawals) | Approve/reject via `TransactionService` |
| `PickupRequests/PickupRequestResource` | `PickupRequest` | Permintaan Jemput | List/Edit (no create), map fields |
| `Articles/ArticleResource` | `Article` | (articles) | CRUD + `FileUpload` cover image |
| `ActivityLogs/ActivityLogResource` | `ActivityLog` | (audit) | List only (read-only) |

**Widgets** (`app/Filament/Widgets/`)
- `StatsOverviewWidget` — donation profit, total volume, retained balance, active-nasabah ratio.
- `TrashCategoryChart`, `TransactionTrendChart`, `DepositCategoryChart` — chart widgets.
- Filament `AccountWidget`.

**Key coupling to preserve:** Filament actions delegate to `App\Services\TransactionService` (`approveDeposit`, `rejectDeposit`, `approveWithdrawal`, `rejectWithdrawal`, `isWithinOperationalHours`) and `App\Services\GamificationService`. These services contain the **entire** money/ledger/badge logic and are UI-agnostic — they carry over verbatim.

**Roles today:** a `role` string column on `users` (`admin` | `petugas` | `nasabah`). No Spatie. Enforcement:
- `EnsureUserHasRole` (alias `role`) — used on Nasabah routes in `routes/web.php`.
- `BlockNasabahFromAdmin` — global, redirects nasabah away from `admin*`.
- `User::isAdmin()` / role checks scattered in routes.

---

## 2. Proposed Blade CMS Architecture

```
Browser
  ↓
routes/admin.php   (prefix "admin", name "admin.", middleware: auth + role:admin,petugas)
  ↓
App\Http\Controllers\Admin\*Controller   (resource controllers)
  ↓
App\Http\Requests\Admin\*Request   (validation)  +  App\Services\* (unchanged business logic)
  ↓
App\Models\*   (unchanged)
  ↓
resources/views/admin/**   (Blade: layout + module views + shared components)
  ↓
Database
```

Design principles:
- **Thin controllers, fat services.** Controllers orchestrate; all financial mutations continue to go through `TransactionService` inside DB transactions. No business logic in controllers or Blade.
- **One consistent layout** (`layouts.admin`) with shared sidebar/navbar/breadcrumb/alerts/modals.
- **Tailwind** for the admin UI (already the project's CSS system — keeps one toolchain; no Bootstrap added).
- **Server-side DataTables** via `yajra/laravel-datatables-oxide` for every listing (search, sort, paginate, filter, bulk actions).
- **Policy-based authorization** so Blade never hardcodes role strings (`@can`).

---

## 3. Folder Structure

```
app/
  Http/
    Controllers/Admin/
      DashboardController.php
      UserController.php
      TrashPriceController.php
      DepositController.php
      WithdrawalController.php
      PickupRequestController.php
      SavingsTargetController.php
      ArticleController.php
      BadgeController.php
      ActivityLogController.php
      ProfileController.php          (admin profile/settings)
    Requests/Admin/
      StoreUserRequest.php / UpdateUserRequest.php
      StoreTrashPriceRequest.php / UpdateTrashPriceRequest.php
      StoreArticleRequest.php / UpdateArticleRequest.php
      ... (one pair per CRUD module)
  Policies/
      UserPolicy.php, DepositPolicy.php, WithdrawalPolicy.php, ...
  Services/                          (UNCHANGED — TransactionService, GamificationService)
  Models/                            (UNCHANGED)

routes/
  admin.php                          (NEW — all admin routes)
  web.php                            (unchanged except: remove /admin redirects, require admin.php)

resources/views/
  layouts/
    admin.blade.php                  (master layout)
  admin/
    partials/
      sidebar.blade.php
      navbar.blade.php
      breadcrumbs.blade.php
      alerts.blade.php
      footer.blade.php
    dashboard/index.blade.php
    users/         index / create / edit / show / _form.blade.php
    trash-prices/  index / create / edit / _form.blade.php
    deposits/      index / show / _items.blade.php  (+ approve/reject modals)
    withdrawals/   index / show
    pickup-requests/ index / edit / show   (+ MapLibre location view)
    savings-targets/ index / create / edit / _form.blade.php
    articles/      index / create / edit / _form.blade.php
    badges/        index / create / edit / _form.blade.php
    activity-logs/ index
    profile/       edit
  components/admin/                   (Blade components, x-admin.*)
    card.blade.php
    table.blade.php
    stat-card.blade.php
    modal.blade.php
    form/input.blade.php, select.blade.php, textarea.blade.php, file.blade.php
    badge.blade.php
    page-header.blade.php
```

---

## 4. Route Structure (`routes/admin.php`)

All routes prefixed `admin`, named `admin.`, guarded by `auth` + `role:admin,petugas` (extend `EnsureUserHasRole` to accept a comma list — see §10).

```php
Route::middleware(['auth', 'role:admin,petugas'])
    ->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data
    Route::resource('trash-prices', TrashPriceController::class)->except('show');
    Route::resource('badges', BadgeController::class)->except('show');

    // Transactions
    Route::resource('deposits', DepositController::class)->only(['index','show']);
    Route::post('deposits/{deposit}/approve', [DepositController::class,'approve'])->name('deposits.approve');
    Route::post('deposits/{deposit}/reject',  [DepositController::class,'reject'])->name('deposits.reject');

    Route::resource('withdrawals', WithdrawalController::class)->only(['index','show']);
    Route::post('withdrawals/{withdrawal}/approve', [WithdrawalController::class,'approve'])->name('withdrawals.approve');
    Route::post('withdrawals/{withdrawal}/reject',  [WithdrawalController::class,'reject'])->name('withdrawals.reject');

    Route::resource('pickup-requests', PickupRequestController::class)->only(['index','show','edit','update']);
    Route::post('pickup-requests/{pickupRequest}/assign', [PickupRequestController::class,'assign'])->name('pickup-requests.assign');

    Route::resource('savings-targets', SavingsTargetController::class);

    // Content Management
    Route::resource('articles', ArticleController::class);

    // User Management (admin only — see policies)
    Route::resource('users', UserController::class);

    // System
    Route::get('activity-logs', [ActivityLogController::class,'index'])->name('activity-logs.index');
    Route::get('profile', [ProfileController::class,'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class,'update'])->name('profile.update');

    // DataTables JSON endpoints (server-side) reuse the same index routes with ?draw=... (Yajra),
    // or dedicated *.data routes if we prefer separation.
});
```

Existing print routes (`admin.deposit.print`, `admin.withdrawal.print`) stay as-is.

---

## 5. Controller Structure

Standard resource controllers (`index/create/store/edit/update/destroy`) plus module-specific verbs. Pattern:

```php
class DepositController extends Controller
{
    public function __construct(private TransactionService $tx) {
        $this->authorizeResource(Deposit::class, 'deposit');
    }

    public function index(Request $r)      // returns view OR Yajra JSON when $r->ajax()
    public function show(Deposit $deposit)  // deposit + items + validator
    public function approve(ApproveDepositRequest $r, Deposit $deposit) {
        $this->tx->approveDeposit($deposit, $r->validated()['items'], auth()->id());
        return back()->with('success', 'Setoran disetujui.');
    }
    public function reject(Deposit $deposit) {
        $this->tx->rejectDeposit($deposit, auth()->id());
        return back()->with('success', 'Setoran ditolak.');
    }
}
```

- **No business logic in controllers.** `approve`/`reject` call the existing service methods that already wrap everything in `DB::transaction` with row locks.
- Flash messages (`success`/`error`) rendered by the shared `alerts` partial.
- File uploads (article cover, user avatar) handled with `Storage::disk('public')` — replacing Filament `FileUpload`.

---

## 6. Blade Layout Structure

```
layouts/admin.blade.php
├── <head> Vite (Tailwind) + DataTables CSS/JS
├── @include('admin.partials.sidebar')      ← navigation groups (§9), @can-gated
├── @include('admin.partials.navbar')       ← brand, user dropdown, logout
├── @include('admin.partials.breadcrumbs')  ← @yield('breadcrumbs')
├── @include('admin.partials.alerts')       ← session success/error/validation
├── <main>@yield('content')</main>
└── @stack('modals') @stack('scripts')
```

Reusable Blade components (`x-admin.*`): `card`, `table`, `stat-card`, `modal`, `page-header`, `badge`, and `form.*` inputs. Every module view `@extends('layouts.admin')` and composes these — **zero duplicated chrome**.

---

## 7. CRUD Pattern (uniform across all modules)

1. **Route** — `Route::resource(...)` in `routes/admin.php`.
2. **Controller** — resource controller, `authorizeResource` in constructor.
3. **Form Request** — `Store*Request` / `Update*Request` with rules mirrored from the current Nasabah-side validation and Filament schema constraints.
4. **Index view** — `<x-admin.table>` + server-side DataTable (search/sort/paginate/filter, bulk delete where safe).
5. **Create/Edit views** — shared `_form.blade.php` partial with `x-admin.form.*` inputs, old() repopulation, `@error` messages, image-upload preview (JS `FileReader`) where applicable.
6. **Feedback** — `->with('success'|'error', ...)` → `alerts` partial.
7. **Delete** — confirmation via `x-admin.modal` + method-spoofed `DELETE` form.

---

## 8. Data Flow

**Read (listing):**
`GET /admin/deposits` → `DepositController@index` → (ajax) Yajra query over `Deposit::with('user','validator')` → JSON → DataTables renders.

**Write (approve deposit — money path):**
`POST /admin/deposits/{id}/approve` → `ApproveDepositRequest` validates real weights → `DepositController@approve` → `TransactionService::approveDeposit()` → `DB::transaction` { lock deposit, recalc items/carbon, credit `saldo`, write `Mutation` ledger, write `ActivityLog` } → `GamificationService::syncBadges()` → redirect back with flash.

The **entire** money/ledger flow stays inside the untouched service — the Blade layer only supplies inputs and shows results, exactly as Filament did.

---

## 9. Navigation Structure

```
Dashboard

Master Data
  ├─ Waste Prices        (trash-prices)
  └─ Badges              (badges)

Transactions
  ├─ Deposits            (deposits)
  ├─ Withdrawals         (withdrawals)
  └─ Pickup Requests     (pickup-requests)

Content Management
  └─ Articles            (articles)

User Management
  ├─ Users               (users)          [admin only]
  └─ Savings Targets     (savings-targets)

System
  ├─ Activity Logs       (activity-logs)  [read-only]
  ├─ Profile             (profile)
  └─ Settings            (future-ready placeholder)
```

Sidebar items render via `@can` against policies (§10) so petugas automatically sees a reduced menu.

---

## 10. Permission Architecture

Keep the existing **role-column** model (no Spatie migration needed — lower risk), but formalize authorization so Blade contains no hardcoded roles.

1. **Route guard** — extend `EnsureUserHasRole::handle` to accept multiple roles (`role:admin,petugas`) by exploding on comma. Backwards compatible with existing single-role usage on Nasabah routes.
2. **Policies** — one policy per model (`UserPolicy`, `DepositPolicy`, …). Example: only `admin` may manage users; both `admin` and `petugas` may validate deposits/withdrawals; activity logs are view-only for all admin roles.
3. **Blade** — gate menu items and action buttons with `@can('create', App\Models\User::class)` etc. — never `@if(auth()->user()->role === ...)`.
4. **Controllers** — `authorizeResource()` / `$this->authorize()` enforce the same policies server-side.

```
auth  →  role:admin,petugas (route)  →  Policy check (controller + Blade @can)  →  action
```

> Optional future upgrade to `spatie/laravel-permission` is compatible with this design (policies stay; only the check source changes). Not required for this migration.

---

## 11. CMS Module Mapping (Filament → Blade)

| Module | From (Filament) | To (Blade) | Business logic source |
|---|---|---|---|
| Dashboard | `Dashboard` page + 4 Widgets | `DashboardController@index` + `dashboard/index` with `x-admin.stat-card` + chart JS | Re-query stats inline (port `StatsOverviewWidget`) |
| Users | `UserResource` | `UserController` (full CRUD) + avatar upload | — |
| Waste Prices | `TrashPriceResource` | `TrashPriceController` (CRUD) | — |
| Deposits | `DepositResource` (+approve/reject/print) | `DepositController` (index/show/approve/reject) | **`TransactionService`** |
| Withdrawals | `WithdrawalResource` | `WithdrawalController` (index/show/approve/reject) | **`TransactionService`** |
| Pickup Requests | `PickupRequestResource` | `PickupRequestController` (index/show/edit/assign) + MapLibre view | pickup workflow on model |
| Savings Targets | (managed nasabah-side) | `SavingsTargetController` (CRUD, admin view) | model logic |
| Articles | `ArticleResource` (+FileUpload) | `ArticleController` (CRUD) + cover upload, slug, featured, publish | slug/publish on model |
| Badges | (GamificationService) | `BadgeController` (CRUD) | **`GamificationService`** |
| Activity Logs | `ActivityLogResource` | `ActivityLogController@index` (read-only DataTable) | — |

---

## 12. Components to Reuse (carry over untouched)

- **All Models & relationships** (`app/Models/*`).
- **`App\Services\TransactionService`** — deposit/withdrawal approval, ledger, admin-fee, operational-hours, SLA.
- **`App\Services\GamificationService`** — badges/eco-points.
- **Database schema & migrations** — no changes.
- **Print Blade views** (`resources/views/print/*`) and print routes.
- **`EnsureUserHasRole` middleware** — extended (not replaced) for multi-role.
- **React/Inertia Nasabah + Public portal** — 100% untouched.
- Tailwind config / Vite pipeline.

## 13. Components to Replace

| Remove | Replace with |
|---|---|
| `App\Filament\**` (Resources, Pages, Schemas, Tables, Widgets) | `Controllers/Admin/*`, `Requests/Admin/*`, `resources/views/admin/**` |
| `App\Providers\Filament\AdminPanelProvider` | `routes/admin.php` + `layouts.admin` |
| Filament auth middleware & panel routing | `auth` + `role:admin,petugas` on `admin.php` |
| Filament `FileUpload` | `Storage::disk('public')` + JS preview |
| Filament `Notification` | session flash → `alerts` partial |
| Filament chart widgets | Chart.js/ApexCharts in Blade fed by controller data |
| `filament/filament` composer dep (final cleanup step) | `yajra/laravel-datatables-oxide` |

The `/admin` redirects in `routes/web.php`, `BlockNasabahFromAdmin`, and the 403 handler in `bootstrap/app.php` are reviewed and simplified once the Blade panel owns `/admin`.

---

## 14. Estimated Migration Complexity

| Module | Complexity | Driver |
|---|---|---|
| Layout + shared components | Medium | Foundational; everything depends on it |
| Dashboard | Low–Med | Port widget queries + chart JS |
| Waste Prices / Badges / Savings Targets | Low | Plain CRUD |
| Articles | Medium | Image upload, slug, featured, publish states |
| Users | Medium | Roles, status, avatar, validation parity |
| **Deposits** | **High** | Approve flow with per-item real-weight repeater → service; print |
| **Withdrawals** | **High** | Approve/reject, admin-fee, balance guards → service |
| Pickup Requests | Medium | MapLibre map render, petugas assignment |
| Activity Logs | Low | Read-only DataTable |
| Permissions/policies | Medium | Multi-role middleware + policy set |
| Filament removal & cleanup | Low | After parity confirmed |

**Overall: Medium–High.** The risk concentrates in Deposits & Withdrawals because they mutate balances and the ledger — but that logic already lives in `TransactionService` and does **not** change, which sharply reduces risk. The bulk of the work is UI (Blade + DataTables) rebuilding.

---

## 15. Recommended Implementation Order

1. **Foundation** — install `yajra/laravel-datatables-oxide`; create `layouts/admin.blade.php`, partials, and `x-admin.*` components; add `routes/admin.php`; extend `role` middleware for multi-role; scaffold policies.
2. **Dashboard** — port stats + charts (validates the layout end-to-end).
3. **Simple CRUD** — Waste Prices → Badges → Savings Targets (proves the uniform CRUD pattern + DataTables + form requests).
4. **Articles** — adds file upload/preview, slug, publish/featured (content CMS pattern).
5. **Users** — role assignment, status, avatar, validation parity + `UserPolicy`.
6. **Deposits** — index/show + approve (real-weight modal)/reject via `TransactionService`; verify ledger + badges; wire print.
7. **Withdrawals** — approve/reject via `TransactionService`; verify admin-fee & balance guards.
8. **Pickup Requests** — list/show/edit, MapLibre location, petugas assignment.
9. **Activity Logs** — read-only DataTable.
10. **Authorization pass** — finalize policies, `@can` gating, petugas-reduced menu.
11. **Verification** — confirm Nasabah/Public portal still reflects admin-managed data (prices, articles, balances) unchanged.
12. **Cleanup** — remove `app/Filament/**`, `AdminPanelProvider`, simplify `/admin` redirects & 403 handler; remove `filament/filament` from `composer.json`.

Each step is independently shippable and testable; Filament can stay installed until step 12 so the panel is never fully down during migration (run the new panel at a temp prefix if a side-by-side cutover is preferred).

---

## Open Questions (please confirm before implementation)

1. **UI framework:** proceed with **Tailwind** (keeps one toolchain), or do you specifically want **Bootstrap** for the admin?
2. **Permissions:** keep the existing **role-column + policies** approach, or invest in **Spatie laravel-permission** now (adds tables/migrations)?
3. **Cutover:** in-place at `/admin` (Filament removed last), or side-by-side at a temporary prefix (e.g. `/cms`) until parity is verified?
4. **Charts:** preferred library — **Chart.js** or **ApexCharts**?
5. **Avatar field:** users currently have no avatar column — add one, or use initials/gravatar placeholders?
