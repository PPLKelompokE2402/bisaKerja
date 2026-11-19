<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJobSeekerAuthenticated
{
    /**
     * Require an authenticated, non-suspended job seeker.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        if ($user->role !== 'pencari_kerja' || $user->status_akun === 'ditangguhkan') {
            abort(403);
        }

        return $next($request);
    }
}
