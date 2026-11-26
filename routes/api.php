
<?php

use App\Http\Controllers\Api\AdminVerificationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderApplicantController;
use App\Http\Controllers\Api\SeekerApplicationController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Monitoring lamaran pencari kerja
Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum', 'role:pencari_kerja', 'cache.headers:private;no_store'])
    ->prefix('pencari/lamaran-saya')
    ->name('seeker.applications.')
    ->group(function () {
        Route::get('/halaman', [SeekerApplicationController::class, 'page'])->name('page');
        Route::get('/', [SeekerApplicationController::class, 'index'])->name('index');
    });

// Monitoring pelamar penyedia pekerjaan
Route::middleware(['auth:sanctum', 'role:penyedia_pekerjaan', 'cache.headers:private;no_store'])
    ->prefix('penyedia/pelamar-saya')
    ->name('provider.applicants.')
    ->group(function () {
        Route::get('/halaman', [ProviderApplicantController::class, 'page'])->name('page');
        Route::get('/', [ProviderApplicantController::class, 'index'])->name('index');
        Route::get('/{lamaran}', [ProviderApplicantController::class, 'show'])
            ->whereNumber('lamaran')->name('show');
        Route::get('/{lamaran}/berkas/{type}', [ProviderApplicantController::class, 'file'])
            ->whereNumber('lamaran')
            ->whereIn('type', ['cv', 'foto'])
            ->name('file');
    });

// Halaman utama API
Route::get('/', function () {
    return view('auth.welcome');
});

// Registrasi dan login
Route::post('/register/job-seeker', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route penyedia pekerjaan
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    Route::post('/provider/profile', [ProviderController::class, 'store']);

    // Route admin
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
