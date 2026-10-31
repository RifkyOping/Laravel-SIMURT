<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pelanggaran;
use App\Models\SesiUjian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengawasDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Ambil seluruh jadwal yang diawasi oleh pengawas ini
        $assignedJadwals = Jadwal::with(['paketSoal', 'pesertas'])
            ->withCount('pesertas')
            ->whereHas('pengawas', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->latest('tanggal')
            ->get();

        if ($assignedJadwals->isEmpty()) {
            $assignedJadwals = Jadwal::with(['paketSoal', 'pesertas'])
                ->withCount('pesertas')
                ->latest('tanggal')
                ->take(5)
                ->get();
        }

        // 2. Tentukan jadwal ujian aktif yang dipantau di layar
        $jadwalAktif = null;
        if ($request->filled('jadwal_id')) {
            $jadwalAktif = Jadwal::with(['paketSoal.bankSoals'])->find($request->jadwal_id);
        }

        if (! $jadwalAktif) {
            $jadwalAktif = $assignedJadwals->firstWhere('status', 'berlangsung')
                ?? Jadwal::with(['paketSoal.bankSoals'])->where('status', 'berlangsung')->first()
                ?? $assignedJadwals->first();
        }

        $ringkasan = [
            'aktif' => 0,
            'peringatan' => 0,
            'terputus' => 0,
            'total' => 0,
        ];

        $pesertaList = collect();
        $totalSoal = 50;
        $sisaDetik = 0;
        $sisaWaktuText = '00:00:00';

        if ($jadwalAktif) {
            $dbTotalSoal = $jadwalAktif->paketSoal?->bankSoals()->where('aktif', true)->count();
            if ($dbTotalSoal && $dbTotalSoal > 0) {
                $totalSoal = $dbTotalSoal;
            }

            // Hitung sisa waktu jika jadwal memiliki durasi & jam_selesai
            if ($jadwalAktif->status === 'berlangsung' && $jadwalAktif->tanggal && $jadwalAktif->jam_selesai) {
                $jadwalEnd = Carbon::parse($jadwalAktif->tanggal->format('Y-m-d').' '.$jadwalAktif->jam_selesai);
                if (now()->lessThan($jadwalEnd)) {
                    $sisaDetik = (int) floor(max(0, now()->diffInSeconds($jadwalEnd)));
                } else {
                    $sisaDetik = 0;
                }
            } elseif ($jadwalAktif->durasi) {
                $sisaDetik = (int) ($jadwalAktif->durasi * 60);
            }

            $hours = (int) floor($sisaDetik / 3600);
            $mins = (int) floor(($sisaDetik % 3600) / 60);
            $secs = (int) ($sisaDetik % 60);
            $sisaWaktuText = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            $sesiList = SesiUjian::where('jadwal_id', $jadwalAktif->id)
                ->with(['jawabanPesertas', 'permintaanIzins'])
                ->get()
                ->keyBy('peserta_id');

            $dbAktif = $sesiList->where('status', 'berlangsung')->where('jumlah_peringatan', 0)->count();
            $dbPeringatan = $sesiList->where('jumlah_peringatan', '>', 0)->where('status', '!=', 'diskualifikasi')->count();
            $dbTerputus = $sesiList->whereIn('status', ['terputus', 'menunggu_izin'])->count();

            $pesertas = $jadwalAktif->pesertas()->with('user')->get();

            $ringkasan = [
                'aktif' => $dbAktif,
                'peringatan' => $dbPeringatan,
                'terputus' => $dbTerputus,
                'total' => $pesertas->count(),
            ];

            $now = now();
            $pesertaList = $pesertas->sortBy(function ($p) use ($sesiList) {
                $sesi = $sesiList->get($p->id);
                if ($sesi && $sesi->jumlah_peringatan > 0) {
                    return 1;
                }
                if ($sesi && ($sesi->status === 'menunggu_izin' || $sesi->permintaanIzins->where('status', 'menunggu')->isNotEmpty())) {
                    return 2;
                }
                if ($sesi && in_array($sesi->status, ['terputus'])) {
                    return 3;
                }
                if ($sesi && $sesi->status === 'berlangsung') {
                    return 4;
                }

                return 10;
            })->values()->map(function ($peserta, $index) use ($sesiList, $totalSoal, $now) {
                $sesi = $sesiList->get($peserta->id);
                $name = $peserta->user->name ?? 'Peserta';
                $noUjian = $peserta->no_ujian ?? '-';
                $statusText = 'Belum Mulai';
                $progresSoal = "0/{$totalSoal} Soal";
                $terakhirAktifText = '-';
                $sesiId = $sesi?->id;
                $hasPendingPermission = false;
                $alasanIzin = null;

                if ($sesi) {
                    $answeredCount = $sesi->jawabanPesertas->whereNotNull('jawaban')->count();
                    $progresSoal = "{$answeredCount}/{$totalSoal} Soal";

                    $pendingIzin = $sesi->permintaanIzins->where('status', 'menunggu')->sortByDesc('created_at')->first();
                    $hasPendingPermission = ($sesi->status === 'menunggu_izin') || ($pendingIzin !== null);
                    $alasanIzin = $pendingIzin?->alasan;

                    if ($sesi->status === 'diskualifikasi') {
                        $statusText = 'Diskualifikasi';
                    } elseif ($hasPendingPermission) {
                        $statusText = 'Menunggu Izin';
                    } elseif ($sesi->jumlah_peringatan > 0) {
                        $statusText = 'Peringatan';
                    } elseif ($sesi->status === 'terputus') {
                        $statusText = 'Terputus';
                    } elseif ($sesi->status === 'selesai') {
                        $statusText = 'Selesai';
                    } else {
                        $statusText = 'Aktif';
                    }

                    if ($sesi->last_heartbeat) {
                        $diffSeconds = $now->diffInSeconds(Carbon::parse($sesi->last_heartbeat));
                        if ($diffSeconds < 10) {
                            $terakhirAktifText = 'Baru saja';
                        } elseif ($diffSeconds < 60) {
                            $terakhirAktifText = "{$diffSeconds} detik yang lalu";
                        } elseif ($diffSeconds < 3600) {
                            $min = (int) floor($diffSeconds / 60);
                            $terakhirAktifText = "{$min} menit yang lalu";
                        } else {
                            $terakhirAktifText = Carbon::parse($sesi->last_heartbeat)->diffForHumans();
                        }
                    }
                }

                return (object) [
                    'no' => $index + 1,
                    'peserta_id' => $peserta->id,
                    'sesi_id' => $sesiId,
                    'nama' => $name,
                    'no_ujian' => $noUjian,
                    'status' => $statusText,
                    'progres' => $progresSoal,
                    'terakhir_aktif' => $terakhirAktifText,
                    'has_pending_permission' => $hasPendingPermission,
                    'alasan_izin' => $alasanIzin,
                ];
            });
        }

        // 2. Peringatan keamanan sistem terbaru
        $latestPelanggaran = Pelanggaran::with(['peserta.user', 'jadwal'])
            ->latest('waktu')
            ->first();

        return view('pengawas.dashboard', compact(
            'assignedJadwals',
            'jadwalAktif',
            'ringkasan',
            'pesertaList',
            'latestPelanggaran',
            'totalSoal',
            'sisaDetik',
            'sisaWaktuText'
        ));
    }
}
