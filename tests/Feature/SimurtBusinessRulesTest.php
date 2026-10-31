<?php

use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\User;
use App\Services\ExamSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

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

test('peserta yang diblokir tidak dapat masuk ruang ujian dan tidak dapat mengajukan izin', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas', 'status_akun' => 'aktif']);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Pengawasan Blokir',
        'durasi' => 60,
        'passing_grade' => 300,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Blokir Terkunci',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '07:00:00',
        'jam_selesai' => '23:00:00',
        'durasi' => 60,
        'status' => 'berlangsung',
    ]);

    $pesertaUser = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi']);
    $peserta = Peserta::create(['user_id' => $pesertaUser->id, 'no_identitas' => 'NIKBLOKIRTEST']);
    $jadwal->pesertas()->attach($peserta->id);

    $service = app(ExamSessionService::class);
    $sesi = $service->startOrResumeSession($jadwal, $peserta);

    // Pengawas memblokir peserta
    $this->actingAs($pengawas)->post(route('pengawas.monitoring.blokir', $sesi->id), [
        'alasan' => 'Pelanggaran berat',
    ]);

    $sesi->refresh();
    expect($sesi->status)->toBe('diskualifikasi');

    // 1. Peserta mencoba masuk ruang ujian -> menampilkan halaman terkunci dengan status didiskualifikasi
    $masukResponse = $this->actingAs($pesertaUser)->get(route('peserta.ujian.masuk', $jadwal->id));
    $masukResponse->assertOk();
    $masukResponse->assertSee('Akses Ujian Diblokir (Didiskualifikasi)');
    $masukResponse->assertSee('Akses Ditutup Permanen');
    $masukResponse->assertDontSee('Ajukan Izin Masuk Kembali');

    // 2. Peserta mencoba mengajukan permohonan izin -> ditolak
    $izinResponse = $this->actingAs($pesertaUser)->post(route('peserta.ujian.minta-izin', $sesi->id), [
        'alasan' => 'Tolong izinkan saya kembali',
    ]);
    $izinResponse->assertSessionHas('error');
    $sesi->refresh();
    expect($sesi->status)->toBe('diskualifikasi');
});

test('peserta yang sudah diblokir tidak lagi menampilkan tombol blokir pada pengawas dan polling status diskualifikasi', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas', 'status_akun' => 'aktif']);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Pengawas Blokir',
        'durasi' => 60,
        'passing_grade' => 300,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Monitoring Blokir',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '07:00:00',
        'jam_selesai' => '23:00:00',
        'durasi' => 60,
        'status' => 'berlangsung',
    ]);

    $jadwal->pengawas()->attach($pengawas->id);

    $pesertaUser = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi', 'name' => 'Budi Blokir']);
    $peserta = Peserta::create(['user_id' => $pesertaUser->id, 'no_identitas' => 'NIKBUDIBLOKIR']);
    $jadwal->pesertas()->attach($peserta->id);

    $service = app(ExamSessionService::class);
    $sesi = $service->startOrResumeSession($jadwal, $peserta);

    // Pengawas memblokir peserta
    $blockResponse = $this->actingAs($pengawas)->postJson(route('pengawas.monitoring.blokir', $sesi->id), [
        'alasan' => 'Membuka tab lain berulang kali',
    ]);
    $blockResponse->assertOk();
    $blockResponse->assertJson(['success' => true]);

    $sesi->refresh();
    expect($sesi->status)->toBe('diskualifikasi');

    // Pengawas memanggil blokir lagi -> tetap aman dan idempotent
    $repeatBlock = $this->actingAs($pengawas)->postJson(route('pengawas.monitoring.blokir', $sesi->id), [
        'alasan' => 'Mencoba blokir lagi',
    ]);
    $repeatBlock->assertOk();

    // Data polling monitoring menampilkan status Diskualifikasi
    $pollResponse = $this->actingAs($pengawas)->getJson(route('pengawas.monitoring.poll', $jadwal->id));
    $pollResponse->assertOk();
    $pollData = $pollResponse->json();
    $item = collect($pollData['items'])->firstWhere('peserta_id', $peserta->id);
    expect($item['status'])->toBe('Diskualifikasi');

    // Tampilan dashboard fallback pengawas menampilkan Diblokir
    $dashboardResponse = $this->actingAs($pengawas)->get(route('pengawas.dashboard'));
    $dashboardResponse->assertOk();
    $dashboardResponse->assertSee('Diblokir');
});

test('pengawas dapat mengirim teguran ke peserta dan otomatis terkunci pada teguran ke-3', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas', 'status_akun' => 'aktif']);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Teguran Test',
        'durasi' => 60,
        'passing_grade' => 300,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Teguran Realtime',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '07:00:00',
        'jam_selesai' => '23:00:00',
        'durasi' => 60,
        'status_jadwal' => 'berlangsung',
    ]);

    $userPeserta = User::factory()->create(['role' => 'peserta', 'status_akun' => 'aktif']);
    $peserta = Peserta::create([
        'user_id' => $userPeserta->id,
        'no_identitas' => '3201019900010099',
        'instansi' => 'Umum',
    ]);

    $sesi = SesiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $jadwal->id,
        'waktu_masuk' => now(),
        'status' => 'berlangsung',
        'jumlah_peringatan' => 0,
        'last_heartbeat' => now(),
    ]);

    // Teguran ke-1 via JSON
    $res1 = $this->actingAs($pengawas)->postJson(route('pengawas.monitoring.tegur', $sesi->id), [
        'keterangan' => 'Jangan menoleh ke kiri atau kanan',
    ]);
    $res1->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('jumlah_peringatan', 1)
        ->assertJsonPath('terkunci', false);

    $sesi->refresh();
    expect($sesi->jumlah_peringatan)->toBe(1);
    expect($sesi->status)->toBe('berlangsung');

    // Pastikan sinyal WebRTC terkirim ke antrean peserta
    $signals = Cache::get("webrtc:signals:{$sesi->id}:peserta", []);
    expect($signals)->not->toBeEmpty();
    $latestSignal = end($signals);
    expect($latestSignal['type'])->toBe('teguran_pengawas');
    expect($latestSignal['data']['pesan'])->toBe('Jangan menoleh ke kiri atau kanan');

    // Teguran ke-2
    $res2 = $this->actingAs($pengawas)->postJson(route('pengawas.monitoring.tegur', $sesi->id), [
        'keterangan' => 'Peringatan kedua: harap fokus ke layar',
    ]);
    $res2->assertOk()->assertJsonPath('jumlah_peringatan', 2);

    // Teguran ke-3 -> Harus auto-lock
    $res3 = $this->actingAs($pengawas)->postJson(route('pengawas.monitoring.tegur', $sesi->id), [
        'keterangan' => 'Peringatan ketiga: Ujian Anda dikunci',
    ]);
    $res3->assertOk()
        ->assertJsonPath('jumlah_peringatan', 3)
        ->assertJsonPath('terkunci', true);

    $sesi->refresh();
    expect($sesi->status)->toBe('menunggu_izin');
});
