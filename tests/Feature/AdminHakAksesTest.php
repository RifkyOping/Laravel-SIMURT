<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dapat mengakses halaman manajemen hak akses sesuai mockup figma', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.hak-akses.index'));

    $response->assertOk();
    $response->assertSee('Manajemen Hak Akses');
    $response->assertSee('Tentukan hak akses setiap pengguna berdasarkan role.');
    $response->assertSee('Peserta');
    $response->assertSee('Panitia');
    $response->assertSee('Pengawas');
    $response->assertSee('Admin');
    $response->assertSee('Konfigurasi Izin: Panitia');

    // Pastikan 9 modul sistem dari mockup tampil
    $response->assertSee('Dashboard');
    $response->assertSee('Data Peserta');
    $response->assertSee('Jadwal Ujian');
    $response->assertSee('Bank Soal');
    $response->assertSee('Ruang Ujian');
    $response->assertSee('Monitoring');
    $response->assertSee('Hasil Ujian');
    $response->assertSee('Laporan');
    $response->assertSee('Log Aktivitas');

    // Kolom tindakan
    $response->assertSee('Lihat');
    $response->assertSee('Tambah');
    $response->assertSee('Edit');
    $response->assertSee('Hapus');
    $response->assertSee('Simpan Perubahan');
});

test('admin dapat memperbarui hak akses untuk role panitia', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.hak-akses.update'), [
        'role' => 'panitia',
        'permissions' => [
            'Dashboard' => ['can_view' => '1'],
            'Data Peserta' => ['can_view' => '1', 'can_create' => '1', 'can_update' => '1'],
            'Jadwal Ujian' => ['can_view' => '1', 'can_create' => '1', 'can_update' => '1', 'can_delete' => '1'],
        ],
    ]);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('hak_akses', [
        'role' => 'panitia',
        'modul' => 'Dashboard',
        'can_view' => true,
        'can_create' => false,
    ]);

    $this->assertDatabaseHas('hak_akses', [
        'role' => 'panitia',
        'modul' => 'Jadwal Ujian',
        'can_view' => true,
        'can_create' => true,
        'can_update' => true,
        'can_delete' => true,
    ]);
});
