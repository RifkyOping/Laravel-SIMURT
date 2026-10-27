<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HakAkses;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHakAksesController extends Controller
{
    public const MODULS = [
        'Dashboard',
        'Data Peserta',
        'Jadwal Ujian',
        'Bank Soal',
        'Ruang Ujian',
        'Monitoring',
        'Hasil Ujian',
        'Laporan',
        'Log Aktivitas',
    ];

    public const DEFAULT_PERMISSIONS = [
        'panitia' => [
            'Dashboard' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Data Peserta' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => false],
            'Jadwal Ujian' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Bank Soal' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Ruang Ujian' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Monitoring' => ['can_view' => true, 'can_create' => false, 'can_update' => true, 'can_delete' => false],
            'Hasil Ujian' => ['can_view' => true, 'can_create' => true, 'can_update' => false, 'can_delete' => false],
            'Laporan' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Log Aktivitas' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
        ],
        'admin' => [
            'Dashboard' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Data Peserta' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Jadwal Ujian' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Bank Soal' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Ruang Ujian' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Monitoring' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Hasil Ujian' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Laporan' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            'Log Aktivitas' => ['can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
        ],
        'pengawas' => [
            'Dashboard' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Data Peserta' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Jadwal Ujian' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Bank Soal' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Ruang Ujian' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Monitoring' => ['can_view' => true, 'can_create' => false, 'can_update' => true, 'can_delete' => false],
            'Hasil Ujian' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Laporan' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Log Aktivitas' => ['can_view' => true, 'can_create' => true, 'can_update' => false, 'can_delete' => false],
        ],
        'peserta' => [
            'Dashboard' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Data Peserta' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Jadwal Ujian' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Bank Soal' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Ruang Ujian' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Monitoring' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Hasil Ujian' => ['can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Laporan' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            'Log Aktivitas' => ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
        ],
    ];

    public function index(Request $request): View
    {
        $selectedRole = $request->get('role', 'panitia');
        $moduls = self::MODULS;

        // Ambil atau inisialisasi entri hak akses untuk role terpilih
        $hakAksesMap = [];
        foreach ($moduls as $modul) {
            $default = self::DEFAULT_PERMISSIONS[$selectedRole][$modul] ?? [
                'can_view' => false,
                'can_create' => false,
                'can_update' => false,
                'can_delete' => false,
            ];

            $entry = HakAkses::firstOrCreate(
                ['role' => $selectedRole, 'modul' => $modul],
                $default
            );
            $hakAksesMap[$modul] = $entry;
        }

        return view('admin.hak-akses.index', compact('selectedRole', 'moduls', 'hakAksesMap'));
    }

    public function update(Request $request): RedirectResponse
    {
        $role = $request->role;
        $permissions = $request->input('permissions', []);

        foreach (self::MODULS as $modul) {
            $modulPerms = $permissions[$modul] ?? [];

            HakAkses::updateOrCreate(
                ['role' => $role, 'modul' => $modul],
                [
                    'can_view' => isset($modulPerms['can_view']),
                    'can_create' => isset($modulPerms['can_create']),
                    'can_update' => isset($modulPerms['can_update']),
                    'can_delete' => isset($modulPerms['can_delete']),
                ]
            );
        }

        ActivityLogger::log('Ubah Hak Akses', "Admin memperbarui matriks hak akses untuk role {$role}");

        return back()->with('success', "Hak akses untuk role {$role} berhasil diperbarui.");
    }
}
