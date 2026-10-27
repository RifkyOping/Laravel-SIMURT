<?php

use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses halaman kelola jadwal ujian dan melihat elemen mockup dengan tepat', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Reguler 1',
        'deskripsi' => 'Paket Soal Ujian',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'akan_datang',
    ]);

    $j1 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => '(SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'akan_datang',
    ]);

    $j2 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'UK (TI)',
        'tanggal' => Carbon::parse('2024-10-18'),
        'jam_mulai' => '10:30:00',
        'jam_selesai' => '12:30:00',
        'durasi' => 120,
        'passing_grade' => 320.00,
        'status' => 'akan_datang',
    ]);

    $j3 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Tes 2',
        'tanggal' => Carbon::parse('2024-10-13'),
        'jam_mulai' => '09:00:00',
        'jam_selesai' => '11:00:00',
        'durasi' => 120,
        'passing_grade' => 310.00,
        'status' => 'berlangsung',
    ]);

    $j4 = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Tes 3',
        'tanggal' => Carbon::parse('2024-10-05'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '09:30:00',
        'durasi' => 90,
        'passing_grade' => 290.00,
        'status' => 'selesai',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.jadwal.index'));

    $response->assertOk();
    $response->assertSee('Kelola Jadwal Ujian');
    $response->assertSee('Buat, ubah, dan hapus jadwal pelaksanaan ujian.');

    // Toolbar Filter
    $response->assertSee('Cari nama ujian...');
    $response->assertSee('Status:');
    $response->assertSee('Semua');

    // Kolom Tabel
    $response->assertSee('Nama Ujian');
    $response->assertSee('Tanggal');
    $response->assertSee('Waktu Mulai');
    $response->assertSee('Waktu Selesai');
    $response->assertSee('Durasi');
    $response->assertSee('Jumlah Peserta');
    $response->assertSee('Status');
    $response->assertSee('Aksi');

    // Baris data mockup
    $response->assertSee('(SKD) - Batch 1');
    $response->assertSee('15 Okt 2024');
    $response->assertSee('08:00 WITA');
    $response->assertSee('10:00 WITA');
    $response->assertSee('UK (TI)');
    $response->assertSee('18 Okt 2024');
    $response->assertSee('10:30 WITA');
    $response->assertSee('Tes 2');
    $response->assertSee('Tes 3');

    // Badges status
    $response->assertSee('Akan Datang');
    $response->assertSee('Berlangsung');
    $response->assertSee('Selesai');

    // Link Aksi
    $response->assertSee('Edit');
    $response->assertSee('Hapus');

    // Pop up modal Edit Jadwal
    $response->assertSee('Edit Jadwal');
    $response->assertSee('Tanggal Pelaksanaan');
    $response->assertSee('Durasi');
    $response->assertSee('Waktu Mulai');
    $response->assertSee('Waktu Selesai');
    $response->assertSee('Batal');
    $response->assertSee('Simpan');
});

test('panitia dapat memfilter jadwal berdasarkan pencarian nama ujian', function () {
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

    Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Khusus TI',
        'tanggal' => Carbon::parse('2024-10-18'),
        'jam_mulai' => '10:00:00',
        'jam_selesai' => '12:00:00',
        'durasi' => 120,
        'status' => 'akan_datang',
    ]);

    Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Umum',
        'tanggal' => Carbon::parse('2024-10-20'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'status' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.jadwal.index', ['search' => 'Khusus TI']));

    $response->assertOk();
    $response->assertSee('Ujian Khusus TI');
    $response->assertDontSee('Seleksi Umum');
});

test('panitia dapat memfilter jadwal berdasarkan status', function () {
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

    Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Jadwal Sedang Aktif',
        'tanggal' => Carbon::parse('2024-10-13'),
        'jam_mulai' => '09:00:00',
        'jam_selesai' => '11:00:00',
        'durasi' => 120,
        'status' => 'berlangsung',
    ]);

    Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Jadwal Sudah Rampung',
        'tanggal' => Carbon::parse('2024-10-05'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '09:30:00',
        'durasi' => 90,
        'status' => 'selesai',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.jadwal.index', ['status' => 'berlangsung']));

    $response->assertOk();
    $response->assertSee('Jadwal Sedang Aktif');
    $response->assertDontSee('Jadwal Sudah Rampung');
});

test('panitia dapat menambahkan jadwal ujian baru', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Baru',
        'deskripsi' => 'Deskripsi',
        'durasi' => 120,
        'passing_grade' => 300,
        'status_paket' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->post(route('panitia.jadwal.store'), [
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Baru Ditambahkan',
        'tanggal' => '2024-11-01',
        'jam_mulai' => '08:00',
        'jam_selesai' => '10:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'akan_datang',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('jadwals', [
        'nama_ujian' => 'Ujian Baru Ditambahkan',
        'status' => 'akan_datang',
        'durasi' => 120,
    ]);
});

test('panitia dapat membuat jadwal dengan menyertakan semua peserta terverifikasi', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $userPeserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);
    $peserta = Peserta::create([
        'user_id' => $userPeserta->id,
        'no_identitas' => '3171999999990001',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Massal',
        'deskripsi' => 'Deskripsi',
        'durasi' => 90,
        'passing_grade' => 250,
        'status_paket' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->post(route('panitia.jadwal.store'), [
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Massal SKD',
        'tanggal' => '2024-12-01',
        'jam_mulai' => '08:00',
        'jam_selesai' => '09:30',
        'durasi' => 90,
        'status' => 'akan_datang',
        'peserta_mode' => 'semua',
    ]);

    $response->assertRedirect();
    $jadwal = Jadwal::where('nama_ujian', 'Ujian Massal SKD')->first();
    expect($jadwal)->not->toBeNull();
    expect($jadwal->pesertas->contains($peserta->id))->toBeTrue();
});

test('panitia dapat mengedit jadwal ujian', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Edit',
        'deskripsi' => 'Deskripsi',
        'durasi' => 120,
        'passing_grade' => 300,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Sebelum Diubah',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'akan_datang',
    ]);

    $editPage = $this->actingAs($panitia)->get(route('panitia.jadwal.edit', $jadwal->id));
    $editPage->assertOk();
    $editPage->assertSee('Perbarui Jadwal Ujian');
    $editPage->assertSee('Ujian Sebelum Diubah');

    $response = $this->actingAs($panitia)->put(route('panitia.jadwal.update', $jadwal->id), [
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Berhasil Diubah',
        'tanggal' => '2024-10-16',
        'jam_mulai' => '09:00',
        'jam_selesai' => '11:00',
        'durasi' => 120,
        'passing_grade' => 310,
        'status' => 'berlangsung',
    ]);

    $response->assertRedirect(route('panitia.jadwal.index'));
    $this->assertDatabaseHas('jadwals', [
        'id' => $jadwal->id,
        'nama_ujian' => 'Ujian Berhasil Diubah',
        'status' => 'berlangsung',
    ]);
});

test('panitia dapat mengedit jadwal melalui pop up modal dan mempertahankan peserta', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Pop Up',
        'deskripsi' => 'Deskripsi',
        'durasi' => 120,
        'passing_grade' => 300,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => '(SKD) - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'akan_datang',
    ]);

    $userPeserta = User::factory()->create(['role' => 'peserta']);
    $peserta = Peserta::create(['user_id' => $userPeserta->id, 'no_identitas' => '1234567890']);
    $jadwal->pesertas()->sync([$peserta->id]);

    // Update via Pop Up modal (only passes tanggal, durasi, jam_mulai, jam_selesai, hidden fields)
    $response = $this->actingAs($panitia)->put(route('panitia.jadwal.update', $jadwal->id), [
        'nama_ujian' => '(SKD) - Batch 1',
        'paket_soal_id' => $paket->id,
        'status' => 'akan_datang',
        'tanggal' => '2024-10-25',
        'jam_mulai' => '08:30',
        'jam_selesai' => '10:30',
        'durasi' => 120,
    ]);

    $response->assertRedirect(route('panitia.jadwal.index'));
    $this->assertDatabaseHas('jadwals', [
        'id' => $jadwal->id,
        'tanggal' => '2024-10-25 00:00:00',
        'jam_mulai' => '08:30',
        'jam_selesai' => '10:30',
    ]);

    // Pastikan relasi peserta tetap terjaga
    expect($jadwal->fresh()->pesertas)->toHaveCount(1);
});

test('panitia dapat menghapus jadwal ujian', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Hapus',
        'deskripsi' => 'Deskripsi',
        'durasi' => 120,
        'passing_grade' => 300,
        'status_paket' => 'akan_datang',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Yang Akan Dihapus',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300,
        'status' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->delete(route('panitia.jadwal.destroy', $jadwal->id));

    $response->assertRedirect(route('panitia.jadwal.index'));
    $this->assertDatabaseMissing('jadwals', [
        'id' => $jadwal->id,
    ]);
});

test('user selain panitia atau admin tidak dapat mengakses halaman kelola jadwal', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($peserta)->get(route('panitia.jadwal.index'));
    $response->assertForbidden();
});
