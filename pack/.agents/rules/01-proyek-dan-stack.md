# 01 — Proyek, Role, Stack (Always On)

## Role & hak akses
- **Peserta**: registrasi (atau registrasi ulang bila ditolak), login, dashboard, jadwal, konfirmasi & masuk ruang ujian, mengerjakan soal (acak, autosave), minta izin masuk kembali, notifikasi & hasil ujian.
- **Panitia**: verifikasi akun peserta (setujui/tolak), kelola data peserta, kelola data pengawas (tambah/edit/hapus), kelola bank soal & paket soal (tambah/edit/hapus/import), kelola jadwal, lihat & rekap nilai (export PDF), laporan ujian.
- **Pengawas**: monitoring peserta real-time, tegur (peringatan), blokir/diskualifikasi (wajib konfirmasi), beri/tolak izin masuk kembali, lihat log aktivitas pengawasan.
- **Admin**: kelola pengguna (tambah/edit/nonaktifkan/hapus), manajemen hak akses per role per modul (Lihat/Tambah/Edit/Hapus), log aktivitas sistem, laporan sistem.

Otorisasi RBAC lewat middleware role. Akses paksa via URL ke halaman role lain -> 403. Belum login -> redirect ke login. Setelah login redirect ke dashboard sesuai role.

## Arsitektur & stack
- Laravel (PHP) MVC, Eloquent ORM, MySQL.
- Frontend: Blade + Tailwind CSS + JavaScript (boleh Alpine.js/vanilla). Bukan SPA.
- Dev: Laragon, VS Code, Git/GitHub, phpMyAdmin.
- Deploy LAN/intranet: 1 komputer server (Laragon), IP statis (mis. http://192.168.1.100:8000). Klien: browser modern di laptop/PC/HP (UI responsif).
- Semua asset harus bisa jalan OFFLINE di LAN: build Tailwind lokal via Vite, tanpa CDN, tanpa Google Fonts remote.
- Keamanan bawaan Laravel: auth, Bcrypt, CSRF token, Eloquent/PDO (anti SQL injection), Blade auto-escape (anti XSS), validasi client (JS) dan server (Form Request).

## Struktur kode yang diharapkan
Controller tipis; logika inti di Service class (mis. ExamSessionService, ScoringService, QuestionShuffleService, ActivityLogger). Form Request untuk validasi. Policy/Middleware untuk otorisasi. Eager loading untuk hindari N+1. Index pada kolom FK/status/waktu.

## Konvensi penamaan
Tabel & kolom mengikuti SDD (snake_case Indonesia): user, peserta, bank_soal, paket_soal, jadwal, nilai_ujian, pelanggaran, aktivitas. Tabel tambahan lihat rule 03.
