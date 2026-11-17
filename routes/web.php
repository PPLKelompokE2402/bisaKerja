<?php

use App\Http\Controllers\Employer\JobVacancyController;
use App\Http\Controllers\Employer\JobVacancyStatusController;
use App\Http\Controllers\ProviderController;
use App\Http\Middleware\UseDemoEmployer;
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

    return view('welcome');
})->name('welcome');

Route::get('/biodata-penyedia', function () {
    return view('auth.biodata-penyedia');
})->name('provider.biodata');

Route::post('/biodata-penyedia', [ProviderController::class, 'store'])
    ->name('provider.biodata.store');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register/role', function () {
    return view('auth.choose-role');
})->name('register.role');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');
Route::get('/admin/verifikasi', function () {
    return view('admin.verifikasi');
})->name('admin.verifikasi');
Route::get('/admin/pengguna', function () {
    return view('admin.pengguna');
})->name('admin.pengguna');
Route::prefix('pemberi-kerja')
    ->name('employer.')
    ->middleware(UseDemoEmployer::class)
    ->group(function () {
        Route::get('lowongan', [JobVacancyController::class, 'index'])->name('vacancies.index');
        Route::post('lowongan', [JobVacancyController::class, 'store'])->name('vacancies.store');
        Route::get('lowongan/{vacancy}/kelola', [JobVacancyController::class, 'edit'])->name('vacancies.edit');
        Route::put('lowongan/{vacancy}', [JobVacancyController::class, 'update'])->name('vacancies.update');
        Route::delete('lowongan/{vacancy}', [JobVacancyController::class, 'destroy'])->name('vacancies.destroy');
        Route::patch('lowongan/{vacancy}/status', [JobVacancyStatusController::class, 'update'])->name('vacancies.status.update');
    });
