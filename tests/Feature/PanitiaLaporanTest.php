<?php

use App\Models\Jadwal;
use App\Models\NilaiUjian;
use App\Models\PaketSoal;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('panitia dapat mengakses halaman laporan ujian dan melihat seluruh elemen mockup figma', function () {
    $panitia = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => '(SKD) - Batch 1',
        'deskripsi' => 'Paket Ujian SKD',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'selesai',
    ]);

    // Buat 48 jadwal dummy agar total entries = 48
    for ($i = 1; $i <= 48; $i++) {
        $namaUjian = match ($i) {
            1 => '(SKD) - Batch 1',
            2 => 'UK (TI)',
            3 => 'Ujian Bahasa Inggris Komparatif',
            4 => 'Ujian Bahasa Inggris Komparatif',
            default => "Tes {$i}",
        };

        Jadwal::create([
            'paket_soal_id' => $paket->id,
            'nama_ujian' => $namaUjian,
            'tanggal' => Carbon::parse('2024-10-15')->subDays($i),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'durasi' => 120,
            'passing_grade' => 300.00,
            'status' => 'selesai',
        ]);
    }

    $response = $this->actingAs($panitia)->get(route('panitia.laporan.index'));

    $response->assertOk();

    // 1. Header & Subtitle
    $response->assertSee('Laporan Ujian');
    $response->assertSee('Laporan aktivitas dan hasil pelaksanaan ujian secara terstruktur.');

    // 2. Toolbar & Filter
    $response->assertSee('Periode:');
    $response->assertSee('Bulan Ini');
    $response->assertSee('Ujian:');
    $response->assertSee('Semua Ujian');
    $response->assertSee('Export PDF');

    // 3. Empat Kartu Metrik Ringkasan
    $response->assertSee('Total Ujian');
    $response->assertSee('12');
    $response->assertSee('Total Peserta');
    $response->assertSee('1,240');
    $response->assertSee('Rata-rata Kelulusan');
    $response->assertSee('78%');
    $response->assertSee('Pelanggaran');
    $response->assertSee('5');

    // 4. Tren Tingkat Kelulusan Per Ujian (%)
    $response->assertSee('Tren Tingkat Kelulusan Per Ujian (%)');
    $response->assertSee('Seleksi Kemampuan Dasar - Batch 1');
    $response->assertSee('80%');
    $response->assertSee('Ujian Kompetensi Bidang (TI)');
    $response->assertSee('72%');
    $response->assertSee('Tes Wawasan Kebangsaan (Susulan)');
    $response->assertSee('50%');
    $response->assertSee('Sertifikasi Profesional Scrum Master');
    $response->assertSee('90%');
    $response->assertSee('Ujian Bahasa Inggris Komparatif');
    $response->assertSee('78%');

    // 5. Header Kolom Tabel
    $response->assertSee('Nama Ujian');
    $response->assertSee('Tanggal');
    $response->assertSee('Peserta');
    $response->assertSee('Rata-rata Nilai');
    $response->assertSee('Kelulusan (%)');
    $response->assertSee('Pelanggaran');
    $response->assertSee('Aksi');

    // 6. Baris Data Mockup
    $response->assertSee('Seleksi Kemampuan Dasar - Batch 1');
    $response->assertSee('15 Okt 2024');
    $response->assertSee('150 Orang');
    $response->assertSee('342');

    $response->assertSee('Ujian Kompetensi Bidang (TI)');
    $response->assertSee('18 Okt 2024');
    $response->assertSee('85 Orang');
    $response->assertSee('382');

    $response->assertSee('Ujian Bahasa Inggris Komparatif');
    $response->assertSee('28 Sep 2024');
    $response->assertSee('200 Orang');
    $response->assertSee('320');

    // 7. Tombol Aksi Detail
    $response->assertSee('Detail');

    // 8. Footer & Pagination Mockup
    $response->assertSee('Menampilkan 1-10 dari 48 entries');
    $response->assertSee('Sebelumnya');
    $response->assertSee('Selanjutnya');
});

test('panitia dapat memfilter laporan ujian berdasarkan ujian tertentu', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Filter Test',
        'deskripsi' => 'Paket Soal',
        'durasi' => 90,
        'passing_grade' => 280.00,
        'status_paket' => 'selesai',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Khusus Filter',
        'tanggal' => Carbon::parse('2024-10-10'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '09:30:00',
        'durasi' => 90,
        'passing_grade' => 280.00,
        'status' => 'selesai',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.laporan.index', [
        'jadwal_id' => $jadwal->id,
    ]));

    $response->assertOk();
    $response->assertSee('Ujian Khusus Filter');
});

test('panitia dapat melihat halaman detail laporan ujian', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Paket Detail Test',
        'deskripsi' => 'Paket Soal',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'selesai',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Seleksi Kemampuan Dasar - Batch 1',
        'tanggal' => Carbon::parse('2024-10-15'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'selesai',
    ]);

    $userPeserta = User::factory()->create(['role' => 'peserta', 'name' => 'Peserta Uji']);
    $peserta = Peserta::create(['user_id' => $userPeserta->id, 'no_identitas' => '3171000000000001']);
    $jadwal->pesertas()->attach($peserta->id);

    NilaiUjian::create([
        'jadwal_id' => $jadwal->id,
        'peserta_id' => $peserta->id,
        'nilai_akhir' => 350.00,
        'jumlah_benar' => 70,
        'jumlah_salah' => 30,
        'status_kelulusan' => 'LULUS',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.laporan.detail', $jadwal->id));

    $response->assertOk();
    $response->assertSee('Detail Laporan');
    $response->assertSee('Seleksi Kemampuan Dasar - Batch 1');
    $response->assertSee('Download Laporan (PDF)');
    $response->assertSee('Distribusi Status Peserta');
    $response->assertSee('Ringkasan hasil berdasarkan status kelulusan');
    $response->assertSee('Log Pelanggaran');
    $response->assertSee('Anomali');
    $response->assertSee('Catatan insiden selama pelaksanaan ujian');
});

test('panitia dapat melihat detail laporan ujian bahasa inggris komparatif sesuai mockup figma', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $paket = PaketSoal::create([
        'nama_paket' => 'Ujian Bahasa Inggris Komparatif',
        'deskripsi' => 'Paket Soal Bahasa Inggris',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status_paket' => 'selesai',
    ]);

    $jadwal = Jadwal::create([
        'paket_soal_id' => $paket->id,
        'nama_ujian' => 'Ujian Bahasa Inggris Komparatif',
        'tanggal' => Carbon::parse('2024-09-28'),
        'jam_mulai' => '08:00:00',
        'jam_selesai' => '10:00:00',
        'durasi' => 120,
        'passing_grade' => 300.00,
        'status' => 'selesai',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.laporan.detail', $jadwal->id));

    $response->assertOk();

    // 1. Header & Navigasi
    $response->assertSee('Kembali');
    $response->assertSee('Detail Laporan: Ujian Bahasa Inggris Komparatif');
    $response->assertSee('Tanggal pelaksanaan: 28 Sep 2024');
    $response->assertSee('Download Laporan (PDF)');

    // 2. 4 Kartu Metrik
    $response->assertSee('Total Peserta');
    $response->assertSee('200');
    $response->assertSee('Peserta terdaftar');

    $response->assertSee('Hadir');
    $response->assertSee('196');
    $response->assertSee('98% dari total peserta');

    $response->assertSee('Rata-rata Nilai');
    $response->assertSee('320');
    $response->assertSee('Skala 0–500');

    $response->assertSee('Total Pelanggaran');
    $response->assertSee('4');
    $response->assertSee('2 kasus diskualifikasi');

    // 3. Distribusi Status Peserta (Donut Chart)
    $response->assertSee('Distribusi Status Peserta');
    $response->assertSee('Ringkasan hasil berdasarkan status kelulusan');
    $response->assertSee('Status');
    $response->assertSee('Lulus');
    $response->assertSee('156 peserta (78%)');
    $response->assertSee('Tidak Lulus');
    $response->assertSee('32 peserta (16%)');
    $response->assertSee('Diskualifikasi');
    $response->assertSee('12 peserta (6%)');

    // 4. Log Pelanggaran & Anomali
    $response->assertSee('Log Pelanggaran');
    $response->assertSee('Anomali');
    $response->assertSee('Catatan insiden selama pelaksanaan ujian');
    $response->assertSee('Waktu Kejadian');
    $response->assertSee('Nama Peserta');
    $response->assertSee('Detail Pelanggaran');
    $response->assertSee('Tindakan');

    $response->assertSee('Andi Pratama');
    $response->assertSee('Menggunakan perangkat seluler aktif selama ujian');
    $response->assertSee('Lestari W.');
    $response->assertSee('Menunjukkan perilaku mencurigakan pada ruang ujian');
    $response->assertSee('Perlu Verifikasi');
    $response->assertSee('Budi Santoso');
    $response->assertSee('Menggunakan catatan tersembunyi di bawah meja');
});

test('panitia dapat melihat halaman export pdf laporan ujian', function () {
    $panitia = User::factory()->create([
        'role' => 'panitia',
        'status_akun' => 'aktif',
    ]);

    $response = $this->actingAs($panitia)->get(route('panitia.laporan.export-pdf'));

    $response->assertOk();
    $response->assertSee('Laporan Eksekutif Pelaksanaan Ujian');
    $response->assertSee('SISTEM MANAJEMEN UJIAN');
    $response->assertSee('Total Ujian');
    $response->assertSee('12');
    $response->assertSee('Tren Tingkat Kelulusan Per Ujian (%)');
    $response->assertSee('Rekapitulasi Pelaksanaan Ujian');
});

test('pengguna bukan panitia tidak dapat mengakses halaman laporan panitia', function () {
    $peserta = User::factory()->create([
        'role' => 'peserta',
        'status_akun' => 'terverifikasi',
    ]);

    $response = $this->actingAs($peserta)->get(route('panitia.laporan.index'));

    $response->assertForbidden();
});
