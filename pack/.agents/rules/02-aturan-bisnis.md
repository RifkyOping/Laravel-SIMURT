# 02 — Aturan Bisnis Kritis (Always On)

**a. Registrasi.** Peserta mengisi: nama lengkap, email, nomor identitas (NIK), instansi, password (min 8) + konfirmasi. Status awal "Menunggu Verifikasi". Peserta berstatus "Ditolak" boleh registrasi ulang. Peserta yang belum disetujui hanya melihat halaman "Menunggu Verifikasi".

**b. Verifikasi.** Panitia menyetujui/menolak (dari Dashboard atau Data Peserta). Status peserta: Menunggu / Terverifikasi / Ditolak.

**c. Login.** Email + password; cek status akun & role; redirect ke dashboard role; pesan error jelas.

**d. Akses ruang ujian** hanya jika: jadwal aktif (dalam rentang tanggal & jam), peserta terverifikasi, peserta terdaftar pada jadwal itu, dan kesempatan masuk BELUM terpakai.

**e. SINGLE-ATTEMPT.** Hanya 1 kali masuk. Jika peserta keluar/terputus/menutup halaman, akses otomatis ditolak dan peserta diarahkan meminta izin pengawas. Izin HANYA dari Pengawas (tombol "Izin"). Jika alasan tidak sah, pengawas menolak -> akses tetap tertutup, ujian peserta diselesaikan (jawaban tersimpan tetap dinilai). Pembuatan sesi ujian harus memakai transaksi/lock agar request ganda tidak menghasilkan double-entry.

**f. PENGACAKAN SOAL.** Urutan soal berbeda tiap peserta. Simpan urutan acak (seed / daftar id soal) di `sesi_ujian` saat PERTAMA masuk, sehingga urutan sama bila peserta kembali setelah izin.

**g. AUTOSAVE.** Jawaban disimpan via AJAX saat memilih jawaban / pindah soal ("Simpan & Lanjut"). Klien retry bila gagal dan menampilkan indikator status simpan. Gangguan koneksi tidak boleh merusak jawaban yang sudah tersimpan. Timer OTORITATIF dari server (waktu_masuk + durasi), bukan dari klien.

**h. Selesai.** Tombol "Selesai Ujian" (tidak bisa dibatalkan, ada konfirmasi) atau waktu habis -> submit otomatis -> penilaian otomatis.

**i. PENILAIAN.** nilai_akhir = jumlah bobot soal yang dijawab benar. Simpan jumlah_benar, jumlah_salah (+ tidak dijawab). Jika hanya sebagian jawaban tersimpan, hitung dari yang tersimpan. Diskualifikasi -> nilai 0, status DISKUALIFIKASI. Status hasil: LULUS / TIDAK LULUS / DISKUALIFIKASI berdasar passing grade. Buat dalam `ScoringService` yang mudah diganti.

**j. NOTIFIKASI.** Setelah nilai tersimpan buat notifikasi in-app ("hasil ujian tersedia"), ada status baca. Sebelum selesai diproses halaman hasil menampilkan "Hasil Ujian Sedang Diproses".

**k. PENGAWASAN real-time.** Pengawas melihat peserta pada ujian aktif: status (Aktif / Peringatan / Terputus), progres soal (mis. 30/50), terakhir aktif ("43 detik yang lalu"), ringkasan jumlah per status, sisa waktu. Implementasi default: polling AJAX tiap 3-5 detik ke endpoint JSON + heartbeat dari klien peserta. WebSocket (Laravel Reverb) hanya opsi tambahan.

**l. Aksi pengawas.** Izin = izin masuk kembali; Tegur = peringatan; Blokir = diskualifikasi (wajib konfirmasi). Semua tercatat di tabel pelanggaran dan aktivitas.

**m. Deteksi dari klien.** JavaScript menangkap pindah tab/blur/visibilitychange/keluar fullscreen lalu kirim ke server -> tercatat di log, status peserta jadi "Peringatan". Risiko pindah tab tidak sengaja ditangani lewat kewenangan pengawas mereset status & memberi izin.

**n. LOG AKTIVITAS (audit trail).** Catat: login, logout, registrasi, verifikasi akun, unggah soal, mulai ujian, pergantian jawaban, keluar tab, selesai ujian, diskualifikasi, ubah hak akses — dengan waktu, alamat IP, pengguna, keterangan.

**o. Laporan.** Ringkasan per ujian: jumlah peserta, hadir (%), rata-rata nilai, kelulusan (%), jumlah pelanggaran, distribusi status, log pelanggaran; filter periode & ujian; export PDF. Rekap nilai per ujian export PDF. Export data peserta/pengawas CSV/Excel.
