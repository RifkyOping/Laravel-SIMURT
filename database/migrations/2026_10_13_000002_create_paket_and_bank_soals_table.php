<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_soals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket', 100);
            $table->text('deskripsi')->nullable();
            $table->integer('durasi')->default(60); // dalam menit
            $table->decimal('passing_grade', 6, 2)->default(300.00);
            $table->enum('status_paket', ['akan_datang', 'berlangsung', 'selesai'])->default('akan_datang');
            $table->timestamps();
        });

        Schema::create('bank_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_soal_id')->constrained('paket_soals')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('pertanyaan');
            $table->string('tipe_soal', 50)->default('pilihan_ganda');
            $table->json('opsi_jawaban'); // {"A":"...","B":"...","C":"...","D":"...","E":"..."}
            $table->string('kunci_jawaban', 10);
            $table->decimal('bobot', 5, 2)->default(5.00);
            $table->string('kategori', 100)->nullable();
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soals');
        Schema::dropIfExists('paket_soals');
    }
};
