<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dapat mengakses halaman kelola pengguna dengan tampilan mockup figma', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee('Kelola Pengguna');
    $response->assertSee('Total Pengguna');
    $response->assertSee('Peserta');
    $response->assertSee('Panitia');
    $response->assertSee('Pengawas');
    $response->assertSee('Tambah Pengguna');
    $response->assertSee('Cari nama atau email...');
    $response->assertSee('Role: Semua Role');
    $response->assertSee('Status: Semua Status');
});
