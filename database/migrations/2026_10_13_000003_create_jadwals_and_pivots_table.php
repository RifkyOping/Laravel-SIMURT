<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_soal_id')->constrained('paket_soals')->cascadeOnDelete();
            $table->string('nama_ujian', 150);
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('durasi'); // menit
            $table->decimal('passing_grade', 6, 2)->nullable();
            $table->enum('status', ['akan_datang', 'berlangsung', 'selesai'])->default('akan_datang');
            $table->timestamps();
        });

        Schema::create('jadwal_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['jadwal_id', 'peserta_id']);
        });

        Schema::create('jadwal_pengawas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['jadwal_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pengawas');
        Schema::dropIfExists('jadwal_peserta');
        Schema::dropIfExists('jadwals');
    }
};
