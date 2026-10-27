<?php

use App\Models\Peserta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses halaman data pengawas dan melihat elemen mockup dengan benar', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $pengawas = User::factory()->create([
        'name' => 'Ratih Purwasih',
        'email' => 'ratih@pengawas.id',
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);
    Peserta::create([
        'user_id' => $pengawas->id,
        'no_identitas' => '3171020908950099',
        'instansi' => 'Pengawas Ujian',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.pengawas.index'));

    $response->assertOk();
    $response->assertSee('Data Pengawas');
    $response->assertSee('Kelola data pengawas yang terdaftar dalam sistem');
    $response->assertSee('Tambah Pengawas');
    $response->assertSee('Cari nama atau email...');
    $response->assertSee('Status:');
    $response->assertSee('Export Data');

    // Kolom tabel
    $response->assertSee('Nomor Identitas (NIK)');
    $response->assertSee('Tanggal Daftar');
    $response->assertSee('Status');
    $response->assertSee('Aksi');

    // Baris data
    $response->assertSee('Ratih Purwasih');
    $response->assertSee('ratih@pengawas.id');
    $response->assertSee('3171020908950099');
    $response->assertSee('Terverifikasi');

    $response->assertSee('Edit');
    $response->assertSee('Hapus');
    $response->assertSee('Menampilkan');
});

test('panitia dapat menambahkan pengawas baru', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($panitia)->post(route('panitia.pengawas.store'), [
        'name' => 'Joko Widodo Pengawas',
        'email' => 'joko.pengawas@simurt.id',
        'no_identitas' => '3271012345670001',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'email' => 'joko.pengawas@simurt.id',
        'role' => 'pengawas',
    ]);
    $this->assertDatabaseHas('pesertas', [
        'no_identitas' => '3271012345670001',
    ]);
});

test('panitia dapat memfilter pengawas berdasarkan status dan melakukan pencarian', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $aktif = User::factory()->create([
        'name' => 'Pengawas Aktif',
        'email' => 'aktif@pengawas.id',
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $nonaktif = User::factory()->create([
        'name' => 'Pengawas Nonaktif',
        'email' => 'nonaktif@pengawas.id',
        'role' => 'pengawas',
        'status_akun' => 'nonaktif',
    ]);

    // Test filter status
    $resStatus = $this->actingAs($panitia)->get(route('panitia.pengawas.index', ['status' => 'terverifikasi']));
    $resStatus->assertOk();
    $resStatus->assertSee('Pengawas Aktif');
    $resStatus->assertDontSee('Pengawas Nonaktif');

    // Test search
    $resSearch = $this->actingAs($panitia)->get(route('panitia.pengawas.index', ['search' => 'Nonaktif']));
    $resSearch->assertOk();
    $resSearch->assertSee('Pengawas Nonaktif');
    $resSearch->assertDontSee('Pengawas Aktif');
});

test('panitia dapat mengekspor data pengawas ke format CSV', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    User::factory()->create([
        'name' => 'Pengawas CSV Test',
        'email' => 'csv.pengawas@simurt.id',
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.pengawas.export-csv'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('panitia dapat memperbarui dan menghapus data pengawas', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $pengawas = User::factory()->create([
        'name' => 'Pengawas Lama',
        'email' => 'lama@pengawas.id',
        'role' => 'pengawas',
        'status_akun' => 'aktif',
    ]);

    // Update
    $updateRes = $this->actingAs($panitia)->put(route('panitia.pengawas.update', $pengawas->id), [
        'name' => 'Pengawas Baru',
        'email' => 'baru@pengawas.id',
        'no_identitas' => '3271012345679999',
        'status_akun' => 'aktif',
    ]);

    $updateRes->assertRedirect(route('panitia.pengawas.index'));
    $this->assertDatabaseHas('users', [
        'id' => $pengawas->id,
        'name' => 'Pengawas Baru',
        'email' => 'baru@pengawas.id',
    ]);

    // Delete
    $deleteRes = $this->actingAs($panitia)->delete(route('panitia.pengawas.destroy', $pengawas->id));
    $deleteRes->assertRedirect();
    $this->assertDatabaseMissing('users', ['id' => $pengawas->id]);
});
