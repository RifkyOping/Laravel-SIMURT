<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        $query = Aktivitas::with(['user', 'jadwal']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_aktivitas', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('alamat_ip', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('jenis_aktivitas')) {
            $query->where('jenis_aktivitas', $request->jenis_aktivitas);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('waktu', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('waktu', '<=', $request->tanggal_selesai);
        }

        $logs = $query->orderBy('waktu', 'desc')->paginate(10)->withQueryString();
        $users = User::orderBy('name')->get();
        $jenisList = Aktivitas::distinct()->pluck('jenis_aktivitas')->filter();

        return view('admin.aktivitas.index', compact('logs', 'users', 'jenisList'));
    }
}
