<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peserta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_identitas',
        'instansi',
    ];

    public function getNoUjianAttribute(): string
    {
        if (! empty($this->attributes['no_ujian'])) {
            return $this->attributes['no_ujian'];
        }
        if (! empty($this->no_identitas) && str_contains($this->no_identitas, '-')) {
            return $this->no_identitas;
        }

        return '26-01-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwals(): BelongsToMany
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_peserta', 'peserta_id', 'jadwal_id')->withTimestamps();
    }

    public function sesiUjians(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'peserta_id');
    }

    public function nilaiUjians(): HasMany
    {
        return $this->hasMany(NilaiUjian::class, 'peserta_id');
    }

    public function pelanggarans(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'peserta_id');
    }
}
