<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IkssController;
use App\Http\Controllers\JadwalController;
use Illuminate\Support\Facades\Route;

// 1. Halaman Utama (Redirect ke Beranda)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 2. Grup Route untuk Modul
Route::prefix('dashboard')->group(function () {
    // Beranda / Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

Route::post('/jadwals', [JadwalController::class, 'store'])->name('jadwals.store');
Route::delete('/jadwals/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwals.destroy');
Route::patch('/jadwals/{jadwal}/completion', [JadwalController::class, 'updateCompletion'])
    ->name('jadwals.completion.update');

// 3. Modul IKSS
Route::prefix('ikss')->group(function () {
    // Tabel IKSS
    Route::get('/', [IkssController::class, 'index'])->name('ikss.index');
});
