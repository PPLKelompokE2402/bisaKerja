<?php

use App\Http\Controllers\Employer\JobVacancyController;
use App\Http\Controllers\Employer\JobVacancyStatusController;
use App\Http\Controllers\ProviderController;
use App\Http\Middleware\UseDemoEmployer;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/biodata-penyedia');

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
