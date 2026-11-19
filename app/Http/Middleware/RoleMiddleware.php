<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // /**
    //  * Handle an incoming request.
    //  *
    //  * @param  Closure(Request): (Response)  $next
    //  */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $expectsJson = $request->expectsJson() || $request->is('api/*');

        if (! $user) {
            if ($expectsJson) {
                return response()->json([
                    'message' => 'Silakan login terlebih dahulu.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        if (! in_array($user->role, $roles, true)) {
            if ($expectsJson) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
                ], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
