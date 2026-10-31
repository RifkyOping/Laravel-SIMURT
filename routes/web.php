<?php

use App\Http\Controllers\Admin\AdminAktivitasController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminHakAksesController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Panitia\PanitiaBankSoalController;
use App\Http\Controllers\Panitia\PanitiaDashboardController;
use App\Http\Controllers\Panitia\PanitiaJadwalController;
use App\Http\Controllers\Panitia\PanitiaLaporanController;
use App\Http\Controllers\Panitia\PanitiaNilaiController;
use App\Http\Controllers\Panitia\PanitiaPaketSoalController;
use App\Http\Controllers\Panitia\PanitiaPengawasController;
use App\Http\Controllers\Panitia\PanitiaPesertaController;
use App\Http\Controllers\Pengawas\PengawasDashboardController;
use App\Http\Controllers\Pengawas\PengawasLogController;
use App\Http\Controllers\Pengawas\PengawasMonitoringController;
use App\Http\Controllers\Peserta\MenungguVerifikasiController;
use App\Http\Controllers\Peserta\PesertaDashboardController;
use App\Http\Controllers\Peserta\PesertaHasilController;
use App\Http\Controllers\Peserta\PesertaJadwalController;
use App\Http\Controllers\Peserta\PesertaUjianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebRtcSignalingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root ke login atau dashboard role
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isPanitia()) {
            return redirect()->route('panitia.dashboard');
        }
        if ($user->isPengawas()) {
            return redirect()->route('pengawas.dashboard');
        }

        return redirect()->route('peserta.dashboard');
    }

    return redirect()->route('login');
});

// Routing dashboard umum (breeze fallback)
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isPanitia()) {
        return redirect()->route('panitia.dashboard');
    }
    if ($user->isPengawas()) {
        return redirect()->route('pengawas.dashboard');
    }

    return redirect()->route('peserta.dashboard');
})->middleware('auth')->name('dashboard');

// Halaman status registrasi peserta
Route::middleware('auth')->group(function () {
    Route::get('/menunggu-verifikasi', [MenungguVerifikasiController::class, 'index'])->name('menunggu.verifikasi');
    Route::get('/registrasi-ditolak', [MenungguVerifikasiController::class, 'ditolak'])->name('registrasi.ditolak');
    Route::post('/registrasi-ulang', [MenungguVerifikasiController::class, 'registrasiUlang'])->name('registrasi.ulang');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// GRUP ROLE: ADMIN
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class)->except(['show', 'create']);

    Route::get('/hak-akses', [AdminHakAksesController::class, 'index'])->name('hak-akses.index');
    Route::post('/hak-akses', [AdminHakAksesController::class, 'update'])->name('hak-akses.update');

    Route::get('/aktivitas', [AdminAktivitasController::class, 'index'])->name('aktivitas.index');

    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [AdminLaporanController::class, 'export'])->name('laporan.export');
});

// ==========================================
// GRUP ROLE: PANITIA
// ==========================================
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])->name('dashboard');

    // Kelola Data Peserta & Verifikasi
    Route::get('/peserta', [PanitiaPesertaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/{user}/edit', [PanitiaPesertaController::class, 'edit'])->name('peserta.edit');
    Route::put('/peserta/{user}', [PanitiaPesertaController::class, 'update'])->name('peserta.update');
    Route::delete('/peserta/{user}', [PanitiaPesertaController::class, 'destroy'])->name('peserta.destroy');
    Route::post('/peserta/{user}/verifikasi', [PanitiaPesertaController::class, 'verifikasi'])->name('peserta.verifikasi');
    Route::get('/peserta/export/csv', [PanitiaPesertaController::class, 'exportCsv'])->name('peserta.export-csv');

    // Kelola Data Pengawas
    Route::get('/pengawas/export/csv', [PanitiaPengawasController::class, 'exportCsv'])->name('pengawas.export-csv');
    Route::resource('pengawas', PanitiaPengawasController::class)->parameters(['pengawas' => 'user'])->except(['show', 'create']);

    // Kelola Paket Soal
    Route::resource('paket', PanitiaPaketSoalController::class)->except(['show', 'create']);

    // Kelola Bank Soal per Paket
    Route::get('/paket/{paket}/soal', [PanitiaBankSoalController::class, 'index'])->name('soal.index');
    Route::post('/paket/{paket}/soal', [PanitiaBankSoalController::class, 'store'])->name('soal.store');
    Route::put('/paket/{paket}/soal/{soal}', [PanitiaBankSoalController::class, 'update'])->name('soal.update');
    Route::delete('/paket/{paket}/soal/{soal}', [PanitiaBankSoalController::class, 'destroy'])->name('soal.destroy');
    Route::post('/paket/{paket}/soal/import', [PanitiaBankSoalController::class, 'import'])->name('soal.import');
    Route::get('/soal/template/download', [PanitiaBankSoalController::class, 'downloadTemplate'])->name('soal.template');

    // Kelola Jadwal Ujian
    Route::resource('jadwal', PanitiaJadwalController::class)->except(['show', 'create']);

    // Rekap Nilai Ujian
    Route::get('/nilai', [PanitiaNilaiController::class, 'index'])->name('nilai.index');
    Route::get('/nilai/{nilai}/detail', [PanitiaNilaiController::class, 'detail'])->name('nilai.detail');
    Route::get('/nilai/cetak', [PanitiaNilaiController::class, 'cetak'])->name('nilai.cetak');

    // Laporan Ujian
    Route::get('/laporan', [PanitiaLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/pdf', [PanitiaLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/laporan/{jadwal}/detail', [PanitiaLaporanController::class, 'detail'])->name('laporan.detail');
    Route::get('/laporan/{jadwal}/cetak', [PanitiaLaporanController::class, 'cetak'])->name('laporan.cetak');
});

// ==========================================
// GRUP ROLE: PENGAWAS
// ==========================================
Route::middleware(['auth', 'role:pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
    Route::get('/dashboard', [PengawasDashboardController::class, 'index'])->name('dashboard');

    Route::get('/monitoring', [PengawasMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{jadwal}', [PengawasMonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/monitoring/{jadwal}/poll', [PengawasMonitoringController::class, 'pollData'])->name('monitoring.poll');
    Route::get('/monitoring/sesi/{sesi}/pelanggaran', [PengawasMonitoringController::class, 'riwayatPelanggaran'])->name('monitoring.pelanggaran');
    Route::post('/monitoring/sesi/{sesi}/tegur', [PengawasMonitoringController::class, 'tegur'])->name('monitoring.tegur');
    Route::post('/monitoring/sesi/{sesi}/izin', [PengawasMonitoringController::class, 'izin'])->name('monitoring.izin');
    Route::post('/monitoring/sesi/{sesi}/blokir', [PengawasMonitoringController::class, 'blokir'])->name('monitoring.blokir');

    Route::get('/log', [PengawasLogController::class, 'index'])->name('log.index');
});

// ==========================================
// GRUP ROLE: PESERTA
// ==========================================
Route::middleware(['auth', 'role:peserta', 'status_akun'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/dashboard', [PesertaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/jadwal', [PesertaJadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/{jadwal}/konfirmasi', [PesertaJadwalController::class, 'konfirmasi'])->name('jadwal.konfirmasi');

    // Ruang Ujian (fokus tanpa layout luar)
    Route::get('/ujian/{jadwal}/masuk', [PesertaUjianController::class, 'masuk'])->name('ujian.masuk');
    Route::post('/ujian/sesi/{sesi}/autosave', [PesertaUjianController::class, 'autosave'])->name('ujian.autosave');
    Route::post('/ujian/sesi/{sesi}/heartbeat', [PesertaUjianController::class, 'heartbeat'])->name('ujian.heartbeat');
    Route::post('/ujian/sesi/{sesi}/pelanggaran', [PesertaUjianController::class, 'laporkanPelanggaran'])->name('ujian.pelanggaran');
    Route::post('/ujian/sesi/{sesi}/minta-izin', [PesertaUjianController::class, 'mintaIzin'])->name('ujian.minta-izin');
    Route::post('/ujian/sesi/{sesi}/selesai', [PesertaUjianController::class, 'selesai'])->name('ujian.selesai');
    Route::get('/ujian/{jadwal}/proses', [PesertaUjianController::class, 'proses'])->name('ujian.proses');

    Route::get('/hasil', [PesertaHasilController::class, 'index'])->name('hasil.index');
    Route::get('/hasil/{jadwal}', [PesertaHasilController::class, 'show'])->name('hasil.show');
});

// ==========================================
// WEBRTC SIGNALING PROCTORING (P2P Monitoring)
// ==========================================
Route::middleware(['auth'])->prefix('webrtc')->name('webrtc.')->group(function () {
    Route::post('/sesi/{sesi}/signal', [WebRtcSignalingController::class, 'sendSignal'])->name('signal.send');
    Route::get('/sesi/{sesi}/signal', [WebRtcSignalingController::class, 'getSignal'])->name('signal.get');
    Route::post('/sesi/{sesi}/close', [WebRtcSignalingController::class, 'closeSession'])->name('signal.close');
});

require __DIR__.'/auth.php';
