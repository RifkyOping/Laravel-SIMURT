<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dapat mengakses dashboard admin dan menampilkan komponen mockup figma dengan benar', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Sistem Administration Panel');
    $response->assertSee('Total Pengguna');
    $response->assertSee('Peserta');
    $response->assertSee('Panitia');
    $response->assertSee('Pengawas');
    $response->assertSee('Daftar Pengguna Terbaru');
    $response->assertSee('Audit Trail & Log Aktivitas Terbaru', false);
    $response->assertSee('Budi Santoso');
    $response->assertSee('Keluar');
});
