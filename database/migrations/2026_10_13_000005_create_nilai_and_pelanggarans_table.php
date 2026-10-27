<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('sesi_ujian_id')->nullable()->constrained('sesi_ujians')->cascadeOnDelete();
            $table->decimal('nilai_akhir', 6, 2)->default(0.00);
            $table->integer('jumlah_benar')->default(0);
            $table->integer('jumlah_salah')->default(0);
            $table->integer('jumlah_tidak_dijawab')->default(0);
            $table->enum('status_kelulusan', ['LULUS', 'TIDAK LULUS', 'DISKUALIFIKASI'])->default('TIDAK LULUS');
            $table->timestamps();

            $table->unique(['peserta_id', 'jadwal_id']);
        });

        Schema::create('pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // pencatat / pengawas
            $table->string('jenis_pelanggaran', 100);
            $table->dateTime('waktu');
            $table->text('keterangan')->nullable();
            $table->string('tindakan', 255)->nullable();
            $table->timestamps();

            $table->index(['jadwal_id', 'waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggarans');
        Schema::dropIfExists('nilai_ujians');
    }
};
