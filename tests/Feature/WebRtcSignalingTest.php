<?php

use App\Models\Jadwal;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
});

function createDummySesi(?User $userPeserta = null): array
{
    $user = $userPeserta ?? User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $peserta = Peserta::create([
        'user_id' => $user->id,
        'no_identitas' => 'ID-'.uniqid(),
        'nama_lengkap' => 'Peserta '.uniqid(),
        'instansi' => 'SMK 1',
    ]);

    $paket = PaketSoal::create([
        'user_id' => $user->id,
        'nama_paket' => 'Paket Ujian 1',
        'durasi' => 60,
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Akhir',
        'tanggal' => now()->toDateString(),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 60,
        'status' => 'berlangsung',
    ]);

    $sesi = SesiUjian::create([
        'jadwal_id' => $jadwal->id,
        'peserta_id' => $peserta->id,
        'waktu_masuk' => now(),
        'status' => 'berlangsung',
    ]);

    return [$user, $peserta, $sesi];
}

test('tamu tidak dapat mengakses endpoint sinyal webrtc', function () {
    [, , $sesi] = createDummySesi();

    $this->postJson(route('webrtc.signal.send', $sesi), [
        'target' => 'pengawas',
        'type' => 'offer',
    ])->assertUnauthorized();

    $this->getJson(route('webrtc.signal.get', $sesi))->assertUnauthorized();

    $this->postJson(route('webrtc.signal.close', $sesi))->assertUnauthorized();
});

test('peserta dapat mengirim sinyal webrtc ke pengawas untuk sesi miliknya', function () {
    [$userPeserta, , $sesi] = createDummySesi();

    $response = $this->actingAs($userPeserta)->postJson(route('webrtc.signal.send', $sesi), [
        'target' => 'pengawas',
        'type' => 'offer',
        'data' => ['sdp' => 'dummy-sdp-offer-string', 'type' => 'offer'],
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Sinyal WebRTC berhasil dikirim.',
        ]);

    $cached = Cache::get("webrtc:signals:{$sesi->id}:pengawas");
    expect($cached)->toBeArray()
        ->and(count($cached))->toBe(1)
        ->and($cached[0]['type'])->toBe('offer')
        ->and($cached[0]['sender_id'])->toBe($userPeserta->id);
});

test('peserta lain tidak dapat mengakses atau mengirim sinyal webrtc untuk sesi yang bukan miliknya', function () {
    [, , $sesi] = createDummySesi();

    $intruder = User::factory()->create(['role' => 'peserta']);
    Peserta::create([
        'user_id' => $intruder->id,
        'no_identitas' => '9999999999',
        'nama_lengkap' => 'Intruder Peserta',
    ]);

    $this->actingAs($intruder)
        ->postJson(route('webrtc.signal.send', $sesi), [
            'target' => 'pengawas',
            'type' => 'offer',
        ])
        ->assertForbidden();

    $this->actingAs($intruder)
        ->getJson(route('webrtc.signal.get', $sesi))
        ->assertForbidden();
});

test('pengawas dapat mengambil sinyal yang dikirim oleh peserta', function () {
    [$userPeserta, , $sesi] = createDummySesi();

    $pengawas = User::factory()->create(['role' => 'pengawas']);

    // Peserta mengirim sinyal ke pengawas
    $this->actingAs($userPeserta)->postJson(route('webrtc.signal.send', $sesi), [
        'target' => 'pengawas',
        'type' => 'offer',
        'data' => ['sdp' => 'test-sdp'],
    ])->assertOk();

    // Pengawas mengambil antrean sinyal miliknya
    $response = $this->actingAs($pengawas)->getJson(route('webrtc.signal.get', $sesi));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'recipient' => 'pengawas',
        ]);

    $signals = $response->json('signals');
    expect($signals)->toBeArray()
        ->and(count($signals))->toBe(1)
        ->and($signals[0]['type'])->toBe('offer');

    // Pengambilan sinyal menggunakan Cache::pull sehingga antrean berikutnya kosong
    $followUp = $this->actingAs($pengawas)->getJson(route('webrtc.signal.get', $sesi));
    expect($followUp->json('signals'))->toBeEmpty();
});

test('menutup sesi webrtc mengirim sinyal stop_stream ke kedua belah pihak', function () {
    $pengawas = User::factory()->create(['role' => 'pengawas']);
    [, , $sesi] = createDummySesi();

    $response = $this->actingAs($pengawas)->postJson(route('webrtc.signal.close', $sesi));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Sesi WebRTC berhasil ditutup.',
        ]);

    $pesertaSignals = Cache::get("webrtc:signals:{$sesi->id}:peserta");
    expect($pesertaSignals)->toBeArray()
        ->and($pesertaSignals[0]['type'])->toBe('stop_stream');

    $pengawasSignals = Cache::get("webrtc:signals:{$sesi->id}:pengawas");
    expect($pengawasSignals)->toBeArray()
        ->and($pengawasSignals[0]['type'])->toBe('stop_stream');
});
