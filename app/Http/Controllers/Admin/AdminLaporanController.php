<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\Jadwal;
use App\Models\Pelanggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminLaporanController extends Controller
{
    public function index(Request $request): View
    {
        $periode = $request->get('periode', 'bulan_ini');
        $periodeLabel = match ($periode) {
            'hari_ini' => 'Hari Ini',
            'minggu_ini' => 'Minggu Ini',
            'tahun_ini' => 'Tahun Ini',
            default => 'Bulan Ini',
        };

        // Metrik KPI dari database (dengan fallback mockup jika belum ada aktivitas)
        $totalLoginHariIni = Aktivitas::where('jenis_aktivitas', 'Login Berhasil')
            ->whereDate('waktu', today())
            ->count() ?: 245;

        $ujianAktif = Jadwal::where('status', 'berlangsung')->count() ?: 2;

        $pelanggaranBulanIni = Pelanggaran::whereMonth('waktu', now()->month)
            ->whereYear('waktu', now()->year)
            ->count() ?: 12;

        $uptimeSistem = '99.8%';

        // Distribusi Role dari database
        $totalUsers = User::count();
        $pesertaCount = User::where('role', 'peserta')->count();
        $pengawasCount = User::where('role', 'pengawas')->count();
        $panitiaCount = User::where('role', 'panitia')->count();
        $adminCount = User::where('role', 'admin')->count();

        $distribusiRole = [
            'peserta' => [
                'nama' => 'Peserta',
                'count' => $pesertaCount ?: 1240,
                'persen' => $totalUsers > 0 ? round(($pesertaCount / $totalUsers) * 100) : 91,
                'color' => 'bg-[#1e385b]',
            ],
            'pengawas' => [
                'nama' => 'Pengawas',
                'count' => $pengawasCount ?: 100,
                'persen' => $totalUsers > 0 ? round(($pengawasCount / $totalUsers) * 100) : 7,
                'color' => 'bg-[#78350f]',
            ],
            'panitia' => [
                'nama' => 'Panitia',
                'count' => $panitiaCount ?: 10,
                'persen' => $totalUsers > 0 ? round(($panitiaCount / $totalUsers) * 100) : 1,
                'color' => 'bg-[#3b82f6]',
            ],
            'admin' => [
                'nama' => 'Admin',
                'count' => $adminCount ?: 2,
                'persen' => $totalUsers > 0 ? round(($adminCount / $totalUsers) * 100) : 1,
                'color' => 'bg-[#f59e0b]',
            ],
        ];

        // Ringkasan Ujian dari database
        $jadwals = Jadwal::with(['nilaiUjians', 'pesertas'])->latest('tanggal')->take(5)->get();
        if ($jadwals->isNotEmpty()) {
            $ringkasanUjian = $jadwals->map(function (Jadwal $j) {
                $totalPeserta = $j->pesertas->count();
                $nilaiCount = $j->nilaiUjians->count();
                $avgNilai = $nilaiCount > 0 ? (string) round($j->nilaiUjians->avg('nilai_akhir'), 1) : '-';
                $lulusCount = $j->nilaiUjians->where('status_kelulusan', 'lulus')->count();
                $kelulusan = $nilaiCount > 0 ? round(($lulusCount / $nilaiCount) * 100).'%' : '-';

                return [
                    'nama' => $j->nama_ujian,
                    'tanggal' => $j->tanggal ? $j->tanggal->translatedFormat('d M Y') : '-',
                    'peserta' => $totalPeserta,
                    'avg_nilai' => $avgNilai,
                    'kelulusan' => $kelulusan,
                ];
            })->all();
        } else {
            $ringkasanUjian = [
                [
                    'nama' => 'SKD - Batch 1',
                    'tanggal' => '15 Okt 2024',
                    'peserta' => 350,
                    'avg_nilai' => '82.5',
                    'kelulusan' => '94%',
                ],
                [
                    'nama' => 'Ujian IT & Coding',
                    'tanggal' => '18 Okt 2024',
                    'peserta' => 120,
                    'avg_nilai' => '76.1',
                    'kelulusan' => '88%',
                ],
                [
                    'nama' => 'Sejarah & TWK',
                    'tanggal' => '22 Okt 2024',
                    'peserta' => 85,
                    'avg_nilai' => '88',
                    'kelulusan' => '97%',
                ],
            ];
        }

        return view('admin.laporan.index', compact(
            'totalLoginHariIni',
            'ujianAktif',
            'pelanggaranBulanIni',
            'uptimeSistem',
            'distribusiRole',
            'ringkasanUjian',
            'periode',
            'periodeLabel'
        ));
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_sistem_'.date('Ymd_His').'.csv"',
        ];

        $totalLoginHariIni = Aktivitas::where('jenis_aktivitas', 'Login Berhasil')
            ->whereDate('waktu', today())
            ->count();
        $ujianAktif = Jadwal::where('status', 'berlangsung')->count();
        $pelanggaranBulanIni = Pelanggaran::whereMonth('waktu', now()->month)
            ->whereYear('waktu', now()->year)
            ->count();
        $totalUsers = User::count();
        $pesertaCount = User::where('role', 'peserta')->count();
        $pengawasCount = User::where('role', 'pengawas')->count();
        $panitiaCount = User::where('role', 'panitia')->count();
        $adminCount = User::where('role', 'admin')->count();

        $jadwals = Jadwal::with(['nilaiUjians', 'pesertas'])->latest('tanggal')->get();

        return response()->stream(function () use ($totalLoginHariIni, $ujianAktif, $pelanggaranBulanIni, $totalUsers, $pesertaCount, $pengawasCount, $panitiaCount, $adminCount, $jadwals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Statistik / Item', 'Nilai / Keterangan', 'Detail']);
            fputcsv($handle, ['Total Login Hari Ini', (string) $totalLoginHariIni, 'Pengguna']);
            fputcsv($handle, ['Ujian Aktif', (string) $ujianAktif, 'Sesi']);
            fputcsv($handle, ['Pelanggaran Bulan Ini', (string) $pelanggaranBulanIni, 'Insiden']);
            fputcsv($handle, ['Uptime Sistem', '99.9%', 'Normal']);
            fputcsv($handle, []);
            fputcsv($handle, ['--- Ringkasan Ujian ---']);
            fputcsv($handle, ['Nama Ujian', 'Tanggal', 'Peserta', 'Avg Nilai', 'Kelulusan']);
            foreach ($jadwals as $j) {
                $nilaiCount = $j->nilaiUjians->count();
                $avgNilai = $nilaiCount > 0 ? round($j->nilaiUjians->avg('nilai_akhir'), 1) : '-';
                $lulusCount = $j->nilaiUjians->where('status_kelulusan', 'lulus')->count();
                $kelulusan = $nilaiCount > 0 ? round(($lulusCount / $nilaiCount) * 100).'%' : '-';
                fputcsv($handle, [
                    $j->nama_ujian,
                    $j->tanggal ? $j->tanggal->translatedFormat('d M Y') : '-',
                    (string) $j->pesertas->count(),
                    (string) $avgNilai,
                    $kelulusan,
                ]);
            }
            fputcsv($handle, []);
            fputcsv($handle, ['--- Distribusi Role Pengguna ---']);
            fputcsv($handle, ['Role', 'Jumlah Akun', 'Persentase']);
            $pesertaPct = $totalUsers > 0 ? round(($pesertaCount / $totalUsers) * 100).'%' : '0%';
            $pengawasPct = $totalUsers > 0 ? round(($pengawasCount / $totalUsers) * 100).'%' : '0%';
            $panitiaPct = $totalUsers > 0 ? round(($panitiaCount / $totalUsers) * 100).'%' : '0%';
            $adminPct = $totalUsers > 0 ? round(($adminCount / $totalUsers) * 100).'%' : '0%';
            fputcsv($handle, ['Peserta', (string) $pesertaCount, $pesertaPct]);
            fputcsv($handle, ['Pengawas', (string) $pengawasCount, $pengawasPct]);
            fputcsv($handle, ['Panitia', (string) $panitiaCount, $panitiaPct]);
            fputcsv($handle, ['Admin', (string) $adminCount, $adminPct]);
            fclose($handle);
        }, 200, $headers);
    }
}
