<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\SesiUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PesertaJadwalController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        $filter = $request->get('tab', 'semua');

        $query = $peserta ? $peserta->jadwals()->with('paketSoal') : Jadwal::whereRaw('1=0');

        if ($filter === 'akan_datang') {
            $query->where('status', 'akan_datang');
        } elseif ($filter === 'berlangsung') {
            $query->where('status', 'berlangsung');
        } elseif ($filter === 'selesai') {
            $query->where('status', 'selesai');
        }

        $jadwals = $query->orderByRaw("CASE WHEN status = 'berlangsung' THEN 1 WHEN status = 'akan_datang' THEN 2 ELSE 3 END")
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $sesiMap = $peserta ? SesiUjian::where('peserta_id', $peserta->id)
            ->whereIn('jadwal_id', $jadwals->pluck('id'))
            ->get()
            ->keyBy('jadwal_id') : collect();

        return view('peserta.jadwal.index', compact('jadwals', 'filter', 'sesiMap'));
    }

    public function konfirmasi(Jadwal $jadwal): View
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        abort_if(! $peserta || ! $jadwal->pesertas->contains($peserta->id), 403, 'Anda tidak terdaftar pada ujian ini.');

        $sesi = SesiUjian::where('jadwal_id', $jadwal->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        $totalSoal = $jadwal->paketSoal->bankSoals()->where('aktif', true)->count();

        return view('peserta.jadwal.konfirmasi', compact('jadwal', 'sesi', 'totalSoal'));
    }
}
