<?php

use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;

test('peserta terverifikasi dapat mengakses dashboard peserta dengan elemen mockup lengkap', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);
    $peserta = Peserta::create([
        'user_id' => $user->id,
        'no_identitas' => '3171012345670001',
        'instansi' => 'Universitas Indonesia',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket SKD',
        'deskripsi' => 'Paket Ujian Seleksi',
        'aktif' => true,
    ]);

    $jadwalSiap = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'berlangsung',
    ]);

    $jadwalBelumMulai = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'UK (TI)',
        'tanggal' => Carbon::parse('2024-10-18'),
        'jam_mulai' => '10:30:00',
        'jam_selesai' => '12:30:00',
        'durasi' => 120,
        'passing_grade' => 320,
        'status' => 'akan_datang',
    ]);

    $peserta->jadwals()->attach([$jadwalSiap->id, $jadwalBelumMulai->id]);

    $response = $this->actingAs($user)->get(route('peserta.dashboard'));

    $response->assertOk();
    $response->assertSee('Selamat Datang, Budi Santoso!');
    $response->assertSee('Gunakan dashboard ini untuk melihat jadwal ujian aktif Anda dan mengakses hasil seleksi.');
    $response->assertSee('Status Akun: TERVERIFIKASI');
    $response->assertSee('Akun Anda telah disetujui oleh panitia.');
    $response->assertSee('Jadwal Ujian Mendatang');
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('Ujian Kompetensi Bidang (Teknologi Informasi)');
    $response->assertSee('Siap');
    $response->assertSee('Masuk Ujian');
    $response->assertSee('Belum Mulai');
    $response->assertSee('Belum Tersedia');
    $response->assertSee('Pengumuman Terbaru');
    $response->assertSee('Dashboard');
    $response->assertSee('Jadwal Ujian');
    $response->assertSee('Hasil Ujian');
    $response->assertSee('Budi Santoso (Peserta)');
});
