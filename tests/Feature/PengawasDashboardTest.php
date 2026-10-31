<?php

use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\PermintaanIzin;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengguna belum login dialihkan ke halaman login saat mengakses dashboard pengawas', function () {
    $response = $this->get(route('pengawas.dashboard'));
    $response->assertRedirect(route('login'));
});

test('pengawas dapat mengakses halaman dashboard ruang pengawas', function () {
    $pengawas = User::factory()->create([
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'user_id' => $pengawas->id,
        'nama_paket' => '(SKD) - Batch 1',
        'deskripsi' => 'Paket Ujian SKD',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'berlangsung',
    ]);

    $response = $this->actingAs($pengawas)->get(route('pengawas.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Ruang Pengawas (Proctoring)');
    $response->assertSee('Daftar Ruang Ujian yang Diawasi');
    $response->assertSee('Masuk Ruang Pengawas');
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('Monitoring Peserta Terhubung');
    $response->assertSee('Peringatan Keamanan Sistem');
});

test('role peserta tidak dapat mengakses dashboard pengawas', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($peserta)->get(route('pengawas.dashboard'));
    $response->assertStatus(403);
});

test('pengawas dapat mengakses halaman monitoring ujian real-time', function () {
    $pengawas = User::factory()->create([
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'user_id' => $pengawas->id,
        'nama_paket' => '(SKD) - Batch 1',
        'deskripsi' => 'Paket Ujian SKD',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'berlangsung',
    ]);

    // 1. Pada halaman monitoring index, tampil list ujian dan filter
    $responseIndex = $this->actingAs($pengawas)->get(route('pengawas.monitoring.index'));
    $responseIndex->assertStatus(200);
    $responseIndex->assertSee('Monitoring Ujian Real-Time');
    $responseIndex->assertSee('Semua');
    $responseIndex->assertSee('Berlangsung');
    $responseIndex->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $responseIndex->assertSee('Masuk');

    // 2. Klik Masuk membuka halaman detail ujian
    $responseShow = $this->actingAs($pengawas)->get(route('pengawas.monitoring.show', $jadwal));
    $responseShow->assertStatus(200);
    $responseShow->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $responseShow->assertSee('Kembali ke Daftar Ujian');
    $responseShow->assertSee('Cari nama peserta...');
});

test('pengawas dapat memanggil endpoint poll data monitoring secara real-time', function () {
    $pengawas = User::factory()->create([
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'user_id' => $pengawas->id,
        'nama_paket' => '(SKD) - Batch 1',
        'deskripsi' => 'Paket Ujian SKD',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'berlangsung',
    ]);

    $response = $this->actingAs($pengawas)->getJson(route('pengawas.monitoring.poll', $jadwal->id));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'summary' => ['aktif', 'peringatan', 'terputus', 'total_peserta'],
        'items',
    ]);
});

test('pengawas dapat mengakses halaman log aktivitas ujian', function () {
    $pengawas = User::factory()->create([
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($pengawas)->get(route('pengawas.log.index'));

    $response->assertStatus(200);
    $response->assertSee('Log Aktivitas Ujian');
    $response->assertSee('Riwayat seluruh aktivitas dan tindakan selama pengawasan ujian.');
    $response->assertSee('Jenis Aktivitas:');
});

test('tombol izin dan status pending hanya aktif jika peserta mengajukan permintaan izin', function () {
    $pengawas = User::factory()->create([
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'user_id' => $pengawas->id,
        'nama_paket' => 'Paket Tes Izin',
        'deskripsi' => 'Paket Ujian',
        'durasi' => 60,
        'passing_grade' => 60.00,
        'status_paket' => 'berlangsung',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Monitoring Izin',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 60,
        'passing_grade' => 60.00,
        'status' => 'berlangsung',
    ]);

    // Peserta 1: Sedang mengerjakan aktif, TIDAK meminta izin
    $user1 = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi']);
    $peserta1 = Peserta::create(['user_id' => $user1->id, 'no_identitas' => '12345678901', 'no_ujian' => 'REG-001', 'instansi' => 'Instansi A']);
    $jadwal->pesertas()->attach($peserta1->id);
    $sesi1 = SesiUjian::create([
        'jadwal_id' => $jadwal->id,
        'peserta_id' => $peserta1->id,
        'status' => 'berlangsung',
        'jumlah_peringatan' => 0,
        'last_heartbeat' => now(),
    ]);

    // Peserta 2: Terkunci dan MEMINTA IZIN
    $user2 = User::factory()->create(['role' => 'peserta', 'status_akun' => 'terverifikasi']);
    $peserta2 = Peserta::create(['user_id' => $user2->id, 'no_identitas' => '12345678902', 'no_ujian' => 'REG-002', 'instansi' => 'Instansi B']);
    $jadwal->pesertas()->attach($peserta2->id);
    $sesi2 = SesiUjian::create([
        'jadwal_id' => $jadwal->id,
        'peserta_id' => $peserta2->id,
        'status' => 'menunggu_izin',
        'jumlah_peringatan' => 3,
        'last_heartbeat' => now(),
    ]);
    PermintaanIzin::create([
        'sesi_ujian_id' => $sesi2->id,
        'alasan' => 'Browser tertutup otomatis karena mati listrik',
        'status' => 'menunggu',
    ]);

    // Uji endpoint poll data
    $pollResponse = $this->actingAs($pengawas)->getJson(route('pengawas.monitoring.poll', $jadwal->id));
    $pollResponse->assertStatus(200);

    $items = collect($pollResponse->json('items'));
    $item1 = $items->firstWhere('peserta_id', $peserta1->id);
    $item2 = $items->firstWhere('peserta_id', $peserta2->id);

    // Peserta 1 TIDAK boleh punya pending permission
    expect($item1['has_pending_permission'])->toBeFalse();
    expect($item1['status'])->toBe('Aktif');

    // Peserta 2 HARUS punya pending permission dan status Menunggu Izin
    expect($item2['has_pending_permission'])->toBeTrue();
    expect($item2['status'])->toBe('Menunggu Izin');
    expect($item2['alasan_izin'])->toBe('Browser tertutup otomatis karena mati listrik');

    // Uji view show monitoring
    $showResponse = $this->actingAs($pengawas)->get(route('pengawas.monitoring.show', $jadwal->id));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Ujian Monitoring Izin');

    // Uji view dashboard pengawas
    $dashResponse = $this->actingAs($pengawas)->get(route('pengawas.dashboard'));
    $dashResponse->assertStatus(200);
});
