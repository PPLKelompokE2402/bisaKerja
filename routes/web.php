<?php

use App\Http\Controllers\Employer\JobVacancyController;
use App\Http\Controllers\Employer\JobVacancyStatusController;
use App\Http\Middleware\UseDemoEmployer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('employer.vacancies.index');
});

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
