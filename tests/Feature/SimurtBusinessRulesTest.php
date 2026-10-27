<?php

use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use App\Services\ExamSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('peserta registrasi mendapatkan status awal menunggu verifikasi', function () {
    $response = $this->post(route('register'), [
        'name' => 'Peserta Baru',
        'email' => 'pesertabaru@test.com',
        'no_identitas' => '3201019908770001',
        'instansi' => 'SMA Negeri 1',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('menunggu.verifikasi'));

    $user = User::where('email', 'pesertabaru@test.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('peserta');
    expect($user->status_akun)->toBe('menunggu');
    expect($user->peserta)->not->toBeNull();
    expect($user->peserta->no_identitas)->toBe('3201019908770001');
});

test('peserta berstatus menunggu tidak dapat mengakses dashboard utama peserta', function () {
    $user = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'menunggu',
    ]);
    Peserta::create(['user_id' => $user->id, 'no_identitas' => '12345']);

    $response = $this->actingAs($user)->get(route('peserta.dashboard'));
    $response->assertRedirect(route('menunggu.verifikasi'));
});

test('panitia dapat menyetujui akun peserta menjadi terverifikasi', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $pesertaUser = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'menunggu',
    ]);
    Peserta::create(['user_id' => $pesertaUser->id, 'no_identitas' => '12345']);

    $response = $this->actingAs($panitia)->post(route('panitia.peserta.verifikasi', $pesertaUser->id), [
        'status' => 'terverifikasi',
    ]);

    $response->assertSessionHas('success');
    expect($pesertaUser->fresh()->status_akun)->toBe('terverifikasi');
});

test('akses paksa role peserta ke admin atau panitia menghasilkan 403', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $responseAdmin = $this->actingAs($peserta)->get(route('admin.dashboard'));
    $responseAdmin->assertForbidden();

    $responsePanitia = $this->actingAs($peserta)->get(route('panitia.dashboard'));
    $responsePanitia->assertForbidden();
});

test('sesi ujian menerapkan single-attempt dan pengacakan soal yang tersimpan', function () {
    $panitia = User::factory()->create(['role' => 'panitia', 'status_akun' => 'aktif']);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Uji',
        'durasi' => 60,
        'passing_grade' => 300,
        'status_paket' => 'berlangsung',
    ]);

    for ($i = 1; $i <= 5; $i++) {
        BankSoal::create([
            'paket_soal_id' => $paket->id,
            'user_id' => $panitia->id,
            'pertanyaan' => "Pertanyaan ke-{$i}",
            'opsi_jawaban' => ['A' => '1', 'B' => '2', 'C' => '3', 'D' => '4', 'E' => '5'],
            'kunci_jawaban' => 'A',
            'bobot' => 50,
            'aktif' => true,
        ]);
    }

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Seleksi',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '07:00:00',
        'jam_selesai' => '23:00:00',
        'durasi' => 60,
        'status' => 'berlangsung',
    ]);

    $pesertaUser = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi']);
    $peserta = Peserta::create(['user_id' => $pesertaUser->id, 'no_identitas' => 'NIK999']);
    $jadwal->pesertas()->attach($peserta->id);

    // Pertama kali masuk: Sesi dibuat
    $service = app(ExamSessionService::class);
    $sesi = $service->startOrResumeSession($jadwal, $peserta);

    expect($sesi)->not->toBeNull();
    expect($sesi->sudah_masuk)->toBeTrue();
    expect($sesi->status)->toBe('berlangsung');
    expect(count($sesi->urutan_soal))->toBe(5);

    // Jika sesi terputus, akses ulang dilarang tanpa izin
    $sesi->update(['status' => 'terputus']);
    expect(fn () => $service->startOrResumeSession($jadwal, $peserta))
        ->toThrow(Exception::class);
});

test('autosave menyimpan jawaban via AJAX dan scoring service menghitung nilai dengan akurat', function () {
    $panitia = User::factory()->create(['role' => 'panitia', 'status_akun' => 'aktif']);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Uji Scoring',
        'durasi' => 60,
        'passing_grade' => 70,
        'status_paket' => 'berlangsung',
    ]);

    $soal1 = BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Soal 1',
        'opsi_jawaban' => ['A' => 'Benar', 'B' => 'Salah', 'C' => 'X', 'D' => 'Y', 'E' => 'Z'],
        'kunci_jawaban' => 'A',
        'bobot' => 50,
        'aktif' => true,
    ]);

    $soal2 = BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Soal 2',
        'opsi_jawaban' => ['A' => 'Salah', 'B' => 'Benar', 'C' => 'X', 'D' => 'Y', 'E' => 'Z'],
        'kunci_jawaban' => 'B',
        'bobot' => 50,
        'aktif' => true,
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Scoring',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '07:00:00',
        'jam_selesai' => '23:00:00',
        'durasi' => 60,
        'status' => 'berlangsung',
        'passing_grade' => 70,
    ]);

    $pesertaUser = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi']);
    $peserta = Peserta::create(['user_id' => $pesertaUser->id, 'no_identitas' => 'NIK999']);
    $jadwal->pesertas()->attach($peserta->id);

    $service = app(ExamSessionService::class);
    $sesi = $service->startOrResumeSession($jadwal, $peserta);

    // Autosave jawaban soal 1 = A (Benar), soal 2 = B (Benar)
    $service->saveAnswer($sesi, $soal1->id, 'A');
    $service->saveAnswer($sesi, $soal2->id, 'B');

    // Selesaikan ujian
    $service->finishExam($sesi);

    $nilai = $peserta->nilaiUjians()->where('jadwal_id', $jadwal->id)->first();
    expect($nilai)->not->toBeNull();
    expect((float) $nilai->nilai_akhir)->toBe(100.0);
    expect($nilai->jumlah_benar)->toBe(2);
    expect($nilai->status_kelulusan)->toBe('LULUS');
});

test('pengawas dapat melakukan polling monitoring real-time dan intervensi blokir', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas', 'status_akun' => 'aktif']);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Pengawasan',
        'durasi' => 60,
        'passing_grade' => 300,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Proctoring',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '07:00:00',
        'jam_selesai' => '23:00:00',
        'durasi' => 60,
        'status' => 'berlangsung',
    ]);

    $pesertaUser = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi']);
    $peserta = Peserta::create(['user_id' => $pesertaUser->id, 'no_identitas' => 'NIKPROCTOR']);
    $jadwal->pesertas()->attach($peserta->id);

    $service = app(ExamSessionService::class);
    $sesi = $service->startOrResumeSession($jadwal, $peserta);

    // Polling endpoint
    $response = $this->actingAs($pengawas)->get(route('pengawas.monitoring.poll', $jadwal->id));
    $response->assertOk();
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('summary.aktif', 1);

    // Diskualifikasi
    $blockResponse = $this->actingAs($pengawas)->post(route('pengawas.monitoring.blokir', $sesi->id), [
        'alasan' => 'Membuka tab terlarang',
    ]);
    $blockResponse->assertSessionHas('success');

    $nilai = $peserta->nilaiUjians()->where('jadwal_id', $jadwal->id)->first();
    expect($nilai->status_kelulusan)->toBe('DISKUALIFIKASI');
    expect((float) $nilai->nilai_akhir)->toBe(0.0);
});
