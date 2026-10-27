<?php

use App\Models\Peserta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses halaman data peserta dan melihat elemen mockup dengan benar', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $peserta1 = User::factory()->create([
        'name' => 'Achmad Fauzi',
        'email' => 'achmad.fauzi@gmail.com',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);
    Peserta::create([
        'user_id' => $peserta1->id,
        'no_identitas' => '3171020908950001',
        'instansi' => 'Umum',
    ]);

    $peserta2 = User::factory()->create([
        'name' => 'Rian Hidayat',
        'email' => 'rian.hid@email.com',
        'role' => 'peserta',
        'status_akun' => 'menunggu',
    ]);
    Peserta::create([
        'user_id' => $peserta2->id,
        'no_identitas' => '3175080101940003',
        'instansi' => 'Umum',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.peserta.index'));

    $response->assertOk();
    $response->assertSee('Data Peserta');
    $response->assertSee('Kelola data peserta yang terdaftar dalam sistem.');
    $response->assertSee('Cari nama atau email...');
    $response->assertSee('Status:');
    $response->assertSee('Export Data');

    // Kolom tabel
    $response->assertSee('Nomor Identitas (NIK)');
    $response->assertSee('Tanggal Daftar');
    $response->assertSee('Status');
    $response->assertSee('Aksi');

    // Data baris
    $response->assertSee('Achmad Fauzi');
    $response->assertSee('achmad.fauzi@gmail.com');
    $response->assertSee('3171020908950001');
    $response->assertSee('Terverifikasi');

    $response->assertSee('Rian Hidayat');
    $response->assertSee('rian.hid@email.com');
    $response->assertSee('3175080101940003');
    $response->assertSee('Menunggu');

    $response->assertSee('Edit');
    $response->assertSee('Hapus');
    $response->assertSee('Menampilkan');
});

test('panitia dapat memfilter data peserta berdasarkan status', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $terverifikasi = User::factory()->create([
        'name' => 'Dewi Lestari',
        'email' => 'dewi.lestari@yahoo.com',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $menunggu = User::factory()->create([
        'name' => 'Rian Hidayat',
        'email' => 'rian.hid@email.com',
        'role' => 'peserta',
        'status_akun' => 'menunggu',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.peserta.index', ['status' => 'menunggu']));

    $response->assertOk();
    $response->assertSee('Rian Hidayat');
    $response->assertDontSee('Dewi Lestari');
});

test('panitia dapat melakukan pencarian data peserta', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $peserta1 = User::factory()->create([
        'name' => 'Achmad Fauzi',
        'email' => 'achmad.fauzi@gmail.com',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $peserta2 = User::factory()->create([
        'name' => 'Fitri Handayani',
        'email' => 'fitri.hnd@yahoo.co.id',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.peserta.index', ['search' => 'Fauzi']));

    $response->assertOk();
    $response->assertSee('Achmad Fauzi');
    $response->assertDontSee('Fitri Handayani');
});

test('panitia dapat mengekspor data peserta ke format CSV', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    User::factory()->create([
        'name' => 'Achmad Fauzi',
        'email' => 'achmad.fauzi@gmail.com',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.peserta.export-csv'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('panitia dapat menghapus peserta ujian', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $peserta = User::factory()->create([
        'name' => 'Hapus Target',
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($panitia)->delete(route('panitia.peserta.destroy', $peserta->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('users', ['id' => $peserta->id]);
});
