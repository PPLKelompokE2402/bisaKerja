<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProviderController;

Route::redirect('/', '/biodata-penyedia');

Route::get('/biodata-penyedia', function () {
    return view('auth.biodata-penyedia');
})->name('provider.biodata');

Route::post('/biodata-penyedia', [ProviderController::class, 'store'])
    ->name('provider.biodata.store');
Route::get('/', function () {
    return view('welcome');
});
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


Route::get('/register/provider/biodata', [ProviderController::class, 'create'])->name('provider.create');
Route::post('/register/provider/biodata', [ProviderController::class, 'store'])->name('provider.store');