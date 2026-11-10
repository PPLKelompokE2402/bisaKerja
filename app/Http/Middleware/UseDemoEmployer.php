<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Temporary stand-in for authentication until Login & Registrasi (SCRUM-51) is merged.
 *
 * Automatically signs in as the demo employer ('penyedia_pekerjaan', e.g. Hikmal Alakbar)
 * from the database if no user is currently authenticated.
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
                ->where('role', 'penyedia_pekerjaan')
                ->oldest('id_pengguna')
                ->first();

            abort_if($demoEmployer === null, 503, 'Belum ada akun penyedia pekerjaan di tabel pengguna. Pastikan database bisakerjaupdatedb sudah di-import.');

            Auth::setUser($demoEmployer);
        }

        return $next($request);
    }
}
