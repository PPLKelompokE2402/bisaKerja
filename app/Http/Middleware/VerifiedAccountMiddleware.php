<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'Silakan login terlebih dahulu.',
            ], 401);
        }
        if ($user->status_akun !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda belum aktif. Tunggu verifikasi Admin.',
                'status_akun' => $user->status_akun,
            ], 403);
        }
        return $next($request);
    }
}
