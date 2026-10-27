# AGENTS.md — Sistem Manajemen Ujian Berbasis Web dengan Pengawasan Real-Time

Proyek kuliah (Proyek Perangkat Lunak, Kelompok 3, model Incremental). Bahasa komunikasi, komentar penting, dan penjelasan: **Bahasa Indonesia**.

## Ringkasan
Platform ujian online: registrasi & verifikasi peserta -> kelola soal & jadwal -> pelaksanaan ujian -> pengawasan real-time -> penilaian otomatis -> hasil/notifikasi -> log & laporan.
Empat role saja: **Admin, Panitia, Pengawas, Peserta**.
Fokus: integritas ujian (soal diacak per peserta, satu kali masuk ruang ujian, pengawasan real-time, audit log).

## Stack (wajib)
Laravel (PHP) pola MVC + Eloquent, MySQL, Blade + Tailwind CSS + JavaScript (bukan SPA), Vite lokal.
Deploy: LAN/intranet lewat Laragon (IP statis). TIDAK boleh bergantung pada CDN/layanan internet eksternal.

## Sumber kebenaran
- Backend: Project Charter, SRS, SDD (diringkas di `.agents/rules/02-*` s.d. `03-*` dan `07-*`).
- Frontend: Figma "LokalKarya" page "PPL" (diringkas di `.agents/rules/04-*` s.d. `06-*`).
Jika aturan di file ini bentrok dengan rule lain, ikuti `07-asumsi-dan-nonfungsional.md` untuk keputusan default.

## Cara kerja (Incremental)
1. Increment 1: Autentikasi & verifikasi akun (registrasi, login, RBAC, dashboard per role).
2. Increment 2: Jadwal & soal (Data Peserta/Pengawas, Paket Soal, Bank Soal, Import, Kelola Jadwal).
3. Increment 3: Ruang ujian (konfirmasi, single-attempt, acak soal, autosave, timer server-side, submit, penilaian).
4. Increment 4: Monitoring real-time, izin masuk kembali/diskualifikasi, notifikasi, rekap nilai, laporan, log, hak akses admin.

Setiap increment: buat Implementation Plan dulu, tunggu persetujuan, lalu kerjakan dan uji sebelum lanjut.
Setiap increment menghasilkan: migration, model+relasi, route, controller, Form Request, middleware/policy, service, view Blade+JS, seeder, feature test aturan bisnis, dan langkah menjalankan di Laragon.

## Larangan
Tidak ada aplikasi mobile native, tidak ada modul akademik umum (SPP/presensi), tidak ada cloud/CDN/SMTP/OAuth eksternal. Jangan menambah fitur di luar lingkup. Jangan pernah log atau commit password/API key.
Tanya klarifikasi hanya jika benar-benar menghalangi; selain itu pakai asumsi default dan tandai komentar `// ASUMSI:` di kode.
