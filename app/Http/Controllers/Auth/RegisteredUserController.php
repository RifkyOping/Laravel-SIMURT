<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi peserta (rule 02-a & 04)
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun peserta baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:'.User::class],
            'no_identitas' => ['required', 'string', 'max:30'],
            'instansi' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => User::generateUniqueUsername($request->email),
                'password' => Hash::make($request->password),
                'role' => 'peserta',
                'status_akun' => 'menunggu', // Rule 02-a: Status awal Menunggu Verifikasi
            ]);

            Peserta::create([
                'user_id' => $user->id,
                'no_identitas' => $request->no_identitas,
                'instansi' => $request->instansi,
            ]);

            ActivityLogger::log('Registrasi Akun', "Pendaftaran peserta baru: {$user->name}", null, $user);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('menunggu.verifikasi');
    }
}
