
<?php

use App\Models\JobVacancy;
use App\Http\Controllers\Employer\JobVacancyController;
use App\Http\Controllers\Employer\JobVacancyStatusController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Middleware\UseDemoEmployer;
use App\Http\Middleware\EnsureJobSeekerAuthenticated;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    $jobs = JobVacancy::with(['employer', 'kategori'])
        ->where('status_lowongan', 'aktif')
        ->latest('dibuat_pada')
        ->take(3)
        ->get();

    return view('welcome', compact('jobs'));
})->name('welcome');

// Daftar dan detail lowongan
Route::get('/lowongan', [JobController::class, 'index'])
    ->name('jobs.index');

Route::get('/lowongan/{id}', [JobController::class, 'show'])
    ->name('jobs.show');

// Pengajuan lamaran untuk pencari kerja
Route::middleware(['web', EnsureJobSeekerAuthenticated::class])->group(function () {
    Route::get('/lowongan/{id}/apply', [JobApplicationController::class, 'create'])
        ->name('jobs.apply');

    Route::post('/lowongan/{id}/apply', [JobApplicationController::class, 'store'])
        ->name('jobs.apply.store');
});

// Biodata penyedia pekerjaan
Route::get('/biodata-penyedia', function () {
    return view('auth.biodata-penyedia');
})->name('provider.biodata');

Route::post('/biodata-penyedia', [ProviderController::class, 'store'])
    ->name('provider.biodata.store');

// Login dan registrasi
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register/role', function () {
    return view('auth.choose-role');
})->name('register.role');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Home pengguna
Route::get('/home', function () {
    return view('home');
})->name('home');

// Halaman Admin
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/verifikasi', function () {
    return view('admin.verifikasi');
})->name('admin.verifikasi');

Route::get('/admin/pengguna', function () {
    return view('admin.pengguna');
})->name('admin.pengguna');

// Pengelolaan lowongan oleh pemberi kerja
Route::prefix('pemberi-kerja')
    ->name('employer.')
    ->middleware(UseDemoEmployer::class)
    ->group(function () {
        Route::get('lowongan', [JobVacancyController::class, 'index'])
            ->name('vacancies.index');

        Route::post('lowongan', [JobVacancyController::class, 'store'])
            ->name('vacancies.store');

        Route::get('lowongan/{vacancy}/kelola', [JobVacancyController::class, 'edit'])
            ->name('vacancies.edit');

        Route::put('lowongan/{vacancy}', [JobVacancyController::class, 'update'])
            ->name('vacancies.update');

        Route::delete('lowongan/{vacancy}', [JobVacancyController::class, 'destroy'])
            ->name('vacancies.destroy');

        Route::patch('lowongan/{vacancy}/status', [JobVacancyStatusController::class, 'update'])
            ->name('vacancies.status.update');
    });
