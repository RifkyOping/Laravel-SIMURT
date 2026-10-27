<?php

test('registration screen can be rendered with figma mockup elements', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Registrasi Akun Peserta');
    $response->assertSee('Lengkapi formulir di bawah untuk mendaftar ujian');
    $response->assertSee('Nama Lengkap');
    $response->assertSee('Masukkan nama lengkap sesuai identitas');
    $response->assertSee('contoh: nama@email.com');
    $response->assertSee('Nomor Identitas');
    $response->assertSee('Masukkan nomor identitas');
    $response->assertSee('Instansi');
    $response->assertSee('Asal instansi');
    $response->assertSee('Buat password (minimal 8 karakter)');
    $response->assertSee('Konfirmasi Password');
    $response->assertSee('Ulangi password');
    $response->assertSee('Daftar Sekarang');
    $response->assertSee('Sudah punya akun?');
    $response->assertSee('Masuk di sini');
});

test('new users can register with valid identification and redirect to menunggu verifikasi', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@example.com',
        'no_identitas' => '3201123456789001',
        'instansi' => 'SMK Negeri 1',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('menunggu.verifikasi', absolute: false));
});
