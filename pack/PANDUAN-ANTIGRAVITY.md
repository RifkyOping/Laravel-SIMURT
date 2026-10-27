# Panduan Pakai di Antigravity

## 1. Taruh file
Salin isi folder ini ke ROOT proyek Laravel Anda (folder yang dibuka di Antigravity):
- AGENTS.md
- .agents/rules/01..07-*.md
- .agents/workflows/*.md
(Versi lama memakai `.agent/` tanpa "s"; jika rule tidak terbaca, ganti nama foldernya.)

## 2. Atur rule
Buka panel Customizations (menu "..." di panel agent) > Rules. Pastikan 01, 02, 03, 07 = Always On.
Rule 04, 05, 06 (frontend) boleh Model Decision atau Manual; bila Manual, sebut dengan @ saat mengerjakan view, mis. `@04-frontend-umum-dan-peserta`.
Batas Antigravity: tiap file rule/workflow maksimal 12.000 karakter (semua file di sini sudah di bawah batas).

## 3. Prompt pertama (ketik di Agent Manager / panel agent)
Buat proyek Laravel baru di folder ini dan siapkan fondasinya. Baca AGENTS.md dan semua rule di .agents/rules. Jalankan /mulai-increment untuk Increment 1. Mulai dengan Implementation Plan (ringkasan pemahaman maks 10 poin, struktur folder, skema database final) dan tunggu persetujuan saya sebelum menulis kode.

## 4. Prompt berikutnya
- Lanjut: `/mulai-increment 2` (lalu 3, 4) setelah increment sebelumnya Anda setujui.
- Audit: `/review-aturan-bisnis`.
- Mengerjakan satu halaman: "Buat halaman Ruang Ujian sesuai @04-frontend-umum-dan-peserta dan aturan di @02-aturan-bisnis."

## 5. Tips
- Antigravity biasanya login dengan akun Google. Jika Anda memakai API key Gemini, atur di pengaturan model/provider yang tersedia di versi Anda; jangan menaruh API key di file rule atau commit ke GitHub (.env sudah di .gitignore Laravel).
- Satu increment per sesi agent agar konteks tetap kecil dan hasil mudah diperiksa.
- Setelah setiap increment: jalankan `php artisan test`, uji manual lewat Laragon, lalu commit ke Git.
