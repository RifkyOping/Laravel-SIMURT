<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiUjian extends Model
{
    use HasFactory;

    protected $table = 'nilai_ujians';

    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'sesi_ujian_id',
        'nilai_akhir',
        'jumlah_benar',
        'jumlah_salah',
        'jumlah_tidak_dijawab',
        'status_kelulusan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_akhir' => 'decimal:2',
            'jumlah_benar' => 'integer',
            'jumlah_salah' => 'integer',
            'jumlah_tidak_dijawab' => 'integer',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_ujian_id');
    }
}
