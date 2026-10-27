<?php

namespace App\Services;

use App\Models\NilaiUjian;
use App\Models\Notifikasi;
use App\Models\Peserta;
use App\Models\SesiUjian;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    /**
     * Menghitung nilai ujian peserta berdasarkan jawaban tersimpan dan passing grade
     */
    public function calculateAndSave(SesiUjian $sesi, bool $isDisqualified = false): NilaiUjian
    {
        return DB::transaction(function () use ($sesi, $isDisqualified) {
            $jadwal = $sesi->jadwal;
            $peserta = $sesi->peserta;

            // Jika didiskualifikasi (rule 02-i)
            if ($isDisqualified) {
                $nilai = NilaiUjian::updateOrCreate(
                    [
                        'peserta_id' => $peserta->id,
                        'jadwal_id' => $jadwal->id,
                    ],
                    [
                        'sesi_ujian_id' => $sesi->id,
                        'nilai_akhir' => 0.00,
                        'jumlah_benar' => 0,
                        'jumlah_salah' => 0,
                        'jumlah_tidak_dijawab' => count($sesi->urutan_soal ?? []),
                        'status_kelulusan' => 'DISKUALIFIKASI',
                    ]
                );

                Notifikasi::create([
                    'user_id' => $peserta->user_id,
                    'judul' => 'Hasil Ujian: Didiskualifikasi',
                    'isi' => "Anda telah didiskualifikasi pada ujian {$jadwal->nama_ujian}.",
                    'dibaca' => false,
                    'waktu' => now(),
                ]);

                return $nilai;
            }

            // Ambil seluruh soal aktif dari paket
            $soals = $jadwal->paketSoal->bankSoals()
                ->where('aktif', true)
                ->get()
                ->keyBy('id');

            $jawabanMap = $sesi->jawabanPesertas->keyBy('bank_soal_id');

            $totalBenar = 0;
            $totalSalah = 0;
            $totalTidakDijawab = 0;
            $nilaiAkhir = 0.00;

            foreach ($soals as $soalId => $soal) {
                $jawaban = $jawabanMap->get($soalId);

                if (! $jawaban || empty($jawaban->jawaban)) {
                    $totalTidakDijawab++;
                } else {
                    $isBenar = (strtoupper(trim($jawaban->jawaban)) === strtoupper(trim($soal->kunci_jawaban)));

                    // Update status benar pada jawaban peserta
                    $jawaban->update(['benar' => $isBenar]);

                    if ($isBenar) {
                        $totalBenar++;
                        $nilaiAkhir += (float) $soal->bobot;
                    } else {
                        $totalSalah++;
                    }
                }
            }

            $passingGrade = $jadwal->passing_grade ?? $jadwal->paketSoal->passing_grade ?? 300.00;
            $statusKelulusan = ($nilaiAkhir >= $passingGrade) ? 'LULUS' : 'TIDAK LULUS';

            $nilai = NilaiUjian::updateOrCreate(
                [
                    'peserta_id' => $peserta->id,
                    'jadwal_id' => $jadwal->id,
                ],
                [
                    'sesi_ujian_id' => $sesi->id,
                    'nilai_akhir' => $nilaiAkhir,
                    'jumlah_benar' => $totalBenar,
                    'jumlah_salah' => $totalSalah,
                    'jumlah_tidak_dijawab' => $totalTidakDijawab,
                    'status_kelulusan' => $statusKelulusan,
                ]
            );

            // Buat notifikasi in-app
            Notifikasi::create([
                'user_id' => $peserta->user_id,
                'judul' => 'Hasil Ujian Tersedia',
                'isi' => "Hasil ujian {$jadwal->nama_ujian} telah tersedia. Nilai akhir Anda: {$nilaiAkhir} ({$statusKelulusan}).",
                'dibaca' => false,
                'waktu' => now(),
            ]);

            return $nilai;
        });
    }
}
