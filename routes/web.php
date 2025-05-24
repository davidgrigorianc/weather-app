<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin')->name('root');

Route::prefix('admin')->name('admin.')->group(function () {
    Auth::routes();

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('welcome');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/users', [DashboardController::class, 'storeUser'])->name('dashboard.users.store');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/users/{user}/resend', [DashboardController::class, 'resendEmail'])->name('users.resend');
    });
});
