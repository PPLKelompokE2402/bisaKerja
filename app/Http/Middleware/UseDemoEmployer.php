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
            abort(401, 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
