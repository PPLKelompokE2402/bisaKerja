<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminVerificationController;
use App\Http\Controllers\ProviderController;


Route::get('/', function () {
    return view('auth.welcome');
});



Route::post('/register/job-seeker', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

// Route::middleware('verified.account')->get('/test-verified', function () {
//     return response()->json([
//         'message' => 'Akun sudah terverifikasi dan boleh mengakses fitur ini.'
//     ]);
// });

Route::post('/provider/profile', [ProviderController::class, 'store']);

Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/pending-users', [
            AdminVerificationController::class,
            'pendingUsers',
        ]);

        Route::patch('/users/{id}/verify', [
            AdminVerificationController::class,
            'verifyUser',
        ]);

        Route::get('/users', [
            AdminVerificationController::class,
            'allUsers',
        ]);
        Route::patch('/users/{id}/status', [
            AdminVerificationController::class,
            'updateUserStatus',
        ]);
        Route::get('/users/{id}', [
        AdminVerificationController::class,
        'showUser',
        ]);
    });
});

