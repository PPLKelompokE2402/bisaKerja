
<?php
use App\Http\Controllers\Employer\JobVacancyController;
use App\Http\Controllers\Employer\JobVacancyStatusController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProviderController;
use App\Http\Middleware\EnsureJobSeekerAuthenticated;
use App\Models\JobVacancy;
use App\Models\KategoriPekerjaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

Route::post('/auth/session', function (Request $request) {
    $validated = $request->validate([
        'token' => ['required', 'string'],
    ]);

    $accessToken = PersonalAccessToken::findToken($validated['token']);

    if (
        ! $accessToken ||
        ! ($accessToken->tokenable instanceof User)
    ) {
        return response()->json([
            'message' => 'Token autentikasi tidak valid.',
        ], 401);
    }

    $user = $accessToken->tokenable;

    if ($user->status_akun === 'ditangguhkan') {
        return response()->json([
            'message' => 'Akun Anda sedang ditangguhkan.',
        ], 403);
    }

    Auth::guard('web')->login($user);

    $request->session()->regenerate();

    return response()->json([
        'message' => 'Session berhasil dibuat.',
    ]);
});

Route::view('/lamaran-saya', 'seeker.applications-entry')->name('seeker.applications');

// Landing page

Route::get('/', function () {
    $jobs = JobVacancy::with(['employer', 'kategori'])
        ->where('status_lowongan', 'aktif')
        ->whereHas('employer', function ($employers) {
            $employers->where('status_akun', 'aktif')
                ->where('role', 'penyedia_pekerjaan');
        })
        ->latest('dibuat_pada')
        ->take(3)
        ->get();
    $categories = KategoriPekerjaan::where('status_kategori', 'aktif')
        ->orderBy('nama_kategori')
        ->get();

    return view('jobs.welcome', compact('jobs', 'categories'));
})->name('welcome');

// Daftar dan detail lowongan
Route::get('/lowongan', [JobController::class, 'index'])
    ->name('jobs.index');

Route::get('/lowongan/{id}', [JobController::class, 'show'])
    ->name('jobs.show');

// Pengajuan lamaran untuk pencari kerja
Route::middleware(['web', EnsureJobSeekerAuthenticated::class])->group(function () {
    Route::get('/lowongan/{id}/apply', [JobApplicationController::class, 'create'])
        ->name('jobs.apply');

    Route::post('/lowongan/{id}/apply', [JobApplicationController::class, 'store'])
        ->name('jobs.apply.store');
});

// Biodata penyedia pekerjaan
Route::middleware(['auth', 'role:penyedia_pekerjaan'])->group(function () {
    Route::get('/biodata-penyedia', [ProviderController::class, 'profile'])
        ->name('provider.biodata');

    Route::get('/biodata-penyedia/edit', [ProviderController::class, 'edit'])
        ->name('provider.profile.edit');

    Route::put('/biodata-penyedia', [ProviderController::class, 'store'])
        ->name('provider.profile.update');
});

Route::post('/biodata-penyedia', [ProviderController::class, 'store'])
    ->name('provider.biodata.store');

// Login dan registrasi
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register/role', function () {
    return view('auth.choose-role');
})->name('register.role');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Home pengguna
Route::get('/home', function () {
    return redirect()->route('employer.vacancies.index');
})->name('home');

Route::get('/register/provider/biodata', [ProviderController::class, 'create'])->name('provider.create');
Route::post('/register/provider/biodata', [ProviderController::class, 'store'])->name('provider.store');

// Halaman Admin
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::get('/verifikasi', function () {
            return view('admin.verifikasi');
        })->name('verifikasi');

        Route::get('/pengguna', function () {
            return view('admin.pengguna');
        })->name('pengguna');
    });

// Pengelolaan lowongan oleh pemberi kerja
Route::prefix('pemberi-kerja')
    ->name('employer.')
    ->middleware('auth')
    ->group(function () {
        Route::get('lowongan', [JobVacancyController::class, 'index'])
            ->name('vacancies.index');

        Route::post('lowongan', [JobVacancyController::class, 'store'])
            ->name('vacancies.store');

        Route::get('lowongan/{vacancy}/kelola', [JobVacancyController::class, 'edit'])
            ->name('vacancies.edit');

        Route::put('lowongan/{vacancy}', [JobVacancyController::class, 'update'])
            ->name('vacancies.update');

        Route::delete('lowongan/{vacancy}', [JobVacancyController::class, 'destroy'])
            ->name('vacancies.destroy');

        Route::patch('lowongan/{vacancy}/status', [JobVacancyStatusController::class, 'update'])
            ->name('vacancies.status.update');
    });
