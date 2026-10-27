<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaketSoal extends Model
{
    use HasFactory;

    protected $table = 'paket_soals';

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'durasi',
        'passing_grade',
        'status_paket',
    ];

    protected function casts(): array
    {
        return [
            'durasi' => 'integer',
            'passing_grade' => 'decimal:2',
        ];
    }

    public function bankSoals(): HasMany
    {
        return $this->hasMany(BankSoal::class, 'paket_soal_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'paket_soal_id');
    }
}
