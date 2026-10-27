<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PanitiaPengawasController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'pengawas')->with('peserta');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('peserta', function ($sub) use ($search) {
                        $sub->where('no_identitas', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $status = strtolower($request->status);
            if ($status === 'terverifikasi') {
                $query->whereIn('status_akun', ['terverifikasi', 'aktif']);
            } elseif ($status === 'ditolak') {
                $query->whereIn('status_akun', ['ditolak', 'nonaktif']);
            } else {
                $query->where('status_akun', $status);
            }
        }

        $pengawas = $query->latest()->paginate(10)->withQueryString();

        return view('panitia.pengawas.index', compact('pengawas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'no_identitas' => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => User::generateUniqueUsername($request->email),
            'password' => Hash::make($request->password),
            'role' => 'pengawas',
            'status_akun' => 'aktif',
        ]);

        if ($request->filled('no_identitas')) {
            Peserta::create([
                'user_id' => $user->id,
                'no_identitas' => $request->no_identitas,
                'instansi' => 'Pengawas Ujian',
            ]);
        }

        ActivityLogger::log('Tambah Pengawas', "Pengawas baru {$user->name} ditambahkan oleh panitia");

        return back()->with('success', "Pengawas {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        $user->load('peserta');

        return view('panitia.pengawas.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,'.$user->id,
            'no_identitas' => 'nullable|string|max:30',
            'status_akun' => 'required|in:aktif,nonaktif,terverifikasi,menunggu,ditolak',
            'password' => ['nullable', 'confirmed', Rules\Password::min(8)],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status_akun' => $request->status_akun,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->filled('no_identitas')) {
            Peserta::updateOrCreate(
                ['user_id' => $user->id],
                ['no_identitas' => $request->no_identitas, 'instansi' => 'Pengawas Ujian']
            );
        }

        ActivityLogger::log('Edit Pengawas', "Data pengawas {$user->name} diperbarui oleh panitia");

        return redirect()->route('panitia.pengawas.index')->with('success', 'Data pengawas berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->jadwalsPengawas()->where('status', 'berlangsung')->exists()) {
            return back()->with('error', "Pengawas {$user->name} tidak dapat dihapus karena sedang bertugas pada jadwal ujian yang berlangsung.");
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('Hapus Pengawas', "Pengawas {$name} dihapus oleh panitia");

        return back()->with('success', "Pengawas {$name} berhasil dihapus.");
    }

    public function exportCsv(): StreamedResponse
    {
        ActivityLogger::log('Export Data Pengawas', 'Mengunduh data pengawas CSV');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_pengawas_'.date('Ymd_His').'.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Nama Lengkap', 'Email', 'NIK / No Identitas', 'Status Akun', 'Tanggal Daftar']);

            $users = User::where('role', 'pengawas')->with('peserta')->latest()->get();
            $no = 1;
            foreach ($users as $u) {
                fputcsv($handle, [
                    $no++,
                    $u->name,
                    $u->email,
                    $u->peserta?->no_identitas ?? '3171'.str_pad($u->id, 12, '0', STR_PAD_LEFT),
                    $u->status_akun,
                    $u->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
