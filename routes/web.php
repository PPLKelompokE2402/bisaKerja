<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProviderController;

Route::redirect('/', '/biodata-penyedia');

Route::get('/biodata-penyedia', function () {
    return view('auth.biodata-penyedia');
})->name('provider.biodata');

Route::post('/biodata-penyedia', [ProviderController::class, 'store'])
    ->name('provider.biodata.store');