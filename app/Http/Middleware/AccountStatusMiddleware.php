<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccountStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->status_akun === 'nonaktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        if ($user->role === 'peserta') {
            if ($user->status_akun === 'menunggu') {
                if (! $request->routeIs('menunggu.verifikasi') && ! $request->routeIs('logout')) {
                    return redirect()->route('menunggu.verifikasi');
                }
            } elseif ($user->status_akun === 'ditolak') {
                if (! $request->routeIs('registrasi.ditolak') && ! $request->routeIs('logout') && ! $request->routeIs('registrasi.ulang')) {
                    return redirect()->route('registrasi.ditolak');
                }
            }
        }

        return $next($request);
    }
}
