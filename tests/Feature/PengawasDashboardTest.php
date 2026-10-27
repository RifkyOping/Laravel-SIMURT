<?php

use App\Models\Jadwal;
use App\Models\PaketSoal;
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
