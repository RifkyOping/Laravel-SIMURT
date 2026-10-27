<?php

use App\Models\BankSoal;
use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses dashboard dan menampilkan layout mockup dengan benar', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'kode_paket' => 'PKT-TEST',
        'nama_paket' => 'Paket Ujian Mockup',
        'passing_grade' => 300,
        'keterangan' => 'Paket test',
    ]);

    BankSoal::create([
        'user_id' => $panitia->id,
        'paket_soal_id' => $paket->id,
        'tipe_soal' => 'pilihan_ganda',
        'pertanyaan' => 'Pertanyaan 1',
        'opsi_jawaban' => ['A' => 'Opsi A', 'B' => 'Opsi B'],
        'kunci_jawaban' => 'A',
        'bobot' => 10,
        'aktif' => true,
    ]);

    $pesertaMenunggu = User::factory()->create([
        'name' => 'Rian Hidayat',
        'email' => 'rian.hid@email.com',
        'role' => 'peserta',
        'status_akun' => 'menunggu',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'berlangsung',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.dashboard'));

    $response->assertOk();
    $response->assertSee('Dashboard Panitia');
    $response->assertSee('Manajemen registrasi peserta, monitoring jadwal, dan kelola kelulusan ujian.');
    $response->assertSee('Total Peserta');
    $response->assertSee('Ujian Aktif');
    $response->assertSee('Soal Tersedia');
    $response->assertSee('Peserta Menunggu Verifikasi');
    $response->assertSee('Jadwal Ujian Mendatang');
    $response->assertSee('Rian Hidayat');
    $response->assertSee('rian.hid@email.com');
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('Budi Santoso (Panitia Ujian)');
    $response->assertSee('Keluar');

    // Sidebar Navigation items sesuai mockup
    $response->assertSee('Data Peserta');
    $response->assertSee('Data Pengawas');
    $response->assertSee('Bank Soal');
    $response->assertSee('Jadwal Ujian');
    $response->assertSee('Hasil Ujian');
    $response->assertSee('Laporan');
});

test('panitia dapat menyetujui verifikasi peserta langsung dari dashboard', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $peserta = User::factory()->create([
        'name' => 'Siti Aminah',
        'email' => 'siti.aminah@email.com',
        'role' => 'peserta',
        'status_akun' => 'menunggu',
    ]);

    $response = $this->actingAs($panitia)->post(route('panitia.peserta.verifikasi', $peserta->id), [
        'status' => 'terverifikasi',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'id' => $peserta->id,
        'status_akun' => 'terverifikasi',
    ]);
});
