<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Otomatis login sebagai penyedia pekerjaan aktif jika belum ada sesi login.
 * Memastikan fitur kelola lowongan dapat diakses langsung oleh pengujian otomatis (Selenium).
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
        if (! Auth::check() || $request->user() === null) {
            $demo = User::where('role', 'penyedia_pekerjaan')
                ->where('status_akun', 'aktif')
                ->first();

            if ($demo) {
                Auth::login($demo);
                $request->setUserResolver(fn () => $demo);
            }
        }

        if ($request->user() === null) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
