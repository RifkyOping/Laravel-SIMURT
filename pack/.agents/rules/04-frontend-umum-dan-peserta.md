# 04 — Frontend: Gaya Umum, Halaman Umum, Peserta (dari Figma)
Aktifkan saat mengerjakan view Blade. Sumber: Figma "LokalKarya", page "PPL" (set frame terbaru).

## Gaya & layout
Wireframe/mockup bergaya dashboard admin, desktop-first (frame 1280x800) tapi RESPONSIF (peserta/pengawas bisa pakai HP). Header: nama sistem "Sistem Manajemen Ujian", nama pengguna, tombol Keluar. Sidebar menu per role. Konvensi SRS: dominan warna netral & profesional, font sans-serif; Biru/primary = navigasi utama; Merah = aksi destruktif/diskualifikasi; Kuning = peringatan; Hijau = aktif/lulus. Tombol aksi konsisten; hapus & diskualifikasi SELALU minta konfirmasi. Tabel selalu punya pencarian, filter status, pagination ("Menampilkan 1-10 dari 48 entries"). Pesan error jelas. Token warna/font final belum ditetapkan di Figma: ikuti konvensi ini, buat sebagai variabel Tailwind di satu tempat.

## Halaman umum
- **Login**: judul "Sistem Manajemen Ujian", "Silakan masuk ke akun Anda"; Email, Password, tombol Masuk, link "Registrasi Akun Peserta", teks "Lupa Password? Hubungi Panitia" (tidak ada reset otomatis).
- **Registrasi Peserta**: Nama Lengkap, Email, Nomor Identitas, Instansi, Password (min 8), Konfirmasi Password, tombol "Daftar Sekarang", link "Kembali ke Login".
- **Menunggu Verifikasi**: halaman status tunggu setelah registrasi.

## Peserta (sidebar: Dashboard, Jadwal Ujian, Hasil Ujian)
- **Dashboard**: sapaan "Selamat Datang, {nama}!", badge "Status Akun: TERVERIFIKASI", tabel Jadwal Ujian Mendatang (Nama, Tanggal, Waktu, Status [Siap / Belum Mulai], Aksi [Masuk Ujian / Belum Tersedia]), kartu Pengumuman Terbaru.
- **Jadwal Ujian**: tab filter Semua / Akan Datang / Berlangsung / Selesai; daftar ujian (nama, tanggal, waktu, jumlah soal, status, tombol Masuk Ujian atau Lihat Hasil); banner "PENTING: Pastikan koneksi internet stabil sebelum memasuki ruang ujian".
- **Konfirmasi Masuk Ujian**: detail (nama, tanggal, waktu, durasi, jumlah soal) + 5 peraturan: (1) soal pilihan ganda sesuai sub-materi, (2) urutan soal diacak, (3) jawaban tersimpan otomatis tiap pindah soal, (4) hanya satu kesempatan masuk, sesi tidak dapat diulang, (5) dilarang membuka tab baru/aplikasi lain/beralih layar. Checkbox persetujuan, tombol "Kembali ke Dashboard" dan "Mulai Ujian" (nonaktif sampai dicentang). Catatan "Pastikan koneksi internet Anda stabil".
- **Ruang Ujian**: layout satu kolom TANPA header/footer/menu utama (fokus). Header: nama ujian, "Peserta: {nama} (No. Ujian: ...)", "Sisa Waktu hh:mm:ss". Panel kiri 320px "Navigasi Soal": grid nomor 1-N dengan legenda Soal Aktif / Sudah Dijawab / Belum Dijawab. Area kanan: "Soal No. X" + badge "Sudah Terjawab", teks soal, pilihan A-E sebagai kartu; tombol "Sebelumnya", "Simpan & Lanjut"; indikator Auto-Save; tombol "Selesai Ujian" dengan peringatan "Periksa kembali seluruh jawaban Anda. Aksi selesai tidak bisa dibatalkan."
- **Hasil Ujian (diproses)**: info ujian + "Hasil Ujian Sedang Diproses / Silahkan Tunggu Beberapa Saat".
- **Hasil Ujian Peserta**: kartu hasil (nama ujian, tanggal, badge LULUS SELEKSI / TIDAK LULUS, Nilai Akhir, Passing Grade, Jumlah Soal, Jawaban Benar, Jawaban Salah) + tabel "Riwayat Hasil Seleksi Lainnya" (Nama Ujian, Tanggal, Nilai, Status).
