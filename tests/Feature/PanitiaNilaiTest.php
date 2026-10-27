<?php

use App\Models\Jadwal;
use App\Models\NilaiUjian;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses halaman rekap nilai ujian dan melihat elemen mockup dengan tepat', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'deskripsi' => 'Paket Ujian SKD',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => '(SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'akan_datang',
    ]);

    $user1 = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi.s@gmail.com', 'role' => 'peserta']);
    $p1 = Peserta::create(['user_id' => $user1->id, 'no_identitas' => '000230240240']);
    NilaiUjian::create([
        'peserta_id' => $p1->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 440,
        'jumlah_benar' => 88,
        'jumlah_salah' => 12,
        'status_kelulusan' => 'LULUS',
    ]);

    $user2 = User::factory()->create(['name' => 'Mega Utami', 'email' => 'mega.utami@gmail.com', 'role' => 'peserta']);
    $p2 = Peserta::create(['user_id' => $user2->id, 'no_identitas' => '049555789343']);
    NilaiUjian::create([
        'peserta_id' => $p2->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 0,
        'jumlah_benar' => 48,
        'jumlah_salah' => 52,
        'status_kelulusan' => 'DISKUALIFIKASI',
    ]);

    $user3 = User::factory()->create(['name' => 'Slamet Raharjo', 'email' => 'slamet.raharjo@gmail.com', 'role' => 'peserta']);
    $p3 = Peserta::create(['user_id' => $user3->id, 'no_identitas' => '139824844045']);
    NilaiUjian::create([
        'peserta_id' => $p3->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 260,
        'jumlah_benar' => 52,
        'jumlah_salah' => 48,
        'status_kelulusan' => 'TIDAK LULUS',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.nilai.index'));

    $response->assertOk();
    $response->assertSee('Rekap Nilai Ujian');
    $response->assertSee('Lihat dan kelola hasil penilaian seluruh peserta ujian.');

    // Filter Bar & Action
    $response->assertSee('Cari nama peserta...');
    $response->assertSee('Ujian:');
    $response->assertSee('Kelulusan:');
    $response->assertSee('Download PDF Rekap');

    // 4 Kartu Statistik
    $response->assertSee('Total Peserta');
    $response->assertSee('Rata-rata Nilai');
    $response->assertSee('Lulus');
    $response->assertSee('Tidak Lulus');

    // Kolom Tabel
    $response->assertSee('Nama Peserta');
    $response->assertSee('Email');
    $response->assertSee('Nomor Identitas');
    $response->assertSee('Jawaban Benar');
    $response->assertSee('Nilai Akhir');
    $response->assertSee('Status');
    $response->assertSee('Aksi');

    // Baris data mockup
    $response->assertSee('Budi Santoso');
    $response->assertSee('budi.s@gmail.com');
    $response->assertSee('000230240240');
    $response->assertSee('88 Soal');
    $response->assertSee('440');
    $response->assertSee('LULUS');

    $response->assertSee('Mega Utami');
    $response->assertSee('049555789343');
    $response->assertSee('DISKUALIFIKASI');

    $response->assertSee('Slamet Raharjo');
    $response->assertSee('139824844045');
    $response->assertSee('TIDAK LULUS');

    // Tombol Detail & Pop up modal Detail Hasil Ujian
    $response->assertSee('Detail');
    $response->assertSee('Detail Hasil Ujian');
    $response->assertSee('Statistik Hasil');
    $response->assertSee('Log Waktu');
    $response->assertSee('Catatan Pengawasan / Pelanggaran');
    $response->assertSee('Tutup');
});

test('panitia dapat memfilter rekap nilai berdasarkan nama peserta', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket A',
        'deskripsi' => 'Deskripsi',
        'durasi' => 60,
        'passing_grade' => 200,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Seleksi',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'status' => 'akan_datang',
    ]);

    $u1 = User::factory()->create(['name' => 'Dewi Lestari', 'email' => 'dewi@gmail.com', 'role' => 'peserta']);
    $p1 = Peserta::create(['user_id' => $u1->id, 'no_identitas' => '111122223333']);
    NilaiUjian::create([
        'peserta_id' => $p1->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 375,
        'jumlah_benar' => 75,
        'status_kelulusan' => 'LULUS',
    ]);

    $u2 = User::factory()->create(['name' => 'Achmad Fauzi', 'email' => 'achmad@gmail.com', 'role' => 'peserta']);
    $p2 = Peserta::create(['user_id' => $u2->id, 'no_identitas' => '444455556666']);
    NilaiUjian::create([
        'peserta_id' => $p2->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 460,
        'jumlah_benar' => 92,
        'status_kelulusan' => 'LULUS',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.nilai.index', [
        'jadwal_id' => $jadwal->id,
        'search' => 'Dewi Lestari',
    ]));

    $response->assertOk();
    $response->assertSee('Dewi Lestari');
    $response->assertDontSee('Achmad Fauzi');
});

test('panitia dapat memfilter rekap nilai berdasarkan status kelulusan', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket B',
        'deskripsi' => 'Deskripsi',
        'durasi' => 60,
        'passing_grade' => 200,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Batch',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'status' => 'akan_datang',
    ]);

    $u1 = User::factory()->create(['name' => 'Peserta Lulus', 'email' => 'lulus@gmail.com', 'role' => 'peserta']);
    $p1 = Peserta::create(['user_id' => $u1->id, 'no_identitas' => '1001']);
    NilaiUjian::create([
        'peserta_id' => $p1->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 380,
        'jumlah_benar' => 76,
        'status_kelulusan' => 'LULUS',
    ]);

    $u2 = User::factory()->create(['name' => 'Peserta Diskualifikasi', 'email' => 'diskualifikasi@gmail.com', 'role' => 'peserta']);
    $p2 = Peserta::create(['user_id' => $u2->id, 'no_identitas' => '1002']);
    NilaiUjian::create([
        'peserta_id' => $p2->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 0,
        'jumlah_benar' => 40,
        'status_kelulusan' => 'DISKUALIFIKASI',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.nilai.index', [
        'jadwal_id' => $jadwal->id,
        'status_kelulusan' => 'DISKUALIFIKASI',
    ]));

    $response->assertOk();
    $response->assertSee('Peserta Diskualifikasi');
    $response->assertDontSee('Peserta Lulus');
});

test('panitia dapat melihat detail hasil ujian peserta', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Detail',
        'deskripsi' => 'Deskripsi',
        'durasi' => 60,
        'passing_grade' => 200,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Detail Nilai',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'status' => 'akan_datang',
    ]);

    $user = User::factory()->create(['name' => 'Peserta Uji Detail', 'email' => 'detail@gmail.com', 'role' => 'peserta']);
    $peserta = Peserta::create(['user_id' => $user->id, 'no_identitas' => '999888777']);
    $nilai = NilaiUjian::create([
        'peserta_id' => $peserta->id,
        'jadwal_id' => $jadwal->id,
        'nilai_akhir' => 450,
        'jumlah_benar' => 90,
        'jumlah_salah' => 10,
        'status_kelulusan' => 'LULUS',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.nilai.detail', $nilai->id));

    $response->assertOk();
    $response->assertSee('Detail Evaluasi');
    $response->assertSee('Peserta Uji Detail');
    $response->assertSee('450');
    $response->assertSee('90');
    $response->assertSee('LULUS');
});

test('panitia dapat mengunduh cetak dokumen pdf rekap nilai', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Cetak',
        'deskripsi' => 'Deskripsi',
        'durasi' => 60,
        'passing_grade' => 200,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Cetak PDF',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'status' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.nilai.cetak', ['jadwal_id' => $jadwal->id]));

    $response->assertOk();
    $response->assertSee('REKAPITULASI NILAI AKHIR UJIAN PESERTA');
    $response->assertSee('Ujian Cetak PDF');
});

test('user selain panitia atau admin tidak dapat mengakses rekap nilai', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($peserta)->get(route('panitia.nilai.index'));
    $response->assertForbidden();
});
