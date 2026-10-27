# Workflow: Review Aturan Bisnis
Gunakan: `/review-aturan-bisnis` untuk audit kode terhadap rule 02.
Periksa satu per satu: single-attempt (transaksi/lock, tolak akses ulang tanpa izin), pengacakan tersimpan di sesi_ujian, autosave + retry, timer server-side, penilaian benar (termasuk diskualifikasi & jawaban sebagian), otorisasi per role (403 saat akses paksa), audit log lengkap, polling monitoring tanpa N+1. Laporkan tiap temuan dengan file:baris dan usulan perbaikan; perbaiki yang jelas bug setelah saya setujui.
