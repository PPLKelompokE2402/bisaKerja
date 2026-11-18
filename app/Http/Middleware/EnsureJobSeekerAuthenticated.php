<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memastikan terdapat akun pencari kerja yang aktif saat melamar pekerjaan.
 * Menjamin pengujian browser tetap berjalan lancar sebelum modul Login (SCRUM-51) di-merge ke main.
 */
class EnsureJobSeekerAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $seeker = User::firstOrCreate(
                ['email' => 'pencari.kerja@bisakerja.com'],
                ['name' => 'Budi Pencari Kerja', 'password' => bcrypt('password123')]
            );

            Auth::login($seeker);
            $request->setUserResolver(fn () => $seeker);
        }

        return $next($request);
    }
}
