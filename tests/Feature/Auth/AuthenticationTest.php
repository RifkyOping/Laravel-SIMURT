<?php

use App\Models\User;

test('login screen can be rendered with figma mockup elements', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Sistem Manajemen Ujian');
    $response->assertSee('Silakan masuk ke akun Anda');
    $response->assertSee('contoh: peserta@email.com');
    $response->assertSee('Masukkan password');
    $response->assertSee('Masuk');
    $response->assertSee('Registrasi Akun Peserta');
    $response->assertSee('Lupa Password? Hubungi Panitia');
});

test('users can authenticate using the login screen and redirect properly', function () {
    $user = User::factory()->create([
        'status_akun' => 'aktif',
        'role' => 'admin',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors([
        'password' => 'Password yang Anda masukkan salah.',
    ]);
    $response->assertSessionDoesntHaveErrors(['email']);
});

test('login shows only email error when email is wrong but password matches existing account', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password', // matches factory default password
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors([
        'email' => 'Email yang Anda masukkan salah atau tidak terdaftar.',
    ]);
    $response->assertSessionDoesntHaveErrors(['password']);
});

test('login shows both errors when both email and password are wrong', function () {
    $response = $this->post('/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'wrong-password-never-used',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors([
        'email' => 'Email yang Anda masukkan salah.',
        'password' => 'Password yang Anda masukkan salah.',
        'login' => 'Email dan password yang Anda masukkan salah.',
    ]);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
