<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanPeserta extends Model
{
    use HasFactory;

    protected $table = 'jawaban_pesertas';

    protected $fillable = [
        'sesi_ujian_id',
        'bank_soal_id',
        'jawaban',
        'benar',
        'waktu_simpan',
    ];

    protected function casts(): array
    {
        return [
            'benar' => 'boolean',
            'waktu_simpan' => 'datetime',
        ];
    }

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_ujian_id');
    }

    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
