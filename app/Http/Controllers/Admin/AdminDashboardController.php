<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalPengguna = User::count();
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalPanitia = User::where('role', 'panitia')->count();
        $totalPengawas = User::where('role', 'pengawas')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        // Pengguna terbaru (real data)
        $penggunaTerbaru = User::where('role', '!=', 'admin')->latest()->take(5)->get();

        // Audit log terbaru (real data)
        $auditLogTerbaru = Aktivitas::with('user')->orderByDesc('waktu')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalPengguna',
            'totalPeserta',
            'totalPanitia',
            'totalPengawas',
            'totalAdmin',
            'penggunaTerbaru',
            'auditLogTerbaru'
        ));
    }
}
