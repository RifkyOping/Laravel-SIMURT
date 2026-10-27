<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\AdminHakAksesController;
use App\Models\Aktivitas;
use App\Models\BankSoal;
use App\Models\HakAkses;
use App\Models\Jadwal;
use App\Models\NilaiUjian;
use App\Models\PaketSoal;
use App\Models\Pelanggaran;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin (Budi Santoso)
        $admin = User::firstOrCreate(
            ['email' => 'admin@simurt.id'],
            [
                'name' => 'Budi Santoso',
                'username' => 'admin_simurt',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status_akun' => 'aktif',
            ]
        );

        // 2. Akun Panitia (Hendra Wijaya)
        $panitia = User::firstOrCreate(
            ['email' => 'hendra@panitia.id'],
            [
                'name' => 'Hendra Wijaya',
                'username' => 'hendra_panitia',
                'password' => Hash::make('password123'),
                'role' => 'panitia',
                'status_akun' => 'aktif',
            ]
        );

        // Alias panitia@simurt.id agar akun lama tetap ada
        User::updateOrCreate(
            ['email' => 'panitia@simurt.id'],
            [
                'name' => 'Budi Santoso',
                'username' => 'panitia_simurt',
                'password' => Hash::make('password123'),
                'role' => 'panitia',
                'status_akun' => 'aktif',
            ]
        );

        // 3. Akun Pengawas (Ratih Purwasih)
        $pengawas1 = User::firstOrCreate(
            ['email' => 'ratih@pengawas.id'],
            [
                'name' => 'Ratih Purwasih',
                'username' => 'ratih_pengawas',
                'password' => Hash::make('password123'),
                'role' => 'pengawas',
                'status_akun' => 'aktif',
            ]
        );

        $pengawas2 = User::firstOrCreate(
            ['email' => 'pengawas@simurt.id'],
            [
                'name' => 'Budi Santoso',
                'username' => 'pengawas_utama',
                'password' => Hash::make('password123'),
                'role' => 'pengawas',
                'status_akun' => 'aktif',
            ]
        );

        // 4. Akun Peserta (Terverifikasi, Menunggu, Ditolak)
        $userPeserta1 = User::firstOrCreate(
            ['email' => 'peserta@simurt.id'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi_santoso',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'status_akun' => 'terverifikasi',
            ]
        );
        $peserta1 = Peserta::firstOrCreate(
            ['user_id' => $userPeserta1->id],
            [
                'no_identitas' => '3171012345670001',
                'instansi' => 'Universitas Indonesia',
            ]
        );

        $userPesertaRian = User::updateOrCreate(
            ['email' => 'rian.hid@email.com'],
            [
                'name' => 'Rian Hidayat',
                'username' => 'rian_hidayat',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'status_akun' => 'menunggu',
                'created_at' => Carbon::parse('2024-10-12 09:00:00'),
            ]
        );
        Peserta::firstOrCreate(
            ['user_id' => $userPesertaRian->id],
            [
                'no_identitas' => '3171012345670010',
                'instansi' => 'Umum',
            ]
        );

        $userPesertaSitiA = User::updateOrCreate(
            ['email' => 'siti.aminah@email.com'],
            [
                'name' => 'Siti Aminah',
                'username' => 'siti_aminah_p',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'status_akun' => 'menunggu',
                'created_at' => Carbon::parse('2024-10-12 10:30:00'),
            ]
        );
        Peserta::firstOrCreate(
            ['user_id' => $userPesertaSitiA->id],
            [
                'no_identitas' => '3171012345670011',
                'instansi' => 'Umum',
            ]
        );

        $userPeserta2 = User::firstOrCreate(
            ['email' => 'peserta2@simurt.id'],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'siti_nur',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'status_akun' => 'menunggu',
            ]
        );
        Peserta::firstOrCreate(
            ['user_id' => $userPeserta2->id],
            [
                'no_identitas' => '3171012345670002',
                'instansi' => 'Institut Teknologi Bandung',
            ]
        );

        $userPeserta3 = User::firstOrCreate(
            ['email' => 'peserta3@simurt.id'],
            [
                'name' => 'Doni Prasetyo',
                'username' => 'doni_pras',
                'password' => Hash::make('password123'),
                'role' => 'peserta',
                'status_akun' => 'ditolak',
            ]
        );
        Peserta::firstOrCreate(
            ['user_id' => $userPeserta3->id],
            [
                'no_identitas' => '3171012345670003',
                'instansi' => 'Universitas Gadjah Mada',
            ]
        );

        // Tambahan peserta terverifikasi untuk simulasi monitoring ujian
        $pesertaDummy = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@simurt.id', 'nik' => '3171012345670004'],
            ['name' => 'Citra Lestari', 'email' => 'citra@simurt.id', 'nik' => '3171012345670005'],
            ['name' => 'Dewi Anggraini', 'email' => 'dewi@simurt.id', 'nik' => '3171012345670006'],
        ];

        $pesertaObjList = [$peserta1];

        foreach ($pesertaDummy as $pd) {
            $u = User::firstOrCreate(
                ['email' => $pd['email']],
                [
                    'name' => $pd['name'],
                    'username' => strstr($pd['email'], '@', true),
                    'password' => Hash::make('password123'),
                    'role' => 'peserta',
                    'status_akun' => 'terverifikasi',
                ]
            );
            $p = Peserta::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'no_identitas' => $pd['nik'],
                    'instansi' => 'SMK / Sederajat',
                ]
            );
            $pesertaObjList[] = $p;
        }

        // 5. Paket Soal & Bank Soal Mockup Screen-Bank-Paket-Soal
        $mockupPaketData = [
            [
                'nama_paket' => '(SKD) - Batch 1',
                'deskripsi' => 'Paket Seleksi Kemampuan Dasar Batch 1',
                'durasi' => 120,
                'passing_grade' => 300.00,
                'status_paket' => 'akan_datang',
                'target_soal' => 150,
                'created_at' => Carbon::parse('2024-10-12 10:00:00'),
            ],
            [
                'nama_paket' => 'UK (TI)',
                'deskripsi' => 'Ujian Kompetensi Teknologi Informasi',
                'durasi' => 120,
                'passing_grade' => 320.00,
                'status_paket' => 'akan_datang',
                'target_soal' => 100,
                'created_at' => Carbon::parse('2024-10-12 09:50:00'),
            ],
            [
                'nama_paket' => 'Tes 1',
                'deskripsi' => 'Simulasi Potensi Akademik',
                'durasi' => 120,
                'passing_grade' => 280.00,
                'status_paket' => 'akan_datang',
                'target_soal' => 50,
                'created_at' => Carbon::parse('2024-10-12 09:40:00'),
            ],
            [
                'nama_paket' => 'Tes 2',
                'deskripsi' => 'Ujian Seleksi Kemampuan Bidang',
                'durasi' => 120,
                'passing_grade' => 310.00,
                'status_paket' => 'berlangsung',
                'target_soal' => 100,
                'created_at' => Carbon::parse('2024-10-12 09:30:00'),
            ],
            [
                'nama_paket' => 'Tes 3',
                'deskripsi' => 'Ujian Pengetahuan Umum',
                'durasi' => 90,
                'passing_grade' => 290.00,
                'status_paket' => 'selesai',
                'target_soal' => 50,
                'created_at' => Carbon::parse('2024-10-12 09:20:00'),
            ],
            [
                'nama_paket' => 'Tes 4',
                'deskripsi' => 'Ujian Tambahan Sesi 1',
                'durasi' => 90,
                'passing_grade' => 300.00,
                'status_paket' => 'berlangsung',
                'target_soal' => 20,
                'created_at' => Carbon::parse('2024-10-10 09:00:00'),
            ],
            [
                'nama_paket' => 'Tes 5',
                'deskripsi' => 'Ujian Tambahan Sesi 2',
                'durasi' => 90,
                'passing_grade' => 300.00,
                'status_paket' => 'berlangsung',
                'target_soal' => 20,
                'created_at' => Carbon::parse('2024-10-10 08:50:00'),
            ],
            [
                'nama_paket' => 'Tes 6',
                'deskripsi' => 'Ujian Tambahan Sesi 3',
                'durasi' => 90,
                'passing_grade' => 300.00,
                'status_paket' => 'berlangsung',
                'target_soal' => 20,
                'created_at' => Carbon::parse('2024-10-10 08:40:00'),
            ],
            [
                'nama_paket' => 'Tes 7',
                'deskripsi' => 'Ujian Tambahan Sesi 4',
                'durasi' => 90,
                'passing_grade' => 300.00,
                'status_paket' => 'selesai',
                'target_soal' => 20,
                'created_at' => Carbon::parse('2024-10-10 08:30:00'),
            ],
            [
                'nama_paket' => 'Tes 8',
                'deskripsi' => 'Ujian Rekap Selesai',
                'durasi' => 90,
                'passing_grade' => 300.00,
                'status_paket' => 'selesai',
                'target_soal' => 20,
                'created_at' => Carbon::parse('2024-10-10 08:20:00'),
            ],
        ];

        $paket1 = null;
        foreach ($mockupPaketData as $mpd) {
            $pkt = PaketSoal::updateOrCreate(
                ['nama_paket' => $mpd['nama_paket']],
                [
                    'deskripsi' => $mpd['deskripsi'],
                    'durasi' => $mpd['durasi'],
                    'passing_grade' => $mpd['passing_grade'],
                    'status_paket' => $mpd['status_paket'],
                    'created_at' => $mpd['created_at'],
                    'updated_at' => $mpd['created_at'],
                ]
            );

            if ($pkt->nama_paket === '(SKD) - Batch 1') {
                $paket1 = $pkt;
            }

            // Pastikan jumlah bank soal sesuai target_soal
            $currentSoalCount = $pkt->bankSoals()->count();
            if ($currentSoalCount < $mpd['target_soal']) {
                $diffSoal = $mpd['target_soal'] - $currentSoalCount;
                $soalBatch = [];
                for ($s = 1; $s <= $diffSoal; $s++) {
                    $soalBatch[] = [
                        'paket_soal_id' => $pkt->id,
                        'user_id' => $panitia->id,
                        'tipe_soal' => 'pilihan_ganda',
                        'pertanyaan' => 'Butir soal nomor '.($currentSoalCount + $s)." untuk paket {$pkt->nama_paket}",
                        'opsi_jawaban' => json_encode(['A' => 'Pilihan A', 'B' => 'Pilihan B', 'C' => 'Pilihan C', 'D' => 'Pilihan D', 'E' => 'Pilihan E']),
                        'kunci_jawaban' => ['A', 'B', 'C', 'D', 'E'][($s % 5)],
                        'bobot' => 2.00,
                        'kategori' => 'Kompetensi Umum',
                        'tingkat_kesulitan' => 'sedang',
                        'aktif' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                foreach (array_chunk($soalBatch, 100) as $chunk) {
                    BankSoal::insert($chunk);
                }
            }
        }

        $soalList = [
            [
                'pertanyaan' => 'Manakah di bawah ini yang merupakan dasar negara Republik Indonesia?',
                'opsi' => ['A' => 'Pancasila', 'B' => 'UUD 1945', 'C' => 'Bhinneka Tunggal Ika', 'D' => 'NKRI', 'E' => 'Ketetapan MPR'],
                'kunci' => 'A',
                'bobot' => 50.00,
                'kategori' => 'Wawasan Kebangsaan',
            ],
            [
                'pertanyaan' => 'Sebutkan dan jelaskan tiga pilar utama dalam konsep Trias Politica!',
                'opsi' => ['A' => 'Legislatif, Eksekutif, Yudikatif', 'B' => 'Presiden, DPR, MPR', 'C' => 'Polisi, Jaksa, Hakim', 'D' => 'Pusat, Provinsi, Daerah', 'E' => 'Militer, Sipil, Yudisial'],
                'kunci' => 'A',
                'bobot' => 50.00,
                'kategori' => 'Tata Negara',
            ],
            [
                'pertanyaan' => 'Undang-Undang Dasar 1945 disahkan pada tanggal...',
                'opsi' => ['A' => '17 Agustus 1945', 'B' => '18 Agustus 1945', 'C' => '19 Agustus 1945', 'D' => '20 Agustus 1945', 'E' => '22 Agustus 1945'],
                'kunci' => 'B',
                'bobot' => 50.00,
                'kategori' => 'Sejarah Nasional',
            ],
            [
                'pertanyaan' => 'Apa yang dimaksud dengan kebijakan fiskal kontraktif?',
                'opsi' => ['A' => 'Kebijakan menurunkan pajak dan menambah belanja', 'B' => 'Kebijakan menaikkan pajak dan mengurangi pengeluaran pemerintah', 'C' => 'Kebijakan mencetak uang baru', 'D' => 'Kebijakan menurunkan suku bunga acuan', 'E' => 'Kebijakan subsidi impor barang pokok'],
                'kunci' => 'B',
                'bobot' => 50.00,
                'kategori' => 'Ekonomi',
            ],
            [
                'pertanyaan' => 'Tuliskan implementasi algoritma pencarian biner dalam bahasa Python!',
                'opsi' => ['A' => 'def binary_search(arr, x): ...', 'B' => 'def linear_search(arr, x): ...', 'C' => 'def bubble_sort(arr): ...', 'D' => 'def quick_sort(arr): ...', 'E' => 'def merge_sort(arr): ...'],
                'kunci' => 'A',
                'bobot' => 50.00,
                'kategori' => 'Teknologi Informasi',
            ],
            [
                'pertanyaan' => 'Ciri utama dari ekosistem padang rumput adalah...',
                'opsi' => ['A' => 'Curah hujan sangat tinggi sepanjang tahun', 'B' => 'Didominasi oleh vegetasi rumput dengan curah hujan terbatas', 'C' => 'Suhu selalu di bawah titik beku', 'D' => 'Hanya dihuni oleh hewan nocturnal', 'E' => 'Banyak pohon berdaun jarum'],
                'kunci' => 'B',
                'bobot' => 50.00,
                'kategori' => 'Biologi Umum',
            ],
            [
                'pertanyaan' => 'Pernyataan: "Semua bunga mawar berduri. Beberapa mawar berwarna merah." Kesimpulan yang tepat adalah?',
                'opsi' => ['A' => 'Semua yang berduri berwarna merah', 'B' => 'Beberapa yang berduri berwarna merah', 'C' => 'Semua yang berwarna merah adalah mawar', 'D' => 'Tidak ada mawar yang berduri', 'E' => 'Semua mawar tidak berduri'],
                'kunci' => 'B',
                'bobot' => 50.00,
                'kategori' => 'Silogisme Logika',
            ],
            [
                'pertanyaan' => 'Pada basis data relasional, constraint UNIQUE berfungsi untuk?',
                'opsi' => ['A' => 'Mencegah nilai null saja', 'B' => 'Memastikan semua nilai dalam kolom berbeda', 'C' => 'Menghapus data otomatis', 'D' => 'Mengenkripsi password', 'E' => 'Mengurutkan data abjad'],
                'kunci' => 'B',
                'bobot' => 50.00,
                'kategori' => 'Basis Data',
            ],
            [
                'pertanyaan' => 'Sinonim dari kata "KREDIBILITAS" adalah?',
                'opsi' => ['A' => 'Keberanian', 'B' => 'Kecerdikan', 'C' => 'Dapat dipercaya', 'D' => 'Kekayaan', 'E' => 'Kecepatan'],
                'kunci' => 'C',
                'bobot' => 50.00,
                'kategori' => 'Verbal',
            ],
            [
                'pertanyaan' => 'Sebuah mobil menempuh jarak 180 km dalam waktu 3 jam. Berapakah kecepatan rata-rata mobil tersebut?',
                'opsi' => ['A' => '50 km/jam', 'B' => '55 km/jam', 'C' => '60 km/jam', 'D' => '65 km/jam', 'E' => '70 km/jam'],
                'kunci' => 'C',
                'bobot' => 50.00,
                'kategori' => 'Aritmatika',
            ],
        ];

        foreach ($soalList as $idx => $s) {
            BankSoal::updateOrCreate(
                [
                    'paket_soal_id' => $paket1->id,
                    'pertanyaan' => $s['pertanyaan'],
                ],
                [
                    'user_id' => $panitia->id,
                    'tipe_soal' => 'pilihan_ganda',
                    'opsi_jawaban' => $s['opsi'],
                    'kunci_jawaban' => $s['kunci'],
                    'bobot' => $s['bobot'],
                    'kategori' => $s['kategori'],
                    'tingkat_kesulitan' => 'sedang',
                    'aktif' => true,
                    'created_at' => Carbon::now()->subSeconds($idx * 5),
                    'updated_at' => Carbon::now()->subSeconds($idx * 5),
                ]
            );
        }

        // 6. Jadwal Ujian
        // 6. Jadwal Ujian Mockup screen-kelola-jadwal-ujian
        $mockupJadwalsData = [
            [
                'nama_ujian' => 'Seleksi Kemampuan Dasar (SKD) - Batch 1',
                'paket_nama' => '(SKD) - Batch 1',
                'tanggal' => Carbon::parse('2024-10-15'),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:00:00',
                'durasi' => 120,
                'passing_grade' => 300.00,
                'status' => 'berlangsung',
                'target_peserta' => 150,
            ],
            [
                'nama_ujian' => 'UK (TI)',
                'paket_nama' => 'UK (TI)',
                'tanggal' => Carbon::parse('2024-10-18'),
                'jam_mulai' => '10:30:00',
                'jam_selesai' => '12:30:00',
                'durasi' => 120,
                'passing_grade' => 320.00,
                'status' => 'akan_datang',
                'target_peserta' => 85,
            ],
            [
                'nama_ujian' => 'Ujian Bahasa Inggris Komparatif',
                'paket_nama' => 'Tes 1',
                'tanggal' => Carbon::parse('2024-09-28'),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:00:00',
                'durasi' => 120,
                'passing_grade' => 300.00,
                'status' => 'selesai',
                'target_peserta' => 200,
            ],
            [
                'nama_ujian' => 'Ujian Bahasa Inggris Komparatif',
                'paket_nama' => 'Tes 2',
                'tanggal' => Carbon::parse('2024-09-28'),
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '15:00:00',
                'durasi' => 120,
                'passing_grade' => 300.00,
                'status' => 'selesai',
                'target_peserta' => 200,
            ],
            [
                'nama_ujian' => 'Tes Wawasan Kebangsaan (Susulan)',
                'paket_nama' => 'Tes 3',
                'tanggal' => Carbon::parse('2024-10-20'),
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '10:30:00',
                'durasi' => 90,
                'passing_grade' => 290.00,
                'status' => 'selesai',
                'target_peserta' => 60,
            ],
            [
                'nama_ujian' => 'Sertifikasi Profesional Scrum Master',
                'paket_nama' => 'Tes 4',
                'tanggal' => Carbon::parse('2024-10-25'),
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '14:30:00',
                'durasi' => 90,
                'passing_grade' => 320.00,
                'status' => 'akan_datang',
                'target_peserta' => 50,
            ],
            [
                'nama_ujian' => 'Tes Wawasan Kebangsaan (TWK) - Kelas Susulan',
                'paket_nama' => 'Tes 1',
                'tanggal' => Carbon::parse('2024-10-22'),
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '15:00:00',
                'durasi' => 120,
                'passing_grade' => 280.00,
                'status' => 'akan_datang',
                'target_peserta' => 12,
            ],
            [
                'nama_ujian' => 'Simulasi Ujian Mandiri & Deteksi Kamera',
                'paket_nama' => 'Tes 2',
                'tanggal' => Carbon::parse('2024-10-12'),
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '11:00:00',
                'durasi' => 120,
                'passing_grade' => 310.00,
                'status' => 'selesai',
                'target_peserta' => 450,
            ],
            [
                'nama_ujian' => 'Tes 3',
                'paket_nama' => 'Tes 3',
                'tanggal' => Carbon::parse('2024-10-05'),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:30:00',
                'durasi' => 90,
                'passing_grade' => 290.00,
                'status' => 'selesai',
                'target_peserta' => 40,
            ],
        ];

        // Bersihkan jadwal lama agar ID urut 1 - 5 untuk 5 mockup utama
        Schema::disableForeignKeyConstraints();
        DB::table('jadwal_peserta')->truncate();
        DB::table('jadwal_pengawas')->truncate();
        Jadwal::truncate();
        Schema::enableForeignKeyConstraints();

        $seededJadwals = [];
        foreach ($mockupJadwalsData as $mjd) {
            $targetPaket = PaketSoal::where('nama_paket', $mjd['paket_nama'])->first() ?? $paket1;
            $j = Jadwal::create([
                'paket_soal_id' => $targetPaket->id,
                'nama_ujian' => $mjd['nama_ujian'],
                'tanggal' => $mjd['tanggal'],
                'jam_mulai' => $mjd['jam_mulai'],
                'jam_selesai' => $mjd['jam_selesai'],
                'durasi' => $mjd['durasi'],
                'passing_grade' => $mjd['passing_grade'],
                'status' => $mjd['status'],
            ]);
            $j->pengawas()->sync([$pengawas1->id, $pengawas2->id]);
            $seededJadwals[$mjd['nama_ujian']] = [
                'jadwal' => $j,
                'target_peserta' => $mjd['target_peserta'],
            ];
        }

        // 7. Hak Akses default (sesuai matriks mockup Screen-Hak-Akses)
        foreach (AdminHakAksesController::DEFAULT_PERMISSIONS as $role => $modulPerms) {
            foreach ($modulPerms as $modul => $perm) {
                HakAkses::updateOrCreate(
                    ['role' => $role, 'modul' => $modul],
                    $perm
                );
            }
        }

        // 9. Batch data pengguna demo (Peserta 10, Panitia 10, Pengawas 10)
        $defaultHashedPassword = Hash::make('password123');

        $currentPanitiaCount = User::where('role', 'panitia')->count();
        if ($currentPanitiaCount < 10) {
            $panitiaBatch = [];
            for ($i = $currentPanitiaCount + 1; $i <= 10; $i++) {
                $panitiaBatch[] = [
                    'name' => "Panitia Ujian {$i}",
                    'username' => "panitia_{$i}",
                    'email' => "panitia{$i}@simurt.id",
                    'password' => $defaultHashedPassword,
                    'role' => 'panitia',
                    'status_akun' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            User::insert($panitiaBatch);
        }

        $currentPengawasCount = User::where('role', 'pengawas')->count();
        if ($currentPengawasCount < 10) {
            $pengawasBatch = [];
            for ($i = $currentPengawasCount + 1; $i <= 10; $i++) {
                $pengawasBatch[] = [
                    'name' => "Pengawas Ruang {$i}",
                    'username' => "pengawas_{$i}",
                    'email' => "pengawas{$i}@simurt.id",
                    'password' => $defaultHashedPassword,
                    'role' => 'pengawas',
                    'status_akun' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            User::insert($pengawasBatch);
        }

        // Pastikan seluruh akun pengawas ditugaskan ke jadwal ujian agar bisa langsung masuk ke ruang ujian
        $allPengawasIds = User::where('role', 'pengawas')->pluck('id')->toArray();
        foreach (Jadwal::all() as $eachJadwal) {
            $eachJadwal->pengawas()->syncWithoutDetaching($allPengawasIds);
        }

        $currentPesertaCount = User::where('role', 'peserta')->count();
        if ($currentPesertaCount < 10) {
            $pesertaBatch = [];
            for ($i = $currentPesertaCount + 1; $i <= 10; $i++) {
                $pesertaBatch[] = [
                    'name' => "Peserta Seleksi {$i}",
                    'username' => "peserta_{$i}",
                    'email' => "peserta{$i}@ujian.simurt.id",
                    'password' => $defaultHashedPassword,
                    'role' => 'peserta',
                    'status_akun' => 'terverifikasi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            User::insert($pesertaBatch);
        }

        // Pastikan tabel pesertas terisi untuk relasi jadwal ujian
        $currentPesertaModelCount = Peserta::count();
        if ($currentPesertaModelCount < 10) {
            $pesertaUsers = User::where('role', 'peserta')
                ->where('status_akun', 'terverifikasi')
                ->whereDoesntHave('peserta')
                ->take(10 - $currentPesertaModelCount)
                ->get();

            $pesertaInsert = [];
            foreach ($pesertaUsers as $pu) {
                $pesertaInsert[] = [
                    'user_id' => $pu->id,
                    'no_identitas' => '3171'.str_pad($pu->id, 12, '0', STR_PAD_LEFT),
                    'instansi' => 'Peserta Ujian',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (! empty($pesertaInsert)) {
                Peserta::insert($pesertaInsert);
            }
        }

        $allPesertaIds = Peserta::pluck('id')->all();
        foreach ($seededJadwals as $name => $info) {
            $target = $info['target_peserta'];
            $subset = array_slice($allPesertaIds, 0, min($target, count($allPesertaIds)));
            $info['jadwal']->pesertas()->sync($subset);
        }

        // Tambahkan dummy jadwal jika total jadwal < 10 agar total jadwal = 10
        $currentJadwalCount = Jadwal::count();
        if ($currentJadwalCount < 10) {
            for ($k = $currentJadwalCount + 1; $k <= 10; $k++) {
                $statusRand = match ($k % 3) {
                    0 => 'selesai',
                    1 => 'akan_datang',
                    default => 'berlangsung',
                };
                $d = Jadwal::create([
                    'paket_soal_id' => $paket1->id,
                    'nama_ujian' => "Tes {$k}",
                    'tanggal' => Carbon::parse('2024-10-10')->addDays($k),
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:00:00',
                    'durasi' => 120,
                    'passing_grade' => 300.00,
                    'status' => $statusRand,
                ]);
                $d->pesertas()->sync(array_slice($allPesertaIds, 0, min(5, count($allPesertaIds))));
            }
        }

        // Khusus Peserta Utama Demo (Budi Santoso) - sinkronisasi jadwal aktif sesuai Mockup Dashboard & Jadwal Ujian Peserta
        if ($peserta1) {
            $mockupPesertaJadwalIds = Jadwal::whereIn('nama_ujian', [
                'Seleksi Kemampuan Dasar (SKD) - Batch 1',
                'UK (TI)',
                'Tes Wawasan Kebangsaan (TWK) - Kelas Susulan',
                'Simulasi Ujian Mandiri & Deteksi Kamera',
            ])->pluck('id')->all();
            if (! empty($mockupPesertaJadwalIds)) {
                $peserta1->jadwals()->sync($mockupPesertaJadwalIds);
            }

            // Sesi ujian selesai untuk Simulasi Ujian Mandiri agar tombol "Lihat Hasil" aktif
            $jadwalSimulasi = Jadwal::where('nama_ujian', 'Simulasi Ujian Mandiri & Deteksi Kamera')->first();
            if ($jadwalSimulasi) {
                SesiUjian::firstOrCreate(
                    [
                        'peserta_id' => $peserta1->id,
                        'jadwal_id' => $jadwalSimulasi->id,
                    ],
                    [
                        'status' => 'selesai',
                        'waktu_mulai' => Carbon::parse('2024-10-12 09:00:00'),
                        'waktu_selesai' => Carbon::parse('2024-10-12 10:45:00'),
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Mozilla/5.0',
                    ]
                );
            }

            Notifikasi::firstOrCreate(
                [
                    'user_id' => $userPeserta1->id,
                    'judul' => 'Sistem Manajemen Ujian',
                ],
                [
                    'isi' => 'Pendaftaran Anda berhasil diverifikasi. Silahkan melihat jadwal anda.',
                    'waktu' => Carbon::parse('2024-10-10 08:00:00'),
                    'dibaca' => false,
                ]
            );

            // Data Riwayat Ujian & Hasil Ujian Budi Santoso (Mockup Hasil Ujian Peserta)
            $jadwalSkdBatch1 = Jadwal::where('nama_ujian', 'Seleksi Kemampuan Dasar (SKD) - Batch 1')->first();
            if ($jadwalSkdBatch1) {
                $jadwalSkdBatch1->update(['passing_grade' => 311.00]);

                NilaiUjian::updateOrCreate(
                    [
                        'peserta_id' => $peserta1->id,
                        'jadwal_id' => $jadwalSkdBatch1->id,
                    ],
                    [
                        'nilai_akhir' => 395.00,
                        'jumlah_benar' => 82,
                        'jumlah_salah' => 18,
                        'jumlah_tidak_dijawab' => 0,
                        'status_kelulusan' => 'LULUS',
                    ]
                );
            }

            $jadwalSimulasiSKD = Jadwal::firstOrCreate(
                ['nama_ujian' => 'Simulasi Ujian SKD Mandiri'],
                [
                    'paket_soal_id' => $paket1->id,
                    'tanggal' => Carbon::parse('2024-10-05'),
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:00:00',
                    'durasi' => 120,
                    'passing_grade' => 300.00,
                    'status' => 'selesai',
                ]
            );

            NilaiUjian::updateOrCreate(
                [
                    'peserta_id' => $peserta1->id,
                    'jadwal_id' => $jadwalSimulasiSKD->id,
                ],
                [
                    'nilai_akhir' => 350.00,
                    'jumlah_benar' => 70,
                    'jumlah_salah' => 30,
                    'jumlah_tidak_dijawab' => 0,
                    'status_kelulusan' => 'LULUS',
                ]
            );

            $jadwalTwkPrelim = Jadwal::firstOrCreate(
                ['nama_ujian' => 'Tes Wawasan Kebangsaan (Preliminary)'],
                [
                    'paket_soal_id' => $paket1->id,
                    'tanggal' => Carbon::parse('2024-09-28'),
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '09:30:00',
                    'durasi' => 90,
                    'passing_grade' => 300.00,
                    'status' => 'selesai',
                ]
            );

            NilaiUjian::updateOrCreate(
                [
                    'peserta_id' => $peserta1->id,
                    'jadwal_id' => $jadwalTwkPrelim->id,
                ],
                [
                    'nilai_akhir' => 290.00,
                    'jumlah_benar' => 58,
                    'jumlah_salah' => 42,
                    'jumlah_tidak_dijawab' => 0,
                    'status_kelulusan' => 'TIDAK LULUS',
                ]
            );
        }

        // 10. Data Peserta spesifik mockup (Screen-Data-Peserta)
        $mockupDataPeserta = [
            [
                'name' => 'Achmad Fauzi',
                'email' => 'achmad.fauzi@gmail.com',
                'username' => 'achmad_fauzi_peserta',
                'nik' => '3171020908950001',
                'status_akun' => 'terverifikasi',
                'created_at' => Carbon::parse('2024-10-12 14:00:00'),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@yahoo.com',
                'username' => 'dewi_lestari_peserta',
                'nik' => '3273051411960002',
                'status_akun' => 'terverifikasi',
                'created_at' => Carbon::parse('2024-10-12 13:30:00'),
            ],
            [
                'name' => 'Rian Hidayat',
                'email' => 'rian.hid@email.com',
                'username' => 'rian_hidayat',
                'nik' => '3175080101940003',
                'status_akun' => 'menunggu',
                'created_at' => Carbon::parse('2024-10-12 12:45:00'),
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@email.com',
                'username' => 'siti_aminah_peserta',
                'nik' => '3201044405970004',
                'status_akun' => 'menunggu',
                'created_at' => Carbon::parse('2024-10-12 11:20:00'),
            ],
            [
                'name' => 'Bambang Pamungkas',
                'email' => 'bambang.p@outlook.com',
                'username' => 'bambang_pamungkas',
                'nik' => '3374092106930005',
                'status_akun' => 'terverifikasi',
                'created_at' => Carbon::parse('2024-10-11 16:15:00'),
            ],
            [
                'name' => 'Mega Utami',
                'email' => 'mega.utami@gmail.com',
                'username' => 'mega_utami',
                'nik' => '3173015502960006',
                'status_akun' => 'ditolak',
                'created_at' => Carbon::parse('2024-10-10 15:40:00'),
            ],
            [
                'name' => 'Aditya Pratama',
                'email' => 'aditya.pratama@gmail.com',
                'username' => 'aditya_pratama',
                'nik' => '3172031112950007',
                'status_akun' => 'terverifikasi',
                'created_at' => Carbon::parse('2024-10-10 10:10:00'),
            ],
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri.hnd@yahoo.co.id',
                'username' => 'fitri_handayani',
                'nik' => '3578024408980008',
                'status_akun' => 'terverifikasi',
                'created_at' => Carbon::parse('2024-10-09 09:05:00'),
            ],
        ];

        // Set peserta lainnya dengan created_at lebih awal agar 8 data mockup berada di halaman pertama
        User::where('role', 'peserta')
            ->whereNotIn('email', collect($mockupDataPeserta)->pluck('email'))
            ->update(['created_at' => Carbon::parse('2024-10-01 08:00:00')]);

        foreach ($mockupDataPeserta as $mdp) {
            $userMdp = User::updateOrCreate(
                ['email' => $mdp['email']],
                [
                    'name' => $mdp['name'],
                    'username' => $mdp['username'],
                    'password' => $defaultHashedPassword,
                    'role' => 'peserta',
                    'status_akun' => $mdp['status_akun'],
                    'created_at' => $mdp['created_at'],
                    'updated_at' => $mdp['created_at'],
                ]
            );

            Peserta::updateOrCreate(
                ['user_id' => $userMdp->id],
                [
                    'no_identitas' => $mdp['nik'],
                    'instansi' => 'Umum',
                ]
            );
        }

        // 11. Pengguna spesifik mockup Kelola Pengguna (Screen-Kelola-Pengguna)
        $mockupUsersKelola = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@peserta.id',
                'username' => 'ahmad_fauzi',
                'role' => 'peserta',
                'status_akun' => 'aktif',
                'terakhir_login' => '2024-10-12 14:00:00',
            ],
            [
                'name' => 'Sri Wahyuni',
                'email' => 'sri.wahyuni@panitia.id',
                'username' => 'sri_wahyuni',
                'role' => 'panitia',
                'status_akun' => 'aktif',
                'terakhir_login' => '2024-10-12 10:15:00',
            ],
            [
                'name' => 'Bambang Hermawan',
                'email' => 'bambang@pengawas.id',
                'username' => 'bambang_hermawan',
                'role' => 'pengawas',
                'status_akun' => 'aktif',
                'terakhir_login' => '2024-10-11 16:30:00',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@peserta.id',
                'username' => 'siti_aminah',
                'role' => 'peserta',
                'status_akun' => 'nonaktif',
                'terakhir_login' => '2024-10-08 09:20:00',
            ],
            [
                'name' => 'Dian Kartika',
                'email' => 'dian@admin.id',
                'username' => 'dian_kartika',
                'role' => 'admin',
                'status_akun' => 'aktif',
                'terakhir_login' => '2024-10-12 11:45:00',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@peserta.id',
                'username' => 'eko_prasetyo',
                'role' => 'peserta',
                'status_akun' => 'aktif',
                'terakhir_login' => '2024-10-10 13:10:00',
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi@pengawas.id',
                'username' => 'rudi_hartono',
                'role' => 'pengawas',
                'status_akun' => 'aktif',
                'terakhir_login' => '2024-10-12 08:30:00',
            ],
            [
                'name' => 'Sinta Dewi',
                'email' => 'sinta@peserta.id',
                'username' => 'sinta_dewi',
                'role' => 'peserta',
                'status_akun' => 'nonaktif',
                'terakhir_login' => '2024-10-05 15:50:00',
            ],
        ];

        foreach ($mockupUsersKelola as $mu) {
            $u = User::updateOrCreate(
                ['email' => $mu['email']],
                [
                    'name' => $mu['name'],
                    'username' => $mu['username'],
                    'password' => $defaultHashedPassword,
                    'role' => $mu['role'],
                    'status_akun' => $mu['status_akun'],
                    'terakhir_login' => $mu['terakhir_login'],
                ]
            );
            if ($mu['role'] === 'peserta') {
                Peserta::firstOrCreate(
                    ['user_id' => $u->id],
                    ['no_identitas' => '3171'.rand(1000000000, 9999999999), 'instansi' => 'SMA / SMK']
                );
            }
        }

        // 11. Data spesifik mockup Log Aktivitas Sistem (Screen-Log-Aktivitas)
        $userMap = User::pluck('id', 'name')->toArray();
        $userHendra = $userMap['Hendra Wijaya'] ?? $panitia->id;
        $userRatih = $userMap['Ratih Purwasih'] ?? $pengawas1->id;
        $userAhmad = $userMap['Ahmad Fauzi'] ?? $userPeserta1->id;
        $userAdmin = $admin->id;
        $userSri = $userMap['Sri Wahyuni'] ?? $panitia->id;
        $userEko = $userMap['Eko Prasetyo'] ?? $userPeserta1->id;
        $userRudi = $userMap['Rudi Hartono'] ?? $pengawas1->id;
        $userSiti = $userMap['Siti Aminah'] ?? $userPeserta1->id;
        $userBambang = $userMap['Bambang Hermawan'] ?? $pengawas1->id;

        $mockupLogs = [
            [
                'user_id' => $userHendra,
                'waktu' => '2024-10-12 14:32:05',
                'jenis_aktivitas' => 'Login Berhasil',
                'keterangan' => 'Berhasil masuk ke dalam sistem panitia.',
                'alamat_ip' => '192.168.1.50',
            ],
            [
                'user_id' => $userRatih,
                'waktu' => '2024-10-12 14:15:12',
                'jenis_aktivitas' => 'Login Berhasil',
                'keterangan' => 'Berhasil masuk ke dalam ruang pengawas.',
                'alamat_ip' => '192.168.1.12',
            ],
            [
                'user_id' => $userAhmad,
                'waktu' => '2024-10-12 13:40:22',
                'jenis_aktivitas' => 'Memulai Ujian',
                'keterangan' => 'Memulai ujian SKD Batch 1 Gelombang 1.',
                'alamat_ip' => '10.12.33.91',
            ],
            [
                'user_id' => $userAdmin,
                'waktu' => '2024-10-12 11:20:10',
                'jenis_aktivitas' => 'Verifikasi Akun',
                'keterangan' => 'Menyetujui 5 akun peserta ujian baru.',
                'alamat_ip' => '192.168.1.2',
            ],
            [
                'user_id' => $userSri,
                'waktu' => '2024-10-12 10:05:44',
                'jenis_aktivitas' => 'Unggah Soal',
                'keterangan' => 'Mengunggah 20 soal paket kemampuan dasar.',
                'alamat_ip' => '192.168.1.18',
            ],
            [
                'user_id' => $userEko,
                'waktu' => '2024-10-11 16:30:15',
                'jenis_aktivitas' => 'Registrasi Akun',
                'keterangan' => 'Pendaftaran akun baru peserta ujian.',
                'alamat_ip' => '36.82.112.4',
            ],
            [
                'user_id' => $userRudi,
                'waktu' => '2024-10-11 15:10:00',
                'jenis_aktivitas' => 'Daftar Absensi',
                'keterangan' => 'Melakukan rekap kehadiran peserta ruang 02.',
                'alamat_ip' => '192.168.1.25',
            ],
            [
                'user_id' => $userAdmin,
                'waktu' => '2024-10-11 14:22:11',
                'jenis_aktivitas' => 'Ubah Hak Akses',
                'keterangan' => 'Mengubah izin modul pengawas ujian.',
                'alamat_ip' => '192.168.1.2',
            ],
            [
                'user_id' => $userSiti,
                'waktu' => '2024-10-10 11:15:30',
                'jenis_aktivitas' => 'Selesai Ujian',
                'keterangan' => 'Mengirimkan lembar jawaban ujian seleksi.',
                'alamat_ip' => '10.12.33.88',
            ],
            [
                'user_id' => $userBambang,
                'waktu' => '2024-10-10 09:30:05',
                'jenis_aktivitas' => 'Diskualifikasi',
                'keterangan' => 'Mendiskualifikasi peserta karena membuka tab lain.',
                'alamat_ip' => '192.168.1.30',
            ],
        ];

        foreach ($mockupLogs as $ml) {
            Aktivitas::updateOrCreate(
                [
                    'waktu' => $ml['waktu'],
                    'jenis_aktivitas' => $ml['jenis_aktivitas'],
                ],
                $ml
            );
        }

        // Pastikan total data log mencapai 10 entries
        $currentLogCount = Aktivitas::count();
        if ($currentLogCount < 10) {
            $diff = 10 - $currentLogCount;
            $batchLogs = [];
            for ($i = 1; $i <= $diff; $i++) {
                $batchLogs[] = [
                    'user_id' => $userAdmin,
                    'jenis_aktivitas' => 'Aktivitas Sistem Rutin',
                    'keterangan' => "Pemeriksaan integritas dan sinkronisasi log audit sistem ke-{$i}.",
                    'alamat_ip' => '127.0.0.1',
                    'waktu' => Carbon::parse('2024-10-09 23:59:59')->subMinutes($i * 20),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Aktivitas::insert($batchLogs);
        }

        // 12. Data Rekap Nilai Ujian Mockup (Screen-Rekap-Nilai-Ujian)
        $targetJadwalSkd = Jadwal::where('nama_ujian', '(SKD) - Batch 1')->first();
        if ($targetJadwalSkd) {
            Schema::disableForeignKeyConstraints();
            NilaiUjian::where('jadwal_id', $targetJadwalSkd->id)->delete();
            Pelanggaran::where('jadwal_id', $targetJadwalSkd->id)->delete();
            SesiUjian::where('jadwal_id', $targetJadwalSkd->id)->delete();
            Schema::enableForeignKeyConstraints();

            $mockupNilaiPeserta = [
                [
                    'name' => 'Budi Santoso',
                    'email' => 'budi.s@gmail.com',
                    'username' => 'budi_santoso_peserta',
                    'no_identitas' => '000230240240',
                    'waktu_masuk' => '2024-10-15 09:00:00',
                    'waktu_selesai' => '2024-10-15 11:15:00',
                    'benar' => 88,
                    'salah' => 12,
                    'nilai' => 440.00,
                    'status' => 'LULUS',
                ],
                [
                    'name' => 'Dewi Lestari',
                    'email' => 'dewi.lestari@yahoo.com',
                    'username' => 'dewi_lestari_peserta',
                    'no_identitas' => '000230240241',
                    'waktu_masuk' => '2024-10-15 08:00:00',
                    'waktu_selesai' => '2024-10-15 10:00:00',
                    'benar' => 75,
                    'salah' => 25,
                    'nilai' => 375.00,
                    'status' => 'LULUS',
                ],
                [
                    'name' => 'Slamet Raharjo',
                    'email' => 'slamet.raharjo@gmail.com',
                    'username' => 'slamet_raharjo_peserta',
                    'no_identitas' => '139824844045',
                    'waktu_masuk' => '2024-10-15 08:00:00',
                    'waktu_selesai' => '2024-10-15 10:00:00',
                    'benar' => 52,
                    'salah' => 48,
                    'nilai' => 260.00,
                    'status' => 'TIDAK LULUS',
                ],
                [
                    'name' => 'Mega Utami',
                    'email' => 'mega.utami@gmail.com',
                    'username' => 'mega_utami',
                    'no_identitas' => '049555789343',
                    'waktu_masuk' => '2024-10-15 08:00:00',
                    'waktu_selesai' => '2024-10-15 08:45:00',
                    'benar' => 48,
                    'salah' => 52,
                    'nilai' => 0.00,
                    'status' => 'DISKUALIFIKASI',
                    'pelanggaran' => [
                        'jenis' => 'Membuka Tab Browser Lain',
                        'keterangan' => 'Terdeteksi membuka aplikasi lain saat ujian berlangsung.',
                    ],
                ],
                [
                    'name' => 'Achmad Fauzi',
                    'email' => 'achmad.fauzi@gmail.com',
                    'username' => 'achmad_fauzi_peserta',
                    'no_identitas' => '023392384347',
                    'waktu_masuk' => '2024-10-15 08:00:00',
                    'waktu_selesai' => '2024-10-15 10:00:00',
                    'benar' => 92,
                    'salah' => 8,
                    'nilai' => 460.00,
                    'status' => 'LULUS',
                ],
                [
                    'name' => 'Fitri Handayani',
                    'email' => 'fitri.hnd@yahoo.co.id',
                    'username' => 'fitri_handayani_peserta',
                    'no_identitas' => '084340329344',
                    'waktu_masuk' => '2024-10-15 08:00:00',
                    'waktu_selesai' => '2024-10-15 10:00:00',
                    'benar' => 80,
                    'salah' => 20,
                    'nilai' => 400.00,
                    'status' => 'LULUS',
                ],
            ];

            $assignedPesertaIds = [];

            foreach ($mockupNilaiPeserta as $mnp) {
                $u = User::updateOrCreate(
                    ['email' => $mnp['email']],
                    [
                        'name' => $mnp['name'],
                        'username' => $mnp['username'],
                        'password' => $defaultHashedPassword,
                        'role' => 'peserta',
                        'status_akun' => 'terverifikasi',
                    ]
                );

                $p = Peserta::updateOrCreate(
                    ['user_id' => $u->id],
                    [
                        'no_identitas' => $mnp['no_identitas'],
                        'instansi' => 'Peserta SKD',
                    ]
                );

                $assignedPesertaIds[] = $p->id;

                $sesi = SesiUjian::create([
                    'jadwal_id' => $targetJadwalSkd->id,
                    'peserta_id' => $p->id,
                    'status' => 'selesai',
                    'waktu_masuk' => Carbon::parse($mnp['waktu_masuk']),
                    'waktu_selesai' => Carbon::parse($mnp['waktu_selesai']),
                    'last_heartbeat' => Carbon::parse($mnp['waktu_selesai']),
                    'sudah_masuk' => true,
                    'jumlah_peringatan' => (! empty($mnp['pelanggaran'])) ? 1 : 0,
                ]);

                NilaiUjian::create([
                    'peserta_id' => $p->id,
                    'jadwal_id' => $targetJadwalSkd->id,
                    'sesi_ujian_id' => $sesi->id,
                    'nilai_akhir' => $mnp['nilai'],
                    'jumlah_benar' => $mnp['benar'],
                    'jumlah_salah' => $mnp['salah'],
                    'jumlah_tidak_dijawab' => 0,
                    'status_kelulusan' => $mnp['status'],
                ]);

                if (! empty($mnp['pelanggaran'])) {
                    Pelanggaran::create([
                        'peserta_id' => $p->id,
                        'jadwal_id' => $targetJadwalSkd->id,
                        'user_id' => $pengawas1->id,
                        'jenis_pelanggaran' => $mnp['pelanggaran']['jenis'],
                        'waktu' => Carbon::parse('2024-10-15 08:45:00'),
                        'keterangan' => $mnp['pelanggaran']['keterangan'],
                        'tindakan' => 'Diskualifikasi',
                    ]);
                }
            }

            // Sisa peserta agar tersinkronisasi
            $remainingPesertas = Peserta::whereNotIn('id', $assignedPesertaIds)->take(4)->get();

            foreach ($remainingPesertas as $idx => $remP) {
                $assignedPesertaIds[] = $remP->id;
                $nilaiVal = ($idx % 2 === 0) ? 363.00 : 260.00;
                $benarVal = ($idx % 2 === 0) ? 73 : 52;
                $statusVal = ($idx % 2 === 0) ? 'LULUS' : 'TIDAK LULUS';

                $sesiRem = SesiUjian::create([
                    'jadwal_id' => $targetJadwalSkd->id,
                    'peserta_id' => $remP->id,
                    'status' => 'selesai',
                    'waktu_masuk' => Carbon::parse('2024-10-15 08:00:00'),
                    'waktu_selesai' => Carbon::parse('2024-10-15 10:00:00'),
                    'last_heartbeat' => Carbon::parse('2024-10-15 10:00:00'),
                    'sudah_masuk' => true,
                ]);

                NilaiUjian::create([
                    'peserta_id' => $remP->id,
                    'jadwal_id' => $targetJadwalSkd->id,
                    'sesi_ujian_id' => $sesiRem->id,
                    'nilai_akhir' => $nilaiVal,
                    'jumlah_benar' => $benarVal,
                    'jumlah_salah' => 100 - $benarVal,
                    'jumlah_tidak_dijawab' => 0,
                    'status_kelulusan' => $statusVal,
                ]);
            }

            // Sinkronkan ke pivot jadwal_peserta
            $targetJadwalSkd->pesertas()->sync($assignedPesertaIds);
        }

        // 13. Data Pelanggaran & Nilai untuk UK (TI) dan Ujian Bahasa Inggris Komparatif
        $jadwalUkTi = Jadwal::where('nama_ujian', 'UK (TI)')->first();
        if ($jadwalUkTi) {
            $pesertaUk = Peserta::first();
            if ($pesertaUk) {
                Pelanggaran::firstOrCreate([
                    'jadwal_id' => $jadwalUkTi->id,
                    'peserta_id' => $pesertaUk->id,
                ], [
                    'user_id' => $pengawas1->id,
                    'jenis_pelanggaran' => 'Membuka Tab Browser Lain',
                    'waktu' => Carbon::parse('2024-10-18 11:15:00'),
                    'keterangan' => 'Membuka tab referensi teknis saat ujian.',
                    'tindakan' => 'Peringatan 1',
                ]);
            }
        }

        $jadwalBing1 = Jadwal::where('nama_ujian', 'Ujian Bahasa Inggris Komparatif')->orderBy('id')->first();
        if ($jadwalBing1) {
            $pesertaSample = Peserta::take(4)->get();
            foreach ($pesertaSample as $pIdx => $pSample) {
                Pelanggaran::firstOrCreate([
                    'jadwal_id' => $jadwalBing1->id,
                    'peserta_id' => $pSample->id,
                ], [
                    'user_id' => $pengawas1->id,
                    'jenis_pelanggaran' => 'Pergerakan Mencurigakan',
                    'waktu' => Carbon::parse('2024-09-28 08:30:00')->addMinutes($pIdx * 10),
                    'keterangan' => 'Terdeteksi noleh/berbicara oleh pengawas (Insiden ke-'.($pIdx + 1).').',
                    'tindakan' => 'Peringatan Sistem',
                ]);
            }
        }
    }
}
