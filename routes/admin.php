<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\PickupRequestController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\TrashPriceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin CMS (Blade) Routes
|--------------------------------------------------------------------------
| Mounted at /cms so it coexists with the legacy Filament panel (/admin)
| during migration. Guarded by auth + role:admin,petugas. Authorization
| granularity is enforced per-action via Policies (see AuthServiceProvider).
*/

Route::middleware(['auth', 'role:super_admin,petugas'])
    ->prefix('cms')
    ->name('cms.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('trash-stats', [DashboardController::class, 'getTrashStats'])->name('dashboard.trash-stats');

        // ---- Master Data ----
        Route::resource('trash-prices', TrashPriceController::class)->except('show');
        Route::get('badges', [BadgeController::class, 'index'])->name('badges.index');

        // ---- Transactions ----
        Route::get('deposits', [DepositController::class, 'index'])->name('deposits.index');
        Route::get('deposits/{deposit}', [DepositController::class, 'show'])->name('deposits.show');
        Route::post('deposits/{deposit}/approve', [DepositController::class, 'approve'])->name('deposits.approve')->middleware('throttle:30,1');
        Route::post('deposits/{deposit}/reject', [DepositController::class, 'reject'])->name('deposits.reject')->middleware('throttle:30,1');

        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('withdrawals/{withdrawal}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::post('withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve')->middleware('throttle:30,1');
        Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject')->middleware('throttle:30,1');

        Route::get('pickup-requests', [PickupRequestController::class, 'index'])->name('pickup-requests.index');
        Route::get('pickup-requests/{pickup_request}', [PickupRequestController::class, 'show'])->name('pickup-requests.show');
        Route::get('pickup-requests/{pickup_request}/edit', [PickupRequestController::class, 'edit'])->name('pickup-requests.edit');
        Route::put('pickup-requests/{pickup_request}', [PickupRequestController::class, 'update'])->name('pickup-requests.update');

        // ---- Content Management ----
        Route::resource('articles', ArticleController::class)->except('show');

        // ---- Pengaturan Landing Page (per-section) ----
        Route::get('site-settings/hero', [SiteSettingController::class, 'hero'])->name('site-settings.hero');
        Route::put('site-settings/hero', [SiteSettingController::class, 'updateHero'])->name('site-settings.hero.update');
        Route::get('site-settings/workflow', [SiteSettingController::class, 'workflow'])->name('site-settings.workflow');
        Route::put('site-settings/workflow', [SiteSettingController::class, 'updateWorkflow'])->name('site-settings.workflow.update');
        Route::get('site-settings/schedule', [SiteSettingController::class, 'schedule'])->name('site-settings.schedule');
        Route::put('site-settings/schedule', [SiteSettingController::class, 'updateSchedule'])->name('site-settings.schedule.update');
        Route::get('site-settings/partners', [SiteSettingController::class, 'partners'])->name('site-settings.partners');
        Route::post('partners', [SiteSettingController::class, 'storePartner'])->name('partners.store');
        Route::delete('partners/{partner}', [SiteSettingController::class, 'destroyPartner'])->name('partners.destroy');

        // ---- User Management ----
        Route::resource('users', UserController::class);

        // ---- System ----
        Route::post('visits/cleanup', [DashboardController::class, 'cleanupVisits'])->name('visits.cleanup')->middleware('throttle:10,1');
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
