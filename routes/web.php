<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IkssController;

// 1. Halaman Utama (Redirect ke Beranda)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 2. Grup Route untuk Modul
Route::prefix('dashboard')->group(function () {
    // Beranda / Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 3. Modul IKSS
Route::prefix('ikss')->group(function () {
    // Tabel IKSS
    Route::get('/', [IkssController::class, 'index'])->name('ikss.index');
});