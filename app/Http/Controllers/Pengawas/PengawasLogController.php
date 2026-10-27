<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengawasLogController extends Controller
{
    public function index(Request $request): View
    {
        $jadwals = Jadwal::all();

        $selectedJadwal = null;
        if ($request->filled('jadwal_id')) {
            $selectedJadwal = Jadwal::find($request->jadwal_id);
        } else {
            $selectedJadwal = Jadwal::where('nama_ujian', 'like', '%SKD%')->first() ?? $jadwals->first();
        }

        $query = Pelanggaran::with(['peserta.user', 'jadwal', 'user']);

        if ($request->filled('jadwal_id')) {
            $query->where('jadwal_id', $request->jadwal_id);
        } elseif ($selectedJadwal) {
            $query->where('jadwal_id', $selectedJadwal->id);
        }

        if ($request->filled('jenis_aktivitas') && $request->jenis_aktivitas !== 'Semua') {
            $query->where('jenis_pelanggaran', $request->jenis_aktivitas);
        }

        // Tanggal: default ke 2024-10-15 sesuai mockup atau tanggal yang dipilih
        $selectedDate = $request->input('tanggal', '2024-10-15');
        if (! empty($selectedDate)) {
            $query->whereDate('waktu', $selectedDate);
        }

        $logs = $query->orderByDesc('waktu')->paginate(10)->withQueryString();

        return view('pengawas.log.index', compact('jadwals', 'selectedJadwal', 'logs', 'selectedDate'));
    }
}
