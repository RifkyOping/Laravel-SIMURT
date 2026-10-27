<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->status_akun === 'nonaktif') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Panitia atau Admin.',
            ]);
        }

        $user->update(['terakhir_login' => now()]);

        $request->session()->regenerate();

        ActivityLogger::log('Login Berhasil', "Login pengguna {$user->name} ({$user->role})", null, $user);

        // Redirect sesuai role dan status akun (rule 01 & 02)
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isPanitia()) {
            return redirect()->intended(route('panitia.dashboard'));
        }

        if ($user->isPengawas()) {
            return redirect()->intended(route('pengawas.dashboard'));
        }

        // Peserta
        if ($user->status_akun === 'menunggu') {
            return redirect()->route('menunggu.verifikasi');
        }

        if ($user->status_akun === 'ditolak') {
            return redirect()->route('registrasi.ditolak');
        }

        return redirect()->intended(route('peserta.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::log('Logout', "Pengguna {$user->name} keluar dari sistem", null, $user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
