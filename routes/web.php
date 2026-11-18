<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
// Profile Pencari Kerja
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])
//         ->name('profile.edit');

//     Route::put('/profile', [ProfileController::class, 'update'])
//         ->name('profile.update');
// });

// TEMPORARY: Testing PBI-03 tanpa login
Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::put('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');