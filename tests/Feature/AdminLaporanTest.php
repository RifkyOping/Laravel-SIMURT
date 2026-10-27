<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dapat mengakses halaman laporan sistem sesuai mockup figma', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.laporan.index'));

    $response->assertOk();
    $response->assertSee('Laporan Sistem');
    $response->assertSee('Ringkasan aktivitas dan performa sistem secara keseluruhan.');
    $response->assertSee('Total Login Hari Ini');
    $response->assertSee('245');
    $response->assertSee('Ujian Aktif');
    $response->assertSee('2');
    $response->assertSee('Pelanggaran Bulan Ini');
    $response->assertSee('12');
    $response->assertSee('Uptime Sistem');
    $response->assertSee('99.8%');
    $response->assertSee('Ringkasan Ujian');
    $response->assertSee('SKD - Batch 1');
    $response->assertSee('Ujian IT & Coding');
    $response->assertSee('Sejarah & TWK');
    $response->assertSee('Distribusi Role Pengguna');
    $response->assertSee('Peserta');
    $response->assertSee('Pengawas');
    $response->assertSee('Panitia');
    $response->assertSee('Admin');
    $response->assertSee('Export Data');
});

test('admin dapat mengunduh export csv laporan sistem', function () {
    $admin = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'admin',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.laporan.export'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('pengguna bukan admin tidak dapat mengakses halaman laporan sistem', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($peserta)->get(route('admin.laporan.index'));

    $response->assertForbidden();
});
