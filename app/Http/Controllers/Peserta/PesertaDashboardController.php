<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Notifikasi;
use App\Models\SesiUjian;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PesertaDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        // Jadwal ujian mendatang untuk peserta ini
        $jadwals = collect();
        $sesiMap = collect();

        if ($peserta) {
            $jadwals = $peserta->jadwals()
                ->with('paketSoal')
                ->whereIn('status', ['akan_datang', 'berlangsung'])
                ->orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->get();

            $sesiMap = SesiUjian::where('peserta_id', $peserta->id)
                ->whereIn('jadwal_id', $jadwals->pluck('id'))
                ->get()
                ->keyBy('jadwal_id');
        }

        // Pengumuman / Notifikasi
        $notifikasis = $user->notifikasis()->latest()->take(5)->get();

        return view('peserta.dashboard', compact('user', 'peserta', 'jadwals', 'sesiMap', 'notifikasis'));
    }
}
