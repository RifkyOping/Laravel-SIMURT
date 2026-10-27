<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->enum('status', [
                'belum_masuk',
                'berlangsung',
                'terputus',
                'menunggu_izin',
                'izin_diberikan',
                'selesai',
                'diskualifikasi',
            ])->default('belum_masuk');
            $table->dateTime('waktu_masuk')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->dateTime('last_heartbeat')->nullable();
            $table->json('urutan_soal')->nullable();
            $table->boolean('sudah_masuk')->default(false);
            $table->integer('jumlah_peringatan')->default(0);
            $table->timestamps();

            $table->unique(['jadwal_id', 'peserta_id']);
            $table->index(['jadwal_id', 'status']);
        });

        Schema::create('jawaban_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_ujian_id')->constrained('sesi_ujians')->cascadeOnDelete();
            $table->foreignId('bank_soal_id')->constrained('bank_soals')->cascadeOnDelete();
            $table->string('jawaban', 10)->nullable();
            $table->boolean('benar')->nullable();
            $table->dateTime('waktu_simpan')->nullable();
            $table->timestamps();

            $table->unique(['sesi_ujian_id', 'bank_soal_id']);
        });

        Schema::create('permintaan_izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_ujian_id')->constrained('sesi_ujians')->cascadeOnDelete();
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->foreignId('pengawas_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('waktu_respon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_izins');
        Schema::dropIfExists('jawaban_pesertas');
        Schema::dropIfExists('sesi_ujians');
    }
};
