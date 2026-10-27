<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanIzin extends Model
{
    use HasFactory;

    protected $table = 'permintaan_izins';

    protected $fillable = [
        'sesi_ujian_id',
        'alasan',
        'status',
        'pengawas_user_id',
        'waktu_respon',
    ];

    protected function casts(): array
    {
        return [
            'waktu_respon' => 'datetime',
        ];
    }

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_ujian_id');
    }

    public function pengawas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengawas_user_id');
    }
}
