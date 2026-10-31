<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\Pelanggaran;
use App\Models\SesiUjian;
use App\Services\ExamSessionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PesertaUjianController extends Controller
{
    public function __construct(protected ExamSessionService $sessionService) {}

    /**
     * Masuk ke ruang ujian
     */
    public function masuk(Jadwal $jadwal): View|RedirectResponse
    {
        $user = Auth::user();
        $peserta = $user->peserta;

        abort_if(! $peserta || ! $jadwal->pesertas->contains($peserta->id), 403, 'Anda tidak terdaftar pada jadwal ujian ini.');

        try {
            $sesi = $this->sessionService->startOrResumeSession($jadwal, $peserta);
        } catch (Exception $e) {
            $sesi = SesiUjian::where('jadwal_id', $jadwal->id)
                ->where('peserta_id', $peserta->id)
                ->first();

            // Jika butuh izin pengawas
            return view('peserta.ujian.izin', [
                'jadwal' => $jadwal,
                'sesi' => $sesi,
                'pesan' => $e->getMessage(),
            ]);
        }

        // Ambil soal berdasarkan urutan acak yang tersimpan di sesi (rule 02-f)
        $soalIds = $sesi->urutan_soal ?? [];
        $soals = BankSoal::whereIn('id', $soalIds)->get()->sortBy(function ($soal) use ($soalIds) {
            return array_search($soal->id, $soalIds);
        })->values();

        // Ambil jawaban yang sudah tersimpan (autosave recovery)
        $jawabanMap = $sesi->jawabanPesertas->pluck('jawaban', 'bank_soal_id')->toArray();

        // Hitung sisa waktu server-side (rule 02-g & batasan jadwal)
        $totalDetik = (int) ($jadwal->durasi * 60);
        $detikTerlewat = $sesi->waktu_masuk ? (int) floor(now()->diffInSeconds($sesi->waktu_masuk)) : 0;
        $sisaDetik = (int) max(0, $totalDetik - $detikTerlewat);

        if ($jadwal->tanggal && $jadwal->jam_selesai) {
            $jadwalEnd = Carbon::parse($jadwal->tanggal->format('Y-m-d').' '.$jadwal->jam_selesai);
            if (now()->greaterThanOrEqualTo($jadwalEnd)) {
                $sisaDetik = 0;
            } else {
                $detikSampaiJadwalSelesai = (int) floor(max(0, now()->diffInSeconds($jadwalEnd)));
                $sisaDetik = (int) min($sisaDetik, $detikSampaiJadwalSelesai);
            }
        }

        return view('peserta.ujian.ruang', compact('jadwal', 'sesi', 'soals', 'jawabanMap', 'sisaDetik'));
    }

    /**
     * Autosave jawaban peserta via AJAX (rule 02-g)
     */
    public function autosave(Request $request, SesiUjian $sesi): JsonResponse
    {
        $request->validate([
            'bank_soal_id' => 'required|exists:bank_soals,id',
            'jawaban' => 'nullable|string|in:A,B,C,D,E',
        ]);

        if (in_array($sesi->status, ['selesai', 'diskualifikasi'])) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi ujian telah berakhir.',
            ], 403);
        }

        $jawaban = $this->sessionService->saveAnswer(
            $sesi,
            (int) $request->bank_soal_id,
            $request->jawaban
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan otomatis.',
            'waktu_simpan' => $jawaban->waktu_simpan?->format('H:i:s'),
        ]);
    }

    /**
     * Heartbeat klien via AJAX
     */
    public function heartbeat(SesiUjian $sesi): JsonResponse
    {
        $this->sessionService->heartbeat($sesi);

        $jadwal = $sesi->jadwal;
        $totalDetik = (int) ($jadwal->durasi * 60);
        $detikTerlewat = $sesi->waktu_masuk ? (int) floor(now()->diffInSeconds($sesi->waktu_masuk)) : 0;
        $sisaDetik = (int) max(0, $totalDetik - $detikTerlewat);

        if ($jadwal->tanggal && $jadwal->jam_selesai) {
            $jadwalEnd = Carbon::parse($jadwal->tanggal->format('Y-m-d').' '.$jadwal->jam_selesai);
            if (now()->greaterThanOrEqualTo($jadwalEnd)) {
                $sisaDetik = 0;
            } else {
                $detikSampaiJadwalSelesai = (int) floor(max(0, now()->diffInSeconds($jadwalEnd)));
                $sisaDetik = (int) min($sisaDetik, $detikSampaiJadwalSelesai);
            }
        }

        // Jika waktu server habis, selesaikan ujian
        if ($sisaDetik <= 0 && $sesi->status === 'berlangsung') {
            $this->sessionService->finishExam($sesi);

            return response()->json([
                'success' => true,
                'status' => 'selesai',
                'sisa_detik' => 0,
                'redirect' => route('peserta.ujian.proses', $jadwal->id),
            ]);
        }

        // Jika status sesi terkunci / diskualifikasi / menunggu izin
        if (in_array($sesi->status, ['diskualifikasi', 'menunggu_izin', 'terputus'])) {
            return response()->json([
                'success' => true,
                'status' => $sesi->status,
                'terkunci' => true,
                'redirect' => route('peserta.ujian.masuk', $jadwal->id),
            ]);
        }

        $latestTeguran = Pelanggaran::where('peserta_id', $sesi->peserta_id)
            ->where('jadwal_id', $sesi->jadwal_id)
            ->where('jenis_pelanggaran', 'Teguran Pengawas')
            ->where('waktu', '>=', now()->subSeconds(45))
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'status' => $sesi->status,
            'jumlah_peringatan' => $sesi->jumlah_peringatan,
            'sisa_detik' => $sisaDetik,
            'pesan_teguran' => $latestTeguran?->keterangan,
            'latest_teguran_id' => $latestTeguran?->id,
        ]);
    }

    /**
     * Deteksi aktivitas mencurigakan dari klien (pindah tab, blur, fullscreen exit) (rule 02-m)
     */
    public function laporkanPelanggaran(Request $request, SesiUjian $sesi): JsonResponse
    {
        $jenis = $request->input('jenis', 'Pindah Tab / Keluar Layar');
        $keterangan = $request->input('keterangan', 'Terdeteksi beralih dari jendela ruang ujian');

        $this->sessionService->recordViolation($sesi, $jenis, $keterangan);
        $freshSesi = $sesi->fresh();
        $isLocked = in_array($freshSesi->status, ['menunggu_izin', 'terputus', 'diskualifikasi']);

        return response()->json([
            'success' => true,
            'peringatan_count' => $freshSesi->jumlah_peringatan,
            'status' => $freshSesi->status,
            'terkunci' => $isLocked,
            'redirect' => $isLocked ? route('peserta.ujian.masuk', $sesi->jadwal_id) : null,
        ]);
    }

    /**
     * Ajukan permintaan izin masuk kembali (rule 02-e)
     */
    public function mintaIzin(Request $request, SesiUjian $sesi): RedirectResponse
    {
        if ($sesi->status === 'diskualifikasi') {
            return back()->with('error', 'Akses ditolak: Anda telah didiskualifikasi dari ujian ini dan tidak dapat mengajukan izin masuk kembali.');
        }

        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $this->sessionService->submitPermissionRequest($sesi, $request->alasan);

        return back()->with('info', 'Permintaan izin telah diajukan ke Pengawas. Mohon tunggu persetujuan.');
    }

    /**
     * Selesaikan ujian secara manual atau waktu habis (rule 02-h)
     */
    public function selesai(SesiUjian $sesi): RedirectResponse
    {
        $this->sessionService->finishExam($sesi);

        return redirect()->route('peserta.ujian.proses', $sesi->jadwal_id);
    }

    /**
     * Layar proses hasil ujian (Mockup Screen Proses Hasil Ujian)
     */
    public function proses(Jadwal $jadwal): View
    {
        $totalSoal = $jadwal->paketSoal ? $jadwal->paketSoal->bankSoals()->where('aktif', true)->count() : 100;
        if ($totalSoal === 0) {
            $totalSoal = 100;
        }

        return view('peserta.ujian.proses', compact('jadwal', 'totalSoal'));
    }
}
