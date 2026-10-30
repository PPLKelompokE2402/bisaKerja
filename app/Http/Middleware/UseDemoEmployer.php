<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Temporary stand-in for authentication until Login & Registrasi (SCRUM-51) is merged.
 *
 * When nobody is signed in, the first seeded employer is used for the current request only.
 * Replace this middleware with `auth` once the login feature is available.
 */
class UseDemoEmployer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() === null) {
            $demoEmployer = User::query()
                ->where('role', UserRole::Employer)
                ->oldest('id')
                ->first();

            abort_if($demoEmployer === null, 503, 'Belum ada akun pemberi kerja. Jalankan: php artisan db:seed');

            Auth::setUser($demoEmployer);
        }

        return $next($request);
    }
}
