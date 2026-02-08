<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| 1. JALUR PUBLIK (GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'prosesLogin'])->name('login.proses');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. JALUR TERPROTEKSI (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // --- DASHBOARD UTAMA ---
    Route::get('/dashboard', [AlatController::class, 'index'])->name('alat.index');
    
    // --- MANAJEMEN USER (ADMIN ONLY) ---
    Route::post('/user/store', [AlatController::class, 'storeUser'])->name('user.store');
    Route::get('/user/destroy/{id}', [AlatController::class, 'destroyUser'])->name('user.destroy');

    // --- MANAJEMEN ALAT (ADMIN ONLY) ---
    Route::post('/alat/store', [AlatController::class, 'store'])->name('alat.store');
    Route::get('/alat/destroy/{id}', [AlatController::class, 'destroy'])->name('alat.destroy');
    
    // --- LOGIKA PEMINJAMAN & VERIFIKASI ---
    Route::post('/pinjam', [AlatController::class, 'ajukanPinjam'])->name('pinjam.store'); 
    Route::get('/pinjam/setujui/{id}', [AlatController::class, 'setujuiPinjam'])->name('pinjam.setujui');
    
    // FIX ERROR: Menambahkan rute tolak untuk menghilangkan Error 500
    Route::get('/pinjam/tolak/{id}', [AlatController::class, 'tolakPinjam'])->name('pinjam.tolak'); 
    
    Route::get('/pinjam/kembalikan/{id}', [AlatController::class, 'kembalikanAlat'])->name('pinjam.kembalikan'); 

    // --- LAPORAN ---
    Route::get('/laporan/cetak', [AlatController::class, 'cetakLaporan'])->name('laporan.cetak');
});