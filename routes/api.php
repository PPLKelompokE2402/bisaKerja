<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminVerificationController;


Route::get('/', function () {
    return view('auth.welcome');
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



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

