<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'paket_soal_id',
        'nama_ujian',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'durasi',
        'passing_grade',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'durasi' => 'integer',
            'passing_grade' => 'decimal:2',
        ];
    }

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_soal_id');
    }

    public function pesertas(): BelongsToMany
    {
        return $this->belongsToMany(Peserta::class, 'jadwal_peserta', 'jadwal_id', 'peserta_id')->withTimestamps();
    }

    public function pengawas(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jadwal_pengawas', 'jadwal_id', 'user_id')->withTimestamps();
    }

    public function sesiUjians(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'jadwal_id');
    }

    public function nilaiUjians(): HasMany
    {
        return $this->hasMany(NilaiUjian::class, 'jadwal_id');
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'jadwal_id');
    }
}
