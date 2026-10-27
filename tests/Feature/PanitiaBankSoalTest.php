<?php

use App\Models\BankSoal;
use App\Models\PaketSoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses halaman bank soal dan melihat elemen mockup dengan tepat', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket1 = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'deskripsi' => 'Paket Seleksi Kemampuan Dasar',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'akan_datang',
    ]);

    $paket2 = PaketSoal::create([
        'nama_paket' => 'UK (TI)',
        'deskripsi' => 'Ujian Kompetensi TI',
        'durasi' => 120,
        'passing_grade' => 320.00,
        'status_paket' => 'berlangsung',
    ]);

    $paket3 = PaketSoal::create([
        'nama_paket' => 'Tes Selesai',
        'deskripsi' => 'Ujian Telah Selesai',
        'durasi' => 90,
        'passing_grade' => 280.00,
        'status_paket' => 'selesai',
    ]);

    // Tambah butir soal dummy
    BankSoal::create([
        'paket_soal_id' => $paket1->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Pertanyaan testing 1',
        'opsi_jawaban' => ['A' => '1', 'B' => '2'],
        'kunci_jawaban' => 'A',
        'bobot' => 10,
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.paket.index'));

    $response->assertOk();
    $response->assertSee('Bank Soal');
    $response->assertSee('Unggah, kelola, dan atur soal ujian.');

    // 3 Kartu statistik
    $response->assertSee('Total Paket Soal');
    $response->assertSee('Paket Soal Berlangsung');
    $response->assertSee('Paket Soal Selesai');

    // Toolbar
    $response->assertSee('Cari Paket Ujian...');
    $response->assertSee('Tambah Paket Soal');

    // Kolom tabel
    $response->assertSee('Nama Paket Soal');
    $response->assertSee('Jumlah Soal');
    $response->assertSee('Durasi');
    $response->assertSee('Status');
    $response->assertSee('Aksi');

    // Baris data
    $response->assertSee('(SKD) - Batch 1');
    $response->assertSee('1 Soal');
    $response->assertSee('120 Menit');
    $response->assertSee('Akan Datang');
    $response->assertSee('UK (TI)');
    $response->assertSee('Berlangsung');
    $response->assertSee('Selesai');

    // Tombol Aksi
    $response->assertSee('Kelola Soal');
    $response->assertSee('Edit');
    $response->assertSee('Hapus');

    // Modal Tambah Paket Soal
    $response->assertSee('misal: Seleksi Kemampuan Dasar (SKD) - Batch 1');
    $response->assertSee('Durasi Ujian (Menit)');
    $response->assertSee('misal: 120');
    $response->assertSee('Pilih Status');
});

test('panitia dapat melakukan pencarian paket soal berdasarkan nama', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'akan_datang',
    ]);

    PaketSoal::create([
        'nama_paket' => 'UK (TI)',
        'durasi' => 120,
        'passing_grade' => 320.00,
        'status_paket' => 'berlangsung',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.paket.index', ['search' => 'SKD']));

    $response->assertOk();
    $response->assertSee('(SKD) - Batch 1');
    $response->assertDontSee('UK (TI)');
});

test('panitia dapat menambahkan paket soal baru melalui form modal', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($panitia)->post(route('panitia.paket.store'), [
        'nama_paket' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'durasi' => 120,
        'status_paket' => 'akan_datang',
    ]);

    $response->assertRedirect(route('panitia.paket.index'));
    $this->assertDatabaseHas('paket_soals', [
        'nama_paket' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'akan_datang',
    ]);
});

test('panitia dapat memperbarui paket soal', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Lama',
        'durasi' => 90,
        'passing_grade' => 250.00,
        'status_paket' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->put(route('panitia.paket.update', $paket), [
        'nama_paket' => 'Paket Revisi',
        'deskripsi' => 'Deskripsi paket revisi',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'berlangsung',
    ]);

    $response->assertRedirect(route('panitia.paket.index'));
    $this->assertDatabaseHas('paket_soals', [
        'id' => $paket->id,
        'nama_paket' => 'Paket Revisi',
        'durasi' => 120,
        'status_paket' => 'berlangsung',
    ]);
});

test('panitia dapat menghapus paket soal beserta bank soal terkait', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Akan Dihapus',
        'durasi' => 60,
        'passing_grade' => 200.00,
        'status_paket' => 'selesai',
    ]);

    $soal = BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Soal yang akan terhapus',
        'opsi_jawaban' => ['A' => '1', 'B' => '2'],
        'kunci_jawaban' => 'A',
        'bobot' => 10,
    ]);

    $response = $this->actingAs($panitia)->delete(route('panitia.paket.destroy', $paket));

    $response->assertRedirect(route('panitia.paket.index'));
    $this->assertDatabaseMissing('paket_soals', ['id' => $paket->id]);
    $this->assertDatabaseMissing('bank_soals', ['id' => $soal->id]);
});

test('role non-panitia tidak dapat mengakses halaman bank soal panitia', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($peserta)->get(route('panitia.paket.index'));
    $response->assertForbidden();
});
