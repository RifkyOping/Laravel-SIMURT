<?php

use App\Models\Jadwal;
use App\Models\NilaiUjian;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;

test('tamu yang belum login diarahkan ke login saat mengakses halaman hasil ujian', function () {
    $response = $this->get(route('peserta.hasil.index'));
    $response->assertRedirect(route('login'));
});

test('peserta dapat mengakses halaman hasil ujian dan melihat tampilan mockup lengkap', function () {
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

    // 1. Ujian Utama (Featured Card)
    $jadwalUtama = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 311,
        'status' => 'berlangsung',
    ]);

    NilaiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $jadwalUtama->id,
        'nilai_akhir' => 395,
        'jumlah_benar' => 82,
        'jumlah_salah' => 18,
        'jumlah_tidak_dijawab' => 0,
        'status_kelulusan' => 'LULUS',
    ]);

    // 2. Riwayat Ujian Lain 1: Simulasi Ujian SKD Mandiri (LULUS)
    $jadwalLain1 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Simulasi Ujian SKD Mandiri',
        'tanggal' => Carbon::parse('2024-10-05'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'selesai',
    ]);

    NilaiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $jadwalLain1->id,
        'nilai_akhir' => 350,
        'jumlah_benar' => 70,
        'jumlah_salah' => 30,
        'jumlah_tidak_dijawab' => 0,
        'status_kelulusan' => 'LULUS',
    ]);

    // 3. Riwayat Ujian Lain 2: Tes Wawasan Kebangsaan (Preliminary) (TIDAK LULUS)
    $jadwalLain2 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Tes Wawasan Kebangsaan (Preliminary)',
        'tanggal' => Carbon::parse('2024-09-28'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '09:30:00',
        'durasi' => 90,
        'passing_grade' => 300,
        'status' => 'selesai',
    ]);

    NilaiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $jadwalLain2->id,
        'nilai_akhir' => 290,
        'jumlah_benar' => 58,
        'jumlah_salah' => 42,
        'jumlah_tidak_dijawab' => 0,
        'status_kelulusan' => 'TIDAK LULUS',
    ]);

    $response = $this->actingAs($user)->get(route('peserta.hasil.index'));

    $response->assertOk();
    $response->assertSee('Hasil Ujian');
    $response->assertSee('Berikut adalah nilai dari ujian yang telah Anda selesaikan beserta status seleksi Anda.');
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('LULUS SELEKSI');
    $response->assertSee('395');
    $response->assertSee('Passing Grade:');
    $response->assertSee('311');
    $response->assertSee('Jumlah Soal:');
    $response->assertSee('Jawaban Benar:');
    $response->assertSee('82');
    $response->assertSee('Jawaban Salah:');
    $response->assertSee('18');
    $response->assertSee('Riwayat Hasil Seleksi Lainnya');
    $response->assertSee('Simulasi Ujian SKD Mandiri');
    $response->assertSee('350');
    $response->assertSee('Tes Wawasan Kebangsaan (Preliminary)');
    $response->assertSee('290');
    $response->assertSee('TIDAK LULUS');
});

test('peserta dapat melihat detail hasil ujian pada halaman show', function () {
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

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 311,
        'status' => 'berlangsung',
    ]);

    NilaiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 395,
        'jumlah_benar' => 82,
        'jumlah_salah' => 18,
        'jumlah_tidak_dijawab' => 0,
        'status_kelulusan' => 'LULUS',
    ]);

    $response = $this->actingAs($user)->get(route('peserta.hasil.show', $jadwal));

    $response->assertOk();
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('395');
    $response->assertSee('LULUS SELEKSI');
    $response->assertSee('Passing Grade:');
    $response->assertSee('311');
});

test('peserta melihat info belum ada hasil jika belum ada nilai ujian', function () {
    $user = User::factory()->create([
        'name' => 'Peserta Baru',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);
    Peserta::create([
        'user_id' => $user->id,
        'no_identitas' => '3171012345670099',
        'instansi' => 'Universitas Indonesia',
    ]);

    $response = $this->actingAs($user)->get(route('peserta.hasil.index'));

    $response->assertOk();
    $response->assertSee('Belum Ada Hasil Ujian');
    $response->assertSee('Lihat Jadwal Ujian');
});

test('peserta dapat mengakses layar proses hasil ujian sesuai mockup', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);
    $peserta = Peserta::create([
        'user_id' => $user->id,
        'no_identitas' => '24098311',
        'instansi' => 'Universitas Indonesia',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket SKD',
        'deskripsi' => 'Paket Ujian Seleksi',
        'aktif' => true,
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'status' => 'berlangsung',
    ]);

    $response = $this->actingAs($user)->get(route('peserta.ujian.proses', $jadwal));

    $response->assertOk();
    $response->assertSee('Sistem Manajemen Ujian');
    $response->assertSee('Hasil Ujian Sedang Diproses');
    $response->assertSee('Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('15 Oktober 2024');
    $response->assertSee('08:00 - 10:00 WITA');
    $response->assertSee('120 Menit');
    $response->assertSee('Silahkan Tunggu Beberapa Saat');
    $response->assertSee('Keluar');
});
