<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PanitiaPesertaController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'peserta')->with('peserta');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('peserta', function ($sub) use ($search) {
                        $sub->where('no_identitas', 'like', "%{$search}%")
                            ->orWhere('instansi', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status_akun', $request->status);
        }

        $pesertas = $query->latest()->paginate(10)->withQueryString();

        return view('panitia.peserta.index', compact('pesertas'));
    }

    public function verifikasi(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:terverifikasi,ditolak',
        ]);

        $user->update(['status_akun' => $request->status]);

        ActivityLogger::log(
            'Verifikasi Akun',
            "Akun peserta {$user->name} diubah menjadi {$request->status} oleh panitia",
            null,
            $user
        );

        return back()->with('success', "Status peserta {$user->name} berhasil diperbarui menjadi {$request->status}.");
    }

    public function edit(User $user): View
    {
        $user->load('peserta');

        return view('panitia.peserta.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,'.$user->id,
            'no_identitas' => 'required|string|max:30',
            'instansi' => 'nullable|string|max:100',
            'status_akun' => 'required|in:menunggu,terverifikasi,ditolak',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'status_akun' => $request->status_akun,
        ]);

        $user->peserta()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'no_identitas' => $request->no_identitas,
                'instansi' => $request->instansi,
            ]
        );

        ActivityLogger::log('Edit Peserta', "Data peserta {$user->name} diperbarui oleh panitia");

        return redirect()->route('panitia.peserta.index')->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $peserta = $user->peserta;
        if ($peserta && $peserta->jadwals()->where('status', 'berlangsung')->exists()) {
            return back()->with('error', "Peserta {$user->name} tidak dapat dihapus karena sedang terdaftar pada jadwal ujian yang berlangsung.");
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('Hapus Peserta', "Peserta {$name} dihapus oleh panitia");

        return back()->with('success', "Peserta {$name} berhasil dihapus.");
    }

    public function exportCsv(): StreamedResponse
    {
        ActivityLogger::log('Export Data Peserta', 'Mengunduh data peserta CSV');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_peserta_'.date('Ymd_His').'.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Nama Lengkap', 'Email', 'NIK / No Identitas', 'Instansi', 'Status Akun', 'Tanggal Daftar']);

            $users = User::where('role', 'peserta')->with('peserta')->latest()->get();
            $no = 1;
            foreach ($users as $u) {
                fputcsv($handle, [
                    $no++,
                    $u->name,
                    $u->email,
                    $u->peserta?->no_identitas ?? '-',
                    $u->peserta?->instansi ?? '-',
                    $u->status_akun,
                    $u->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
