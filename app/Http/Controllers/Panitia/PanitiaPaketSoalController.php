<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanitiaPaketSoalController extends Controller
{
    public function index(Request $request): View
    {
        $query = PaketSoal::withCount('bankSoals');

        if ($request->filled('search')) {
            $query->where('nama_paket', 'like', "%{$request->search}%");
        }

        $paketSoals = $query->latest()->paginate(10)->withQueryString();

        $totalPaket = PaketSoal::count();
        $paketAktif = PaketSoal::where('status_paket', 'berlangsung')->count();
        $paketSelesai = PaketSoal::where('status_paket', 'selesai')->count();

        return view('panitia.paket.index', compact('paketSoals', 'totalPaket', 'paketAktif', 'paketSelesai'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'passing_grade' => 'nullable|numeric|min:0|max:500',
            'status_paket' => 'required|in:akan_datang,berlangsung,selesai',
        ]);

        if (empty($validated['passing_grade'])) {
            $validated['passing_grade'] = 300.00;
        }

        $paket = PaketSoal::create($validated);

        ActivityLogger::log('Tambah Paket Soal', "Paket soal baru dibuat: {$paket->nama_paket}");

        return redirect()->route('panitia.paket.index')->with('success', "Paket soal {$paket->nama_paket} berhasil ditambahkan.");
    }

    public function edit(PaketSoal $paket): View
    {
        return view('panitia.paket.edit', compact('paket'));
    }

    public function update(Request $request, PaketSoal $paket): RedirectResponse
    {
        $validated = $request->validate([
            'nama_paket' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'passing_grade' => 'required|numeric|min:0|max:500',
            'status_paket' => 'required|in:akan_datang,berlangsung,selesai',
        ]);

        $paket->update($validated);

        ActivityLogger::log('Edit Paket Soal', "Paket soal diubah: {$paket->nama_paket}");

        return redirect()->route('panitia.paket.index')->with('success', 'Paket soal berhasil diperbarui.');
    }

    public function destroy(PaketSoal $paket): RedirectResponse
    {
        $name = $paket->nama_paket;
        $paket->delete();

        ActivityLogger::log('Hapus Paket Soal', "Paket soal dihapus: {$name}");

        return redirect()->route('panitia.paket.index')->with('success', "Paket soal {$name} berhasil dihapus.");
    }
}
