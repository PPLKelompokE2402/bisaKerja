<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');
Route::get('/admin/verifikasi', function () {
    return view('admin.verifikasi');
})->name('admin.verifikasi');
Route::get('/admin/pengguna', function () {
    return view('admin.pengguna');
})->name('admin.pengguna');