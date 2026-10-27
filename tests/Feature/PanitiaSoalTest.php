<?php

use App\Models\BankSoal;
use App\Models\PaketSoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses kelola butir soal dan melihat elemen mockup dengan tepat', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'akan_datang',
    ]);

    $soal = BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Manakah di bawah ini yang merupakan dasar negara Republik Indonesia?',
        'opsi_jawaban' => ['A' => 'Pancasila', 'B' => 'UUD 1945'],
        'kunci_jawaban' => 'A',
        'bobot' => 50,
        'aktif' => true,
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.soal.index', $paket->id));

    $response->assertOk();
    $response->assertSee('Bank Soal');
    $response->assertSee('Unggah, kelola, dan atur soal ujian.');

    // 3 Kartu statistik
    $response->assertSee('Total Soal');
    $response->assertSee('Soal Aktif');
    $response->assertSee('Soal Non-Aktif');

    // Toolbar
    $response->assertSee('Cari soal...');
    $response->assertSee('Import Soal');
    $response->assertSee('Tambah Soal');

    // Kolom tabel
    $response->assertSee('No');
    $response->assertSee('Pertanyaan');
    $response->assertSee('Aksi');

    // Data soal & aksi
    $response->assertSee('Manakah di bawah ini yang merupakan dasar negara Republik Indonesia?');
    $response->assertSee('Edit');
    $response->assertSee('Hapus');

    // Modal Tambah Soal
    $response->assertSee('Tulis pertanyaan soal di sini...');
    $response->assertSee('Pilihan Jawaban');
    $response->assertSee('Pilih bulatan untuk menentukan Kunci Jawaban');
    $response->assertSee('Jawaban A');
    $response->assertSee('Jawaban B');
});

test('panitia dapat melakukan pencarian butir soal berdasarkan teks pertanyaan', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'status_paket' => 'akan_datang',
    ]);

    BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Pertanyaan A tentang Pancasila',
        'opsi_jawaban' => ['A' => '1', 'B' => '2'],
        'kunci_jawaban' => 'A',
        'bobot' => 10,
        'aktif' => true,
    ]);

    BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Pertanyaan B tentang Ekonomi',
        'opsi_jawaban' => ['A' => '1', 'B' => '2'],
        'kunci_jawaban' => 'A',
        'bobot' => 10,
        'aktif' => true,
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.soal.index', [$paket->id, 'search' => 'Pancasila']));

    $response->assertOk();
    $response->assertSee('Pertanyaan A tentang Pancasila');
    $response->assertDontSee('Pertanyaan B tentang Ekonomi');
});

test('panitia dapat menambahkan butir soal baru melalui modal', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'status_paket' => 'akan_datang',
    ]);

    $response = $this->actingAs($panitia)->post(route('panitia.soal.store', $paket->id), [
        'pertanyaan' => 'Apakah fungsi dari protokol HTTPS?',
        'opsi_a' => 'Enkripsi data komunikasi web',
        'opsi_b' => 'Mempercepat download',
        'opsi_c' => 'Kompresi gambar',
        'opsi_d' => 'Pengganti alamat IP',
        'opsi_e' => 'Format file teks',
        'kunci_jawaban' => 'B',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('bank_soals', [
        'paket_soal_id' => $paket->id,
        'pertanyaan' => 'Apakah fungsi dari protokol HTTPS?',
        'kunci_jawaban' => 'B',
        'bobot' => 50.00,
    ]);
});

test('panitia dapat memperbarui butir soal', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'status_paket' => 'akan_datang',
    ]);

    $soal = BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Pertanyaan awal',
        'opsi_jawaban' => ['A' => '1', 'B' => '2', 'C' => '3', 'D' => '4', 'E' => '5'],
        'kunci_jawaban' => 'A',
        'bobot' => 20,
        'aktif' => true,
    ]);

    $response = $this->actingAs($panitia)->put(route('panitia.soal.update', [$paket->id, $soal->id]), [
        'pertanyaan' => 'Pertanyaan yang telah direvisi',
        'kategori' => 'Revisi',
        'tingkat_kesulitan' => 'sulit',
        'bobot' => 40,
        'opsi_a' => 'Opsi A baru',
        'opsi_b' => 'Opsi B baru',
        'opsi_c' => 'Opsi C baru',
        'opsi_d' => 'Opsi D baru',
        'opsi_e' => 'Opsi E baru',
        'kunci_jawaban' => 'B',
        'aktif' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('bank_soals', [
        'id' => $soal->id,
        'pertanyaan' => 'Pertanyaan yang telah direvisi',
        'kunci_jawaban' => 'B',
    ]);
});

test('panitia dapat menghapus butir soal', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'status_paket' => 'akan_datang',
    ]);

    $soal = BankSoal::create([
        'paket_soal_id' => $paket->id,
        'user_id' => $panitia->id,
        'pertanyaan' => 'Soal yang akan dihapus',
        'opsi_jawaban' => ['A' => '1', 'B' => '2'],
        'kunci_jawaban' => 'A',
        'bobot' => 10,
        'aktif' => true,
    ]);

    $response = $this->actingAs($panitia)->delete(route('panitia.soal.destroy', [$paket->id, $soal->id]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('bank_soals', ['id' => $soal->id]);
});

test('panitia dapat mengunduh template CSV soal', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.soal.template'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('role non-panitia tidak dapat mengakses kelola butir soal', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'durasi' => 120,
        'status_paket' => 'akan_datang',
    ]);

    $response = $this->actingAs($peserta)->get(route('panitia.soal.index', $paket->id));
    $response->assertForbidden();
});
