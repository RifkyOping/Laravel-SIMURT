<?php

use App\Models\Aktivitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dapat mengakses halaman log aktivitas sistem sesuai mockup figma', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.aktivitas.index'));

    $response->assertOk();
    $response->assertSee('Log Aktivitas Sistem');
    $response->assertSee('Pantau seluruh aktivitas pengguna dalam sistem.');
    $response->assertSee('Tanggal:');
    $response->assertSee('Pengguna: Semua Pengguna');
    $response->assertSee('Jenis: Semua Aktivitas');
    $response->assertSee('IP Address');
});

test('admin dapat memfilter log aktivitas berdasarkan jenis aktivitas', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    Aktivitas::create([
        'user_id' => $admin->id,
        'jenis_aktivitas' => 'Login Berhasil',
        'keterangan' => 'User berhasil login ke sistem',
        'alamat_ip' => '192.168.1.1',
        'waktu' => now(),
    ]);

    Aktivitas::create([
        'user_id' => $admin->id,
        'jenis_aktivitas' => 'Ubah Hak Akses',
        'keterangan' => 'User mengubah konfigurasi hak akses',
        'alamat_ip' => '192.168.1.2',
        'waktu' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.aktivitas.index', ['jenis_aktivitas' => 'Login Berhasil']));

    $response->assertOk();
    $response->assertSee('Login Berhasil');
    $response->assertSee('192.168.1.1');
});
