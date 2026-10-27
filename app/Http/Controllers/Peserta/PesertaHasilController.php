<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\NilaiUjian;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PesertaHasilController extends Controller
{
    public function show(Jadwal $jadwal): View
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        abort_if(! $peserta, 403);

        $nilai = NilaiUjian::with('jadwal.paketSoal')
            ->where('peserta_id', $peserta->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();

        // Riwayat hasil ujian lainnya
        $riwayatLain = NilaiUjian::with('jadwal.paketSoal')
            ->where('peserta_id', $peserta->id)
            ->where('jadwal_id', '!=', $jadwal->id)
            ->get()
            ->sortByDesc(fn ($n) => $n->jadwal ? $n->jadwal->tanggal->format('Y-m-d') : '1970-01-01')
            ->values();

        $featuredNilai = $nilai;
        $totalSoal = $jadwal->paketSoal ? $jadwal->paketSoal->bankSoals()->where('aktif', true)->count() : 100;
        if ($totalSoal === 0) {
            $totalSoal = 100;
        }

        return view('peserta.hasil.show', compact('jadwal', 'nilai', 'featuredNilai', 'riwayatLain', 'totalSoal'));
    }

    public function index(): View
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        $allNilai = $peserta ? NilaiUjian::with(['jadwal.paketSoal'])
            ->where('peserta_id', $peserta->id)
            ->get()
            ->sortByDesc(fn ($n) => $n->jadwal ? $n->jadwal->tanggal->format('Y-m-d') : '1970-01-01')
            ->values() : collect();

        $featuredNilai = $allNilai->first();
        $riwayatLain = $allNilai->slice(1)->values();

        $jadwal = $featuredNilai ? $featuredNilai->jadwal : null;
        $nilai = $featuredNilai;
        $totalSoal = $jadwal && $jadwal->paketSoal ? $jadwal->paketSoal->bankSoals()->where('aktif', true)->count() : 100;
        if ($totalSoal === 0) {
            $totalSoal = 100;
        }

        return view('peserta.hasil.index', compact('allNilai', 'featuredNilai', 'riwayatLain', 'jadwal', 'nilai', 'totalSoal'));
    }
}
