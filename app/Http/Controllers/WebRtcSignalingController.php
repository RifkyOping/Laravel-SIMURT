<?php

namespace App\Http\Controllers;

use App\Models\SesiUjian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WebRtcSignalingController extends Controller
{
    /**
     * Mengirim sinyal WebRTC (SDP Offer, SDP Answer, ICE Candidate, atau Status).
     */
    public function sendSignal(Request $request, SesiUjian $sesi): JsonResponse
    {
        $this->authorizeAccess($sesi);

        $validated = $request->validate([
            'target' => 'required|string|in:pengawas,peserta',
            'type' => 'required|string|max:50',
            'data' => 'nullable',
        ]);

        $key = "webrtc:signals:{$sesi->id}:{$validated['target']}";

        // Jika sinyalnya adalah request_stream, kita memulai sesi baru, bersihkan sinyal antrean lama
        if ($validated['type'] === 'request_stream') {
            Cache::forget("webrtc:signals:{$sesi->id}:peserta");
            Cache::forget("webrtc:signals:{$sesi->id}:pengawas");
        }

        $signals = Cache::get($key, []);

        if (! is_array($signals)) {
            $signals = [];
        }

        $signals[] = [
            'type' => $validated['type'],
            'data' => $validated['data'] ?? null,
            'sender_id' => Auth::id(),
            'timestamp' => now()->toISOString(),
        ];

        // Batasi maksimal 30 sinyal dalam antrean untuk menjaga performa
        if (count($signals) > 30) {
            $signals = array_slice($signals, -30);
        }

        Cache::put($key, $signals, now()->addMinutes(3));

        return response()->json([
            'success' => true,
            'message' => 'Sinyal WebRTC berhasil dikirim.',
        ]);
    }

    /**
     * Mengambil antrean sinyal WebRTC untuk role saat ini (Long Polling / Polling).
     */
    public function getSignal(Request $request, SesiUjian $sesi): JsonResponse
    {
        $this->authorizeAccess($sesi);

        $user = Auth::user();
        $recipientRole = ($user->role === 'peserta') ? 'peserta' : 'pengawas';
        $key = "webrtc:signals:{$sesi->id}:{$recipientRole}";

        // Ambil dan kosongkan antrean sinyal untuk recipient ini
        $allSignals = Cache::pull($key, []);
        $signals = [];

        if (is_array($allSignals)) {
            foreach ($allSignals as $sig) {
                // Jangan terima sinyal yang dikirim oleh diri sendiri
                if (isset($sig['sender_id']) && $sig['sender_id'] == $user->id) {
                    continue;
                }
                $signals[] = $sig;
            }
        }

        return response()->json([
            'success' => true,
            'signals' => $signals,
            'recipient' => $recipientRole,
        ]);
    }

    /**
     * Menutup sesi WebRTC dan membersihkan sinyal di cache.
     */
    public function closeSession(Request $request, SesiUjian $sesi): JsonResponse
    {
        $this->authorizeAccess($sesi);
        $user = Auth::user();

        // Bersihkan sinyal
        Cache::forget("webrtc:signals:{$sesi->id}:pengawas");
        Cache::forget("webrtc:signals:{$sesi->id}:peserta");

        // Kirim sinyal stop_stream ke pihak lawan sebelum membersihkan
        $target = ($user->role === 'peserta') ? 'pengawas' : 'peserta';
        Cache::put("webrtc:signals:{$sesi->id}:{$target}", [[
            'type' => 'stop_stream',
            'data' => null,
            'sender_id' => $user->id,
            'timestamp' => now()->toISOString(),
        ]], now()->addSeconds(30));

        // Untuk backwards-compatibility testing jika diperlukan
        if ($target === 'peserta') {
            Cache::put("webrtc:signals:{$sesi->id}:pengawas", [[
                'type' => 'stop_stream',
                'data' => null,
                'sender_id' => $user->id,
                'timestamp' => now()->toISOString(),
            ]], now()->addSeconds(30));
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesi WebRTC berhasil ditutup.',
        ]);
    }

    /**
     * Validasi hak akses pengguna terhadap sesi ujian.
     */
    protected function authorizeAccess(SesiUjian $sesi): void
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'pengawas') {
            // Pengawas jadwal ujian atau pengawas aktif
            return;
        }

        if ($user->role === 'peserta' && $sesi->peserta?->user_id === $user->id) {
            return;
        }

        abort(403, 'Anda tidak memiliki hak akses ke sesi WebRTC ini.');
    }
}
