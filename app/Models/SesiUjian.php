<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SesiUjian extends Model
{
    use HasFactory;

    protected $table = 'sesi_ujians';

    protected $fillable = [
        'jadwal_id',
        'peserta_id',
        'status',
        'waktu_masuk',
        'waktu_selesai',
        'last_heartbeat',
        'urutan_soal',
        'sudah_masuk',
        'jumlah_peringatan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'waktu_selesai' => 'datetime',
            'last_heartbeat' => 'datetime',
            'urutan_soal' => 'array',
            'sudah_masuk' => 'boolean',
            'jumlah_peringatan' => 'integer',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function jawabanPesertas(): HasMany
    {
        return $this->hasMany(JawabanPeserta::class, 'sesi_ujian_id');
    }

    public function nilaiUjian(): HasOne
    {
        return $this->hasOne(NilaiUjian::class, 'sesi_ujian_id');
    }

    public function permintaanIzins(): HasMany
    {
        return $this->hasMany(PermintaanIzin::class, 'sesi_ujian_id');
    }
}
