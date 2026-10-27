# 03 — Database (SDD v1.0 + perluasan wajib)

## 9 tabel dari SDD (pertahankan)
- **user**: id_user PK, nama VARCHAR(100), email VARCHAR(100) unik, username VARCHAR(50) unik, password_hash VARCHAR(255), role ENUM(admin,panitia,pengawas,peserta), status_akun ENUM
- **peserta**: id_peserta PK, id_user FK, no_identitas VARCHAR(30), instansi VARCHAR(50) null
- **bank_soal**: id_bank_soal PK, id_user FK (pengelola), id_paket FK, pertanyaan TEXT, tipe_soal VARCHAR(100), opsi_jawaban (pilihan A-E; pakai JSON, bukan ENUM), kunci_jawaban TEXT, bobot DECIMAL(5,2)
- **paket_soal**: id_paket PK, nama_paket VARCHAR(100), deskripsi TEXT null, jumlah_soal INT, status_paket ENUM
- **jadwal**: id_jadwal PK, id_paket FK, tanggal DATE, jam_mulai TIME, jam_selesai TIME, durasi INT (menit), status ENUM(akan_datang, berlangsung, selesai)
- **nilai_ujian**: id_nilai PK, id_peserta FK, id_jadwal FK, nilai_akhir DECIMAL(5,2), jumlah_benar INT, jumlah_salah INT
- **pelanggaran**: id_pelanggaran PK, id_peserta FK, id_jadwal FK, id_user FK (pengawas pencatat), jenis_pelanggaran VARCHAR(100), waktu DATETIME, keterangan TEXT null, tindakan VARCHAR(255) null
- **aktivitas**: id_aktivitas PK, id_user FK, id_jadwal FK null, jenis_aktivitas VARCHAR(100), waktu DATETIME, keterangan TEXT null, alamat_ip VARCHAR(45) null

Catatan: laravel boleh tetap memakai nama PK id_xxx (set `$primaryKey`) dan kolom `password_hash` (override getAuthPassword). Password selalu Bcrypt, tidak pernah plaintext.

## KEKURANGAN ERD — wajib dilengkapi lewat migration tambahan
1. **jadwal_peserta** (pivot peserta terdaftar pada jadwal) — Figma menampilkan "Jumlah Peserta" per jadwal.
2. **jadwal_pengawas** (pivot pengawas bertugas) — pengawas hanya mengakses ujian yang ia awasi.
3. **sesi_ujian** (per peserta per jadwal): status ENUM(belum_masuk, berlangsung, terputus, menunggu_izin, izin_diberikan, selesai, diskualifikasi), waktu_masuk, waktu_selesai, last_heartbeat, urutan_soal (JSON/seed), sudah_masuk (bool single-attempt), jumlah_peringatan. Unique (id_peserta, id_jadwal). Inti single-attempt, acak soal, status real-time.
4. **jawaban_peserta**: id_sesi FK, id_bank_soal FK, jawaban, benar (bool null), waktu_simpan. Unique (id_sesi, id_bank_soal). SDD belum punya tabel ini.
5. **permintaan_izin**: id_sesi FK, alasan, status ENUM(menunggu, disetujui, ditolak), id_pengawas FK null, waktu.
6. **notifikasi**: id_user FK, judul, isi, dibaca bool, waktu (hasil ujian + pengumuman dashboard peserta).
7. **passing_grade** pada jadwal atau paket_soal (Figma: "Passing Grade 311", skala 0-500).
8. Kolom tambahan: bank_soal.kategori, bank_soal.tingkat_kesulitan, bank_soal.aktif (bool); paket_soal.durasi; jadwal.nama_ujian (atau ambil dari paket); user.terakhir_login.
9. **status_akun**: SDD hanya aktif/nonaktif, padahal alur butuh Menunggu / Terverifikasi / Ditolak (peserta) dan Aktif / Non-Aktif (staf). Rancang enum yang mencakup semuanya.
10. **hak_akses**: role, modul, can_view, can_create, can_update, can_delete (matriks Admin: modul Data Peserta, Jadwal Ujian, Bank Soal, Ruang Ujian, Monitoring, Hasil Ujian, Laporan).

## Peringatan dokumen
Penjelasan tabel di SDD memuat sisa draft lama ("dosen", "NIM/kelas/program studi", "Tabel Mata Pelajaran"). Itu SALAH — abaikan, pakai skema di atas.
Gunakan foreign key + constraint, index pada FK/status/waktu, seeder untuk akun contoh tiap role + data dummy.
