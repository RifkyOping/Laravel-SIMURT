<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanitiaJadwalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jadwal::with('paketSoal')->withCount(['pesertas', 'pengawas']);

        if ($request->filled('search')) {
            $query->where('nama_ujian', 'like', "%{$request->search}%");
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        $jadwals = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();
        $paketSoals = PaketSoal::all();
        $pesertas = Peserta::with('user')->whereHas('user', fn ($q) => $q->where('status_akun', 'terverifikasi'))->get();
        $pengawas = User::where('role', 'pengawas')->where('status_akun', 'aktif')->get();

        return view('panitia.jadwal.index', compact('jadwals', 'paketSoals', 'pesertas', 'pengawas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'nama_ujian' => 'required|string|max:150',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'durasi' => 'required|integer|min:1',
            'status' => 'required|in:akan_datang,berlangsung,selesai',
            'passing_grade' => 'nullable|numeric|min:0|max:500',
            'peserta_mode' => 'nullable|string|in:semua,pilih',
            'peserta_ids' => 'nullable|array',
            'pengawas_ids' => 'nullable|array',
        ]);

        $paket = PaketSoal::findOrFail($request->paket_soal_id);

        $jadwal = Jadwal::create([
            'paket_soal_id' => $paket->id,
            'nama_ujian' => $request->nama_ujian,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'durasi' => $request->durasi,
            'passing_grade' => $request->passing_grade ?? $paket->passing_grade,
            'status' => $request->status,
        ]);

        if ($request->input('peserta_mode') === 'semua') {
            $allPesertaIds = Peserta::whereHas('user', fn ($q) => $q->where('status_akun', 'terverifikasi'))->pluck('id')->toArray();
            $jadwal->pesertas()->sync($allPesertaIds);
        } elseif ($request->has('peserta_ids')) {
            $jadwal->pesertas()->sync($request->peserta_ids);
        }

        if ($request->has('pengawas_ids')) {
            $jadwal->pengawas()->sync($request->pengawas_ids);
        }

        ActivityLogger::log('Tambah Jadwal', "Jadwal ujian dibuat: {$jadwal->nama_ujian}", $jadwal->id);

        return back()->with('success', "Jadwal ujian {$jadwal->nama_ujian} berhasil ditambahkan.");
    }

    public function edit(Jadwal $jadwal): View
    {
        $jadwal->load(['pesertas', 'pengawas']);
        $paketSoals = PaketSoal::all();
        $pesertas = Peserta::with('user')->whereHas('user', fn ($q) => $q->where('status_akun', 'terverifikasi'))->get();
        $pengawas = User::where('role', 'pengawas')->where('status_akun', 'aktif')->get();

        return view('panitia.jadwal.edit', compact('jadwal', 'paketSoals', 'pesertas', 'pengawas'));
    }

    public function update(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $request->validate([
            'paket_soal_id' => 'sometimes|required|exists:paket_soals,id',
            'nama_ujian' => 'sometimes|required|string|max:150',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'durasi' => 'required|integer|min:1',
            'status' => 'sometimes|required|in:akan_datang,berlangsung,selesai',
            'passing_grade' => 'nullable|numeric|min:0|max:500',
            'peserta_mode' => 'nullable|string|in:semua,pilih',
            'peserta_ids' => 'nullable|array',
            'pengawas_ids' => 'nullable|array',
        ]);

        $jadwal->update([
            'paket_soal_id' => $request->paket_soal_id ?? $jadwal->paket_soal_id,
            'nama_ujian' => $request->nama_ujian ?? $jadwal->nama_ujian,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'durasi' => $request->durasi,
            'passing_grade' => $request->filled('passing_grade') ? $request->passing_grade : $jadwal->passing_grade,
            'status' => $request->status ?? $jadwal->status,
        ]);

        if ($request->input('peserta_mode') === 'semua') {
            $allPesertaIds = Peserta::whereHas('user', fn ($q) => $q->where('status_akun', 'terverifikasi'))->pluck('id')->toArray();
            $jadwal->pesertas()->sync($allPesertaIds);
        } elseif ($request->has('peserta_ids')) {
            $jadwal->pesertas()->sync($request->peserta_ids);
        }

        if ($request->has('pengawas_ids')) {
            $jadwal->pengawas()->sync($request->pengawas_ids);
        }

        ActivityLogger::log('Edit Jadwal', "Jadwal ujian diperbarui: {$jadwal->nama_ujian}", $jadwal->id);

        return redirect()->route('panitia.jadwal.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $name = $jadwal->nama_ujian;
        $jadwal->delete();

        ActivityLogger::log('Hapus Jadwal', "Jadwal ujian dihapus: {$name}");

        return redirect()->route('panitia.jadwal.index')->with('success', "Jadwal ujian {$name} berhasil dihapus.");
    }
}
