<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\PaketSoal;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PanitiaBankSoalController extends Controller
{
    public function index(Request $request, PaketSoal $paket): View
    {
        $query = $paket->bankSoals();

        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', "%{$request->search}%");
        }

        $soals = $query->latest()->paginate(10)->withQueryString();

        $totalSoal = $paket->bankSoals()->count();
        $soalAktif = $paket->bankSoals()->where('aktif', true)->count();
        $soalNonAktif = $paket->bankSoals()->where('aktif', false)->count();

        return view('panitia.soal.index', compact('paket', 'soals', 'totalSoal', 'soalAktif', 'soalNonAktif'));
    }

    public function store(Request $request, PaketSoal $paket): RedirectResponse
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'tingkat_kesulitan' => 'nullable|in:mudah,sedang,sulit',
            'bobot' => 'nullable|numeric|min:0.1',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
        ]);

        $opsi = [
            'A' => $request->opsi_a,
            'B' => $request->opsi_b,
            'C' => $request->opsi_c,
            'D' => $request->opsi_d,
            'E' => $request->opsi_e,
        ];

        BankSoal::create([
            'paket_soal_id' => $paket->id,
            'user_id' => Auth::id(),
            'pertanyaan' => $request->pertanyaan,
            'tipe_soal' => 'pilihan_ganda',
            'opsi_jawaban' => $opsi,
            'kunci_jawaban' => $request->kunci_jawaban,
            'bobot' => $request->filled('bobot') ? $request->bobot : 50.00,
            'kategori' => $request->kategori,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'aktif' => true,
        ]);

        ActivityLogger::log('Tambah Soal', "Soal baru ditambahkan ke paket {$paket->nama_paket}");

        return back()->with('success', 'Soal berhasil ditambahkan ke paket.');
    }

    public function update(Request $request, PaketSoal $paket, BankSoal $soal): RedirectResponse
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'tingkat_kesulitan' => 'nullable|in:mudah,sedang,sulit',
            'bobot' => 'nullable|numeric|min:0.1',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'opsi_e' => 'required|string',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
            'aktif' => 'nullable|boolean',
        ]);

        $opsi = [
            'A' => $request->opsi_a,
            'B' => $request->opsi_b,
            'C' => $request->opsi_c,
            'D' => $request->opsi_d,
            'E' => $request->opsi_e,
        ];

        $soal->update([
            'pertanyaan' => $request->pertanyaan,
            'opsi_jawaban' => $opsi,
            'kunci_jawaban' => $request->kunci_jawaban,
            'bobot' => $request->filled('bobot') ? $request->bobot : ($soal->bobot ?? 50.00),
            'kategori' => $request->kategori,
            'tingkat_kesulitan' => $request->tingkat_kesulitan,
            'aktif' => $request->boolean('aktif', true),
        ]);

        ActivityLogger::log('Edit Soal', "Soal #{$soal->id} pada paket {$paket->nama_paket} diperbarui");

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(PaketSoal $paket, BankSoal $soal): RedirectResponse
    {
        $soal->delete();

        ActivityLogger::log('Hapus Soal', "Soal pada paket {$paket->nama_paket} dihapus");

        return back()->with('success', 'Soal berhasil dihapus.');
    }

    public function import(Request $request, PaketSoal $paket): RedirectResponse
    {
        $request->validate([
            'file_soal' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file_soal');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $inserted = 0;
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count($row) < 9) {
                continue;
            }

            // format: Pertanyaan, Opsi A, Opsi B, Opsi C, Opsi D, Opsi E, Kunci, Bobot, Kategori
            $pertanyaan = trim($row[0]);
            $opsiA = trim($row[1]);
            $opsiB = trim($row[2]);
            $opsiC = trim($row[3]);
            $opsiD = trim($row[4]);
            $opsiE = trim($row[5]);
            $kunci = strtoupper(trim($row[6]));
            $bobot = is_numeric($row[7]) ? (float) $row[7] : 5.00;
            $kategori = isset($row[8]) ? trim($row[8]) : 'Umum';

            if (! empty($pertanyaan) && in_array($kunci, ['A', 'B', 'C', 'D', 'E'])) {
                BankSoal::create([
                    'paket_soal_id' => $paket->id,
                    'user_id' => Auth::id(),
                    'pertanyaan' => $pertanyaan,
                    'tipe_soal' => 'pilihan_ganda',
                    'opsi_jawaban' => [
                        'A' => $opsiA,
                        'B' => $opsiB,
                        'C' => $opsiC,
                        'D' => $opsiD,
                        'E' => $opsiE,
                    ],
                    'kunci_jawaban' => $kunci,
                    'bobot' => $bobot,
                    'kategori' => $kategori,
                    'tingkat_kesulitan' => 'sedang',
                    'aktif' => true,
                ]);
                $inserted++;
            }
        }
        fclose($handle);

        ActivityLogger::log('Import Soal', "Mengimpor {$inserted} butir soal ke paket {$paket->nama_paket}");

        return back()->with('success', "Berhasil mengimpor {$inserted} butir soal.");
    }

    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_soal.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'Kunci Jawaban', 'Bobot', 'Kategori']);
            fputcsv($handle, [
                'Berapakah hasil dari 25 x 4?',
                '50',
                '75',
                '100',
                '125',
                '150',
                'C',
                '5.00',
                'Logika Matematika',
            ]);
            fclose($handle);
        }, 200, $headers);
    }
}
