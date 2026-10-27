<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\NilaiUjian;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanitiaNilaiController extends Controller
{
    public function index(Request $request): View
    {
        $jadwals = Jadwal::orderBy('id', 'asc')->get();

        $defaultJadwal = $jadwals->firstWhere('nama_ujian', '(SKD) - Batch 1') ?? $jadwals->first();
        $selectedJadwalId = $request->has('jadwal_id')
            ? ($request->jadwal_id === 'Semua' || $request->jadwal_id === '' ? null : $request->jadwal_id)
            : ($defaultJadwal ? $defaultJadwal->id : null);

        $query = NilaiUjian::with(['peserta.user', 'peserta.pelanggarans', 'jadwal', 'sesiUjian']);

        if ($selectedJadwalId) {
            $query->where('jadwal_id', $selectedJadwalId);
        }

        if ($request->filled('status_kelulusan') && $request->status_kelulusan !== 'Semua') {
            $query->where('status_kelulusan', $request->status_kelulusan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('peserta', function ($sub) use ($search) {
                $sub->where('no_identitas', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $nilais = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();

        // Statistik ringkasan
        $statsQuery = NilaiUjian::query();
        if ($selectedJadwalId) {
            $statsQuery->where('jadwal_id', $selectedJadwalId);
        }

        $totalPeserta = (clone $statsQuery)->count();
        $rataRataNilai = (clone $statsQuery)->avg('nilai_akhir') ?? 0;
        $totalLulus = (clone $statsQuery)->where('status_kelulusan', 'LULUS')->count();
        $totalTidakLulus = (clone $statsQuery)->whereIn('status_kelulusan', ['TIDAK LULUS', 'DISKUALIFIKASI'])->count();

        return view('panitia.nilai.index', compact(
            'jadwals',
            'nilais',
            'selectedJadwalId',
            'totalPeserta',
            'rataRataNilai',
            'totalLulus',
            'totalTidakLulus'
        ));
    }

    public function detail(NilaiUjian $nilai): View
    {
        $nilai->load(['peserta.user', 'jadwal.paketSoal', 'sesiUjian.jawabanPesertas']);

        return view('panitia.nilai.detail', compact('nilai'));
    }

    public function cetak(Request $request): View
    {
        $jadwal = null;
        $query = NilaiUjian::with(['peserta.user', 'jadwal']);

        if ($request->filled('jadwal_id')) {
            $jadwal = Jadwal::find($request->jadwal_id);
            $query->where('jadwal_id', $request->jadwal_id);
        }

        $nilais = $query->orderByDesc('nilai_akhir')->get();

        return view('panitia.nilai.cetak', compact('nilais', 'jadwal'));
    }
}
