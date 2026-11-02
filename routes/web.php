<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Middleware\EnsureJobSeekerAuthenticated;

// Mengarahkan halaman utama (root) langsung ke daftar lowongan pekerjaan
Route::get('/', function () {
    return redirect()->route('jobs.index');
});

// PBI-04 & PBI-05: Daftar dan Detail Lowongan Pekerjaan
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');

// Route Fallback Login untuk keamanan middleware auth jika modul login web belum di-merge
Route::get('/login', function () {
    return redirect()->route('jobs.index')->with('error', 'Silakan login terlebih dahulu untuk mengakses pengajuan lamaran.');
})->name('login');

// PBI-06: Pengajuan Lamaran Pekerjaan (Memerlukan Akun Pencari Kerja)
Route::middleware(['web', EnsureJobSeekerAuthenticated::class])->group(function () {
    Route::get('/jobs/{id}/apply', [JobApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{id}/apply', [JobApplicationController::class, 'store'])->name('jobs.apply.store');
});
