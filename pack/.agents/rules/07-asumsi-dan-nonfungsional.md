# 07 — Keputusan Default, Ketidakkonsistenan Dokumen, Non-Fungsional (Always On)
Tandai tiap asumsi di kode dengan komentar `ASUMSI:`.

## Ketidakkonsistenan & default
1. **Rumus nilai**: Charter menyebut penilaian "berbasis perbandingan kemampuan", SRS/SDD menetapkan "jumlah soal yang berhasil dijawab". DEFAULT: nilai = jumlah bobot soal benar (Figma: skala 0-500, mis. 88 benar = 440 -> bobot 5/soal). Bungkus di ScoringService agar mudah diganti.
2. **Ada di Figma, tidak ada di SRS/SDD**: deteksi kamera (wajah tidak terdeteksi), deteksi suara bising, passing grade & status lulus, kategori/tingkat kesulitan soal, import soal via file, export data/PDF, tombol Tegur/Blokir. DEFAULT: bangun passing grade, kategori/kesulitan, import soal, export PDF/CSV, Tegur (= peringatan) dan Blokir (= diskualifikasi). Deteksi kamera/suara JANGAN dibangun; buat jenis_aktivitas yang extensible.
3. **Tipe soal**: Ruang Ujian Figma hanya pilihan ganda A-E, sementara contoh daftar soal ada esai/coding. DEFAULT: pilihan ganda penuh dengan auto-grade; tipe lain boleh disimpan tapi di luar penilaian otomatis.
4. **Pengelolaan akun pengawas**: Panitia boleh CRUD akun pengawas (SRS/Figma); Admin boleh CRUD semua akun.
5. **Login** dengan email (username tetap disimpan & unik).
6. **Real-time** memakai polling (rule 02-k).
7. **LAN**: tanpa SMTP/CDN/OAuth; notifikasi cukup in-app.
8. Data contoh Figma bertanggal 2024, abaikan; jangan hardcode tanggal.

## Non-fungsional
- **Performance**: respons <= 3 detik kondisi normal untuk registrasi/login/dashboard/CRUD; mampu banyak peserta bersamaan (index DB, eager loading); simpan jawaban cepat dan tidak mengganggu pengerjaan; sediakan skrip load test sederhana sebelum hari ujian.
- **Security**: autentikasi + RBAC, Bcrypt, CSRF, validasi server-side, escape output, pembatasan akses ruang ujian, audit trail lengkap.
- **Reliability**: autosave + retry; aturan single-attempt konsisten (transaksi/lock); penilaian sesuai data tersimpan; kegagalan simpan tidak merusak data yang sudah tersimpan.
- **Auditability, data integrity** (FK & constraint), **maintainability** (MVC rapi, Form Request, Service, Policy), **scalability** (siap tambah peserta/soal/jadwal).
- **Usability**: navigasi konsisten, pesan error jelas, ruang ujian bebas distraksi.
- **Compatibility**: Chrome/Edge/Firefox modern.

## Deployment (Laragon, LAN)
.env lokal; `php artisan migrate --seed`; `npm run build` (dev: `npm run dev`); jalankan `php artisan serve --host=0.0.0.0 --port=8000`; server pakai IP statis; klien di jaringan yang sama. Sertakan README langkah setup dan akun seeder.
