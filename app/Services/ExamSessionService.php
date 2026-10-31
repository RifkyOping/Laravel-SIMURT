<?php

namespace App\Services;

use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\JawabanPeserta;
use App\Models\Pelanggaran;
use App\Models\PermintaanIzin;
use App\Models\Peserta;
use App\Models\SesiUjian;
use Exception;
use Illuminate\Support\Facades\DB;

class ExamSessionService
{
    public function __construct(
        protected QuestionShuffleService $shuffleService,
        protected ScoringService $scoringService
    ) {}

    /**
     * Memulai atau memvalidasi sesi ujian peserta dengan mekanisme locking (rule 02-e & 02-f)
     *
     * @throws Exception
     */
    public function startOrResumeSession(Jadwal $jadwal, Peserta $peserta): SesiUjian
    {
        return DB::transaction(function () use ($jadwal, $peserta) {
            $sesi = SesiUjian::where('jadwal_id', $jadwal->id)
                ->where('peserta_id', $peserta->id)
                ->lockForUpdate()
                ->first();

            if (! $sesi) {
                // Pertama kali masuk: buat sesi, acak urutan soal, set single-attempt lock
                $shuffledIds = $this->shuffleService->generateShuffledQuestionIds($jadwal->paketSoal);

                $sesi = SesiUjian::create([
                    'jadwal_id' => $jadwal->id,
                    'peserta_id' => $peserta->id,
                    'status' => 'berlangsung',
                    'waktu_masuk' => now(),
                    'last_heartbeat' => now(),
                    'urutan_soal' => $shuffledIds,
                    'sudah_masuk' => true,
                    'jumlah_peringatan' => 0,
                ]);

                ActivityLogger::log('Memulai Ujian', "Memulai ujian {$jadwal->nama_ujian}", $jadwal->id, $peserta->user);

                return $sesi;
            }

            // Validasi status sesi yang sudah ada
            if ($sesi->status === 'diskualifikasi') {
                throw new Exception('Akses ditolak: Anda telah didiskualifikasi dari ujian ini.');
            }

            if ($sesi->status === 'selesai') {
                throw new Exception('Ujian telah selesai dan jawaban Anda telah diserahkan.');
            }

            // Jika izin diberikan oleh pengawas
            if ($sesi->status === 'izin_diberikan') {
                $sesi->update([
                    'status' => 'berlangsung',
                    'last_heartbeat' => now(),
                ]);

                ActivityLogger::log('Masuk Kembali Ujian', 'Melanjutkan ujian setelah disetujui pengawas', $jadwal->id, $peserta->user);

                return $sesi;
            }

            // Jika peserta keluar/terputus dan mencoba masuk lagi tanpa izin
            if ($sesi->sudah_masuk && in_array($sesi->status, ['terputus', 'menunggu_izin'])) {
                throw new Exception('Sesi terputus. Anda harus mendapatkan izin pengawas untuk masuk kembali.');
            }

            // Perbarui heartbeat
            $sesi->update(['last_heartbeat' => now()]);

            return $sesi;
        });
    }

    /**
     * Menyimpan jawaban peserta via autosave AJAX (rule 02-g)
     */
    public function saveAnswer(SesiUjian $sesi, int $bankSoalId, ?string $jawaban): JawabanPeserta
    {
        $soal = BankSoal::findOrFail($bankSoalId);
        $isBenar = (strtoupper(trim($jawaban ?? '')) === strtoupper(trim($soal->kunci_jawaban)));

        $jawabanPeserta = JawabanPeserta::updateOrCreate(
            [
                'sesi_ujian_id' => $sesi->id,
                'bank_soal_id' => $bankSoalId,
            ],
            [
                'jawaban' => $jawaban,
                'benar' => $isBenar,
                'waktu_simpan' => now(),
            ]
        );

        $sesi->update(['last_heartbeat' => now()]);

        return $jawabanPeserta;
    }

    /**
     * Update heartbeat sesi ujian
     */
    public function heartbeat(SesiUjian $sesi): void
    {
        $sesi->update(['last_heartbeat' => now()]);
    }

    /**
     * Mencatat pelanggaran peserta (misal: pindah tab / keluar layar)
     */
    public function recordViolation(SesiUjian $sesi, string $jenis, ?string $keterangan = null): Pelanggaran
    {
        $sesi->increment('jumlah_peringatan');
        $freshSesi = $sesi->fresh();

        $pelanggaran = Pelanggaran::create([
            'peserta_id' => $freshSesi->peserta_id,
            'jadwal_id' => $freshSesi->jadwal_id,
            'user_id' => null, // Sistem
            'jenis_pelanggaran' => $jenis,
            'waktu' => now(),
            'keterangan' => $keterangan ?? "Terdeteksi aktivitas: {$jenis}",
            'tindakan' => $freshSesi->jumlah_peringatan >= 3 ? 'Kunci Ujian Otomatis (3x Peringatan)' : 'Peringatan Otomatis',
        ]);

        // Auto-lock jika mencapai batas maksimal 3 kali pelanggaran
        if ($freshSesi->jumlah_peringatan >= 3 && $freshSesi->status === 'berlangsung') {
            $freshSesi->update(['status' => 'menunggu_izin']);

            PermintaanIzin::firstOrCreate(
                [
                    'sesi_ujian_id' => $freshSesi->id,
                    'status' => 'menunggu',
                ],
                [
                    'alasan' => 'Ujian terkunci otomatis oleh sistem karena telah mencapai batas maksimal 3 kali pelanggaran ('.$jenis.').',
                ]
            );

            ActivityLogger::log('Ujian Terkunci Otomatis', "Ujian terkunci karena 3 kali pelanggaran: {$jenis}", $freshSesi->jadwal_id, $freshSesi->peserta->user);
        } else {
            ActivityLogger::log('Pelanggaran Terdeteksi', $keterangan ?? $jenis, $freshSesi->jadwal_id, $freshSesi->peserta->user);
        }

        return $pelanggaran;
    }

    /**
     * Mengajukan izin masuk kembali ke pengawas (rule 02-e)
     */
    public function submitPermissionRequest(SesiUjian $sesi, string $alasan): PermintaanIzin
    {
        if ($sesi->status === 'diskualifikasi') {
            throw new Exception('Akses ditolak: Anda telah didiskualifikasi dari ujian ini dan tidak dapat mengajukan izin.');
        }

        $permintaan = PermintaanIzin::create([
            'sesi_ujian_id' => $sesi->id,
            'alasan' => $alasan,
            'status' => 'menunggu',
        ]);

        $sesi->update(['status' => 'menunggu_izin']);

        ActivityLogger::log('Permintaan Izin Masuk Kembali', $alasan, $sesi->jadwal_id, $sesi->peserta->user);

        return $permintaan;
    }

    /**
     * Menyelesaikan ujian dan melakukan penilaian otomatis (rule 02-h & 02-i)
     */
    public function finishExam(SesiUjian $sesi): void
    {
        $sesi->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        $this->scoringService->calculateAndSave($sesi, false);

        ActivityLogger::log('Selesai Ujian', "Ujian {$sesi->jadwal->nama_ujian} telah diselesaikan", $sesi->jadwal_id, $sesi->peserta->user);
    }
}
