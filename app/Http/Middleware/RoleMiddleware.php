<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login')->withErrors('You must be logged in to access this page.');
        }

        // Cek apakah level user yang sedang login termasuk dalam level yang diizinkan
        if (!in_array(Auth::user()->role, $roles)) {
            return redirect('/error_page')->withErrors('You do not have access to this section.');
        }

        return $next($request);
    }
}
