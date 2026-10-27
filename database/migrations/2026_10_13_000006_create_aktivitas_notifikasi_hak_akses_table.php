<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwals')->cascadeOnDelete();
            $table->string('jenis_aktivitas', 100);
            $table->dateTime('waktu');
            $table->text('keterangan')->nullable();
            $table->string('alamat_ip', 45)->nullable();
            $table->timestamps();

            $table->index(['waktu', 'jenis_aktivitas']);
        });

        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->text('isi');
            $table->boolean('dibaca')->default(false);
            $table->dateTime('waktu');
            $table->timestamps();

            $table->index(['user_id', 'dibaca']);
        });

        Schema::create('hak_akses', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['admin', 'panitia', 'pengawas', 'peserta']);
            $table->string('modul', 100);
            $table->boolean('can_view')->default(true);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();

            $table->unique(['role', 'modul']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hak_akses');
        Schema::dropIfExists('notifikasis');
        Schema::dropIfExists('aktivitas');
    }
};
