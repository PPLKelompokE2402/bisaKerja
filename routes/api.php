<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminVerificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

Route::middleware('role:admin')->prefix('admin')->group(function () {
    Route::get('/pending-users', [
        AdminVerificationController::class,
        'pendingUsers',
    ]);

    Route::patch('/users/{id}/verify', [
        AdminVerificationController::class,
        'verifyUser',
    ]);
});
});

