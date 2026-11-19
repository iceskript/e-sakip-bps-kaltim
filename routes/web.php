<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\IkssController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [GoogleAuthController::class, 'show'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
    Route::get('/forgot-password', [PasswordController::class, 'requestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'resetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'reset'])
        ->middleware('throttle:6,1')
        ->name('password.update');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');
});

Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

Route::middleware(['auth', 'user.active'])->group(function () {
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
    Route::patch('/account/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('account.password.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ikss', [IkssController::class, 'index'])->name('ikss.index');

    Route::post('/jadwals', [JadwalController::class, 'store'])->name('jadwals.store');
    Route::delete('/jadwals/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwals.destroy');
    Route::patch('/jadwals/{jadwal}/completion', [JadwalController::class, 'updateCompletion'])
        ->name('jadwals.completion.update');

    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');

    Route::prefix('admin')->name('admin.')->middleware('user.admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])
            ->name('users.status.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });
});
