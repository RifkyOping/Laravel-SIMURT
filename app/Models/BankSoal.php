<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankSoal extends Model
{
    use HasFactory;

    protected $table = 'bank_soals';

    protected $fillable = [
        'paket_soal_id',
        'user_id',
        'pertanyaan',
        'tipe_soal',
        'opsi_jawaban',
        'kunci_jawaban',
        'bobot',
        'kategori',
        'tingkat_kesulitan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'opsi_jawaban' => 'array',
            'bobot' => 'decimal:2',
            'aktif' => 'boolean',
        ];
    }

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class, 'paket_soal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jawabanPesertas(): HasMany
    {
        return $this->hasMany(JawabanPeserta::class, 'bank_soal_id');
    }
}
