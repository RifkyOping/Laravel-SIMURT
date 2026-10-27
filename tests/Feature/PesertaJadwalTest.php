<?php

use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\User;
use Carbon\Carbon;

test('peserta terverifikasi dapat mengakses halaman jadwal ujian dengan desain mockup lengkap', function () {
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

    $j1 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'berlangsung',
    ]);

    $j2 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'UK (TI)',
        'tanggal' => Carbon::parse('2024-10-18'),
        'jam_mulai' => '10:30:00',
        'jam_selesai' => '12:30:00',
        'durasi' => 120,
        'passing_grade' => 320,
        'status' => 'akan_datang',
    ]);

    $j3 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Tes Wawasan Kebangsaan (TWK) - Kelas Susulan',
        'tanggal' => Carbon::parse('2024-10-22'),
        'jam_mulai' => '13:00:00',
        'jam_selesai' => '15:00:00',
        'durasi' => 120,
        'passing_grade' => 280,
        'status' => 'akan_datang',
    ]);

    $j4 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Simulasi Ujian Mandiri & Deteksi Kamera',
        'tanggal' => Carbon::parse('2024-10-12'),
        'jam_mulai' => '09:00:00',
        'jam_selesai' => '11:00:00',
        'durasi' => 120,
        'passing_grade' => 310,
        'status' => 'selesai',
    ]);

    $peserta->jadwals()->attach([$j1->id, $j2->id, $j3->id, $j4->id]);

    SesiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $j4->id,
        'status' => 'selesai',
        'waktu_mulai' => Carbon::parse('2024-10-12 09:00:00'),
        'waktu_selesai' => Carbon::parse('2024-10-12 10:45:00'),
        'ip_address' => '127.0.0.1',
    ]);

    // Request tab 'semua'
    $response = $this->actingAs($user)->get(route('peserta.jadwal.index'));

    $response->assertOk();
    $response->assertSee('Jadwal Ujian');
    $response->assertSee('Daftar ujian yang harus Anda ikuti beserta jadwal dan statusnya.');
    $response->assertSee('PENTING: Pastikan koneksi internet stabil sebelum memasuki ruang ujian.');
    $response->assertSee('Semua');
    $response->assertSee('Akan Datang');
    $response->assertSee('Berlangsung');
    $response->assertSee('Selesai');

    // Cek keberadaan nama ujian dan badge/aksi sesuai mockup
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('15 Oktober 2024');
    $response->assertSee('08:00 - 10:00 WITA');
    $response->assertSee('Siap');
    $response->assertSee('Masuk Ujian');

    $response->assertSee('Ujian Kompetensi Bidang (Teknologi Informasi)');
    $response->assertSee('18 Oktober 2024');
    $response->assertSee('10:30 - 12:30 WITA');
    $response->assertSee('Akan Datang');
    $response->assertSee('Belum Mulai');

    $response->assertSee('Tes Wawasan Kebangsaan (TWK) - Kelas Susulan');
    $response->assertSee('22 Oktober 2024');
    $response->assertSee('13:00 - 15:00 WITA');

    $response->assertSee('Simulasi Ujian Mandiri & Deteksi Kamera');
    $response->assertSee('12 Oktober 2024');
    $response->assertSee('09:00 - 11:00 WITA');
    $response->assertSee('Lihat Hasil');
});

test('filter tab pada halaman jadwal ujian bekerja dengan baik', function () {
    $user = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);
    $peserta = Peserta::create([
        'user_id' => $user->id,
        'no_identitas' => '3171012345670002',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Demo',
        'aktif' => true,
    ]);

    $jBerlangsung = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Aktif Hari Ini',
        'tanggal' => Carbon::today(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'berlangsung',
    ]);

    $jSelesai = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Yang Sudah Selesai',
        'tanggal' => Carbon::yesterday(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'selesai',
    ]);

    $peserta->jadwals()->attach([$jBerlangsung->id, $jSelesai->id]);

    // Filter berlangsung
    $responseBerlangsung = $this->actingAs($user)->get(route('peserta.jadwal.index', ['tab' => 'berlangsung']));
    $responseBerlangsung->assertSee('Ujian Aktif Hari Ini');
    $responseBerlangsung->assertDontSee('Ujian Yang Sudah Selesai');

    // Filter selesai
    $responseSelesai = $this->actingAs($user)->get(route('peserta.jadwal.index', ['tab' => 'selesai']));
    $responseSelesai->assertSee('Ujian Yang Sudah Selesai');
    $responseSelesai->assertDontSee('Ujian Aktif Hari Ini');
});
