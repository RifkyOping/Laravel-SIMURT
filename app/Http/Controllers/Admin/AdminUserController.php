<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $totalPengguna = User::count();
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalPanitia = User::where('role', 'panitia')->count();
        $totalPengawas = User::where('role', 'pengawas')->count();

        $query = User::with('peserta');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && ! in_array(strtolower($request->role), ['semua', 'semua role'])) {
            $query->where('role', strtolower($request->role));
        }

        if ($request->filled('status') && ! in_array(strtolower($request->status), ['semua', 'semua status'])) {
            $statusVal = strtolower($request->status);
            if ($statusVal === 'non-aktif' || $statusVal === 'nonaktif') {
                $query->whereIn('status_akun', ['nonaktif', 'ditolak']);
            } else {
                $query->where('status_akun', $statusVal);
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'totalPengguna',
            'totalPeserta',
            'totalPanitia',
            'totalPengawas'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'role' => 'required|in:admin,panitia,pengawas,peserta',
            'status_akun' => 'required|in:aktif,nonaktif,terverifikasi,menunggu,ditolak',
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'no_identitas' => 'nullable|string|max:30',
            'instansi' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => User::generateUniqueUsername($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status_akun' => $request->status_akun,
        ]);

        if ($request->role === 'peserta') {
            Peserta::create([
                'user_id' => $user->id,
                'no_identitas' => $request->no_identitas ?? 'NIK-'.rand(100000, 999999),
                'instansi' => $request->instansi ?? 'Umum',
            ]);
        }

        ActivityLogger::log('Tambah Pengguna', "Admin menambahkan pengguna: {$user->name} ({$user->role})");

        return back()->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        $user->load('peserta');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,panitia,pengawas,peserta',
            'status_akun' => 'required|in:aktif,nonaktif,terverifikasi,menunggu,ditolak',
            'password' => ['nullable', 'confirmed', Rules\Password::min(8)],
            'no_identitas' => 'nullable|string|max:30',
            'instansi' => 'nullable|string|max:100',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status_akun' => $request->status_akun,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($user->role === 'peserta') {
            $user->peserta()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'no_identitas' => $request->no_identitas ?? 'NIK-'.rand(100000, 999999),
                    'instansi' => $request->instansi,
                ]
            );
        }

        ActivityLogger::log('Edit Pengguna', "Admin mengubah data pengguna: {$user->name}");

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete();

        ActivityLogger::log('Hapus Pengguna', "Admin menghapus pengguna: {$name}");

        return back()->with('success', "Pengguna {$name} berhasil dihapus.");
    }
}
