<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\View\View;

class PanitiaDashboardController extends Controller
{
    public function index(): View
    {
        $totalPeserta = User::where('role', 'peserta')->count();
        $ujianAktif = Jadwal::where('status', 'berlangsung')->count();
        $soalTersedia = BankSoal::where('aktif', true)->count();
        $pesertaMenunggu = User::where('role', 'peserta')->where('status_akun', 'menunggu')->count();

        // Peserta menunggu verifikasi (tabel verifikasi cepat di dashboard)
        $antreanVerifikasi = User::where('role', 'peserta')
            ->where('status_akun', 'menunggu')
            ->with('peserta')
            ->latest()
            ->take(5)
            ->get();

        // Jadwal ujian mendatang
        $jadwalMendatang = Jadwal::with('paketSoal')
            ->withCount('pesertas')
            ->whereIn('status', ['akan_datang', 'berlangsung'])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->take(5)
            ->get();

        return view('panitia.dashboard', compact(
            'totalPeserta',
            'ujianAktif',
            'soalTersedia',
            'pesertaMenunggu',
            'antreanVerifikasi',
            'jadwalMendatang'
        ));
    }
}
