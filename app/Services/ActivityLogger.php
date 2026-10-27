<?php

namespace App\Services;

use App\Models\Aktivitas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $jenisAktivitas,
        ?string $keterangan = null,
        ?int $jadwalId = null,
        ?User $user = null
    ): Aktivitas {
        $currentUser = $user ?? Auth::user();

        return Aktivitas::create([
            'user_id' => $currentUser?->id,
            'jadwal_id' => $jadwalId,
            'jenis_aktivitas' => $jenisAktivitas,
            'waktu' => now(),
            'keterangan' => $keterangan,
            'alamat_ip' => request()->ip(),
        ]);
    }
}
