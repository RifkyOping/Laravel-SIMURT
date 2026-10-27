<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenungguVerifikasiController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->status_akun === 'terverifikasi' || $user->status_akun === 'aktif') {
            return redirect()->route('peserta.dashboard');
        }

        if ($user->status_akun === 'ditolak') {
            return redirect()->route('registrasi.ditolak');
        }

        return view('peserta.menunggu-verifikasi', compact('user'));
    }

    public function ditolak(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->status_akun === 'terverifikasi') {
            return redirect()->route('peserta.dashboard');
        }

        return view('peserta.registrasi-ditolak', compact('user'));
    }

    public function registrasiUlang(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Peserta berstatus Ditolak boleh registrasi ulang (rule 02-a)
        $request->validate([
            'name' => 'required|string|max:100',
            'no_identitas' => 'required|string|max:30',
            'instansi' => 'nullable|string|max:100',
        ]);

        $user->update([
            'name' => $request->name,
            'status_akun' => 'menunggu',
        ]);

        $user->peserta()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'no_identitas' => $request->no_identitas,
                'instansi' => $request->instansi,
            ]
        );

        return redirect()->route('menunggu.verifikasi')->with('success', 'Pendaftaran ulang berhasil diajukan. Mohon menunggu verifikasi panitia.');
    }
}
