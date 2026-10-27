<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'status_akun',
        'terakhir_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terakhir_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peserta(): HasOne
    {
        return $this->hasOne(Peserta::class, 'user_id');
    }

    public function aktivitas(): HasMany
    {
        return $this->hasMany(Aktivitas::class, 'user_id');
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function jadwalsPengawas(): BelongsToMany
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_pengawas', 'user_id', 'jadwal_id')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPanitia(): bool
    {
        return $this->role === 'panitia';
    }

    public function isPengawas(): bool
    {
        return $this->role === 'pengawas';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public static function generateUniqueUsername(string $email): string
    {
        $base = strstr($email, '@', true) ?: 'user';
        $base = preg_replace('/[^a-zA-Z0-9_]/', '', $base) ?: 'user';
        $username = $base.'_'.rand(100, 999);

        while (static::where('username', $username)->exists()) {
            $username = $base.'_'.rand(1000, 99999);
        }

        return $username;
    }

    public function isVerified(): bool
    {
        return $this->status_akun === 'terverifikasi' || $this->status_akun === 'aktif';
    }
}
