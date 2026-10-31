<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pelanggaran;
use App\Models\PermintaanIzin;
use App\Models\SesiUjian;
use App\Services\ActivityLogger;
use App\Services\ScoringService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PengawasMonitoringController extends Controller
{
    public function __construct(protected ScoringService $scoringService) {}

    public function index(Request $request): View|RedirectResponse
    {
        if ($request->filled('jadwal_id')) {
            return redirect()->route('pengawas.monitoring.show', $request->jadwal_id);
        }

        $user = Auth::user();

        // 1. Query dasar jadwal yang diawasi oleh pengawas ini
        $baseQuery = Jadwal::with(['paketSoal'])
            ->withCount('pesertas')
            ->whereHas('pengawas', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });

        // Fallback jika belum ada penugasan spesifik
        if ($baseQuery->count() === 0) {
            $baseQuery = Jadwal::with(['paketSoal'])
                ->withCount('pesertas');
        }

        // 2. Hitung jumlah item untuk setiap tab filter
        $allJadwals = (clone $baseQuery)->get();
        $counts = [
            'semua' => $allJadwals->count(),
            'berlangsung' => $allJadwals->where('status', 'berlangsung')->count(),
            'akan_datang' => $allJadwals->where('status', 'akan_datang')->count(),
            'selesai' => $allJadwals->where('status', 'selesai')->count(),
        ];

        // 3. Terapkan filter tab status
        $statusFilter = $request->input('status', 'semua');
        if (in_array($statusFilter, ['berlangsung', 'akan_datang', 'selesai'])) {
            $baseQuery->where('status', $statusFilter);
        }

        // 4. Terapkan filter pencarian nama ujian / paket soal
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $baseQuery->where(function ($q) use ($searchQuery) {
                $q->where('nama_ujian', 'like', "%{$searchQuery}%")
                    ->orWhereHas('paketSoal', function ($sub) use ($searchQuery) {
                        $sub->where('nama_paket', 'like', "%{$searchQuery}%");
                    });
            });
        }

        $jadwals = $baseQuery->latest('tanggal')->paginate(10)->withQueryString();

        return view('pengawas.monitoring.index', compact('jadwals', 'counts', 'statusFilter', 'searchQuery'));
    }

    /**
     * Tampilan detail monitoring real-time setelah pengawas klik "Masuk" pada salah satu ujian
     */
    public function show(Jadwal $jadwal): View
    {
        $selectedJadwal = $jadwal->load(['paketSoal.bankSoals']);
        $user = Auth::user();

        // Ambil semua jadwal yang diawasi untuk dropdown ganti ruangan
        $allJadwals = Jadwal::whereHas('pengawas', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->get();

        if ($allJadwals->isEmpty()) {
            $allJadwals = Jadwal::all();
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

        if ($selectedJadwal) {
            $dbTotalSoal = $selectedJadwal->paketSoal?->bankSoals()->where('aktif', true)->count();
            if ($dbTotalSoal && $dbTotalSoal > 0) {
                $totalSoal = $dbTotalSoal;
            }

            if ($selectedJadwal->status === 'berlangsung' && $selectedJadwal->tanggal && $selectedJadwal->jam_selesai) {
                $jadwalEnd = Carbon::parse($selectedJadwal->tanggal->format('Y-m-d').' '.$selectedJadwal->jam_selesai);
                if (now()->lessThan($jadwalEnd)) {
                    $sisaDetik = (int) floor(max(0, now()->diffInSeconds($jadwalEnd)));
                } else {
                    $sisaDetik = 0;
                }
            } elseif ($selectedJadwal->durasi) {
                $sisaDetik = (int) ($selectedJadwal->durasi * 60);
            }

            $hours = (int) floor($sisaDetik / 3600);
            $mins = (int) floor(($sisaDetik % 3600) / 60);
            $secs = (int) ($sisaDetik % 60);
            $sisaWaktuText = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            $sesiList = SesiUjian::where('jadwal_id', $selectedJadwal->id)
                ->with(['jawabanPesertas', 'permintaanIzins'])
                ->get()
                ->keyBy('peserta_id');

            $dbAktif = $sesiList->where('status', 'berlangsung')->where('jumlah_peringatan', 0)->count();
            $dbPeringatan = $sesiList->where('jumlah_peringatan', '>', 0)->where('status', '!=', 'diskualifikasi')->count();
            $dbTerputus = $sesiList->whereIn('status', ['terputus', 'menunggu_izin'])->count();

            $pesertas = $selectedJadwal->pesertas()->with('user')->get();

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
                        $diffSec = $now->diffInSeconds(Carbon::parse($sesi->last_heartbeat));
                        if ($diffSec < 10) {
                            $terakhirAktifText = 'Baru saja';
                        } elseif ($diffSec < 60) {
                            $terakhirAktifText = "{$diffSec} detik yang lalu";
                        } elseif ($diffSec < 3600) {
                            $min = (int) floor($diffSec / 60);
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

        return view('pengawas.monitoring.show', compact(
            'selectedJadwal',
            'allJadwals',
            'ringkasan',
            'pesertaList',
            'totalSoal',
            'sisaDetik',
            'sisaWaktuText'
        ));
    }

    /**
     * Endpoint polling AJAX real-time (rule 02-k & 06)
     */
    public function pollData(Jadwal $jadwal): JsonResponse
    {
        if ($jadwal->pengawas()->exists() && ! $jadwal->pengawas()->where('users.id', Auth::id())->exists() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Anda tidak memiliki hak akses monitoring untuk jadwal ini.'], 403);
        }

        $totalSoal = 50;
        $dbTotalSoal = $jadwal->paketSoal?->bankSoals()->where('aktif', true)->count();
        if ($dbTotalSoal && $dbTotalSoal > 0) {
            $totalSoal = $dbTotalSoal;
        }

        // Ambil semua peserta terdaftar pada jadwal ini
        $pesertas = $jadwal->pesertas()->with('user')->get();

        $sesiList = SesiUjian::where('jadwal_id', $jadwal->id)
            ->with(['jawabanPesertas', 'permintaanIzins'])
            ->get()
            ->keyBy('peserta_id');

        $pesertas = $pesertas->sortBy(function ($p) use ($sesiList) {
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
        })->values();

        $now = now();
        $aktifCount = 0;
        $peringatanCount = 0;
        $terputusCount = 0;

        $items = [];

        foreach ($pesertas as $index => $peserta) {
            $sesi = $sesiList->get($peserta->id);
            $name = $peserta->user->name ?? 'Peserta';
            $noUjian = $peserta->no_ujian ?? '-';
            $statusText = 'Belum Mulai';
            $badgeColor = 'slate';
            $terakhirAktifText = '-';
            $progresSoal = "0/{$totalSoal} Soal";
            $sesiId = $sesi?->id;
            $hasPendingPermission = false;
            $alasanIzin = null;
            $peringatanTotal = 0;

            if ($sesi) {
                $peringatanTotal = $sesi->jumlah_peringatan;
                $answeredCount = $sesi->jawabanPesertas->whereNotNull('jawaban')->count();
                $progresSoal = "{$answeredCount}/{$totalSoal} Soal";

                $pendingIzin = $sesi->permintaanIzins->where('status', 'menunggu')->sortByDesc('created_at')->first();
                $hasPendingPermission = ($sesi->status === 'menunggu_izin') || ($pendingIzin !== null);
                $alasanIzin = $pendingIzin?->alasan;

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
                } else {
                    $diffSeconds = 999;
                }

                if ($sesi->status === 'diskualifikasi') {
                    $statusText = 'Diskualifikasi';
                    $badgeColor = 'red';
                } elseif ($hasPendingPermission) {
                    $statusText = 'Menunggu Izin';
                    $badgeColor = 'orange';
                    $terputusCount++;
                } elseif ($sesi->status === 'selesai') {
                    $statusText = 'Selesai';
                    $badgeColor = 'blue';
                } elseif ($diffSeconds > 40 || in_array($sesi->status, ['terputus'])) {
                    $statusText = 'Terputus';
                    $badgeColor = 'red';
                    $terputusCount++;
                } elseif ($peringatanTotal > 0) {
                    $statusText = 'Peringatan';
                    $badgeColor = 'yellow';
                    $peringatanCount++;
                } else {
                    $statusText = 'Aktif';
                    $badgeColor = 'green';
                    $aktifCount++;
                }
            }

            $items[] = [
                'no' => $index + 1,
                'sesi_id' => $sesiId,
                'peserta_id' => $peserta->id,
                'nama' => $name,
                'no_ujian' => $noUjian,
                'email' => $peserta->user->email ?? '-',
                'status' => $statusText,
                'badge_color' => $badgeColor,
                'progres' => $progresSoal,
                'terakhir_aktif' => $terakhirAktifText,
                'peringatan_count' => $peringatanTotal,
                'has_pending_permission' => $hasPendingPermission,
                'alasan_izin' => $alasanIzin,
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'aktif' => $aktifCount,
                'peringatan' => $peringatanCount,
                'terputus' => $terputusCount,
                'total_peserta' => $pesertas->count(),
            ],
            'items' => $items,
        ]);
    }

    /**
     * Aksi Pengawas: Beri teguran / peringatan
     */
    public function tegur(Request $request, SesiUjian $sesi): JsonResponse|RedirectResponse
    {
        $sesi->increment('jumlah_peringatan');
        $freshSesi = $sesi->fresh();

        $keterangan = $request->input('keterangan', 'Peringatan pengawas: Terdeteksi aktivitas mencurigakan');

        Pelanggaran::create([
            'peserta_id' => $freshSesi->peserta_id,
            'jadwal_id' => $freshSesi->jadwal_id,
            'user_id' => Auth::id(),
            'jenis_pelanggaran' => 'Teguran Pengawas',
            'waktu' => now(),
            'keterangan' => $keterangan,
            'tindakan' => $freshSesi->jumlah_peringatan >= 3 ? 'Kunci Ujian Otomatis (3x Peringatan)' : 'Peringatan',
        ]);

        $isLocked = false;
        if ($freshSesi->jumlah_peringatan >= 3 && $freshSesi->status === 'berlangsung') {
            $freshSesi->update(['status' => 'menunggu_izin']);
            $isLocked = true;

            PermintaanIzin::firstOrCreate(
                [
                    'sesi_ujian_id' => $freshSesi->id,
                    'status' => 'menunggu',
                ],
                [
                    'alasan' => 'Ujian terkunci otomatis oleh sistem karena telah mencapai batas maksimal 3 kali pelanggaran (Teguran Pengawas: '.$keterangan.').',
                ]
            );

            ActivityLogger::log('Ujian Terkunci Otomatis', 'Ujian terkunci karena 3 kali pelanggaran (Teguran Pengawas)', $freshSesi->jadwal_id, $freshSesi->peserta->user);
        } else {
            ActivityLogger::log('Teguran Pengawas', "Teguran dikirim ke {$freshSesi->peserta->user->name}: {$keterangan}", $freshSesi->jadwal_id);
        }

        // Kirim notifikasi realtime ke peserta melalui antrean sinyal WebRTC (polled tiap 1 detik oleh peserta)
        $signalKey = "webrtc:signals:{$freshSesi->id}:peserta";
        $signals = Cache::get($signalKey, []);
        if (! is_array($signals)) {
            $signals = [];
        }
        $signals[] = [
            'type' => 'teguran_pengawas',
            'data' => [
                'pesan' => $keterangan,
                'jumlah_peringatan' => $freshSesi->jumlah_peringatan,
                'terkunci' => $isLocked,
                'waktu' => now()->toISOString(),
            ],
            'sender_id' => Auth::id(),
            'timestamp' => now()->toISOString(),
        ];
        Cache::put($signalKey, array_slice($signals, -30), now()->addMinutes(3));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Teguran berhasil dikirim.',
                'jumlah_peringatan' => $freshSesi->jumlah_peringatan,
                'terkunci' => $isLocked,
            ]);
        }

        return back()->with('success', 'Teguran berhasil dikirim ke peserta.');
    }

    /**
     * Aksi Pengawas: Beri izin masuk kembali (rule 02-e)
     */
    public function izin(Request $request, SesiUjian $sesi): JsonResponse|RedirectResponse
    {
        $sesi->update([
            'status' => 'izin_diberikan',
            'last_heartbeat' => now(),
            'jumlah_peringatan' => 0, // Reset counter pelanggaran sesi aktif setelah diizinkan pengawas
        ]);

        // Update permintaan izin terakhir bila ada
        $permintaan = PermintaanIzin::where('sesi_ujian_id', $sesi->id)
            ->where('status', 'menunggu')
            ->latest()
            ->first();

        if ($permintaan) {
            $permintaan->update([
                'status' => 'disetujui',
                'pengawas_user_id' => Auth::id(),
                'waktu_respon' => now(),
            ]);
        }

        ActivityLogger::log('Izin Masuk Diberikan', "Izin masuk diberikan kepada {$sesi->peserta->user->name}", $sesi->jadwal_id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Izin masuk kembali berhasil diberikan.']);
        }

        return back()->with('success', 'Izin masuk kembali berhasil diberikan.');
    }

    /**
     * Ambil riwayat audit log pelanggaran peserta untuk modal inspeksi pengawas
     */
    public function riwayatPelanggaran(SesiUjian $sesi): JsonResponse
    {
        $pelanggarans = Pelanggaran::where('peserta_id', $sesi->peserta_id)
            ->where('jadwal_id', $sesi->jadwal_id)
            ->latest('waktu')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'jenis' => $p->jenis_pelanggaran,
                    'keterangan' => $p->keterangan ?? '-',
                    'tindakan' => $p->tindakan ?? 'Peringatan Otomatis',
                    'waktu' => $p->waktu ? $p->waktu->format('H:i:s').' WITA' : '-',
                    'tanggal' => $p->waktu ? $p->waktu->format('d/m/Y') : '-',
                ];
            });

        return response()->json([
            'success' => true,
            'peserta_nama' => $sesi->peserta?->user?->name ?? 'Peserta',
            'no_ujian' => $sesi->peserta?->no_ujian ?? '-',
            'jumlah_peringatan' => $sesi->jumlah_peringatan,
            'status_sesi' => $sesi->status,
            'pelanggarans' => $pelanggarans,
        ]);
    }

    /**
     * Aksi Pengawas: Blokir / Diskualifikasi peserta (rule 02-l)
     */
    public function blokir(Request $request, SesiUjian $sesi): JsonResponse|RedirectResponse
    {
        if ($sesi->status === 'diskualifikasi') {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Peserta sudah didiskualifikasi.']);
            }

            return back()->with('info', 'Peserta sudah didiskualifikasi.');
        }

        $keterangan = $request->input('alasan', 'Diskualifikasi oleh pengawas karena pelanggaran aturan ujian.');

        $sesi->update([
            'status' => 'diskualifikasi',
            'waktu_selesai' => now(),
        ]);

        // Nilai diset 0, status DISKUALIFIKASI
        $this->scoringService->calculateAndSave($sesi, true);

        // Update pelanggaran terakhir jika ada agar riwayat tidak berulang/redundant, atau buat baru
        $existingPelanggaran = Pelanggaran::where('peserta_id', $sesi->peserta_id)
            ->where('jadwal_id', $sesi->jadwal_id)
            ->latest('waktu')
            ->first();

        if ($existingPelanggaran && ! str_contains(strtolower($existingPelanggaran->tindakan ?? ''), 'diskualifikasi')) {
            $existingPelanggaran->update([
                'tindakan' => 'Diskualifikasi & Nilai 0',
                'keterangan' => $existingPelanggaran->keterangan.' | Diskualifikasi: '.$keterangan,
            ]);
        } else {
            Pelanggaran::create([
                'peserta_id' => $sesi->peserta_id,
                'jadwal_id' => $sesi->jadwal_id,
                'user_id' => Auth::id(),
                'jenis_pelanggaran' => 'Diskualifikasi',
                'waktu' => now(),
                'keterangan' => $keterangan,
                'tindakan' => 'Diskualifikasi & Nilai 0',
            ]);
        }

        ActivityLogger::log('Diskualifikasi Peserta', "Peserta {$sesi->peserta->user->name} didiskualifikasi: {$keterangan}", $sesi->jadwal_id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Peserta berhasil didiskualifikasi.']);
        }

        return back()->with('success', 'Peserta berhasil didiskualifikasi.');
    }
}
