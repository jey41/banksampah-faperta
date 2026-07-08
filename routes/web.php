<?php

use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\TrackSiteVisit;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [WelcomeController::class, 'index'])
    ->middleware([TrackSiteVisit::class])
    ->name('welcome');
Route::get('/harga', [WelcomeController::class, 'prices'])->name('public.prices');
Route::get('/artikel', [WelcomeController::class, 'articles'])->name('public.articles');
Route::get('/artikel/{slug}', [WelcomeController::class, 'article'])->name('public.article');

// Role-based Dashboard Redirection
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (in_array($user->role, ['super_admin', 'petugas'])) {
        return redirect('/cms');
    }

    if ($user->role === 'nasabah') {
        return redirect()->route('nasabah.dashboard');
    }

    return redirect('/login');
})->middleware(['auth'])->name('dashboard');

// Nasabah Routes
Route::middleware(['auth', 'role:nasabah'])->prefix('nasabah')->group(function () {
    Route::get('/dashboard', [NasabahController::class, 'dashboard'])->name('nasabah.dashboard');

    Route::get('/jemput', [NasabahController::class, 'pickupRequest'])->name('nasabah.pickup');
    Route::post('/jemput', [NasabahController::class, 'storePickupRequest'])->name('nasabah.pickup.store')->middleware('throttle:10,1');

    Route::get('/tarik', [NasabahController::class, 'withdraw'])->name('nasabah.withdraw');
    Route::post('/tarik', [NasabahController::class, 'storeWithdraw'])->name('nasabah.withdraw.store')->middleware('throttle:10,1');

    Route::get('/riwayat', [NasabahController::class, 'history'])->name('nasabah.history');
    Route::post('/target', [NasabahController::class, 'storeTarget'])->name('nasabah.target.store')->middleware('throttle:10,1');
    Route::delete('/target/{target}', [NasabahController::class, 'deleteTarget'])->name('nasabah.target.delete');
});

// Profile Routes (default Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Printable Receipt Routes (Admin/Petugas only)
Route::middleware(['auth'])->prefix('admin-print')->group(function () {
    Route::get('/deposit/{deposit}/print', function (Deposit $deposit) {
        if (! in_array(auth()->user()->role, ['super_admin', 'petugas'])) {
            abort(403);
        }

        return view('print.deposit', compact('deposit'));
    })->name('admin.deposit.print');

    Route::get('/withdrawal/{withdrawal}/print', function (Withdrawal $withdrawal) {
        if (! in_array(auth()->user()->role, ['super_admin', 'petugas'])) {
            abort(403);
        }

        return view('print.withdrawal', compact('withdrawal'));
    })->name('admin.withdrawal.print');
});

require __DIR__.'/auth.php';
